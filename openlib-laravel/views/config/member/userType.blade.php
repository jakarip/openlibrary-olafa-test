@extends('layouts/layoutMaster')

@section('title', __('config.user_type.page.title'))

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
                    <th width="10%">{{ __('common.action') }}</th>
                    <th width="10%">{{ __('config.user_type.input.name') }}</th> 
                    <th width="10%">{{ __('config.user_type.input.total_member') }}</th> 
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
                <div class="modal-title"><i class="ti ti-forms me-2"></i> {{ __('config.user_type.form.title') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <form id="frm" class="form-validate">
            @csrf
            <input type="hidden" name="id" id="id">
            
            <div class="card mb-3">
                <div class="card-header">
                    <h6>{{ __('config.user_type.page.title') }}</h6>
                </div>
                <div class="card-body">
                        <div class="form-group row mb-4">
                            <label class="col-md-3 col-form-label">{{ __('config.user_type.page.title') }}</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="inp[name]" id="name" data-rule-required="true">
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
let url = '{{ url('config/user-type') }}';

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
        { data: 'name', name: 'name', orderable: true, searchable: true },
        { data: 'total_items', name: 'total_items', orderable: true, searchable: true , class: 'text-center'}, 
        ]

    });
    @if(auth()->can('config-user-type.create'))
        $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> {{ __('config.user_type.form.add_text') }}</button>`)
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
            $.each(e, function(key, value) { 
                if ($('#' + key).hasClass("select2"))
                    $('#' + key).val(value).trigger('change');
                else
                $('#' + key).val(value); 
            });
            $(".timestampable-section").removeClass('d-none'); // Tampilkan bagian timestamp jika ada
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
                if(data.status == 'success'){
                    $('#frmbox').modal('hide');
                    $('#frmbox').modal('hide'); // Tutup modal jika berhasil menyimpan
                    dTable.draw();
                    toastr.success("{{ __('common.message_save_title') }}", "{{ __('common.message_success_save') }}", toastrOptions)
                }else if (data.status === 'error') {
                    // Menampilkan alert ketika ada error
                    Swal.fire({
                        icon: 'error',
                        title: "{{ __('common.message_error_title') }}",
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
    })
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