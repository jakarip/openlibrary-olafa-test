@extends('layouts/layoutMaster')

@section('title', 'Pengadaan Dosen/Pegawai')

@section('vendor-style')
@endsection

@section('page-style')
<style>

    .select2-container {
            z-index: 99999;
        }
    
        .card {
            z-index: 0;
        }
    
        /* table.dataTable.dt-select-no-highlight tbody tr.selected,
        table.dataTable.dt-select-no-highlight tbody th.selected,
        table.dataTable.dt-select-no-highlight tbody td.selected {
            color: unset;
        }
        table.dataTable.dt-select-no-highlight tbody>tr.selected,
        table.dataTable.dt-select-no-highlight tbody>tr>.selected {
            background-color: unset;
        } */
    
    </style>
    
@endsection

@section('content')

<div class="card">
    <h5 class="card-header">Advanced Search</h5>
    <!--Search Form -->
    <div class="card-body">
        <form class="dt_adv_search">
            <div class="row">
                <div class="col-12">
                    <div class="row g-3">

                        <div class="col-12 col-sm-6 col-lg-4">
                            <label for="status" class="form-label">Pilih Status:</label>
                            <select id="status" class="select2 form-select form-select-md" >
                                <option default value="">Semua Status</option>
                                <option value="Request">Request</option>
                                <option value="Approved">Approved</option>
                                <option value="Not Approved">Not Approved</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3 " id="">
                            <label for="created_date_option" class="form-label">Pilih Tanggal Pembuatan:</label>
                            <select id="created_date_option" class="select2 form-select form-select-md">
                                <option default value="all">Semua Tanggal Pembuatan</option>
                                <option value="date">Range Tanggal Pembuatan</option>
                            </select>
                        </div>
                        
                        <div class="col-12 col-sm-6 col-lg-3 d-none" id="created_dateDiv" >
                            <label for="created_date" class="form-label">Pilih Range Tanggal Pembuatan:</label>
                            <input type="text" id="created_date" class="form-control"/>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-1 d-flex align-items-end col-auto">
                            <button id="search-btn" class="btn btn-primary">Cari</button>
                        </div>
                        
                    </div>
                </div>
            </div>
        </form>
    </div>

    <hr class="mt-0">
    <div class="card-datatable " >
            <table class="table table-bordered table-striped dataTable no-footer nowrap" id="table">
                <thead>
                    <tr>   
                        <th >Aksi</th>
                        <th >Status</th>
                        <th >Alasan Ditolak</th> 
                        <th >File Approval Kaprodi</th>
                        <th >File RPS</th>
                        <th >Tanggal</th>
                        <th >Fakultas</th>
                        <th >Prodi</th>
                        <th >NIK</th>
                        <th >Nama</th>
                        <th >Judul</th>
                        <th >Pengarang</th>
                        <th >Penerbit</th>
                        <th >Tahun terbit</th>
                        <th >Matakuliah</th>   
                        <th >Reference</th>   
                    </tr>
                </thead>
                <tbody>
    
                </tbody>
            </table>
    </div>
    

</div>


