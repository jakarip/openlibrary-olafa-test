@extends('layouts/layoutMaster')

@section('title', __('config.classification.page.title'))

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
                    <th width="10%">{{ __('config.classification.input.code') }}</th> 
                    <th width="50%">{{ __('config.classification.input.subject') }}</th> 
                    <th width="10%">{{ __('config.catalog_type.input.total_catalog') }}</th> 
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
                <div class="modal-title"><i class="ti ti-forms me-2"></i> {{ __('config.classification.form.title') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <form id="frm" class="form-validate">
            @csrf
            <input type="hidden" name="id" id="id">
            <!-- Card untuk Kode Klasifikasi -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6>{{ __('config.classification.page.title') }}</h6>
                </div>
                <div class="card-body">
                        <div class="form-group row mb-4">
                            <label class="col-md-3 col-form-label">{{ __('config.classification.input.code') }}</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="inp[code]" id="code" data-rule-required="true">
                            </div>                            
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-md-3 col-form-label">{{ __('config.classification.input.subject') }}</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="inp[name]" id="name" data-rule-required="true">
                            </div>                            
                        </div>                 

                        <div class="form-group row mb-4">
                            <label class="col-md-3 col-form-label">{{ __('config.classification.input.description') }}</label>
                            <div class="col-md-9">
                                <textarea class="form-control" name="inp[description]" id="description" rows="4" data-rule-required="true"></textarea>
                            </div>
                        </div>
                        
                        
                </div>
            </div>
    
            <!-- Card untuk Timestampable -->
            <div class="card timestampable-section d-none">
                <div class="card-header">
                    <h6>Timestampable</h6>
                </div>
                <div class="card-body">
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('common.updated_by') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="updated_by" id="updated_by" readonly>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('common.updated_at') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="updated_at" id="updated_at" readonly>
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
let url = '{{ url('config/catalog-classification') }}';

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
        { data: 'code', name: 'code', orderable: true, searchable: true }, 
        { data: 'name', name: 'name', orderable: true, searchable: true }, 
        { data: 'total_items', name: 'total_items', orderable: true, searchable: true , class: 'text-center'}, 
        ]

    });
    @if(auth()->can('config-catalog-classification.create'))
    $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> {{ __('config.classification.form.add_text') }}</button>`)
    @endif
});

function add() {
    _reset();
    $(".timestampable-section").addClass('d-none'); // Sembunyikan timestamp
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
            $('#code').val(e.code);
            $('#name').val(e.name);
            $('#description').val(e.description);

            // Tampilkan bagian timestamp jika ada
            $(".timestampable-section").removeClass('d-none');

             // format tanggal form edit
             $('#updated_by').val(e.updated_by || ''); // Tampilkan kosong jika tidak ada
            $('#updated_at').val(formatDate(e.updated_at));

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
                    title: "{{ __('common.message_error_title') }}",
                    text: "{{ __('common.message_failed_save') }}"
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
                            title: "{{ __('common.message_error_title') }}",
                            text: e.message // Menampilkan pesan error dari server
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // Menangani kesalahan jika AJAX gagal
                    Swal.fire({
                        icon: 'error',
                        title: "{{ __('common.message_error_title') }}",
                        text: "{{ __('common.message_failed_delete') }}"
                    });
                }
            });
        }
    });
} 

function formatDate(dateString) {
    // Cek jika data kosong atau tanggal invalid
    if (!dateString || dateString === '-000001-11-30T00:00:00.000000Z') {
        return ''; // Mengembalikan string kosong untuk data tidak valid
    }

    const date = new Date(dateString);
    if (isNaN(date.getTime())) {
        return ''; // Jika tanggal tidak valid, kembalikan string kosong
    }

    const day = String(date.getDate()).padStart(2, '0'); // Tambahkan nol di depan hari jika perlu
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Bulan dimulai dari 0
    const year = date.getFullYear();
    return `${day}-${month}-${year}`; // Format DD-MM-YYYY
}

</script>
@endsection