@php
    use App\Models\Setting;
    $canSeePrices = Setting::doctorCanSeePrices();
@endphp

@extends('layouts.app')

@section('title', 'Göndərişi Redaktə Et')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.dashboard') }}"><i class="bi bi-house-door"></i> Ana Səhifə</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.patients') }}"><i class="bi bi-people"></i> Xəstələr</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('doctor.patients.create') }}"><i class="bi bi-person-plus"></i> Yeni Xəstə</a></li>
    <li class="nav-item"><a class="nav-link active" href="{{ route('doctor.referrals') }}"><i class="bi bi-file-medical"></i> Göndərişlər</a></li>
@endsection

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h1 class="h3 fw-bold mb-1">
                    <i class="bi bi-pencil-square text-warning"></i> Göndərişi Redaktə Et #{{ $referral->id }}
                </h1>
                <div class="text-muted">Təsdiqlənməmişdir, {{ $referral->remaining_edit_time }} dəqiqə redaktə müddəti qalıb</div>
            </div>
            <a href="{{ route('doctor.referrals.show', $referral->id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Ləğv et
            </a>
        </div>
    </div>

    @if($referral->remaining_edit_time <= 10)
        <div class="alert alert-warning d-flex align-items-center mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>Diqqət! Redaktə müddətiniz bitməyə çox azdır. Zəhmət olmasa tez bir zamanda yadda saxlayın.</div>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('doctor.referrals.update', $referral->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Xəstə</label>
                            <div class="card bg-light border">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-person-circle fs-4 text-primary"></i>
                                        <div>
                                            <div class="fw-semibold">{{ $referral->patient->full_name }}</div>
                                            <small class="text-muted">
                                                @if($referral->patient->serial_number)
                                                    Seriya: {{ $referral->patient->serial_number }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="patient_id" value="{{ $referral->patient_id }}">
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> Xəstə məlumatı dəyişdirilə bilməz
                            </small>
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
                                                            {{ in_array($analysis->id, old('analyses', $selectedAnalyses)) ? 'checked' : '' }}
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
                                                            {{ in_array($analysis->id, old('analyses', $selectedAnalyses)) ? 'checked' : '' }}
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
                                rows="3"
                                placeholder="Əlavə qeydlər..."
                            >{{ old('notes', $referral->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="{{ route('doctor.referrals.show', $referral->id) }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Ləğv et
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Dəyişiklikləri Yadda Saxla
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // JavaScript kodu create.blade.php ilə eynidir
    document.addEventListener('DOMContentLoaded', function() {
        const categoryButtons = document.querySelectorAll('.category-filter');
        const analysisItems = document.querySelectorAll('.analysis-item');
        const searchInput = document.getElementById('searchAnalysis');
        const categoryTitle = document.getElementById('categoryTitle');
        const selectedCountBadge = document.getElementById('selectedCount');
        const selectAllBtn = document.getElementById('selectAllBtn');
        const deselectAllBtn = document.getElementById('deselectAllBtn');

        let currentCategory = 'all';

        // Update selected count
        function updateSelectedCount() {
            const checkedBoxes = document.querySelectorAll('.analysis-checkbox:checked');
            const count = checkedBoxes.length;
            selectedCountBadge.textContent = count > 0 ? `${count} analiz seçildi` : 'Heç biri seçilməyib';
        }

        // Filter analyses by category
        function filterByCategory(category) {
            currentCategory = category;
            
            analysisItems.forEach(item => {
                const itemCategory = item.dataset.category;
                
                if (category === 'all' || itemCategory === category) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });

            // Update title
            if (category === 'all') {
                categoryTitle.textContent = 'Bütün Analizlər';
            } else if (category === 'popular') {
                categoryTitle.textContent = 'Ən Çox İstədiklərim';
            } else {
                categoryTitle.textContent = category;
            }
        }

        // Search functionality
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            analysisItems.forEach(item => {
                const itemCategory = item.dataset.category;
                const name = item.dataset.name;
                const matchesSearch = name.includes(searchTerm);
                const matchesCategory = currentCategory === 'all' || itemCategory === currentCategory;
                
                if (matchesSearch && matchesCategory) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Category button clicks
        categoryButtons.forEach(button => {
            button.addEventListener('click', function() {
                categoryButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                const category = this.dataset.category;
                filterByCategory(category);
                searchInput.value = '';
            });
        });

        // Select all visible
        selectAllBtn.addEventListener('click', function() {
            analysisItems.forEach(item => {
                if (item.style.display !== 'none') {
                    const checkbox = item.querySelector('.analysis-checkbox');
                    checkbox.checked = true;
                }
            });
            updateSelectedCount();
        });

        // Deselect all visible
        deselectAllBtn.addEventListener('click', function() {
            analysisItems.forEach(item => {
                if (item.style.display !== 'none') {
                    const checkbox = item.querySelector('.analysis-checkbox');
                    checkbox.checked = false;
                }
            });
            updateSelectedCount();
        });

        // Update count on checkbox change
        document.querySelectorAll('.analysis-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });

        // Initial count update
        updateSelectedCount();
    });
</script>
@endsection
