				<div class="modal fade" id="modal_info" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header bg-primary">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
								<strong><h4 class="modal-title"></h4></strong>
							</div>
							<form id="form" class="form-horizontal">
							<div class="modal-body">
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;<?php echo getLang("ok") ?></button>
							</div>
							</form>
						</div>
					</div>
				</div>  
          </div>
          <div class="footer">
            <!-- <div class="copyright">
              <p class="pull-left sm-pull-reset">
                <span>Copyright <span class="copyright">©</span> 2015 </span>
                <span>THEMES LAB</span>.
                <span>All rights reserved. </span>
              </p>
            </div> -->
          </div>
        </div>
        <!-- END PAGE CONTENT -->
      </div>
      <!-- END MAIN CONTENT -->
    </section>
    <!-- BEGIN PRELOADER -->
    <div class="loader-overlay">
      <div class="spinner">
        <div class="bounce1"></div>
        <div class="bounce2"></div>
        <div class="bounce3"></div>
      </div>
    </div>
    <!-- END PRELOADER -->
    <a href="#" class="scrollup"><i class="fa fa-angle-up"></i></a> 
    <script src="tools/assets/global/plugins/jquery/jquery-1.11.1.min.js"></script>
    <script src="tools/assets/global/plugins/jquery/jquery-migrate-1.2.1.min.js"></script>
    <script src="tools/assets/global/plugins/jquery-ui/jquery-ui-1.11.2.min.js"></script>
    <script src="tools/assets/global/plugins/gsap/main-gsap.min.js"></script>
    <script src="tools/assets/global/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="tools/assets/global/plugins/jquery-cookies/jquery.cookies.min.js"></script> <!-- Jquery Cookies, for theme -->
    <script src="tools/assets/global/plugins/jquery-block-ui/jquery.blockUI.min.js"></script> <!-- simulate synchronous behavior when using AJAX -->
    <script src="tools/assets/global/plugins/bootbox/bootbox.min.js"></script> <!-- Modal with Validation -->
    <script src="tools/assets/global/plugins/mcustom-scrollbar/jquery.mCustomScrollbar.concat.min.js"></script> <!-- Custom Scrollbar sidebar -->
    <script src="tools/assets/global/plugins/bootstrap-dropdown/bootstrap-hover-dropdown.min.js"></script> <!-- Show Dropdown on Mouseover -->
    <script src="tools/assets/global/plugins/charts-sparkline/sparkline.min.js"></script> <!-- Charts Sparkline -->
    <script src="tools/assets/global/plugins/retina/retina.min.js"></script> <!-- Retina Display --> 
    <script src="tools/assets/global/plugins/select2/js/select2.full.min.js"></script> <!-- Select Inputs --> 
    <script src="tools/assets/global/plugins/backstretch/backstretch.min.js"></script> <!-- Background Image -->
    <script src="tools/assets/global/plugins/bootstrap-progressbar/bootstrap-progressbar.min.js"></script> <!-- Animated Progress Bar -->
	<!--<script src="tools/assets/global/plugins/datatables/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
	<script src="tools/assets/global/plugins/datatables/js/dataTables.bootstrap.js"></script>
	<script src="tools/assets/global/plugins/datatables/js/jquery.dataTables.min.js"></script>-->
	<script type="text/javascript" src="tools/assets/global/plugins/datatables1/media/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="tools/assets/global/plugins/datatables1/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
	<script type="text/javascript" src="tools/assets/global/plugins/datatables1/plugins/bootstrap/dataTables.bootstrap.js"></script>
    <script src="tools/assets/global/plugins/charts-chartjs/Chart.min.js"></script>
    <script src="tools/assets/global/js/sidebar_hover.js"></script> <!-- Sidebar on Hover -->
    <script src="tools/assets/global/js/widgets/notes.js"></script> <!-- Notes Widget -->
    <script src="tools/assets/global/js/quickview.js"></script> <!-- Chat Script -->
    <script src="tools/assets/global/js/pages/search.js"></script> <!-- Search Script -->
    <script src="tools/assets/global/js/plugins.js"></script> <!-- Main Plugin Initialization Script -->
    <script src="tools/assets/global/js/application.js"></script> <!-- Main Application Script -->
    <script src="tools/assets/admin/layout1/js/layout.js"></script> <!-- Main Application Script -->
	<!-- BEGIN PAGE SCRIPT -->
    <script src="tools/assets/global/plugins/noty/jquery.noty.packaged.min.js"></script>  <!-- Notifications -->
    <script src="tools/assets/global/js/pages/notifications.js"></script>
	<script src="tools/assets/global/plugins/bootstrap-loading/lada.min.js"></script>
	<script src="tools/assets/global/plugins/jquery-validation/jquery.validate.min.js"></script>
	<script src="tools/assets/global/plugins/jquery-validation/additional-methods.min.js"></script>
	<script src="tools/assets/global/plugins/highcharts/highcharts.js"></script>
	<script src="tools/assets/global/plugins/highcharts/exporting.js"></script>	
    <script type="text/javascript" src="tools/assets/global/plugins/moment/moment.min.js"></script>
    <script type="text/javascript" src="tools/assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
	<script type="text/javascript" src="tools/assets/global/plugins/jquery-date-range-picker-master/dist/jquery.daterangepicker.min.js"></script>
	<script type="text/javascript" src="tools/analytics.js"></script>
	<script type="text/javascript" language="javascript" src="tools/assets/global/plugins/tokeninput/src/jquery.tokeninput.js"></script>
	<script src='tools/assets/global/plugins/searchable/src/jquery-customselect.js'></script> 
    <!-- END PAGE SCRIPTS -->

  </body>
