@extends('layouts/layoutMaster')

@section('title', __('config.workflow_state.page.title'))

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
                    <th width="10%">Workflow</th> 
                    <th width="20%">Name</th>
                    <th width="30%">Description</th> 
                    <th width="10%">{{ __('common.updated_by') }}</th> 
                    <th width="10%">{{ __('common.updated_at') }}</th> 
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
                <div class="modal-title"><i class="ti ti-forms me-2"></i>Form Workflow State</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm" class="form-validate">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    
                    <!-- Card for Workflow and Workflow State Name -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6>Workflow State</h6>
                        </div>
                        <div class="card-body">

                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">Workflow</label>
                                <div class="col-md-9">
                                    <select id="workflow_id" name="inp[workflow_id]" class="select2 form-select form-select-lg"> 
                                        @foreach ($workflows as $workflow)
                                            <option value="{{ $workflow->id }}" {{ isset($workflowState) && $workflowState->workflow_id == $workflow->id ? 'selected' : '' }}>
                                                {{ $workflow->name }}
                                            </option>
                                        @endforeach                                    
                                    </select>
                                    <small class="form-text text-muted">Pilih Workflow</small>
                                </div>
                            </div>

                            
                            <!-- Input for Workflow State Name -->
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">{{ __('config.workflow_state.input.name') }}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="inp[name]" id="name" data-rule-required="true">
                                    <small class="form-text text-muted">Name of the workflow state.</small>
                                </div>
                            </div>
            
                            <!-- Input for Description -->
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">{{ __('config.workflow_state.input.description') }}</label>
                                <div class="col-md-9">
                                    <textarea class="form-control" name="inp[description]" id="description" rows="3" data-rule-required="true"></textarea>
                                    <small class="form-text text-muted">Provide a description for this workflow state.</small>
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">Pengaturan Khusus</label>
                                <div class="col-md-9">
                                    <select id="rule_type" name="inp[rule_type]" class="form-control select2 form-select form-select-lg">
                                        <option value="0" {{ isset($workflowState) && $workflowState->rule_type == 0 ? 'selected' : '' }}>Tidak Ada</option>
                                        <option value="1" {{ isset($workflowState) && $workflowState->rule_type == 1 ? 'selected' : '' }}>Khusus Pembuat Dokumen</option>
                                        <option value="2" {{ isset($workflowState) && $workflowState->rule_type == 2 ? 'selected' : '' }}>Khusus Pembimbing Akademik</option>
                                        <option value="3" {{ isset($workflowState) && $workflowState->rule_type == 3 ? 'selected' : '' }}>Khusus Atasan Struktural</option>
                                        <option value="4" {{ isset($workflowState) && $workflowState->rule_type == 4 ? 'selected' : '' }}>Khusus Kepala Unit</option>
                                    </select>
                                    <small class="form-text text-muted">Pilih Pengaturan Khusus</small>
                                </div>
                            </div>
            
                            
                            
                        </div>
                    </div>
                </form>
            
                    <!-- Card for Timestampable (Updated By and Updated At) -->
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
let url = '{{ url('config/workflow-state') }}';

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
            {data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' },
            {data: 'workflow_name', name: 'workflow_name', orderable: true, searchable: true },
            {data: 'state_name', name: 'state_name', orderable: true, searchable: true },
            { data: 'description', name: 'description', orderable: false, searchable: true },
            { data: 'updated_by', name: 'updated_by', orderable: false, searchable: false },
            { 
                data: 'updated_at', 
                name: 'updated_at', 
                orderable: false, 
                searchable: false,
                render: function(data) {
                    return formatDate(data); // Panggil fungsi formatDate
                }
            },
        ]
    });

    const workflowId = '{{ $workflow_id }}';
    const stateId = '{{ $state_id }}';
    if (workflowId) {
        $('#workflow_id').val(workflowId).trigger('change');
        add();
    }

    if (stateId) { 
        edit(stateId); 
    }

    @if(auth()->can('config-state-designer.create'))
        $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> Tambah Workflow State</button>`);
    @endif
});

function add() {
    _reset();

    const workflowId = '{{ $workflow_id }}';
    if (workflowId) {
        $('#workflow_id').val(workflowId).trigger('change');
    }
    $(".timestampable-section").addClass('d-none');
    $("#frmbox").modal('show');
}

function edit(id) {
    $.ajax({
        url: url + '/get/' + id, 
        type: 'get',
        dataType: 'json',
        success: function(e) {
            _reset(); 
            
            $('#id').val(e.id); 
            $('#name').val(e.name); 
            $('#description').val(e.description);

            $('#workflow_id').val(e.workflow_id).trigger('change'); 
            $('#rule_type').val(e.rule_type).trigger('change'); 

             // Tampilkan bagian timestamp jika ada
            $(".timestampable-section").removeClass('d-none');

            // Cek nilai dan set ke input
            $('#updated_by').val(e.updated_by || ''); // Tampilkan kosong jika tidak ada
            $('#updated_at').val(formatDate(e.updated_at));

            $("#frmbox").modal('show');
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
                url: url+'/delete',
                data: { id : id },
                type: 'delete',
                dataType: 'json',
                success: function(e) {
                    if (e.status == 'success') {
                        dTable.draw();
                        toastr.success("{{ __('common.message_delete_title') }}", "{{ __('common.message_success_delete') }}", toastrOptions)
                    }
                }
            });
        }
    })
} 

// Reset function to clear inputs and select fields
function _reset() {
    $('#frm')[0].reset(); // Reset form fields
    $('.select2').val(null).trigger('change'); // Reset Select2 fields
    $('#updated_by').val(''); // Clear updated_by field
    $('#updated_at').val(''); // Clear updated_at field
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