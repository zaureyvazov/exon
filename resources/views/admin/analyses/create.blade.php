@extends('layouts.app')

@section('title', 'Yeni Analiz')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Ana Səhifə</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.users') }}">İstifadəçilər</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.analyses') }}">Analizlər</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.settings') }}">Ayarlar</a></li>
@endsection

@section('content')
    <div class="mb-4">
        <h1 class="fw-bold mb-1" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;"><i class="bi bi-plus-circle" style="-webkit-text-fill-color: #667eea;"></i> Yeni Analiz Əlavə Et</h1>
        <p class="text-muted">Sistemə yeni analiz növü əlavə edin</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('admin.analyses.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="category_id"><i class="bi bi-tags"></i> Analiz Növü *</label>
                            <select
                                id="category_id"
                                name="category_id"
                                class="form-control @error('category_id') is-invalid @enderror"
                                required
                            >
                                <option value="">Seçin...</option>
                                @foreach(\App\Models\AnalysisCategory::active()->orderBy('name')->get() as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="name"><i class="bi bi-clipboard-plus"></i> Analiz Adı *</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control"
                                value="{{ old('name') }}"
                                required
                                autofocus
                                placeholder="Məsələn: Ümumi qan analizi"
                            >
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="description"><i class="bi bi-file-text"></i> Təsvir</label>
                            <textarea
                                id="description"
                                name="description"
                                class="form-control"
                                rows="4"
                                placeholder="Analiz haqqında ətraflı məlumat..."
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="price"><i class="bi bi-cash"></i> Qiymət (AZN) *</label>
                                <input
                                    type="number"
                                    id="price"
                                    name="price"
                                    class="form-control"
                                    value="{{ old('price') }}"
                                    step="0.01"
                                    min="0"
                                    required
                                    placeholder="0.00"
                                >
                                @error('price')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="commission_percentage"><i class="bi bi-percent"></i> Doktor Komisyonu (%) *</label>
                                <input
                                    type="number"
                                    id="commission_percentage"
                                    name="commission_percentage"
                                    class="form-control"
                                    value="{{ old('commission_percentage') }}"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    required
                                    placeholder="Məsələn: 10.50"
                                >
                                <small class="text-muted">Adi xəstələr üçün komissiya faizi</small>
                                @error('commission_percentage')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="discount_commission_rate"><i class="bi bi-percent"></i> Endirimli Komissiya (%) *</label>
                                <input
                                    type="number"
                                    id="discount_commission_rate"
                                    name="discount_commission_rate"
                                    class="form-control"
                                    value="{{ old('discount_commission_rate', 0) }}"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    required
                                    placeholder="Məsələn: 5.00"
                                >
                                <small class="text-muted">Endirimli xəstələr üçün komissiya faizi</small>
                                @error('discount_commission_rate')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    id="is_active"
                                    name="is_active"
                                    value="1"
                                    {{ old('is_active', true) ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="is_active">
                                    <i class="bi bi-check-circle"></i> Aktiv (Doktorlar bu analizi seçə bilər)
                                </label>
                            </div>
                            @error('is_active')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary" id="submitBtn"><i class="bi bi-check-circle"></i> Analiz Yarat</button>
                            <a href="{{ route('admin.analyses') }}" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Ləğv Et</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Double submit prevention and back button fix
    let isSubmitting = false;
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submitBtn');

    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }

            isSubmitting = true;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Göndərilir...';
        });

        // Reset on back button
        window.addEventListener('pageshow', function(event) {
            if (event.persisted || performance.getEntriesByType("navigation")[0]?.type === 'back_forward') {
                isSubmitting = false;
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Analiz Yarat';
            }
        });
    }
</script>
@endsection
