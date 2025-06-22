<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Telkom University Open Library</title>
	<base href="<?= base_url() ?>" />   
  <?php $iuser = $this->session->userdata('user_doc'); ?>
	<link rel="shortcut icon" href="cdn/environment/favicon.ico" type="image/x-icon">
	<!--<link rel="icon" href="assets/limitless/layout_1/frontend/images/favicon.ico" type="image/x-icon">-->

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="assets/limitless/global/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="assets/limitless/global/css/icons/fontawesome/styles.min.css" rel="stylesheet" type="text/css">
	<link href="assets/limitless/layout_1/css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="assets/limitless/layout_1/css/core.css" rel="stylesheet" type="text/css">
	<link href="assets/limitless/layout_1/css/components.css" rel="stylesheet" type="text/css">
	<link href="assets/limitless/layout_1/css/colors.css" rel="stylesheet" type="text/css">
	<link href="assets/limitless/global/js/plugins/forms/datepicker/css/datepicker.css" rel="stylesheet" type="text/css">
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
	<div class="navbar navbar-inverse" style="z-index: 27;background-image:url(assets/images/backgrounds/bg.png); margin-bottom:0px !important;background-color: #C9302C;">
		<div class="navbar-header">
			<a class="navbar-brand" href="https://openlibrary.telkomuniversity.ac.id/knowledgeitem.html" style="padding-top: 1.5rem; padding-bottom: 0.75rem">
				<span style="font-size: 20px"><strong>Telkom University Open Library</strong></span>
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
                        <a class="dropdown-toggle" >
                            <span>Hai <?= $iuser['fullname']; ?></span> 
                        </a>  
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

			<!-- Main content -->
			<div class="content-wrapper">

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
                            </div>
                            
                            
                        </div>
					</div>

					<div class="breadcrumb-line">
						<ul class="breadcrumb">
							<li><a href="https://openlibrary.telkomuniversity.ac.id/open/index.php/document"><i class="icon-home2 position-left"></i> Home</a></li>
							<li class="active"><?= isset($title) ? $title : '' ?></li>  
						</ul>
						<ul class="breadcrumb-elements"> 
						</ul>
                        		
					</div>
				</div>
				<!-- /page header -->


				<!-- Content area -->
				<div class="content">
					<?php $this->load->view($view); ?>

</body>
</html>