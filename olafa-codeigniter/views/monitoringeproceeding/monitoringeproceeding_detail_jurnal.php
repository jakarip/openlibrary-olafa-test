
<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?> <?php echo $site ?></strong> | <?php echo $jurusan->nama_prodi ?> / Total <?php echo $total ?> / <?php echo $edition->nama ?></h3>
		</div>
		<div class="panel-content pagination2">
			<div id="dt_table1">
				<table id="table" class="table table-striped table-bordered table-hover">
					<thead>
						<?php if ($detail=="ta"){  ?>
						
						<tr class="headings"> 
							<th width="5%"># </th>
							<th width="10%">NIM </th>
							<th width="20%">Nama </th>
							<th width="45%">Judul </th>
							<th width="20%">Status </th> 
						</tr>
						<?php } else if ($detail=="publish"){ ?>
						<tr class="headings"> 
							<th width="5%"># </th>
							<th width="10%">NIM </th>
							<th width="20%">Nama </th>
							<th width="65%">Judul </th>
							<th width="5%">Surat Bebas Pinjam </th>
						</tr>
						<?php }else { ?> 
						
						<tr class="headings"> 
							<th width="5%"># </th>
							<th width="10%">NIM </th>
							<th width="20%">Nama </th>
							<th width="70%">Judul </th> 
						</tr>
						
							<?php } ?> 
					</thead>

					<tbody>
						 <?php  													
							$no=1; foreach ($data as $row)  { ?>
							<tr> 
								<td class=""><?php echo  $no ?></td>
								<td class=""><?php echo  $row->master_data_user ?></td>
								<td class=""><?php echo  ucwords(strtolower($row->master_data_fullname)) ?></td>
								<td class=""><?php echo  ucwords(strtolower($row->title)) ?></td> 
							
								<?php if ($detail=="publish") { ?> <td class=""><?php echo ($row->free_letter>=1 ? '&#9989;' : '-'); ?></td> <?php } ?>
									  
								<?php if ($detail=="ta") { ?> <td class=""><?php echo  ucwords(strtolower($row->state_name)) ?></td> <?php } ?>
							 </tr> 	

							<?php  
								$no++;
							} ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div> 	  


<?php $this->load->view('theme_footer'); ?>