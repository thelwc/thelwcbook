@extends('admin.layouts.layout')

@section('content')
<div class="card p-4">
    <div class="d-flex justify-content-between mb-4">
        <h3 class="fw-bold">✏️ Chỉnh sửa bài viết</h3>
        <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>

    <form action="{{ route('admin.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT') {{-- Bắt buộc để gửi request Update --}}
        
        <div class="row">
            <div class="col-md-8">
                <div class="mb-3">
                    <label class="fw-bold">Tiêu đề</label>
                    <input type="text" name="title" class="form-control" value="{{ $post->title }}" required>
                </div>
                
                <div class="mb-3">
                    <label class="fw-bold">Mô tả ngắn</label>
                    <textarea name="short_description" class="form-control" rows="3">{{ $post->short_description }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Nội dung</label>
                    {{-- Hiển thị nội dung cũ trong Summernote --}}
                    <textarea id="summernote" name="content">{!! $post->content !!}</textarea>
                </div>
            </div>

            <div class="col-md-4">
                <div class="mb-3">
                    <label class="fw-bold">Ảnh bìa hiện tại</label> <br>
                    <img src="{{ asset($post->thumbnail) }}" class="rounded border mb-2" style="width: 100%; height: 200px; object-fit: cover;">
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Đổi ảnh mới (Nếu cần)</label>
                    <input type="file" name="thumbnail" class="form-control">
                </div>

                <button type="submit" class="btn btn-warning w-100 fw-bold text-white">Lưu thay đổi</button>
            </div>
        </div>
    </form>
</div>

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    $(document).ready(function() {
        $('#summernote').summernote({
            placeholder: 'Nội dung bài viết...',
            tabsize: 2,
            height: 400
        });

        // Bắt sự kiện khi Form được Submit
        $('form').on('submit', function(e) {
            // Lấy nội dung chữ (text thuần) bên trong Summernote
            if ($('#summernote').summernote('isEmpty')) {
                // Hủy submit form
                e.preventDefault();
                // Hiện thông báo lỗi
                alert('Nội dung bài viết không được để trống!');
                // Focus lại vào khung soạn thảo
                $('#summernote').summernote('focus');
            }
        });
    });
</script>
@endsection