@extends('layouts/layoutMaster')

@section('title', __('config.roles.page.title'))

@section('vendor-style')
@endsection

@section('page-style')
    <style>
        .highcharts-credits,
        .highcharts-button {
            display: none;
        }
    </style>
@endsection

@section('content')
    <div class="card mb-2">
        <div class="card-body p-3">
            <div class="row">
                <div class="col-lg">
                    <div class="bs-stepper wizard-modern bs-stepper-indikator">
                        <div class="bs-stepper-header p-0">
                            <div class="step active }}">
                                <a href="{{url('config/apps/roles/mapping/'.$id)}}" class="step-trigger">
                                    <span class="bs-stepper-circle"><i class="ti ti-list-check"></i></span>
                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">{{ __('config.roles.header.role_permission') }}</span>
                                        <small class="bs-stepper-subtitle">{{ __('config.roles.header.role_desc') }}</small>
                                    </span>
                                </a>
                            </div>
                            <div class="step">
                                <a href="{{url('config/apps/roles/mapping-user/'.$id)}}" class="step-trigger">
                                    <span class="bs-stepper-circle"><i class="ti ti-users"></i></span>
                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">{{ __('config.roles.header.user_all') }}</span>
                                        <small class="bs-stepper-subtitle">{{ __('config.roles.header.user_desc') }}</small>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <label class="form-label">{{ __('config.roles.input.role_name') }}</label>
                    <select class="select2" id="event-version">
                        @foreach ($roles as $item)
                            <option value="{{ $item->ar_id }}" {{ $role->ar_id == $item->ar_id ? 'selected' : '' }}>
                                {{ $item->ar_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-auto mt-auto">
                    <button onclick="save()" class="btn btn-openlib-red btn-sm">
                        <i class="ti ti-file-plus ti-sm me-1"></i>
                        {{ __('common.save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <form id="frm" class="card-datatable table-responsive pt-0">
            @csrf
            <table class="table border-top" id="table">
                <thead>
                    <tr>
                        <th width="25%">{{ __('config.roles.header.module') }}</th>
                        <th width="75%">{{ __('config.roles.header.permission') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($groups as $group)
                        <tr>
                            <td colspan="2" style="background-color: #F9FBFC"><i
                                    class="{{ $group->group->amg_icon }} me-2"></i>{{ $group->group->amg_name_id }}</td>
                        </tr>
                        @foreach ($group->childrens as $module)
                            @if ($module->module)
                                <tr>
                                    <td>{{ $module->module->am_name_id }}</td>
                                    <td>
                                        <div class="d-flex gap-3">
                                            @foreach ($module->module->permission as $permission)
                                                <div class="form-check d-flex align-items-center" style="gap:5px">
                                                    <input {{ in_array($permission->ap_id, $permissions) ? 'checked' : '' }}
                                                        class="form-check-input" type="checkbox"
                                                        id="permission-{{ $permission->ap_slug }}"
                                                        name="permission[{{ $permission->ap_id }}]">
                                                    <label class="form-check-label"
                                                        for="permission-{{ $permission->ap_slug }}">{{ $permission->ap_action }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </form>
    </div>
@endsection

@section('page-script')
    <script>
        let dTable = null;
        let url = '{{ url('config/apps/roles/mapping/' . $id) }}';

        function save() {
            if ($("#frm").valid()) {
                let formData = new FormData($('#frm')[0]);

                $.ajax({
                    url: url + '/save',
                    type: 'post',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.status == 'success') {
                            $('#frmbox').modal('hide');
                            toastr.success("{{ __('common.message_save_title') }}", "{{ __('common.message_success_save') }}", toastrOptions)
                        }
                    }
                });
            }
        }

        let anyChangesMade = false

        $('.form-check-input').change(function() {
            anyChangesMade = true
        })

        $('#event-version').change(function() {
            let val = $(this).val()
            window.location.href = "{{ url('/config/apps/roles/mapping/') }}/" + val
        })

        window.onbeforeunload = function() {
            if (anyChangesMade)
                return "{{ __('common.message_leave_no_save') }}";
        };
    </script>
@endsection
