@extends('layouts.app')

@section('title', 'Bütün Göndərişlər')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('registrar.dashboard') }}">Ana Səhifə</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('registrar.referrals') }}">Göndərişlər</a></li>
@endsection

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-1">Bütün Göndərişlər</h1>
        <div class="text-muted">Klinikaya göndərilmiş bütün analizlər</div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('registrar.referrals') }}">
                <div class="row g-3">
                    <div class="col-12 col-md-5">
                        <label class="form-label fw-semibold">Axtarış</label>
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Xəstə adı, Kimlik No..."
                            value="{{ request('search') }}"
                        >
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Hamısı</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Gözləyir</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Tamamlandı</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Ləğv edildi</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel"></i> Filtrələ
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if($referrals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Xəstə</th>
                                <th class="d-none d-md-table-cell">Kimlik No</th>
                                <th class="d-none d-lg-table-cell">Telefon</th>
                                <th class="d-none d-lg-table-cell">Doktor</th>
                                <th>Analizlər</th>
                                <th class="d-none d-md-table-cell">Qiymət</th>
                                <th>Təsdiq</th>
                                <th class="d-none d-lg-table-cell">Status</th>
                                <th class="d-none d-md-table-cell">Tarix</th>
                                <th>Əməliyyat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($referrals as $referral)
                                <tr class="{{ $referral->discount_type !== 'none' ? 'table-warning' : '' }}">
                                    <td>#{{ $referral->id }}</td>
                                    <td>{{ $referral->patient->full_name }}</td>
                                    <td class="d-none d-md-table-cell">
                                        <strong>{{ $referral->patient->name }} {{ $referral->patient->father_name }} {{ $referral->patient->surname }}</strong>
                                        @if($referral->patient->serial_number)
                                            <br><small class="text-muted">{{ $referral->patient->serial_number }}</small>
                                        @endif
                                    </td>
                                    <td class="d-none d-lg-table-cell">{{ $referral->patient->phone }}</td>
                                    <td class="d-none d-lg-table-cell">Dr. {{ $referral->doctor->name }}</td>
                                    <td>{{ $referral->analyses->count() }} analiz</td>
                                    <td class="d-none d-md-table-cell">
                                        @if($referral->discount_type !== 'none' && $referral->final_price)
                                            <div><strong class="text-success">{{ number_format($referral->final_price_with_tax, 2) }} AZN</strong></div>
                                            <small class="text-muted"><s>{{ number_format($referral->total_price_with_tax, 2) }} AZN</s></small>
                                        @else
                                            <strong>{{ number_format($referral->total_price_with_tax, 2) }} AZN</strong>
                                        @endif
                                    </td>
                                    <td>
                                        @if($referral->is_approved)
                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Təsdiqlənib</span>
                                        @else
                                            <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Gözləyir</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        @if($referral->status == 'pending')
                                            <span class="badge bg-warning text-dark">Gözləyir</span>
                                        @elseif($referral->status == 'completed')
                                            <span class="badge bg-success">Tamamlandı</span>
                                        @else
                                            <span class="badge bg-danger">Ləğv edildi</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-md-table-cell">{{ $referral->created_at->format('d.m.Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('registrar.referrals.show', $referral->id) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i> Bax
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $referrals->links() }}
                </div>
            @else
                <p class="text-center text-muted py-5">
                    @if(request('search') || request('status') != 'all')
                        Axtarış kriteriyalarına uyğun göndəriş tapılmadı
                    @else
                        Hələ heç bir göndəriş yoxdur
                    @endif
                </p>
            @endif
        </div>
    </div>
@endsection
