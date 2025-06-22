<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong></h3>
		</div>
			
		<div class="panel-content pagination2">
			<form id="form" class="form-horizontal" action="" method="post"> 
				<div class="form-group">
					<label class="col-sm-2 control-label"><?php echo getLang('choose_date')?></label>
					<div class="col-sm-3 prepend-icon"> 
						 <input type="text" name="reservation" class="form-control" id="reservation" placeholder="<?php echo getLang('choose_date')?>" value="<?php echo (ISSET($reservation)?$reservation:'') ?>"  aria-describedby="inputSuccess2Status"><i class="fa fa-calendar"></i>
					</div> 
					<label class="col-sm-2 control-label">Pilih Lokasi</label>
					<div class="col-sm-3"> 
						<select name="location[]" id="location" class="form-control select2"  multiple="multiple" style="width:100% !important"> 
							<?php foreach($location as $row){ ?>
								<option value="<?=$row->id ?>" <?=(in_array($row->id,$location_choose)?'selected':'') ?>><?=$row->name ?></option>
							<?php
								} 
							?>
						</select> 
					</div>
					<label class="col-sm-2 control-label"><button type="submit" value="submit" id="submitdate" name="report" class="btn btn-success">Report</button>
				</div> 
			</form> 
			<div class="x_content">
				<table class="table table-striped responsive-utilities jambo_table bulk_action">
					<thead>
						<tr class="headings"> 
							<th class="column-title" width="4%" rowspan="2">#</th>
							<th class="column-title" width="14%" rowspan="2">Nama</th>
							<th class="column-title" width="9%" rowspan="2">Judul</th>
							<th class="column-title" width="9%" rowspan="2">Total Eksemplar</th>
							<th class="column-title" width="64%" colspan="8" style="text-align:center">Eksemplar</th> 
						</tr>
						 <tr class="headings">  
							<th class="column-title" width="8%">Tersedia</th>
							<th class="column-title" width="8%">Dipinjam</th>
							<th class="column-title" width="7%">Rusak</th> 
							<th class="column-title" width="7%">Hilang Diganti</th> 
							<th class="column-title" width="7%">Sedang Diproses</th> 
							<th class="column-title" width="7%">Cadangan</th> 
							<th class="column-title" width="7%">Weeding</th>
							<th class="column-title" width="7%">Hilang</th>
							<th class="column-title" width="7%">Expired</th>
						</tr>
					</thead>

					<tbody>
								
					<?php 
					$no		= 1; 
					$style 	= 'even pointer'; 
					
					$judul 			= 0;
					$eksemplar		= 0;
					$tersedia 		= 0;
					$dipinjam 		= 0;
					$rusak 			= 0;
					$hilang 		= 0;
					$expired 		= 0;
					$hilang_diganti = 0;
					$diolah 		= 0;
					$cadangan 		= 0;
					$weeding 		= 0;
					
					foreach ($jurusan as $row) { 
						$judul 			= $judul+$row->judul;
						$eksemplar 		= $eksemplar+$row->eksemplar;
						$tersedia 		= $tersedia+$row->tersedia;
						$dipinjam 		= $dipinjam+$row->dipinjam;
						$rusak 			= $rusak+$row->rusak;
						$hilang 		= $hilang+$row->hilang;
						$expired 		= $expired+$row->expired;
						$hilang_diganti = $hilang_diganti+$row->hilang_diganti;
						$diolah			= $diolah+$row->diolah;
						$cadangan		= $cadangan+$row->cadangan;
						$weeding		= $weeding+$row->weeding;
					?> 
					<tr class="<?php echo $style?>">
						<td class=""><?php echo  $no; ?></td>
						<td class=""><?php echo  ucwords(strtolower($row->nama)) ?></td>
						<td class="" style="color:green;cursor:pointer" onclick="detail('<?=$row->id ?>','judul')"><?php echo  $row->judul ?></td>
						<td class="" style="color:green;cursor:pointer" onclick="detail('<?=$row->id ?>','eksemplar')"><?php echo  $row->eksemplar ?></td>
						<td class="" style="color:green;cursor:pointer" onclick="detail('<?=$row->id ?>','1')"><?php echo  $row->tersedia ?></td>
						<td class="" style="color:green;cursor:pointer" onclick="detail('<?=$row->id ?>','2')"><?php echo  $row->dipinjam ?></td>
						<td class="" style="color:green;cursor:pointer" onclick="detail('<?=$row->id ?>','3')"><?php echo  $row->rusak ?></td>
						<td class="" style="color:green;cursor:pointer" onclick="detail('<?=$row->id ?>','6')"><?php echo  $row->hilang_diganti ?></td>
						<td class="" style="color:green;cursor:pointer" onclick="detail('<?=$row->id ?>','7')"><?php echo  $row->diolah ?></td>
						<td class="" style="color:green;cursor:pointer" onclick="detail('<?=$row->id ?>','8')"><?php echo  $row->cadangan ?></td>
						<td class="" style="color:green;cursor:pointer" onclick="detail('<?=$row->id ?>','9')"><?php echo  $row->weeding ?></td>
						<td class="" style=";cursor:pointer" onclick="detail('<?=$row->id ?>','4')"><?php echo  $row->hilang ?></td>
						<td class="" style="cursor:pointer" onclick="detail('<?=$row->id ?>','5')"><?php echo  $row->expired ?></td>
					</tr>

					<?php 
						$no++; 
						if($style 	= 'even pointer') $style 	= 'odd pointer'; 
						else $style 	= 'even pointer'; 
					}
					?>
				
				</tbody>
				<tfoot>
					<tr class="<?php echo $style?>">
						<td class="" colspan="2">TOTAL</td>
						<td class="" style="color:green"><?php echo  $judul ?></td>
						<td class="" style="color:green"><?php echo  $eksemplar ?></td>
						<td class="" style="color:green"><?php echo  $tersedia ?></td>
						<td class="" style="color:green"><?php echo  $dipinjam ?></td>
						<td class="" style="color:green"><?php echo  $rusak ?></td>
						<td class="" style="color:green"><?php echo  $hilang_diganti ?></td>
						<td class="" style="color:green"><?php echo  $diolah ?></td>
						<td class="" style="color:green"><?php echo  $cadangan ?></td>
						<td class="" style="color:green"><?php echo  $weeding ?></td>
						<td class=""><?php echo  $hilang ?></td>
						<td class=""><?php echo  $expired ?></td>
					</tr>
				</table>
				</tfoot>
			</div>
		</div>		 
	</div>
