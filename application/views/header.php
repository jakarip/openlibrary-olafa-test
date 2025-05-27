
<!DOCTYPE html>
<html lang="en"> 
<head>
	<base href="<?php echo base_url()?>">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"> 
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>OLAFA</title>

    <!-- Bootstrap core CSS -->

    <link href="tools/css/bootstrap.min.css" rel="stylesheet">

    <link href="tools/fonts/css/font-awesome.min.css" rel="stylesheet">
    <link href="tools/css/animate.min.css" rel="stylesheet">

    <!-- Custom styling plus plugins -->
    <link href="tools/css/custom.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="tools/css/maps/jquery-jvectormap-2.0.1.css" />
    <link href="tools/css/icheck/flat/green.css" rel="stylesheet" />
    <link href="tools/css/floatexamples.css" rel="stylesheet" type="text/css" />
	<link href="tools/css/datatables/tools/css/dataTables.tableTools.css" rel="stylesheet">
	
	<link rel="stylesheet" type="text/css" href="tools/js/taginput/css/textext.core.css" />
	<link rel="stylesheet" type="text/css" href="tools/js/taginput/css/textext.plugin.arrow.css" />
	<link rel="stylesheet" type="text/css" href="tools/js/taginput/css/textext.plugin.autocomplete.css" />
	<link rel="stylesheet" type="text/css" href="tools/js/taginput/css/textext.plugin.clear.css" />
	<link rel="stylesheet" type="text/css" href="tools/js/taginput/css/textext.plugin.focus.css" />
	<link rel="stylesheet" type="text/css" href="tools/js/taginput/css/textext.plugin.prompt.css" />
	<link rel="stylesheet" type="text/css" href="tools/js/taginput/css/textext.plugin.tags.css" />

    <script src="tools/js/jquery.min.js"></script> 
    <!--[if lt IE 9]>
        <script src="../assets/js/ie8-responsive-file-warning.js"></script>
        <![endif]-->

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

</head>


