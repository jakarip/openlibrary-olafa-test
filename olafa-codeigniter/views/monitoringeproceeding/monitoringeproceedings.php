
<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3><i class="fa fa-bar-chart"></i><strong><?php echo getLang("eproceeding_monitoring"); ?></strong></h3> 
		</div>
		<div class="panel-content">
			<form id="form" method="post" class="form-horizontal"> 
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo getLang("choose_eproceeding_edition") ?></label>
					<div class="col-sm-9"> 
						<select name="choose" id="choose" class="form-control">
							<?php
								foreach ($edition as $row){
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
			<div id="dt_table1">
			<table id="table" class="table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th width="4%" rowspan="2"><?php echo getLang("no") ?></th>
						<th width="7%" rowspan="2"><?php echo getLang("faculty") ?></th>
						<th width="7%" rowspan="2"><?php echo getLang("study_program") ?></th>
						<th width="7%" rowspan="2">TA/PA/Thesis Masuk</th>
						<th width="7%" rowspan="2">Jurnal Masuk</th>
						<th width="7%" rowspan="2">On Draft</th> 
						<th width="7%" rowspan="2">Need Revision</th>
						<th width="7%" rowspan="2">Ready for Review</th>
						<th width="8%" rowspan="2">Not Feasible Jurnal</th>
						<th width="7%" rowspan="2">Not Feasible All</th>
						<th width="8%" rowspan="2">Publish Eksternal</th>
					   <th width="8%" rowspan="2">Jurnal Approved <br> Publish Tel-U Proceeding</th>
						<th width="8%" colspan="4">Archived</th>
					</tr>
					<tr>
						<th width="2%">Not Feasible</th>
						<th width="2%">Publish Eksternal</th> 
						<th width="2%">Jurnal Approved <br> Publish Tel-U Proceeding</th> 		
						<th width="2%">Total</th> 					
					</tr>
				</thead>
				<tbody>
					<?php 
					$no		= 1; 
					$style 	= 'even pointer'; 
					if(ISSET($jurusan)){
						$tamasuks=0;$jurnals=0;$drafts=0;$revisions=0;$reviews=0;$feasiblejurnals=0;$feasiblealls=0;$eksternals=0;$jurnalpublishs=0;$archievedfeasibles=0;$archievedeksternals=0;$archievedjurnalpublishs=0;$archieveds=0;
						foreach ($jurusan as $key=>$row) {  
							if ($key==0){
								$eproc = $row->jenis_eproc;
							} 
							else {
								if ($eproc!=$row->jenis_eproc){ 
								?>
									<tr>
										<td><?php echo $eproc ?></td><td></td><td></td>
										<td><?php echo $tamasuks.' Judul' ?></td>
										<td><?php echo $jurnals.' Judul' ?></td>
										<td><?php echo $drafts.' Judul' ?></td>
										<td><?php echo $revisions.' Judul' ?></td>
										<td><?php echo $reviews.' Judul' ?></td>
										<td><?php echo $feasiblejurnals.' Judul' ?></td>
										<td><?php echo $feasiblealls.' Judul' ?></td>
										<td><?php echo $eksternals.' Judul' ?></td>
										<td><?php echo $jurnalpublishs.' Judul' ?></td>
										<td><?php echo $archievedfeasibles.' Judul' ?></td>
										<td><?php echo $archievedeksternals.' Judul' ?></td>
										<td><?php echo $archievedjurnalpublishs.' Judul' ?></td>
										<td><?php echo $archieveds.' Judul' ?></td>
									</tr>
						<?php		
								
								$eproc = $row->jenis_eproc;
								$tamasuks=0;$jurnals=0;$drafts=0;$revisions=0;$reviews=0;$feasiblejurnals=0;$feasiblealls=0;$eksternals=0;$jurnalpublishs=0;$archievedfeasibles=0;$archievedeksternals=0;$archievedjurnalpublishs=0;$archieveds=0;
							
								}
								else {
									$tamasuks+=$tamasuk[$no];$jurnals+=$jurnal[$no];$drafts+=$draft[$no];$revisions+=$revision[$no];$reviews+=$review[$no];$feasiblejurnals+=$feasiblejurnal[$no];$feasiblealls+=$feasibleall[$no];$eksternals+=$eksternal[$no];$jurnalpublishs+=$jurnalpublish[$no];$archievedfeasibles+=$archievedfeasible[$no];$archievedeksternals+=$archievedeksternal[$no];$archievedjurnalpublishs+=$archievedjurnalpublish[$no];$archieveds+=$archieved[$no];
								}
							}
						?>
						<tr class="<?php echo $style?>"> 
							<td align="center"><?php echo  $no; ?></td>
							<td><?php echo  ucwords(strtolower($row->nama_fakultas)) ?></td>
							<td><?php echo  ucwords(strtolower($row->nama_prodi)) ?></td>
							<td><a href="<?php echo base_url()?>index.php/monitoringeproceeding/ta/<?php echo  strtolower($row->c_kode_prodi).'/'.$choose ?>" target="_blank"><?php echo  $tamasuk[$no] ?> Judul</a></td>
							<td><a href="<?php echo base_url()?>index.php/monitoringeproceeding/jurnal/<?php echo  strtolower($row->c_kode_prodi).'/'.$choose ?>" target="_blank"><?php echo  $jurnal[$no] ?> Judul</a></td> 
							<td><a href="<?php echo base_url()?>index.php/monitoringeproceeding/doc/22/<?php echo  strtolower($row->c_kode_prodi).'/'.$choose ?>" target="_blank"><?php echo  $draft[$no] ?> Judul</a></td>
							<td><a href="<?php echo base_url()?>index.php/monitoringeproceeding/doc/2/<?php echo  strtolower($row->c_kode_prodi).'/'.$choose ?>" target="_blank"><?php echo  $revision[$no] ?> Judul</a></td>
							<td><a href="<?php echo base_url()?>index.php/monitoringeproceeding/doc/1/<?php echo  strtolower($row->c_kode_prodi).'/'.$choose ?>" target="_blank"><?php echo  $review[$no] ?> Judul</a></td> 
							<td><a href="<?php echo base_url()?>index.php/monitoringeproceeding/doc/3/<?php echo  strtolower($row->c_kode_prodi).'/'.$choose ?>" target="_blank"><?php echo  $feasiblejurnal[$no] ?> Judul</a></td>
							<td><a href="<?php echo base_url()?>index.php/monitoringeproceeding/doc/4/<?php echo  strtolower($row->c_kode_prodi).'/'.$choose ?>" target="_blank"><?php echo  $feasibleall[$no] ?> Judul</a></td>
							<td><a href="<?php echo base_url()?>index.php/monitoringeproceeding/doc/52/<?php echo  strtolower($row->c_kode_prodi).'/'.$choose ?>" target="_blank"><?php echo  $eksternal[$no] ?> Judul</a></td> 
							<td><a href="<?php echo base_url()?>index.php/monitoringeproceeding/publish/<?php echo  strtolower($row->c_kode_prodi).'/'.$choose ?>" target="_blank"><?php echo  $jurnalpublish[$no] ?> Judul</a></td> 
							<td><a href="<?php echo base_url()?>index.php/monitoringeproceeding/archieved/3/<?php echo  strtolower($row->c_kode_prodi).'/'.$choose ?>" target="_blank"><?php echo  $archievedfeasible[$no] ?> Judul</a></td>
							 <td><a href="<?php echo base_url()?>index.php/monitoringeproceeding/archieved/52/<?php echo  strtolower($row->c_kode_prodi).'/'.$choose ?>" target="_blank"><?php echo  $archievedeksternal[$no] ?> Judul</a></td>
							 <td><a href="<?php echo base_url()?>index.php/monitoringeproceeding/archievedjournalpublish/<?php echo  strtolower($row->c_kode_prodi).'/'.$choose ?>" target="_blank"><?php echo  $archievedjurnalpublish[$no] ?> Judul</a></td>
							<td><a href="<?php echo base_url()?>index.php/monitoringeproceeding/doc/5/<?php echo  strtolower($row->c_kode_prodi).'/'.$choose ?>" target="_blank"><?php echo  $archieved[$no] ?> Judul</a></td>
						</tr>


						<?php 
							$no++; 
							if($style 	= 'even pointer') $style 	= 'odd pointer'; 
							else $style 	= 'even pointer'; 
						} 
					} 
					?> 
                </tbody>
				<tfoot>
					<tr>
						<td><?php echo $eproc ?></td><td></td><td></td>
						<td><?php echo $tamasuks.' Judul' ?></td>
						<td><?php echo $jurnals.' Judul' ?></td>
						<td><?php echo $drafts.' Judul' ?></td>
						<td><?php echo $revisions.' Judul' ?></td>
						<td><?php echo $reviews.' Judul' ?></td>
						<td><?php echo $feasiblejurnals.' Judul' ?></td>
						<td><?php echo $feasiblealls.' Judul' ?></td>
						<td><?php echo $eksternals.' Judul' ?></td>
						<td><?php echo $jurnalpublishs.' Judul' ?></td>
						<td><?php echo $archievedfeasibles.' Judul' ?></td>
						<td><?php echo $archievedeksternals.' Judul' ?></td>
						<td><?php echo $archievedjurnalpublishs.' Judul' ?></td>
						<td><?php echo $archieveds.' Judul' ?></td>
					</tr>
					<tr>
						<td colspan="3">TOTAL</td>
						<td><?php echo array_sum($tamasuk).' Judul' ?></td>
						<td><?php echo array_sum($jurnal).' Judul' ?></td>
						<td><?php echo array_sum($draft).' Judul' ?></td>
						<td><?php echo array_sum($revision).' Judul' ?></td>
						<td><?php echo array_sum($review).' Judul' ?></td>
						<td><?php echo array_sum($feasiblejurnal).' Judul' ?></td>
						<td><?php echo array_sum($feasibleall).' Judul' ?></td>
						<td><?php echo array_sum($eksternal).' Judul' ?></td>
						<td><?php echo array_sum($jurnalpublish).' Judul' ?></td>
						<td><?php echo array_sum($archievedfeasible).' Judul' ?></td>
						<td><?php echo array_sum($archievedeksternal).' Judul' ?></td>
						<td><?php echo array_sum($archievedjurnalpublish).' Judul' ?></td>
						<td><?php echo array_sum($archieved).' Judul' ?></td>
					</tr>
				</tfoot>
			</table>
			</div>
		</div> 
	</div>
</div> 		  

<?php $this->load->view('theme_footer'); ?>
								
<script language="javascript" type="application/javascript"> 
$(document).ready(function () {  
	
	table = $('#table').DataTable({ 
        "processing": true,   
		"destroy": true,
        "order": [] ,   
		"pageLength": -1,        
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
       
        "columnDefs": [
			{ 
				"targets": [ -1 ], 
				"orderable": false,  
			},
        ]
    }); 
	
	$('#dt_table1 .dataTables_filter input').unbind().bind('keyup', function(e) {
       if(e.keyCode == 13) {
		$("#table").dataTable().fnFilter(this.value);
		}
	}); 
	
	$( "#choose" ).change(function() {
		$( "#form" ).submit();
	}); 
});
</script>			