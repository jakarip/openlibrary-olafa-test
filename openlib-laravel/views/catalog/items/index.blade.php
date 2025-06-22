@extends('layouts/layoutMaster')

@section('title', __('catalogs.koleksi.copies_management') . ' - Stock Items')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}">
@endsection

@section('page-style')
    <style>
        .dropdown-menu.portal-open {
            z-index: 1060;
            position: absolute;
            display: block;
        }

        .my-btn-group {
            position: relative;
        }

        .modal .select2-container,
        .modal .flatpickr-calendar,
        .modal .dropdown-menu {
            z-index: 999999 !important;
        }

        .flatpickr-calendar {
            z-index: 999999 !important;
        }

        .filter-card .select2-container {
            z-index: 1 !important;
        }

        .filter-card .select2-container--open {
            z-index: 998 !important;
        }

        .select2-dropdown {
            z-index: 999 !important;
        }

        .catalog-cover {
            width: 45px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .catalog-cover:hover {
            transform: scale(1.15);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            z-index: 10;
            position: relative;
            border-color: #666eea;
        }

        .avatar-wrapper:hover .catalog-cover {
            transform: scale(1.15);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            z-index: 10;
            border-color: #666eea;
        }

        .stock-code {
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.875rem;
            font-weight: 600;
            color: #6f6b7d;
            background: #f8f9fa;
            padding: 4px 8px;
            border-radius: 4px;
            border: 1px solid #e9ecef;
        }

        .status-badge {
            font-size: 0.75rem;
            font-weight: 500;
            padding: 4px 8px;
            border-radius: 12px;
        }

        .stats-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .filter-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
        }

        #stockTable tbody tr {
            vertical-align: middle;
        }

        .dt-buttons {
            margin-bottom: 1rem;
        }

        .btn-outline-secondary {
            border-color: #d0d5dd;
            color: #667085;
        }

        .btn-outline-secondary:hover {
            background-color: #f2f4f7;
            border-color: #d0d5dd;
            color: #667085;
        }

        .select2-results__option--catalog {
            display: flex !important;
            align-items: center;
            padding: 8px 12px !important;
        }

        .catalog-option-cover {
            width: 30px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 10px;
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }

        .catalog-option-cover:hover {
            transform: scale(1.05);
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

        .stats-loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #666eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .select2-results__option--loading {
            display: flex !important;
            align-items: center;
            justify-content: center;
            padding: 12px !important;
        }

        .select2-search__field:focus {
            border-color: #666eea !important;
        }

        .search-delay-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            color: #666;
            font-size: 0.85rem;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 768px) {
            .catalog-cover {
                width: 35px;
                height: 50px;
            }

            .catalog-cover:hover,
            .avatar-wrapper:hover .catalog-cover {
                transform: scale(1.1);
            }
        }
    </style>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
@endsection

