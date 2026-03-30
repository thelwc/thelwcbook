<!DOCTYPE html>
<html>
<head>
    <title>Xác nhận đơn hàng #{{ $order->id }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f5f7; padding: 20px 0;">
    <div style="max-width: 650px; margin: 0 auto; background-color: #ffffff; padding: 30px; border: 1px solid #e0e0e0; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        
        <div style="text-align: center; border-bottom: 2px solid #d9534f; padding-bottom: 15px; margin-bottom: 25px;">
            <h2 style="color: #d9534f; margin: 0; text-transform: uppercase;">Cảm ơn bạn đã đặt hàng!</h2>
            <p style="margin: 5px 0 0 0; color: #555;">Đơn hàng <strong>#{{ $order->id }}</strong> của bạn đã được tiếp nhận.</p>
        </div>
        
        <p style="font-size: 15px;">Xin chào <strong style="color: #0056b3;">{{ $order->name ?? 'Khách hàng' }}</strong>,</p>
        <p>Thelwc Books đã nhận được yêu cầu đặt hàng của bạn và đang tiến hành xử lý. Dưới đây là thông tin chi tiết đơn hàng của bạn:</p>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px;">
            <thead>
                <tr style="background-color: #f8f9fa;">
                    <th style="padding: 12px 10px; border: 1px solid #ddd; text-align: left; width: 45%;">Sản phẩm</th>
                    <th style="padding: 12px 10px; border: 1px solid #ddd; text-align: right; width: 20%;">Đơn giá</th>
                    <th style="padding: 12px 10px; border: 1px solid #ddd; text-align: center; width: 10%;">SL</th>
                    <th style="padding: 12px 10px; border: 1px solid #ddd; text-align: right; width: 25%;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @php $subtotal = 0; @endphp
                @foreach($order->details as $item)
                @php 
                    $lineTotal = $item->price * $item->quantity;
                    $subtotal += $lineTotal; 
                @endphp
                <tr>
                    {{-- 1. Cột Tên sách & Phân loại --}}
                    <td style="padding: 12px 10px; border: 1px solid #ddd;">
                        <div style="font-weight: bold; margin-bottom: 5px; color: #333;">{{ $item->book->title ?? 'Sản phẩm đã xóa' }}</div>
                        @if($item->type == 'ebook')
                            <span style="color: #007bff; font-size: 12px; font-weight: bold; background-color: #e6f2ff; padding: 2px 6px; border-radius: 4px;">Ebook - Sách điện tử</span>
                        @else
                            <span style="color: #28a745; font-size: 12px; font-weight: bold; background-color: #e6f9ed; padding: 2px 6px; border-radius: 4px;">Sách giấy</span>
                        @endif
                    </td>

                    {{-- 2. Cột Đơn giá (Check giá giảm) --}}
                    <td style="padding: 12px 10px; border: 1px solid #ddd; text-align: right;">
                        @if($item->type != 'ebook' && $item->book && $item->book->price > $item->price)
                            <div style="text-decoration: line-through; color: #999; font-size: 11px;">{{ number_format($item->book->price) }}đ</div>
                            <div style="color: #d9534f; font-weight: bold;">{{ number_format($item->price) }}đ</div>
                        @else
                            <strong style="color: #333;">{{ number_format($item->price) }}đ</strong>
                        @endif
                    </td>

                    {{-- 3. Cột Số lượng & Thành tiền --}}
                    <td style="padding: 12px 10px; border: 1px solid #ddd; text-align: center; color: #333;">{{ $item->quantity }}</td>
                    <td style="padding: 12px 10px; border: 1px solid #ddd; text-align: right; font-weight: bold; color: #333;">
                        {{ number_format($lineTotal) }}đ
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Khu vực tính tổng tiền --}}
        <table style="width: 100%; border-collapse: collapse; margin-top: 0;">
            <tr>
                <td colspan="3" style="padding: 8px 10px; border: 1px solid #ddd; border-top: none; text-align: right; color: #555;">Tạm tính:</td>
                <td style="padding: 8px 10px; border: 1px solid #ddd; border-top: none; text-align: right; font-weight: bold; width: 25%;">
                    {{ number_format($subtotal) }}đ
                </td>
            </tr>
            <tr>
                <td colspan="3" style="padding: 8px 10px; border: 1px solid #ddd; text-align: right; color: #555;">Phí vận chuyển:</td>
                <td style="padding: 8px 10px; border: 1px solid #ddd; text-align: right; font-weight: bold; color: #0056b3;">
                    {{ $order->shipping_fee > 0 ? number_format($order->shipping_fee) . 'đ' : 'Miễn phí' }}
                </td>
            </tr>
            @if($order->discount > 0)
            <tr>
                <td colspan="3" style="padding: 8px 10px; border: 1px solid #ddd; text-align: right; color: #555;">Giảm giá Voucher:</td>
                <td style="padding: 8px 10px; border: 1px solid #ddd; text-align: right; font-weight: bold; color: #28a745;">
                    -{{ number_format($order->discount) }}đ
                </td>
            </tr>
            @endif
            <tr>
                <td colspan="3" style="padding: 12px 10px; border: 1px solid #ddd; text-align: right; font-weight: bold; font-size: 16px; color: #333;">TỔNG THANH TOÁN:</td>
                <td style="padding: 12px 10px; border: 1px solid #ddd; text-align: right; color: #d9534f; font-weight: bold; font-size: 18px;">
                    {{ number_format($order->total_price) }}đ
                </td>
            </tr>
        </table>

        <div style="background-color: #fff8e1; border-left: 4px solid #ffc107; padding: 15px; margin-top: 25px; border-radius: 4px;">
            <strong style="color: #856404;">📌 Lưu ý:</strong>
            <ul style="margin: 5px 0 0 0; padding-left: 20px; color: #856404; font-size: 13px;">
                <li><strong>Với Sách điện tử (Ebook):</strong> Sẽ tự động mở khóa trong tài khoản của bạn ngay sau khi đơn hàng được cửa hàng xác nhận.</li>
                <li><strong>Với Sách giấy:</strong> Chúng tôi sẽ sớm liên hệ hoặc gửi thông báo khi sách được giao cho đơn vị vận chuyển.</li>
            </ul>
        </div>

        <div style="text-align: center; margin-top: 40px; font-size: 12px; color: #777; border-top: 1px dashed #eee; padding-top: 20px;">
            <p style="margin: 0 0 5px 0;">Đây là email tự động, vui lòng không trả lời qua email này.</p>
            <p style="margin: 0;"><strong>Thelwc Books</strong> - 710/2 Phan Tôn, Long Xuyên, An Giang</p>
        </div>
    </div>
</body>
</html>