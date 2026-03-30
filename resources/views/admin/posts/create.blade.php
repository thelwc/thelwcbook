@extends('admin.layouts.layout')

@section('content')
<div class="card p-4">
    <h3 class="fw-bold mb-4">✍️ Viết bài mới</h3>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label class="fw-bold">Tiêu đề</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Mô tả ngắn</label>
                    <textarea name="short_description" class="form-control" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Nội dung (Dán ảnh thoải mái)</label>
                    <textarea id="summernote" name="content"></textarea>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="fw-bold">Ảnh bìa</label>
                    <input type="file" name="thumbnail" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Đăng bài</button>
            </div>
        </div>
    </form>
</div>

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    $('#summernote').summernote({
        placeholder: 'Viết nội dung vào đây, có thể copy ảnh từ Word/Web dán vào...',
        tabsize: 2,
        height: 400
    });
</script>
@endsection