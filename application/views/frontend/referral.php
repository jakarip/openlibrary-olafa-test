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
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link href="assets/limitless/global/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
    <link href="assets/limitless/global/css/icons/fontawesome/styles.min.css" rel="stylesheet" type="text/css">

    <link href="assets/limitless/layout_1/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="assets/limitless/layout_1/css/core.min.css" rel="stylesheet" type="text/css">
    <link href="assets/limitless/layout_1/css/components.min.css" rel="stylesheet" type="text/css">
    <link href="assets/limitless/layout_1/css/colors.min.css" rel="stylesheet" type="text/css">
    <!-- /global stylesheets -->
	
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

<body class="login-container login-cover" style="background-image:url(cdn/environment/login_cover_user.jpg);">

	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

			<!-- Main content -->
			<div class="content-wrapper">

				<!-- Content area -->  
				<div class="content">
                    <div class="login-form width-400">
                        <?php
                        if($this->session->flashdata('login_log')) {
                            $log = $this->session->flashdata('login_log');
                        ?>
                            <div class="alert alert-<?= $log['status'] ?> alert-bordered">
                                <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>
                                <span class="text-semibold"><?= $log['text'] ?>
                            </div>
                        <?php } ?>
                    </div>
                    <!-- Tabbed form -->
                    <div class="tabbable panel login-form width-400">
                        <ul class="nav nav-tabs nav-justified">
                            <li class="active"><a href="#basic-tab1" data-toggle="tab"><h6><i class="icon-user-lock position-left"></i> Login Referral</h6></a></li>
                            <li><a href="#basic-tab2" data-toggle="tab"><h6><i class="icon-user-plus position-left"></i> Buat Akun Baru</h6></a></li>
                        </ul>

                        <div class="tab-content panel-body">
                            <div class="tab-pane fade in active" id="basic-tab1">
                                <form action="referral/exe" method="post" id="frm">
                                    <div class="text-center">
                                        <img src="cdn/environment/logo.png" height="50" style="height: 50px; margin-bottom: 10px">
                                        <h5 class="content-group">Login Agen Referral <small class="display-block"><?= strtoupper($iset['website_name']) ?></small></h5>
                                    </div>

                                    <div class="form-group has-feedback has-feedback-left">
                                        <input type="text" class="form-control" placeholder="Username" name="username" required="required">
                                        <div class="form-control-feedback">
                                            <i class="icon-user text-muted"></i>
                                        </div>
                                    </div>

                                    <div class="form-group has-feedback has-feedback-left">
                                        <input type="password" class="form-control" placeholder="Password" name="password" required="required">
                                        <div class="form-control-feedback">
                                            <i class="icon-lock2 text-muted"></i>
                                        </div>
                                    </div>

                                    <div class="form-group login-options">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label class="checkbox-inline">
                                                    <input type="checkbox" class="styled" checked="checked">
                                                    Remember
                                                </label>
                                            </div>

                                            <div class="col-sm-6 text-right">
                                                <a href="#">Forgot password?</a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn bg-danger-800 btn-block">Login <i class="icon-arrow-right14 position-right"></i></button>
                                    </div>
                                </form>

                                <span class="help-block text-center no-margin">By continuing, you're confirming that you've read our <a href="#">Terms &amp; Conditions</a> and <a href="#">Cookie Policy</a></span>
                            </div>

                            <div class="tab-pane fade" id="basic-tab2">
                                <form action="referral/reg" method="post" id="frm2" onsubmit="if(grecaptcha.getResponse() == '') { alert('Centang Terlebih Dahulu I\'m not a robot'); return false; }">
                                    <div class="text-center">
                                        <img src="cdn/environment/logo.png" height="50" style="height: 50px; margin-bottom: 10px">
                                        <h5 class="content-group">Pendaftaran Agen Referral <small class="display-block"><?= strtoupper($iset['website_name']) ?></small></h5>
                                    </div>

                                    <div class="form-group has-feedback has-feedback-left">
                                        <input type="text" name="inp[ref_fullname]" id="ref_fullname" class="form-control" placeholder="Nama Lengkap" required="required" minlength="2" maxlength="100">
                                        <div class="form-control-feedback">
                                            <i class="icon-user-check text-muted"></i>
                                        </div>
                                    </div>

                                    <div class="form-group has-feedback has-feedback-left">
                                        <input type="text" name="inp[ref_username]" id="ref_username" class="form-control" placeholder="Username" required="required" minlength="6" maxlength="25">
                                        <div class="form-control-feedback">
                                            <i class="icon-user-check text-muted"></i>
                                        </div>
                                        <span class="help-block no-margin no-padding">Username Merupakan <strong>Kode Referral</strong> Anda</span>
                                    </div>

                                    <div class="form-group has-feedback has-feedback-left">
                                        <input type="text" name="inp[ref_phone]" id="ref_phone" class="form-control" placeholder="Nomor Handphone" required="required" minlength="5" maxlength="14" data-rule-phoneid="true">
                                        <div class="form-control-feedback">
                                            <i class=" icon-mobile text-muted"></i>
                                        </div>
                                        <span class="help-block no-margin no-padding">Pastikan No HP Terdaftar di LinkAja</span>
                                    </div>

                                    <div class="form-group has-feedback has-feedback-left">
                                        <input type="text" name="inp[ref_email]" id="ref_email" class="form-control" placeholder="Alamat Email" required="required" maxlength="150" data-rule-customemail="true">
                                        <div class="form-control-feedback">
                                            <i class="icon-mention text-muted"></i>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="g-recaptcha" data-sitekey="6Lf0nHIUAAAAAHMAk0CWEBpAHgO-XJXG9JTzLJ7r"></div>
                                    </div>

                                    <button type="submit" class="btn bg-danger-800 btn-block">Register <i class="icon-circle-right2 position-right"></i></button>
                                </form>
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
    <script type="text/javascript" src="assets/limitless/global/js/plugins/forms/validation/additional_methods.min.js"></script>
    <script type="text/javascript" src="assets/limitless/global/js/plugins/tables/datatables/datatables.min.js"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script type="text/javascript" src="assets/limitless/global/js/core/setting.js"></script>
    <!-- /core JS files -->
</body>
</html>