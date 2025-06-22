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
<div class="card">
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table border-top" id="table">
            <thead>
                <tr>
                    <th width="10%">{{ __('common.action') }}</th>
                    <th width="30%">{{ __('config.roles.input.role_name') }}</th>
                    <th width="30%">{{ __('config.roles.input.slug') }}</th>
                    <th width="30%">{{ __('config.roles.input.description') }}</th>
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
                <div class="modal-title"><i class="ti ti-forms me-2"></i> {{ __('config.roles.form.title') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm" class="form-validate">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('config.roles.input.role_name') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="inp[ar_name]" id="ar_name" data-rule-required="true">
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('config.roles.input.slug') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control slug-input" name="inp[ar_slug]" id="ar_slug" data-rule-required="true">                   
                            <small class="text-muted">{{ __('config.roles.form.slug_desc') }}</small>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('config.roles.input.description') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="inp[ar_description]" id="ar_description" data-rule-required="true">  
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
let url = '{{ url('config/apps/roles') }}';

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
            { data: 'ar_name', name: 'ar_name', orderable: true, searchable: true},
            { data: 'ar_slug', name: 'ar_slug', orderable: true, searchable: true},
            { data: 'ar_description', name: 'ar_description', orderable: true, searchable: true}
        ]
    });

    $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> {{ __('config.roles.form.add_text') }}</button>`)
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