@extends('layouts.app')

@section('title', 'Admin Panel')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Ana Səhifə</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.users') }}">İstifadəçilər</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.analyses') }}">Analizlər</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.balances') }}">Balanslar</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.settings') }}">Ayarlar</a></li>
@endsection

@section('content')
    <div class="mb-4">
        <h1 class="fw-bold text-dark mb-2">Admin Panel</h1>
        <p class="text-muted">Sistem idarəetməsi</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="opacity-90 mb-2"><i class="bi bi-people"></i> Ümumi İstifadəçilər</div>
                            <div class="fs-2 fw-bold">{{ $stats['total_users'] }}</div>
                        </div>
                        <i class="bi bi-people display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="opacity-90 mb-2"><i class="bi bi-person-badge"></i> Doktorlar</div>
                            <div class="fs-2 fw-bold">{{ $stats['total_doctors'] }}</div>
                        </div>
                        <i class="bi bi-person-badge display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="opacity-90 mb-2"><i class="bi bi-person-check"></i> Qeydiyyatçılar</div>
                            <div class="fs-2 fw-bold">{{ $stats['total_registrars'] }}</div>
                        </div>
                        <i class="bi bi-person-check display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <a href="{{ route('admin.active.sessions') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="opacity-90 mb-2">
                                    <i class="bi bi-people-fill"></i> Aktiv İstifadəçilər
                                    <span class="spinner-border spinner-border-sm ms-1" role="status" id="activeUsersLoader" style="display: none;">
                                        <span class="visually-hidden">Yüklənir...</span>
                                    </span>
                                </div>
                                <div class="fs-2 fw-bold" id="activeUsersCount">
                                    <span class="spinner-border spinner-border-sm" role="status">
                                        <span class="visually-hidden">Yüklənir...</span>
                                    </span>
                                </div>
                                <small class="opacity-75">Son 2 dəqiqə</small>
                            </div>
                            <i class="bi bi-people-fill display-4 opacity-25"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-lg-6">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="opacity-90 mb-2"><i class="bi bi-file-earmark-medical"></i> Ümumi Göndərişlər</div>
                            <div class="fs-2 fw-bold">{{ $stats['total_referrals'] }}</div>
                        </div>
                        <i class="bi bi-file-earmark-medical display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-6">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="opacity-90 mb-2"><i class="bi bi-clipboard-pulse"></i> Analizlər</div>
                            <div class="fs-2 fw-bold">{{ $stats['total_analyses'] }}</div>
                        </div>
                        <i class="bi bi-clipboard-pulse display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <a href="{{ route('admin.discounted.referrals') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="opacity-90 mb-2"><i class="bi bi-percent"></i> Komissiya Gözləyən</div>
                                <div class="fs-2 fw-bold">{{ $stats['awaiting_commission'] }}</div>
                                <small class="opacity-75">Endirimli göndərişlər</small>
                            </div>
                            <i class="bi bi-exclamation-triangle display-4 opacity-25"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-4 fw-bold"><i class="bi bi-lightning-charge-fill text-warning"></i> Tez Əməliyyatlar</h5>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.users.create') }}" class="btn btn-lg" style="background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%); color: white; border: none;">
                    <i class="bi bi-person-plus"></i> Yeni İstifadəçi
                </a>
                <a href="{{ route('admin.analyses.create') }}" class="btn btn-lg" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none;">
                    <i class="bi bi-plus-circle"></i> Yeni Analiz
                </a>
                <a href="{{ route('admin.users') }}" class="btn btn-outline-success btn-lg">
                    <i class="bi bi-people"></i> İstifadəçiləri İdarə Et
                </a>
                <a href="{{ route('admin.analyses') }}" class="btn btn-outline-success btn-lg">
                    <i class="bi bi-clipboard-pulse"></i> Analizləri İdarə Et
                </a>
                <a href="{{ route('admin.charts') }}" class="btn btn-lg" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: white; border: none;">
                    <i class="bi bi-bar-chart-fill"></i> Dashboard Qrafiklər
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Aktiv istifadəçi sayını yeniləmək üçün funksiya
    function updateActiveUsersCount() {
        const countElement = document.getElementById('activeUsersCount');
        const loaderElement = document.getElementById('activeUsersLoader');

        // Loader-i göstər
        if (loaderElement && countElement && !countElement.querySelector('.spinner-border')) {
            loaderElement.style.display = 'inline-block';
        }

        fetch('{{ route("admin.active.users.count") }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (countElement) {
                countElement.textContent = data.active_count || 0;
            }
            if (loaderElement) {
                loaderElement.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Aktiv istifadəçi sayı yüklənərkən xəta:', error);
            if (countElement && !countElement.textContent) {
                countElement.innerHTML = '<i class="bi bi-exclamation-triangle"></i>';
            }
            if (loaderElement) {
                loaderElement.style.display = 'none';
            }
        });
    }

    // Səhifə yüklənəndə
    document.addEventListener('DOMContentLoaded', function() {
        // İlk yükləmə
        updateActiveUsersCount();

        // Hər 20 saniyədə bir yenilə
        setInterval(updateActiveUsersCount, 20000);
    });
</script>
@endpush
