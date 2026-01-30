@extends('layouts.app')

@section('title', 'Qeydiyyatçı Panel')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('registrar.dashboard') }}">Ana Səhifə</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('registrar.referrals') }}">Göndərişlər</a></li>
@endsection

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-1">Xoş Gəlmisiniz, {{ Auth::user()->name }}</h1>
        <div class="text-muted">Sistem statistikaları</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%);">
                <div class="card-body text-white">
                    <div class="opacity-90 small mb-2"><i class="bi bi-file-earmark-medical"></i> Ümumi Göndərişlər</div>
                    <div class="fs-4 fw-bold">{{ $stats['total_referrals'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <div class="card-body text-white">
                    <div class="opacity-90 small mb-2"><i class="bi bi-clock-history"></i> Gözləyən</div>
                    <div class="fs-4 fw-bold">{{ $stats['pending_referrals'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="card-body text-white">
                    <div class="opacity-90 small mb-2"><i class="bi bi-check-circle"></i> Tamamlanan</div>
                    <div class="fs-4 fw-bold">{{ $stats['completed_referrals'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                <div class="card-body text-white">
                    <div class="opacity-90 small mb-2"><i class="bi bi-calendar-check"></i> Bu Gün</div>
                    <div class="fs-4 fw-bold">{{ $stats['today_referrals'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
                <h5 class="mb-2 mb-md-0 fw-bold"><i class="bi bi-clock-history text-success"></i> Son Göndərişlər</h5>
                <a href="{{ route('registrar.referrals') }}" class="btn" style="background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%); color: white; border: none;">Bütün Göndərişlər</a>
            </div>

            @if($recentReferrals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Xəstə</th>
                                <th class="d-none d-md-table-cell">Kimlik No</th>
                                <th class="d-none d-lg-table-cell">Doktor</th>
                                <th>Analizlər</th>
                                <th class="d-none d-md-table-cell">Qiymət</th>
                                <th>Təsdiq</th>
                                <th class="d-none d-lg-table-cell">Status</th>
                                <th class="d-none d-md-table-cell">Tarix</th>
                                <th>Əməliyyat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentReferrals as $referral)
                                <tr class="{{ $referral->discount_type !== 'none' ? 'table-warning' : '' }}">
                                    <td>#{{ $referral->id }}</td>
                                    <td>{{ $referral->patient->full_name }}</td>
                                    <td class="d-none d-md-table-cell">{{ $referral->patient->serial_number ?? '-' }}</td>
                                    <td class="d-none d-lg-table-cell">Dr. {{ $referral->doctor->name }}</td>
                                    <td>{{ $referral->analyses->count() }} analiz</td>
                                    <td class="d-none d-md-table-cell">
                                        @if($referral->discount_type !== 'none' && $referral->final_price)
                                            <div><strong class="text-success">{{ number_format($referral->final_price_with_tax, 2) }} AZN</strong></div>
                                            <small class="text-muted"><s>{{ number_format($referral->total_price_with_tax, 2) }} AZN</s></small>
                                        @else
                                            {{ number_format($referral->total_price_with_tax, 2) }} AZN
                                        @endif
                                    </td>
                                    <td>
                                        @if($referral->is_approved)
                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Təsdiqlənib</span>
                                        @else
                                            <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Gözləyir</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        @if($referral->status == 'pending')
                                            <span class="badge bg-warning text-dark">Gözləyir</span>
                                        @elseif($referral->status == 'completed')
                                            <span class="badge bg-success">Tamamlandı</span>
                                        @else
                                            <span class="badge bg-danger">Ləğv edildi</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-md-table-cell">{{ $referral->created_at->format('d.m.Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('registrar.referrals.show', $referral->id) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i> Bax
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-muted py-5">Göndəriş yoxdur</p>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
// Auto-refresh page every 60 seconds
let refreshInterval;

function startAutoRefresh() {
    refreshInterval = setInterval(() => {
        if (!document.hidden) {
            location.reload();
        }
    }, 60000); // 60 seconds
}

// Start refresh when page loads
document.addEventListener('DOMContentLoaded', startAutoRefresh);

// Clear interval when leaving page
window.addEventListener('beforeunload', () => {
    if (refreshInterval) clearInterval(refreshInterval);
});
</script>
@endpush
