@extends('layouts/layoutMaster')

@section('title', 'Dashboard Pengadaan Per Prodi/Fakultas')

@section('vendor-style')
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
                        <div class="col-12 col-sm-6 col-lg-3">
                          <label for="bs-datepicker-daterange" class="form-label">Pilih Tanggal:</label>
                          <input type="text" id="bs-datepicker-daterange" class="form-control" />
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <label for="fakultas" class="form-label">Pilih Fakultas:</label>
                            <select id="fakultas" class="select2 form-select form-select-md">
                                <option value="">Semua</option>
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


                        <div class="col-12 col-sm-6 col-lg-1 d-flex align-items-end col-auto">
                            <button id="show-date-btn" class="btn btn-primary">Cari</button>
                        </div>
                        
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row d-flex justify-content-center">

</div>

@endsection

@section('vendor-script')
@endsection

@section('page-script')

<script>

let dTable = null;
let url = '{{ url('pengadaan/dashboard') }}';
let bsDatepickerRange = null;

var startDate = moment().startOf('month');
var endDate = moment().endOf('month');

let cardColor, headingColor, legendColor, labelColor, borderColor;
if (isDarkStyle) {
    cardColor = config.colors_dark.cardColor;
    labelColor = config.colors_dark.textMuted;
    legendColor = config.colors_dark.bodyColor;
    headingColor = config.colors_dark.headingColor;
    borderColor = config.colors_dark.borderColor;
} 
else {
        cardColor = config.colors.cardColor;
        labelColor = config.colors.textMuted;
        legendColor = config.colors.bodyColor;
        headingColor = config.colors.headingColor;
        borderColor = config.colors.borderColor;
    }

// Chart Colors
const chartColors = {
        donut: {
            series1: '#fee802',
            series2: '#3fd0bd',
            series3: '#826bf8',
            series4: '#2b9bf4',
            series5: config.colors.success
        },
};


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
        startDate = start;
        endDate = end;  
    });

});

