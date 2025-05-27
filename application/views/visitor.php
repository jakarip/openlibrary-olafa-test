<?php
$edition = explode(" ",$eproceeding['edition']);
$month 	 = getLang(strtolower($edition[0]));
//echo $this->session->userdata('usergroup');
?>

<?php
    // $url1=$_SERVER['REQUEST_URI'];
    // header("Refresh: 5; URL=$url1");
?>
 
<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3><i class="fa fa-user"></i><strong><?php echo getLang("visitor"); ?></strong></h3> 
		</div>
		<div class="panel-content">
			<div class="row" id="report"> 
				<div class="col-md-3 col-sm-12">
					<table class="table info_dashboard" cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<td class="desc_dashboard" align="center" style="font-size:50px;height:100px;"><?php echo date("Y") ?></td> 
							</tr> 
							<tr>
								<td align="center" style="font-size:50px;height:100px;"><?php echo number_format($pengunjung['year']->total,0,'','.'); ?></td> 
							</tr>   
						</thead>
					</table>
				</div> 
				<div class="col-md-3 col-sm-12">
					<table class="table info_dashboard" cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<td class="desc_dashboard" align="center" style="font-size:50px;height:100px;"><?php echo date("F Y") ?></td> 
							</tr> 
							<tr>
								<td align="center" style="font-size:50px;height:100px;"><?php echo number_format($pengunjung['month']->total,0,'','.'); ?></td> 
							</tr>   
						</thead>
					</table>
				</div> 
				 
				<div class="col-md-3 col-sm-12">
					<table class="table info_dashboard" cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<td class="desc_dashboard" align="center" style="font-size:50px;height:100px;">Check In Hari Ini</td> 
							</tr> 
							<tr>
								<td align="center" style="font-size:50px;height:100px;"><?php echo number_format($pengunjung['day']->total,0,'','.'); ?></td> 
							</tr>   
						</thead>
					</table>
				</div>  
				 
				 <div class="col-md-3 col-sm-12">
					 <table class="table info_dashboard" cellpadding="0" cellspacing="0">
						 <thead>
							 <tr>
								 <td class="desc_dashboard" align="center" style="font-size:50px;height:100px;">Check Out Hari Ini</td> 
							 </tr> 
							 <tr>
								 <td align="center" style="font-size:50px;height:100px;"><?php echo number_format($pengunjung['checkout']->total,0,'','.'); ?></td> 
							 </tr>   
						 </thead>
					 </table>
				 </div>  
			</div>
		</div>
	</div> 
</div> 
 
 
<?php 
 
$this->load->view('theme_footer'); ?>

<script type="text/javascript">
$(document).ready(function(){  
    setInterval(report, 10000);


});



function report(){ 
    $.ajax({
        url : '<?php echo site_url('index.php/visitor/report')?>',
        type: "POST",  
        // beforeSend : function() {
        //     showLoading();
        // },
        // complete : function() {
        //     hideLoading();
        // },
        success: function(data){ 
                $('#report').html(data);      
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            // info_alert('warning','<?php echo getLang("error_xhr")?>');
        }
    }); 
}  
</script>