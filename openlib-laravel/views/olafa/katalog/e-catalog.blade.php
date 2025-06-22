@extends('layouts/layoutMaster')

@section('title', 'Generate E-Catalog')

@section('vendor-style')
@endsection

@section('page-style')
<style>
</style>
@endsection

@section('content')
<div class="card">
    <h5 class="card-header">Advanced Search</h5>
    <!--Search Form -->
    <div class="card-body">
        <form class="dt_adv_search">
            <div class="row">
                <div class="col-12">
                    <div class="row g-3">
                        <h6 class=" col-lg-2 d-flex align-items-end col-auto">Pilih Tanggal:</h6>
                        <div class="col-12 col-sm-6 col-lg-8">
                            <input type="text" id="bs-datepicker-basic" class="form-control" />
                        </div>

                        <div class="col-12 col-sm-6 col-lg-1 d-flex align-items-end col-auto">
                            <button id="show-date-btn" class="btn btn-primary">Cari</button>
                        </div>
                        
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('vendor-script')
@endsection

@section('page-script')
<script>
    
let dTable = null;
let url = '{{ url('olafa/katalog') }}';
let bsDatepickerRange = null;

var startDate ;
var endDate ;

$(function(){

    $('#bs-datepicker-basic').datepicker({
        todayHighlight: true,
        orientation: 'bottom',
        format: 'mm/yyyy',
        minViewMode: 'months', // Only allow month and year selection
        }, 
    );

    $('#show-date-btn').click(function(event) {
        event.preventDefault(); // Prevent the default action (page refresh)
        selectedDate = $('#bs-datepicker-basic').datepicker('getDate'); // ajax rquest ke controler, format data yg di kirim ke controller
        console.log("Selected date: " + moment(selectedDate).format('YYYY-MM'));

    });

});    
</script>
@endsection