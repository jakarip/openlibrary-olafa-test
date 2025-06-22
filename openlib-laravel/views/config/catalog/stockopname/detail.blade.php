@extends('layouts/layoutMaster')

@section('title', 'Stock Opname Detail')

@section('vendor-style')
@endsection

@section('page-style')
<style>
    .highcharts-credits,
    .highcharts-button {
        display: none;
    }
    .select2-container {
        z-index: 9999;
    }
    .custom-modal-width {
        max-width: 90%;
        width: auto;
    }
    .button-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .button-grid-full {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
    }

    @media (min-width: 768px) {
        .button-grid {
            grid-template-columns: repeat(4, 1fr);
        }

        .button-grid-full {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 767.98px) {
        .button-grid-full .btn {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form class="dt_adv_search">
            <div class="row">
                <div class="col-12">
                    <div class="row g-3">
                        {{-- Select Berdasarkan Jenis Katalog --}}
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="jeniskatalog" class="form-label">{{ __('catalogs.stock_detail_filter_catalogtype') }}</label>
                            <select id="jeniskatalog" class="form-select form-select-md">
                                <option value="">{{ __('common.all') }}</option>
                                <?php foreach ($jeniskatalog as $katalog): ?>
                                    <option value="<?= $katalog->id; ?>"><?= $katalog->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        {{-- Select Berdasarkan User --}}
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="user" class="form-label">{{ __('catalogs.stock_detail_filter_user') }}</label>
                            <select id="user" class="form-select form-select-md">
                                <option value="">{{ __('common.all') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->master_data_fullname }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Select Berdasarkan Lokasi Openlib --}}
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="lokasiopenlib" class="form-label">{{ __('catalogs.stock_detail_filter_locationopenlib') }}</label>
                            <select id="lokasiopenlib" class="form-select form-select-md">
                                <option value="">{{ __('common.all') }}</option>
                                <?php foreach ($locations as $lokasiopenlib): ?>
                                    <option value="<?= $lokasiopenlib->id; ?>"><?= $lokasiopenlib->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        {{-- Select Berdasarkan Lokasi SO --}}
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="lokasiso" class="form-label">{{ __('catalogs.stock_detail_filter_locationso') }}</label>
                            <select id="lokasiso" class="form-select form-select-md">
                                <option value="">{{ __('common.all') }}</option>
                                <?php foreach ($locations as $lokasiopenlib): ?>
                                    <option value="<?= $lokasiopenlib->id; ?>"><?= $lokasiopenlib->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        {{-- Select Berdasarkan Status Openlib --}}
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="statusopenlib" class="form-label">{{ __('catalogs.stock_detail_filter_statusopenlib') }}</label>
                            <select id="statusopenlib" class="form-select form-select-md">
                                <option value="">{{ __('common.all') }}</option>
                                <option value="1">Tersedia</option>
                                <option value="2">Dipinjam</option>
                                <option value="3">Rusak</option>
                                <option value="4">Hilang</option>
                                <option value="5">Expired</option>
                                <option value="6">Hilang Diganti</option>
                                <option value="7">Sedang Diproses</option>
                                <option value="8">Cadangan</option>
                                <option value="9">Weeding</option>
                            </select>
                        </div>

                        {{-- Select Berdasarkan Status SO --}}
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="statusso" class="form-label">{{ __('catalogs.stock_detail_filter_statusso') }}</label>
                            <select id="statusso" class="form-select form-select-md">
                                <option value="">{{ __('common.all') }}</option>
                                <option value="1">Tersedia</option>
                                <option value="2">Dipinjam</option>
                                <option value="3">Rusak</option>
                                <option value="4">Hilang</option>
                                <option value="5">Expired</option>
                                <option value="6">Hilang Diganti</option>
                                <option value="7">Sedang Diproses</option>
                                <option value="8">Cadangan</option>
                                <option value="9">Weeding</option>
                            </select>
                        </div>

                        {{-- Select Berdasarkan Perbedaan --}}
                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="status" class="form-label">{{ __('catalogs.stock_detail_filter_condition') }}</label>
                            <select id="status" class="form-select form-select-md" name="status">
                                <option value="">{{ __('common.all') }}</option>
                                <option value="status_diff">{{ __('catalogs.stock_detail_filter_condition_status') }}</option>
                                <option value="location_diff">{{ __('catalogs.stock_detail_filter_condition_location') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <hr class="mt-0">
    <div class="card-body">
        <div class="button-grid">
            <button type="button" class="btn btn-info" id="btnBarcodeDuplicate">{{ __('catalogs.stock_detail_button_duplicate') }}</button>
            <button type="button" class="btn btn-info" id="btnBarcodeBelumAda">{{ __('catalogs.stock_detail_button_barcodenotso') }}</button>
            <button type="button" class="btn btn-warning" id="btnStatistikBelumSO">{{ __('catalogs.stock_detail_button_barcodestatisticsnotso') }}</button>
            <button type="button" class="btn btn-warning" id="btnStatistikSudahSO">{{ __('catalogs.stock_detail_button_barcodestatisticsso') }}</button>
        </div>
    </div>
    <hr class="mt-0">
    <div class="card-body">
        <div class="button-grid-full">
            @if ($stockopname->so_status == 1) {{-- 1 berarti Aktif --}}
                <button type="button" class="btn btn-danger" id="btnHapusSemuaData">{{ __('catalogs.stock_detail_button_deleteall') }} {{ $currentUser->master_data_user }}</button>
                <button type="button" class="btn btn-success" id="btnImporDataSo">{{ __('catalogs.stock_detail_button_import') }}</button>
                <button type="button" class="btn btn-success" id="btnInsertManualDataSo">{{ __('catalogs.stock_detail_button_inputmanual') }}</button>
            @endif
        </div>
    </div>
    <hr class="mt-0">
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table" id="table">
                <thead>
                    <tr class="text-nowrap">
                        <th>{{ __('common.action') }}</th>
                        <th>{{ __('catalogs.stock_detail_table_number') }}</th>
                        <th>{{ __('catalogs.stock_detail_table_date') }}</th>
                        <th>{{ __('catalogs.stock_detail_table_member') }}</th>
                        <th>{{ __('catalogs.stock_detail_table_catalogtype') }}</th>
                        <th>{{ __('catalogs.stock_detail_table_title') }}</th>
                        <th>{{ __('catalogs.stock_detail_table_classificationnumber') }}</th>
                        <th>{{ __('catalogs.stock_detail_table_catalognumber') }}</th>
                        <th>{{ __('catalogs.stock_detail_table_barcode') }}</th>
                        <th>{{ __('catalogs.stock_detail_table_locationlib') }}</th>
                        <th>{{ __('catalogs.stock_detail_table_locationso') }}</th>
                        <th>{{ __('catalogs.stock_detail_table_statuslib') }}</th>
                        <th>{{ __('catalogs.stock_detail_table_statusso') }}</th>
                        <th>{{ __('catalogs.stock_detail_table_label') }}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="barcodeduplicateModal" tabindex="-1" aria-labelledby="barcodeduplicateModalLabel" aria-hidden="true">
    <div class="modal-dialog custom-modal-width">
        <div class="modal-content">

            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i>{{ __('catalogs.stock_detail_button_duplicate') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="card-body">
                    <form class="dt_adv_search">
                        <div class="row">
                            <div class="col-12">
                                <div class="row g-3">
                                    {{-- Select Berdasarkan Jenis Katalog --}}
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <label for="jeniskatalogduplicate" class="form-label">{{ __('catalogs.stock_detail_filter_catalogtype') }}</label>
                                        <select id="jeniskatalogduplicate" class="form-select form-select-md">
                                            <option value="">{{ __('common.all') }}</option>
                                            <?php foreach ($jeniskatalog as $katalog): ?>
                                                <option value="<?= $katalog->id; ?>"><?= $katalog->name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    {{-- Select Berdasarkan Status Openlib --}}
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <label for="statusopenlibduplicate" class="form-label">{{ __('catalogs.stock_detail_table_statuslib') }}</label>
                                        <select id="statusopenlibduplicate" class="form-select form-select-md">
                                            <option value="">{{ __('common.all') }}</option>
                                            <option value="1">{{ __('catalogs.stock_available') }}</option>
                                            <option value="2">{{ __('catalogs.stock_borrowed') }}</option>
                                            <option value="3">{{ __('catalogs.stock_damaged') }}</option>
                                            <option value="4">{{ __('catalogs.stock_lost') }}</option>
                                            <option value="5">{{ __('catalogs.stock_expired') }}</option>
                                            <option value="6">{{ __('catalogs.stock_lostreplaced') }}</option>
                                            <option value="7">{{ __('catalogs.stock_beingprocessed') }}</option>
                                            <option value="8">{{ __('catalogs.stock_reserve') }}</option>
                                            <option value="9">{{ __('catalogs.stock_weeding') }}</option>
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <hr class="mt-3">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatables-basic table" id="barcodeduplicateTable">
                        <thead>
                            <tr class="text-nowrap">
                                <th style="width: 5%;">{{ __('catalogs.stockduplicate_number') }}</th>
                                <th style="width: 10%;">{{ __('catalogs.stockduplicate_totalmembers') }}</th>
                                <th style="width: 15%;">{{ __('catalogs.stockduplicate_members') }}</th>
                                <th style="width: 15%;">{{ __('catalogs.stockduplicate_catalogtype') }}</th>
                                <th style="width: 20%;">{{ __('catalogs.stockduplicate_title') }}</th>
                                <th style="width: 10%;">{{ __('catalogs.stockduplicate_classificationnumber') }}</th>
                                <th style="width: 10%;">{{ __('catalogs.stockduplicate_catalognumber') }}</th>
                                <th style="width: 10%;">{{ __('catalogs.stockduplicate_barcode') }}</th>
                                <th style="width: 15%;">{{ __('catalogs.stockduplicate_filename') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.close') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="barcodebelumsoModal" tabindex="-1" aria-labelledby="barcodebelumsoModalLabel" aria-hidden="true">
    <div class="modal-dialog custom-modal-width">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i>{{ __('catalogs.stock_detail_button_barcodenotso') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <form class="dt_adv_search">
                        <div class="row">
                            <div class="col-12">
                                <div class="row g-3">
                                    {{-- Select Berdasarkan Jenis Katalog --}}
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <label for="jeniskatalogbelumso" class="form-label">{{ __('catalogs.stock_detail_filter_catalogtype') }}</label>
                                        <select id="jeniskatalogbelumso" class="form-select form-select-md">
                                            <option value="">{{ __('common.all') }}</option>
                                            <?php foreach ($jeniskatalog as $katalog): ?>
                                                <option value="<?= $katalog->id; ?>"><?= $katalog->name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    {{-- Select Berdasarkan Status Openlib --}}
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <label for="statusopenlibbelumso" class="form-label">{{ __('catalogs.stock_detail_table_statuslib') }}</label>
                                        <select id="statusopenlibbelumso" class="form-select form-select-md">
                                            <option value="">{{ __('common.all') }}</option>
                                            <option value="1">{{ __('catalogs.stock_available') }}</option>
                                            <option value="2">{{ __('catalogs.stock_borrowed') }}</option>
                                            <option value="3">{{ __('catalogs.stock_damaged') }}</option>
                                            <option value="4">{{ __('catalogs.stock_lost') }}</option>
                                            <option value="5">{{ __('catalogs.stock_expired') }}</option>
                                            <option value="6">{{ __('catalogs.stock_lostreplaced') }}</option>
                                            <option value="7">{{ __('catalogs.stock_beingprocessed') }}</option>
                                            <option value="8">{{ __('catalogs.stock_reserve') }}</option>
                                            <option value="9">{{ __('catalogs.stock_weeding') }}</option>
                                        </select>
                                    </div>

                                    {{-- Select Berdasarkan Lokasi Openlib --}}
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <label for="lokasiopenlibbelumso" class="form-label">{{ __('catalogs.stock_detail_table_locationlib') }}</label>
                                        <select id="lokasiopenlibbelumso" class="form-select form-select-md">
                                            <option value="">{{ __('common.all') }}</option>
                                            <?php foreach ($locations as $lokasiopenlib): ?>
                                                <option value="<?= $lokasiopenlib->id; ?>"><?= $lokasiopenlib->name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="row g-3 align-items-end">
                                    {{-- Input Filter No Klasifikasi --}}
                                    <div class="col-12 col-sm-6 col-lg-2">
                                        <label for="klasifikasiAwal" class="form-label">{{ __('catalogs.stock_weeding_filter_Initial') }}</label>
                                        <input type="text" id="klasifikasiAwal" class="form-control">
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-2">
                                        <label for="klasifikasiAkhir" class="form-label">{{ __('catalogs.stock_weeding_filter_final') }}</label>
                                        <input type="text" id="klasifikasiAkhir" class="form-control">
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-2">
                                        <label for="filterNoKlasifikasi" class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-primary w-100" id="filterNoKlasifikasi">Filter</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <hr class="mt-3">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatables-basic table" id="barcodebelumsoTable">
                        <thead>
                            <tr class="text-nowrap">
                                <th style="width: 5%;">{{ __('catalogs.stock_barcodenot_number') }}</th>
                                <th style="width: 15%;">{{ __('catalogs.stock_barcodenot_location') }}</th>
                                <th style="width: 15%;">{{ __('catalogs.stock_barcodenot_catalogtype') }}</th>
                                <th style="width: 20%;">{{ __('catalogs.stock_barcodenot_title') }}</th>
                                <th style="width: 15%;">{{ __('catalogs.stock_barcodenot_author') }}</th>
                                <th style="width: 10%;">{{ __('catalogs.stock_barcodenot_classificationnumber') }}</th>
                                <th style="width: 10%;">{{ __('catalogs.stock_barcodenot_catalognumber') }}</th>
                                <th style="width: 10%;">{{ __('catalogs.stock_barcodenot_barcode') }}</th>
                                <th style="width: 10%;">{{ __('catalogs.stock_barcodenot_openlibstatus') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.close') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="statistikbarcodebelumsoModal" tabindex="-1" aria-labelledby="statistikbarcodebelumsoModalLabel" aria-hidden="true">
    <div class="modal-dialog custom-modal-width">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i>{{ __('catalogs.stock_detail_button_barcodestatisticsnotso')}}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <form class="dt_adv_search">
                        <div class="row">
                            <div class="col-12">
                                <div class="row g-3 align-items-end">
                                    {{-- Input Filter Lokasi --}}
                                    <div class="col-12 col-sm-6 col-lg-2">
                                        <label for="lokasiopenlib-belumso" class="form-label">{{ __('catalogs.stock_detail_table_locationlib')}}</label>
                                        <select id="lokasiopenlib-belumso" class="form-select form-select-md">
                                            <option value="">{{ __('common.all') }}</option>
                                            <?php foreach ($locations as $lokasiopenlib): ?>
                                                <option value="<?= $lokasiopenlib->id; ?>"><?= $lokasiopenlib->name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    {{-- Input Filter Tanggal --}}
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <label for="bs-rangepicker-dropdown-belumso" class="form-label">{{ __('catalogs.stock_detail_datadate') }}</label>
                                        <input type="text" id="bs-rangepicker-dropdown-belumso" class="form-control" />
                                    </div>
                                    {{-- Tombol Filter --}}
                                    <div class="col-12 col-sm-6 col-lg-2">
                                        <label for="filterstatistikbarcodebelumso" class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-primary w-100" id="filterstatistikbarcodebelumso">Filter</button>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-2">
                                        <label for="clearstatistikbarcodebelumso" class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-secondary w-100" id="clearstatistikbarcodebelumso">Clear</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <hr class="mt-3">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatables-basic table border-top" id="statistikbarcodebelumsoTable">
                        <thead>
                            <tr>
                                <th style="width: 10%;" rowspan="2">{{ __('catalogs.stockstatisticbarcode_catalogtype') }}</th>
                                <th style="width: 5%;" rowspan="2">{{ __('catalogs.stockstatisticbarcode_totaltitles') }}</th>
                                <th colspan="2" style="width: 10%; text-align: center;">{{ __('catalogs.stock_available') }}</th>
                                <th colspan="2" style="width: 10%; text-align: center;">{{ __('catalogs.stock_borrowed') }}</th>
                                <th colspan="2" style="width: 10%; text-align: center;">{{ __('catalogs.stock_damaged') }}</th>
                                <th colspan="2" style="width: 10%; text-align: center;">{{ __('catalogs.stock_lost') }}</th>
                                <th colspan="2" style="width: 10%; text-align: center;">{{ __('catalogs.stock_expired') }}</th>
                                <th colspan="2" style="width: 10%; text-align: center;">{{ __('catalogs.stock_lostreplaced') }}</th>
                                <th colspan="2" style="width: 10%; text-align: center;">{{ __('catalogs.stock_beingprocessed') }}</th>
                                <th colspan="2" style="width: 10%; text-align: center;">{{ __('catalogs.stock_reserve') }}</th>
                                <th colspan="2" style="width: 10%; text-align: center;">{{ __('catalogs.stock_weeding') }}</th>
                            </tr>
                            <tr>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_title') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_copy') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_title') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_copy') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_title') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_copy') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_title') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_copy') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_title') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_copy') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_title') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_copy') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_title') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_copy') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_title') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_copy') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_title') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_copy') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Baris data akan ditambahkan di sini -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.close') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="statistikbarcodesudahsoModal" tabindex="-1" aria-labelledby="statistikbarcodesudahsoModalLabel" aria-hidden="true">
    <div class="modal-dialog custom-modal-width">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i>{{ __('catalogs.stock_detail_button_barcodestatisticsso') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <form class="dt_adv_search">
                        <div class="row">
                            <div class="col-12">
                                <div class="row g-3 align-items-end">
                                    {{-- Input Filter Lokasi --}}
                                    <div class="col-12 col-sm-6 col-lg-2">
                                        <label for="lokasiopenlib-sudahso" class="form-label">{{ __('catalogs.stock_detail_table_locationlib') }}</label>
                                        <select id="lokasiopenlib-sudahso" class="form-select form-select-md">
                                            <option value="">{{ __('common.all') }}</option>
                                            <?php foreach ($locations as $lokasiopenlib): ?>
                                                <option value="<?= $lokasiopenlib->id; ?>"><?= $lokasiopenlib->name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    {{-- Input Filter Tanggal --}}
                                    <div class="col-12 col-sm-6 col-lg-3">
                                        <label for="bs-rangepicker-dropdown-sudahso" class="form-label">{{ __('catalogs.stock_detail_datadate') }}</label>
                                        <input type="text" id="bs-rangepicker-dropdown-sudahso" class="form-control" />
                                    </div>
                                    {{-- Tampilkan jumlah berdasarkan --}}
                                    <div class="col-12 col-sm-6 col-lg-2">
                                        <label for="show_status" class="form-label">{{ __('catalogs.stock_detail_filter_amountby') }}</label>
                                        <select id="show_status" class="form-select form-select-md">
                                            <option value="">{{ __('common.all') }}</option>
                                            <option value="so">{{ __('catalogs.stock_detail_table_statusso') }}</option>
                                            <option value="openlib">{{ __('catalogs.stock_detail_table_statuslib') }}</option>
                                        </select>
                                    </div>
                                    {{-- Tombol Filter --}}
                                    <div class="col-12 col-sm-6 col-lg-2">
                                        <label for="filterstatistikbarcodesudahso" class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-primary w-100" id="filterstatistikbarcodesudahso">Filter</button>
                                    </div>
                                    <div class="col-12 col-sm-6 col-lg-2">
                                        <label for="clearstatistikbarcodesudahso" class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-secondary w-100" id="clearstatistikbarcodesudahso">Clear</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <hr class="mt-3">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatables-basic table border-top" id="statistikbarcodesudahsoTable">
                        <thead>
                            <tr>
                                <th style="width: 10%;" rowspan="2">{{ __('catalogs.stockstatisticbarcode_catalogtype') }}</th>
                                <th style="width: 5%;" rowspan="2">{{ __('catalogs.stockstatisticbarcode_totaltitles') }}</th>
                                <th colspan="2" style="width: 10%; text-align: center;">{{ __('catalogs.stock_available') }}</th>
                                <th colspan="2" style="width: 10%; text-align: center;">{{ __('catalogs.stock_borrowed') }}</th>
                                <th colspan="2" style="width: 10%; text-align: center;">{{ __('catalogs.stock_damaged') }}</th>
                                <th colspan="2" style="width: 10%; text-align: center;">{{ __('catalogs.stock_lost') }}</th>
                                <th colspan="2" style="width: 10%; text-align: center;">{{ __('catalogs.stock_expired') }}</th>
                                <th colspan="2" style="width: 10%; text-align: center;">{{ __('catalogs.stock_lostreplaced') }}</th>
                                <th colspan="2" style="width: 10%; text-align: center;">{{ __('catalogs.stock_beingprocessed') }}</th>
                                <th colspan="2" style="width: 10%; text-align: center;">{{ __('catalogs.stock_reserve') }}</th>
                                <th colspan="2" style="width: 10%; text-align: center;">{{ __('catalogs.stock_weeding') }}</th>
                            </tr>
                            <tr>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_title') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_copy') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_title') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_copy') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_title') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_copy') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_title') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_copy') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_title') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_copy') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_title') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_copy') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_title') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_copy') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_title') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_copy') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_title') }}</th>
                                <th style="width: 5%;">{{ __('catalogs.stockstatisticbarcode_copy') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Baris data akan ditambahkan di sini -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.close') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="insertmanualdataModal" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title"><i class="ti ti-forms me-2"></i> {{ __('catalogs.stock_detail_button_inputmanual') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form_manual" class="form-validate">
                    @csrf
                    <input type="hidden" name="id" id="id" value="{{ $id }}">

                    <div class="form-group row mb-4">
                        <label class="col-md-3 col-form-label">{{ __('catalogs.stock_detail_table_statusso') }} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select class="form-control" name="sos_status" id="sos_status" required>
                                <option value="">{{ __('catalogs.stock_selectsostatus') }}</option>
                                <option value="1">{{ __('catalogs.stock_available') }}</option>
                                <option value="2">{{ __('catalogs.stock_borrowed') }}</option>
                                <option value="3">{{ __('catalogs.stock_damaged') }}</option>
                                <option value="4">{{ __('catalogs.stock_lost') }}</option>
                                <option value="5">{{ __('catalogs.stock_expired') }}</option>
                                <option value="6">{{ __('catalogs.stock_lostreplaced') }}</option>
                                <option value="7">{{ __('catalogs.stock_beingprocessed') }}</option>
                                <option value="8">{{ __('catalogs.stock_reserve') }}</option>
                                <option value="9">{{ __('catalogs.stock_weeding') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row mb-4">
                        <label class="col-md-3 col-form-label">{{ __('catalogs.stock_detail_table_locationso') }} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select class="form-control" name="sos_id_location" id="sos_id_location" required>
                                <option value="">{{ __('catalogs.stock_selectsolocation') }}</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row mb-4">
                        <label class="col-md-3 col-form-label">{{ __('catalogs.stock_labelshelf') }} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="sos_filename" id="sos_filename" placeholder="{{ __('catalogs.stock_labelshelf') }}" required>
                        </div>
                    </div>

                    <div class="form-group row mb-4">
                        <label class="col-md-3 col-form-label">{{ __('catalogs.stock_detail_table_barcode') }} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="barcode" id="barcode" placeholder="{{ __('catalogs.stock_detail_table_barcode') }}" required>
                        </div>
                    </div>

                    <div class="form-group row mb-4">
                        <label class="col-md-3 col-form-label">{{ __('catalogs.stock_result') }}</label>
                        <div class="col-md-9">
                            <div class="form-control-plaintext" id="result" style="white-space: pre-line;"></div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="impordataModal" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-transparent">
                <div class="modal-title">
                    <i class="ti ti-forms me-2"></i> {{ __('catalogs.stock_detail_button_import') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="form" class="form-validate" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id" value="{{ $id ?? '' }}">
                    <div class="form-group row mb-4">
                        <label class="col-md-3 col-form-label">{{ __('catalogs.stock_detail_table_statusso') }} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select class="form-select form-select-lg" name="sos_status" id="sos_status" required style="font-size: 15px;">
                                <option value="">{{ __('catalogs.stock_selectsostatus') }}</option>
                                <option value="1">{{ __('catalogs.stock_available') }}</option>
                                <option value="2">{{ __('catalogs.stock_borrowed') }}</option>
                                <option value="3">{{ __('catalogs.stock_damaged') }}</option>
                                <option value="4">{{ __('catalogs.stock_lost') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-md-3 col-form-label">{{ __('catalogs.stock_detail_table_locationso') }} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select class="form-select form-select-lg" name="sos_id_location" id="sos_id_location" required style="font-size: 15px;">
                                <option value="">{{ __('catalogs.stock_selectsolocation') }}</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row mb-2">
                        <label class="col-md-3 col-form-label">File.txt <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="file" class="form-control" name="file" id="file" required accept=".txt">
                            <div class="mt-2">
                                <p id="fileInfo" style="display: none;"></p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="saveImage()">{{ __('common.save') }}</button>
            </div>

            <div class="modal-body">
                <div class="form-group row mb-4">
                    <label class="col-md-3 col-form-label">{{ __('catalogs.stock_result') }}</label>
                    <div class="col-md-9">
                        <div id="result2" class="result2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('vendor-script')
@endsection

@section('page-script')
<script>
let id = '{{ $id }}';
let url = '{{ url('catalog/stockopname/detail') }}/' + id;

$(function() {
    let mainTable = $('#table').DataTable({
        ajax: {
            url: '{{ url('catalog/stockopname/detail') }}/' + id + '/dt',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.knowledge_type_id = $('#jeniskatalog').val();
                d.item_location_id = $('#lokasiopenlib').val();
                d.sos_id_location = $('#lokasiso').val();
                d.status = $('#statusopenlib').val();
                d.sos_status = $('#statusso').val();
                d.condition = $('#status').val();
                d.user_id = $('#user').val();
            }

        },
        columns: [
            { data: 'action', name: 'action', orderable: true, searchable: true },
            { data: 'row_number', name: 'row_number', orderable: true, searchable: false },
            { data: 'formatted_date', name: 'sos_date', orderable: true, searchable: true },
            { data: 'member', name: 'member', orderable: true, searchable: true },
            { data: 'jenis_katalog', name: 'jenis_katalog', orderable: true, searchable: true },
            { data: 'title', name: 'title', orderable: true, searchable: true },
            { data: 'code', name: 'code', orderable: true, searchable: true },
            { data: 'no_katalog', name: 'no_katalog', orderable: true, searchable: true },
            { data: 'barcode', name: 'barcode', orderable: true, searchable: true },
            { data: 'lokasi_openlibrary', name: 'lokasi_openlibrary', orderable: true, searchable: true },
            { data: 'lokasi_so', name: 'lokasi_so', orderable: true, searchable: true },
            { data: 'formatted_status_openlib', name: 'status_openlib', orderable: true, searchable: true },
            { data: 'formatted_sos_status', name: 'sos_status', orderable: true, searchable: true },
            { data: 'sos_filename', name: 'sos_filename', orderable: true, searchable: true }
        ],
        responsive: false,
        scrollX: true,
    });

    let barcodeduplicateTable = $('#barcodeduplicateTable').DataTable({
        ajax: {
            url: '{{ url('catalog/stockopname/detail') }}/' + id + '/dt_barcodeduplicate',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.knowledge_type_id = $('#jeniskatalogduplicate').val();
                d.status = $('#statusopenlibduplicate').val();
            }
        },
        columns: [
            { data: 'row_number', name: 'row_number', orderable: true, searchable: false },
            { data: 'total_member', name: 'total_member', orderable: true, searchable: false },
            { data: 'master_data_fullname', name: 'master_data_fullname', orderable: true, searchable: true },
            { data: 'jenis_katalog', name: 'jenis_katalog', orderable: true, searchable: true },
            { data: 'title', name: 'title', orderable: true, searchable: true },
            { data: 'no_klasifikasi', name: 'no_klasifikasi', orderable: true, searchable: true },
            { data: 'no_katalog', name: 'no_katalog', orderable: true, searchable: true },
            { data: 'barcode', name: 'barcode', orderable: true, searchable: true },
            { data: 'sos_filename', name: 'sos_filename', orderable: true, searchable: true }
        ],
        responsive: false,
        scrollX: true,
    });

    let barcodebelumsoTable = $('#barcodebelumsoTable').DataTable({
        ajax: {
            url: '{{ url('catalog/stockopname/detail') }}/' + id + '/dt_barcodebelumso',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.knowledge_type_id = $('#jeniskatalogbelumso').val();
                d.status = $('#statusopenlibbelumso').val();
                d.item_location_id = $('#lokasiopenlibbelumso').val();
                d.classification_start = $('#klasifikasiAwal').val();
                d.classification_end = $('#klasifikasiAkhir').val();
            }
        },
        columns: [
            { data: 'row_number', name: 'row_number', orderable: true, searchable: false },
            { data: 'lokasi_openlibrary', name: 'lokasi_openlibrary', orderable: true, searchable: true },
            { data: 'jenis_katalog', name: 'jenis_katalog', orderable: true, searchable: true },
            { data: 'title', name: 'title', orderable: true, searchable: true },
            { data: 'author', name: 'author', orderable: true, searchable: true },
            { data: 'no_klasifikasi', name: 'no_klasifikasi', orderable: true, searchable: true },
            { data: 'no_katalog', name: 'no_katalog', orderable: true, searchable: true },
            { data: 'barcode', name: 'barcode', orderable: true, searchable: true },
            { data: 'formatted_status_openlibbelumso', name: 'status_openlib', orderable: true, searchable: true }
        ],
        responsive: false,
        scrollX: true,
    });

    let statistikbarcodebelumsoTable = $('#statistikbarcodebelumsoTable').DataTable({
        ajax: {
            url: '{{ url('catalog/stockopname/detail') }}/' + id + '/dt_statistikbarcodebelumso',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.entrance_date = $('#bs-rangepicker-dropdown-belumso').val(); // Sesuaikan ID
                d.item_location_id = $('#lokasiopenlib-belumso').val(); // Sesuaikan ID
            }
        },
        columns: [
            { data: 'name' },
            { data: 'judul' },
            { data: 'judul1' },
            { data: 'eksemplar1' },
            { data: 'judul2' },
            { data: 'eksemplar2' },
            { data: 'judul3' },
            { data: 'eksemplar3' },
            { data: 'judul4' },
            { data: 'eksemplar4' },
            { data: 'judul5' },
            { data: 'eksemplar5' },
            { data: 'judul6' },
            { data: 'eksemplar6' },
            { data: 'judul7' },
            { data: 'eksemplar7' },
            { data: 'judul8' },
            { data: 'eksemplar8' },
            { data: 'judul9' },
            { data: 'eksemplar9' }
        ],
        paging: false,
        responsive: false,
        scrollX: true,
    });

    let statistikbarcodesudahsoTable = $('#statistikbarcodesudahsoTable').DataTable({
        ajax: {
            url: '{{ url('catalog/stockopname/detail') }}/' + id + '/dt_statistikbarcodesudahso',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.entrance_date = $('#bs-rangepicker-dropdown-sudahso').val(); // Sesuaikan ID
                d.item_location_id = $('#lokasiopenlib-sudahso').val(); // Sesuaikan ID
                d.show_status = $('#show_status').val();
            }
        },
        columns: [
            { data: 'name' },
            { data: 'judul' },
            { data: 'judul1' },
            { data: 'eksemplar1' },
            { data: 'judul2' },
            { data: 'eksemplar2' },
            { data: 'judul3' },
            { data: 'eksemplar3' },
            { data: 'judul4' },
            { data: 'eksemplar4' },
            { data: 'judul5' },
            { data: 'eksemplar5' },
            { data: 'judul6' },
            { data: 'eksemplar6' },
            { data: 'judul7' },
            { data: 'eksemplar7' },
            { data: 'judul8' },
            { data: 'eksemplar8' },
            { data: 'judul9' },
            { data: 'eksemplar9' }
        ],
        paging: false,
        responsive: false,
        scrollX: true,
    });

    $('#jeniskatalog, #lokasiopenlib, #lokasiso, #statusopenlib, #statusso, #status, #user').on('change', function() {
        mainTable.ajax.reload();
    });

    $('#jeniskatalogduplicate, #statusopenlibduplicate').on('change', function() {
        barcodeduplicateTable.ajax.reload();
    });

    $('#filterNoKlasifikasi').on('click', function() {
        barcodebelumsoTable.ajax.reload();
    });

    $('#filterstatistikbarcodebelumso').on('click', function() {
        statistikbarcodebelumsoTable.ajax.reload();
    });

    $('#filterstatistikbarcodesudahso').on('click', function() {
        statistikbarcodesudahsoTable.ajax.reload();
    });

    $('#clearstatistikbarcodebelumso').on('click', function() {
        // Kosongkan nilai input range date
        $('#bs-rangepicker-dropdown-belumso').val('');

        // Reload DataTable tanpa filter tanggal
        statistikbarcodebelumsoTable.ajax.reload();
    });

    $('#clearstatistikbarcodesudahso').on('click', function() {
        // Kosongkan nilai input range date
        $('#bs-rangepicker-dropdown-sudahso').val('');

        // Reload DataTable tanpa filter tanggal
        statistikbarcodesudahsoTable.ajax.reload();
    });
});

const buttonHandlers = {
    '#btnImporDataSo': 'impordataModal',
    '#btnInsertManualDataSo': 'insertmanualdataModal',
    '#btnBarcodeDuplicate': 'barcodeduplicateModal',
    '#btnBarcodeBelumAda': 'barcodebelumsoModal',
    '#btnStatistikBelumSO': 'statistikbarcodebelumsoModal',
    '#btnStatistikSudahSO': 'statistikbarcodesudahsoModal'
};

Object.entries(buttonHandlers).forEach(([buttonId, modalId]) => {
    $(buttonId).on('click', () => {
        $(`#${modalId}`).modal('show');
    });
});

function deleteData(id) {
    yswal_delete.fire({
        title: "{{ __('common.message_delete_prompt_title') }}",
        text: "{{ __('common.message_delete_prompt_text') }}"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ url('catalog/stockopname/detail') }}/' + id + '/delete',
                data: { id : id },
                type: 'delete',
                dataType: 'json',
                success: function(e) {
                    if (e.status == 'success') {
                        $('#table').DataTable().ajax.reload();
                        toastr.success("{{ __('common.message_delete_title') }}", "{{ __('common.message_success_delete') }}", toastrOptions)
                    }
                }
            });
        }
    })
}

