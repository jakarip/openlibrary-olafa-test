<style>
.ui-datepicker-calendar {
    display: none;
    }
</style>
<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong></h3>
		</div>
		<div class="panel-content pagination2">
			<form id="form" class="form-horizontal" action="" method="post"> 
				<div class="form-group">
					<label class="col-sm-2 control-label"><?php echo getLang('choose_date')?></label>
					<div class="col-sm-3"> 
						 <input type="text" name="reservation" class="form-control" id="reservation" placeholder="<?php echo getLang('choose_date')?>" value="<?php echo (ISSET($reservation)?$reservation:'') ?>"  aria-describedby="inputSuccess2Status" required>
					</div> 
					
				</div>  
				<div class="form-group">
					<label class="col-sm-2 control-label"><?php echo getLang('Fakultas/Prodi')?></label>
					<div class="col-sm-5 "> 
						<select name="prodi" id="prodi" class="form-control">
							<option value="">Semua</option>
							<?php
							$a = "";
							foreach ($prodi as $row) {
							 
								if($a=="" or $a!=$row->nama_fakultas){
									echo "<option value='$row->c_kode_fakultas' ".($row->c_kode_fakultas==$prodi_value?"selected": 
								"").">".$row->nama_fakultas."</option>";
									$a = $row->nama_fakultas;
								}
								echo "<option value='".$row->c_kode_fakultas."-".$row->c_kode_prodi."' ".($row->c_kode_fakultas."-".$row->c_kode_prodi==$prodi_value?"selected": 
								"").">Program Studi ".$row->nama_prodi."</option>";
							} ?>
						</select>
					</div> 
					 
				</div>  
				<div class="form-group">
					<label class="col-sm-2 control-label"></label> 
						<label class="col-sm-7 control-label"><button type="submit" value="submit" id="submitdate" name="report" class="btn btn-success">Report</button> 
				</div> 
			</form> 
			<form action="" method="post">
			<div id="dt_table1">
					<table id="table" class="table table-striped table-bordered table-hover"> 
							<tr> 
								<td class="column-title" width="30%">Pengunjung Onsite</td>  
								<td class="column-title" width="5%">:</td>  
								<td class="column-title" width="65%"><?=$report['pengunjung']?></td>		
							</tr> 
							<tr> 
								<td class="column-title" width="30%">Peminjaman Buku</td>  
								<td class="column-title" width="5%">:</td>  
								<td class="column-title" width="65%"><?=$report['peminjaman']?></td>		
							</tr> 
							<tr> 
								<td class="column-title" width="30%">Pengembalian Buku</td>  
								<td class="column-title" width="5%">:</td>  
								<td class="column-title" width="65%"><?=$report['pengembalian']?></td>		
							</tr> 
							<tr> 
								<td class="column-title" width="30%">Bebas Pustaka</td>  
								<td class="column-title" width="5%">:</td>  
								<td class="column-title" width="65%"><?=$report['bebaspustaka']?></td>		
							</tr> 
							<tr> 
								<td class="column-title" width="30%">- Document TA/Thesis Not Feasible</td>  
								<td class="column-title" width="5%">:</td>  
								<td class="column-title" width="65%"><?=$report['4']?></td>		
							</tr> 
							<tr> 
								<td class="column-title" width="30%">- Document TA/Thesis Approved For Catalog & Journal No Publish Tel-U Proceedings ( Not Feasible )</td>  
								<td class="column-title" width="5%">:</td>  
								<td class="column-title" width="65%"><?=$report['3']?></td>		
							</tr> 
							<tr> 
								<td class="column-title" width="30%">- Document TA/Thesis Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal )</td>  
								<td class="column-title" width="5%">:</td>  
								<td class="column-title" width="65%"><?=$report['52']?></td>		
							</tr> 
							<tr> 
								<td class="column-title" width="30%">- Document TA/Thesis Approved For Catalog & Journal No Publish Tel-U Proceedings ( Publish Eksternal - LoA Pending )</td>  
								<td class="column-title" width="5%">:</td>  
								<td class="column-title" width="65%"><?=$report['64']?></td>		
							</tr> 
							<tr> 
								<td class="column-title" width="30%">- Document TA/Thesis Approved For Catalog & Journal Publish Tel-U Proceedings</td>  
								<td class="column-title" width="5%">:</td>  
								<td class="column-title" width="65%"><?=$report['53']?></td>		
							</tr> 
							<tr> 
								<td class="column-title" width="30%">- Metadata Approve for Catalog & Journal Publish External</td>  
								<td class="column-title" width="5%">:</td>  
								<td class="column-title" width="65%"><?=$report['91']?></td>		
							</tr>
							<tr> 
								<td class="column-title" width="30%">Ruangan</td>  
								<td class="column-title" width="5%">:</td>  
								<td class="column-title" width="65%"><?=$report['ruangan']?></td>		
							</tr>  
							<tr> 
								<td class="column-title" width="30%">Karya Ilmiah (Akses)</td>  
								<td class="column-title" width="5%">:</td>  
								<td class="column-title" width="65%">
									<table class="table table-striped table-bordered table-hover">
										<tr>
										<td colspan="12">Laporan Keseluruhan <?=$report['tapa_readonly']->year ?></td>
										</tr>
										<tr>
											<td>Jan</td>
											<td>Feb</td>
											<td>Mar</td>
											<td>Apr</td>
											<td>Mei</td>
											<td>Jun</td>
											<td>Jul</td>
											<td>Agu</td>
											<td>Sep</td>
											<td>Okt</td>
											<td>Nov</td>
											<td>Des</td>
										</tr>
										<tr>
										<td><?=$report['tapa_readonly']->januari?></td>
										<td><?=$report['tapa_readonly']->februari ?></td>
										<td><?=$report['tapa_readonly']->maret ?></td>
										<td><?=$report['tapa_readonly']->april ?></td>
										<td><?=$report['tapa_readonly']->mei ?></td>
										<td><?=$report['tapa_readonly']->juni ?></td>
										<td><?=$report['tapa_readonly']->juli ?></td>
										<td><?=$report['tapa_readonly']->agustus ?></td>
										<td><?=$report['tapa_readonly']->september ?></td>
										<td><?=$report['tapa_readonly']->oktober ?></td>
										<td><?=$report['tapa_readonly']->november ?></td>
										<td><?=$report['tapa_readonly']->desember ?></td>
										</tr>
									</table>
								</td>		
							</tr> 
							<tr> 
								<td class="column-title" width="30%">Ebook (Akses)</td>  
								<td class="column-title" width="5%">:</td>  
								<td class="column-title" width="65%">
									<table class="table table-striped table-bordered table-hover">
										<tr>
											<td colspan="12">Laporan Keseluruhan <?=$report['ebook_readonly']->year ?></td>
										</tr>
										<tr>
											<td>Jan</td>
											<td>Feb</td>
											<td>Mar</td>
											<td>Apr</td>
											<td>Mei</td>
											<td>Jun</td>
											<td>Jul</td>
											<td>Agu</td>
											<td>Sep</td>
											<td>Okt</td>
											<td>Nov</td>
											<td>Des</td>
										</tr>
										<tr>
											<td><?=$report['ebook_readonly']->januari?></td>
											<td><?=$report['ebook_readonly']->februari ?></td>
											<td><?=$report['ebook_readonly']->maret ?></td>
											<td><?=$report['ebook_readonly']->april ?></td>
											<td><?=$report['ebook_readonly']->mei ?></td>
											<td><?=$report['ebook_readonly']->juni ?></td>
											<td><?=$report['ebook_readonly']->juli ?></td>
											<td><?=$report['ebook_readonly']->agustus ?></td>
											<td><?=$report['ebook_readonly']->september ?></td>
											<td><?=$report['ebook_readonly']->oktober ?></td>
											<td><?=$report['ebook_readonly']->november ?></td>
											<td><?=$report['ebook_readonly']->desember ?></td>
										</tr>
									</table>
								</td>		
							</tr> 
							<tr> 
								<td class="column-title" width="30%">Visitor Online Openlibrary(Google Analytics)</td>  
								<td class="column-title" width="5%">:</td>  
								<td class="column-title" width="65%">
									<table class="table table-striped table-bordered table-hover">
										<tr>
											<td colspan="13">Laporan Keseluruhan <?=$report['visitor_openlib'][0]->year?></td>
										</tr>
										<tr>
											<td></td>
											<td>Jan</td>
											<td>Feb</td>
											<td>Mar</td>
											<td>Apr</td>
											<td>Mei</td>
											<td>Jun</td>
											<td>Jul</td>
											<td>Agu</td>
											<td>Sep</td>
											<td>Okt</td>
											<td>Nov</td>
											<td>Des</td>
										</tr>
										<?php foreach($report['visitor_openlib'] as $row){ ?>
										<tr> 
											<td><?=$row->type?></td>
											<td><?=$row->januari?></td>
											<td><?=$row->februari ?></td>
											<td><?=$row->maret ?></td>
											<td><?=$row->april ?></td>
											<td><?=$row->mei ?></td>
											<td><?=$row->juni ?></td>
											<td><?=$row->juli ?></td>
											<td><?=$row->agustus ?></td>
											<td><?=$row->september ?></td>
											<td><?=$row->oktober ?></td>
											<td><?=$row->november ?></td>
											<td><?=$row->desember ?></td>
										</tr>
										<?php } ?>
										
									</table>
								</td>		
							</tr> 
							<tr> 
								<td class="column-title" width="30%">Visitor Online Eproceeding(Google Analytics)</td>  
								<td class="column-title" width="5%">:</td>  
								<td class="column-title" width="65%">
									<table class="table table-striped table-bordered table-hover">
										<tr>
											<td colspan="13">Laporan Keseluruhan <?=$report['visitor_eproc'][0]->year?></td>
										</tr>
										<tr>
											<td></td>
											<td>Jan</td>
											<td>Feb</td>
											<td>Mar</td>
											<td>Apr</td>
											<td>Mei</td>
											<td>Jun</td>
											<td>Jul</td>
											<td>Agu</td>
											<td>Sep</td>
											<td>Okt</td>
											<td>Nov</td>
											<td>Des</td>
										</tr>
										<?php foreach($report['visitor_eproc'] as $row){ ?>
										<tr> 
											<td><?=$row->type?></td>
											<td><?=$row->januari?></td>
											<td><?=$row->februari ?></td>
											<td><?=$row->maret ?></td>
											<td><?=$row->april ?></td>
											<td><?=$row->mei ?></td>
											<td><?=$row->juni ?></td>
											<td><?=$row->juli ?></td>
											<td><?=$row->agustus ?></td>
											<td><?=$row->september ?></td>
											<td><?=$row->oktober ?></td>
											<td><?=$row->november ?></td>
											<td><?=$row->desember ?></td>
										</tr>
										<?php } ?>
									</table>
								</td>		
							</tr>  
					</table>
					 
			</div>
			</form>
		</div>		 
	</div>
