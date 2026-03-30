@extends('admin.layouts.layout') 

@section('header', 'Tất cả thông báo')

@section('content')
<div class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-bell text-primary me-2"></i> Lịch Sử Thông Báo
                    </h5>
                    
                    @if(Auth::user()->unreadNotifications->count() > 0)
                        <a href="{{ route('notifications.readAll') }}" class="btn btn-sm btn-primary rounded-pill fw-bold px-3">
                            <i class="fas fa-check-double me-1"></i> Đánh dấu tất cả đã đọc
                        </a>
                    @endif
                </div>

                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($notifications as $notification)
                            <a href="{{ $notification->data['link'] ?? '#' }}" 
                               class="list-group-item list-group-item-action p-4 border-bottom {{ $notification->read_at ? 'bg-white' : 'bg-primary bg-opacity-10' }}">
                                <div class="d-flex align-items-center">
                                    {{-- Icon --}}
                                    <div class="me-4 flex-shrink-0 text-center" style="width: 45px;">
                                        <div class="bg-white shadow-sm rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                            <i class="{{ $notification->data['icon'] ?? 'fas fa-bell' }} fs-5 {{ $notification->read_at ? 'text-secondary' : 'text-primary' }}"></i>
                                        </div>
                                    </div>
                                    
                                    {{-- Nội dung --}}
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="mb-0 fw-bold {{ $notification->read_at ? 'text-dark' : 'text-primary' }}">
                                                {{ $notification->data['title'] }}
                                            </h6>
                                            <small class="text-muted fw-medium">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-0 text-secondary" style="font-size: 0.9rem;">
                                            {{ $notification->data['message'] }}
                                        </p>
                                    </div>
                                    
                                    {{-- Dấu chấm chưa đọc (chấm xanh) --}}
                                    @if(!$notification->read_at)
                                        <div class="ms-3 flex-shrink-0">
                                            <span class="p-2 bg-primary rounded-circle d-inline-block"></span>
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-5">
                                <i class="far fa-bell-slash fa-4x text-muted mb-3 opacity-25"></i>
                                <h5 class="fw-bold text-secondary">Bạn chưa có thông báo nào!</h5>
                            </div>
                        @endforelse
                    </div>
                </div>
                
                {{-- Phân trang Mới --}}
                @if($notifications->hasPages())
                    <div class="card-footer bg-white border-top p-3 d-flex justify-content-center"> 
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection