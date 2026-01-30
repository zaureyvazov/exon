<div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="bi bi-tags"></i> Analiz Növü üzrə Qruplaşdırma</h5>
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
                            <th>Analiz Növü</th>
                            <th class="text-center">İstifadə Sayı</th>
                            <th class="text-end">Toplam Gəlir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $data)
                            <tr>
                                <td><strong>{{ $data->name }}</strong></td>
                                <td class="text-center">{{ $data->usage_count }}</td>
                                <td class="text-end">{{ number_format($data->total_revenue, 2) }} AZN</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>CƏMI</th>
                            <th class="text-center">{{ $reportData->sum('usage_count') }}</th>
                            <th class="text-end">{{ number_format($reportData->sum('total_revenue'), 2) }} AZN</th>
                        </tr>
                    </tfoot>
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
