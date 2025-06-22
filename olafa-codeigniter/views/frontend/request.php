<?php $iset = y_load_setting(); ?>
<!DOCTYPE html>
 
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= $iset['website_name'] ?></title>
	<base href="<?= base_url() ?>" />
    
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">

	<!-- Global stylesheets -->
	<link href="assets/fonts/roboto/roboto-v15.css" rel="stylesheet" type="text/css">
	<link href="assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="assets/css/icons/fontawesome/styles.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="assets/css/core.css" rel="stylesheet" type="text/css">
	<link href="assets/css/components.css" rel="stylesheet" type="text/css">
	<link href="assets/css/colors.css" rel="stylesheet" type="text/css">
	<link href="assets/css/custom.css" rel="stylesheet" type="text/css"> 
	<!-- /global stylesheets -->
	
	
<style>
.login-container .page-container .login-form {
    width: 343px !important;
}

.error {
	color: #f44336;
}

.content {
	padding: 0 10px 80px;
}
.page-header-default {
	margin-bottom: 10px;
}
.daterange-custom-display::after {
	content:'';
}
.icons-list {
	margin-left:30px !important;
}
.form-group {
	margin-bottom:10px;
}
.btn-table {
	padding:2px 8px !important;
	border-radius:0px !important;
}
.label-table {
	font-size:11px;
}
.center {
	text-align:center;
}

.yloading {
	width:100%;
	height:100%;
	padding:0px;
	margin:0px;
	top:0px;
	left:0px;
	background-color:rgba(51,51,51,0.5);
	z-index: 130299;
	position: fixed;
	display:none;
}
.loader-container {
	width: 150px;
	height: 150px;
	padding: 50px;
	background-color:#232323;
	color:#FFF;
	text-align: center;
	border-radius: 10px !important;
	position: fixed;
	z-index: 130300;
	left: 50%;
	top: 50%;
	margin-left: -150px;
	margin-top: -150px;
	
	box-sizing:content-box !important;
}

.theme_xbox, .theme_xbox_sm, .theme_xbox_xs {
	width: 100px;
	height: 100px;
}

.theme_xbox .pace_activity, .theme_xbox_sm .pace_activity, .theme_xbox_xs .pace_activity {
	width: 150px;
	height: 150px;
}

.theme_xbox .pace_activity, .theme_xbox .pace_activity::after, .theme_xbox .pace_activity::before, .theme_xbox_sm .pace_activity, .theme_xbox_sm .pace_activity::after, .theme_xbox_sm .pace_activity::before, .theme_xbox_xs .pace_activity, .theme_xbox_xs .pace_activity::after, .theme_xbox_xs .pace_activity::before {
	border-top-color: #61ED00;
}

.theme_xbox_with_text span {
	width:150px;
	margin-top: 7px;
}
</style> 
</head>
<!-- Loading -->
		<div class="yloading" id="loading-img">
		<div class="loader-container">
			<div class="theme_xbox theme_xbox_with_text">
				<div class="pace_progress" data-progress-text="60%" data-progress="60"></div>
				<div class="pace_activity"></div> <span>LOADING...</span>
			</div>
		</div>
		</div>
	<!-- Loading -->
<body class="login-container" style="background-image:url(assets/images/backgrounds/login_cover_user.jpg);">
	
    
	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

			<!-- Main content -->
			<div class="content-wrapper">

				<!-- Content area -->  
				<div class="content">
					
                    <!-- Advanced login -->
					<form method="post" id="form-request">
						<div class="panel panel-body login-form">
							<div class="text-center">
								<img src="assets/images/logo_light_2.png">
								<h5 class="content-group-lg">Request PIN<br><?= $iset['website_name'] ?></h5>
							</div>
                           
                            <div class="alert alert-danger alert-bordered" style="display:none">
                                <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>
                                <span class="text-semibold">
                            </div> 
                            <div class="alert alert-success alert-bordered" style="display:none">
                                <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>
                                <span class="text-semibold">
                            </div> 
							<div class="error">
								<div class="form-group has-feedback has-feedback-left">
									<input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
									<div class="form-control-feedback">
										<i class="icon-mail5 text-muted"></i>
									</div>
								</div> 
							</div>
							<!--<div class="form-group">                    
								<div class="g-recaptcha" data-sitekey="6Lev9lkUAAAAAC0s9LWdeCVMHvOfQrx-KubD80Za"></div>
							</div>-->
							<div class="g-recaptcha" data-sitekey="6LchJ1oUAAAAAKo_POT4gLPWDl3ywwpAQbReoASp"></div>

							<div class="form-group">
								<button type="button" onclick="save()" class="btn bg-danger-800 btn-block">Request <i class="icon-circle-right2 position-right"></i></button>
							</div> 
						</div>
					</form>
					<!-- /advanced login -->

				</div>
				<!-- /content area -->

			</div>
			<!-- /main content -->

		</div>
		<!-- /page content -->

	</div>
	<!-- /page container -->

</body>
</html>

<script type="text/javascript" src="assets/js/core/libraries/jquery.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/validation/additional-methods.js"></script>
<!--<script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=id"></script>-->
<script src='https://www.google.com/recaptcha/api.js'></script>
<script>

var form_reg 	= $('#form-request');

$(document).ready(function(){ 
form_reg.validate({ 
		highlight: function (element) { // hightlight error inputs
			$(element)
				.closest('.form-group').addClass('has-error'); // set error class to the control group
		},
		success: function (label) {
			$('.form-group').removeClass('has-error');
			label.remove();
		}, 
		errorPlacement: function (error, element) {
			error.insertAfter(element.closest('.error'));
		}
	});
});	
	
function save()
{
	
	if(form_reg.valid()){
		if (confirm("Silahkan cek email anda : \n"+$('#email').val()+"\n\n Apakah email anda sudah benar ?")) {
			$.ajax({
				type : "POST",
				url : "request/exe",
				data : $('#form-request').serialize(),
				dataType:'json',
				global:false,
				async:true,
				success : function (e) {
					if(e.status == 'ok;')
					{
						$('.alert-success .text-semibold').html("Terima kasih telah melakukan request PIN.\n\nSilahkan cek email anda untuk melihat PIN.\nJika email tidak ada silahkan cek folder SPAM."); 
						$('.alert-success').show();
						$('.alert-danger').hide(); 
					}
					else
					{
						
						//alert(e.text);
						$('.text-semibold').html(e.text); 
						$('.alert-danger').show(); 
					
						grecaptcha.reset();	
						
						document.body.scrollTop = 0; // For Safari
						document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
					}		
				},
				error : function() {
					alert('Terjadi gangguan jaringan silahkan ulangi kembali atau hubungi administrator');	 
				},
				beforeSend : function() {
					$('#loading-img').show();
				},
				complete : function() {
					$('#loading-img').hide();
				}
			});	
		}
	}
}

</script>