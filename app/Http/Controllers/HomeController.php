<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Nhớ thêm dòng này ở đầu file

class HomeController extends Controller
{
    public function index()
    {

        // 1. Lấy Banner (Mới thêm)
        $banners = \App\Models\Banner::where('status', 'active')
            ->orderBy('order', 'asc')
            ->latest()
            ->get();
        // 1. DEAL SỐC (ĐÃ SỬA: Xếp theo mức giảm giá % cao nhất) 🔥
        $saleBooks = Book::where('sale_price', '>', 0) // Chỉ lấy giá > 0
            ->whereColumn('sale_price', '<', 'price') // Giá sale phải nhỏ hơn giá gốc
            ->orderByRaw('((price - sale_price) / price) DESC') // Sắp xếp giảm giá sâu nhất lên đầu
            ->take(10)
            ->get();

        // 2. SÁCH BÁN CHẠY (ĐÃ SỬA: Lấy kèm số lượng đã bán "total_sold") 🔥
        // Bước A: Lấy Top 10 ID sách và tổng số lượng bán của nó từ bảng order_details
        // 1. SÁCH BÁN CHẠY (Đồng bộ 100% số liệu từ DB & Load sẵn Đánh giá sao)
        $bestSellingBooks = \App\Models\Book::orderBy('total_sold', 'desc')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->take(10)
            ->get();

        // 2. KHO EBOOK (Load sẵn Đánh giá sao)
        $ebooks = \App\Models\Book::where('ebook_price', '>', 0)
            ->orderBy('created_at', 'desc')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->take(10)
            ->get();

        // 3. SÁCH MỚI PHÁT HÀNH
        $dateLimit = \Carbon\Carbon::now()->subDays(7);
        $newBooks = \App\Models\Book::where('created_at', '>=', $dateLimit)
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->latest()
            ->take(10)
            ->get();

        // 4. TOÀN BỘ GIAN HÀNG (Hiển thị 18 cuốn ngẫu nhiên)
        $allBooks = \App\Models\Book::inRandomOrder()
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->limit(20)
            ->get();

        // 5. BÀI VIẾT MỚI NHẤT
        $latestPosts = \App\Models\Post::orderBy('created_at', 'desc')->take(3)->get();

        // 6. DEAL SỐC (NẾU CÓ - Dựa theo code cũ của cậu)
        // Đừng quên thêm ->withAvg('reviews', 'rating')->withCount('reviews') vào $saleBooks nếu cậu khai báo nó ở trên nhé!

        return view('client.pages.home', compact('banners', 'saleBooks', 'bestSellingBooks', 'newBooks', 'allBooks', 'ebooks', 'latestPosts'));
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        // Bắt đầu khởi tạo câu lệnh tìm kiếm
        $query = \App\Models\Book::query();

        // ========================================================
        // 1. TÌM KIẾM TỪ KHÓA (Giữ nguyên logic cũ của cậu nhưng bọc lại)
        // ========================================================
        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%$keyword%")
                    ->orWhere('author', 'like', "%$keyword%")
                    ->orWhere('published_date', 'like', "%$keyword%")
                    ->orWhere('cover_type', 'like', "%$keyword%")
                    ->orWhereHas('publisher', function ($pubQuery) use ($keyword) {
                        $pubQuery->where('name', 'like', "%$keyword%");
                    })
                    ->orWhereHas('category', function ($catQuery) use ($keyword) {
                        $catQuery->where('name', 'like', "%$keyword%");
                    });
            });
        }

        // ========================================================
        // 2. LỌC NÂNG CAO (Các điều kiện mới cô giáo yêu cầu)
        // ========================================================

        // Lọc theo khoảng giá (Nếu có giá Sale thì ưu tiên tính giá Sale, không thì tính giá gốc)
        if ($request->filled('min_price')) {
            $query->whereRaw('IF(sale_price > 0, sale_price, price) >= ?', [$request->min_price]);
        }
        if ($request->filled('max_price')) {
            $query->whereRaw('IF(sale_price > 0, sale_price, price) <= ?', [$request->max_price]);
        }

        // Lọc theo Thể loại sách
        if ($request->filled('category_id')) {
            $query->whereIn('category_id', $request->category_id);
        }

        // Lọc theo Loại bìa (Cứng / Mềm)
        if ($request->filled('cover_type')) {
            $query->whereIn('cover_type', $request->cover_type);
        }

        // Lọc theo Nguồn gốc (Trong nước / Nước ngoài)
        if ($request->filled('is_foreign')) {
            $query->whereIn('is_foreign', $request->is_foreign);
        }

        // ========================================================
        // 3. XUẤT KẾT QUẢ VÀ CHUYỂN TRANG
        // ========================================================

        // Nối thêm appends($request->query()) để khi khách qua trang 2, trang 3 nó không bị mất điều kiện lọc
        $books = $query->paginate(20)->appends($request->query());

        return view('client.pages.search_results', compact('books', 'keyword'));
    }
    // Nhớ nhận vào biến $slug
    public function categoryBook($slug)
    {
        // 1. Tìm danh mục dựa vào cột 'slug'
        // firstOrFail nghĩa là: Tìm không thấy slug này thì báo lỗi 404 luôn
        $category = \App\Models\Category::where('slug', $slug)->firstOrFail();

        // 2. Lấy sách thuộc danh mục đó
        $books = $category->books()->paginate(20);

        // 3. Trả về view cũ
        return view('client.pages.category', compact('category', 'books'));
    }
    // Hàm xem chi tiết 1 quyển sách
    public function detail($id)
    {
        $userId = auth()->id(); // Lấy ID người đang đăng nhập (nếu có)

        // 1. Lấy sách & review (Lọc: Trạng thái không bị ẩn HOẶC review đó là của chính user đang xem)
        $book = Book::with(['category', 'reviews' => function ($query) use ($userId) {
            $query->where(function ($q) use ($userId) {
                $q->where('status', '!=', 'hidden'); // Lấy các cmt bình thường

                if ($userId) {
                    $q->orWhere(function ($subQ) use ($userId) {
                        $subQ->where('status', 'hidden')->where('user_id', $userId); // Cmt bị ẩn của chính user đó
                    });
                }
            })->with('user');
        }])->findOrFail($id);

        // 2. Tính điểm đánh giá (VẪN GIỮ NGUYÊN BỘ LỌC CHẶT CHẼ: Điểm trung bình tuyệt đối không tính cmt bị ẩn)
        $avgRating = round($book->reviews()->where('status', '!=', 'hidden')->avg('rating') ?? 0, 1);
        $totalReviews = $book->reviews()->where('status', '!=', 'hidden')->count();

        // 3. Kiểm tra quyền đánh giá (Đã tối ưu điều kiện)
        $canReview = false;
        if (auth()->check()) {
            $canReview = \App\Models\Order::where('user_id', auth()->id())
                ->where('status', 'completed') // Chỉ cho phép đơn hàng đã hoàn thành mới được đánh giá
                ->whereHas('details', function ($q) use ($id) {
                    $q->where('book_id', $id);
                })
                ->exists();
        }

        // 4. 🔥 THUẬT TOÁN SÁCH LIÊN QUAN (CHẤM ĐIỂM ƯU TIÊN) 🔥
        // Lấy năm xuất bản (nếu không có thì để 0000)
        $publishedYear = $book->published_date ? date('Y', strtotime($book->published_date)) : '0000';

        $relatedBooks = \App\Models\Book::where('id', '!=', $id) // Bỏ qua cuốn đang xem
            ->where('quantity', '>', 0) // Tùy chọn: Ưu tiên sách còn hàng
            ->select('*')
            ->selectRaw('
                ((CASE WHEN author = ? THEN 50 ELSE 0 END) +
                 (CASE WHEN category_id = ? THEN 40 ELSE 0 END) +
                 (CASE WHEN publisher_id = ? THEN 30 ELSE 0 END) +
                 (CASE WHEN YEAR(published_date) = ? THEN 20 ELSE 0 END) +
                 (CASE WHEN cover_type = ? THEN 10 ELSE 0 END)) AS relevance_score
            ', [
                $book->author,
                $book->category_id,
                $book->publisher_id,
                $publishedYear,
                $book->cover_type
            ])
            ->orderByDesc('relevance_score') // Ưu tiên điểm cao xếp trước
            ->inRandomOrder() // Bằng điểm hoặc 0 điểm thì lấy ngẫu nhiên
            ->take(10) // Lấy 10 cuốn
            ->get();

        // 5. Trả về đúng 1 View duy nhất
        return view('client.pages.detail', compact('book', 'relatedBooks', 'canReview', 'avgRating', 'totalReviews'));
    }
    public function deleteAvatar()
    {
        $user = auth()->user();

        // 1. Nếu đang có ảnh và ảnh đó KHÔNG PHẢI là link Google (không chứa http) -> Xóa file trong máy
        if ($user->avatar && !str_contains($user->avatar, 'http')) {
            Storage::delete('public/' . $user->avatar); // Xóa file vật lý
        }

        // 2. Cập nhật database về null
        $user->avatar = null;
        $user->save();

        return back()->with('success', 'Đã xóa ảnh đại diện. Ảnh mặc định sẽ được hiển thị.');
    }
    public function updateProfile(Request $request)
    {
        // 1. Validate dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            // 20MB = 20480 KB
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480',
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'avatar.max' => 'Ảnh quá lớn! Vui lòng chọn ảnh dưới 20MB.',
            'avatar.image' => 'File tải lên phải là hình ảnh.',
        ]);

        $user = Auth::user();

        // 2. Xử lý Upload Avatar (Nếu có chọn ảnh mới)
        if ($request->hasFile('avatar')) {

            // A. Xóa ảnh cũ (nếu có và không phải link Google)
            if ($user->avatar && !str_contains($user->avatar, 'http')) {
                // Xóa file cũ trong thư mục storage/app/public/avatars
                if (Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
            }

            // B. Lưu ảnh mới
            // File sẽ được lưu vào: storage/app/public/avatars
            $path = $request->file('avatar')->store('avatars', 'public');

            // C. Cập nhật đường dẫn vào User
            $user->avatar = $path;
        }

        // 3. Cập nhật các thông tin khác
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->address = $request->address;

        $user->save();

        return redirect()->back()->with('success', 'Cập nhật hồ sơ thành công!');
    }
    public function myBooks()
    {
        // Kiểm tra xem người dùng đã đăng nhập chưa (để chắc chắn)
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem tủ sách.');
        }

        // Lấy danh sách sách mà user đang sở hữu (qua quan hệ booksOwned)
        // Sắp xếp theo thời gian mua mới nhất
        $books = auth()->user()->booksOwned()->orderBy('created_at', 'desc')->get();

        // Trả về View mà tớ vừa đưa cho cậu ở trên
        return view('client.account.my_books', compact('books'));
    }
    public function ebooks(Request $request)
    {
        // Lọc chỉ lấy sách có bản Ebook
        $query = \App\Models\Book::where('ebook_price', '>', 0)
            ->whereNotNull('file_ebook');

        // Tích hợp tìm kiếm nếu có (Form lọc)
        if ($request->filled('keyword')) {
            $k = $request->keyword;
            $query->where('title', 'like', "%$k%");
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Sắp xếp
        if ($request->sort == 'price_asc') $query->orderBy('ebook_price', 'asc');
        elseif ($request->sort == 'price_desc') $query->orderBy('ebook_price', 'desc');
        else $query->orderBy('created_at', 'desc'); // Mặc định mới nhất

        $books = $query->paginate(20)->withQueryString();
        $categories = \App\Models\Category::all(); // Để hiện lọc danh mục

        return view('client.pages.ebooks_all', compact('books', 'categories'));
    }

    public function readNotification($id)
    {
        // 1. Tìm cái thông báo khách vừa click
        $notification = auth()->user()->notifications()->find($id);

        if ($notification) {
            // 2. Đánh dấu đã đọc (Nó sẽ tự động điền thời gian vào cột read_at trong Database)
            $notification->markAsRead();

            // 3. Đá khách sang cái link thật sự lưu bên trong thông báo
            return redirect($notification->data['link'] ?? url('/'));
        }

        return back();
    }
    // Hàm xử lý tìm kiếm trực tiếp bằng AJAX
    // Hàm xử lý tìm kiếm trực tiếp bằng AJAX (Đã độ lại Ưu Tiên & Bỏ Giới Hạn)
    public function ajaxSearch(Request $request)
    {
        $keyword = $request->keyword;

        if (empty($keyword)) {
            return response()->json([]);
        }

        // 🔥 QUERY THẦN THÁNH: Kết hợp JOIN bảng và ORDER BY CASE 🔥
        $books = \App\Models\Book::with(['publisher', 'category']) // Load kèm dữ liệu để JS in ra
            ->select('books.*') // Bắt buộc phải có dòng này để ID sách không bị đè bởi ID danh mục
            ->leftJoin('categories', 'books.category_id', '=', 'categories.id')
            ->leftJoin('publishers', 'books.publisher_id', '=', 'publishers.id')
            ->where(function ($query) use ($keyword) {
                // TÌM Ở CẢ 4 NƠI
                $query->where('books.title', 'like', "%{$keyword}%")
                    ->orWhere('books.author', 'like', "%{$keyword}%")
                    ->orWhere('categories.name', 'like', "%{$keyword}%")
                    ->orWhere('publishers.name', 'like', "%{$keyword}%");
            })
            // 👑 THUẬT TOÁN SẮP XẾP ƯU TIÊN CHUẨN SHOPEE 👑
            ->orderByRaw("
                CASE 
                    WHEN books.title LIKE ? THEN 1    /* Ưu tiên 1: Trùng Tên sách */
                    WHEN books.author LIKE ? THEN 2   /* Ưu tiên 2: Trùng Tác giả */
                    WHEN categories.name LIKE ? THEN 3 /* Ưu tiên 3: Trùng Thể loại */
                    WHEN publishers.name LIKE ? THEN 4 /* Ưu tiên 4: Trùng NXB */
                    ELSE 5 
                END ASC
            ", ["%{$keyword}%", "%{$keyword}%", "%{$keyword}%", "%{$keyword}%"])
            ->get(); // 🔥 Đã xóa ->limit(5), bốc sạch sẽ kho sách ra luôn!

        return response()->json($books);
    }
}
