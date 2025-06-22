@extends('layouts/layoutMaster')

@section('title', 'Daftar Kartu Pengunjung')

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

<div class="row">
    <div class="col">
        <div class="nav-align-top nav-tabs-shadow mb-4">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="tab_satu-tab" data-bs-toggle="tab" href="#tab_satu" role="tab" aria-controls="tab_satu" aria-selected="true">rfid tidak ada di db</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="tab_dua-tab" data-bs-toggle="tab" href="#tab_dua" role="tab" aria-controls="tab_dua" aria-selected="false">rfid yang tidak ada nama anggota di db</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="tab_satu" role="tabpanel" aria-labelledby="tab_satu-tab">
                
                <table class="dt-scrollableTable table" id="tabel_satu">
                    <thead>
                        <tr>
                            <th width="1%">{{ __('common.action') }}</th>
                            <th width="10%">Username</th> 
                            <th width="10%">Fullname</th> 
                            <th width="10%">RFID</th> 
                        </tr>
                    </thead>
                    <tbody>
        
                    </tbody>
                </table>
                
            </div>
            <div class="tab-pane fade" id="tab_dua" role="tabpanel" aria-labelledby="tab_dua-tab">

                <table class="dt-scrollableTable table" id="tabel_dua">
                    <thead>
                        <tr>
                            <th width="1%">{{ __('common.action') }}</th>
                            <th width="10%">RFID</th> 
                            <th width="10%">Description</th> 
                        </tr>
                    </thead>
                    <tbody>
        
                    </tbody>
                </table>

            </div>
        </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_1" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i> Form Daftar Kartu Pengunjung</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm_satu" class="form-validate">
                    @csrf
                    
                    <input type="hidden" id="hiddenInputField" name="inp[username]">
                    <input type="hidden" id="id" name="inp[id]">
                    <input type="hidden" name="type" value="form_satu">

                    <div class="card mb-3">
                        
                        <div class="card-body" >
                            <div class="form-group row mb-4" id="typeaheadContainer">
                                <label for="TypeaheadBasic" class="col-md-3 col-form-label">Username</label>
                                <div class="col-md-9">
                                    <input id="TypeaheadBasic" class="form-control" type="text" autocomplete="off" placeholder="Cari Username..."  />
                                    <small class="text-secondary">Ketik Minimal 3 Huruf</small>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">RFID</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="inp[rfid]" id="rfid" data-rule-required="true" required>
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
<div class="modal fade" id="modal_2" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i>  Tambah gallery</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm_dua" class="form-validate">
                    @csrf
                    <input type="hidden" name="inp[id]" id="id">
                    <input type="hidden" name="type" value="form_dua">

                    <div class="card mb-3">
                        <div class="card-header">
                            <h6> Tambah gallery</h6>
                        </div>

                        <div class="card-body">
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">RFID</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="inp[rfid]" id="rfid" data-rule-required="true"  >
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">Deskripsi</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="inp[description]" id="description" data-rule-required="true" required>
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

let dTableSatu = null;
let dTableDua = null;
let url = '{{ url('olafa/rfidreg') }}';



$(document).ready(function() {
    let ajaxRequest;

    function updateButton() {
        if ($('#tab_satu-tab').attr('aria-selected') === 'true') {
            if ($.fn.DataTable.isDataTable('#tabel_dua')) {
                dTableDua.destroy(); // Destroy the previous table
            }
            dTableSatu = $('#tabel_satu').DataTable({
                pageLength: 10,
                searching: true,
                ajax: {
                    url: url+'/ajax_data',
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                },
                columns: [
                    { data: 'action', name: 'action', class: 'text-center' },
                    { data: 'username', name: 'username', orderable: true, searchable: true },
                    { data: 'master_data_fullname', name: 'master_data_fullname', orderable: true, searchable: true },
                    { data: 'rfid', name: 'rfid', orderable: true, searchable: true },
                ],
            });
            $('.dtb').append(`
                <button class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> Tambah RFID</button>
            `);
        } else if ($('#tab_dua-tab').attr('aria-selected') === 'true') {
            if ($.fn.DataTable.isDataTable('#tabel_satu')) {
                dTableSatu.destroy(); // Destroy the previous table
            }
            dTableDua = $('#tabel_dua').DataTable({
                pageLength: 10,
                searching: true,
                ajax: {
                    url: url+'/ajax_image',
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                },
                columns: [
                    { data: 'action', name: 'action', class: 'text-center' },
                    { data: 'rfid', name: 'rfid', orderable: true, searchable: true },
                    { data: 'description', name: 'description', orderable: true, searchable: true },
                ],
            });
            $('.dtb').append(`
                <button class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> Tambah Anggota</button>
            `);
        }
    }

    // Initial button update
    updateButton();

    // Update button on tab change
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        updateButton();
    });

    $('#TypeaheadBasic').typeahead(
            {
                hint: false,
                highlight: true,
                minLength: 3,
                
            },
            {
                name: 'members',
                source: function(query, syncResults, asyncResults) {
                // Synchronous suggestions (if any)
                syncResults([]);
                
                // Clear the previous timeout if it exists
                if (ajaxRequest) {
                    clearTimeout(ajaxRequest);
                }

                // Set a new timeout
                ajaxRequest = setTimeout(function() {
                        // Asynchronous suggestions
                        $.ajax({
                            url: url + '/autodata',
                            type: 'POST',
                            data: { q: query },
                            dataType: 'json',
                            success: function(data) {
                                asyncResults($.map(data, function(item) {
                                    return item;
                                }));
                            },
                        });
                    }, 500); 
                },
                display: function(item) {
                    return item.name;
                },
            }
    );
    
        $('#TypeaheadBasic').bind('typeahead:select', function(ev, suggestion) {
            $('#hiddenInputField').val(suggestion.id);
    });


});

