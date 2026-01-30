@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold" style="color: #1e7e4f;"><i class="bi bi-people"></i> Xəstələr</h3>
            <p class="text-muted">Sistemdəki bütün xəstələr</p>
        </div>
    </div>

    <!-- Patients Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($patients->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Ad Soyad</th>
                                <th>Kimlik No</th>
                                <th>Telefon</th>
                                <th>Yaş</th>
                                <th>Cins</th>
                                <th>Qeydiyyatçı</th>
                                <th>Tarix</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($patients as $patient)
                                <tr>
                                    <td class="fw-semibold">#{{ $patient->id }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $patient->full_name }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $patient->serial_number ?? 'Seriya yoxdur' }}</span>
                                    </td>
                                    <td>
                                        <i class="bi bi-telephone"></i> {{ $patient->phone ?? '-' }}
                                    </td>
                                    <td>
                                        @if($patient->date_of_birth)
                                            {{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} yaş
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($patient->gender == 'male')
                                            <i class="bi bi-gender-male text-primary"></i> Kişi
                                        @elseif($patient->gender == 'female')
                                            <i class="bi bi-gender-female text-danger"></i> Qadın
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div class="fw-semibold">{{ $patient->registeredBy->name ?? '-' }} {{ $patient->registeredBy->surname ?? '' }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div>{{ $patient->created_at->format('d.m.Y') }}</div>
                                            <div class="text-muted">{{ $patient->created_at->format('H:i') }}</div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($patients->hasPages())
                    <div class="mt-3">
                        {{ $patients->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1"></i>
                    <div class="mt-3">Xəstə yoxdur</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
