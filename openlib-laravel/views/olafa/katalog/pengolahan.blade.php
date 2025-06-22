@extends('layouts/layoutMaster')

@section('title', 'Pengolahan')

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

    /* table.dataTable.dt-select-no-highlight tbody tr.selected,
    table.dataTable.dt-select-no-highlight tbody th.selected,
    table.dataTable.dt-select-no-highlight tbody td.selected {
        color: unset;
    }
    table.dataTable.dt-select-no-highlight tbody>tr.selected,
    table.dataTable.dt-select-no-highlight tbody>tr>.selected {
        background-color: unset;
    } */

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

                        <div class="col-12 col-sm-6 col-lg-12">
                            <label for="location" class="form-label">Pilih Lokasi:</label>
                            <select id="location" class="select2 form-select form-select-md" multiple>

                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="bs-datepicker-daterange" class="form-label">Pilih Tanggal:</label>
                            <input type="text" id="bs-datepicker-daterange" class="form-control" />
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="type" class="form-label">Pilih Jenis Katalog:</label>
                            <select id="type" class="select2 form-select form-select-md" >
                                <option value='all'>Semua</option>
                                @foreach($tipe as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="origination" class="form-label">Beli/Sumbangan Buku:</label>
                            <select id="origination" class="select2 form-select form-select-md">
                                <option value="all" selected>Semua</option>
                                <option value="1">Beli</option>
                                <option value="2">Sumbangan</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="status" class="form-label">Pilih Status Buku:</label>
                            <select id="status" class="select2 form-select form-select-md" >
                                <option value="all" selected disabled >Pilih Status Buku</option>
                                <option value='7' >Sedang Diproses</option>
                                <option value='1' >Tersedia</option> 
                                <option value='3' >Rusak</option>
                                <option value='4' >Hilang</option>
                                <option value='5' >Expired</option>
                                <option value='6' >Hilang Diganti</option>
                                <option value='8' >Cadangan</option>
                                <option value='9' >Weeding</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="klasifikasi" class="form-label">Pilih Klasifikasi:</label>
                            <select id="klasifikasi" class="select2 form-select form-select-md">
                                <option value="" selected disabled>Pilih Klasifikasi</option>
                                <option value="0">000-099</option>
                                <option value="1">100-199</option>
                                <option value="2">200-299</option>
                                <option value="3">300-399</option>
                                <option value="4">400-499</option>
                                <option value="5">500-599</option>
                                <option value="6">600-699</option>
                                <option value="7">700-799</option>
                                <option value="8">800-899</option>
                                <option value="9">900-999</option> 
                            </select>
                        </div>
                        
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="barcode" class="form-label">Barcode:</label>
                            <input id="barcode" type="text"  class="form-control" />
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
        <table class="table table-bordered table-striped no-footer dataTable dt-select-no-highlight" id="table">
            <thead>
                <tr>
                    <th  ></th>
                    <th  >Jenis</th>
                    <th  >Katalog</th>
                    <th  >Barcode</th>
                    <th  >Klasifikasi</th>
                    <th  >Judul</th>
                    <th  >Pengarang</th>
                    <th  >Penerbit</th>
                    <th  >Lokasi</th>
                    <th  >Status</th>
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

// let dTable = null;
let url = '{{ url('olafa/katalog/pengolahan') }}';

var startDate ;
var endDate ;


$(function(){
    
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
    });

    var lokasi = @json($locations);
        lokasi.forEach(function(location) {
            $('#location').append('<option value="' + location.id + '">' + location.name + '</option>');
        });

});    


function initializeDataTable() {
        if ($.fn.DataTable.isDataTable('.table')) {
            dTable.ajax.reload();
        } else {
            dTable = $('.table').DataTable({
                ajax: {
                    url: url + '/dt',
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(d) {
                        d.startDate = startDate ? startDate.format('YYYY-MM-DD') : '';
                        d.endDate = endDate ? endDate.format('YYYY-MM-DD') : '';

                        d.location = $('#location').val();
                        d.type = $('#type').val();
                        d.origination = $('#origination').val();
                        d.status = $('#status').val();
                        d.klasifikasi = $('#klasifikasi').val();
                        d.barcode = $('#barcode').val();

                    },
                    dataSrc: function(json) {
                        return json.data;
                    }
                },
                columns: [
                    { data: 'id', name: 'id', orderable: true, searchable: true },
                    { data: 'tipe', name: 'tipe', orderable: true, searchable: true },
                    { data: 'catalog', name: 'catalog', orderable: true, searchable: true },
                    { data: 'barcode', name: 'barcode', orderable: true, searchable: true },
                    { data: 'klasifikasi', name: 'klasifikasi', orderable: true, searchable: true },
                    { data: 'title', name: 'title', orderable: true, searchable: true },
                    { data: 'author', name: 'author', orderable: true, searchable: true },
                    { data: 'publisher_name', name: 'publisher_name', orderable: true, searchable: true },
                    { data: 'location_name', name: 'location_name', orderable: true, searchable: true },
                    { data: 'origination', name: 'origination', orderable: true, searchable: true },
                ],
                columnDefs: [
                    {
                        // For Checkboxes
                        targets: 0,
                        searchable: false,
                        orderable: false,
                        render: function () {
                            return '<input type="checkbox" class="dt-checkboxes form-check-input">';
                        },
                        checkboxes: {
                            selectRow: true,
                            selectAllRender: '<input type="checkbox" class="form-check-input">'
                        }
                    },
                ],
                select: {
                    // Select style
                    style: 'multi',
                    items: 'row', // Allow row selection
                    selector: 'td:first-child input[type="checkbox"]', // Only select the checkbox column
                    blurable: true,
                    className: 'row-selected',
                    info: false
                },
                // responsive: false,
                // scrollX: true,
            });
        }

        $('.dtb').append('<button class="btn btn-openlib-red btn-sm me-2" onclick="ubah()"><i class="ti ti-file-plus ti-sm me-1"></i> Ubah Status</button>');
}


function ubah(){
    var selectedItems = dTable.rows({ selected: true }).data();
    var selectedIds = [];
    selectedItems.each(function (item) {
        selectedIds.push(item.id);
    });

    $.ajax({
            url: url + '/ubah',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                ids: selectedIds,
            },
            success: function(data) {
                if (data.success) {
                    dTable.ajax.reload();
                    dTable.rows().deselect();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });
                }
            },
            error: function(xhr,data) {
                
                // Handle the error here
                // console.error(xhr.responseText);
            }
    });

    
}

$(document).ready(function() {
    
    $('#show-date-btn').on('click', function(event) {
        event.preventDefault();
        if ($.fn.DataTable.isDataTable('.table')) {
                dTable.ajax.reload();
            } else {
                initializeDataTable();
            }
    });
});
</script>
@endsection