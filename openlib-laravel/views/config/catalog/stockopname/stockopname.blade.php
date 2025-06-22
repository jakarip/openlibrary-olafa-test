@extends('layouts/layoutMaster')

@section('title', __('catalogs.stock_title'))

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
    .inactive-row {
        background-color: rgba(255, 0, 0, 0.2) !important; /* Warna merah transparan */
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-datatable table-responsive m-4">

        <div class="d-flex justify-content-end align-items-center w-100 gap-3">
            <div class="text-end" style="font-size: 14px;">
                {!! __('common.red_row_instruction', ['color' => '<span style="color:#dc3545;">' . __('common.red') . '</span>']) !!}<br>
                <span class="d-block mt-2">
                    {!! __('common.action_row_instruction', ['color' => '<span style="color:#7367f0;">' . __('common.action_button') . '</span>']) !!}
                </span>
            </div>
        </div>

        <hr class="mt-3">

        <table class="datatables-basic table" id="table">
            <thead>
                <tr>
                    <th width="10%">{{ __('common.action') }}</th>
                    <th>{{ __('catalogs.stock_number') }}</th>
                    <th>{{ __('catalogs.stock_date') }}</th>
                    <th>{{ __('catalogs.stock_name') }}</th>
                    <th>{{ __('catalogs.stock_status') }}</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('vendor-script')
@endsection

@section('page-script')
<script>
let dTable = null;
let url = '{{ url('catalog/stockopname') }}';

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
            { data: 'so_id', name: 'so_id', orderable: false, searchable: false },
            { data: 'so_date', name: 'so_date', orderable: true, searchable: true },
            { data: 'so_name', name: 'so_name', orderable: true, searchable: true },
            { data: 'so_status', name: 'so_status', orderable: true, searchable: true }
        ],
        rowCallback: function(row, data) {
            if (data.row_class) {
                $(row).addClass(data.row_class);
            }
        },
        responsive: false,
        scrollX: true
    });

    $('.dtb').append(`
        <a href="stockopname/katalog" class="btn btn-info btn-sm me-2">
            <i class="ti ti-file ti-sm me-1"></i> {{ __('catalogs.stock_add_referenceweeding') }}
        </a>
    `);
});

</script>
@endsection
