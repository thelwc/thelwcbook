<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RevenueExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        // Khởi tạo câu truy vấn lấy các đơn hàng thành công
        $query = Order::whereIn('status', ['completed', '2', '3']);

        // CHỈ LỌC NGÀY NẾU CÓ TRUYỀN NGÀY VÀO (Bấm từ trang Chi tiết)
        if ($this->startDate && $this->endDate) {
            $query->whereDate('created_at', '>=', $this->startDate)
                  ->whereDate('created_at', '<=', $this->endDate);
        }

        // Trả về kết quả, sắp xếp mới nhất lên đầu
        return $query->orderBy('created_at', 'desc')->get();
    }

    // Đặt tên tiêu đề cho các cột trong Excel
    public function headings(): array
    {
        return [
            'Mã Đơn', 
            'Khách Hàng', 
            'Số Điện Thoại', 
            'Phương Thức TT', 
            'Tổng Tiền', 
            'Phí Ship', 
            'Giảm Giá', 
            'Ngày Hoàn Thành'
        ];
    }

    // Map dữ liệu vào từng cột tương ứng
    public function map($order): array
    {
        return [
            '#' . $order->id,
            $order->name,
            $order->phone,
            strtoupper($order->payment_method),
            $order->total_price,
            $order->shipping_fee,
            $order->discount,
            $order->created_at->format('d/m/Y H:i:s')
        ];
    }
}