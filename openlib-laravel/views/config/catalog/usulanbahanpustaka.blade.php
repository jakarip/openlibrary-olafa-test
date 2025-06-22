@extends('layouts/layoutMaster')

@section('title', __('catalogs.bahanpustaka_title'))

@section('vendor-style')
@endsection

@section('page-style')
<style>
    .highcharts-credits,
    .highcharts-button {
        display: none;
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form class="dt_adv_search">
            <div class="row">
                <div class="col-12">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-lg-4">
                            <label for="status" class="form-label">{{ __('common.select_status') }}</label>
                            <select id="status" class="form-select form-select-md">
                                <option value="">{{ __('common.all') }}</option>
                                <option value="Request">Request</option>
                                <option value="Approved">Approved</option>
                                <option value="Not Approved">Not Approved</option>
                                <option value="Process">Process</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <hr class="mt-0">
    <div class="card">
        <div class="card-datatable table-responsive pt-4">
            <table class="datatables-basic table" id="table">
                <thead>
                    <tr class="text-nowrap">
                        <th>{{ __('common.action') }}</th>
                        <th>{{ __('catalogs.bahanpustaka_table_number') }}</th>
                        <th>{{ __('catalogs.bahanpustaka_table_date') }}</th>
                        <th>{{ __('catalogs.bahanpustaka_table_faculty') }}</th>
                        <th>{{ __('catalogs.bahanpustaka_table_studyprogram') }}</th>
                        <th>{{ __('catalogs.bahanpustaka_table_studentnumber') }}</th>
                        <th>{{ __('catalogs.bahanpustaka_table_name') }}</th>
                        <th>{{ __('catalogs.bahanpustaka_table_title') }}</th>
                        <th>{{ __('catalogs.bahanpustaka_table_author') }}</th>
                        <th>{{ __('catalogs.bahanpustaka_table_publisher') }}</th>
                        <th>{{ __('catalogs.bahanpustaka_table_publishyear') }}</th>
                        <th>{{ __('catalogs.bahanpustaka_table_subject') }}</th>
                        <th>{{ __('catalogs.bahanpustaka_table_semester') }}</th>
                        <th>{{ __('catalogs.bahanpustaka_table_reference') }}</th>
                        <th>{{ __('catalogs.bahanpustaka_table_catalognumber') }}</th>
                        <th>{{ __('catalogs.bahanpustaka_table_historystatus') }}</th>
                        <th>{{ __('catalogs.bahanpustaka_table_status') }}</th>
                        <th>{{ __('catalogs.bahanpustaka_table_reasonrejection') }}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i>{{ __('catalogs.bahanpustaka_table_historystatus') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="historyList" style="display: flex; flex-direction: column;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
@endsection

@section('page-script')
<script>
let dTable = null;
let url = '{{ url('catalog/usulanbahanpustaka') }}';

$(function() {
    dTable = $('.table').DataTable({
        ajax: {
            url: url+'/dt',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.bp_status = $('#status').val();
            }
        },
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false, },
            { data: 'bp_id', name: 'bp_id', orderable: true, searchable: true },
            { data: 'bp_createdate', name: 'bp_createdate', orderable: true, searchable: true },
            { data: 'nama_fakultas', name: 'nama_fakultas', orderable: true, searchable: true },
            { data: 'nama_prodi', name: 'nama_prodi', orderable: true, searchable: true },
            { data: 'master_data_number', name: 'master_data_number', orderable: true, searchable: true },
            { data: 'master_data_fullname', name: 'master_data_fullname', orderable: true, searchable: true },
            { data: 'bp_title', name: 'bp_title', orderable: true, searchable: true },
            { data: 'bp_author', name: 'bp_author', orderable: true, searchable: true },
            { data: 'bp_publisher', name: 'bp_publisher', orderable: true, searchable: true },
            { data: 'bp_publishedyear', name: 'bp_publishedyear', orderable: true, searchable: true },
            { data: 'bp_matakuliah', name: 'bp_matakuliah', orderable: true, searchable: true },
            { data: 'bp_semester', name: 'bp_semester', orderable: true, searchable: true },
            { data: 'bp_reference', name: 'bp_reference', orderable: true, searchable: true },
            { data: 'bp_item_code', name: 'bp_item_code', orderable: true, searchable: true },
            { data: 'history', name: 'history', orderable: true, searchable: true },
            { data: 'bp_status', name: 'bp_status', orderable: true, searchable: true },
            { data: 'bp_reason', name: 'bp_reason', orderable: true, searchable: true }

        ],
        responsive: false,
        scrollX: true,
    });

    $('#status').on('change', function() {
        dTable.ajax.reload();
    });
});

