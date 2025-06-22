@extends('layouts/layoutMaster')

@section('title', 'Data Pengajuan Buku')

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
    <button class="btn btn-openlib-red mx-4 mt-4 col-2" type="button" data-bs-toggle="collapse" data-bs-target=".multi-collapse" aria-expanded="false" aria-controls="multiCollapse1 multiCollapse2 multiCollapse3 multiCollapse4 multiCollapse2 multiCollapse5">
        Advanced Search
    </button>
    {{-- <h5 class="card-header col-4">Advanced Search</h5> --}}
    <!--Search Form -->
    <div class="card-body">
        <form class="dt_adv_search">
            <div class="row">
                <div class="col-12">
                    <div class="row g-3">

                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="fakultas" class="form-label">Pilih Fakultas:</label>
                            <select id="fakultas" class="select2 form-select form-select-md">
                                <option value="">Semua Fakultas</option>
                                @foreach($faculty as $item)
                                    <option value="{{ $item->c_kode_fakultas }}">
                                        {{ $item->nama_fakultas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="prodi" class="form-label">Pilih Prodi:</label>
                            <select id="prodi" class="select2 form-select form-select-md">
                                <option value="">Semua</option>
                                {{-- @foreach($prodi as $item)
                                    <option value="{{ $item->C_KODE_PRODI }}" data-fakultas="{{ $item->C_KODE_FAKULTAS }}">
                                        {{ $item->NAMA_PRODI }}
                                    </option>
                                @endforeach --}}
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="status" class="form-label">Pilih Status:</label>
                            <select id="status" class="select2 form-select form-select-md" >
                                <option value="">Semua Status</option>
                                <option value="pengajuan">Pengajuan dari Prodi</option>
                                <option value="logistik">Pengajuan ke Logistik</option>
                                <option value="penerimaan">Penerimaan Buku</option>
                                <option value="r_ketersediaan">Ketersediaan buku</option>
                                <option value="s_email">Konfirmasi Email</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="jenisbuku" class="form-label">Pilih Jenis Buku:</label>
                            <select id="jenisbuku" class="select2 form-select form-select-md">
                                <option value="">Semua Jenis Buku</option>
                                <option value="cetak">Buku Cetak</option>
                                <option value="ebook">E-Book</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3 collapse multi-collapse" id="multiCollapse1">
                            <label for="datePengajuan" class="form-label">Pilih Tanggal Terima Pengajuan:</label>
                            <select id="datePengajuan" class="select2 form-select form-select-md">
                                <option value="all">Semua Tanggal Terima Pengajuan</option>
                                <option value="date">Range Tanggal Terima Pengajuan</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3 d-none collapse multi-collapse" id="datePengajuanDiv" >
                            <label for="datepickerPengajuan" class="form-label">Pilih Range Tanggal Terima Pengajuan:</label>
                            <input type="text" id="datepickerPengajuan" class="form-control"/>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3 collapse multi-collapse" id="multiCollapse2">
                            <label for="dateLogistik" class="form-label">Pilih Tanggal Pengajuan Logistik:</label>
                            <select id="dateLogistik" class="select2 form-select form-select-md">
                                <option value="all">Semua Tanggal Pengajuan Logistik</option>
                                <option value="date">Range Tanggal Pengajuan Logistik</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3 d-none collapse multi-collapse" id="dateLogistikDiv" >
                            <label for="datepickerLogistik" class="form-label">Pilih Range Tanggal Pengajuan Logistik:</label>
                            <input type="text" id="datepickerLogistik" class="form-control"/>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3 collapse multi-collapse" id="multiCollapse3">
                            <label for="dateBuku" class="form-label">Pilih Tanggal  Penerimaan Buku:</label>
                            <select id="dateBuku" class="select2 form-select form-select-md">
                                <option value="all">Semua Tanggal Penerimaan Buku</option>
                                <option value="date">Range Tanggal Penerimaan Buku</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3 d-none collapse multi-collapse" id="dateBukuDiv" >
                            <label for="datepickerBuku" class="form-label">Pilih Range Tanggal Penerimaan Buku:</label>
                            <input type="text" id="datepickerBuku" class="form-control"/>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3 collapse multi-collapse" id="multiCollapse4">
                            <label for="dateEmail" class="form-label">Pilih Tanggal Konfirmasi Email:</label>
                            <select id="dateEmail" class="select2 form-select form-select-md">
                                <option value="all">Semua Tanggal Konfirmasi Email</option>
                                <option value="date">Range Tanggal Konfirmasi Email</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3 d-none collapse multi-collapse" id="dateEmailDiv" >
                            <label for="datepickerEmail" class="form-label">Pilih Range Tanggal Konfirmasi Email:</label>
                            <input type="text" id="datepickerEmail" class="form-control"/>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3 collapse multi-collapse" id="multiCollapse5">
                            <label for="dateTersedia" class="form-label">Pilih Tanggal Ketersediaan Buku:</label>
                            <select id="dateTersedia" class="select2 form-select form-select-md">
                                <option value="all">Semua Tanggal Ketersediaan Buku</option>
                                <option value="date">Range Tanggal Ketersediaan Buku</option>
                            </select>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3 d-none collapse multi-collapse" id="dateTersediaDiv" >
                            <label for="datepickerTersedia" class="form-label">Pilih Range Tanggal Ketersediaan Buku:</label>
                            <input type="text" id="datepickerTersedia" class="form-control"/>
                        </div>


                        <div class="col-12 col-sm-6 col-lg-1 d-flex align-items-end col-auto">
                            <button id="show-date-btn" class="btn btn-openlib-red">Cari</button>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>

    <hr class="mt-0">

    <div class="card-datatable text-wrap" >
            <table class="table table-bordered table-striped no-footer dataTable dt-select-no-highlight nowrap" id="table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Action</th>
                        <th>Status</th>
                        <th>Nama Prodi</th>
                        <th>Pemohon</th>
                        <th>Mata Kuliah</th>
                                        <th>Jenis Buku</th>
                                        <th>Judul Buku</th>
                        <th>Pengarang</th>
                        <th>Penerbit</th>
                                        <th>Tahun Terbit</th>
                                        <th>Tanggal Terima Pengajuan dari Prodi</th>
                                        <th>Tanggal Pengajuan ke Logistik</th>
                                        <th>Nomor E-Memo Pengajuan ke Logistik</th>
                                        <th class="nosort">Waktu Proses Pengajuan</th>
                                        <th>Tanggal Proses Logistik</th>
                                        <th>Tanggal Penerimaan Buku</th>
                                        <th class="nosort">Waktu Proses Pengadaan</th>
                                        <th>Harga Pengadaan</th>
                                        <th>Jumlah Harga</th>
                                        <th>Jumlah Buku</th>
                                        <th class="nosort">Waktu Proses Ketersediaan Buku</th>
                                        <th>Tanggal Ketersediaan Buku</th>
                                        <th class="nosort">Waktu Proses Konfirmasi Email</th>
                                        <th>Tanggal Konfirmasi Email</th>
                                        <th>No. Katalog</th>
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
                {{-- <input type="hidden" id="hiddenInputField" name="inp[book_id]"> --}}
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
                                    <select id="select_prodi" name="inp[book_id_prodi]" class="select2 form-select form-select-md" >
                                        @foreach($prodi as $item)
                                            <option value="{{ $item->C_KODE_PRODI }}" data-fakultas="{{ $item->C_KODE_FAKULTAS }}">
                                                {{ $item->NAMA_PRODI }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="book_member" class="col-md-3 col-form-label">Pemohon</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[book_member]" id="book_member" >
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="book_subject" class="col-md-3 col-form-label">Mata Kuliah</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[book_subject]" id="book_subject" >
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="book_title" class="col-md-3 col-form-label">Judul Buku</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[book_title]" id="book_title" >
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="book_author" class="col-md-3 col-form-label">Pengarang</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[book_author]" id="book_author" >
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="book_publisher" class="col-md-3 col-form-label">Penerbit</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[book_publisher]" id="book_publisher" >
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="book_published_year" class="col-md-3 col-form-label">Tahun Terbit</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[book_published_year]" id="book_published_year" >
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="book_date_prodi_submission" class="col-md-3 col-form-label">Tanggal Terima Pengajuan Prodi</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="inp[book_date_prodi_submission]" id="bs-datepicker-basic" >
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

<div class="modal fade" id="frmbox_logistic" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i> Form Data Pengajuan ke Logistik</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="frm_logistic" class="form-validate">
                @csrf
                {{-- <input type="hidden" id="hiddenInputField" name="inp[book_id]"> --}}
                <input type="hidden" id="id" name="id">

                <div class="modal-body">
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6>Pengajuan ke Logistik</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <label for="list_buku" class="col-md-3 col-form-label">List Buku</label>
                                <ol id="list_buku" class="list-group list-group-numbered">
                                    {{-- <li class="list-group-item">Bear claw cake biscuit</li>
                                    <li class="list-group-item">Soufflé pastry pie ice</li>
                                    <li class="list-group-item">Tart tiramisu cake</li>
                                    <li class="list-group-item">Bonbon toffee muffin</li>
                                    <li class="list-group-item">Dragée tootsie roll</li> --}}
                                </ol>
                            </div>

                            <div class="form-group row mb-4">
                                <label for="book_memo_logistic_number" class="col-md-3 col-form-label">Nomor E-Memo Pengajuan ke Logistik</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[book_memo_logistic_number]" id="book_memo_logistic_number" >
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label for="book_date_logistic_submission" class="col-md-3 col-form-label">Tanggal Pengajuan ke Logistik</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control bs-datepicker" name="inp[book_date_logistic_submission]" id="book_date_logistic_submission" >
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="save_logistic()">{{ __('common.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="frmbox_logistic_update" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i> Form Data Pengajuan ke Logistik</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="frm_logistic_update" class="form-validate">
                @csrf
                {{-- <input type="hidden" id="hiddenInputField" name="inp[book_id]"> --}}
                <input type="hidden" id="id" name="id">

                <div class="modal-body">
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6>Pengajuan ke Logistik</h6>
                        </div>
                        <div class="card-body">

                            <div class="form-group row mb-4">
                                <label for="book_title" class="col-md-3 col-form-label">Nama Buku</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[book_title]" id="book_title" >
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label for="book_memo_logistic_number" class="col-md-3 col-form-label">Nomor E-Memo Pengajuan ke Logistik</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[book_memo_logistic_number]" id="book_memo_logistic_number" >
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label for="book_date_logistic_submission" class="col-md-3 col-form-label">Tanggal Pengajuan ke Logistik</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control bs-datepicker" name="inp[book_date_logistic_submission]" id="book_date_logistic_submission" >
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="update_logistic()">{{ __('common.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="frmbox_penerimaan" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i> Form Data Penerimaan Buku</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="frm_penerimaan" class="form-validate">
                @csrf
                {{-- <input type="hidden" id="hiddenInputField" name="inp[book_id]"> --}}
                <input type="hidden" id="id" name="id">

                <div class="modal-body">
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6>Penerimaan Buku</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group row mb-4">
                                <label for="book_title" class="col-md-3 col-form-label">Nama Buku</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[book_title]" id="book_title" >
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label for="book_type" class="col-md-3 col-form-label">Jenis Buku</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="inp[book_type]" id="book_type" >
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label for="book_date_acceptance" class="col-md-3 col-form-label">Tanggal Penerimaan Buku</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control bs-datepicker" name="inp[book_date_acceptance]" id="book_date_acceptance" >
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label for="book_procurement_price" class="col-md-3 col-form-label">Harga Pengadaan</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="inp[book_procurement_price]" id="book_procurement_price" >
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label for="book_copy" class="col-md-3 col-form-label">Jumlah Buku</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="inp[book_copy]" id="book_copy" >
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="save_penerimaan()">{{ __('common.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="frmbox_ketersedian" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i> Form Data Ketersediaan Buku</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="frm_ketersedian" class="form-validate">
                @csrf
                {{-- <input type="hidden" id="hiddenInputField" name="inp[book_id]"> --}}
                <input type="hidden" id="id" name="id">

                <div class="modal-body">
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6>Ketersediaan Buku</h6>
                        </div>
                        <div class="card-body">

                            <div class="form-group row mb-4">
                                <label for="book_title" class="col-md-3 col-form-label">Nama Buku</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[book_title]" id="book_title" >
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label for="book_date_available" class="col-md-3 col-form-label">Tanggal Ketersediaan Buku</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control bs-datepicker" name="inp[book_date_available]" id="book_date_available" >
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label for="book_catalog_number" class="col-md-3 col-form-label">No. Katalog</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="inp[book_catalog_number]" id="book_catalog_number" >
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="save_ketersediaan()">{{ __('common.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="frmbox_email" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i> Form Data Konfirmasi Email</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="frm_email" class="form-validate">
                @csrf
                {{-- <input type="hidden" id="hiddenInputField" name="inp[book_id]"> --}}
                <input type="hidden" id="id" name="id">

                <div class="modal-body">
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6>Konfirmasi Email</h6>
                        </div>
                        <div class="card-body">

                            <div class="form-group row mb-4">
                                <label for="book_title" class="col-md-3 col-form-label">Nama Buku</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="inp[book_title]" id="book_title" >
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label for="book_date_email_confirmed" class="col-md-3 col-form-label">Tanggal Konfirmasi Email</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control bs-datepicker" name="inp[book_date_email_confirmed]" id="book_date_email_confirmed" >
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="save_email()">{{ __('common.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="frmbox_upload" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i> Form Upload Template Pengajuan</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="frm_upload" class="form-validate" enctype="multipart/form-data">
                @csrf
                {{-- <input type="hidden" id="hiddenInputField" name="inp[book_id]"> --}}
                <input type="hidden" id="id" name="id">

                <div class="modal-body">
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6>Upload Template Pengajuan</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group row mb-4">
                                <label for="formFile" class="col-md-3 col-form-label">Upload File </label>
                                <div class="col-md-9">
                                    <input class="form-control" name="formFile" type="file" id="formFile">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="save_upload()">{{ __('common.save') }}</button>
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
let url = '{{ url('pengadaan/buku') }}';
let status = '{{ isset($status) ? $status : "" }}';
let dateRange = '{{ isset($date_range) ? $date_range : "" }}';
let getFakultas = '{{ isset($getFakultas) ? $getFakultas : "" }}';
let getProdi = '{{ isset($getProdi) ? $getProdi : "" }}';

var selectedIds = [];
var startDate ;
var endDate ;

function updateStatus() {
    status = $('#status').val(); // Update status with the current value
}
function updateFakultas() {
    getFakultas = $('#fakultas').val(); 
}
function updateProdi() {
    getProdi = $('#prodi').val(); 
}

$(function(){

    dTable = $('#table').DataTable({
        pageLength: 25,
        ajax: {
            url: url+'/dt',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.prodi = getProdi ? getProdi : $('#prodi').val();
                d.status =   status ? status : $('#status').val(); 
                d.type = $('#jenisbuku').val();
                d.faculty = getFakultas ? getFakultas : $('#fakultas').val();
                d.dates_submission = dateRange ? dateRange : $('#datepickerPengajuan').val();
                d.dates_logistic = $('#datepickerLogistik').val();
                d.dates_acceptance = $('#datepickerBuku').val();
                d.dates_email_confirmed = $('#datepickerEmail').val();
                d.dates_available = $('#datepickerTersedia').val();
                d.dates_submission_option = dateRange ? 'date' : $('#datePengajuan').val();
                d.dates_logistic_option = $('#dateLogistik').val();
                d.dates_acceptance_option = $('#dateBuku').val();
                d.dates_email_confirmed_option = $('#dateEmail').val();
                d.dates_available_option = $('#dateTersedia').val();
            },
        },
        columns: [
            { data: 'book_status', name: 'book_status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false },
            { data: 'book_status_view', name: 'book_status_view', orderable: true, searchable: true },
            { data: 'NAMA_PRODI', name: 'NAMA_PRODI', orderable: true, searchable: true },
            { data: 'book_member', name: 'book_member', orderable: true, searchable: true },
            { data: 'book_subject', name: 'book_subject', orderable: true, searchable: true },
            { data: 'book_type', name: 'book_type', orderable: true, searchable: true },
            { data: 'book_title', name: 'book_title', orderable: true, searchable: true },
            { data: 'book_author', name: 'book_author', orderable: true, searchable: true },
            { data: 'book_publisher', name: 'book_publisher', orderable: true, searchable: true },
            { data: 'book_published_year', name: 'book_published_year', orderable: true, searchable: true },
            { data: 'book_date_prodi_submission', name: 'book_date_prodi_submission', orderable: true, searchable: true },
            { data: 'book_date_logistic_submission', name: 'book_date_logistic_submission', orderable: true, searchable: true },
            { data: 'book_memo_logistic_number', name: 'book_memo_logistic_number', orderable: true, searchable: true },
            { data: 'proses_pengajuan', name: 'proses_pengajuan', orderable: true, searchable: true },
            { data: 'book_date_logistic_process', name: 'book_date_logistic_process', orderable: true, searchable: true },
            { data: 'book_date_acceptance', name: 'book_date_acceptance', orderable: true, searchable: true },
            { data: 'proses_pengadaan', name: 'proses_pengadaan', orderable: true, searchable: true },
            {
                data: 'book_procurement_price',
                name: 'book_procurement_price',
                orderable: true,
                searchable: true,
                render: function(data) {
                    return data ? `Rp ${parseInt(data).toLocaleString('id-ID')}` : 'Rp 0';
                }
            },
            {
                data: 'book_total_price',
                name: 'book_total_price',
                orderable: true,
                searchable: true,
                render: function(data) {
                    return data ? `Rp ${parseInt(data).toLocaleString('id-ID')}` : 'Rp 0';
                }
            },
            { data: 'book_copy', name: 'book_copy', orderable: true, searchable: true },
            { data: 'proses_ketersediaan', name: 'proses_ketersediaan', orderable: true, searchable: true },
            { data: 'book_date_available', name: 'book_date_available', orderable: true, searchable: true },
            { data: 'proses_email', name: 'proses_email', orderable: true, searchable: true },
            { data: 'book_date_email_confirmed', name: 'book_date_email_confirmed', orderable: true, searchable: true },
            { data: 'book_catalog_number', name: 'book_catalog_number', orderable: true, searchable: true },
        ],
        columnDefs: [
                    {
                        // For Checkboxes
                        targets: 0,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row) {
                            // console.log(row.book_status);
                            if (row.book_status === 'pengajuan') {
                                return '<input type="checkbox" class="dt-checkboxes form-check-input">';
                            }
                            return '';
                        },
                        checkboxes: {
                            selectRow: true,
                            selectAllRender: '<input type="checkbox" class="form-check-input">'
                        }
                    },
                ],
        select: {
            // Select style
            style: 'multi',
            items: 'row', // Allow row selection
            selector: 'td:first-child input[type="checkbox"]', // Only select the checkbox column
            blurable: true,
            className: 'row-selected',
            info: false
        },
        responsive: false,
        scrollX: true,

    });

    // Update status dropdown based on URL parameter
    if (status) {
        $('#status').val(status).trigger('change');
    }
    if (getFakultas) {
        $('#fakultas').val(getFakultas).trigger('change');
    }
    


    $('#bs-datepicker-basic').datepicker({
        todayHighlight: true,
        orientation: 'top',
        format: 'dd/mm/yyyy',
        },
    );

    $('.bs-datepicker').datepicker({
        todayHighlight: true,
        orientation: 'top',
        format: 'dd/mm/yyyy',
        },
    );

    $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="add()"><i class="ti ti-file-plus ti-sm me-1"></i> Tambah Data </button>`)
    $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="showLogisticModal()"><i class="ti ti-file-plus ti-sm me-1"></i> Input  Logistik </button>`)
    $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="showUploadModal()"><i class="ti ti-file-plus ti-sm me-1"></i> Upload Pengajuan </button>`)
    $('.dtb').append(`<button class="btn btn-openlib-red btn-sm me-2" onclick="window.location.href = url + '/download';"><i class="ti ti-file-plus ti-sm me-1"></i> Template Pengajuan </button>`)
})


$(document).ready(function() {

    $('#datePengajuan').on('change', function() {
        var datePengajuanDiv = $('#datePengajuanDiv');
        if ($(this).val() === 'date') {
            datePengajuanDiv.removeClass('d-none');
        } else {
            datePengajuanDiv.addClass('d-none');
        }
    });
    $('#dateLogistik').on('change', function() {
        var dateLogistikDiv = $('#dateLogistikDiv');
        if ($(this).val() === 'date') {
            dateLogistikDiv.removeClass('d-none');
        } else {
            dateLogistikDiv.addClass('d-none');
        }
    });
    $('#dateBuku').on('change', function() {
        var dateBuku = $('#dateBukuDiv');
        if ($(this).val() === 'date') {
            dateBuku.removeClass('d-none');
        } else {
            dateBuku.addClass('d-none');
        }
    });
    $('#dateEmail').on('change', function() {
        var dateEmail = $('#dateEmailDiv');
        if ($(this).val() === 'date') {
            dateEmail.removeClass('d-none');
        } else {
            dateEmail.addClass('d-none');
        }
    });
    $('#dateTersedia').on('change', function() {
        var dateTersedia = $('#dateTersediaDiv');
        if ($(this).val() === 'date') {
            dateTersedia.removeClass('d-none');
        } else {
            dateTersedia.addClass('d-none');
        }
    });

    $('#datepickerPengajuan').daterangepicker({
        // autoUpdateInput: false,
        ranges: {
            Today: [moment(), moment()],
            Yesterday: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        },
        showDropdowns: true,
        opens: isRtl ? 'left' : 'right',
        }, function(start, end, label) {
                startDate = start;
                endDate = end;
            });

    $('#datepickerLogistik').daterangepicker({
        // autoUpdateInput: false,
        ranges: {
            Today: [moment(), moment()],
            Yesterday: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        },
        showDropdowns: true,
        opens: isRtl ? 'left' : 'right',
        }, function(start, end, label) {
                startDate = start;
                endDate = end;
            });

    $('#datepickerBuku').daterangepicker({
        // autoUpdateInput: false,
        ranges: {
            Today: [moment(), moment()],
            Yesterday: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        },
        showDropdowns: true,
        opens: isRtl ? 'left' : 'right',
        }, function(start, end, label) {
                startDate = start;
                endDate = end;
            });

    $('#datepickerEmail').daterangepicker({
        // autoUpdateInput: false,
        ranges: {
            Today: [moment(), moment()],
            Yesterday: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        },
        showDropdowns: true,
        opens: isRtl ? 'left' : 'right',
        }, function(start, end, label) {
                startDate = start;
                endDate = end;
            });

    $('#datepickerTersedia').daterangepicker({
        // autoUpdateInput: false,
        ranges: {
            Today: [moment(), moment()],
            Yesterday: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        },
        showDropdowns: true,
        opens: isRtl ? 'left' : 'right',
        }, function(start, end, label) {
                startDate = start;
                endDate = end;
            });
    
    if (dateRange) {
        let dates = dateRange.split(' - ');
        startDate = moment(dates[0], 'MM/DD/YYYY');
        endDate = moment(dates[1], 'MM/DD/YYYY');
        
        $('#datepickerPengajuan').data('daterangepicker').setStartDate(startDate);
        $('#datepickerPengajuan').data('daterangepicker').setEndDate(endDate);
        $('#datePengajuan').val('date').trigger('change');
    }

    $('#show-date-btn').on('click', function(event) {
        event.preventDefault();
        updateStatus();
        updateFakultas();
        updateProdi();
        dateRange = null;
        dTable.ajax.reload();
    });

});

$('#fakultas').change(function() {
	var facultyId = $(this).val();
	if(facultyId) {
		$.ajax({
			'url':url+'/getProdi',
			type: 'POST',
			data: {facultyId: facultyId},
			dataType: 'json',
			success: function(data) {

                var prodiSelect = $('#prodi');
                prodiSelect.empty().append('<option value="">Semua</option>');
                $.each(data, function(key, value) {
                    prodiSelect.append('<option value="' + key + '">' + value + '</option>');
                });
                
                if (getProdi) {
                    $('#prodi').val(getProdi).trigger('change');
                }
			}
		});
	} else {
		$('#prodi').empty().append('<option value="">Semua Prodi</option>');
	}
});

function add() {
    _reset();
    $('#frmbox').modal('show');
}

function showUploadModal() {
    _reset();
    $('#frmbox_upload').modal('show');
}

function save_upload(){

    if($("#frm_upload").valid())
    {
        let formData = new FormData($('#frm_upload')[0]);

        $.ajax({
            url: url+'/upload',
            type: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if(data.status === 'success') {
                    $('#frmbox_upload').modal('hide'); // Tutup modal jika berhasil menyimpan
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
                    $('#frmbox_upload').modal('hide');
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
                $('#frmbox_upload').modal('hide');
            }
        });
    } else {
        // Jika form tidak valid, tetap tutup modal
        // $('#frmbox').modal('hide');
    }
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

function edit(id) {
    _reset();
    // let rowData = dataTableData.find(row => row.book_id == id);
    $.ajax({
        url: url + '/get/' + id,
        type: 'get',
        dataType: 'json',
        success: function(e) {
            _reset();

            $('#id').val(id);
            $.each(e, function(key, value) {
                $('#' + key).val(value);
            });

            $('#frmbox #select_prodi').val(e.book_id_prodi).trigger('change');

            var formattedDate = moment(e.book_date_prodi_submission).format('DD/MM/YYYY');
            $('#frmbox #bs-datepicker-basic').val(formattedDate).datepicker('update');
            // $('#frmbox_edit #hiddenInputField').val(e.book_id_user).trigger('change');
            // $('#frmbox_edit #book_id_prodi').val(e.book_id_prodi).trigger('change');
            // $('#frmbox_edit #username_edit').val(rowData.master_data_fullname);

            $("#frmbox").modal('show');
        }
    });
}

function showLogisticModal(id){
    _reset();
    selectedIds = [];
    var selectedItems = dTable.rows({ selected: true }).data();
    var selectedItemsList = $('#list_buku');
    selectedItemsList.empty();

    // Filter rows to include only those with book_status === 'pengajuan'
    var filteredItems = [];
    selectedItems.each(function (item) {
        if (item.book_status === 'pengajuan') {
            filteredItems.push(item);
            selectedIds.push(item.book_id);
        }
    });
    
    if (filteredItems.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Data Selected',
            text: 'Please select data first.',
        });
        return;
    }

    selectedItemsList.empty(); // Clear the list before appending new items

    // var selectedIds = [];
    filteredItems.forEach(function (item) {
        // Append the selected items to the list
        selectedItemsList.append('<li class="list-group-item">' + item.book_member + ' - ' + item.book_subject + '</li>');
    });

    $("#frmbox_logistic").modal('show');
}

function editLogisticModal(id){
    _reset();
    // let rowData = dataTableData.find(row => row.book_id == id);
    $.ajax({
        url: url + '/get_logistik/' + id,
        type: 'get',
        dataType: 'json',
        success: function(e) {
            _reset();

            $('#id').val(id);
            $.each(e, function(key, value) {
                $('#frmbox_logistic_update #' + key).val(value);
            });

            // $('#frmbox #select_prodi').val(e.book_id_prodi).trigger('change');

            // var formattedDate = moment(e.book_date_prodi_submission).format('DD/MM/YYYY');
            // $('#frmbox #bs-datepicker-basic').val(formattedDate).datepicker('update');
            // $('#frmbox_edit #hiddenInputField').val(e.book_id_user).trigger('change');
            // $('#frmbox_edit #book_id_prodi').val(e.book_id_prodi).trigger('change');
            // $('#frmbox_edit #username_edit').val(rowData.master_data_fullname);

            $("#frmbox_logistic_update").modal('show');
        }
    });
}

function save_logistic(){

    if($("#frm_logistic").valid())
    {
    // Create a deep copy of selectedIds to avoid reference issues
    var idsToSubmit = JSON.parse(JSON.stringify(selectedIds));
            
            // Validate if we have any IDs to submit
            if (idsToSubmit.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No items selected for logistics processing.'
                });
                return;
            }

            let formData = new FormData($('#frm_logistic')[0]);
            
            // Append the selectedIds array to the formData
            formData.append('selectedIds', JSON.stringify(idsToSubmit));

        $.ajax({
            url: url+'/logistics',
            type: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if(data.status === 'success') {
                    $('#frmbox_logistic').modal('hide'); // Tutup modal jika berhasil menyimpan
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
                    $('#frmbox_logistic').modal('hide');
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
                $('#frmbox_logistic').modal('hide');
            }
        });
    } else {
        // Jika form tidak valid, tetap tutup modal
        // $('#frmbox').modal('hide');
    }
}

function update_logistic(){

    if($("#frm_logistic_update").valid())
    {
        let formData = new FormData($('#frm_logistic_update')[0]);

        // Append the selectedIds array to the formData
        // formData.append('selectedIds', JSON.stringify(selectedIds));

        $.ajax({
            url: url+'/logistics_update',
            type: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if(data.status === 'success') {
                    $('#frmbox_logistic_update').modal('hide'); // Tutup modal jika berhasil menyimpan
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
                    $('#frmbox_logistic_update').modal('hide');
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
                $('#frmbox_logistic_update').modal('hide');
            }
        });
    } else {
        // Jika form tidak valid, tetap tutup modal
        // $('#frmbox').modal('hide');
    }
}

function showPenerimaanModal(id){

    _reset();
    // let rowData = dataTableData.find(row => row.book_id == id);
    $.ajax({
        url: url + '/get_penerimaan/' + id,
        type: 'get',
        dataType: 'json',
        success: function(e) {
            _reset();

            $('#id').val(id);
            $.each(e, function(key, value) {
                $('#frmbox_penerimaan #' + key).val(value);
            });

            // $('#frmbox #select_prodi').val(e.book_id_prodi).trigger('change');

            // var formattedDate = moment(e.book_date_prodi_submission).format('DD/MM/YYYY');
            // $('#frmbox #bs-datepicker-basic').val(formattedDate).datepicker('update');
            // $('#frmbox_edit #hiddenInputField').val(e.book_id_user).trigger('change');
            // $('#frmbox_edit #book_id_prodi').val(e.book_id_prodi).trigger('change');
            // $('#frmbox_edit #username_edit').val(rowData.master_data_fullname);

            $("#frmbox_penerimaan").modal('show');
        }
    });

}

function save_penerimaan(){
    if($("#frm_penerimaan").valid())
    {
        let formData = new FormData($('#frm_penerimaan')[0]);

        $.ajax({
            url: url+'/penerimaan',
            type: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if(data.status === 'success') {
                    $('#frmbox_penerimaan').modal('hide'); // Tutup modal jika berhasil menyimpan
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
                    $('#frmbox_penerimaan').modal('hide');
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
                $('#frmbox_penerimaan').modal('hide');
            }
        });
    } else {
        // Jika form tidak valid, tetap tutup modal
        // $('#frmbox').modal('hide');
    }
}

function showKetersediaanModal(id){

    _reset();
    // let rowData = dataTableData.find(row => row.book_id == id);
    $.ajax({
        url: url + '/get_ketersediaan/' + id,
        type: 'get',
        dataType: 'json',
        success: function(e) {
            _reset();

            $('#id').val(id);
            $.each(e, function(key, value) {
                $('#frmbox_ketersedian #' + key).val(value);
            });

            // $('#frmbox #select_prodi').val(e.book_id_prodi).trigger('change');

            // var formattedDate = moment(e.book_date_prodi_submission).format('DD/MM/YYYY');
            // $('#frmbox #bs-datepicker-basic').val(formattedDate).datepicker('update');
            // $('#frmbox_edit #hiddenInputField').val(e.book_id_user).trigger('change');
            // $('#frmbox_edit #book_id_prodi').val(e.book_id_prodi).trigger('change');
            // $('#frmbox_edit #username_edit').val(rowData.master_data_fullname);

            $("#frmbox_ketersedian").modal('show');
        }
    });

}

function save_ketersediaan(){
    if($("#frm_ketersedian").valid())
    {
        let formData = new FormData($('#frm_ketersedian')[0]);

        $.ajax({
            url: url+'/ketersedian',
            type: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if(data.status === 'success') {
                    $('#frmbox_ketersedian').modal('hide'); // Tutup modal jika berhasil menyimpan
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
                    $('#frmbox_ketersedian').modal('hide');
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
                $('#frmbox_ketersedian').modal('hide');
            }
        });
    } else {
        // Jika form tidak valid, tetap tutup modal
        // $('#frmbox').modal('hide');
    }
}

function showEmailModal(id){
    _reset();
    // let rowData = dataTableData.find(row => row.book_id == id);
    $.ajax({
        url: url + '/get_email/' + id,
        type: 'get',
        dataType: 'json',
        success: function(e) {
            _reset();

            // console.log(id);

            $('#id').val(id);
            $.each(e, function(key, value) {
                $('#frmbox_email #' + key).val(value);
            });

            // $('#frmbox #select_prodi').val(e.book_id_prodi).trigger('change');

            // var formattedDate = moment(e.book_date_prodi_submission).format('DD/MM/YYYY');
            // $('#frmbox #bs-datepicker-basic').val(formattedDate).datepicker('update');
            // $('#frmbox_edit #hiddenInputField').val(e.book_id_user).trigger('change');
            // $('#frmbox_edit #book_id_prodi').val(e.book_id_prodi).trigger('change');
            // $('#frmbox_edit #username_edit').val(rowData.master_data_fullname);

            $("#frmbox_email").modal('show');
        }
    });

}

function save_email(){
    if($("#frm_email").valid())
    {
        let formData = new FormData($('#frm_email')[0]);

        $.ajax({
            url: url+'/email',
            type: 'post',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if(data.status === 'success') {
                    $('#frmbox_email').modal('hide'); // Tutup modal jika berhasil menyimpan
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
                    $('#frmbox_email').modal('hide');
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
                $('#frmbox_email').modal('hide');
            }
        });
    } else {
        // Jika form tidak valid, tetap tutup modal
        // $('#frmbox').modal('hide');
    }


}

function _reset() {
    // Reset all form inputs
    $('#frm, #frm_edit, #frm_logistic').trigger('reset');

    // Reset all datepickers
    $('.datepicker').each(function() {
        $(this).datepicker('setDate', null);
    });

    // Clear select2 fields
    // $('.select2').val(null).trigger('change');
}
</script>
@endsection
