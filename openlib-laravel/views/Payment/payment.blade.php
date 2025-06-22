@extends('layouts.layoutMaster')

@section('title', 'Payment List')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/limitless/global/css/plugins/pickers/datepicker.css') }}">
@endsection

@section('page-style')
<style>
    .alert-box {
        margin-bottom: 15px;
    }
    .table-responsive {
        overflow-x: auto;
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="alert alert-info">
                    <strong>Total Penalty:</strong> <span id="total-denda">Rp {{ number_format($penalty, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-success">
                    <strong>Total Payment:</strong> <span id="total-bayar">Rp {{ number_format($payment, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="col-md-4">
                 <div class="alert alert-warning">
                    <strong>Remaining Penalty:</strong> 
                    <span id="sisa-denda">
                        Rp {{ number_format(max($penalty - $payment, 0), 0, ',', '.') }}
                    </s>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" id="table">
                <thead>
                    <tr>   
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Payment Date</th>
                        <th>Payment Type</th>
                        <th>Payment Amount</th>
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
        ajax: {
            url: '{{ route("payment.json") }}',
            type: 'GET'
        },
        order: [[2, 'desc']],
        dom: 'Blfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        scrollX: true,
        language: {
            searchPlaceholder: 'Search...'
        },
        initComplete: function() {
            // Initialize select2 for length menu
            $('.dataTables_length select').select2({
                minimumResultsForSearch: Infinity,
                width: 'auto'
            });
        }
    });

    // Update uniform styling if needed
    $.uniform.update();
});
</script>
@endsection