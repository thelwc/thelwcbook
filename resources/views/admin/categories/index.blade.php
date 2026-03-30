@extends('admin.layouts.layout')

@section('content')

{{-- HEADER: TIÊU ĐỀ VÀ NÚT CÔNG CỤ --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <h3 class="fw-bold mb-0 text-dark">📂 Quản Lý Thể Loại</h3>
    
    <div class="d-flex flex-wrap gap-2">
        {{-- EXPORT --}}
        @if(in_array(Auth::user()->role, [1, 2]))
            <a href="{{ route('categories.export') }}" class="btn btn-success text-white shadow-sm flex-grow-1 flex-md-grow-0 text-nowrap fw-bold">
                <i class="fas fa-file-excel me-1"></i> Xuất Excel
            </a>
        @endif

        {{-- IMPORT & THÊM MỚI --}}
        @if(in_array(Auth::user()->role, [2, 3]))
            <form action="{{ route('categories.import') }}" method="POST" enctype="multipart/form-data" class="d-inline flex-grow-1 flex-md-grow-0">
                @csrf
                <input type="file" name="file" id="catFileInput" style="display: none;" accept=".xlsx, .xls" onchange="this.form.submit()">
                <button type="button" class="btn btn-warning shadow-sm w-100 text-nowrap fw-bold" onclick="document.getElementById('catFileInput').click()">
                    <i class="fas fa-file-import me-1"></i> Nhập Excel
                </button>
            </form>

            <a href="{{ route('categories.create') }}" class="btn btn-primary shadow-sm flex-grow-1 flex-md-grow-0 text-nowrap fw-bold">
                <i class="fas fa-plus me-1"></i> Thêm mới
            </a>
        @endif
    </div>
</div>

{{-- KHU VỰC THÔNG BÁO --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3" role="alert">
        <i class="fas fa-check-circle me-2"></i> <strong>Thành công!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i> <strong>Úi chà!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- KHU VỰC HIỂN THỊ DỮ LIỆU --}}
<div class="card shadow-sm border-0 rounded-4 overflow-hidden bg-transparent bg-md-white">
    <div class="card-body p-0">
        
        {{-- 🔥 1. GIAO DIỆN DESKTOP (TABLE) 🔥 --}}
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary text-uppercase text-xs fw-bold">
                    <tr>
                        <th class="py-3 ps-4" style="width: 80px;">ID</th>
                        <th class="py-3">Tên danh mục</th>
                        <th class="py-3 text-center" style="width: 150px;">Số lượng sách</th>
                        <th class="py-3 text-end pe-4" style="width: 200px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cate)
                    <tr class="border-bottom">
                        <td class="ps-4 fw-bold text-muted">#{{ $cate->id }}</td>
                        <td>
                            <span class="fw-bold text-primary" style="font-size: 1.05rem;">{{ $cate->name }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary rounded-pill px-3 py-2">{{ $cate->books->count() }} cuốn</span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('books.index', ['category_id' => $cate->id]) }}" class="btn btn-sm btn-info text-white shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Xem sách">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if(in_array(Auth::user()->role, [2, 3]))
                                    <a href="{{ route('categories.edit', $cate->id) }}" class="btn btn-sm btn-warning text-dark shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('categories.destroy', $cate->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa danh mục này?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="fas fa-folder-open fa-3x opacity-25 mb-3"></i>
                            <h6 class="fw-bold">Chưa có danh mục nào</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 🔥 2. GIAO DIỆN MOBILE (CARD) 🔥 --}}
        <div class="d-md-none p-2 p-sm-3">
            @forelse($categories as $cate)
                <div class="card mb-3 shadow-sm border-0 rounded-4">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-3 border-bottom pb-3">
                            <div>
                                <span class="badge bg-light text-secondary border fw-bold mb-1">ID: #{{ $cate->id }}</span>
                                <h5 class="fw-bold text-primary mb-0 text-wrap lh-base">{{ $cate->name }}</h5>
                            </div>
                            <span class="badge bg-secondary rounded-pill px-3 py-2 flex-shrink-0 ms-2 mt-1">{{ $cate->books->count() }} cuốn</span>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('books.index', ['category_id' => $cate->id]) }}" class="btn btn-sm btn-info text-white flex-grow-1 fw-bold rounded-pill">
                                <i class="fas fa-eye me-1"></i> Xem sách
                            </a>

                            @if(in_array(Auth::user()->role, [2, 3]))
                                <a href="{{ route('categories.edit', $cate->id) }}" class="btn btn-sm btn-outline-warning text-dark flex-grow-1 fw-bold rounded-pill">
                                    <i class="fas fa-edit me-1"></i> Sửa
                                </a>
                                
                                <form action="{{ route('categories.destroy', $cate->id) }}" method="POST" class="d-inline flex-grow-1" onsubmit="return confirm('Xóa danh mục này?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100 fw-bold rounded-pill">
                                        <i class="fas fa-trash me-1"></i> Xóa
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 bg-white rounded-4 border">
                    <i class="fas fa-folder-open fa-3x text-muted opacity-25 mb-3"></i>
                    <h6 class="fw-bold text-muted">Chưa có danh mục nào</h6>
                </div>
            @endforelse
        </div>

    </div>
</div>

@endsection