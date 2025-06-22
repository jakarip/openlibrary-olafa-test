<?php
$edition = explode(" ",$eproceeding['edition']);
$month 	 = getLang(strtolower($edition[0]));
//echo $this->session->userdata('usergroup');

?>

<div class="col-md-12 portlets"> 
	<div class="panel">
		<div class="panel-header bg-red">
			<div class="row">
				<div class="col-lg-8" > 
					<h3> <strong></strong> </h3>
				</div> 
				<div class="text-right col-lg-2" style="margin-top:1px;text-align:right;padding-top:7px;">
					<?php echo getLang('Pilih Tahun') ?> </div>
				<div class="text-right col-lg-2" style="margin-top:1px;">
					<select name="grow_year" id="grow_year" class="form-control">
							<?php
                            
								$last = date('Y')-10;
								$now = date('Y');
								for($i=$now;$i>=$last;$i--){
									echo '<option value="'.$i.'" '.($i==$year?'selected':'').'>'.$i.'</option>';
								}
							?>
						</select>  
				</div> 
			</div>  
		</div> 
	</div>
</div> 

<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3><i class="fa fa-book"></i><strong><?php echo getLang("collection"); ?> <?php echo $year ?></strong></h3> 
		</div>
		<div class="panel-content">
			<div class="row">
				<div class="col-md-4 col-sm-12">
					<table class="table info_dashboard">
						<thead>
							<tr>
								<td width="50%" align="right"><?php echo number_format($koleksi['judul']->total,0,'','.'); ?> / <?php echo number_format($koleksi['judul_all']->total,0,'','.'); ?></td>
								<td width="50%" class="desc_dashboard"><?php echo getLang("title") ?> Fisik</td>
							</tr>
							<tr>
								<td  align="right"><?php echo number_format($koleksi['koleksi']->total,0,'','.'); ?> / <?php echo number_format($koleksi['koleksi_all']->total,0,'','.'); ?></td>
								<td class="desc_dashboard"><?php echo getLang("collection") ?> Fisik</td>
							</tr> 
							<tr>
								<td width="50%" align="right"><?php echo number_format($koleksi['judul_digital']->total,0,'','.'); ?> / <?php echo number_format($koleksi['judul_digital_all']->total,0,'','.'); ?></td>
								<td width="50%" class="desc_dashboard"><?php echo getLang("title") ?> Digital</td>
							</tr>
							<tr>
								<td  align="right"><?php echo number_format($koleksi['judul_digital']->total,0,'','.'); ?> / <?php echo number_format($koleksi['judul_digital_all']->total,0,'','.'); ?></td>
								<td class="desc_dashboard"><?php echo getLang("collection") ?> Digital</td>
							</tr>   
						</thead>
					</table>
				</div>
				<div class="col-md-8 col-sm-12"><div id="judul" style="height: 350px; "></div></div>
			</div>
		</div>
	</div>
</div> 


<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3><i class="fa fa-book"></i><strong><?php echo getLang("anggota"); ?> <?php echo $year ?></strong></h3> 
		</div>
		<div class="panel-content">
			<div class="row">
				<div class="col-md-4 col-sm-12">
					<table class="table info_dashboard">
						<thead>
							<tr>
								<td width="50%" align="right"><?php echo number_format($anggota['civitas_webmobile']->total,0,'','.'); ?> / <?php echo number_format($anggota['civitas_all_webmobile']->total,0,'','.'); ?></td>
								<td width="50%" class="desc_dashboard">Civitas (Web & Mobile)</td>
							</tr>
							<tr>
								<td width="50%" align="right"><?php echo number_format($anggota['civitas_web']->total,0,'','.'); ?> / <?php echo number_format($anggota['civitas_all_web']->total,0,'','.'); ?></td>
								<td width="50%" class="desc_dashboard">Civitas (Web)</td>
							</tr>
							<tr>
								<td  align="right"><?php echo number_format($anggota['umum_webmobile']->total,0,'','.'); ?> / <?php echo number_format($anggota['umum_all_webmobile']->total,0,'','.'); ?></td>
								<td class="desc_dashboard"><?php echo getLang("Umum") ?> (Web & Mobile)</td>
							</tr> 
							<tr>
								<td  align="right"><?php echo number_format($anggota['umum_web']->total,0,'','.'); ?> / <?php echo number_format($anggota['umum_all_web']->total,0,'','.'); ?></td>
								<td class="desc_dashboard"><?php echo getLang("Umum") ?> (Web)</td>
							</tr> 
						</thead>
					</table>
				</div>
				<div class="col-md-8 col-sm-12"><div id="anggota" style="height: 350px; "></div></div>
			</div>
		</div>
	</div>
