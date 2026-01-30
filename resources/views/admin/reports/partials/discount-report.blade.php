<div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="bi bi-percent"></i> Endirim Statistikası</h5>
    </div>
    <div class="card-body">
        <p class="text-muted mb-3">
            <i class="bi bi-calendar-range"></i>
            Tarix aralığı: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d.m.Y') }}</strong> -
            <strong>{{ \Carbon\Carbon::parse($endDate)->format('d.m.Y') }}</strong>
        </p>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="alert alert-info">
                    <strong>Endirimli Göndəriş:</strong> {{ $reportData['total_count'] }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="alert alert-warning">
                    <strong>Toplam Endirim Məbləği:</strong> {{ number_format($reportData['total_discount_amount'], 2) }} AZN
                </div>
            </div>
        </div>

        @if(count($reportData['discounted_referrals']) > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Xəstə</th>
                            <th>Həkim</th>
                            <th>Tarix</th>
                            <th>Analizlər</th>
                            <th class="text-end">Son Qiymət</th>
                            <th class="text-end">Admin Təyin Etdiyi Komissiya</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData['discounted_referrals'] as $referral)
                            <tr>
                                <td>{{ $referral->patient->name }} {{ $referral->patient->surname }}</td>
                                <td>{{ $referral->doctor->name }}</td>
                                <td>{{ $referral->created_at->format('d.m.Y') }}</td>
                                <td>{{ $referral->analyses->count() }} analiz</td>
                                <td class="text-end">
                                    <div>{{ number_format($referral->final_price, 2) }} AZN</div>
                                    <small class="text-muted">Vergi daxil: {{ number_format($referral->final_price_with_tax, 2) }} AZN</small>
                                </td>
                                <td class="text-end">{{ number_format($referral->doctor_commission, 2) }} AZN</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                Seçilən tarix aralığında endirimli göndəriş yoxdur.
            </div>
        @endif
    </div>
</div>
