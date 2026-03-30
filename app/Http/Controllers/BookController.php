<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use App\Exports\BooksExport;
use App\Imports\BooksImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;

class BookController extends Controller
{
    // --- 1. HIỂN THỊ DANH SÁCH ---
    public function index(Request $request)
{
    // 1. Khởi tạo Query (Load sẵn quan hệ để tối ưu)
    $query = \App\Models\Book::with(['category', 'publisher']);

    // --- A. Lọc theo TỪ KHÓA (Tên sách, Tác giả) ---
    if ($request->filled('keyword')) {
        $k = $request->keyword;
        $query->where(function($q) use ($k) {
            $q->where('title', 'like', "%$k%")
              ->orWhere('author', 'like', "%$k%");
        });
    }

    // --- B. Lọc theo DANH MỤC ---
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    // --- C. Lọc theo LOẠI SÁCH (MỚI THÊM) ---
    if ($request->filled('type')) {
        if ($request->type == 'ebook') {
            // Lấy những sách CÓ bán Ebook (Giá ebook > 0 và có file)
            $query->where('ebook_price', '>', 0)->whereNotNull('file_ebook');
        } elseif ($request->type == 'physical') {
            // Lấy những sách CÓ bán Sách giấy (Giá thường > 0)
            $query->where('price', '>', 0);
        }
    }

    // --- D. Lọc theo NHÀ XUẤT BẢN (Nếu cần) ---
    if ($request->filled('publisher_id')) {
        $query->where('publisher_id', $request->publisher_id);
    }

    // 2. Chạy lệnh lấy dữ liệu (Sắp xếp mới nhất trước)
    $books = $query->orderBy('id', 'desc')->paginate(10);
    
    // Giữ lại các tham số trên URL khi chuyển trang (Phân trang không bị mất bộ lọc)
    $books->appends($request->all());

    // 3. Lấy danh sách Danh mục để đổ vào Dropdown lọc
    $categories = \App\Models\Category::all();

    // 4. Trả về View
    if (view()->exists('admin.books.index')) {
        return view('admin.books.index', compact('books', 'categories'));
    }
    
    return view('admin.books.index', compact('books', 'categories'));
}

    // --- 2. XUẤT EXCEL ---
    public function export()
    {
        if (ob_get_length() > 0) ob_end_clean();
        return Excel::download(new BooksExport, 'danh-sach-sach.xlsx');
    }

