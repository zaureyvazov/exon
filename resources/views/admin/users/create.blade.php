@extends('layouts.app')

@section('title', 'Yeni İstifadəçi')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Ana Səhifə</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.users') }}">İstifadəçilər</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.analyses') }}">Analizlər</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.settings') }}">Ayarlar</a></li>
@endsection

@section('content')
    <div class="mb-4">
        <h1 class="fw-bold text-dark mb-2">Yeni İstifadəçi Əlavə Et</h1>
        <p class="text-muted">Sistemə yeni istifadəçi əlavə edin</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('admin.users.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label fw-semibold">Ad *</label>
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

                            <div class="col-12 col-md-6">
                                <label for="surname" class="form-label fw-semibold">Soyad *</label>
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

                            <div class="col-12 col-md-6">
                                <label for="username" class="form-label fw-semibold">İstifadəçi Adı *</label>
                                <input
                                    type="text"
                                    id="username"
                                    name="username"
                                    class="form-control"
                                    value="{{ old('username') }}"
                                    required
                                    placeholder="admin, doctor, registrar və s."
                                >
                                @error('username')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label fw-semibold">
                                    Email <small class="text-muted fw-normal">(İstəyə bağlı)</small>
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="form-control"
                                    value="{{ old('email') }}"
                                    placeholder="email@example.com"
                                >
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="phone" class="form-label fw-semibold">
                                    Telefon <small class="text-muted fw-normal">(İstəyə bağlı)</small>
                                </label>
                                <input
                                    type="text"
                                    id="phone"
                                    name="phone"
                                    class="form-control"
                                    value="{{ old('phone') }}"
                                    placeholder="+994501234567"
                                >
                                @error('phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="hospital" class="form-label fw-semibold">
                                    Xəstəxana <small class="text-muted fw-normal">(İstəyə bağlı)</small>
                                </label>
                                <input
                                    type="text"
                                    id="hospital"
                                    name="hospital"
                                    class="form-control"
                                    value="{{ old('hospital') }}"
                                >
                                @error('hospital')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="position" class="form-label fw-semibold">
                                    Vəzifə <small class="text-muted fw-normal">(İstəyə bağlı)</small>
                                </label>
                                <input
                                    type="text"
                                    id="position"
                                    name="position"
                                    class="form-control"
                                    value="{{ old('position') }}"
                                >
                                @error('position')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="role_id" class="form-label fw-semibold">Rol *</label>
                                <select id="role_id" name="role_id" class="form-select" required>
                                    <option value="">Rol seçin</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ $role->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="password" class="form-label fw-semibold">Şifrə *</label>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-control"
                                    required
                                >
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="password_confirmation" class="form-label fw-semibold">Şifrə Təsdiqi *</label>
                                <input
                                    type="password"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    class="form-control"
                                    required
                                >
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-save"></i> Yadda Saxla
                            </button>
                            <a href="{{ route('admin.users') }}" class="btn btn-secondary">
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
                submitBtn.innerHTML = '<i class="bi bi-save"></i> Yadda Saxla';
            }
        });
    }
</script>
@endsection
