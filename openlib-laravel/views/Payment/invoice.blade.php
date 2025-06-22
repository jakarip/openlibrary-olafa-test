@extends('layouts.layoutMaster')

@section('title', 'Payment Invoice')

@section('page-style')
<style>
    .invoice-table th, .invoice-table td {
        color: black; /* Warna teks hitam */
    }
</style>
@endsection

@section('content')
<div class="card invoice-container">
    <div id="invoice-content">
        <div class="invoice-header" style="background-color: #3b7ddd; color: white; text-align: center; padding: 15px; font-size: 18px; font-weight: bold;">
                <div class="print-only">
                <img src="https://openlibrary.telkomuniversity.ac.id/images/logo_openlibrary.png" alt="Logo" style="height:40px;">
                </div>
            <h5 style="color: white;"><i class="fas fa-file-alt" style="color: white;"></i> Payment Invoice</h5>
        </div>
        
        <div class="card-body invoice-body">
            <table class="table table-borderless invoice-table">
                <tr>
                    <th>No Ref</th>
                    <td>: {{ $invoice->pay_no_ref ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Tanggal</th>
                    <td>: {{ $invoice->pay_payment_date ? \Carbon\Carbon::parse($invoice->pay_payment_date)->format('d-m-Y H:i') : '-' }}</td>
                </tr> 
                <tr>
                    <th>Nama Lengkap</th>
                    <td>: {{ $invoice->member($invoice->pay_id_member)->master_data_fullname ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>: {{ $invoice->member($invoice->pay_id_member)->master_data_email ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Nomor Telepon</th>
                    <td>: {{ $invoice->member($invoice->pay_id_member)->master_data_mobile_phone ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Total Bayar Denda</th>
                    <td>: Rp {{ number_format($invoice->pay_amount, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Metode Pembayaran</th>
                    <td>: {{ $invoice->pay_method_name ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                     <td style="color: green;">: Pembayaran Berhasil</td>
                </tr> 
            </table>
            
            <div class="text-center mt-4">
                <strong>Terima kasih atas pembayaran Anda.</strong>
            </div>
        </div>
    </div>
    
    <div class="card-footer text-center no-print">
        <button onclick="printInvoice()" class="btn btn-primary">
            <i class="icon-printer"></i> Print Invoice
        </button>
        <a href="{{ route('payment.online') }}" class="btn btn-secondary ml-2">
            <i class="icon-arrow-left"></i> Back to Payments
        </a>
    </div>
</div>
@endsection

@section('page-script')
<script>
function printInvoice() {
    var printContents = document.getElementById('invoice-content').innerHTML;
	var originalContents = document.body.innerHTML;
	document.body.innerHTML = printContents;
	window.print();
	document.body.innerHTML = originalContents;
	location.reload();
}
</script>
@endsection