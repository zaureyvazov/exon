@extends('layouts.app')

@section('title', 'Doktor Balansları')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Ana Səhifə</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.users') }}">İstifadəçilər</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.analyses') }}">Analizlər</a></li>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown">
            Balanslar
        </a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item active" href="{{ route('admin.balances') }}">DR Balansları</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.program.commission') }}">Proqram Komissiyası</a></li>
        </ul>
    </li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.settings') }}">Ayarlar</a></li>
@endsection

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="fw-bold mb-1"><i class="bi bi-wallet2 text-success"></i> Doktor Balansları</h1>
                <p class="text-muted mb-0">Bütün doktorların komissiya məlumatları və statistikası</p>
            </div>
            <form method="GET" action="{{ route('admin.balances') }}" class="d-flex gap-2" style="max-width: 400px;">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input
                        type="text"
                        name="search"
                        class="form-control border-start-0"
                        placeholder="Doktor adı və ya soyadı..."
                        value="{{ request('search') }}"
                    >
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.balances') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="opacity-90 small mb-2"><i class="bi bi-people"></i> Aktiv Doktorlar</div>
                            <div class="fs-3 fw-bold">{{ count($balanceData) }}</div>
                        </div>
                        <i class="bi bi-person-badge display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="opacity-90 small mb-2"><i class="bi bi-file-medical"></i> Ümumi Göndərişlər</div>
                            <div class="fs-3 fw-bold">{{ collect($balanceData)->sum('total_referrals') }}</div>
                        </div>
                        <i class="bi bi-files display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="opacity-90 small mb-2"><i class="bi bi-check-circle"></i> Təsdiqlənmiş</div>
                            <div class="fs-3 fw-bold">{{ collect($balanceData)->sum('approved_referrals') }}</div>
                        </div>
                        <i class="bi bi-check2-circle display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="opacity-90 small mb-2"><i class="bi bi-wallet2"></i> Ümumi Balans</div>
                            <div class="fs-3 fw-bold">{{ number_format(collect($balanceData)->sum('balance'), 2, '.', '') }} AZN</div>
                        </div>
                        <i class="bi bi-cash-coin display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Balance Stats -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="opacity-90 small mb-2"><i class="bi bi-check-circle"></i> Ödənilmiş Balans</div>
                            <div class="fs-3 fw-bold">{{ number_format(collect($balanceData)->sum('paid_balance'), 2, '.', '') }} AZN</div>
                        </div>
                        <i class="bi bi-credit-card display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="opacity-90 small mb-2"><i class="bi bi-hourglass-split"></i> Qalıq Balans</div>
                            <div class="fs-3 fw-bold">{{ number_format(collect($balanceData)->sum('remaining_balance'), 2, '.', '') }} AZN</div>
                        </div>
                        <i class="bi bi-piggy-bank display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100 card-hover" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="opacity-90 small mb-2"><i class="bi bi-receipt"></i> Ümumi Ödənişlər</div>
                            <div class="fs-3 fw-bold">{{ collect($balanceData)->sum('payment_count') }}</div>
                        </div>
                        <i class="bi bi-cash-stack display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-table text-success"></i> Doktor Balans Cədvəli</h5>
                <button class="btn btn-outline-success btn-sm" onclick="window.print()">
                    <i class="bi bi-printer"></i> Çap Et
                </button>
            </div>
        </div>
        <div class="card-body">
        @if(count($balanceData) > 0)
            <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fw-semibold"><i class="bi bi-person"></i> Doktor</th>
                        <th class="fw-semibold d-none d-md-table-cell"><i class="bi bi-envelope"></i> Email</th>
                        <th class="fw-semibold d-none d-lg-table-cell"><i class="bi bi-hospital"></i> Xəstəxana</th>
                        <th class="fw-semibold d-none d-lg-table-cell"><i class="bi bi-briefcase"></i> Vəzifə</th>
                        <th class="fw-semibold text-center">
                            <a href="{{ route('admin.balances', ['search' => request('search'), 'sort_by' => 'total_referrals', 'sort_order' => (request('sort_by') == 'total_referrals' && request('sort_order') == 'desc') ? 'asc' : 'desc']) }}" class="text-decoration-none text-dark">
                                <i class="bi bi-list-ol"></i> Ümumi
                                @if(request('sort_by') == 'total_referrals')
                                    <i class="bi bi-chevron-{{ request('sort_order') == 'desc' ? 'down' : 'up' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th class="fw-semibold text-center">
                            <a href="{{ route('admin.balances', ['search' => request('search'), 'sort_by' => 'approved_referrals', 'sort_order' => (request('sort_by') == 'approved_referrals' && request('sort_order') == 'desc') ? 'asc' : 'desc']) }}" class="text-decoration-none text-dark">
                                <i class="bi bi-check-circle"></i> Təsdiqlənmiş
                                @if(request('sort_by') == 'approved_referrals')
                                    <i class="bi bi-chevron-{{ request('sort_order') == 'desc' ? 'down' : 'up' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th class="fw-semibold text-end">
                            <a href="{{ route('admin.balances', ['search' => request('search'), 'sort_by' => 'balance', 'sort_order' => (request('sort_by') == 'balance' && request('sort_order') == 'desc') ? 'asc' : 'desc']) }}" class="text-decoration-none text-dark">
                                <i class="bi bi-wallet2"></i> Balans
                                @if(request('sort_by') == 'balance' || !request('sort_by'))
                                    <i class="bi bi-chevron-{{ (request('sort_order') ?? 'desc') == 'desc' ? 'down' : 'up' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th class="fw-semibold text-end d-none d-xl-table-cell">
                            <a href="{{ route('admin.balances', ['search' => request('search'), 'sort_by' => 'paid_balance', 'sort_order' => (request('sort_by') == 'paid_balance' && request('sort_order') == 'desc') ? 'asc' : 'desc']) }}" class="text-decoration-none text-dark">
                                <i class="bi bi-check-circle"></i> Ödənilmiş
                                @if(request('sort_by') == 'paid_balance')
                                    <i class="bi bi-chevron-{{ request('sort_order') == 'desc' ? 'down' : 'up' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th class="fw-semibold text-end d-none d-xl-table-cell">
                            <a href="{{ route('admin.balances', ['search' => request('search'), 'sort_by' => 'remaining_balance', 'sort_order' => (request('sort_by') == 'remaining_balance' && request('sort_order') == 'desc') ? 'asc' : 'desc']) }}" class="text-decoration-none text-dark">
                                <i class="bi bi-hourglass-split"></i> Qalıq
                                @if(request('sort_by') == 'remaining_balance')
                                    <i class="bi bi-chevron-{{ request('sort_order') == 'desc' ? 'down' : 'up' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th class="fw-semibold text-center"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($balanceData as $data)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle text-white fw-bold d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%); font-size: 14px;">
                                        {{ strtoupper(substr($data['doctor']->name, 0, 1)) }}{{ strtoupper(substr($data['doctor']->surname, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $data['doctor']->name }} {{ $data['doctor']->surname }}</div>
                                        <small class="text-muted d-md-none">{{ $data['doctor']->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell"><small class="text-muted">{{ $data['doctor']->email }}</small></td>
                            <td class="d-none d-lg-table-cell">{{ $data['doctor']->hospital ?? '-' }}</td>
                            <td class="d-none d-lg-table-cell">{{ $data['doctor']->position ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge" style="background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);">
                                    {{ $data['total_referrals'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                    {{ $data['approved_referrals'] }}
                                </span>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-success fs-6">{{ number_format($data['balance'], 2, '.', '') }} AZN</span>
                            </td>
                            <td class="text-end d-none d-xl-table-cell">
                                <span class="badge bg-success-subtle text-success border border-success">{{ number_format($data['paid_balance'], 2, '.', '') }} AZN</span>
                            </td>
                            <td class="text-end d-none d-xl-table-cell">
                                @if($data['remaining_balance'] > 0)
                                    <span class="badge bg-warning-subtle text-warning border border-warning">{{ number_format($data['remaining_balance'], 2, '.', '') }} AZN</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary">0.00 AZN</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.balances.doctor', $data['doctor']->id) }}" class="btn btn-sm btn-outline-success me-1">
                                    <i class="bi bi-eye"></i> Detay
                                </a>
                                <a href="{{ route('admin.payments.create', $data['doctor']->id) }}" class="btn btn-sm btn-success">
                                    <i class="bi bi-cash-coin"></i> Ödəniş Et
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td colspan="6" class="text-end pe-3">ÜMUMI BALANS:</td>
                        <td class="text-success fs-5 text-end">
                            {{ number_format(collect($balanceData)->sum('balance'), 2, '.', '') }} AZN
                        </td>
                        <td class="text-end d-none d-xl-table-cell">
                            <span class="badge bg-success fs-6">{{ number_format(collect($balanceData)->sum('paid_balance'), 2, '.', '') }} AZN</span>
                        </td>
                        <td class="text-end d-none d-xl-table-cell">
                            <span class="badge bg-warning fs-6">{{ number_format(collect($balanceData)->sum('remaining_balance'), 2, '.', '') }} AZN</span>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            </div>
        @else
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                <p>Doktor yoxdur</p>
            </div>
        @endif
        </div>
    </div>
@endsection
