@extends('client.layouts.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold">🔔 Tất cả thông báo</h3>
                @if(Auth::user()->unreadNotifications->count() > 0)
                    <a href="{{ route('notifications.readAll') }}" class="btn btn-sm btn-outline-primary">
                        Đánh dấu tất cả đã đọc
                    </a>
                @endif
            </div>

            <div class="card shadow-sm border-0 rounded-3">
                <div class="list-group list-group-flush">
                    @forelse($notifications as $notification)
                        <a href="{{ $notification->data['link'] ?? '#' }}" class="list-group-item list-group-item-action p-3 {{ $notification->read_at ? '' : 'bg-light' }}">
                            <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                <strong class="text-primary {{ $notification->read_at ? 'text-muted' : '' }}">
                                    <i class="{{ $notification->data['icon'] ?? 'fas fa-bell' }} me-2"></i>
                                    {{ $notification->data['title'] }}
                                </strong>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1 text-secondary">{{ $notification->data['message'] }}</p>
                            @if(!$notification->read_at)
                                <span class="badge bg-danger rounded-pill mt-2">Mới</span>
                            @endif
                        </a>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-bell-slash fa-3x mb-3"></i>
                            <p>Bạn không có thông báo nào.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Phân trang --}}
            <div class="mt-4 d-flex justify-content-center">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</div>
@endsection