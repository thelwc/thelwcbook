<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Voucher;
use Illuminate\Support\Facades\Auth; // Nhớ import Auth
use App\Models\Setting;

class CartController extends Controller
{
    // 1. Xem giỏ hàng
    // 1. Xem giỏ hàng
    public function index()
    {
        $userId = auth()->id(); 

        // --- 1. LẤY DANH SÁCH VOUCHER (Code cũ của cậu) ---
        $vouchers = Voucher::where('end_date', '>=', now()) 
            ->where('quantity', '>', 0)
            ->where(function($query) use ($userId) {
                $query->whereNull('user_id')
                    ->orWhere('user_id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // --- 2. TÍNH TỔNG TIỀN VÀ PHÍ SHIP (Code mới thêm) ---
        $cart = session()->get('cart', []);
        $tongTienHang = 0;
        $needsShipping = false;

        if ($cart) {
            foreach ($cart as $item) {
                // Kiểm tra xem giỏ hàng có sách giấy không
                if (!isset($item['type']) || $item['type'] == 'physical') {
                    $needsShipping = true;
                }
                // Cộng dồn tiền hàng
                $tongTienHang += $item['price'] * $item['quantity'];
            }
        }

        // Gọi cấu hình từ Database ra (Dùng trực tiếp \App\Models\Setting để khỏi lo quên import)
        $mocFreeship = \App\Models\Setting::where('key', 'free_ship_threshold')->value('value') ?? 500000;
        $phiShipMacDinh = \App\Models\Setting::where('key', 'shipping_fee')->value('value') ?? 30000;

        $tienShip = \App\Models\Setting::calculateShipping($tongTienHang, $needsShipping);

        // --- 3. TRUYỀN HẾT BIẾN RA NGOÀI VIEW ---
        return view('client.pages.cart', compact('vouchers', 'cart', 'tongTienHang', 'tienShip', 'mocFreeship', 'needsShipping'));
    }

    // 2. Thêm sản phẩm vào giỏ (CHECK SỞ HỮU EBOOK)
    public function addToCart(Request $request, $id)
{
    $book = Book::findOrFail($id);
    $cart = session()->get('cart', []);
    
    // Lấy thông tin
    $qty = $request->input('quantity', 1);
    $type = $request->input('type', 'physical');

    // Biến lưu trạng thái cảnh báo
    $isWarning = false;
    $warningMsg = '';

    // =================================================================
    // 🔥 LOGIC SỬA ĐỔI: CHỈ CẢNH BÁO MUA EBOOK NẾU ĐÃ SỞ HỮU (Không chặn cứng)
    // =================================================================
    if ($type == 'ebook' && Auth::check()) {
        // Kiểm tra user hiện tại có sách này trong tủ sách chưa
        if (Auth::user()->booksOwned->contains($id)) {
            $isWarning = true;
            $warningMsg = 'Bạn đã có Ebook này trong Tủ sách. Hãy chắc chắn bạn muốn mua thêm (VD: mua tặng) nhé!';
        }
    }

    // 🔥 TẠO ID RIÊNG CHO TỪNG LOẠI
    $cartID = $id . '_' . $type; 

    // --- 1. XÁC ĐỊNH GIÁ TIỀN ---
    $price = 0;
    if ($type == 'ebook') {
        $price = $book->ebook_price;
    } else {
        $price = ($book->sale_price && $book->sale_price < $book->price) ? $book->sale_price : $book->price;
    }

    // --- 2. CHECK TỒN KHO (CHỈ SÁCH GIẤY) ---
    if ($type == 'physical') {
        if ($qty > $book->quantity) {
            return redirect()->back()->with('error', 'Kho chỉ còn ' . $book->quantity . ' cuốn!');
        }
    }

    // --- 3. XỬ LÝ GIỎ HÀNG ---
    if(isset($cart[$cartID])) {
        
        // Nếu là Ebook -> Bỏ chặn cứng, cho phép cộng dồn nhưng hiện cảnh báo
        if ($type == 'ebook') {
            $isWarning = true;
            $warningMsg = 'Ebook này đang có sẵn trong giỏ hàng. Bạn có chắc muốn mua số lượng nhiều hơn 1 cuốn không?';
            $cart[$cartID]['quantity'] += $qty; 
        } 
        // Nếu là Sách giấy -> Cộng dồn và check tồn kho bình thường
        else {
            if ($cart[$cartID]['quantity'] + $qty > $book->quantity) {
                return redirect()->back()->with('error', 'Kho chỉ còn ' . $book->quantity . ' cuốn. Không thể thêm tiếp!');
            }
            $cart[$cartID]['quantity'] += $qty;
        }
        
        $cart[$cartID]['price'] = $price; 

    } else {
        // Thêm mới
        $cart[$cartID] = [
            "id" => $book->id,
            "name" => $book->title . ($type == 'ebook' ? ' (Ebook)' : ''),
            "quantity" => $qty,
            "price" => $price,
            "image" => $book->image,
            "type" => $type,
            "max_quantity" => $book->quantity,
            
        ];
    }

    session()->put('cart', $cart);
    // =================================================================
    // 🔥 RETURN KẾT QUẢ: Xử lý nút "Mua ngay" và "Thêm vào giỏ"
    // =================================================================
    // Lấy tín hiệu từ form xem khách bấm nút nào ('add' hay 'buy')
    $action = $request->input('action'); 

    // 1. Nếu có cờ cảnh báo (Ví dụ: Đòi mua 10 cuốn nhưng kho chỉ còn 5)
    if ($isWarning) {
        if ($action === 'buy') {
            // Vẫn cho sang giỏ hàng nhưng ném theo câu chửi (warning)
            return redirect()->route('cart.index')->with('warning', $warningMsg);
        }
        // Nút thêm giỏ bình thường
        return redirect()->back()->with('warning', $warningMsg);
    }

    // 2. Nếu mọi thứ suôn sẻ
    if ($action === 'buy') {
        // Khách bấm "Mua ngay" -> Bế thẳng sang trang Giỏ hàng
        return redirect()->route('cart.index')->with('success', 'Đã thêm vào giỏ hàng thành công!');
    }

    // Mặc định (Khách bấm "Thêm vào giỏ") -> Ở lại trang hiện tại
    return redirect()->back()->with('success', 'Đã thêm vào giỏ hàng thành công!');
}

    // ... (Các hàm update, remove, recheckCoupon giữ nguyên như cũ) ...
  public function update(Request $request)
    {
        if($request->id && $request->quantity){
            $cart = session()->get('cart');
            
            $bookId = intval(explode('_', $request->id)[0]);
            $book = Book::find($bookId);

            // Kiểm tra tồn kho (Chỉ áp dụng cho sách giấy)
            if ($book && isset($cart[$request->id]['type']) && $cart[$request->id]['type'] == 'physical') {
                if ($request->quantity > $book->quantity) {
                    
                    // Câu thông báo đúng chuẩn cậu muốn
                    $errorMsg = 'Sách "' . $book->title . '" trong kho không đủ! Chỉ còn tối đa ' . $book->quantity . ' cuốn.';
                    
                    // Nếu dùng AJAX (Javascript)
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'status' => 'error', 
                            'message' => $errorMsg
                        ], 422);
                    }
                    
                    // Nếu submit form bình thường
                    return redirect()->back()->with('error', $errorMsg);
                }
            }

            $cart[$request->id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
            
            $this->recheckCoupon(); 

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'success']);
            }
        }
        return redirect()->back()->with('success', 'Đã cập nhật giỏ hàng!');
    }
    public function remove(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            
            $this->recheckCoupon();

            session()->flash('success', 'Đã xóa sản phẩm!');
        }
        return redirect()->back();
    }

    private function recheckCoupon()
    {
        if (!session()->has('coupon')) return;

        $cart = session()->get('cart');
        $total = 0;
        if($cart) {
            foreach($cart as $item) $total += $item['price'] * $item['quantity'];
        } else {
            session()->forget('coupon');
            return;
        }

        $code = session('coupon')['code'];
        $voucher = \App\Models\Voucher::where('code', $code)->first();

        if ($voucher) {
            if ($total < $voucher->min_order_amount) {
                session()->forget('coupon');
                session()->flash('error', 'Mã giảm giá đã bị hủy do đơn hàng không đủ ' . number_format($voucher->min_order_amount) . 'đ');
                return;
            }

            $discountAmount = 0;
            if ($voucher->type == 'fixed') {
                $discountAmount = $voucher->value;
            } else {
                $discountAmount = $total * ($voucher->value / 100);
            }

            session()->put('coupon', [
                'code' => $voucher->code,
                'discount' => $discountAmount,
                'type' => $voucher->type,
                'value' => $voucher->value
            ]);
        }
    }

    public function applyCoupon(Request $request)
    {
        $code = $request->code;
        $voucher = \App\Models\Voucher::where('code', $code)->first();

        if (!$voucher) {
            return redirect()->back()->with('error', 'Mã giảm giá không tồn tại!');
        }

        if ($voucher->user_id && $voucher->user_id != auth()->id()) {
            return redirect()->back()->with('error', 'Mã giảm giá này không thuộc về bạn!');
        }

        if ($voucher->quantity <= 0) {
            return redirect()->back()->with('error', 'Mã này đã hết lượt sử dụng!');
        }

        $now = \Carbon\Carbon::now();
        if ($now < $voucher->start_date) {
            return redirect()->back()->with('error', 'Mã này chưa đến đợt áp dụng!');
        }
        if ($now > $voucher->end_date) {
            return redirect()->back()->with('error', 'Mã này đã hết hạn!');
        }

        $cart = session()->get('cart');
        if(!$cart) return redirect()->back()->with('error', 'Giỏ hàng trống!');
        
        $total = 0;
        foreach($cart as $item) $total += $item['price'] * $item['quantity'];

        if ($total < $voucher->min_order_amount) {
            return redirect()->back()->with('error', 'Đơn hàng phải từ ' . number_format($voucher->min_order_amount) . 'đ mới dùng được mã này!');
        }

        $discountAmount = 0;
        if ($voucher->type == 'fixed') {
            $discountAmount = $voucher->value;
        } else {
            $discountAmount = $total * ($voucher->value / 100);
        }

        session()->put('coupon', [
            'code' => $voucher->code,
            'discount' => $discountAmount,
            'type' => $voucher->type,
            'value' => $voucher->value
        ]);

        return redirect()->back()->with('success', 'Áp dụng mã giảm giá thành công!');
    }

    public function removeCoupon()
    {
        session()->forget('coupon');
        return redirect()->back()->with('success', 'Đã gỡ bỏ mã giảm giá!');
    }
}