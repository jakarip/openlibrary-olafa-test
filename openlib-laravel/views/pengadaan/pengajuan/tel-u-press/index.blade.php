@extends('layouts/layoutMaster')

@section('title', 'Data Pengajuan TelU Press')

@section('vendor-style')
@endsection

@section('page-style')
<style>

.select2-container {
        z-index: 99999;
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

                        <div class="col-12 col-sm-6 col-lg-4">
                            <label for="prodi" class="form-label">Pilih Prodi:</label>
                            <select id="prodi" class="select2 form-select form-select-md" >
                                <option value="">Semua Prodi</option>
                                @foreach($prodi as $id => $name)
                                    <option value="{{ $id }}">
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-4">
                            <label for="status" class="form-label">Pilih Status:</label>
                            <select id="status" class="select2 form-select form-select-md" >
                                <option value="">Semua Jenis Status</option>
                                <option value="1">Pengajuan Naskah</option>
                                <option value="2">Review Naskah</option>
                                <option value="3">Editing & Proofread</option>
                                <option value="4">Layout</option>
                                <option value="5">ISBN</option>
                                <option value="6">Cetak</option>
                                <option value="7">Sudah Diterima</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-1 d-flex align-items-end col-auto">
                            <button id="search-btn" class="btn btn-primary">Cari</button>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>

    <hr class="mt-0">
    <div class="card-datatable " >
            <table class="table table-bordered table-striped dataTable no-footer nowrap" id="table">
                <thead>
                    <tr>
                        <th class="" rowspan="2">Action</th>
                        <th rowspan="2">Status</th>
                        <th rowspan="2">Nama User</th>
                        <th rowspan="2">Fakultas</th>
                        <th rowspan="2">Prodi</th>
                        <th rowspan="2">Judul Buku</th>
                        <th colspan="2">Pengajuan Naskah (Kelengkapan Administratif)<br> 3 Hari Kerja</th>
                        <th colspan="2">Review Naskah<br> 40 Hari Kerja</th>
                        <th colspan="2">Editing & Proofread<br> 20 Hari Kerja</th>
                        <th colspan="2">Layout<br> 20 Hari Kerja</th>
                        <th colspan="2">ISBN<br> 7 Hari Kerja</th>
                        <th colspan="2">Cetak<br> 10 Hari Kerja</th>
                        <th rowspan="2">Keterangan</th>
                        <th rowspan="2">Penerimaan Naskah</th>
                        <th rowspan="2">Total Biaya Produksi</th>
                        <th rowspan="2">Jumlah Hari Kerja Penerimaan Naskah - Cetak</th>
                    </tr>
                    <tr>
                        <th>Target</th>
                        <th>Realisasi</th>
                        <th>Target</th>
                        <th>Realisasi</th>
                        <th>Target</th>
                        <th>Realisasi</th>
                        <th>Target</th>
                        <th>Realisasi</th>
                        <th>Target</th>
                        <th>Realisasi</th>
                        <th>Target</th>
                        <th>Realisasi</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
    </div>


</div>


<div class="modal fade" id="frmbox" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i> Form Data Pengajuan Buku</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="frm" class="form-validate">
                @csrf
                <input type="hidden" id="hiddenInputField" name="inp[book_id_user]">
                <input type="hidden" id="id" name="id">

                <div class="modal-body">
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6>Pengajuan Buku</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group row mb-4">
                                <label for="select_prodi" class="col-md-3 col-form-label">Pilih Prodi</label>
                                <div class="col-md-9">
                                    <select id="select_prodi" name="inp[book_id_prodi]" class="select2 form-select form-select-md" required>
                                        @foreach($prodi as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row mb-4" id="typeaheadContainer">
                                <label for="TypeaheadBasic" class="col-md-3 col-form-label">Pemohon</label>
                                <div class="col-md-9">
                                    <input id="TypeaheadBasic" name="" class="form-control" type="text" autocomplete="off" placeholder="Cari Username..." required>
                                    <small class="text-secondary">Ketik Minimal 3 Huruf</small>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="title" class="col-md-3 col-form-label">Judul Buku</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[book_title]" id="book_title" required>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col col-form-label">Tanggal Realisasi Pengajuan Naskah:</label>
                                <div class="input-group input-daterange px-0" id="bs-datepicker-basic">
                                    <input type="text" name="inp[book_startdate_realization_step_1]" placeholder="Tanggal Awal" class="form-control">
                                    <span class="input-group-text">To</span>
                                    <input type="text" name="inp[book_enddate_realization_step_1]" placeholder="Tanggal Akhir" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col col-form-label">Tanggal Realisasi Review Naskah:</label>
                                <div class="input-group input-daterange px-0" id="bs-datepicker-basic2">
                                    <input type="text" name="inp[book_startdate_realization_step_2]" placeholder="Tanggal Awal" class="form-control">
                                    <span class="input-group-text">To</span>
                                    <input type="text" name="inp[book_enddate_realization_step_2]" placeholder="Tanggal Akhir" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col col-form-label">Tanggal Realisasi Editing & Proofread:</label>
                                <div class="input-group input-daterange px-0" id="bs-datepicker-basic3">
                                    <input type="text" name="inp[book_startdate_realization_step_3]" placeholder="Tanggal Awal" class="form-control">
                                    <span class="input-group-text">To</span>
                                    <input type="text" name="inp[book_enddate_realization_step_3]" placeholder="Tanggal Akhir" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col col-form-label">Tanggal Awal Realisasi Layout:</label>
                                <div class="input-group input-daterange px-0" id="bs-datepicker-basic4">
                                    <input type="text" name="inp[book_startdate_realization_step_4]" placeholder="Tanggal Awal" class="form-control">
                                    <span class="input-group-text">To</span>
                                    <input type="text" name="inp[book_enddate_realization_step_4]" placeholder="Tanggal Akhir" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col col-form-label">Tanggal Awal Realisasi ISBN:</label>
                                <div class="input-group input-daterange px-0" id="bs-datepicker-basic5">
                                    <input type="text" name="inp[book_startdate_realization_step_5]" placeholder="Tanggal Awal" class="form-control">
                                    <span class="input-group-text">To</span>
                                    <input type="text" name="inp[book_enddate_realization_step_5]" placeholder="Tanggal Akhir" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col col-form-label">Tanggal Awal Realisasi Cetak:</label>
                                <div class="input-group input-daterange px-0" id="bs-datepicker-basic6">
                                    <input type="text" name="inp[book_startdate_realization_step_6]" placeholder="Tanggal Awal" class="form-control">
                                    <span class="input-group-text">To</span>
                                    <input type="text" name="inp[book_enddate_realization_step_6]" placeholder="Tanggal Akhir" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <div class="col-6 input-daterange" id="bs-datepicker-basic7">
                                    <label for="book_received_date" class="col col-form-label">Tanggal Diterima:</label>
                                    <input type="text" name="inp[book_received_date]" id="book_received_date" class="form-control">
                                </div>
                                <div class="col-6">
                                    <label for="book_cost" class="col col-form-label">Total Biaya Produksi:</label>
                                    <input type="text" name="inp[book_cost]" id="book_cost" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="book_desc" class="col-md-3 col-form-label">Keterangan:</label>
                                <div class="col">
                                    <input type="text" name="inp[book_desc]" id="book_desc" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="save()">{{ __('common.save') }}</button>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="modal fade" id="frmbox_edit" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i> Form Edit Data Pengajuan Buku</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="frm_edit" class="form-validate">
                @csrf
                <input type="hidden" id="hiddenInputField" name="inp[book_id_user]">
                <input type="hidden" id="id" name="id">

                <div class="modal-body">
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6>Edit Pengajuan Buku</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group row mb-4">
                                <label for="book_id_prodi" class="col-md-3 col-form-label">Pilih Prodi</label>
                                <div class="col-md-9">
                                    <select id="book_id_prodi" name="inp[book_id_prodi]" class="select2 form-select form-select-md" required>
                                        @foreach($prodi as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row mb-4" id="typeaheadContainer">
                                <label for="username_edit" class="col-md-3 col-form-label">Pemohon</label>
                                <div class="col-md-9">
                                    <input id="username_edit" name="" class="form-control" type="text" autocomplete="off" placeholder="Cari Username..." disabled>
                                    <small class="text-secondary">Ketik Minimal 3 Huruf</small>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="book_title" class="col-md-3 col-form-label">Judul Buku</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[book_title]" id="book_title" required>
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label for="pus_name" class="col col-form-label">Tanggal Realisasi Pengajuan Naskah</label>
                                <div class="input-group input-daterange px-0" id="bs-datepicker-basic_edit">
                                    <input type="text" class="form-control" name="inp[book_startdate_realization_step_1]" id="book_startdate_realization_step_1" placeholder="Tanggal Awal" required>
                                    <span class="input-group-text">To</span>
                                    <input type="text" class="form-control" name="inp[book_enddate_realization_step_1]" id="book_enddate_realization_step_1" placeholder="Tanggal Akhir">
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label for="pus_name" class="col col-form-label">Tanggal Realisasi  Review Naskah</label>
                                <div class="input-group input-daterange px-0" id="bs-datepicker-basic_edit2">
                                    <input type="text" class="form-control" name="inp[book_startdate_realization_step_2]" id="book_startdate_realization_step_2" placeholder="Tanggal Awal">
                                    <span class="input-group-text">To</span>
                                    <input type="text" class="form-control" name="inp[book_enddate_realization_step_2]" id="book_enddate_realization_step_2" placeholder="Tanggal Akhir">
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="pus_name" class="col col-form-label">Tanggal Realisasi Editing & Proofread</label>
                                <div class="input-group input-daterange px-0 " id="bs-datepicker-basic_edit3">
                                    <input type="text" class="form-control" name="inp[book_startdate_realization_step_3]" id="book_startdate_realization_step_3" placeholder="Tanggal Awal">
                                    <span class="input-group-text">To</span>
                                    <input type="text" class="form-control" name="inp[book_enddate_realization_step_3]" id="book_enddate_realization_step_3" placeholder="Tanggal Akhir">
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="pus_name" class="col col-form-label">Tanggal Awal Realisasi Layout</label>
                                <div class="input-group input-daterange px-0 " id="bs-datepicker-basic_edit4">
                                    <input type="text" class="form-control" name="inp[book_startdate_realization_step_4]" id="book_startdate_realization_step_4" placeholder="Tanggal Awal">
                                    <span class="input-group-text">To</span>
                                    <input type="text" class="form-control" name="inp[book_enddate_realization_step_4]" id="book_enddate_realization_step_4" placeholder="Tanggal Akhir">
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="pus_name" class="col col-form-label">Tanggal Awal Realisasi ISBN</label>
                                <div class="input-group input-daterange px-0 " id="bs-datepicker-basic_edit5">
                                    <input type="text" class="form-control" name="inp[book_startdate_realization_step_5]" id="book_startdate_realization_step_5" placeholder="Tanggal Awal">
                                    <span class="input-group-text">To</span>
                                    <input type="text" class="form-control" name="inp[book_enddate_realization_step_5]" id="book_enddate_realization_step_5" placeholder="Tanggal Akhir">
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="pus_name" class="col col-form-label">Tanggal Awal Realisasi Cetak</label>
                                <div class="input-group input-daterange px-0 " id="bs-datepicker-basic_edit6">
                                    <input type="text" class="form-control" name="inp[book_startdate_realization_step_6]" id="book_startdate_realization_step_6" placeholder="Tanggal Awal">
                                    <span class="input-group-text">To</span>
                                    <input type="text" class="form-control" name="inp[book_enddate_realization_step_6]" id="book_enddate_realization_step_6" placeholder="Tanggal Akhir">
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="pus_name" class="col col-form-label">Tanggal Target Pengajuan Naskah<br></label>
                                <div class="input-group input-daterange px-0 " id="bs-datepicker-basic_target">
                                    <input type="text" class="form-control" name="inp[book_startdate_target_step_1]" id="book_startdate_target_step_1" placeholder="Tanggal Awal" >
                                    <span class="input-group-text">To</span>
                                    <input type="text" class="form-control" name="inp[book_enddate_target_step_1]" id="book_enddate_target_step_1" placeholder="Tanggal Akhir" >
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="pus_name" class="col col-form-label">Tanggal Target  Review Naskah<br></label>
                                <div class="input-group input-daterange px-0 " id="bs-datepicker-basic_target2">
                                    <input type="text" class="form-control" name="inp[book_startdate_target_step_2]" id="book_startdate_target_step_2" placeholder="Tanggal Awal" >
                                    <span class="input-group-text">To</span>
                                    <input type="text" class="form-control" name="inp[book_enddate_target_step_2]" id="book_enddate_target_step_2" placeholder="Tanggal Akhir" >
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="pus_name" class="col col-form-label">Tanggal Target Editing & Proofread<br></label>
                                <div class="input-group input-daterange px-0 " id="bs-datepicker-basic_target3">
                                    <input type="text" class="form-control" name="inp[book_startdate_target_step_3]" id="book_startdate_target_step_3" placeholder="Tanggal Awal" >
                                    <span class="input-group-text">To</span>
                                    <input type="text" class="form-control" name="inp[book_enddate_target_step_3]" id="book_enddate_target_step_3" placeholder="Tanggal Akhir" >
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="pus_name" class="col col-form-label">Tanggal Awal Target Layout <br></label>
                                <div class="input-group input-daterange px-0 " id="bs-datepicker-basic_target4">
                                    <input type="text" class="form-control" name="inp[book_startdate_target_step_4]" id="book_startdate_target_step_4" placeholder="Tanggal Awal" >
                                    <span class="input-group-text">To</span>
                                    <input type="text" class="form-control" name="inp[book_enddate_target_step_4]" id="book_enddate_target_step_4" placeholder="Tanggal Akhir" >
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="pus_name" class="col col-form-label">Tanggal Awal Target ISBN<br></label>
                                <div class="input-group input-daterange px-0 " id="bs-datepicker-basic_target5">
                                    <input type="text" class="form-control" name="inp[book_startdate_target_step_5]" id="book_startdate_target_step_5" placeholder="Tanggal Awal" >
                                    <span class="input-group-text">To</span>
                                    <input type="text" class="form-control" name="inp[book_enddate_target_step_5]" id="book_enddate_target_step_5" placeholder="Tanggal Akhir" >
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="pus_name" class="col col-form-label">Tanggal Awal Target Cetak<br></label>
                                <div class="input-group input-daterange px-0 " id="bs-datepicker-basic_target6">
                                    <input type="text" class="form-control" name="inp[book_startdate_target_step_6]" id="book_startdate_target_step_6" placeholder="Tanggal Awal" >
                                    <span class="input-group-text">To</span>
                                    <input type="text" class="form-control" name="inp[book_enddate_target_step_6]" id="book_enddate_target_step_6" placeholder="Tanggal Akhir" >
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <div class="col-6 input-daterange" id="bs-datepicker-basic_edit7">
                                    <label for="pus_name" class="col col-form-label">Tanggal Diterima</label>
                                    <input type="text" class="form-control" name="inp[book_received_date]" id="book_received_date">
                                </div>
                                <div class="col-6">
                                    <label for="pus_name" class="col col-form-label">Total Biaya Produksi</label>
                                    <input type="text" class="form-control" name="inp[book_cost]" id="book_cost" >
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="pus_name" class="col col-form-label">Keterangan</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="inp[book_desc]" id="book_desc" >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="update($('#frm_edit #id').val())">{{ __('common.save') }}</button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection

@section('vendor-script')
@endsection

@section('page-script')
<script>

let dTable = null;
let dataTableData = [];
let url = '{{ url('pengadaan/tel-u-press') }}';

$(function(){
    dTable = $('#table').DataTable({
        ajax: {
            url: url+'/dt',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.prodi = $('#prodi').val();
                d.status = $('#status').val();
            },
            dataSrc: function(json) {
                dataTableData = json.data;
                return json.data;
            }
        },
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false },
            { data: 'book_status', name: 'book_status', orderable: true, searchable: true },
            { data: 'master_data_fullname', name: 'master_data_fullname', orderable: true, searchable: true },
            { data: 'NAMA_FAKULTAS', name: 'NAMA_FAKULTAS', orderable: true, searchable: true },
            { data: 'NAMA_PRODI', name: 'NAMA_PRODI', orderable: true, searchable: true },
            { data: 'book_title', name: 'book_title', orderable: true, searchable: true },
            {
                data: null,
                name: 'book_startdate_target_step_1',
                orderable: true,
                searchable: true,
                render: function(data) {
                    return formatDate(data.book_startdate_target_step_1) + ' s/d ' + formatDate(data.book_enddate_target_step_1);
                }
            },
            {
                data: null,
                name: 'book_startdate_realization_step_1',
                orderable: true,
                searchable: true,
                render: function(data) {
                    if (data.proses_step1 < 0) {
                        return `<span style="color:red;">${formatDate(data.book_startdate_realization_step_1)} s/d ${formatDate(data.book_enddate_realization_step_1)}</span>`;
                    } else if (data.book_startdate_realization_step_1 && data.book_enddate_realization_step_1) {
                        return `<span style="color:green;">${formatDate(data.book_startdate_realization_step_1)} s/d ${formatDate(data.book_enddate_realization_step_1)}</span>`;
                    } else {
                        return `${data.book_startdate_realization_step_1 ? formatDate(data.book_startdate_realization_step_1) : ''} s/d ${data.book_enddate_realization_step_1 ? formatDate(data.book_enddate_realization_step_1) : ''}`;
                    }
                }
            },
            {
                data: null,
                name: 'book_startdate_target_step_2',
                orderable: true,
                searchable: true,
                render: function(data) {
                    return formatDate(data.book_startdate_target_step_2) + ' s/d ' + formatDate(data.book_enddate_target_step_2);
                }
            },
            {
                data: null,
                name: 'book_startdate_realization_step_2',
                orderable: true,
                searchable: true,
                render: function(data) {
                    if (data.proses_step2 < 0) {
                        return `<span style="color:red;">${formatDate(data.book_startdate_realization_step_2)} s/d ${formatDate(data.book_enddate_realization_step_2)}</span>`;
                    } else if (data.book_startdate_realization_step_2 && data.book_enddate_realization_step_2) {
                        return `<span style="color:green;">${formatDate(data.book_startdate_realization_step_2)} s/d ${formatDate(data.book_enddate_realization_step_2)}</span>`;
                    } else {
                        return `${data.book_startdate_realization_step_2 ? formatDate(data.book_startdate_realization_step_2) : ''} s/d ${data.book_enddate_realization_step_2 ? formatDate(data.book_enddate_realization_step_2) : ''}`;
                    }
                }
            },
            {
                data: null,
                name: 'book_startdate_target_step_3',
                orderable: true,
                searchable: true,
                render: function(data) {
                    return formatDate(data.book_startdate_target_step_3) + ' s/d ' + formatDate(data.book_enddate_target_step_3);
                }
            },
            {
                data: null,
                name: 'book_startdate_realization_step_3',
                orderable: true,
                searchable: true,
                render: function(data) {
                    if (data.proses_step3 < 0) {
                        return `<span style="color:red;">${formatDate(data.book_startdate_realization_step_3)} s/d ${formatDate(data.book_enddate_realization_step_3)}</span>`;
                    } else if (data.book_startdate_realization_step_3 && data.book_enddate_realization_step_3) {
                        return `<span style="color:green;">${formatDate(data.book_startdate_realization_step_3)} s/d ${formatDate(data.book_enddate_realization_step_3)}</span>`;
                    } else {
                        return `${data.book_startdate_realization_step_3 ? formatDate(data.book_startdate_realization_step_3) : ''} s/d ${data.book_enddate_realization_step_3 ? formatDate(data.book_enddate_realization_step_3) : ''}`;
                    }
                }
            },
            {
                data: null,
                name: 'book_startdate_target_step_4',
                orderable: true,
                searchable: true,
                render: function(data) {
                    return formatDate(data.book_startdate_target_step_4) + ' s/d ' + formatDate(data.book_enddate_target_step_4);
                }
            },
            {
                data: null,
                name: 'book_startdate_realization_step_4',
                orderable: true,
                searchable: true,
                render: function(data) {
                    if (data.proses_step4 < 0) {
                        return `<span style="color:red;">${formatDate(data.book_startdate_realization_step_4)} s/d ${formatDate(data.book_enddate_realization_step_4)}</span>`;
                    } else if (data.book_startdate_realization_step_4 && data.book_enddate_realization_step_4) {
                        return `<span style="color:green;">${formatDate(data.book_startdate_realization_step_4)} s/d ${formatDate(data.book_enddate_realization_step_4)}</span>`;
                    } else {
                        return `${data.book_startdate_realization_step_4 ? formatDate(data.book_startdate_realization_step_4) : ''} s/d ${data.book_enddate_realization_step_4 ? formatDate(data.book_enddate_realization_step_4) : ''}`;
                    }
                }
            },
            {
                data: null,
                name: 'book_startdate_target_step_5',
                orderable: true,
                searchable: true,
                render: function(data) {
                    return formatDate(data.book_startdate_target_step_5) + ' s/d ' + formatDate(data.book_enddate_target_step_5);
                }
            },
            {
                data: null,
                name: 'book_startdate_realization_step_5',
                orderable: true,
                searchable: true,
                render: function(data) {
                    if (data.proses_step5 < 0) {
                        return `<span style="color:red;">${formatDate(data.book_startdate_realization_step_5)} s/d ${formatDate(data.book_enddate_realization_step_5)}</span>`;
                    } else if (data.book_startdate_realization_step_5 && data.book_enddate_realization_step_5) {
                        return `<span style="color:green;">${formatDate(data.book_startdate_realization_step_5)} s/d ${formatDate(data.book_enddate_realization_step_5)}</span>`;
                    } else {
                        return `${data.book_startdate_realization_step_5 ? formatDate(data.book_startdate_realization_step_5) : ''} s/d ${data.book_enddate_realization_step_5 ? formatDate(data.book_enddate_realization_step_5) : ''}`;
                    }
                }
            },
            {
                data: null,
                name: 'book_startdate_target_step_6',
                orderable: true,
                searchable: true,
                render: function(data) {
                    return formatDate(data.book_startdate_target_step_6) + ' s/d ' + formatDate(data.book_enddate_target_step_6);
                }
            },
            {
                data: null,
                name: 'book_startdate_realization_step_6',
                orderable: true,
                searchable: true,
                render: function(data) {
                    if (data.proses_step6 < 0) {
                        return `<span style="color:red;">${formatDate(data.book_startdate_realization_step_6)} s/d ${formatDate(data.book_enddate_realization_step_6)}</span>`;
                    } else if (data.book_startdate_realization_step_6 && data.book_enddate_realization_step_6) {
                        return `<span style="color:green;">${formatDate(data.book_startdate_realization_step_6)} s/d ${formatDate(data.book_enddate_realization_step_6)}</span>`;
                    } else {
                        return `${data.book_startdate_realization_step_6 ? formatDate(data.book_startdate_realization_step_6) : ''} s/d ${data.book_enddate_realization_step_6 ? formatDate(data.book_enddate_realization_step_6) : ''}`;
                    }
                }
            },
            { data: 'book_desc', name: 'book_desc', orderable: true, searchable: true },
            {
                data: 'book_received_date',
                name: 'book_received_date',
                orderable: true,
                searchable: true,
                render: function(data) {
                    return data ? formatDate(data) : '-';
                }
            },
            {
                data: 'book_cost',
                name: 'book_cost',
                orderable: true,
                searchable: true,
                render: function(data) {
                    return data ? `Rp ${parseInt(data).toLocaleString('id-ID')}` : 'Rp 0';
                }
            },
            { data: 'total_proses_naskah_cetak', name: 'total_proses_naskah_cetak', orderable: true, searchable: true }
        ],
        responsive: false,
        scrollX: true,

    });

    $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> Tambah Data </button>`)

    $('#bs-datepicker-basic').datepicker({
        todayHighlight: true,
        orientation: 'bottom',
        format: 'dd/mm/yyyy',
        },
    );
    $('#bs-datepicker-basic2').datepicker({
        todayHighlight: true,
        orientation: 'bottom',
        format: 'dd/mm/yyyy',
        },
    );
    $('#bs-datepicker-basic3').datepicker({
        todayHighlight: true,
        orientation: 'bottom',
        format: 'dd/mm/yyyy',
        },
    );
    $('#bs-datepicker-basic4').datepicker({
        todayHighlight: true,
        orientation: 'bottom',
        format: 'dd/mm/yyyy',
        },
    );
    $('#bs-datepicker-basic5').datepicker({
        todayHighlight: true,
        orientation: 'bottom',
        format: 'dd/mm/yyyy',
        },
    );
    $('#bs-datepicker-basic6').datepicker({
        todayHighlight: true,
        orientation: 'bottom',
        format: 'dd/mm/yyyy',
        },
    );
    $('#bs-datepicker-basic7').datepicker({
        todayHighlight: true,
        orientation: 'top',
        format: 'dd/mm/yyyy',
        },
    );

    $('#bs-datepicker-basic_edit').datepicker({
                todayHighlight: true,
                orientation: 'bottom',
                format: 'dd/mm/yyyy',
                },
            );
    $('#bs-datepicker-basic_edit2').datepicker({
        todayHighlight: true,
        orientation: 'bottom',
        format: 'dd/mm/yyyy',
        },
    );
    $('#bs-datepicker-basic_edit3').datepicker({
        todayHighlight: true,
        orientation: 'bottom',
        format: 'dd/mm/yyyy',
        },
    );
    $('#bs-datepicker-basic_edit4').datepicker({
        todayHighlight: true,
        orientation: 'bottom',
        format: 'dd/mm/yyyy',
        },
    );
    $('#bs-datepicker-basic_edit5').datepicker({
        todayHighlight: true,
        orientation: 'bottom',
        format: 'dd/mm/yyyy',
        },
    );
    $('#bs-datepicker-basic_edit6').datepicker({
        todayHighlight: true,
        orientation: 'bottom',
        format: 'dd/mm/yyyy',
        },
    );
    $('#bs-datepicker-basic_edit7').datepicker({
        todayHighlight: true,
        orientation: 'top',
        format: 'dd/mm/yyyy',
        },
    );

    $('#bs-datepicker-basic_target').datepicker({
        todayHighlight: true,
        orientation: 'bottom',
        format: 'dd/mm/yyyy',
        },
    );
    $('#bs-datepicker-basic_target2').datepicker({
        todayHighlight: true,
        orientation: 'bottom',
        format: 'dd/mm/yyyy',
        },
    );
    $('#bs-datepicker-basic_target3').datepicker({
        todayHighlight: true,
        orientation: 'bottom',
        format: 'dd/mm/yyyy',

        },
    );
    $('#bs-datepicker-basic_target4').datepicker({
        todayHighlight: true,
        orientation: 'bottom',
        format: 'dd/mm/yyyy',
        },
    );
    $('#bs-datepicker-basic_target5').datepicker({
        todayHighlight: true,
        orientation: 'bottom',
        format: 'dd/mm/yyyy',
        },
    );
    $('#bs-datepicker-basic_target6').datepicker({
                todayHighlight: true,
                orientation: 'bottom',
                format: 'dd/mm/yyyy',
                },
            );

});


$(document).ready(function() {

// function sendAjaxRequest() {
//     $.ajax({
//         url: url + '/dt',
//         type: 'post',
//         headers: {
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//         },
//         data: {
//             // startDate: startDate ? startDate.format('YYYY-MM-DD') : '',
//             // endDate: endDate ? endDate.format('YYYY-MM-DD') : '',
//             // location: $('#lokasi').val()
//         },
//         success: function(response) {

//         },
//         error: function(xhr) {
//             // Handle the error here
//             // console.error(xhr.responseText);
//         }
//     });
// }


    $('#search-btn').on('click', function(event) {
        event.preventDefault();
        // sendAjaxRequest();
        dTable.ajax.reload();
    });

    let ajaxRequest;

    $('#TypeaheadBasic').typeahead(
            {
                hint: false,
                highlight: true,
                minLength: 3,

            },
            {
                name: 'members',
                source: function(query, syncResults, asyncResults) {
                // Synchronous suggestions (if any)
                syncResults([]);

                // Clear the previous timeout if it exists
                if (ajaxRequest) {
                    clearTimeout(ajaxRequest);
                }

                // Set a new timeout
                ajaxRequest = setTimeout(function() {
                        // Asynchronous suggestions
                        $.ajax({
                            url: url + '/autodata',
                            type: 'POST',
                            data: { q: query },
                            dataType: 'json',
                            success: function(data) {
                                asyncResults($.map(data, function(item) {
                                    return item;
                                }));
                            },
                        });
                    }, 500);
                },
                display: function(item) {
                    return item.name;
                },
            }
    );

    $('#TypeaheadBasic').bind('typeahead:select', function(ev, suggestion) {
            $('#hiddenInputField').val(suggestion.id);
    });

});

function add() {
    _reset();
    $('#frmbox').modal('show');
}

function save()
{
    if($("#frm").valid())
    {
        let formData = new FormData($('#frm')[0]);

        $.ajax({
            url: url+'/save',
            type: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if(data.status === 'success') {
                    $('#frmbox').modal('hide'); // Tutup modal jika berhasil menyimpan
                    dTable.draw();
                    toastr.success("{{ __('common.message_save_title') }}", "{{ __('common.message_success_save') }}", toastrOptions);
                } else if (data.status === 'error') {
                    // Menampilkan alert ketika ada error
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });

                    // Menutup modal walaupun ada error
                    $('#frmbox').modal('hide');
                }
            },
            error: function(xhr, status, error) {
                // Menangani error pada saat request
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat menyimpan data.'
                });

                // Menutup modal walaupun ada error
                $('#frmbox').modal('hide');
            }
        });
    } else {
        // Jika form tidak valid, tetap tutup modal
        // $('#frmbox').modal('hide');
    }
}

function edit(id) {
    _reset();
    let rowData = dataTableData.find(row => row.book_id == id);
    $.ajax({
        url: url + '/get/' + id,
        type: 'get',
        dataType: 'json',
        success: function(e) {
            _reset();

            $.each(e, function(key, value) {
                $('#frmbox_edit #'+key).val(value);
            });

            $('#frmbox_edit #id').val(id);
            // $('#frmbox_edit #hiddenInputField').html('<option selected value="'+e.book_id_user+'">('+e.master_data_number+') - '+e.master_data_fullname+'</option>');
            $('#frmbox_edit #hiddenInputField').val(e.book_id_user).trigger('change');
            $('#frmbox_edit #book_id_prodi').val(e.book_id_prodi).trigger('change');
            $('#frmbox_edit #username_edit').val(rowData.master_data_fullname);


            // Initialize datepickers and set their values
            // $('#bs-datepicker-basic_edit').datepicker('setDate', e.book_startdate_realization_step_1);
            // $('#bs-datepicker-basic_edit2').datepicker('setDate', e.book_startdate_realization_step_2);
            // $('#bs-datepicker-basic_edit3').datepicker('setDate', e.book_startdate_realization_step_3);
            // $('#bs-datepicker-basic_edit4').datepicker('setDate', e.book_startdate_realization_step_4);
            // $('#bs-datepicker-basic_edit5').datepicker('setDate', e.book_startdate_realization_step_5);
            // $('#bs-datepicker-basic_edit6').datepicker('setDate', e.book_startdate_realization_step_6);
            // $('#bs-datepicker-basic_edit7').datepicker('setDate', e.book_received_date);

            // $('#bs-datepicker-basic_target').datepicker('setDate', e.book_startdate_target_step_1);
            // $('#bs-datepicker-basic_target2').datepicker('setDate', e.book_startdate_target_step_2);
            // $('#bs-datepicker-basic_target3').datepicker('setDate', e.book_startdate_target_step_3);
            // $('#bs-datepicker-basic_target4').datepicker('setDate', e.book_startdate_target_step_4);
            // $('#bs-datepicker-basic_target5').datepicker('setDate', e.book_startdate_target_step_5);
            // $('#bs-datepicker-basic_target6').datepicker('setDate', e.book_startdate_target_step_6);


            $("#frmbox_edit").modal('show');
        }
    });
}

function update(book_id) {

    if($("#frm_edit").valid())
		{
			$.ajax({
				url: url + '/update/' + book_id,
				global:false,
				async:true,
				type:'post',
				dataType:'json',
				data: $('#frm_edit').serialize(),
				success : function(data) {
                    if(data.status === 'success') {
                        $('#frmbox_edit').modal('hide'); // Tutup modal jika berhasil menyimpan
                        dTable.draw();
                        toastr.success("{{ __('common.message_save_title') }}", "{{ __('common.message_success_save') }}", toastrOptions);
                    } else if (data.status === 'error') {
                        // Menampilkan alert ketika ada error
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                        });

                        // Menutup modal walaupun ada error
                        $('#frmbox').modal('hide');
                    }
				},
			});
		}

}

function del(id)
{
    yswal_delete.fire({
        title: "{{ __('common.message_delete_prompt_title') }}",
        text: "{{ __('common.message_delete_prompt_text') }}"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url+'/delete',
                data: { id : id },
                type: 'delete',
                dataType: 'json',
                success: function(e) {
                    if (e.status == 'success') {
                        dTable.draw();
                        toastr.success("{{ __('common.message_delete_title') }}", "{{ __('common.message_success_delete') }}", toastrOptions)
                    }
                }
            });
        }
    })
}

function _reset() {
    // Reset all form inputs
    $('#frm, #frm_edit').trigger('reset');

    // Reset all datepickers
    $('.datepicker').each(function() {
        $(this).datepicker('setDate', null);
    });

    // Clear select2 fields
    $('.select2').val(null).trigger('change');
}

function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}-${month}-${year}`;
}

</script>
@endsection
