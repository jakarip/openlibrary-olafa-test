@extends('layouts/layoutMaster')

@section('title', __('catalogs.katalog.title'))

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

        /* TAMBAH STYLE UNTUK COVER IMAGE */
        .catalog-cover {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #e0e0e0;
            transition: transform 0.2s ease;
        }

        .catalog-cover:hover {
            transform: scale(1.05);
        }

        /* Loading state untuk image */
        .catalog-cover.loading {
            background: #f8f9fa url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiByeD0iNCIgZmlsbD0iI0Y1RjVGNSIvPgo8L3N2Zz4=') center no-repeat;
        }

        /* Style untuk DataTable row */
        #catalogTable tbody tr {
            vertical-align: middle;
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
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="ti ti-books me-2"></i>{{ __('catalogs.data') }} </h5>
            <div class="d-flex gap-2">
                <a href="{{ route('catalog.add') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i> {{ __('catalogs.katalog.add') }}
                </a>
                <button class="btn btn-label-primary" data-bs-toggle="modal" data-bs-target="#columnSettingsModal">
                    <i class="ti ti-settings"></i> {{ __('config.member.select_additional_data') }}
                </button>
            </div>
        </div>

        <div class="card-body pt-0">
            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">{{ __('catalogs.type_filter') }} </label>
                    <select id="typeFilter" class="form-select select2-filter">
                        <option value="">{{ __('catalogs.all_type') }} </option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('config.holiday.filter_location') }}</label>
                    <select id="locationFilter" class="form-select select2-filter">
                        <option value="">{{ __('catalogs.all_location') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('catalogs.filter_faculty') }}</label>
                    <select id="facultyFilter" class="form-select select2-filter">
                        <option value="">{{ __('catalogs.all_faculty') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('catalogs.filter_year') }}</label>
                    <select id="yearFilter" class="form-select select2-filter">
                        <option value="">{{ __('catalogs.all_year') }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card-datatable table-responsive overflow-auto pt-0">
            <table class="datatables-basic table border-top" id="catalogTable">
                <thead>
                    <tr>
                        <th width="8%">{{ __('common.action') }}</th>
                        <th>{{ __('config.file_type.input.title') }}</th>
                        <th>{{ __('config.classification.input.code') }}</th>
                        <th>{{ __('catalogs.stock_table_author') }}</th>
                        <th>{{ __('catalogs.tipe') }}</th>
                        <th>{{ __('config.classification.input.subject') }}</th>
                        <th>{{ __('config.holiday.location') }}</th>
                        <th>{{ __('config.stock') }}</th>
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
                <div class="modal-header bg-light">
                    <h5 class="modal-title"><i class="ti ti-settings me-2"></i>
                        {{ __('config.member.select_additional_data') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="columnSettingsForm">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="selectAllColumns">
                            <label class="form-check-label fw-bold"
                                for="selectAllColumns">{{ __('config.member.select_all') }}</label>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input column-checkbox" type="checkbox" value="isbn" id="isbn">
                                    <label class="form-check-label" for="isbn">ISBN</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input column-checkbox" type="checkbox" value="published_year"
                                        id="publishedYear">
                                    <label class="form-check-label"
                                        for="publishedYear">{{ __('catalogs.bahanpustaka_table_publishyear') }}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input column-checkbox" type="checkbox" value="publisher_name"
                                        id="publisherName">
                                    <label class="form-check-label"
                                        for="publisherName">{{ __('catalogs.publisher') }}</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input column-checkbox" type="checkbox" value="price"
                                        id="price">
                                    <label class="form-check-label" for="price">{{ __('catalogs.price') }}</label>
                                </div>
                                <!-- PERBAIKAN: Pastikan checkbox created_at dan updated_at ada -->
                                <div class="form-check">
                                    <input class="form-check-input column-checkbox" type="checkbox" value="created_at"
                                        id="createdAt">
                                    <label class="form-check-label"
                                        for="createdAt">{{ __('config.holiday.createdAt') }}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input column-checkbox" type="checkbox" value="updated_at"
                                        id="updatedAt">
                                    <label class="form-check-label" for="updatedAt">{{ __('common.updated_at') }}</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i> {{ __('common.cancel') }}
                    </button>
                    <button type="button" class="btn btn-primary" id="applyColumns">
                        <i class="ti ti-check me-1"></i> {{ __('common.apply') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        const DEFAULT_COVER_PATH = '{{ asset("assets/img/default-book-cover.jpg") }}';

        // Function untuk handle image error secara global
        function handleImageError(img) {
            if (img.src !== DEFAULT_COVER_PATH) {
                img.onerror = null; // Prevent infinite loop
                img.src = DEFAULT_COVER_PATH;
                img.alt = 'Default Cover';
                console.log('Image failed to load, using default cover');
            }
        }

        // Global event listener untuk semua image error
        $(document).on('error', 'img.catalog-cover', function () {
            handleImageError(this);
        });

        // Function untuk create image dengan fallback
        function createCoverImage(coverUrl, altText, className = 'catalog-cover') {
            const img = document.createElement('img');
            img.className = className;
            img.alt = altText || 'Cover';
            img.style.cssText = 'width: 40px; height: 40px; object-fit: cover; border-radius: 4px;';

            // Set default src first
            img.src = DEFAULT_COVER_PATH;

            // Try to load actual cover if provided
            if (coverUrl && coverUrl !== DEFAULT_COVER_PATH) {
                const tempImg = new Image();
                tempImg.onload = function () {
                    img.src = coverUrl;
                };
                tempImg.onerror = function () {
                    console.log('Cover failed to load:', coverUrl);
                    // Keep default image
                };
                tempImg.src = coverUrl;
            }

            return img;
        }

        // Global variables
        let catalogTable = null;
        let selectedColumns = [];
        let formOptions = {};

        // Default columns for DataTable - TAMBAH SLUG COLUMN
        const defaultColumns = [
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                class: 'text-center'
            },
            { data: 'title_with_cover', name: 'title', title: 'Judul', orderable: true, searchable: true },
            { data: 'code', name: 'code', title: 'Kode' },
            { data: 'author', name: 'author', title: 'Pengarang' },
            { data: 'knowledge_type', name: 'knowledge_type', title: 'Jenis' },
            { data: 'subject', name: 'subject', title: 'Subjek' },
            { data: 'location', name: 'location', title: 'Lokasi' },
            { data: 'stock_info', name: 'stock_info', title: 'Stok', orderable: false },
            // HIDDEN COLUMNS untuk ID dan SLUG
            { data: 'id', name: 'id', visible: false },
            { data: 'slug', name: 'slug', visible: false }
        ];


        // Additional columns mapping
        const additionalColumns = {
            isbn: { data: 'isbn', name: 'isbn', title: 'ISBN' },
            published_year: { data: 'published_year', name: 'published_year', title: 'Tahun Terbit' },
            publisher_name: { data: 'publisher_name', name: 'publisher_name', title: 'Penerbit' },
            price: { data: 'price', name: 'price', title: 'Harga' },
            // PERBAIKAN: Gunakan nama yang sama dengan database
            created_at: { data: 'created_at', name: 'created_at', title: 'Dibuat Pada', orderable: true, searchable: false },
            updated_at: { data: 'updated_at', name: 'updated_at', title: 'Diperbarui Pada', orderable: true, searchable: false }
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
                url: '/catalog/data/get-form-options',
                type: 'GET',
                success: function (response) {
                    if (response.success) {
                        formOptions = response.data;
                        populateFilters();
                    } else {
                        toastr.error('Gagal memuat data dropdown: ' + response.message);
                    }
                },
                error: function (xhr) {
                    console.error('AJAX Error:', xhr);
                    toastr.error('Gagal memuat data dropdown');

                    // Fallback: populate basic options
                    $('#typeFilter').html('<option value="">Semua Jenis</option>');
                    $('#locationFilter').html('<option value="">Semua Lokasi</option>');
                    $('#facultyFilter').html('<option value="">Semua Fakultas</option>');
                    $('#yearFilter').html('<option value="">Semua Tahun</option>');
                }
            });
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

            // Faculty filter dari database
            let facultyFilterOptions = '<option value="">{{ __("catalogs.all_faculty") }}</option>';
            if (formOptions.fakultas && formOptions.fakultas.length > 0) {
                formOptions.fakultas.forEach(function (fakultas) {
                    let displayName = fakultas.SINGKATAN || fakultas.NAMA_FAKULTAS;
                    if (!fakultas.SINGKATAN && fakultas.NAMA_FAKULTAS) {
                        displayName = fakultas.NAMA_FAKULTAS.split(' ').map(word => word.charAt(0)).join('');
                    }
                    facultyFilterOptions += `<option value="${fakultas.C_KODE_FAKULTAS}" title="${fakultas.NAMA_FAKULTAS}">${displayName}</option>`;
                });
            }
            $('#facultyFilter').html(facultyFilterOptions);

            // Year filter
            let yearOptions = '<option value="">{{ __("catalogs.all_year") }}</option>';
            const currentYear = new Date().getFullYear();
            for (let year = currentYear; year >= currentYear - 50; year--) {
                yearOptions += `<option value="${year}">${year}</option>`;
            }
            $('#yearFilter').html(yearOptions);
        }

        // Update table header
        function updateTableHeader() {
            // Manual header update untuk test
            let headerHtml = `
                    <th width="8%">{{ __('common.action') }}</th>
                    <th>{{ __('config.file_type.input.title') }}</th>
                    <th>{{ __('config.classification.input.code') }}</th>
                    <th>{{ __('catalogs.stock_table_author') }}</th>
                    <th>{{ __('catalogs.tipe') }}</th>
                    <th>{{ __('config.classification.input.subject') }}</th>
                    <th>{{ __('config.holiday.location') }}</th>
                    <th>{{ __('config.stock') }}</th>
                    <th>{{ __("sbkps.created_date") }}</th>
                    <th>{{ __("common.updated_at") }}</th>
                `;


            // PERBAIKAN: Tambahkan header untuk kolom yang dipilih
            selectedColumns.forEach(key => {
                if (additionalColumns[key]) {
                    headerHtml += `<th>${additionalColumns[key].title}</th>`;
                }
            });

            $('#catalogTable thead tr').html(headerHtml);
        }



        // Initialize DataTable
        function initDataTable() {
            if ($.fn.DataTable.isDataTable('#catalogTable')) {
                $('#catalogTable').DataTable().clear().destroy();
            }

            updateTableHeader();

            // PERBAIKAN: Buat columns definition yang benar
            let columnsDefinition = [...defaultColumns];

            // Tambahkan kolom tambahan yang dipilih
            selectedColumns.forEach(key => {
                if (additionalColumns[key]) {
                    columnsDefinition.push(additionalColumns[key]);
                }
            });

            console.log('Columns Definition:', columnsDefinition); // DEBUG
            console.log('Selected Columns:', selectedColumns); // DEBUG

            catalogTable = $('#catalogTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                responsive: false,
                autoWidth: false,
                ajax: {
                    url: '/catalog/data/dt',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {
                        d.selected_columns = selectedColumns;
                        d.knowledge_type_id = $('#typeFilter').val();
                        d.item_location_id = $('#locationFilter').val();
                        d.faculty_code = $('#facultyFilter').val();
                        d.published_year = $('#yearFilter').val();

                        console.log('AJAX Data:', d); // DEBUG
                    },
                    error: function (xhr, error, code) {
                        console.error('DataTable AJAX Error:', xhr.responseText);
                        toastr.error('Gagal memuat data: ' + (xhr.responseJSON?.message || 'Server error'));
                    }
                },
                columns: columnsDefinition,
                order: [[1, 'asc']],
                language: {
                    processing: 'Memproses...',
                    search: 'Cari:',
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
                    // PERBAIKAN: Callback setelah table di-render
                    console.log('Table drawn with ' + settings.fnRecordsTotal() + ' records');
                }
            });
        }

        // PERBAIKAN: View button handler dengan slug yang WAJIB ADA
        $(document).on('click', '.view-btn', function () {
            const id = $(this).data('id');
            const slug = $(this).data('slug'); // Ambil dari atribut data-slug
            $('.dropdown-menu').hide();

            let detailUrl = `/catalog/detail/${id}`;
            if (slug && slug.trim() !== '') {
                detailUrl += `/${slug}`;
            }
            window.location.href = detailUrl;
        });

        // PERBAIKAN: Edit button handler tetap sama
        $(document).on('click', '.edit-btn', function () {
            const id = $(this).data('id');
            $('.dropdown-menu').hide();

            // Redirect to edit page (route edit tidak berubah)
            window.location.href = `/catalog/edit/${id}`;
        });

        // Delete button handler (tidak berubah)
        $(document).on('click', '.delete-btn', function () {
            const id = $(this).data('id');
            $('.dropdown-menu').hide();

            Swal.fire({
                title: '{{ __("catalogs.confirmation") }}',
                text: "{{ __('catalogs.katalog.delete_desc') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '{{ __("common.delete") }}',
                cancelButtonText: '{{ __("common.cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/catalog/delete/${id}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (response.success) {
                                toastr.success(response.message);
                                catalogTable.ajax.reload();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function (xhr) {
                            toastr.error(xhr.responseJSON?.message || "{{ __('catalogs.katalog.failed_delete') }}");
                        }
                    });
                }
            });
        });
        $(document).ready(function () {
            // PERBAIKAN: Inisialisasi selectedColumns kosong dulu
            selectedColumns = [];

            // Load form options
            loadFormOptions();

            // Initialize DataTable
            initDataTable();

            // Initialize Select2
            $('.select2-filter').select2({
                width: '100%'
            });

            // Filter change handlers
            $('#typeFilter, #locationFilter, #facultyFilter, #yearFilter').on('change', function () {
                if (catalogTable) {
                    catalogTable.ajax.reload();
                }
            });

            // Column settings handlers
            $('#selectAllColumns').on('change', function () {
                $('.column-checkbox').prop('checked', $(this).is(':checked'));
            });

            // PERBAIKAN: Check individual checkboxes handler
            $('.column-checkbox').on('change', function () {
                const totalCheckboxes = $('.column-checkbox').length;
                const checkedCheckboxes = $('.column-checkbox:checked').length;

                $('#selectAllColumns').prop('checked', totalCheckboxes === checkedCheckboxes);
            });
        });
        $('#applyColumns').on('click', function () {
            selectedColumns = $('.column-checkbox:checked').map(function () {
                return $(this).val();
            }).get();

            console.log('Applying columns:', selectedColumns); // DEBUG
            console.log('Available additional columns:', Object.keys(additionalColumns)); // DEBUG

            // Re-initialize DataTable dengan kolom baru
            initDataTable();
            $('#columnSettingsModal').modal('hide');

            // Show success message dengan info kolom
            const columnNames = selectedColumns.map(col => additionalColumns[col]?.title || col).join(', ');
            toastr.success(`Kolom berhasil diperbarui: ${columnNames || 'tidak ada'}`);
        });
    </script>
@endsection