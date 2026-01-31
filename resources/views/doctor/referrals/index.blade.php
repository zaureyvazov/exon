@php
    use App\Models\Setting;
    $canSeePrices = Setting::doctorCanSeePrices();
@endphp

@extends('layouts.app')

@section('title', 'Göndərişlərim')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.dashboard') }}">Ana Səhifə</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.patients') }}">Xəstələr</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.patients.create') }}">Yeni Xəstə</a></li>
    <li class="nav-item"><a class="nav-link active" href="{{ route('doctor.referrals') }}">Göndərişlər</a></li>
@endsection

@section('content')
    <div class="mb-4">
        <h1 class="fw-bold mb-1" style="background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Göndərişlərim</h1>
        <p class="text-muted">Klinikaya göndərdiyiniz analizlər</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
        @if($referrals->count() > 0)
            <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fw-semibold">ID</th>
                        <th class="fw-semibold">Xəstə</th>
                        <th class="fw-semibold d-none d-md-table-cell">Seriya №</th>
                        <th class="fw-semibold">Analiz sayı</th>
                        <th class="fw-semibold d-none d-lg-table-cell">Ümumi Qiymət</th>
                        <th class="fw-semibold">Təsdiq</th>
                        <th class="fw-semibold d-none d-md-table-cell">Status</th>
                        <th class="fw-semibold d-none d-md-table-cell">Tarix</th>
                        <th class="fw-semibold text-center">Əməliyyatlar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($referrals as $referral)
                        <tr>
                            <td class="fw-bold text-primary">#{{ $referral->id }}</td>
                            <td>{{ $referral->patient->full_name }}</td>
                            <td class="d-none d-md-table-cell">
                                <span class="badge bg-secondary">{{ $referral->patient->serial_number ?? '-' }}</span>
                            </td>
                            <td>
                                @php
                                    $totalAnalyses = $referral->analyses->count();
                                    $cancelledCount = $referral->analyses->filter(function($a) {
                                        return $a->pivot->is_cancelled ?? false;
                                    })->count();
                                    $activeCount = $totalAnalyses - $cancelledCount;
                                @endphp
                                <span class="badge bg-info">{{ $activeCount }} analiz</span>
                                @if($cancelledCount > 0)
                                    <span class="badge bg-danger ms-1">{{ $cancelledCount }} iptal</span>
                                @endif
                            </td>
                            <td class="d-none d-lg-table-cell">
                                @if($canSeePrices && $referral->is_priced)
                                    <span class="fw-bold text-success">{{ number_format($referral->total_price, 2) }} AZN</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($referral->is_approved)
                                    @if($referral->is_priced)
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Təsdiqlənib</span>
                                    @else
                                        <span class="badge bg-info"><i class="bi bi-hourglass-split"></i> Qiymətləndirilməyib</span>
                                    @endif
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
                            <td class="d-none d-md-table-cell">
                                <small class="text-muted">{{ $referral->created_at->format('d.m.Y H:i') }}</small>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('doctor.referrals.show', $referral->id) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> Detallar
                                    </a>
                                    @if($referral->canBeEditedByDoctor())
                                        <a href="{{ route('doctor.referrals.edit', $referral->id) }}" class="btn btn-sm btn-warning" title="Redaktə et ({{ $referral->remaining_edit_time }} dəq qalıb)">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>

            <div class="mt-3">
                {{ $referrals->links() }}
            </div>
        @else
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                <p>Hələ göndəriş yaratmamısınız</p>
            </div>
        @endif
        </div>
    </div>
@endsection