</div> 

<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3><i class="fa fa-user"></i><strong><?php echo getLang("visitor"); ?> / Akses <?php echo $year ?></strong></h3> 
		</div>
		<div class="panel-content">
			<div class="row"> 
				<div class="col-md-4 col-sm-12">
					<table class="table info_dashboard" cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<td width="50%" align="right"><?php echo number_format($pengunjung['fisik']->total,0,'','.'); ?></td>
								<td width="50%" class="desc_dashboard"><?php echo getLang("physic_visitor") ?></td>
							</tr>
							<!--<tr>
								<td width="50%" align="right"><?php echo number_format($pengunjung['journal']->total,0,'','.'); ?></td>
								<td width="50%" class="desc_dashboard"><?php echo getLang("journal_visitor") ?></td>
							</tr>-->
							<tr>
								<td  align="right"><?php echo number_format($pengunjung['online_user']->total,0,'','.'); ?></td>
								<td class="desc_dashboard"><?php echo getLang("online_visitor") ?> Openlibrary</td>
							</tr> 
							<tr>
								<td  align="right"><?php echo number_format($pengunjung['online_pageview']->total,0,'','.'); ?></td>
								<td class="desc_dashboard"><?php echo getLang("page_views") ?> Openlibrary</td>
							</tr>  
							<tr>
								<td  align="right"><?php echo number_format($pengunjung['online_user_eproc']->total,0,'','.'); ?></td>
								<td class="desc_dashboard"><?php echo getLang("online_visitor") ?> E-Proceeding</td>
							</tr> 
							<tr>
								<td  align="right"><?php echo number_format($pengunjung['online_pageview_eproc']->total,0,'','.'); ?></td>
								<td class="desc_dashboard"><?php echo getLang("page_views") ?> E-Proceeding</td>
							</tr>  
							<tr>
								<td width="50%" align="right"><?php echo number_format($sirkulasi['ebook']->total,0,'','.'); ?></td>
								<td width="50%" class="desc_dashboard">Akses Ebook</td>
							</tr>
							<tr>
								<td width="50%" align="right"><?php echo number_format($sirkulasi['karyailmiah']->total,0,'','.'); ?></td>
								<td width="50%" class="desc_dashboard">Akses Karya Ilmiah</td>
							</tr>
						</thead>
					</table>
				</div>
				<div class="col-md-8 col-sm-12"><div id="pengunjung" style="height: 400px; "></div></div>
			</div>
		</div>
	</div>
</div> 
 
