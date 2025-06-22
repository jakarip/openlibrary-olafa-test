 <div class="modal-header bg-red">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
	<h4 class="modal-title" id="myModalLabel">Detail  <?php echo ucwords(strtolower($type))?> MK <?php echo ucwords(strtolower($mk['namamk']))?></h4>
</div>
<div class="modal-body">
	<div class="panel-content pagination2">
		<div id="dt_table1">
			<table id="table" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th width="3%" class="column-title">#</th>
						<th width="10%" class="column-title">No Induk</th>
						<th width="10%" class="column-title">No Kelas</th>
						<th width="37%" class="column-title">Judul Buku</th>
						<th width="15%" class="column-title">Pengarang</th>
						<th width="15%" class="column-title">Tahun Terbit</th>
						<th width="15%" class="column-title">ISBN</th>
						<th width="12%" class="column-title">Jumlah Total</th> 
						<th width="12%" class="column-title">Jumlah Tersedia</th> 
					</tr>
				</thead>
				<tbody>			
				<?php  
				$style 	= 'even pointer'; $no=0;  
				foreach ($bukuref as $row) { ?>
					<tr class="<?php echo $style?>">
						<td class=""><?php echo  ++$no ?></td>
						<td class=""><?php echo  $row->kode_buku ?></td>
						<td class=""><?php echo  $row->klasifikasi ?></td>
						<td class=""><?php echo  ucwords(strtolower($row->title)) ?></td>
						<td class=""><?php echo  $row->author ?></td>
						<td class=""><?php echo  $row->published_year ?></td>
						<td class=""><?php echo  $row->isbn ?></td>
						<td class=""><?php echo  $row->eks ?> Eksemplar</td> 
						<td class=""><?php echo  $row->tersedia ?> Eksemplar</td> 
					</tr> 
				<?php  
					if($style 	= 'even pointer') $style 	= 'odd pointer'; 
					else $style 	= 'even pointer'; 
				} ?> 
				</tbody>
				
			</table>
		</div>
	</div>
</div>                   
<div class="modal-footer">
	<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("close") ?></button>
</div>

 