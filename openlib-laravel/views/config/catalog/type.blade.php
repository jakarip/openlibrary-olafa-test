@extends('layouts/layoutMaster')

@section('title', __('config.catalog_type.page.title'))

@section('vendor-style')
@endsection

@section('page-style')
<style>
.highcharts-credits,
.highcharts-button {
    display: none;
}
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
                    <th>{{ __('config.catalog_type.input.name') }}</th>
                    <th>{{ __('config.catalog_type.input.form') }}</th> 
                    <th>{{ __('config.catalog_type.input.circulation') }}</th> 
                    <th>{{ __('config.catalog_type.input.active') }}</th> 
                    <th>{{ __('config.catalog_type.input.member_type') }}</th> 
                    <th>{{ __('config.catalog_type.input.total_catalog') }}</th> 
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="frmbox" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i> {{ __('config.catalog_type.form.title') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm" class="form-validate">
                    @csrf
                    <input type="hidden" name="id" id="id">

                    <!-- Card untuk Jenis Katalog -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6>{{ __('config.catalog_type.page.title') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">{{ __('config.catalog_type.input.name') }}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="inp[name]" id="name" data-rule-required="true">
                                    <small class="form-text text-muted">{{ __('config.catalog_type.input.name_desc') }}</small>
                                </div>
                            </div>

                            <div class="form-group row mb-2 align-items-center">
                                <label class="col-md-3 col-form-label">{{ __('config.catalog_type.input.active') }}</label>
                                <div class="col-md-9">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="inp[active]" id="active" value="1" onclick="toggleActive(this)">
                                        <label class="form-check-label" for="active">{{ __('config.catalog_type.input.active') }}</label>
                                    </div>
                                    <small class="form-text text-muted">{{ __('config.catalog_type.input.active_desc') }}</small>
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">{{ __('config.catalog_type.input.form')
                                    }}</label>
                                <div class="col-md-9">
                                    <select name="inp[type]" id="type" data-rule-required="true"
                                        class="select2 form-select form-select-lg">
                                        <option value="1">Softcopy</option>
                                        <option value="2">Hardcopy</option>
                                    </select>
                                    <small class="form-text text-muted">{{ __('config.catalog_type.input.form_desc') }}</small>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">{{ __('config.catalog_type.input.circulation')
                                    }}</label>
                                <div class="col-md-9">
                                    <select name="inp[rentable]" id="rentable" data-rule-required="true"
                                        class="select2 form-select form-select-lg">
                                        <option value="1">{{ __('config.catalog_type.input.circulation') }}</option>
                                        <option value="0">{{ __('config.catalog_type.input.non_circulation') }}</option>
                                    </select>
                                    <small class="form-text text-muted">{{ __('config.catalog_type.input.circulation_desc') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <h6>{{ __('config.catalog_type.input.member_type_label') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">{{ __('config.catalog_type.input.member_type_label') }}</label>
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label for="associated_members"><strong>{{ __('config.catalog_type.input.associated') }}</strong></label>
                                            <select id="associated_members" name="associated_members[]" class="form-control" multiple style="height: 200px;">
                                            </select>
                                        </div>
                                        
                                        
                                        <div class="col-md-2 d-flex flex-column align-items-center"> 
                                            <button type="button" class="btn btn-sm btn-primary mb-2" onclick="moveToAssociated()" style="font-size: 0.8rem;"><<</button>
                                            <button type="button" class="btn btn-sm btn-primary" onclick="moveToUnassociated()" style="font-size: 0.8rem;">>></button>
                                        </div>
                    
                                        <div class="col-md-5">
                                            <label for="unassociated_members"><strong>{{ __('config.catalog_type.input.unassociated') }}</strong></label>
                                            <select id="unassociated_members" class="form-control" multiple style="height: 200px;">
                                                @foreach ($members as $member)
                                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">{{ __('config.catalog_type.input.member_type_desc') }}</small>
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
let url = '{{ url('config/catalog-type') }}';

let originalData = {};  // Menyimpan nilai asli saat form dimuat
let changedData = {};   // Menyimpan field yang diubah

// Fungsi untuk menyimpan data asli
function setOriginalData() {
    originalData['type'] = $('#type').val();
    originalData['rentable'] = $('#rentable').val();
}

// Fungsi untuk mendeteksi perubahan pada form
function trackChanges() {
    $('#type').on('change', function() {
        if ($(this).val() != originalData['type']) {
            changedData['type'] = $(this).val();
        } else {
            delete changedData['type'];
        }
    });

    $('#rentable').on('change', function() {
        if ($(this).val() != originalData['rentable']) {
            changedData['rentable'] = $(this).val();
        } else {
            delete changedData['rentable'];
        }
    });
}

$(function() {
    dTable = $('.table').DataTable({
        ajax: {
            url: url + '/dt',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX error: ", textStatus, errorThrown);
                console.error("Response: ", jqXHR.responseText);
            }
        },
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' },
            { data: 'name', name: 'name', orderable: true, searchable: true },
            { data: 'type', name: 'type', orderable: false, searchable: false, 
                render: function(data) {
                    if (data == 1) {
                        return 'Softcopy';
                    } else if (data == 2) {
                        return 'Hardcopy';
                    } else {
                        return '-'; // Jika datanya bukan 1 atau 2, tampilkan placeholder
                    }
                }
            },
            { data: 'rentable', name: 'rentable', orderable: false, searchable: false, class: 'text-center', 
                render: function(data) {
                    if (data == 1) {
                        return '<i class="ti ti-square-check-filled ti-sm me-2" style="color: green;"></i>'; // Hijau untuk aktif
                    } else {
                        return '<i class="ti ti-square-check ti-sm me-2" style="color: gray;"></i>'; // Abu-abu untuk tidak aktif
                    }
                }
            },
            { data: 'active', name: 'active', orderable: false, searchable: false, class: 'text-center',
                render: function(data) {
                    if (data == 1) {
                        return '<i class="ti ti-square-check-filled ti-sm me-2" style="color: green;"></i>'; // Hijau untuk aktif
                    } else {
                        return '<i class="ti ti-square-check ti-sm me-2" style="color: gray;"></i>'; // Abu-abu untuk tidak aktif
                    }
                }
            },
            { data: 'member_types', name: 'member_types', orderable: false, searchable: false },
            { data: 'item_count', name: 'item_count', orderable: false, searchable: false }
        ]
    });

    @if(auth()->can('config-catalog-type.create'))
    $('.dtb').append(`
        <button class="btn btn-openlib-red btn-sm me-2" onclick="add()">
            <i class="ti ti-file-plus ti-sm me-1"></i> {{ __('config.catalog_type.form.add_text') }}
        </button>
    `);
    @endif

});

function add() {
    _reset();
    $(".timestampable-section").addClass('d-none');
    $("#frmbox").modal('show');
}

function save() 
{
    if($("#frm").valid())
    {
        let formData = new FormData($('#frm')[0]);

        // Tambahkan hanya field yang berubah ke formData
        for (let key in changedData) {
            formData.append(`inp[${key}]`, changedData[key]);
        }

        // Ambil data dari associated_members
        const associatedMembers = $('#associated_members option').map(function() {
            return this.value; // Ambil ID anggota
        }).get();
        formData.append('associated_members', JSON.stringify(associatedMembers)); // Mengirim data ke server

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

function edit(id) {
    $.ajax({
        url: url+'/get/'+id,
        type: 'get',
        data: { id: id },
        dataType: 'json',
        success: function(e) {
            if (e.status === 'success') {
                _reset();
                $('#id').val(e.type.id);
                $('#name').val(e.type.name);
                $('#type').val(e.type.type).trigger('change');
                $('#rentable').val(e.type.rentable).trigger('change');
                $('#active').prop('checked', e.type.active == 1);
                $('#updated_by').val(e.type.updated_by || ''); // Jika kosong, tampilkan string kosong
                $('#updated_at').val(formatDate(e.type.updated_at)); // Format tanggal
                $(".timestampable-section").removeClass('d-none');

                // Kosongkan daftar anggota yang terasosiasi
                $('#associated_members').empty();
                e.member_types.forEach(function(member) {
                    $('#associated_members').append(new Option(member.name, member.id, true, true)); // Menambahkan anggota yang terasosiasi
                });
                
                // Kosongkan daftar anggota yang tidak terasosiasi
                $('#unassociated_members').empty();
                e.all_members.forEach(function(member) {
                    if (!e.member_types.some(m => m.id == member.id)) {
                        $('#unassociated_members').append(new Option(member.name, member.id)); // Menambahkan anggota yang tidak terasosiasi
                    }
                });
                $('#frmbox').modal('show');
            }
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

function toggleActive(checkbox) {
    // Jika checkbox tidak dipilih, atur nilai active menjadi 0
    if (!checkbox.checked) {
        checkbox.value = 0; // Mengatur nilai menjadi 0 saat checkbox tidak terpilih
    } else {
        checkbox.value = 1; // Mengatur nilai menjadi 1 saat checkbox terpilih
    }
}

function moveToAssociated() {
    $('#unassociated_members option:selected').each(function() {
        $(this).remove().appendTo('#associated_members');
    });
}

function moveToUnassociated() {
    $('#associated_members option:selected').each(function() {
        $(this).remove().appendTo('#unassociated_members');
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

// Reset function to clear inputs and select fields
function _reset() {
    $('#frm')[0].reset(); // Reset form fields
    $('.select2').val(null).trigger('change'); // Reset Select2 fields
    $('#updated_by').val(''); // Clear updated_by field
    $('#updated_at').val(''); // Clear updated_at field
}

</script>
@endsection
