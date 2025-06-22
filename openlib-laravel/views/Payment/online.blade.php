@extends('layouts.layoutMaster')

@section('title', 'Online Payments')

@section('vendor-style')
@endsection

@section('page-style')
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="alert alert-info">
                    <strong>Total Penalty:</strong> 
                    <span id="total-denda">Rp {{ number_format($penalty, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-success">
                    <strong>Total Paid:</strong> 
                    <span id="total-bayar">Rp {{ number_format($payment, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-warning">
                    <strong>Remaining Penalty:</strong> 
                    <span id="sisa-denda">Rp {{ number_format($penalty - $payment, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover table-xs" id="table">
                <thead>
                    <tr>   
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Reference No</th>
                        <th>Payment Date</th>
                        <th>Payment Request Date</th>
                        <th>Payment Deadline</th>
                        <th>Payment Link Status</th>
                        <th>Payment Link</th>
                        <th>Payment Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Payment Request Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fa fa-navicon"></i> &nbsp;Payment Request Form
                </h5>
            </div>
            <form id="paymentForm" class="form-horizontal">
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Remaining Penalty</label>
                        <div class="col-sm-9" id="remaining-penalty"></div>
                    </div>
                    <div class="form-group row">
                        <label for="payment-amount" class="col-sm-3 col-form-label">Payment Amount</label>
                        <div class="col-sm-9">
                            <input type="tel" class="form-control" name="amount" id="payment-amount" required placeholder="Enter amount">
                            <small id="amount-error" class="text-danger d-none"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger mr-auto" data-dismiss="modal">
                        <i class="icon-switch"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="icon-floppy-disk"></i> Submit
                    </button>
                </div>
            </form>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#table').DataTable({
        ajax: {
            url: '{{ route("payment.online.json") }}',
            type: 'GET'
        },
        order: [[4, 'desc']],
        dom: '<"top"Bf>rt<"bottom"lip><"clear">',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="icon-file-excel"></i> Export Excel',
                className: 'btn btn-light'
            },
            {
                text: '<i class="icon-calendar2"></i> Request Payment',
                className: 'btn btn-warning',
                action: function() {
                    showPaymentModal();
                }
            }
        ],
        scrollX: true,
       columns: [
    { 
        data: 'member.master_data_user', 
        name: 'member.master_data_user',
        render: function(data) {
            return data || '';
        }
    },
    { 
        data: 'member.master_data_fullname', 
        name: 'member.master_data_fullname',
        render: function(data) {
            return data || '';
        }
    },
    { 
        data: 'pay_no_ref', 
        name: 'pay_no_ref',
        render: function(data, type, row) {
        if (row.pay_status == 1 && !data.includes('Invoice')) {
            return data + '<br><a href="' + row.invoice_url + '" target="_blank" class="btn btn-xs btn-info mt-1"><i class="icon-file-text"></i> Invoice</a>';
        }
            return data;
        }
    },
    { data: 'pay_payment_date', name: 'pay_payment_date' },
    { data: 'pay_request_date', name: 'pay_request_date' },
    { data: 'pay_expired_date', name: 'pay_expired_date' },
    { 
         data: 'pay_link_status', 
    name: 'pay_link_status',
    render: function(data) {
        let badgeClass = 'badge-secondary';
        let statusText = '-';
        
        if (data == 'success') {
            badgeClass = 'badge bg-success';
            statusText = 'Success';
        } else if (data == 'failed') {
            badgeClass = 'badge bg-danger';
            statusText = 'Failed';
        }
        
        return `<span class="${badgeClass}">${statusText}</span>`;
    }
    },
    { 
        data: 'pay_link', 
        name: 'pay_link',
        render: function(data, type, row) {
            if (row.pay_link_status == 'failed') {
                return `<button class="btn btn-xs btn-warning" onclick="generatePaymentLink(${row.pay_id})">
                    <i class="icon-sync"></i> Regenerate Link
                </button>`;
            }
            return `<a href="${data}" target="_blank" class="btn btn-xs btn-primary">
                <i class="icon-file-pdf"></i> Payment Link
            </a>`;
        }
    },
    { 
        data: 'pay_amount', 
        name: 'pay_amount',
        render: function(data) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
        }
    },
    { 
        data: 'pay_status', 
        name: 'pay_status',
        render: function(data) {
            const statusMap = {
            0: ['Request', 'badge bg-warning'],
            1: ['Success', 'badge bg-success'],
            2: ['Pending', 'badge bg-info'],
            3: ['Failed', 'badge bg-danger'],
            4: ['Void', 'badge bg-secondary'],
            5: ['Cancelled', 'badge bg-dark'],
            6: ['Expired', 'badge bg-secondary']
            };
            
            return `<span class="badge badge-${statusMap[data][1]}">${statusMap[data][0]}</span>`;
        }
    }
],
        initComplete: function() {
            $('.dataTables_filter input').attr('placeholder', 'Search...');
            $('.dataTables_length select').select2({
                minimumResultsForSearch: Infinity,
                width: 'auto'
            });
        }
    });

    // Payment modal logic

    $('#paymentModal').on('shown.bs.modal', function() {
    fetchRemainingPenalty();
    $('#payment-amount').val('');
    $('#amount-error').addClass('d-none');
    $('#payment-amount').focus();

    if (remainingPenalty <= 0) {
        alert('Remaining penalty is not available or invalid.');
        $('#paymentModal').modal('hide');
    }
});

    function formatInputAsRupiah(input) {
        let cursorPosition = input.selectionStart;
        let originalLength = input.value.length;
        
        // Get raw value
        let rawValue = input.value.replace(/[^0-9]/g, '');
        let numberValue = parseInt(rawValue) || 0;
        
        // Format as Rupiah
        let formattedValue = formatRupiah(numberValue);
        
        // Set the formatted value
        input.value = formattedValue;
        
        // Adjust cursor position
        let newLength = input.value.length;
        let lengthDiff = newLength - originalLength;
        input.setSelectionRange(cursorPosition + lengthDiff, cursorPosition + lengthDiff);
    }

    $('#payment-amount').on('input', function(e) {
        formatInputAsRupiah(this);
        
        // Get raw amount
        const rawAmount = $(this).val().replace(/[^0-9]/g, '');
        const amount = parseInt(rawAmount) || 0;
        console.log('Remaining Penalty:', remainingPenalty);
        // Validate
        if (amount > remainingPenalty) {
            $('#amount-error').text('Amount cannot exceed remaining penalty').removeClass('d-none');
            $(this).val(formatRupiah(remainingPenalty));
        } else {
            $('#amount-error').addClass('d-none');
        }
    });
$('.btn-danger[data-dismiss="modal"]').on('click', function() {
    $('#paymentModal').modal('hide');
});
    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();
        submitPaymentRequest();
    });
});