function add() {
    _reset();

    if ($('#tab_satu-tab').attr('aria-selected') === 'true') {
        $('#modal_1').modal('show');
    } else if ($('#tab_dua-tab').attr('aria-selected') === 'true') {
        $('#modal_2').modal('show');
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
            $('#rfid').val(e.rfid);
            $('#hiddenInputField').val(e.username);

            // Disable the Typeahead input
            $('#typeaheadContainer').hide();

            $("#modal_1").modal('show');
        }
    });
}

function save() 
{
    let formData = null;

    if ($('#tab_satu-tab').attr('aria-selected') === 'true' && $("#frm_satu").valid()) {
        formData = new FormData($('#frm_satu')[0]);
        formData.append('type', 'form_satu');
    } else if ($('#tab_dua-tab').attr('aria-selected') === 'true' && $("#frm_dua").valid()) {
        formData = new FormData($('#frm_dua')[0]);
        formData.append('type', 'form_dua');
    }   

    if(formData)
    {
        $.ajax({
            url: url+'/save',
            type: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if(data.status === 'success') {

                    if ($('#tab_satu-tab').attr('aria-selected') === 'true') {
                        $('#modal_1').modal('hide');
                        dTableSatu.draw();
                    } else if ($('#tab_dua-tab').attr('aria-selected') === 'true') {
                        $('#modal_2').modal('hide');
                        dTableDua.draw();
                    }   

                    toastr.success("{{ __('common.message_save_title') }}", "{{ __('common.message_success_save') }}", toastrOptions);
                } else if (data.status === 'error') {
                    // Menampilkan alert ketika ada error
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });

                    // // Menutup modal walaupun ada error
                    // $('#frmbox').modal('hide'); 
                }
            },
            error: function(xhr, status, error) {
                // Menangani error pada saat request
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat menyimpan data.'
                });

                // // Menutup modal walaupun ada error
                // $('#frmbox').modal('hide'); 
            }
        });
    } else {
        // Jika form tidak valid, tetap tutup modal
        // $('#frmbox').modal('hide'); 
    }
}

function del(id)
{
    let type;
    if ($('#tab_satu-tab').attr('aria-selected') === 'true') {
        type = 'form_satu';
    } else if ($('#tab_dua-tab').attr('aria-selected') === 'true') {
        type = 'form_dua';
    }

    yswal_delete.fire({
        title: "{{ __('common.message_delete_prompt_title') }}",
        text: "{{ __('common.message_delete_prompt_text') }}"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url + '/delete',
                data: { id: id, type:type ,_token: '{{ csrf_token() }}' }, // Pastikan untuk menyertakan token CSRF
                type: 'delete',
                dataType: 'json',
                success: function(e) {
                    if (e.status == 'success') {
                        // Refresh the table based on the type
                        if (type === 'form_satu') {
                            dTableSatu.draw();
                        } else if (type === 'form_dua') {
                            dTableDua.draw();
                        }
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

function _reset() {
    $('input[type="text"], input[type="number"], input[type="email"], textarea').val('');
    $('#id').val('');
    $('#hiddenInputField').val('');
    $('#typeaheadContainer').show();
}

</script>
@endsection