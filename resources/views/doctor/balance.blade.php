@php
    use App\Models\Setting;
    $canSeePrices = Setting::doctorCanSeePrices();
@endphp

@extends('layouts.app')

@section('title', 'Balans')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.dashboard') }}"><i class="bi bi-house-door"></i> Ana Səhifə</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.patients') }}"><i class="bi bi-people"></i> Xəstələr</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.patients.create') }}"><i class="bi bi-person-plus"></i> Yeni Xəstə</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.referrals') }}"><i class="bi bi-file-medical"></i> Göndərişlər</a></li>
    <li class="nav-item"><a class="nav-link active" href="{{ route('doctor.balance') }}"><i class="bi bi-wallet2"></i> Balans</a></li>
@endsection

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-1">Balansım</h1>
        <div class="text-muted">Təsdiqlənmiş göndərişlərdən komisyon məlumatları</div>
    </div>

    <!-- Balance Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="opacity-90 small mb-2"><i class="bi bi-wallet2"></i> Ümumi Balans</div>
                            <div class="fs-3 fw-bold">{{ number_format($totalBalance, 2, '.', '') }} AZN</div>
                        </div>
                        <i class="bi bi-cash-coin display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="opacity-90 small mb-2"><i class="bi bi-check-circle"></i> Ödənilmiş Balans</div>
                            <div class="fs-3 fw-bold">{{ number_format($paidBalance, 2, '.', '') }} AZN</div>
                        </div>
                        <i class="bi bi-credit-card display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="opacity-90 small mb-2"><i class="bi bi-hourglass-split"></i> Gözləyən Balans</div>
                            <div class="fs-3 fw-bold">{{ number_format($remainingBalance, 2, '.', '') }} AZN</div>
                        </div>
                        <i class="bi bi-piggy-bank display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1"><i class="bi bi-check-circle"></i> Təsdiqlənmiş Göndərişlər</div>
                    <div class="fs-4 fw-bold text-success">{{ $stats['total_approved_referrals'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1"><i class="bi bi-files"></i> Ümumi Göndərişlər</div>
                    <div class="fs-4 fw-bold">{{ $stats['total_referrals'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1"><i class="bi bi-clock-history"></i> Gözləyən Göndərişlər</div>
                    <div class="fs-4 fw-bold text-warning">{{ $stats['pending_referrals'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History -->
    @if($payments->count() > 0)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history text-success"></i> Ödəniş Tarixçəsi</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Məbləğ</th>
                            <th>Qeyd</th>
                            <th>Tarix</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td class="fw-semibold">#{{ $payment->id }}</td>
                                <td>
                                    <span class="badge bg-success fs-6">{{ number_format($payment->amount, 2, '.', '') }} AZN</span>
                                </td>
                                <td>
                                    @if($payment->note)
                                        <span class="text-muted">{{ Str::limit($payment->note, 50) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small">
                                        <div>{{ $payment->created_at->format('d.m.Y') }}</div>
                                        <div class="text-muted">{{ $payment->created_at->format('H:i') }}</div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Commission Details -->
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="mb-0"><i class="bi bi-list-check"></i> Komisyon Detalları</h5>
            </div>

            @if(is_array($balanceDetails) && count($balanceDetails) > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Göndəriş</th>
                                <th>Xəstə</th>
                                @if($canSeePrices)
                                <th class="d-none d-md-table-cell">Orijinal</th>
                                <th class="d-none d-md-table-cell">Endirim</th>
                                <th class="d-none d-lg-table-cell">Final</th>
                                @endif
                                <th>Komisyon</th>
                                <th class="d-none d-md-table-cell">Tarix</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($balanceDetails as $detail)
                                <tr>
                                    <td class="fw-semibold">#{{ $detail['referral_id'] }}</td>
                                    <td>{{ $detail['patient_name'] }}</td>
                                    @if($canSeePrices)
                                    <td class="d-none d-md-table-cell">{{ number_format($detail['original_price'], 2) }} AZN</td>
                                    <td class="d-none d-md-table-cell">
                                        @if($detail['discount_type'] === 'percentage')
                                            <span class="badge bg-warning text-dark">{{ $detail['discount_value'] }}%</span>
                                        @elseif($detail['discount_type'] === 'amount')
                                            <span class="badge bg-warning text-dark">{{ number_format($detail['discount_value'], 2) }} AZN</span>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-lg-table-cell">{{ number_format($detail['final_price'], 2) }} AZN</td>
                                    @endif
                                    <td>
                                        @if($detail['is_free'])
                                            <span class="badge bg-danger">0 AZN</span>
                                        @else
                                            <span class="badge bg-success">{{ number_format($detail['commission_amount'], 2) }} AZN</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-md-table-cell text-muted small">{{ $detail['date']->format('d.m.Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                @if($canSeePrices)
                                <td colspan="5" class="text-end fw-bold">ÜMUMI BALANS:</td>
                                @else
                                <td colspan="2" class="text-end fw-bold">ÜMUMI BALANS:</td>
                                @endif
                                <td colspan="2" class="fw-bold text-success">{{ number_format($totalBalance, 2, '.', '') }} AZN</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-wallet2 fs-1 d-block mb-2"></i>
                    <div class="fw-semibold">Hələ təsdiqlənmiş göndəriş yoxdur</div>
                    <div class="small">Göndərişləriniz təsdiqləndikdən sonra balansınız burada görünəcək</div>
                </div>
            @endif
        </div>
    </div>
@endsection
