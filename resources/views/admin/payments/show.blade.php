@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.payments.index') }}" class="text-success text-decoration-none">Ödənişlər</a></li>
            <li class="breadcrumb-item active">Ödəniş #{{ $payment->id }}</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0" style="color: #1e7e4f;">
                        <i class="bi bi-receipt"></i> Ödəniş Detalları
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Payment Amount -->
                    <div class="text-center mb-4 p-4 rounded" style="background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%);">
                        <div class="text-white opacity-90 mb-2">Ödəniş Məbləği</div>
                        <div class="text-white display-4 fw-bold">{{ number_format($payment->amount, 2, '.', '') }} AZN</div>
                    </div>

                    <!-- Doctor Info -->
                    <div class="mb-4">
                        <h6 class="fw-semibold mb-3" style="color: #1e7e4f;"><i class="bi bi-person-badge"></i> Doktor</h6>
                        <div class="d-flex align-items-center gap-3 p-3 rounded bg-light">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 50px; height: 50px; background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%); color: white; font-weight: bold; font-size: 18px;">
                                {{ strtoupper(substr($payment->doctor->name, 0, 1)) }}{{ strtoupper(substr($payment->doctor->surname, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $payment->doctor->name }} {{ $payment->doctor->surname }}</div>
                                <div class="small text-muted">
                                    <i class="bi bi-envelope"></i> {{ $payment->doctor->email }}
                                    @if($payment->doctor->hospital)
                                        <br><i class="bi bi-hospital"></i> {{ $payment->doctor->hospital }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Info -->
                    <div class="mb-4">
                        <h6 class="fw-semibold mb-3" style="color: #1e7e4f;"><i class="bi bi-shield-check"></i> Ödənişi Edən Admin</h6>
                        <div class="p-3 rounded bg-light">
                            <div class="fw-semibold">{{ $payment->admin->name }} {{ $payment->admin->surname }}</div>
                            <div class="small text-muted"><i class="bi bi-envelope"></i> {{ $payment->admin->email }}</div>
                        </div>
                    </div>

                    <!-- Payment Note -->
                    @if($payment->note)
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3" style="color: #1e7e4f;"><i class="bi bi-chat-left-text"></i> Qeyd</h6>
                            <div class="p-3 rounded bg-light">
                                {{ $payment->note }}
                            </div>
                        </div>
                    @endif

                    <!-- Payment Date -->
                    <div class="mb-4">
                        <h6 class="fw-semibold mb-3" style="color: #1e7e4f;"><i class="bi bi-calendar-event"></i> Ödəniş Tarixi</h6>
                        <div class="p-3 rounded bg-light">
                            <div class="fw-semibold">{{ $payment->created_at->format('d.m.Y H:i') }}</div>
                            <div class="small text-muted">{{ $payment->created_at->diffForHumans() }}</div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex gap-2 mt-4">
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Geri
                        </a>
                        <button onclick="window.print()" class="btn btn-success">
                            <i class="bi bi-printer"></i> Çap Et
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .btn, nav, .navbar, .footer {
            display: none !important;
        }
    }
</style>
@endsection
