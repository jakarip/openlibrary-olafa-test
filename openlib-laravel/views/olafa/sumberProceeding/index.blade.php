@extends('layouts/layoutMaster')

@section('title', 'Sumber Proceeding')

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
            <h5 class="m-0"> Sumber Proceeding </h5>
        </div>
        <table class="dt-row-grouping table border-top" id="table">
            <thead>
                <tr>
                    <th>Title</th>
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
                <div class="modal-title"><i class="ti ti-forms me-2"></i> Form Sumber Proceeding</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="frm" class="form-validate" >
                @csrf
                <input type="hidden" id="id" name="id">
                <div class="modal-body">
                    
                    <div class="card mb-3">

                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6>Sumber Proceeding</h6>
                            <button type="button" class="btn btn-primary btn-sm ms-2" onclick="showSubjectModal()">
                                <i class="ti ti-plus"></i> Add Subject
                            </button>
                        </div>

                        <div class="card-body">
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">Subject</label>
                                <div class="col-md-9">
                                    <select id="subject_id" name="inp[subject_id]" class="select2 form-select form-select-lg"> 
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}">
                                                {{ $subject->subject_name }}
                                            </option>
                                        @endforeach 
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">Title</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[title]" id="title"></input>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">Link</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[link]" id="link"></input>
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

<div class="modal fade" id="frmbox_subject" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i> Form Proceeding Subject</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="frm_subject" class="form-validate" >
                @csrf
                <input type="hidden" id="id" name="id">
                <div class="modal-body">
                    
                    <div class="card mb-3">

                        <div class="card-header">
                            <h6>Proceeding Subject</h6>
                        </div>

                        <div class="card-body">
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">Subject</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[subject_name]" id="subject_name"></input>
                                </div>
                            </div>

                            <div class="form-group row mb-4" id="update_subject_group" style="display: none;">
                                <label class="col-md-3 col-form-label">Update Subject</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[updated_subject_name]" id="updated_subject_name"></input>
                                </div>
                            </div>
                            
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <h6>List Proceeding Subject</h6>
                        </div>

                        <div class="card-body">
                            <table class="dt-row-grouping table border-top" id="table_subjects">
                                <thead>
                                    <tr>
                                        <th>Nama Subjek</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                
                            </table>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"  class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" id="saveButton" class="btn btn-primary waves-effect waves-light" onclick="save()">{{ __('common.save') }}</button>
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
let dTable_subjects = null;
let url = '{{ url('olafa/sumber-proceeding')}}';

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
            { data: 'title', name: 'title', orderable: false, searchable: true },
            { data: 'link', name: 'link', orderable: false, searchable: true, render: function(data, type, row) {
                return `<a href="${data}" target="_blank">${data}</a>`;
            }},
            {data: 'action', name: 'action', orderable: false, searchable: false, },
        ],
        rowGroup: {
            dataSrc: 'subject_name',
            // startRender: function(rows, group) {
            //     let actionButton = `<div class="btn-group">
            //     <button class="btn rounded-pill btn-icon btn-label-primary waves-effect" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            //         <i class="ti ti-dots-vertical"></i>
            //     </button>
            //     <ul class="dropdown-menu dropdown-menu-end">
            //         <li class="d-flex"><a class="dropdown-item d-flex align-items-center text-black" onclick="groupAction('${group}')"><i class="ti ti-edit ti-sm me-2"></i> Edit Data</a></li>
            //         <li class="d-flex"><a class="dropdown-item d-flex align-items-center text-danger" onclick="groupAction('${group}')"><i class="ti ti-trash me-2"></i> Delete Data</a></li>
            //     </ul>
            // </div>
            // `;
            //     return $('<tr/>')
            //         .append('<td colspan="2" class="bg-light text-dark bg-opacity-50">' + group + '</td>')
            //         .append('<td class="group-header bg-light text-dark bg-opacity-50">' + actionButton + '</td>');
            // }
        }
    });

    

    $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> Sumber Proceeding </button>`)
});

$(document).ready(function() {
    $('#frmbox_subject').on('show.bs.modal', function () {
        if (!$.fn.DataTable.isDataTable('#table_subjects')) {
            dTable_subjects = $('#table_subjects').DataTable({
                pageLength: 10,
                searching: false,
                buttons: [],
                lengthChange: false,
                paging: false,
                info: false,
                ajax: {
                    url: url + '/dt_subjects',
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                },
                columns: [
                    { data: 'subject_name', name: 'subject_name', orderable: true, searchable: true },
                    { data: 'action', name: 'action', class: 'text-center' },
                ],
                createdRow: function(row, data, dataIndex) {
                    // Remove action column for the first three rows
                    if (dataIndex < 3) {
                        $('td:eq(1)', row).html('');
                    }
                }
            });
        } else {
            dTable_subjects.ajax.reload();
        }
    });
});


function add() {
    _reset();
    updateSubjects();
    $('#frmbox').modal('show');            
}


function save() {
    let formSelector = '#frm'; // Default form selector

    
    if ($('#frmbox_subject').is(':visible')) {
        formSelector = '#frm_subject';
    }
    
    if($(formSelector).valid())
    {
        let formData = new FormData($(formSelector)[0]);
        formData.delete('inp[updated_subject_name]');

        $.ajax({
            url: url+'/save',
            type: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if(data.status === 'success') {
                    if (formSelector === '#frm_subject') {
                        updateSubjects();
                        dTable_subjects.draw();
                    } else {
                        $('#frmbox').modal('hide'); 
                        dTable.draw();
                    }
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

function edit(id) {
    if ($('#frmbox_subject').is(':visible')) {
        // User wants to edit a subject
        $.ajax({
            url: url + '/get-subject/' + id, // Adjust the URL to match your route for fetching a subject
            type: 'get',
            dataType: 'json',
            success: function(e) {
                _reset(); 
                $('#id').val(e.id); // Set the hidden input value to the subject ID
                $('#subject_name').val(e.subject_name).prop('disabled', true);
                $('#updated_subject_name').val(e.subject_name);// Display the subject name in the input field
                $('#update_subject_group').show();
                $("#saveButton").attr("onclick", "update()");
                // $("#frmbox_subject").modal('show');
            }
        });
    } else {
        // User wants to edit a proceeding title
        $.ajax({
            url: url + '/get/' + id, 
            type: 'get',
            dataType: 'json',
            success: function(e) {
                _reset(); 
                $('#id').val(e.id);
                $('#subject_id').val(e.subject_id).trigger('change');
                $('#title').val(e.title);
                $('#link').val(e.link);

                $("#saveButton").attr("onclick", "save()");

                $("#frmbox").modal('show');
            }
        });
    }
}

function update() {
    let id = $('#id').val();
    let updatedSubjectName = $('#updated_subject_name').val();

    $.ajax({
        url: url + '/update-subject/' + id, // Adjust the URL to match your route for updating a subject
        type: 'post',
        data: {
            id: id,
            subject_name: updatedSubjectName,
            _token: $('meta[name="csrf-token"]').attr('content') // Assuming you have a CSRF token in a meta tag
        },
        success: function(data) {
            if (data.status === 'success') {
                $('#frmbox_subject').modal('hide');
                updateSubjects(); // Update the subjects after updating a subject
                toastr.success("{{ __('common.message_save_title') }}", "{{ __('common.message_success_save') }}", toastrOptions);
            } else if (data.status === 'error') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat menyimpan data.'
            });
        }
    });
}


function del(id){
    // console.log($('#subject_id').val());
    let formSelector = '#frm'; // Default form selector

    if ($('#frmbox_subject').is(':visible')) {
        formSelector = '#frm_subject';
        $('#frmbox').modal('hide');
    }

    if (formSelector === '#frm_subject') {
        $('#frmbox_subject').modal('hide'); // Close subject modal on success
    } 

    let dataType = formSelector === '#frm_subject' ? 'subject' : 'title';
    
    yswal_delete.fire({
        title: "{{ __('common.message_delete_prompt_title') }}",
        text: "{{ __('common.message_delete_prompt_text') }}",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url + '/delete',
                data: { id: id, type: dataType, _token: '{{ csrf_token() }}' }, // Pastikan untuk menyertakan token CSRF
                type: 'delete',
                dataType: 'json',
                success: function(e) {
                    if (e.status == 'success') {
                        if (formSelector === '#frm_subject') {
                            dTable_subjects.draw();
                            $('#frmbox_subject').modal('show');
                            
                        } else {
                            dTable.draw();
                        }

                        toastr.success("{{ __('common.message_delete_title') }}", "{{ __('common.message_success_delete') }}", toastrOptions);
                    } else {
                        $('#frmbox_subject').modal('hide');
                        $('#frmbox').modal('hide');
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

    $('#subject_id').val('').trigger('change');
    // console.log($('#subject_id').val());
} 


function updateSubjects() {
    $.ajax({
        url: url +'/get-subjects', // Adjust the URL to match your route
        type: 'get',
        dataType: 'json',
        success: function(data) {
            let subjectSelect = $('#subject_id');
            subjectSelect.empty();

            // Append new options
            data.forEach(function(subject) {
                subjectSelect.append(`<option value="${subject.id}">${subject.subject_name}</option>`);
            });

            // Reinitialize the select2 plugin if used
            subjectSelect.select2();
        },
        error: function(xhr, status, error) {
            console.error('Failed to fetch subjects:', error);
        }
    });
}


function _reset() {
    $('#frm_subject')[0].reset(); // Reset all input fields in the form
    $('#frm')[0].reset(); 
    $('.select2').val(null).trigger('change');
    $('#subject_name').prop('disabled', false); // Enable the subject name input field
    $('#update_subject_group').hide(); // Hide the update subject input field
    $("#saveButton").attr("onclick", "save()"); // Reset the onclick attribute to save()
}

function showSubjectModal() {
    // Reset all input fields in the form
    $('#frm_subject')[0].reset(); 
    // Enable the subject name input field
    $('#subject_name').prop('disabled', false); 
    // Hide the update subject input field
    $('#update_subject_group').hide(); 
    // Reset the onclick attribute to save()
    $("#saveButton").attr("onclick", "save()"); 
    // Show the modal
    $('#frmbox_subject').modal('show');
}

</script>
@endsection










