@extends('admin.layouts.layout')

@section('header', 'Quản lý Sách')

@section('content')

{{-- 🔥 THANH CÔNG CỤ & BỘ LỌC 🔥 --}}
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body p-3">
        <form action="{{ route('books.index') }}" method="GET" class="row g-3 align-items-center">
            
            {{-- Tìm kiếm --}}
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="keyword" class="form-control border-start-0" 
                           placeholder="Tên sách, tác giả..." value="{{ request('keyword') }}">
                </div>
            </div>

            {{-- Lọc Danh mục --}}
            <div class="col-md-3">
                <select name="category_id" class="form-select cursor-pointer" onchange="this.form.submit()">
                    <option value="">-- Tất cả danh mục --</option>
                    @foreach($categories as $cate)
                        <option value="{{ $cate->id }}" {{ request('category_id') == $cate->id ? 'selected' : '' }}>
                            {{ $cate->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Lọc Loại sách --}}
            <div class="col-md-3">
                <select name="type" class="form-select cursor-pointer" onchange="this.form.submit()">
                    <option value="">-- Tất cả loại hình --</option>
                    <option value="physical" {{ request('type') == 'physical' ? 'selected' : '' }}>📚 Sách giấy</option>
                    <option value="ebook" {{ request('type') == 'ebook' ? 'selected' : '' }}>📱 Ebook (Điện tử)</option>
                </select>
            </div>

            {{-- Nút Reset & Thêm mới --}}
            <div class="col-md-2 d-flex gap-2 justify-content-end">
                <a href="{{ route('books.index') }}" class="btn btn-light border" title="Làm mới bộ lọc">
                    <i class="fas fa-sync-alt text-muted"></i>
                </a>
                @if(in_array(Auth::user()->role, [2, 3]))
                    <a href="{{ route('books.create') }}" class="btn btn-primary fw-bold shadow-sm">
                        <i class="fas fa-plus"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- NOTIFICATIONS --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4">
        <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4">
        <i class="fas fa-exclamation-triangle me-1"></i> Có lỗi xảy ra:
        <ul class="mb-0 mt-1 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- KHU VỰC HIỂN THỊ DỮ LIỆU --}}