</div>  		


				 

<div class="modal fade modalViewBuku" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" to click out and data-keyboard="false">
	<div class="modal-dialog modal-lg" style="width:80%"> 
		<div class="modal-header bg-red">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		  <h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?> Detail</strong></h3>
		</div>
		<div class="modal-content" style="padding:20px;">
			<table id="table-detail" class="table table-striped responsive-utilities jambo_table bulk_action">
				<thead>
					<tr class="headings"> 
						<th class="column-title" width="4%">#</th>
						<th class="column-title" width="12%">Kode Katalog</th>
						<th class="column-title" width="12%">Barcode</th>
						<th class="column-title" width="12%">Judul</th>
						<th class="column-title" width="12%">Subject</th> 
						<th class="column-title" width="12%">Klasifikasi</th>  
						<th class="column-title" width="12%">Pengarang</th> 
						<th class="column-title" width="12%">Publisher</th> 
						<th class="column-title" width="12%">Status</th>
					</tr> 
				</thead>

				<tbody>
				</tbody>
			</table> 
			<div class="modal-footer">
                <button type="button" class="btn btn-danger btn-labeled btn-xs pull-right" data-dismiss="modal">
                	<b><i class="icon-switch"></i></b> Close
                </button>                 
			</div>      
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
});
 
function detail(id,type){ 
	$('.modalViewBuku').modal('show');
	
	table = $('#table-detail').DataTable({ 
        "processing": true,  
        "serverSide": true,  
		"destroy": true,
        "order": [ 3, 'asc' ] ,   
		"pageLength": 25,       
		scrollY: '48vh', 
		scrollCollapse: true,		
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
        "ajax": {
            "url": 'index.php/katalog/ajax_statistik',
            "type": "POST",
			"data" : {
				id : id,
				type : type
			}
        }, 
        "columnDefs": [
			{ 
				"targets": [ -1 ], 
				"orderable": false,  
			},
        ]
    }); 	
}

 

</script>