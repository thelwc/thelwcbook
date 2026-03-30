<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    // 1. Xem danh sách yêu thích
    public function index()
    {
        $favorites = Favorite::with('book')->where('user_id', Auth::id())->latest()->get();
        return view('client.account.favorites', compact('favorites'));
    }

    // 2. Thả tim / Bỏ tim (Toggle)
    public function toggle($book_id)
    {
        $user_id = Auth::id();
        
        // Kiểm tra xem đã tim chưa
        $favorite = Favorite::where('user_id', $user_id)->where('book_id', $book_id)->first();

        if ($favorite) {
            // Có rồi -> Xóa (Bỏ tim)
            $favorite->delete();
            return redirect()->back()->with('success', 'Đã xóa khỏi danh sách yêu thích!');
        } else {
            // Chưa có -> Tạo mới (Thả tim)
            Favorite::create([
                'user_id' => $user_id,
                'book_id' => $book_id
            ]);
            return redirect()->back()->with('success', 'Đã thêm vào danh sách yêu thích!');
        }
    }
}