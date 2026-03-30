<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Hóa đơn #{{ $order->id }} - Thelwc Books</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #d9534f; } /* Màu đỏ cho nổi */
        .header p { margin: 5px 0; }
        
        .info-section { width: 100%; margin-bottom: 20px; }
        .info-section td { vertical-align: top; padding: 5px 0; }
        .info-label { font-weight: bold; width: 130px; }

        .product-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .product-table th { background-color: #f2f2f2; font-weight: bold; padding: 10px; border: 1px solid #ccc; text-align: center; }
        .product-table td { border: 1px solid #ccc; padding: 10px; vertical-align: middle; }
        
        .badge-ebook { color: #007bff; font-weight: bold; font-size: 11px; }
        .badge-physical { color: #28a745; font-weight: bold; font-size: 11px; }
        .price-old { color: #999; font-size: 11px; text-decoration: line-through; display: block; }
        .price-sale { color: #d9534f; font-weight: bold; }

        .summary-section { width: 100%; margin-top: 20px; }
        .summary-table { width: 50%; float: right; border-collapse: collapse; }
        .summary-table td { padding: 5px; text-align: right; }
        .summary-label { font-weight: bold; }
        .grand-total { font-size: 18px; color: #d9534f; font-weight: bold; border-top: 2px solid #333; padding-top: 10px !important; }

        .footer { text-align: center; margin-top: 50px; font-size: 12px; color: #777; font-style: italic; clear: both; }
    </style>
</head>
<body>
    <div class="header">
        <h1>THELWC BOOKS STORE</h1>
        <p>Địa chỉ: Đại học An Giang - TP. Long Xuyên</p>
        <p>Hotline: 0964 617 664 | Email: letheluc04@gmail.com</p>
    </div>

    <table class="info-section">
        <tr>
            <td width="50%">
                <h3 style="margin-bottom: 10px; color: #333;">THÔNG TIN KHÁCH HÀNG</h3>
                <table>
                    <tr><td class="info-label">Khách hàng:</td><td>{{ $order->name }}</td></tr>
                    <tr><td class="info-label">Số điện thoại:</td><td>{{ $order->phone }}</td></tr>
                    <tr><td class="info-label">Email:</td><td>{{ $order->email }}</td></tr>
                    <tr><td class="info-label">Địa chỉ:</td><td>{{ $order->address }}</td></tr>
                </table>
            </td>
            <td width="50%">
                <h3 style="margin-bottom: 10px; color: #333;">THÔNG TIN ĐƠN HÀNG</h3>
                <table>
                    <tr><td class="info-label">Mã hóa đơn:</td><td><strong>#{{ $order->id }}</strong></td></tr>
                    <tr><td class="info-label">Ngày đặt:</td><td>{{ $order->created_at->format('d/m/Y H:i') }}</td></tr>
                    <tr><td class="info-label">Thanh toán:</td><td>{{ $order->payment_method }}</td></tr> 
                    <tr><td class="info-label">Ghi chú:</td><td>{{ $order->note ?? 'Không có' }}</td></tr> 
                </table>
            </td>
        </tr>
    </table>

    <table class="product-table">
        <thead>
            <tr>
                <th style="width: 5%">STT</th>
                <th style="width: 45%; text-align: left;">Tên sách</th>
                <th style="width: 15%; text-align: right;">Đơn giá</th>
                <th style="width: 10%;">SL</th>
                <th style="width: 25%; text-align: right;">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @php $subtotal = 0; @endphp
            @foreach($order->details as $index => $item)
            @php 
                $lineTotal = $item->price * $item->quantity;
                $subtotal += $lineTotal; 
            @endphp
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                
                {{-- Cột Tên sách + Loại hàng --}}
                <td>
                    <div style="font-weight: bold; margin-bottom: 4px;">{{ $item->book->title }}</div>
                    @if($item->type == 'ebook')
                        <span class="badge-ebook">[ Ebook - Sách điện tử ]</span>
                    @else
                        <span class="badge-physical">[ Sách giấy ]</span>
                    @endif
                </td>

                {{-- Cột Giá tiền (Check giá gốc) --}}
                <td style="text-align: right;">
                    {{-- Chỉ hiện giá gạch ngang nếu là sách giấy và mua với giá thấp hơn giá gốc --}}
                    @if($item->type != 'ebook' && $item->book && $item->book->price > $item->price)
                        <span class="price-old">{{ number_format($item->book->price) }} đ</span>
                        <span class="price-sale">{{ number_format($item->price) }} đ</span>
                    @else
                        <strong>{{ number_format($item->price) }} đ</strong>
                    @endif
                </td>

                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($lineTotal) }} đ</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-section">
        <table class="summary-table">
            <tr>
                <td class="summary-label">Tạm tính:</td>
                <td>{{ number_format($subtotal) }} đ</td>
            </tr>
            <tr>
                <td class="summary-label">Phí vận chuyển:</td>
                <td>
                    {{ $order->shipping_fee > 0 ? number_format($order->shipping_fee) . ' đ' : 'Miễn phí' }}
                </td>
            </tr>
            @if($order->discount > 0)
            <tr>
                <td class="summary-label">Giảm giá ({{ $order->coupon_code }}):</td>
                <td style="color: #28a745; font-weight: bold;">- {{ number_format($order->discount) }} đ</td>
            </tr>
            @endif
            <tr>
                <td class="summary-label grand-total">TỔNG THANH TOÁN:</td>
                <td class="grand-total">{{ number_format($order->total_price) }} đ</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Cảm ơn cậu đã mua sắm tại Thelwc Books!</p>
        <p>Nếu có bất kỳ thắc mắc nào, vui lòng liên hệ Hotline hoặc Email để được hỗ trợ.</p>
    </div>
</body>
</html>