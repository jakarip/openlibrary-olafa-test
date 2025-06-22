@extends('layouts/layoutMaster')

@section('title', 'Dashboard Pengadaan')

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
  <!-- Donut Chart -->
  <div class="col-md-12 col-12 mb-4">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-center">
        <div class="text-center">
          <h5 class="card-title mb-0">Status Pengajuan</h5>
          <small class="text-muted">Tanggal s/d Tanggal</small>
        </div>
      </div>
      <div class="card-body">
        <div id="donutChartPengajuan" ></div>
      </div>
    </div>
  </div>
  <!-- /Donut Chart -->

</div>

<div class="row d-flex justify-content-center">
    
  <!-- Donut Chart -->
  <div class="col-md-12 col-12 mb-4">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-center">
        <div class="text-center">
          <h5 class="card-title mb-0">Status Pengajuan TelU Press</h5>
          <small class="text-muted">Tanggal s/d Tanggal</small>
        </div>
      </div>
      <div class="card-body">
        <div id="donutChartTeluPress" ></div>
      </div>
    </div>
  </div>
  <!-- /Donut Chart -->

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
            url: url + '/ajax',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                year: $('#bs-datepicker-daterange').val()
            },
            success: function(data) {
                // Prepare data for the chart
                let chartData = [
                {
                  "label": "Pengajuan dari Prodi",
                  "value": data.total[0].pengajuan,
                  "status": "pengajuan",
                },
                {
                  "label": "Pengajuan ke Logistik",
                  "value": data.total[0].logistik,
                  "status": "logistik",
                },
                {
                  "label": "Penerimaan Buku (Waktu Proses " + data.rerata_penerimaan[0].rerata_penerimaan + " hari)",
                  "value": data.total[0].penerimaan,
                  "status": "penerimaan",
                },
                {
                  "label": "Ketersediaan Buku",
                  "value": data.total[0].available,
                  "status": "r_ketersediaan",
                },
                {
                  "label": "Konfirmasi Email",
                  "value": data.total[0].email_confirmed,
                  "status": "s_email",
                } 
              ];

                renderChartPengajuan(chartData);
                renderChartTeluPress(data.total_telupress[0]);

                $('small.text-muted').text(data.startDate + ' s/d ' + data.endDate);

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

    function renderChartPengajuan(data){

      const labels = data.map(item => `${item.label}: ${item.value}`);
      const series = data.map(item => item.value);
      
      $('#donutChartPengajuan').empty();

      var options ={
        chart: {
          height: 390,
          type: 'donut',
          events: {
            legendClick: function(chartContext, seriesIndex, opts) {
              var label = chartContext.opts.labels[seriesIndex];
              var status = data[seriesIndex].status;
              
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


      var chart = new ApexCharts(document.querySelector(`#donutChartPengajuan`), options);
      chart.render();
    };

    function renderChartTeluPress(data){
      $('#donutChartTeluPress').empty();

      var total = data.total;
      var step1 = data.step1;
      var step2 = data.step2;
      var step3 = data.step3;
      var step4 = data.step4;
      var step5 = data.step5;
      var step6 = data.step6;
      var step7 = data.step7;

      var options ={
        chart: {
          height: 390,
          type: 'donut'
        },
        labels: [
            'Pengajuan Naskah: ' + step1,
            "Review Naskah: " + step2,
            "Editing & Proofread: " + step3,
            "Layout: " + step4,
            "ISBN: " + step5,
            "Cetak: " + step6,
            "Sudah Diterima: " + step7,
        ],
        series: [step1, step2, step3, step4, step5, step6, step7],
        colors: [
            "#F44336",
            "#FF5722",
            "#00BCD4",
            "#ff0090",
            "#550080",
            "#ffa18e",
            "#4CAF50",
        ],
        stroke: {
          show: false,
          curve: 'straight'
        },
        dataLabels: {
          enabled: false,
          formatter: function (val, opt) {
            return opt.w.config.series[opt.seriesIndex];
          }
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
                  fontSize: '2rem',
                  fontFamily: 'Public Sans'
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
                    return total;
                  }
                }
              }
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


      var chart = new ApexCharts(document.querySelector(`#donutChartTeluPress`), options);
      chart.render();
  };



})



</script>
@endsection