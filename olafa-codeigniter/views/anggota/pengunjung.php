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
					<label class="col-sm-2 control-label"><?php echo getLang('choose_month_and_year')?></label>
					<div class="col-sm-8 prepend-icon"> 
						 <input type="text" name="month" value="<?php echo (empty($month)?'':$month)?>" class="form-control has-feedback-left" id="single_cal1" placeholder="<?php echo getLang('choose_month_and_year')?>" aria-describedby="inputSuccess2Status"><i class="fa fa-calendar"></i>
					</div> 
					<label class="col-sm-2 control-label"><button type="submit" value="submit" name="submit" class="btn btn-success">Report</button></label>
				</div> 
			</form> 
			<div class="x_content">
				<table class="table table-striped responsive-utilities jambo_table bulk_action">
					<thead>
						<tr class="headings"> 
							<th class="column-title" width="4%">#</th>
							<th class="column-title" width="25%"><?php echo getLang("faculty") ?></th>
							<th class="column-title" width="25%"><?php echo getLang("study_program") ?></th>
							<th class="column-title" width="10%"><?php echo getLang("day") ?> (08:00 - 16:30)</th>
							<th class="column-title" width="10%"><?php echo getLang("night") ?> (16:30 - 19:30) </th>
							<th class="column-title" width="16%"><?php echo getLang("total") ?> </th> 		
							<th class="column-title" width="10%"><?php echo getLang("download") ?> </th> 										
						</tr>
					</thead>
					<tbody>
					<?php 
					$no		= 1; 
					if(ISSET($jurusan)){
						foreach ($jurusan as $row) {   
						?>
						<tr> 
							<td class="" align="center"><?php echo  $no; ?></td>
							<td class=""><?php echo  ucwords(strtolower($row->nama_fakultas)) ?></td>
							<td class=""><?php echo  ucwords(strtolower($row->nama_prodi)) ?></td>
							<td class=""><?php echo  $day[$no] ?></td> 
							<td class=""><?php echo  $night[$no] ?></td> 
							<td class=""><?php echo  $day[$no]+$night[$no] ?></td> 
							<td class=""><button class="btn btn-sm btn-success btn-embossed" onclick="excel('<?php echo (empty($month)?'':$month)?>','<?=$row->c_kode_prodi?>')" title="download"><i class="fa fa-cloud-download"></i></button></td> 
						</tr>


						<?php 
							$no++; 
						}
						?> 
						<tr> 
							<td class="" align="center"><?php echo  $no; ?></td>
							<td class="" colspan="2">Dosen / Pegawai</td> 
							<td class=""><?php echo  $day[$no] ?></td> 
							<td class=""><?php echo  $night[$no] ?></td> 
							<td class=""><?php echo  $day[$no]+$night[$no] ?></td> 
							<td class=""><button class="btn btn-sm btn-success btn-embossed" onclick="excel('<?php echo (empty($month)?'':$month)?>','dospeg')" title="download"><i class="fa fa-cloud-download"></i></button></td> 
						</tr> 
						<?php 
							$no++;  
						?>
						<tr> 
							<td class="" align="center"><?php echo  $no; ?></td>
							<td class="" colspan="2">Public</td> 
							<td class=""><?php echo  $day[$no] ?></td> 
							<td class=""><?php echo  $night[$no] ?></td> 
							<td class=""><?php echo  $day[$no]+$night[$no] ?></td> 
							<td class=""><button class="btn btn-sm btn-success btn-embossed" onclick="excel('<?php echo (empty($month)?'':$month)?>','public')" title="download"><i class="fa fa-cloud-download"></i></button></td> 
						</tr>
						<tr>
							<td colspan="3">TOTAL</td>
							<td><?php echo array_sum($day).' Pengunjung' ?></td>
							<td><?php echo array_sum($night).' Pengunjung' ?></td>
							<td><?php echo array_sum($day)+array_sum($night).' Pengunjung' ?></td>
							<td></td>
						</tr>
					<?php } ?>
					 </tbody>
				</table>
			</div>
		</div>		 
	</div>
</div>  				 

<?php $this->load->view('theme_footer'); ?>					
<script type="text/javascript">
$(document).ready(function () {
	// $("#single_cal1").datepicker( {
	// 	dateFormat: "mm-yy",
	// 	changeMonth: true,
    //     changeYear: true,
    //     showButtonPanel: true,
	// 	onClose: function() {
	// 		var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
	// 		var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
	// 		$(this).datepicker('setDate', new Date(iYear, iMonth, 1));
	// 	}, 
	// 	beforeShow: function() {
	// 	   if ((selDate = $(this).val()).length > 0) 
	// 	   { 
	// 		  iYear = selDate.substring(selDate.length - 4, selDate.length);
	// 		  iMonth = selDate.substring(0, 2)-1;
	// 		  $(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
	// 		   $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
	// 	   }
	// 	}
	// });

	
	$('#single_cal1').dateRangePicker(
	{
		showShortcuts: false,
		format: 'DD-MM-YYYY'
	});
}); 


function excel(month,prodi) {
	$.ajax({
        url : 'index.php/anggota/excel',
        type: "POST",
		data: {
				month :month,
				prodi : prodi
			},
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
</script>