@section('content')
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg me-3">
                        <span class="avatar-initial rounded bg-label-success">
                            <i class="ti ti-package ti-md"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="card-title mb-1" id="totalStock">
                            <div class="stats-loading"></div>
                        </h5>
                        <p class="card-text text-muted mb-0">{{ __('catalogs.koleksi.total_copies') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg me-3">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="ti ti-check ti-md"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="card-title mb-1" id="availableStock">
                            <div class="stats-loading"></div>
                        </h5>
                        <p class="card-text text-muted mb-0">{{ __('catalogs.avail') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg me-3">
                        <span class="avatar-initial rounded bg-label-warning">
                            <i class="ti ti-clock ti-md"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="card-title mb-1" id="borrowedStock">
                            <div class="stats-loading"></div>
                        </h5>
                        <p class="card-text text-muted mb-0">{{ __('catalogs.borrowed') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stats-card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="avatar avatar-lg me-3">
                        <span class="avatar-initial rounded bg-label-danger">
                            <i class="ti ti-alert-circle ti-md"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="card-title mb-1" id="damagedStock">
                            <div class="stats-loading"></div>
                        </h5>
                        <p class="card-text text-muted mb-0">{{ __('catalogs.damaged') }}/{{ __('catalogs.lost') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1"><i class="ti ti-package me-2"></i>{{ __('catalogs.koleksi.copies_management') }}</h5>
                <p class="text-muted mb-0">{{ __('catalogs.koleksi.manage_collection') }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('catalog.items.add') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> {{ __('catalogs.koleksi.add_copies') }}
                </a>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="ti ti-settings me-1"></i> {{ __('common.action_button') }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="bulkUpdateStatus()">
                                <i class="ti ti-edit me-2"></i>{{ __('common.update') }} Status</a></li>
                        <li><a class="dropdown-item" href="#" onclick="transferLocation()">
                                <i class="ti ti-map-pin me-2"></i>{{ __('catalogs.koleksi.change_location') }}</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#columnSettingsModal">
                                <i class="ti ti-columns me-2"></i>{{ __('catalogs.koleksi.display_column') }}</button></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="card-body pt-0">
            <!-- Filters -->
            <div class="filter-card p-3 mb-3">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">{{ __('catalogs.filter_catalog') }}</label>
                        <select id="catalogFilter" class="form-select select2-filter">
                            <option value="">{{ __('catalogs.koleksi.all_catalog') }}</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label">{{ __('catalogs.type_filter') }}</label>
                        <select id="typeFilter" class="form-select select2-filter">
                            <option value="">{{ __('catalogs.all_types') }}<</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label">{{ __('config.holiday.filter_location') }}</label>
                        <select id="locationFilter" class="form-select select2-filter">
                            <option value="">{{ __('catalogs.all_location') }}<</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label">{{ __('catalogs.filter_status') }}</label>
                        <select id="statusFilter" class="form-select select2-filter">
                            <option value="">{{ __('catalogs.all_status') }}<</option>
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-12">
                        <label class="form-label">{{ __('catalogs.filter_date') }}</label>
                        <div class="input-group">
                            <input type="date" id="dateFrom" class="form-control">
                            <span class="input-group-text">s/d</span>
                            <input type="date" id="dateTo" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="button" class="btn btn-outline-secondary me-2" onclick="resetFilters()">
                            <i class="ti ti-refresh me-1"></i> {{ __('catalogs.reset_filter') }}
                        </button>
                        <button type="button" class="btn btn-primary" onclick="applyFilters()">
                            <i class="ti ti-search me-1"></i> {{ __('catalogs.filter_status') }} Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-datatable table-responsive overflow-auto pt-0">
            <table class="datatables-basic table border-top" id="stockTable">
                <thead>
                    <tr>
                        <th width="5%">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                            </div>
                        </th>
                        <th width="8%">{{ __('common.action') }}</th>
                        <th>{{ __('catalogs.katalog') }}</th>
                        <th>{{ __('catalogs.katalog.copies_code') }}</th>
                        <th>Status</th>
                        <th>{{ __('catalogs.tipe') }}</th>
                        <th>{{ __('config.holiday.location') }}</th>
                        <th>{{ __('catalogs.entry_date') }}</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- Column Settings Modal -->
    <div class="modal fade" id="columnSettingsModal" tabindex="-1" aria-labelledby="columnSettingsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ti ti-columns me-2"></i> {{ __('config.member.select_additional_data') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="columnSettingsForm">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="selectAllColumns">
                            <label class="form-check-label fw-bold" for="selectAllColumns">{{ __('config.member.select_all') }}</label>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input column-checkbox" type="checkbox" value="rfid" id="rfid">
                                    <label class="form-check-label" for="rfid">RFID</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input column-checkbox" type="checkbox" value="supplier"
                                        id="supplier">
                                    <label class="form-check-label" for="supplier">{{ __('catalogs.supplier') }}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input column-checkbox" type="checkbox" value="price"
                                        id="price">
                                    <label class="form-check-label" for="price">{{ __('catalogs.price') }}</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input column-checkbox" type="checkbox" value="created_at"
                                        id="createdAt">
                                    <label class="form-check-label" for="createdAt">{{ __('config.holiday.createdAt') }}</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i> {{ __('common.cancel') }}
                    </button>
                    <button type="button" class="btn btn-primary" id="applyColumns">
                        <i class="ti ti-check me-1"></i> {{ __('common.apply') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Update Status Modal -->
    <div class="modal fade" id="modalBulkStatus" tabindex="-1" aria-labelledby="modalBulkStatusLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ti ti-edit me-2"></i> {{ __('catalogs.koleksi.update_status') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formBulkStatus">
                        <div class="mb-3">
                            <label class="form-label">{{ __('catalogs.koleksi.new_status') }}<span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="">{{ __('common.select_status') }}</option>
                            </select>
                        </div>
                        <p class="text-muted">
                            <i class="ti ti-info-circle me-1"></i>
                            {{ __('catalogs.koleksi.status_apply') }} <span id="selectedCount">0</span> {{ __('catalogs.koleksi.chosen_copy') }}
                        </p>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="btnBulkStatus">{{ __('common.update') }} Status</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Transfer Location Modal -->
    <div class="modal fade" id="modalTransferLocation" tabindex="-1" aria-labelledby="modalTransferLocationLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ti ti-map-pin me-2"></i> {{ __('catalogs.koleksi.change_location') }} 
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formTransferLocation">
                        <div class="mb-3">
                            <label class="form-label">{{ __('catalogs.koleksi.new_location') }} <span class="text-danger">*</span></label>
                            <select name="new_location_id" class="form-select" required>
                                <option value="">{{ __('common.select_location') }}</option>
                            </select>
                        </div>
                        <p class="text-muted">
                            <i class="ti ti-info-circle me-1"></i>
                            {{ __('catalogs.koleksi.location_apply') }} <span id="selectedCountLocation">0</span> {{ __('catalogs.koleksi.chosen_copy') }}
                        </p>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="btnTransferLocation">Pindah Lokasi</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        const DEFAULT_COVER_PATH = '{{ asset("assets/img/default-book-cover.jpg") }}';

        // Global variables
        let stockTable = null;
        let selectedColumns = [];
        let formOptions = {};

        // Default columns for DataTable
        const defaultColumns = [
            {
                data: 'checkbox',
                name: 'checkbox',
                orderable: false,
                searchable: false,
                class: 'text-center',
                render: function (data, type, row) {
                    return '<div class="form-check"><input class="form-check-input row-checkbox" type="checkbox" value="' + row.id + '"></div>';
                }
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                class: 'text-center'
            },
            { data: 'catalog_info', name: 'catalog_info', title: '{{ __("catalogs.katalog") }}', orderable: false, searchable: false },
            { data: 'stock_code', name: 'code', title: '{{ __("catalogs.katalog.copies_code") }}' },
            { data: 'status_badge', name: 'status', title: 'Status', orderable: false },
            { data: 'type_info', name: 'type_info', title: '{{ __("catalogs.tipe") }}', orderable: false },
            { data: 'location_info', name: 'location_info', title: '{{ __("catalogs.holiday.location") }}', orderable: false },
            { data: 'entrance_date', name: 'entrance_date', title: '{{ __("catalogs.entry_date") }}' }
        ];

        // Additional columns mapping
        const additionalColumns = {
            rfid: { data: 'rfid', name: 'rfid', title: 'RFID' },
            supplier: { data: 'supplier', name: 'supplier', title: 'Pemasok' },
            price: { data: 'price_formatted', name: 'price', title: 'Harga' },
            created_at: { data: 'created_at', name: 'created_at', title: 'Dibuat Pada' }
        };

        // Handle dropdown toggles
        $(document).on('click', '.my-dropdown-toggle', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const $btn = $(this);
            const $btnGroup = $btn.closest('.my-btn-group');
            const $menu = $btnGroup.find('.dropdown-menu').first();

            if ($menu.hasClass('portal-open')) {
                closePortalDropdown($menu);
                return;
            }

            $('.dropdown-menu.portal-open').each(function () {
                closePortalDropdown($(this));
            });

            openPortalDropdown($btn, $menu);
        });

        function openPortalDropdown($btn, $menu) {
            if (!$menu.data('original-parent')) {
                $menu.data('original-parent', $menu.parent());
            }

            $('body').append($menu);

            const rect = $btn[0].getBoundingClientRect();

            $menu.css({
                position: 'absolute',
                top: (rect.bottom + window.scrollY) + 'px',
                left: (rect.left + window.scrollX) + 'px',
                display: 'block',
                zIndex: 9999
            }).addClass('portal-open');

            $(document).on('click.portalDropdown', function (ev) {
                if (!$(ev.target).closest($menu).length && ev.target !== $btn[0]) {
                    closePortalDropdown($menu);
                    $(document).off('click.portalDropdown');
                }
            });
        }

        function closePortalDropdown($menu) {
            $menu.removeClass('portal-open').hide();
            const originalParent = $menu.data('original-parent');
            if (originalParent) {
                originalParent.append($menu);
            }
            $(document).off('click.portalDropdown');
        }

        // Load form options
        function loadFormOptions() {
            $.ajax({
                url: '{{ route("catalog.items") }}/get-form-options',
                type: 'GET',
                success: function (response) {
                    if (response.success) {
                        formOptions = response.data;
                        populateFilters();
                        loadStatistics();
                    } else {
                        toastr.error('Gagal memuat data dropdown: ' + response.message);
                    }
                },
                error: function (xhr) {
                    console.error('AJAX Error:', xhr);
                    toastr.error('Gagal memuat data dropdown');
                }
            });
        }

        // Load statistics from database
        function loadStatistics() {
            $.ajax({
                url: '{{ route("catalog.items") }}/statistics',
                type: 'GET',
                success: function (response) {
                    if (response.success) {
                        const stats = response.data;

                        $('#totalStock').html(animateNumber(stats.total_stock));
                        $('#availableStock').html(animateNumber(stats.available_stock));
                        $('#borrowedStock').html(animateNumber(stats.borrowed_stock));
                        $('#damagedStock').html(animateNumber(stats.damaged_stock));
                    } else {
                        console.error('Failed to load statistics:', response.message);
                        $('#totalStock').text('0');
                        $('#availableStock').text('0');
                        $('#borrowedStock').text('0');
                        $('#damagedStock').text('0');
                    }
                },
                error: function (xhr) {
                    console.error('Error loading statistics:', xhr);
                    $('#totalStock').text('0');
                    $('#availableStock').text('0');
                    $('#borrowedStock').text('0');
                    $('#damagedStock').text('0');
                }
            });
        }

        // Animate number counting effect
        function animateNumber(finalNumber) {
            const duration = 1000;
            const steps = 20;
            const increment = finalNumber / steps;
            let current = 0;
            let step = 0;

            const $element = $('<span>0</span>');

            const timer = setInterval(function () {
                step++;
                current = Math.min(Math.round(increment * step), finalNumber);
                $element.text(current.toLocaleString());

                if (step >= steps || current >= finalNumber) {
                    clearInterval(timer);
                    $element.text(finalNumber.toLocaleString());
                }
            }, duration / steps);

            return $element;
        }

        // Enhanced catalog filter dengan search delay
        function initializeCatalogFilter() {
            $('#catalogFilter').select2({
                width: '100%',
                placeholder: '{{ __("catalogs.koleksi.find_catalog") }}...',
                allowClear: true,
                minimumInputLength: 0,
                dropdownParent: $('body'),
                ajax: {
                    url: '{{ route("catalog.items") }}/search-catalogs',
                    dataType: 'json',
                    delay: 500,
                    data: function (params) {
                        return {
                            search: params.term,
                            limit: 15
                        };
                    },
                    processResults: function (data) {
                        if (data.success) {
                            return {
                                results: data.data.map(function (catalog) {
                                    return {
                                        id: catalog.id,
                                        text: `${catalog.code} - ${catalog.title.substring(0, 50)}${catalog.title.length > 50 ? '...' : ''}`,
                                        catalog: catalog
                                    };
                                })
                            };
                        }
                        return { results: [] };
                    },
                    cache: true
                },
                templateResult: formatCatalogFilterOption,
                templateSelection: formatCatalogFilterSelection,
                escapeMarkup: function (markup) { return markup; },
                language: {
                    inputTooShort: function () {
                        return '{{ __("catalogs.koleksi.find_catalog_2") }}...';
                    },
                    searching: function () {
                        return '<div class="search-delay-indicator"><i class="ti ti-search me-2"></i>{{ __("catalogs.koleksi.find_catalog") }}...</div>';
                    },
                    noResults: function () {
                        return '{{ __("catalogs.koleksi.catalog_not_found") }}';
                    }
                }
            });
        }

        // Format catalog filter option
        function formatCatalogFilterOption(catalog) {
            if (!catalog.catalog) {
                return catalog.text;
            }

            const data = catalog.catalog;
            const coverUrl = data.cover_url || DEFAULT_COVER_PATH;
            const title = data.title.length > 35 ? data.title.substring(0, 35) + '...' : data.title;
            const author = data.author.length > 25 ? data.author.substring(0, 25) + '...' : data.author;

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

        // Format selected catalog for filter
        function formatCatalogFilterSelection(catalog) {
            if (!catalog.catalog) {
                return catalog.text;
            }

            const data = catalog.catalog;
            const coverUrl = data.cover_url || DEFAULT_COVER_PATH;

            return $(`
                    <div class="catalog-selected-content">
                        <img src="${coverUrl}" class="catalog-selected-cover" alt="Cover" onerror="this.src='${DEFAULT_COVER_PATH}'">
                        <span>${data.code} - ${data.title.substring(0, 40)}${data.title.length > 40 ? '...' : ''}</span>
                    </div>
                `);
        }

        // Populate filter dropdowns
        function populateFilters() {
            // Type filter
            let typeFilterOptions = '<option value="">{{ __("catalogs.all_type") }}</option>';
            if (formOptions.knowledge_types && formOptions.knowledge_types.length > 0) {
                formOptions.knowledge_types.forEach(function (type) {
                    typeFilterOptions += `<option value="${type.id}">${type.name}</option>`;
                });
            }
            $('#typeFilter').html(typeFilterOptions);

            // Location filter
            let locationFilterOptions = '<option value="">{{ __("catalogs.all_location") }}</option>';
            if (formOptions.locations && formOptions.locations.length > 0) {
                formOptions.locations.forEach(function (location) {
                    locationFilterOptions += `<option value="${location.id}">${location.name}</option>`;
                });
            }
            $('#locationFilter').html(locationFilterOptions);

            // Status filter
            let statusFilterOptions = '<option value="">{{ __("catalogs.all_status") }}</option>';
            if (formOptions.status_options) {
                Object.keys(formOptions.status_options).forEach(function (key) {
                    statusFilterOptions += `<option value="${key}">${formOptions.status_options[key]}</option>`;
                });
            }
            $('#statusFilter').html(statusFilterOptions);

            // Populate bulk modals
            let statusOptions = '<option value="">{{ __("catalogs.select_status") }}</option>';
            if (formOptions.status_options) {
                Object.keys(formOptions.status_options).forEach(function (key) {
                    statusOptions += `<option value="${key}">${formOptions.status_options[key]}</option>`;
                });
            }
            $('#modalBulkStatus select[name="status"]').html(statusOptions);

            let locationOptions = '<option value="">{{ __("catalogs.select_location") }}</option>';
            if (formOptions.locations && formOptions.locations.length > 0) {
                formOptions.locations.forEach(function (location) {
                    locationOptions += `<option value="${location.id}">${location.name}</option>`;
                });
            }
            $('#modalTransferLocation select[name="new_location_id"]').html(locationOptions);
        }

        // Update table header
        function updateTableHeader() {
            let headerHtml = `
                    <th width="5%">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAll">
                        </div>
                    </th>
                    <th width="8%">Action</th>
                    <th>{{ __('catalogs.katalog') }}</th>
                    <th>{{ __('catalogs.katalog.copies_code') }}</th>
                    <th>Status</th>
                    <th>{{ __('catalogs.tipe') }}</th>
                    <th>{{ __('config.holiday.location') }}</th>
                    <th>{{ __('catalogs.entry_date') }}</th>
                `;

            selectedColumns.forEach(key => {
                if (additionalColumns[key]) {
                    headerHtml += `<th>${additionalColumns[key].title}</th>`;
                }
            });

            $('#stockTable thead tr').html(headerHtml);
        }

        // Initialize DataTable
        function initDataTable() {
            if ($.fn.DataTable.isDataTable('#stockTable')) {
                $('#stockTable').DataTable().clear().destroy();
            }

            updateTableHeader();

            let columnsDefinition = [
                {
                    data: 'checkbox',
                    name: 'checkbox',
                    orderable: false,
                    searchable: false,
                    class: 'text-center',
                    render: function (data, type, row) {
                        return '<div class="form-check"><input class="form-check-input row-checkbox" type="checkbox" value="' + row.id + '"></div>';
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    class: 'text-center'
                },
                {
                    data: 'catalog_info',
                    name: 'catalog_title',
                    title: '{{ __("catalogs.katalog") }}',
                    orderable: false,
                    searchable: true
                },
                {
                    data: 'stock_code',
                    name: 'code',
                    title: '{{ __("catalogs.katalog.copies_code") }}',
                    searchable: true
                },
                {
                    data: 'status_badge',
                    name: 'status',
                    title: 'Status',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'type_info',
                    name: 'type_name',
                    title: '{{ __("catalogs.tipe") }}',
                    orderable: false,
                    searchable: true
                },
                {
                    data: 'location_info',
                    name: 'location_name',
                    title: '{{ __("config.holiday.location") }}',
                    orderable: false,
                    searchable: true
                },
                {
                    data: 'entrance_date',
                    name: 'entrance_date',
                    title: '{{ __("catalogs.entry_date") }}',
                    searchable: true
                }
            ];

            // Add additional columns
            selectedColumns.forEach(key => {
                if (additionalColumns[key]) {
                    let column = { ...additionalColumns[key] };
                    column.searchable = true;
                    columnsDefinition.push(column);
                }
            });

            stockTable = $('#stockTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                responsive: false,
                autoWidth: false,
                searching: true,
                ajax: {
                    url: '{{ route("catalog.items") }}/dt',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {
                        d.selected_columns = selectedColumns;
                        d.knowledge_item_id = $('#catalogFilter').val();
                        d.knowledge_type_id = $('#typeFilter').val();
                        d.item_location_id = $('#locationFilter').val();
                        d.status = $('#statusFilter').val();
                        d.date_from = $('#dateFrom').val();
                        d.date_to = $('#dateTo').val();
                    },
                    error: function (xhr, error, thrown) {
                        console.error('DataTables AJAX Error:', xhr.responseText);
                        toastr.error('Gagal memuat data: ' + (xhr.responseJSON?.message || error));
                    }
                },
                columns: columnsDefinition,
                order: [[3, 'asc']],
                searchDelay: 350,
                search: {
                    return: true
                },
                language: {
                    processing: 'Memproses...',
                    search: 'Cari:',
                    searchPlaceholder: 'Ketik untuk mencari...',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                    infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
                    infoFiltered: '(disaring dari _MAX_ data keseluruhan)',
                    loadingRecords: 'Memuat...',
                    zeroRecords: 'Tidak ditemukan data yang sesuai',
                    emptyTable: 'Tidak ada data tersedia',
                    paginate: {
                        first: 'Pertama',
                        previous: 'Sebelumnya',
                        next: 'Selanjutnya',
                        last: 'Terakhir'
                    }
                },
                drawCallback: function (settings) {
                    $('[data-bs-toggle="tooltip"]').tooltip();
                },
                initComplete: function (settings, json) {
                    $('.dataTables_filter input').addClass('form-control').attr('placeholder', 'Cari katalog, kode eksemplar, atau detail lainnya...');
                }
            });
        }

        // UPDATED: Action button handlers - sekarang menggunakan direct links dari controller
        // View dan Edit button sudah menjadi direct links di generateActionButtons()
        // Hanya perlu handle untuk close dropdown saat diklik
        $(document).on('click', '.view-btn, .edit-btn', function () {
            $('.dropdown-menu').hide();
        });

        // Delete button handler tetap menggunakan AJAX
        $(document).on('click', '.delete-btn', function () {
            const id = $(this).data('id');
            $('.dropdown-menu').hide();

            Swal.fire({
                title: '{{ __("common..confirmation") }}',
                text: "{{ __('catalogs.koleksi.delete_copy') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '{{ __("common.delete_data") }}',
                cancelButtonText: '{{ __("common.cancel) }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("catalog.items") }}/delete',
                        type: 'DELETE',
                        data: { id: id },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (response.success) {
                                toastr.success(response.message);
                                stockTable.ajax.reload();
                                loadStatistics();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function (xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Gagal menghapus eksemplar');
                        }
                    });
                }
            });
        });

        // Filter functions
        function applyFilters() {
            stockTable.ajax.reload();
        }

        function resetFilters() {
            $('#catalogFilter').val('').trigger('change');
            $('#typeFilter').val('').trigger('change');
            $('#locationFilter').val('').trigger('change');
            $('#statusFilter').val('').trigger('change');
            $('#dateFrom').val('');
            $('#dateTo').val('');
            stockTable.ajax.reload();
        }

        // Bulk operations
        function bulkUpdateStatus() {
            const selectedIds = getSelectedIds();
            if (selectedIds.length === 0) {
                toastr.warning('{{ __("catalogs.koleksi.one_copy_update") }}');
                return;
            }
            $('#selectedCount').text(selectedIds.length);
            $('#modalBulkStatus').modal('show');
        }

        function transferLocation() {
            const selectedIds = getSelectedIds();
            if (selectedIds.length === 0) {
                toastr.warning('{{ __("catalogs.koleksi.one_copy_move") }}');
                return;
            }
            $('#selectedCountLocation').text(selectedIds.length);
            $('#modalTransferLocation').modal('show');
        }

        function getSelectedIds() {
            return $('.row-checkbox:checked').map(function () {
                return $(this).val();
            }).get();
        }

        // Bulk status update
        $('#btnBulkStatus').on('click', function () {
            const selectedIds = getSelectedIds();
            const status = $('#modalBulkStatus select[name="status"]').val();

            if (!status) {
                toastr.error('{{ __("catalogs.koleksi.select_status_apply") }}');
                return;
            }

            $.ajax({
                url: '{{ route("catalog.items") }}/bulk-update-status',
                type: 'POST',
                data: {
                    stock_ids: selectedIds,
                    status: status
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#modalBulkStatus').modal('hide');
                        stockTable.ajax.reload();
                        loadStatistics();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Gagal update status');
                }
            });
        });

        // Transfer location
        $('#btnTransferLocation').on('click', function () {
            const selectedIds = getSelectedIds();
            const locationId = $('#modalTransferLocation select[name="new_location_id"]').val();

            if (!locationId) {
                toastr.error('{{ __("common.select_location) }}');
                return;
            }

            $.ajax({
                url: '{{ route("catalog.items") }}/transfer-location',
                type: 'POST',
                data: {
                    stock_ids: selectedIds,
                    new_location_id: locationId
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#modalTransferLocation').modal('hide');
                        stockTable.ajax.reload();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Gagal pindah lokasi');
                }
            });
        });

        // Select all checkbox
        $(document).on('change', '#selectAll', function () {
            $('.row-checkbox').prop('checked', $(this).prop('checked'));
        });

        // Check if all rows are selected
        $(document).on('change', '.row-checkbox', function () {
            const total = $('.row-checkbox').length;
            const checked = $('.row-checkbox:checked').length;
            $('#selectAll').prop('checked', total === checked);
        });

        // Column settings
        $('#selectAllColumns').on('change', function () {
            $('.column-checkbox').prop('checked', $(this).is(':checked'));
        });

        $('#applyColumns').on('click', function () {
            selectedColumns = $('.column-checkbox:checked').map(function () {
                return $(this).val();
            }).get();

            initDataTable();
            $('#columnSettingsModal').modal('hide');
        });

        // Initialize when document ready
        $(document).ready(function () {
            loadFormOptions();
            initDataTable();

            $('.select2-filter:not(#catalogFilter)').select2({
                width: '100%',
                dropdownParent: $('body')
            });

            initializeCatalogFilter();

            let filterTimeout;
            $('#catalogFilter, #typeFilter, #locationFilter, #statusFilter').on('change', function () {
                clearTimeout(filterTimeout);
                filterTimeout = setTimeout(function () {
                    stockTable.ajax.reload();
                }, 100);
            });

            $('#dateFrom, #dateTo').on('change', function () {
                clearTimeout(filterTimeout);
                filterTimeout = setTimeout(function () {
                    stockTable.ajax.reload();
                }, 100);
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const dropdownElements = document.querySelectorAll('.dropdown-toggle');
            dropdownElements.forEach(function (dropdown) {
                new bootstrap.Dropdown(dropdown);
            });
        });
    </script>
@endsection