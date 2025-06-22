@extends('layouts/layoutMaster')

@section('title', 'Laporan Utilisasi')

@section('vendor-style')
@endsection

@section('page-style')
<style>
</style>
@endsection

@section('content')

<div class="card mb-3">
    <h5 class="card-header">Advanced Search</h5>
        <!--Search Form -->
        <div class="card-body">
            <form class="dt_adv_search">
                <div class="row">
                    <div class="col-12">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label for="bs-datepicker-daterange" class="form-label">Pilih Tanggal:</label>
                                <input type="text" id="bs-datepicker-daterange" class="form-control" />
                            </div>

                            <div class="col-12 col-sm-6 col-lg-4">
                                <label for="prodi" class="form-label">Pilih Fakultas/Prodi:</label>
                                <select id="prodi" class="select2 form-select form-select-md">
                                    {{-- <option value="">Semua</option> --}}
                                    @php $a = ""; @endphp
                                    @foreach($prodi as $row)
                                        @if($a == "" || $a != $row->nama_fakultas)
                                            <option value="{{ $row->c_kode_fakultas }}" >
                                                {{ $row->nama_fakultas }}
                                            </option>
                                            @php $a = $row->nama_fakultas; @endphp
                                        @endif
                                        <option value="{{ $row->c_kode_fakultas }}-{{ $row->c_kode_prodi }}">
                                            Program Studi {{ $row->nama_prodi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-1 d-flex align-items-end col-auto">
                                <button id="show-date-btn" class="btn btn-primary">Cari</button>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- <hr class="mt-0"> --}}

       
  

    {{-- <div class="card-datatable text-wrap">
        <table class="table-bordered table " id="table">
            <thead>
                <tr>
                    <th>Laporan</th>
                    <th>Jumlah</th>
                </tr>
            </thead>

            
        </table>
    </div> --}}
</div>

 <!-- Statistics -->
<div class="col-lg-12 mb-4 ">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between">
        <h5 class="card-title mb-0">Statistics</h5>
        {{-- <small class="text-muted">Updated 1 month ago</small> --}}
      </div>
      <div class="card-body pt-2">
        <div class="row gy-3">
          <div class=" col">
            <div class="d-flex align-items-center">
              <div class="badge rounded-pill bg-label-primary me-3 p-2"><i class="ti ti-chart-pie-2 ti-sm"></i></div>
              <div class="card-info">
                <h5 class="mb-0" id="pengunjung-total">0</h5>
                <small>Pengunjung Onsite</small>
              </div>
            </div>
          </div>
          <div class=" col">
            <div class="d-flex align-items-center">
              <div class="badge rounded-pill bg-label-info me-3 p-2"><i class="ti ti-users ti-sm"></i></div>
              <div class="card-info">
                <h5 class="mb-0" id="peminjaman-total">0</h5>
                <small>Peminjaman Buku</small>
              </div>
            </div>
          </div>
          <div class=" col">
            <div class="d-flex align-items-center">
              <div class="badge rounded-pill bg-label-danger me-3 p-2"><i class="ti ti-shopping-cart ti-sm"></i></div>
              <div class="card-info">
                <h5 class="mb-0" id="pengembalian-total">0</h5>
                <small>Pengembalian Buku</small>
              </div>
            </div>
          </div>
          <div class=" col">
            <div class="d-flex align-items-center">
              <div class="badge rounded-pill bg-label-success me-3 p-2"><i class="ti ti-currency-dollar ti-sm"></i></div>
              <div class="card-info">
                <h5 class="mb-0" id="bebaspustaka-total">0</h5>
                <small>Bebas Pustaka</small>
              </div>
            </div>
          </div>
          <div class=" col">
            <div class="d-flex align-items-center">
              <div class="badge rounded-pill bg-label-success me-3 p-2"><i class="ti ti-currency-dollar ti-sm"></i></div>
              <div class="card-info">
                <h5 class="mb-0" id="ruangan-total">0</h5>
                <small>Ruangan</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>