function showPaymentModal() {
    $('#paymentModal').modal('show');
}

function fetchRemainingPenalty() {
    $.ajax({
        url: '{{ route("payment.online.remaining") }}',
        type: 'GET',
        success: function(response) {
            remainingPenalty = response.remaining;
            $('#remaining-penalty').text('Rp ' + new Intl.NumberFormat('id-ID').format(remainingPenalty));
        },
        error: function() {
            alert('Failed to fetch remaining penalty');
        }
    });
}

function submitPaymentRequest() {
    const rawAmount = $('#payment-amount').val().replace(/[^0-9]/g, '');
    const amount = parseInt(rawAmount) || 0;
    
    // Validation
    if (amount <= 0) {
        alert('Payment amount must be greater than 0');
        return;
    }
    
    if (amount > remainingPenalty) {
        alert('Payment amount cannot exceed remaining penalty');
        return;
    }
    
    // Show loading state
    const submitBtn = $('#paymentForm').find('button[type="submit"]');
    submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
    
    $.ajax({
        url: '{{ route("payment.online.request") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            amount: amount
        },
        success: function(response) {   
            if (response.success) {
                $('#table').DataTable().ajax.reload();
                $('#paymentModal').modal('hide');
                
                // Open payment link in new tab
                if (response.payment_url) {
                    window.open(response.payment_url, '_blank');
                } else {
                    alert('Payment request created but no payment URL returned');
                }
            } else {
                alert(response.message || 'Failed to process payment request');
            }
        },
        error: function(xhr) {
            let errorMessage = 'An error occurred while processing your request';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            alert(errorMessage);
        },
        complete: function() {
            submitBtn.prop('disabled', false).html('<i class="icon-floppy-disk"></i> Submit');
        }
    });
}

function generatePaymentLink(paymentId) {
    if (confirm('Are you sure you want to regenerate payment link?')) {
        $.ajax({
            url: '{{ route("payment.online.regenerate") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                payment_id: paymentId
            },
            success: function(response) {
                if (response.success) {
                    $('#table').DataTable().ajax.reload();
                    
                    if (response.payment_url) {
                        window.open(response.payment_url, '_blank');
                    } else {
                        alert('Payment link regenerated but failed to open');
                    }
                } else {
                    alert(response.message || 'Failed to regenerate payment link');
                }
            },
            error: function() {
                alert('An error occurred while regenerating payment link');
            }
        });
    }
}

// Helper functions
function parseRupiah(str) {
    return parseInt(str.toString().replace(/[^0-9]/g, ''), 10) || 0;
}

function formatRupiah(amount) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
}
</script>
@endsection