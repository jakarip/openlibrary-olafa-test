<?php // $iset = y_load_setting(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Telkom University Open Library</title>
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
                    <?php
                    $name               = ""; 
                    $phone              = "";
                    $address            = ""; 
                    $email_umum         = "";
                    $email_alumni       = "";
                    $email_ptasuh       = "";
                    $email_lemdikti     = "";
                    $email_internasional     = "";
                    $institution_umum   = "";
                    $institution_ptasuh   = "";
                    $institution_lemdikti = "";
                    $institution_internasional = "";

                    if($this->session->flashdata('reg_log')) {
                        $reg_log                = $this->session->flashdata('reg_log');
                        $name                   = $reg_log['name']; 
                        $phone                  = $reg_log['phone'];
                        $address                = $reg_log['address']; 
                        $email_umum             = $reg_log['email_umum'];
                        $email_alumni           = $reg_log['email_alumni'];
                        $email_ptasuh           = $reg_log['email_ptasuh'];
                        $email_lemdikti         = $reg_log['email_lemdikti'];
                        $email_internasional    = $reg_log['email_internasional'];
                        $institution_umum       = $reg_log['institution_umum'];
                        $institution_ptasuh     = $reg_log['institution_ptasuh'];
                        $institution_lemdikti   = $reg_log['institution_lemdikti']; 
                        $institution_internasional   = $reg_log['institution_internasional']; 
                    }


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
                        <li class="active"><a href="#basic-tab1" data-toggle="tab"><h6><i class="icon-user-lock position-left"></i> Login</h6></a></li>
                        <li><a href="#basic-tab2" data-toggle="tab"><h6><i class="icon-user-plus position-left"></i> Register</h6></a></li> 
                    </ul>

                    <div class="tab-content panel-body">
                        <div class="tab-pane fade in active" id="basic-tab1">
                            <form action="index.php/login/exe" method="post" id="frm">
                                <div class="text-center">
                                    <!--<img src="cdn/environment/logo.png" height="70" style="height: 70px">-->
                                    <h5 class="content-group">
                                    <?php //strtoupper($iset['website_name'])  ?> 
                                    <small class="display-block">Masukkan Username dan Password Peserta Anda <br>Please input your username and password</small></h5>
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
                                <div class="form-group" align="center">
                                    <a href="javascript:show()"><i class="icon-user-plus position-left"></i>Buat Akun Baru / Make New Account</a> <br><br>
                                    <!--<a target="_blank" href="cdn/panduan_pendaftaran_keanggotaan_umum.pdf?1"><i class="icon-book3 position-left"></i>Panduan Pendaftaran Umum / User Guide</a><br>
                                    <a target="_blank" href="cdn/panduan_pendaftaran_keanggotaan_alumni.pdf?1"><i class="icon-book3 position-left"></i>Panduan Pendaftaran Alumni</a><br>
                                    <a target="_blank" href="cdn/panduan_pendaftaran_keanggotaan_pt.pdf?1"><i class="icon-book3 position-left"></i>Panduan Pendaftaran Lemdikti YPT & PT Asuh</a>-->
                                </div>
                            </form>
                        </div> 
						
						<div class="tab-pane fade" id="basic-tab2">
                            <form action="index.php/login/reg" method="post" id="frm3" enctype="multipart/form-data"> 
                                <div class="text-center">
                                    <!--<img src="cdn/environment/logo.png" height="70" style="height: 70px">-->
                                    <h5 class="content-group"><?php //strtoupper($iset['website_name'])
									?> <small class="display-block">Masukkan Identitas Anda / Please Input Your Identity</small></h5>
                                </div>

                                <div class="form-group has-feedback has-feedback-left">
                                    <input type="text" name="inp[name]" id="name" class="form-control" placeholder="Nama Lengkap / Fullname" required="required" minlength="2" maxlength="100" value="<?=$name?>">
                                    <div class="form-control-feedback">
                                        <i class="icon-user-check text-muted"></i>
                                    </div>
                                </div>
                                <div class="form-group has-feedback has-feedback-left">
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required="required" minlength="2" maxlength="100">
                                    <div class="form-control-feedback">
                                        <i class="icon-user-check text-muted"></i>
                                    </div>
                                </div>

                                <div class="form-group has-feedback has-feedback-left">
                                    <input type="text" name="inp[phone]" id="phone" class="form-control" placeholder="Nomor Handphone / Mobile Phone" required="required" minlength="5" maxlength="14" value="<?=$phone?>">
                                    <div class="form-control-feedback">
                                        <i class=" icon-mobile text-muted"></i>
                                    </div>
                                </div>    
                                <div class="form-group has-feedback has-feedback-left">
                                    <input type="text" name="inp[address]" id="address" class="form-control" placeholder="Alamat / Address" required="required" value="<?=$address ?>">
                                    <div class="form-control-feedback">
                                        <i class="icon-home2 text-muted"></i>
                                    </div> 
                                </div> 
                                <div class="form-group has-feedback has-feedback-left">
                                    <?= form_dropdown('type', $jenis_anggota, '', 'class="form-control select2" id="type" required="required"') ?>
                                </div> 
								<div id="umum" style="display:none"> 
									<div class="form-group has-feedback has-feedback-left">
										<input type="text" name="inp[institution_umum]" id="institution_umum" class="form-control umum" placeholder="Nama Institusi" required="required" value="<?=$institution_umum?>" >
										<div class="form-control-feedback">
											<i class=" icon-office text-muted"></i>
										</div>
									</div> 
									<div class="form-group has-feedback has-feedback-left">
										<input type="text" name="inp[email_umum]" id="email_umum" class="form-control umum" placeholder="Email" required="required" maxlength="150" value="<?=$email_umum ?>">
										<div class="form-control-feedback">
											<i class="icon-mention text-muted"></i>
										</div>
										<span class="help-block">Domain email yang digunakan adalah @gmail.com</span>
									</div>  
									<div class="form-group has-feedback has-feedback-left"> 
										<label class="font-weight-bold">KTP </label><br>
                                        <input type="file" name="ktp_umum" id="ktp_umum" class="form-control umum file_uploads" required="required">  
                                        <span class="help-block">Format yang diterima : <strong>jpg, jpeg, png, pdf</strong>; &nbsp;Maksimal size : <strong>2 MB</strong></span>
									</div> 
									<div class="form-group has-feedback has-feedback-left"> 
										<label class="font-weight-bold">Karpeg / KTM </label><br>
                                        <input type="file" name="idcard_umum" id="idcard_umum" class="form-control  file_uploads">  
                                        <span class="help-block">Format yang diterima : <strong>jpg, jpeg, png, pdf</strong>; &nbsp;Maksimal size : <strong>2 MB</strong></span>
									</div>
								</div>
                                <div id="internasional" style="display:none"> 
									<div class="form-group has-feedback has-feedback-left">
										<input type="text" name="inp[institution_internasional]" id="institution_internasional" class="form-control internasional" placeholder="Nama Institusi / Your Institution" required="required" value="<?=$institution_internasional?>" >
										<div class="form-control-feedback">
											<i class=" icon-office text-muted"></i>
										</div>
									</div> 
									<div class="form-group has-feedback has-feedback-left">
										<input type="text" name="inp[email_internasional]" id="email_internasional" class="form-control internasional" placeholder="Email" required="required" maxlength="150" value="<?=$email_internasional ?>">
										<div class="form-control-feedback">
											<i class="icon-mention text-muted"></i>
										</div> 
									</div>   
								</div>
								<div id="alumni"  style="display:none">  
									<div class="form-group has-feedback has-feedback-left">
										<input type="text" name="inp[email_alumni]" id="email_alumni" class="form-control alumni" placeholder="Email" required="required" maxlength="150"  value="<?=$email_alumni?>">
										<div class="form-control-feedback">
											<i class="icon-mention text-muted"></i>
										</div>
										<span class="help-block">Domain email yang digunakan adalah @gmail.com</span>
									</div>   
									<div class="form-group has-feedback has-feedback-left"> 
										<label class="font-weight-bold">KTP </label><br>
                                        <input type="file" name="ktp_alumni" id="ktp_alumni" class="form-control alumni file_uploads" required="required">  
                                        <span class="help-block">Format yang diterima : <strong>jpg, jpeg, png, pdf</strong>; &nbsp;Maksimal size : <strong>2 MB</strong></span>
									</div>

									<div class="form-group has-feedback has-feedback-left"> 
										<label class="font-weight-bold">Ijasah </label><br>
                                        <input type="file" name="ijasah" id="par_ijazah" class="form-control alumni file_uploads" required="required">  
                                        <span class="help-block">Format yang diterima : <strong>jpg, jpeg, png, pdf</strong>; &nbsp;Maksimal size : <strong>2 MB</strong></span>
									</div>
								</div>
                                <div id="ptasuh"  style="display:none">  
                                    <div class="form-group has-feedback has-feedback-left">
                                        <?= form_dropdown('inp[institution_ptasuh]', $ptasuh, $institution_ptasuh, 'class="form-control select2" id="institution_ptasuh" required="required"') ?>
                                    </div>  
									<div class="form-group has-feedback has-feedback-left">
										<input type="text" name="inp[email_ptasuh]" id="email_ptasuh" class="form-control ptasuh" placeholder="Email" required="required" maxlength="150"  value="<?=$email_ptasuh?>">
										<div class="form-control-feedback">
											<i class="icon-mention text-muted"></i>
										</div>
										<span class="help-block">Domain email yang digunakan adalah domain institusi</span>
									</div>   
									<div class="form-group has-feedback has-feedback-left"> 
										<label class="font-weight-bold">KTP </label><br>
                                        <input type="file" name="ktp_ptasuh" id="ktp_ptasuh" class="form-control ptasuh file_uploads" required="required">  
                                        <span class="help-block">Format yang diterima : <strong>jpg, jpeg, png, pdf</strong>; &nbsp;Maksimal size : <strong>2 MB</strong></span>
									</div>

									<div class="form-group has-feedback has-feedback-left"> 
										<label class="font-weight-bold">Karpeg / KTM </label><br>
                                        <input type="file" name="idcard_ptasuh" id="idcard_ptasuh" class="form-control ptasuh file_uploads" required="required">  
                                        <span class="help-block">Format yang diterima : <strong>jpg, jpeg, png, pdf</strong>; &nbsp;Maksimal size : <strong>2 MB</strong></span>
									</div>
								</div>
                                <div id="lemdikti"  style="display:none">  
                                    <div class="form-group has-feedback has-feedback-left">
                                        <?= form_dropdown('inp[institution_lemdikti]', $lemdikti, $institution_lemdikti, 'class="form-control select2" id="institution_lemdikti" required="required"') ?>
                                    </div>  
									<div class="form-group has-feedback has-feedback-left">
										<input type="text" name="inp[email_lemdikti]" id="email_lemdikti" class="form-control lemdikti" placeholder="Email" required="required" maxlength="150"  value="<?=$email_lemdikti?>">
										<div class="form-control-feedback">
											<i class="icon-mention text-muted"></i>
										</div>
										<span class="help-block">Domain email yang digunakan adalah domain institusi</span>
									</div>  

									<div class="form-group has-feedback has-feedback-left"> 
										<label class="font-weight-bold">KTP </label><br>
                                        <input type="file" name="ktp_lemdikti" id="ktp_lemdikti" class="form-control lemdikti file_uploads" required="required">  
                                        <span class="help-block">Format yang diterima : <strong>jpg, jpeg, png, pdf</strong>; &nbsp;Maksimal size : <strong>2 MB</strong></span>
									</div>

									<div class="form-group has-feedback has-feedback-left"> 
										<label class="font-weight-bold">Karpeg / KTM </label><br>
                                        <input type="file" name="idcard_lemdikti" id="idcard_lemdikti" class="form-control lemdikti file_uploads" required="required">  
                                        <span class="help-block">Format yang diterima : <strong>jpg, jpeg, png, pdf</strong>; &nbsp;Maksimal size : <strong>2 MB</strong></span>
									</div>
								</div> 
                                <div class="form-group">
                                    <div class="g-recaptcha" data-sitekey="6LcImxYaAAAAAHUoFxiAn_YSkARuPZhEeac8flTh"></div>
                                </div>

                                <button type="submit" class="btn bg-danger-800 btn-block">Register <i class="icon-circle-right2 position-right"></i></button> 

                                <div class="content-divider text-muted form-group"></div>

                                <span class="help-block text-center no-margin">Sudah memiliki Akun ? <strong><a  href="https://openlibrary.telkomuniversity.ac.id">Silahkan Login</a></strong></span>
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
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/validation/additional-methods.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/selects/select2.min.js"></script>
<script src='https://www.google.com/recaptcha/api.js'></script>
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
		
		
		$('#type').change(function() {
			if($(this).val()=='umum'){
				$('#alumni').hide();
				$('#ptasuh').hide();
				$('#lemdikti').hide();
				$('#umum').show();
				$('#internasional').hide();

				$('.alumni').attr('required',false);
				$('.ptasuh').attr('required',false);
				$('.lemdikti').attr('required',false);
				$('.internasional').attr('required',false);
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