<!-- Cards with few info -->
<div class="row">
    <div class="col-lg-6 col-sm-6 mb-4">
        <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">
                <h5 class="mb-0 me-2" id="status-4">0</h5>
                <small>Document TA/Thesis Not Feasible</small>
                </div>
                <div class="card-icon">
                <span class="badge bg-label-primary rounded-pill p-2">
                    <i class='ti ti-cpu ti-sm'></i>
                </span>
                </div>
                </div>
            </div>
    </div>

    <div class="col-lg-6 col-sm-6 mb-4">
        <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
            <div class="card-title mb-0">
                <h5 class="mb-0 me-2" id="status-3">0</h5>
                <small>Document TA/Thesis Approved For Catalog & Journal No Publish Tel-U Proceedings ( Not Feasible )</small>
            </div>
            <div class="card-icon">
                <span class="badge bg-label-success rounded-pill p-2">
                <i class='ti ti-server ti-sm'></i>
                </span>
            </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-sm-6 mb-4">
        <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
            <div class="card-title mb-0">
                <h5 class="mb-0 me-2" id="status-52">0</h5>
                <small>Document TA/Thesis Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal )</small>
            </div>
            <div class="card-icon">
                <span class="badge bg-label-success rounded-pill p-2">
                <i class='ti ti-server ti-sm'></i>
                </span>
            </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-sm-6 mb-4">
        <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
            <div class="card-title mb-0">
                <h5 class="mb-0 me-2" id="status-64">0</h5>
                <small>Document TA/Thesis Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal LoA Pending )</small>
            </div>
            <div class="card-icon">
                <span class="badge bg-label-danger rounded-pill p-2">
                <i class='ti ti-chart-pie-2 ti-sm'></i>
                </span>
            </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-sm-6 mb-4">
        <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
            <div class="card-title mb-0">
                <h5 class="mb-0 me-2" id="status-53">0</h5>
                <small>Document TA/Thesis Approved For Catalog & Journal Publish Tel-U Proceedings</small>
            </div>
            <div class="card-icon">
                <span class="badge bg-label-warning rounded-pill p-2">
                <i class='ti ti-alert-octagon ti-sm'></i>
                </span>
            </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-sm-6 mb-4">
        <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
            <div class="card-title mb-0">
                <h5 class="mb-0 me-2" id="status-91">0</h5>
                <small>Metadata Approve for Catalog & Journal Publish External</small>
            </div>
            <div class="card-icon">
                <span class="badge bg-label-warning rounded-pill p-2">
                <i class='ti ti-alert-octagon ti-sm'></i>
                </span>
            </div>
            </div>
        </div>
    </div>
</div>
<!--/ Cards with few info -->


<!-- tapaReadonlyChart-->
<div class="col-12  mb-4 order-1 order-lg-0">
    <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="card-title m-0">
                    <h5 class="mb-0">Karya Ilmiah (Akses)</h5>
                    <small class="text-muted stats_year">Laporan Keseluruhan </small>
                </div>
                
            </div>
            <div class="card-body">
                <div id="tapaReadonlyChart"></div>
        </div>
    </div>
</div>

<!-- ebookReadonlyChart-->
<div class="col-12  mb-4 order-1 order-lg-0">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
            <div class="card-title m-0">
                <h5 class="mb-0">Ebook (Akses)</h5>
                <small class="text-muted stats_year" >Laporan Keseluruhan </small>
            </div>
            
        </div>
        <div class="card-body">
            <div id="ebookReadonlyChart"></div>
        </div>
    </div>
</div>


<!-- visitorOpenlib -->
<div class="col-12  mb-4 order-1 order-lg-0">
    <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="card-title m-0 ">
                    <h5 class="mb-0" >Visitor Online Openlibrary(Google Analytics)</h5>
                    <small class="text-muted stats_year">Laporan Keseluruhan </small>
                </div>
            </div>
            <div class="card-body">
                <div id="visitorOpenlibChart"></div>
            </div>
    </div>
</div>

<!-- visitorEproc -->
<div class="col-12  mb-4 order-3 order-xl-0">
    <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="card-title m-0 ">
                    <h5 class="mb-0" >Visitor Online Openlibrary(Google Analytics)</h5>
                    <small class="text-muted stats_year">Laporan Keseluruhan </small>
                </div>
            </div>
            <div class="card-body">
                <div id="visitorEprocChart"></div>
            </div>
    </div>
