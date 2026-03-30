@extends('admin.layouts.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-dark">📚 Thêm Sách Mới</h3>
    <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i> Quay lại
    </a>
</div>

{{-- Form bắt đầu --}}
<form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row">
        {{-- ==================== CỘT TRÁI: THÔNG TIN CHÍNH (Chiếm 8 phần) ==================== --}}
        <div class="col-md-8">
            <div class="card shadow border-0 rounded-4 mb-4">
                <div class="card-body p-4">

                    {{-- 1. Tên sách --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên sách <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="Nhập tên sách..." required>
                    </div>

                    {{-- 2. Thể loại & NXB --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Thể loại <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                <option value="">-- Chọn thể loại --</option>
                                @foreach($categories as $cate)
                                <option value="{{ $cate->id }}">{{ $cate->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nhà xuất bản <span class="text-danger">*</span></label>
                            <select name="publisher_id" class="form-select" required>
                                <option value="">-- Chọn NXB --</option>
                                @foreach($publishers as $pub)
                                <option value="{{ $pub->id }}">{{ $pub->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- 3. Tác giả --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tác giả <span class="text-danger">*</span></label>
                        <input type="text" name="author" class="form-control" placeholder="Nhập tên tác giả..." required>
                    </div>

                    {{-- 4. KHỐI GIÁ TIỀN --}}
                    <div class="card bg-light border-0 p-3 mb-3 rounded-3">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Giá gốc (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" name="price" id="originalPrice" class="form-control fw-bold" placeholder="0" required oninput="calculateFromOriginal()">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-success">Giá Khuyến Mãi (Giá bán)</label>
                                <input type="number" name="sale_price" id="salePrice" class="form-control border-success fw-bold text-success"
                                    placeholder="Để trống nếu không giảm">
                                <div class="form-text text-danger small" id="priceWarning" style="display: none;">Giá KM không được cao hơn giá gốc!</div>
                            </div>
                        </div>

                        {{-- Công cụ tính nhanh --}}
                        <div class="row border-top pt-2 mt-2">
                            <label class="small fw-bold text-primary mb-2">🎯 Công cụ tính nhanh</label>
                            <div class="col-md-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Giảm theo %</span>
                                    <input type="number" id="discountPercent" class="form-control" placeholder="VD: 20" oninput="applyDiscount('percent')">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">Giảm tiền</span>
                                    <input type="number" id="discountAmount" class="form-control" placeholder="VD: 50000" oninput="applyDiscount('amount')">
                                    <span class="input-group-text">đ</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 5. Mô tả ngắn --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mô tả ngắn (Giới thiệu)</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Nhập mô tả giới thiệu sách..."></textarea>
                    </div>

                    {{-- 🔥 6. NỘI DUNG SÁCH (HYBRID: PDF HOẶC NHẬP TAY) 🔥 --}}
                    <div class="card border border-warning mt-4">
                        <div class="card-header bg-warning text-dark fw-bold">
                            <i class="fas fa-book-open me-2"></i> Nội dung sách (Ebook/Đọc thử)
                        </div>
                        <div class="card-body">

                            {{-- Tab chuyển đổi --}}
                            <ul class="nav nav-tabs mb-3" id="contentTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active fw-bold" id="pdf-tab" data-bs-toggle="tab" data-bs-target="#upload-pdf" type="button" role="tab">
                                        <i class="fas fa-file-pdf me-1"></i> Upload PDF (Ưu tiên)
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-bold" id="text-tab" data-bs-toggle="tab" data-bs-target="#type-text" type="button" role="tab">
                                        <i class="fas fa-keyboard me-1"></i> Nhập tay (Văn bản)
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content">
                                {{-- TAB 1: Upload PDF (Code cũ dời vào đây) --}}
                                <div class="tab-pane fade show active" id="upload-pdf" role="tabpanel">
                                    <div class="alert alert-info small mb-3">
                                        <i class="fas fa-info-circle me-1"></i> Nếu upload file PDF, hệ thống sẽ ưu tiên hiển thị PDF thay vì nội dung nhập tay.
                                    </div>

                                    {{-- File Đọc thử --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small">File Đọc thử (PDF)</label>
                                        <input type="file" name="file_preview" class="form-control" accept=".pdf">
                                        <div class="form-text text-xs">Chỉ upload 10-20 trang đầu.</div>
                                    </div>

                                    {{-- Giới hạn trang --}}
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small">Giới hạn số trang đọc thử</label>
                                        <input type="number" name="preview_pages" class="form-control"
                                            value="{{ old('preview_pages', 10) }}" min="1">
                                        <div class="form-text text-muted text-xs">Khách chỉ xem được đến trang này.</div>
                                    </div>

                                    <hr class="border-secondary opacity-25">

                                    {{-- Bán Ebook Full --}}
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="ebookToggle" onchange="toggleEbookOptions()">
                                        <label class="form-check-label fw-bold small" for="ebookToggle">Bán kèm bản Ebook Full?</label>
                                    </div>
                                    <div id="ebookOptions" style="display: none;">
                                        <div class="mb-2">
                                            <label class="form-label small">File Ebook Chính (PDF/EPUB)</label>
                                            <input type="file" name="file_ebook" class="form-control" accept=".pdf,.epub">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small">Giá bán Ebook (VNĐ)</label>
                                            <input type="number" name="ebook_price" class="form-control" placeholder="VD: 50000">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label small fw-bold">Font chữ Ebook</label>
                                            <input type="text" name="font_family" class="form-control" placeholder="VD: Arial, Times New Roman..." value="{{ old('font_family') }}">
                                        </div>

                                        <div class="col-md-6 mb-2">
                                            <label class="form-label small fw-bold">Dung lượng File</label>
                                            <input type="text" class="form-control bg-white text-success fw-bold"
                                                value="Hệ thống tự tính khi lưu" readonly disabled>
                                            <div class="form-text text-muted" style="font-size: 0.7rem;">Hệ thống sẽ tự động quét file và tính MB/KB.</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- TAB 2: Nhập tay (Summernote) --}}
                                <div class="tab-pane fade" id="type-text" role="tabpanel">
                                    <div class="alert alert-warning small mb-3">
                                        <i class="fas fa-exclamation-triangle me-1"></i> Dùng khi không có file PDF. Bạn có thể copy/paste nội dung truyện/sách vào đây.
                                    </div>
                                    <textarea name="book_content" id="summernote"></textarea>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            {{-- 🔥 PHẦN MỚI: THÔNG SỐ CHI TIẾT --}}
            <div class="card shadow border-0 rounded-4 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-info"><i class="fas fa-info-circle me-2"></i> Thông số chi tiết</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold">Ngày XB</label>
                            <input type="date" name="published_date" class="form-control"
                                max="{{ date('Y-m-d') }}"
                                value="{{ old('published_date', $book->published_date ?? date('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold">Số trang</label>
                            <input type="number" name="page_count" class="form-control" placeholder="VD: 300" min="1">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold">Khổ giấy (cm)</label>
                            <div class="input-group input-group-sm">
                                <input type="number" id="dim_w" class="form-control px-1 text-center" placeholder="Rộng" step="0.5" oninput="updateDimensions()">
                                <span class="input-group-text px-1">x</span>
                                <input type="number" id="dim_h" class="form-control px-1 text-center" placeholder="Dài" step="0.5" oninput="updateDimensions()">
                            </div>
                            <input type="hidden" name="dimensions" id="real_dimensions">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold">Loại bìa</label>
                            <select name="cover_type" class="form-select">
                                <option value="Mềm">Bìa Mềm</option>
                                <option value="Cứng">Bìa Cứng</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Nguồn gốc</label>
                            <select name="is_foreign" class="form-select" id="originSelect" onchange="toggleTranslator()">
                                <option value="0">Sách Trong Nước</option>
                                <option value="1">Sách Nước Ngoài</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="translatorGroup" style="display: none;">
                            <label class="form-label small fw-bold text-primary">Tên Dịch giả</label>
                            <input type="text" name="translator" class="form-control" placeholder="Nhập tên dịch giả...">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================== CỘT PHẢI: ẢNH & KHO (Chiếm 4 phần) ==================== --}}
        <div class="col-md-4">

            {{-- 1. Ảnh bìa --}}
            <div class="card shadow border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <label class="form-label fw-bold">Ảnh bìa sách</label>
                    <input type="file" name="image" class="form-control mb-2" onchange="previewImage(this)">
                    <div class="text-muted small fst-italic mb-2">* Nên chọn ảnh tỉ lệ dọc</div>
                    <img id="imgPreview" src="https://via.placeholder.com/150" class="img-thumbnail w-100" style="display: none; object-fit: contain; max-height: 300px;">
                </div>
            </div>

            {{-- 2. Nhập kho --}}
            <div class="card shadow border-0 rounded-4 mb-4">
                <div class="card-header bg-white py-2">
                    <h6 class="m-0 fw-bold text-primary small"><i class="fas fa-warehouse me-2"></i> Kho hàng</h6>
                </div>
                <div class="card-body p-4 bg-light">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Số lượng tồn kho</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-box"></i></span>
                            <input type="number" name="quantity" class="form-control fw-bold"
                                placeholder="VD: 100" min="0" value="0">
                        </div>
                        <div class="form-text text-xs text-muted">Nhập tổng số lượng sách giấy hiện có.</div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1 rounded-pill">
                            <i class="fas fa-check-circle me-1"></i> Sẵn sàng bán
                        </span>
                    </div>
                </div>
            </div>

            {{-- Nút Submit --}}
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-dark fw-bold py-2">
                    <i class="fas fa-save me-2"></i> LƯU SÁCH MỚI
                </button>
                <a href="{{ route('books.index') }}" class="btn btn-light border">Hủy bỏ</a>
            </div>

        </div>
    </div>
</form>

{{-- SCRIPT TỔNG HỢP --}}
<script>
    // --- Kích hoạt Summernote ---
    $(document).ready(function() {
        $('#summernote').summernote({
            placeholder: 'Nhập nội dung sách (dành cho sách không có file PDF)...',
            tabsize: 2,
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });

    // --- Các hàm Logic cũ (Giá, Ảnh, Ebook...) ---
    function calculateFromOriginal() {
        document.getElementById('discountPercent').value = '';
        document.getElementById('discountAmount').value = '';
    }

    function applyDiscount(type) {
        let original = parseFloat(document.getElementById('originalPrice').value) || 0;
        let saleInput = document.getElementById('salePrice');
        if (original === 0) {
            alert('Vui lòng nhập giá gốc trước!');
            return;
        }

        if (type === 'percent') {
            let percent = parseFloat(document.getElementById('discountPercent').value) || 0;
            document.getElementById('discountAmount').value = '';
            let discountValue = original * (percent / 100);
            saleInput.value = Math.round(original - discountValue);
        } else if (type === 'amount') {
            let amount = parseFloat(document.getElementById('discountAmount').value) || 0;
            document.getElementById('discountPercent').value = '';
            saleInput.value = Math.round(original - amount);
        }
        checkPriceLogic();
    }

    document.getElementById('salePrice').addEventListener('input', function() {
        document.getElementById('discountPercent').value = '';
        document.getElementById('discountAmount').value = '';
        checkPriceLogic();
    });

    function checkPriceLogic() {
        let original = parseFloat(document.getElementById('originalPrice').value) || 0;
        let sale = parseFloat(document.getElementById('salePrice').value) || 0;
        let warning = document.getElementById('priceWarning');
        warning.style.display = (sale > original && original > 0) ? 'block' : 'none';
    }

    function previewImage(input) {
        var preview = document.getElementById('imgPreview');
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function toggleTranslator() {
        var isForeign = document.getElementById('originSelect').value;
        var transGroup = document.getElementById('translatorGroup');
        transGroup.style.display = (isForeign == "1") ? 'block' : 'none';
    }

    function toggleEbookOptions() {
        var isChecked = document.getElementById('ebookToggle').checked;
        var ebookOptions = document.getElementById('ebookOptions');
        ebookOptions.style.display = isChecked ? 'block' : 'none';
    }

    function updateDimensions() {
        let w = document.getElementById('dim_w').value;
        let h = document.getElementById('dim_h').value;
        let realInput = document.getElementById('real_dimensions');
        if (w && h) {
            realInput.value = w + ' x ' + h + ' cm';
        } else {
            realInput.value = '';
        }
    }
</script>
@endsection