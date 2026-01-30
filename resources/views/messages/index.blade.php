@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold" style="color: #1e7e4f;"><i class="bi bi-chat-dots"></i> Mesajlar</h3>
            <p class="text-muted mb-0">
                @if(Auth::user()->isDoctor())
                    Qeydiyyatçılarla mesajlaşma
                @else
                    Doktorlarla mesajlaşma
                @endif
            </p>
        </div>
        @if(Auth::user()->isRegistrar())
            <div>
                @if(request()->has('all'))
                    <a href="{{ route('messages.index') }}" class="btn btn-primary">
                        <i class="bi bi-filter"></i> Yalnız Oxunmamışlar
                    </a>
                @else
                    <a href="{{ route('messages.index', ['all' => 1]) }}" class="btn btn-outline-primary">
                        <i class="bi bi-people"></i> Bütün Doktorlar
                    </a>
                @endif
            </div>
        @endif
    </div>

    @if(Auth::user()->isRegistrar())
        <!-- Axtarış Forması -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('messages.index') }}" class="row g-3">
                    @if(request()->has('all'))
                        <input type="hidden" name="all" value="1">
                    @endif
                    <div class="col-md-10">
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Doktor adı və ya soyadı ilə axtarış..."
                            value="{{ request('search') }}"
                        >
                    </div>
                    <div class="col-md-2">
                        @if(request('search'))
                            <a href="{{ route('messages.index', request()->has('all') ? ['all' => 1] : []) }}" class="btn btn-secondary w-100">
                                <i class="bi bi-x-circle"></i> Təmizlə
                            </a>
                        @else
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Axtar
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @forelse($conversations as $conversation)
                        <a href="{{ route('messages.show', $conversation['user']->id) }}"
                           class="text-decoration-none">
                            <div class="conversation-item p-3 border-bottom {{ $conversation['unread_count'] > 0 ? 'bg-light' : '' }}"
                                 style="transition: all 0.3s ease;">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 50px; background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%);">
                                            <span class="text-white fw-bold">
                                                {{ strtoupper(substr($conversation['user']->name, 0, 1)) }}{{ strtoupper(substr($conversation['user']->surname, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <h6 class="mb-0 fw-semibold text-dark">
                                                {{ $conversation['user']->name }} {{ $conversation['user']->surname }}
                                                @if($conversation['unread_count'] > 0)
                                                    <span class="badge bg-success ms-2">{{ $conversation['unread_count'] }}</span>
                                                @endif
                                            </h6>
                                            @if($conversation['last_message'])
                                                <small class="text-muted">{{ $conversation['last_message']->created_at->diffForHumans() }}</small>
                                            @endif
                                        </div>
                                        <div class="small text-muted">
                                            <i class="bi bi-envelope"></i> {{ $conversation['user']->email }}
                                            @if($conversation['user']->hospital)
                                                <span class="ms-2"><i class="bi bi-hospital"></i> {{ $conversation['user']->hospital }}</span>
                                            @endif
                                        </div>
                                        @if($conversation['last_message'])
                                            <p class="mb-0 mt-2 text-muted small">
                                                <strong>{{ $conversation['last_message']->sender_id == Auth::id() ? 'Siz:' : '' }}</strong>
                                                {{ Str::limit($conversation['last_message']->message, 60) }}
                                            </p>
                                        @else
                                            <p class="mb-0 mt-2 text-muted small fst-italic">Hələ mesaj yoxdur</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1"></i>
                            <div class="mt-3">Mesajlaşa biləcəyiniz istifadəçi yoxdur</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .conversation-item:hover {
        background: rgba(45, 155, 108, 0.05) !important;
    }
</style>
@endsection
