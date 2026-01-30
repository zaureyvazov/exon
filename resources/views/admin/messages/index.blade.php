@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold" style="color: #1e7e4f;"><i class="bi bi-chat-square-text"></i> Mesajlaşma Nəzarəti</h3>
            <p class="text-muted">Doktor və qeydiyyatçılar arasında mesajlaşma</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%);">
                <div class="card-body text-white">
                    <div class="opacity-90 small mb-2"><i class="bi bi-chat-dots"></i> Ümumi Mesajlar</div>
                    <div class="fs-3 fw-bold">{{ $stats['total_messages'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <div class="card-body text-white">
                    <div class="opacity-90 small mb-2"><i class="bi bi-calendar-day"></i> Bu gün</div>
                    <div class="fs-3 fw-bold">{{ $stats['today_messages'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);">
                <div class="card-body text-white">
                    <div class="opacity-90 small mb-2"><i class="bi bi-envelope"></i> Oxunmamış</div>
                    <div class="fs-3 fw-bold">{{ $stats['unread_messages'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.messages.index') }}" class="row g-3">
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold"><i class="bi bi-person"></i> Göndərən</label>
                    <select name="sender_id" class="form-select">
                        <option value="">Hamısı</option>
                        <optgroup label="Doktorlar">
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ request('sender_id') == $doctor->id ? 'selected' : '' }}>
                                    {{ $doctor->name }} {{ $doctor->surname }}
                                </option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Qeydiyyatçılar">
                            @foreach($registrars as $registrar)
                                <option value="{{ $registrar->id }}" {{ request('sender_id') == $registrar->id ? 'selected' : '' }}>
                                    {{ $registrar->name }} {{ $registrar->surname }}
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold"><i class="bi bi-person-check"></i> Qəbul edən</label>
                    <select name="receiver_id" class="form-select">
                        <option value="">Hamısı</option>
                        <optgroup label="Doktorlar">
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ request('receiver_id') == $doctor->id ? 'selected' : '' }}>
                                    {{ $doctor->name }} {{ $doctor->surname }}
                                </option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Qeydiyyatçılar">
                            @foreach($registrars as $registrar)
                                <option value="{{ $registrar->id }}" {{ request('receiver_id') == $registrar->id ? 'selected' : '' }}>
                                    {{ $registrar->name }} {{ $registrar->surname }}
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold"><i class="bi bi-calendar"></i> Tarix</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-12 col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-success flex-grow-1">
                        <i class="bi bi-filter"></i> Filtrlə
                    </button>
                    <a href="{{ route('admin.messages.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Təmizlə
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Messages Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($messages->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Göndərən</th>
                                <th>Qəbul edən</th>
                                <th>Mesaj</th>
                                <th>Status</th>
                                <th>Tarix</th>
                                <th>Əməliyyat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($messages as $message)
                                <tr>
                                    <td class="fw-semibold">#{{ $message->id }}</td>
                                    <td>
                                        <div class="small">
                                            <div class="fw-semibold">{{ $message->sender->name }} {{ $message->sender->surname }}</div>
                                            <div class="text-muted">{{ $message->sender->email }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div class="fw-semibold">{{ $message->receiver->name }} {{ $message->receiver->surname }}</div>
                                            <div class="text-muted">{{ $message->receiver->email }}</div>
                                        </div>
                                    </td>
                                    <td>{{ Str::limit($message->message, 50) }}</td>
                                    <td>
                                        @if($message->is_read)
                                            <span class="badge bg-success">Oxunub</span>
                                        @else
                                            <span class="badge bg-warning">Oxunmayıb</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div>{{ $message->created_at->format('d.m.Y') }}</div>
                                            <div class="text-muted">{{ $message->created_at->format('H:i') }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.messages.show', [$message->sender_id, $message->receiver_id]) }}"
                                           class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-eye"></i> Bax
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($messages->hasPages())
                    <div class="mt-3">
                        {{ $messages->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1"></i>
                    <div class="mt-3">Mesaj yoxdur</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
