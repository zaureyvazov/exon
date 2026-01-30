<div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="bi bi-cash-coin"></i> Ümumi Maliyyə Hesabatı</h5>
    </div>
    <div class="card-body">
        <p class="text-muted mb-4">
            <i class="bi bi-calendar-range"></i>
            Tarix aralığı: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d.m.Y') }}</strong> -
            <strong>{{ \Carbon\Carbon::parse($endDate)->format('d.m.Y') }}</strong>
        </p>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Ümumi Gəlir</h6>
                        <h3 class="text-success">{{ number_format($reportData['total_revenue'], 2) }} AZN</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Həkim Komissiyaları</h6>
                        <h3 class="text-warning">{{ number_format($reportData['total_commissions'], 2) }} AZN</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Xalis Gəlir</h6>
                        <h3 class="text-primary">{{ number_format($reportData['net_profit'], 2) }} AZN</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-3">
            <div class="col-md-4">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Ödənilmiş Komissiya</h6>
                        <h3 class="text-info">{{ number_format($reportData['paid_commissions'], 2) }} AZN</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-danger">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Ödənilməmiş Komissiya</h6>
                        <h3 class="text-danger">{{ number_format($reportData['unpaid_commissions'], 2) }} AZN</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-secondary">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Toplam Göndəriş</h6>
                        <h3>{{ $reportData['total_referrals'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
