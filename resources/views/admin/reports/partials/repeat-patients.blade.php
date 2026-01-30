<div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="bi bi-arrow-repeat"></i> Təkrar Müraciət Edən Xəstələr</h5>
    </div>
    <div class="card-body">
        <p class="text-muted mb-3">
            <i class="bi bi-calendar-range"></i>
            Tarix aralığı: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d.m.Y') }}</strong> -
            <strong>{{ \Carbon\Carbon::parse($endDate)->format('d.m.Y') }}</strong>
        </p>

        <div class="alert alert-info">
            <strong>Toplam Təkrar Xəstə:</strong> {{ $reportData['total_repeat_patients'] }}
        </div>

        @if(count($reportData['repeat_patients']) > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Xəstə</th>
                            <th>FIN</th>
                            <th>Qeydiyyatçı Həkim</th>
                            <th class="text-center">Göndəriş Sayı</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData['repeat_patients'] as $patient)
                            <tr>
                                <td><strong>{{ $patient->name }} {{ $patient->surname }}</strong></td>
                                <td>{{ $patient->serial_number ?? '-' }}</td>
                                <td>{{ $patient->registeredBy->name }}</td>
                                <td class="text-center">{{ $patient->referrals_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                Seçilən tarix aralığında təkrar müraciət edən xəstə yoxdur.
            </div>
        @endif
    </div>
</div>