function status(bp_status, action, id) {
    console.log("Status function called with:", bp_status, action, id);
    let newStatus;

   if (action === 'approve') {
        newStatus = 'Approved';
        confirmStatusChange(newStatus, id);
    } else if (action === 'process') {
        newStatus = 'Process';
        confirmStatusChange(newStatus, id);
    } else if (action === 'completed') {
        newStatus = 'Completed';
        confirmStatusChange(newStatus, id);
    } else if (action === 'notApprove') {
        yswal_reason_usulanbahanpustaka.fire({
            title: "{{ __('common.message_rejection_prompt_title') }}",
            input: 'textarea',
            inputPlaceholder: "{{ __('common.message_rejection_prompt_text') }}",
            preConfirm: (reason) => {
                if (!reason) {
                    yswal_reason_usulanbahanpustaka.showValidationMessage("{{ __('common.message_rejection_prompt_validation') }}");
                }
                return reason;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                newStatus = 'Not Approved';
                confirmStatusChange(newStatus, id, result.value);
            }
        });
    } else if (action === 'cancel') {
        newStatus = 'Cancel';
        confirmStatusChange(newStatus, id);
    }
}

function confirmStatusChange(newStatus, id, reason = null) {
    if (newStatus === 'Completed') {
        yswal_confirmstatuschange_usulanbahanpustaka.fire({
            title: `{{ __('common.message_change_status_prompt_title') }}`,
            text: "{{ __('common.konfigurasi_message_change_status_prompt_text') }}",
        }).then((result) => {
            if (result.isConfirmed) {
                saveToKnowledgeItem(id, newStatus, reason);
            }
        });
    } else {
        yswal_confirmstatus_usulanbahanpustaka.fire({
            title: `{{ __('common.message_change_status_prompt_title') }}`,
            text: `{{ __('common.message_change_status_prompt_text') }} ${newStatus}?`
        }).then((result) => {
            if (result.isConfirmed) {
                updateStatus(id, newStatus, reason);
            }
        });
    }
}

function saveToKnowledgeItem(id, newStatus, reason = null) {
    $.ajax({
        url: url + `/completed`,
        type: 'POST',
        dataType: 'json',
        data: {
            id: id,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            if (response.status) {
                toastr.success("{{ __('common.message_success_transfer_catalog') }}", "{{ __('common.success') }}", toastrOptions);
                updateStatus(id, newStatus, reason);
            } else {
                toastr.error("{{ __('common.message_failed_transfer_catalog') }}", "{{ __('common.message_error_title') }}", toastrOptions);
            }
        },
        error: function (xhr) {
            toastr.error("{{ __('common.message_error_title') }}", "Error", toastrOptions);
        }
    });
}

function updateStatus(id, newStatus, reason = null) {
    $.ajax({
        url: url + `/change/${id}`,
        type: 'POST',
        dataType: 'json',
        data: {
            bp_status: newStatus,
            bp_reason: reason,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(e) {
            if (e.message) {
                toastr.success(e.message, 'Success', toastrOptions);
                $('.table').DataTable().ajax.reload();
            }
        },
        error: function(xhr) {
            toastr.error("{{ __('common.message_error_title') }}", "Error", toastrOptions);
        }
    });
}

function history(concatenated_bps_date, concatenated_bps_status) {
    $('#historyList').empty();

    $('#historyList').append(`
        <div style="display: flex; justify-content: space-between; padding: 10px 0; font-weight: bold;">
            <div style="flex: 1;">{{ __('catalogs.bahanpustaka_table_status') }}</div>
            <div style="flex: 1; text-align: right;">{{ __('catalogs.bahanpustaka_table_date') }}</div>
        </div>
    `);

    // Split the concatenated data into arrays
    const dates = concatenated_bps_date ? concatenated_bps_date.split(',') : [];
    const statuses = concatenated_bps_status ? concatenated_bps_status.split(',') : [];

    // Populate the list
    const maxLength = Math.max(dates.length, statuses.length);
    for (let i = 0; i < maxLength; i++) {
        const date = dates[i] ? dates[i].trim() : '';
        const status = statuses[i] ? statuses[i].trim() : '';

        // Create a div for each row
        $('#historyList').append(`
            <div style="display: flex; justify-content: space-between; padding: 5px 0;">
                <div style="flex: 1;">${status}</div>
                <div style="flex: 1; text-align: right;">${date}</div>
            </div>
        `);
    }

    $('#historyModal').modal('show');
}
</script>
@endsection
