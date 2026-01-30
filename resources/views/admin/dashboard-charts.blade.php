@extends('layouts.app')

@section('title', 'Admin Panel - Qrafiklər')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Ana Səhifə</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.users') }}">İstifadəçilər</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.analyses') }}">Analizlər</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.balances') }}">Balanslar</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.settings') }}">Ayarlar</a></li>
@endsection

@section('content')
    <div class="mb-4">
        <h1 class="fw-bold text-dark mb-2">Dashboard Qrafiklər</h1>
        <p class="text-muted">Sistem statistikaları və təhlillər</p>
    </div>

    <!-- Row 1: Aylıq Statistika və Gəlir -->
    <div class="row g-3 mb-3">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-graph-up text-success"></i> Aylıq Göndəriş Statistikası</h6>
                    <div id="monthlyReferralsChart"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-currency-dollar text-warning"></i> Gəlir Trendi</h6>
                    <div id="revenueTrendChart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Top Analizlər və Top Doktorlar -->
    <div class="row g-3 mb-3">
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-clipboard-data text-primary"></i> Ən Çox İstənilən Analizlər</h6>
                    <div id="topAnalysesChart"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-person-badge text-info"></i> Ən Aktiv Doktorlar</h6>
                    <div id="doctorPerformanceChart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Xəstə Dinamikası -->
    <div class="row g-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-people-fill text-danger"></i> Xəstə Sayı Dinamikası</h6>
                    <div id="patientDynamicsChart"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // EXON Tema Rəngləri
    const colors = {
        primary: '#2D9B6C',
        success: '#10b981',
        warning: '#f59e0b',
        danger: '#ef4444',
        info: '#06b6d4',
        purple: '#8b5cf6'
    };

    // 1. Aylıq Göndəriş Qrafiki
    const monthlyReferralsOptions = {
        series: [{
            name: 'Göndərişlər',
            data: @json($monthlyReferrals ?? array_fill(0, 12, 0))
        }],
        chart: {
            type: 'area',
            height: 280,
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        colors: [colors.primary],
        dataLabels: { enabled: false },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.1,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: @json($monthlyLabels ?? []),
            labels: {
                style: { fontSize: '11px' }
            }
        },
        yaxis: {
            labels: {
                style: { fontSize: '11px' }
            }
        },
        grid: {
            borderColor: '#f1f1f1',
            strokeDashArray: 3
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + ' göndəriş';
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#monthlyReferralsChart"), monthlyReferralsOptions).render();

    // 2. Gəlir Trendi
    const revenueTrendOptions = {
        series: [{
            name: 'Gəlir',
            data: @json($revenueTrend ?? array_fill(0, 6, 0))
        }],
        chart: {
            type: 'line',
            height: 280,
            toolbar: { show: false },
            sparkline: { enabled: false }
        },
        colors: [colors.warning],
        stroke: {
            curve: 'smooth',
            width: 3
        },
        xaxis: {
            categories: @json($revenueLabels ?? []),
            labels: {
                style: { fontSize: '10px' }
            }
        },
        yaxis: {
            labels: {
                formatter: function(val) {
                    return val.toFixed(0) + ' ₼';
                },
                style: { fontSize: '10px' }
            }
        },
        grid: {
            borderColor: '#f1f1f1',
            strokeDashArray: 3
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val.toFixed(2) + ' AZN';
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#revenueTrendChart"), revenueTrendOptions).render();

    // 3. Ən Çox İstənilən Analizlər
    const topAnalysesData = @json($topAnalyses ?? []);
    const topAnalysesOptions = {
        series: [{
            name: 'Sayı',
            data: topAnalysesData.map(a => a.count)
        }],
        chart: {
            type: 'bar',
            height: 320,
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                borderRadius: 6,
                horizontal: true,
                distributed: true
            }
        },
        colors: [colors.primary, colors.success, colors.info, colors.purple, colors.warning],
        dataLabels: {
            enabled: true,
            style: {
                fontSize: '11px',
                fontWeight: 'bold'
            }
        },
        xaxis: {
            categories: topAnalysesData.map(a => a.name.length > 25 ? a.name.substring(0, 25) + '...' : a.name),
            labels: {
                style: { fontSize: '11px' }
            }
        },
        yaxis: {
            labels: {
                style: { fontSize: '11px' }
            }
        },
        legend: { show: false },
        grid: {
            borderColor: '#f1f1f1',
            strokeDashArray: 3
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + ' dəfə';
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#topAnalysesChart"), topAnalysesOptions).render();

    // 4. Doktor Performansı
    const doctorData = @json($doctorPerformance ?? []);
    const doctorPerformanceOptions = {
        series: [{
            name: 'Göndərişlər',
            data: doctorData.map(d => d.referrals_count || d.count || 0)
        }],
        chart: {
            type: 'bar',
            height: 320,
            toolbar: { show: false }
        },
        plotOptions: {
            bar: {
                borderRadius: 6,
                horizontal: true,
                distributed: true
            }
        },
        colors: ['#6366f1', '#8b5cf6', '#ec4899', '#f43f5e', '#f59e0b'],
        dataLabels: {
            enabled: true,
            style: {
                fontSize: '11px',
                fontWeight: 'bold'
            }
        },
        xaxis: {
            categories: doctorData.map(d => d.name),
            labels: {
                style: { fontSize: '11px' }
            }
        },
        yaxis: {
            labels: {
                style: { fontSize: '11px' }
            }
        },
        legend: { show: false },
        grid: {
            borderColor: '#f1f1f1',
            strokeDashArray: 3
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + ' göndəriş';
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#doctorPerformanceChart"), doctorPerformanceOptions).render();

    // 5. Xəstə Dinamikası
    const patientDynamicsOptions = {
        series: [{
            name: 'Xəstələr',
            data: @json($patientDynamics ?? array_fill(0, 12, 0))
        }],
        chart: {
            type: 'area',
            height: 250,
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        colors: [colors.info],
        dataLabels: { enabled: false },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.1,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: @json($patientLabels ?? []),
            labels: {
                style: { fontSize: '11px' }
            }
        },
        yaxis: {
            labels: {
                style: { fontSize: '11px' }
            }
        },
        grid: {
            borderColor: '#f1f1f1',
            strokeDashArray: 3
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + ' xəstə';
                }
            }
        }
    };
    new ApexCharts(document.querySelector("#patientDynamicsChart"), patientDynamicsOptions).render();
</script>
@endsection
