@php
    use App\Models\Setting;
    $canSeePrices = Setting::doctorCanSeePrices();
@endphp

@extends('layouts.app')

@section('title', 'Yeni Göndəriş')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.dashboard') }}"><i class="bi bi-house-door"></i> Ana Səhifə</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.patients') }}"><i class="bi bi-people"></i> Xəstələr</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.patients.create') }}"><i class="bi bi-person-plus"></i> Yeni Xəstə</a></li>
    <li class="nav-item"><a class="nav-link active" href="{{ route('doctor.referrals') }}"><i class="bi bi-file-medical"></i> Göndərişlər</a></li>
@endsection

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-1">Yeni Göndəriş Yarat</h1>
        <div class="text-muted">Xəstə üçün analiz göndərişi yaradın</div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('doctor.referrals.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="patient_id">Xəstə Seçin *</label>
                            <select
                                id="patient_id"
                                name="patient_id"
                                class="form-select @error('patient_id') is-invalid @enderror"
                                required
                                {{ $patient ? 'disabled' : '' }}
                            >
                                <option value="">Xəstə seçin...</option>
                                @foreach($patients as $p)
                                    <option value="{{ $p->id }}" {{ (old('patient_id', $patient?->id) == $p->id) ? 'selected' : '' }}>
                                        {{ $p->name }} {{ $p->father_name }} {{ $p->surname }} @if($p->serial_number) - {{ $p->serial_number }} @endif
                                    </option>
                                @endforeach
                            </select>
                            @if($patient)
                                <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                            @endif
                            @error('patient_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <label class="form-label fw-semibold mb-0">Analizlər Seçin *</label>
                            <span id="selectedCount" class="badge bg-primary-subtle text-primary border border-primary-subtle"></span>
                        </div>
                        @error('analyses')
                            <div class="text-danger small mb-2">{{ $message }}</div>
                        @enderror

                        <div class="mb-3">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input
                                    type="text"
                                    id="searchAnalysis"
                                    class="form-control border-start-0 ps-0"
                                    placeholder="Analiz adı ilə axtarın..."
                                >
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- Sol tərəf - Kateqoriyalar -->
                            <div class="col-md-4">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-semibold">
                                            <i class="bi bi-folder2"></i> Analiz Növləri
                                        </h6>
                                    </div>
                                    <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;" id="categoryList">
                                        @if($popularAnalyses->count() > 0)
                                            <button type="button" class="list-group-item list-group-item-action category-filter bg-success-subtle" data-category="popular">
                                                <i class="bi bi-star-fill text-warning"></i> Ən çox istədiklərim
                                                <span class="badge bg-warning text-dark float-end">{{ $popularAnalyses->count() }}</span>
                                            </button>
                                        @endif
                                        <button type="button" class="list-group-item list-group-item-action category-filter active" data-category="all">
                                            <i class="bi bi-grid-3x3-gap"></i> Hamısı
                                            <span class="badge bg-primary float-end">{{ $analyses->flatten()->count() }}</span>
                                        </button>
                                        @foreach($analyses as $categoryName => $categoryAnalyses)
                                            <button type="button" class="list-group-item list-group-item-action category-filter" data-category="{{ $categoryName }}">
                                                <i class="bi bi-folder2"></i> {{ $categoryName }}
                                                <span class="badge bg-secondary float-end">{{ $categoryAnalyses->count() }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Sağ tərəf - Analizlər -->
                            <div class="col-md-8">
                                <div class="card border">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-semibold">
                                            <i class="bi bi-clipboard-pulse"></i> <span id="categoryTitle">Bütün Analizlər</span>
                                        </h6>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="selectAllBtn">
                                                <i class="bi bi-check-all"></i> Hamısını seç
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="deselectAllBtn">
                                                <i class="bi bi-x"></i> Heç birini
                                            </button>
                                        </div>
                                    </div>
                                    <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;" id="analysisList">
                                        @if($popularAnalyses->count() > 0)
                                            @foreach($popularAnalyses as $analysis)
                                                <label class="list-group-item list-group-item-action analysis-item"
                                                       data-name="{{ strtolower($analysis->name) }}"
                                                       data-category="popular"
                                                       data-analysis-id="{{ $analysis->id }}">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <input
                                                            type="checkbox"
                                                            class="form-check-input mt-0 flex-shrink-0 analysis-checkbox"
                                                            name="analyses[]"
                                                            value="{{ $analysis->id }}"
                                                            id="analysis_{{ $analysis->id }}"
                                                            {{ old('analyses') && in_array($analysis->id, old('analyses')) ? 'checked' : '' }}
                                                        >
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-start gap-2">
                                                                <div>
                                                                    <div class="fw-semibold">
                                                                        <i class="bi bi-star-fill text-warning small"></i> {{ $analysis->name }}
                                                                    </div>
                                                                    <div class="small text-muted">
                                                                        <i class="bi bi-folder2"></i> {{ $analysis->category->name }}
                                                                        <span class="badge bg-warning text-dark ms-1">{{ $analysis->usage_count }} dəfə</span>
                                                                    </div>
                                                                </div>
                                                                @if($canSeePrices)
                                                                    <span class="badge bg-info-subtle text-info border border-info">{{ number_format($analysis->price, 2) }} AZN</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                            @endforeach
                                        @endif
                                        @foreach($analyses as $categoryName => $categoryAnalyses)
                                            @foreach($categoryAnalyses as $analysis)
                                                <label class="list-group-item list-group-item-action analysis-item"
                                                       data-name="{{ strtolower($analysis->name) }}"
                                                       data-category="{{ $categoryName }}"
                                                       data-analysis-id="{{ $analysis->id }}">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <input
                                                            type="checkbox"
                                                            class="form-check-input mt-0 flex-shrink-0 analysis-checkbox"
                                                            name="analyses[]"
                                                            value="{{ $analysis->id }}"
                                                            id="analysis_{{ $analysis->id }}"
                                                            {{ old('analyses') && in_array($analysis->id, old('analyses')) ? 'checked' : '' }}
                                                        >
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-start gap-2">
                                                                <div>
                                                                    <div class="fw-semibold">{{ $analysis->name }}</div>
                                                                    <div class="small text-muted">
                                                                        <i class="bi bi-folder2"></i> {{ $categoryName }}
                                                                    </div>
                                                                </div>
                                                                @if($canSeePrices)
                                                                    <span class="badge bg-info-subtle text-info border border-info">{{ number_format($analysis->price, 2) }} AZN</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="form-label fw-semibold" for="notes">Qeydlər (İstəyə bağlı)</label>
                            <textarea
                                id="notes"
                                name="notes"
                                class="form-control @error('notes') is-invalid @enderror"
                                rows="4"
                                placeholder="Əlavə qeydlər..."
                            >{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 flex-wrap mt-4">
                            <button type="submit" class="btn btn-success" id="submitBtn">
                                <i class="bi bi-save"></i> Göndərişi Yarat
                            </button>
                            <a href="{{ route('doctor.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Geri
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .category-filter {
        cursor: pointer;
        transition: all 0.2s;
    }

    .category-filter:hover {
        background-color: #f8f9fa !important;
    }

    .category-filter.active {
        background-color: #0d6efd !important;
        color: white !important;
        border-color: #0d6efd !important;
    }

    .category-filter.active .badge {
        background-color: white !important;
        color: #0d6efd !important;
    }

    .analysis-item {
        cursor: pointer;
        transition: all 0.2s;
    }

    .analysis-item:hover {
        background-color: #f8f9fa;
    }

    .analysis-checkbox:checked + .flex-grow-1 {
        opacity: 1;
    }

    .analysis-checkbox:not(:checked) + .flex-grow-1 {
        opacity: 0.6;
    }

    /* Mobile responsiveness */
    @media (max-width: 767.98px) {
        #categoryList {
            max-height: 200px !important;
        }

        #analysisList {
            max-height: 300px !important;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectedCountEl = document.getElementById('selectedCount');
        const searchInput = document.getElementById('searchAnalysis');
        const analysisItems = document.querySelectorAll('.analysis-item');
        const form = document.querySelector('form');
        const submitBtn = document.getElementById('submitBtn');
        let isSubmitting = false;

        // Form submit handling - prevent double submission and fix back button
        if (form && submitBtn) {
            form.addEventListener('submit', function(e) {
                // Validation: at least one analysis must be selected
                const checkedAnalyses = document.querySelectorAll('input[name="analyses[]"]:checked');
                if (checkedAnalyses.length === 0) {
                    e.preventDefault();
                    alert('Zəhmət olmasa ən azı bir analiz seçin');
                    return false;
                }

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
                if (event.persisted || (window.performance && performance.getEntriesByType("navigation")[0]?.type === 'back_forward')) {
                    isSubmitting = false;
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-save"></i> Göndərişi Yarat';
                }
            });

            // Reset on page unload
            window.addEventListener('beforeunload', function() {
                if (!isSubmitting) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-save"></i> Göndərişi Yarat';
                }
            });
        }

        // Axtarma funksiyası
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const activeCategory = document.querySelector('.category-filter.active')?.getAttribute('data-category') || 'all';

                analysisItems.forEach(item => {
                    const analysisName = item.getAttribute('data-name');
                    const itemCategory = item.getAttribute('data-category');

                    // Check if matches search
                    const matchesSearch = analysisName.includes(searchTerm);

                    // Check if matches category filter
                    const matchesCategory = activeCategory === 'all' || itemCategory === activeCategory;

                    // Show only if matches both
                    if (matchesSearch && matchesCategory) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });

                updateCount();
            });
        }

        // Kateqoriya filtri
        const categoryFilters = document.querySelectorAll('.category-filter');
        const categoryTitle = document.getElementById('categoryTitle');

        categoryFilters.forEach(filter => {
            filter.addEventListener('click', function() {
                // Remove active from all
                categoryFilters.forEach(f => f.classList.remove('active'));
                // Add active to clicked
                this.classList.add('active');

                const selectedCategory = this.getAttribute('data-category');
                const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';

                // Update title
                if (selectedCategory === 'all') {
                    categoryTitle.textContent = 'Bütün Analizlər';
                } else if (selectedCategory === 'popular') {
                    categoryTitle.textContent = 'Ən çox istədiklərim';
                } else {
                    categoryTitle.textContent = selectedCategory;
                }

                analysisItems.forEach(item => {
                    const itemCategory = item.getAttribute('data-category');
                    const analysisName = item.getAttribute('data-name');

                    // Check category match
                    const matchesCategory = selectedCategory === 'all' || itemCategory === selectedCategory;

                    // Check search match
                    const matchesSearch = !searchTerm || analysisName.includes(searchTerm);

                    // Show only if matches both
                    if (matchesCategory && matchesSearch) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });

                updateCount();
            });
        });

        // Hamısını seç / Heç birini düyməsi
        const selectAllBtn = document.getElementById('selectAllBtn');
        const deselectAllBtn = document.getElementById('deselectAllBtn');

        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function() {
                // Get only visible checkboxes
                const visibleCheckboxes = Array.from(document.querySelectorAll('.analysis-checkbox'))
                    .filter(checkbox => checkbox.closest('.analysis-item').style.display !== 'none');

                visibleCheckboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });

                updateCount();
            });
        }

        if (deselectAllBtn) {
            deselectAllBtn.addEventListener('click', function() {
                // Get only visible checkboxes
                const visibleCheckboxes = Array.from(document.querySelectorAll('.analysis-checkbox'))
                    .filter(checkbox => checkbox.closest('.analysis-item').style.display !== 'none');

                visibleCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });

                updateCount();
            });
        }

        // Seçilmiş analizlərin sayını göstər
        function updateCount() {
            const checkedCount = document.querySelectorAll('.analysis-checkbox:checked').length;
            const totalCount = document.querySelectorAll('.analysis-checkbox').length;

            selectedCountEl.textContent = `${checkedCount} / ${totalCount} seçildi`;
        }

        document.querySelectorAll('.analysis-checkbox').forEach((checkbox) => {
            checkbox.addEventListener('change', updateCount);
        });

        // Initial count
        updateCount();
    });
</script>
@endsection