</div>

@endsection

@section('vendor-script')
@endsection

@section('page-script')
<script>
let dTable = null;
let url = '{{ url('olafa/laporan') }}';
let bsDatepickerRange = null;

var startDate = moment().startOf('month');
var endDate = moment().endOf('month');

$(function(){
    
    $('#bs-datepicker-daterange').daterangepicker({
        ranges: {
            Today: [moment(), moment()],
            Yesterday: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        showDropdowns: true,
        opens: isRtl ? 'left' : 'right',
    }, function(start, end, label) {
        // console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        startDate = start;
        endDate = end;  
        // dTable.ajax.reload();
    });

});


$(document).ready(function() {

// dTable = $('#table').DataTable({
    //     processing: true,
    //     serverSide: true,
    //     ajax: {
    //         url: url + '/dt',
    //         type: 'post',
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         data: function (d) {
    //             d.startDate = startDate.format('YYYY-MM-DD');
    //             d.endDate = endDate.format('YYYY-MM-DD');
    //             d.prodi = $('#prodi').val();
    //         }
    //     },
        
    //     columns: [
    //         // { data: 'fakultas', name: 'fakultas', orderable: true, searchable: true },
    //         // { data: 'prodi', name: 'prodi', orderable: false, searchable: false },
    //         // { data: 'day', name: 'day', orderable: false, searchable: false },
    //         // { data: 'night', name: 'night', orderable: false, searchable: false },
    //     ],
// });
    
    function updateReportData(report) {
        $('#pengunjung-total').text(report.pengunjung ?? 0);
        $('#peminjaman-total').text(report.peminjaman ?? 0);
        $('#pengembalian-total').text(report.pengembalian ?? 0);
        $('#bebaspustaka-total').text(report.bebaspustaka ?? 0);
        $('#ruangan-total').text(report.ruangan ?? 0);
        $('#status-4').text(report['4'] ?? 0);
        $('#status-3').text(report['3'] ?? 0);
        $('#status-52').text(report['52'] ?? 0);
        $('#status-64').text(report['64'] ?? 0);
        $('#status-53').text(report['53'] ?? 0);
        $('#status-91').text(report['91'] ?? 0);
        if (report.tapa_readonly.length > 0 && report.tapa_readonly[0].year) {
            $('.stats_year').text('Laporan Keseluruhan ' + report.tapa_readonly[0].year);
        } else {
            $('.stats_year').text('Laporan Keseluruhan');
        }

    }

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

    function renderChart(containerId, type, data) {

        var monthlyData = data[0];

        var options = {
            chart: {
                height: 350,
                parentHeightOffset: 0,
                type: 'bar',
                toolbar: {
                show: false
                }
            },
            // colors: colorArr,
            plotOptions: {
                bar: {
                    columnWidth: '32%',
                    startingShape: 'rounded',
                    borderRadius: 4,
                    distributed: true,
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            grid: {
                show: false,
                padding: {
                top: 0,
                bottom: 0,
                left: -10,
                right: -10
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    if (val > 10000) {
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
            series: [{
                name: type,
                data: [
                    monthlyData.januari,
                    monthlyData.februari,
                    monthlyData.maret,
                    monthlyData.april,
                    monthlyData.mei,
                    monthlyData.juni,
                    monthlyData.juli,
                    monthlyData.agustus,
                    monthlyData.september,
                    monthlyData.oktober,
                    monthlyData.november,
                    monthlyData.desember
                ]
            }],
            legend: {
                show: false
            },
            // tooltip: {
            //     enabled: false
            // },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                axisBorder: {
                    show: true,
                    color: borderColor
                },
                axisTicks: {
                show: false
                },
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: '13px',
                        fontFamily: 'Public Sans'
                    }
                }
            },
            yaxis: {
                labels: {
                offsetX: -15,
                formatter: function (val) {
                    return parseInt(val / 1) ;
                },
                style: {
                    fontSize: '13px',
                    colors: labelColor,
                    fontFamily: 'Public Sans'
                },
                // min: 0,
                // max: 60000,
                tickAmount: 6
                }
            },
            responsive: [
                    {
                    breakpoint: 1441,
                    options: {
                        plotOptions: {
                        bar: {
                            columnWidth: '41%'
                        }
                        }
                    }
                    },
                    {
                    breakpoint: 590,
                    options: {
                        plotOptions: {
                        bar: {
                            columnWidth: '61%',
                            borderRadius: 5
                        }
                        },
                        yaxis: {
                        labels: {
                            show: false
                        }
                        },
                        grid: {
                        padding: {
                            right: 0,
                            left: -20
                        }
                        },
                        dataLabels: {
                        style: {
                            fontSize: '12px',
                            fontWeight: '400'
                        }
                        }
                    }
                    }
                ]
        };

        var chart = new ApexCharts(document.querySelector(`#${containerId}`), options);
        chart.render();
    }

    function renderChartVisitor(containerId, type1, data1, type2, data2) {

        const monthlyData1 = data1[0];
        const monthlyData2 = data2[0];

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
                enabled: true,
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
                    name: type1,
                    data: [
                        monthlyData1.januari,
                        monthlyData1.februari,
                        monthlyData1.maret,
                        monthlyData1.april,
                        monthlyData1.mei,
                        monthlyData1.juni,
                        monthlyData1.juli,
                        monthlyData1.agustus,
                        monthlyData1.september,
                        monthlyData1.oktober,
                        monthlyData1.november,
                        monthlyData1.desember
                    ]
                },
                {
                    name: type2,
                    data: [
                        monthlyData2.januari,
                        monthlyData2.februari,
                        monthlyData2.maret,
                        monthlyData2.april,
                        monthlyData2.mei,
                        monthlyData2.juni,
                        monthlyData2.juli,
                        monthlyData2.agustus,
                        monthlyData2.september,
                        monthlyData2.oktober,
                        monthlyData2.november,
                        monthlyData2.desember
                    ]
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

        var chart = new ApexCharts(document.querySelector(`#${containerId}`), options);
        chart.render();
    }

    function sendAjaxRequest() {
        $.ajax({
            url: url + '/ajax',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                startDate: startDate.format('YYYY-MM-DD'),
                endDate: endDate.format('YYYY-MM-DD'),
                prodi: $('#prodi').val()
            },
            success: function(response) {
                // Handle the response here
                updateReportData(response.report);

                if (response.report.tapa_readonly.length > 0) {
                    renderChart('tapaReadonlyChart', response.report.tapa_readonly[0].type, response.report.tapa_readonly[0].data);
                }

                if (response.report.ebook_readonly.length > 0) {
                    renderChart('ebookReadonlyChart', response.report.ebook_readonly[0].type, response.report.ebook_readonly[0].data);
                }

                const visitorOpenlib = response.report.visitor_openlib;
                if (visitorOpenlib.length > 0) {
                    renderChartVisitor('visitorOpenlibChart', visitorOpenlib[0].type, visitorOpenlib[0].data, visitorOpenlib[1]?.type, visitorOpenlib[1]?.data);
                }

                const visitorEproc = response.report.visitor_eproc;
                if (visitorEproc.length > 0) {
                    renderChartVisitor('visitorEprocChart', visitorEproc[0].type, visitorEproc[0].data, visitorEproc[1]?.type, visitorEproc[1]?.data);
                }

                // renderChart('tapaReadonlyChart', response.report.tapa_readonly[0].type, response.report.tapa_readonly[0].data);
                // renderChart('ebookReadonlyChart', response.report.ebook_readonly[0].type, response.report.ebook_readonly[0].data);

                // // console.log(visitorOpenlib);
                // renderChartVisitor('visitorOpenlibChart', visitorOpenlib[0].type, visitorOpenlib[0].data, visitorOpenlib[1].type, visitorOpenlib[1].data);
                // renderChartVisitor('visitorEprocChart', visitorEproc[0].type, visitorEproc[0].data, visitorEproc[1].type, visitorEproc[1].data);
                // // console.log(response);
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

});

</script>
@endsection