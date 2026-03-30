<?php

namespace App\Exports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BooksExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        return Book::with(['category', 'publisher'])->get();
    }

    public function map($book): array
    {
        return [
            $book->id,
            $book->title,
            $book->author,
            $book->category ? $book->category->name : 'Không có', 
            $book->publisher ? $book->publisher->name : 'Không có', 
            
            // --- Thông tin Sách In ---
            $book->price ?? 0,       
            $book->sale_price ?? 0, 
            $book->quantity ?? 0,
            $book->total_sold ?? 0,
            $book->cover_type,
            $book->dimensions,
            $book->page_count,
            $book->published_date ? \Carbon\Carbon::parse($book->published_date)->format('d/m/Y') : '',
            $book->is_foreign ? 'Có' : 'Không',
            $book->translator,

            // --- Thông tin Ebook ---
            $book->ebook_price ?? 0,
            $book->ebook_sold ?? 0,
            $book->preview_pages ?? 0,
            $book->font_family,

            // --- Khác ---
            $book->created_at ? $book->created_at->format('d/m/Y H:i') : '',
            strip_tags($book->description), // Lấy chữ thô cho Excel đỡ rác HTML
        ];
    }

    public function headings(): array
    {
        return [
            'ID', 
            'Tên Sách', 
            'Tác Giả', 
            'Danh Mục', 
            'Nhà Xuất Bản', 
            
            // Sách In
            'Giá Gốc', 
            'Giá Khuyến Mãi', 
            'Tồn Kho', 
            'Đã Bán In',
            'Loại Bìa',
            'Kích Thước',
            'Số Trang',
            'Ngày Xuất Bản',
            'Sách Nước Ngoài',
            'Dịch Giả',

            // Ebook
            'Giá Ebook',
            'Đã Bán Ebook',
            'Trang Đọc Thử',
            'Font Chữ',

            // Khác
            'Ngày Tạo',
            'Mô Tả'
        ];
    }
}