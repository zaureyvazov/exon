@extends('layouts.app')

@section('title', 'Yeni Xəstə Qeydiyyatı')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.dashboard') }}">Ana Səhifə</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.patients') }}">Xəstələr</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.patients.create') }}">Yeni Xəstə</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.referrals') }}">Göndərişlər</a></li>
@endsection

@section('content')
    <div class="mb-4">
        <h1 class="fw-bold mb-1" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;"><i class="bi bi-person-plus" style="-webkit-text-fill-color: #667eea;"></i> Yeni Xəstə Qeydiyyatı</h1>
        <p class="text-muted">Xəstə məlumatlarını daxil edin</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('doctor.patients.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold" for="name">
                                    <i class="bi bi-person"></i> Ad *
                                </label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    class="form-control"
                                    value="{{ old('name') }}"
                                    required
                                    autofocus
                                >
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold" for="surname">
                                    <i class="bi bi-person"></i> Soyad *
                                </label>
                                <input
                                    type="text"
                                    id="surname"
                                    name="surname"
                                    class="form-control"
                                    value="{{ old('surname') }}"
                                    required
                                >
                                @error('surname')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold" for="father_name">
                                    <i class="bi bi-person"></i> Ata Adı *
                                </label>
                                <input
                                    type="text"
                                    id="father_name"
                                    name="father_name"
                                    class="form-control"
                                    value="{{ old('father_name') }}"
                                    required
                                >
                                @error('father_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="serial_prefix">
                                    <i class="bi bi-credit-card"></i> Seriya Nömrəsi
                                </label>
                                <div class="input-group">
                                    <select
                                        id="serial_prefix"
                                        name="serial_prefix"
                                        class="form-select"
                                        style="max-width: 100px;"
                                    >
                                        <option value="">Seç</option>
                                        <option value="AA" {{ old('serial_prefix') == 'AA' ? 'selected' : '' }}>AA</option>
                                        <option value="AZE" {{ old('serial_prefix') == 'AZE' ? 'selected' : '' }}>AZE</option>
                                        <option value="MYI" {{ old('serial_prefix') == 'MYI' ? 'selected' : '' }}>MYI</option>
                                    </select>
                                    <input
                                        type="text"
                                        id="serial_number"
                                        name="serial_number"
                                        class="form-control"
                                        value="{{ old('serial_number') }}"
                                        maxlength="7"
                                    >
                                </div>
                                <small class="text-muted">Məsələn: AA0471403, AZE0444554 (İstəyə bağlı)</small>
                                @error('serial_prefix')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                @error('serial_number')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="phone">
                                    <i class="bi bi-telephone"></i> Əlaqə Nömrəsi *
                                </label>
                                <input
                                    type="text"
                                    id="phone"
                                    name="phone"
                                    class="form-control"
                                    value="{{ old('phone') }}"
                                    placeholder="+994501234567"
                                    required
                                >
                                @error('phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-check-circle"></i> Qeyd Et və Analiz Seç
                            </button>
                            <a href="{{ route('doctor.patients') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Ləğv Et
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Form submit handling
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submitBtn');
    let isSubmitting = false;

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

        // Fix browser back/forward cache issue
        window.addEventListener('pageshow', function(event) {
            if (event.persisted || performance.getEntriesByType("navigation")[0].type === 'back_forward') {
                // Page loaded from cache, reset form state
                isSubmitting = false;
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Qeyd Et və Analiz Seç';
                form.reset();
            }
        });

        // Reset on page unload (for some browsers)
        window.addEventListener('beforeunload', function() {
            if (!isSubmitting) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Qeyd Et və Analiz Seç';
            }
        });
    }
</script>
@endsection
