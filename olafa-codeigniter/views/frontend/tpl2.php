 
<!doctype html>

<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Telkom University Open Library</title>
  <?php $iuser = $this->session->userdata('user'); ?>
	<base href="<?= base_url() ?>" />   

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css"> 
	<link href="assets/limitless/global/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="assets/limitless/global/css/icons/fontawesome/styles.min.css" rel="stylesheet" type="text/css">
    
    <link href="assets/limitless/layout_4/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="assets/limitless/layout_4/css/core.min.css" rel="stylesheet" type="text/css">
	<link href="assets/limitless/layout_4/css/components.min.css" rel="stylesheet" type="text/css">
	<link href="assets/limitless/layout_4/css/colors.min.css" rel="stylesheet" type="text/css">
	<!-- /global stylesheets -->

    <link rel="shortcut icon" href="assets/images/favicon.ico" type="image/x-icon"> 


    <style>
	.content {
		padding: 0 10px 80px;
	}
	.page-header-default {
		margin-bottom: 10px;
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

    .navbar-brand {
        text-transform: uppercase;
        font-weight: bold;
        font-size: 18px;
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
<body> 
<noscript> 
    <style type="text/css">
        .body-container { display:none; }
    </style>
    <div class="noscriptmsg" style="text-align:center;">
        <img src="cdn/JavaScript-Disabled-Notice.png">
    </div>
</noscript>

<div class="body-container">
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

	<div class="navbar navbar-inverse" style="z-index: 27;background-image:url(assets/images/backgrounds/bg.png); margin-bottom:0px !important;background-color: #10528B;">
		<div class="navbar-header">
			<a href="" class="navbar-brand">
							DIGITAL LIBRARY BRIN
			</a>
            
            <ul class="nav navbar-nav pull-right visible-xs-block">
				<li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
			</ul>
		</div>

		<div class="navbar-collapse collapse" id="navbar-mobile">
            <ul class="nav navbar-nav navbar-right">
				<li class="dropdown dropdown-user">
					<a class="dropdown-toggle" data-toggle="dropdown"> 
								<span>Hai <?= $iuser['fullname']; ?></span>
						<i class="caret"></i>
					</a>

					<ul class="dropdown-menu dropdown-menu-right">
						<li><a href="index.php/login/logout"><i class="icon-switch2"></i> Logout</a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div>

    <!-- Second navbar -->
    <div class="navbar navbar-default" id="navbar-second">
        <ul class="nav navbar-nav no-border visible-xs-block">
            <li><a class="text-center collapsed" data-toggle="collapse" data-target="#navbar-second-toggle"><i class="icon-menu7"></i></a></li>
        </ul>

        <div class="navbar-collapse collapse" id="navbar-second-toggle">
            <ul class="nav navbar-nav">
                <li>
				    			<a href="https://openlibrary.telkomuniversity.ac.id"><i class="icon-home position-left"></i> Home</a>
                </li>
                <li class="<?=($view=='frontend/dashboard/index'?'active':'')?>">
				    			<a href=""><i class="icon-user position-left"></i> Profile</a>
								</li> 

								<?php 
								if ($iuser['usergroup']!='internasional') { ?>
                <li class="<?=($view=='frontend/subscribe/index'?'active':'')?>">
                    <a href="index.php/subscribe"><i class="icon-coins position-left"></i> Berlangganan</a>
                </li>   
								<?php } ?>
            </ul>
        </div>
    </div>
    <!-- End Second navbar -->

	<!-- Page content -->
	<div class="page-container">

		<!-- Main content -->
		<div class="page-content">

			<!-- Main content -->
			<div class="content-wrapper">

                <?php $this->load->view($view); ?>

</div>
</body>
</html>