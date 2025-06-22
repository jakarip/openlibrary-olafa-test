@extends('layouts/layoutMaster')

@section('title', __('config.workflow_designer.page.title'))

@section('vendor-style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
    <div class="card-header sticky-element bg-label-secondary d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Workflow Details</h5>
        <div class="action-btns">
            <a href="{{ route('workflow-designer') }}" class="btn btn-label-primary me-3">
                <span class="align-middle">Kembali</span>
            </a>
            
            <button type="button" class="btn btn-primary waves-effect waves-light me-3" onclick="save()">{{ __('common.save') }}</button>
        </div>
    </div>
</div>


<form id="frm" class="form-validate">
    @csrf
    <input type="hidden" name="id" id="id" value="{{ $workflow->id ?? '' }}">

    
    <!-- Workflow Card -->
    <div class="card mb-3">
        <div class="card-header">
            <h6>Workflow</h6>
        </div>
        <div class="card-body">
            <div class="form-group row mb-4">
                <label class="col-md-2 col-form-label">{{ __('config.workflow_task.input.name') }}</label>
                <div class="col-md-4">
                    <input type="text" 
                        class="form-control" 
                        name="inp[name]" 
                        id="name" 
                        value="{{ old('inp.name', $workflow->name ?? '') }}" 
                        data-rule-required="true">
                </div>                            
            </div>                

            <div class="form-group row mb-4">
                <label class="col-md-2 col-form-label">{{ __('config.classification.input.description') }}</label>
                <div class="col-md-4">
                    <textarea class="form-control" 
                            name="inp[description]" 
                            id="description" 
                            rows="4" 
                            data-rule-required="true">{{ old('inp.description', $workflow->description ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Members Card -->
    <div class="card mb-3">
        <div class="card-header">
            <h6>Members</h6>
        </div>
        <div class="card-body">
            <label for="members" class="form-label">Started by</label>
            <div class="row" id="members">
                @foreach($members as $index => $member)
                    <div class="col-md-2 d-flex align-items-center py-1">
                        <input class="form-check-input me-2" 
                            style="transform: scale(0.8);" 
                            type="checkbox" 
                            id="member_id_{{ $member->id }}" 
                            name="members[]" 
                            value="{{ $member->id }}" 
                            @if(in_array($member->id, $selectedMembers ?? [])) checked @endif>
                        <small>{{ $member->name }}</small>
                    </div>

                    @if (($index + 1) % 5 === 0)
                        <div class="w-100"></div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="card-title mb-0">Workflow States</h6>
            <div class="action-btns">
                <a href="{{ url('config/workflow-state?workflow_id=' . $workflow->id) }}" class="btn btn-primary waves-effect waves-light me-3">
                    <span class="align-middle">Tambah</span>
                </a>
            </div>
        </div>
        <div class="card-body">

            <div class="form-group row mb-4">
                <label for="start_state_id" class="col-md-2 col-form-label">Start From State</label>
                <div class="col-md-3">
                    <select id="start_state_id" name="inp[start_state_id]" class=" form-select form-select-md">
                        @foreach ($workflowStates as $state)
                            <option value="{{ $state->id }}" data-workflow-id="{{ $state->id }}"
                                @if ($state->id == $workflow->start_state_id) selected @endif>
                                {{ $state->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group row mb-4">
                <label for="final_state_id" class="col-md-2 col-form-label">Final State</label>
                <div class="col-md-3">
                    <select id="final_state_id" name="inp[final_state_id]" class=" form-select form-select-md">
                        @foreach ($workflowStates as $state)
                            <option value="{{ $state->id }}" data-workflow-id="{{ $state->id }}"
                                @if ($state->id == $workflow->final_state_id) selected @endif>
                                {{ $state->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th width="40%">State Name</th> 
                        <th width="20%">Pengaturan Khusus</th> 
                        <th width="20%">Grant Access</th> 
                    </tr>
                </thead>
                <tbody>
                    @foreach ($workflowStates as $state)
                        <tr>
                            <td>
                                <a href="{{ url('config/workflow-state?state_id=' . $state->id) }}" class="text-decoration-none">
                                    <strong>{{ $state->name }}</strong>
                                </a>
                                <br>
                                <small>{{ $state->description }}</small>
                            </td>
                            <td>
                                @switch($state->rule_type)
                                    @case(0)
                                        Tidak Ada
                                        @break
                                    @case(1)
                                        Khusus Pembuat Dokumen
                                        @break
                                    @case(2)
                                        Khusus Pembimbing Akademik
                                        @break
                                    @case(3)
                                        Khusus Atasan Struktural
                                        @break
                                    @case(4)
                                        Khusus Kepala Unit
                                        @break
                                    @default
                                        - // Handle unexpected values
                                @endswitch
                            </td>
                            <td>
                                @php
                                    $memberNames = explode(',', $state->member_type_names);
                                    $canComments = explode(',', $state->can_comments);
                                    $canEditStates = explode(',', $state->can_edit_states);
                                    $canEditAttributes = explode(',', $state->can_edit_attributes);
                                    $canUploads = explode(',', $state->can_uploads);
                                    $canDownloads = explode(',', $state->can_downloads);
                                    $permissionIds = explode(',', $state->permission_ids);
                                @endphp
                        
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td>
                                    <a href="{{ route('workflow.permission', ['state_id' => $state->id]) }}" 
                                        class="d-flex align-items-center text-primary mb-3" 
                                        style="text-decoration: none; white-space: nowrap;">
                                        <span style="display: flex; align-items: center; flex-direction: row;">
                                            <i class="fas fa-plus-circle" style="font-size: 20px; margin-right: 8px;"></i>
                                            <span>Tambahkan Hak Akses</span>
                                        </span>
                                    </a>
                                </td>
                            </tr>
                    
                            @if (!empty($state->member_type_names))
                                @foreach ($memberNames as $index => $memberName)
                                    <tr>
                                        <td>
                                            <ul style="display: flex; list-style-type: none; padding: 0; margin: 0;">
                                                <li style="margin-right: 10px; text-align: center;">
                                                    <i class="fas fa-comments" style="color: {{ $canComments[$index] == 1 ? 'green' : 'red' }};"></i>
                                                </li>
                                                <li style="margin-right: 10px; text-align: center;">
                                                    <i class="fas fa-edit" style="color: {{ $canEditStates[$index] == 1 ? 'green' : 'red' }};"></i>
                                                </li>
                                                <li style="margin-right: 10px; text-align: center;">
                                                    <i class="fas fa-cog" style="color: {{ $canEditAttributes[$index] == 1 ? 'green' : 'red' }};"></i>
                                                </li>
                                                <li style="margin-right: 10px; text-align: center;">
                                                    <i class="fas fa-upload" style="color: {{ $canUploads[$index] == 1 ? 'green' : 'red' }};"></i>
                                                </li>
                                                <li style="text-align: center;">
                                                    <i class="fas fa-download" style="color: {{ $canDownloads[$index] == 1 ? 'green' : 'red' }};"></i>
                                                </li>
                                            </ul>
                                        </td>
                                        <td width="30%">
                                            <a href="{{ route('workflow.permission.edit', ['id' => $permissionIds[$index]]) }}" 
                                                class="text-primary" style="text-decoration: none;">
                                                <strong>{{ $memberName }}</strong>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </table>
                            </td>
                            
                            
                            
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    

    <!-- Workflow Tasks Card -->
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="card-title mb-0">Workflow Tasks</h6>
            <div class="action-btns">
                <a href="{{ url('config/workflow-task?workflow_id=' . $workflow->id) }}" class="btn btn-primary waves-effect waves-light me-3">
                    <span class="align-middle">Tambah</span>
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="50%">Task Name</th>
                        <th width="5%">Durasi</th>
                        <th width="20%">From State</th>
                        <th width="20%">To State</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($workflowTasks as $task)
                        <tr>
                            <td>{{ $task->display_order }}</td>
                            <td>
                                <a href="{{ url('config/workflow-task?task_id=' . $task->id) }}" class="text-decoration-none">
                                    <strong>{{ $task->name }}</strong>
                                </a><br>
                                <small>{{ $task->description }}</small>
                            </td>
                            <td>{{ $task->duration }} Hari</td>
                            <td>{!! nl2br(e($task->from_state_name)) !!}</td>
                            <td>{!! nl2br(e($task->to_state_name)) !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Document and File Types Card -->
    <div class="card mb-3">
        <div class="card-header">
            <h6>Document and File Types</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Document Types -->
                <div class="col-md-6">
                    <label for="select2Doc" class="form-label">Document Types</label>
                    <select id="select2Doc" name="documents[]" class="select2 form-select" multiple>
                        @foreach ($documents as $document)
                            <option value="{{ $document->id }}" 
                                    @if(in_array($document->id, $selectedDocuments ?? [])) selected @endif>
                                {{ $document->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- File Types -->
                <div class="col-md-6">
                    <label for="select2File" class="form-label">Available File Types</label>
                    <select id="select2File" name="files[]" class="select2 form-select" multiple>
                        @foreach ($files as $file)
                            <option value="{{ $file->id }}" 
                                    @if(in_array($file->id, $selectedFiles ?? [])) selected @endif>
                                {{ $file->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Card untuk Timestampable -->
    <div class="card mb-3 timestampable-section">
        <div class="card-header">
            <h6>Timestampable</h6>
        </div>
        <div class="card-body">
            <div class="form-group row mb-2">
                <label class="col-md-3 col-form-label">{{ __('common.updated_by') }}</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="updated_by" id="updated_by" value="{{ $workflow->updated_by ?? 'N/A' }}"  readonly>
                </div>
            </div>
            <div class="form-group row mb-2">
                <label class="col-md-3 col-form-label">{{ __('common.updated_at') }}</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="updated_at" id="updated_at" readonly>
                </div>
            </div>
        </div>
    </div>

    
</form>


@endsection

@section('vendor-script')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('page-script')
<script>
let dTable = null;
let url = '{{ url('config/workflow-designer') }}';

function save() {
    if ($("#frm").valid()) {
        const formData = new FormData();

        // Collecting form data
        formData.append('id', $('#id').val());
        formData.append('name', $('#name').val());
        formData.append('description', $('#description').val());
        formData.append('start_state_id', $('#start_state_id').val());
        formData.append('final_state_id', $('#final_state_id').val());

        // Get selected member IDs as an array of numbers
        const selectedMembers = getCheckedValues('member_id_');
        formData.append('members', JSON.stringify(selectedMembers));

        // Get selected document and file type IDs
        formData.append('documents', JSON.stringify(getSelect2Values('#select2Doc')));
        formData.append('filesType', JSON.stringify(getSelect2Values('#select2File')));

        // AJAX request to send data to the backend
        $.ajax({
            url: `${url}/save`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status === 'success') {
                    // Show success message
                    toastr.success("{{ __('common.message_save_title') }}", "{{ __('common.message_success_save') }}", toastrOptions);
                    
                    // Redirect to the workflow designer page after a successful save
                    window.location.href = url; // Use the defined url variable
                } else {
                    toastr.error("{{ __('common.message_error_title') }}", response.message, toastrOptions);
                }
            },
        });
    }
}

// Utility function to get selected values from a Select2 input
function getSelect2Values(selector) {
    return $(selector).val().map(Number);
}

// Utility function to get checked checkbox values by prefix
function getCheckedValues(prefix) {
    return $(`input[id^="${prefix}"]:checked`).map(function () {
        return parseInt(this.id.replace(prefix, ''), 10);
    }).get();
}

// Format date to DD-MM-YYYY
function formatDate(dateString) {
    if (!dateString || dateString === '0000-00-00 00:00:00') {
        return ''; // Handle invalid dates
    }

    const date = new Date(dateString);
    if (isNaN(date.getTime())) return ''; // Return empty for invalid dates

    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Month is zero-indexed
    const year = date.getFullYear();
    return `${day}-${month}-${year}`;
}

// Populate updated_at field on page load
document.addEventListener('DOMContentLoaded', function () {
    const updatedAt = @json($workflow->updated_at);
    document.getElementById('updated_at').value = formatDate(updatedAt);
});




</script>
@endsection