$(document).ready(function() {
    $('#btnHapusSemuaData').on('click', function() {
        yswal_delete.fire({
            title: "{{ __('common.message_delete_prompt_title') }}",
            text: "{{ __('common.message_delete_prompt_text_user') }} {{ $currentUser->master_data_user }} ?",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url('catalog/stockopname/detail') }}/' + id + '/delete_all',
                    type: 'DELETE',
                    data: { sos_id_so: id }, // Kirim sos_id_so
                    dataType: 'json',
                    success: function(e) {
                        if (e.status == 'success') {
                            $('#table').DataTable().ajax.reload();
                            toastr.success("{{ __('common.message_delete_title') }}", "{{ __('common.message_success_delete') }}", toastrOptions);
                        } else if (e.status == 'error' && e.message === 'No data found to delete') {
                            toastr.warning("{{ __('common.message_warning_title') }}", "{{ __('common.message_no_data_found') }}", toastrOptions);
                        } else {
                            toastr.error("{{ __('common.message_error_title') }}", "{{ __('common.message_error_delete') }}", toastrOptions);
                        }
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status === 404) {
                            toastr.warning("{{ __('common.message_warning_title') }}", "{{ __('common.message_no_data_found') }}", toastrOptions);
                        } else {
                            toastr.error("{{ __('common.message_error_title') }}", "{{ __('common.message_error_delete') }}", toastrOptions);
                        }
                    }
                });
            }
        });
    });
});

