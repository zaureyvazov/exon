@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.messages.index') }}" class="text-success text-decoration-none">Mesajlar</a></li>
            <li class="breadcrumb-item active">{{ $sender->name }} ↔ {{ $receiver->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <!-- Participants Header -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-4">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 45px; height: 45px; background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%);">
                                    <span class="text-white fw-bold">
                                        {{ strtoupper(substr($sender->name, 0, 1)) }}{{ strtoupper(substr($sender->surname, 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $sender->name }} {{ $sender->surname }}</div>
                                    <div class="small text-muted">{{ $sender->email }}</div>
                                </div>
                            </div>
                            <i class="bi bi-arrow-left-right text-success fs-4"></i>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 45px; height: 45px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                                    <span class="text-white fw-bold">
                                        {{ strtoupper(substr($receiver->name, 0, 1)) }}{{ strtoupper(substr($receiver->surname, 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $receiver->name }} {{ $receiver->surname }}</div>
                                    <div class="small text-muted">{{ $receiver->email }}</div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.messages.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Geri
                        </a>
                    </div>
                </div>
            </div>

            <!-- Messages Container -->
            <div class="card border-0 shadow-sm" style="min-height: 500px;">
                <div class="card-body p-4" style="max-height: 500px; overflow-y: auto;">
                    @forelse($messages as $message)
                        <div class="mb-3 {{ $message->sender_id == $sender->id ? 'text-start' : 'text-end' }}">
                            <div class="d-inline-block" style="max-width: 70%;">
                                <div class="small text-muted mb-1">
                                    <strong>{{ $message->sender->name }} {{ $message->sender->surname }}</strong>
                                </div>
                                <div class="p-3 rounded {{ $message->sender_id == $sender->id ? 'bg-light' : 'bg-success text-white' }}"
                                     style="word-wrap: break-word;">
                                    <p class="mb-1">{{ $message->message }}</p>
                                    <small class="{{ $message->sender_id == $sender->id ? 'text-muted' : 'text-white-50' }}">
                                        {{ $message->created_at->format('d.m.Y H:i') }}
                                        @if($message->is_read)
                                            <i class="bi bi-check-all"></i>
                                        @else
                                            <i class="bi bi-check"></i>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-chat-dots fs-1"></i>
                            <div class="mt-3">Mesaj yoxdur</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
