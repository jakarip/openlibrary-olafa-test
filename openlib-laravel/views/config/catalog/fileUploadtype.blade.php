@extends('layouts/layoutMaster')

@section('title', __('config.file_type.page.title'))

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
                    <th>{{ __('config.file_type.input.keyword') }}</th> 
                    <th>{{ __('config.file_type.input.extension') }}</th> 
                    <th>{{ __('config.file_type.input.title') }}</th> 
                    <th>{{ __('config.file_type.input.member_only') }}</th> 
                    <th>Readonly</th>
                    <th>Download</th>
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
                <div class="modal-title"><i class="ti ti-forms me-2"></i> {{ __('config.file_type.form.title') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm" class="form-validate">
                        @csrf
                        <input type="hidden" name="id" id="id">
                        <!-- Card untuk Jenis Upload -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6>{{ __('config.file_type.page.title') }}</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group row mb-4">
                                    <label class="col-md-3 col-form-label">{{ __('config.file_type.input.keyword') }}</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="name" id="name" data-rule-required="true">
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label class="col-md-3 col-form-label">{{ __('config.file_type.input.extension') }}</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="extension" id="extension" data-rule-required="true">
                                    </div>
                                </div>
                                <div class="form-group row mb-4">
                                    <label class="col-md-3 col-form-label">{{ __('config.file_type.input.title') }}</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="title" id="title" data-rule-required="true">
                                    </div>
                                </div>
                                <div class="form-group row mb-2 align-items-center">
                                    <label class="col-md-3 col-form-label">{{ __('config.file_type.input.member_only') }}</label>
                                    <div class="col-md-9">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_secure" id="is_secure" value="1">
                                            <label class="form-check-label" for="is_secure">{{ __('config.file_type.input.member_only') }}</label>
                                        </div>
                                    </div>
                                </div>        
                            </div>
                        </div>

                        


                        <div class="card mb-3">
                            <div class="card-header">
                                <div class="card-body ">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- Header with Member, Read Only, and Download -->
                                            <div class="d-flex align-items-center mb-1">
                                                <div class="flex-grow-1">
                                                    <small class="text-normal fw-medium d-block">{{ __('config.file_type.input.member_access') }}</small>
                                                </div>
                                                <div class="ms-2">
                                                    <small class="text-normal fw-medium d-block">Read Only</small>
                                                </div>
                                                <div class="ms-2">
                                                    <small class="text-normal fw-medium d-block">Download</small>
                                                </div>
                                            </div>
                                    
                                            <!-- Member Access Rows -->
                                            <ul class="list-group list-group-flush">
                                                @foreach($members as $member)
                                                    <li class="list-group-item d-flex align-items-center py-0 px-1">
                                                        <!-- Member Name -->
                                                        <div class="flex-grow-1">
                                                            <small>{{ $member->name }}</small>
                                                        </div>
                                            
                                                        <!-- Read Only Checkbox -->
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" style="transform: scale(0.8);"
                                                                type="checkbox" id="readOnly_{{ $member->id }}"
                                                                value="read_only_{{ $member->id }}"
                                                                onclick="toggleCheckBox('readOnly', {{ $member->id }})">
                                                        </div>
                                            
                                                        <!-- Download Checkbox -->
                                                        <div class="form-check form-check-inline ">
                                                            <input class="form-check-input" style="transform: scale(0.8);"
                                                                type="checkbox" id="download_{{ $member->id }}"
                                                                value="download_{{ $member->id }}"
                                                                onclick="toggleCheckBox('download', {{ $member->id }})">
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            
                                        </div>
                                    </div>
                                </div>
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
let url = '{{ url('config/file-upload-type') }}';

// Global variable to store selected member ids
let selectedReadOnly = [];
let selectedDownload = [];

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
            { data: 'name', name: 'name', orderable: false, searchable: true },
            { data: 'extension', name: 'extension', orderable: false, searchable: false },
            { data: 'title', name: 'title', orderable: false, searchable: false },
            {
                data: 'is_secure',
                name: 'is_secure',
                orderable: false,
                searchable: false,
                class: 'text-center',
                render: function(data) {
                    if (data == 1) {
                        return '<i class="ti ti-square-check-filled ti-sm me-2" style="color: green;"></i>'; // Green for active
                    } else {
                        return '<i class="ti ti-square-check ti-sm me-2" style="color: gray;"></i>'; // Gray for inactive
                    }
                }
            },
            { data: 'member_readonly', name: 'member_readonly', orderable: false, searchable: false },
            { data: 'member_download', name: 'member_download', orderable: false, searchable: false }
        ]
    });
    @if(auth()->can('file-upload-type.create'))
    // Append a button for adding new items
    $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> {{ __('config.file_type.form.add_text') }}</button>`);
    @endif

});

function add() {
    _reset();
    $(".timestampable-section").addClass('d-none'); // Sembunyikan timestamp
    selectedReadOnly = []; // Clear selected read-only array
    selectedDownload = []; // Clear selected download array
    $('input[type=checkbox]').prop('checked', false); // Uncheck all checkboxes
    $("#frmbox").modal('show');// Show the modal
}

// When opening the modal for editing
function edit(id) {
    $.ajax({
        url: url + '/get/' + id, // Route /get/{id}
        type: 'get',
        dataType: 'json',
        success: function(e) {
            _reset(); // Reset form
            // Populate form fields
            $('#id').val(e.uploadType.id);
            $('#name').val(e.uploadType.name);
            $('#extension').val(e.uploadType.extension);
            $('#title').val(e.uploadType.title);
            $('#is_secure').prop('checked', e.uploadType.is_secure == 1);
            $(".timestampable-section").removeClass('d-none'); // Tampilkan bagian timestamp jika ada
            $('#updated_by').val(e.uploadType.updated_by || ''); 
            $('#updated_at').val(formatDate(e.updated_at));
            // Set checkbox based on memberTypeUpload and memberTypeReadOnly
            e.members.forEach(function(member) {
                $('#readOnly_' + member.id).prop('checked', e.memberTypeReadOnly.includes(member.id));
                $('#download_' + member.id).prop('checked', e.memberTypeUpload.includes(member.id));
            });
            $('#frmbox').modal('show'); // Show modal
        },
    });
}

// Function to handle checkbox toggle
function toggleCheckBox(type, memberId) {
    if (type === 'readOnly') {
        const index = selectedReadOnly.indexOf(memberId);
        if (index > -1) {
            selectedReadOnly.splice(index, 1); // Remove from readOnly if exists

            // Also uncheck the corresponding download checkbox
            $('#download_' + memberId).prop('checked', false);
            const downloadIndex = selectedDownload.indexOf(memberId);
            if (downloadIndex > -1) {
                selectedDownload.splice(downloadIndex, 1); // Remove from download array
            }
        } else {
            selectedReadOnly.push(memberId); // Add to readOnly array if not exists
        }
    } else if (type === 'download') {
        const index = selectedDownload.indexOf(memberId);
        if (index > -1) {
            selectedDownload.splice(index, 1); // Remove from download array if exists
        } else {
            selectedDownload.push(memberId); // Add to download array if not exists

            // Automatically check the corresponding readOnly checkbox
            $('#readOnly_' + memberId).prop('checked', true);
            const readOnlyIndex = selectedReadOnly.indexOf(memberId);
            if (readOnlyIndex === -1) {
                selectedReadOnly.push(memberId); // Add to readOnly array if not already present
            }
        }
    }
}



function save() 
{
    if($("#frm").valid()) {
        let formData = new FormData($('#frm')[0]);

        // Append selected member arrays to formData
        formData.append('readOnlyMembers', JSON.stringify(selectedReadOnly));
        formData.append('downloadMembers', JSON.stringify(selectedDownload));
        
        $.ajax({
            url: url + '/save',
            type: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if(data.status == 'success') {
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
                url: url + '/delete',
                data: { id: id },
                type: 'delete',
                dataType: 'json',
                success: function(e) {
                    if (e.status == 'success') {
                        dTable.draw();
                        toastr.success("{{ __('common.message_delete_title') }}", "{{ __('common.message_success_delete') }}", toastrOptions);
                    }
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