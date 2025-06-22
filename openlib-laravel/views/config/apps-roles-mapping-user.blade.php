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
    <style>
        .select2-container .select2-dropdown {
            z-index: 9999 !important;
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
                            <div class="step">
                                <a href="{{ url('config/apps/roles/mapping/' . $id) }}" class="step-trigger">
                                    <span class="bs-stepper-circle"><i class="ti ti-list-check"></i></span>
                                    <span class="bs-stepper-label">
                                        <span class="bs-stepper-title">{{ __('config.roles.header.role_permission') }}</span>
                                        <small class="bs-stepper-subtitle">{{ __('config.roles.header.role_desc') }}</small>
                                    </span>
                                </a>
                            </div>
                            <div class="step active">
                                <a href="{{ url('config/apps/roles/mapping-user/' . $id) }}" class="step-trigger">
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
                    <select class="select2" id="event-version" style="z-index: unset !important">
                        @foreach ($roles as $item)
                            <option value="{{ $item->ar_id }}" {{ $role->ar_id == $item->ar_id ? 'selected' : '' }}>
                                {{ $item->ar_name }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- <div class="col-lg-auto mt-auto">
                    <button onclick="save()" class="btn btn-openlib-red btn-sm">
                        <i class="ti ti-file-plus ti-sm me-1"></i>
                        Simpan
                    </button>
                </div> --}}
            </div>
        </div>
    </div>

    <div class="card">
        <div  class="card-datatable table-responsive pt-0">
            @csrf
            <table class="table border-top" id="table">
                <thead>
                    <tr>
                        {{-- <th width="25%">ACTION</th> --}}
                        <th width="75%">{{ __('config.roles.header.user') }}</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="frmbox" role="dialog" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-transparent">
                    <div class="modal-title"><i class="ti ti-forms me-2"></i> {{ __('config.roles.form.assign_user') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="frm" class="form-validate">
                        @csrf
                        <div class="form-group row mb-2">
                            <label class="col-md-3 col-form-label">{{ __('config.roles.header.user') }}</label>
                            <div class="col-md-9">
                                <select class="form-control select2" id="user_id" name="user_id" data-rule-required="true"> 
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect"
                        data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="save()">{{ __('common.assign') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        let dTable = null;
        let url = '{{ url("config/apps/roles/user-list/".$id) }}';

        $("#user_id").select2({
            dropdownParent: $("#frmbox"),
            ajax: {
                url: '{{ url("api/apps/roles/users") }}', //URL for searching companies
                dataType: "json",
                type: 'post',
                delay: 200,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (params) {
                    return {
                        value: params.term, //params send to companies controller
                    };
                },
                processResults: function (data) {
                    data = data.data?.map(item => {
                        return ({
                            id: item.id, text: item.master_data_fullname
                        }) 
                    })
                    return {
                        results: data
                    };
                },
                cache: true
            },
            placeholder: "{{ __('config.roles.form.search_user') }}",
            minimumInputLength: 3,
        });

        $(function() {
            dTable = $('.table').DataTable({
                ajax: {
                    url: url + '/dt',
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columns: [
                    // {
                    //     data: 'action',
                    //     name: 'action',
                    //     orderable: false,
                    //     searchable: false,
                    //     class: 'text-center'
                    // },
                    {
                        data: 'master_data_fullname',
                        name: 'master_data_fullname',
                        orderable: true,
                        searchable: true
                    },
                ]
            });

            $('.dtb').append(
                `<button type="button" class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> {{ __('config.roles.form.assign_user') }}</button>`
                )
        });

        function add() {
            _reset();
            $("#frmbox").modal('show');
        }

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
                            dTable.draw()
                            toastr.success("{{ __('common.message_save_title') }}", "{{ __('common.message_success_save') }}", toastrOptions)
                        }
                    }
                });
            }
        }

        // function del(id) {
        //     yswal_delete.fire({
        //         title: 'Apakah anda yakin akan menghapus data ini ?',
        //         text: 'Periksa Kembali, Data yang sudah dihapus tidak dapat dikembalikan.'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $.ajax({
        //                 url: url + '/delete',
        //                 data: {
        //                     user_id: id
        //                 },
        //                 type: 'delete',
        //                 dataType: 'json',
        //                 success: function(e) {
        //                     if (e.status == 'success') {
        //                         dTable.draw();
        //                         toastr.success('Hapus Data', 'Data berhasil dihapus.', toastrOptions)
        //                     }
        //                 }
        //             });
        //         }
        //     })
        // }

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
