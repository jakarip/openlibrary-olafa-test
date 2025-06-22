@extends('layouts/layoutMaster')

@section('title', __('rooms.history_title'))

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
            @csrf
            <div class="row">
                <div class="col-12">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-lg-4">
                            <label for="ruangan" class="form-label">{{ __('common.select_room') }}</label>
                            <select id="ruangan" class="form-select form-select-md">
                                <option value="">{{ __('common.all') }}</option>
                                @foreach ($rooms as $room)
                                    <option value="{{ $room->room_id }}">
                                        {{ $room->room_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <div class="col-12 col-sm-6 col-lg-4">
                            <label for="status" class="form-label">{{ __('common.select_status') }}</label>
                            <select id="status" class="form-select form-select-md">
                                <option value="">{{ __('common.all') }}</option>
                                <option value="Attend">Attend</option>
                                <option value="Cancel">Cancel</option>
                                <option value="Not Approved">Not Approved</option>
                            </select>
                        </div> --}}
                    </div>
                </div>
            </div>
        </form>
    </div>
    <hr class="mt-3">
    <div class="card">
        <div class="card-datatable table-responsive pt-4">
            <table class="datatables-basic table" id="table">
                <thead>
                    <tr class="text-nowrap">
                        <th>{{ __('common.action') }}</th>
                        <th>{{ __('rooms.history_table_ordername') }}</th>
                        <th>{{ __('rooms.history_table_phonenumber') }}</th>
                        <th>{{ __('rooms.history_table_roomname') }}</th>
                        <th>{{ __('rooms.history_table_date') }}</th>
                        <th>{{ __('rooms.history_table_starttime') }}</th>
                        <th>{{ __('rooms.history_table_endtime') }}</th>
                        <th>{{ __('rooms.history_table_purpose') }}</th>
                        <th>{{ __('rooms.history_table_numbermembers') }}</th>
                        <th>{{ __('common.status') }}</th>
                        <th>{{ __('rooms.history_table_payment') }}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="paymentModal" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i>{{ __('rooms.history_table_payment') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="paymentForm" class="form-validate">
                    @csrf
                    <input type="hidden" name="bk_id" id="bk_id">
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">{{ __('rooms.history_table_payment') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="inp[bk_payment]" id="bk_payment_input">
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
let url = '{{ url('room/history') }}';

function formatRupiah(angka, prefix) {
    let number_string = angka.replace(/[^,\d]/g, '').toString(),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
    return prefix === undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
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
                d.room = $('#ruangan').val();
                d.bk_status = $('#status').val();
            }
        },
        columns: [
            { data: 'action', name: 'action', orderable: true, searchable: true },
            { data: 'bk_member_name', name: 'bk_member_name', orderable: true, searchable: true },
            { data: 'bk_mobile_phone', name: 'bk_mobile_phone', orderable: true, searchable: true },
            { data: 'bk_room_name', name: 'bk_room_name', orderable: true, searchable: true },
            { data: 'bk_startdate', name: 'bk_startdate', orderable: true, searchable: true },
            { data: 'jam_mulai', name: 'jam_mulai', orderable: true, searchable: true },
            { data: 'jam_selesai', name: 'jam_selesai', orderable: true, searchable: true },
            { data: 'bk_purpose', name: 'bk_purpose', orderable: true, searchable: true },
            { data: 'bk_total', name: 'bk_total', orderable: true, searchable: true },
            { data: 'bk_status', name: 'bk_status', orderable: true, searchable: true },
            { data: 'bk_payment', name: 'bk_payment', orderable: true, searchable: true }
        ],
        responsive: false,
        scrollX: true,
    });

    $('#ruangan, #status').on('change', function() {
        dTable.ajax.reload();
    });

    $('#bk_payment_input').on('keyup', function(e) {
        let val = $(this).val();
        $(this).val(formatRupiah(val, 'Rp '));
    });
});

function save() {
    if($("#paymentForm").valid()) {
        let rawPayment = $('#bk_payment_input').val().replace(/[^,\d]/g, '');
        $('#bk_payment_input').val(rawPayment);

        let formData = new FormData($('#paymentForm')[0]);

        $.ajax({
            url: url+'/save',
            type: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if(data.status == 'success'){
                    $('#paymentModal').modal('hide');
                    $('.table').DataTable().ajax.reload(null, false);
                    toastr.success("{{ __('common.message_save_title') }}", "{{ __('common.message_success_save') }}", toastrOptions);
                }
            }
        });
    }
}

function status(paymentId, type, bookingId) {
    _reset();
    $('#paymentModal').modal('show');
    $('#bk_id').val(bookingId);
    $('#bk_payment_input').val(formatRupiah(paymentId.toString(), 'Rp '));
    console.log(paymentId, type, bookingId);
}

</script>
@endsection
