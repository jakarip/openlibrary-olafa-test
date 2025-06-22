@extends('layouts/layoutMaster')

@section('title', 'Hide / Show Journal')

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
                    <th width="15%">Kode</th>
                    <th width="25%">Judul</th>
                    <th width="15%">Pengarang</th>
                    <th width="15%">Editor</th>
                    <th width="10%">Jurnal Status</th>
                    <th width="10%">Jurnal Eproc Status</th>
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
let url = '{{ url('olafa/remove-journal') }}';

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
            { data: 'code', name: 'code', orderable: true, searchable: true },
            { data: 'title', name: 'title', orderable: true, searchable: true },
            { data: 'author', name: 'author', orderable: true, searchable: true },
            { data: 'editor', name: 'editor', orderable: true, searchable: true },
        ]
    });

});


</script>
@endsection