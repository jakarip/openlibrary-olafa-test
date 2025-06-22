<style>
.panel-pie {
	padding:20px 10px;
}
</style>
 
<div class="row">
	<div class="col-sm-12 col-md-12">
        <div class="panel panel-body panel-pie text-center">
		
	<div class="row" style="margin:20px 15px;">	
		
		<div class="col-sm-9" >
		</div>
		<div class="col-sm-2" >
			<input type="text" class="form-control input-sm" name="dates_filter" id="dates_filter" value="<?=$date1?> - <?= $date2 ?>">
		</div>   
		<div class="col-sm-1"> 
			<button type="button" class="btn btn-primary btn-labeled btn-xs" id="filter" >
				<b><i class="icon-search4"></i></b> Filter
			</button>
		</div>
	</div> 
	<div class="row" style="margin:20px 15px;">	
		<div>
			<h6 class="text-semibold no-margin-bottom mt-5"><a target="_blank" href="<?= y_url_apps('bookprocurement_url') ?>/submission">Status Pengajuan</a></h6>
				<div class="text-size-small text-muted content-group-sm"><?=$date1?> s/d <?= $date2 ?></div>
		
				<div class="svg-center" id="c-participant-status"></div>
			</div>
		</div>  
		<br><br><br>
		<div>
			<h6 class="text-semibold no-margin-bottom mt-5"><a target="_blank" href="<?= y_url_apps('bookprocurement_url') ?>/submission">Status Pengajuan TelU Press</a></h6>
				<div class="text-size-small text-muted content-group-sm"><?=$date1?> s/d <?= $date2 ?></div>
		
				<div class="svg-center" id="c-participant-status-telupress"></div>
			</div>
		</div> 
	</div> 

<?php $this->load->view('backend/tpl_footer'); ?> 
<script type="text/javascript" src="assets/limitless/global/js/plugins/visualization/d3/d3.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/visualization/d3/d3_tooltip.js"></script>

<script>
$(document).ready(function() {	  
	$('#dates_filter').daterangepicker({  
		locale: {
			format: 'DD-MM-YYYY'
		},
		showDropdowns: true,
		opens: 'left',
		applyClass: 'bg-primary-600',
		cancelClass: 'btn-light'
	});
	
	$("#filter").click(function(){ 
		// _reload();
		location.href= "bookprocurement/dashboard/index/"+$('#dates_filter').val().replaceAll(" ", "_"); 
	}); 
});


var data_participant_status = [
	{
		"status": "Pengajuan dari Prodi",
		"value": '<?= $total->pengajuan ?>',
		"color": "#F44336",
		"url"	:"<?= y_url_apps('bookprocurement_url') ?>/submission/index/<?=$filter?>/all/0/pengajuan"
	}, {
		"status": "Pengajuan ke Logistik",
		"value": '<?= $total->logistik ?>',
		"color": "#FF5722",
		"url"	:"<?= y_url_apps('bookprocurement_url') ?>/submission/index/<?=$filter?>/all/0/logistik"
	} , {
		"status": "Penerimaan Buku (Waktu Proses <?= ceil($rerata_penerimaan) ?> hari)",
		"value": '<?= $total->penerimaan ?>',
		"color": "#00BCD4",
		"url"	:"<?= y_url_apps('bookprocurement_url') ?>/submission/index/<?=$filter?>/all/0/penerimaan"
	}  , {
		"status": "Ketersediaan Buku",
		"value": '<?= $total->available ?>',
		"color": "#2196F3",
		"url"	:"<?= y_url_apps('bookprocurement_url') ?>/submission/index/<?=$filter?>/all/0/r_ketersediaan"
	}   , {
		"status": "Konfirmasi Email",
		"value": '<?= $total->email_confirmed ?>',
		"color": "#4CAF50",
		"url"	:"<?= y_url_apps('bookprocurement_url') ?>/submission/index/<?=$filter?>/all/0/s_email"
	}
];
animatedDonutWithLegend("#c-participant-status", 120, data_participant_status);



var data_participant_status_telupress = [
	{
		"status": "Pengajuan Naskah",
		"value": '<?= $total_telupress->step1 ?>',
		"color": "#F44336"
	}, {
		"status": "Review Naskah",
		"value": '<?= $total_telupress->step2 ?>',
		"color": "#FF5722"
	} , {
		"status": "Editing & Proofread",
		"value": '<?= $total_telupress->step3 ?>',
		"color": "#00BCD4"
	}  , {
		"status": "Layout",
		"value": '<?= $total_telupress->step4 ?>',
		"color": "#ff0090"
	}  , {
		"status": "ISBN",
		"value": '<?= $total_telupress->step5 ?>',
		"color": "#550080"
	}   , {
		"status": "Cetak",
		"value": '<?= $total_telupress->step6 ?>',
		"color": "#ffa18e"
	}   , {
		"status": "Sudah Diterima",
		"value": '<?= $total_telupress->step7 ?>',
		"color": "#4CAF50"
	} 
];
animatedDonutWithLegendTelUPress("#c-participant-status-telupress", 120, data_participant_status_telupress);


 

