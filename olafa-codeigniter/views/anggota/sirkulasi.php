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
							<th class="column-title" width="5%" rowspan="2">#</th>
							<th class="column-title" width="30%" rowspan="2"><?php echo getLang("faculty") ?></th>
							<th class="column-title" width="25%" rowspan="2"><?php echo getLang("study_program") ?></th>
							<th class="column-title" width="20%" colspan="2"><?php echo getLang("borrowing") ?></th>
							<th class="column-title" width="20%" colspan="2"><?php echo getLang("returning") ?></th> 			
						</tr>
						<tr class="headings">  
							<th class="column-title" width="10%"><?php echo getLang("member") ?></th>
							<th class="column-title" width="10%"><?php echo getLang("borrowing") ?></th>
							<th class="column-title" width="10%"><?php echo getLang("member") ?></th>
							<th class="column-title" width="10%"><?php echo getLang("returning") ?></th> 			
						</tr>
					</thead>

					<tbody>
					<?php 
					$no		= 1; 
					if(ISSET($jurusan)){
						$anggotapinjam 	= 0;
						$anggotakembali = 0;
						$peminjaman 	= 0;
						$pengembalian 	= 0;
						
						foreach ($jurusan as $row) {   
						?>
						<tr> 
							<td class="" align="center"><?php echo  $no; ?></td>
							<td class=""><?php echo  ucwords(strtolower($row->nama_fakultas)) ?></td>
							<td class=""><?php echo  ucwords(strtolower($row->nama_prodi)) ?></td>
							<td class=""><?php echo  $pinjam[$no]->anggota+$download[$no]->anggota ?></td> 
							<td class=""><?php echo  $pinjam[$no]->total+$download[$no]->total ?></td> 
							<td class=""><?php echo  $kembali[$no]->anggota+$download[$no]->anggota ?></td> 
							<td class=""><?php echo  $kembali[$no]->total+$download[$no]->total ?></td> 
						</tr>


						<?php 
							$anggotapinjam 	= $anggotapinjam+$pinjam[$no]->anggota+$download[$no]->anggota;
							$anggotakembali = $anggotakembali+$kembali[$no]->anggota+$download[$no]->anggota;
							$peminjaman 	= $peminjaman+$pinjam[$no]->total+$download[$no]->total;
							$pengembalian 	= $pengembalian+$kembali[$no]->total+$download[$no]->total;
							$no++; 
						}
						?>
						<tr> 
							<td class="" align="center"><?php echo  $no; ?></td>
							<td class="" colspan="2"><?php echo getLang("lecture").' / '.getLang("employee") ?></td> 
							<td class=""><?php echo  $pinjam[$no]->anggota+$download[$no]->anggota ?></td> 
							<td class=""><?php echo  $pinjam[$no]->total+$download[$no]->total ?></td> 
							<td class=""><?php echo  $kembali[$no]->anggota+$download[$no]->anggota ?></td> 
							<td class=""><?php echo  $kembali[$no]->total+$download[$no]->total ?></td> 
						</tr>
						<?php 
							$anggotapinjam 	= $anggotapinjam+$pinjam[$no]->anggota+$download[$no]->anggota;
							$anggotakembali = $anggotakembali+$kembali[$no]->anggota+$download[$no]->anggota;
							$peminjaman 	= $peminjaman+$pinjam[$no]->total+$download[$no]->total;
							$pengembalian 	= $pengembalian+$kembali[$no]->total+$download[$no]->total;
						?>
						<tr>
							<td colspan="3"><?php echo getLang("total") ?></td>
							<td><?php echo $anggotapinjam.'<br>'.getLang("member") ?></td>
							<td><?php echo $peminjaman.'<br>'.getLang("borrowing") ?></td>
							<td><?php echo $anggotakembali.'<br>'.getLang("member") ?></td>
							<td><?php echo $pengembalian.'<br>'.getLang("returning") ?></td> 	
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
	$("#single_cal1").datepicker( {
		dateFormat: "mm-yy",
		changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
		onClose: function() {
			var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
			var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
			$(this).datepicker('setDate', new Date(iYear, iMonth, 1));
		}, 
		beforeShow: function() {
		   if ((selDate = $(this).val()).length > 0) 
		   { 
			  iYear = selDate.substring(selDate.length - 4, selDate.length);
			  iMonth = selDate.substring(0, 2)-1;
			  $(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
			   $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
		   }
		}
	});
});
</script>