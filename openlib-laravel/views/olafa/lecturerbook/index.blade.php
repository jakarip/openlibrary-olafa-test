@extends('layouts/layoutMaster')

@section('title', 'Buku Dosen Telkom University')

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
    <div class="card-datatable table-responsive" >
        <table class="dt-scrollableTable table" id="table">
            <thead>
                <tr>
                    <th width="1%">{{ __('common.action') }}</th>
                    <th width="10%">No Katalog</th> 
                    <th width="10%">Jenis Buku </th> 
                    <th width="10%">Judul </th> 
                    <th width="10%">Pengarang </th> 
                    <th width="10%">Penerbit </th> 
                    <th width="10%">Tahun Terbit </th> 
                    <th width="10%">Unit </th> 
                    <th width="10%">ISBN </th> 
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
                <div class="modal-title"><i class="ti ti-forms me-2"></i> Tambah Buku Dosen Telkom University</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm" class="form-validate">
                    @csrf
                    <input type="hidden" name="id" id="id">

                    <div class="card mb-3">
                        <div class="card-header">
                            <h6>Tambah Buku Dosen</h6>
                        </div>

                        <div class="card-body">
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">No Katalog</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="inp[press_barcode]" id="press_barcode" data-rule-required="true"  >
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">Jenis Buku</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="inp[press_type]" id="press_type" data-rule-required="true" required>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">Judul</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="inp[press_title]" id="press_title" data-rule-required="true" required>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">Pengarang</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="inp[press_author]" id="press_author" data-rule-required="true" required>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">Penerbit</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="inp[press_publisher]" id="press_publisher" data-rule-required="true" required>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">Tahun Terbit</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="inp[press_published_year]" id="press_published_year" data-rule-required="true" required pattern="\d+">
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">Unit</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="inp[press_faculty_unit]" id="press_faculty_unit" data-rule-required="true" required>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">ISSN/ISBN</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="inp[press_isbn]" id="press_isbn" data-rule-required="true" required>
                                </div>
                            </div>
                        </div>


                    </div>

                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="save()">{{ __('common.save') }}</button>
                </div>
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
let url = '{{ url('olafa/lecturerbook') }}';

$(function() {
    dTable = $('.table').DataTable({
        pageLength: 10,
        ajax: {
            url: url+'/dt',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        },
        columns: [
            { data: 'action', name: 'action', class: 'text-center' },
            { data: 'press_barcode', name: 'press_barcode', },
            { data: 'press_type', name: 'press_type', },
            { data: 'press_title', name: 'press_title', },
            { data: 'press_author', name: 'press_author', },
            { data: 'press_publisher', name: 'press_publisher', },
            { data: 'press_published_year', name: 'press_published_year', },
            { data: 'press_faculty_unit', name: 'press_faculty_unit', },
            { data: 'press_isbn', name: 'press_isbn', },
        ],
        
    });

    $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> Tambah Buku Dosen
        </button>`)
});

function add() {
    _reset();
    $('#frmbox').modal('show');    
}


function edit(id) {
    $.ajax({
        url: url + '/get/' + id, 
        type: 'get',
        dataType: 'json',
        success: function(e) {
            _reset(); 
            
            $('#id').val(e.press_id); 
            $('#press_barcode').val(e.press_barcode); 
            $('#press_type').val(e.press_type); 
            $('#press_title').val(e.press_title); 
            $('#press_author').val(e.press_author); 
            $('#press_publisher').val(e.press_publisher); 
            $('#press_published_year').val(e.press_published_year); 
            $('#press_faculty_unit').val(e.press_faculty_unit); 
            $('#press_isbn').val(e.press_isbn); 
            


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
                if(data.status === 'success') {
                    $('#frmbox').modal('hide'); // Tutup modal jika berhasil menyimpan
                    dTable.draw();
                    toastr.success("{{ __('common.message_save_title') }}", "{{ __('common.message_success_save') }}", toastrOptions);
                } else if (data.status === 'error') {
                    // Menampilkan alert ketika ada error
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });

                    // Menutup modal walaupun ada error
                    $('#frmbox').modal('hide'); 
                }
            },
            error: function(xhr, status, error) {
                // Menangani error pada saat request
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat menyimpan data.'
                });

                // Menutup modal walaupun ada error
                $('#frmbox').modal('hide'); 
            }
        });
    } else {
        // Jika form tidak valid, tetap tutup modal
        // $('#frmbox').modal('hide'); 
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
                url: url + '/delete',
                data: { id: id, _token: '{{ csrf_token() }}' }, // Pastikan untuk menyertakan token CSRF
                type: 'delete',
                dataType: 'json',
                success: function(e) {
                    if (e.status == 'success') {
                        dTable.draw();
                        toastr.success("{{ __('common.message_delete_title') }}", "{{ __('common.message_success_delete') }}", toastrOptions);
                    } else {
                        // Jika ada error, tampilkan alert
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: e.message // Menampilkan pesan error dari server
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // Menangani kesalahan jika AJAX gagal
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan',
                        text: 'Gagal menghapus data. Silakan coba lagi.'
                    });
                }
            });
        }
    });
} 

</script>
@endsection