</html>

	
<script type="text/javascript">
	
$(document).ready(function(){ 
	
	$('.language').click(function(event){
		event.preventDefault();
		var language = $(".language span").text();
		$.ajax({
			url			: 'index.php/custom/language',
			type		: 'post',
			data		: {
				language	: language
			},
			success: function(e) { 
				location.reload();
			}
		});   
	}); 
});

function dynamic_menu($url) {
    $.ajax({
		url : 'custom/dynamic_menu',
		type: "POST",
		data :{
			url : $url
		},
		success: function(data)
		{
			$('.nav-sidebar').html(data);
		}
	});
} 

function info_alert(type,text) {
	if (type=="warning"){
		$('#modal_info .modal-header').attr('class', 'modal-header bg-red');
		$('#modal_info .btn').attr('class', 'btn btn-danger');
		$('#modal_info .modal-title').html('<i class="fa fa-warning"></i>&nbsp;&nbsp;<?php echo getLang("warning")?>'); 
	}
	else if (type=='success'){
		$('#modal_info .modal-header').attr('class', 'modal-header bg-green');
		$('#modal_info .btn').attr('class', 'btn btn-success');
		$('#modal_info .modal-title').html('<i class="fa fa-check"></i>&nbsp;&nbsp;<?php echo getLang("info")?>'); 
	}
	$('#modal_info .modal-body').html(text); 
	$('#modal_info').modal({keyboard: false, backdrop: 'static'});
}   


function showLoading()
{
	if(jQuery('body').find('#resultLoading').attr('id') != 'resultLoading'){
	jQuery('body').append('<div id="resultLoading" style="display:none"><div><image src="tools/assets/global/images/loading.gif"><div></div></div><div class="bg"></div></div>');
	}

	jQuery('#resultLoading').css({
		'width':'100%',
		'height':'100%',
		'position':'fixed',
		'z-index':'10000000',
		'top':'0',
		'left':'0',
		'right':'0',
		'bottom':'0',
		'margin':'auto'
	});

	jQuery('#resultLoading .bg').css({
		'background':'#000000',
		'opacity':'0.7',
		'width':'100%',
		'height':'100%',
		'position':'absolute',
		'top':'0'
	});

	jQuery('#resultLoading>div:first').css({
		'width': '250px',
		'height':'75px',
		'text-align': 'center',
		'position': 'fixed',
		'top':'0',
		'left':'0',
		'right':'0',
		'bottom':'0',
		'margin':'auto',
		'font-size':'16px',
		'z-index':'10',
		'color':'#ffffff'

	});

    jQuery('#resultLoading .bg').height('100%');
       jQuery('#resultLoading').fadeIn(300);
    jQuery('body').css('cursor', 'wait');
} 

function hideLoading()
{
    jQuery('#resultLoading .bg').height('100%');
       jQuery('#resultLoading').fadeOut(300);
    jQuery('body').css('cursor', 'default');
}
</script>
