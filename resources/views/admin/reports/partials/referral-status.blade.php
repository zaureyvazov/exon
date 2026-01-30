<div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="bi bi-clipboard-check"></i> Göndəriş Statusu Raporu</h5>
    </div>
    <div class="card-body">
        <p class="text-muted mb-3">
            <i class="bi bi-calendar-range"></i>
            Tarix aralığı: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d.m.Y') }}</strong> -
            <strong>{{ \Carbon\Carbon::parse($endDate)->format('d.m.Y') }}</strong>
        </p>

        <div class="row g-3">
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Təsdiqlənmiş</h6>
                        <h3 class="text-success">{{ $reportData['approval_data']['approved'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Təsdiq Gözləyir</h6>
                        <h3 class="text-warning">{{ $reportData['approval_data']['pending_approval'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Qiymətləndirilmiş</h6>
                        <h3 class="text-info">{{ $reportData['approval_data']['priced'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-danger">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Qiymət Gözləyir</h6>
                        <h3 class="text-danger">{{ $reportData['approval_data']['pending_pricing'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <h5 class="mt-4">Status üzrə Bölgü</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th class="text-center">Say</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reportData['status_data'] as $status => $data)
                        <tr>
                            <td><strong>{{ ucfirst($status) }}</strong></td>
                            <td class="text-center">{{ $data->count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
