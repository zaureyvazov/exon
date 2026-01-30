@extends('layouts.app')

@section('title', 'Analiz Növləri')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Analiz Növləri</h2>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Analiz Növü
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Ad</th>
                            <th>Açıqlama</th>
                            <th>Analiz Sayı</th>
                            <th>Status</th>
                            <th>Əməliyyatlar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr class="{{ !$category->is_active ? 'table-danger' : '' }}">
                                <td>
                                    <strong class="{{ !$category->is_active ? 'text-danger' : '' }}">
                                        {{ $category->name }}
                                    </strong>
                                </td>
                                <td>{{ $category->description ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $category->analyses_count }}</span>
                                </td>
                                <td>
                                    @if($category->is_active)
                                        <span class="badge bg-success">Aktiv</span>
                                    @else
                                        <span class="badge bg-danger">Deaktiv</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.categories.edit', $category->id) }}"
                                           class="btn btn-sm btn-success" title="Redaktə">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <form action="{{ route('admin.categories.delete', $category->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Bu analiz növünü silmək istədiyinizdən əminsiniz?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Sil">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Hələ heç bir analiz növü əlavə edilməyib
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
