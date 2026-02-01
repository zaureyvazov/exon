@extends('layouts.app')

@section('title', 'Göndəriş Detalları')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Ana Səhifə</a></li>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            Göndərişlər
        </a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('admin.referrals.non-discounted') }}">Endirimsiz</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.referrals.discounted') }}">Endirimli</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="{{ route('admin.referrals.cancelled') }}"><i class="bi bi-x-circle text-danger"></i> İptal Edilmişlər</a></li>
        </ul>
    </li>
@endsection

@section('content')
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

    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h1 class="h3 fw-bold mb-1">Göndəriş #{{ $referral->id }}
                    @if($referral->is_cancelled)
                        <span class="badge bg-danger"><i class="bi bi-x-circle"></i> İptal Edilib</span>
                    @endif
                </h1>
                <div class="text-muted">Göndəriş detalları və məlumatları</div>
            </div>
            <div class="d-flex gap-2">
                @if($referral->is_cancelled)
                    <form method="POST" action="{{ route('admin.referrals.uncancel', $referral->id) }}" class="d-inline" onsubmit="return confirm('İptalı geri almaq istədiyinizdən əminsiniz?')">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-arrow-counterclockwise"></i> İptalı Geri Al
                        </button>
                    </form>
                @else
                    @if(!$referral->is_approved)
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            <i class="bi bi-x-circle"></i> İptal Et
                        </button>
                    @endif
                @endif
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Geri
                </a>
            </div>
        </div>
    </div>

    @if($referral->is_cancelled)
        <div class="alert alert-danger" role="alert">
            <h6 class="alert-heading mb-2"><i class="bi bi-exclamation-triangle"></i> İptal Səbəbi</h6>
            <p class="mb-2">{{ $referral->cancellation_reason }}</p>
            <hr>
            <div class="small">
                <strong>İptal edən:</strong> {{ $referral->cancelledBy->name }} {{ $referral->cancelledBy->surname }}<br>
                <strong>Tarix:</strong> {{ $referral->cancelled_at->format('d.m.Y H:i') }}
            </div>
        </div>
    @endif

    <div class="row g-3">
        <div class="col-12 col-xl-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0"><i class="bi bi-person"></i> Xəstə Məlumatları</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Ad Soyad</span>
                            <span class="fw-semibold">{{ $referral->patient->full_name }} {{ $referral->patient->father_name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Kimlik No</span>

                            @if($referral->patient->serial_number)
                                <span class="badge bg-secondary ms-2">{{ $referral->patient->serial_number }}</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Telefon</span>
                            <span class="fw-semibold">{{ $referral->patient->phone }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Göndərən Həkim</span>
                            <span class="fw-semibold">Dr. {{ $referral->doctor->name }} {{ $referral->doctor->surname }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Status</span>
                            <span>
                                @if($referral->status == 'pending')
                                    <span class="badge bg-warning text-dark">Gözləyir</span>
                                @elseif($referral->status == 'completed')
                                    <span class="badge bg-success">Tamamlandı</span>
                                @else
                                    <span class="badge bg-danger">Ləğv edildi</span>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Tarix</span>
                            <span class="fw-semibold">{{ $referral->created_at->format('d.m.Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Təsdiq</span>
                            <span>
                                @if($referral->is_approved)
                                    <span class="badge bg-success">Təsdiqlənib</span>
                                @else
                                    <span class="badge bg-warning">Gözləyir</span>
                                @endif
                            </span>
                        </li>
                        @if($referral->is_approved && $referral->approvedBy)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Təsdiq edən</span>
                            <span class="fw-semibold">{{ $referral->approvedBy->name }}</span>
                        </li>
                        @endif
                    </ul>

                    @if($referral->notes)
                        <div class="alert alert-warning mt-3 mb-0" role="alert">
                            <div class="fw-semibold mb-1"><i class="bi bi-sticky"></i> Həkim Qeydləri</div>
                            <div class="small">{{ $referral->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0"><i class="bi bi-clipboard-pulse"></i> Analizlər</h5>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                        {{ $referral->analyses->count() }} analiz
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Analiz</th>
                                    <th class="text-end">Qiymət</th>
                                    <th class="text-center">Komissiya %</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($referral->analyses as $analysis)
                                    <tr class="{{ $analysis->pivot->is_cancelled ? 'table-danger' : '' }}">
                                        <td>
                                            <div class="fw-semibold">{{ $analysis->name }}</div>
                                            @if($analysis->pivot->is_cancelled && $analysis->pivot->cancellation_reason)
                                                <div class="small text-danger mt-1">
                                                    <i class="bi bi-x-circle"></i> {{ $analysis->pivot->cancellation_reason }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($analysis->pivot->is_cancelled)
                                                <s class="text-muted">{{ number_format($analysis->pivot->analysis_price, 2) }} AZN</s>
                                            @else
                                                <div>{{ number_format($analysis->pivot->analysis_price, 2) }} AZN</div>
                                                <small class="text-muted">Vergi daxil: {{ number_format($analysis->pivot->analysis_price * 1.3, 2) }} AZN</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if(!$analysis->pivot->is_cancelled)
                                                <span class="badge bg-info">{{ $analysis->pivot->commission_percentage }}%</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($analysis->pivot->is_cancelled)
                                                <span class="badge bg-danger">Ləğv</span>
                                            @else
                                                <span class="badge bg-success">Aktiv</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="2" class="fw-bold text-end">Ümumi:</td>
                                    <td colspan="2">
                                        <div class="fw-bold">{{ number_format($referral->total_price, 2) }} AZN</div>
                                        <small class="text-muted">Vergi daxil: {{ number_format($referral->total_price_with_tax, 2) }} AZN</small>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Discount and Commission Info -->
            @if($referral->is_approved)
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header {{ $referral->discount_type !== 'none' ? 'bg-warning bg-opacity-10' : 'bg-transparent' }}">
                    <h5 class="mb-0"><i class="bi bi-calculator"></i> Maliyyə Məlumatları</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @if($referral->discount_type !== 'none')
                        <div class="col-md-4">
                            <small class="text-muted d-block">Orijinal Qiymət</small>
                            <div>{{ number_format($referral->total_price, 2) }} AZN</div>
                            <small class="text-muted">Vergi daxil: {{ number_format($referral->total_price_with_tax, 2) }} AZN</small>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Endirim</small>
                            @if($referral->discount_type === 'percentage')
                                <strong class="text-warning">{{ $referral->discount_value }}%</strong>
                            @elseif($referral->discount_type === 'amount')
                                <strong class="text-warning">{{ number_format($referral->discount_value, 2) }} AZN</strong>
                            @endif
                            @if($referral->discount_reason)
                                <div><small class="text-muted">{{ $referral->discount_reason }}</small></div>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Final Qiymət</small>
                            <div class="fw-bold text-success">{{ number_format($referral->final_price, 2) }} AZN</div>
                            <small class="text-muted">Vergi daxil: {{ number_format($referral->final_price_with_tax, 2) }} AZN</small>
                        </div>
                        @endif
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block">Həkim Komissiyası</small>
                                    @if($referral->is_priced && $referral->doctor_commission)
                                        <strong class="text-primary fs-5">{{ number_format($referral->doctor_commission, 2) }} AZN</strong>
                                    @else
                                        <span class="badge bg-warning">Təyin olunmayıb</span>
                                    @endif
                                </div>
                                @if($referral->discount_type !== 'none' && !$referral->is_priced)
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#commissionModal">
                                        <i class="bi bi-cash"></i> Komissiya Təyin Et
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Commission Modal -->
    @if($referral->discount_type !== 'none' && !$referral->is_priced)
    <div class="modal fade" id="commissionModal" tabindex="-1">
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

                        <div class="mb-3">
                            <h6 class="fw-semibold mb-2">Analizlər:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Analiz</th>
                                            <th class="text-end">Qiymət</th>
                                            <th class="text-center">%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($referral->analyses->filter(fn($a) => !$a->pivot->is_cancelled) as $analysis)
                                        <tr>
                                            <td><small>{{ $analysis->name }}</small></td>
                                            <td class="text-end"><small>{{ number_format($analysis->pivot->analysis_price, 2) }} AZN</small></td>
                                            <td class="text-center"><small>{{ $analysis->pivot->commission_percentage }}%</small></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
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

    <!-- Cancel Modal -->
    @if(!$referral->is_cancelled && !$referral->is_approved)
    <div class="modal fade" id="cancelModal" tabindex="-1">
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
                            <strong>Diqqət!</strong> Bu əməliyyat göndərişi sistemdən tamamilə gizlədəcək. Yalnız admin panelində görünəcək.
                        </div>

                        <div class="mb-3">
                            <strong>Göndəriş #{{ $referral->id }}</strong><br>
                            Xəstə: {{ $referral->patient->full_name }}<br>
                            Həkim: Dr. {{ $referral->doctor->name }} {{ $referral->doctor->surname }}
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
@endsection
