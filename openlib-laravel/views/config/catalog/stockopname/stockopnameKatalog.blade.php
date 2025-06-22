@extends('layouts/layoutMaster')

@section('title', __('catalogs.stock_add_referenceweeding'))

@section('vendor-style')
@endsection

@section('page-style')
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form class="dt_adv_search">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-auto">
                    <div class="row g-2">
                        <div class="col">
                            <label for="klasifikasiAwal" class="form-label">{{ __('catalogs.stock_weeding_filter_Initial') }}</label>
                            <input type="text" id="klasifikasiAwal" class="form-control">
                        </div>
                        <div class="col">
                            <label for="klasifikasiAkhir" class="form-label">{{ __('catalogs.stock_weeding_filter_final') }}</label>
                            <input type="text" id="klasifikasiAkhir" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-auto">
                    <div class="row g-2">
                        <div class="col">
                            <label for="tahunawal" class="form-label">{{ __('catalogs.stock_weeding_filter_Initial_year') }}</label>
                            <input type="text" id="tahunawal" class="form-control">
                        </div>
                        <div class="col">
                            <label for="tahunakhir" class="form-label">{{ __('catalogs.stock_weeding_filter_final_year') }}</label>
                            <input type="text" id="tahunakhir" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-auto">
                    <label for="terakhirpinjam" class="form-label">{{ __('catalogs.stock_weeding_filter_notborrowed') }}</label>
                    <select id="terakhirpinjam" name="terakhirpinjam" class="form-select">
                        <?php
                        for ($i = 1; $i <= 10; $i++) {
                            echo "<option value='$i'>$i " . __('common.last_year') . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="row mt-3 g-3">
                <div class="col-12 col-sm-6 col-lg-3">
                    <label for="status" class="form-label">{{ __('catalogs.stock_weeding_filter_status_openlib') }}</label>
                    <select id="status" class="form-select">
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

                <div class="col-12 col-sm-6 col-lg-3">
                    <label for="lokasiopenlib" class="form-label">{{ __('catalogs.stock_weeding_filter_location_openlib') }}</label>
                    <select id="lokasiopenlib" class="form-select">
                        <option value="">{{ __('common.all') }}</option>
                        @foreach ($locations as $location)
                        <option value="{{ $location->id }}" {{ $location->id == $selectedLocationId ? 'selected' : '' }}>
                            {{ $location->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <label for="filterNoKlasifikasi" class="form-label invisible"></label>
                    <button type="button" class="btn btn-info w-100" id="filterNoKlasifikasi"><i class="ti ti-filter ti-sm me-1"></i> Filter</button>
                </div>
            </div>
        </form>
    </div>

    <hr class="mt-0">

    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table border-top" id="table">
            <thead>
                <tr class="text-nowrap">
                    <th>{{ __('catalogs.stock_table_number') }}</th>
                    <th>{{ __('catalogs.stock_table_catalogtype') }}</th>
                    <th>{{ __('catalogs.stock_table_title') }}</th>
                    <th>{{ __('catalogs.stock_table_classificationnumber') }}</th>
                    <th>{{ __('catalogs.stock_table_author') }}</th>
                    <th>{{ __('catalogs.stock_table_publicyear') }}</th>
                    <th>{{ __('catalogs.stock_table_catalognumber') }}</th>
                    <th>{{ __('catalogs.stock_table_barcode') }}</th>
                    <th>{{ __('catalogs.stock_weeding_filter_location_openlib') }}</th>
                    <th>{{ __('catalogs.stock_status') }}</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('vendor-script')
@endsection

@section('page-script')
<script>
let dTable = null;
let url = '{{ url('catalog/stockopname/katalog') }}';

$(function() {
    dTable = $('#table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: url+'/dt',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.item_location_id = $('#lokasiopenlib').val();
                d.status = $('#status').val();
                d.classification_start = $('#klasifikasiAwal').val();
                d.classification_end = $('#klasifikasiAkhir').val();
                d.year_start = $('#tahunawal').val();
                d.year_end = $('#tahunakhir').val();
                d.terakhirpinjam = $('#terakhirpinjam').val();
            }
        },
        columns: [
            { data: 'row_number', name: 'row_number', orderable: false, searchable: false },
            { data: 'jenis_katalog', name: 'jenis_katalog', orderable: true, searchable: true },
            { data: 'title', name: 'title', orderable: true, searchable: true },
            { data: 'no_klasifikasi', name: 'no_klasifikasi', orderable: true, searchable: true },
            { data: 'author', name: 'author', orderable: true, searchable: true },
            { data: 'published_year', name: 'published_year', orderable: true, searchable: true },
            { data: 'no_katalog', name: 'no_katalog', orderable: true, searchable: true },
            { data: 'barcode', name: 'barcode', orderable: true, searchable: true },
            { data: 'lokasi_openlibrary', name: 'lokasi_openlibrary', orderable: true, searchable: true },
            { data: 'status_openlib', name: 'status_openlib', orderable: true, searchable: true }
        ],
        responsive: false,
        scrollX: true,
    });

    $('#filterNoKlasifikasi').on('click', function() {
        dTable.ajax.reload();
    });
});

</script>
@endsection
