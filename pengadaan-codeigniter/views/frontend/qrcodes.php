<?php // $iset = y_load_setting(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Digilib BRIN</title>
    <base href="<?= base_url() ?>" />

    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">

    <!-- Global stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link href="assets/limitless/global/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
    <link href="assets/limitless/global/css/icons/fontawesome/styles.min.css" rel="stylesheet" type="text/css">

    <link href="assets/limitless/layout_1/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="assets/limitless/layout_1/css/core.min.css" rel="stylesheet" type="text/css">
    <link href="assets/limitless/layout_1/css/components.min.css" rel="stylesheet" type="text/css">
    <link href="assets/limitless/layout_1/css/colors.min.css" rel="stylesheet" type="text/css">
    <!-- /global stylesheets -->

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-141035950-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-141035950-1');
    </script>

    <style>
        @media (min-width:769px) {
            .login-container .page-container .login-form {
                margin:0px 20px;
                -webkit-box-shadow: 1px 2px 14px 1px rgba(38,38,38,.5);
                -moz-box-shadow: 1px 2px 14px 1px rgba(38,38,38,.5);
                box-shadow: 1px 2px 14px 1px rgba(38,38,38,.5);
            }
            .login-container .content {
                padding:20px;
            }
            .login-container .page-container {
                padding-top: 20px;
            }
        }

    </style>
</head>

<body class="login-container login-cover" style="background-image:url(cdn/environment/login_cover_user.jpg?abc);">

<!-- Page container -->
<div class="page-container">

    <!-- Page content -->
    <div class="page-content">

        <!-- Main content -->
        <div class="content-wrapper">

            <!-- Content area -->
            <div class="content">
                <div class="login-form width-400">
                    
                </div>
                <!-- Tabbed form -->
                <div class="tabbable panel">
                    <ul class="nav nav-tabs nav-justified"> 
                        <li class="active"><a href="#basic-tab1" data-toggle="tab"><h2><i class="icon-qrcode position-left"></i> QR Code Guest Book</h2></a></li> 
                    </ul>
					<div style="text-align:center;font-size:14px;color:red;">
					<strong>Silahkan melakukan scan QR Code untuk mengisi buku tamu :</strong>
					</div>
                    <div class="tab-content panel-body"> 
						<div class="tab-pane fade in active" id="basic-tab1" style="text-align:center">
                             <?php echo $qrcode ?>
                        </div>
                    </div>
                </div>
                <!-- /tabbed form -->

            </div>
            <!-- /content area -->

        </div>
        <!-- /main content -->

    </div>
    <!-- /page content -->

</div>
<!-- /page container --> 

<!-- Core JS files -->
<script type="text/javascript" src="assets/limitless/global/js/core/libraries/jquery.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/core/libraries/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/loaders/blockui.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/validation/validate.min.js"></script> 
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/validation/additional-methods.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/selects/select2.min.js"></script>
<!--<script src='https://www.google.com/recaptcha/api.js'></script>-->
<script type="text/javascript" src="assets/limitless/global/js/core/setting.js"></script>
<script>
    $(document).ready(function() {

        

        $.validator.addClassRules({
            file_uploads:{
                extension: "jpg|jpeg|png|pdf",
                maxFileSize: {
                    "unit": "MB",
                    "size": 2
                } 
            },
        });

        <?php if($this->session->flashdata('reg_log')) { ?>
        $('.nav-tabs a[href="#basic-tab2"]').tab('show');
        <?php } ?>
		
		$('#type').select2();
		
		$('#institution_ptasuh').select2();
		$('#institution_lemdikti').select2();
		
		$('#umum').show(); 
		$('.umum').attr('required',true);
		
		$('#type').change(function() {
			if($(this).val()=='umum'){ 
				$('#umum').show(); 
				$('.umum').attr('required',true);
			}
			else if($(this).val()=='alumni'){
				$('#alumni').show();
				$('#ptasuh').hide();
				$('#lemdikti').hide();
				$('#umum').hide();
				$('#internasional').hide();

				$('.alumni').attr('required',true);
				$('.ptasuh').attr('required',false);
				$('.lemdikti').attr('required',false);
				$('.internasional').attr('required',false);
				$('.umum').attr('required',false);
			}
			else if($(this).val()=='ptasuh'){
				$('#alumni').hide();
				$('#ptasuh').show();
				$('#lemdikti').hide();
				$('#umum').hide();
				$('#internasional').hide();

				$('.alumni').attr('required',false);
				$('.ptasuh').attr('required',true);
				$('.lemdikti').attr('required',false);
				$('.internasional').attr('required',false);
				$('.umum').attr('required',false);
			}
			else if($(this).val()=='lemdikti'){
				$('#alumni').hide();
				$('#ptasuh').hide();
				$('#lemdikti').show();
				$('#umum').hide();
				$('#internasional').hide();

				$('.alumni').attr('required',false);
				$('.ptasuh').attr('required',false);
				$('.lemdikti').attr('required',true);
				$('.internasional').attr('required',false);
				$('.umum').attr('required',false);
			}
			else if($(this).val()=='internasional'){
				$('#alumni').hide();
				$('#ptasuh').hide();
				$('#lemdikti').hide();
				$('#umum').hide();
				$('#internasional').show();

				$('.alumni').attr('required',false);
				$('.ptasuh').attr('required',false);
				$('.lemdikti').attr('required',false);
				$('.internasional').attr('required',true);
				$('.umum').attr('required',false);
			}
			else {
				$('#umum').hide();
                $('#alumni').hide();
				$('#ptasuh').hide();
				$('#lemdikti').hide();
				$('#internasional').hide();
			}
		});
    });
    function show() {
        $('.nav-tabs a[href="#basic-tab2"]').tab('show');
    }
    function show_login() {
        $('.nav-tabs a[href="#basic-tab1"]').tab('show');
    } 
</script>
<!-- /core JS files -->
</body>
</html>