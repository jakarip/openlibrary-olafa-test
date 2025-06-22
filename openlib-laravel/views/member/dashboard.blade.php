@extends('layouts.layoutMaster')

@section('title', 'Dashboard OpenLibrary Membership')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
@endsection

@section('page-style')
    <style>
    </style>
@endsection

@section('content')
    <div class="card mb-3">
        <!-- Search Form -->
        <p class="card-header">Advanced Search</p>
        <div class="card-body">
            <form class="dt_adv_search">
                <div class="row">
                    <div class="col-12">
                        <div class="row g-3">
                            <p class="fs-4 col-lg-2 d-flex align-items-end">Pilih Tahun</p>
                            <div class="col-12 col-sm-6 col-lg-3 mt-2">
                                <select id="year" class="form-select select2">
                                    @php
                                        $last = date('Y') - 10;
                                        $now = date('Y');
                                    @endphp
                                    @for ($i = $now; $i >= $last; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-1 d-flex align-items-start col-auto mt-2">
                                <button id="show-date-btn" class="btn btn-primary">Cari</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @foreach (['Koleksi', 'Anggota', 'Pengunjung/Akses', 'Layanan'] as $chart)
        <div class="col-12 mb-4 order-1 order-lg-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="card-title m-0">
                        <h5 class="mb-0">{{ $chart }}</h5>
                        <small class="text-muted">Total {{ $chart }} per Bulan</small>
                    </div>
                </div>
                <div class="card-body">
                    <div id="{{ Str::slug($chart) }}Chart"></div>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function () {
            let url = '{{ url('member/dashboard') }}';

            $('#show-date-btn').on('click', function (event) {
                event.preventDefault();
                sendAjaxRequest();
            });

            function sendAjaxRequest() {
                $.ajax({
                    url: url + '/ajax',
                    type: 'post',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: { year: $('#year').val() },
                    success: function (data) {
                        renderChart('Koleksi', data.koleksi);
                        renderChart('Anggota', data.anggota);
                        renderChart('Pengunjung/Akses', data.pengunjung);
                        renderChart('Layanan', data.layanan);
                    }
                });
            }

            function renderChart(chartName, data) {
                let chartId = '#' + chartName.replace(/\s+/g, '') + 'Chart';
                var options = {
                    chart: { height: 350, type: 'bar', parentHeightOffset: 0, toolbar: { show: false } },
                    colors: ['#7367F0', '#28C76F'],
                    series: [{ name: chartName, data: data.year }],
                    xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] }
                };

                var chart = new ApexCharts(document.querySelector(chartId), options);
                chart.render();
            }
        });
    </script>
@endsection