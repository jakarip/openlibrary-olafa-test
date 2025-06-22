@extends('layouts/layoutMaster')

@section('title', ' Jurnal Internasional')

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
        z-index: 9999;
    }

    
</style>
@endsection

@section('content')

<div class="card">
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table border-top" id="table">
            <thead>
                <tr>
                    <th width="10%">Aksi</th>
                    <th width="45%">Jurnal</th>
                    <th width="45%">URL</th>
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
                <div class="modal-title"><i class="ti ti-forms me-2"></i> Form Jurnal Internasional</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm" class="form-validate">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6>Tambah Jurnal Internasional</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">Nama</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="inp[io_name]" id="io_name" data-rule-required="true">
                                    <small class="form-text text-muted">Nama Jurnal</small>
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">URL</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="inp[io_url]" id="io_url" data-rule-required="true">
                                    <small class="form-text text-muted">URL</small>
                                </div>
                            </div>
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
let url = '{{ url('olafa/jurnal-internasional') }}';

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
            { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' },
            {data: 'io_name', name: 'io_name', orderable: true, searchable: true },
            {data: 'io_url', name: 'io_url', orderable: true, searchable: true },
        ]
    });

    $('.dtb').append('<button class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> Tambah Jurnal Internasional Online	</button>');
});

function add() {
    _reset();
    $("#frmbox").modal('show');
}

function edit(id) {
    $.ajax({
        url: `${url}/get/${id}`,
        type: 'get',
        dataType: 'json',
        success: function(e) {
            _reset(); 
            
            $('#id').val(e.io_id); 
            $('#io_name').val(e.io_name); 
            $('#io_url').val(e.io_url);

            $("#frmbox").modal('show');
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
                    toastr.success("{{ __('common.message_save_title') }}", "{{ __('common.message_success_save') }}", toastrOptions);
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