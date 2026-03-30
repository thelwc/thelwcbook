<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Str;

class PostController extends Controller
{
    // ==========================================
    // 1. PHẦN CHO KHÁCH HÀNG (PUBLIC)
    // ==========================================
    
    // Trang danh sách tin tức
    public function index() {
        $posts = Post::where('status', 1)->orderBy('created_at', 'desc')->paginate(9);
        return view('client.posts.index', compact('posts'));
    }

    // Trang đọc bài viết
    public function show($id) {
        $post = Post::where('status', 1)->findOrFail($id);
        $post->increment('views'); // Tăng view
        
        // Bài viết liên quan
        $relatedPosts = Post::where('status', 1)->where('id', '!=', $id)
                            ->orderBy('created_at', 'desc')->take(3)->get();

        return view('client.posts.show', compact('post', 'relatedPosts'));
    }

    // ==========================================
    // 2. PHẦN CHO ADMIN (QUẢN TRỊ)
    // ==========================================

    // Form viết bài mới
    public function create() {
        return view('admin.posts.create');
    }

    // Lưu bài viết vào DB
    public function store(Request $request) {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'thumbnail' => 'required|image'
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->title) . '-' . time();
        $data['user_id'] = auth()->id();
        $data['views'] = 0;

        // Upload ảnh bìa
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/posts'), $filename);
            $data['thumbnail'] = 'uploads/posts/' . $filename;
        }

        Post::create($data);

        return redirect()->route('admin.posts.index')->with('success', 'Đăng bài thành công!');
    }
    // --- ADMIN: Danh sách bài viết ---
    public function adminIndex() {
        $posts = Post::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.posts.index', compact('posts'));
    }

    // --- ADMIN: Xóa bài viết ---
    public function destroy($id) {
        $post = Post::findOrFail($id);
        // Xóa ảnh cũ nếu có (Tuỳ chọn, làm sau cũng được)
        $post->delete();
        return back()->with('success', 'Đã xóa bài viết!');
    }

    // --- ADMIN: Hiển thị form sửa ---
    public function edit($id) {
        $post = Post::findOrFail($id);
        return view('admin.posts.edit', compact('post'));
    }

    // --- ADMIN: Xử lý cập nhật ---
    public function update(Request $request, $id) {
        $post = Post::findOrFail($id);
        
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'thumbnail' => 'nullable|image' // Cho phép để trống nếu không đổi ảnh
        ]);

        $data = $request->all();
        // Cập nhật slug theo tiêu đề mới
        $data['slug'] = Str::slug($request->title) . '-' . $post->id;

        // Xử lý ảnh mới (nếu có upload)
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/posts'), $filename);
            $data['thumbnail'] = 'uploads/posts/' . $filename;
        }

        $post->update($data);

        return redirect()->route('admin.posts.index')->with('success', 'Đã cập nhật bài viết!');
    }
    // Xử lý Ẩn/Hiện bài viết
    public function toggleStatus($id)
    {
        $post = \App\Models\Post::findOrFail($id);
        
        // SỬA: Dùng số 1 (Hiện) và 0 (Ẩn)
        // Nếu đang là 1 thì chuyển thành 0, ngược lại thành 1
        $newStatus = ($post->status == 1) ? 0 : 1;
        
        $post->status = $newStatus;
        $post->save();

        // Thông báo
        $msg = $newStatus == 1 ? 'Đã hiển thị bài viết!' : 'Đã ẩn bài viết!';
        return redirect()->back()->with('success', $msg);
    }
}