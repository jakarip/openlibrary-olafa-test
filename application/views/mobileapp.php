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
		  <h3><i class="fa fa-user"></i><strong><?php echo getLang("TelU Openlib Mobile Application"); ?></strong></h3> 
		</div>
		<div class="panel-content" style="background-color:#DFDCCB">
			<div class="row" id="report"> 
				<div class="col-md-12 col-sm-12">
					<table class="table info_dashboard" cellpadding="0" cellspacing="0"  style="background-color:#DFDCCB" width="100%">
						<thead>
							<tr> 
								<td class="desc_dashboard" align="center" width="50%">

								<?php
$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
if(stripos($ua,'Mobile') == true) {
//if(stripos($ua,'iPhone') == true) {
// echo 'android';
    // header("location: markzet://details?id=com.telu.openlib");
}


//   else echo $_SERVER['HTTP_USER_AGENT'];
?>

								<?php
									$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
									if(stripos($ua,'android') == true) {
									//if(stripos($ua,'iPhone') == true) {
										echo '<a href="https://play.google.com/store/apps/details?id=com.telu.openlib" target="_blank">';
										 
									}
									else echo '<a href="https://play.google.com/store/apps/details?id=com.telu.openlib" target="_blank">';
								?>
									<!-- <a href="https://play.google.com/store/apps/details?id=com.telu.openlib">  -->
										<img src="cdn/environment/playstore.png"  width="100%">
									</a>
								</td> 
								<td class="desc_dashboard" align="center" width="50%">
									<a href="https://apps.apple.com/us/app/telu-openlib/id6456411367">
										<img src="cdn/environment/appstore.png"  width="100%">
									</a>
							</tr>
							<tr>
								<td colspan="2" style="text-align:center;color:#B80005;font-size:14px">
									Silahkan klik / scan QR Code untuk mendownload / membuka aplikasi TelU Openlib Mobile Application
								</td>
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

});

  
</script>
<script>
if (/(android)/i.test(navigator.userAgent)) {
  document.querySelectorAll('a[href]').forEach(function (link) {
    link.href = link.href.replace('https://play.google.com/store/apps/','market://');
  });
}
</script>