<div class="modal fade" id="frmbox" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i> Form Data Pengajuan Buku</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="frm" class="form-validate" enctype="multipart/form-data">
                @csrf
                {{-- <input type="hidden" id="hiddenInputField" name="inp[book_id]"> --}}
                <input type="hidden" id="id" name="id">

                <div class="modal-body">
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6>Pengajuan Buku</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group row mb-4">
                                <label for="select_prodi" class="col-md-3 col-form-label">Pilih Prodi</label>
                                <div class="col-md-9">
                                    <select id="select_prodi" name="inp[bp_prodi_id]" class="select2 form-select form-select-md" >
                                        @foreach($prodi as $item)
                                            <option value="{{ $item->C_KODE_PRODI }}" data-fakultas="{{ $item->C_KODE_FAKULTAS }}">
                                                {{ucwords(strtolower($item->NAMA_FAKULTAS))}} - {{ $item->NAMA_PRODI }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="bp_matakuliah" class="col-md-3 col-form-label">Mata Kuliah</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[bp_matakuliah]" id="bp_matakuliah" >
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="bp_title" class="col-md-3 col-form-label">Judul Buku</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[bp_title]" id="bp_title" >
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="bp_author" class="col-md-3 col-form-label">Pengarang</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[bp_author]" id="bp_author" >
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="bp_publisher" class="col-md-3 col-form-label">Penerbit</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[bp_publisher]" id="bp_publisher" >
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="bp_publishedyear" class="col-md-3 col-form-label">Tahun Terbit</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[bp_publishedyear]" id="bp_publishedyear" >
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="" class="col-md-3 col-form-label">Reference</label>
                                <div class="col-md-9">
                                    <div class="form-check mt-3">

                                        <input name="inp[bp_reference]" class="form-check-input" type="radio" value="Utama" id="defaultRadio1" />
                                        <label class="form-check-label" for="defaultRadio1">
                                        Utama
                                        </label>
                                        
                                    </div>
                                    <div class="form-check mt-3">
                                        <input name="inp[bp_reference]" class="form-check-input" type="radio" value="Pendukung" id="defaultRadio2" />
                                        <label class="form-check-label" for="defaultRadio2">
                                        Pendukung
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label for="formFileApproval" class="col-md-3 col-form-label">Upload File Approval Kaprodi</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="formFileApproval" type="file" id="formFileApproval">
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="formFileRPS" class="col-md-3 col-form-label">Upload File RPS</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="formFileRPS" type="file" id="formFileRPS">
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


<div class="modal fade" id="modal_approve" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i> Approved</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="frm_approved" class="form-validate">
                @csrf
                <input type="hidden" id="id" name="id">
                <input type="hidden" name="bp_status" id="bp_status">
                
                <div class="modal-body">
                    <div>
                        Apakah anda yakin akan mengubah status menjadi <strong>Approved </strong> ?
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-openlib-red waves-effect waves-light" onclick="approved()">{{ __('common.update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="modal_not_approved" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i>Not Approved</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="frm_not_approved" class="form-validate">
                @csrf
                <input type="hidden" id="id" name="id">
                <input type="hidden" name="bp_status" id="bp_status">
                
                <div class="modal-body">
                    <div class="mb-4">
                        Apakah anda yakin akan mengubah status menjadi <strong> Not Approved </strong> ?
                    </div>

                    <div class="form-group row mb-4">
                        <label for="option_reason" class="col-md-3 col-form-label">Pilih Alasan</label>
                            <div class="col-md-9">
                                <select id="option_reason" name="option_reason" class="select2 form-select form-select-md" >
                                    <option value="Judul Buku sudah ada di Open Library">Judul Buku sudah ada di Open Library</option>
                                    <option value="Judul buku tidak ditemukan di penerbit manapun">Judul buku tidak ditemukan di penerbit manapun</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>

                                <div class="mt-3" id="option_lain" style="display:none">
                                    <textarea class="form-control" name="bp_reason" id="bp_reason"></textarea>
                                </div>
                            </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-openlib-red waves-effect waves-light" onclick="notApproved()">{{ __('common.update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection

@section('vendor-script')
@endsection

@section('page-script')

<script>

let dTable = null;
let url = '{{ url('pengadaan/dosen-pegawai') }}';

var selectedIds = [];
var startDate ;
var endDate ;

$(function(){


    dTable = $('#table').DataTable({
        pageLength: 25,
        ajax: {
            url: url+'/dt',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.status = $('#status').val();
                d.created_date_option = $('#created_date_option').val();
                d.created_date = $('#created_date').val();
            },
        },
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'bp_reason', name: 'bp_reason', orderable: true, searchable: true },
            {
                data: 'bp_approval_kaprodi_file',
                name: 'bp_approval_kaprodi_file',
                orderable: true,
                searchable: true,
                render: function(data) {
                    if (data) {
                        return `<a href="/storage/pengadaan/byUser/approval/${data}" target="_blank" >Download</a>`;
                    }
                    return '<span class="text-muted"></span>';
                }
            },
            {
                data: 'bp_rps_file',
                name: 'bp_rps_file',
                orderable: true,
                searchable: true,
                render: function(data) {
                    if (data) {
                        return `<a href="/storage/pengadaan/byUser/rps/${data}" target="_blank" >Download</a>`;
                    }
                    return '<span class="text-muted"></span>';
                }
            },
            { data: 'bp_createdate', name: 'bp_createdate', orderable: true, searchable: true },
            { data: 'nama_fakultas', name: 'nama_fakultas', orderable: true, searchable: true },
            { data: 'nama_prodi', name: 'nama_prodi', orderable: true, searchable: true },
            { data: 'master_data_number', name: 'master_data_number', orderable: true, searchable: true },
            { data: 'master_data_fullname', name: 'master_data_fullname', orderable: true, searchable: true },
            { data: 'bp_title', name: 'bp_title', orderable: true, searchable: true },
            { data: 'bp_author', name: 'bp_author', orderable: true, searchable: true },
            { data: 'bp_publisher', name: 'bp_publisher', orderable: true, searchable: true },
            { data: 'bp_publishedyear', name: 'bp_publishedyear', orderable: true, searchable: true },
            { data: 'bp_matakuliah', name: 'bp_matakuliah', orderable: true, searchable: true },
            { data: 'bp_reference', name: 'bp_reference', orderable: true, searchable: true },
        ],
        responsive: false,
        scrollX: true,
        
    });

    $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> Tambah Data </button>`)

    // $('#created_date_option').datepicker({
    //     todayHighlight: true,
    //     orientation: 'top',
    //     format: 'dd/mm/yyyy',
    //     }, 
    // );
    
    
})

$(document).ready(function(){

    $('#search-btn').on('click', function(event) {
        event.preventDefault();
        // sendAjaxRequest();
        dTable.ajax.reload();
    });

    $('#created_date_option').on('change', function() {
        var created_dateDiv = $('#created_dateDiv');
        if ($(this).val() === 'date') {
            created_dateDiv.removeClass('d-none');
        } else {
            created_dateDiv.addClass('d-none');
        }
    });

    $('#created_date').daterangepicker({
        // autoUpdateInput: false,
        ranges: {
            Today: [moment(), moment()],
            Yesterday: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        },
        showDropdowns: true,
        opens: isRtl ? 'left' : 'right',
        }, function(start, end, label) {
                startDate = start;
                endDate = end;
    });
})

function add() {
    resetForm();
    $('#frmbox').modal('show');
}

function save(){
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

function modalApproved(id,data) { 
    resetForm();
	$('#modal_approve').modal('show'); 
	$('#modal_approve #id').val(id); 
	$('#modal_approve #bp_status').val(data); 	
} 

function modalNotApproved(id,data) { 
    $('#modal_not_approved #id').val('');
    $('#modal_not_approved #bp_status').val('');
    $('#modal_not_approved #option_reason').val('').trigger('change');
    $('#modal_not_approved #bp_reason').val('').prop('required', false);
    $('#modal_not_approved #option_lain').hide();
    
	$('#modal_not_approved').modal('show');
	$('#modal_not_approved #id').val(id);  
	$('#modal_not_approved #bp_status').val(data); 

	$("#modal_not_approved #option_reason").change(function(){
		if($(this).val()=='lainnya'){
			$("#modal_not_approved #option_lain").show();
			$("#modal_not_approved #bp_reason").prop('required',true);
		}
		else { 
			$("#modal_not_approved #option_lain").hide();
			$("#modal_not_approved #bp_reason").prop('required',false);
		}
	});
} 

function approved() {
    if($("#frm_approved").valid())
    {
        let formData = new FormData($('#frm_approved')[0]);

        $.ajax({
            url: url+'/approved',
            type: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if(data.status === 'success') {
                    $('#modal_approve').modal('hide'); // Tutup modal jika berhasil menyimpan
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
                    $('#modal_approve').modal('hide');
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
                $('#modal_approve').modal('hide');
            }
        });
    } else {
        // Jika form tidak valid, tetap tutup modal
        // $('#frmbox').modal('hide');
    }
}

function notApproved() {
    if($("#frm_not_approved").valid())
    {
        let formData = new FormData($('#frm_not_approved')[0]);

        $.ajax({
            url: url+'/not-approved',
            type: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if(data.status === 'success') {
                    $('#modal_not_approved').modal('hide'); // Tutup modal jika berhasil menyimpan
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
                    $('#modal_not_approved').modal('hide');
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
                $('#modal_not_approved').modal('hide');
            }
        });
    } else {
        // Jika form tidak valid, tetap tutup modal
        // $('#modal_not_approved').modal('hide');
    }
}


function resetForm() {
    // Reset all form inputs
    $('#frm').trigger('reset');

    // Clear select2 fields
    // $('.select2').val(null).trigger('change');
}
</script>

@endsection