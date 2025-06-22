@extends('layouts/layoutMaster')

@section('title2', 'Detail Document')

@section('vendor-style')
@endsection

@section('page-style')
<style>
    .validation-invalid-label {
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
        display: block;
        color: #ef5350;
        position: relative;
        padding-left: 1.625rem;
    }
    .validation-valid-label {
        color: #25b372;
    }
    .validation-invalid-label:before, .validation-valid-label:before {
        font-family: icomoon;
        font-size: 1rem;
        position: absolute;
        top: 0.1875rem;
        left: 0;
        display: inline-block;
        line-height: 1;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    .file-upload-group {
        margin-bottom: 15px;
    }
    .file-upload-group input {
        margin-top: 5px;
    }
    .workflow-history th {
        border-top: none !important;
    }
</style>
@endsection

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center;">
</div>

<form name="frm" class="form-horizontal" id="frm" method="post" enctype="multipart/form-data" action="{{ route('dokumen.update', $document->id) }}">
    @csrf
    @method('PUT')

    <input type="hidden" name="wd_id" value="{{ $document->id ?? '' }}">
    <input type="hidden" name="latest_state_id_old" value="{{ $document->latest_state_id ?? '' }}">
    <input type="hidden" name="workflow_id" value="{{ $document->workflow_id ?? '' }}">

    <!-- Workflow Card -->
    <div class="card mb-4 mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('documents.workflow') }}</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-12">
                    <div class="form-group">
                        <b><label class="form-label">{{ __('documents.creator') }}</label></b>
                        <div class="form-control-plaintext">
                            {{ $document->master_data_user ?? 'Unknown' }} - {{ $document->master_data_fullname ?? 'Unknown' }}
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                    <b><label class="form-label">{{ __('documents.workflow') }}</label></b>
                        <div class="form-control-plaintext">
                            {{ $document->w_name ?? 'Unknown' }}
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                    <b><label class="form-label">{{ __('documents.knowledge_type') }}</label></b>
                        <div class="form-control-plaintext">
                            {{ $document->jenis_katalog ?? 'Unknown' }}
                        </div>
                    </div>
                </div>
                @if(($document->w_id ?? 0) == 1)
                <div class="col-md-12">
                    <div class="form-group">
                    <b><label class="form-label">Bentuk Karya Ilmiah</label></b>
                        @if($document->can_edit_attribute == 1)
                            <select name="file_upload_terms" id="file_upload_terms" class="form-select select2">
                                <option value="Dalam bentuk buku karya ilmiah" {{ ($document->file_upload_terms ?? '') == 'Dalam bentuk buku karya ilmiah' ? 'selected' : '' }}>Dalam bentuk buku karya ilmiah</option>
                                <option value="Dalam bentuk pengganti sidang - Artikel Jurnal" {{ ($document->file_upload_terms ?? '') == 'Dalam bentuk pengganti sidang - Artikel Jurnal' ? 'selected' : '' }}>Dalam bentuk pengganti sidang - Artikel Jurnal</option>
                                <option value="Dalam bentuk pengganti sidang - Rancangan Karya Akhir" {{ ($document->file_upload_terms ?? '') == 'Dalam bentuk pengganti sidang - Rancangan Karya Akhir' ? 'selected' : '' }}>Dalam bentuk pengganti sidang - Rancangan Karya Akhir</option>
                                <option value="WRAP Apprenticeship" {{ ($document->file_upload_terms ?? '') == 'WRAP Apprenticeship' ? 'selected' : '' }}>WRAP Apprenticeship</option>
                                <option value="WRAP Internship" {{ ($document->file_upload_terms ?? '') == 'WRAP Internship' ? 'selected' : '' }}>WRAP Internship</option>
                                <option value="WRAP Entrepreneurship" {{ ($document->file_upload_terms ?? '') == 'WRAP Entrepreneurship' ? 'selected' : '' }}>WRAP Entrepreneurship</option>
                                <option value="WRAP Researchship" {{ ($document->file_upload_terms ?? '') == 'WRAP Researchship' ? 'selected' : '' }}>WRAP Researchship</option>
                                <option value="Capstone" {{ ($document->file_upload_terms ?? '') == 'Capstone' ? 'selected' : '' }}>Capstone</option>
                            </select>
                        @else
                            <div class="form-control-plaintext">{{ $document->file_upload_terms ?? '-' }}</div>
                        @endif
                    </div>
                </div>
                @endif
                <div class="col-md-12">
                    <div class="form-group">
                    <b><label class="form-label">Current State</label></b>
                        <div class="form-control-plaintext">
                            {{ $document->state_name ?? 'Unknown' }}
                        </div>
                    </div>
                </div>
                @if($document->can_edit_state && $document->latest_state_id == 1)
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label"></label>
                        <div class="form-control-plaintext">
                            <img src="{{ asset('cdn/jenisapproval.png') }}" width="60%">
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">Next State</label>
                        @if($document->can_edit_state == 1)
                            <select name="latest_state_id" id="latest_state_id" class="form-select select2">
                                @foreach($next as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        @else
                            <div class="form-control-plaintext">-</div>
                        @endif
                    </div>
                </div>
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
                    <b><label class="form-label">{{ __('documents.title') }}<span class="text-danger">*</span></label></b>
                        @if($document->can_edit_attribute ?? false)
                            <input type="text" name="title" id="title" class="form-control" value="{{ $document->title ?? '' }}" required>
                        @else
                            <div class="form-control-plaintext">{{ $document->title ?? '' }}</div>
                        @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                    <b><label class="form-label">{{ __('documents.subject') }} <span class="text-danger">*</span></label></b>
                        @if($document->can_edit_attribute ?? false)
                            <select name="knowledge_subject_id" id="knowledge_subject_id" class="form-control select2" required>
                                <option value="{{ $document->knowledge_subject_id ?? '' }}" selected>{{ $document->ks_name ?? 'Unknown' }}</option>
                            </select>
                        @else
                            <div class="form-control-plaintext">{{ $document->ks_name ?? 'Unknown' }}</div>
                        @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                    <b><label class="form-label">{{ __('documents.abstract') }}<span class="text-danger">*</span></label></b>
                        @if($document->can_edit_attribute ?? false)
                            <textarea name="abstract_content" id="abstract_content" class="form-control" rows="4" required>{{ $document->abstract_content ?? '' }}</textarea>
                        @else
                            <div class="form-control-plaintext">{!! nl2br(e($document->abstract_content ?? '')) !!}</div>
                        @endif
                    </div>
                </div>
                @if(($document->w_id ?? 0) == 1)
                <div id="lecturer">
                    @if(auth()->user()->membertype == '1' && ($document->latest_state_id ?? 0) == 1)
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">Approve By <br>(Digunakan jika dosen pembimbing 1 berhalangan)</label>
                            @if($document->can_edit_attribute ?? false)
                                <select name="approved_id" id="approved_id" class="form-control select2">
                                    <option value="{{ $document->approved_id ?? '' }}" selected>
                                        @if($document->approved_number && $document->approved_name)
                                            ({{ $document->approved_number }}) - {{ $document->approved_name }}
                                        @else
                                            Select Approver
                                        @endif
                                    </option>
                                </select>
                            @else
                                <div class="form-control-plaintext">
                                    @if($document->approved_number && $document->approved_name)
                                        ({{ $document->approved_number }}) - {{ $document->approved_name }}
                                    @else
                                        -
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                    <div class="col-md-12">
                        <div class="form-group">
                        <b><label class="form-label">{{ __('documents.lecturer_1') }}<span class="text-danger">*</span></label></b>
                            @if($document->can_edit_attribute ?? false)
                                <select name="lecturer_id" id="lecturer_id" class="form-control select2" required>
                                    <option value="{{ $document->lecturer_id ?? '' }}" selected>
                                        @if($document->lecturer_number && $document->lecturer_name)
                                            ({{ $document->lecturer_number }}) - {{ $document->lecturer_name }}
                                        @else
                                            Select Lecturer
                                        @endif
                                    </option>
                                </select>
                            @else
                                <div class="form-control-plaintext">
                                    @if($document->lecturer_number && $document->lecturer_name)
                                        ({{ $document->lecturer_number }}) - {{ $document->lecturer_name }}
                                    @else
                                        -
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                        <b><label class="form-label">{{ __('documents.lecturer_2') }}</label></b>
                            @if($document->can_edit_attribute ?? false)
                                <select name="lecturer2_id" id="lecturer2_id" class="form-control select2">
                                    <option value="{{ $document->lecturer2_id ?? '' }}" selected>
                                        @if($document->lecturer2_number && $document->lecturer2_name)
                                            ({{ $document->lecturer2_number }}) - {{ $document->lecturer2_name }}
                                        @else
                                            Select Lecturer
                                        @endif
                                    </option>
                                </select>
                            @else
                                <div class="form-control-plaintext">
                                    @if($document->lecturer2_number && $document->lecturer2_name)
                                        ({{ $document->lecturer2_number }}) - {{ $document->lecturer2_name }}
                                    @else
                                        -
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if(($document->workflow_id ?? 0) == 1)
    <!-- SDGs Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">SDGs Point</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-12">
                    <div class="form-group">
                    <b><label class="form-label">SDGs <span class="text-danger">*</span></label></b>
                        @if(($document->can_edit_attribute ?? false) || ($document->can_edit_state ?? false) && ($document->latest_state_id ?? 0) == 1)
                            @foreach($sdgs as $id => $name)
                                <div class="form-check">
                                    <input class="form-check-input sdgs" type="checkbox" name="sdgs[]" id="sdgs{{ $id }}" value="{{ $id }}" 
                                        {{ in_array($id, $sdgsExist) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sdgs{{ $id }}">{{ $name }}</label>
                                </div>
                            @endforeach
                        @else
                            <div class="form-control-plaintext">
                                @forelse($sdgsExist as $sdg)
                                    {{$sdgs[$sdg] ?? 'Unknown' }}<br>
                                @empty
                                    No SDGs selected
                                @endforelse
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Unit Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('documents.unit') }}</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-12">
                    <div class="form-group">
                    <b><label class="form-label">Unit <span class="text-danger">*</span></label></b>
                        @if($document->can_edit_attribute ?? false)
                            <select name="course_code" id="unit" class="form-select select2" required>
                                @foreach($unitOptions as $code => $name)
                                    <option value="{{ $code }}" {{ ($document->course_code ?? '') == $code ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        @else
                            <div class="form-control-plaintext">{{ $unitOptions[$document->course_code ?? ''] ?? 'Unknown' }}</div>
                        @endif
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                    <b><label class="form-label">{{ __('documents.master_subject') }}</label></b>
                        @if($document->can_edit_attribute ?? false)
                            <select name="master_subject[]" id="master_subject" class="form-select select2" multiple>
                                {!! $masterSubjectOptions !!}
                            </select>
                        @else
                            <div class="form-control-plaintext">
                                {!! $masterSubjectView ?: 'No subjects selected' !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Files Card -->
    <div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Files</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-12">
                <div class="form-group">
                    <b><label class="form-label">Existing Files</label></b>
                    <div class="form-control-plaintext">
                        <strong>File yang telah di upload untuk dokumen ini</strong><br>
                        @forelse($existingFiles as $file)
                            <div class="mb-2">
                                @if($canDownload)
                                    <!-- Tombol aktif jika memiliki izin -->
                                    <a href="{{ route('dokumen.download', ['document' => $document->id, 'file' => $file->id]) }}" 
                                       class="text-primary" 
                                       target="_blank">
                                        <i class="fas fa-download me-1"></i>
                                        {{ $file->title }} ({{ strtoupper($file->extension) }})
                                    </a>
                                @else
                                    <!-- Tombol nonaktif jika tidak memiliki izin -->
                                    <span class="text-muted" style="cursor: not-allowed;">
                                        <i class="fas fa-download me-1"></i>
                                        {{ $file->title }} ({{ strtoupper($file->extension) }})
                                    </span>
                                @endif
                            </div>
                        @empty
                            <div class="text-muted">No files uploaded</div>
                        @endforelse
                    </div>
                </div>
            </div>
                @if($document->can_edit_attribute ?? false)
                <div class="col-md-12">
                    <div class="form-group">
                    <b><label class="form-label">{{ __('documents.file_upload') }}</label></b>
                        <div id="file_list" class="border p-3 rounded">
                            <strong>Pilih file yang sesuai dengan masing-masing jenis file upload yang disediakan sesuai dengan kebutuhan.</strong>
                            <br>File baru akan menggantikan file lama yang sejenis secara otomatis.<br><br>
                            @forelse($fileTypes as $fileType)
                                @if($fileType)
                                    <div class="file-upload-group">
                                        <strong>{{ $fileType->title ?? 'Unknown' }}</strong> ({{ $fileType->name ?? 'Unknown' }}.{{ $fileType->extension ?? 'Unknown' }})<br>
                                        <input type="file" name="upload_type[{{ $fileType->id ?? '' }}]" id="upload_type_{{ $fileType->id ?? '' }}" class="upload_type form-control">
                                    </div>
                                @endif
                            @empty
                                No file types available
                            @endforelse
                        </div>
                    </div>
                </div>
                @else
                <div class="col-md-12">
                    <div class="form-group">
                    <b><label class="form-label">{{ __('documents.file_upload') }}</label></b>
                        <div class="form-control-plaintext">
                            <strong>Jenis keanggotaan anda tidak diperbolehkan melakukan upload file terhadap dokumen ini</strong>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Comments Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Comments</h5>
        </div>
        <div class="card-body">
            <div id="comments-container">
                @forelse($comments as $comment)
                    <div class="comment mb-4" id="comment-{{ $comment['id'] }}">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>
                                    <i class="fas fa-user"></i> 
                                    {{ $comment['name'] }} ({{ $comment['user'] }})
                                </strong>
                                @if($comment['user'] == auth()->user()->master_data_user || auth()->user()->membertype == '1')
                                    <span class="delete-comment ms-2" data-id="{{ $comment['id'] }}" style="cursor: pointer;">
                                        <i class="fas fa-trash text-danger"></i>
                                    </span>
                                @endif
                            </div>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($comment['created_at'])->format('d M Y H:i') }}
                            </small>
                        </div>
                        <div class="comment-content p-3 bg-light rounded mb-2">
                            {!! nl2br(e($comment['comment'])) !!}
                        </div>
                        <!-- Replies Section -->
                        @if(!empty($comment['reply']))
                            <div class="replies ms-4 mt-2">
                                @foreach($comment['reply'] as $reply)
                                    <div class="reply mb-3" id="reply-{{ $reply['id'] }}">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <div>
                                                <strong>
                                                    <i class="fas fa-reply fa-rotate-180"></i> 
                                                    {{ $reply['name'] }} ({{ $reply['user'] }})
                                                </strong>
                                                @if($reply['user'] == auth()->user()->master_data_user || auth()->user()->membertype == '1')
                                                    <span class="delete-comment ms-2" data-id="{{ $reply['id'] }}" style="cursor: pointer;">
                                                        <i class="fas fa-trash text-danger"></i>
                                                    </span>
                                                @endif
                                            </div>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($reply['created_at'])->format('d M Y H:i') }}
                                            </small>
                                        </div>
                                        <div class="reply-content p-2 ps-3 bg-light rounded">
                                            {!! nl2br(e($reply['comment'])) !!}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <!-- Reply Form -->
                        <div class="reply-form mt-2 ms-4">
                            <form class="add-reply" data-parent-id="{{ $comment['id'] }}">
                                @csrf
                                <input type="hidden" name="document_id" value="{{ $document->id }}">
                                <input type="hidden" name="parent_id" value="{{ $comment['id'] }}">
                                <div class="input-group">
                                    <textarea name="comment" class="form-control" placeholder="Write a reply..." rows="2" required></textarea>
                                </div>
                            </form>
                        </div>
                    </div>
                    <hr>
                @empty
                    <div class="text-muted">No comments yet</div>
                @endforelse
            </div>
            <!-- New Comment Form -->
            <div class="new-comment mt-4">
                <h6>Add New Comment</h6>
                <form id="new-comment-form">
                    @csrf
                    <input type="hidden" name="document_id" value="{{ $document->id }}">
                    <div class="form-group mb-2">
                        <textarea name="new_comment" class="form-control" rows="3" placeholder="Write your comment..." required></textarea>
                    </div>  
                </form>
            </div>
        </div>
    </div>
    

    <!-- State History Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">State History</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table workflow-history">
                    <thead>
                        <tr>
                            <th>State</th>
                            <th>Anggota</th>
                            <th>Fullname</th>
                            <th>Jenis</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentStates as $history)
                            @if($history)
                                <tr>
                                    <td>{!! $history->close_date ? '<s>'.($history->state_name ?? 'Unknown').'</s>' : ($history->state_name ?? 'Unknown') !!}</td>
                                    <td>{{ $history->master_data_user ?? '' }}</td>
                                    <td>{{ $history->master_data_fullname ?? '' }}</td>
                                    <td>{{ $history->member_type_name ?? '' }}</td>
                                    <td>
                                        @if($history->open_date)
                                            <i class="fas fa-pencil-alt"></i> {{ \Carbon\Carbon::parse($history->open_date)->format('d M Y H:i') }}<br>
                                        @endif
                                        @if($history->close_date)
                                            <i class="fas fa-check-circle"></i> {{ \Carbon\Carbon::parse($history->close_date)->format('d M Y H:i') }}
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No history available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Timestamps Card -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Timestamps</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">Created</label>
                        <div class="form-control-plaintext">
                            {{ $document->created_at ? \Carbon\Carbon::parse($document->created_at)->format('d M Y H:i') : '' }} by {{ $document->created_by ?? 'Unknown' }}
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">Updated</label>
                        <div class="form-control-plaintext">
                            {{ $document->updated_at ? \Carbon\Carbon::parse($document->updated_at)->format('d M Y H:i') : '' }} by {{ $document->updated_by ?? 'Unknown' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="text-center mt-4">
        <a href="{{ route('dokumen.index') }}" class="btn btn-danger me-3">
            <i class="fas fa-arrow-left me-2"></i> {{ __('documents.back') }}
        </a>
       @if(($document->can_edit_attribute ?? false) || 
    ($document->can_edit_state ?? false))
    <button type="button" class="btn btn-primary" id="save-button">
        <i class="fas fa-save me-2"></i> {{ __('documents.save') }}
    </button>
        @endif
    </div>
</form>
@endsection

@section('vendor-script')
@endsection

@section('page-script')
<script src="{{ asset('assets/vendor/libs/ckeditor/ckeditor.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Initialize select2
    $('.select2').select2();

    // Initialize CKEditor if editable
    @if($document->can_edit_attribute ?? false)
    CKEDITOR.replace('abstract_content', {
        height: 300,
        enterMode: CKEDITOR.ENTER_BR
    });
    @endif

    // Save button click handler
    $('#save-button').click(function () {
    let isValid = true;

    if ($('#title').length && $('#title').val().trim() === '') {
        alert('Judul tidak boleh kosong');
        isValid = false;
    }

    if ($('#knowledge_subject_id').val() === null) {
        alert('Subject tidak boleh kosong');
        isValid = false;
    }

    @if($document->can_edit_attribute ?? false)
    if (CKEDITOR.instances.abstract_content.getData().trim() === '') {
        alert('Abstrak tidak boleh kosong');
        isValid = false;
    }
    @endif

    if ($('#unit').val() === null) {
        alert('Unit tidak boleh kosong');
        isValid = false;
    }

    @if(($document->workflow_id ?? 0) == 1)
    if ($('#lecturer_id').val() === null) {
        alert('Dosen Pembimbing 1 tidak boleh kosong');
        isValid = false;
    }

    if ($('.sdgs:checked').length == 0 && $('#latest_state_id').val() != 2 && $('#latest_state_id').val() != "") {
        alert('Silahkan checklist point SDGs');
        isValid = false;
    }
    @endif

    if (isValid) {
        @if(($document->workflow_id ?? 0) == 1 && ($document->latest_state_id ?? 0) == 1)
        if ($('#latest_state_id').val() == 2 || $('#latest_state_id').val() == "") {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Dokumen ilmiah yang saya setujui sudah bebas dari unsur-unsur plagiasi. Jika dikemudian hari terbukti plagiasi, maka saya siap menerima konsekuensi/sanksi sesuai peraturan yang berlaku.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Setuju!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#frm').submit();
                }
            });
        } else {
            $('#frm').submit();
        }
        @else
        $('#frm').submit();
        @endif
    }
});

    // Lecturer select2
    $('#lecturer_id, #lecturer2_id, #approved_id').select2({
        ajax: {
            url: '{{ route("dokumen.getLecturerId") }}',
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    searchTerm: params.term,
                    _token: '{{ csrf_token() }}'
                };
            },
            processResults: function(data) {
                return { results: data };
            }
        },
        minimumInputLength: 3
    });

    // Knowledge subject select2
    $('#knowledge_subject_id').select2({
        ajax: {
            url: '{{ route("dokumen.getSubjects") }}',
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    searchTerm: params.term,
                    _token: '{{ csrf_token() }}'
                };
            },
            processResults: function(data) {
                return { results: data };
            }
        },
        minimumInputLength: 3
    });

    // Unit change handler
    $('#unit').change(function() {
        var unitId = $(this).val();
        if (unitId) {
            $.ajax({
                url: '{{ route("dokumen.getMasterSubject") }}',
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
                    $('#master_subject').trigger('change');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("AJAX Error:", textStatus, errorThrown);
                }
            });
        }
    });

    // Next state change handler
    $('#latest_state_id').on('select2:select', function(e) {
        if(e.params.data.id == 2) {
            $('#comment').attr('required', true);
        } else {
            $('#comment').attr('required', false);
        }
    });


