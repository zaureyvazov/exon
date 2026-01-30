@extends('layouts.app')

@section('title', 'İstifadəçi Redəktə Et')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Ana Səhifə</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.users') }}">İstifadəçilər</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.analyses') }}">Analizlər</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.settings') }}">Ayarlar</a></li>
@endsection

@section('content')
    <div class="mb-4">
        <h1 class="fw-bold text-dark mb-2"><i class="bi bi-person-gear"></i> İstifadəçi Redəktə Et</h1>
        <p class="text-muted">İstifadəçi məlumatlarını dəyişdirin</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="name"><i class="bi bi-person"></i> Ad *</label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    class="form-control"
                                    value="{{ old('name', $user->name) }}"
                                    required
                                    autofocus
                                >
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="surname"><i class="bi bi-person"></i> Soyad *</label>
                                <input
                                    type="text"
                                    id="surname"
                                    name="surname"
                                    class="form-control"
                                    value="{{ old('surname', $user->surname) }}"
                                    required
                                >
                                @error('surname')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="username"><i class="bi bi-person-badge"></i> İstifadəçi Adı *</label>
                                <input
                                    type="text"
                                    id="username"
                                    name="username"
                                    class="form-control"
                                    value="{{ old('username', $user->username) }}"
                                    required
                                    placeholder="admin, doctor, registrar və s."
                                >
                                @error('username')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="email"><i class="bi bi-envelope"></i> Email <small class="text-muted fw-normal">(İstəyə bağlı)</small></label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="form-control"
                                    value="{{ old('email', $user->email) }}"
                                    placeholder="email@example.com"
                                >
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="phone"><i class="bi bi-telephone"></i> Telefon <small class="text-muted fw-normal">(İstəyə bağlı)</small></label>
                                <input
                                    type="text"
                                    id="phone"
                                    name="phone"
                                    class="form-control"
                                    value="{{ old('phone', $user->phone) }}"
                                    placeholder="+994501234567"
                                >
                                @error('phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="hospital"><i class="bi bi-hospital"></i> Xəstəxana <small class="text-muted fw-normal">(İstəyə bağlı)</small></label>
                                <input
                                    type="text"
                                    id="hospital"
                                    name="hospital"
                                    class="form-control"
                                    value="{{ old('hospital', $user->hospital) }}"
                                    placeholder="Məsələn: Mərkəzi Klinik Xəstəxana"
                                >
                                @error('hospital')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="position"><i class="bi bi-briefcase"></i> Vəzifə <small class="text-muted fw-normal">(İstəyə bağlı)</small></label>
                                <input
                                    type="text"
                                    id="position"
                                    name="position"
                                    class="form-control"
                                    value="{{ old('position', $user->position) }}"
                                    placeholder="Məsələn: Baş həkim, Terapevt"
                                >
                                @error('position')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold" for="role_id"><i class="bi bi-shield-check"></i> Rol *</label>
                                <select
                                    id="role_id"
                                    name="role_id"
                                    class="form-select"
                                    required
                                >
                                    <option value="">Rol seçin...</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                            {{ $role->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="password"><i class="bi bi-lock"></i> Şifrə</label>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-control"
                                >
                                <small class="text-muted">Boş buraxsanz dəyişməyəcək</small>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold" for="password_confirmation"><i class="bi bi-lock-fill"></i> Şifrə Təsdiqi</label>
                                <input
                                    type="password"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    class="form-control"
                                >
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary" id="submitBtn"><i class="bi bi-check-circle"></i> Dəyişiklikləri Yadda Saxla</button>
                            <a href="{{ route('admin.users') }}" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Ləğv Et</a>
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

        window.addEventListener('pageshow', function(event) {
            if (event.persisted || performance.getEntriesByType("navigation")[0]?.type === 'back_forward') {
                isSubmitting = false;
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Dəyişiklikləri Yadda Saxla';
            }
        });
    }
</script>
@endsection