<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3><i class="fa fa-retweet"></i><strong><?php echo getLang("Layanan"); ?> <?php echo $year ?></strong></h3> 
		</div>
		<div class="panel-content">
			<div class="row"> 
				<div class="col-md-4 col-sm-12">
					<table class="table info_dashboard">
						<thead>
							<tr>
								<td width="50%" align="right"><?php echo number_format($sirkulasi['peminjaman']->total,0,'','.'); ?></td>
								<td width="50%" class="desc_dashboard"><?php echo getLang("borrowing") ?> Buku</td>
							</tr>
							<tr>
								<td  align="right"><?php echo number_format($sirkulasi['pengembalian']->total,0,'','.'); ?></td>
								<td class="desc_dashboard"><?php echo getLang("returning") ?> Buku</td>
							</tr> 
							<tr>
								<td width="50%" align="right"><?php echo number_format($sirkulasi['ruangan']->total,0,'','.'); ?></td>
								<td width="50%" class="desc_dashboard"><?php echo getLang("borrowing") ?> Ruangan</td>
							</tr>
							<tr>
								<td width="50%" align="right"><?php echo number_format($sirkulasi['bds']->total,0,'','.'); ?></td>
								<td width="50%" class="desc_dashboard">Book Delivery</td>
							</tr>
							<tr>
								<td width="50%" align="right"><?php echo number_format($sirkulasi['usulan']->total,0,'','.'); ?></td>
								<td width="50%" class="desc_dashboard">Usulan Bahan Pustaka</td>
							</tr>
							<tr>
								<td width="50%" align="right"><?php echo number_format($sirkulasi['sbkp']->total,0,'','.'); ?></td>
								<td width="50%" class="desc_dashboard">SBKP</td>
							</tr>
						</thead>
					</table>
				</div>
				<div class="col-md-8 col-sm-12"><div id="sirkulasi" style="height: 400px; "></div></div>
			</div>
		</div>
	</div>
</div> 

<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3><i class="fa fa-bullhorn"></i><strong><?php echo getLang("curriculum_support"); ?></strong></h3> 
		</div>
		<div class="panel-content">
			<div class="row"> 
				<div class="col-md-4 col-sm-12">
					<table class="table info_dashboard">
						<thead>
							<tr>
								<td width="50%" align="right"><?php echo $rasio['totals'] ?>%</td>
								<td width="50%" class="desc_dashboard"><?php echo getLang("ratio") ?></td>
							</tr> 
							<tr>
								<td width="50%" align="right"><?php echo number_format(array_sum($rasio['total']),0,'','.')  ?></td>
								<td width="50%" class="desc_dashboard">Judul Koleksi Inti</td>
							</tr> 
						</thead>
					</table>
				</div>
				<div class="col-md-8 col-sm-12"><div id="rasio" style="height: 350px; "></div></div>
			</div>
		</div>
	</div>
</div> 

<!-- <div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3><i class="fa fa-file-pdf-o"></i><strong><?php echo getLang("digital_collection"); ?></strong></h3> 
		</div>
		<div class="panel-content">
			<div class="row"> 
				<div class="col-md-4 col-sm-12">
					<table class="table info_dashboard">
						<thead>
							<tr>
								<td width="50%" align="right"><?php echo number_format(array_sum($file['total']),0,'','.'); ?></td>
								<td width="50%" class="desc_dashboard"><?php echo getLang("file") ?></td>
							</tr> 
						</thead>
					</table>
				</div>
				<div class="col-md-8 col-sm-12"><div id="file" style="height: 350px; "></div></div>
			</div>
		</div>
	</div>
</div>   -->

<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3><i class="fa fa-file-text-o"></i><strong><?php echo getLang("E-Proceeding"); ?> <?php echo $eproceeding['edition'] ?></strong></h3> 
		</div>
		<div class="panel-content">
			<div class="row"> 
				<div class="col-md-12 col-sm-12"><div id="eproceeding" style="height: 350px; "></div></div>
			</div>
		</div>
	</div>
</div> 

<!-- <div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3><i class="fa fa-file"></i><strong><?php echo getLang("Ithencticate").' '.$year; ?></strong></h3> 
		</div>
		<div class="panel-content">
			<div class="row"> 
				<div class="col-md-12 col-sm-12">
					<table class="table info_dashboard">
						<thead>
							<tr>
								<td width="50%" align="right"><?php echo number_format(421,0,'','.'); ?> / 500</td>
								<td width="50%" class="desc_dashboard"><?php echo getLang("user") ?></td>
							</tr> 
							<tr>
								<td align="right"><?php echo number_format(12000-810,0,'','.'); ?> / 12.000</td>
								<td class="desc_dashboard"><?php echo getLang("document") ?></td>
							</tr> 
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>    -->



