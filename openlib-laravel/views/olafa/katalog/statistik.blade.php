@extends('layouts/layoutMaster')

@section('title', 'Data Statistik')

@section('vendor-style')
@endsection

@section('page-style')
<style>
    .select2-container {
        z-index: 1;
    }

    .card {
        z-index: 0;
    }
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
                            <label for="bs-datepicker-daterange" class="form-label">Pilih Tanggal:</label>
                            <input type="text" id="bs-datepicker-daterange" class="form-control" />
                        </div>

                        <div class="col-12 col-sm-6 col-lg-4">
                            <label for="location" class="form-label">Pilih Lokasi:</label>
                            <select id="location" class="select2 form-select form-select-md" multiple>
                                {{-- <option value="">Semua</option> --}}
                                {{-- @foreach($locations as $location)
                                    <option value="{{ $location->id }}">
                                        {{ $location->name }}
                                    </option>
                                @endforeach --}}
                            </select>
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
    <div class="card-datatable text-wrap" >
            <table class="table table-bordered table-striped dataTable no-footer" id="table">
                <thead>
                    <tr>
                        <th  rowspan="2">Nama</th>
                        <th  rowspan="2">Judul</th>
                        <th  rowspan="2">Total Eksemplar</th>
                        <th  colspan="7" class="text-center">Eksemplar</th>
                    </tr>
                    <tr>
                        <th >Tersedia </th>
                        <th >Dipinjam </th>
                        <th >Rusak </th>
                        <th >Hilang Diganti </th>
                        <th >Sedang Diproses </th>
                        <th >Cadangan </th>
                        <th >Weeding </th>
                        <th >Hilang </th>
                        <th >Expired </th>
                    </tr>
                </thead>
                <tbody>
    
                </tbody>
            </table>
    </div>
    

</div>


<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modal_Label" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-simple">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_Label">Data Statistik Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Modal content goes here -->
                <div class="card mb-3">
                    <div class="card-body">
                            <div class="card-datatable text-wrap">
                                    <table class="table" id="table_detail">
                                        <thead>
                                            <tr > 
                                                <th>Kode Katalog</th>
                                                <th>Barcode</th>
                                                <th>Judul</th>
                                                <th>Subject</th> 
                                                <th>Klasifikasi</th>  
                                                <th>Pengarang</th> 
                                                <th>Publisher</th> 
                                                <th>Status</th>
                                            </tr> 
                                        </thead>
                                        <tbody >
                                            
                                        </tbody>
                                    </table>
                            </div>
                    </div>
                </div>
                

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
let url = '{{ url('olafa/katalog') }}';

var startDate ;
var endDate ;

$(function(){
    dTable = $('#table').DataTable({
        ajax: {
            url: url+'/dt',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.startDate = startDate ? startDate.format('YYYY-MM-DD') : '';
                d.endDate = endDate ? endDate.format('YYYY-MM-DD') : '';
                d.location = $('#lokasi').val();
            },
            dataSrc: function(json) {
                return json.data;
            }
        },
        columns: [
            { data: 'nama', name: 'nama', },
            { data: 'judul', name: 'judul', class: 'text-center', render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0 " onclick="showModal(' + row.id + ',\'judul\')">' + data + '</button>';
            }},
            { data: 'eksemplar', name: 'eksemplar' ,class: 'text-center',render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0 " onclick="showModal(' + row.id + ',\'eksemplar\')">' + data + '</button>';
            }},
            { data: 'tersedia', name: 'tersedia', class: 'text-center',render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0 " onclick="showModal(' + row.id + ',\'1\')">' + data + '</button>';
            }},
            { data: 'dipinjam', name: 'dipinjam' ,class: 'text-center',render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0 " onclick="showModal(' + row.id + ',\'2\')">' + data + '</button>';
            }},
            { data: 'rusak', name: 'rusak' ,class: 'text-center',render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0 " onclick="showModal(' + row.id + ',\'3\')">' + data + '</button>';
            }},
            { data: 'hilang_diganti', name: 'hilang_diganti' ,class: 'text-center',render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0 " onclick="showModal(' + row.id + ',\'6\')">' + data + '</button>';
            }},
            { data: 'diolah', name: 'diolah' ,class: 'text-center',render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0 " onclick="showModal(' + row.id + ',\'7\')">' + data + '</button>';
            }},
            { data: 'cadangan', name: 'cadangan', class: 'text-center',render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0 " onclick="showModal(' + row.id + ',\'8\')">' + data + '</button>';
            }},
            { data: 'weeding', name: 'weeding' ,class: 'text-center',render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0 " onclick="showModal(' + row.id + ',\'9\')">' + data + '</button>';
            }},
            { data: 'hilang', name: 'hilang' ,class: 'text-center',render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0 " onclick="showModal(' + row.id + ',\'4\')">' + data + '</button>';
            }},
            { data: 'expired', name: 'expired' ,class: 'text-center',render: function(data, type, row) {
                return '<button type="button" class="btn btn-link p-0 " onclick="showModal(' + row.id + ',\'5\')">' + data + '</button>';
            }}
        ],
        responsive: false,
        scrollX: true,
        
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
        // dTable.ajax.reload();
    });

    var lokasi = @json($locations);
        lokasi.forEach(function(location) {
            $('#location').append('<option value="' + location.id + '">' + location.name + '</option>');
        });

});

$(document).ready(function() {

    function sendAjaxRequest() {
        $.ajax({
            url: url + '/dt',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                startDate: startDate ? startDate.format('YYYY-MM-DD') : '',
                endDate: endDate ? endDate.format('YYYY-MM-DD') : '',
                location: $('#lokasi').val()
            },
            success: function(response) {

            },
            error: function(xhr) {
                // Handle the error here
                // console.error(xhr.responseText);
            }
        });
    }


    $('#show-date-btn').on('click', function(event) {
        event.preventDefault();
        // sendAjaxRequest();
        dTable.ajax.reload();
    });

});

function showModal(id,type) {
    // console.log("id kolom adalah " + id);
    // console.log("type kolom adalah " + type);

    $('#modal').modal('show');

    // Destroy the DataTable if it already exists
    if ($.fn.DataTable.isDataTable('#table_detail')) {
        $('#table_detail').DataTable().clear().destroy();
        // $('#table_detail').empty(); // Clear the table's HTML
    }

    initializeDataTable(id, type);
}

function initializeDataTable(id, type) {

    $('#table_detail').DataTable({
        processing: true,
        serverSide: true,
        // searching: false,
        buttons: [],
        // lengthChange: false,
        // paging: false,
        // info: false,
        pageLength: 10,
        responsive: false,
        scrollX: true,
        ajax: {
            url: url+'/dt_detail',
            type: 'POST',
            headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            data: {
                id: id,
                type: type,
            }
        },
        columns: [
                { data: 'code', name: 'code' },
                { data: 'barcode', name: 'barcode' },
                { data: 'title', name: 'title' },
                { data: 'subjek', name: 'subjek' },
                { data: 'klasifikasi', name: 'klasifikasi' },
                { data: 'author', name: 'author' },
                { data: 'publisher_name', name: 'publisher_name' },
                { data: 'status', name: 'status' },
            ]   
    });
}

</script>
@endsection