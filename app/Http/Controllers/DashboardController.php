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
            ->limit(7)
            ->get();
            $totalLowStockBooks = Book::where('quantity', '<', 10)->count();

        // E. Top 5 sách bán chạy (Đồng bộ số liệu từ bảng Books & Kèm tính Sao đánh giá)
        $topBooks = \App\Models\Book::orderBy('total_sold', 'desc')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->limit(5)
            ->get();

        // ==============================================================
        // 3. BIỂU ĐỒ DOANH THU (Nâng lên 30 ngày)
        // ==============================================================
        $days = 30; // Thay vì 7 ngày, nâng lên 30 ngày cho biểu đồ đẹp mắt và tổng quan hơn
        $startDate = Carbon::now()->subDays($days - 1)->startOfDay();

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

        // Gộp data của 30 ngày lại
        for ($i = $days - 1; $i >= 0; $i--) {
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

        $statusCounts = [0, 0, 0, 0, 0]; // Đổi thành 5 phần tử

        foreach ($statusData as $item) {
            if ($item->status == 0 || $item->status == 'pending') {
                $statusCounts[0] += $item->total; // Chờ xử lý
            } elseif ($item->status == 1 || $item->status == 'confirmed' || $item->status == 'shipping') {
                $statusCounts[1] += $item->total; // Đã xác nhận / Đang giao
            } elseif ($item->status == 2 || $item->status == 'completed') {
                $statusCounts[2] += $item->total; // Thành công
            } elseif ($item->status == 'cancelled' || $item->status == 3) {
                $statusCounts[3] += $item->total; // Đã hủy (Khách tự hủy)
            } elseif ($item->status == 'bom_hang' || $item->status == 4) {
                $statusCounts[4] += $item->total; // Bom hàng (Từ chối nhận)
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
            'totalLowStockBooks',
            'topBooks'
        ));
    }
    // ==============================================================
    // 6. XEM CHI TIẾT DOANH THU LỌC THEO NGÀY
    // ==============================================================
    public function revenueDetails(Request $request)
    {
        // Kiểm tra quyền (chỉ Giám đốc hoặc Admin mới được xem)
        $role = Auth::user()->role;
        if (!in_array($role, [0, 1])) { // Giả sử 1 là Giám đốc
            return redirect()->back()->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        // Lấy ngày lọc từ request (Mặc định là tháng hiện tại)
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Lấy danh sách các đơn hàng đã Hoàn Thành trong khoảng thời gian này
        $orders = Order::whereIn('status', ['completed', '2', '3'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->orderBy('created_at', 'desc')
            ->paginate(20); // Phân trang cho đẹp

        // Tính tổng tiền của khoảng thời gian này
        $totalFilteredRevenue = Order::whereIn('status', ['completed', '2', '3'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->sum('total_price');

        return view('admin.revenue.details', compact('orders', 'startDate', 'endDate', 'totalFilteredRevenue'));
    }

    // ==============================================================
    // 7. XUẤT FILE EXCEL BÁO CÁO DOANH THU
    // ==============================================================
    public function exportExcel(Request $request)
    {
        // Nhận dữ liệu ngày tháng (Có thể rỗng nếu bấm từ ngoài Dashboard)
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Đặt tên file thông minh theo trường hợp
        if ($startDate && $endDate) {
            $fileName = 'Bao_Cao_Doanh_Thu_Thelwc_' . $startDate . '_den_' . $endDate . '.xlsx';
        } else {
            $fileName = 'Bao_Cao_Doanh_Thu_Thelwc_Toan_Bo.xlsx';
        }

        // Gọi class Export
        return \Excel::download(new \App\Exports\RevenueExport($startDate, $endDate), $fileName);
    }
    // ==============================================================
    // 8. XEM DANH SÁCH TẤT CẢ SÁCH SẮP HẾT HÀNG
    // ==============================================================
    public function urgentBooks()
    {
        // Lấy sách < 10 cuốn, ưu tiên thằng nào ít nhất lên đầu và phân trang 15 cuốn/trang
        $books = Book::where('quantity', '<', 10)
                     ->orderBy('quantity', 'asc')
                     ->paginate(20);

        // Trả về một view mới (cậu nhớ tạo file view này nhé)
        return view('admin.books.urgent_list', compact('books'));
    }
    // ==============================================================
    // 9. XUẤT EXCEL DANH SÁCH SÁCH SẮP HẾT HÀNG
    // ==============================================================
    public function exportUrgentBooks()
    {
        $fileName = 'Danh_Sach_Can_Nhap_Gap_' . date('Y_m_d_H_i') . '.xlsx';
        return \Excel::download(new \App\Exports\UrgentBooksExport, $fileName);
    }
}
