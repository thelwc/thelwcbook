<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Điều khoản sử dụng - Thelwc Books</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fa;
            color: #212529;
            line-height: 1.6;
        }
        a { text-decoration: none; transition: all 0.3s ease; color: #212529; }
        a:hover, .text-brand-hover:hover { color: #c5a992 !important; }
        
        .doc-container {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            padding: 40px 50px;
            margin: 40px auto;
            max-width: 800px;
        }
        
        .doc-title { color: #212529; font-weight: 800; }
        .doc-content h5 { font-weight: 700; margin-top: 30px; margin-bottom: 15px; color: #c5a992; }
        .doc-content p { color: #555; text-align: justify; }
        .doc-content ul { color: #555; }
        
        .btn-dark-brand {
            background-color: #212529;
            border: 1px solid #212529;
            color: #ffffff;
            padding: 10px 30px;
            font-weight: 700;
            transition: all 0.3s ease;
        }
        .btn-dark-brand:hover {
            background-color: #c5a992;
            border-color: #c5a992;
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(197, 169, 146, 0.4);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="doc-container">
        <div class="text-center mb-5">
            <a href="{{ route('home') }}" class="d-inline-block mb-3 text-dark fw-bold fs-4 text-brand-hover">
                Thelwc Books
            </a>
            <h1 class="doc-title display-6">Điều Khoản Sử Dụng</h1>
            <p class="text-muted small">Cập nhật lần cuối: {{ date('d/m/Y') }}</p>
        </div>

        <div class="doc-content">
            <p>Chào mừng bạn đến với <strong>Thelwc Books</strong>. Khi bạn đăng ký tài khoản và sử dụng các dịch vụ của chúng tôi, đồng nghĩa với việc bạn đã đọc, hiểu và đồng ý tuân thủ các điều khoản dưới đây.</p>

            <h5>1. Giới thiệu chung</h5>
            <p>Thelwc Books là nền tảng thương mại điện tử chuyên cung cấp các đầu sách giấy và sách điện tử (Ebook) bản quyền. Chúng tôi bảo lưu quyền thay đổi, chỉnh sửa, thêm hoặc lược bỏ bất kỳ phần nào trong Điều khoản sử dụng này vào bất cứ lúc nào. Các thay đổi có hiệu lực ngay khi được đăng trên trang web mà không cần thông báo trước.</p>

            <h5>2. Quyền và Trách nhiệm của Người dùng</h5>
            <ul>
                <li>Bạn phải cung cấp thông tin cá nhân chính xác, đầy đủ và cập nhật khi đăng ký tài khoản.</li>
                <li>Bạn có trách nhiệm bảo mật thông tin tài khoản và mật khẩu của mình. Thelwc Books sẽ không chịu trách nhiệm cho bất kỳ tổn thất nào phát sinh do việc rò rỉ thông tin tài khoản từ phía bạn.</li>
                <li>Nghiêm cấm sử dụng bất kỳ phần nào của trang web này với mục đích thương mại hoặc nhân danh bất kỳ đối tác thứ ba nào nếu không được chúng tôi cho phép bằng văn bản.</li>
            </ul>

            <h5>3. Quy định về Ebook & Bản quyền</h5>
            <p>Tất cả nội dung sách điện tử (Ebook), hình ảnh, và văn bản trên Thelwc Books đều thuộc sở hữu bản quyền của Thelwc Books hoặc các Nhà xuất bản đối tác. Việc sao chép, phát tán, hoặc chia sẻ Ebook dưới bất kỳ hình thức nào mà không có sự đồng ý đều vi phạm pháp luật và sẽ bị khóa tài khoản vĩnh viễn.</p>

            <h5>4. Giải quyết tranh chấp</h5>
            <p>Bất kỳ tranh cãi, khiếu nại hoặc tranh chấp phát sinh từ hoặc liên quan đến giao dịch tại Thelwc Books hoặc các Quy định và Điều kiện này đều sẽ được giải quyết theo pháp luật của nước Cộng hòa Xã hội Chủ nghĩa Việt Nam.</p>
        </div>

        <div class="text-center mt-5 pt-3 border-top">
            <button onclick="window.close();" class="btn btn-dark-brand rounded-pill">
                <i class="fas fa-check me-2"></i> Đã hiểu và Đóng
            </button>
        </div>
    </div>
</div>

</body>
</html>