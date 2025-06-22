@extends('layouts/layoutMaster')

@section('title', 'Detail Mapping Katalog')

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


<form id="frm" class="form-validate">
    @csrf
    <input type="hidden" name="id" id="id" value="{{$id}}">
    <div class="row ">
        <div class="card mb-3 p-0">
            <div class="card-header sticky-element bg-danger d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0 text-white">Detail Mapping Katalog Mata Kuliah</h5>
            </div>
            <div class="card-body">
                <div class="form-group row mb-4">
                    <label class="col-md-2 col-form-label">Judul</label>
                    <div class="col-md-4">
                        <input type="text" 
                            class="form-control" 
                            name="inp[name]" 
                            id="name" 
                            value="{{$katalog->title}}" 
                            data-rule-required="true"
                            disabled>
                    </div>          
                </div>
                    
                <div class="form-group row mb-4">
                    <label class="col-md-2 col-form-label">Pilih Tahun Kurikulum</label>
                    <div class="col-md-4">
                        <select id="kurikulum" name="inp[kurikulum]" class="select2 form-select form-select-md">
                            @foreach ($curriculum_year as $year)
                                <option value="{{$year->curriculum_code}}">{{$year->curriculum_code}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
    
                <div class="form-group row mb-4">
                    <label class="col-md-2 col-form-label">Pilih Program Studi</label>
                    <div class="col-md-4">
                        <select id="prodi" name="inp[prodi]" class="select2 form-select form-select-md">
                            <option value="all">Semua</option>
                            @foreach ($study_program as $row)
                                <option value="{{$row->C_KODE_PRODI}}">{{ $row->NAMA_FAKULTAS }} - {{ $row->NAMA_PRODI }}</option>
                            @endforeach
                        </select>
                    </div>                            
                </div>
            </div>
        </div>
    </div>


    <div class="row row-cols-3 p-0">
        <div class="card col-md-5 p-0">
            <div class="card-header bg-danger d-flex justify-content-between align-items-center mb-3 ">
                <h5 class="card-title mb-0 text-white">{{ __('Mata Kuliah Di Katalog') }}</h5>
            </div>
            <div class="card-datatable table-responsive">
                <form id="form_registered_user_list">
                    <table id="registered_user_list" class="datatables-basic table table_registered">
                        <thead>
                            <tr>
                                <th width="5%"><input type="checkbox" id="select_all_registered" class="dt-checkboxes form-check-input"></th>
                                <th width="20%">{{ __('Kode') }}</th>
                                <th width="65%">{{ __('Mata Kuliah') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
        <div class="col-lg-2"> 
            <div class="text-center" style="margin-top:200px;">
                <button type="button" class="btn btn-danger mb-1" id="left" onclick="moveleft()" title="{{ __('Insert Course') }}"><i class="fa fa-arrow-left"></i></button><br>
                <button type="button" class="btn btn-danger" id="right" onclick="moveright()" title="{{ __('Delete Course') }}"><i class="fa fa-arrow-right"></i></button>
            </div>
        </div>  
        <div class="card col-md-5 p-0">
            <div class="card-header bg-danger d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0 text-white">{{ __('List Mata Kuliah') }}</h5>
            </div>
            <div class="card-datatable table-responsive">
                <form id="form_all_user_list">
                    <table id="all_user_list" class="datatables-basic table table_list">
                        <thead>
                            <tr>
                                <th width="5%"><input type="checkbox" id="select_all_all" class="dt-checkboxes form-check-input"></th>
                                <th width="20%">{{ __('Kode') }}</th>
                                <th width="65%">{{ __('Mata Kuliah') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
    
</form>

@endsection

@section('vendor-script')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection


@section('page-script')
<script>
let dTableAll = null;
let dTableRegistered = null;

let url = '{{ url('olafa/bahan-pustaka-mapping/detail') }}' ;

$(document).ready(function() { 
    dt_all_user_list($("#kurikulum").val(), $("#prodi").val());
    dt_registered_user_list($("#kurikulum").val(), $("#prodi").val());

    $('#kurikulum').change(function(){ 
        dt_all_user_list($(this).val(), $("#prodi").val());
        dt_registered_user_list($(this).val(), $("#prodi").val());
    });

    $('#prodi').change(function(){  
        dt_all_user_list($("#kurikulum").val(), $(this).val());
        dt_registered_user_list($("#kurikulum").val(), $(this).val());
    });

    $('#select_all_registered').click(function() {
        var checked = this.checked;
        $('#registered_user_list tbody input[type="checkbox"]').each(function() {
            this.checked = checked;
        });
    });

    $('#select_all_all').click(function() {
        var checked = this.checked;
        $('#all_user_list tbody input[type="checkbox"]').each(function() {
            this.checked = checked;
        });
    });

    
});

function dt_all_user_list(curriculum_year, study_program) {  
    if (dTableAll) {
        dTableAll.destroy();
    }
    dTableAll = $('.table_list').DataTable({
        pageLength: 10,
        buttons: [],
        processing: true,
        serverSide: true,
        ajax: {
            url: url+'/dt_not_list',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(data) {
                data.id = $('#id').val();
                data.kurikulum = $('#kurikulum').val(); //harus refactor nama 
                data.prodi = $('#prodi').val(); //harus refactor nama
            },
        },
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false },
            { data: 'code', name: 'code', orderable: true, searchable: true }, 
            { data: 'name', name: 'name', orderable: true, searchable: true }, 
        ]
    });
}

function dt_registered_user_list(curriculum_year, study_program) {  
    if (dTableRegistered) {
        dTableRegistered.destroy();
    }
    dTableRegistered = $('.table_registered').DataTable({
        pageLength: 10,
        buttons: [],
        processing: true,
        serverSide: true,
        ajax: {
            url: url+'/dt_list',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(data) {
                data.id = $('#id').val();
                data.kurikulum = $('#kurikulum').val(); //harus refactor nama 
                data.prodi = $('#prodi').val(); //harus refactor nama
            },
        },
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false },
            { data: 'code', name: 'code', orderable: true, searchable: true }, 
            { data: 'name', name: 'name', orderable: true, searchable: true },
        ]
    });
}

function moveright() {
    var selectedIds = $('#registered_user_list tbody input[name="inp[id][]"]:checked').map(function() {
        return $(this).val();
    }).get();
    var id = $('#id').val();

    if (selectedIds.length == 0) {
        alert('Please select at least one course to delete.');
    } else {
        $.ajax({
            url: url + '/delete',
            type: "POST",
            data: {
                ids: selectedIds,
                id: id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "JSON",
            success: function(data) { 
                dt_registered_user_list($("#kurikulum").val(), $("#prodi").val());
                dt_all_user_list($("#kurikulum").val(), $("#prodi").val());
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Error deleting data.');
            }
        });
    } 
}

function moveleft() { 
    var selectedIds = $('#all_user_list tbody input[name="inp[id][]"]:checked').map(function() {
        return $(this).val();
    }).get();
    var id = $('#id').val();

    if (selectedIds.length == 0) {
        alert('Please select at least one course to insert.');
    } else {
        $.ajax({
            url: url + '/insert',
            type: "POST",
            data: {
                ids: selectedIds,
                id: id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "JSON",
            success: function(data) { 
                dt_registered_user_list($("#kurikulum").val(), $("#prodi").val());
                dt_all_user_list($("#kurikulum").val(), $("#prodi").val());
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert('Error inserting data.');
            }
        });
    } 
}
</script>
@endsection