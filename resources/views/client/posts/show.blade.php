@extends('client.layouts.master')
@section('title', $post->title)

@section('content')
<div class="container py-5">
    <div class="row">
        {{-- CỘT TRÁI: NỘI DUNG BÀI VIẾT --}}
        <div class="col-lg-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('posts.index') }}" class="text-decoration-none text-muted">Tin tức</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chi tiết</li>
                </ol>
            </nav>

            <h1 class="fw-bold mb-3">{{ $post->title }}</h1>
            
            <div class="d-flex align-items-center text-muted mb-4 small">
                <i class="far fa-calendar-alt me-2"></i> {{ $post->created_at->format('d/m/Y') }}
                <span class="mx-2">|</span>
                <i class="far fa-eye me-2"></i> {{ $post->views }} lượt xem
                <span class="mx-2">|</span>
                <i class="far fa-user me-2"></i> {{ $post->author ? $post->author->name : 'Admin' }}
            </div>

            {{-- Nội dung chi tiết (Hiển thị HTML từ Summernote) --}}
            <div class="content-body lh-lg">
                {!! $post->content !!}
            </div>

            {{-- Nút chia sẻ / Quay lại --}}
            <div class="mt-5 pt-4 border-top">
                <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary rounded-pill">
                    <i class="fas fa-arrow-left me-2"></i> Quay lại tin tức
                </a>
            </div>
        </div>

        {{-- CỘT PHẢI: BÀI VIẾT LIÊN QUAN --}}
        <div class="col-lg-4 mt-5 mt-lg-0">
            {{-- 🔥 ĐÃ SỬA: z-index: 1 và top: 120px --}}
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 120px; z-index: 1;">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0">Bài viết mới nhất</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($relatedPosts as $related)
                        <li class="list-group-item p-3 border-light">
                            <div class="d-flex gap-3">
                                <img src="{{ asset($related->thumbnail) }}" class="rounded" style="width: 80px; height: 60px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-1" style="font-size: 0.95rem;">
                                        <a href="{{ route('posts.show', $related->id) }}" class="text-dark text-decoration-none fw-bold">
                                            {{ Str::limit($related->title, 45) }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">{{ $related->created_at->format('d/m/Y') }}</small>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CSS tùy chỉnh cho ảnh trong bài viết để không bị vỡ khung --}}
<style>
    .content-body img {
        max-width: 100% !important;
        height: auto !important;
        border-radius: 8px;
        margin: 10px 0;
    }
</style>
@endsection