@extends('layouts.app')

@section('title', 'Doktor Balans Detalları')

@section('content')
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-success">Ana Səhifə</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.balances') }}" class="text-decoration-none text-success">Balanslar</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $doctor->name }} {{ $doctor->surname }}</li>
            </ol>
        </nav>

        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle text-white fw-bold d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%); font-size: 20px;">
                {{ strtoupper(substr($doctor->name, 0, 1)) }}{{ strtoupper(substr($doctor->surname, 0, 1)) }}
            </div>
            <div>
                <h1 class="h3 fw-bold mb-1">{{ $doctor->name }} {{ $doctor->surname }}</h1>
                <div class="text-muted">
                    <i class="bi bi-envelope"></i> {{ $doctor->email }}
                    @if($doctor->hospital)
                        | <i class="bi bi-hospital"></i> {{ $doctor->hospital }}
                    @endif
                    @if($doctor->position)
                        | <i class="bi bi-briefcase"></i> {{ $doctor->position }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.balances.doctor', $doctor->id) }}" class="row g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold"><i class="bi bi-calendar"></i> Başlanğıc Tarixi</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold"><i class="bi bi-calendar-check"></i> Bitmə Tarixi</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-12 col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-success flex-grow-1">
                        <i class="bi bi-filter"></i> Filtrlə
                    </button>
                    <a href="{{ route('admin.balances.doctor', $doctor->id) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Təmizlə
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="opacity-90 small mb-2"><i class="bi bi-wallet2"></i> Ümumi Balans</div>
                            <div class="fs-3 fw-bold">{{ number_format($stats['total_balance'], 2, '.', '') }} AZN</div>
                        </div>
                        <i class="bi bi-cash-coin display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="opacity-90 small mb-2"><i class="bi bi-credit-card"></i> Ödənilmiş</div>
                            <div class="fs-3 fw-bold">{{ number_format($doctor->getTotalPaidAmount(), 2, '.', '') }} AZN</div>
                        </div>
                        <i class="bi bi-check2-circle display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="opacity-90 small mb-2"><i class="bi bi-piggy-bank"></i> Qalıq Balans</div>
                            <div class="fs-3 fw-bold">{{ number_format($doctor->getRemainingBalance(), 2, '.', '') }} AZN</div>
                        </div>
                        <i class="bi bi-wallet display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="opacity-90 small mb-2"><i class="bi bi-file-medical"></i> Göndərişlər</div>
                            <div class="fs-3 fw-bold">{{ $stats['approved_referrals'] }} / {{ $stats['total_referrals'] }}</div>
                        </div>
                        <i class="bi bi-files display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="opacity-90 small mb-2"><i class="bi bi-clipboard-pulse"></i> Adi Göndərişlər</div>
                            <div class="fs-3 fw-bold">{{ $stats['normal_count'] }}</div>
                        </div>
                        <i class="bi bi-clipboard-data display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="opacity-90 small mb-2"><i class="bi bi-percent"></i> Endirimli Göndərişlər</div>
                            <div class="fs-3 fw-bold">{{ $stats['discounted_count'] }}</div>
                        </div>
                        <i class="bi bi-tag display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History -->
    @if($doctor->paymentsReceived->count() > 0)
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
                            <th>Admin</th>
                            <th>Qeyd</th>
                            <th>Tarix</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($doctor->paymentsReceived as $payment)
                            <tr>
                                <td class="fw-semibold">#{{ $payment->id }}</td>
                                <td>
                                    <span class="badge bg-success fs-6">{{ number_format($payment->amount, 2, '.', '') }} AZN</span>
                                </td>
                                <td>
                                    <div class="small">
                                        <div class="fw-semibold">{{ $payment->admin->name }} {{ $payment->admin->surname }}</div>
                                        <div class="text-muted">{{ $payment->admin->email }}</div>
                                    </div>
                                </td>
                                <td>
                                    @if($payment->note)
                                        <span class="text-muted" data-bs-toggle="tooltip" title="{{ $payment->note }}">
                                            {{ Str::limit($payment->note, 40) }}
                                        </span>
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

    <!-- Details Tables -->

    <!-- Normal Referrals (With Percentage) -->
    @if(count($normalReferrals) > 0)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 fw-bold"><i class="bi bi-table text-success"></i> Adi Göndərişlər (Faizlə Hesablanan)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th><i class="bi bi-hash"></i> Göndəriş</th>
                            <th><i class="bi bi-person"></i> Xəstə</th>
                            <th class="d-none d-md-table-cell"><i class="bi bi-credit-card"></i> FIN</th>
                            <th><i class="bi bi-clipboard-pulse"></i> Analiz</th>
                            <th class="text-end"><i class="bi bi-cash"></i> Qiymət</th>
                            <th class="text-center"><i class="bi bi-percent"></i> Faiz</th>
                            <th class="text-end"><i class="bi bi-coin"></i> Komissiya</th>
                            <th class="text-center d-none d-lg-table-cell"><i class="bi bi-flag"></i> Status</th>
                            <th class="d-none d-md-table-cell"><i class="bi bi-calendar"></i> Tarix</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($normalReferrals as $detail)
                            <tr>
                                <td><span class="badge bg-secondary">#{{ $detail['referral_id'] }}</span></td>
                                <td>{{ $detail['patient_name'] }}</td>
                                <td class="d-none d-md-table-cell"><small class="text-muted">{{ $detail['patient_fin'] }}</small></td>
                                <td>{{ $detail['analysis_name'] }}</td>
                                <td class="text-end">
                                    <span class="badge bg-info-subtle text-info border border-info">
                                        {{ number_format($detail['analysis_price'], 2, '.', '') }} AZN
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning">{{ number_format($detail['commission_percentage'], 2, '.', '') }}%</span>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-success">{{ number_format($detail['commission_amount'], 2, '.', '') }} AZN</span>
                                </td>
                                <td class="text-center d-none d-lg-table-cell">
                                    @if($detail['status'] == 'pending')
                                        <span class="badge bg-warning">Gözləyir</span>
                                    @elseif($detail['status'] == 'completed')
                                        <span class="badge bg-success">Tamamlandı</span>
                                    @else
                                        <span class="badge bg-danger">Ləğv edildi</span>
                                    @endif
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <small class="text-muted">{{ $detail['date']->format('d.m.Y H:i') }}</small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td colspan="6" class="text-end">ARA CƏMI:</td>
                            <td class="text-end text-success">{{ number_format(collect($normalReferrals)->sum('commission_amount'), 2, '.', '') }} AZN</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Discounted Referrals (Admin Set Commission) -->
    @if(count($discountedReferrals) > 0)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 fw-bold"><i class="bi bi-percent text-warning"></i> Endirimli Göndərişlər (Admin Təyin Edib)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th><i class="bi bi-hash"></i> Göndəriş</th>
                            <th><i class="bi bi-person"></i> Xəstə</th>
                            <th class="d-none d-md-table-cell"><i class="bi bi-credit-card"></i> FIN</th>
                            <th class="text-center"><i class="bi bi-clipboard-pulse"></i> Analizlər</th>
                            <th class="text-end"><i class="bi bi-cash"></i> Final Qiymət</th>
                            <th class="text-center"><i class="bi bi-tag"></i> Endirim</th>
                            <th class="text-end"><i class="bi bi-coin"></i> Komissiya</th>
                            <th class="text-center d-none d-lg-table-cell"><i class="bi bi-flag"></i> Status</th>
                            <th class="d-none d-md-table-cell"><i class="bi bi-calendar"></i> Tarix</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($discountedReferrals as $detail)
                            <tr>
                                <td><span class="badge bg-secondary">#{{ $detail['referral_id'] }}</span></td>
                                <td>{{ $detail['patient_name'] }}</td>
                                <td class="d-none d-md-table-cell"><small class="text-muted">{{ $detail['patient_fin'] }}</small></td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $detail['analyses_count'] }} analiz</span>
                                </td>
                                <td class="text-end">
                                    <div>
                                        <span class="badge bg-info-subtle text-info border border-info">
                                            {{ number_format($detail['final_price'], 2, '.', '') }} AZN
                                        </span>
                                        <small class="text-muted d-block">Vergi daxil: {{ number_format($detail['final_price'] * 1.3, 2, '.', '') }} AZN</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($detail['discount_type'] === 'percentage')
                                        <span class="badge bg-warning text-dark">{{ $detail['discount_value'] }}%</span>
                                    @elseif($detail['discount_type'] === 'amount')
                                        <span class="badge bg-warning text-dark">{{ number_format($detail['discount_value'], 2) }} AZN</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-success">{{ number_format($detail['commission_amount'], 2, '.', '') }} AZN</span>
                                </td>
                                <td class="text-center d-none d-lg-table-cell">
                                    @if($detail['status'] == 'pending')
                                        <span class="badge bg-warning">Gözləyir</span>
                                    @elseif($detail['status'] == 'completed')
                                        <span class="badge bg-success">Tamamlandı</span>
                                    @else
                                        <span class="badge bg-danger">Ləğv edildi</span>
                                    @endif
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <small class="text-muted">{{ $detail['date']->format('d.m.Y H:i') }}</small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td colspan="6" class="text-end">ARA CƏMI:</td>
                            <td class="text-end text-success">{{ number_format(collect($discountedReferrals)->sum('commission_amount'), 2, '.', '') }} AZN</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Total Summary -->
    @if(count($normalReferrals) > 0 || count($discountedReferrals) > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center p-3 rounded" style="background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%);">
                        <h4 class="text-white mb-0 fw-bold">
                            <i class="bi bi-wallet2"></i> ÜMUMİ BALANS
                        </h4>
                        <h3 class="text-white mb-0 fw-bold">
                            {{ number_format($stats['total_balance'], 2, '.', '') }} AZN
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted d-block mb-3 opacity-50"></i>
            <p class="text-muted">Təsdiqlənmiş göndəriş yoxdur</p>
        </div>
    </div>
    @endif
@endsection
