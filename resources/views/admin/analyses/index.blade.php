@extends('layouts.app')

@section('title', 'Analizlər')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Ana Səhifə</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.users') }}">İstifadəçilər</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.analyses') }}">Analizlər</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.settings') }}">Ayarlar</a></li>
@endsection

@section('content')
    <div class="mb-4">
        <h1 class="fw-bold text-dark mb-2"><i class="bi bi-clipboard-data"></i> Analizlər</h1>
        <p class="text-muted">Sistem analiz növlərini idarə edin</p>
    </div>

    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <a href="{{ route('admin.analyses.create') }}" class="btn btn-lg" style="background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%); color: white; border: none;">
                <i class="bi bi-plus-circle"></i> Yeni Analiz
            </a>
            <form method="GET" action="{{ route('admin.analyses') }}" class="d-flex gap-2 align-items-center">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="show_inactive" id="showInactive" 
                           value="1" {{ request('show_inactive') ? 'checked' : '' }} onchange="this.form.submit()">
                    <label class="form-check-label" for="showInactive">
                        Aktiv olmayanları göstər
                    </label>
                </div>
                <input type="text" name="search" class="form-control" placeholder="Analiz adı, özel kod və ya təsvir..." 
                       value="{{ request('search') }}" style="min-width: 300px;">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Axtar</button>
                @if(request('search') || request('show_inactive'))
                    <a href="{{ route('admin.analyses') }}" class="btn btn-outline-secondary">Təmizlə</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
        @if($analyses->count() > 0)
            <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fw-semibold">ID</th>
                        <th class="fw-semibold">Özel Kod</th>
                        <th class="fw-semibold">Ad</th>
                        <th class="fw-semibold d-none d-lg-table-cell">Analiz Növü</th>
                        <th class="fw-semibold d-none d-md-table-cell">Təsvir</th>
                        <th class="fw-semibold">Qiymət</th>
                        <th class="fw-semibold d-none d-lg-table-cell">Komisyon</th>
                        <th class="fw-semibold">Status</th>
                        <th class="fw-semibold d-none d-md-table-cell">Yaradılma</th>
                        <th class="fw-semibold">Əməliyyat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($analyses as $analysis)
                        <tr class="{{ !$analysis->is_active ? 'table-danger' : '' }}">
                            <td><span class="badge bg-secondary">#{{ $analysis->id }}</span></td>
                            <td>
                                @if($analysis->ozel_kod)
                                    <span class="badge bg-info-subtle text-info border border-info">{{ $analysis->ozel_kod }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="fw-semibold {{ !$analysis->is_active ? 'text-danger' : '' }}">{{ $analysis->name }}</td>
                            <td class="d-none d-lg-table-cell">
                                @if($analysis->category)
                                    <span class="badge {{ $analysis->category->is_active ? 'bg-info' : 'bg-danger' }}">
                                        {{ $analysis->category->name }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="d-none d-md-table-cell text-muted">{{ Str::limit($analysis->description, 50) }}</td>
                            <td class="fw-bold {{ !$analysis->is_active ? 'text-danger' : 'text-success' }}">{{ number_format($analysis->price, 2) }} AZN</td>
                            <td class="d-none d-lg-table-cell"><span class="badge bg-warning text-dark">{{ $analysis->commission_percentage }}%</span></td>
                            <td>
                                @if($analysis->is_active)
                                    <span class="badge bg-success">Aktiv</span>
                                @else
                                    <span class="badge bg-danger">Deaktiv</span>
                                @endif
                            </td>
                            <td class="d-none d-md-table-cell text-muted">{{ $analysis->created_at->format('d.m.Y') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.analyses.edit', $analysis->id) }}" class="btn btn-sm btn-success" title="Redaktə"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('admin.analyses.delete', $analysis->id) }}" method="POST" class="d-inline delete-form" data-analysis-name="{{ $analysis->name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger delete-btn" title="Sil"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>

            <div class="mt-3">
                {{ $analyses->links() }}
            </div>
        @else
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                <p>Analiz yoxdur</p>
            </div>
        @endif
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Modern delete confirmation
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('.delete-form');
            const analysisName = form.dataset.analysisName;

            // Create modern confirmation modal
            if (confirm(`${analysisName} adlı analizi silmək istədiyinizdən əminsiniz?\n\nBu əməliyyat geri alına bilməz!`)) {
                // Disable button and show loading
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                form.submit();
            }
        });
    });
</script>
@endsection
