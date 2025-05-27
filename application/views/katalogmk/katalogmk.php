<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
			<div class="row">
				<div class="col-lg-6" > 
					  <h3><i class="fa fa-files-o"></i><strong><?php echo getLang("catalog"); ?></strong></h3> 
				</div>
				<div class="text-right col-lg-2" style="margin-top:1px;text-align:right;padding-top:7px;">
					<?php echo getLang('type') ?> </div>
				<div class="text-right col-lg-4" style="margin-top:1px;">
					<select class="form-control tipe" name="type"> 
						<?php 
							
							if(isset($tipe)){
								foreach($tipe as $row){
							echo "<option value='".$row->id."' ".($row->id=='1'?'selected':'').">".$row->name."</option>";
								}
							}
						?>
					</select>
				</div>
			</div>  
		</div>
		
		<div class="panel-content">
			
			<div class="row">
				<div class="col-lg-6" >  
				</div>
				<div class="text-right col-lg-2" style="margin-top:1px;text-align:right;padding-top:7px;">
					<?php echo getLang('search_by') ?> </div>
				<div class="text-right col-lg-4" style="margin-top:1px;">
					<select class="form-control searchtype">
						<option value="all"><?php echo getLang('all') ?></option> 
						<option value="title"><?php echo getLang('title') ?></option> 
						<option value="subject"><?php echo getLang('subjects') ?></option> 
						<option value="author"><?php echo getLang('author') ?></option> 
					</select>
				</div>
			</div>  <br>
			<div id="dt_table1">
				<table id="table" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th width="5%"><?php echo getLang("no") ?></th>
							<th width="5%"><?php echo getLang("type") ?></th>
							<th width="5%"><?php echo getLang("code") ?></th>
							<th width="20%"><?php echo getLang("title") ?></th>
							<th width="20%"><?php echo getLang("classification") ?></th>
							<th width="15%"><?php echo getLang("subjects") ?></th> 
							<th width="15%"><?php echo getLang("author") ?></th>
							<th width="10%"><?php echo getLang("year") ?></th>
							<th width="10%"><?php echo getLang("total mapping") ?></th>
							<th width="5%"><?php echo getLang("action") ?></th>
						</tr>
					</thead>
					<tbody>
							
					</tbody>
					
				</table>
			</div>
		</div>
	</div>
</div> 						

<?php $this->load->view('theme_footer'); ?>
								
<script language="javascript" type="application/javascript"> 
$(document).ready(function () { 
	 
	table = searching("1","all");
	
	$('.tipe').on("change", function(e) {  
		table = searching($(this).select2("val"),$('.searchtype').select2("val")); 
    });    
	 
   
   $('#table tbody').on('click', 'td', function () {
		var aData = table.cell( this ).index().row;
		table.rows( aData ).nodes().to$().addClass('highlight_row_datatables');
	});
});

function searching(tipe,searchtype,search=""){
	table = datatables(tipe,searchtype,search); 
	$('.dataTables_filter input').unbind().bind('keyup', function(e) {
	   if(e.keyCode == 13) {  
			searching(tipe,$('.searchtype').select2("val"),this.value); 
		}
	});
	
	return table;
}

function datatables(tipe,searchtype,searchs=""){
	return $('#table').DataTable({ 
			"processing": true,  
			"serverSide": true,  
			"destroy": true,
			'order':[
				[0, 'asc']
			],
			"pageLength": 25,        
			"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
			"ajax": {
				"url": 'index.php/katalogmk/ajax_index',
				"type": "POST",
				"data" : {
					tipe : tipe,
					searchtype : searchtype,
					searchs : searchs
				}
			}, 
			"columnDefs": [
				{ 
					"targets": [ -2,-1 ], 
					"orderable": false,  
				},
			], 
			"fnRowCallback": function( nRow, aaData, iDisplayIndex ) {
			if ( aaData[8] != "0 MK" )
				{ 
					$(nRow).addClass('highlight_row_datatables');
					//$('td:eq(1)', nRow).addClass( 'testrow'); 
				}
			}
		}); 
}


</script>					