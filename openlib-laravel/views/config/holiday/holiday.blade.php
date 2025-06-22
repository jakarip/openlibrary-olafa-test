@extends('layouts/layoutMaster')

@section('title', __('config.holiday.page.title'))

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}">
@endsection

@section('page-style')
    <style>
        .modal {
            overflow: visible !important;
        }

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
        .modal .dropdown-menu,
        .modal .flatpickr-day {
            z-index: 999999 !important;
        }

        .modal .select2-container {
            z-index: 999999 !important;
        }

        .flatpickr-calendar {
            z-index: 999999 !important;
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
            <h5>{{__('config.holiday.data_holiday')}}</h5>
            <div class="">
                <button class="btn btn-danger me-2" onclick="addHoliday()">
                    <i class="ti ti-plus me-1"></i> {{__('common.add_data')}}
                </button>

                <button class="btn btn-label-primary" data-bs-toggle="modal" data-bs-target="#columnSettingsModal">
                    <i class="ti ti-settings"></i> {{__('config.holiday.select_additional_data')}}
                </button>
                <a href="{{ url('config/holiday/detail') }}" class="btn btn-label-success">
                    <i class="ti ti-calendar-stats me-1"></i> {{__('config.holiday.view_detail')}}
                </a>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">{{__('config.holiday.filter_location')}}</label>
                    <select id="locationFilter" class="form-select select2-filter">
                        <option value="">{{__('config.holiday.all_locations')}}</option>
                        <!-- Options will be loaded dynamically -->
                    </select>
                </div>
            </div>
        </div>
        <div class="card-datatable table-responsive overflow-auto pt-0">
            <table class="datatables-basic table border-top" id="holidayTable">
                <thead>
                    <tr>
                        <th width="10%">{{__('common.action')}}</th>
                        <th>ID</th>
                        <th>{{__('config.holiday.input.name')}}</th>
                        <th>{{__('config.holiday.weekday')}}</th>
                        <th>{{__('config.app_language.input.date')}}</th>
                        <th>{{__('config.holiday.input.month')}}</th>
                        <th>{{__('config.holiday.input.from_year')}}</th>
                        <th>{{__('config.holiday.input.to_year')}}</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="columnSettingsModal" tabindex="-1" aria-labelledby="columnSettingsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title"><i class="ti ti-settings me-2"></i> {{__('config.holiday.select_additional_data')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="columnSettingsForm">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="selectAllColumns">
                            <label class="form-check-label fw-bold" for="selectAllColumns">{{__('config.member.select_all')}}</label>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input column-checkbox" type="checkbox" value="created_by"
                                        id="createdBy">
                                    <label class="form-check-label" for="createdBy">{{__('config.holiday.createdBy')}}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input column-checkbox" type="checkbox" value="created_at"
                                        id="createdAt">
                                    <label class="form-check-label" for="createdAt">{{__('config.holiday.createdAt')}}</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input column-checkbox" type="checkbox" value="updated_by"
                                        id="updatedBy">
                                    <label class="form-check-label" for="updatedBy">{{__('config.holiday.updatedBy')}}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input column-checkbox" type="checkbox" value="updated_at"
                                        id="updatedAt">
                                    <label class="form-check-label" for="updatedAt">{{__('config.holiday.updatedAt')}}</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i> {{__('common.cancel')}}
                    </button>
                    <button type="button" class="btn btn-primary" id="applyColumns">
                        <i class="ti ti-check me-1"></i> {{__('common.apply')}}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Holiday Rule -->
    <div class="modal fade" id="modalAddHoliday" tabindex="-1" aria-labelledby="modalAddHolidayLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAddHolidayLabel">
                        <i class="ti ti-calendar-plus me-2"></i> {{__('config.holiday.add_rule')}}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="formAddHolidayRule">
                    <div class="modal-body row g-3">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label">{{__('config.holiday.rule_name')}}<span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required
                                placeholder="{{__('config.holiday.saturday.national')}}">
                        </div>
                        <div class="col-md-6 mt-2">
                            <label class="form-label">{{__('config.holiday.location')}}</label>
                            <select name="location_id" class="form-select select2">
                                <option value="">{{__('config.holiday.global')}}</option>
                                <!-- Options will be loaded dynamically -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{__('config.holiday.weekday')}}</label>
                            <select name="weekday" class="form-select select2">
                                <option value="">{{__('config.holiday.all_days')}}</option>
                                <option value="0">{{__('config.holiday.sunday')}}</option>
                                <option value="1">{{__('config.holiday.monday')}}</option>
                                <option value="2">{{__('config.holiday.tuesday')}}</option>
                                <option value="3">{{__('config.holiday.wednesday')}}</option>
                                <option value="4">{{__('config.holiday.thursday')}}</option>
                                <option value="5">{{__('config.holiday.friday')}}</option>
                                <option value="6">{{__('config.holiday.saturday')}}</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{__('config.app_language.input.date')}}</label>
                            <input type="text" name="day" class="form-control" placeholder="* = {{__('config.holiday.all_dates')}}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{__('config.holiday.input.month')}}</label>
                            <input type="text" name="month" class="form-control" placeholder="* = {{__('config.holiday.all_months')}}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">{{__('config.member_type.form.year')}}</label>
                            <div class="input-group">
                                <input type="number" name="year_from" class="form-control" placeholder="{{__('config.holiday.from')}}" required
                                    min="0" max="3000" value="{{ date('Y') }}">
                                <span class="input-group-text">-</span>
                                <input type="number" name="year_to" class="form-control" placeholder="{{__('config.holiday.to')}}" required
                                    min="0" max="3000" value="{{ date('Y') }}">
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                            <i class="ti ti-x"></i> {{__('common.cancel')}}
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="ti ti-calendar-plus"></i> {{__('common.save')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('page-script')
    <script>
        // Handle dropdown toggle for action buttons
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

        $(document).on('click', '.dropdown-menu a', function () {
            const $menu = $(this).closest('.dropdown-menu');
            closePortalDropdown($menu);
        });

        // Edit button handler
        // Edit button handler
        $(document).on('click', '.edit-btn', function () {
            const id = $(this).data('id');

            // Hide all dropdowns
            $('.dropdown-menu').hide();

            // Get the holiday rule data
            $.ajax({
                url: '{{ url("config/holiday/getbyid") }}',
                type: 'POST',
                data: { id: id },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    const rule = response.data;

                    // Reset the form
                    $('#formAddHolidayRule')[0].reset();

                    // Add hidden input for ID
                    if (!$('#formAddHolidayRule input[name="id"]').length) {
                        $('#formAddHolidayRule').append('<input type="hidden" name="id" value="' + rule.id + '">');
                    } else {
                        $('#formAddHolidayRule input[name="id"]').val(rule.id);
                    }

                    // Populate form fields
                    $('#formAddHolidayRule input[name="name"]').val(rule.name);

                    // Format month with leading zero if needed
                    let month = rule.month;
                    if (month !== '*' && month !== '' && month !== null) {
                        if (month.length === 1 && parseInt(month) < 10) {
                            month = '0' + month;
                        }
                    } else {
                        month = '';
                    }

                    // Set regular input fields
                    $('#formAddHolidayRule input[name="day"]').val(rule.day !== '*' ? rule.day : '');
                    $('#formAddHolidayRule input[name="month"]').val(month);
                    $('#formAddHolidayRule input[name="year_from"]').val(rule.year_from);
                    $('#formAddHolidayRule input[name="year_to"]').val(rule.year_to);

                    // Destroy and reinitialize select2 with correct values
                    $('#formAddHolidayRule select[name="weekday"]').select2('destroy');
                    $('#formAddHolidayRule select[name="weekday"]').val(rule.weekday !== '*' ? rule.weekday : '');
                    $('#formAddHolidayRule select[name="weekday"]').select2({
                        dropdownParent: $('#modalAddHoliday')
                    });

                    // Set location
                    $('#formAddHolidayRule select[name="location_id"]').select2('destroy');
                    $('#formAddHolidayRule select[name="location_id"]').val(rule.location_id || '');
                    $('#formAddHolidayRule select[name="location_id"]').select2({
                        dropdownParent: $('#modalAddHoliday')
                    });

                    // Update modal title
                    $('#modalAddHolidayLabel').html('<i class="ti ti-calendar-plus me-2"></i> Edit Holiday Rule');

                    // Show modal
                    $('#modalAddHoliday').modal('show');
                },
                error: function (xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Failed to get holiday rule data');
                }
            });
        });

        // Delete button handler
        $(document).on('click', '.delete-btn', function () {
            const id = $(this).data('id');

            // Hide all dropdowns
            $('.dropdown-menu').hide();

            // Confirm deletion
            Swal.fire({
                title: '{{__("common.confirmation")}}',
                text: "{{__('common.message_delete_prompt_title')}}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '{{__("common.delete_data")}}',
                cancelButtonText: '{{__("common.cancel")}}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("config/holiday/delete") }}',
                        type: 'DELETE',
                        data: { id: id },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            toastr.success('Data berhasil dihapus!');
                            $('#holidayTable').DataTable().ajax.reload();
                        },
                        error: function (xhr) {
                            toastr.error(xhr.responseJSON?.message || 'Gagal menghapus data');
                        }
                    });
                }
            });
        });
        function validateHolidayForm() {
            // Get form values
            const day = $('#formAddHolidayRule input[name="day"]').val();
            const month = $('#formAddHolidayRule input[name="month"]').val();
            const yearFrom = parseInt($('#formAddHolidayRule input[name="year_from"]').val());
            const yearTo = parseInt($('#formAddHolidayRule input[name="year_to"]').val());

            // If day and month are both * or empty, no validation needed
            if ((day === '*' || day === '') && (month === '*' || month === '')) {
                return true;
            }

            let validationPassed = true;
            let errorMessage = '';

            // Check for multiple leading zeros
            if (day !== '*' && day !== '') {
                const trimmedDay = day.replace(/^0+/, ''); // Remove leading zeros
                if (day.length - trimmedDay.length > 1) {
                    Swal.fire({
                        title: '{{__("config.holiday.failed.validation")}}',
                        text: '{{__("config.holiday.date.format_desc")}}',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }

                // Validate the numeric value
                const dayNum = parseInt(trimmedDay);
                if (isNaN(dayNum) || dayNum < 1 || dayNum > 31) {
                    validationPassed = false;
                    errorMessage = '{{__("config.holiday.date_must")}}';
                }
            }

            if (month !== '*' && month !== '') {
                const trimmedMonth = month.replace(/^0+/, ''); // Remove leading zeros
                if (month.length - trimmedMonth.length > 1) {
                    Swal.fire({
                        title: '{{__("config.holiday.failed.validation")}}',
                        text: '{{__("config.holiday.month_format")}}',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }

                // Validate the numeric value
                const monthNum = parseInt(trimmedMonth);
                if (isNaN(monthNum) || monthNum < 1 || monthNum > 12) {
                    validationPassed = false;
                    errorMessage = '{{__("config.holiday.month_format_desc")}}';
                }

                // If day is also specified, validate day for that month
                if (validationPassed && day !== '*' && day !== '') {
                    const dayNum = parseInt(day.replace(/^0+/, ''));
                    const monthNum = parseInt(trimmedMonth);

                    // Check if day is valid for this month in any year in the specified range
                    let isValidForAnyYear = false;

                    for (let year = yearFrom; year <= yearTo; year++) {
                        // Create a date object and check if it's valid
                        const date = new Date(year, monthNum - 1, dayNum);
                        if (date.getDate() === dayNum && date.getMonth() === monthNum - 1) {
                            isValidForAnyYear = true;
                            break;
                        }
                    }

                    if (!isValidForAnyYear) {
                        validationPassed = false;
                        errorMessage = `{{__("config.holiday.date_desc")}} ${dayNum} {{__("config.holiday.month_not_valid")}} ${monthNum} {{__("config.holiday.date_range")}}`;
                    }
                }
            }

            // If validation fails, show SweetAlert
            if (!validationPassed) {
                Swal.fire({
                    title: '{{__("config.holiday.failed.validation")}} ',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            return true;
        }

        const defaultColumns = [
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                class: 'text-center'
            },
            { data: 'id', name: 'id', title: 'ID' },
            { data: 'name', name: 'name', title: 'Name' },
            { data: 'weekday', name: 'weekday', title: 'Weekday' },
            { data: 'day', name: 'day', title: 'Day' },
            { data: 'month', name: 'month', title: 'Month' },
            { data: 'location', name: 'location', title: 'Location' }, // Add this line
            { data: 'year_from', name: 'year_from', title: 'Year From' },
            { data: 'year_to', name: 'year_to', title: 'Year To' }
        ];
        function loadLocations() {
            $.ajax({
                url: '{{ url("config/holiday/get-locations") }}',
                type: 'GET',
                success: function (response) {
                    const locations = response.data;
                    let optionsHtml = '<option value="">Global (All Locations)</option>';

                    locations.forEach(function (location) {
                        optionsHtml += `<option value="${location.id}">${location.name}</option>`;
                    });

                    // Add to filter dropdown
                    $('#locationFilter').html(optionsHtml);
                    $('#locationFilter').select2();

                    // Add to modal form dropdown
                    $('select[name="location_id"]').html(optionsHtml);
                },
                error: function (xhr) {
                    toastr.error('Failed to load locations');
                }
            });
        }
        const additionalColumns = {
            created_by: { data: 'created_by', name: 'created_by', title: 'Created By' },
            created_at: { data: 'created_at', name: 'created_at', title: 'Created At' },
            updated_by: { data: 'updated_by', name: 'updated_by', title: 'Updated By' },
            updated_at: { data: 'updated_at', name: 'updated_at', title: 'Updated At' }
        };

        let selectedColumns = [];

        let holidayTable = null;

        // Update table header function to include location
        function updateTableHeader() {
            let headerHtml = `
                    <th width="10%">{{__('common.action')}}</th>
                    <th>ID</th>
                    <th>{{__('config.holiday.input.name')}}</th>
                    <th>{{__('config.holiday.weekday')}}</th>
                    <th>{{__('config.app_language.input.date')}}</th>
                    <th>{{__('config.holiday.input.month')}}</th>
                    <th>{{__('config.holiday.location')}}</th>
                    <th>{{__('config.holiday.input.from_year')}}</th>
                    <th>{{__('config.holiday.input.to_year')}}</th>
                `;

            selectedColumns.forEach(key => {
                if (additionalColumns[key]) {
                    headerHtml += `<th>${additionalColumns[key].title}</th>`;
                }
            });

            $('#holidayTable thead tr').html(headerHtml);
        }

        function initDataTable() {
            if ($.fn.DataTable.isDataTable('#holidayTable')) {
                $('#holidayTable').DataTable().clear().destroy();
            }

            updateTableHeader();

            let columnsDefinition = defaultColumns.concat(
                selectedColumns.map(key => additionalColumns[key])
            );

            holidayTable = $('#holidayTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                responsive: false,
                autoWidth: false,
                ajax: {
                    url: '{{ url("config/holiday/dt") }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {
                        d.selected_columns = selectedColumns;
                        // Add location filter
                        d.location_id = $('#locationFilter').val();
                    }
                },
                columns: columnsDefinition,
                order: [[1, 'asc']]
            });
        }

        $(document).ready(function () {
            // Load locations
            loadLocations();

            // Initialize datatable
            initDataTable();

            // Initialize select2
            $('select.select2').select2({
                dropdownParent: $('#modalAddHoliday') // Keep dropdown within modal
            });

            $('select.select2-filter').select2();

            // Location filter change event
            $('#locationFilter').on('change', function () {
                $('#holidayTable').DataTable().ajax.reload();
            });

            // Other existing initialization code...

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
        });

        // Fungsi ini dipindahkan agar tidak duplikat
        function addHoliday() {
            // Update modal title to "Add" when adding new data
            $('#modalAddHolidayLabel').html('<i class="ti ti-calendar-plus me-2"></i> Tambah Holiday Rule');

            // Remove the hidden id input if it exists
            $('#formAddHolidayRule input[name="id"]').remove();

            // Reset form
            $('#formAddHolidayRule')[0].reset();

            // Reset Select2 fields
            $('#formAddHolidayRule select[name="weekday"]').val('').trigger('change');
            $('#formAddHolidayRule select[name="location_id"]').val('').trigger('change');

            // Show modal
            $('#modalAddHoliday').modal('show');
        }

        // Form submission handler - digabungkan untuk menangani baik add maupun edit
        $('#formAddHolidayRule').on('submit', function (e) {
            e.preventDefault();

            // Run validation before proceeding
            if (!validateHolidayForm()) {
                return false;
            }

            const formData = $(this).serialize();
            const isEdit = $(this).find('input[name="id"]').length > 0;

            const url = isEdit
                ? '{{ url("config/holiday/save") }}'
                : '{{ url("config/holiday/add") }}';

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                success: function (res) {
                    toastr.success(isEdit ? 'Rule berhasil diperbarui!' : 'Rule berhasil ditambahkan!');
                    $('#modalAddHoliday').modal('hide');
                    $('#holidayTable').DataTable().ajax.reload();
                },
                error: function (xhr) {
                    // Handle error response from server
                    let errorMessage = xhr.responseJSON?.error || xhr.responseJSON?.message || 'Gagal menyimpan rule';
                    Swal.fire({
                        title: 'Error',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
        $(document).ready(function () {
            // Auto-normalize input fields on blur
            $('#formAddHolidayRule input[name="day"], #formAddHolidayRule input[name="month"]').on('blur', function () {
                const value = $(this).val();
                if (value !== '*' && value !== '') {
                    // If it has leading zeros, normalize it (keeping at most one leading zero)
                    if (value.match(/^0+\d/)) {
                        // Remove all leading zeros, then add back one if it's a single digit
                        let normalized = value.replace(/^0+/, '');
                        $(this).val(normalized);
                    }
                }
            });
        });
        // Add validation on input change for better user experience
        $('#formAddHolidayRule input[name="day"], #formAddHolidayRule input[name="month"]').on('change', function () {
            validateHolidayForm();
        });

        document.addEventListener('DOMContentLoaded', function () {
            const yearFromInput = document.querySelector('input[name="year_from"]');
            const yearToInput = document.querySelector('input[name="year_to"]');

            function validateYears() {
                let yearFrom = parseInt(yearFromInput.value) || new Date().getFullYear();
                let yearTo = parseInt(yearToInput.value) || new Date().getFullYear();

                if (yearFrom < 0) yearFrom = 0;
                if (yearTo < 0) yearTo = 0;

                const maxYear = 3000;
                if (yearFrom > maxYear) yearFrom = maxYear;
                if (yearTo > maxYear) yearTo = maxYear;

                if (yearFrom > yearTo) {
                    yearTo = yearFrom;
                }

                yearFromInput.value = yearFrom;
                yearToInput.value = yearTo;
            }

            yearFromInput.addEventListener('input', validateYears);
            yearToInput.addEventListener('input', validateYears);

            yearFromInput.addEventListener('blur', validateYears);
            yearToInput.addEventListener('blur', validateYears);
        });
    </script>
@endsection