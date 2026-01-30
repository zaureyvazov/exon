@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('messages.index') }}" class="text-success text-decoration-none">Mesajlar</a></li>
            <li class="breadcrumb-item active">{{ $otherUser->name }} {{ $otherUser->surname }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <!-- Chat Header -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 50px; height: 50px; background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%);">
                            <span class="text-white fw-bold fs-5">
                                {{ strtoupper(substr($otherUser->name, 0, 1)) }}{{ strtoupper(substr($otherUser->surname, 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $otherUser->name }} {{ $otherUser->surname }}</h5>
                            <div class="text-muted small">
                                <i class="bi bi-envelope"></i> {{ $otherUser->email }}
                                @if($otherUser->hospital)
                                    <span class="ms-2"><i class="bi bi-hospital"></i> {{ $otherUser->hospital }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages Container -->
            <div class="card border-0 shadow-sm" style="min-height: 500px;">
                <div class="card-body p-4" style="max-height: 500px; overflow-y: auto;" id="messagesContainer">
                    @forelse($messages as $message)
                        <div class="mb-3 {{ $message->sender_id == Auth::id() ? 'text-end' : '' }}">
                            <div class="d-inline-block" style="max-width: 70%;">
                                <div class="p-3 rounded {{ $message->sender_id == Auth::id() ? 'bg-success text-white' : 'bg-light' }}"
                                     style="word-wrap: break-word;">
                                    <p class="mb-1">{{ $message->message }}</p>
                                    <small class="{{ $message->sender_id == Auth::id() ? 'text-white-50' : 'text-muted' }}">
                                        {{ $message->created_at->format('d.m.Y H:i') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-chat-dots fs-1"></i>
                            <div class="mt-3">Hələ mesaj yoxdur. İlk mesajı göndərin!</div>
                        </div>
                    @endforelse
                </div>

                <!-- Message Input -->
                <div class="card-footer bg-white border-top">
                    <form action="{{ route('messages.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="receiver_id" value="{{ $otherUser->id }}">
                        <div class="input-group">
                            <textarea name="message"
                                      class="form-control @error('message') is-invalid @enderror"
                                      rows="2"
                                      placeholder="Mesajınızı yazın..."
                                      required></textarea>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-send"></i> Göndər
                            </button>
                        </div>
                        @error('message')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto scroll to bottom
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('messagesContainer');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });
</script>
@endsection
