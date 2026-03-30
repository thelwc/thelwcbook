<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Book;
use App\Models\Voucher;
use App\Models\Setting; // 🔥 Import thêm Model Setting

class CheckoutController extends Controller
{
    // 1. Hiện trang thanh toán
    public function index()
    {
        $cart = session()->get('cart');
        if (!$cart) {
            return redirect()->route('home')->with('error', 'Giỏ hàng trống, vui lòng chọn sách trước!');
        }
        
        // --- 1. KIỂM TRA CẦN SHIP KHÔNG & TÍNH TỔNG TIỀN HÀNG ---
        $needsShipping = false;
        $tongTienHang = 0;

        foreach ($cart as $item) {
            if (!isset($item['type']) || $item['type'] == 'physical') {
                $needsShipping = true;
            }
            // Cộng dồn tiền hàng
            $tongTienHang += $item['price'] * $item['quantity'];
        }

        // --- 2. LẤY CẤU HÌNH PHÍ SHIP TỪ DATABASE ---
        // (Dùng default 500k và 30k nếu lỡ admin chưa cài đặt)
        $mocFreeship = Setting::where('key', 'free_ship_threshold')->value('value') ?? 500000;
        $phiShipMacDinh = Setting::where('key', 'shipping_fee')->value('value') ?? 30000;

        // --- 3. TÍNH TIỀN SHIP ---
        $tienShip = 0;
        if ($needsShipping) {
            $tienShip = ($tongTienHang >= $mocFreeship) ? 0 : $phiShipMacDinh;
        }

        // --- 4. TÍNH VOUCHER VÀ TỔNG THANH TOÁN ---
        $discount = session()->has('coupon') ? session('coupon')['discount'] : 0;
        
        $tongThanhToan = $tongTienHang + $tienShip - $discount;
        if($tongThanhToan < 0) $tongThanhToan = 0;

        // Ném các biến này ra view checkout để hiển thị
        return view('client.pages.checkout', compact(
            'cart', 'needsShipping', 'tongTienHang', 'mocFreeship', 'phiShipMacDinh', 'tienShip', 'discount', 'tongThanhToan'
        ));
    }
}