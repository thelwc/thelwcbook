@extends('admin.layouts.layout')

@section('content')

@php
    // 🔥 CHIẾN THUẬT PHÂN QUYỀN TRONG PHÒNG:
    // Mảng này chứa ID của những người ĐƯỢC PHÉP CHỈNH SỬA (Giám đốc = 1, Quản lý = 2)
    // Nếu cậu muốn ông sếp Role 1 oai nhất hệ thống thì xóa số 2 đi nhé =))
    $canEdit = in_array(Auth::user()->role, [1, 2]); 
@endphp

<div class="card shadow border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Cấu hình phí vận chuyển</h5>
        
        @if(!$canEdit)
            <span class="badge bg-secondary">Chỉ xem</span>
        @else
            <span class="badge bg-primary">Chế độ chỉnh sửa</span>
        @endif
    </div>

    <div class="card-body {{ $canEdit ? '' : 'bg-light' }}">
        <form action="{{ route('admin.shipping_fee.update') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label fw-bold {{ $canEdit ? '' : 'text-muted' }}">Mức giá đơn hàng được Freeship (VNĐ)</label>
                <div class="input-group">
                    {{-- Nếu biến $canEdit là false -> nó sẽ tự in chữ 'disabled' ra để khóa ô này lại --}}
                    <input type="number" name="free_ship_threshold" class="form-control bg-white fw-bold {{ $canEdit ? '' : 'text-success' }}" 
                           value="{{ \App\Models\Setting::where('key', 'free_ship_threshold')->value('value') ?? 0 }}"
                           {{ $canEdit ? '' : 'disabled' }}>
                    <span class="input-group-text bg-white fw-bold">VNĐ</span>
                </div>
                
                @if(!$canEdit)
                    <small class="text-danger fst-italic mt-1 d-block"><i class="fas fa-lock me-1"></i> Mục này đã bị khóa, quyền của bạn chỉ được xem.</small>
                @endif
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold {{ $canEdit ? '' : 'text-muted' }}">Phí ship mặc định (VNĐ)</label>
                 <div class="input-group">
                    <input type="number" name="shipping_fee" class="form-control bg-white fw-bold {{ $canEdit ? '' : 'text-primary' }}" 
                           value="{{ \App\Models\Setting::where('key', 'shipping_fee')->value('value') ?? 0 }}"
                           {{ $canEdit ? '' : 'disabled' }}>
                    <span class="input-group-text bg-white fw-bold">VNĐ</span>
                </div>

                @if(!$canEdit)
                    <small class="text-danger fst-italic mt-1 d-block"><i class="fas fa-lock me-1"></i> Mục này đã bị khóa, quyền của bạn chỉ được xem.</small>
                @endif
            </div>

            {{-- Ảo thuật đổi nút bấm: Nếu được sửa thì hiện nút LƯU, ngược lại hiện nút QUAY LẠI --}}
            @if($canEdit)
                <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-save me-2"></i> Lưu Cấu Hình</button>
            @else
                <a href="{{ url()->previous() }}" class="btn btn-secondary shadow-sm"><i class="fas fa-arrow-left me-2"></i> Quay lại</a>
            @endif
        </form>
    </div>
</div>
@endsection