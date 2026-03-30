<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Order;
use App\Models\OrderDetail; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // =========================================================
    // 1. ADMIN & KIỂM DUYỆT: XEM DANH SÁCH (Hàm này đang thiếu)
    // =========================================================

    public function index(Request $request)
    {
        // 1. Khởi tạo query kèm theo user và book (tránh lỗi N+1 như cậu đã comment)
        $query = Review::with(['user', 'book']);

        // 2. Bắt đầu xử lý bộ lọc nút bấm
        if ($request->status == 'unreplied') {
            // Lọc ra mấy cái admin_reply đang trống (Chưa rep)
            $query->whereNull('admin_reply');
        } elseif ($request->status == 'replied') {
            // Lọc ra mấy cái đã có chữ trong admin_reply (Đã rep)
            $query->whereNotNull('admin_reply');
        }

        // 3. Sắp xếp theo 'updated_at' (Cực kỳ quan trọng: Ai mới gửi hoặc VỪA SỬA LẠI sẽ bị đẩy lên đầu)
        $reviews = $query->orderBy('updated_at', 'desc')->paginate(10);

        return view('admin.reviews.index', compact('reviews'));
    }

    // =========================================================
    // 2. KHÁCH HÀNG: GỬI ĐÁNH GIÁ (Logic xịn của cậu)
    // =========================================================
    public function store(Request $request)
    {
        // 1. Kiểm tra dữ liệu đầu vào
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $userId = Auth::id();
        $bookId = $request->book_id;

        // 2. 🔥 CHECK QUAN TRỌNG: Khách đã mua và NHẬN cuốn sách này chưa?
        $hasPurchased = \App\Models\Order::where('user_id', $userId)
            ->where('status', 'completed') // ĐÃ SỬA: Chặn luôn 'shipping', chỉ cho 'completed'
            ->whereHas('details', function ($query) use ($bookId) {
                $query->where('book_id', $bookId);
            })
            ->exists();

        if (!$hasPurchased) {
            return redirect()->back()->with('error', 'Bạn phải nhận hàng thành công mới được đánh giá sách nhé!');
        }

        // 3. 🔥 XỬ LÝ: "1 khách chỉ 1 bình luận" & Báo động cho Admin
        \App\Models\Review::updateOrCreate(
            ['user_id' => $userId, 'book_id' => $bookId], // Điều kiện tìm (Ai? Cuốn nào?)
            [
                'rating'      => $request->rating,
                'comment'     => $request->comment,
                'status'      => 'active',
                'admin_reply' => null, // 🔥 QUAN TRỌNG: Xóa câu rep cũ của Admin (nếu có)
                'updated_at'  => now() // 🔥 QUAN TRỌNG: Cập nhật thời gian để bế lên TOP 1
            ]
        );
        // 4. 🔥 TẠO BIẾN $savedReview ĐỂ CHUẨN BỊ GỬI THÔNG BÁO
        // Phải load kèm ('user', 'book') thì trong thông báo mới in ra được Tên khách và Tên sách
        $savedReview = \App\Models\Review::with(['user', 'book'])
                            ->where('user_id', $userId)
                            ->where('book_id', $bookId)
                            ->first();

        // 5. 🔥 BẮN THÔNG BÁO CHO QUẢN TRỊ VIÊN
        $admins = \App\Models\User::whereIn('role', [2, 3, 4])->get(); 
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\NewReviewNotify($savedReview));
        return redirect()->back()->with('success', 'Đánh giá của bạn đã được ghi nhận!');
    }

    // =========================================================
    // 3. ADMIN / KIỂM DUYỆT: XÓA ĐÁNH GIÁ VI PHẠM
    // =========================================================
    public function destroy($id)
    {
        // Lưu ý: Việc kiểm tra quyền (Admin/Manager/Moderator) 
        // đã được xử lý bởi Middleware trong routes/web.php rồi 
        // nên ở đây ta cứ xóa thoải mái.

        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->back()->with('success', 'Đã xóa bình luận vi phạm.');
    }
    public function reply(Request $request, $id)
    {
        $request->validate([
            'admin_reply' => 'required|string|max:1000'
        ]);

        $review = \App\Models\Review::findOrFail($id);
        $review->admin_reply = $request->admin_reply;
        $review->timestamps = false; 
        
        $review->save();
        // Bắn thông báo cho Khách hàng
        $review->user->notify(new \App\Notifications\AdminReplyNotify($review));
        return back()->with('success', 'Đã lưu phản hồi cho khách hàng!');
        return back()->with('success', 'Đã lưu phản hồi cho khách hàng!');
    }
    // 1. Hàm xóa Rep của Admin
    public function deleteReply($id)
    {
        $review = \App\Models\Review::findOrFail($id);
        $review->admin_reply = null;
        $review->timestamps = false; // 🔥 Đóng băng thời gian để không làm mất mác của khách
        $review->save();

        return back()->with('success', 'Đã xóa phản hồi của Admin!');
    }

    // 2. Hàm Ẩn / Hiện bình luận của khách
    public function toggleStatus($id)
    {
        
        $review = \App\Models\Review::findOrFail($id);
        // Đang ẩn thì thành active, đang active thì thành hidden
        $review->status = ($review->status == 'hidden') ? 'active' : 'hidden';
        $review->timestamps = false; // 🔥 Đóng băng thời gian luôn
        $review->save();

        $msg = $review->status == 'hidden' ? 'Đã ẨN bình luận của khách!' : 'Đã HIỂN THỊ lại bình luận!';
        return back()->with('success', $msg);
    }
}