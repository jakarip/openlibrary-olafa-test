@extends('layouts/layoutMaster')

@section('title', 'Workflow Permission')

@section('vendor-style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
@endsection

@section('page-style')
@endsection

@section('content')
<div class="card">
    <div class="card-header sticky-element bg-label-secondary d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">New Workflow State Permission</h5>
        <div class="action-btns">
            <button type="button" class="btn btn-label-primary waves-effect me-2" onclick="window.history.back();" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
            <button type="button" class="btn btn-primary waves-effect waves-light" onclick="save()">{{ __('common.save') }}</button>
        </div>

    </div>
</div>

<form id="frm" class="form-validate">
    @csrf
    <input type="hidden" name="id" value="{{ old('id') }}">
    <!-- Workflow Card -->
    <div class="card mb-3">
        <div class="card-header">
            <h6>Permission Details</h6>
        </div>
        <div class="card-body">

            <div class="form-group row mb-4">
                <label for="state_id" class="col-md-3 col-form-label">State</label>
                <div class="col-md-4">
                    <select id="state_id" name="inp[state_id]" class="select2 form-select form-select-md"> 
                        @foreach ($workflowStates as $state)
                            <option value="{{ $state->id }}" >
                                {{ $state->name }}
                            </option>
                        @endforeach                                    
                    </select>
                </div>
            </div> 

            <div class="form-group row mb-4">
                <label for="member_type_id" class="col-md-3 col-form-label">Member Type</label>
                <div class="col-md-4">
                    <select class="select2 form-select form-select-md" id="member_type_id" name="inp[member_type_id]" required>
                        @foreach($memberTypes as $memberType)
                            @if(in_array($memberType->id, $selectedMembers))
                                <option value="{{ $memberType->id }}" {{ old('member_type_id') == $memberType->id ? 'selected' : '' }}>
                                    {{ $memberType->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Grant Access --}}
    <div class="card mb-3">
        <div class="card-header">
            <h6>Grant Access</h6>
        </div>
        <div class="card-body">
            <div class="form-group row mb-4 align-items-center">
                <label class="col-md-3 col-form-label">Edit State</label>
                <div class="col-md-9">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="can_edit_state" id="can_edit_state" value="1" onclick="toggleActive(this)">
                    </div>
                    <small class="form-text text-muted">Jenis anggota dapat menentukan state dokumen selanjutnya</small>
                </div>
            </div>

            <div class="form-group row mb-4 align-items-center">
                <label class="col-md-3 col-form-label">Edit Attribute</label>
                <div class="col-md-9">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="can_edit_attribute" id="can_edit_attribute" value="1" onclick="toggleActive(this)">
                    </div>
                    <small class="form-text text-muted">Jenis anggota dapat melakukan perubahan judul, abstraksi, dan subjek dokumen</small>
                </div>
            </div>
            
            <div class="form-group row mb-4 align-items-center">
                <label class="col-md-3 col-form-label">Upload</label>
                <div class="col-md-9">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="can_upload" id="can_upload" value="1" onclick="toggleActive(this)">
                    </div>
                    <small class="form-text text-muted">Jenis anggota dapat melakukan upload file baru untuk dokumen</small>
                </div>
            </div>

            <div class="form-group row mb-4 align-items-center">
                <label class="col-md-3 col-form-label">Download</label>
                <div class="col-md-9">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="can_download" id="can_download" value="1" onclick="toggleActive(this)">
                    </div>
                    <small class="form-text text-muted">Jenis anggota dapat men-download file yang telah diupload untuk dokumen</small>
                </div>
            </div>

            <div class="form-group row mb-4 align-items-center">
                <label class="col-md-3 col-form-label">Comment</label>
                <div class="col-md-9">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="can_comment" id="can_comment" value="1" onclick="toggleActive(this)">
                    </div>
                    <small class="form-text text-muted">Jenis anggota dapat menambahkan/membalas komentar dokumen</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Card untuk Timestampable -->
    <div class="card timestampable-section mb-3 d-none">
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
@endsection

@section('vendor-script')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
@endsection

@section('page-script')
<script>
    let dTable = null;
    let url = '{{ url('config/workflow-permission') }}';

    function save() {
        if($("#frm").valid()) {
            let formData = new FormData($('#frm')[0]);

            $.ajax({
                url: url + '/save',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if(data.status === 'success') {
                        window.location.href = data.redirect;
                        $('#frmbox').modal('hide');
                        dTable.draw();
                        toastr.success("{{ __('common.message_save_title') }}", "{{ __('common.message_success_save') }}", toastrOptions);
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
    }

    function toggleActive(checkbox) {
        checkbox.value = checkbox.checked ? 1 : 0;
    }
    
    </script>
@endsection