$(document).ready(function() {

    function sendAjaxRequest() {
        $.ajax({
            url: url + '/ajax_detail',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                year: $('#bs-datepicker-daterange').val(),
                faculty: $('#fakultas').val(),
                prodi: $('#prodi').val(),
            },
            success: function(data) {
                $('.row.d-flex.justify-content-center').empty();

                if (data.info == 'faculty_all') {
                    data.faculty.forEach(function(faculty, index) {
                        // Generate HTML for each faculty chart
                        let chartHtml = `
                            <div class="col-md-6 col-12 mb-4">
                                <div class="card">
                                    <div class="card-header d-flex align-items-center justify-content-center">
                                        <div class="text-center">
                                            <h5 class="card-title mb-0">Status Pengajuan ${faculty.nama_fakultas}</h5>
                                            <small class="text-muted">${data.startDate} s/d ${data.endDate}</small>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="donutChart-${faculty.c_kode_fakultas}"></div>
                                    </div>
                                </div>
                            </div>
                        `;
                        $('.row.d-flex.justify-content-center').append(chartHtml);

                        // Prepare data for the chart
                        let chartData = [
                            {
                                "label": "Pengajuan dari Prodi",
                                "value": data.total[faculty.c_kode_fakultas][0].pengajuan,
                                "status": "pengajuan",
                                "fakultas": faculty.c_kode_fakultas,
                            },
                            {
                                "label": "Pengajuan ke Logistik",
                                "value": data.total[faculty.c_kode_fakultas][0].logistik,
                                "status": "logistik",
                                "fakultas": faculty.c_kode_fakultas,
                            },
                            {
                                "label": "Penerimaan Buku (Waktu Proses " + data.rerata_penerimaan[faculty.c_kode_fakultas][0].rerata_penerimaan + " hari)",
                                "value": data.total[faculty.c_kode_fakultas][0].penerimaan,
                                "status": "penerimaan",
                                "fakultas": faculty.c_kode_fakultas,
                            },
                            {
                                "label": "Ketersediaan Buku",
                                "value": data.total[faculty.c_kode_fakultas][0].available,
                                "status": "r_ketersediaan",
                                "fakultas": faculty.c_kode_fakultas,
                            },
                            {
                                "label": "Konfirmasi Email",
                                "value": data.total[faculty.c_kode_fakultas][0].email_confirmed,
                                "status": "s_email",
                                "fakultas": faculty.c_kode_fakultas,
                            }
                        ];

                        // Render the chart
                        renderChartPengajuan(`#donutChart-${faculty.c_kode_fakultas}`, chartData);
                    });
                }else if (data.prod) {
                    data.prod.forEach(function(prodi, index) {
                        // Generate HTML for each program chart
                        let chartHtml = `
                            <div class="col-md-6 col-12 mb-4">
                                <div class="card">
                                    <div class="card-header d-flex align-items-center justify-content-center">
                                        <div class="text-center">
                                            <h5 class="card-title mb-0">Status Pengajuan ${prodi.NAMA_PRODI}</h5>
                                            <small class="text-muted">${data.startDate} s/d ${data.endDate}</small>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="donutChart-${prodi.C_KODE_PRODI}"></div>
                                    </div>
                                </div>
                            </div>
                        `;
                        $('.row.d-flex.justify-content-center').append(chartHtml);

                        // Prepare data for the chart
                        let chartData = [
                            {
                                "label": "Pengajuan dari Prodi",
                                "value": data.total[prodi.C_KODE_PRODI][0].pengajuan,
                                "status": "pengajuan",
                                "fakultas": prodi.C_KODE_FAKULTAS,
                                "prodi": prodi.C_KODE_PRODI,
                            },
                            {
                                "label": "Pengajuan ke Logistik",
                                "value": data.total[prodi.C_KODE_PRODI][0].logistik,
                                "status": "logistik",
                                "fakultas": prodi.C_KODE_FAKULTAS,
                                "prodi": prodi.C_KODE_PRODI,
                            },
                            {
                                "label": "Penerimaan Buku (Waktu Proses " + data.rerata_penerimaan[prodi.C_KODE_PRODI][0].rerata_penerimaan + " hari)",
                                "value": data.total[prodi.C_KODE_PRODI][0].penerimaan,
                                "status": "penerimaan",
                                "fakultas": prodi.C_KODE_FAKULTAS,
                                "prodi": prodi.C_KODE_PRODI,
                            },
                            {
                                "label": "Ketersediaan Buku",
                                "value": data.total[prodi.C_KODE_PRODI][0].available,
                                "status": "r_ketersediaan",
                                "fakultas": prodi.C_KODE_FAKULTAS,
                                "prodi": prodi.C_KODE_PRODI,
                            },
                            {
                                "label": "Konfirmasi Email",
                                "value": data.total[prodi.C_KODE_PRODI][0].email_confirmed,
                                "status": "s_email",
                                "fakultas": prodi.C_KODE_FAKULTAS,
                                "prodi": prodi.C_KODE_PRODI,
                            }
                        ];

                        // Render the chart
                        renderChartPengajuan(`#donutChart-${prodi.C_KODE_PRODI}`, chartData);
                    });
                }
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

                }
            });
        } else {
            $('#prodi').empty().append('<option value="">Semua Prodi</option>');
        }
    });

    function renderChartPengajuan(id,data){
        // Generate labels and series from data
        const labels = data.map(item => `${item.label}: ${item.value}`);
        const series = data.map(item => item.value);

        var options ={
        chart: {
            height: 390,
            type: 'donut',
            events: {
            legendClick: function(chartContext, seriesIndex, opts) {
                    var label = chartContext.opts.labels[seriesIndex];
                    var status = data[seriesIndex].status;
                    var fakultas = data[seriesIndex].fakultas;
                    var prodi = data[seriesIndex].prodi;
                
                    if (status) {
                        var dateRange = $('#bs-datepicker-daterange').data('daterangepicker');
                        var formattedDateRange = dateRange.startDate.format('MM/DD/YYYY') + ' - ' + dateRange.endDate.format('MM/DD/YYYY');

                        // Create a form and submit it to redirect to the desired page
                        var form = $('<form>', {
                            'method': 'POST',
                            'action': '{{ url("pengadaan/buku") }}'
                        });

                        // Add CSRF token
                        var csrfToken = $('meta[name="csrf-token"]').attr('content');
                        form.append($('<input>', {
                            'type': 'hidden',
                            'name': '_token',
                            'value': csrfToken
                        }));

                        // Add status and date_range inputs
                        form.append($('<input>', {
                            'type': 'hidden',
                            'name': 'status',
                            'value': status
                        }));
                        form.append($('<input>', {
                            'type': 'hidden',
                            'name': 'date_range',
                            'value': formattedDateRange
                        }));
                        form.append($('<input>', {
                            'type': 'hidden',
                            'name': 'fakultas',
                            'value': fakultas
                        }));
                        form.append($('<input>', {
                            'type': 'hidden',
                            'name': 'prodi',
                            'value': prodi
                        }));

                        // Append the form to the body and submit it
                        $('body').append(form);
                        form.submit();
                    }
                },
            }
        },
        labels: labels,
        series: series,
        colors: [
            "#F44336",
            "#FF5722",
            "#00BCD4",
            "#2196F3",
            "#4CAF50",
        ],
        stroke: {
            show: false,
            curve: 'straight'
        },
        dataLabels: {
            enabled: false,
        },
        legend: {
            show: true,
            position: 'bottom',
            markers: { offsetX: -3 },
            itemMargin: {
            vertical: 3,
            horizontal: 10
            },
            labels: {
            colors: legendColor,
            fontSize: '3rem',
            useSeriesColors: false
            }
        },
        plotOptions: {
            pie: {
            donut: {
                labels: {
                show: true,
                name: {
                    show: false,
                },
                value: {
                    fontSize: '3rem',
                    color: legendColor,
                    fontFamily: 'Public Sans',
                    formatter: function (val) {
                    return val;
                    }
                },
                total: {
                    show: true,
                    fontSize: '1.5rem',
                    color: headingColor,
                    label: 'Total',
                    formatter: function (w) {
                        return series.reduce((a, b) => a + b, 0);
                    }
                }
                },
            
            }
            }
        },
        tooltip: {
            enabled: false,
        },
        responsive: [
            {
            breakpoint: 992,
            options: {
                chart: {
                height: 380
                },
                legend: {
                position: 'bottom',
                labels: {
                    colors: legendColor,
                    useSeriesColors: false
                }
                }
            }
            },
            {
            breakpoint: 576,
            options: {
                chart: {
                height: 320
                },
                plotOptions: {
                pie: {
                    donut: {
                    labels: {
                        show: true,
                        name: {
                        fontSize: '1.5rem'
                        },
                        value: {
                        fontSize: '1rem'
                        },
                        total: {
                        fontSize: '1.5rem'
                        }
                    }
                    }
                }
                },
                legend: {
                position: 'bottom',
                labels: {
                    colors: legendColor,
                    useSeriesColors: false
                }
                }
            }
            },
            {
            breakpoint: 420,
            options: {
                chart: {
                height: 280
                },
                legend: {
                show: false
                }
            }
            },
            {
            breakpoint: 360,
            options: {
                chart: {
                height: 250
                },
                legend: {
                show: false
                }
            }
            }
        ]
        }

        var chart = new ApexCharts(document.querySelector(id), options);
        chart.render();
        };
});
</script>

@endsection