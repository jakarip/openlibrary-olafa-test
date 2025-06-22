@extends('layouts/layoutMaster')

@section('title', __('config.worflow_task.page.title'))

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
                    <th>{{ __('config.workflow_task.input.workflow') }}</th> 
                    <th>{{ __('config.workflow_task.input.name') }}</th> 
                    <th>{{ __('config.workflow_task.input.description') }}</th> 
                    <th>{{ __('common.updated_by') }}</th> 
                    <th>{{ __('common.updated_at') }}</th> 
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
                <div class="modal-title"><i class="ti ti-forms me-2"></i> {{ __('config.workflow_task.form.title') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm" class="form-validate">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <!-- Card untuk Task Details -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6>Task Detail</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">{{ __('config.workflow_task.input.workflow') }}</label>
                                <div class="col-md-9">
                                    <select id="workflow_id" name="inp[workflow_id]" class="select2 form-select form-select-lg"> 
                                        @foreach ($workflows as $workflow)
                                            <option value="{{ $workflow->id }}">{{ $workflow->name }}</option>
                                        @endforeach                                    
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">{{ __('config.workflow_task.input.name') }}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="inp[name]" id="name" data-rule-required="true">
                                </div>                            
                            </div>                 
    
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">{{ __('config.classification.input.description') }}</label>
                                <div class="col-md-9">
                                    <textarea class="form-control" name="inp[description]" id="description" rows="4" data-rule-required="true"></textarea>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">{{ __('config.workflow_task.input.duration') }}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="inp[duration]" id="duration" data-rule-required="true">
                                </div>                            
                            </div>                 
    
                            <div class="form-group row mb-4">
                                <label class="col-md-3 col-form-label">{{ __('config.workflow_task.input.display_order') }}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="inp[display_order]" id="display_order" data-rule-required="true">
                                </div>                            
                            </div>                 
                        </div>
                    </div>

                    {{-- Card untuk workflow proses --}}
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6>Workflow Proses</h6>
                        </div>
                        <div class="card-body">

                            <div class="form-group row mb-4">
                                <label for="state_id" class="col-md-3 col-form-label">From State</label>
                                <div class="col-md-9">
                                    <select id="state_id" name="inp[state_id][]" class="select2 form-select form-select-lg" multiple>
                                        @foreach ($workflows_state as $state)
                                            <option value="{{ $state->id }}"
                                                {{ in_array($state->id, $stateIds ?? []) ? 'selected' : '' }}>
                                                {{ $state->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-4">
                                <label for="next_state_id" class="col-md-3 col-form-label">Next State</label>
                                <div class="col-md-9">
                                    <select id="next_state_id" name="inp[next_state_id]" class="select2 form-select form-select-lg">
                                        @foreach ($workflows_state as $state)
                                            <option value="{{ $state->id }}" data-workflow-id="{{ $state->workflow_id }}">
                                                {{ $state->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </form>
    
                    <!-- Card untuk Timestampable -->
                    <div class="card timestampable-section d-none ">
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
let url = '{{ url('config/workflow-task') }}';

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
            { data: 'workflow_name', name: 'workflow_name', orderable: false, searchable: false }, 
            { data: 'name', name: 'name', orderable: true, searchable: true }, 
            { data: 'description', name: 'description', orderable: false, searchable: false }, 
            { data: 'updated_by', name: 'updated_by', orderable: false, searchable: false }, 
            { data: 'updated_at', name: 'updated_at', orderable: false, searchable: false,
                render: function(data, type, row) {
                    if (!data || isNaN(new Date(data).getTime())) {
                        return '-'; // Return a placeholder if the date is invalid
                    }
                    const date = new Date(data);
                    const day = String(date.getDate()).padStart(2, '0'); // Menambahkan nol di depan jika perlu
                    const month = String(date.getMonth() + 1).padStart(2, '0'); // Bulan dimulai dari 0
                    const year = date.getFullYear();
                    return day + '-' + month + '-' + year; // Format DD-MM-YYYY
                }
            }, 
        ]
    });

    $('#workflow_id').on('change', function() {
        const workflowId = $(this).val();
        updateStateOptions(workflowId);
    });

    const workflowId = '{{ $workflow_id }}';
    const taskId = '{{ $task_id }}';
    
    if (workflowId) {
        
        $('#workflow_id').val(workflowId).trigger('change');
        add(); 
    } else {
        updateStateOptions('');
    }

    if (taskId) { 
        edit(taskId); 
    }

    function updateStateOptions(workflowId) {
        const $nextStateSelect = $('#next_state_id');

        $nextStateSelect.empty();

        @foreach ($workflows_state as $state)
            if ("{{ $state->workflow_id }}" == workflowId) {
                $nextStateSelect.append('<option value="{{ $state->id }}">{{ $state->name }}</option>');
            }
        @endforeach
    }
    @if(auth()->can('config-workflow-task.create'))
    $('.dtb').append('<button class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> {{ __('config.workflow_designer.form.add_task') }}</button>');
    @endif

});

function add() {
    _reset();
    const workflowId = '{{ $workflow_id }}';
    if (workflowId) {
        $('#workflow_id').val(workflowId).trigger('change'); // Set and select the workflow
    }
    $(".timestampable-section").addClass('d-none'); // Sembunyikan timestamp
    $("#frmbox").modal('show');
}

function edit(id) {
    $.ajax({
        url: url + '/get/' + id,
        type: 'get',
        success: function(e) {
            if (e.task){
            
                // console.log(e); // Log the response

                $("#id").val(e.task.id);
                $("#name").val(e.task.name);
                $("#description").val(e.task.description);
                $("#duration").val(e.task.duration);
                $("#display_order").val(e.task.display_order);
                $(".timestampable-section").removeClass('d-none');
                $("#workflow_id").val(e.task.workflow_id).trigger('change'); // Update workflow state

                // Set state values
                // $("#state_id").val(e.state_id).trigger('change');
                $('#state_id').val(e.stateIds).trigger('change');
                $("#next_state_id").val(e.task.next_state_id).trigger('change');

                // Fill Updated By and Updated At fields
                $('#updated_by').val(e.task.updated_by || ''); // Show empty if none
                $('#updated_at').val(formatDate(e.task.updated_at));

                $("#frmbox").modal('show');
            }
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

function _reset() {
    $('#frm')[0].reset(); // Reset form
    $("#id").val(''); // Reset ID
    $('.select2').val(null).trigger('change'); // Reset Select2 fields
    $("#updated_by").val(''); // Reset updated_by
    $("#updated_at").val(''); // Reset updated_at
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
