<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Book;
use App\Models\User; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\NewOrderNotification; 
use App\Notifications\OrderStatusNotification; 
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderPlaced;
use App\Models\Voucher;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    // ==========================================
    // PHẦN 1: KHÁCH HÀNG (MUA HÀNG)
    // ==========================================

    public function checkout()
    {
        $cart = session()->get('cart');
        if(!$cart) {
            return redirect()->route('home')->with('error', 'Giỏ hàng trống!');
        }
        return view('client.pages.checkout', compact('cart'));
    }

    // 2. Xử lý lưu đơn hàng
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required', 
            'email' => 'required|email', 
            'payment_method' => 'required|in:cod,bank_transfer',
        ]);

        $cart = session()->get('cart');
        if(!$cart) return redirect()->route('home')->with('error', 'Giỏ hàng trống!');
        
        $subtotal = 0;
        $needsShipping = false;
        foreach($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
            if (!isset($item['type']) || $item['type'] == 'physical') {
                $needsShipping = true;
            }
        }

        $shippingFee = \App\Models\Setting::calculateShipping($subtotal, $needsShipping);
        $discountAmount = session()->get('coupon')['discount'] ?? 0;
        $couponCode = session()->get('coupon')['code'] ?? null;
        $finalTotal = max(0, $subtotal + $shippingFee - $discountAmount);

        DB::beginTransaction();
        try {
            $order = Order::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'note' => $request->note,
                'payment_method' => $request->payment_method,
                'total_price' => $finalTotal,
                'discount' => $discountAmount,
                'coupon_code' => $couponCode,
                'shipping_fee' => $shippingFee,
                'status' => 'pending', 
                'user_id' => auth()->id()
            ]);

            foreach($cart as $key => $item) {
                $bookId = $item['id'] ?? intval(explode('_', $key)[0]); 
                
                OrderDetail::create([
                    'order_id' => $order->id,
                    'book_id' => $bookId, 
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'type' => $item['type'] ?? 'physical',
                ]);

                $book = Book::find($bookId);
                if($book) {
                    // 🔥 SỬA LỖI: Đã xóa dòng cộng total_sold ở đây. Sẽ chỉ cộng khi giao thành công!
                    
                    // Trừ kho ngay lập tức để giữ chỗ (Chỉ dành cho sách giấy)
                    if(!isset($item['type']) || $item['type'] == 'physical') {
                        $book->decrement('quantity', $item['quantity']);
                    }
                }
            }

            if ($couponCode) {
                $voucher = Voucher::where('code', $couponCode)->first();
                if($voucher) {
                    if($voucher->quantity <= 1) $voucher->delete();
                    else $voucher->decrement('quantity');
                }
            }

            DB::commit();
            $this->sendNotifications($order, $request->email);
            session()->forget(['cart', 'coupon']);

            return redirect()->route('orders.success', ['id' => $order->id])
                             ->with('success', 'Đặt hàng thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Lỗi đặt hàng: " . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi nè: ' . $e->getMessage());
        }
    }


    // ==========================================
    // PHẦN 2: ADMIN & QUẢN LÝ
    // ==========================================

    public function index(Request $request)
    {
        $query = Order::orderBy('created_at', 'desc');

        // 1. Lọc theo TRẠNG THÁI
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // 2. Lọc theo PHƯƠNG THỨC THANH TOÁN
        if ($request->has('payment_method') && $request->payment_method != 'all') {
            if ($request->payment_method == 'cod') {
                $query->where(function($q) {
                    $q->where('payment_method', 'COD')->orWhere('payment_method', 'cod');
                });
            } elseif ($request->payment_method == 'banking') {
                $query->where(function($q) {
                    $q->where('payment_method', '!=', 'COD')->where('payment_method', '!=', 'cod');
                });
            }
        }

        // 3. TÌM KIẾM TỪ KHÓA
        if ($request->has('keyword') && $request->keyword != '') {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('id', $keyword)
                  ->orWhere('name', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    // XỬ LÝ BOM HÀNG (DÀNH CHO ADMIN)
    public function markAsBomHang($id)
    {
        $order = \App\Models\Order::with('details')->findOrFail($id);

        if (in_array($order->status, ['completed', 'bom_hang', 'cancelled'])) {
            return back()->with('error', 'Đơn hàng này không thể xử lý bom hàng được nữa!');
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            foreach($order->details as $detail) {
                $book = \App\Models\Book::find($detail->book_id);
                if($book) {
                    $isEbook = $detail->type == 'ebook'; // 🔥 Lọc Ebook bằng type
                    if (!$isEbook) {
                        $book->increment('quantity', $detail->quantity);
                    } 
                }
            }

            if ($order->coupon_code) {
                $voucher = \App\Models\Voucher::where('code', $order->coupon_code)->first();
                if ($voucher) { $voucher->increment('quantity'); }
            }

            $order->status = 'bom_hang'; 
            $order->save();

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', 'Đã báo Bom hàng! Sách giấy đã được hoàn kho (Ebook vẫn giữ nguyên cho khách).');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }
    public function show($id)
    {
        // ❌ Đã bỏ 'details.branch'
        $order = Order::with('details.book')->findOrFail($id);
        
        if(auth()->check()) {
            auth()->user()->unreadNotifications->where('data.link', route('orders.show', $id))->markAsRead();
        }

        return view('admin.orders.show', compact('order'));
    }

    // --- CÁC HÀM CHO KHÁCH HÀNG ---
    
    public function history()
    {
        $orders = Order::where('user_id', Auth::id())
                       ->orderBy('created_at', 'desc')
                       ->paginate(10);
        return view('client.account.history', compact('orders'));
    }

    public function historyDetail($id)
    {
        $order = Order::with('details.book')
                      ->where('user_id', Auth::id())
                      ->where('id', $id)
                      ->firstOrFail();
                      
        if(auth()->check()) {
            auth()->user()->unreadNotifications->where('data.link', route('client.account.history.detail', $id))->markAsRead();
        }

        return view('client.account.history_detail', compact('order'));
    }

    
    // =========================================================
    // 5. CẬP NHẬT TRẠNG THÁI (TÁCH RIÊNG LOGIC EBOOK VÀ SÁCH GIẤY)
    // =========================================================
    public function updateStatus(Request $request, $id)
    {
        $order = Order::with(['details.book', 'user'])->findOrFail($id);
        $newStatus = $request->status;
        $oldStatus = $order->status;

        // 1. XÁC NHẬN ĐƠN HÀNG (PENDING -> CONFIRMED)
        if (($oldStatus == 'pending' || $oldStatus == 0) && in_array($newStatus, ['confirmed', 1, 'shipping', 'completed', 3, 2])) {
            foreach ($order->details as $detail) {
                $isEbook = $detail->type == 'ebook'; // 🔥 Lọc siêu sạch!
                
                if ($isEbook) {
                    if ($order->user_id && $order->user) {
                        $order->user->booksOwned()->syncWithoutDetaching([$detail->book_id]);
                    }
                    $detail->book->increment('total_sold', $detail->quantity);
                    $detail->book->increment('ebook_sold', $detail->quantity);
                }
            }
        }
        
        // 2. GIAO THÀNH CÔNG (SHIPPING -> COMPLETED)
        if (($oldStatus != 'completed' && $oldStatus != 3) && ($newStatus == 'completed' || $newStatus == 3)) {
            foreach ($order->details as $detail) {
                $isEbook = $detail->type == 'ebook';
                
                if (!$isEbook) {
                    $detail->book->increment('total_sold', $detail->quantity);
                }
            }
        }

        // 3. UNDO HOÀN THÀNH VỀ ĐANG GIAO
        if (($oldStatus == 'completed' || $oldStatus == 3) && ($newStatus != 'completed' && $newStatus != 3)) {
            foreach ($order->details as $detail) {
                $isEbook = $detail->type == 'ebook';
                if (!$isEbook) {
                    $detail->book->decrement('total_sold', $detail->quantity);
                }
            }
        }

        $order->status = $newStatus;
        $order->save();

        if ($order->user) {
            try { $order->user->notifyNow(new OrderStatusNotification($order)); } catch (\Exception $e) {}
        }
        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công!');
    }

    public function markAsOutOfStock($id)
    {
        $order = Order::with('details.book')->findOrFail($id);

        if ($order->status == 'pending' || $order->status == 0) {
            foreach ($order->details as $detail) {
                $isEbook = $detail->type == 'ebook';
                if (!$isEbook && $detail->book) {
                    $detail->book->increment('quantity', $detail->quantity);
                }
            }
            if ($order->coupon_code) {
                Voucher::where('code', $order->coupon_code)->increment('quantity');
            }

            $order->status = 'cancelled';
            $order->note = "Đơn hàng bị hủy do sản phẩm đã hết hàng.";
            $order->save();

            if ($order->user) {
                try {
                    \Illuminate\Support\Facades\Mail::raw('Xin lỗi...', function ($message) use ($order) {
                        $message->to($order->user->email)->subject('Thông báo hủy đơn hàng #' . $order->id);
                    });
                    $order->user->notifyNow(new OrderStatusNotification($order));
                } catch (\Exception $e) {}
            }
            return redirect()->back()->with('success', 'Đã hủy đơn, hoàn lại kho và báo hết hàng!');
        }
        return redirect()->back()->with('error', 'Không thể thao tác trên đơn hàng này.');
    }

    // =========================================================
    // KHÁCH TỰ HỦY ĐƠN
    // =========================================================
    public function cancel($id)
    {
        $order = Order::with('details.book')->findOrFail($id);

        if ($order->user_id != auth()->id()) {
            return back()->with('error', 'Bạn không có quyền hủy đơn này.');
        }

        if ($order->status == 0 || $order->status == 'pending') {
            foreach ($order->details as $detail) {
                $isEbook = $detail->type == 'ebook';
                if (!$isEbook && $detail->book) {
                    $detail->book->increment('quantity', $detail->quantity);
                }
            }
            if ($order->coupon_code) {
                Voucher::where('code', $order->coupon_code)->increment('quantity');
            }

            $order->status = ($order->status == 'pending') ? 'cancelled' : 2;
            $order->save();
            return redirect()->route('client.account.history.detail', $id)->with('success', 'Đã hủy đơn hàng thành công, kho đã được cập nhật lại!');
        }

        return back()->with('error', 'Đơn hàng đã được xử lý, không thể hủy lúc này.');
    }

    public function success($id) {
        $order = \App\Models\Order::findOrFail($id);
        return view('client.pages.success', compact('order'));
    }

    public function printInvoice($id)
    {
        $order = Order::with('details.book')->findOrFail($id);
        
        $pdf = Pdf::loadView('admin.orders.invoice', compact('order'));
        return $pdf->download('hoadon-' . $order->id . '.pdf');
    }
    /**
 * Hàm hỗ trợ gửi thông báo và Email (Gom nhóm cho sạch code)
 */
private function sendNotifications($order, $customerEmail)
{
    try {
        // 1. Gửi thông báo cho Admin (Role 0, 1, 2, 3)
        $admins = \App\Models\User::whereIn('role', [0, 1, 2, 3])->get();
        foreach ($admins as $admin) {
            $admin->notifyNow(new \App\Notifications\NewOrderNotification($order));
        }

        // 2. Gửi email cho khách hàng
        if ($customerEmail) {
            \Illuminate\Support\Facades\Mail::to($customerEmail)
                ->send(new \App\Mail\OrderPlaced($order));
        }
    } catch (\Exception $e) {
        // Nếu lỗi gửi mail/thông báo thì ghi log chứ không làm dừng quá trình đặt hàng
        \Log::error("Lỗi gửi thông báo/mail: " . $e->getMessage());
    }
}
}