@extends('admin.layouts.layout')

@section('content')
{{-- HEADER --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <h4 class="fw-bold mb-0 text-dark d-flex align-items-center">
        <i class="fas fa-newspaper text-primary me-2"></i> Quản Lý Tin Tức
    </h4>
    
    {{-- PHÂN QUYỀN: QUẢN LÝ (2) & KIỂM DUYỆT (4) ĐƯỢC VIẾT BÀI --}}
    @if(in_array(Auth::user()->role, [2, 4]))
        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary shadow-sm fw-bold rounded-pill px-4">
            <i class="fas fa-pen-nib me-1"></i> Viết bài mới
        </a>
    @endif
</div>

{{-- Hiển thị thông báo --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3">
        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- KHU VỰC HIỂN THỊ DỮ LIỆU --}}
<div class="card shadow-sm border-0 rounded-4 overflow-hidden bg-transparent bg-lg-white">
    <div class="card-body p-0 p-lg-0">
        
        {{-- 🔥 1. GIAO DIỆN DESKTOP (TABLE) 🔥 --}}
        <div class="table-responsive d-none d-lg-block">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary text-uppercase small fw-bold">
                    <tr>
                        <th class="py-3 ps-4" style="width: 120px;">Hình ảnh</th>
                        <th class="py-3" style="width: 35%;">Tiêu đề & Nội dung</th>
                        <th class="py-3" style="width: 150px;">Tác giả</th>
                        <th class="py-3" style="width: 120px;">Ngày đăng</th>
                        <th class="py-3 text-center" style="width: 130px;">Trạng thái</th>
                        <th class="py-3 text-end pe-4" style="width: 150px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                    <tr class="border-bottom border-light">
                        {{-- 1. HÌNH ẢNH --}}
                        <td class="ps-4 py-3">
                            @if($post->thumbnail)
                                <img src="{{ asset($post->thumbnail) }}" class="rounded-3 shadow-sm border" style="width: 90px; height: 60px; object-fit: cover;" alt="Thumbnail">
                            @else
                                <div class="bg-light text-muted d-flex align-items-center justify-content-center rounded-3 border" style="width: 90px; height: 60px;">
                                    <i class="far fa-image"></i>
                                </div>
                            @endif
                        </td>

                        {{-- 2. TIÊU ĐỀ --}}
                        <td>
                            <div class="fw-bold text-dark mb-1 text-wrap" style="font-size: 1.05rem; line-height: 1.4;">{{ $post->title }}</div>
                            <small class="text-muted d-block text-truncate" style="max-width: 300px;">{{ $post->short_description }}</small>
                        </td>

                        {{-- 3. TÁC GIẢ --}}
                        <td>
                            <span class="badge bg-info bg-opacity-10 text-info border border-info px-2 py-1">
                                <i class="fas fa-user-edit me-1"></i> {{ $post->user->name ?? 'Admin' }}
                            </span>
                        </td>

                        {{-- 4. NGÀY ĐĂNG --}}
                        <td>
                            <span class="text-muted small fw-bold"><i class="far fa-calendar-alt me-1"></i>{{ $post->created_at->format('d/m/Y') }}</span>
                        </td>
                        
                        {{-- 5. TRẠNG THÁI --}}
                        <td class="text-center">
                            @if(in_array(Auth::user()->role, [2, 4]))
                                <form action="{{ route('admin.posts.toggle', $post->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm rounded-pill fw-bold px-3 shadow-sm w-100 {{ $post->status == 1 ? 'btn-success' : 'btn-outline-secondary' }}" title="Đổi trạng thái">
                                        @if($post->status == 1) <i class="fas fa-eye me-1"></i> Hiện
                                        @else <i class="fas fa-eye-slash me-1"></i> Ẩn @endif
                                    </button>
                                </form>
                            @else
                                <span class="badge rounded-pill px-3 py-2 w-100 {{ $post->status == 1 ? 'bg-success' : 'bg-secondary' }}">
                                    {!! $post->status == 1 ? '<i class="fas fa-eye me-1"></i> Đang hiện' : '<i class="fas fa-eye-slash me-1"></i> Đang ẩn' !!}
                                </span>
                            @endif
                        </td>

                        {{-- 6. HÀNH ĐỘNG --}}
                        <td class="text-end pe-4">
                            @if(in_array(Auth::user()->role, [2, 4]))
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-sm btn-outline-warning text-dark rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa bài viết này?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;" title="Xóa">
                                            <i class="fas fa-trash"></i>
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
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-newspaper fa-3x opacity-25 mb-3"></i>
                            <h6 class="fw-bold">Chưa có bài viết nào</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 🔥 2. GIAO DIỆN MOBILE (CARD DỌC) 🔥 --}}
        <div class="d-lg-none p-2 p-sm-3">
            @forelse($posts as $post)
                <div class="card mb-3 shadow-sm border-0 rounded-4 overflow-hidden">
                    <div class="card-body p-3">
                        <div class="d-flex mb-3">
                            {{-- Ảnh Thumbnail --}}
                            <div class="flex-shrink-0 me-3">
                                @if($post->thumbnail)
                                    <img src="{{ asset($post->thumbnail) }}" class="rounded-3 shadow-sm border" style="width: 80px; height: 80px; object-fit: cover;" alt="Thumbnail">
                                @else
                                    <div class="bg-light text-muted d-flex align-items-center justify-content-center rounded-3 border" style="width: 80px; height: 80px;">
                                        <i class="far fa-image fs-4"></i>
                                    </div>
                                @endif
                            </div>

                            {{-- Tiêu đề, Tác giả, Ngày đăng --}}
                            <div class="flex-grow-1 overflow-hidden">
                                <h6 class="fw-bold text-dark text-wrap mb-2" style="line-height: 1.4; font-size: 0.95rem;">{{ $post->title }}</h6>
                                <div class="d-flex flex-wrap gap-2 mb-1">
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info small"><i class="fas fa-user-edit me-1"></i> {{ $post->user->name ?? 'Admin' }}</span>
                                    <span class="badge bg-light text-secondary border small"><i class="far fa-calendar-alt me-1"></i> {{ $post->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Mô tả ngắn --}}
                        <div class="text-muted small mb-3 lh-base" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $post->short_description }}
                        </div>

                        {{-- Nút Trạng thái & Hành động (Flexbox dàn ngang) --}}
                        <div class="d-flex flex-wrap gap-2 pt-3 border-top border-light">
                            {{-- Nút Ẩn/Hiện --}}
                            @if(in_array(Auth::user()->role, [2, 4]))
                                <form action="{{ route('admin.posts.toggle', $post->id) }}" method="POST" class="flex-grow-1">
                                    @csrf
                                    <button type="submit" class="btn btn-sm rounded-pill fw-bold w-100 shadow-sm py-2 {{ $post->status == 1 ? 'btn-success' : 'btn-outline-secondary' }}">
                                        @if($post->status == 1) <i class="fas fa-eye me-1"></i> Đang hiện
                                        @else <i class="fas fa-eye-slash me-1"></i> Đang ẩn @endif
                                    </button>
                                </form>
                            @else
                                <div class="flex-grow-1">
                                    <span class="badge rounded-pill px-3 py-2 w-100 d-block {{ $post->status == 1 ? 'bg-success' : 'bg-secondary' }}">
                                        {!! $post->status == 1 ? '<i class="fas fa-eye me-1"></i> Đang hiện' : '<i class="fas fa-eye-slash me-1"></i> Đang ẩn' !!}
                                    </span>
                                </div>
                            @endif

                            {{-- Nút Sửa & Xóa --}}
                            @if(in_array(Auth::user()->role, [2, 4]))
                                <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-sm btn-outline-warning text-dark rounded-pill fw-bold px-4 py-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa bài viết này?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill fw-bold px-4 py-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 bg-white rounded-4 border shadow-sm">
                    <i class="fas fa-newspaper fa-3x text-muted opacity-25 mb-3"></i>
                    <h6 class="fw-bold text-muted">Chưa có bài viết nào</h6>
                </div>
            @endforelse
        </div>

    </div>
</div>

<div class="mt-4 d-flex justify-content-center justify-content-lg-end">
    {{ $posts->links() }}
</div>
@endsection