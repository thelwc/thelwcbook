@extends('admin.layouts.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold text-dark">📖 Thông Tin Chi Tiết Sách</h3>
    <a href="{{ route('books.index') }}" class="btn btn-outline-secondary rounded-pill shadow-sm">
        <i class="fas fa-arrow-left me-2"></i> Quay lại
    </a>
</div>

<div class="row">
    {{-- CỘT TRÁI: THÔNG TIN CHI TIẾT --}}
    <div class="col-md-8">
        <div class="card shadow border-0 rounded-4 mb-4">
            <div class="card-body p-4">
                {{-- 1. HEADER TÊN SÁCH & TÁC GIẢ --}}
                <h4 class="fw-bold text-primary mb-1">{{ $book->title }}</h4>
                <div class="text-muted mb-3 d-flex gap-4">
                    <span><i class="fas fa-pen-nib me-1"></i> Tác giả: <span class="fw-bold text-dark">{{ $book->author }}</span></span>
                    
                    {{-- Nếu là sách dịch thì hiện thêm Dịch giả --}}
                    @if($book->is_foreign)
                        <span><i class="fas fa-language me-1"></i> Dịch giả: <span class="fw-bold text-dark">{{ $book->translator ?? 'Đang cập nhật' }}</span></span>
                    @endif
                </div>

                {{-- 2. TAGS (DANH MỤC & NXB) --}}
                <div class="d-flex gap-2 mb-4">
                    <span class="badge bg-info bg-opacity-10 text-info border border-info px-3 py-2 rounded-pill">
                        <i class="fas fa-bookmark me-1"></i> {{ $book->category->name ?? 'Chưa phân loại' }}
                    </span>
                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-2 rounded-pill">
                        <i class="fas fa-building me-1"></i> {{ $book->publisher->name ?? 'Chưa cập nhật NXB' }}
                    </span>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-3 py-2 rounded-pill">
                        <i class="fas fa-globe me-1"></i> {{ $book->is_foreign ? 'Sách dịch (Nước ngoài)' : 'Sách trong nước' }}
                    </span>
                </div>

                <hr class="text-muted opacity-25">

                {{-- 3. KHỐI GIÁ & KHUYẾN MÃI (SÁCH IN) --}}
                <div class="row mb-4">
                    <div class="col-md-12">
                        <label class="fw-bold text-secondary text-uppercase small mb-2"><i class="fas fa-tags me-1"></i> Giá Sách In</label>
                        <div class="bg-light p-3 rounded-3 border d-flex align-items-center justify-content-between">
                            <div>
                                <div class="small text-muted">Giá niêm yết (Gốc)</div>
                                <div class="fw-bold text-secondary text-decoration-line-through fs-5">
                                    {{ number_format($book->price) }} ₫
                                </div>
                            </div>

                            @if($book->sale_price && $book->sale_price < $book->price)
                                @php
                                    $discount = round((($book->price - $book->sale_price) / $book->price) * 100);
                                @endphp
                                <div class="text-center">
                                    <span class="badge bg-danger fs-6 mb-1">-{{ $discount }}%</span>
                                    <div class="small text-danger fw-bold">Giảm giá sốc</div>
                                </div>
                                <div class="text-end">
                                    <div class="small text-success fw-bold">Giá đang bán</div>
                                    <div class="fw-bold text-success fs-3">
                                        {{ number_format($book->sale_price) }} ₫
                                    </div>
                                </div>
                            @else
                                <div class="text-end">
                                    <div class="small text-dark fw-bold">Giá đang bán</div>
                                    <div class="fw-bold text-dark fs-3">
                                        {{ number_format($book->price) }} ₫
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- 4. SO SÁNH THÔNG TIN: SÁCH IN VÀ EBOOK --}}
                <div class="row mb-4 g-3">
                    {{-- Cột Sách In --}}
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush border rounded-3 small h-100 shadow-sm">
                            <li class="list-group-item bg-light fw-bold text-secondary py-3"><i class="fas fa-book-open me-2"></i> Đặc điểm Sách In</li>
                            <li class="list-group-item d-flex justify-content-between py-3"><span>Loại bìa:</span> <span class="fw-bold">{{ $book->cover_type ?? '---' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between py-3"><span>Kích thước:</span> <span class="fw-bold">{{ $book->dimensions ?? '---' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between py-3"><span>Số trang:</span> <span class="fw-bold">{{ $book->page_count ?? '---' }} trang</span></li>
                            <li class="list-group-item d-flex justify-content-between py-3">
                                <span>Ngày XB:</span> 
                                <span class="fw-bold">{{ $book->published_date ? \Carbon\Carbon::parse($book->published_date)->format('d/m/Y') : '---' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between py-3 bg-white">
                                <span>Tồn kho:</span> 
                                <span class="fw-bold {{ $book->quantity > 0 ? 'text-success' : 'text-danger' }}">{{ $book->quantity }} cuốn</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Cột Ebook --}}
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush border rounded-3 small h-100 shadow-sm">
                            <li class="list-group-item bg-light fw-bold text-secondary py-3"><i class="fas fa-tablet-alt me-2"></i> Phiên bản Ebook</li>
                            <li class="list-group-item d-flex justify-content-between py-3">
                                <span>Giá Ebook:</span> 
                                <span class="fw-bold text-primary">{{ $book->ebook_price ? number_format($book->ebook_price) . ' ₫' : 'Chưa bán' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between py-3"><span>Dung lượng:</span> <span class="fw-bold">{{ $book->file_size ?? '---' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between py-3"><span>Đọc thử:</span> <span class="fw-bold">{{ $book->preview_pages ?? 0 }} trang</span></li>
                            <li class="list-group-item d-flex justify-content-between py-3"><span>Font chữ:</span> <span class="fw-bold">{{ $book->font_family ?? '---' }}</span></li>
                            <li class="list-group-item d-flex justify-content-between py-2 align-items-center bg-white">
                                <span>Tệp đính kèm:</span> 
                                <div class="text-end">
                                    @if($book->file_preview) <span class="badge bg-success mb-1 d-block"><i class="fas fa-file-pdf"></i> Có file đọc thử</span> @endif
                                    @if($book->file_ebook) <span class="badge bg-primary d-block"><i class="fas fa-download"></i> Có file Ebook</span> @endif
                                    @if(!$book->file_preview && !$book->file_ebook) <span class="badge bg-secondary">Chưa tải lên file</span> @endif
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- 5. MÔ TẢ --}}
                <div class="mb-3">
                    <label class="fw-bold text-secondary text-uppercase small mb-2"><i class="fas fa-align-left me-1"></i> Mô tả nội dung</label>
                    <div class="bg-light border p-3 rounded text-dark" style="min-height: 150px; line-height: 1.8;">
                        {!! $book->description ?? '<em class="text-muted">Đang cập nhật mô tả...</em>' !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CỘT PHẢI: ẢNH & THÔNG TIN HỆ THỐNG --}}
    <div class="col-md-4">
        {{-- Card Ảnh --}}
        <div class="card shadow border-0 rounded-4 mb-4">
            <div class="card-body text-center p-4">
                <label class="fw-bold text-secondary text-uppercase small mb-3">Ảnh bìa sách</label>
                
                @php
                    $imagePath = $book->image;
                    if($imagePath && !str_starts_with($imagePath, 'http')) {
                        if (!str_contains($imagePath, 'uploads/')) {
                            $imagePath = 'uploads/' . $imagePath;
                        }
                    }
                @endphp

                <div class="rounded-3 overflow-hidden shadow-sm border mx-auto" style="max-width: 100%;">
                    @if($book->image)
                        <img src="{{ asset($imagePath) }}" class="img-fluid w-100" style="object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center text-muted" style="height: 300px;">
                            <i class="fas fa-image fs-1 opacity-25"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card Thông tin hệ thống --}}
        <div class="card shadow border-0 rounded-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-server me-1"></i> Thống kê hệ thống</h6>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">ID Sách:</span>
                        <span class="fw-bold">#{{ $book->id }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Đã bán (Sách in):</span>
                        <span class="fw-bold text-success">{{ $book->total_sold ?? 0 }} cuốn</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Đã bán (Ebook):</span>
                        <span class="fw-bold text-primary">{{ $book->ebook_sold ?? 0 }} lượt</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Ngày tạo:</span>
                        <span>{{ $book->created_at ? $book->created_at->format('d/m/Y H:i') : '---' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Lần sửa cuối:</span>
                        <span>{{ $book->updated_at ? $book->updated_at->format('d/m/Y H:i') : '---' }}</span>
                    </li>
                </ul>

                {{-- 🔥 KIỂM TRA QUYỀN: ROLE 0, 1, 4 KHÔNG ĐƯỢC THẤY NÚT SỬA 🔥 --}}
                @if(!in_array(Auth::user()->role, [0, 1, 4]))
                    <hr>
                    <div class="d-grid">
                        <a href="{{ route('books.edit', $book->id) }}" class="btn btn-warning fw-bold shadow-sm rounded-pill py-2">
                            <i class="fas fa-edit me-2"></i> Chỉnh sửa sách
                        </a>
                    </div>
                @else
                    <hr>
                    <div class="alert alert-secondary text-center small mb-0 p-2">
                        <i class="fas fa-lock text-danger me-1"></i> Quyền của bạn chỉ được xem
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection