@extends('layouts/layoutMaster')

@section('title', ' Detail Mata Kuliah')

@section('vendor-style')
@endsection

@section('page-style')
<style>
.highcharts-credits,
.highcharts-button {
    display: none;
}
</style>
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
    <div class="card-datatable table-responsive pt-0">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0"> Detail Mata Kuliah {{$nama_prodi}}</h5>
            <button class="btn btn-openlib-red btn-md me-2" onclick="window.history.back()"><i class="ti ti-arrow-left ti-sm me-1"></i> Kembali</button>
        </div>
        <table class="datatables-basic table border-top" id="table">
            <thead>
                <tr>
                    <th width="10%">Aksi</th>
                    <th width="15%">Kode Mata Kuliah</th>
                    <th width="15%">Semester</th>
                    <th width="20%">Mata Kuliah</th>
                    <th width="10%">SKS</th>
                    <th width="15%">Jumlah Judul Refrensi Buku Tercetak</th>
                    <th width="15%">Jumlah Judul Refrensi E-Book</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i>Detail Mata Kuliah</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="card">
                    <div class="card-datatable p-3">
                        <table class="table table-bordered no-footer dataTable dt-select-no-highlight nowrap " id="bukuDetailTable">
                            <thead>
                                <tr>
                                    <th width="10%">No Induk</th>
                                    <th width="10%">No Kelas</th>
                                    <th width="3%">Judul Buku</th>
                                    <th width="15%">Pengarang</th>
                                    <th width="15%">Tahun Terbit</th>
                                    <th width="15%">ISBN</th>
                                    <th width="12%">Jumlah Total</th> 
                                    <th width="12%">Jumlah Tersedia</th> 
                                </tr>
                            </thead>
                            <tbody> 
                                
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@section('vendor-script')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection


@section('page-script')
<script>
let dTable = null;

const kode_prodi = '{{$kode_prodi}}';
const tahun = '{{$tahun}}';

let url = '{{ url('olafa/bahan-pustaka/detail') }}/' + kode_prodi + '/' + tahun;

$(function() {
    dTable = $('.table').DataTable({
        ajax: {
            url: url + '/dt',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [

            {data: 'action', name: 'action', orderable: true, searchable: true },
            {data: 'code', name: 'code', orderable: true, searchable: true },
            {data: 'semester', name: 'semester', orderable: true, searchable: true },
            {data: 'name', name: 'name', orderable: true, searchable: true },
            {data: 'sks', name: 'sks', orderable: true, searchable: true },
            {data: 'jmljudul_fisik', name: 'jmljudul_fisik', orderable: true, searchable: true },
            {data: 'jmljudul', name: 'jmljudul', orderable: true, searchable: true },
        ]
    });
});

function showModal(id, type) {
    $("#modal").modal('show');

    if ($.fn.DataTable.isDataTable('#bukuDetailTable')) {
        $('#bukuDetailTable').DataTable().clear().destroy();
        // $('#table_detail').empty(); // Clear the table's HTML
    }

    initializeDataTable(id, type);
}

function initializeDataTable(id, type) {
    $('#bukuDetailTable').DataTable({
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
            url: url + '/get/' + id +'/' + type, 
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
                { data: 'kode_buku', name: 'kode_buku' },
                { data: 'klasifikasi', name: 'klasifikasi' },
                { data: 'title', name: 'title' },
                { data: 'author', name: 'author' },
                { data: 'published_year', name: 'klasifikasi' },
                { data: 'isbn', name: 'isbn' },
                { data: 'eks', name: 'eks' },
                { data: 'tersedia', name: 'tersedia' },
            ]   
    });

    // $.ajax({
    //     url: url + '/get/' + id +'/' + type, 
    //     type: 'get',
    //     dataType: 'json',
    //     success: function(response) {

    //         console.log(response);
    //         response.forEach(function(item) {
    //             $("#bukuDetailTable tbody").append(`
    //                 <tr>
    //                     <td>${item.kode_buku}</td>
    //                     <td>${item.klasifikasi}</td>
    //                     <td>${item.title}</td>
    //                     <td>${item.author}</td>
    //                     <td>${item.published_year}</td>
    //                     <td>${item.isbn}</td>
    //                     <td>${item.eks}</td>
    //                     <td>${item.tersedia}</td>
    //                 </tr>
    //             `);
    //         });
    //         $("#modal").modal('show');
    //     }
    // });
}


</script>
@endsection