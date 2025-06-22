@extends('layouts/layoutMaster')

@section('title', __('config.holiday.detail_holiday_data'))

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
@endsection

@section('page-style')
    <style>
        .modal {
            overflow: visible !important;
        }

        .modal .select2-container,
        .modal .flatpickr-calendar,
        .modal .dropdown-menu,
        .modal .flatpickr-day {
            z-index: 999999 !important;
        }

        .select2-container {
            z-index: 999999 !important;
        }

        .flatpickr-calendar {
            z-index: 999999 !important;
        }
    </style>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ url('config/holiday') }}" class="btn btn-label-secondary me-2">
                    <i class="ti ti-arrow-left"></i> {{__('config.holiday.back')}}
                </a>
            </div>
            <h5 class="mb-0">{{__('config.holiday.detail_holiday_data')}}</h5>
            <div>
                <button class="btn btn-label-primary" data-bs-toggle="modal" data-bs-target="#columnSettingsModal">
                    <i class="ti ti-settings"></i> {{__('config.holiday.select_additional_data')}}
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">{{__('config.holiday.filter_location')}}</label>
                    <select id="locationFilterDetail" class="form-select select2-filter">
                        <option value="">{{__('config.holiday.all_locations')}}</option>

                    </select>
                </div>

            </div>
        </div>

        <div class="card-datatable table-responsive overflow-auto pt-0">
            <table class="datatables-basic table border-top" id="holidayDetailTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>{{__('config.holiday.id_rule')}}</th>
                        <th>{{__('config.holiday.location')}}</th>
                        <th>{{__('config.holiday.date')}}</th>
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
                    <h5 class="modal-title">
                        <i class="ti ti-settings me-2"></i> {{__('config.holiday.select_additional_data')}}
                    </h5>
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
@endsection

@section('page-script')
    <script>
        const defaultColumns = [
            { data: 'id', name: 'id', title: 'ID' },
            { data: 'holiday_rule_id', name: 'holiday_rule_id', title: 'Holiday Rule ID' },
            { data: 'location', name: 'location', title: 'Location' },
            { data: 'holiday_date', name: 'holiday_date', title: 'Holiday Date' },

        ];

        const additionalColumns = {
            created_by: { data: 'created_by', name: 'created_by', title: 'Created By' },
            created_at: { data: 'created_at', name: 'created_at', title: 'Created At' },
            updated_by: { data: 'updated_by', name: 'updated_by', title: 'Updated By' },
            updated_at: { data: 'updated_at', name: 'updated_at', title: 'Updated At' },
        };

        let selectedColumns = [];
        let holidayTable = null;

        function updateTableHeader() {
            let headerHtml = `
                    <th>ID</th>
                    <th>{{__('config.holiday.id_rule')}}</th>
                    <th>{{__('config.holiday.location')}}</th>
                    <th>{{__('config.holiday.date')}}</th>
                `;

            selectedColumns.forEach(key => {
                if (additionalColumns[key]) {
                    headerHtml += `<th>${additionalColumns[key].title}</th>`;
                }
            });

            $('#holidayDetailTable thead tr').html(headerHtml);
        }

        function initDataTable() {
            if ($.fn.DataTable.isDataTable('#holidayDetailTable')) {
                $('#holidayDetailTable').DataTable().clear().destroy();
            }

            updateTableHeader();

            let columnsDefinition = defaultColumns.concat(
                selectedColumns.map(key => additionalColumns[key])
            );

            holidayTable = $('#holidayDetailTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                responsive: false,
                autoWidth: false,
                ajax: {
                    url: '{{ url("config/holiday/detail/dt") }}',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function (d) {
                        d.selected_columns = selectedColumns;
                        // Add location filter
                        d.location_id = $('#locationFilterDetail').val();
                    }
                },
                columns: columnsDefinition,
                order: [[0, 'asc']]
            });
        }

        $(document).ready(function () {
            initDataTable();
            loadLocations();
            $('#selectAllColumns').on('change', function () {
                $('.column-checkbox').prop('checked', $(this).is(':checked'));
            });
            $('#locationFilterDetail').on('change', function () {
                $('#holidayDetailTable').DataTable().ajax.reload();
            });
            $('#applyColumns').on('click', function () {
                selectedColumns = $('.column-checkbox:checked').map(function () {
                    return $(this).val();
                }).get();

                initDataTable();
                $('#columnSettingsModal').modal('hide');
            });
        });
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

                    // Add to detail filter dropdown
                    $('#locationFilterDetail').html(optionsHtml);
                    $('#locationFilterDetail').select2();
                },
                error: function (xhr) {
                    toastr.error('Failed to load locations');
                }
            });
        }
    </script>
@endsection