function animatedDonutWithLegend(element, size, data) 
{
	// Main variables
	var d3Container = d3.select(element),
		distance = 2, // reserve 2px space for mouseover arc moving
		radius = (size/2) - distance,
		sum = d3.sum(data, function(d) { return d.value; });


	// Create chart
	// ------------------------------

	// Add svg element
	var container = d3Container.append("svg");
	
	// Add SVG group
	var svg = container
		.attr("width", size)
		.attr("height", size)
		.append("g")
			.attr("transform", "translate(" + (size / 2) + "," + (size / 2) + ")");  


	// Construct chart layout
	// ------------------------------

	// Pie
	var pie = d3.layout.pie()
		.sort(null)
		.startAngle(Math.PI)
		.endAngle(3 * Math.PI)
		.value(function (d) { 
			return d.value;
		}); 

	// Arc
	var arc = d3.svg.arc()
		.outerRadius(radius)
		.innerRadius(radius / 1.5);


	//
	// Append chart elements
	//

	// Group chart elements
	var arcGroup = svg.selectAll(".d3-arc")
		.data(pie(data))
		.enter()
		.append("g") 
			.attr("class", "d3-arc")
			.style({
				'stroke': '#fff',
				'stroke-width': 2,
				'cursor': 'pointer'
			});
	
	// Append path
	var arcPath = arcGroup
		.append("path")
		.style("fill", function (d) {
			return d.data.color;
		});


	// Add interactions
	arcPath
		.on('mouseover', function (d, i) {

			// Transition on mouseover
			d3.select(this)
			.transition()
				.duration(500)
				.ease('elastic')
				.attr('transform', function (d) {
					d.midAngle = ((d.endAngle - d.startAngle) / 2) + d.startAngle;
					var x = Math.sin(d.midAngle) * distance;
					var y = -Math.cos(d.midAngle) * distance;
					return 'translate(' + x + ',' + y + ')';
				});

			// Animate legend
			$(element + ' [data-slice]').css({
				'opacity': 0.3,
				'transition': 'all ease-in-out 0.15s'
			});
			$(element + ' [data-slice=' + i + ']').css({'opacity': 1});
		})
		.on('mouseout', function (d, i) {

			// Mouseout transition
			d3.select(this)
			.transition()
				.duration(500)
				.ease('bounce')
				.attr('transform', 'translate(0,0)');

			// Revert legend animation
			$(element + ' [data-slice]').css('opacity', 1);
		});

	// Animate chart on load
	arcPath
		.transition()
			.delay(function(d, i) {
				return i * 500;
			})
			.duration(500)
			.attrTween("d", function(d) {
				var interpolate = d3.interpolate(d.startAngle,d.endAngle);
				return function(t) {
					d.endAngle = interpolate(t);
					return arc(d);  
				}; 
			});


	//
	// Append counter
	//

	// Append text
	svg
		.append('text')
		.attr('text-anchor', 'middle')
		.attr('dy', 6)
		.style({
			'font-size': '17px',
			'font-weight': 500
		});

	// Animate text
	svg.select('text')
		.transition()
		.duration(1500)
		.tween("text", function(d) {
			var i = d3.interpolate(this.textContent, sum);
			return function(t) {
				this.textContent = d3.format(",d")(Math.round(i(t)));
			};
		});


	//
	// Append legend
	//

	// Add element
	var legend = d3.select(element)
		.append('ul')
		.attr('class', 'chart-widget-legend')
		.selectAll('li').data(pie(data))
		.enter().append('li')
		.attr('data-slice', function(d, i) {
			return i;
		})
		.attr('style', function(d, i) {
			return 'border-bottom: 2px solid ' + d.data.color;
		})
		.html(function(d, i) { // Modifikasi di sini untuk menggunakan .html()
        return '<a href="' + d.data.url + '" target="_blank">' + d.data.status + ' : ' + d.data.value + '</a>';
    });
}


