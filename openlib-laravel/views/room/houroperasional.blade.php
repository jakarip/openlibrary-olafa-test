@extends('layouts/layoutMaster')

@section('title', __('rooms.operatinghour_title'))

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/jquery-timepicker/jquery-timepicker.css')}}" />
@endsection

@section('page-style')
<style>
.highcharts-credits,
.highcharts-button {
    display: none;
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
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table" id="table">
            <thead>
                <tr>
                    <th width="10%">{{ __('common.action') }}</th>
                    <th>{{ __('rooms.operatinghour_location') }}</th>
                    <th>{{ __('rooms.operatinghour_type') }}</th>
                    <th>{{ __('rooms.operatinghour_start') }}</th>
                    <th>{{ __('rooms.operatinghour_end') }}</th>
                    <th>{{ __('rooms.operatinghour_status') }}</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="frmbox" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i> {{ __('rooms.operatinghour_title') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm" class="form-validate">
                    @csrf
                    <input type="hidden" name="rh_id" id="rh_id">
                    <div class="form-group row mb-2">
                        <label class="col-md-4 col-form-label">{{ __('rooms.operatinghour_location') }} <span class="text-danger">*</span> </label>
                        <div class="col-md-8">
                            <select id="rh_id_location" name="inp[rh_id_location]" class="select2 form-select form-select-lg" data-rule-required="true">
                                <option value="" disabled selected>{{ __('common.select_location') }}</option>
                                @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-4 col-form-label">{{ __('rooms.operatinghour_type') }} <span class="text-danger">*</span> </label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="inp[rh_name]" id="rh_name" data-rule-required="true">
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-4 col-form-label">{{ __('rooms.operatinghour_start') }} <span class="text-danger">*</span> </label>
                        <div class="col-md-8">
                            <input type="text" name="inp[rh_starthour]" id="rh_starthour" placeholder="08:00" class="form-control" data-rule-required="true" />
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-4 col-form-label">{{ __('rooms.operatinghour_end') }} <span class="text-danger">*</span> </label>
                        <div class="col-md-8">
                            <input type="text" name="inp[rh_endhour]" id="rh_endhour" placeholder="08:00" class="form-control" data-rule-required="true" />
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn btn-primary waves-effect waves-light" onclick="save()">{{ __('common.save') }}</button>
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
        let url = '{{ url('room/houroperasional') }}';
        let startHourTimepicker = $('#rh_starthour');
        let endHourTimepicker = $('#rh_endhour');

        if (startHourTimepicker.length) {
            startHourTimepicker.timepicker({
                show: '24:00',
                timeFormat: 'H:i',
                orientation: isRtl ? 'r' : 'l',
                minTime: '08:00', // mulai dari jam 08:00
                maxTime: '24:00', // sampai jam 24:00
                startTime: '08:00'
            });
        }
        if (endHourTimepicker.length) {
            endHourTimepicker.timepicker({
                show: '24:00',
                timeFormat: 'H:i',
                orientation: isRtl ? 'r' : 'l',
                minTime: '08:00', // mulai dari jam 08:00
                maxTime: '24:00', // sampai jam 24:00
                startTime: '08:00'
            });
        }

        $(function() {
            dTable = $('#table').DataTable({
                ajax: {
                    url: url+'/dt',
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columns: [
                    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' },
                    { data: 'location_name', name: 'location_name', orderable: true, searchable: true },
                    { data: 'rh_name', name: 'rh_name', orderable: true, searchable: true },
                    { data: 'rh_starthour', name: 'rh_starthour', orderable: true, searchable: true },
                    { data: 'rh_endhour', name: 'rh_endhour', orderable: true, searchable: true },
                    { data: 'active_formatted', name: 'rh_status', orderable: true, searchable: true }
                ]
            });

            $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> {{ __('rooms.operatinghour_add') }}</button>`);
        });

        function _reset() {
            $('#frm')[0].reset();
            $('#rh_id').val('');
            $('#rh_id_location').val('').trigger('change');
        }

        function add() {
            _reset();
            $("#frmbox").modal('show');
        }

        function edit(id) {
            $.ajax({
                url: url+'/get/'+id,
                type: 'get',
                dataType: 'json',
                success: function(e) {
                    _reset();
                    $('#rh_id').val(e.rh_id);
                    $('#rh_id_location').val(e.rh_id_location).trigger('change');
                    $('#rh_name').val(e.rh_name);
                    $('#rh_starthour').val(e.rh_starthour);
                    $('#rh_endhour').val(e.rh_endhour);
                    $('#frmbox').modal('show');
                }
            });
        }

        function save()
        {
            if($("#frm").valid())
            {
                let formData = new FormData($('#frm')[0]);
                $.ajax({
                    url: url+'/save',
                    type: 'post',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if(data.status == 'success'){
                            $('#frmbox').modal('hide');
                            dTable.draw();
                            toastr.success("{{ __('common.message_save_title') }}", "{{ __('common.message_success_save') }}", toastrOptions)
                        }
                    }
                });
            }
        }

        function del(id)
        {
            yswal_delete.fire({
                title: "{{ __('common.message_delete_prompt_title') }}",
                text: "{{ __('common.message_delete_prompt_text') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url+'/delete',
                        data: { id : id },
                        type: 'delete',
                        dataType: 'json',
                        success: function(e) {
                            if (e.status == 'success') {
                                dTable.draw();
                                toastr.success("{{ __('common.message_delete_title') }}", "{{ __('common.message_success_delete') }}", toastrOptions)
                            }
                        }
                    });
                }
            })
        }

        function disable(roomHourId) {
            let table = $('#table').DataTable();
            let rowData = table.rows().data().toArray().find(row => row.action.includes(roomHourId));

            yswal_disable.fire({
                title: "{{ __('common.message_deactivate_prompt_title') }}",
                text: "{{ __('common.message_deactivate_prompt_text') }} " + "?",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url + '/toggle-status',
                        type: 'post',
                        data: {
                            rh_id: roomHourId,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status == 'success') {
                                $('#table').DataTable().ajax.reload();
                                toastr.success("{{ __('common.message_deactivate_prompt_title_success') }}", "{{ __('common.message_success_deactivate') }}", toastrOptions);
                            } else {
                                toastr.error("{{ __('common.message_error_title') }}", "{{ __('common.message_failed_deactivate') }}", toastrOptions);
                            }
                        }
                    });
                }
            });
        }

        function activate(roomHourId) {
            let table = $('#table').DataTable();
            let rowData = table.rows().data().toArray().find(row => row.action.includes(roomHourId));

            yswal_activate.fire({
                title: "{{ __('rooms.operatinghour_active_title') }}",
                text: "{{ __('rooms.operatinghour_active_text') }}",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url + '/toggle-status',
                        type: 'post',
                        data: {
                            rh_id: roomHourId,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status == 'success') {
                                $('#table').DataTable().ajax.reload();
                                toastr.success("{{ __('common.message_activate_prompt_title_success') }}", "{{ __('common.message_success_activate') }}", toastrOptions);
                            } else {
                                toastr.error("{{ __('common.message_error_title') }}", "{{ __('common.message_failed_activate') }}", toastrOptions);
                            }
                        }
                    });
                }
            });
        }

    </script>
@endsection
