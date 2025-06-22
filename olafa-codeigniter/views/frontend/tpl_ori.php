<?php $iuser = y_info_login_student(); $iset = y_load_setting(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= $iset['website_name'] ?></title>
	<base href="<?= base_url() ?>" />   
    
    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon">
	<!--<link rel="shortcut icon" href="assets/frontend/images/favicon.ico" type="image/x-icon">
	<link rel="icon" href="assets/frontend/images/favicon.ico" type="image/x-icon">-->

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="assets/css/icons/fontawesome/styles.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="assets/css/core.css" rel="stylesheet" type="text/css">
	<link href="assets/css/components.css" rel="stylesheet" type="text/css">
	<link href="assets/css/colors.css" rel="stylesheet" type="text/css">
	<link href="assets/css/custom.css" rel="stylesheet" type="text/css">
	<link href="assets/js/plugins/forms/bootstrap-datepicker/dist/css/bootstrap-datepicker.css" rel="stylesheet" type="text/css">
	<!-- /global stylesheets -->
    
    <style>
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

<body class="<?= isset($body) && !empty($body) ? $body : '' ?>">
	
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
    
	<!-- Main navbar -->
	<div class="navbar navbar-inverse bg-danger-800" style="background-image:url(assets/images/backgrounds/bg.png)">
		<div class="navbar-header">
			<a class="navbar-brand" href="" style="padding:5px; 20px">
            	<img src="assets/images/logo_light.png" alt="" style="height:32px; padding-top:0px">
            </a>

			<ul class="nav navbar-nav visible-xs-block">
				<li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
				<li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
			</ul>
		</div>

		<div class="navbar-collapse collapse" id="navbar-mobile">
			<ul class="nav navbar-nav">
				<li><a class="sidebar-control sidebar-main-toggle hidden-xs"><i class="icon-paragraph-justify3"></i></a></li>
			</ul>

			<div class="navbar-right">
				<ul class="nav navbar-nav">
                	<li class="dropdown dropdown-user">
                        <a class="dropdown-toggle" data-toggle="dropdown">
                            <span><?= y_greeting('id', 'yes'); ?>, <?= $iuser->student_username ?>!</span>
                            <i class="caret"></i>
                        </a>
                
                        <ul class="dropdown-menu dropdown-menu-right"> 
                            <li><a href="login/logout"><i class="icon-switch2"></i> Logout</a></li>
                        </ul>
            		</li>
            	</ul>
            </div>
		</div>
	</div>
	<!-- /main navbar -->


	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

			<!-- Main sidebar -->
			<div class="sidebar sidebar-main">
				<div class="sidebar-content">

					<!-- User menu -->
					<div class="sidebar-user">
						<div class="category-content">
							<div class="media">
								<a href="#" class="media-left"><img src="<?=(y_photo_siswa()==""?'assets/avatar-default.png':y_photo_siswa()) ?>" class="img-circle img-sm" alt="" width="36" height="36"></a>
								<div class="media-body">
									<span class="media-heading text-semibold"><?= $iuser->student_username ?></span>
								</div>

								<div class="media-right media-middle">
									<ul class="icons-list">
										<li>
											<a href="javascript:;"><i class="icon-cog3"></i></a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<!-- /user menu -->


					<!-- Main navigation -->
					<div class="sidebar-category sidebar-category-visible">
						<div class="category-content no-padding">
							<ul class="navigation navigation-main navigation-accordion">
                            	<li class="navigation-header"><span>Menu</span> <i class="icon-menu" title="" data-original-title="Forms"></i></li>
								<li><a href="pendaftaran/home"><i class="icon-profile"></i> <span>Pendaftaran Calon Mahasiswa</span></a></li>
                                <li><a href="pendaftaran/kelulusan"><i class="icon-graduation2"></i> <span>Kelulusan</span></a></li> 
							</ul>
						</div>
					</div>
					<!-- /main navigation -->

				</div>
			</div>
			<!-- /main sidebar -->


			<!-- Main content -->
			<div class="content-wrapper">
				
                <?php if(!isset($header) or $header) { ?>
				<!-- Page header -->
				<div class="page-header page-header-default page-header-xs">
					<div class="page-header-content">
						<div class="page-title" style="padding-bottom:10px; padding-top:10px">
							<h4><i class="<?= isset($icon) ? $icon : 'icon-pushpin' ?> position-left"></i> <?= isset($title) ? $title : '' ?></h4>
						</div>
                        <div class="heading-elements">
                            <div class="form-group">
                                <div class="daterange-custom" id="reportrange">
                                    <div class="daterange-custom-display">
                                    	<i><?= date('d') ?></i> <b><i><?= y_get_month(date('m')) ?></i> <i><?= date('Y') ?></i></b>
                                    </div>
                                </div>
                                
                                <ul class="icons-list">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="icon-grid3"></i> <span class="caret"></span></a>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li><a href="pendaftaran/home">Pendaftaran Calon Mahasiswa</a></li>
                                            <li><a href="pendaftaran/kelulusan">Kelulusan</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            
                            
                        </div>
					</div>

					<div class="breadcrumb-line">
						<ul class="breadcrumb">
							<li><a href=""><i class="icon-home2 position-left"></i> Home</a></li>
							<li class="active"><?= isset($title) ? $title : '' ?></li>
						</ul>
                        		
					</div>
				</div>
				<!-- /page header -->
				<?php } ?>

				<!-- Content area -->
				<div class="content">
					<?php $this->load->view($view); ?>

</body>
</html>