$(document).ready(function () {
    $('#barcode').on('keypress', function (e) {
        if (e.which === 13) { // Tombol Enter
            e.preventDefault();
            processBarcode();
            $('#barcode').val(''); // Reset setelah tombol enter
        }
    });

    $('#insertmanualdataModal').on('hidden.bs.modal', function () {
        resetModal();
    });
});

function processBarcode() {
    var id = $('#id').val();
    var barcode = $('#barcode').val();
    var formData = $("#form_manual").serialize();

    if (!barcode) {
        $('#result').text('Barcode tidak boleh kosong.');
        return;
    }

    $.ajax({
        url: '{{ url('catalog/stockopname/detail') }}/' + id + '/check_barcode',
        type: 'POST',
        data: { barcode: barcode, _token: '{{ csrf_token() }}' },
        dataType: 'JSON',
        success: function (data) {
            var currentResult = $('#result').text();
            var newResult = currentResult + '\n' + data.message; // Menambahkan hasil baru
            $('#result').text(newResult); // Tampilkan hasil pengecekan barcode di result

            if (data.success) {
                $('#barcode').val('');

                // Lanjutkan untuk menyimpan data jika barcode valid
                saveManual(formData, newResult);
            }
        },
        error: function () {
            $('#result').text('Terjadi kesalahan saat memeriksa barcode.');
        }
    });
}

