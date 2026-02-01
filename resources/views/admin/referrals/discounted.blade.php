@extends('layouts.app')

@section('title', 'Endirimli Göndərişlər')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Ana Səhifə</a></li>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown">
            Göndərişlər
        </a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('admin.referrals.non-discounted') }}">Endirimsiz</a></li>
            <li><a class="dropdown-item active" href="{{ route('admin.referrals.discounted') }}">Endirimli</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="{{ route('admin.referrals.cancelled') }}"><i class="bi bi-x-circle text-danger"></i> İptal Edilmişlər</a></li>
        </ul>
    </li>
@endsection

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-bold">Endirimli Göndərişlər</h1>
        <p class="text-muted">Endirim tətbiq edilmiş bütün göndərişlər</p>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.referrals.discounted') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Başlanğıc Tarix</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Bitmə Tarix</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Həkim</label>
                        <select name="doctor_id" class="form-select">
                            <option value="">Hamısı</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                    Dr. {{ $doctor->name }} {{ $doctor->surname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Xəstə Axtarışı</label>
                        <input type="text" name="search" class="form-control" placeholder="Ad, FIN..." value="{{ request('search') }}">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-filter"></i> Filtrele</button>
                        <a href="{{ route('admin.referrals.discounted') }}" class="btn btn-outline-secondary">Təmizlə</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

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
                            <th>Analizlər</th>
                            <th>Qiymət</th>
                            <th>Endirim</th>
                            <th>Komissiya</th>
                            <th>Status</th>
                            <th>Tarix</th>
                            <th>Əməliyyat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($referrals as $referral)
                        <tr class="table-warning">
                            <td><span class="badge bg-secondary">#{{ $referral->id }}</span></td>
                            <td>
                                <div class="fw-semibold">Dr. {{ $referral->doctor->name }}</div>
                                <small class="text-muted">{{ $referral->doctor->surname }}</small>
                            </td>
                            <td>
                                <div>{{ $referral->patient->full_name }} {{ $referral->patient->father_name }}</div>

                                @if($referral->patient->serial_number)
                                    <br><small class="text-muted">{{ $referral->patient->serial_number }}</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $totalAnalyses = $referral->analyses->count();
                                    $cancelledCount = $referral->analyses->filter(fn($a) => $a->pivot->is_cancelled ?? false)->count();
                                    $activeCount = $totalAnalyses - $cancelledCount;
                                @endphp
                                <span class="badge bg-info">{{ $activeCount }} aktiv</span>
                                @if($cancelledCount > 0)
                                    <span class="badge bg-danger">{{ $cancelledCount }} ləğv</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-success">{{ number_format($referral->final_price, 2) }} AZN</div>
                                <small class="text-muted"><s>{{ number_format($referral->total_price, 2) }} AZN</s></small>
                                <div><small class="text-muted">Vergi daxil: {{ number_format($referral->final_price_with_tax, 2) }} AZN</small></div>
                            </td>
                            <td>
                                @if($referral->discount_type === 'percentage')
                                    <span class="badge bg-warning text-dark">{{ $referral->discount_value }}%</span>
                                @elseif($referral->discount_type === 'amount')
                                    <span class="badge bg-warning text-dark">{{ number_format($referral->discount_value, 2) }} AZN</span>
                                @endif
                            </td>
                            <td>
                                @if($referral->is_priced && $referral->doctor_commission)
                                    <span class="fw-bold text-success">{{ number_format($referral->doctor_commission, 2) }} AZN</span>
                                @else
                                    <span class="badge bg-danger">Təyin olunmayıb</span>
                                @endif
                            </td>
                            <td>
                                @if($referral->is_approved)
                                    <span class="badge bg-success">Təsdiqlənib</span>
                                @else
                                    <span class="badge bg-warning">Gözləyir</span>
                                @endif
                            </td>
                            <td><small>{{ $referral->created_at->format('d.m.Y H:i') }}</small></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.referrals.show', $referral->id) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-eye"></i> Bax
                                    </a>
                                    @if(!$referral->is_priced)
                                    <button type="button" class="btn btn-success"
                                            data-bs-toggle="modal"
                                            data-bs-target="#commissionModal{{ $referral->id }}">
                                        <i class="bi bi-cash"></i>
                                    </button>
                                    @endif
                                    @if(!$referral->is_approved)
                                    <button type="button" class="btn btn-outline-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#cancelModal{{ $referral->id }}">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cancel Modals -->
    @foreach($referrals as $referral)
    @if(!$referral->is_approved)
    <div class="modal fade" id="cancelModal{{ $referral->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.referrals.cancel', $referral->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="bi bi-x-circle"></i> Göndərişi İptal Et</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <strong>Diqqət!</strong> Bu göndəriş iptal ediləcək və yalnız admin panelində görünəcək.
                        </div>

                        <div class="mb-3">
                            <strong>Göndəriş #{{ $referral->id }}</strong><br>
                            Xəstə: {{ $referral->patient->full_name }}<br>
                            Həkim: Dr. {{ $referral->doctor->name }} {{ $referral->doctor->surname }}<br>
                            Qiymət: {{ number_format($referral->total_price, 2) }} AZN
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">İptal Səbəbi</label>
                            <textarea name="cancellation_reason" class="form-control" rows="4" placeholder="İptal səbəbini daxil edin (istəyə görə)..."></textarea>
                            <small class="text-muted">Maksimum 500 simvol (istəyə görə)</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-circle"></i> İptal Et
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    @endforeach

    <!-- Commission Modals -->
    @foreach($referrals as $referral)
    @if(!$referral->is_priced)
    <div class="modal fade" id="commissionModal{{ $referral->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.discounted.setCommission', $referral->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-cash"></i> Komissiya Təyin Et</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>Göndəriş #{{ $referral->id }}</strong><br>
                            Xəstə: {{ $referral->patient->full_name }}<br>
                            Həkim: Dr. {{ $referral->doctor->name }} {{ $referral->doctor->surname }}
                        </div>

                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <small class="text-muted">Orijinal Qiymət:</small>
                                        <div class="fw-bold">{{ number_format($referral->total_price, 2) }} AZN</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Final Qiymət:</small>
                                        <div class="fw-bold text-success">{{ number_format($referral->final_price, 2) }} AZN</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Həkim Komissiyası (AZN) <span class="text-danger">*</span></label>
                            <input type="number" name="doctor_commission" class="form-control" step="0.01" min="0"
                                   max="{{ $referral->final_price }}" required placeholder="Məsələn: 10.50">
                            <small class="text-muted">Maksimum: {{ number_format($referral->final_price, 2) }} AZN</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ləğv Et</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Təyin Et
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    @endforeach

    <div class="mt-3">
        {{ $referrals->links() }}
    </div>
    @else
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
            <p class="text-muted">Endirimli göndəriş tapılmadı</p>
        </div>
    </div>
    @endif
@endsection
