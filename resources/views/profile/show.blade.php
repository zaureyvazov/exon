@extends('layouts.app')

@section('title', 'Profil')

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-1">Profil</h1>
        <div class="text-muted">Şəxsi məlumatlarınızı idarə edin</div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <!-- Profile Header Card -->
            <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #f0f9f4 0%, #ffffff 100%);">
                <div class="card-body">
                    <div class="d-flex align-items-center flex-column flex-sm-row gap-3 pb-3 border-bottom">
                        <div class="rounded-circle text-white fw-bold d-inline-flex align-items-center justify-content-center flex-shrink-0 px-4 py-3 fs-3" style="background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%);">
                            {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr($user->surname, 0, 1)) }}
                        </div>
                        <div class="text-center text-sm-start">
                            <h4 class="mb-1">{{ $user->name }} {{ $user->surname }}</h4>
                            <p class="text-muted mb-2">{{ $user->email }}</p>
                            <div>
                                @if($user->isAdmin())
                                    <span class="badge" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                                        <i class="bi bi-shield-check"></i> Administrator
                                    </span>
                                @elseif($user->isDoctor())
                                    <span class="badge" style="background: linear-gradient(135deg, #2D9B6C 0%, #1e7e4f 100%);">
                                        <i class="bi bi-person-badge"></i> Doktor
                                    </span>
                                @elseif($user->isRegistrar())
                                    <span class="badge" style="background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);">
                                        <i class="bi bi-person-check"></i> Qeydiyyatçı
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Information Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-person-circle text-success"></i> Şəxsi Məlumatlar
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label">
                                    <i class="bi bi-person"></i> Ad
                                </label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    class="form-control"
                                    value="{{ old('name', $user->name) }}"
                                    {{ $user->isAdmin() ? '' : 'readonly' }}
                                >
                                @if(!$user->isAdmin())
                                    <div class="form-text">
                                        <i class="bi bi-info-circle"></i> Yalnız admin dəyişə bilər
                                    </div>
                                @endif
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="surname" class="form-label">
                                    <i class="bi bi-person"></i> Soyad
                                </label>
                                <input
                                    type="text"
                                    id="surname"
                                    name="surname"
                                    class="form-control"
                                    value="{{ old('surname', $user->surname) }}"
                                    {{ $user->isAdmin() ? '' : 'readonly' }}
                                >
                                @if(!$user->isAdmin())
                                    <div class="form-text">
                                        <i class="bi bi-info-circle"></i> Yalnız admin dəyişə bilər
                                    </div>
                                @endif
                                @error('surname')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope"></i> Email
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="form-control"
                                    value="{{ old('email', $user->email) }}"
                                >
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="phone" class="form-label">
                                    <i class="bi bi-telephone"></i> Telefon
                                </label>
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

                            @if($user->isDoctor())
                                <div class="col-12 col-md-6">
                                    <label for="hospital" class="form-label">
                                        <i class="bi bi-hospital"></i> Xəstəxana
                                    </label>
                                    <input
                                        type="text"
                                        id="hospital"
                                        name="hospital"
                                        class="form-control"
                                        value="{{ old('hospital', $user->hospital) }}"
                                    >
                                    @error('hospital')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="position" class="form-label">
                                        <i class="bi bi-briefcase"></i> Vəzifə
                                    </label>
                                    <input
                                        type="text"
                                        id="position"
                                        name="position"
                                        class="form-control"
                                        value="{{ old('position', $user->position) }}"
                                    >
                                    @error('position')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Məlumatları Yenilə
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Change Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-shield-lock text-success"></i> Şifrə Dəyişikliyi
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="current_password" class="form-label">
                                    <i class="bi bi-key"></i> Cari Şifrə
                                </label>
                                <input
                                    type="password"
                                    id="current_password"
                                    name="current_password"
                                    class="form-control"
                                    placeholder="••••••••"
                                >
                                @error('current_password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock"></i> Yeni Şifrə
                                </label>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="form-control"
                                    placeholder="••••••••"
                                >
                                <div class="form-text">
                                    <i class="bi bi-info-circle"></i> Minimum 8 simvol
                                </div>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="password_confirmation" class="form-label">
                                    <i class="bi bi-lock-fill"></i> Yeni Şifrə Təsdiqi
                                </label>
                                <input
                                    type="password"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    class="form-control"
                                    placeholder="••••••••"
                                >
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-shield-check"></i> Şifrəni Dəyiş
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
