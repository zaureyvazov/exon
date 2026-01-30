<div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="bi bi-people"></i> Xəstə Statistikası</h5>
    </div>
    <div class="card-body">
        <p class="text-muted mb-3">
            <i class="bi bi-calendar-range"></i>
            Tarix aralığı: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d.m.Y') }}</strong> -
            <strong>{{ \Carbon\Carbon::parse($endDate)->format('d.m.Y') }}</strong>
        </p>

        <div class="alert alert-info">
            <strong>Toplam Qeydiyyat:</strong> {{ $reportData['total_patients'] }} xəstə
        </div>

        @if(count($reportData['doctor_stats']) > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Həkim</th>
                            <th class="text-center">Qeydiyyat Etdiyi Xəstə Sayı</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData['doctor_stats'] as $data)
                            <tr>
                                <td><strong>{{ $data['doctor']->name }}</strong></td>
                                <td class="text-center">{{ $data['patient_count'] }}</td>
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
