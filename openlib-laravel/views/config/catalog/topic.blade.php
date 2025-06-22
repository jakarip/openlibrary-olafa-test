@extends('layouts/layoutMaster')

@section('title', __('config.topic.page.title'))

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
                    <th width="30%">{{ __('config.topic.input.name') }}</th> 
                    <th width="5%">{{ __('config.topic.input.active') }}</th> 
                    <th>{{ __('config.catalog_type.input.total_catalog') }}</th> 
                    <th>{{ __('common.updated_at') }}</th> 
                    <th>{{ __('common.updated_by') }}</th> 
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
                <div class="modal-title"><i class="ti ti-forms me-2"></i> {{ __('config.topic.form.title') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm" class="form-validate">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <!-- Card untuk Topik Katalog -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6>{{ __('config.topic.page.title') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">{{ __('config.topic.input.name') }}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="inp[name]" id="name" data-rule-required="true">
                                    <small class="form-text text-muted">{{ __('config.topic.input.name_desc') }}</small>
                                </div>
                        
                            </div>

                            <div class="form-group row mb-2 align-items-center">
                                <label class="col-md-3 col-form-label">{{ __('config.topic.input.active') }}</label>
                                <div class="col-md-9">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="inp[active]" id="active" value="1" onclick="toggleActive(this)">
                                        <label class="form-check-label" for="active">{{ __('config.topic.input.active') }}</label>
                                    </div>
                                    <small class="form-text text-muted">{{ __('config.topic.input.active_desc') }}</small>
                                </div>
                            </div>
                            
                            <!-- Hidden field to store the jumlah_katalog value -->
                            <input type="hidden" id="jumlah_katalog" value="">
                        </div>

                        
                    </div>
                </form>
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
let url = '{{ url('config/catalog-topics') }}';

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
                { data: 'name', name: 'name', orderable: true, searchable: true} ,
                {
                data: 'active',
                name: 'active',
                orderable: false,
                searchable: false,
                class: 'text-center',
                render: function(data) {
                        if (data == 1) {
                            return '<i class="ti ti-square-check-filled ti-sm me-2" style="color: green;"></i>'; // Hijau untuk aktif
                        } else {
                            return '<i class="ti ti-square-check ti-sm me-2" style="color: gray;"></i>'; // Abu-abu untuk tidak aktif
                        }
                    }
                },
                { data: 'jumlah_katalog', name: 'jumlah_katalog', orderable: false,class: 'text-center', searchable: false },
                { data: 'updated_at', name: 'updated_at',orderable: false,searchable: false,
                render: function(data) {
                    return formatDate(data); // Panggil fungsi formatDate
                }
                },
                { data: 'updated_by', name: 'updated_by', orderable: false, searchable: false },
            ]

        });

        @if(auth()->can('config-catalog-topics.create'))
        $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> {{ __('config.topic.form.add_text') }}</button>`)
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
            $('#name').val(e.name);

            // Logic Checkbox
            $('#active').prop('checked', e.active == 1);
            $('#active').val(e.active); // Set the initial value of the checkbox

            // Set jumlah_katalog value for validation
            $('#jumlah_katalog').val(e.jumlah_katalog);

            // Tampilkan bagian timestamp jika ada
            $(".timestampable-section").removeClass('d-none');

            // Cek nilai dan set ke input
            $('#updated_by').val(e.updated_by || ''); // Tampilkan kosong jika tidak ada
            $('#updated_at').val(formatDate(e.updated_at));

            $('#frmbox').modal('show');
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
                            text: e.message
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

function toggleActive(checkbox) {
    checkbox.value = checkbox.checked ? 1 : 0;
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