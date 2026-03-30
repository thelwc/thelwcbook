@extends('admin.layouts.layout')

@section('content')

{{-- HEADER: TIÊU ĐỀ VÀ NÚT CÔNG CỤ --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <h3 class="fw-bold mb-0 text-dark">🏢 Quản Lý Nhà Xuất Bản</h3>
    
    <div class="d-flex flex-wrap gap-2">
        {{-- CHỈ QUẢN LÝ (2) ĐƯỢC THÊM MỚI --}}
        @if(in_array(Auth::user()->role, [2, 3]))
            <a href="{{ route('publishers.create') }}" class="btn btn-primary shadow-sm flex-grow-1 flex-md-grow-0 text-nowrap fw-bold">
                <i class="fas fa-plus me-1"></i> Thêm mới
            </a>
        @endif
    </div>
</div>

{{-- KHU VỰC HIỂN THỊ THÔNG BÁO --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3">
        <i class="fas fa-check-circle me-2"></i> <strong>Tuyệt vời!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3">
        <i class="fas fa-exclamation-triangle me-2"></i> <strong>Úi chà!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
                        <th class="py-3 ps-4" style="width: 100px;">ID</th>
                        <th class="py-3" style="width: 30%;">Tên Nhà xuất bản</th>
                        <th class="py-3" style="width: 30%;">Địa chỉ</th>
                        <th class="py-3 text-center" style="width: 150px;">Số lượng sách</th>
                        <th class="py-3 text-end pe-4" style="width: 150px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($publishers as $pub)
                    <tr class="border-bottom">
                        <td class="ps-4 fw-bold text-muted">#{{ $pub->id }}</td>
                        <td>
                            <span class="fw-bold text-primary" style="font-size: 1.05rem;">{{ $pub->name }}</span>
                        </td>
                        <td class="text-secondary small">
                            <i class="fas fa-map-marker-alt me-1 text-muted opacity-50"></i> {{ $pub->address ?? '---' }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary rounded-pill px-3 py-2">{{ $pub->books->count() }} cuốn</span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('books.index', ['publisher_id' => $pub->id]) }}" class="btn btn-sm btn-info text-white shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Xem sách">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if(in_array(Auth::user()->role, [2, 3]))
                                    <a href="{{ route('publishers.edit', $pub->id) }}" class="btn btn-sm btn-warning text-dark shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('publishers.destroy', $pub->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa Nhà xuất bản này không?');">
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
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-building fa-3x opacity-25 mb-3"></i>
                            <h6 class="fw-bold">Chưa có nhà xuất bản nào</h6>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 🔥 2. GIAO DIỆN MOBILE (CARD) 🔥 --}}
        <div class="d-md-none p-2 p-sm-3">
            @forelse($publishers as $pub)
                <div class="card mb-3 shadow-sm border-0 rounded-4">
                    <div class="card-body p-3">
                        
                        {{-- Header Card --}}
                        <div class="d-flex justify-content-between align-items-start mb-3 border-bottom pb-3">
                            <div>
                                <span class="badge bg-light text-secondary border fw-bold mb-1">ID: #{{ $pub->id }}</span>
                                <h5 class="fw-bold text-primary mb-0 text-wrap lh-base">{{ $pub->name }}</h5>
                            </div>
                            <span class="badge bg-secondary rounded-pill px-3 py-2 flex-shrink-0 ms-2 mt-1">{{ $pub->books->count() }} cuốn</span>
                        </div>

                        {{-- Body Card: Địa chỉ --}}
                        <div class="mb-3 text-secondary small">
                            <i class="fas fa-map-marker-alt me-1 text-muted opacity-50"></i> {{ $pub->address ?? 'Chưa cập nhật địa chỉ' }}
                        </div>

                        {{-- Footer Card: Các nút hành động --}}
                        <div class="d-flex justify-content-end gap-2 mt-2 pt-2">
                            <a href="{{ route('books.index', ['publisher_id' => $pub->id]) }}" class="btn btn-sm btn-info text-white flex-grow-1 fw-bold rounded-pill">
                                <i class="fas fa-eye me-1"></i> Xem sách
                            </a>

                            @if(in_array(Auth::user()->role, [2, 3]))
                                <a href="{{ route('publishers.edit', $pub->id) }}" class="btn btn-sm btn-outline-warning text-dark flex-grow-1 fw-bold rounded-pill">
                                    <i class="fas fa-edit me-1"></i> Sửa
                                </a>
                                
                                <form action="{{ route('publishers.destroy', $pub->id) }}" method="POST" class="d-inline flex-grow-1" onsubmit="return confirm('Xóa Nhà xuất bản này?');">
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
                    <i class="fas fa-building fa-3x text-muted opacity-25 mb-3"></i>
                    <h6 class="fw-bold text-muted">Chưa có nhà xuất bản nào</h6>
                </div>
            @endforelse
        </div>

    </div>
    
    {{-- Phân trang --}}
    <div class="card-footer bg-white py-3 border-0 rounded-bottom-4">
        <div class="d-flex justify-content-center justify-content-md-end">
            {{ $publishers->links() }}
        </div>
    </div>
</div>

@endsection