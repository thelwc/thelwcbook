@extends('admin.layouts.layout')

@section('content')
{{-- HEADER ĐÃ FIX LỖI KÉO DÀI TRÊN PC --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <h4 class="fw-bold mb-0 text-dark d-flex align-items-center">
        <i class="fas fa-comments text-primary me-2"></i> Kiểm Duyệt Bình Luận
    </h4>
    
    {{-- BỘ LỌC TÌM KIẾM NHANH (Đã bỏ w-100 để không bị kéo giãn) --}}
    <div class="btn-group shadow-sm" role="group">
        <a href="{{ route('admin.reviews.index') }}" class="btn {{ !request('status') ? 'btn-primary fw-bold' : 'btn-light border' }}">
            Tất cả
        </a>
        <a href="{{ route('admin.reviews.index', ['status' => 'unreplied']) }}" class="btn {{ request('status') == 'unreplied' ? 'btn-primary fw-bold' : 'btn-light border' }}">
            <i class="fas fa-exclamation-circle text-danger d-none d-sm-inline me-1"></i> Chưa Rep
        </a>
        <a href="{{ route('admin.reviews.index', ['status' => 'replied']) }}" class="btn {{ request('status') == 'replied' ? 'btn-primary fw-bold' : 'btn-light border' }}">
            <i class="fas fa-check-circle text-success d-none d-sm-inline me-1"></i> Đã Rep
        </a>
    </div>
</div>

{{-- THÔNG BÁO --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@error('admin_reply')
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3">
        <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@enderror

{{-- KHU VỰC HIỂN THỊ DỮ LIỆU --}}
<div class="card shadow-sm border-0 rounded-4 overflow-hidden bg-transparent bg-lg-white">
    <div class="card-body p-0 p-lg-0">
        
        {{-- 🔥 1. GIAO DIỆN DESKTOP (TABLE) 🔥 --}}
        <div class="table-responsive d-none d-lg-block">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary text-uppercase small fw-bold border-bottom border-light">
                    <tr>
                        <th class="ps-4 py-3" style="width: 180px;">Khách hàng</th>
                        <th class="py-3" style="width: 200px;">Sản phẩm</th>
                        <th class="py-3" style="width: 35%;">Nội dung đánh giá</th>
                        <th class="py-3 text-center" style="width: 100px;">Sao</th>
                        <th class="text-end pe-4 py-3" style="width: 150px;">Trạng thái / Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                    <tr class="border-bottom border-light">  
                        {{-- 1. Khách Hàng --}}
                        <td class="ps-4">
                            <div class="fw-bold text-dark fs-6">{{ $review->user->name ?? 'Ẩn danh' }}</div>
                            <small class="text-muted d-block"><i class="far fa-clock me-1"></i>{{ $review->created_at->format('d/m/Y H:i') }}</small>
                            
                            @if(empty($review->admin_reply))
                                <div class="mt-1 d-flex flex-wrap gap-1">
                                    @if($review->created_at > now()->subHours(24))
                                        <span class="badge bg-primary" style="font-size: 0.65rem;"><i class="fas fa-sparkles"></i> Mới</span>
                                    @endif
                                    @if($review->updated_at && $review->created_at && $review->updated_at->gt($review->created_at->addSeconds(5)))
                                        <span class="badge bg-warning text-dark" style="font-size: 0.65rem;"><i class="fas fa-edit"></i> Đã sửa</span>
                                    @endif
                                </div>
                            @endif
                        </td>

                        {{-- 2. Sản Phẩm --}}
                        <td>
                            <div class="d-flex align-items-center">
                                @if($review->book)
                                    @php
                                        $img = $review->book->image;
                                        if($img && !str_starts_with($img, 'http') && !str_contains($img, 'uploads/')) $img = 'uploads/' . $img;
                                    @endphp
                                    <img src="{{ asset($img) }}" class="rounded-3 shadow-sm border me-2 flex-shrink-0" width="45" height="65" style="object-fit: cover;">
                                    <a href="{{ route('admin.books.show', $review->book->id) }}" class="fw-bold text-dark text-decoration-none hover-primary text-wrap" style="max-width: 180px; font-size: 0.95rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                                        {{ $review->book->title }}
                                    </a>
                                @else
                                    <div class="bg-light rounded-3 border d-flex align-items-center justify-content-center me-2 flex-shrink-0" style="width: 45px; height: 65px;">
                                        <i class="fas fa-unlink text-muted"></i>
                                    </div>
                                    <span class="text-muted fst-italic small">Sách đã xóa</span>
                                @endif
                            </div>
                        </td>

                        {{-- 3. Nội dung --}}
                        <td class="py-3">
                            <div class="bg-light p-3 rounded-4 border text-dark lh-base" style="font-size: 0.95rem; word-break: break-word;">
                                {{ $review->comment }}
                            </div>
                            
                            @if($review->admin_reply)
                                <div class="mt-2 p-3 rounded-4 border-start border-info border-4 shadow-sm" style="background-color: #f0f9ff; font-size: 0.95rem; word-break: break-word;">
                                    <div class="fw-bold text-info mb-1" style="font-size: 0.85rem;"><i class="fas fa-headset me-1"></i> ADMIN PHẢN HỒI</div>
                                    <span class="text-dark lh-base">{{ $review->admin_reply }}</span>
                                </div>
                            @endif
                        </td>

                        {{-- 4. Số sao --}}
                        <td class="text-center">
                            <div class="text-warning text-nowrap d-flex flex-column align-items-center">
                                <span class="fw-bold fs-5 text-dark mb-1">{{ $review->rating }}.0</span>
                                <div>
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-muted opacity-25' }}" style="font-size: 0.8rem;"></i>
                                    @endfor
                                </div>
                            </div>
                        </td>

                        {{-- 5. Hành động --}}
                        <td class="text-end pe-4">
                            <div class="mb-3 d-flex justify-content-end">
                                @if($review->status == 'hidden')
                                    <span class="badge bg-secondary px-3 py-2 rounded-pill shadow-sm"><i class="fas fa-eye-slash"></i> Đang Ẩn</span>
                                @elseif($review->admin_reply)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill"><i class="fas fa-check"></i> Đã rep</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill"><i class="fas fa-exclamation"></i> Chưa rep</span>
                                @endif
                            </div>

                            @if(in_array(Auth::user()->role, [2, 3, 4]))
                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                    {{-- Nút Ẩn/Hiện --}}
                                    <form action="{{ route('admin.reviews.toggle_status', $review->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        @if($review->status == 'hidden')
                                            <button class="btn btn-sm btn-outline-success rounded-circle shadow-sm" style="width: 32px; height: 32px;" title="Hiển thị lại">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-outline-warning rounded-circle shadow-sm" style="width: 32px; height: 32px;" title="Ẩn bình luận">
                                                <i class="fas fa-eye-slash"></i>
                                            </button>
                                        @endif
                                    </form>

                                    {{-- Nút Rep --}}
                                    @if(!$review->admin_reply)
                                        <button type="button" class="btn btn-sm btn-primary shadow-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#replyModal{{ $review->id }}" title="Viết phản hồi">
                                            <i class="fas fa-reply me-1"></i> Rep
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-info text-white shadow-sm rounded-circle" style="width: 32px; height: 32px;" data-bs-toggle="modal" data-bs-target="#replyModal{{ $review->id }}" title="Sửa phản hồi">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.reviews.delete_reply', $review->id) }}" method="POST" onsubmit="return confirm('Xóa câu trả lời của Admin?');">
                                            @csrf @method('PUT')
                                            <button class="btn btn-sm btn-outline-secondary rounded-circle shadow-sm" style="width: 32px; height: 32px;" title="Xóa phản hồi">
                                                <i class="fas fa-eraser"></i>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Nút Xóa Vĩnh Viễn --}}
                                    <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Xóa vĩnh viễn bình luận này? Hành động này không thể hoàn tác.');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger rounded-circle shadow-sm" style="width: 32px; height: 32px;" title="Xóa vĩnh viễn">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="badge bg-light text-secondary border"><i class="fas fa-lock me-1"></i> Chỉ xem</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-comment-slash fa-3x opacity-25 mb-3"></i>
                            <h6 class="fw-bold">Chưa có đánh giá nào</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 🔥 2. GIAO DIỆN MOBILE (CARD CHAT) 🔥 --}}
        <div class="d-lg-none p-2 p-sm-3">
            @forelse($reviews as $review)
                <div class="card mb-4 shadow-sm border-0 rounded-4 overflow-hidden position-relative">
                    
                    {{-- Dải màu mép trái báo hiệu Trạng Thái --}}
                    <div class="position-absolute top-0 bottom-0 start-0" style="width: 5px; background-color: {{ $review->status == 'hidden' ? '#6c757d' : ($review->admin_reply ? '#198754' : '#dc3545') }};"></div>
                    
                    <div class="card-body p-3 ps-4">
                        
                        {{-- Thông tin Khách Hàng & Sách --}}
                        <div class="d-flex justify-content-between align-items-start mb-3 border-bottom border-light pb-3">
                            <div>
                                <h6 class="fw-bold text-dark mb-1"><i class="fas fa-user-circle text-muted me-1"></i> {{ $review->user->name ?? 'Ẩn danh' }}</h6>
                                <div class="small text-muted mb-2"><i class="far fa-clock me-1"></i> {{ $review->created_at->format('H:i d/m/Y') }}</div>
                                
                                <div class="text-warning mb-2" style="font-size: 0.85rem;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-muted opacity-25' }}"></i>
                                    @endfor
                                    <span class="text-dark fw-bold ms-1">({{ $review->rating }}.0)</span>
                                </div>
                            </div>
                            
                            {{-- Sách liên quan thu nhỏ --}}
                            @if($review->book)
                                @php $img = $review->book->image; if($img && !str_starts_with($img, 'http') && !str_contains($img, 'uploads/')) $img = 'uploads/' . $img; @endphp
                                <a href="{{ route('admin.books.show', $review->book->id) }}" class="text-decoration-none">
                                    <img src="{{ asset($img) }}" class="rounded-3 shadow-sm border" width="45" height="65" style="object-fit: cover;" title="{{ $review->book->title }}">
                                </a>
                            @endif
                        </div>

                        {{-- Nội Dung Review --}}
                        <div class="bg-light p-3 rounded-4 border text-dark lh-base mb-3 position-relative" style="font-size: 0.95rem; word-break: break-word;">
                            {{-- Mũi tên hộp chat --}}
                            <div class="position-absolute" style="top: -10px; left: 20px; border-width: 0 10px 10px 10px; border-style: solid; border-color: transparent transparent #f8f9fa transparent;"></div>
                            "{{ $review->comment }}"
                        </div>
                        
                        {{-- Admin Phản Hồi --}}
                        @if($review->admin_reply)
                            <div class="p-3 rounded-4 border-start border-info border-4 shadow-sm mb-3 ms-3 position-relative" style="background-color: #f0f9ff; font-size: 0.95rem; word-break: break-word;">
                                <div class="fw-bold text-info mb-1" style="font-size: 0.85rem;"><i class="fas fa-headset me-1"></i> ADMIN PHẢN HỒI</div>
                                <span class="text-dark lh-base">{{ $review->admin_reply }}</span>
                            </div>
                        @endif

                        {{-- Nút bấm Mobile (Flexbox dàn ngang 100%) --}}
                        <div class="d-flex flex-wrap gap-2 pt-2 border-top border-light">
                            @if(in_array(Auth::user()->role, [2, 3, 4]))
                                
                                {{-- Nút Ẩn / Hiện --}}
                                <form action="{{ route('admin.reviews.toggle_status', $review->id) }}" method="POST" class="flex-fill">
                                    @csrf @method('PUT')
                                    <button class="btn btn-sm w-100 rounded-pill fw-bold py-2 shadow-sm {{ $review->status == 'hidden' ? 'btn-success' : 'btn-outline-warning text-dark' }}">
                                        @if($review->status == 'hidden') <i class="fas fa-eye me-1"></i> Hiện lại @else <i class="fas fa-eye-slash me-1"></i> Ẩn đi @endif
                                    </button>
                                </form>

                                {{-- Nút Rep / Sửa Rep --}}
                                @if(!$review->admin_reply)
                                    <button type="button" class="btn btn-sm btn-primary w-100 flex-fill rounded-pill fw-bold py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#replyModal{{ $review->id }}">
                                        <i class="fas fa-reply me-1"></i> Trả lời
                                    </button>
                                @else
                                    <button type="button" class="btn btn-sm btn-info text-white w-100 flex-fill rounded-pill fw-bold py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#replyModal{{ $review->id }}">
                                        <i class="fas fa-edit me-1"></i> Sửa Rep
                                    </button>
                                @endif

                                {{-- Nút Xóa --}}
                                <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="flex-fill" onsubmit="return confirm('Xóa vĩnh viễn bình luận này?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger w-100 rounded-pill fw-bold py-2 shadow-sm">
                                        <i class="fas fa-trash-alt"></i> Xóa
                                    </button>
                                </form>
                            @else
                                <div class="badge bg-light text-secondary border w-100 py-2 rounded-pill"><i class="fas fa-lock me-1"></i> Chỉ xem</div>
                            @endif
                        </div>

                    </div>
                </div>
            @empty
                <div class="text-center py-5 bg-white rounded-4 border shadow-sm">
                    <i class="fas fa-comment-slash fa-3x text-muted opacity-25 mb-3"></i>
                    <h6 class="fw-bold text-muted">Chưa có đánh giá nào</h6>
                </div>
            @endforelse
        </div>

    </div>
