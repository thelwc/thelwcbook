<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use App\Models\Order;
use App\Models\Publisher;
use App\Models\Voucher;
use App\Models\Post;
use App\Models\Banner;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 🔥 TRẠM PHÂN LUỒNG TỐI THƯỢNG ĐẶT Ở ĐÂY 🔥
        $role = Auth::user()->role;

        if ($role == 0) {
            // Admin IT -> Đá về Nhân sự
            return redirect()->route('users.index');
        } elseif ($role == 2 || $role == 3) {
            // Quản lý & Nhân viên -> Đá về Đơn hàng
            return redirect()->route('orders.index');
        } elseif ($role == 4) {
            // Kiểm duyệt viên -> Đá về Tin tức
            return redirect()->route('admin.posts.index');
        } elseif ($role == 5) {
            // Khách hàng -> Cút ra trang chủ
            return redirect('/');
        }
        // ==============================================================
        // 1. CÁC CHỈ SỐ CƠ BẢN (COUNTS)
        // ==============================================================
        $book_count = Book::count();
        $category_count = Category::count();
        $user_count = User::where('role', '!=', 0)->count();
        $order_count = Order::whereIn('status', ['pending', '0'])->count();

        $publisher_count = Publisher::count();
        $voucher_count = class_exists(Voucher::class) ? Voucher::count() : 0;
        $post_count = class_exists(Post::class) ? Post::count() : 0;
        $banner_count = class_exists(Banner::class) ? Banner::count() : 0;

        // ==============================================================
        // 2. SỐ LIỆU CHIẾN LƯỢC
        // ==============================================================

        // 🔥 A. TỔNG DOANH THU (Đã tách logic Ebook và Sách giấy) 🔥
        // - Ebook: Lấy từ đơn Đã xác nhận, Đang giao, Hoàn thành
        $ebookRevenue = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->where('order_details.type', 'ebook')
            ->whereIn('orders.status', ['confirmed', 1, 'shipping', 'completed', 3, 2])
            ->sum(DB::raw('order_details.price * order_details.quantity'));

        // - Sách giấy: CHỈ lấy từ đơn Hoàn thành
        $physicalRevenue = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->where('order_details.type', 'physical')
            ->whereIn('orders.status', ['completed', 3, 2])
            ->sum(DB::raw('order_details.price * order_details.quantity'));

        // - Phụ phí: Lấy phí ship và trừ đi giảm giá (chỉ tính cho đơn hoàn thành cho chắc cú)
        $extras = DB::table('orders')
            ->whereIn('status', ['completed', 3, 2])
            ->selectRaw('SUM(shipping_fee) as total_ship, SUM(discount) as total_discount')
            ->first();

        // Tổng chốt sổ (Đảm bảo không bị âm)
        $totalRevenue = max(0, $ebookRevenue + $physicalRevenue + ($extras->total_ship ?? 0) - ($extras->total_discount ?? 0));

        // B. Tổng số đơn hàng
        $totalOrders = Order::count();

        // C. Tổng khách hàng
        $totalUsers = \App\Models\User::where('role', 5)->count();

        // D. Sách sắp hết hàng
        $lowStockBooks = Book::where('quantity', '<', 10)
            ->orderBy('quantity', 'asc')
            ->limit(5)
            ->get();

        // E. Top 5 sách bán chạy (Đồng bộ số liệu từ bảng Books & Kèm tính Sao đánh giá)
        $topBooks = \App\Models\Book::orderBy('total_sold', 'desc')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->limit(5)
            ->get();

        // ==============================================================
        // 3. BIỂU ĐỒ DOANH THU (Đã đồng bộ logic Ebook và Sách giấy)
        // ==============================================================
        $startDate = Carbon::now()->subDays(6)->startOfDay();

        // Doanh thu Ebook theo ngày
        $dailyEbook = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->where('order_details.type', 'ebook')
            ->whereIn('orders.status', ['confirmed', 1, 'shipping', 'completed', 3, 2])
            ->where('orders.created_at', '>=', $startDate)
            ->select(DB::raw('DATE(orders.created_at) as date'), DB::raw('SUM(order_details.price * order_details.quantity) as total'))
            ->groupBy('date')
            ->get()->keyBy('date');

        // Doanh thu Sách giấy theo ngày
        $dailyPhysical = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->where('order_details.type', 'physical')
            ->whereIn('orders.status', ['completed', 3, 2])
            ->where('orders.created_at', '>=', $startDate)
            ->select(DB::raw('DATE(orders.created_at) as date'), DB::raw('SUM(order_details.price * order_details.quantity) as total'))
            ->groupBy('date')
            ->get()->keyBy('date');

        // Phí ship & Giảm giá theo ngày
        $dailyExtras = DB::table('orders')
            ->whereIn('status', ['completed', 3, 2])
            ->where('created_at', '>=', $startDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(shipping_fee - discount) as total'))
            ->groupBy('date')
            ->get()->keyBy('date');

        $labels = [];
        $data = [];

        // Gộp data của 7 ngày lại
        for ($i = 6; $i >= 0; $i--) {
            $dateStr = Carbon::now()->subDays($i)->format('Y-m-d');
            $displayDate = Carbon::now()->subDays($i)->format('d/m');

            $eRev = isset($dailyEbook[$dateStr]) ? $dailyEbook[$dateStr]->total : 0;
            $pRev = isset($dailyPhysical[$dateStr]) ? $dailyPhysical[$dateStr]->total : 0;
            $exRev = isset($dailyExtras[$dateStr]) ? $dailyExtras[$dateStr]->total : 0;

            $labels[] = $displayDate;
            $data[] = max(0, $eRev + $pRev + $exRev); // Cộng dồn các món lại
        }

        // ==============================================================
        // 4. BIỂU ĐỒ TRÒN (TRẠNG THÁI ĐƠN HÀNG)
        // ==============================================================
        $statusData = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        $statusCounts = [0, 0, 0, 0];

        foreach ($statusData as $item) {
            if ($item->status == 0 || $item->status == 'pending') {
                $statusCounts[0] += $item->total;
            } elseif ($item->status == 1 || $item->status == 'confirmed' || $item->status == 'shipping') {
                // Tớ gộp chung Đang giao vào phần Xác nhận trên biểu đồ để dễ nhìn
                $statusCounts[1] += $item->total;
            } elseif ($item->status == 2 || $item->status == 'completed') {
                $statusCounts[2] += $item->total;
            } elseif ($item->status == 'cancelled' || $item->status == 'bom_hang' || $item->status == 4) {
                $statusCounts[3] += $item->total;
            }
        }

        // ==============================================================
        // 5. TRẢ VỀ VIEW
        // ==============================================================
        return view('admin.dashboard', compact(
            'book_count',
            'category_count',
            'user_count',
            'order_count',
            'publisher_count',
            'voucher_count',
            'post_count',
            'banner_count',
            'labels',
            'data',
            'statusCounts',
            'totalRevenue',
            'totalOrders',
            'totalUsers',
            'lowStockBooks',
            'topBooks'
        ));
    }
}
