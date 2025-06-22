<?php 
    $first = date('Y/m/d', strtotime('-6 days'));
    $second = date('Y/m/d');

    if($widgetPengadaan->pengadaan == 0){
        $pengadaan = '<p class="number">'.$widgetPengadaan->pengadaan.'</p>';
    }else{
        $pengadaan = '<p class="number countup" data-from="0" data-to="'.$widgetPengadaan->pengadaan.'">'.$widgetPengadaan->pengadaan.'</p>';
    }

?>

<div class="row m-t-10">
    <div class="col-xlg-2 col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="panel">
            <div class="panel-content widget-info">
                <div class="row">
                    <div class="left">
                        <i class="fa fa-umbrella bg-green"></i>
                    </div>
                    <div class="right">
                        <p class="number">0</p>
                        <p class="text">Penjualan Bulan Ini</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xlg-2 col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="panel">
            <div class="panel-content widget-info">
                <div class="row">
                    <div class="left">
                        <i class="fa fa-umbrella bg-red"></i>
                    </div>
                    <div class="right">
                        <?php echo $pengadaan ?>
                        <p class="text">Pengadaan Bulan Ini</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xlg-2 col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="panel">
            <div class="panel-content widget-info">
                <div class="row">
                    <div class="left">
                        <i class="fa fa-umbrella bg-orange"></i>
                    </div>
                    <div class="right">
                        <p class="number">0</p>
                        <p class="text">Pay Supplier Bulan ini</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xlg-2 col-lg-3 col-md-3 col-sm-3 col-xs-12">
        <div class="panel">
            <div class="panel-content widget-info">
                <div class="row">
                    <div class="left">
                        <i class="fa fa-umbrella bg-blue"></i>
                    </div>
                    <div class="right">
                        <p class="number">0</p>
                        <p class="text">Pay Customer Bulan ini</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">

    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="panel">
            <div class="panel-header panel-controls">
                <h3><i class="icon-graph"></i> <strong>Cashflow</strong> Keuangan</h3>
                <div class="control-btn">
                    <a class="panel-toggle"><i class="fa fa-angle-down"></i></a>
                </div>
            </div>
			<div class="panel-content widget-full widget-stock stock1">
			    <div class="tabs">
			        <ul class="nav nav-tabs nav-3">
<!-- 			            <li class="active lines-3">
			                <a href="#microsoft-tab" id="microsoft" data-toggle="tab">
			                    <span class="title">Global</span>
			                </a>
			            </li> -->
<!-- 			            <li class="lines-3">
			                <a href="#sony-tab" id="sony" data-toggle="tab">
			                    <span class="title">Pengeluaran</span>
			                </a>
			            </li>
			            <li class="lines-3">
			                <a href="#samsung-tab" id="samsung" data-toggle="tab">
			                    <span class="title">Pemasukan</span>
			                </a>
			            </li> -->
			        </ul>
			        <div class="tab-content">
			            <div class="tab-pane active" id="microsoft-tab">
                            <form class="form-inline" id="form_custom">
                                <div class="form-group">
                                    <div class="input-daterange b-datepickers input-group" id="datepicker">
                                        <input value="<?php echo $first ?>" class="form-white form-control from_date" name="startDate" id="startDate" placeholder="Beginning..." type="text">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-daterange b-datepickers input-group" id="datepicker">
                                        <input value="<?php echo $second ?>" class=" form-white form-control to_date" name="endDate" id="endDate" placeholder="Ending..." type="text">
                                    </div>
                                </div>

                                <div class="input-group">
                                    <button type="button" style="margin-top:10px;" class="btn btn-primary btn-embossed" id="daterange"><i class="fa fa-filter"></i> Filter</button>
                                </div>

                            </form>


                            <canvas id="line-chart-js" height="240" width="100%" ></canvas>

			            </div>
<!-- 			            <div class="tab-pane" id="sony-tab">
			                <canvas id="line-chart-outcome" height="240" width="100%" ></canvas>
			            </div>
			            <div class="tab-pane" id="samsung-tab">
			                <div id="stock-samsung"></div>
			            </div> -->
			        </div>
			    </div>
			</div>
		</div>
	</div>
</div>


