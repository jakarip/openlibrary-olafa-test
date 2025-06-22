<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head> 
    <base href="<?= base_url() ?>" />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="admin-themes-lab">
    <meta name="author" content="themes-lab">
    <link rel="shortcut icon" href="tools/assets/global/images/favicon.png" type="image/png">
    <title>Azzurra |
        <?php echo getCurrentMenuName() ?>
    </title>
    <link href="tools/assets/global/css/style.css" rel="stylesheet">
    <link href="tools/assets/global/css/theme.css" rel="stylesheet">
    <link href="tools/assets/global/css/custom.css" rel="stylesheet">
    <link href="tools/assets/global/css/ui.css" rel="stylesheet">
    <link href="tools/assets/admin/layout1/css/layout.css" rel="stylesheet">
    <script src="tools/assets/global/plugins/modernizr/modernizr-2.6.2-respond-1.1.0.min.js"></script>
    <link href="tools/assets/global/plugins/datatables1/plugins/bootstrap/dataTables.bootstrap.css" rel="stylesheet">
<!--     <link href='<?php echo base_url();?>tools/assets/global/js/jquery.autocomplete.css' rel='stylesheet' /> --> 
    <link href="tools/assets/global/plugins/font-awesome-animation/font-awesome-animation.min.css" rel="stylesheet">   
	<link rel="stylesheet" type="text/css" href="tools/assets/global/plugins/fancyapps-fancyBox/source/jquery.fancybox.css?v=2.1.5" media="screen" />
	<!-- Add Button helper (this is optional) -->
	<link rel="stylesheet" type="text/css" href="tools/assets/global/plugins/fancyapps-fancyBox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" />

<!--     <link href="tools/assets/global/plugins/datatables/dataTables.min.css" rel="stylesheet"> -->


</head>
<!-- LAYOUT: Apply "submenu-hover" class to body element to have sidebar submenu show on mouse hover -->
<!-- LAYOUT: Apply "sidebar-collapsed" class to body element to have collapsed sidebar -->
<!-- LAYOUT: Apply "sidebar-top" class to body element to have sidebar on top of the page -->
<!-- LAYOUT: Apply "sidebar-hover" class to body element to show sidebar only when your mouse is on left / right corner -->
<!-- LAYOUT: Apply "submenu-hover" class to body element to show sidebar submenu on mouse hover -->
<!-- LAYOUT: Apply "fixed-sidebar" class to body to have fixed sidebar -->
<!-- LAYOUT: Apply "fixed-topbar" class to body to have fixed topbar -->
<!-- LAYOUT: Apply "rtl" class to body to put the sidebar on the right side -->
<!-- LAYOUT: Apply "boxed" class to body to have your page with 1200px max width -->
<!-- THEME STYLE: Apply "theme-sdtl" for Sidebar Dark / Topbar Light -->
<!-- THEME STYLE: Apply  "theme sdtd" for Sidebar Dark / Topbar Dark -->
<!-- THEME STYLE: Apply "theme sltd" for Sidebar Light / Topbar Dark -->
<!-- THEME STYLE: Apply "theme sltl" for Sidebar Light / Topbar Light -->
<!-- THEME COLOR: Apply "color-default" for dark color: #2B2E33 -->
<!-- THEME COLOR: Apply "color-primary" for primary color: #319DB5 -->
<!-- THEME COLOR: Apply "color-red" for red color: #C9625F -->
<!-- THEME COLOR: Apply "color-green" for green color: #18A689 -->
<!-- THEME COLOR: Apply "color-orange" for orange color: #B66D39 -->
<!-- THEME COLOR: Apply "color-purple" for purple color: #6E62B5 -->
<!-- THEME COLOR: Apply "color-blue" for blue color: #4A89DC -->
<!-- BEGIN BODY -->

<body class="fixed-topbar fixed-sidebar theme-sdtl color-default">
    <!--[if lt IE 7]>
<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
    <section>
        <!-- BEGIN SIDEBAR -->
        <div class="sidebar">
            <div class="logopanel">
                <h1><a href="<?php echo site_url() ?>"></a></h1>
            </div>
            <div class="sidebar-inner">
                <ul class="nav nav-sidebar">
                    <?php echo dynamic_menu(); ?>
                </ul>
                <!-- SIDEBAR WIDGET FOLDERS -->
                <div class="sidebar-footer clearfix">
                    &nbsp;
                    <br>&nbsp;
                </div>
            </div>
        </div>
        <!-- END SIDEBAR -->
        <!-- BEGIN MAIN CONTENT -->
        <div class="main-content">
            <!-- BEGIN TOPBAR -->
            <div class="topbar">
                <div class="header-left">
                    <div class="topnav">
                        <a class="menutoggle" href="#" data-toggle="sidebar-collapsed"><span class="menu__handle"><span>Menu</span></span></a>
                    </div>
                </div>
                <div class="header-right">
                    <ul class="header-menu nav navbar-nav">
                        <!-- BEGIN USER DROPDOWN -->
                        <li class="dropdown" id="language-header">
                            <a href="#" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" class="language">
                                <i class="icon-globe"></i>
                                <span><?php echo ($this->session->userdata('language')=='ina'?'EN':'ID'); ?></span>
                            </a>
                        </li>
                        <!-- notification-->
                        <li class="dropdown" id="notifications-header">
                            <a href="#" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" id="aNotif">
                            </a>
                            <ul class="dropdown-menu">
                                <li class="dropdown-header clearfix">
                                    <p class="pull-left" id="headerNotif"></p>
                                </li>
                                <li>
                                    <ul class="dropdown-menu-list withScroll ul_notif" data-height="220"></ul>
                                </li>
                                <span id="moreNotif"></span>
                            </ul>
                        </li>
                        <!-- end notification-->
                        <!-- END USER DROPDOWN -->
                        <!-- BEGIN USERGROUP DROPDOWN -->
                        <li class="dropdown" id="notifications-header">
                            <a href="#" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <i class="icon-settings"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="dropdown-header clearfix">
                                    <p class="pull-left">
                                        <?php echo getLang('usergroup'); ?>
                                    </p>
                                </li>
                                <?php echo usergroup(); ?>
                            </ul>
                        </li>
                        <!-- END USERGROUP DROPDOWN -->
                        <!-- BEGIN USER DROPDOWN -->
                        <li class="dropdown" id="user-header">
                            <a href="#" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <img src="tools/assets/global/images/avatars/avatar11.png" alt="user image">
                                <span class="username"><?php echo getLang('hi') ?>, <?php echo $this->session->userdata('username')?></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#"><i class="icon-user"></i><span><?php echo getLang('my_profile') ?></span></a>
                                </li>
                                <li>
                                    <a href="backend/login/logout"><i class="icon-logout"></i><span><?php echo getLang('logout') ?></span></a>
                                </li>
                            </ul>
                        </li>
                        <!-- END USER DROPDOWN -->
                    </ul>
                </div>
                <!-- header-right -->
            </div>
            <!-- END TOPBAR -->
            <!-- BEGIN PAGE CONTENT -->
            <div class="page-content">
				<!--
                <div class="header">
                    <h2><strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong></h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <?php echo (!empty(getParentMenu())?'<li class="parentmenu">'.getParentMenu().'</li>':'')?>
                            <li class="active">
                                <?php echo getCurrentMenuName() ?>
                            </li>
                        </ol>
                    </div>
                </div>
				-->
                <div class="row">
                    <?php $this->load->view($menu); ?>