</div> 	


<?php $this->load->view('theme_footer'); ?>		
		
<script type="text/javascript">

var form = $('#form');  
form.validate({       
	ignore: ""
}); 

$( document ).ready(function() {
	$('#reservation').dateRangePicker(
	{
		showShortcuts: false,
		format: 'DD-MM-YYYY'
	});
	
	 
	
	var oTable = $('#table').DataTable({ 
		"tableTools": {
			"sSwfPath": "tools/assets/global/plugins/datatables1/extensions/TableTools/swf/copy_csv_xls.swf",

		},
		"dom": "T<'row'<'col-md-4 col-sm-12'l><'col-md-4 col-sm-12'r><'col-md-4 col-sm-12'f>><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>"  ,
        "processing": true,  
		"destroy": true,
        "order": [] ,   
		"pageLength": 25,        
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
       
        "columnDefs": [
			{ 
				"targets": [1], 
				"orderable": false,  
			},
        ]
    }); 
	
	
	$("#checkall").click(function() {
			if(!this.checked)
				$(':checkbox').prop('checked', false); 
			else $(':checkbox').prop('checked', true); 
			
    });
	
	$('.cb').click(function() {
      // If checkbox is not checked
      if(!this.checked) $('#checkall').prop('checked', false); 
	});	
	
});



function excels(){
	if (form.valid()) {
		$.ajax({
			url : 'index.php/katalog/excel',
			type: "POST",
			data: form.serialize(),
			beforeSend : function() {
				showLoading();
			},
			complete : function() {
				hideLoading();
			},
			success: function(data)
			{
				document.location.href =(data);
			}
		});	   
	}
}	
 

 

</script>