@php
    use App\Models\Setting;
    $canSeePrices = Setting::doctorCanSeePrices();
@endphp

@extends('layouts.app')

@section('title', 'Göndəriş Detalları')

@section('content')
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h1 class="fw-bold mb-1" style="background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    Göndəriş #{{ $referral->id }}
                </h1>
                <p class="text-muted">Göndəriş detalları və analizlər</p>
            </div>
            <div class="d-flex gap-2">
                @if($referral->canBeEditedByDoctor())
                    <a href="{{ route('doctor.referrals.edit', $referral->id) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Redaktə Et
                    </a>
                    <div class="alert alert-warning mb-0 py-2 px-3 d-flex align-items-center">
                        <i class="bi bi-clock-history me-2"></i>
                        <small>Redaktə müddəti: {{ $referral->remaining_edit_time }} dəqiqə qalıb</small>
                    </div>
                @endif
                <a href="{{ route('doctor.referrals') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Geri
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Xəstə Məlumatları -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-4 fw-bold">
                        <i class="bi bi-person-circle text-primary"></i> Xəstə Məlumatları
                    </h5>
                    <div class="mb-3">
                        <small class="text-muted d-block">Ad Soyad</small>
                        <div class="fw-semibold">{{ $referral->patient->full_name }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Seriya Nömrəsi</small>
                        <div class="fw-semibold">{{ $referral->patient->serial_number ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Telefon</small>
                        <div class="fw-semibold">{{ $referral->patient->phone }}</div>
                    </div>
                    @if($referral->patient->email)
                    <div class="mb-3">
                        <small class="text-muted d-block">Email</small>
                        <div class="fw-semibold">{{ $referral->patient->email }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Göndəriş Statusu -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4 fw-bold">
                        <i class="bi bi-info-circle text-info"></i> Status Məlumatları
                    </h5>
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="text-center p-3 rounded" style="background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%);">
                                <div class="text-white opacity-75 small">Göndəriş ID</div>
                                <div class="text-white fs-4 fw-bold">#{{ $referral->id }}</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="text-center p-3 rounded bg-light">
                                <div class="text-muted small">Status</div>
                                <div class="fs-6 fw-bold">
                                    @if($referral->status == 'pending')
                                        <span class="badge bg-warning text-dark">Gözləyir</span>
                                    @elseif($referral->status == 'completed')
                                        <span class="badge bg-success">Tamamlandı</span>
                                    @else
                                        <span class="badge bg-danger">Ləğv edildi</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="text-center p-3 rounded bg-light">
                                <div class="text-muted small">Təsdiq</div>
                                <div class="fs-6 fw-bold">
                                    @if($referral->is_approved)
                                        @if($referral->is_priced)
                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Təsdiqlənib</span>
                                        @else
                                            <span class="badge bg-info"><i class="bi bi-hourglass-split"></i> Qiymətləndirilməyib</span>
                                        @endif
                                    @else
                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Gözləyir</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <div class="text-center p-3 rounded" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                <div class="text-white opacity-75 small">Komissiya</div>
                                <div class="text-white fs-4 fw-bold">
                                    @if($referral->is_priced)
                                        {{ number_format($referral->doctor_commission, 2, '.', '') }} AZN
                                    @else
                                        <small class="fs-6">Qiymət gözlənilir</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <small class="text-muted d-block">Göndəriş Tarixi</small>
                                <div class="fw-semibold">{{ $referral->created_at->format('d.m.Y H:i') }}</div>
                            </div>
                            @if($referral->notes)
                            <div class="col-12 col-md-6 mt-3 mt-md-0">
                                <small class="text-muted d-block">Qeydlər</small>
                                <div class="fw-semibold">{{ $referral->notes }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analizlər Siyahısı -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4 fw-bold">
                        <i class="bi bi-clipboard-pulse text-success"></i> Analizlər və Qiymətlər
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-semibold">#</th>
                                    <th class="fw-semibold">Analiz Adı</th>
                                    <th class="fw-semibold">Təsvir</th>
                                    @if($canSeePrices && $referral->is_priced)
                                    <th class="fw-semibold text-center">Faiz %</th>
                                    <th class="fw-semibold text-end">Qiymət</th>
                                    <th class="fw-semibold text-end">Komissiya</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($referral->analyses as $index => $analysis)
                                @php
                                    $snapshotPrice = $analysis->pivot->analysis_price;
                                    $commissionRate = $analysis->pivot->commission_percentage ?? 20;
                                    $commissionAmount = $snapshotPrice * $commissionRate / 100;
                                    $isCancelled = $analysis->pivot->is_cancelled ?? false;
                                @endphp
                                <tr class="{{ $isCancelled ? 'table-danger' : '' }}">
                                    <td class="fw-bold text-primary">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $analysis->name }}
                                            @if($isCancelled)
                                                <span class="badge bg-danger ms-2">İptal edilib</span>
                                            @endif
                                        </div>
                                        @if($isCancelled && $analysis->pivot->cancellation_reason)
                                            <div class="small text-danger mt-1">
                                                <i class="bi bi-x-circle"></i> Səbəb: {{ $analysis->pivot->cancellation_reason }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $analysis->description ?? '-' }}</small>
                                    </td>
                                    @if($canSeePrices && $referral->is_priced)
                                    <td class="text-center">
                                        @if(!$isCancelled)
                                            <span class="badge bg-warning-subtle text-warning border border-warning">
                                                {{ number_format($commissionRate, 2, '.', '') }}%
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($isCancelled)
                                            <s class="text-muted">{{ number_format($snapshotPrice, 2, '.', '') }} AZN</s>
                                        @else
                                            <span class="badge bg-info-subtle text-info border border-info">
                                                {{ number_format($snapshotPrice, 2, '.', '') }} AZN
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($isCancelled)
                                            <span class="text-muted">0 AZN</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success">
                                                {{ number_format($commissionAmount, 2, '.', '') }} AZN
                                            </span>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    @if($canSeePrices && $referral->is_priced)
                                    <td colspan="4" class="text-end fw-bold">Ümumi Məbləğ:</td>
                                    <td class="text-end">
                                        <span class="badge bg-primary fs-6">
                                            {{ number_format($referral->total_price, 2, '.', '') }} AZN
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-success fs-6">
                                            {{ number_format($referral->doctor_commission, 2, '.', '') }} AZN
                                        </span>
                                    </td>
                                    @else
                                    <td colspan="3" class="text-center text-muted">
                                        <i class="bi bi-hourglass-split"></i> Qiymətləndirmə gözlənilir
                                    </td>
                                    @endif
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
