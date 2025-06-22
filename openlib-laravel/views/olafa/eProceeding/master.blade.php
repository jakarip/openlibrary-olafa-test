@extends('layouts/layoutMaster')

@section('title', 'Edisi E-Proceeding')

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
                    <th width="15%">Aksi</th>
                    <th width="35%">Nama</th>
                    <th width="25%">Tanggal Awal</th>
                    <th width="25%">Tanggal Akhir</th>
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
                <div class="modal-title"><i class="ti ti-forms me-2"></i> Form Edisi E-Proceeding</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm" class="form-validate">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6>Tambah Edisi E-Proceeding</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">Nama</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="inp[nama]" id="nama" data-rule-required="true">
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label  class="col-md-3 col-form-label">Date Start</label>
                                <div class="col-md-9">
                                    <input class="form-control" type="date" value="" name="inp[datestart]" id="datestart" data-rule-required="true" />
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label  class="col-md-3 col-form-label">Date Finish</label>
                                <div class="col-md-9">
                                    <input class="form-control" type="date" value="" name="inp[datefinish]" id="datefinish" data-rule-required="true" />
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
let url = '{{ url('olafa/e-proceeding') }}';

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
            { data: 'nama', name: 'nama', orderable: true, searchable: true },
            { data: 'datestart', name: 'datestart', orderable: true, searchable: true },
            { data: 'datefinish', name: 'datefinish', orderable: true, searchable: true },
        ]
    });

    $('.dtb').append('<button class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> Tambah Edisi E-Proceeding	</button>');
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
            
            $('#id').val(e.eproc_edition_id); 
            $('#nama').val(e.nama); 
            $('#datestart').val(e.datestart); 
            $('#datefinish').val(e.datefinish);

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