@extends('layouts/layoutMaster')

@section('title', __('config.schedule_title'))

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.css')}}" />
@endsection

@section('page-style')
    <style>
        .highcharts-credits,
        .highcharts-button {
            display: none;
        }

        /* Tambahkan CSS ini ke page-style */
        .dropdown-item {
            white-space: nowrap;
            width: auto;
            min-width: 120px;
            max-width: 100%;
        }

        .dropdown-menu {
            width: auto !important;
            min-width: 120px !important;
            transform: none !important;
        }

        /* Fix flex overflow */
        .dropdown-item.d-flex {
            display: block !important;
            /* Override d-flex to prevent overflow */
        }

        .dropdown-item i {
            margin-right: 0.5rem;
            display: inline-block;
            vertical-align: middle;
        }
    </style>
    <style>
        .select2-container {
            z-index: 9999;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">{{__('config.schedule_title')}}</h5>
            <button class="btn btn-openlib-red btn-sm" onclick="add()">
                <i class="ti ti-file-plus ti-sm me-1"></i> {{__('config.schedule_add')}}
            </button>
        </div>
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table" id="table">
                <thead>
                    <tr>
                        <th width="10%">{{ __('common.action') }}</th>
                        <th>{{__('config.holiday.location')}}</th>
                        <th>{{__('common.operational_hour')}}</th>
                        <th>{{__('config.holiday.input.date')}}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for operating hours -->
    <div class="modal fade" id="frmbox" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-transparent">
                    <div class="modal-title"><i class="ti ti-forms me-2"></i> {{ __('config.schedule_form') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="frm" class="form-validate">
                        @csrf
                        <input type="hidden" name="ilh_id" id="ilh_id">
                        <div class="form-group row mb-2">
                            <label class="col-md-4 col-form-label">{{ __('common.select_location') }} <span
                                    style="color: red;">*</span> </label>
                            <div class="col-md-8">
                                <select id="ilh_item_location" name="inp[ilh_item_location]"
                                    class="select2 form-select form-select-lg" data-rule-required="true">
                                    <option value="" disabled selected>{{ __('common.select_location') }}</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-md-4 col-form-label">{{ __('config.schedule_start') }} <span style="color: red;">*</span>
                            </label>
                            <div class="col-md-8">
                                <input type="text" name="ilh_hour_start" id="ilh_hour_start" placeholder="08:00"
                                    class="form-control" data-rule-required="true" />
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-md-4 col-form-label">{{ __('config.schedule_end') }} <span style="color: red;">*</span>
                            </label>
                            <div class="col-md-8">
                                <input type="text" name="ilh_hour_end" id="ilh_hour_end" placeholder="16:00"
                                    class="form-control" data-rule-required="true" />
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-md-4 col-form-label">{{ __('config.holiday.input.date') }} <span style="color: red;">*</span> </label>
                            <div class="col-md-8">
                                <input type="date" name="inp[ilh_date]" id="ilh_date" class="form-control"
                                    data-rule-required="true" />
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect"
                        data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light"
                        onclick="save()">{{ __('common.save') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('vendor-script')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.js')}}"></script>
@endsection

@section('page-script')
    <script>
        let dTable = null;
        let url = '{{ url('config/operational-hour') }}';
        let specialHourStartTimepicker = $('#ilh_hour_start');
        let specialHourEndTimepicker = $('#ilh_hour_end');

        if (specialHourStartTimepicker.length) {
            specialHourStartTimepicker.timepicker({
                show: '24:00',
                timeFormat: 'H:i',
                orientation: isRtl ? 'r' : 'l',
                minTime: '08:00', // mulai dari jam 08:00
                maxTime: '24:00', // sampai jam 24:00
                startTime: '08:00'
            });
        }

        if (specialHourEndTimepicker.length) {
            specialHourEndTimepicker.timepicker({
                show: '24:00',
                timeFormat: 'H:i',
                orientation: isRtl ? 'r' : 'l',
                minTime: '08:00', // mulai dari jam 08:00
                maxTime: '24:00', // sampai jam 24:00
                startTime: '08:00'
            });
        }

        $(function () {
            // Initialize table with my-dropdown-toggle for consistency with Holiday
            dTable = $('#table').DataTable({
                ajax: {
                    url: url + '/dt',
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columns: [
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' },
                    { data: 'location_name', name: 'location_name', orderable: true, searchable: true },
                    { data: 'ilh_hour', name: 'ilh_hour', orderable: true, searchable: true },
                    { data: 'ilh_date', name: 'ilh_date', orderable: true, searchable: true }
                ]
            });

            // Handling dropdown toggle events untuk konsistensi dengan Holiday
            $(document).on('click', '.my-dropdown-toggle', function (e) {
                e.preventDefault();
                e.stopPropagation();

                // Hide all other dropdowns
                $('.my-btn-group .dropdown-menu').hide();

                // Toggle current dropdown
                $(this).siblings('.dropdown-menu').toggle();
            });

            // Close dropdown when clicking outside
            $(document).on('click', function (e) {
                if (!$(e.target).closest('.my-btn-group').length) {
                    $('.my-btn-group .dropdown-menu').hide();
                }
            });

            // Edit button click handler
            $(document).on('click', '.edit-btn', function () {
                let id = $(this).data('id');
                edit(id);
            });

            // Delete button click handler
            $(document).on('click', '.delete-btn', function () {
                let id = $(this).data('id');
                del(id);
            });

        });

        function _reset() {
            $('#frm')[0].reset();
            $('#ilh_id').val('');
            $('#ilh_item_location').val('').trigger('change');
        }

        function add() {
            _reset();
            $("#frmbox").modal('show');
        }

        function edit(id) {
            $.ajax({
                url: url + '/get/' + id,
                type: 'get',
                dataType: 'json',
                success: function (response) {
                    _reset();
                    const data = response.data;
                    $('#ilh_id').val(data.ilh_id);
                    $('#ilh_item_location').val(data.ilh_item_location).trigger('change');

                    // Split the hours if it contains a dash
                    if (data.ilh_hour && data.ilh_hour.includes('-')) {
                        let hours = data.ilh_hour.split('-');
                        $('#ilh_hour_start').val(hours[0].trim());
                        $('#ilh_hour_end').val(hours[1].trim());
                    } else {
                        $('#ilh_hour_start').val('');
                        $('#ilh_hour_end').val('');
                    }

                    $('#ilh_date').val(data.ilh_date);
                    $('#frmbox').modal('show');
                }
            });
        }

        function save() {
            if ($("#frm").valid()) {
                // Get the start and end times
                let startTime = $('#ilh_hour_start').val();
                let endTime = $('#ilh_hour_end').val();

                // Validate that both times are provided
                if (!startTime || !endTime) {
                    toastr.error("Error", "Both start and end times are required", toastrOptions);
                    return;
                }

                // Create the combined format for database storage
                let combinedHour = startTime + ' - ' + endTime;

                // Create formData from the form
                let formData = new FormData($('#frm')[0]);

                // Add the combined hour to formData
                formData.append('inp[ilh_hour]', combinedHour);

                $.ajax({
                    url: url + '/save',
                    type: 'post',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        if (data.message) {
                            $('#frmbox').modal('hide');
                            dTable.ajax.reload();
                            toastr.success("{{ __('common.message_save_title') }}", data.message, toastrOptions);
                        } else if (data.error) {
                            toastr.error("Error", data.error, toastrOptions);
                        }
                    },
                    error: function (xhr) {
                        let errorMessage = 'Failed to save data';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        }
                        toastr.error("Error", errorMessage, toastrOptions);
                    }
                });
            }
        }

        function del(id) {
            Swal.fire({
                title: "{{ __('common.message_delete_prompt_title') }}",
                text: "{{ __('common.message_delete_prompt_text') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "{{ __('common.yes') }}",
                cancelButtonText: "{{ __('common.no') }}",
                customClass: {
                    confirmButton: 'btn btn-primary me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url + '/delete',
                        data: { id: id, _token: '{{ csrf_token() }}' },
                        type: 'delete',
                        dataType: 'json',
                        success: function (response) {
                            if (response.message) {
                                dTable.ajax.reload();
                                toastr.success("{{ __('common.message_delete_title') }}", response.message, toastrOptions);
                            } else if (response.error) {
                                toastr.error("Error", response.error, toastrOptions);
                            }
                        },
                        error: function (xhr) {
                            let errorMessage = 'Failed to delete data';
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                errorMessage = xhr.responseJSON.error;
                            }
                            toastr.error("Error", errorMessage, toastrOptions);
                        }
                    });
                }
            });
        }
    </script>
@endsection