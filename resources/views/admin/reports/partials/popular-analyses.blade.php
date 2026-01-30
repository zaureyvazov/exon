<div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="bi bi-clipboard-data"></i> Ən Populyar Analizlər</h5>
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
                            <th>Sıra</th>
                            <th>Analiz Adı</th>
                            <th class="text-center">İstifadə Sayı</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $data)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $data->name }}</strong></td>
                                <td class="text-center">{{ $data->usage_count }}</td>
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
