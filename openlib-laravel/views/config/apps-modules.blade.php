@extends('layouts/layoutMaster')

@section('title', __('config.modules.page.title'))

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
<div class="card">
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table border-top" id="table">
            <thead>
                <tr>
                    <th width="10%">{{ __('common.action') }}</th>
                    <th width="25%">{{ __('config.modules.input.name_id') }}</th>
                    <th width="25%">{{ __('config.modules.input.name_en') }}</th>
                    <th width="20%">{{ __('config.modules.input.slug') }}</th>
                    <th width="20%">{{ __('config.modules.input.icon') }}</th>
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
                <div class="modal-title"><i class="ti ti-forms me-2"></i> {{ __('config.modules.form.title') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm" class="form-validate">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('config.modules.input.name_id') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="inp[am_name_id]" id="am_name_id" data-rule-required="true">
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('config.modules.input.name_en') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="inp[am_name_en]" id="am_name_en" data-rule-required="true">
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('config.modules.input.slug') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control slug-input" name="inp[am_slug]" id="am_slug" data-rule-required="true">                     
                            <small class="text-muted">{{ __('config.modules.form.example_slug') }} <span class="fw-bold">config-catalog-classification</span></small>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('config.modules.input.url') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control slug-input" name="inp[am_url]" id="am_url" data-rule-required="true">                     
                            <small class="text-muted">{{ __('config.modules.form.example_url') }} <span class="fw-bold">config/catalog-classification</span></small>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('config.modules.input.icon') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="inp[am_icon]" id="am_icon" data-rule-required="true">
                            <small class="text-muted">{{ __('config.modules.form.select_icon_here') }}: <a href="https://tabler-icons.io/" target="_blank">https://tabler-icons.io</a></small>
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
let url = '{{ url('config/apps/modules') }}';

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
            { data: 'action', name: 'action', orderable: false, searchable: false,class: 'text-center' },
            { data: 'am_name_id', name: 'am_name_id', orderable: true, searchable: true},
            { data: 'am_name_en', name: 'am_name_en', orderable: true, searchable: true},
            { data: 'am_url', name: 'am_url', orderable: true, searchable: true},
            { data: 'am_icon', name: 'am_icon', orderable: true, searchable: true},
        ]
    });

    $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> {{ __('config.modules.form.add_text') }}</button>`)
});

function add() {
    _reset();
    $("#frmbox").modal('show');
}

function edit(id) {
    $.ajax({
        url: url+'/'+id,
        type: 'get',
        dataType: 'json',
        success: function(e) {
            _reset();

            $('#id').val(id);
            $.each(e, function(key, value) {
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

function slugInput(elem){
    let val = elem.val()
    val = val.toLowerCase().replaceAll(' ', '-')
    elem.val(val)
}

$('.slug-input').change(function(){
    slugInput($(this))
})
</script>
@endsection