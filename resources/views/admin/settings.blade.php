@extends('layouts.app')

@section('title', 'Ayarlar')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Ana Səhifə</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.users') }}">İstifadəçilər</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.analyses') }}">Analizlər</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.balances') }}">Balanslar</a></li>
    <li class="nav-item"><a class="nav-link active" href="{{ route('admin.settings') }}">Ayarlar</a></li>
@endsection

@section('content')
    <div class="mb-4">
        <h1 class="fw-bold text-dark mb-2">Sistem Ayarları</h1>
        <p class="text-muted">Sistemin ümumi konfiqurasiyası</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Konfiqurasiya</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf

                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3 text-dark">
                                <i class="bi bi-person-badge me-2"></i>Doktor Ayarları
                            </h6>

                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <div class="form-check form-switch">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            role="switch"
                                            id="doctor_can_see_prices"
                                            name="doctor_can_see_prices"
                                            value="1"
                                            {{ $settings['doctor_can_see_prices'] == '1' ? 'checked' : '' }}
                                            onchange="this.form.doctor_can_see_prices.value = this.checked ? '1' : '0'"
                                        >
                                        <input type="hidden" name="doctor_can_see_prices" value="{{ $settings['doctor_can_see_prices'] }}">
                                        <label class="form-check-label" for="doctor_can_see_prices">
                                            <strong>Doktorlar qiymətləri görsün</strong>
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Bu ayar aktivdirsə, doktorlar yönəliş yaradarkən və məlumat baxarkən analizlərin qiymətlərini görə bilərlər.
                                        Deaktiv olduqda, qiymətlər gizli olacaq.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-2"></i>Dəyişiklikləri Yadda Saxla
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">
                        <i class="bi bi-lightbulb me-2"></i>Məlumat
                    </h6>
                    <p class="small text-muted mb-2">
                        Bu bölmədə sistemin işləmə prinsiplərini dəyişdirə bilərsiniz.
                    </p>
                    <hr class="my-3">
                    <p class="small text-muted mb-0">
                        <strong>Doktor Qiymət Görünüşü:</strong><br>
                        Aktivdirsə: Doktorlar bütün qiymətləri görə bilər.<br>
                        Deaktivdirsə: Doktorlar yalnız təsdiq olunmuş və qiymətləndirilmiş yönəlişlərin komissiyasını görə bilər.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Sync checkbox with hidden input
    const checkbox = document.getElementById('doctor_can_see_prices');
    const hiddenInput = document.querySelector('input[name="doctor_can_see_prices"][type="hidden"]');

    checkbox.addEventListener('change', function() {
        hiddenInput.value = this.checked ? '1' : '0';
    });
</script>
@endpush
