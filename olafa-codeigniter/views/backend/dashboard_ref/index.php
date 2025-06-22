<style>
.panel-pie {
	padding:20px 10px;
}
</style>

<?php if (empty($id)) { ?>

<div class="row">
	<div class="col-sm-12 col-md-12">
        <div class="panel panel-body panel-pie text-center">
         <h6 class="text-semibold no-margin-bottom mt-5">Data Affiliate</h6>
            <div class="text-size-small text-muted content-group-sm"><?= y_get_month(date('m')).' '.date('Y') ?></div>
    
            <div class="svg-center" id="c-participant-status"></div>
        </div>
	</div>
	 
</div>
<?php }  ?>
<?php $this->load->view('backend/tpl_footer'); ?> 
<script type="text/javascript" src="assets/limitless/global/js/plugins/visualization/d3/d3.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/visualization/d3/d3_tooltip.js"></script>

<script>
$(document).ready(function() {	  
});


var data_participant_status = [
	{
		"status": "Total Aktif Affiliate",
		"value": '<?= $participant->ya ?>',
		"color": "#76FF03"
	}, {
		"status": "Total Pasif Affiliate",
		"value": '<?= $participant->tidak ?>',
		"color": "#F44336"
	} 
];
animatedDonutWithLegend("#c-participant-status", 120, data_participant_status);

  
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
		.text(function(d, i) {
			return d.data.status + ': ';
		});

	// Add value
	legend.append('span')
		.text(function(d, i) {
			return d.data.value;
		});
}
 
</script>