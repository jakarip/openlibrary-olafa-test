@extends('layouts/layoutMaster')

@section('title', ' Duplikat Jurnal menjadi Jurnal Eproc')

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
</style>
@endsection

@section('content')

<div class="card ">

    <div class="form-group row m-4">
        <label for="eproc_edition_id" class="col-md-6 col-form-label">Pilih Edisi Proceeding</label>
        <div class="col-md-6">
            <select id="eproc_edition_id" name="inp[eproc_edition_id]" class="select2 form-select form-select-md">
                @foreach ($editions as $row)
                    @php
                        $mulai = explode('-', $row->datestart);
                        $mulai = $mulai[2] . '-' . $mulai[1] . '-' . $mulai[0];
                        $finish = explode('-', $row->datefinish);
                        $finish = $finish[2] . '-' . $finish[1] . '-' . $finish[0];
                    @endphp
                    <option value="{{ $row->eproc_edition_id }} ">
                        {{ $row->nama . ' (' . $mulai . ' s/d ' . $finish . ')' }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table border-top" id="table">
            <thead>
                <tr>
                    <th >Aksi</th>
                    <th >Nim</th>
                    <th >Nama</th>
                    <th >Katalog</th>
                    <th >Judul</th>
                    <th style="width: 15%">Status</th>
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
let url = '{{ url('olafa/e-proceeding-duplicate') }}';

$(function() {
    let eprocEditionId = $('#eproc_edition_id').val();
    // console.log(eprocEditionId);

    // Load data with the initial eprocEditionId
    dTable = $('.table').DataTable({
        ajax: {
            url: url + '/dt/' + eprocEditionId,
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false, },
            { data: 'master_data_user', name: 'master_data_user', orderable: true, searchable: true },
            { data: 'master_data_fullname', name: 'master_data_fullname', orderable: true, searchable: true },
            { data: 'code', name: 'code', orderable: true, searchable: true },
            { data: 'title', name: 'title', orderable: true, searchable: true },
            { data: 'duplicate', name: 'duplicate', orderable: true, searchable: true },
        ]
    });

    $('#eproc_edition_id').change(function() {
        let eprocEditionId = $(this).val();
        dTable.ajax.url(url + '/dt/' + eprocEditionId).load();
    });
});
</script>
@endsection