function animatedDonutWithLegendTelUPress(element, size, data) 
{
	// Main variables
	var d3Container = d3.select(element),
		distance = 2, // reserve 2px space for mouseover arc moving
		radius = (size/2) - distance,
		sum = d3.sum(data, function(d) { return d.value; });


	// Create chart
	// ------------------------------

	// Add svg element
	var container = d3Container.append("svg");
	
	// Add SVG group
	var svg = container
		.attr("width", size)
		.attr("height", size)
		.append("g")
			.attr("transform", "translate(" + (size / 2) + "," + (size / 2) + ")");  


	// Construct chart layout
	// ------------------------------

	// Pie
	var pie = d3.layout.pie()
		.sort(null)
		.startAngle(Math.PI)
		.endAngle(3 * Math.PI)
		.value(function (d) { 
			return d.value;
		}); 

	// Arc
	var arc = d3.svg.arc()
		.outerRadius(radius)
		.innerRadius(radius / 1.5);


	//
	// Append chart elements
	//

	// Group chart elements
	var arcGroup = svg.selectAll(".d3-arc")
		.data(pie(data))
		.enter()
		.append("g") 
			.attr("class", "d3-arc")
			.style({
				'stroke': '#fff',
				'stroke-width': 2,
				'cursor': 'pointer'
			});
	
	// Append path
	var arcPath = arcGroup
		.append("path")
		.style("fill", function (d) {
			return d.data.color;
		});


	// Add interactions
	arcPath
		.on('mouseover', function (d, i) {

			// Transition on mouseover
			d3.select(this)
			.transition()
				.duration(500)
				.ease('elastic')
				.attr('transform', function (d) {
					d.midAngle = ((d.endAngle - d.startAngle) / 2) + d.startAngle;
					var x = Math.sin(d.midAngle) * distance;
					var y = -Math.cos(d.midAngle) * distance;
					return 'translate(' + x + ',' + y + ')';
				});

			// Animate legend
			$(element + ' [data-slice]').css({
				'opacity': 0.3,
				'transition': 'all ease-in-out 0.15s'
			});
			$(element + ' [data-slice=' + i + ']').css({'opacity': 1});
		})
		.on('mouseout', function (d, i) {

			// Mouseout transition
			d3.select(this)
			.transition()
				.duration(500)
				.ease('bounce')
				.attr('transform', 'translate(0,0)');

			// Revert legend animation
			$(element + ' [data-slice]').css('opacity', 1);
		});

	// Animate chart on load
	arcPath
		.transition()
			.delay(function(d, i) {
				return i * 500;
			})
			.duration(500)
			.attrTween("d", function(d) {
				var interpolate = d3.interpolate(d.startAngle,d.endAngle);
				return function(t) {
					d.endAngle = interpolate(t);
					return arc(d);  
				}; 
			});


	//
	// Append counter
	//

	// Append text
	svg
		.append('text')
		.attr('text-anchor', 'middle')
		.attr('dy', 6)
		.style({
			'font-size': '17px',
			'font-weight': 500
		});

	// Animate text
	svg.select('text')
		.transition()
		.duration(1500)
		.tween("text", function(d) {
			var i = d3.interpolate(this.textContent, sum);
			return function(t) {
				this.textContent = d3.format(",d")(Math.round(i(t)));
			};
		});


	//
	// Append legend
	//

	// Add element
	var legend = d3.select(element)
		.append('ul')
		.attr('class', 'chart-widget-legend')
		.selectAll('li').data(pie(data))
		.enter().append('li')
		.attr('data-slice', function(d, i) {
			return i;
		})
		.attr('style', function(d, i) {
			return 'border-bottom: 2px solid ' + d.data.color;
		})
		.html(function(d, i) { // Modifikasi di sini untuk menggunakan .html()
        return '' + d.data.status + ' : ' + d.data.value + '';
    });
}

function stackedMultiples(element, height, data) 
{
	//sort bars based on value
	data = data.sort(function (a, b) {
		return d3.ascending(a.value, b.value);
	});
	
	// Define main variables
	var d3Container = d3.select(element),
		margin = {top: 25, right: 40, bottom: 20, left: 250},
		width = d3Container.node().getBoundingClientRect().width - margin.left - margin.right,
		height = height - margin.top - margin.bottom - 5,
		barHeight = 30,
		duration = 750,
		delay = 25;

	//set up svg using margin conventions - we'll need plenty of room on the left for labels
	/*var margin = {
		top: 25,
		right: 40,
		bottom: 20,
		left: 250
	};*/

	/*var width = 980 - margin.left - margin.right,
		height = 270 - margin.top - margin.bottom;*/

	var svg = d3Container.append("svg")
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom)
		.append("g")
		.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

	var x = d3.scale.linear()
		.range([0, width])
		.domain([0, d3.max(data, function (d) {
			return d.value;
		})]);

	var y = d3.scale.ordinal()
		.rangeRoundBands([height, 0], .1)
		.domain(data.map(function (d) {
			return d.name;
		}));

	//make y axis to show bar names
	var yAxis = d3.svg.axis()
		.scale(y)
		//no tick marks
		.tickSize(0)
		.orient("left");

	var gy = svg.append("g")
		.attr("class", "d3-axis-vertical")
		.call(yAxis)
	
	var xAxis = d3.svg.axis()
	.scale(x)
	
		.tickSize(6)
	.orient("top");
	
	 gy = svg.append("g")
		.attr("class", "d3-axis d3-axis-horizontal d3-axis-strong")
		 
		.call(xAxis)
	
	var bars = svg.selectAll(".bar")
		.data(data)
		.enter()
		.append("g")

	//append rects
	bars.append("rect")
		.attr("class", "d3-bars-background")
		.attr("y", function (d) {
			return y(d.name);
		})
		.attr("height", y.rangeBand())
		.attr("x", 0)
		.attr("width", function (d) {
			return x(d.value);
		})
		.style('fill', '#26A69A');

	//add a value label to the right of each bar
	bars.append("text")
		.attr("class", "label")
		//y position of the label is halfway down the bar
		.attr("y", function (d) {
			return y(d.name) + y.rangeBand() / 2 + 4;
		})
		//x position is 3 pixels to the right of the bar
		.attr("x", function (d) {
			return x(d.value) + 3;
		})
		.text(function (d) {
			return d.value;
		});	
}
</script>