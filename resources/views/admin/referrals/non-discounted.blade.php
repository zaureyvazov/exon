@extends('layouts.app')

@section('title', 'Endirimsiz Göndərişlər')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Ana Səhifə</a></li>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown">
            Göndərişlər
        </a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item active" href="{{ route('admin.referrals.non-discounted') }}">Endirimsiz</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.referrals.discounted') }}">Endirimli</a></li>
        </ul>
    </li>
@endsection

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-bold">Endirimsiz Göndərişlər</h1>
        <p class="text-muted">Endirim tətbiq edilməmiş bütün göndərişlər</p>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.referrals.non-discounted') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Başlanğıc Tarix</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Bitmə Tarix</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Həkim</label>
                        <select name="doctor_id" class="form-select">
                            <option value="">Hamısı</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                    Dr. {{ $doctor->name }} {{ $doctor->surname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Xəstə Axtarışı</label>
                        <input type="text" name="search" class="form-control" placeholder="Ad, FIN..." value="{{ request('search') }}">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-filter"></i> Filtrele</button>
                        <a href="{{ route('admin.referrals.non-discounted') }}" class="btn btn-outline-secondary">Təmizlə</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($referrals->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Həkim</th>
                            <th>Xəstə</th>
                            <th>Analizlər</th>
                            <th>Qiymət</th>
                            <th>Komissiya</th>
                            <th>Status</th>
                            <th>Tarix</th>
                            <th>Əməliyyat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($referrals as $referral)
                        <tr>
                            <td><span class="badge bg-secondary">#{{ $referral->id }}</span></td>
                            <td>
                                <div class="fw-semibold">Dr. {{ $referral->doctor->name }}</div>
                                <small class="text-muted">{{ $referral->doctor->surname }}</small>
                            </td>
                            <td>
                                <div>{{ $referral->patient->full_name }} {{ $referral->patient->father_name }}</div>
                                
                                @if($referral->patient->serial_number)
                                    <br><small class="text-muted">{{ $referral->patient->serial_number }}</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $totalAnalyses = $referral->analyses->count();
                                    $cancelledCount = $referral->analyses->filter(fn($a) => $a->pivot->is_cancelled ?? false)->count();
                                    $activeCount = $totalAnalyses - $cancelledCount;
                                @endphp
                                <span class="badge bg-info">{{ $activeCount }} aktiv</span>
                                @if($cancelledCount > 0)
                                    <span class="badge bg-danger">{{ $cancelledCount }} ləğv</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold">{{ number_format($referral->total_price, 2) }} AZN</div>
                                <small class="text-muted">Vergi daxil: {{ number_format($referral->total_price_with_tax, 2) }} AZN</small>
                            </td>
                            <td>
                                @if($referral->is_priced && $referral->doctor_commission)
                                    <span class="fw-bold text-success">{{ number_format($referral->doctor_commission, 2) }} AZN</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($referral->is_approved)
                                    <span class="badge bg-success">Təsdiqlənib</span>
                                @else
                                    <span class="badge bg-warning">Gözləyir</span>
                                @endif
                            </td>
                            <td><small>{{ $referral->created_at->format('d.m.Y H:i') }}</small></td>
                            <td>
                                <a href="{{ route('admin.referrals.show', $referral->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Bax
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $referrals->links() }}
    </div>
    @else
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
            <p class="text-muted">Endirimsiz göndəriş tapılmadı</p>
        </div>
    </div>
    @endif
@endsection
