@extends('client.layouts.master')
@section('content')
<div class="container py-5">
    <h2 class="fw-bold text-center mb-5">Tin Tức & Sự Kiện</h2>
    <div class="row g-4">
        @foreach($posts as $post)
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <img src="{{ asset($post->thumbnail) }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="fw-bold"><a href="{{ route('posts.show', $post->id) }}" class="text-dark text-decoration-none">{{ $post->title }}</a></h5>
                    <p class="text-muted small">{{ Str::limit($post->short_description, 100) }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $posts->links() }}</div>
</div>
@endsection