@extends('layouts.app')

@section('title', 'Analiz Növünü Düzəliş Et')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Analiz Növünü Düzəliş Et</h2>
        <a href="{{ route('admin.categories') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Ad <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control @error('name') is-invalid @enderror"
                           id="name"
                           name="name"
                           value="{{ old('name', $category->name) }}"
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Açıqlama</label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description"
                              name="description"
                              rows="3">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="is_active" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-control @error('is_active') is-invalid @enderror"
                            id="is_active"
                            name="is_active"
                            required>
                        <option value="1" {{ old('is_active', $category->is_active) == 1 ? 'selected' : '' }}>
                            Aktiv
                        </option>
                        <option value="0" {{ old('is_active', $category->is_active) == 0 ? 'selected' : '' }}>
                            Deaktiv
                        </option>
                    </select>
                    @error('is_active')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if($category->analyses()->count() > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Bu analiz növünə bağlı <strong>{{ $category->analyses()->count() }}</strong> analiz var.
                        Silmək üçün əvvəlcə analizləri başqa növə köçürün və ya silin.
                    </div>
                @endif

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Yadda saxla
                    </button>
                    <a href="{{ route('admin.categories') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Ləğv et
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
