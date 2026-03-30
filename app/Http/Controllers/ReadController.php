<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;

class ReadController extends Controller
{
    // 1. Hiển thị giao diện đọc
    public function read(Request $request, $id) // 🔥 Thêm Request $request
    {
        $book = Book::findOrFail($id);
        $user = Auth::user();

        // --- BƯỚC 1: KIỂM TRA QUYỀN ĐỌC FULL ---
        $isFullAccess = false; 
        
        // Chỉ kiểm tra quyền nếu KHÔNG PHẢI là chế độ xem thử
        if ($request->query('mode') !== 'preview' && $user) {
            // Admin (0), Giám đốc (1) hoặc Đã mua sách
            if ($user->role == 0 || $user->role == 1 || $user->booksOwned->contains($id)) {
                $isFullAccess = true;
            }
        }

        // --- BƯỚC 2: CHỌN GIAO DIỆN ---
        if (!empty($book->book_content)) {
            return view('client.books.read', compact('book', 'isFullAccess'));
        } 
        
        if (!empty($book->file_preview) || !empty($book->file_ebook)) {
            return view('client.books.readpdf', compact('book', 'isFullAccess'));
        }

        return back()->with('error', 'Sách này chưa có nội dung số để đọc.');
    }

    // 2. Stream nội dung file
    public function getFileContent(Request $request, $id) // 🔥 Thêm Request $request
    {
        $book = Book::findOrFail($id);
        $user = Auth::user();

        // --- CHECK QUYỀN LẠI ---
        $serveFullFile = false;

        // Nếu có tham số ?mode=preview -> Ép buộc KHÔNG cho xem full
        if ($request->query('mode') !== 'preview' && $user) {
            if ($user->role == 0 || $user->role == 1 || $user->booksOwned->contains($id)) {
                $serveFullFile = true;
            }
        }

        $path = null;

        // TRƯỜNG HỢP 1: ĐƯỢC XEM FULL -> Lấy file 'ebooks'
        if ($serveFullFile && !empty($book->file_ebook)) {
            $fullPath = public_path('uploads/ebooks/' . $book->file_ebook);
            if (file_exists($fullPath)) $path = $fullPath;
        }

        // TRƯỜNG HỢP 2: KHÔNG ĐƯỢC XEM FULL (Hoặc đang xem thử) -> Lấy file 'previews'
        if (!$path && !empty($book->file_preview)) {
            $previewPath = public_path('uploads/previews/' . $book->file_preview);
            if (file_exists($previewPath)) $path = $previewPath;
        }

        // Nếu tìm thấy file
        if ($path && file_exists($path)) {
            return response()->file($path, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($path) . '"'
            ]);
        }

        abort(404, 'File sách không tồn tại.');
    }
}