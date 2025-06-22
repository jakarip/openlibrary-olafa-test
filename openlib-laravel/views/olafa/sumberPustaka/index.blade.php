@extends('layouts/layoutMaster')

@section('title', 'Perpustakaan Mitra')

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
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0"> Perpustakaan Mitra </h5>
        </div>
        <table class="dt-row-grouping table border-top" id="table">
            <thead>
                <tr>
                    <th>Logo</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Nomor</th>
                    <th>Fax</th>
                    <th>Link</th>
                    <th width="13%">Aksi</th>
                </tr>
            </thead>
            
        </table>
    </div>
</div>

<div class="modal fade" id="frmbox" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i> Form Perpustakaan Mitra</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="frm" class="form-validate" enctype="multipart/form-data" >
                @csrf
                <input type="hidden" id="id" name="id">
                <div class="modal-body">
                    
                    <div class="card mb-3">

                        <div class="card-header">
                            <h6>Perpustakaan Mitra</h6>
                        </div>

                        <div class="card-body">
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">Library Name</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[library_name]" id="library_name"></input>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">Address</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[address]" id="address"></input>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">Phone Number</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[phone_number]" id="phone_number"></input>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">Fax Number</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[fax_number]" id="fax_number"></input>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">Website Link</label>
                                <div class="col-md-9">
                                    {{-- type="url" --}}
                                    <input class="form-control"  name="inp[website_link]" id="website_link"></input>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">Logo</label>
                                <div class="col-md-9">
                                    <input class="form-control" type="file" name="inp[logo]" id="logo_path">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="save()">{{ __('common.save') }}</button>
                </div>
            </form>
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
let url = '{{ url('olafa/sumber-pustaka')}}';

$(function() {
    dTable = $('#table').DataTable({
        ajax: {
            url: url + '/dt',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        },
        columns: [
            { data: 'logo_name', name: 'logo_name', orderable: false, searchable: false, render: function(data, type, row) {
                if (data) {
                const filePath = `storage/olafa/mitra/${data}`;
                return `<img src="{{ url('${filePath}') }}" alt="Logo" style="width:50px;height:auto;">`;
            }
            return 'No Logo';
            }},
            { data: 'library_name', name: 'library_name', orderable: true, searchable: true },
            { data: 'address', name: 'address', orderable: true, searchable: true },
            { data: 'phone_number', name: 'phone_number', orderable: true, searchable: true },
            { data: 'fax_number', name: 'fax_number', orderable: true, searchable: true },
            { data: 'website_link', name: 'website_link', orderable: false, searchable: true, render: function(data, type, row) {
                return `<a href="${data}" target="_blank">${data}</a>`;
            }},
            {data: 'action', name: 'action', orderable: false, searchable: false, },
        ],
    });
    
    $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> Tambah Mitra </button>`)
});

function add() {
    _reset();
    $('#frmbox').modal('show');            
}


function save() {
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
        $('#frmbox').modal('hide'); 
    }
}

function edit(id) {
    $.ajax({
        url: url + '/get/' + id, 
        type: 'get',
        dataType: 'json',
        success: function(e) {
            _reset(); 
            
            $('#id').val(e.id); 
            $('#library_name').val(e.library_name); 
            $('#address').val(e.address);
            $('#phone_number').val(e.phone_number);
            $('#fax_number').val(e.fax_number);
            $('#website_link').val(e.website_link);

            $("#frmbox").modal('show');
        }
    });
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