<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3><i class="fa fa-retweet"></i><strong><?php echo getLang("Scholar Report"); ?></strong></h3> 
		</div>
		<div class="panel-content">
			<div class="row"> 
				<div class="col-md-4 col-sm-12">
					<table class="table info_dashboard">
						<thead>
							<tr>
								<td width="50%" align="right"><?php echo number_format(15721,0,'','.'); ?></td>
								<td width="50%" class="desc_dashboard">Total E-Proceeding</td>
							</tr>
							<tr>
								<td  align="right"><?php echo number_format(14155,0,'','.'); ?></td>
								<td class="desc_dashboard">Scholar</td>
							</tr> 
						</thead>
					</table>
				</div>
				<div class="col-md-8 col-sm-12"><div id="scholar" style="height: 350px; "></div></div>
			</div>
		</div>
	</div>
</div> 



<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3><i class="fa fa-retweet"></i><strong><?php echo getLang("E-Journal Usages Report"); ?></strong></h3> 
		</div>
		<div class="panel-content">
			<div class="row"> 
				<div class="col-md-4 col-sm-12">
					<table class="table info_dashboard">
						<thead>
							<tr>
								<td width="50%" align="right"><?php echo number_format(91380,0,'','.'); ?></td>
								<td width="50%" class="desc_dashboard">2017</td>
							</tr> 
							<tr>
								<td width="50%" align="right"><?php echo number_format(94466,0,'','.'); ?></td>
								<td width="50%" class="desc_dashboard">2018</td>
							</tr> 
							<tr>
								<td width="50%" align="right"><?php echo number_format(86675,0,'','.'); ?></td>
								<td width="50%" class="desc_dashboard">2019</td>
							</tr> 
							<tr>
								<td width="50%" align="right"><?php echo number_format(73449,0,'','.'); ?></td>
								<td width="50%" class="desc_dashboard">2020</td>
							</tr> 
							<tr>
								<td width="50%" align="right"><?php echo number_format(85275,0,'','.'); ?></td>
								<td width="50%" class="desc_dashboard">2021</td>
							</tr> 
							<tr>
								<td width="50%" align="right"><?php echo number_format(422801,0,'','.'); ?></td>
								<td width="50%" class="desc_dashboard">2022</td>
							</tr> 
							<tr>
								<td width="50%" align="right"><?php echo number_format(591104,0,'','.'); ?></td>
								<td width="50%" class="desc_dashboard">2023</td>
							</tr> 
						</thead>
					</table>
				</div>
				<div class="col-md-8 col-sm-12"><div id="ejournals" style="height: 250px; "></div></div>
			</div>
		</div>
	</div>
</div> 
 
<?php 
 
$this->load->view('theme_footer'); ?>