// Handle comment replies
$(document).on('submit', '.add-reply', function(e) {
    e.preventDefault();
    const form = $(this);
    const formData = form.serialize();
    const submitBtn = form.find('button[type="submit"]');
    
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');
    
    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                toastr.success('Reply added successfully');
                location.reload();
            } else {
                toastr.error(response.message || 'Failed to add reply');
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
            toastr.error('An error occurred while adding the reply');
        },
        complete: function() {
            submitBtn.prop('disabled', false).html('<i class="fas fa-reply"></i> Reply');
        }
    });
});

// Handle new comments
$('#new-comment-form').on('submit', function(e) {
    e.preventDefault();
    const form = $(this);
    const formData = form.serialize();
    const submitBtn = form.find('button[type="submit"]');
    
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
    
    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                toastr.success('Comment added successfully');
                location.reload();
            } else {
                toastr.error(response.message || 'Failed to add comment');
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
            toastr.error('An error occurred while adding the comment');
        },
        complete: function() {
            submitBtn.prop('disabled', false).html('Submit Comment');
        }
    });
});

// Handle comment deletion
// Handle comment deletion
$(document).on('click', '.delete-comment', function () {
    const commentId = $(this).data('id');
    const commentElement = $(this).closest('.comment, .reply');

    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: 'Komentar yang dihapus tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("dokumen.deleteComment") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: commentId
                },
                beforeSend: function () {
                    commentElement.css('opacity', '0.5');
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire(
                            'Dihapus!',
                            'Komentar berhasil dihapus.',
                            'success'
                        );
                        commentElement.slideUp(300, function () {
                            $(this).remove();
                        });
                    } else {
                        Swal.fire(
                            'Gagal!',
                            response.message || 'Gagal menghapus komentar.',
                            'error'
                        );
                        commentElement.css('opacity', '1');
                    }
                },
                error: function (xhr) {
                    console.error('Error:', xhr.responseText);
                    Swal.fire(
                        'Error!',
                        'Terjadi kesalahan saat menghapus komentar.',
                        'error'
                    );
                    commentElement.css('opacity', '1');
                }
            });
        }
    });
});
    // Form validation and submission
    function save() {
        let isValid = true;

        if ($('#title').val().trim() === '') {
            console.error('Validation Error: Title is empty');
            alert('Judul tidak boleh kosong');
            isValid = false;
        }

        if ($('#knowledge_subject_id').val() === null) {
            console.error('Validation Error: Subject is not selected');
            alert('Subject tidak boleh kosong');
            isValid = false;
        }

        @if($document->can_edit_attribute ?? false)
        if (CKEDITOR.instances.abstract_content.getData().trim() === '') {
            console.error('Validation Error: Abstract is empty');
            alert('Abstrak tidak boleh kosong');
            isValid = false;
        }
        @endif

        if ($('#unit').val() === null) {
            console.error('Validation Error: Unit is not selected');
            alert('Unit tidak boleh kosong');
            isValid = false;
        }

        @if(($document->workflow_id ?? 0) == 1)
        if ($('#lecturer_id').val() === null) {
            console.error('Validation Error: Lecturer 1 is not selected');
            alert('Dosen Pembimbing 1 tidak boleh kosong');
            isValid = false;
        }

        if ($('.sdgs:checked').length == 0 && $('#latest_state_id').val() != 2 && $('#latest_state_id').val() != "") {
            console.error('Validation Error: SDGs not selected');
            alert('Silahkan checklist point SDGs');
            isValid = false;
        }
        @endif
        if (isValid) {
            @if(($document->workflow_id ?? 0) == 1 && ($document->latest_state_id ?? 0) == 1)
            if ($('#latest_state_id').val() == 2 || $('#latest_state_id').val() == "") {
                if (confirm("Dokumen ilmiah yang saya setujui sudah bebas dari unsur-unsur plagiasi. Jika dikemudian hari terbukti plagiasi, maka saya siap menerima konsekuensi/sanksi sesuai peraturan yang berlaku.")) {
                    $('#frm').submit();
                }
            } else {
                $('#frm').submit();
            }
            @else
            $('#frm').submit();
            @endif
        }
    }
});
</script>
@endsection