<div class="row">
    <div class="col-md-4">
        <div class="panel">
            <div class="panel-header">
                <h3><i class="icon-list"></i> Daftar Tagihan <strong> Suplier</strong></h3>
                <div class="control-btn">
                    <a class="panel-reload" title="Reload Data"><i class="icon-reload"></i></a>
                </div>
            </div>
            <div class="panel-content widget-table">
                <div class="withScroll mCustomScrollbar _mCS_6" data-height="300">
                    <div class="mCustomScrollBox mCS-light">
                        <div class="mCSB_container">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Inv. Supplier</th>
                                        <th>Supplier</th>
                                        <th class="text-center">Sisa Tagihan</th>
                                    </tr>
                                </thead>
                                <tbody id="table_billsup"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="panel">
            <div class="panel-header">
                <h3><i class="icon-list"></i> Daftar Tagihan <strong> Kurir</strong></h3>
                <div class="control-btn">
                    <a class="panel-reload" title="Reload Data"><i class="icon-reload"></i></a>
                </div>
            </div>
            <div class="panel-content widget-table">
                <div class="withScroll mCustomScrollbar _mCS_6" data-height="300">
                    <div class="mCustomScrollBox mCS-light">
                        <div class="mCSB_container">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No. Ref</th>
                                        <th>Kurir</th>
                                        <th class="text-center">Metode Kirim</th>
                                    </tr>
                                </thead>
                                <tbody id="table_billcour"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="panel">
            <div class="panel-header">
                <h3><i class="icon-list"></i> Daftar Tagihan <strong> Customer</strong></h3>
                <div class="control-btn">
                    <a class="panel-reload" title="Reload Data"><i class="icon-reload"></i></a>
                </div>
            </div>
            <div class="panel-content widget-table">
                <div class="withScroll mCustomScrollbar _mCS_6" data-height="300">
                    <div class="mCustomScrollBox mCS-light">
                        <div class="mCSB_container">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Kode Transaksi</th>
                                        <th>Customer</th>
                                        <th class="text-center">Sisa Tagihan</th>
                                    </tr>
                                </thead>
                                <tbody id="table_billcust"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('backend/theme_footer'); ?>

<script type="text/javascript">
    $(document).ready(function(){        
        $('.b-datepickers').each(function () {
            $(this).bootstrapDatepicker({
                format: 'yyyy/mm/dd',
                startView: $(this).data('view') ? $(this).data('view') : 0, // 0: month view , 1: year view, 2: multiple year view
                language: $(this).data('lang') ? $(this).data('lang') : "en",
                forceParse: $(this).data('parse') ? $(this).data('parse') : false,
                daysOfWeekDisabled: $(this).data('day-disabled') ? $(this).data('day-disabled') : "", // Disable 1 or various day. For monday and thursday: 1,3
                calendarWeeks: $(this).data('calendar-week') ? $(this).data('calendar-week') : false, // Display week number 
                autoclose: $(this).data('autoclose') ? $(this).data('autoclose') : false,
                todayHighlight: $(this).data('today-highlight') ? $(this).data('today-highlight') : true, // Highlight today date
                toggleActive: $(this).data('toggle-active') ? $(this).data('toggle-active') : true, // Close other when open
                multidate: $(this).data('multidate') ? $(this).data('multidate') : false, // Allow to select various days
                orientation: $(this).data('orientation') ? $(this).data('orientation') : "top", // Allow to select various days,
                rtl: $('html').hasClass('rtl') ? true : false,
            });
        });

        $('.b-datepickers').on('changeDate', function(ev){
            $(this).bootstrapDatepicker('hide');
        });

        $.ajax({
            url : 'backend/dashboard/daterange',
            type: "POST",
            data: {
                status : "default",
                first  : "<?php echo $first ?>",
                second : "<?php echo $second ?>"
            },
            dataType: "JSON",
            success: function(data){
               redraw(data[0],data[1],data[2]);
				// console.log(data[0]);
            },
            error: function (jqXHR, textStatus, errorThrown){
            }
        });


        $('#daterange').click(function(){
            if ($(".from_date").val()!="" && $(".to_date").val()!=""){ 
                $.ajax({
                    url : 'backend/dashboard/daterange',
                    type: "POST",
                    data: {
                        
                        status : "not",
                        first  : $(".from_date").val(),
                        second : $(".to_date").val()
                    },
                    dataType: "JSON",
                    success: function(data){
                        redraw(data[0],data[1],data[2]);
                    },
                    error: function (jqXHR, textStatus, errorThrown){
                    }
                }); 
            }
        });
    });

    Chart.numberWithCommas = function(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    };
    //separator

    var myLineChart=null;
    var myLineChart2=null;

    function redraw(label,income,outcome){ 
        if(myLineChart!=null){
            myLineChart.destroy();
        }

        $("canvas").each(function(i,el){
                $(el).attr({
                    "width":$(el).parent().width(),
                    "height":$(el).parent().outerHeight()
                });
            });
        var m = 0;
        $(".chartJS").height("");
        $(".chartJS").each(function(i,el){ m = Math.max(m,$(el).height()); });
        $(".chartJS").height(m);

         var Linedata = {
            labels :label,
            datasets : [
                {
                    label: "Outcome",
                    fillColor: "rgba(199,87,87,0.08)",
                    strokeColor: "rgba(199,87,87,0.08)",
                    pointColor: "rgba(199,87,87,2)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#c75757",
                    pointHighlightStroke: "rgba(151,187,205,0.2)",
                    data : outcome
                },
                {
                    label: "Income",
                    fillColor : "rgba(24,166,137,0.08)",
                    strokeColor : "rgba(24,166,137,0.1)",
                    pointColor : "rgba(24,166,137,2)",
                    pointStrokeColor : "#fff",
                    pointHighlightFill: "#18a689",
                    pointHighlightStroke: "rgba(179,237,208,0.2)",
                    data : income
                },
            ]
        }
        myLineChart = new Chart(document.getElementById("line-chart-js").getContext("2d")).Line(Linedata,{
            scaleLabel: "<%=Chart.numberWithCommas(value)%>",
            multiTooltipTemplate: "<%= datasetLabel %> : <%=Chart.numberWithCommas(value)%>"
        });
    }

</script>
