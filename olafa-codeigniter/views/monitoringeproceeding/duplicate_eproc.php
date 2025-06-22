 
<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?> <?php echo $site ?></strong> | Total <?php echo $total ?> / <?php echo $edition->nama ?> / Duplicate : <?php echo $duplicate.'/'.$total ?></h3>
		</div>
		<div class="panel-content pagination2">
			<form id="form" method="post" class="form-horizontal"> 
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo getLang("choose_eproceeding_edition") ?></label>
					<div class="col-sm-9"> 
						<select name="choose" id="choose" class="form-control">
							<?php
								foreach ($editions as $row){
									$mulai = explode('-',$row->datestart);
									$mulai = $mulai[2].'-'.$mulai[1].'-'.$mulai[0];
									$finish = explode('-',$row->datefinish);
									$finish = $finish[2].'-'.$finish[1].'-'.$finish[0];
							?>
								<option value="<?php echo $row->eproc_edition_id ?>" <?php echo (($row->eproc_edition_id==$choose)?'selected':'')?>><?php echo $row->nama.' ('.$mulai.' s/d '.$finish.')' ?></option> 
						<?php } ?>
						</select>
						
					</div> 
				</div> 
			</form>
			<div class="clearfix"><button type="button" onclick="window.open('<?php echo base_url()?>index.php/monitoringeproceeding/duplicateall/<?php echo $edition->eproc_edition_id ?>','_self')" class="btn btn-primary" style="background-color:#4CAE4C;color:#fff;border:1px solid #4CAE4C">Duplicate All</button></div>
	
			<div id="dt_table1">
				<table id="table" class="table table-striped table-bordered table-hover">
					<thead>
						<?php if ($detail=="ta"){ ?>
						
						<tr> 
							<th width="5%"># </th>
							<th width="10%">NIM </th>
							<th width="20%">Nama </th>
							<th width="45%">Judul </th>
							<th width="20%">Status </th> 
						</tr>
						
						<?php }else { ?> 
						
						<tr> 
							<th width="5%"># </th>
							<th width="10%">NIM </th>
							<th width="20%">Nama </th>
							<th width="10%">Katalog </th>
							<th width="30%">Judul </th>
							<th width="10%">Status </th>
							<th width="15%">Action </th>
							
						</tr>
						
							<?php } ?> 
					</thead>

					<tbody>
						 <?php   
							$no=1; foreach ($data as $row)  { 
							
							?>
							<tr> 
								<td class=""><?php echo  $no ?></td>
								<td class=""><?php echo  $row->master_data_user ?></td>
								<td class=""><?php echo  ucwords(strtolower($row->master_data_fullname)) ?></td>
								<td class=""><?php echo  ucwords(strtolower($row->code)) ?></td> 
								<td class=""><?php echo  ucwords(strtolower($row->title)) ?></td> 
								<td class=""><?php echo  $status[$row->code] ?></td>
								<td class=""><?php echo  $action[$row->code] ?></td>
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

								
<script language="javascript" type="application/javascript"> 
$(document).ready(function () {  
	
	$( "#choose" ).change(function() {
		$( "#form" ).submit();
	}); 
});
</script>