@extends('layouts.layoutMaster')

@section('title', 'Penalty Management')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/limitless/global/css/plugins/pickers/datepicker.css') }}">
@endsection

@section('page-style')
<style>
    .summary-card {
        margin-bottom: 15px;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .table-xs td, .table-xs th {
        padding: 0.5rem;
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="alert alert-info summary-card">
                    <strong>Total Penalty:</strong> 
                    <span id="total-denda">Rp {{ number_format($penalty, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-success summary-card">
                    <strong>Total Paid:</strong> 
                    <span id="total-bayar">Rp {{ number_format($payment, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-warning summary-card">
                    <strong>Remaining Penalty:</strong> 
                    Rp {{ number_format(max($penalty - $payment, 0), 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover table-xs" id="table">
                <thead>
                    <tr>   
                        <th>Status</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Title</th>
                        <th>Catalog Number</th>
                        <th>Barcode</th>
                        <th>Borrow Date</th>
                        <th>Return Date</th>
                        <th>Due Date</th>
                        <th>Penalty</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
<script src="{{ asset('assets/limitless/global/js/plugins/pickers/datepicker.js') }}"></script>
<script src="{{ asset('assets/limitless/global/js/plugins/tables/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/limitless/global/js/plugins/forms/selects/select2.min.js') }}"></script>
<script src="{{ asset('assets/limitless/global/js/plugins/forms/validation/validate.min.js') }}"></script>
<script src="{{ asset('assets/limitless/global/js/plugins/forms/styling/uniform.min.js') }}"></script>
@endsection

@section('page-script')
<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("penalty.json") }}',
            type: 'GET'
        },
        order: [[0, 'asc']],
        dom: '<"top"Bf>rt<"bottom"lip><"clear">',
        buttons: [
            {
                extend: 'excel',
                text: 'Export Excel',
                className: 'btn btn-success'
            },
            {
                extend: 'pdf',
                text: 'Export PDF',
                className: 'btn btn-danger'
            },
            {
                extend: 'print',
                text: 'Print',
                className: 'btn btn-primary'
            }
        ],
        scrollX: true,
        language: {
            search: '_INPUT_',
            searchPlaceholder: 'Search...',
            lengthMenu: 'Show _MENU_ entries'
        },
        initComplete: function() {
            // Initialize select2 for length menu
            $('.dataTables_length select').select2({
                minimumResultsForSearch: Infinity,
                width: 'auto'
            });
        },
       columns: [
    { data: 'status', name: 'status' },
    { data: 'username', name: 'username' },
    { data: 'full_name', name: 'full_name' },
    { data: 'title', name: 'title' },
    { data: 'catalog_number', name: 'catalog_number' },
    { data: 'barcode', name: 'barcode' },
    { data: 'borrow_date', name: 'borrow_date' },
    { data: 'return_date', name: 'return_date' },
    { data: 'due_date', name: 'due_date' },
    { data: 'penalty', name: 'penalty' }
]
    });

    // Initialize uniform styling if needed
    $.uniform.update();
});
</script>
@endsection