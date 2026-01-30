@extends('layouts.app')

@section('title', 'Proqram Komissiyası')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Ana Səhifə</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.users') }}">İstifadəçilər</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.analyses') }}">Analizlər</a></li>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown">
            Balanslar
        </a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('admin.balances') }}">DR Balansları</a></li>
            <li><a class="dropdown-item active" href="{{ route('admin.program.commission') }}">Proqram Komissiyası</a></li>
        </ul>
    </li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.settings') }}">Ayarlar</a></li>
@endsection

@section('content')
    <div class="mb-4">
        <h1 class="fw-bold mb-1"><i class="bi bi-coin text-warning"></i> Proqram Komissiyası</h1>
        <p class="text-muted">Sistemin ümumi komissiya balansı və ödənişlər</p>
    </div>

    <!-- Balance Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="opacity-90 small mb-2"><i class="bi bi-wallet2"></i> Ümumi Komissiya</div>
                            {{ number_format($totalCommission, 2, '.', '') }} AZN
                        </div>
                        <i class="bi bi-cash-stack display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="opacity-90 small mb-2"><i class="bi bi-check-circle"></i> Ödənilmiş</div>
                            <div class="fs-3 fw-bold">{{ number_format($totalPaid, 2, '.', '') }} AZN</div>
                        </div>

                        <i class="bi bi-check2-circle display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="opacity-90 small mb-2"><i class="bi bi-hourglass-split"></i> Qalıq</div>
                            {{ number_format($remainingBalance, 2, '.', '') }} AZN
                        </div>
                        <i class="bi bi-exclamation-circle display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Payment Button -->
    @if($remainingBalance > 0)
        <div class="mb-4">
            <button type="button" class="btn btn-lg btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal">
                <i class="bi bi-cash"></i> Ödəniş Et
            </button>
        </div>
    @endif

    <!-- Referrals Commission List -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h5 class="mb-0"><i class="bi bi-list-check"></i> Göndərişlər Komissiyası</h5>
                <form method="GET" action="{{ route('admin.program.commission') }}" class="d-flex gap-2">
                    <input
                        type="date"
                        name="date"
                        class="form-control form-control-sm"
                        value="{{ request('date') }}"
                        placeholder="Tarix"
                    >
                    <input
                        type="text"
                        name="search"
                        class="form-control form-control-sm"
                        placeholder="Xəstə adı..."
                        value="{{ request('search') }}"
                        style="min-width: 200px;"
                    >
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-search"></i>
                    </button>
                    @if(request('date') || request('search'))
                        <a href="{{ route('admin.program.commission') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            @if($referrals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fw-semibold">Tarix</th>
                                <th class="fw-semibold">Xəstə</th>
                                <th class="fw-semibold">Doktor</th>
                                <th class="fw-semibold">Analizlər</th>
                                <th class="fw-semibold text-end">Komissiya</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($referrals as $referral)
                                <tr>
                                    <td>
                                        <small class="text-muted">
                                            {{ $referral->approved_at->format('d.m.Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $referral->patient->name }} {{ $referral->patient->surname }}</div>
                                        <strong>{{ $referral->patient->name }} {{ $referral->patient->father_name }} {{ $referral->patient->surname }}</strong>
                                        @if($referral->patient->serial_number)
                                            <br><small class="text-muted">{{ $referral->patient->serial_number }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $referral->doctor->name }} {{ $referral->doctor->surname }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $referral->analyses->count() }} analiz</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-warning text-dark">
                                            {{ number_format($referral->total_program_commission, 2) }} AZN
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-top py-3">
                    {{ $referrals->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted opacity-25"></i>
                    <p class="text-muted mt-3">Təsdiqlənmiş göndəriş tapılmadı</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Payment History -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Ödəniş Tarixçəsi</h5>
        </div>
        <div class="card-body p-0">
            @if($payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fw-semibold">Tarix</th>
                                <th class="fw-semibold">Məbləğ</th>
                                <th class="fw-semibold">Qeyd Edən</th>
                                <th class="fw-semibold">Qeyd</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>
                                        <small class="text-muted">
                                            {{ $payment->created_at->format('d.m.Y H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            {{ number_format($payment->amount, 2) }} AZN
                                        </span>
                                    </td>
                                    <td>{{ $payment->paidBy->name ?? '-' }}</td>
                                    <td><small class="text-muted">{{ $payment->notes ?? '-' }}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-top py-3">
                    {{ $payments->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted opacity-25"></i>
                    <p class="text-muted mt-3">Hələ ödəniş qeyd edilməyib</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.program.commission.payment') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-cash"></i> Yeni Ödəniş</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Ödənilməli qalıq: <strong>{{ number_format($remainingBalance, 2) }} AZN</strong>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label fw-semibold">Məbləğ (AZN) *</label>
                            <input
                                type="number"
                                step="0.01"
                                class="form-control @error('amount') is-invalid @enderror"
                                id="amount"
                                name="amount"
                                max="{{ $remainingBalance }}"
                                value="{{ old('amount') }}"
                                required
                            >
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label fw-semibold">Qeyd</label>
                            <textarea
                                class="form-control"
                                id="notes"
                                name="notes"
                                rows="3"
                                placeholder="Əlavə məlumat (istəyə bağlı)"
                            >{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ləğv et</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Təsdiq et
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
