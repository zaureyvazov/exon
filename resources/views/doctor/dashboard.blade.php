@php
    use App\Models\Setting;
    $canSeePrices = Setting::doctorCanSeePrices();
@endphp

@extends('layouts.app')

@section('title', 'Doktor Panel')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.dashboard') }}">Ana Səhifə</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.patients') }}">Xəstələr</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.patients.create') }}">Yeni Xəstə</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.referrals') }}">Göndərişlər</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.balance') }}">Balans</a></li>
@endsection

@section('content')
    <div class="mb-4">
        <h1 class="fw-bold text-dark mb-2">Xoş Gəlmisiniz, Dr. {{ Auth::user()->name }}</h1>
        <p class="text-muted">Sistem statistikalarınız</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm" style="transition: all 0.3s ease;" onmouseover="this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" onmouseout="this.style.boxShadow=''">
                <div class="card-body">
                    <div class="text-muted mb-2"><i class="bi bi-people"></i> Ümumi Xəstələr</div>
                    <div class="fs-2 fw-bold text-primary">{{ $stats['total_patients'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm" style="transition: all 0.3s ease;" onmouseover="this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" onmouseout="this.style.boxShadow=''">
                <div class="card-body">
                    <div class="text-muted mb-2"><i class="bi bi-file-earmark-medical"></i> Ümumi Göndərişlər</div>
                    <div class="fs-2 fw-bold text-success">{{ $stats['total_referrals'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm" style="transition: all 0.3s ease;" onmouseover="this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" onmouseout="this.style.boxShadow=''">
                <div class="card-body">
                    <div class="text-muted mb-2"><i class="bi bi-clock"></i> Gözləyən</div>
                    <div class="fs-2 fw-bold text-warning">{{ $stats['pending_referrals'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm" style="transition: all 0.3s ease;" onmouseover="this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" onmouseout="this.style.boxShadow=''">
                <div class="card-body">
                    <div class="text-muted mb-2"><i class="bi bi-check-circle"></i> Tamamlanan</div>
                    <div class="fs-2 fw-bold text-info">{{ $stats['completed_referrals'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm" style="transition: all 0.3s ease;" onmouseover="this.style.boxShadow='0 8px 16px rgba(0,0,0,0.15)'" onmouseout="this.style.boxShadow=''">
                <div class="card-body">
                    <div class="text-muted mb-2"><i class="bi bi-hourglass-split"></i> Qiymət Gözlənilir</div>
                    <div class="fs-2 fw-bold text-info">{{ $stats['awaiting_pricing'] }}</div>
                    <small class="text-muted">Təsdiqlənib, qiymətləndirilməyib</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tez Əməliyyatlar -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <a href="{{ route('doctor.patients.create') }}" class="card border-0 shadow-sm text-decoration-none h-100" style="transition: all 0.3s ease; background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%);" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 12px 24px rgba(45,155,108,0.4)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                <div class="card-body p-4 text-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-4 rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-person-plus" viewBox="0 0 16 16">
                                <path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H1s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C9.516 10.68 8.289 10 6 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                                <path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5"/>
                            </svg>
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold">Yeni Xəstə Əlavə Et</h5>
                            <small class="opacity-75">Yeni xəstə qeydiyyatı</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-md-6">
            <a href="{{ route('doctor.referrals.create') }}" class="card border-0 shadow-sm text-decoration-none h-100" style="transition: all 0.3s ease; background: linear-gradient(135deg, #10b981 0%, #059669 100%);" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 12px 24px rgba(16,185,129,0.4)'" onmouseout="this.style.transform=''; this.style.boxShadow=''">
                <div class="card-body p-4 text-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-4 rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-file-earmark-plus" viewBox="0 0 16 16">
                                <path d="M8 6.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V11a.5.5 0 0 1-1 0V9.5H6a.5.5 0 0 1 0-1h1.5V7a.5.5 0 0 1 .5-.5"/>
                                <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5z"/>
                            </svg>
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold">Yeni Göndəriş Yarat</h5>
                            <small class="opacity-75">Analiz göndərişi yarat</small>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-4 fw-bold">Son Göndərişlər</h5>

            @if($recentReferrals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fw-semibold">ID</th>
                                <th class="fw-semibold">Xəstə</th>
                                <th class="fw-semibold d-none d-md-table-cell">FIN Kod</th>
                                <th class="fw-semibold d-none d-lg-table-cell">Analizlər</th>
                                @if($canSeePrices)
                                <th class="fw-semibold d-none d-lg-table-cell">Qiymət</th>
                                @endif
                                <th class="fw-semibold">Təsdiq</th>
                                <th class="fw-semibold d-none d-md-table-cell">Status</th>
                                <th class="fw-semibold d-none d-xl-table-cell">Tarix</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentReferrals as $referral)
                                <tr>
                                    <td class="fw-bold text-primary">#{{ $referral->id }}</td>
                                    <td>{{ $referral->patient->full_name }}</td>
                                    <td class="d-none d-md-table-cell"><small class="text-muted">{{ $referral->patient->serial_number ?? '-' }}</small></td>
                                    <td class="d-none d-lg-table-cell"><small>{{ $referral->analyses->count() }} analiz</small></td>
                                    @if($canSeePrices)
                                    <td class="d-none d-lg-table-cell"><span class="fw-bold text-success">{{ number_format($referral->total_price, 2) }} AZN</span></td>
                                    @endif
                                    <td>
                                        @if($referral->is_approved)
                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Təsdiqlənib</span>
                                        @else
                                            <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Gözləyir</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-md-table-cell">
                                        @if($referral->status == 'pending')
                                            <span class="badge bg-warning text-dark">Gözləyir</span>
                                        @elseif($referral->status == 'completed')
                                            <span class="badge bg-success">Tamamlandı</span>
                                        @else
                                            <span class="badge bg-danger">Ləğv edildi</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-xl-table-cell"><small class="text-muted">{{ $referral->created_at->format('d.m.Y H:i') }}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                    <p>Hal-hazırda göndəriş yoxdur</p>
                </div>
            @endif
        </div>
    </div>
@endsection
