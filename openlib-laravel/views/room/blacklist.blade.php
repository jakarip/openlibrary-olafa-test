@extends('layouts/layoutMaster')

@section('title', __('rooms.blacklist_title'))

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
    <div class="card-datatable table-responsive pt-4">
        <table class="datatables-basic table" id="table">
            <thead>
                <tr>
                    <th>{{ __('common.action') }}</th>
                    <th>{{ __('rooms.blacklist_table_username') }}</th>
                    <th>{{ __('rooms.blacklist_table_name') }}</th>
                    <th>{{ __('rooms.blacklist_table_reason') }}</th>
                    <th>{{ __('rooms.blacklist_table_date') }}</th>
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
                <div class="modal-title"><i class="ti ti-forms me-2"></i>{{ __('rooms.blacklist_adddata') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="frm" class="form-validate">
                    @csrf
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('rooms.blacklist_form_membername') }} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="inp[bl_username]" id="bl_username" data-rule-required="true" placeholder="{{ __('rooms.blacklist_form_membername_placeholder') }}">
                            <div id="search-results" class="dropdown-menu" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('rooms.blacklist_form_reason') }} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="inp[bl_reason]" id="bl_reason" data-rule-required="true">
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('rooms.blacklist_form_blacklistuntildate') }} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="date" class="form-control" name="inp[bl_date]" id="bl_date" data-rule-required="true">
                        </div>
                    </div>
                </form>
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
@endsection

@section('page-script')
<script>
let dTable = null;
let url = '{{ url('room/blacklist') }}';

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
            { data: 'bl_username', name: 'bl_username', orderable: true, searchable: true },
            { data: 'master_data_fullname', name: 'master_data_fullname', orderable: true, searchable: true },
            { data: 'bl_reason', name: 'bl_reason', orderable: true, searchable: true },
            { data: 'bl_date', name: 'bl_date', orderable: true, searchable: true }
        ]
    });

    @if(auth()->can('room-blacklist.create'))
        $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> {{ __('rooms.blacklist_adddata') }} </button>`)
    @endif
});

var debounceTimer;
$(document).ready(function() {
    $('#bl_username').on('input', function() {
        clearTimeout(debounceTimer);
        var query = $(this).val();
        debounceTimer = setTimeout(function() {
            if (query.length >= 5 && query.length <= 10) {
                $('#search-results').html('<div class="loading">Loading...</div>').show();

                $.ajax({
                    url: url + '/search',
                    method: 'GET',
                    data: { search: query },
                    dataType: 'json',
                    success: function(data) {
                        var results = '';
                        if (data.length > 0) {
                            $.each(data, function(index, item) {
                                results += '<a href="#" class="dropdown-item" data-id="' + item.master_data_user + '" data-fullname="' + item.master_data_fullname + '">' +
                                            '<div>' + item.master_data_number + ' - ' + item.master_data_user + ' - ' + item.master_data_fullname + '</div>' +
                                            '</a>';
                            });
                        } else {
                            results = '<div class="dropdown-item">"{{ __('common.message_error_data_not_found') }}"</div>';
                        }
                        $('#search-results').html(results).show();
                    },
                    error: function() {
                        $('#search-results').html('<div class="dropdown-item">"{{ __('common.message_error_title') }}"</div>').show();
                    }
                });
            } else {
                $('#search-results').hide();
            }
        }, 300);
    });

    // Handle selecting a result
    $(document).on('click', '.dropdown-item', function(e) {
        e.preventDefault(); // Mencegah perilaku default anchor
        var selectedUser  = $(this).data('id'); // Ambil master_data_user dari data-id
        $('#bl_username').val(selectedUser ); // Set value to input dengan master_data_user
        $('#search-results').hide(); // Hide results
    });

    // Hide results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#bl_username').length) {
            $('#search-results').hide();
        }
    });
});

function add() {
    // _reset();
    $("#frmbox").modal('show');
}

function save()
{
    if ($("#frm").valid()) {
        let formData = new FormData($('#frm')[0]);

        let bl_username = $('#bl_username').val();
        formData.append('bl_username', bl_username);

        $.ajax({
            url: url + '/save',
            type: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if (data.status == 'success') {
                    $('#frmbox').modal('hide');
                    dTable.draw();
                    toastr.success("{{ __('common.message_save_title') }}", "{{ __('common.message_success_save') }}", toastrOptions);
                } else if (data.status == 'error') {
                    toastr.error("{{ __('common.message_save_title') }}", data.message, toastrOptions);
                }
            }
        });
    }
}

function deleteByUsername(username) {
    yswal_deleteblacklist.fire({
        title: "{{ __('common.message_delete_prompt_title') }}",
        text: "{{ __('common.message_delete_prompt_text') }}"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url + '/delete',
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    bl_username: username
                },
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success("{{ __('common.message_save_title') }}", response.message, toastrOptions);
                        dTable.draw();
                    } else {
                        toastr.error("{{ __('common.message_save_title') }}", response.message, toastrOptions);
                    }
                },
                error: function() {
                    toastr.error("{{ __('common.message_save_title') }}", "Terjadi kesalahan saat menghapus data.", toastrOptions);
                }
            });
        }
    });
}

$('#frmbox').on('shown.bs.modal', function() {
    $('#frmbox form')[0].reset();
    let today = new Date();
    today.setMonth(today.getMonth() + 1);
    let month = (today.getMonth() + 1).toString().padStart(2, '0');
    let day = today.getDate().toString().padStart(2, '0');
    let defaultDate = today.getFullYear() + '-' + month + '-' + day;
    document.getElementById('bl_date').value = defaultDate;
});

</script>
@endsection
