@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.balances') }}" class="text-success text-decoration-none">Balanslar</a></li>
            <li class="breadcrumb-item active">Ödəniş et</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Doctor Info Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3" style="color: #1e7e4f;"><i class="bi bi-person-badge"></i> Doktor Məlumatları</h5>
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 60px; height: 60px; background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%); color: white; font-weight: bold; font-size: 24px;">
                            {{ strtoupper(substr($doctor->name, 0, 1)) }}{{ strtoupper(substr($doctor->surname, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1">{{ $doctor->name }} {{ $doctor->surname }}</h5>
                            <div class="text-muted small">
                                <i class="bi bi-envelope"></i> {{ $doctor->email }}
                                @if($doctor->hospital)
                                    <span class="ms-3"><i class="bi bi-hospital"></i> {{ $doctor->hospital }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Balance Info Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3" style="color: #1e7e4f;"><i class="bi bi-wallet2"></i> Balans Məlumatları</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 rounded" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                                <div class="text-white opacity-90 small mb-1">Ümumi Balans</div>
                                <div class="text-white fs-4 fw-bold">{{ number_format($totalBalance, 2, '.', '') }} AZN</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                <div class="text-white opacity-90 small mb-1">Ödənilmiş</div>
                                <div class="text-white fs-4 fw-bold">{{ number_format($totalPaid, 2, '.', '') }} AZN</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                                <div class="text-white opacity-90 small mb-1">Qalıq Balans</div>
                                <div class="text-white fs-4 fw-bold">{{ number_format($remainingBalance, 2, '.', '') }} AZN</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Form Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4" style="color: #1e7e4f;"><i class="bi bi-cash-coin"></i> Ödəniş Məlumatları</h5>

                    <form action="{{ route('admin.payments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Ödəniş Məbləği (AZN) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-currency-dollar"></i></span>
                                <input type="number"
                                       name="amount"
                                       class="form-control @error('amount') is-invalid @enderror"
                                       step="0.01"
                                       min="0.01"
                                       max="{{ $remainingBalance }}"
                                       value="{{ old('amount', $remainingBalance) }}"
                                       required
                                       placeholder="Məbləği daxil edin">
                                <span class="input-group-text">AZN</span>
                            </div>
                            @error('amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bi bi-info-circle"></i> Maksimum ödəyə biləcəyiniz məbləğ: <strong>{{ number_format($remainingBalance, 2, '.', '') }} AZN</strong>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Qeyd (İstəyə bağlı)</label>
                            <textarea name="note"
                                      class="form-control @error('note') is-invalid @enderror"
                                      rows="4"
                                      placeholder="Ödəniş haqqında qeyd...">{{ old('note') }}</textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle-fill"></i>
                            Ödəniş təsdiqləndikdən sonra doktora bildiriş göndəriləcək və balansdan avtomatik çıxılacaq.
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-check-circle"></i> Ödənişi Təsdiq Et
                            </button>
                            <a href="{{ route('admin.balances') }}" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-x-circle"></i> İmtina
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
