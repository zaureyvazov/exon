@extends('layouts.app')

@section('title', 'İstifadəçilər')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Ana Səhifə</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.users') }}">İstifadəçilər</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.analyses') }}">Analizlər</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.settings') }}">Ayarlar</a></li>
@endsection

@section('content')
    <div class="mb-4">
        <h1 class="fw-bold text-dark mb-2">İstifadəçilər</h1>
        <p class="text-muted">Sistem istifadəçilərini idarə edin</p>
    </div>

    <div class="mb-3 d-flex justify-content-between align-items-center gap-3 flex-wrap">
        <a href="{{ route('admin.users.create') }}" class="btn btn-lg" style="background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%); color: white; border: none;">
            <i class="bi bi-person-plus"></i> Yeni İstifadəçi
        </a>

        <form method="GET" action="{{ route('admin.users') }}" class="d-flex gap-2 flex-grow-1" style="max-width: 500px;">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input 
                    type="text" 
                    name="search" 
                    class="form-control border-start-0" 
                    placeholder="Ad, soyad, username, email və ya telefon..." 
                    value="{{ request('search') }}"
                >
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Axtar
            </button>
            @if(request('search'))
                <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i>
                </a>
            @endif
        </form>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fw-semibold">ID</th>
                                <th class="fw-semibold">Ad Soyad</th>
                                <th class="fw-semibold d-none d-md-table-cell">Email</th>
                                <th class="fw-semibold d-none d-lg-table-cell">Telefon</th>
                                <th class="fw-semibold d-none d-xl-table-cell">Xəstəxana</th>
                                <th class="fw-semibold d-none d-xl-table-cell">Vəzifə</th>
                                <th class="fw-semibold">Rol</th>
                                <th class="fw-semibold d-none d-lg-table-cell">Qeydiyyat</th>
                                <th class="fw-semibold">Əməliyyat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td class="fw-bold text-primary">#{{ $user->id }}</td>
                                    <td>{{ $user->name }} {{ $user->surname }}</td>
                                    <td class="d-none d-md-table-cell"><small class="text-muted">{{ $user->email }}</small></td>
                                    <td class="d-none d-lg-table-cell">{{ $user->phone ?? '-' }}</td>
                                    <td class="d-none d-xl-table-cell">{{ $user->hospital ?? '-' }}</td>
                                    <td class="d-none d-xl-table-cell">{{ $user->position ?? '-' }}</td>
                                    <td>
                                        @if($user->role)
                                            <span class="badge bg-warning text-dark">{{ $user->role->display_name }}</span>
                                        @endif
                                    </td>
                                    <td class="d-none d-lg-table-cell"><small class="text-muted">{{ $user->created_at->format('d.m.Y') }}</small></td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-success" title="Redaktə">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if($user->id !== Auth::id())
                                                <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="d-inline delete-form\" data-user-name="{{ $user->full_name }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn" title="Sil">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $users->links() }}
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                    <p>İstifadəçi yoxdur</p>
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
            const userName = form.dataset.userName;

            // Create modern confirmation modal
            if (confirm(`${userName} adlı istifadəçini silmək istədiyinizdən əminsiniz?\n\nBu əməliyyat geri alına bilməz!`)) {
                // Disable button and show loading
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                form.submit();
            }
        });
    });
</script>
@endsection
