@extends('layouts/layoutMaster')

@section('title', __('config.location.page.title'))

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
    .select2-container {
        z-index: 1;
    }

    .card {
        z-index: 0;
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table border-top" id="table">
            <thead>
                <tr>
                    <th width="10%">{{ __('common.action') }}</th>
                    <th>{{ __('config.location.input.name') }}</th>
                    <th>{{ __('config.location.input.address') }}</th>
                    <th>{{ __('common.updated_at') }}</th>
                    <th>{{ __('config.location.input.total_collection') }}</th>
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
                <div class="modal-title"><i class="ti ti-forms me-2"></i> {{ __('config.location.form.title') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm" class="form-validate">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('config.location.input.name') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="inp[name]" id="name" data-rule-required="true">
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('config.location.input.address') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="inp[address]" id="address" data-rule-required="true">
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('config.location.input.phone') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="inp[phone]" id="phone" data-rule-required="true">
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('config.location.input.fax') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="inp[fax]" id="fax" data-rule-required="true">
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('config.location.input.email') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="inp[email]" id="email" data-rule-required="true">
                        </div>
                    </div>

                    <hr />

                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('config.location.input.show_in_footer') }}</label>
                        <div class="col-md-9">
                            <select class="select2" name="inp[show_as_footer]" id="show_as_footer" data-rule-required="true">
                                <option value="1">{{ __('common.show') }}</option>
                                <option value="0">{{ __('common.dont_show') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('config.location.input.operational_monday') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="inp[open_mon]" id="open_mon" data-rule-required="true">
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('config.location.input.operational_tuesday') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="inp[open_tue]" id="open_tue" data-rule-required="true">
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('config.location.input.operational_wednesday') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="inp[open_wed]" id="open_wed" data-rule-required="true">
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('config.location.input.operational_thursday') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="inp[open_thu]" id="open_thu" data-rule-required="true">
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('config.location.input.operational_friday') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="inp[open_fri]" id="open_fri" data-rule-required="true">
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('config.location.input.operational_saturday') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="inp[open_sat]" id="open_sat" data-rule-required="true">
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('config.location.input.operational_sunday') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="inp[open_sun]" id="open_sun" data-rule-required="true">
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
@endsection

@section('page-script')
<script>
let dTable = null;
let url = '{{ url('config/catalog-location') }}';

$(function() {
    dTable = $('.table').DataTable({
        ajax: {
            url: url+'/dt',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
    { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' },
    { data: 'name', name: 'name', orderable: true, searchable: true },
    { data: 'address', name: 'address', orderable: true, searchable: true },
    {
        data: 'created_at',
        name: 'created_at',
        orderable: true,
        searchable: true,
        render: function(data, type, row) {
            const date = new Date(data);
            const day = String(date.getDate()).padStart(2, '0'); // Menambahkan nol di depan jika perlu
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Bulan dimulai dari 0
            const year = date.getFullYear();
            return `${day}-${month}-${year}`; // Format DD-MM-YYYY
        }
    },
    { data: 'collection', name: 'collection', orderable: true, searchable: true }
]

    });

    @if(auth()->can('config-catalog-location.create'))
        $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> {{ __('config.location.form.add_text') }}</button>`);
    @endif
});

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

            $('#id').val(id);
            $.each(e, function(key, value) {
                if ($('#' + key).hasClass("select2"))
                    $('#' + key).val(value).trigger('change');
                else
                $('#' + key).val(value);
            });

            $('#frmbox').modal('show')
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

</script>
@endsection
