@extends('layouts/layoutMaster')

@section('title', __('config.navigation.page.title'))

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/nestable/jquery.nestable.css') }}" />
@endsection

@section('page-style')
    <style>
        .select2-container .select2-dropdown {
            z-index: 9999 !important;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center mb-2" style="gap:10px">
                <button class="btn btn-sm btn-danger float-end" onclick="addNewModule()">
                    <i class="ti ti-square-plus me-1"></i> {{ __('config.navigation.form.add_module') }}
                </button>
                <button class="btn btn-sm btn-danger float-end" onclick="addNewGroup()">
                    <i class="ti ti-square-plus me-1"></i> {{ __('config.navigation.form.add_group') }}
                </button>
            </div>

            <div class="dd">
                <ol class="dd-list">

                    @include('config.apps-navigation-items', ['items' => $MyNavBar->roots()])

                </ol>
            </div>
        </div>
    </div>

    <div class="modal fade" id="frmbox" role="dialog" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-transparent">
                    <div class="modal-title"><i class="ti ti-forms me-2"></i> {{ __('config.navigation.form.add_module') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="frm" class="form-validate">
                        @csrf
                        <div class="form-group row mb-2">
                            <label class="col-md-3 col-form-label">{{ __('config.navigation.form.search_module') }}</label>
                            <div class="col-md-9">
                                <select class="select2" required name="module_id">
                                    <option disabled selected>{{ __('config.navigation.form.select_module') }}</option>
                                    @foreach ($modules as $item)
                                        <option value="{{ $item->am_id }}">{{ $item->am_name_id }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect"
                        data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light"
                        onclick="addModule()">{{ __('common.save') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="frmbox-group" role="dialog" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-transparent">
                    <div class="modal-title"><i class="ti ti-forms me-2"></i> {{ __('config.navigation.form.add_group') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="frm-group" class="form-validate">
                        @csrf
                        <div class="form-group row mb-2">
                            <label class="col-md-3 col-form-label">{{ __('config.navigation.form.search_group') }}</label>
                            <div class="col-md-9">
                                <select class="select2" required name="module_id">
                                    <option disabled selected>{{ __('config.navigation.form.select_group') }}</option>
                                    @foreach ($groups as $item)
                                        <option value="{{ $item->amg_id }}">{{ $item->amg_name_id }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect"
                        data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light"
                        onclick="addGroup()">{{ __('common.save') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('vendor-script')
    <script type="text/javascript" src="{{ asset('assets/vendor/libs/nestable/jquery.nestable.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendor/libs/nestable/jquery.nestable++.js') }}"></script>
@endsection

@section('page-script')
    <script>
        let dTable = null;
        let url = '{{ url('config/apps/navigation') }}';

        function addNewModule() {
            $("#frmbox").modal('show');
        }

        function addModule() {
            let formData = new FormData($('#frm')[0])
            if ($('#frm').valid()) {
                $.ajax({
                    url: url + '/add-module',
                    type: 'post',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.status == 'success') {
                            $('#frmbox').modal('hide');
                            toastr.success("{{ __('common.message_save_title') }}", "{{ __('common.message_success_save') }}", toastrOptions)
                            setTimeout(() => {
                                location.reload()
                            }, 1000);
                        }
                    }
                });
            }
        }

        function addNewGroup() {
            $("#frmbox-group").modal('show');
        }

        function addGroup() {
            let formData = new FormData($('#frm-group')[0])
            if ($('#frm-group').valid()) {
                $.ajax({
                    url: url + '/add-group',
                    type: 'post',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.status == 'success') {
                            $('#frmbox').modal('hide');
                            toastr.success("{{ __('common.message_save_title') }}", "{{ __('common.message_success_save') }}", toastrOptions)
                            setTimeout(() => {
                                location.reload()
                            }, 1000);
                        }
                    }
                });
            }
        }

        $(function() {
            $('.dd').nestable({
                maxDepth: 2
            }).on('change', function() {
                //var slug = $(this).data('slug');
                //var parent = $(this).closest('li').data('slug');
                //console.log($(this));
                //console.log(slug+' - '+parent);
                orderItem();
            });
        });

        function orderItem() {
            $.ajax({
                url: url + '/order',
                type: 'post',
                dataType: 'json',
                data: ({
                    order: JSON.stringify($('.dd').nestable('serialize'))
                }),
                success: function(e) {
                    if (e.status == 'success') {
                        toastr.success("{{ __('common.success') }}", "{{ __('config.navigation.message.success_order') }}", toastrOptions)
                    } else {
                        toastr.error("{{ __('common.failed') }}", "{{ __('config.navigation.message.failed_order') }}", toastrOptions)
                    }
                }
            });
        }

        function del(id) {
            yswal_delete.fire({
                title: "{{ __('common.message_delete_prompt_title') }}",
                text: "{{ __('common.message_delete_prompt_text') }}"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url + '/delete',
                        data: {
                            id: id
                        },
                        type: 'delete',
                        dataType: 'json',
                        success: function(e) {
                            if (e.status == 'success') {
                                toastr.success("{{ __('common.message_delete_title') }}", "{{ __('common.message_success_delete') }}", toastrOptions)

                                setTimeout(() => {
                                    location.reload()
                                }, 1000);
                            }
                        }
                    });
                }
            })
        }
    </script>
@endsection
