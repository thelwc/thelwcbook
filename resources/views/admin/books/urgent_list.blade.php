@extends('admin.layouts.layout')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-danger fw-bold">
                <i class="fas fa-exclamation-triangle me-2"></i>Danh sách sách cần nhập gấp (< 10 cuốn)
            </h4>
            
            {{-- Thêm thẻ div gom 2 nút lại cho đẹp --}}
            <div class="d-flex gap-2">
                {{-- Nút Xuất Excel (Chỉ hiện cho Admin hoặc Quản lý, giống logic nãy) --}}
                @if(in_array(Auth::user()->role, [1]))
                    <a href="{{ route('dashboard.urgent_books.export') }}" class="btn btn-success btn-sm shadow-sm">
                        <i class="fas fa-file-excel me-1"></i> Xuất Excel
                    </a>
                @endif
                
                {{-- Nút quay lại cũ --}}
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow border-0 rounded-4">
        <div class="card-body p-0">
            
            {{-- ========================================== --}}
            {{-- GIAO DIỆN DESKTOP (Hiển thị dạng Bảng)     --}}
            {{-- ========================================== --}}
            <div class="table-responsive d-none d-md-block"> {{-- Ẩn trên mobile, hiện trên màn md trở lên --}}
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Sách</th>
                            <th>Tên sách</th>
                            <th class="text-center">Số lượng tồn</th>
                            <th class="text-end pe-4">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $book)
                        <tr>
                            <td class="ps-4" style="width: 80px;">
                                <img src="{{ asset($book->image) }}" class="rounded shadow-sm" width="50" height="70" style="object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $book->title }}</div>
                                <small class="text-muted">Mã: #{{ $book->id }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger rounded-pill px-3 py-2" style="font-size: 0.9rem;">
                                    Còn {{ $book->quantity }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                @if(in_array(Auth::user()->role, [0, 2]))
                                    <a href="{{ route('books.edit', $book->id) }}" class="btn btn-sm btn-danger rounded-3 px-3">
                                        <i class="fas fa-plus-circle me-1"></i> Nhập hàng
                                    </a>
                                @else
                                    <span class="badge bg-light text-secondary border px-2 py-1 fw-normal text-nowrap" style="cursor: not-allowed;" title="Bạn chỉ có quyền xem">
                                        <i class="fas fa-lock text-muted me-1"></i> Chỉ xem
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fs-1 mb-3 text-success"></i>
                                <h5>Kho hàng đang rất dồi dào!</h5>
                                <p>Không có cuốn sách nào sắp hết hàng.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ========================================== --}}
            {{-- GIAO DIỆN MOBILE (Hiển thị dạng Thẻ/Card) --}}
            {{-- ========================================== --}}
            <div class="d-block d-md-none"> {{-- Hiện trên mobile, ẩn trên màn md trở lên --}}
                <ul class="list-group list-group-flush">
                    @forelse($books as $book)
                    <li class="list-group-item p-3">
                        <div class="d-flex mb-2">
                            <img src="{{ asset($book->image) }}" class="rounded shadow-sm me-3 flex-shrink-0" width="70" height="100" style="object-fit: cover;">
                            <div class="d-flex flex-column justify-content-between w-100">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1" style="line-height: 1.4;">{{ $book->title }}</h6>
                                    <small class="text-muted d-block mb-2">Mã: #{{ $book->id }}</small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <span class="text-danger fw-bold fs-6">
                                        Còn: {{ $book->quantity }}
                                    </span>
                                    
                                    @if(in_array(Auth::user()->role, [0, 2]))
                                        <a href="{{ route('books.edit', $book->id) }}" class="btn btn-sm btn-danger rounded-3">
                                            Nhập ngay
                                        </a>
                                    @else
                                        <span class="badge bg-light text-secondary border px-2 py-1 fw-normal" style="cursor: not-allowed;">
                                            <i class="fas fa-lock me-1"></i> Chỉ xem
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item text-center py-5 text-muted border-0">
                        <i class="fas fa-box-open fs-1 mb-2 text-success"></i>
                        <h6>Kho hàng đang ổn định!</h6>
                    </li>
                    @endforelse
                </ul>
            </div>

        </div>
        
        {{-- ========================================== --}}
        {{-- THANH PHÂN TRANG (PAGINATION)              --}}
        {{-- ========================================== --}}
        @if($books->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-center">
                {{ $books->links('pagination::bootstrap-5') }} {{-- Dùng giao diện Bootstrap 5 cho phân trang --}}
            </div>
        </div>
        @endif
        
    </div>
</div>
@endsection