@extends('layouts.app')

@section('title', 'Göndəriş Detalları')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('registrar.dashboard') }}">Ana Səhifə</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('registrar.referrals') }}">Göndərişlər</a></li>
@endsection

@section('content')
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h1 class="h3 fw-bold mb-1">Göndəriş #{{ $referral->id }}</h1>
                <div class="text-muted">Göndəriş detalları və məlumatları</div>
            </div>
            <a href="{{ route('registrar.referrals') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Geri
            </a>
        </div>
    </div>

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
                            <span class="text-muted">Göndərən Doktor</span>
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
                    </ul>

                    @if($referral->notes)
                        <div class="alert alert-warning mt-3 mb-0" role="alert">
                            <div class="fw-semibold mb-1"><i class="bi bi-sticky"></i> Doktor Qeydləri</div>
                            <div class="small">{{ $referral->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0"><i class="bi bi-clipboard-pulse"></i> Seçilmiş Analizlər</h5>
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
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($referral->analyses as $analysis)
                                    <tr class="{{ $analysis->pivot->is_cancelled ? 'table-danger' : '' }}">
                                        <td>
                                            <div class="fw-semibold">{{ $analysis->name }}</div>
                                            @if($analysis->description)
                                                <div class="small text-muted">{{ $analysis->description }}</div>
                                            @endif
                                            @if($analysis->pivot->is_cancelled && $analysis->pivot->cancellation_reason)
                                                <div class="small text-danger mt-1">
                                                    <i class="bi bi-x-circle"></i> İptal səbəbi: {{ $analysis->pivot->cancellation_reason }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold">
                                            @if($analysis->pivot->is_cancelled)
                                                <s class="text-muted">{{ number_format($analysis->price_with_tax, 2) }} AZN</s>
                                            @else
                                                {{ number_format($analysis->price_with_tax, 2) }} AZN
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($analysis->pivot->is_cancelled)
                                                <span class="badge bg-danger">İptal edilib</span>
                                            @else
                                                <span class="badge bg-success">Aktiv</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <td colspan="2" class="fw-bold">Ümumi məbləğ</td>
                                    <td class="text-end fw-bold">
                                        @if($referral->discount_type !== 'none' && $referral->final_price)
                                            <div><strong class="text-success">{{ number_format($referral->final_price_with_tax, 2) }} AZN</strong></div>
                                            <small class="text-muted"><s>{{ number_format($referral->total_price_with_tax, 2) }} AZN</s></small>
                                        @else
                                            {{ number_format($referral->total_price_with_tax, 2) }} AZN
                                        @endif
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                        <div>
                            <div class="fw-semibold mb-1"><i class="bi bi-shield-check"></i> Təsdiq</div>
                            @if(!$referral->is_approved)
                                <div class="text-muted small">Bu göndəriş hələ təsdiqlənməyib</div>
                            @else
                                <div class="text-muted small">
                                    Təsdiq edən: {{ $referral->approvedBy->name }} {{ $referral->approvedBy->surname }}<br>
                                    Tarix: {{ $referral->approved_at->format('d.m.Y H:i') }}
                                </div>
                            @endif
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            @if(!$referral->is_approved)
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                    <i class="bi bi-check-circle"></i> Təsdiqlə
                                </button>
                            @else
                                <form action="{{ route('registrar.referrals.reject', $referral->id) }}" method="POST" class="d-inline rejection-form">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger rejection-btn">
                                        <i class="bi bi-x-circle"></i> Təsdiqi Ləğv Et
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Discount Info (Qeydiyyatçı üçün komissiya gizli) -->
                    @if($referral->is_approved && $referral->is_priced)
                    <div class="mt-4 p-3 {{ $referral->discount_type !== 'none' ? 'bg-warning bg-opacity-10' : 'bg-light' }} rounded">
                        <h6 class="fw-bold mb-3"><i class="bi bi-calculator"></i> Qiymət Məlumatı</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <small class="text-muted d-block">
                                    @if($referral->discount_type !== 'none')
                                        Endirim Olunmuş Qiymət
                                    @else
                                        Qiymət
                                    @endif
                                </small>
                                @if($referral->discount_type !== 'none')
                                    <strong class="text-success d-block">{{ number_format($referral->final_price_with_tax, 2) }} AZN</strong>
                                    <small class="text-muted"><s>{{ number_format($referral->total_price_with_tax, 2) }} AZN</s></small>
                                @else
                                    <strong class="text-dark">{{ number_format($referral->total_price_with_tax, 2) }} AZN</strong>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Endirim</small>
                                @if($referral->discount_type === 'percentage')
                                    <strong class="text-warning">{{ $referral->discount_value }}%</strong>
                                @elseif($referral->discount_type === 'amount')
                                    <strong class="text-warning">{{ number_format($referral->discount_value, 2) }} AZN</strong>
                                @else
                                    <strong class="text-muted">Yoxdur</strong>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Endirim Məbləği</small>
                                @if($referral->discount_type !== 'none')
                                    <strong class="text-warning">
                                        {{ number_format(($referral->total_price_with_tax - $referral->final_price_with_tax), 2) }} AZN
                                    </strong>
                                @else
                                    <strong class="text-muted">-</strong>
                                @endif
                            </div>
                        </div>
                        @if($referral->discount_reason)
                        <div class="mt-2">
                            <small class="text-muted">Endirim səbəbi:</small>
                            <p class="mb-0 small">{{ $referral->discount_reason }}</p>
                        </div>
                        @endif
                    </div>
                    @endif

                    <div class="mt-4">
                        <form action="{{ route('registrar.referrals.status', $referral->id) }}" method="POST" class="row g-2 align-items-end status-form">
                            @csrf
                            <div class="col-12 col-md-7">
                                <label class="form-label fw-semibold" for="status">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="pending" {{ $referral->status == 'pending' ? 'selected' : '' }}>Gözləyir</option>
                                    <option value="completed" {{ $referral->status == 'completed' ? 'selected' : '' }}>Tamamlandı</option>
                                    <option value="cancelled" {{ $referral->status == 'cancelled' ? 'selected' : '' }}>Ləğv edildi</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-5">
                                <button type="submit" class="btn btn-primary w-100 status-btn">
                                    <i class="bi bi-arrow-repeat"></i> Statusu Yenilə
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Modal with Discount Options -->
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('registrar.referrals.approve', $referral->id) }}" method="POST" id="approveForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-check-circle"></i> Göndərişi Təsdiqlə</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Analysis Selection with Cancellation -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-clipboard-pulse"></i> Analizlər</h6>
                            @foreach($referral->analyses as $analysis)
                            <div class="card mb-2">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="form-check">
                                                <input class="form-check-input analysis-checkbox" type="checkbox"
                                                       name="cancelled_analyses[]" value="{{ $analysis->id }}"
                                                       id="cancel_{{ $analysis->id }}"
                                                       {{ $analysis->pivot->is_cancelled ? 'checked' : '' }}>
                                                <label class="form-check-label fw-semibold" for="cancel_{{ $analysis->id }}">
                                                    <span class="text-danger">İptal et</span> - {{ $analysis->name }}
                                                </label>
                                            </div>
                                            <div class="small text-muted ms-4">{{ number_format($analysis->price_with_tax, 2) }} AZN</div>
                                            <div class="ms-4 mt-2 cancellation-reason-div" id="reason_div_{{ $analysis->id }}" style="display: {{ $analysis->pivot->is_cancelled ? 'block' : 'none' }};">
                                                <input type="text"
                                                       name="cancellation_reasons[{{ $analysis->id }}]"
                                                       class="form-control form-control-sm"
                                                       placeholder="İptal səbəbi (məs: Reaktiv yoxdur, Cihaz işləmir...)"
                                                       value="{{ $analysis->pivot->cancellation_reason ?? '' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Endirim Növü</label>
                            <select name="discount_type" id="discountType" class="form-select" required>
                                <option value="none">Endirim yoxdur (Adi xəstə)</option>
                                <option value="percentage">Faiz ilə endirim (%)</option>
                                <option value="amount">Məbləğ ilə endirim (AZN)</option>
                            </select>
                        </div>

                        <div class="mb-3" id="discountValueDiv" style="display: none;">
                            <label class="form-label fw-semibold" id="discountValueLabel">Endirim Dəyəri</label>
                            <input type="number" name="discount_value" id="discountValue" class="form-control" min="0" step="0.01" value="0">
                            <small class="text-muted">Adi xəstə üçün 0 qalır</small>
                        </div>

                        <div class="alert alert-info" id="discountInfo" style="display: none;">
                            <small><i class="bi bi-info-circle"></i> <span id="discountInfoText"></span></small>
                        </div>

                        <div class="alert alert-warning" id="warning100" style="display: none;">
                            <small><i class="bi bi-exclamation-triangle"></i> <strong>Diqqət:</strong> 100% endirim - Həkim komissiyası ödənilməyəcək!</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Endirim Səbəbi <small class="text-muted">(İstəyə bağlı)</small></label>
                            <textarea name="discount_reason" class="form-control" rows="2" placeholder="Məsələn: Şəhid ailəsi, Veteran, Daimi xəstə..."></textarea>
                        </div>

                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title mb-2"><i class="bi bi-calculator"></i> Hesablama</h6>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Aktiv Qiymət:</span>
                                    <strong id="activePriceText">{{ number_format($referral->total_price_with_tax, 2) }} AZN</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1" id="discountAmountRow" style="display: none;">
                                    <span>Endirim:</span>
                                    <strong class="text-warning" id="discountAmountText">0 AZN</strong>
                                </div>
                                <div class="d-flex justify-content-between border-top pt-2">
                                    <span class="fw-bold">Final Qiymət:</span>
                                    <strong class="text-success" id="finalPriceText">{{ number_format($referral->total_price_with_tax, 2) }} AZN</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ləğv Et</button>
                        <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Təsdiqlə</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Store analysis prices (with tax)
    const analysisPrices = {
        @foreach($referral->analyses as $analysis)
        '{{ $analysis->id }}': {{ $analysis->price_with_tax }},
        @endforeach
    };

    const discountType = document.getElementById('discountType');
    const discountValue = document.getElementById('discountValue');
    const discountValueDiv = document.getElementById('discountValueDiv');
    const discountValueLabel = document.getElementById('discountValueLabel');
    const discountInfo = document.getElementById('discountInfo');
    const discountInfoText = document.getElementById('discountInfoText');
    const warning100 = document.getElementById('warning100');
    const discountAmountRow = document.getElementById('discountAmountRow');
    const discountAmountText = document.getElementById('discountAmountText');
    const finalPriceText = document.getElementById('finalPriceText');

    // Calculate active price (non-cancelled analyses)
    function getActivePrice() {
        let activePrice = 0;
        document.querySelectorAll('.analysis-checkbox').forEach(checkbox => {
            const analysisId = checkbox.value;
            const isCancelled = checkbox.checked;
            if (!isCancelled && analysisPrices[analysisId]) {
                activePrice += analysisPrices[analysisId];
            }
        });
        return activePrice;
    }

    // Toggle cancellation reason input
    document.querySelectorAll('.analysis-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const analysisId = this.value;
            const reasonDiv = document.getElementById('reason_div_' + analysisId);
            if (this.checked) {
                reasonDiv.style.display = 'block';
            } else {
                reasonDiv.style.display = 'none';
            }
            // Recalculate when checkbox changes
            updateCalculation();
        });
    });

    discountType.addEventListener('change', function() {
        if (this.value === 'none') {
            discountValueDiv.style.display = 'none';
            discountValue.value = 0;
            discountValue.removeAttribute('required');
            discountInfo.style.display = 'none';
            warning100.style.display = 'none';
            updateCalculation();
        } else {
            discountValueDiv.style.display = 'block';
            discountValue.setAttribute('required', 'required');
            discountInfo.style.display = 'block';

            if (this.value === 'percentage') {
                discountValueLabel.textContent = 'Endirim Faizi (%)';
                discountValue.setAttribute('max', '100');
                discountInfoText.textContent = 'Faiz daxil edin (məs: 50 = 50% endirim)';
            } else {
                discountValueLabel.textContent = 'Endirim Məbləği (AZN)';
                const activePrice = getActivePrice();
                const roundedPrice = Math.round(activePrice * 100) / 100; // Yuvarlaqlaşdır
                discountValue.setAttribute('max', roundedPrice);
                discountInfoText.textContent = 'Məbləğ daxil edin (məs: 10 = 10 AZN endirim)';
            }
        }
    });

    discountValue.addEventListener('input', updateCalculation);

    function updateCalculation() {
        const activePrice = getActivePrice();
        const type = discountType.value;
        const value = parseFloat(discountValue.value) || 0;
        let discountAmount = 0;
        let finalPrice = activePrice;

        // Update active price display
        const activePriceText = document.getElementById('activePriceText');
        if (activePriceText) {
            activePriceText.textContent = activePrice.toFixed(2) + ' AZN';
        }

        if (type === 'percentage') {
            discountAmount = (activePrice * value) / 100;
            finalPrice = activePrice - discountAmount;

            if (value == 100) {
                warning100.style.display = 'block';
            } else {
                warning100.style.display = 'none';
            }
        } else if (type === 'amount') {
            discountAmount = value;
            finalPrice = Math.max(0, activePrice - value);
        }

        if (type !== 'none' && value > 0) {
            discountAmountRow.style.display = 'flex';
            discountAmountText.textContent = discountAmount.toFixed(2) + ' AZN';
        } else {
            discountAmountRow.style.display = 'none';
        }

        finalPriceText.textContent = finalPrice.toFixed(2) + ' AZN';

        // Update max value for amount discount
        if (type === 'amount') {
            const roundedPrice = Math.round(activePrice * 100) / 100; // Yuvarlaqlaşdır
            discountValue.setAttribute('max', roundedPrice);
        }
    }

    // Initial calculation
    updateCalculation();

    // Double submit prevention for all forms
    let isApproving = false;
    let isRejecting = false;
    let isUpdatingStatus = false;

    // Approval form
    const approvalForm = document.querySelector('.approval-form');
    const approvalBtn = document.querySelector('.approval-btn');
    if (approvalForm && approvalBtn) {
        approvalForm.addEventListener('submit', function(e) {
            if (isApproving) {
                e.preventDefault();
                return false;
            }
            isApproving = true;
            approvalBtn.disabled = true;
            approvalBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Təsdiq edilir...';
        });
    }

    // Rejection form
    const rejectionForm = document.querySelector('.rejection-form');
    const rejectionBtn = document.querySelector('.rejection-btn');
    if (rejectionForm && rejectionBtn) {
        rejectionForm.addEventListener('submit', function(e) {
            if (isRejecting) {
                e.preventDefault();
                return false;
            }
            isRejecting = true;
            rejectionBtn.disabled = true;
            rejectionBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Ləğv edilir...';
        });
    }

    // Status update form
    const statusForm = document.querySelector('.status-form');
    const statusBtn = document.querySelector('.status-btn');
    if (statusForm && statusBtn) {
        statusForm.addEventListener('submit', function(e) {
            if (isUpdatingStatus) {
                e.preventDefault();
                return false;
            }
            isUpdatingStatus = true;
            statusBtn.disabled = true;
            statusBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Yenilənir...';
        });
    }

    // Reset on back button
    window.addEventListener('pageshow', function(event) {
        if (event.persisted || performance.getEntriesByType("navigation")[0]?.type === 'back_forward') {
            isApproving = false;
            isRejecting = false;
            isUpdatingStatus = false;

            if (approvalBtn) {
                approvalBtn.disabled = false;
                approvalBtn.innerHTML = '<i class="bi bi-check-circle"></i> Təsdiqlə';
            }
            if (rejectionBtn) {
                rejectionBtn.disabled = false;
                rejectionBtn.innerHTML = '<i class="bi bi-x-circle"></i> Təsdiqi Ləğv Et';
            }
            if (statusBtn) {
                statusBtn.disabled = false;
                statusBtn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Statusu Yenilə';
            }
        }
    });
</script>
@endsection
