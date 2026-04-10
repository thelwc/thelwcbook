<?php

namespace App\Exports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class UrgentBooksExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        // Lấy sách < 10 cuốn, xếp số lượng từ thấp lên cao
        return Book::where('quantity', '<', 10)->orderBy('quantity', 'asc')->get();
    }

    public function map($book): array
    {
        return [
            $book->id,
            $book->title,
            $book->quantity,
        ];
    }

    public function headings(): array
    {
        return [
            'Mã Sách',
            'Tên Sách',
            'Số Lượng Tồn',
        ];
    }
}