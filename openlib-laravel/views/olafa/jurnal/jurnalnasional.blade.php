@extends('layouts/layoutMaster')

@section('title', ' Jurnal Nasional')

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
                    <th >No</th>
                    <th >Judul</th>
                    <th >Perguruan Tnggi</th>
                    <th >Total</th>
                    <th >ISSN</th>
                    <th >Tahun</th>
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
let url = '{{ url('olafa/jurnal-nasional') }}';

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
            {
                data: null, 
                name: 'id', 
                orderable: false, 
                searchable: false,
                className: 'text-center',
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            {data: 'title', name: 'title', orderable: true, searchable: true },
            {data: 'publisher_name', name: 'publisher_name', orderable: true, searchable: true },
            {data: 'eks', name: 'eks', orderable: true, searchable: true ,className: 'text-center'},
            {data: 'isbn', name: 'isbn', orderable: true, searchable: true },
            {data: 'published_year', name: 'published_year', orderable: true, searchable: true },
        ]
    });
});

</script>
@endsection