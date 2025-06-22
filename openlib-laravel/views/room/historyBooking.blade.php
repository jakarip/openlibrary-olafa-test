@extends('layouts/layoutMaster')

@section('title', __('rooms.currentbooking_title'))

@section('vendor-style')
@endsection

@section('page-style')
<style>
    .highcharts-credits,
    .highcharts-button {
        display: none;
    }
    .select2-container {
        z-index: 9999;
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
                        @php
                            $filteredLocations = $locations->filter(function($location) {
                                return in_array($location->id, [9, 15]); // hanya tampilkan lokasi dengan id 9 dan 15
                            });

                            // Set default location jika belum ada yang dipilih
                            if (!isset($selected_location) || !$filteredLocations->contains('id', $selected_location)) {
                                $selected_location = 9; // Default ke ID 9
                            }
                        @endphp

                        <div class="col-12 col-sm-6 col-lg-4">
                            <label for="lokasi" class="form-label">{{ __('common.select_location') }}</label>
                            <select id="lokasi" name="location" class="form-select form-select-md">
                                <option value="">Semua</option>
                                @foreach ($filteredLocations as $location)
                                    <option value="{{ $location->id }}"
                                            {{ $selected_location == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <hr class="mt-0">
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table" id="table">
                <thead>
                    <tr class="text-nowrap">
                        <th width="12%">{{ __('common.action') }}</th>
                        <th>{{ __('rooms.currentbooking_table_ordername') }}</th>
                        <th>{{ __('rooms.currentbooking_table_phonenumber') }}</th>
                        <th>{{ __('rooms.currentbooking_table_roomname') }}</th>
                        <th>{{ __('rooms.currentbooking_table_date') }}</th>
                        <th>{{ __('rooms.currentbooking_table_starttime') }}</th>
                        <th>{{ __('rooms.currentbooking_table_endtime') }}</th>
                        <th>{{ __('rooms.currentbooking_table_purpose') }}</th>
                        <th>{{ __('rooms.currentbooking_table_numbermembers') }}</th>
                        <th>Nama Anggota</th>
                        <th>{{ __('common.status') }}</th>
                        <th>{{ __('common.reason') }}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
@endsection

@section('page-script')
<script>
let dTable = null;
let url = '{{ url('room/historybooking') }}';

function add() {
    _reset();
    $("#frmbox").modal('show');
}

$(function() {
    dTable = $('.table').DataTable({
        ajax: {
            url: url+'/dt',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.location = $('#lokasi').val();
            }
        },
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false, },
            { data: 'bk_member_name', name: 'bk_member_name', orderable: true, searchable: true },
            { data: 'bk_mobile_phone', name: 'bk_mobile_phone', orderable: true, searchable: true },
            { data: 'bk_room_name', name: 'bk_room_name', orderable: true, searchable: true },
            { data: 'bk_startdate', name: 'bk_startdate', orderable: true, searchable: true },
            { data: 'jam_mulai', name: 'jam_mulai', orderable: true, searchable: true },
            { data: 'jam_selesai', name: 'jam_selesai', orderable: true, searchable: true },
            { data: 'bk_purpose', name: 'bk_purpose', orderable: true, searchable: true },
            { data: 'bk_total', name: 'bk_total', orderable: true, searchable: true },
            { data: 'bk_name', name: 'bk_name', orderable: true, searchable: true },
            { data: 'bk_status', name: 'bk_status', orderable: true, searchable: true },
            { data: 'bk_reason', name: 'bk_reason', orderable: true, searchable: true }

        ],
        responsive: false,
        scrollX: true,
    });
    $('#lokasi').on('change', function() {
        dTable.ajax.reload();
    });
});

function status(bk_status, action, id) {
    let newStatus;
    if (action === 'approve') {
        newStatus = 'Approved';
        confirmStatusChange(newStatus, id);
    } else if (action === 'notApprove') {
        yswal_reason_historybooking.fire({
            title: "{{ __('common.message_rejection_prompt_title') }}",
            input: 'textarea',
            inputPlaceholder: "{{ __('common.message_rejection_prompt_text') }}",
            preConfirm: (reason) => {
                if (!reason) {
                    yswal_reason_historybooking.showValidationMessage("{{ __('common.message_rejection_prompt_validation') }}");
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
    } else if (action === 'attend') {
        newStatus = 'Attend';
        confirmStatusChange(newStatus, id);
    } else if (action === 'notAttend') {
        newStatus = 'Not Attend';
        confirmStatusChange(newStatus, id);
    }
}

function confirmStatusChange(newStatus, id, reason = null) {
    yswal_confirmstatus_historybooking.fire({
        title: `{{ __('common.message_change_status_prompt_title') }}`,
        text: `{{ __('common.message_change_status_prompt_text') }} ${newStatus}?`
    }).then((result) => {
        if (result.isConfirmed) {
            updateStatus(id, newStatus, reason);
        }
    });
}

function updateStatus(id, newStatus, reason = null) {
    $.ajax({
        url: url + `/change/${id}`,
        type: 'POST',
        dataType: 'json',
        data: { bk_status: newStatus, bk_reason: reason, _token: $('meta[name="csrf-token"]').attr('content') },
        success: function(e) {
            if (e.message) {
                $('.table').DataTable().ajax.reload();
                toastr.success("{{ __('common.message_save_title') }}", "{{ __('common.message_success_save') }}", toastrOptions)
            }
        },
        error: function (xhr, status, error) {
            toastr.error("{{ __('common.message_failed_save') }}", "{{ __('common.message_error_title') }}", toastrOptions);
        }
    });
}

</script>
@endsection
