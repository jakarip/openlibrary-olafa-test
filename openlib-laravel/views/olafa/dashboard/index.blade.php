@extends('layouts/layoutMaster')

@section('title', 'Dashboard OLAFA')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
@endsection

@section('page-style')
<style>
</style>
@endsection

@section('content')
<div class="card mb-3">
    <!--Search Form -->
    <p class="card-header">Advanced Search</p>
    <div class="card-body">
        <form class="dt_adv_search">
            <div class="row">
                <div class="col-12">
                    <div class="row g-3">
                        <p class="fs-4 col-lg-2 d-flex align-items-end">Pilih Tahun</p>
                        <div class="col-12 col-sm-6 col-lg-3 mt-2">
                            {{-- <label for="bs-datepicker-daterange" class="form-label">Pilih Tanggal:</label> --}}
                            <select  id="year" class="form-select select2" >
                                @php
                                    $last = date('Y') - 10;
                                    $now = date('Y');
                                @endphp
                                @for ($i = $now; $i >= $last; $i--)
                                    <option value="{{ $i }}" >{{ $i }}</option>
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

<div class="col-12  mb-4 order-1 order-lg-0">
    <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="card-title m-0">
                    <h5 class="mb-0">Koleksi</h5>
                    <small class="text-muted ">Total Koleksi per Bulan</small>
                </div>
                
            </div>
            <div class="card-body">
                <div id="koleksiChart"></div>
        </div>
    </div>
</div>

<div class="col-12  mb-4 order-1 order-lg-0">
    <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="card-title m-0">
                    <h5 class="mb-0">Anggota</h5>
                    <small class="text-muted ">Total Anggota per Bulan</small>
                </div>
                
            </div>
            <div class="card-body">
                <div id="anggotaChart"></div>
        </div>
    </div>
</div>

<div class="col-12  mb-4 order-1 order-lg-0">
    <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="card-title m-0">
                    <h5 class="mb-0">Pengunjung/Akses</h5>
                    <small class="text-muted ">Total Pengunjung/Akses per Bulan</small>
                </div>
                
            </div>
            <div class="card-body">
                <div id="pengunjungChart"></div>
        </div>
    </div>
</div>

<div class="col-12  mb-4 order-1 order-lg-0">
    <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="card-title m-0">
                    <h5 class="mb-0">Layanan</h5>
                    <small class="text-muted ">Total Layanan per Bulan</small>
                </div>
                
            </div>
            <div class="card-body">
                <div id="layananChart"></div>
        </div>
    </div>
</div>

@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
@endsection

@section('page-script')
<script>

let dTable = null;
let url = '{{ url('olafa/dashboard') }}';

let cardColor, headingColor, legendColor, labelColor, borderColor;
    if (isDarkStyle) {
        cardColor = config.colors_dark.cardColor;
        labelColor = config.colors_dark.textMuted;
        legendColor = config.colors_dark.bodyColor;
        headingColor = config.colors_dark.headingColor;
        borderColor = config.colors_dark.borderColor;
    } else {
        cardColor = config.colors.cardColor;
        labelColor = config.colors.textMuted;
        legendColor = config.colors.bodyColor;
        headingColor = config.colors.headingColor;
        borderColor = config.colors.borderColor;
    }

// Chart Colors
const chartColors = {
        donut: {
        series1: config.colors.success,
        series2: '#4fddaa',
        series3: '#8ae8c7',
        series4: '#c4f4e3'
        },
        bar: {
        series1: config.colors.primary,
        series2: '#7367F0CC',
        series3: '#7367f099'
        }
};

