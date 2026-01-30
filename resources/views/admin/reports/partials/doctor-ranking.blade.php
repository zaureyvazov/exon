<div class="card">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="bi bi-trophy"></i> Həkim Performans Rankınq</h5>
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
                            <th>Rank</th>
                            <th>Həkim</th>
                            <th class="text-center">Göndəriş</th>
                            <th class="text-center">Xəstə</th>
                            <th class="text-end">Qazanc</th>
                            <th class="text-center">Bal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $data)
                            <tr>
                                <td>
                                    @if($index == 0)
                                        <span class="badge bg-warning">🥇</span>
                                    @elseif($index == 1)
                                        <span class="badge bg-secondary">🥈</span>
                                    @elseif($index == 2)
                                        <span class="badge bg-secondary">🥉</span>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </td>
                                <td><strong>{{ $data['doctor']->name }}</strong></td>
                                <td class="text-center">{{ $data['referral_count'] }}</td>
                                <td class="text-center">{{ $data['patient_count'] }}</td>
                                <td class="text-end">{{ number_format($data['total_commission'], 2) }} AZN</td>
                                <td class="text-center">{{ number_format($data['score'], 1) }}</td>
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