<div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4 bg-transparent bg-lg-white">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-lg-1 border-bottom-0 rounded-top-4">
        <div class="text-muted small">
            Hiển thị <span class="fw-bold text-dark">{{ $books->count() }}</span> / <span class="fw-bold text-primary">{{ $books->total() }}</span> sách
        </div>
        
        {{-- Nút Export/Import --}}
        @if(in_array(Auth::user()->role, [2, 3]))
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-cog me-1"></i> Công cụ
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                <li><a class="dropdown-item" href="{{ route('books.export') }}"><i class="fas fa-file-excel text-success me-2"></i>Xuất Excel</a></li>
                <li>
                    <form action="{{ route('books.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label class="dropdown-item cursor-pointer mb-0">
                            <i class="fas fa-file-import text-warning me-2"></i> Nhập Excel
                            <input type="file" name="file" style="display: none;" accept=".xlsx, .xls" onchange="this.form.submit()">
                        </label>
                    </form>
                </li>
            </ul>
        </div>
        @endif
    </div>

    <div class="card-body p-0 p-lg-0">
        
        {{-- 🔥 1. GIAO DIỆN DESKTOP (TABLE) 🔥 --}}
        {{-- Đã đổi từ d-md-block sang d-lg-block vì màn hình Tablet cũng có thể bị chật --}}
        <div class="table-responsive d-none d-lg-block">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary text-uppercase text-xs font-weight-bolder">
                    <tr>
                        <th class="ps-4 py-3" style="width: 60px;">ID</th>
                        <th style="width: 35%;">Thông tin sách</th>
                        <th style="width: 20%;">Giá bán (VNĐ)</th>
                        <th style="width: 15%;" class="text-center">Kho & Bán</th>
                        <th style="width: 10%;" class="text-end pe-4">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($books as $book)
                    <tr>
                        <td class="ps-4 text-muted fw-bold">#{{ $book->id }}</td>
                        
                        {{-- 1. THÔNG TIN SÁCH --}}
                        <td>
                            <div class="d-flex align-items-center py-2">
                                <div class="position-relative flex-shrink-0 me-3">
                                    @if($book->image)
                                        <img src="{{ asset(str_contains($book->image, 'uploads') ? $book->image : 'uploads/' . $book->image) }}" 
                                             class="rounded-3 border shadow-sm" width="50" height="75" style="object-fit: cover;">
                                    @else
                                        <div class="rounded-3 border bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 75px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                    
                                    @if($book->ebook_price > 0 && $book->file_ebook)
                                        <span class="position-absolute bottom-0 start-100 translate-middle badge rounded-pill bg-info border border-white" title="Có bản Ebook">
                                            <i class="fas fa-tablet-alt" style="font-size: 0.6rem;"></i>
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-1 text-dark fw-bold text-wrap" style="line-height: 1.4; max-width: 300px;">
                                        <a href="{{ route('books.edit', $book->id) }}" class="text-decoration-none text-dark hover-primary">
                                            {{ $book->title }}
                                        </a>
                                    </h6>
                                    <div class="text-muted small mb-1"><i class="fas fa-pen-nib me-1 text-xs"></i> {{ $book->author }}</div>
                                    <span class="badge bg-light text-secondary border fw-normal">{{ $book->category->name ?? '---' }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- 2. CỘT GIÁ BÁN --}}
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <div class="d-flex align-items-center justify-content-between" style="max-width: 180px;">
                                    <span class="text-secondary small"><i class="fas fa-book me-1"></i> Giấy:</span>
                                    @if($book->sale_price > 0 && $book->sale_price < $book->price)
                                        <div class="text-end">
                                            <span class="fw-bold text-danger">{{ number_format($book->sale_price) }}</span><br>
                                            <span class="text-decoration-line-through text-muted text-xs">{{ number_format($book->price) }}</span>
                                        </div>
                                    @else
                                        <span class="fw-bold text-dark">{{ number_format($book->price) }}</span>
                                    @endif
                                </div>

                                @if($book->ebook_price > 0)
                                    <div class="d-flex align-items-center justify-content-between border-top pt-1 mt-1" style="max-width: 180px;">
                                        <span class="text-info small"><i class="fas fa-tablet-alt me-1"></i> Ebook:</span>
                                        <span class="fw-bold text-info">{{ number_format($book->ebook_price) }}</span>
                                    </div>
                                @endif
                            </div>
                        </td>

                        {{-- 3. CỘT KHO & ĐÃ BÁN --}}
                        <td class="text-center">
                            <div class="mb-2">
                                <span class="badge {{ $book->quantity < 10 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} rounded-pill px-3 border">
                                    Kho: <b>{{ $book->quantity }}</b>
                                </span>
                            </div>
                            <div class="d-inline-flex flex-column align-items-start small text-muted bg-light border rounded px-2 py-1">
                                <div><i class="fas fa-book me-1 text-secondary"></i> Giấy: <b>{{ $book->total_sold - ($book->ebook_sold ?? 0) }}</b></div>
                                @if($book->ebook_sold > 0)
                                    <div><i class="fas fa-tablet-alt me-1 text-info"></i> Ebook: <b>{{ $book->ebook_sold }}</b></div>
                                @endif
                            </div>
                        </td>
                        
                        {{-- 4. HÀNH ĐỘNG --}}
                        <td class="text-end pe-4">
                            @if(in_array(Auth::user()->role, [2, 3]))
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm rounded-circle shadow-sm border" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v text-muted"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('books.edit', $book->id) }}">
                                                <i class="fas fa-edit text-primary me-2"></i> Chỉnh sửa
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('books.destroy', $book->id) }}" method="POST" onsubmit="return confirm('Xóa sách này?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash me-2"></i> Xóa sách
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @else
                                <a href="{{ route('books.show', $book->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="fas fa-eye me-1"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- 🔥 2. GIAO DIỆN ĐIỆN THOẠI (CARD) 🔥 --}}
        <div class="d-lg-none p-2 p-sm-3">
            @forelse($books as $book)
                <div class="card mb-3 shadow-sm border-0 rounded-4">
                    <div class="card-body p-3">
                        
                        {{-- Header Card: ID & Action --}}
                        <div class="d-flex justify-content-between align-items-start mb-2 pb-2 border-bottom">
                            <span class="badge bg-light text-secondary border fw-bold">ID: #{{ $book->id }}</span>
                            
                            @if(in_array(Auth::user()->role, [2, 3]))
                                <div class="dropdown">
                                    <button class="btn btn-sm text-muted p-0" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v fs-5 px-2"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                        <li><a class="dropdown-item" href="{{ route('books.edit', $book->id) }}"><i class="fas fa-edit text-primary me-2"></i> Chỉnh sửa</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('books.destroy', $book->id) }}" method="POST" onsubmit="return confirm('Xóa sách này?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash me-2"></i> Xóa sách</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @else
                                <a href="{{ route('books.show', $book->id) }}" class="text-primary"><i class="fas fa-eye"></i></a>
                            @endif
                        </div>

                        {{-- Body Card: Hình ảnh & Tên Sách --}}
                        <div class="d-flex mb-3">
                            <div class="position-relative flex-shrink-0 me-3">
                                @if($book->image)
                                    <img src="{{ asset(str_contains($book->image, 'uploads') ? $book->image : 'uploads/' . $book->image) }}" class="rounded-3 border shadow-sm" width="65" height="95" style="object-fit: cover;">
                                @else
                                    <div class="rounded-3 border bg-light d-flex align-items-center justify-content-center" style="width: 65px; height: 95px;"><i class="fas fa-image text-muted"></i></div>
                                @endif
                                @if($book->ebook_price > 0 && $book->file_ebook)
                                    <span class="position-absolute bottom-0 start-100 translate-middle badge rounded-pill bg-info border border-white" title="Có Ebook"><i class="fas fa-tablet-alt" style="font-size: 0.6rem;"></i></span>
                                @endif
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h6 class="fw-bold text-dark text-wrap mb-1" style="line-height: 1.3; font-size: 0.95rem;">{{ $book->title }}</h6>
                                <div class="text-muted small mb-2"><i class="fas fa-pen-nib me-1"></i>{{ $book->author }}</div>
                                <span class="badge bg-light text-secondary border fw-normal">{{ $book->category->name ?? 'Chưa phân loại' }}</span>
                            </div>
                        </div>

                        {{-- Footer Card: Giá & Tồn Kho --}}
                        <div class="row g-2 border-top pt-2 mt-2 bg-light rounded-3 px-1 py-2 mx-0">
                            {{-- Cột Giá --}}
                            <div class="col-6 border-end">
                                <div class="small text-muted mb-1 fw-bold">Giá bán</div>
                                <div class="d-flex flex-column gap-1">
                                    <div class="small">
                                        <i class="fas fa-book text-secondary me-1"></i>
                                        @if($book->sale_price > 0 && $book->sale_price < $book->price)
                                            <span class="fw-bold text-danger">{{ number_format($book->sale_price) }}đ</span>
                                        @else
                                            <span class="fw-bold text-dark">{{ number_format($book->price) }}đ</span>
                                        @endif
                                    </div>
                                    @if($book->ebook_price > 0)
                                        <div class="small">
                                            <i class="fas fa-tablet-alt text-info me-1"></i>
                                            <span class="fw-bold text-info">{{ number_format($book->ebook_price) }}đ</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            {{-- Cột Kho & Bán --}}
                            <div class="col-6 ps-2">
                                <div class="small text-muted mb-1 fw-bold">Kho & Đã bán</div>
                                <div class="mb-1">
                                    <span class="badge {{ $book->quantity < 10 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} border border-opacity-25 px-2">Kho: {{ $book->quantity }}</span>
                                </div>
                                <div class="small text-muted" style="font-size: 0.75rem;">
                                    Đã bán: <strong class="text-dark">{{ $book->total_sold }}</strong>
                                    @if($book->ebook_sold > 0)
                                        <span class="ms-1 text-info">(E: {{ $book->ebook_sold }})</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            @empty
                <div class="text-center py-5 bg-white rounded-4 border">
                    <i class="fas fa-box-open fa-3x text-muted opacity-25 mb-3"></i>
                    <h6 class="fw-bold text-muted">Không tìm thấy sách nào</h6>
                </div>
            @endforelse
        </div>

    </div>

    {{-- Phân trang --}}
    <div class="card-footer bg-white py-3 border-0">
        <div class="d-flex justify-content-center justify-content-lg-end">
            {{ $books->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection