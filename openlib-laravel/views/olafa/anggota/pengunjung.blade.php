@extends('layouts/layoutMaster')

@section('title', 'Pengunjung')

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
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label for="bs-datepicker-daterange" class="form-label">Pilih bulan & tahun</label>
                                <input type="text" id="bs-datepicker-daterange" class="form-control" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <hr class="mt-0">

    <div class="card-datatable table-responsive pt-0">
        <table class="dt-row-grouping table border-top" id="table">
            <thead>
                <tr>
                    <th >Fakultas</th>
                    <th >Program Studi</th>
                    <th style="width: 15%;">Day (08:00 - 16:30)</th>
                    <th style="width: 15%;">Night (16:30 - 19:30)</th>
                    <th style="width: 15%;">Total</th>
                </tr>
            </thead>


            <tfoot>
                <tr>
                    <th colspan="2">Total</th>
                    <th id="total-day"></th>
                    <th id="total-night"></th>
                    <th id="total-all"></th>
                </tr>
            </tfoot>
            
        </table>
    </div>
</div>
@endsection

@section('vendor-script')

@endsection

@section('page-script')
<script>

let dTable = null;
let url = '{{ url('olafa/pengunjung') }}';
let bsDatepickerRange = null;


var startDate = moment().startOf('month');
var endDate = moment().endOf('month');
// var startDate = moment('2020-12-01');
// var endDate = moment('2025-02-02');

$(function() {

    dTable = $('#table').DataTable({
        pageLength: 25,
        ajax: {
            url: url + '/dt',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.startDate = startDate.format('YYYY-MM-DD');
                d.endDate = endDate.format('YYYY-MM-DD');
            },
        },
        
        columns: [
            { data: 'fakultas', name: 'fakultas', orderable: true, searchable: true },
            { data: 'prodi', name: 'prodi', orderable: false, searchable: false },
            { data: 'day', name: 'day', orderable: false, searchable: false },
            { data: 'night', name: 'night', orderable: false, searchable: false },
            { 
                data: null, 
                name: 'total', 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    return row.day + row.night;
                }
            },
        ],
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();

            // Calculate the total for day, night, and total columns
            var totalDay = api.column(2).data().reduce(function (a, b) {
                return a + b;
            }, 0);

            var totalNight = api.column(3).data().reduce(function (a, b) {
                return a + b;
            }, 0);

            var totalAll = totalDay + totalNight;

            // Update the footer
            $(api.column(2).footer()).html(totalDay + ' Pengunjung');
            $(api.column(3).footer()).html(totalNight + ' Pengunjung');
            $(api.column(4).footer()).html(totalAll + ' Pengunjung');
        }
    });

    $('#bs-datepicker-daterange').daterangepicker({
        ranges: {
            Today: [moment(), moment()],
            Yesterday: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        showDropdowns: true,
        opens: isRtl ? 'left' : 'right',
    }, function(start, end, label) {
        // console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        startDate = start;
        endDate = end;  
        dTable.ajax.reload();
    });

});
</script>
@endsection