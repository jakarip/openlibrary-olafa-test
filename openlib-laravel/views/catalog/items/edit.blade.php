@extends('layouts/layoutMaster')

@section('title', __('catalogs.koleksi.edit_copies') . ' - Stock Items')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}">
@endsection

@section('page-style')
    <style>
        .section-header {
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 12px 16px;
            border-radius: 8px 8px 0 0;
        }

        .section-body {
            padding: 16px;
        }

        .form-section {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .catalog-option-cover {
            width: 30px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 10px;
            flex-shrink: 0;
        }

        .catalog-option-content {
            flex: 1;
            min-width: 0;
        }

        .catalog-option-title {
            font-weight: 500;
            color: #333;
            font-size: 0.9rem;
            line-height: 1.2;
            margin-bottom: 2px;
        }

        .catalog-option-meta {
            font-size: 0.8rem;
            color: #666;
            line-height: 1.1;
        }

        .select2-results__option--catalog {
            display: flex !important;
            align-items: center;
            padding: 8px 12px !important;
        }

        .catalog-selected-content {
            display: flex;
            align-items: center;
        }

        .catalog-selected-cover {
            width: 20px;
            height: 25px;
            object-fit: cover;
            border-radius: 2px;
            margin-right: 8px;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loading-spinner {
            text-align: center;
        }

        .current-catalog-info {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 15px;
        }

        .current-catalog-cover {
            width: 60px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
        }
    </style>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
@endsection

@section('content')
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('catalog.items') }}">{{ __('catalogs.koleksi.copies_management') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ __('catalogs.koleksi.edit_copy') }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1"><i class="ti ti-edit me-2"></i>{{ __('catalogs.koleksi.edit_copy') }}</h5>
                        <p class="text-muted mb-0">{{ __('catalogs.koleksi.update_copies') }}</p>
                    </div>
                    <a href="{{ route('catalog.items') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left me-1"></i> {{ __('common.back') }}
                    </a>
                </div>

                <div class="card-body" id="formContainer" style="display: none;">
                    <form id="formStock" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{ $id }}">

                        <!-- Current Catalog Info -->
                        <div class="current-catalog-info" id="currentCatalogInfo" style="display: none;">
                            <h6 class="mb-2"><i class="ti ti-info-circle me-2"></i>{{ __('catalogs.koleksi.current_catalog') }}</h6>
                            <div class="d-flex align-items-center">
                                <img id="currentCatalogCover" src="" alt="Cover" class="current-catalog-cover me-3">
                                <div>
                                    <h6 class="mb-1" id="currentCatalogTitle">-</h6>
                                    <small class="text-muted">
                                    {{ __('config.classification.input.code') }}: <strong id="currentCatalogCode">-</strong><br>
                                    {{ __('catalogs.bahanpustaka_table_author') }}: <span id="currentCatalogAuthor">-</span>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Koleksi Section -->
                        <div class="form-section">
                            <div class="section-header">
                                <h6 class="mb-0"><i class="ti ti-book me-2"></i>{{ __('catalogs.koleksi') }}</h6>
                            </div>
                            <div class="section-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">{{ __('catalogs.katalog') }} <span class="text-danger">*</span></label>
                                        <select name="knowledge_item_id" class="form-select select2-catalog" required>
                                            <option value="">{{ __('catalogs.katalog.choose_katalog') }}</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('catalogs.katalog.copies_code') }}</label>
                                        <input type="text" name="code" class="form-control" readonly
                                            style="background-color: #f8f9fa;">
                                        <div class="form-text text-primary">
                                            <i class="ti ti-info-circle me-1"></i>
                                            {{ __('catalogs.koleksi.code_updated') }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">RFID</label>
                                        <input type="text" name="rfid" class="form-control"
                                            placeholder="Kode RFID (opsional)">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('catalogs.koleksi.condition') }} <span class="text-danger">*</span></label>
                                        <select name="status" class="form-select select2-basic" required>
                                            <option value="">{{ __('catalogs.koleksi.choose_condition') }}</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('catalogs.entry_date') }} <span class="text-danger">*</span></label>
                                        <input type="date" name="entrance_date" class="form-control" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Koleksi Section -->
                        <div class="form-section">
                            <div class="section-header">
                                <h6 class="mb-0"><i class="ti ti-info-circle me-2"></i>{{ __('catalogs.koleksi.collection_info') }}</h6>
                            </div>
                            <div class="section-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Unit <span class="text-danger">*</span></label>
                                        <select name="item_location_id" class="form-select select2-basic" required>
                                            <option value="">{{ __('common.choose') }} Unit</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">{{ __('catalogs.koleksi.receive_sourcse') }} <span class="text-danger">*</span></label>
                                        <select name="origination" class="form-select select2-basic" required>
                                            <option value="">{{ __('catalogs.select_source') }}</option>
                                            <option value="1">{{ __('catalogs.katalog.purchase') }}</option>
                                            <option value="2">{{ __('catalogs.katalog.donation') }}</option>
                                        </select>
                                        <div class="form-text">{{ __('catalogs.koleksi.select_source') }}</div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">{{ __('catalogs.tipe') }} <span class="text-danger">*</span></label>
                                        <select name="knowledge_type_id" class="form-select select2-basic" required>
                                            <option value="">{{ __('catalogs.katalog.choose_type') }}</option>
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('catalogs.katalog.supplier') }}</label>
                                        <input type="text" name="supplier" class="form-control"
                                            placeholder="{{ __('catalogs.katalog.supplier_name') }}">
                                        <div class="form-text">{{ __('catalogs.koleksi.require_purchase') }}</div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('catalogs.katalog.purchase_price') }}</label>
                                        <input type="number" name="price" class="form-control" min="0"
                                            placeholder="{{ __('catalogs.katalog.purchase_price') }}">
                                        <div class="form-text">{{ __('catalogs.koleksi.require_purchase') }}</div>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('catalogs.bahanpustaka_table_faculty') }}</label>
                                        <select name="{{ __('catalogs.koleksi.faculty_code') }}" class="form-select select2-basic">
                                            <option value="">{{ __('catalogs.katalog.choose_faculty') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('catalogs.bahanpustaka_table_studyprogram') }}</label>
                                        <select name="{{ __('catalogs.koleksi.course_code') }}" class="form-select select2-basic">
                                            <option value="">{{ __('catalogs.choose_study_program') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('catalog.items') }}" class="btn btn-outline-secondary">
                                        <i class="ti ti-arrow-left me-1"></i> {{ __('common.back') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-check me-1"></i> {{ __('catalogs.koleksi.update') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        const DEFAULT_COVER_PATH = '{{ asset("assets/img/default-book-cover.jpg") }}';
        const STOCK_ID = {{ $id }};
        let formOptions = {};
        let stockData = {};

        // Load data when document ready
        $(document).ready(function () {
            loadFormOptionsAndStockData();
        });

        // Load form options and stock data
        function loadFormOptionsAndStockData() {
            // Load form options first
            $.ajax({
                url: '{{ route("catalog.items") }}/get-form-options',
                type: 'GET',
                success: function (response) {
                    if (response.success) {
                        formOptions = response.data;
                        populateDropdowns();
                        // Then load stock data
                        loadStockData();
                    } else {
                        showError('Gagal memuat data dropdown: ' + response.message);
                    }
                },
                error: function (xhr) {
                    console.error('AJAX Error:', xhr);
                    showError('Gagal memuat data dropdown');
                }
            });
        }

        // Load stock data
        function loadStockData() {
            $.ajax({
                url: '{{ route("catalog.items") }}/edit',
                type: 'POST',
                data: { id: STOCK_ID },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        stockData = response.data;
                        populateFormWithData();
                        initializeComponents();
                        showForm();
                        hideLoading();
                    } else {
                        showError('Gagal memuat data eksemplar: ' + response.message);
                    }
                },
                error: function (xhr) {
                    console.error('Error loading stock data:', xhr);
                    showError(xhr.responseJSON?.message || 'Gagal memuat data eksemplar');
                }
            });
        }

        // Hide loading and show form
        function showForm() {
            $('#formContainer').show();
        }

        // Hide loading overlay
        function hideLoading() {
            $('#loadingOverlay').fadeOut();
        }

        // Show error message
        function showError(message) {
            hideLoading();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '{{ route("catalog.items") }}';
            });
        }

        // Populate dropdown options
        function populateDropdowns() {
            // Knowledge Types
            let typeOptions = '<option value="">Pilih Jenis</option>';
            if (formOptions.knowledge_types && formOptions.knowledge_types.length > 0) {
                formOptions.knowledge_types.forEach(function (type) {
                    typeOptions += `<option value="${type.id}">${type.name}</option>`;
                });
            }
            $('select[name="knowledge_type_id"]').html(typeOptions);

            // Locations
            let locationOptions = '<option value="">Pilih Unit</option>';
            if (formOptions.locations && formOptions.locations.length > 0) {
                formOptions.locations.forEach(function (location) {
                    locationOptions += `<option value="${location.id}">${location.name}</option>`;
                });
            }
            $('select[name="item_location_id"]').html(locationOptions);

            // Faculty
            let facultyOptions = '<option value=""></option>';
            if (formOptions.fakultas && formOptions.fakultas.length > 0) {
                formOptions.fakultas.forEach(function (fakultas) {
                    let displayName = fakultas.SINGKATAN ?
                        `${fakultas.SINGKATAN} - ${fakultas.NAMA_FAKULTAS}` :
                        fakultas.NAMA_FAKULTAS;
                    facultyOptions += `<option value="${fakultas.C_KODE_FAKULTAS}">${displayName}</option>`;
                });
            }
            $('select[name="faculty_code"]').html(facultyOptions);

            // Program Studi
            let prodiOptions = '<option value="">{{ __("catalogs.choose_study_program") }}</option>'; 
            if (formOptions.prodi && formOptions.prodi.length > 0) {
                formOptions.prodi.forEach(function (prodi) {
                    prodiOptions += `<option value="${prodi.C_KODE_PRODI}">${prodi.NAMA_PRODI}</option>`;
                });
            }
            $('select[name="course_code"]').html(prodiOptions);

            // Status Options
            let statusOptions = '<option value="">{{ __("catalogs.koleksi.choose_condition") }}</option>';
            if (formOptions.status_options) {
                Object.keys(formOptions.status_options).forEach(function (key) {
                    statusOptions += `<option value="${key}">${formOptions.status_options[key]}</option>`;
                });
            }
            $('select[name="status"]').html(statusOptions);
        }

        // Populate form with existing data
        function populateFormWithData() {
            // Populate basic fields
            Object.keys(stockData).forEach(function (key) {
                const $field = $('#formStock [name="' + key + '"]');
                if ($field.length && stockData[key] !== null && stockData[key] !== undefined) {
                    if ($field.is('select')) {
                        $field.val(stockData[key]);
                    } else {
                        $field.val(stockData[key]);
                    }
                }
            });

            // Show current catalog info
            if (stockData.knowledge_item) {
                const catalog = stockData.knowledge_item;
                const coverUrl = catalog.cover_url || DEFAULT_COVER_PATH;

                $('#currentCatalogCover').attr('src', coverUrl);
                $('#currentCatalogTitle').text(catalog.title || '-');
                $('#currentCatalogCode').text(catalog.code || '-');
                $('#currentCatalogAuthor').text(catalog.author || '-');
                $('#currentCatalogInfo').show();
            }
        }

        // Initialize components
        function initializeComponents() {
            // Initialize Select2
            $('.select2-basic').select2({
                width: '100%'
            });

            // Initialize catalog Select2 with AJAX
            initializeCatalogSelect2();

            // Set selected values after Select2 initialization
            setTimeout(() => {
                setSelectedValues();
            }, 100);

            // Bootstrap maxlength
            $('input[maxlength], textarea[maxlength]').maxlength();

            // Event handlers
            setupEventHandlers();
        }

        // Set selected values for Select2 fields
        function setSelectedValues() {
            // Set basic select values
            $('select[name="knowledge_type_id"]').val(stockData.knowledge_type_id).trigger('change');
            $('select[name="item_location_id"]').val(stockData.item_location_id).trigger('change');
            $('select[name="origination"]').val(stockData.origination).trigger('change');
            $('select[name="status"]').val(stockData.status).trigger('change');
            $('select[name="faculty_code"]').val(stockData.faculty_code || '').trigger('change');
            $('select[name="course_code"]').val(stockData.course_code || '').trigger('change');

            // Handle catalog selection
            if (stockData.knowledge_item) {
                const catalog = stockData.knowledge_item;
                const newOption = new Option(
                    `${catalog.code} - ${catalog.title}`,
                    stockData.knowledge_item_id,
                    true,
                    true
                );
                $('.select2-catalog').append(newOption).trigger('change');
            }
        }

        // Initialize catalog Select2 with AJAX
        function initializeCatalogSelect2() {
            $('.select2-catalog').select2({
                width: '100%',
                placeholder: 'Cari katalog...',
                minimumInputLength: 0,
                ajax: {
                    url: '{{ route("catalog.items") }}/search-catalogs',
                    dataType: 'json',
                    delay: 300,
                    data: function (params) {
                        return {
                            search: params.term,
                            limit: 20
                        };
                    },
                    processResults: function (data) {
                        if (data.success) {
                            return {
                                results: data.data.map(function (catalog) {
                                    return {
                                        id: catalog.id,
                                        text: `${catalog.code} - ${catalog.title}`,
                                        catalog: catalog
                                    };
                                })
                            };
                        }
                        return { results: [] };
                    },
                    cache: true
                },
                templateResult: formatCatalogOption,
                templateSelection: formatCatalogSelection,
                escapeMarkup: function (markup) { return markup; }
            });
        }

        // Format catalog option with cover image
        function formatCatalogOption(catalog) {
            if (!catalog.catalog) {
                return catalog.text;
            }

            const data = catalog.catalog;
            const coverUrl = data.cover_url || DEFAULT_COVER_PATH;
            const title = data.title.length > 40 ? data.title.substring(0, 40) + '...' : data.title;
            const author = data.author.length > 30 ? data.author.substring(0, 30) + '...' : data.author;

            return $(`
                            <div class="select2-results__option--catalog">
                                <img src="${coverUrl}" class="catalog-option-cover" alt="Cover" onerror="this.src='${DEFAULT_COVER_PATH}'">
                                <div class="catalog-option-content">
                                    <div class="catalog-option-title">${title}</div>
                                    <div class="catalog-option-meta">
                                        <strong>${data.code}</strong> • ${author}
                                    </div>
                                </div>
                            </div>
                        `);
        }

        // Format selected catalog
        function formatCatalogSelection(catalog) {
            if (!catalog.catalog) {
                return catalog.text;
            }

            const data = catalog.catalog;
            const coverUrl = data.cover_url || DEFAULT_COVER_PATH;

            return $(`
                            <div class="catalog-selected-content">
                                <img src="${coverUrl}" class="catalog-selected-cover" alt="Cover" onerror="this.src='${DEFAULT_COVER_PATH}'">
                                <span>${data.code} - ${data.title}</span>
                            </div>
                        `);
        }

        // Setup event handlers
        function setupEventHandlers() {
            // Auto-generate stock code when catalog is changed (not for initial load)
            $('.select2-catalog').on('change', function () {
                const catalogId = $(this).val();
                if (catalogId && catalogId != stockData.knowledge_item_id) {
                    const selectedData = $(this).select2('data')[0];
                    if (selectedData && selectedData.catalog) {
                        $('input[name="code"]').val(selectedData.catalog.code + '-XXX');
                    }
                }
            });

            // Auto-fill supplier and price requirement based on origination
            $('select[name="origination"]').on('change', function () {
                const value = $(this).val();
                if (value == '1') { // Pembelian
                    $('input[name="supplier"]').attr('required', true);
                    $('input[name="price"]').attr('required', true);
                    $('.form-text:contains("Wajib diisi untuk pembelian")').show();
                } else {
                    $('input[name="supplier"]').attr('required', false);
                    $('input[name="price"]').attr('required', false);
                    $('.form-text:contains("Wajib diisi untuk pembelian")').hide();
                }
            });

            // Form submission
            $('#formStock').on('submit', function (e) {
                e.preventDefault();
                submitForm();
            });
        }

        // Submit form
        function submitForm() {
            const submitBtn = $('#formStock button[type="submit"]');
            const originalText = submitBtn.html();

            // Show loading
            submitBtn.prop('disabled', true).html(`<i class="ti ti-loader ti-xs me-2"></i> Memperbarui...`);

            $.ajax({
                url: '{{ route("catalog.items") }}/update',
                method: 'POST',
                data: new FormData($('#formStock')[0]),
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __("common.success") }}!',
                            text: response.message,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = '{{ route("catalog.items") }}';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("common.failed") }}!',
                            text: response.message,
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function (xhr) {
                    let errorMessage = 'Gagal memperbarui eksemplar';
                    if (xhr.responseJSON?.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMessage = errors.join('<br>');
                    } else if (xhr.responseJSON?.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        html: errorMessage,
                        confirmButtonText: 'OK'
                    });
                },
                complete: function () {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        }
    </script>
@endsection