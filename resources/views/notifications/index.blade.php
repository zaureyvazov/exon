@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold" style="color: #1e7e4f;"><i class="bi bi-bell"></i> Bildirişlər</h3>
            <p class="text-muted">Bütün bildirişləriniz</p>
        </div>
        @if(Auth::user()->notifications()->unread()->count() > 0)
            <button id="markAllReadBtn" class="btn btn-success">
                <i class="bi bi-check-all"></i> Hamısını oxundu et
            </button>
        @endif
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @forelse($notifications as $notification)
                        <div class="notification-row p-3 border-bottom {{ !$notification->is_read ? 'bg-light' : '' }}"
                             data-id="{{ $notification->id }}">
                            <div class="d-flex align-items-start gap-3">
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 50px; height: 50px; background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%);">
                                        <i class="bi {{
                                            $notification->type == 'payment' ? 'bi-cash-coin' :
                                            ($notification->type == 'referral_approved' ? 'bi-check-circle' : 'bi-clipboard-check')
                                        }} text-white fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0 fw-semibold">
                                            {{ $notification->title }}
                                            @if(!$notification->is_read)
                                                <span class="badge bg-success ms-2">Yeni</span>
                                            @endif
                                        </h6>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-2 text-muted">{{ $notification->message }}</p>
                                    <div class="d-flex gap-2">
                                        <small class="text-muted">
                                            <i class="bi bi-calendar"></i> {{ $notification->created_at->format('d.m.Y H:i') }}
                                        </small>
                                        @if(!$notification->is_read)
                                            <button class="btn btn-sm btn-outline-success mark-read-btn" data-id="{{ $notification->id }}">
                                                <i class="bi bi-check"></i> Oxundu et
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1"></i>
                            <div class="mt-3">Bildiriş yoxdur</div>
                        </div>
                    @endforelse
                </div>
            </div>

            @if($notifications->hasPages())
                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Mark single notification as read
    document.querySelectorAll('.mark-read-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            markAsRead(id);
        });
    });

    // Mark all as read
    document.getElementById('markAllReadBtn')?.addEventListener('click', function() {
        if (confirm('Bütün bildirişləri oxundu kimi işarələmək istəyirsiniz?')) {
            markAllAsRead();
        }
    });

    function markAsRead(id) {
        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(() => {
            location.reload();
        })
        .catch(error => console.error('Error:', error));
    }

    function markAllAsRead() {
        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(() => {
            location.reload();
        })
        .catch(error => console.error('Error:', error));
    }
</script>
@endsection
