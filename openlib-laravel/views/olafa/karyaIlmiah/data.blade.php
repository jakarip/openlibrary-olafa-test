@extends('layouts/layoutMaster')

@section('title', 'Data Karya Ilmiah')

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

<div class="card">
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table border-top" id="table">
            <thead>
                <tr>
                    <th width="10%">Aksi</th>
                    <th width="30%">Fakultas</th>
                    <th width="40%">Program Studi</th>
                    <th width="20%">Jumlah Karya Ilmiah</th>
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
let url = '{{ url('olafa/karya-ilmiah') }}';

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
            { data: 'action', name: 'action', orderable: false, searchable: false, class: 'text-center' },
            { data: 'nama_prodi', name: 'nama_prodi', orderable: true, searchable: true },
            { data: 'nama_fakultas', name: 'nama_fakultas', orderable: true, searchable: true },
            { data: 'jml_ta', name: 'jml_ta', orderable: true, searchable: true, class: 'text-center' },
        ]
    });

});


</script>
@endsection