function saveManual(formData, barcodeMessage) {
    var id = $('#id').val();

    $.ajax({
        url: '{{ url('catalog/stockopname/detail') }}/' + id + '/save_manual',
        type: 'POST',
        data: formData,
        dataType: 'JSON',
        success: function (data) {
            $('#result').text(barcodeMessage);
            $('#barcode').val(''); // Reset kolom barcode setelah penyimpanan
            $('#table').DataTable().ajax.reload();
        },
        error: function () {
            $('#result').text('Terjadi kesalahan saat menyimpan data.');
        }
    });
}

function saveImage() {
    var formData = new FormData($('#impordataModal #form')[0]);
    $.ajax({
        url: '{{ url('catalog/stockopname/detail') }}/' + id + '/save_image',
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        dataType: "JSON",
        success: function(response) {
            if (response.status === 'success') {
                let htmlResultAda = '';
                let htmlResultTidakAda = '';
                let htmlDuplicateInfo = '';

                if (response.duplicateInfo && response.duplicateInfo.length > 0) {
                    htmlDuplicateInfo = `<p><strong>Informasi Duplikat:</strong></p><ul style="list-style-type: none; padding-left: 0;">`;
                    response.duplicateInfo.forEach(info => {
                        htmlDuplicateInfo += `<li>${info}</li>`;
                    });
                    htmlDuplicateInfo += `</ul>`;
                }

                // Pastikan elemen <ul> ada, jika belum ada, tambahkan
                if ($('#listDataAda').length === 0) {
                    $('#result2').append(`
                        <p class="dataAda"><strong>Data ada di database:</strong></p>
                        <ul id="listDataAda" style="list-style-type: none; padding-left: 0;"></ul>
                    `);
                }
                if ($('#listDataTidakAda').length === 0) {
                    $('#result2').append(`
                        <p class="dataTidakAda"><strong>Data tidak ada di database:</strong></p>
                        <ul id="listDataTidakAda" style="list-style-type: none; padding-left: 0;"></ul>
                    `);
                }

                // Tambahkan data sesuai kategori dengan nomor urut
                response.dataAda.forEach((item, index) => {
                    htmlResultAda += `<li style="margin-bottom: 5px;">${item.index}. ${item.code}</li>`;
                });
                response.dataTidakAda.forEach((item, index) => {
                    htmlResultTidakAda += `<li style="margin-bottom: 5px;">${item.index}. ${item.code}</li>`;
                });

                // Append ke dalam list yang sesuai
                $('#listDataAda').html(htmlResultAda); // Gunakan .html() untuk mengganti isi list
                $('#listDataTidakAda').html(htmlResultTidakAda); // Gunakan .html() untuk mengganti isi list

                // Tampilkan informasi duplikat
                $('#result2').prepend(htmlDuplicateInfo);

                $('#table').DataTable().ajax.reload();
            } else if (response.status === 'error') {
                toastr.warning(response.message, "Peringatan", toastrOptions);
            }

            // Hapus input file agar bisa diinput ulang
            $('#file').val('');
        },
        error: function(xhr) {
            var errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan saat memproses permintaan.';
            toastr.error(errorMessage, "Error", toastrOptions);
        }
    });
}