</div>

<div class="mt-4 d-flex justify-content-center justify-content-lg-end">
    {{ $reviews->appends(request()->query())->links() }}
</div>

{{-- 🔥 ĐÂY LÀ PHẦN MODAL TRẢ LỜI ĐÃ ĐƯỢC CHÈN LẠI VÀO CUỐI FILE ĐỂ BẤM LÊN 100% NHÉ 🔥 --}}
@foreach($reviews as $review)
    <div class="modal fade text-start" id="replyModal{{ $review->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title fw-bold text-primary">
                        <i class="fas fa-reply me-2"></i> Phản hồi khách hàng
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.reviews.reply', $review->id) }}" method="POST">
                    @csrf
                    <div class="modal-body p-4 pt-2">
                        <div class="mb-3 p-3 bg-light rounded-3 border">
                            <small class="text-muted d-block fw-bold mb-1"><i class="fas fa-user me-1"></i> Khách: {{ $review->user->name ?? 'Ẩn danh' }}</small>
                            <span class="text-secondary" style="font-size: 0.95rem; word-break: break-word;">{{ $review->comment }}</span>
                        </div>
                        <div class="form-group mb-0">
                            <label class="fw-bold mb-2 text-dark">Nội dung phản hồi từ Admin <span class="text-danger">*</span></label>
                            <textarea name="admin_reply" class="form-control rounded-3 border-secondary border-opacity-25 shadow-sm" rows="4" placeholder="Nhập câu trả lời của bạn gửi đến khách hàng..." required>{{ $review->admin_reply }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0 pb-4 pe-4">
                        <button type="button" class="btn btn-light fw-bold px-4 rounded-pill border shadow-sm" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="submit" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm">Gửi phản hồi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@endsection