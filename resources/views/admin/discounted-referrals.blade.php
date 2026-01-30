@extends('layouts.app')

@section('title', 'Endirimli Göndərişlər')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Ana Səhifə</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.users') }}">İstifadəçilər</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.analyses') }}">Analizlər</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.balances') }}">Balanslar</a></li>
    <li class="nav-item"><a class="nav-link active" href="{{ route('admin.discounted.referrals') }}">Endirimli Göndərişlər</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.payments.index') }}">Ödənişlər</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.settings') }}">Ayarlar</a></li>
@endsection

@section('content')
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h1 class="h3 fw-bold mb-1">Endirimli Göndərişlər</h1>
                <div class="text-muted">Komissiya təyini gözləyən endirimli göndərişlər</div>
            </div>
            <span class="badge bg-warning fs-5">{{ $referrals->total() }} göndəriş</span>
        </div>
    </div>

    @if($referrals->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-semibold">ID</th>
                            <th class="fw-semibold">Həkim</th>
                            <th class="fw-semibold">Xəstə</th>
                            <th class="fw-semibold">Orijinal</th>
                            <th class="fw-semibold">Endirim</th>
                            <th class="fw-semibold">Final</th>
                            <th class="fw-semibold">Səbəb</th>
                            <th class="fw-semibold">Tarix</th>
                            <th class="fw-semibold text-center">Əməliyyat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($referrals as $referral)
                        <tr>
                            <td class="fw-bold text-primary">#{{ $referral->id }}</td>
                            <td>
                                <div class="fw-semibold">Dr. {{ $referral->doctor->name }} {{ $referral->doctor->surname }}</div>
                            </td>
                            <td>
                                <div>{{ $referral->patient->full_name }}</div>
                                <strong>{{ $referral->patient->name }} {{ $referral->patient->father_name }} {{ $referral->patient->surname }}</strong>
                                @if($referral->patient->serial_number)
                                    <br><small class="text-muted">{{ $referral->patient->serial_number }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold">{{ number_format($referral->total_price, 2) }} AZN</div>
                                <small class="text-muted">Vergi daxil: {{ number_format($referral->total_price_with_tax, 2) }} AZN</small>
                            </td>
                            <td>
                                @if($referral->discount_type === 'percentage')
                                    <span class="badge bg-warning text-dark">{{ $referral->discount_value }}%</span>
                                @elseif($referral->discount_type === 'amount')
                                    <span class="badge bg-warning text-dark">{{ number_format($referral->discount_value, 2) }} AZN</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-success">{{ number_format($referral->final_price, 2) }} AZN</div>
                                <small class="text-muted">Vergi daxil: {{ number_format($referral->final_price_with_tax, 2) }} AZN</small>
                            </td>
                            <td>
                                <small class="text-muted">{{ Str::limit($referral->discount_reason, 30) ?? '-' }}</small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $referral->approved_at->format('d.m.Y H:i') }}</small>
                            </td>
                            <td class="text-center">
                                <button type="button"
                                        class="btn btn-sm btn-success commission-btn"
                                        data-referral-id="{{ $referral->id }}"
                                        data-doctor-name="Dr. {{ $referral->doctor->name }} {{ $referral->doctor->surname }}"
                                        data-patient-name="{{ $referral->patient->full_name }}"
                                        data-final-price="{{ $referral->final_price }}"
                                        data-discount-type="{{ $referral->discount_type }}"
                                        data-discount-value="{{ $referral->discount_value }}"
                                        data-discount-reason="{{ $referral->discount_reason ?? '' }}"
                                        data-analyses='@json($referral->analyses->filter(fn($a) => !($a->pivot->is_cancelled ?? false))->map(fn($a) => ["name" => $a->name, "price" => $a->pivot->analysis_price]))'>
                                    <i class="bi bi-cash"></i> Komissiya Təyin Et
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $referrals->links() }}
    </div>
    @else
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
            <p class="text-muted">Komissiya təyini gözləyən endirimli göndəriş yoxdur</p>
        </div>
    </div>
    @endif

    <!-- Single Commission Modal -->
    <div class="modal fade" id="commissionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="commissionForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Komissiya Təyin Et</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info" id="modalInfo">
                            <!-- Dynamic content -->
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Həkim Komissiyası (AZN) *</label>
                            <input type="number"
                                   id="commissionInput"
                                   name="doctor_commission"
                                   class="form-control"
                                   step="0.01"
                                   min="0"
                                   required>
                            <small class="text-muted" id="maxCommissionText"></small>
                        </div>

                        <div id="discountReasonContainer" style="display: none;">
                            <div class="alert alert-warning">
                                <strong>Endirim Səbəbi:</strong><br>
                                <span id="discountReasonText"></span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="fw-semibold">Analizlər:</label>
                            <ul class="list-group" id="analysesList">
                                <!-- Dynamic content -->
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ləğv Et</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Təsdiq Et
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const commissionModal = new bootstrap.Modal(document.getElementById('commissionModal'));
    const commissionForm = document.getElementById('commissionForm');
    const modalTitle = document.getElementById('modalTitle');
    const modalInfo = document.getElementById('modalInfo');
    const commissionInput = document.getElementById('commissionInput');
    const maxCommissionText = document.getElementById('maxCommissionText');
    const discountReasonContainer = document.getElementById('discountReasonContainer');
    const discountReasonText = document.getElementById('discountReasonText');
    const analysesList = document.getElementById('analysesList');

    // Attach click event to all commission buttons
    document.querySelectorAll('.commission-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const referralId = this.dataset.referralId;
            const doctorName = this.dataset.doctorName;
            const patientName = this.dataset.patientName;
            const finalPrice = parseFloat(this.dataset.finalPrice);
            const discountType = this.dataset.discountType;
            const discountValue = this.dataset.discountValue;
            const discountReason = this.dataset.discountReason;
            const analyses = JSON.parse(this.dataset.analyses);

            // Set form action
            commissionForm.action = `/admin/discounted-referrals/${referralId}/set-commission`;

            // Update modal title
            modalTitle.textContent = `Komissiya Təyin Et - #${referralId}`;

            // Update info section
            let discountText = discountType === 'percentage'
                ? `${discountValue}%`
                : `${parseFloat(discountValue).toFixed(2)} AZN`;

            modalInfo.innerHTML = `
                <strong>Həkim:</strong> ${doctorName}<br>
                <strong>Xəstə:</strong> ${patientName}<br>
                <strong>Final Qiymət:</strong> ${finalPrice.toFixed(2)} AZN<br>
                <strong>Endirim:</strong> ${discountText}
            `;

            // Set max commission
            commissionInput.max = finalPrice;
            commissionInput.value = '';
            maxCommissionText.textContent = `Maksimum: ${finalPrice.toFixed(2)} AZN`;

            // Show/hide discount reason
            if (discountReason && discountReason.trim() !== '') {
                discountReasonText.textContent = discountReason;
                discountReasonContainer.style.display = 'block';
            } else {
                discountReasonContainer.style.display = 'none';
            }

            // Update analyses list
            analysesList.innerHTML = '';
            analyses.forEach(analysis => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between';
                li.innerHTML = `
                    <span>${analysis.name}</span>
                    <span class="text-muted">${parseFloat(analysis.price).toFixed(2)} AZN</span>
                `;
                analysesList.appendChild(li);
            });

            // Show modal
            commissionModal.show();
        });
    });
});
</script>
@endpush
