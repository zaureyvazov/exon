@extends('layouts.app')

@section('title', 'Göndərişi Düzəlt')

@section('nav-menu')
    <li class="nav-item"><a class="nav-link" href="{{ route('registrar.dashboard') }}">Ana Səhifə</a></li>
    <li class="nav-item"><a class="nav-link active" href="{{ route('registrar.referrals') }}">Göndərişlər</a></li>
@endsection

@section('content')
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h1 class="h3 fw-bold mb-1">
                    <i class="bi bi-pencil-square text-warning"></i> Göndərişi Düzəlt #{{ $referral->id }}
                </h1>
                <div class="text-muted">Doktorun səhvini düzəltmək üçün analiz əlavə edin və ya silin</div>
            </div>
            <a href="{{ route('registrar.referrals.show', $referral->id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Ləğv et
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <!-- Xəstə Məlumatları Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="bi bi-person-circle text-primary"></i> Xəstə Məlumatları
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded">
                                <small class="text-muted d-block">Xəstə</small>
                                <div class="fw-semibold">{{ $referral->patient->full_name }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded">
                                <small class="text-muted d-block">Seriya №</small>
                                <div class="fw-semibold">{{ $referral->patient->serial_number ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded">
                                <small class="text-muted d-block">Doktor</small>
                                <div class="fw-semibold">Dr. {{ $referral->doctor->name }} {{ $referral->doctor->surname }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="bi bi-info-circle"></i> Xəstə və doktor məlumatları dəyişdirilə bilməz. Yalnız analizlər və qeydlər dəyişdirilə bilər.
                    </div>
                </div>
            </div>

            <!-- Edit Form -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('registrar.referrals.update', $referral->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <label class="form-label fw-semibold mb-0">
                                <i class="bi bi-clipboard-pulse text-success"></i> Analizləri Düzəlt *
                            </label>
                            <span id="selectedCount" class="badge bg-primary-subtle text-primary border border-primary-subtle"></span>
                        </div>
                        @error('analyses')
                            <div class="alert alert-danger">{{ $message }}</div>
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
                                                                <span class="badge bg-info-subtle text-info border border-info">
                                                                    {{ number_format($analysis->price, 2) }} AZN
                                                                </span>
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
                            <label class="form-label fw-semibold" for="notes">
                                <i class="bi bi-chat-left-text"></i> Qeydlər (İstəyə bağlı)
                            </label>
                            <textarea
                                id="notes"
                                name="notes"
                                class="form-control @error('notes') is-invalid @enderror"
                                rows="3"
                                placeholder="Düzəliş səbəbi və ya əlavə qeydlər..."
                            >{{ old('notes', $referral->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <a href="{{ route('registrar.referrals.show', $referral->id) }}" class="btn btn-secondary">
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
