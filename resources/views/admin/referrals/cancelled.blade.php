@extends('layouts.app')

@section('title', 'İptal Edilmiş Göndərişlər')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Ana Səhifə</a></li>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown">
            Göndərişlər
        </a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('admin.referrals.non-discounted') }}">Endirimsiz</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.referrals.discounted') }}">Endirimli</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item active" href="{{ route('admin.referrals.cancelled') }}"><i class="bi bi-x-circle text-danger"></i> İptal Edilmişlər</a></li>
        </ul>
    </li>
@endsection

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-bold"><i class="bi bi-x-circle text-danger"></i> İptal Edilmiş Göndərişlər</h1>
        <p class="text-muted">Sistemdə iptal edilmiş bütün göndərişlər</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($referrals->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Həkim</th>
                            <th>Xəstə</th>
                            <th>Qiymət</th>
                            <th>İptal Səbəbi</th>
                            <th>İptal Edən</th>
                            <th>İptal Tarixi</th>
                            <th>Əməliyyat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($referrals as $referral)
                        <tr>
                            <td><span class="badge bg-danger">#{{ $referral->id }}</span></td>
                            <td>
                                <div class="fw-semibold">Dr. {{ $referral->doctor->name }}</div>
                                <small class="text-muted">{{ $referral->doctor->surname }}</small>
                            </td>
                            <td>
                                <div>{{ $referral->patient->full_name }} {{ $referral->patient->father_name }}</div>
                                @if($referral->patient->fin_code)
                                    <small class="text-muted">FIN: {{ $referral->patient->fin_code }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ number_format($referral->total_price, 2) }} ₼</div>
                                @if($referral->discount_type)
                                    <small class="text-success">
                                        @if($referral->discount_type === 'percentage')
                                            {{ $referral->discount_value }}% endirim
                                        @else
                                            {{ $referral->discount_value }} ₼ endirim
                                        @endif
                                    </small>
                                @endif
                            </td>
                            <td>
                                <div class="text-danger" style="max-width: 250px;">
                                    <i class="bi bi-exclamation-circle"></i> 
                                    {{ $referral->cancellation_reason }}
                                </div>
                            </td>
                            <td>
                                @if($referral->cancelledBy)
                                    <div>{{ $referral->cancelledBy->name }}</div>
                                    <small class="text-muted">{{ $referral->cancelledBy->surname }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $referral->cancelled_at->format('d.m.Y') }}</div>
                                <small class="text-muted">{{ $referral->cancelled_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.referrals.show', $referral->id) }}" 
                                       class="btn btn-outline-primary" 
                                       title="Ətraflı">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form method="POST" 
                                          action="{{ route('admin.referrals.uncancel', $referral->id) }}" 
                                          class="d-inline"
                                          onsubmit="return confirm('İptalı geri almaq istədiyinizdən əminsiniz?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success" title="İptalı Geri Al">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-transparent">
            {{ $referrals->links() }}
        </div>
    </div>
    @else
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox display-1 text-muted"></i>
            <p class="text-muted mt-3">İptal edilmiş göndəriş yoxdur</p>
        </div>
    </div>
    @endif
@endsection
