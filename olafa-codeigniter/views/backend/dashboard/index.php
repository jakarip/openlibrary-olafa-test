<style>
.panel-pie {
	padding:20px 10px;
}
</style>
 

<div class="row">
	<div class="col-sm-12 col-md-12">
        <div class="panel panel-body panel-pie text-center">
      
        </div>
	</div> 
</div> 
<?php $this->load->view('backend/tpl_footer'); ?> 
<script type="text/javascript" src="assets/limitless/global/js/plugins/visualization/d3/d3.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/visualization/d3/d3_tooltip.js"></script>

<script>
$(document).ready(function() {	 
	$(".breadcrumb-elements").html('<?=$list ?>'); 
	
}); 
</script>