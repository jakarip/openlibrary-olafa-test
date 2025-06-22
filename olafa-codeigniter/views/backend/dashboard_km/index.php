<style>

.yearselect{
	width:90px !important;
}
</style>
 

<div class="panel panel-body panel-pie text-center">
	<div class="row">
		<form id="frm">
			<div class="col-sm-9"> 
			</div>
			<div class="col-sm-2">
				<input type="text" class="form-control input-sm" name="dates" id="dates" required value="<?= date('m-Y',strtotime($start_date)).' - '.date('m-Y',strtotime($end_date))  ?>">
			</div>
			<div class="col-sm-1"> 
				<button type="button" class="btn btn-primary btn-labeled btn-xs" id="act-update" onclick="filter()">
					<b><i class="icon-search4"></i></b> Filter
				</button>
			</div>
		</form>
	</div>  
	<br>
	<br>
	<br>
	<?php
		if($error!=""){ ?> 
		<div class="row">
				<div class="col-sm-12 col-md-12">
					<h6 class="text-semibold no-margin-bottom mt-5"><?=$error?></h6> 
				</div>
		</div>  
	<?php	
		} else {
			foreach($km_db as $key=>$km){

				if($key!='up3'){
	?>
	<div class="row">
		<div class="col-sm-12 col-md-12">
			<h6 class="text-semibold no-margin-bottom mt-5">Data <?=$km?></h6>
				<div class="text-muted content-group-sm"><?= date('M Y',strtotime($start_date)).' - '.date('M Y',strtotime($end_date))  ?></div>
				<table class="table table-bordered table-striped table-hover" id="table"> 
       <thead>
				<tr>
					<td></td>
					<td>Total</td>
					<?php
						$dates = $start_date;
						for($i=0;$i<$month;$i++){ 
							echo "<td>".date('M Y',strtotime($dates))."</td>";
							$dates = date("Y-m-d", strtotime("$dates +1 Month")); 
						}
					?>
				</tr> 
				</thead>
				<tbody>
				<tr>
					<td>KM</td>
					<td><?=$total_target[$key] ?></td> 
					<?php
						$dates = $start_date;
						for($i=0;$i<$month;$i++){  
							if (array_key_exists(date('Y-m',strtotime($dates)), $target[$key])) {
								echo "<td>".$target[$key][date('Y-m',strtotime($dates))]."</td>";
							}   
							else echo "<td>0</td>";
							$dates = date("Y-m-d", strtotime("$dates +1 Month")); 
						}
					?>
				</tr>
				<tr>
					<td>Realisasi</td>
					<td><?=$total_realisasi[$key] ?></td> 
					<?php
						$dates = $start_date;
						for($i=0;$i<$month;$i++){  
							if (array_key_exists(date('Y-m',strtotime($dates)), $realisasi[$key])) {
								echo "<td>".$realisasi[$key][date('Y-m',strtotime($dates))]."</td>";
							}   
							else echo "<td>0</td>";
							$dates = date("Y-m-d", strtotime("$dates +1 Month")); 
						}
					?>
				</tr>
				</tbody>
    </table>
		</div>
	</div>   
	<br>
	<br>
	<br>
	<?php
			}
		}
	}
	?>
</div>

<?php $this->load->view('backend/tpl_footer'); ?> 
<script type="text/javascript" src="assets/limitless/global/js/plugins/visualization/d3/d3.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/visualization/d3/d3_tooltip.js"></script>

<script>
$(document).ready(function() {	  
	
	$('#frm #dates').daterangepicker({  
		locale: {
			format: 'MM-YYYY'
		},
		showDropdowns: true,
		opens: 'left',
		applyClass: 'bg-primary-600',
		cancelClass: 'btn-light'
	});
});

function filter(){
	var tgl = $("#dates").val().split(" - ");

	var start = tgl[0].split("-");
	var end = tgl[1].split("-");
	var total = monthDiff(new Date(start[1], start[0]),new Date(end[1], end[0]));
 
	if(total<0 || total>11) {
		alert("Maksimal range bulan adalah 12 bulan");
	}
	else {
		window.location.href='<?= y_url_admin() ?>/dashboard_km/report/'+start[1]+'-'+start[0]+'/'+end[1]+'-'+end[0];
	}
	
}

function monthDiff(dateFrom, dateTo) {
 return dateTo.getMonth() - dateFrom.getMonth() + 
   (12 * (dateTo.getFullYear() - dateFrom.getFullYear()))
}
 
</script>