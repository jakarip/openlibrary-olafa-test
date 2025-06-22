@extends('layouts/layoutMaster')

@section('title', ' List Katalog')

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
                        

                        <div class="col-12 col-sm-6 col-lg-12">
                            <label for="type" class="form-label">Pilih Jenis Koleksi:</label>
                            <select id="type" class="select2 form-select form-select-md" multiple>
                                {{-- <option value="">Semua</option> --}}
                                {{-- @foreach($koleksi as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->name }}
                                    </option>
                                @endforeach --}}
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="bs-datepicker-daterange" class="form-label">Pilih Tanggal:</label>
                            <input type="text" id="bs-datepicker-daterange" class="form-control" />
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
                        <th  >Jenis</th>
                        <th  >Katalog</th>
                        <th  >Judul</th>
                        <th  >Pengarang</th>
                        <th  >Penerbit</th>
                        <th  >Tahun</th>
                        <th  >Ekesemplar</th>
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
let url = '{{ url('olafa/katalog/list-katalog') }}';
let bsDatepickerRange = null;

var startDate ;
var endDate ;

$(function(){

    dTable = $('.table').DataTable({
        ajax: {
            url: url+'/dt',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.startDate = startDate ? startDate.format('YYYY-MM-DD') : '';
                d.endDate = endDate ? endDate.format('YYYY-MM-DD') : '';
                d.klasifikasi = $('#klasifikasi').val();
                d.type = $('#type').val();
            },
            dataSrc: function(json) {
                return json.data;
            }
        },
        columns: [
            { data: 'tipe', name: 'tipe', orderable: true, searchable: true },
            { data: 'catalog', name: 'catalog', orderable: true, searchable: true },
            { data: 'title', name: 'title', orderable: true, searchable: true },
            { data: 'author', name: 'author', orderable: true, searchable: true },
            { data: 'publisher_name', name: 'publisher_name', orderable: true, searchable: true },
            { data: 'published_year', name: 'published_year', orderable: false, searchable: false },
            { data: 'eksemplar', name: 'eksemplar', orderable: false, searchable: false },
        ],
        // responsive: false,
        // scrollX: true,
        
    });

    var koleksi = @json($koleksi);
        koleksi.forEach(function(item) {
            $('#type').append('<option value="' + item.id + '">' + item.name + '</option>');
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
})

$(document).ready(function() {
    
    $('#show-date-btn').on('click', function(e) {
        e.preventDefault();
        dTable.ajax.reload();
    });
});

</script>
@endsection