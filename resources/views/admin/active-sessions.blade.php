@extends('layouts.app')

@section('title', 'Aktiv Sessiyalar')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Aktiv İstifadəçi Sessiyaları</h2>
            <p class="text-muted">Sistemdə aktiv olan istifadəçilər və onların bağlantı məlumatları</p>
        </div>
        <div>
            <span class="badge bg-success fs-6">
                <i class="bi bi-people-fill"></i>
                {{ count($activeSessions) }} Sessiya
            </span>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-success">{{ count(array_filter($activeSessions, fn($s) => $s['is_active'])) }}</h3>
                    <p class="mb-0 text-muted">Hal-hazırda Aktiv</p>
                    <small class="text-muted">(Son 5 dəqiqə)</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-primary">{{ count($activeSessions) }}</h3>
                    <p class="mb-0 text-muted">Toplam Sessiya</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-info">{{ count(array_unique(array_column($activeSessions, 'browser'))) }}</h3>
                    <p class="mb-0 text-muted">Fərqli Brauzer</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-warning">{{ count(array_unique(array_column($activeSessions, 'platform'))) }}</h3>
                    <p class="mb-0 text-muted">Fərqli Platform</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-list-check"></i> Aktiv Sessiya Siyahısı</h5>
        </div>
        <div class="card-body p-0">
            @if(count($activeSessions) > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Status</th>
                                <th>İstifadəçi</th>
                                <th>Rol</th>
                                <th>Brauzer</th>
                                <th>Platform</th>
                                <th>IP Ünvanı</th>
                                <th>Son Fəaliyyət</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeSessions as $session)
                                <tr>
                                    <td>
                                        @if($session['is_active'])
                                            <span class="badge bg-success">
                                                <i class="bi bi-circle-fill" style="font-size: 8px;"></i> Aktiv
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-circle" style="font-size: 8px;"></i> Passiv
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-2" style="width: 35px; height: 35px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                                {{ strtoupper(substr($session['user']->name, 0, 1)) }}{{ strtoupper(substr($session['user']->surname, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $session['user']->name }} {{ $session['user']->surname }}</div>
                                                <small class="text-muted">{{ $session['user']->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($session['user']->isAdmin())
                                            <span class="badge bg-danger">Admin</span>
                                        @elseif($session['user']->isDoctor())
                                            <span class="badge bg-primary">Həkim</span>
                                        @elseif($session['user']->isRegistrar())
                                            <span class="badge bg-info">Qeydiyyatçı</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($session['browser'] == 'Chrome')
                                            <i class="bi bi-browser-chrome text-warning"></i>
                                        @elseif($session['browser'] == 'Firefox')
                                            <i class="bi bi-browser-firefox text-danger"></i>
                                        @elseif($session['browser'] == 'Edge')
                                            <i class="bi bi-browser-edge text-primary"></i>
                                        @elseif($session['browser'] == 'Safari')
                                            <i class="bi bi-browser-safari text-info"></i>
                                        @else
                                            <i class="bi bi-globe"></i>
                                        @endif
                                        {{ $session['browser'] }}
                                    </td>
                                    <td>
                                        @if($session['platform'] == 'Windows')
                                            <i class="bi bi-windows text-primary"></i>
                                        @elseif($session['platform'] == 'Mac OS')
                                            <i class="bi bi-apple text-dark"></i>
                                        @elseif($session['platform'] == 'Android')
                                            <i class="bi bi-android2 text-success"></i>
                                        @elseif($session['platform'] == 'Linux')
                                            <i class="bi bi-ubuntu text-warning"></i>
                                        @else
                                            <i class="bi bi-device-ssd"></i>
                                        @endif
                                        {{ $session['platform'] }}
                                    </td>
                                    <td>
                                        <code>{{ $session['ip_address'] }}</code>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-clock me-1 text-muted"></i>
                                            {{ $session['last_activity']->diffForHumans() }}
                                            <br>
                                            <small class="text-muted">{{ $session['last_activity']->format('d.m.Y H:i') }}</small>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="text-muted mt-3">Aktiv sessiya tapılmadı</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Browser Statistics -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-bar-chart"></i> Brauzer Statistikası</h6>
                </div>
                <div class="card-body">
                    @php
                        $browsers = array_count_values(array_column($activeSessions, 'browser'));
                        arsort($browsers);
                    @endphp
                    @foreach($browsers as $browser => $count)
                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ $browser }}</span>
                                <span class="text-muted">{{ $count }} ({{ round(($count / count($activeSessions)) * 100, 1) }}%)</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-primary" style="width: {{ ($count / count($activeSessions)) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-pie-chart"></i> Platform Statistikası</h6>
                </div>
                <div class="card-body">
                    @php
                        $platforms = array_count_values(array_column($activeSessions, 'platform'));
                        arsort($platforms);
                    @endphp
                    @foreach($platforms as $platform => $count)
                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ $platform }}</span>
                                <span class="text-muted">{{ $count }} ({{ round(($count / count($activeSessions)) * 100, 1) }}%)</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: {{ ($count / count($activeSessions)) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-circle {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.4);
        }
        50% {
            box-shadow: 0 0 0 10px rgba(102, 126, 234, 0);
        }
    }
</style>
@endsection
