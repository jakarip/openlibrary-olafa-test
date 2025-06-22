@extends('layouts/layoutMaster')

@section('title', 'Sirkulasi Anggota')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
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
                            <div class="col-12 col-sm-6 col-lg-2">
                                <label for="bs-datepicker-basic" class="form-label">Pilih bulan & tahun</label>
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

        <hr class="mt-0">

    <div class="card-datatable text-wrap">
        <table class="table-bordered table " id="table">
            <thead>
                <tr> 
                    <th rowspan="2">Fakultas</th>
                    <th rowspan="2">Program Studi</th>
                    <th colspan="2" class="text-center">Peminjaman</th>
                    <th colspan="2" class="text-center">Pengembalian</th>             
                </tr>
                <tr>  
                    <th width="10%">Member</th>
                    <th width="10%">Peminjaman</th>
                    <th width="10%">Member</th>
                    <th width="10%">Pengembalian</th>             
                </tr>
            </thead>

            <tfoot>
                <tr>
                    <th colspan="2">Total</th>
                    <th id="total-peminjaman-anggota"></th>
                    <th id="total-peminjaman-total"></th>
                    <th id="total-pengembalian-anggota"></th>
                    <th id="total-pengembalian-total"></th>
                </tr>
            </tfoot>

            
        </table>
    </div>
</div>
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
@endsection

@section('page-script')
<script>

let dTable = null;
let url = '{{ url('olafa/sirkulasi') }}';
let selectedDate = null;

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
        // console.log("Selected date: " + moment(selectedDate).format('YYYY-MM'));

        $('#table').DataTable({
            pageLength: 25,
            destroy: true, // Destroy any existing table instance before creating a new one
            ajax: {
                url: url + '/dt',
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {
                    d.selectedDate = moment(selectedDate).format('YYYY-MM');
                },
                dataSrc: 'data', 
            },
            columns: [
                { data: 'fakultas', name: 'fakultas', orderable: true, searchable: true },
                { data: 'prodi', name: 'prodi', orderable: true, searchable: true },
                { 
                    data: null, 
                    name: 'peminjaman_anggota', 
                    orderable: true, 
                    searchable: true,
                    render: function(data, type, row) {
                        return (parseInt(row.peminjaman[0]?.anggota || 0) + parseInt(row.downloads[0]?.anggota || 0));
                    }
                },
                { 
                    data: null, 
                    name: 'peminjaman_total', 
                    orderable: true, 
                    searchable: true,
                    render: function(data, type, row) {
                        return (parseInt(row.peminjaman[0]?.total || 0) + parseInt(row.downloads[0]?.total || 0));
                    }
                },
                { 
                    data: null, 
                    name: 'pengembalian_anggota', 
                    orderable: true, 
                    searchable: true,
                    render: function(data, type, row) {
                        return (parseInt(row.pengembalian[0]?.anggota || 0) + parseInt(row.downloads[0]?.anggota || 0));
                    }
                },
                { 
                    data: null, 
                    name: 'pengembalian_total', 
                    orderable: true, 
                    searchable: true,
                    render: function(data, type, row) {
                        return (parseInt(row.pengembalian[0]?.total || 0) + parseInt(row.downloads[0]?.total || 0));
                    }
                },
            ],
            footerCallback: function (row, data, start, end, display) {
                var api = this.api();

                // Initialize totals
                var totalPeminjamanAnggota = 0;
                var totalPeminjamanTotal = 0;
                var totalPengembalianAnggota = 0;
                var totalPengembalianTotal = 0;

                // Calculate the total for each column
                data.forEach(function(rowData, index) {
                    var peminjamanAnggota = parseInt(rowData.peminjaman[0]?.anggota || 0) + parseInt(rowData.downloads[0]?.anggota || 0);
                    var peminjamanTotal = parseInt(rowData.peminjaman[0]?.total || 0) + parseInt(rowData.downloads[0]?.total || 0);
                    var pengembalianAnggota = parseInt(rowData.pengembalian[0]?.anggota || 0) + parseInt(rowData.downloads[0]?.anggota || 0);
                    var pengembalianTotal = parseInt(rowData.pengembalian[0]?.total || 0) + parseInt(rowData.downloads[0]?.total || 0);

                    totalPeminjamanAnggota += peminjamanAnggota;
                    totalPeminjamanTotal += peminjamanTotal;
                    totalPengembalianAnggota += pengembalianAnggota;
                    totalPengembalianTotal += pengembalianTotal;
                });

                $(api.column(2).footer()).html(totalPeminjamanAnggota + ' Member');
                $(api.column(3).footer()).html(totalPeminjamanTotal + ' Peminjaman');
                $(api.column(4).footer()).html(totalPengembalianAnggota + ' Member');
                $(api.column(5).footer()).html(totalPengembalianTotal + ' Pengembalian');
            }
        });
    });
});

</script>
@endsection