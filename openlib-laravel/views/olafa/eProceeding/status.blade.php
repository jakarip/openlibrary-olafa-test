@extends('layouts/layoutMaster')

@section('title', ' Ubah Status Publish Dokumen')

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
                    <th >Username</th>
                    <th >Nama</th>
                    <th >Kode</th>
                    <th >Judul</th>
                    <th >Editor</th>
                    <th >Status Publish</th>
                    <th width="45%">Aksi</th>
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
let url = '{{ url('olafa/e-proceeding-status') }}';

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
            { data: 'master_data_user', name: 'master_data_user', orderable: true, searchable: true },
            { data: 'master_data_fullname', name: 'master_data_fullname', orderable: true, searchable: true },
            { data: 'code', name: 'code', orderable: true, searchable: true },
            { data: 'title', name: 'title', orderable: true, searchable: true },
            { data: 'editor', name: 'editor', orderable: true, searchable: true },
            { data: 'name', name: 'name', orderable: true, searchable: true },
            { data: 'action', name: 'action', orderable: false, searchable: false, },
        ]
    });
});

function status(id,status,latest_state_id) {
    $.ajax({
        url: `${url}/change/${id}`,
        type: 'POST',
        dataType: 'json',
        data : {
			id : id,
			status : status,
			latest_state_id : latest_state_id
		},
        success: function(e) {

        }
    });
}

</script>
@endsection