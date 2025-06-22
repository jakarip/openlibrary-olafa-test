
<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong> | <?php echo getLang('total') ?> <?php echo $total ?></h3>
		</div>
		<div class="panel-content pagination2">
			<div id="dt_table1">
				<table id="table" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th width="5%"><?php echo getLang("no") ?></th>
							<th width="30%"><?php echo getLang("title") ?> </th>
							<th width="35%"><?php echo getLang("college") ?> </th>
							<th width="10%"><?php echo getLang("total") ?></th> 
							<th width="10%"><?php echo getLang("issn") ?></th> 
							<th width="10%"><?php echo getLang("year") ?></th>
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
	
	
	table = $('#table').DataTable({ 
        "processing": true,  
        "serverSide": true,  
		"destroy": true,
        "order": [ 3, 'desc' ] ,   
		"pageLength": 25,        
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
        "ajax": {
            "url": 'index.php/prosidingseminar/ajax_index',
            "type": "POST"
        }, 
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
});
</script>	