        // --- 3. NHẬP EXCEL ---
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ], [
            'file.required' => 'Vui lòng chọn file Excel trước!',
            'file.mimes' => 'Chỉ chấp nhận file đuôi .xlsx hoặc .xls'
        ]);
        
        try {
            // 🔥 1. Tách class ra một biến riêng để tí nữa "khám xét"
            $import = new BooksImport();
            
            // 🔥 2. Bắt đầu chạy lệnh Nhập dữ liệu
            Excel::import($import, $request->file('file'));
            
            // 🔥 3. Kiểm tra cái Máy đếm
            if ($import->importedCount > 0) {
                return back()->with('success', 'Tuyệt vời! Đã nhập thành công ' . $import->importedCount . ' cuốn sách!');
            } else {
                // Nếu chạy xong mà đếm = 0 thì chửi thẳng mặt!
                return back()->with('error', 'File Excel xàm quá! Không có cuốn sách nào được lưu (Do sai tên cột hoặc cột Tên Sách bị bỏ trống).');
            }

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            return back()->with('error', 'Lỗi dữ liệu: Cấu trúc file Excel không hợp lệ.');
        } catch (\Exception $e) {
            // Bắt luôn cái Exception báo thiếu cột "ten_sach" mà anh em mình gài bên BooksImport
            return back()->with('error', 'Lỗi nhập liệu: ' . $e->getMessage());
        }
    }

    // --- 4. CÁC HÀM CRUD ---

    // Form Thêm mới
    public function create()
    {
        $categories = Category::all();
        $publishers = Publisher::all();
        
        if (view()->exists('admin.books.create')) {
            return view('admin.books.create', compact('categories', 'publishers'));
        }
        return view('admin.books.create', compact('categories', 'publishers'));
    }

    // Xử lý Lưu sách mới
    public function store(Request $request)
{
    // 1. Validate dữ liệu
    $request->validate([
        'title' => 'required|max:255',
        'author' => 'required',
        'price' => 'required|numeric|min:0',
        'category_id' => 'required',
        'publisher_id' => 'required',
        'quantity' => 'nullable|integer|min:0', 
        'image' => 'nullable|image|max:2048', 

        'file_preview' => 'nullable|mimes:pdf|max:102400',
        'file_ebook'   => 'nullable|mimes:pdf,epub|max:102400',
        // book_content không cần validate strict vì có thể null
        
        // Thêm validate cho mấy trường mới (nếu cần)
        'published_date' => 'nullable|date',
        'font_family' => 'nullable|string|max:100',
    ]);

    $data = $request->all();

    // 🔥 QUAN TRỌNG: Lưu nội dung nhập tay vào DB
    $data['book_content'] = $request->book_content;

    // Lưu số lượng
    $data['quantity'] = $request->quantity ?? 0;
    
    // 2. Xử lý Upload Ảnh bìa
    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads'), $filename);
        $data['image'] = 'uploads/' . $filename;
    }

    // 3. Xử lý Upload File Preview
    if ($request->hasFile('file_preview')) {
        $file = $request->file('file_preview');
        $filename = time() . '_preview.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/previews'), $filename);
        $data['file_preview'] = $filename;
    }

    // 4. Xử lý Upload File Ebook & TỰ TÍNH DUNG LƯỢNG
    if ($request->hasFile('file_ebook')) {
        $file = $request->file('file_ebook');
        
        // 🔥 LOGIC TÍNH MB/KB 🔥
        $bytes = $file->getSize();
        if ($bytes >= 1048576) {
            $data['file_size'] = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $data['file_size'] = number_format($bytes / 1024, 2) . ' KB';
        } else {
            $data['file_size'] = $bytes . ' bytes';
        }

        // Lưu file vật lý
        $filename = time() . '_ebook.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/ebooks'), $filename);
        $data['file_ebook'] = $filename;
    }

    // 5. Logic Dịch giả
    if ($request->is_foreign == 0) {
        $data['translator'] = null;
    }

    // 6. Tạo sách
    Book::create($data);

    return redirect()->route('books.index')->with('success', 'Đã thêm sách thành công!');
}

    public function show($id)
    {
        $book = \App\Models\Book::with(['category', 'publisher'])->findOrFail($id);
        return view('admin.books.show', compact('book'));
    }

    // Form Chỉnh sửa
    public function edit(string $id)
    {
        $book = Book::findOrFail($id);
        $categories = Category::all();
        $publishers = Publisher::all();
        
        if (view()->exists('admin.books.edit')) {
            return view('admin.books.edit', compact('book', 'categories', 'publishers'));
        }
        return view('admin.books.edit', compact('book', 'categories', 'publishers'));
    }

    // Xử lý Cập nhật
    public function update(Request $request, string $id)
{
    $book = Book::findOrFail($id);

    // 1. Validate
    $request->validate([
        'title' => 'required',
        'author' => 'required',
        'price' => 'required|numeric',
        'publisher_id' => 'nullable|exists:publishers,id',
        'quantity' => 'nullable|integer|min:0',
        'image' => 'nullable|image|max:20480',
        'file_preview' => 'nullable|mimes:pdf|max:102400',
        'file_ebook'   => 'nullable|mimes:pdf,epub|max:102400',
        
        // Validate thêm các trường mới
        'published_date' => 'nullable|date',
        'font_family' => 'nullable|string|max:100',
    ]);

    $data = $request->all();

    // 🔥 QUAN TRỌNG: Cập nhật nội dung nhập tay
    $data['book_content'] = $request->book_content;

    // Cập nhật số lượng
    $data['quantity'] = $request->quantity ?? 0;

    // 2. Xử lý Ảnh
    if ($request->hasFile('image')) {
        if ($book->image && File::exists(public_path($book->image))) {
            File::delete(public_path($book->image));
        }
        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads'), $filename);
        $data['image'] = 'uploads/' . $filename;
    }

    // 3. Xử lý File Preview
    if ($request->hasFile('file_preview')) {
        if ($book->file_preview && File::exists(public_path('uploads/previews/' . $book->file_preview))) {
            File::delete(public_path('uploads/previews/' . $book->file_preview));
        }
        $file = $request->file('file_preview');
        $filename = time() . '_preview.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/previews'), $filename);
        $data['file_preview'] = $filename;
    }

    // 4. Xử lý File Ebook & TỰ TÍNH LẠI DUNG LƯỢNG MỚI
    if ($request->hasFile('file_ebook')) {
        // Xóa file cũ nếu có
        if ($book->file_ebook && File::exists(public_path('uploads/ebooks/' . $book->file_ebook))) {
            File::delete(public_path('uploads/ebooks/' . $book->file_ebook));
        }
        $file = $request->file('file_ebook');
        
        // 🔥 LOGIC TÍNH MB/KB 🔥
        $bytes = $file->getSize();
        if ($bytes >= 1048576) {
            $data['file_size'] = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $data['file_size'] = number_format($bytes / 1024, 2) . ' KB';
        } else {
            $data['file_size'] = $bytes . ' bytes';
        }

        // Lưu file vật lý
        $filename = time() . '_ebook.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/ebooks'), $filename);
        $data['file_ebook'] = $filename;
    }

    // 5. Logic phụ
    if ($request->is_foreign == 0) {
        $data['translator'] = null;
    }

    // 6. Update thông tin
    $book->update($data);

    // Trả về cái URL mà mình đã gài trong form, nếu không có thì mới về trang 1
    return redirect($request->input('previous_url', route('books.index')))
            ->with('success', 'Cập nhật sách thành công!');
}

    // --- CÁC HÀM HIỂN THỊ KHÁC ---

    public function shop()
    {
        $books = Book::orderBy('created_at', 'desc')->paginate(20);
        return view('client.pages.shop', compact('books'));
    }

    public function destroy(string $id)
    {
        $book = Book::findOrFail($id);
        
        // Xóa file rác trước khi xóa sách
        if ($book->image && File::exists(public_path($book->image))) {
            File::delete(public_path($book->image));
        }
        if ($book->file_preview && File::exists(public_path('uploads/previews/' . $book->file_preview))) {
            File::delete(public_path('uploads/previews/' . $book->file_preview));
        }
        if ($book->file_ebook && File::exists(public_path('uploads/ebooks/' . $book->file_ebook))) {
            File::delete(public_path('uploads/ebooks/' . $book->file_ebook));
        }

        $book->delete();
        return redirect()->route('books.index')->with('success', 'Đã xóa sách xong!');
    }

    public function flashSale()
    {
        // BỘ LỌC CHUẨN CHO DEAL SỐC
    $books = Book::whereNotNull('sale_price')            // Lớp 1: Phải có nhập giá sale
                 ->where('sale_price', '>', 0)           // Lớp 2: Giá sale phải lớn hơn 0đ
                 ->whereColumn('sale_price', '<', 'price') // Lớp 3: Giá sale phải nhỏ hơn giá gốc
                 ->orderBy('sale_price', 'asc')          // Sắp xếp ưu tiên rẻ nhất (Tùy chọn)
                 ->paginate(20);                         // Cắt 20 cuốn 1 trang
        return view('client.pages.flash-sale', compact('books'));
    }

    public function newArrivals()
    {
        $dateLimit = \Carbon\Carbon::now()->subDays(7);
        $books = \App\Models\Book::where('created_at', '>=', $dateLimit)
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
        return view('client.pages.new-arrivals', compact('books'));
    }

    public function bestSellers()
    {
        $books = \App\Models\Book::orderBy('total_sold', 'desc')->paginate(20);
        return view('client.pages.best-sellers', compact('books'));
    }
}