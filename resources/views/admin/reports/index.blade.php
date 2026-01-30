@extends('layouts.app')

@section('title', 'Raporlar')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2>Admin Raporları</h2>
        <p class="text-muted">Sistem üzrə mühüm raporları görüntüləyin və Excel formatında yükləyin</p>
    </div>

    <!-- Report Selection and Date Range -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-funnel"></i> Raport Filtri</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.generate') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="report_type" class="form-label">Raport Növü <span class="text-danger">*</span></label>
                        <select class="form-control @error('report_type') is-invalid @enderror"
                                id="report_type"
                                name="report_type"
                                required>
                            <option value="">Raport seçin...</option>

                            <optgroup label="Maliyyə Raporları">
                                <option value="financial-summary" {{ old('report_type', $reportType ?? '') == 'financial-summary' ? 'selected' : '' }}>
                                    Ümumi Maliyyə Hesabatı
                                </option>
                                <option value="daily-revenue" {{ old('report_type', $reportType ?? '') == 'daily-revenue' ? 'selected' : '' }}>
                                    Günlük Gəlir
                                </option>
                                <option value="monthly-revenue" {{ old('report_type', $reportType ?? '') == 'monthly-revenue' ? 'selected' : '' }}>
                                    Aylıq Gəlir
                                </option>
                            </optgroup>

                            <optgroup label="Xəstə Raporları">
                                <option value="patient-statistics" {{ old('report_type', $reportType ?? '') == 'patient-statistics' ? 'selected' : '' }}>
                                    Xəstə Statistikası
                                </option>
                                <option value="repeat-patients" {{ old('report_type', $reportType ?? '') == 'repeat-patients' ? 'selected' : '' }}>
                                    Təkrar Müraciət Edən Xəstələr
                                </option>
                            </optgroup>

                            <optgroup label="Analiz Raporları">
                                <option value="popular-analyses" {{ old('report_type', $reportType ?? '') == 'popular-analyses' ? 'selected' : '' }}>
                                    Ən Populyar Analizlər
                                </option>
                                <option value="analysis-revenue" {{ old('report_type', $reportType ?? '') == 'analysis-revenue' ? 'selected' : '' }}>
                                    Analizlərin Gəliri
                                </option>
                                <option value="analysis-by-category" {{ old('report_type', $reportType ?? '') == 'analysis-by-category' ? 'selected' : '' }}>
                                    Analiz Növü üzrə Qruplaşdırma
                                </option>
                                <option value="doctor-analysis-category" {{ old('report_type', $reportType ?? '') == 'doctor-analysis-category' ? 'selected' : '' }}>
                                    Doktorların Analiz Növü üzrə Göndərişləri
                                </option>
                            </optgroup>

                            <optgroup label="Həkim Performans Raporları">
                                <option value="doctor-performance" {{ old('report_type', $reportType ?? '') == 'doctor-performance' ? 'selected' : '' }}>
                                    Həkim Performans Raporu
                                </option>
                                <option value="doctor-ranking" {{ old('report_type', $reportType ?? '') == 'doctor-ranking' ? 'selected' : '' }}>
                                    Həkim Performans Rankınq
                                </option>
                            </optgroup>

                            <optgroup label="Endirim və Əməliyyat">
                                <option value="discount-report" {{ old('report_type', $reportType ?? '') == 'discount-report' ? 'selected' : '' }}>
                                    Endirim Statistikası
                                </option>
                                <option value="referral-status" {{ old('report_type', $reportType ?? '') == 'referral-status' ? 'selected' : '' }}>
                                    Göndəriş Statusu Raporu
                                </option>
                            </optgroup>
                        </select>
                        @error('report_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div id="date-range-section" class="row g-3 mt-2" style="display: {{ isset($reportType) ? 'flex' : 'none' }};">
                    <div class="col-md-5">
                        <label for="start_date" class="form-label">Başlanğıc Tarixi <span class="text-danger">*</span></label>
                        <input type="date"
                               class="form-control @error('start_date') is-invalid @enderror"
                               id="start_date"
                               name="start_date"
                               value="{{ old('start_date', $startDate ?? '') }}">
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-5">
                        <label for="end_date" class="form-label">Son Tarix <span class="text-danger">*</span></label>
                        <input type="date"
                               class="form-control @error('end_date') is-invalid @enderror"
                               id="end_date"
                               name="end_date"
                               value="{{ old('end_date', $endDate ?? '') }}">
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Raporla
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('report_type').addEventListener('change', function() {
            const dateRangeSection = document.getElementById('date-range-section');
            if (this.value) {
                dateRangeSection.style.display = 'flex';
            } else {
                dateRangeSection.style.display = 'none';
            }
        });
    </script>

    <!-- Report Results -->
    @if(isset($reportData) && isset($reportType))
        <div class="mb-3 text-end">
            <a href="{{ route('admin.reports.export', ['report_type' => $reportType, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
               class="btn btn-success"
               target="_blank">
                <i class="bi bi-file-earmark-excel"></i> Excel-ə Köçür
            </a>
        </div>

        @if($reportType == 'financial-summary')
            @include('admin.reports.partials.financial-summary')
        @elseif($reportType == 'daily-revenue')
            @include('admin.reports.partials.daily-revenue')
        @elseif($reportType == 'monthly-revenue')
            @include('admin.reports.partials.monthly-revenue')
        @elseif($reportType == 'patient-statistics')
            @include('admin.reports.partials.patient-statistics')
        @elseif($reportType == 'repeat-patients')
            @include('admin.reports.partials.repeat-patients')
        @elseif($reportType == 'popular-analyses')
            @include('admin.reports.partials.popular-analyses')
        @elseif($reportType == 'analysis-revenue')
            @include('admin.reports.partials.analysis-revenue')
        @elseif($reportType == 'analysis-by-category')
            @include('admin.reports.partials.analysis-by-category')
        @elseif($reportType == 'doctor-performance')
            @include('admin.reports.partials.doctor-performance')
        @elseif($reportType == 'doctor-ranking')
            @include('admin.reports.partials.doctor-ranking')
        @elseif($reportType == 'discount-report')
            @include('admin.reports.partials.discount-report')
        @elseif($reportType == 'referral-status')
            @include('admin.reports.partials.referral-status')
        @elseif($reportType == 'doctor-analysis-category')
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-bar-graph"></i> Doktorların Analiz Növü üzrə Göndərişləri</h5>
                    <a href="{{ route('admin.reports.doctor-analysis-category.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                       class="btn btn-light btn-sm"
                       target="_blank">
                        <i class="bi bi-file-earmark-excel"></i> Excel-ə Köçür
                    </a>
                </div>
            <div class="card-body">
                <p class="text-muted mb-3">
                    <i class="bi bi-calendar-range"></i>
                    Tarix aralığı: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d.m.Y') }}</strong> -
                    <strong>{{ \Carbon\Carbon::parse($endDate)->format('d.m.Y') }}</strong>
                </p>

                @if(count($reportData) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Doktor</th>
                                    @foreach($categories as $category)
                                        <th class="text-center">{{ $category->name }}</th>
                                    @endforeach
                                    <th class="text-center">CƏMI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reportData as $data)
                                    <tr>
                                        <td class="fw-bold">{{ $data['doctor']->name }}</td>
                                        @foreach($categories as $category)
                                            <td class="text-center">
                                                @php
                                                    $count = $data['categories'][$category->id]['count'] ?? 0;
                                                @endphp
                                                {{ $count }}
                                            </td>
                                        @endforeach
                                        <td class="text-center">
                                            <strong>{{ $data['total'] }}</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>ÜMUMI</th>
                                    @foreach($categories as $category)
                                        <th class="text-center">
                                            @php
                                                $categoryTotal = 0;
                                                foreach($reportData as $data) {
                                                    $categoryTotal += $data['categories'][$category->id]['count'] ?? 0;
                                                }
                                            @endphp
                                            <strong>{{ $categoryTotal }}</strong>
                                        </th>
                                    @endforeach
                                    <th class="text-center">
                                        @php
                                            $grandTotal = 0;
                                            foreach($reportData as $data) {
                                                $grandTotal += $data['total'];
                                            }
                                        @endphp
                                        <strong>{{ $grandTotal }}</strong>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Seçilən tarix aralığında heç bir məlumat tapılmadı.
                    </div>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
