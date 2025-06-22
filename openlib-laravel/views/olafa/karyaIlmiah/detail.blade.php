@extends('layouts/layoutMaster')

@section('title', 'Detail Data Karya Ilmiah')

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
        z-index: 9999;
    }
</style>
@endsection

@section('content')

{{-- <h6>{{$kode_prodi}}</h6> --}}

<div class="card">
    <div class="card-datatable table-responsive pt-0">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Detail Data Karya Ilmiah {{$nama_prodi}}</h5>
            <button class="btn btn-openlib-red btn-md me-2" onclick="window.history.back()"><i class="ti ti-arrow-left ti-sm me-1"></i> Kembali</button>
        </div>
        <table class="datatables-basic table border-top" id="table">
            <thead>
                <tr>
                    <th width="20%">Nama</th>
                    <th width="70%">Judul</th>
                    <th width="10%">Tahun</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
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
let url = '{{ url('olafa/karya-ilmiah/detail') }}/' + kode_prodi;

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
            {data: 'author', name: 'author', orderable: true, searchable: true },
            {data: 'title', name: 'title', orderable: true, searchable: true },
            {data: 'published_year', name: 'published_year', orderable: true, searchable: true },
        ]
    });

});

</script>
@endsection