<script type="text/javascript">
	$(document).ready(function(){ 
	$('#judul').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: '<?php echo getLang("collection_total_per_month") ?>'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories: [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec'
            ],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: ' <?php echo getLang("title") ?>'
            }
        }, 
		plotOptions: {
            series: {
                colorByPoint: true
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:14px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.f} </b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0 
            }
        },
        series: [{
            name: '<?php echo getLang("title") ?> Fisik <?php echo $year-1; ?>',
            data: [<?php echo implode(', ',$grafik['judul']['lastyear']) ?>]

        },{
            name: '<?php echo getLang("collection") ?> Fisik <?php echo $year-1; ?>',
            data: [<?php echo implode(', ',$grafik['koleksi']['lastyear']) ?>]

        },{
            name: '<?php echo getLang("title") ?> Digital <?php echo $year-1; ?>',
            data: [<?php echo implode(', ',$grafik['judul_digital']['lastyear']) ?>]

        },{ 
            name: '<?php echo getLang("collection") ?> Digital <?php echo $year-1; ?>',
            data: [<?php echo implode(', ',$grafik['judul_digital']['lastyear']) ?>]

        }, {
            name: '<?php echo getLang("title") ?> Fisik <?php echo $year ?>',
            data: [<?php echo implode(', ',$grafik['judul']['year']) ?>]
 
        }, {
            name: '<?php echo getLang("collection") ?> Fisik <?php echo $year ?>',
            data: [<?php echo implode(', ',$grafik['koleksi']['year']) ?>]

        }, {
            name: '<?php echo getLang("title") ?> Digital <?php echo $year ?>',
            data: [<?php echo implode(', ',$grafik['judul_digital']['year']) ?>]

        }, {
            name: '<?php echo getLang("collection") ?> Digital <?php echo $year ?>',
            data: [<?php echo implode(', ',$grafik['judul_digital']['year']) ?>]

        }]
    });
	
    $('#anggota').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: ' Total Anggota per Bulan'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories: [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec'
            ],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: ' <?php echo getLang('Anggota') ?>'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:14px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.f} </b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: 'Civitas (Web & Mobile)<?php echo $year-1; ?>',
            data: [<?php echo implode(', ',$grafik['civitas_webmobile']['lastyear']) ?>]

        },{
            name: 'Civitas (Web)<?php echo $year-1; ?>',
            data: [<?php echo implode(', ',$grafik['civitas_web']['lastyear']) ?>]

        },{
            name: 'Umum (Web & Mobile)<?php echo $year-1; ?>',
            data: [<?php echo implode(', ',$grafik['umum_webmobile']['lastyear']) ?>]

        },{
            name: 'Umum (Web)<?php echo $year-1; ?>',
            data: [<?php echo implode(', ',$grafik['umum_web']['lastyear']) ?>]

        },{
            name: 'Civitas (Web & Mobile) <?php echo $year ?>',
            data: [<?php echo implode(', ',$grafik['civitas_webmobile']['year']) ?>]

        },{
            name: 'Civitas (Web) <?php echo $year ?>',
            data: [<?php echo implode(', ',$grafik['civitas_web']['year']) ?>]

        },{
            name: 'Umum (Web & Mobile)<?php echo $year ?>',
            data: [<?php echo implode(', ',$grafik['umum_webmobile']['year']) ?>]

        },{
            name: 'Umum (Web)<?php echo $year ?>',
            data: [<?php echo implode(', ',$grafik['umum_web']['year']) ?>]

        }]
    });
	
	$('#pengunjung').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: ' Total Pengunjung / Akses Per Bulan'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories: [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec'
            ],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: '<?php echo getLang("visitor") ?> / Akses'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:14px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.f} </b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        }, series: [{
            name: '<?php echo getLang('physic') ?> <?php echo $year-1; ?>',
            data: [<?php echo implode(', ',$grafik['pengunjung']['lastyear']) ?>]

        },{
            name: 'Online Openlibrary <?php echo $year-1; ?>',
            data: [<?php echo implode(',',$online['lastyear']) ?>]

        },{
            name: 'Page Views Openlibrary <?php echo $year-1; ?>',
            data: [<?php echo implode(',',$pageviews['lastyear']) ?>]

        },{
            name: 'Online E-Proceeding <?php echo $year-1; ?>',
            data: [<?php echo implode(',',$online['lastyear']) ?>]

        },{
            name: 'Page Views E-Proceeding <?php echo $year-1; ?>',
            data: [<?php echo implode(',',$pageviews['lastyear']) ?>]

        },{
            name: 'Akses Ebook <?php echo $year-1; ?>',
            data: [<?php echo implode(', ',$ebook['lastyear']) ?>]

        },{
            name: 'Akses Karya Ilmiah <?php echo $year-1; ?>',
            data: [<?php echo implode(', ',$karyailmiah['lastyear']) ?>]

        },{
            name: '<?php echo getLang('physic') ?> <?php echo $year ?>',
            data: [<?php echo implode(', ',$grafik['pengunjung']['year']) ?>]

        },{
            name: 'Online Openlibrary <?php echo $year; ?>',
            data: [<?php echo implode(',',$online['year']) ?>]

        },{
            name: 'Page Views Openlibrary <?php echo $year; ?>',
            data: [<?php echo implode(',',$pageviews['year']) ?>]

        },{
            name: 'Online E-Proceeding <?php echo $year; ?>',
            data: [<?php echo implode(',',$onlineeproc['year']) ?>]

        },{
            name: 'Page Views E-Proceeding <?php echo $year; ?>',
            data: [<?php echo implode(',',$pageviewseproc['year']) ?>]

        },{
            name: 'Akses Ebook <?php echo $year; ?>',
            data: [<?php echo implode(', ',$ebook['year']) ?>]

        },{
            name: 'Akses Karya Ilmiah <?php echo $year; ?>',
            data: [<?php echo implode(', ',$karyailmiah['year']) ?>]

        }]
    });
	
	
    $('#sirkulasi').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: ' Total Layanan per Bulan'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories: [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec'
            ],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: ' <?php echo getLang('circulation') ?>'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:14px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.f} </b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: '<?php echo getLang('borrowing') ?> buku <?php echo $year-1; ?>',
            data: [<?php echo implode(', ',$grafik['peminjaman']['lastyear']) ?>]

        },{
            name: '<?php echo getLang('returning') ?> buku <?php echo $year-1; ?>',
            data: [<?php echo implode(', ',$grafik['pengembalian']['lastyear']) ?>]

        },{ 
            name: '<?php echo getLang('borrowing') ?> ruangan <?php echo $year-1; ?>',
            data: [<?php echo implode(', ',$grafik['ruangan']['lastyear']) ?>]

        },{
            name: 'Book Delivery <?php echo $year-1; ?>',
            data: [<?php echo implode(', ',$grafik['bds']['lastyear']) ?>]

        },{
            name: 'Usulan Bahan Pustaka <?php echo $year-1; ?>',
            data: [<?php echo implode(', ',$grafik['usulan']['lastyear']) ?>]

        },{
            name: 'SBKP <?php echo $year-1; ?>',
            data: [<?php echo implode(', ',$grafik['sbkp']['lastyear']) ?>]

        },{
            name: '<?php echo getLang('borrowing') ?> buku <?php echo $year ?>',
            data: [<?php echo implode(', ',$grafik['peminjaman']['year']) ?>]

        },{
            name: '<?php echo getLang('returning') ?> buku <?php echo $year ?>',
            data: [<?php echo implode(', ',$grafik['pengembalian']['year']) ?>]

        },{
            name: '<?php echo getLang('borrowing') ?> ruangan <?php echo $year; ?>',
            data: [<?php echo implode(', ',$grafik['ruangan']['year']) ?>]

        },{
            name: 'Book Delivery <?php echo $year; ?>',
            data: [<?php echo implode(', ',$grafik['bds']['year']) ?>]

        },{
            name: 'Usulan Bahan Pustaka <?php echo $year; ?>',
            data: [<?php echo implode(', ',$grafik['usulan']['year']) ?>]

        },{
            name: 'SBKP <?php echo $year; ?>',
            data: [<?php echo implode(', ',$grafik['sbkp']['year']) ?>]

        }]
    });
	
	$('#rasio').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Persentasi Koleksi yang Menunjang Kurikulum per Program Studi'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories: [
               <?php echo implode(', ',$rasio['prodi']) ?>
            ],
            crosshair: true,
			labels: {
                style: {
                    fontSize:'14px'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: ' %'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:14px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.f} %</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }, 
			series: {
				dataLabels: {
					enabled: true
				}
			}
        },
        series: [{
            name: '<?php echo getLang('ratio') ?>',
            data: [<?php echo implode(', ',$rasio['rasio']) ?>]

        }]
    });
	
	
	// $('#file').highcharts({
    //     chart: {
    //         type: 'column'
    //     },
    //     title: {
    //         text: '<?php echo getLang('digital_collection') ?>'
    //     },
    //     subtitle: {
    //         text: ''
    //     },
    //     xAxis: {
    //         categories: [
    //            <?php echo implode(', ',$file['name']) ?>
    //         ],
    //         crosshair: true,
	// 		labels: {
    //             style: {
    //                 fontSize:'14px'
    //             }
    //         }
    //     },
    //     yAxis: {
    //         min: 0,
    //         title: {
    //             text: ' <?php echo getLang('file') ?>'
    //         }
    //     },
    //     tooltip: {
    //         headerFormat: '<span style="font-size:14px">{point.key}</span><table>',
    //         pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
    //             '<td style="padding:0"><b>{point.y:.f}</b></td></tr>',
    //         footerFormat: '</table>',
    //         shared: true,
    //         useHTML: true
    //     },
    //     plotOptions: {
    //         column: {
    //             pointPadding: 0.2,
    //             borderWidth: 0
    //         }
    //     },
    //     series: [{
    //         name: 'Total',
    //         data: [<?php echo implode(', ',$file['total']) ?>]

    //     }]
    // });
	
	$('#eproceeding').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'E-Proceeding <?php echo $month." ".$edition[1] ?>'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories: [
               <?php echo implode(', ',$eproceeding['name']) ?>
            ],
            crosshair: true,
			labels: {
                style: {
                    fontSize:'14px'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: ' <?php echo getLang('file') ?>'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:14px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: 'Total',
            data: [<?php echo implode(', ',$eproceeding['total']) ?>]

        }]
    });
	
	$('#scholar').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: ' <?php echo getLang('Report Scholar') ?>'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories: [
                '2014',
                '2015',
                '2016',
                '2017',
                '2018',
                '2019',
                '2020',
                '2021',
                '2022',
                '2023',
                '2024'
            ],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: ' <?php echo getLang('Scholar Report') ?>'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:14px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.f} </b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: 'E-Proceeding Library',
            data: [280,2129,1506,1534,2173,2667,2586,2770,1342,2361,1369]

        },{
            name: 'Google Scholar',
            data: [276,2048,1454,1512,2103,2612,2519,2557,1020,2462,751]

        }]
    });
	
	
	
	$('#ejournals').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: ' <?php echo getLang('E-Journals Usages Report') ?>'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories: [ 
                '2017',
                '2018',
                '2019',
                '2020',
                '2021',
                '2022',
                '2023'
            ],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: ' <?php echo getLang('E-Journals Usages Report') ?>'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:14px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.f} </b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: 'ACM',
            data: [7757,9418,9296,9283,3099,17778,16535]

        },{
            name: 'Science Direct',
            data: [23143,27732,19588,18131,23822,120479,88576]

        },{
            name: 'Springer',
            data: [3928,5817,6347,6081,6651,68609,56174]

        },{
            name: 'IEEE',
            data: [18317,18676,16060,18580,7848,39572,45575]

        },{
            name: 'Emerald',
            data: [37798,31940,34093,20226,41943,73429,73841]

        },{
            name: 'Taylor & Francis',
            data: [437,883,1291,1148,1533,3848,8891]

        },{
            name: 'SAGE',
            data: [0,700,520,610,805,1687,4126]

        },{
            name: 'Developmental Entrepreneurship',
            data: [0,91,80,50,54,127,546]

        },{
            name: 'Design Research',
            data: [0,40,54,30,90,94,133]

        },{
            name: 'Statista',
            data: [0,0,0,924,8958,13466,23933]

        },{
            name: 'Ithenticate',
            data: [0,11200,11590,13863,14545,14262,33773]

        },{
            name: 'Turnitin',
            data: [0,0,0,0,1714,3030,8626]

        },{
            name: 'Scopus',
            data: [0,0,0,0,0,83712,272774]

        }]
    });
 
    $("#grow_year").on("change", function() {
        // Get the value of the #grow_year element
        var year = $(this).val();
        
        // Set the URL with the value and reload the page
        var newUrl = "<?=base_url()?>index.php/dashboard/index/" + year;
        window.location.href = newUrl;
    });
});
</script>