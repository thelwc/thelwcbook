@extends('admin.layouts.layout')

@section('content')
<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tạo Mã Giảm Giá Mới</h6>
        </div>
        <div class="card-body">

        {{-- 🔥 THÊM ĐOẠN NÀY ĐỂ HIỆN LỖI 🔥 --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            {{-- ----------------------------- --}}



            <form action="{{ route('vouchers.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Mã Code (VD: SALE50)</label>
                        <input type="text" name="code" class="form-control text-uppercase" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Số lượng</label>
                        <input type="number" name="quantity" class="form-control" value="100" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Loại giảm giá</label>
                        <select name="type" class="form-control">
                            <option value="fixed">Giảm tiền mặt (VNĐ)</option>
                            <option value="percent">Giảm phần trăm (%)</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Giá trị giảm</label>
                        <input type="number" name="value" class="form-control" placeholder="VD: 50000 hoặc 10" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Đơn hàng tối thiểu (đ)</label>
                        <input type="number" name="min_order_amount" class="form-control" value="0" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Ngày bắt đầu</label>
                        <input type="datetime-local" name="start_date" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Ngày kết thúc</label>
                        <input type="datetime-local" name="end_date" class="form-control" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Lưu mã</button>
                <a href="{{ route('vouchers.index') }}" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
@endsection