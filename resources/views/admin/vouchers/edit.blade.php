@extends('admin.layouts.layout')

@section('content')
<div class="container">
    {{-- Tiêu đề --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chỉnh sửa Voucher</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Cập nhật thông tin: <span class="text-danger">{{ $voucher->code }}</span></h6>
        </div>
        <div class="card-body">
            {{-- Form gửi đến hàm Update --}}
            <form action="{{ route('vouchers.update', $voucher->id) }}" method="POST">
                @csrf
                @method('PUT') {{-- Bắt buộc phải có dòng này để báo hiệu update --}}

                <div class="row">
                    {{-- Mã Code --}}
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Mã Code</label>
                        <input type="text" name="code" class="form-control text-uppercase" 
                               value="{{ old('code', $voucher->code) }}" required>
                        @error('code') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    
                    {{-- Số lượng --}}
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Số lượng</label>
                        <input type="number" name="quantity" class="form-control" 
                               value="{{ old('quantity', $voucher->quantity) }}" required>
                    </div>
                </div>

                <div class="row">
                    {{-- Loại giảm giá --}}
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">Loại giảm giá</label>
                        <select name="type" class="form-control">
                            <option value="fixed" {{ $voucher->type == 'fixed' ? 'selected' : '' }}>Giảm tiền mặt (VNĐ)</option>
                            <option value="percent" {{ $voucher->type == 'percent' ? 'selected' : '' }}>Giảm phần trăm (%)</option>
                        </select>
                    </div>

                    {{-- Giá trị --}}
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">Giá trị giảm</label>
                        <input type="number" name="value" class="form-control" 
                               value="{{ old('value', intval($voucher->value)) }}" required>
                    </div>

                    {{-- Đơn tối thiểu --}}
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">Đơn hàng tối thiểu (đ)</label>
                        <input type="number" name="min_order_amount" class="form-control" 
                               value="{{ old('min_order_amount', intval($voucher->min_order_amount)) }}" required>
                    </div>
                </div>

                <div class="row">
                    {{-- Ngày bắt đầu --}}
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Ngày bắt đầu</label>
                        {{-- Phải format lại ngày thì input datetime-local mới hiện được --}}
                        <input type="datetime-local" name="start_date" class="form-control" 
                               value="{{ date('Y-m-d\TH:i', strtotime($voucher->start_date)) }}" required>
                    </div>

                    {{-- Ngày kết thúc --}}
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Ngày kết thúc</label>
                        <input type="datetime-local" name="end_date" class="form-control" 
                               value="{{ date('Y-m-d\TH:i', strtotime($voucher->end_date)) }}" required>
                    </div>
                </div>

                <hr>
                
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('vouchers.index') }}" class="btn btn-secondary">Quay lại</a>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection