@extends('layouts/layoutMaster')

@section('title2', 'New Document')

@section('vendor-style')
@endsection

@section('page-style')
<style>
.file-upload-group {
    margin-bottom: 15px; /* Memberi jarak antar input file */
}

.file-upload-group input {
    margin-top: 5px; /* Memberi jarak antara teks dan input */
}
</style>
@endsection

@section('content')
<form name="frm" class="form-horizontal" id="frm" method="post" action="{{ route('dokumen.store') }}" enctype="multipart/form-data">
    @csrf
    
    <!-- Workflow Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('documents.workflow') }}</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">{{ __('documents.creator') }}</label>
                        <div class="form-control-plaintext">
                            {{ Auth::user()->master_data_user }} - {{ Auth::user()->master_data_fullname }}
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="workflow" class="form-label">{{ __('documents.workflow') }}:</label>
                        <select id="workflow_id" name="workflow_id" class="select2 form-select form-select-md" required>
                            <option value="">{{ __('documents.workflow_select') }}</option>
                            @foreach($workflows as $workflow)
                                <option value="{{ $workflow->id }}">{{ $workflow->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="knowledge_type_id" class="form-label">{{ __('documents.knowledge_type') }}:</label>
                        <select id="knowledge_type_id" name="knowledge_type_id" class="select2 form-select form-select-md" required>
                            <option value="">{{ __('documents.knowledge_select') }}</option>
                        </select>
                    </div>
                </div>

                @if(isset($wd) && $wd->w_id == '1')
                <div class="col-md-6" id="file_upload_terms_option" style="display:none">
                    <div class="form-group">
                        <label class="form-label">Bentuk Karya Ilmiah <span class="text-danger">*</span></label>
                        <select name="file_upload_terms" id="file_upload_terms" class="form-select select2" required>
                            <option value="">Pilih Bentuk Karya Ilmiah</option>
                            <option value="Dalam bentuk buku karya ilmiah">Dalam bentuk buku karya ilmiah</option>
                            <option value="Dalam bentuk pengganti sidang - Artikel Jurnal">Dalam bentuk pengganti sidang - Artikel Jurnal</option>
                            <option value="Dalam bentuk pengganti sidang - Rancangan Karya Akhir">Dalam bentuk pengganti sidang - Rancangan Karya Akhir</option>
                            <option value="WRAP Apprenticeship">WRAP Apprenticeship</option>
                            <option value="WRAP Internship">WRAP Internship</option>
                            <option value="WRAP Entrepreneurship (Capstone)">WRAP Entrepreneurship (Capstone)</option>
                            <option value="WRAP Researchship">WRAP Researchship</option>
                        </select>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Document Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('documents.document') }}</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">{{ __('documents.title') }}<span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">{{ __('documents.subject') }} <span class="text-danger">*</span></label>
                        <select name="knowledge_subject_id[]" id="knowledge_subject_id" class="form-select select2" multiple required>
                            <option value="">Pilih Subject</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">{{ __('documents.abstract') }} <span class="text-danger">*</span></label>
                        <textarea name="abstract_content" id="abstract_content" class="form-control" rows="4" required></textarea>
                    </div>
                </div>

                <div class="col-md-6" id="lecturer">
                    <div class="form-group">
                        <label class="form-label">{{ __('documents.lecturer_1') }} <span class="text-danger">*</span></label>
                        <select name="lecturer_id" id="lecturer_id" class="form-select select2" required>
                            <option value="">{{ __('documents.sellecturer_1') }}</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6" id="lecturer2">
                    <div class="form-group">
                        <label class="form-label">{{ __('documents.lecturer_2') }}</label>
                        <select name="lecturer2_id" id="lecturer2_id" class="form-select select2">
                            <option value="">{{ __('documents.sellecturer_2') }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Unit Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('documents.unit') }}</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">{{ __('documents.unit') }} <span class="text-danger">*</span></label>
                        <select name="course_code" id="unit" class="form-select select2" required>
                            <option value="">{{ __('documents.selunit') }}</option>
                            @foreach($unitOptions as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">{{ __('documents.master_subject') }}</label>
                        <select name="master_subject[]" id="master_subject" class="form-select select2" multiple required>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- File Upload Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('documents.file_upload') }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">{{ __('documents.doc_upload') }}</label>
                        <div id="file_list" class="border p-3 rounded" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="text-center mt-4">
        <button type="button" class="btn btn-danger me-3" onclick="window.history.back()">
            <i class="fas fa-arrow-left me-2"></i>{{ __('documents.back') }}
        </button>
        <button type="submit" class="btn btn-primary" onclick="return save()">
            <i class="fas fa-save me-2"></i>{{ __('documents.save') }}
        </button>
    </div>
</form>
@endsection

@section('vendor-script')
@endsection

@section('page-script')
<script src="/assets/vendor/libs/ckeditor/ckeditor.js"></script>
<script>
let url = '{{ url('/document') }}';
let dTable = null;
$("#lecturer").hide();
$("#lecturer2").hide();
$(document).ready(function() {
    $('#dates_acceptance_option').on('change', function() {
        var datesAcceptanceDiv = $('#dates_acceptance_div');
        if ($(this).val() === 'date') {
            datesAcceptanceDiv.removeClass('d-none');
        } else {
            datesAcceptanceDiv.addClass('d-none');
        }
    });

    $('#dates_acceptance').daterangepicker({
        locale: {
            format: 'DD-MM-YYYY'
        },
        showDropdowns: true,
        opens: 'left',
        applyClass: 'bg-primary-600',
        cancelClass: 'btn-light'
    });

    $('#workflow_id').on('change', function() {
        if ($(this).val() == '1') {
            $("#lecturer").show();
            $("#lecturer_id").attr('required', true);
            $("#lecturer2").show();
            $("#lecturer2_id").attr('required', false); 
            $("#file_upload_terms_option").show();
            $("#file_upload_terms").attr('required', true);
        } else {
            $("#lecturer").hide();
            $("#lecturer_id").attr('required', false);
            $("#lecturer2").hide(); 
            $("#lecturer2_id").attr('required', false); 
            $("#file_upload_terms_option").hide();
            $("#file_upload_terms").attr('required', false);
        }
        $.ajax({
            url: url + '/getknowledgetype',
            type: "POST",
            data: {
                'id': $(this).val(),
                '_token': "{{ csrf_token() }}"
            },
            dataType: "JSON",
            success: function(response) {
                $("#knowledge_type_id").html('<option value="">{{ __('documents.knowledge_select') }}</option>');
                if (response.knowledge_types && response.knowledge_types.length > 0) {
                    $.each(response.knowledge_types, function(index, value) {
                        $("#knowledge_type_id").append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                }
                $('#knowledge_type_id').select2();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX Error:", textStatus, errorThrown);
                console.error("Response:", jqXHR.responseText);
            }
        });
        $.ajax({
        url: '{{ route('dokumen.getFile') }}',
        type: 'POST',
        data: {
            'id': $(this).val(),
            'action': 'insert',
            '_token': '{{ csrf_token() }}'
        },
        dataType: 'JSON',
        beforeSend: function() {
            $('#loading-img').show();
        },
        complete: function() {
            $('#loading-img').hide();
        },
        success: function(dt) {
            let fileListHtml = `
                <strong>Pilih file yang sesuai dengan masing-masing jenis file upload yang disediakan sesuai dengan kebutuhan.</strong>
                <br>File baru akan menggantikan file lama yang sejenis secara otomatis.<br><br>`;
            $.each(dt, function(index, value) {
                if (value.id && value.title && value.name && value.extension) {  
                    fileListHtml += `
                        <div class="file-upload-group">
                            <strong>${value.title}</strong> (${value.name}.${value.extension})<br>
                            <input type="file" name="upload_type[${value.id}]" id="upload_type_${value.id}" class="upload_type form-control">
                        </div>
                    `;
                } else {
                    console.error('Invalid data:', value); 
                }
            });
            $("#file_list").html(fileListHtml);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error:", textStatus, errorThrown);
            console.error("Response:", jqXHR.responseText);
        }
    });
});

    $('#par_birthdate').datepicker({
        format: "dd-mm-yyyy",
    });

    $('#knowledge_subject_id').select2({
        ajax: {
            url: '{{ route('dokumen.getSubjects') }}',
            dataType: 'json',
            type: 'POST',
            data: function (params) {
                return {
                    searchTerm: params.term,
                    _token: '{{ csrf_token() }}'
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            }
        },
        minimumInputLength: 3
    });

    $('#lecturer_id').select2({
        ajax: {
            url: '{{ route('dokumen.getLecturerId') }}',
            dataType: 'json',
            type: 'POST',
            data: function(params) {
                return {
                    searchTerm: params.term,
                    _token: '{{ csrf_token() }}'
                };
            },
            processResults: function(data) {
                return {
                    results: data
                };
            }
        },
        minimumInputLength: 3
    });

    $('#lecturer2_id').select2({
        ajax: {
            url:'{{ route('dokumen.getLecturerId') }}',
            dataType: 'json',
            type: 'POST',
            data: function(params) {
                return {
                    searchTerm: params.term,
                    _token: '{{ csrf_token() }}'
                };
            },
            processResults: function(data) {
                return {
                    results: data
                };
            }
        },
        minimumInputLength: 3
    });

    CKEDITOR.replace('abstract_content', {
        height: 300,
        enterMode: CKEDITOR.ENTER_BR
    });

    $('#unit').on('change', function() {
        var unitId = $(this).val();
        if (unitId) {
            $.ajax({
                url: '{{ route('dokumen.getMasterSubject') }}',
                type: 'POST',
                data: {
                    id: unitId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('#master_subject').empty();
                    $.each(data, function(key, value) {
                        $('#master_subject').append('<option value="' + value.id + '">' + value.code + ' - ' + value.name + '</option>');
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("AJAX Error:", textStatus, errorThrown);
                    console.error("Response:", jqXHR.responseText);
                }
            });
        } else {
            $('#master_subject').empty();
        }
    });
});

function save() {
    if ($("#frm").valid()) {
        if ($('#workflow_id').val() === '') {
            alert('Workflow tidak boleh kosong.');
            return false;
        }
        if ($('#knowledge_type_id').val() === '') {
            alert('Jenis Pustaka tidak boleh kosong.');
            return false;
        }
        if ($('#title').val() === '') {
            alert('Judul tidak boleh kosong.');
            return false;
        }
        if ($('#knowledge_subject_id').val() === '') {
            alert('Subject tidak boleh kosong.');
            return false;
        }
        if (CKEDITOR.instances.abstract_content.getData() === '') {
            alert('Abstrak tidak boleh kosong.');
            return false;
        }
        if ($('#unit').val() === '') {
            alert('Unit tidak boleh kosong.');
            return false;
        }
        if ($('#master_subject').val() === '') {
            alert('Kompetensi tidak boleh kosong.');
            return false;
        }
        if ($('#file_list input[type="file"]').length === 0) {
            alert('Upload Dokumen tidak boleh kosong.');
            return false;
        }
        $("#frm").submit();
    }
    return false;
}
</script>
@endsection