<body class="nav-md">

    <div class="container body">


        <div class="main_container">

            <div class="col-md-3 left_col">
                <div class="left_col scroll-view">

                    <div class="navbar nav_title" style="border: 0;">
                        <a href="" class="site_title"><img src="tools/images/logo.png" width="45px"> <span>OLAFA</span></a>
                    </div>
                    <div class="clearfix"></div> 
			
                    <br />

                    <!-- sidebar menu -->
                    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">

                        <div class="menu_section">
                            <ul class="nav side-menu">
                                <li><a href=""><i class="fa fa-home"></i>Home</a></li>
                                <li><a href="index.php/bahanpustaka"><i class="fa fa-book"></i> Bahan Pustaka</a></li> 
								<li><a href="index.php/karyailmiah"><i class="fa fa-mortar-board"></i>Karya Ilmiah</a></li>
								<li><a><i class="fa fa-file-text"></i> Jurnal <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu" style="display: none">
                                        <li><a href="index.php/dikti">Jurnal Terakreditasi Dikti</a>
                                        </li>
                                        <li><a href="index.php/internationalfisik">Jurnal International Fisik</a>
                                        </li>
                                        <li><a href="index.php/internationalonline">Jurnal International Online</a>
                                        </li>
                                    </ul>
                                </li>
                                <li><a href="index.php/prosidingseminar"><i class="fa fa-file-o"></i>Prosiding Seminar</a></li> 
                                <li><a href="index.php/sumberpustaka"><i class="fa fa-globe"></i>Sumber Pustaka</a></li>
								<li><a><i class="fa fa-line-chart"></i>E-Proceeding <span class="fa fa-chevron-down"></span></a>
									<ul class="nav child_menu" style="display: none">
											
									<li><a href="index.php/monitoringeproceeding">Monitoring E-Proceeding</a>
									</li>
									<li><a href="index.php/monitoringeproceeding/generate">Generate E-Proceeding</a>
									</li>
								</ul>
								</li>
								<li><a><i class="fa fa-line-chart"></i> Katalog <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu" style="display: none">
                                        <li><a href="index.php/katalog"></i>Data Statistik</a></li>
                                        <li><a href="index.php/katalog/ecatalog"></i>Generate E-Catalog</a></li>
                                        <li><a href="index.php/katalog/pengolahan"></i>Pengolahan</a></li>
                                        <li><a href="index.php/katalog/lists">List Katalog</a>
                                        </li>
                                    </ul>
                                </li> 
								<li><a><i class="fa fa-line-chart"></i> Anggota <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu" style="display: none">
                                        <li><a href="index.php/anggota/pengunjung"></i>Pengunjung</a></li>
                                        <li><a href="index.php/anggota/sirkulasi"></i>Sirkulasi Per Anggota</a></li> 
                                    </ul>
                                </li>  
								<?php if ($this->session->userdata("login")){ ?>
									<li><a><i class="fa fa-line-chart"></i> Monitoring Pengadaan <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu" style="display: none">
											
											<li><a href="index.php/pengadaan/pengajuan">Pengajuan Dosen</a>
											</li>
											<li><a href="index.php/pengadaan/nodin">Nota Dinas ke Logistik</a>
											</li>
											<li><a href="index.php/pengadaan/bast">BAST dari Logistik</a>
											</li>
											<li><a href="index.php/pengadaan/lists">List Pengadaan</a>
											</li>
										</ul>
									</li>
								<?php } else { ?>
								<li><a href="index.php/pengadaan/lists"><i class="fa fa-line-chart"></i>Monitoring Pengadaan</a></li>
                               <?php } ?>
							   <!--
							   <?php if ($this->session->userdata("login")){ ?>
									<li><a><i class="fa fa-line-chart"></i> SMS Blast <span class="fa fa-chevron-down"></span></a>
										<ul class="nav child_menu" style="display: none"> 
											<li><a href="index.php/sms/grup">Setting Grup Sms</a>
											</li>
											<li><a href="index.php/sms/send">Send Sms</a>
											</li>
											<li><a href="index.php/sms/report">Report Sms</a>
											</li>
										</ul>
									</li>
								<?php } ?>-->
                            </ul>
                        </div> 

                    </div>
                    <!-- /sidebar menu -->

                </div>
            </div>

            <!-- top navigation -->
            <div class="top_nav">

                <div class="nav_menu" id="nav_menus">
                    <nav class="" role="navigation">
                        <div class="nav toggle">
                            <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                        </div>

						
						 <ul class="nav navbar-nav navbar-right menus">
							<?php if ($this->session->userdata("login")){ ?>
							 <li class="login">
                                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <img src="images/img.jpg" alt=""><?php echo $this->session->userdata("username")?>
                                    <span class=" fa fa-angle-down"></span>
                                </a>
                                <ul class="dropdown-menu dropdown-usermenu animated fadeInDown pull-right"> 
                                    <li><a href="index.php/home/logout""><i class="fa fa-sign-out pull-right"></i> Log Out</a>
                                    </li>
                                </ul>
                            </li>
							<?php } else { ?>
							<li class="login">
                                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false" >
                                    Login
                                    <span class=" fa fa-angle-down"></span>
                                </a>
                                <ul id="submenu" class=" dropdown-menu dropdown-usermenu animated fadeInDown pull-right">
									
									<form method="post" id="formlogin" style="padding:20px;">
									<li id="pesan" style='color:#C22E32 !important;display:none'>
                                         Username atau Password yang anda masukkan salah
                                    </li>
								   <li>Username
                                    </li>
                                    <li>
                                           <input type="text" class="form-control" name="username" id="username">
                                    </li>
									<li>
                                           &nbsp;
                                    </li>
                                   <li>Password
								   </li>
                                    <li>
                                           <input type="password" class="form-control" name="password" id="password">
                                    </li>
									<li>
                                           &nbsp;
                                    </li>
                                    <li>
										<button type="submit" value="submit" id="submitlogin" name="submit" class="btn btn-success">Login</button>
                                    </li>
									</form>
                                </ul>
                            </li>
							<?php } ?>							
                        </ul>
						
						
						
                    </nav>
                </div>

            </div>
            <!-- /top navigation -->
			
			
            <!-- page content -->
            <div class="right_col" role="main">
			
			<div class="page-title"> 
                            <div class="col-md-8">
                                    <h3><?php echo ucwords($site) ?><small></small></h3>
                                </div> 
 
                             <div class="col-md-4" style="float:right;">
                                    <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                                        <span><?php echo date('l jS \of F Y'); ?></span> 
                                    </div>
                             </div> 
							 <div class="clearfix"></div>
			</div>
			 
<script type="text/javascript">
$(document).ready(function(){ 
	
	$('body').click(function(evt){    
	   if(evt.target.id == "submenu")
		  return;
	   //For descendants of menu_content being clicked, remove this check if you do not want to put constraint on descendants.
	   if($(evt.target).closest('#submenu').length)
		  return;             

		if ($('.login').hasClass('open')) {
			$('.login').removeClass('open');
		}
	});
	
	$('#submitlogin').click(function(evt){
		evt.preventDefault();
		$.ajax({
			url: "index.php/home/ceklogin",
			type: "post",
			data:  $('#formlogin').serialize(), 
			success: function(response) {
				if(response=='failed'){
					$('#pesan').fadeIn();
					 $("#pesan").fadeOut(2000);
				}
				else if (response=='success'){
					window.location='index.php/home';
					//HTMLFormElement.prototype.submit.call($('#formlogin')[0]);
				}
			}
		});
	});
	
	
	
});
</script> 