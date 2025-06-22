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
					<div class="col-sm-5 prepend-icon"> 
						 <input type="text" name="reservation" class="form-control" id="reservation" placeholder="<?php echo getLang('choose_date')?>" value="<?php echo (ISSET($reservation)?$reservation:'') ?>"  aria-describedby="inputSuccess2Status" ><i class="fa fa-calendar"></i>
					</div> 
					
				</div> 
				<div class="form-group">
					<label class="col-sm-2 control-label"><?php echo getLang('jenis katalog')?></label>
					<div class="col-sm-5"> 
						 <select class="form-control tipe" data-search="true" name='tipe'> 
							<option value='all'>Semua</option>
							<?php 
								
								if(isset($tipe)){
									foreach($tipe as $row){
										echo "<option value='".$row->id."' ".($choose==$row->id?'selected':'').">".$row->name."</option>";
									}
								}
							?>
						</select> 
					</div>   
				</div> 
				<div class="form-group">
					<label class="col-sm-2 control-label"><?php echo getLang('Beli/Sumbangan Buku')?></label>
					<div class="col-sm-5"> 
						 <select class="form-control status" data-search="true" name='status'> 
							<option value='all'>Semua</option>
							<option value='1' <?=($status=='1'?'selected':'' )?>>Beli</option>
							<option value='2' <?=($status=='2'?'selected':'' )?>>Sumbangan</option>
						</select>
					</div>  
			 	</div>  
				<div class="form-group">
					<label class="col-sm-2 control-label"><?php echo getLang('Pilih Status Buku')?></label>
					<div class="col-sm-5"> 
						 <select class="form-control status_book" data-search="true" name='status_book'> 
							<option value='7' <?=($status_book=='7'?'selected':'' )?>>Sedang Diproses</option>
							<option value='1' <?=($status_book=='1'?'selected':'' )?>>Tersedia</option> 
							<option value='3' <?=($status_book=='3'?'selected':'' )?>>Rusak</option>
							<option value='4' <?=($status_book=='4'?'selected':'' )?>>Hilang</option>
							<option value='5' <?=($status_book=='5'?'selected':'' )?>>Expired</option>
							<option value='6' <?=($status_book=='6'?'selected':'' )?>>Hilang Diganti</option>
							<option value='8' <?=($status_book=='8'?'selected':'' )?>>Cadangan</option>
							<option value='9' <?=($status_book=='9'?'selected':'' )?>>Weeding</option>
						</select>
					</div>  
				</div>  
				<div class="form-group">
					<label class="col-sm-2 control-label"><?php echo getLang("Pilih Lokasi") ?></label>
					<div class="col-sm-5"> 
						<select name="location[]" id="location" class="form-control select2"  multiple="multiple"> 
							<?php foreach($location as $row){ ?>
								<option value="<?=$row->id ?>" <?=(in_array($row->id,$location_choose)?'selected':'') ?>><?=$row->name ?></option>
							<?php
								} 
							?>
						</select> 
					</div> 
				</div>  
				<div class="form-group">
					<label class="col-sm-2 control-label"><?php echo getLang("Pilih Klasifikasi") ?></label>
					<div class="col-sm-5"> 
						<select name="klasifikasi" id="klasifikasi" class="form-control select2">  
							<option value="" <?=($klasifikasi==''?'selected':'' )?>>Semua</option>
							<option value="0" <?=($klasifikasi=='0'?'selected':'' )?>>000-099</option>
							<option value="1" <?=($klasifikasi=='1'?'selected':'' )?>>100-199</option>
							<option value="2" <?=($klasifikasi=='2'?'selected':'' )?>>200-299</option>
							<option value="3" <?=($klasifikasi=='3'?'selected':'' )?>>300-399</option>
							<option value="4" <?=($klasifikasi=='4'?'selected':'' )?>>400-499</option>
							<option value="5" <?=($klasifikasi=='5'?'selected':'' )?>>500-599</option>
							<option value="6" <?=($klasifikasi=='6'?'selected':'' )?>>600-699</option>
							<option value="7" <?=($klasifikasi=='7'?'selected':'' )?>>700-799</option>
							<option value="8" <?=($klasifikasi=='8'?'selected':'' )?>>800-899</option>
							<option value="9" <?=($klasifikasi=='9'?'selected':'' )?>>900-999</option> 
						</select> 
					</div> 
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label"><?php echo getLang('barcode')?></label>
					<div class="col-sm-5"> 
						 <input type="text" name="barcode" class="form-control" id="barcode" placeholder="<?php echo getLang('barcode')?>" value="<?php echo (ISSET($barcode)?$barcode:'') ?>"  ></i>
					</div> 
				</div> 
				<div class="form-group">
					<label class="col-sm-2 control-label"></label> 
						<label class="col-sm-7 control-label"><button type="button" onclick="submitforms()" value="submit"  id="submitform" name="report" class="btn btn-success">Report</button>&nbsp;<button type="button" id="excel" name="excel" onclick="excels()" class="btn btn-primary">Excel</button></label> 
				</div> 
			</form> 
			<form action="" method="post">
			<div id="dt_table1">
				<table id="table" class="table table-striped table-bordered table-hover">
					<thead>
						<tr class="headings"> 
							<th class="column-title" width="5%">#</th> 
							<th class="column-title" width="5%"><input name="checkall" id="checkall" class="tableflat" type="checkbox"  value="" /></th>
							<th class="column-title" width="10%"><?php echo getLang("type") ?></th>
							<th class="column-title" width="10%"><?php echo getLang("catalog") ?></th>
							<th class="column-title" width="10%"><?php echo getLang("barcode") ?></th>
							<th class="column-title" width="15%"><?php echo getLang("classification") ?></th>
							<th class="column-title" width="20%"><?php echo getLang("title") ?></th>
							<th class="column-title" width="15%"><?php echo getLang("author") ?></th>
							<th class="column-title" width="10%"><?php echo getLang("publisher") ?></th> 		
							<th class="column-title" width="10%"><?php echo getLang("lokasi") ?></th> 	
							<th class="column-title" width="10%"><?php echo getLang("status") ?></th> 																					
						</tr>
					</thead> 
					<tbody>
							<?php 
							$no		= 1; 
							$style 	= 'even pointer'; 
							if(ISSET($book)){
								foreach ($book as $row) {   
								?>
								<tr class="<?php echo $style?>"> 
									<td class="" align="center"><?php echo  $no; ?></td> 
									<td class="center "><?php if ($this->session->userdata('login')) { ?>
										<input type="checkbox" class="cb" name="olah[]" value="<?php echo $row->id ?>">
										<?php } ?></td>
									<td class=""><?php echo  ucwords(strtolower($row->tipe)) ?></td>
									<td class=""><?php echo  ucwords(strtolower($row->catalog)) ?></td>
									<td class=""><?php echo  ucwords(strtolower($row->barcode)) ?></td> 
									<td class=""><?php echo  ucwords(strtolower($row->klasifikasi)) ?></td>
									<td class=""><?php echo  ucwords(strtolower($row->title)) ?></td>  
									<td class=""><?php echo  ucwords(strtolower($row->author)) ?></td> 
									<td class=""><?php echo  ucwords(strtolower($row->publisher_name)) ?></td> 
									<td class=""><?php echo  ucwords(strtolower($row->location_name)) ?></td> 
									<td class=""><?php echo  ucwords(strtolower($row->origination=='1'?'beli':'sumbangan')) ?></td> 
								</tr>


								<?php 
									$no++; 
									if($style 	= 'even pointer') $style 	= 'odd pointer'; 
									else $style 	= 'even pointer'; 
								}
								?> 
							<?php } ?>

                               
                                     </tbody>
					<tfoot>
						<tr>
							<td colspan="8">
							<?php if ($this->session->userdata('login')) { 
									echo '<button   type="submit" value="submit" name="submit" class="btn btn-success">Ubah Status Tersedia</button> ';
									} ?>
							</td>
						</tr>
					</tfoot>
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
		scrollCollapse: true,
		scrollY: '500px',
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


function submitforms(){
	if (form.valid()) {
		if($("#barcode").val()=="" && $("#reservation").val()=="" ) alert("Silahkan isi tanggal atau isi barcode");
		else {
			form.trigger("submit");
			// $.ajax({
			// 	url : 'index.php/katalog/excel', 
			// 	type: "POST",
			// 	data: form.serialize(),
			// 	beforeSend : function() {
			// 		showLoading();
			// 	},
			// 	complete : function() {
			// 		hideLoading();
			// 	},
			// 	success: function(data)
			// 	{
			// 		document.location.href =(data);
			// 	}
			// });	   
		}
	}
}	

function excels(){
	if (form.valid()) {
		if($("#barcode").val()=="" && $("#reservation").val()=="" ) alert("Silahkan isi tanggal atau isi barcode");
		else {
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
}	
 

 

</script>