$(document).ready(function() {

    function sendAjaxRequest() {
        $.ajax({
            url: url + '/ajax',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                year: $('#year').val()
            },
            success: function(data) {
                renderChartKoleksi(data.grafik);
                renderChartAnggota(data.grafik);
                renderChartPengunjung(data.grafik);
                renderChartLayanan(data.grafik);
            },
            error: function(xhr) {
                // Handle the error here
                // console.error(xhr.responseText);
            }
        });
    }


    $('#show-date-btn').on('click', function(event) {
        event.preventDefault();
        sendAjaxRequest();
        // dTable.ajax.reload();
    });

    function renderChartKoleksi(data) {
    
        var koleksiLastYearData = data.koleksi.lastyear;
        var koleksiYearData = data.koleksi.year;

        var judulLastYearData = data.judul.lastyear;
        var judulYearData = data.judul.year;

        var judulDigitalLastYearData = data.judul_digital.lastyear;
        var judulDigitalYearData = data.judul_digital.year;

        // console.log(koleksiLastYearData);
        // console.log(koleksiYearData);

        var options = {
            chart: {
                height: 350,
                type: 'bar',
                parentHeightOffset: 0,
                stacked: false,
                toolbar: {
                show: false
                },
                zoom: {
                enabled: false
                }
            },
            colors: [chartColors.bar.series1, chartColors.bar.series2],
            fill: {
                opacity: 1
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '50%',
                    barHeight: '70%',
                    barGap: '10%',
                    startingShape: 'rounded',
                    endingShape: 'flat',
                    borderRadius: 4,
                    dataLabels: {
                        position: 'top'
                    },
                }
            },
            grid: {
                strokeDashArray: 6,
                padding: {
                bottom: 5
                }
            },
            dataLabels: {
                enabled: false,
                formatter: function (val) {
                    if (val === 0) {
                        return '';
                    } else if (val > 10000) {
                        return parseInt(val / 1000) + 'k';
                    } else {
                        return val;
                    }
                },
                offsetY: -20,
                style: {
                fontSize: '15px',
                colors: [legendColor],
                fontWeight: '500',
                fontFamily: 'Public Sans'
                }
            },
            series: [
                {
                    name: 'Koleksi Fisik',
                    data: koleksiLastYearData
                },{
                    name: 'Koleksi Fisik',
                    data: koleksiYearData
                },
                {
                    name: 'Koleksi Digital',
                    data: koleksiLastYearData
                },{
                    name: 'Koleksi Digital',
                    data: koleksiYearData
                },
                {
                    name: 'Judul Fisik',
                    data: judulLastYearData
                },{
                    name: 'Judul Fisik',
                    data: judulYearData
                },
                {
                    name: 'Judul Digital',
                    data: judulDigitalLastYearData
                },{
                    name: 'Judul Digital',
                    data: judulDigitalYearData
                },
            ],
            legend: {
                show: true,
                position: 'bottom',
                markers: {
                    width: 8,
                    height: 8,
                    offsetX: -3,
                    radius: 12
                },
                height: 40,
                offsetY: 0,
                itemMargin: {
                    horizontal: 10,
                    vertical: 0
                },
                fontSize: '13px',
                fontFamily: 'Public Sans',
                fontWeight: 400,
                labels: {
                    colors: headingColor,
                    useSeriesColors: false
                },
                offsetY: 10
            },
            // tooltip: {
            //     enabled: false
            // },
            xaxis: {
                tickAmount: 10,
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: '13px',
                        fontFamily: 'Public Sans',
                        fontWeight: 400
                    }
                },
                    axisBorder: {
                    show: false
                },
                    axisTicks: {
                    show: false
                }
            },
            yaxis: {
                tickAmount: 4,
                // min: 1,
                // max: 5,
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: '13px',
                        fontFamily: 'Public Sans',
                        fontWeight: 400
                    },
                    formatter: function (val) {
                        return val;
                    }
                }
            },
            responsive: [
                {
                breakpoint: 1400,
                options: {
                    chart: {
                    height: 275
                    },
                    legend: {
                    fontSize: '13px',
                    offsetY: 10
                    }
                }
                },
                {
                breakpoint: 576,
                options: {
                    chart: {
                    height: 300
                    },
                    legend: {
                    itemMargin: {
                        vertical: 5,
                        horizontal: 10
                    },
                    offsetY: 7
                    }
                }
                }
            ]
        };

        var chart = new ApexCharts(document.querySelector(`#koleksiChart`), options);
        chart.render();
    }

    function renderChartAnggota(data) {
        
        // Civitas (Web & Mobile) civitas_webmobile
        // Civitas (Web) civitas_web
        // Umum (Web & Mobile) umum_webmobile
        // Umum (Web) umum_web

        var civitasWebMobileLastYearData = data.civitas_webmobile.lastyear;
        var civitasWebMobileCurrentYearData = data.civitas_webmobile.year;

        var civitasWebLastYearData = data.civitas_web.lastyear;
        var civitasWebCurrentYearData = data.civitas_web.year;

        var umumWebMobileLastYearData = data.umum_webmobile.lastyear;
        var umumWebMobileCurrentYearData = data.umum_webmobile.year;

        var umumWebLastYearData = data.umum_web.lastyear;
        var umumWebCurrentYearData = data.umum_web.year;

        // console.log(koleksiLastYearData);
        // console.log(koleksiYearData);

        var options = {
            chart: {
                height: 350,
                type: 'bar',
                parentHeightOffset: 0,
                stacked: false,
                toolbar: {
                show: false
                },
                zoom: {
                enabled: false
                }
            },
            colors: [chartColors.bar.series1, chartColors.bar.series2],
            fill: {
                opacity: 1
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '50%',
                    barHeight: '70%',
                    barGap: '10%',
                    startingShape: 'rounded',
                    endingShape: 'flat',
                    borderRadius: 4,
                    dataLabels: {
                        position: 'top'
                    },
                }
            },
            grid: {
                strokeDashArray: 6,
                padding: {
                bottom: 5
                }
            },
            dataLabels: {
                enabled: false,
                formatter: function (val) {
                    if (val === 0) {
                        return '';
                    } else if (val > 10000) {
                        return parseInt(val / 1000) + 'k';
                    } else {
                        return val;
                    }
                },
                offsetY: -20,
                style: {
                fontSize: '15px',
                colors: [legendColor],
                fontWeight: '500',
                fontFamily: 'Public Sans'
                }
            },
            series: [
                {
                    name: 'Civitas Web Mobile Last Year',
                    data: civitasWebMobileLastYearData
                }, {
                    name: 'Civitas Web Mobile Current Year',
                    data: civitasWebMobileCurrentYearData
                }, {
                    name: 'Civitas Web Last Year',
                    data: civitasWebLastYearData
                }, {
                    name: 'Civitas Web Current Year',
                    data: civitasWebCurrentYearData
                }, {
                    name: 'Umum Web Mobile Last Year',
                    data: umumWebMobileLastYearData
                }, {
                    name: 'Umum Web Mobile Current Year',
                    data: umumWebMobileCurrentYearData
                }, {
                    name: 'Umum Web Last Year',
                    data: umumWebLastYearData
                }, {
                    name: 'Umum Web Current Year',
                    data: umumWebCurrentYearData
                },
            ],
            legend: {
                show: true,
                position: 'bottom',
                markers: {
                    width: 8,
                    height: 8,
                    offsetX: -3,
                    radius: 12
                },
                height: 40,
                offsetY: 0,
                itemMargin: {
                    horizontal: 10,
                    vertical: 0
                },
                fontSize: '13px',
                fontFamily: 'Public Sans',
                fontWeight: 400,
                labels: {
                    colors: headingColor,
                    useSeriesColors: false
                },
                offsetY: 10
            },
            // tooltip: {
            //     enabled: false
            // },
            xaxis: {
                tickAmount: 10,
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: '13px',
                        fontFamily: 'Public Sans',
                        fontWeight: 400
                    }
                },
                    axisBorder: {
                    show: false
                },
                    axisTicks: {
                    show: false
                }
            },
            yaxis: {
                tickAmount: 4,
                // min: 1,
                // max: 5,
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: '13px',
                        fontFamily: 'Public Sans',
                        fontWeight: 400
                    },
                    formatter: function (val) {
                        return val;
                    }
                }
            },
            responsive: [
                {
                breakpoint: 1400,
                options: {
                    chart: {
                    height: 275
                    },
                    legend: {
                    fontSize: '13px',
                    offsetY: 10
                    }
                }
                },
                {
                breakpoint: 576,
                options: {
                    chart: {
                    height: 300
                    },
                    legend: {
                    itemMargin: {
                        vertical: 5,
                        horizontal: 10
                    },
                    offsetY: 7
                    }
                }
                }
            ]
        };

        var chart = new ApexCharts(document.querySelector(`#anggotaChart`), options);
        chart.render();
    }

    function renderChartPengunjung(data) {
        
        // Civitas (Web & Mobile) civitas_webmobile
        // Civitas (Web) civitas_web
        // Umum (Web & Mobile) umum_webmobile
        // Umum (Web) umum_web

        var pengunjungLastYearData = data.pengunjung.lastyear;
        var pengunjungCurrentYearData = data.pengunjung.year;

        // console.log(koleksiLastYearData);
        // console.log(koleksiYearData);

        var options = {
            chart: {
                height: 350,
                type: 'bar',
                parentHeightOffset: 0,
                stacked: false,
                toolbar: {
                show: false
                },
                zoom: {
                enabled: false
                }
            },
            colors: [chartColors.bar.series1, chartColors.bar.series2],
            fill: {
                opacity: 1
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '50%',
                    barHeight: '70%',
                    barGap: '10%',
                    startingShape: 'rounded',
                    endingShape: 'flat',
                    borderRadius: 4,
                    dataLabels: {
                        position: 'top'
                    },
                }
            },
            grid: {
                strokeDashArray: 6,
                padding: {
                bottom: 5
                }
            },
            dataLabels: {
                enabled: false,
                formatter: function (val) {
                    if (val === 0) {
                        return '';
                    } else if (val > 10000) {
                        return parseInt(val / 1000) + 'k';
                    } else {
                        return val;
                    }
                },
                offsetY: -20,
                style: {
                fontSize: '15px',
                colors: [legendColor],
                fontWeight: '500',
                fontFamily: 'Public Sans'
                }
            },
            series: [
                {
                    name: 'Pengunjung Last Year',
                    data: pengunjungLastYearData
                }, 
                {
                    name: 'Pengunjung Current Year',
                    data: pengunjungCurrentYearData
                }, 
            ],
            legend: {
                show: true,
                position: 'bottom',
                markers: {
                    width: 8,
                    height: 8,
                    offsetX: -3,
                    radius: 12
                },
                height: 40,
                offsetY: 0,
                itemMargin: {
                    horizontal: 10,
                    vertical: 0
                },
                fontSize: '13px',
                fontFamily: 'Public Sans',
                fontWeight: 400,
                labels: {
                    colors: headingColor,
                    useSeriesColors: false
                },
                offsetY: 10
            },
            // tooltip: {
            //     enabled: false
            // },
            xaxis: {
                tickAmount: 10,
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: '13px',
                        fontFamily: 'Public Sans',
                        fontWeight: 400
                    }
                },
                    axisBorder: {
                    show: false
                },
                    axisTicks: {
                    show: false
                }
            },
            yaxis: {
                tickAmount: 4,
                // min: 1,
                // max: 5,
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: '13px',
                        fontFamily: 'Public Sans',
                        fontWeight: 400
                    },
                    formatter: function (val) {
                        return val;
                    }
                }
            },
            responsive: [
                {
                breakpoint: 1400,
                options: {
                    chart: {
                    height: 275
                    },
                    legend: {
                    fontSize: '13px',
                    offsetY: 10
                    }
                }
                },
                {
                breakpoint: 576,
                options: {
                    chart: {
                    height: 300
                    },
                    legend: {
                    itemMargin: {
                        vertical: 5,
                        horizontal: 10
                    },
                    offsetY: 7
                    }
                }
                }
            ]
        };

        var chart = new ApexCharts(document.querySelector(`#pengunjungChart`), options);
        chart.render();
    }
    function renderChartLayanan(data) {
        

        var peminjamanLastYearData = data.peminjaman.lastyear;
        var peminjamanCurrentYearData = data.peminjaman.year;

        var pengembalianLastYearData = data.pengembalian.lastyear;
        var pengembalianCurrentYearData = data.pengembalian.year;

        var ruanganLastYearData = data.ruangan.lastyear;
        var ruanganCurrentYearData = data.ruangan.year;

        var bdsLastYearData = data.bds.lastyear;
        var bdsCurrentYearData = data.bds.year;

        var usulanLastYearData = data.usulan.lastyear;
        var usulanCurrentYearData = data.usulan.year;

        var sbkpLastYearData = data.sbkp.lastyear;
        var sbkpCurrentYearData = data.sbkp.year;

        var options = {
            chart: {
                height: 350,
                type: 'bar',
                parentHeightOffset: 0,
                stacked: false,
                toolbar: {
                show: false
                },
                zoom: {
                enabled: false
                }
            },
            colors: [chartColors.bar.series1, chartColors.bar.series2],
            fill: {
                opacity: 1
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '50%',
                    barHeight: '70%',
                    barGap: '10%',
                    startingShape: 'rounded',
                    endingShape: 'flat',
                    borderRadius: 4,
                    dataLabels: {
                        position: 'top'
                    },
                }
            },
            grid: {
                strokeDashArray: 6,
                padding: {
                bottom: 5
                }
            },
            dataLabels: {
                enabled: false,
                formatter: function (val) {
                    if (val === 0) {
                        return '';
                    } else if (val > 10000) {
                        return parseInt(val / 1000) + 'k';
                    } else {
                        return val;
                    }
                },
                offsetY: -20,
                style: {
                fontSize: '15px',
                colors: [legendColor],
                fontWeight: '500',
                fontFamily: 'Public Sans'
                }
            },
            series: [
                {
                    name: 'Peminjaman Last Year',
                    data: peminjamanLastYearData
                }, {
                    name: 'Peminjaman Current Year',
                    data: peminjamanCurrentYearData
                }, {
                    name: 'Pengembalian Last Year',
                    data: pengembalianLastYearData
                }, {
                    name: 'Pengembalian Current Year',
                    data: pengembalianCurrentYearData
                }, {
                    name: 'Ruangan Last Year',
                    data: ruanganLastYearData
                }, {
                    name: 'Ruangan Current Year',
                    data: ruanganCurrentYearData
                }, {
                    name: 'BDS Last Year',
                    data: bdsLastYearData
                }, {
                    name: 'BDS Current Year',
                    data: bdsCurrentYearData
                }, {
                    name: 'Usulan Last Year',
                    data: usulanLastYearData
                }, {
                    name: 'Usulan Current Year',
                    data: usulanCurrentYearData
                }, {
                    name: 'SBKP Last Year',
                    data: sbkpLastYearData
                }, {
                    name: 'SBKP Current Year',
                    data: sbkpCurrentYearData
                }
            ],
            legend: {
                show: true,
                position: 'bottom',
                markers: {
                    width: 8,
                    height: 8,
                    offsetX: -3,
                    radius: 12
                },
                height: 40,
                offsetY: 0,
                itemMargin: {
                    horizontal: 10,
                    vertical: 0
                },
                fontSize: '13px',
                fontFamily: 'Public Sans',
                fontWeight: 400,
                labels: {
                    colors: headingColor,
                    useSeriesColors: false
                },
                offsetY: 10
            },
            // tooltip: {
            //     enabled: false
            // },
            xaxis: {
                tickAmount: 10,
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: '13px',
                        fontFamily: 'Public Sans',
                        fontWeight: 400
                    }
                },
                    axisBorder: {
                    show: false
                },
                    axisTicks: {
                    show: false
                }
            },
            yaxis: {
                tickAmount: 4,
                // min: 1,
                // max: 5,
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: '13px',
                        fontFamily: 'Public Sans',
                        fontWeight: 400
                    },
                    formatter: function (val) {
                        return val;
                    }
                }
            },
            responsive: [
                {
                breakpoint: 1400,
                options: {
                    chart: {
                    height: 275
                    },
                    legend: {
                    fontSize: '13px',
                    offsetY: 10
                    }
                }
                },
                {
                breakpoint: 576,
                options: {
                    chart: {
                    height: 300
                    },
                    legend: {
                    itemMargin: {
                        vertical: 5,
                        horizontal: 10
                    },
                    offsetY: 7
                    }
                }
                }
            ]
        };

        var chart = new ApexCharts(document.querySelector(`#layananChart`), options);
        chart.render();
    }
});

</script>
@endsection