@extends('layouts/layoutMaster')

@section('title', __('config.apps_language.page.title'))

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
<div class="card">
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table border-top" id="table">
            <thead>
                <tr>
                    <th width="7%">{{ __('common.action') }}</th>
                    <th width="24%">{{ __('config.app_language.input.key') }}</th>
                    <th width="25%"><i class="fi fi-id"></i></th>
                    <th width="25%"><i class="fi fi-us"></i></th>
                    <th width="10%">{{ __('config.app_language.input.date') }}</th>
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
                <div class="modal-title"><i class="ti ti-forms me-2"></i> {{ __('config.app_language.form.title') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm" class="form-validate">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="form-group row mb-2">
                        <label class="col-md-2 col-form-label">{{ __('config.app_language.input.group') }}</label>
                        <div class="col-md-10">
                            {{-- {{ Form::select('inp[al_group]', config('option.config.apps_groups'), '', ['class' => 'form-select', 'id' => 'al_group', 'data-rule-required' => 'true']) }} --}}
                            <select class="select2" name="inp[al_group]" id="al_group" data-rule-required="true">
                                <option value="common">Common</option>
                                @foreach ($groups as $key => $item)
                                    <option value="{{$item->amg_slug}}">{{$item->amg_name_id}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-2 col-form-label">{{ __('config.app_language.input.key') }}</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="inp[al_key]" id="al_key" data-rule-required="true">
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-2 col-form-label">{{ __('config.app_language.input.value_id') }}</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="inp[al_lang_id]" id="al_lang_id" data-rule-required="true">
                        </div>
                    </div>                    
                    <div class="form-group row mb-2">
                        <label class="col-md-2 col-form-label">{{ __('config.app_language.input.value_en') }}</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="inp[al_lang_en]" id="al_lang_en" data-rule-required="true">
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

<div class="modal fade" id="frmboxgenerate" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i> {{ __('config.app_language.form.generate_title') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-flush-spacing">
                <tbody>
                    <tr>
                        <td class="text-nowrap fw-semibold">Common</td>
                        <td><button class="btn btn-primary btn-sm" onclick="generate('common')"><i class="ti ti-transition-bottom me-2"></i> {{ __('config.app_language.form.generate') }}</button></td>
                    </tr>
                    @foreach ($groups as $key => $item)
                    <tr>
                        <td class="text-nowrap fw-semibold">{{$item->amg_name_id}}</td>
                        <td><button class="btn btn-primary btn-sm" onclick="generate('{{$item->amg_slug}}')"><i class="ti ti-transition-bottom me-2"></i> {{ __('config.app_language.form.generate') }}</button></td>
                    </tr>
                    @endforeach
                </tbody>
                </table>
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
let url = '{{ url('config/apps/languages') }}';

$(function() {
    dTable = $('.datatables-basic').DataTable({
        ajax: {
            url: url+'/dt',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d){
                d.group = $('#al_group_filter').val()
            }
        },
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false,class: 'text-center' },
            { data: 'al_key', name: 'al_key', orderable: true, searchable: true},
            { data: 'al_lang_id', name: 'al_lang_id', orderable: true, searchable: true},
            { data: 'al_lang_en', name: 'al_lang_en', orderable: true, searchable: true},
            { data: 'updated_at', name: 'updated_at', orderable: true, searchable: false}  
        ],
        order: [[4, 'desc']],
        drawCallback: function(){
            $('.languages').click(function(){
                let id = $(this).attr('id')
                $(this).addClass('d-none')

                $('#col-'+id).removeClass('d-none')
            })

            $('.languages-close').click(function(){
                let id = $(this).attr('id').replace('close-', '')
                $('#'+id).removeClass('d-none')
            })

            $('.languages-save').click(function(){
                let id = $(this).attr('id').replace('save-', '')
                $('#'+id).removeClass('d-none')
                $('#col-'+id).addClass('d-none')

                let formData = new FormData();
                if(id.includes('id')){
                    formData.append('inp[al_lang_id]', $('#input-'+id).val())
                }
                else {
                    formData.append('id', id.replace('lang-en-', ''))
                    formData.append('inp[al_lang_en]', $('#input-'+id).val())
                }
                
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
            })
        }
    }); 

    $('.dtb').append(`
    <div class="d-flex align-items-center" style="gap:10px">
        <button class="btn btn-openlib-red btn-sm" onclick="add()"><i class="ti ti-file-plus ti-sm me-2"></i> {{ __('common.add_data') }}</button>
        <button class="btn btn-info btn-sm" onclick="generate_show()"><i class="ti ti-adjustments ti-sm me-2"></i> {{ __('config.app_language.form.generate') }}</button>
        <div class="me-1" style="width:150px">
            <select class="select2 w-100" id="al_group_filter">
                <option value="">All</option>
                <option value="common">Common</option>
                @foreach ($groups as $key => $item)
                    <option value="{{$item->amg_slug}}">{{$item->amg_name_id}}</option>
                @endforeach
            </select>
        </div>
    </div>
    `)

    $('#al_group_filter').select2()
    $('#al_group_filter').change(function(){
        dTable.draw()
    })
});

function add() {
    _reset();
    $("#al_group").trigger('change')
    $("#frmbox").modal('show');
}

function generate_show() {
    $("#frmboxgenerate").modal('show');
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
                } else {
                    toastr.error("{{ __('common.message_save_title') }}", data.message, toastrOptions)
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

function copy(txt) 
{
    if (window.isSecureContext && navigator.clipboard) {
        navigator.clipboard.writeText(txt);
        toastr.success('Copy Data', 'Data berhasil di copy.', toastrOptions);
    } else {
        const textArea = document.createElement("textarea");
        textArea.value = txt;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            toastr.success('Copy Data', 'Data berhasil di copy.', toastrOptions);
        } catch (err) {
            toastr.error('Copy Data', 'Unable to copy to clipboard', toastrOptions);
        }
        document.body.removeChild(textArea);
    }   
}

function generate(id) {
    $.ajax({
        url: url+'/generate',
        type: 'post',
        data: {'group' : id},
        dataType: 'json',
        success: function(e) {
            if (e.status == 'success') {
                toastr.success('Generate Data', 'Data berhasil digenerate.', toastrOptions)
            }
            /*_reset();

            $('#id').val(id);
            $.each(e, function(key, value) {
                $('#' + key).val(value);
            });

            $('#frmbox').modal('show')*/
        }
    });
}  

</script>
@endsection