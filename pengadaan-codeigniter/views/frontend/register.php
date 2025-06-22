 <?php // $iset = y_load_setting(); ?>
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
                    $name  = "";
                    $email = "";
                    $phone = "";
                    if($this->session->flashdata('reg_log')) {
                        $reg_log = $this->session->flashdata('reg_log');
                        $name  = $reg_log['name'];
                        $email = $reg_log['email'];
                        $phone = $reg_log['phone'];
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
                <div class="tabbable panel login-form width-600">
                    <ul class="nav nav-tabs nav-justified">
                        <li class="active"><a href="#basic-tab1" data-toggle="tab"><h6><i class="icon-user-plus position-left"></i> Login</h6></a></li>
                        <li><a href="#basic-tab2" data-toggle="tab"><h6><i class="icon-user-plus position-left"></i> Register Umum</h6></a></li>
                        <li><a href="#basic-tab3" data-toggle="tab"><h6><i class="icon-user-plus position-left"></i> Register Alumni</h6></a></li>
                    </ul>

                    <div class="tab-content panel-body">
                        <div class="tab-pane fade in active" id="basic-tab1">
                            <form action="login/exe" method="post" id="frm">
                                <div class="text-center">
                                    <!--<img src="cdn/environment/logo.png" height="70" style="height: 70px">-->
                                    <h5 class="content-group"><?php//strtoupper($iset['website_name']) 
									?> <small class="display-block">Masukkan Username dan Password Peserta Anda</small></h5>
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
                                    <a href="javascript:show()"><i class="icon-user-plus position-left"></i>Buat Akun Baru</a> &nbsp; &nbsp; &nbsp; | &nbsp; &nbsp; &nbsp;
                                    <a target="_blank" href="https://ittelkom-sby.ac.id/wp-content/uploads/2020/01/Panduan-Pendaftaran-PMB.pdf">Panduan Pendaftaran</a>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="basic-tab2">
                            <form action="login/reg" method="post" id="frm2" onsubmit="if(grecaptcha.getResponse() == '') { alert('Centang Terlebih Dahulu I\'m not a robot'); return false; }" enctype="multipart/form-data">
								<input type="hidden" name="type" value="umum">
                                <div class="text-center">
                                    <!--<img src="cdn/environment/logo.png" height="70" style="height: 70px">-->
                                    <h5 class="content-group"><?php //strtoupper($iset['website_name'])
									?> <small class="display-block">Masukkan Identitas Anda</small></h5>
                                </div>

                                <div class="form-group has-feedback has-feedback-left">
                                    <input type="text" name="inp[par_fullname]" id="par_fullname" class="form-control" placeholder="Nama Lengkap" required="required" minlength="2" maxlength="100" value="<?=$name?>">
                                    <div class="form-control-feedback">
                                        <i class="icon-user-check text-muted"></i>
                                    </div>
                                </div>

                                <div class="form-group has-feedback has-feedback-left">
                                    <input type="password" name="inp[par_password]" id="par_password" class="form-control" placeholder="Password" required="required" minlength="2" maxlength="100">
                                    <div class="form-control-feedback">
                                        <i class="icon-user-check text-muted"></i>
                                    </div>
                                </div>

                                <div class="form-group has-feedback has-feedback-left">
                                    <input type="text" name="inp[par_mobile]" id="par_mobile" class="form-control" placeholder="Nomor Handphone" required="required" minlength="5" maxlength="14" data-rule-phoneid="true" value="<?=$phone?>">
                                    <div class="form-control-feedback">
                                        <i class=" icon-mobile text-muted"></i>
                                    </div>
                                </div>

                                <div class="form-group has-feedback has-feedback-left">
                                    <input type="text" name="inp[par_institution]" id="par_institution" class="form-control" placeholder="Institution" required="required" minlength="5" value="<?=$phone ?>">
                                    <div class="form-control-feedback">
                                        <i class=" icon-mobile text-muted"></i>
                                    </div>
                                </div>


                                <div class="form-group has-feedback has-feedback-left">
                                    <input type="text" name="inp[par_email]" id="par_email" class="form-control" placeholder="Alamat Email" required="required" maxlength="150" data-rule-customemail="true" value="<?=$email?>">
                                    <div class="form-control-feedback">
                                        <i class="icon-mention text-muted"></i>
                                    </div>
                                    <span class="help-block"> Waijib menggunakan email institusi</span>
                                </div>  

                                <!--<div class="form-group">
                                    <div class="g-recaptcha" data-sitekey="6Lf0nHIUAAAAAHMAk0CWEBpAHgO-XJXG9JTzLJ7r"></div>
                                </div>-->

                                <button type="submit" class="btn bg-danger-800 btn-block">Register <i class="icon-circle-right2 position-right"></i></button>

                                <div class="content-divider text-muted form-group"></div>

                                <span class="help-block text-center no-margin">Sudah memiliki Akun ? <strong><a  href="https://openlibrary.telkomuniversity.ac.id">Silahkan Login</a></strong></span>
                            </form>
                        </div>
						
						<div class="tab-pane fade" id="basic-tab3">
                            <form action="login/reg_alumni" method="post" id="frm3" onsubmit="if(grecaptcha.getResponse() == '') { alert('Centang Terlebih Dahulu I\'m not a robot'); return false; }" enctype="multipart/form-data">
								<input type="hidden" name="type" value="alumni">
                                <div class="text-center">
                                    <!--<img src="cdn/environment/logo.png" height="70" style="height: 70px">-->
                                    <h5 class="content-group"><?php //strtoupper($iset['website_name'])
									?> <small class="display-block">Masukkan Identitas Anda</small></h5>
                                </div>

                                <div class="form-group has-feedback has-feedback-left">
                                    <input type="text" name="inp[par_fullname]" id="par_fullname" class="form-control" placeholder="Nama Lengkap" required="required" minlength="2" maxlength="100" value="<?=$name?>">
                                    <div class="form-control-feedback">
                                        <i class="icon-user-check text-muted"></i>
                                    </div>
                                </div>

                                <div class="form-group has-feedback has-feedback-left">
                                    <input type="password" name="inp[par_password]" id="par_password" class="form-control" placeholder="Password" required="required" minlength="2" maxlength="100">
                                    <div class="form-control-feedback">
                                        <i class="icon-user-check text-muted"></i>
                                    </div>
                                </div>

                                <div class="form-group has-feedback has-feedback-left">
                                    <input type="text" name="inp[par_mobile]" id="par_mobile" class="form-control" placeholder="Nomor Handphone" required="required" minlength="5" maxlength="14" data-rule-phoneid="true" value="<?=$phone?>">
                                    <div class="form-control-feedback">
                                        <i class=" icon-mobile text-muted"></i>
                                    </div>
                                </div>

                                <div class="form-group has-feedback has-feedback-left">
                                    <input type="text" name="inp[par_email]" id="par_email" class="form-control" placeholder="Alamat Email" required="required" maxlength="150" data-rule-customemail="true" value="<?=$email?>">
                                    <div class="form-control-feedback">
                                        <i class="icon-mention text-muted"></i>
                                    </div>
                                    <span class="help-block">Domain email yang digunakan adalah @gmail.com</span>
                                </div> 

                                <div class="form-group has-feedback has-feedback-left">   
										<input type="radio" name="ijasah" required value="6"> Paket Berlangganan 6 Bulan (150.0000) <br>
										<input type="radio" name="ijasah" required value="12"> Paket Berlangganan 12 Bulan (250.000)  
                                </div>

                                <div class="form-group has-feedback has-feedback-left"> 
									<label class="font-weight-bold">Ijasah </label><br>
                                    <input type="file" name="inp[par_ijazah]" id="par_ijazah" class="form-control" required> 
                                </div>

                                <div class="form-group has-feedback has-feedback-left">
									<label class="font-weight-bold">Bukti Transfer Pembayaran </label><br>
                                    <input type="file" name="inp[par_bukti]" id="par_bukti" class="form-control" required> 
                                    <span class="help-block">•	Nomor rekening : 131.00.142161.56 (Bank Mandiri)<br>
															•	Atas nama	: Siti Mintarsih Oktrianti
									</span>
                                </div>

                                <!--<div class="form-group">
                                    <div class="g-recaptcha" data-sitekey="6Lf0nHIUAAAAAHMAk0CWEBpAHgO-XJXG9JTzLJ7r"></div>
                                </div>-->

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
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/validation/additional_methods.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/tables/datatables/datatables.min.js"></script>
<script src='https://www.google.com/recaptcha/api.js'></script>
<script type="text/javascript" src="assets/limitless/global/js/core/setting.js"></script>
<script>
    $(document).ready(function() {
        <?php if($this->session->flashdata('reg_log')) { ?>
        $('.nav-tabs a[href="#basic-tab2"]').tab('show');
        <?php } ?>
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