<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chính sách bảo mật - Thelwc Books</title>
    
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
                📚 Thelwc Books
            </a>
            <h1 class="doc-title display-6">Chính Sách Bảo Mật</h1>
            <p class="text-muted small">Cập nhật lần cuối: {{ date('d/m/Y') }}</p>
        </div>

        <div class="doc-content">
            <p>Sự riêng tư của thông tin khách hàng là điều vô cùng quan trọng đối với <strong>Thelwc Books</strong>. Chúng tôi cam kết bảo vệ dữ liệu cá nhân của bạn và chỉ sử dụng chúng theo đúng những gì được mô tả trong Chính sách bảo mật này.</p>

            <h5>1. Mục đích thu thập thông tin</h5>
            <p>Việc thu thập dữ liệu chủ yếu trên website Thelwc Books bao gồm: email, điện thoại, mật khẩu đăng nhập, địa chỉ khách hàng. Đây là các thông tin mà chúng tôi cần thành viên cung cấp bắt buộc khi đăng ký sử dụng dịch vụ và để liên hệ xác nhận khi mua hàng nhằm đảm bảo quyền lợi cho người tiêu dùng.</p>

            <h5>2. Phạm vi sử dụng thông tin</h5>
            <p>Thelwc Books sử dụng thông tin thành viên cung cấp để:</p>
            <ul>
                <li>Cung cấp các dịch vụ/sản phẩm đến khách hàng (Giao sách, cấp quyền tải Ebook).</li>
                <li>Gửi các thông báo về hoạt động trao đổi thông tin giữa khách hàng và Thelwc Books.</li>
                <li>Ngừa các hoạt động phá hủy tài khoản người dùng của thành viên hoặc các hoạt động giả mạo.</li>
                <li>Liên lạc và giải quyết khiếu nại với khách hàng trong những trường hợp đặc biệt.</li>
            </ul>

            <h5>3. Cam kết bảo mật</h5>
            <p>Thông tin cá nhân của thành viên trên Thelwc Books được cam kết bảo mật tuyệt đối theo chính sách bảo vệ thông tin cá nhân của công ty. Việc thu thập và sử dụng thông tin của mỗi thành viên chỉ được thực hiện khi có sự đồng ý của khách hàng đó trừ những trường hợp pháp luật có quy định khác.</p>
            <p>Chúng tôi sử dụng công nghệ mã hóa an toàn để bảo vệ dữ liệu mật khẩu và giao dịch của bạn khỏi việc truy cập trái phép.</p>

            <h5>4. Liên hệ</h5>
            <p>Nếu bạn có bất kỳ câu hỏi hay thắc mắc nào liên quan đến Chính sách bảo mật, vui lòng liên hệ với chúng tôi qua email hỗ trợ: <strong>letheluc04@gmail.com</strong>.</p>
        </div>

        <div class="text-center mt-5 pt-3 border-top">
            <button onclick="window.close();" class="btn btn-dark-brand rounded-pill">
                <i class="fas fa-shield-alt me-2"></i> Đồng ý & Đóng
            </button>
        </div>
    </div>
</div>

</body>
</html>