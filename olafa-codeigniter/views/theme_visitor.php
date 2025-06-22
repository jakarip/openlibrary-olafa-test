<!DOCTYPE html>  
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
	<base href="<?php echo base_url() ?>" />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="admin-themes-lab">
    <meta name="author" content="themes-lab"> 
   <title>OLAFA</title>
    <link href="tools/assets/global/css/style.css" rel="stylesheet">
    <link href="tools/assets/global/css/theme.css" rel="stylesheet">
    <link href="tools/assets/global/css/custom.css" rel="stylesheet">
    <link href="tools/assets/global/css/ui.css" rel="stylesheet">
    <link href="tools/assets/admin/layout1/css/layout.css" rel="stylesheet">
    <script src="tools/assets/global/plugins/modernizr/modernizr-2.6.2-respond-1.1.0.min.js"></script> 
	<link href="tools/assets/global/plugins/tokeninput/styles/token-input.css" rel="stylesheet" type="text/css">
    <link href="tools/assets/global/plugins/tokeninput/styles/token-input-facebook.css" rel="stylesheet" type="text/css" />
    <link href="tools/assets/global/plugins/datatables1/plugins/bootstrap/dataTables.bootstrap.css" rel="stylesheet">
	<link rel="stylesheet" href="tools/assets/global/plugins/jquery-date-range-picker-master/dist/daterangepicker.min.css"> 
	<link rel="stylesheet" href="tools/assets/global/plugins/select2/css/select2.css"> 

  <style>
     
  .main-content {
      background: #F5F5F5;
      margin-left: 0px !important;
      min-height: 750px;
  }
  

  .main-content .page-content {
    background: #F5F5F5;
    margin-top: 0px;
    overflow: hidden;
    padding: 0px 0px 0;
    position: relative;
    height: 100%;
}
  </style>
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
	  <!-- BEGIN MAIN CONTENT -->
      <div class="main-content"> 
		<!-- BEGIN PAGE CONTENT -->
		<div class="page-content"> 
			<div class="breadcrumb-wrapper">
			  <ol class="breadcrumb">
				<?php echo (getParentMenu()?'<li class="parentmenu">'.getParentMenu().'</li>':'')?>
				<li class="active"><?php echo getCurrentMenuName() ?></li>
			  </ol>
			</div> 
		  <div class="row"> 
				<?php $this->load->view($menu); ?>