$('#impordataModal').on('hidden.bs.modal', function () {
    $('#form')[0].reset();
    $('#result2').html('');
});

function resetModal() {
    $("#form_manual")[0].reset();

    $("#sos_status").val("").trigger('change');
    $("#sos_id_location").val("").trigger('change');
    $("#sos_filename").val("");
    $("#barcode").val("");
    $("#result").text("");
}

document.addEventListener('DOMContentLoaded', function () {
    $('#bs-rangepicker-dropdown-belumso').daterangepicker({
        opens: 'left',
        drops: 'down',
        autoApply: true,
        locale: {
            format: 'YYYY-MM-DD',
            separator: ' - ',
            applyLabel: 'Apply',
            cancelLabel: 'Cancel',
            fromLabel: 'From',
            toLabel: 'To',
            customRangeLabel: 'Custom',
            weekLabel: 'W',
            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            firstDay: 1
        }
    });

    $('#bs-rangepicker-dropdown-sudahso').daterangepicker({
        opens: 'left',
        drops: 'down',
        autoApply: true,
        locale: {
            format: 'YYYY-MM-DD',
            separator: ' - ',
            applyLabel: 'Apply',
            cancelLabel: 'Cancel',
            fromLabel: 'From',
            toLabel: 'To',
            customRangeLabel: 'Custom',
            weekLabel: 'W',
            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            firstDay: 1
        }
    });
});

</script>
@endsection
