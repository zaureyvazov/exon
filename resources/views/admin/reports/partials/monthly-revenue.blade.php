<div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="bi bi-calendar-month"></i> Aylıq Gəlir Raporu</h5>
    </div>
    <div class="card-body">
        <p class="text-muted mb-3">
            <i class="bi bi-calendar-range"></i>
            Tarix aralığı: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d.m.Y') }}</strong> -
            <strong>{{ \Carbon\Carbon::parse($endDate)->format('d.m.Y') }}</strong>
        </p>

        @if(count($reportData) > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Ay</th>
                            <th class="text-end">Gəlir</th>
                            <th class="text-end">Komissiya</th>
                            <th class="text-end">Xalis Gəlir</th>
                            <th class="text-center">Göndəriş Sayı</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $month => $data)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</td>
                                <td class="text-end">{{ number_format($data['revenue'], 2) }} AZN</td>
                                <td class="text-end">{{ number_format($data['commissions'], 2) }} AZN</td>
                                <td class="text-end"><strong>{{ number_format($data['net_profit'], 2) }} AZN</strong></td>
                                <td class="text-center">{{ $data['referral_count'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                Seçilən tarix aralığında məlumat yoxdur.
            </div>
        @endif
    </div>
</div>
