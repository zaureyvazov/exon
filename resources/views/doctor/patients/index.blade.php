@extends('layouts.app')

@section('title', 'Xəstələr')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.dashboard') }}">Ana Səhifə</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.patients') }}">Xəstələr</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.patients.create') }}">Yeni Xəstə</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.referrals') }}">Göndərişlər</a></li>
@endsection

@section('content')
    <div class="mb-4">
        <h1 class="fw-bold text-dark mb-2">Xəstələrim</h1>
        <p class="text-muted">Qeydiyyatdan keçirdiyiniz xəstələr</p>
    </div>

    <div class="mb-3">
        <a href="{{ route('doctor.patients.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Yeni Xəstə Əlavə Et
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($patients->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fw-semibold"><i class="bi bi-hash"></i> ID</th>
                                <th class="fw-semibold"><i class="bi bi-person"></i> Ad Soyad</th>
                                <th class="fw-semibold d-none d-md-table-cell"><i class="bi bi-credit-card"></i> FIN Kod</th>
                                <th class="fw-semibold d-none d-md-table-cell"><i class="bi bi-telephone"></i> Telefon</th>
                                <th class="fw-semibold d-none d-lg-table-cell"><i class="bi bi-file-medical"></i> Göndərişlər</th>
                                <th class="fw-semibold d-none d-lg-table-cell"><i class="bi bi-calendar"></i> Qeydiyyat Tarixi</th>
                                <th class="fw-semibold">Əməliyyat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($patients as $patient)
                                <tr>
                                    <td class="fw-bold text-primary">#{{ $patient->id }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $patient->full_name }}</div>
                                        <div class="d-md-none small text-muted mt-1">
                                            <div><i class="bi bi-credit-card"></i> {{ $patient->serial_number ?? '-' }}</div>
                                            <div><i class="bi bi-telephone"></i> {{ $patient->phone }}</div>
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell fw-semibold">{{ $patient->serial_number ?? '-' }}</td>
                                    <td class="d-none d-md-table-cell">{{ $patient->phone }}</td>
                                    <td class="d-none d-lg-table-cell">
                                        <span class="badge bg-info text-dark">{{ $patient->referrals_count }} göndəriş</span>
                                    </td>
                                    <td class="d-none d-lg-table-cell text-muted">{{ $patient->created_at->format('d.m.Y') }}</td>
                                    <td>
                                        <a href="{{ route('doctor.referrals.create', $patient->id) }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-plus-circle"></i> <span class="d-none d-sm-inline">Göndəriş</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $patients->links() }}
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                    <p>Hələ xəstə qeydiyyatdan keçirməmisiniz</p>
                </div>
            @endif
        </div>
    </div>
@endsection
