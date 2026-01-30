@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold" style="color: #1e7e4f;"><i class="bi bi-cash-stack"></i> Ödənişlər</h3>
            <p class="text-muted">Doktorlara edilən ödənişlərin tarixçəsi</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%);">
                <div class="card-body text-white">
                    <div class="opacity-90 small mb-2"><i class="bi bi-cash-coin"></i> Bu ay ödənilən</div>
                    <div class="fs-3 fw-bold">{{ number_format($payments->where('created_at', '>=', now()->startOfMonth())->sum('amount'), 2, '.', '') }} AZN</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="card-body text-white">
                    <div class="opacity-90 small mb-2"><i class="bi bi-receipt"></i> Ümumi ödənişlər</div>
                    <div class="fs-3 fw-bold">{{ $payments->total() }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                <div class="card-body text-white">
                    <div class="opacity-90 small mb-2"><i class="bi bi-people"></i> Ödəniş alan doktorlar</div>
                    <div class="fs-3 fw-bold">{{ $payments->groupBy('doctor_id')->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <div class="card-body text-white">
                    <div class="opacity-90 small mb-2"><i class="bi bi-wallet2"></i> Cəmi məbləğ</div>
                    <div class="fs-3 fw-bold">{{ number_format($payments->sum('amount'), 2, '.', '') }} AZN</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Doktor</th>
                            <th>Məbləğ</th>
                            <th>Qeyd</th>
                            <th>Admin</th>
                            <th>Tarix</th>
                            <th>Əməliyyatlar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td class="fw-semibold">#{{ $payment->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                                             style="width: 40px; height: 40px; background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%); color: white; font-weight: bold;">
                                            {{ strtoupper(substr($payment->doctor->name, 0, 1)) }}{{ strtoupper(substr($payment->doctor->surname, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $payment->doctor->name }} {{ $payment->doctor->surname }}</div>
                                            <div class="small text-muted">{{ $payment->doctor->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-success fs-6">{{ number_format($payment->amount, 2, '.', '') }} AZN</span>
                                </td>
                                <td>
                                    @if($payment->note)
                                        <span class="text-muted" data-bs-toggle="tooltip" title="{{ $payment->note }}">
                                            {{ Str::limit($payment->note, 30) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small">
                                        <div class="fw-semibold">{{ $payment->admin->name }} {{ $payment->admin->surname }}</div>
                                        <div class="text-muted">{{ $payment->admin->email }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">
                                        <div>{{ $payment->created_at->format('d.m.Y') }}</div>
                                        <div class="text-muted">{{ $payment->created_at->format('H:i') }}</div>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.payments.show', $payment->id) }}"
                                       class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-eye"></i> Bax
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-1"></i>
                                    <div class="mt-2">Hələ ki ödəniş yoxdur</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($payments->hasPages())
                <div class="mt-3">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
