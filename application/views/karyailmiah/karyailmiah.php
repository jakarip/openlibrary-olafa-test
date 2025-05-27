<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3><i class="fa fa-mortar-board"></i><strong><?php echo getLang("scientific_paper"); ?></strong></h3> 
		</div>
		<div class="panel-content pagination2">
			<div id="dt_table1">
				<table id="table" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th width="5%"><?php echo getLang("no") ?></th>
							<th width="20%"><?php echo getLang("faculty") ?></th>
							<th width="41%"><?php echo getLang("study_program") ?></th>
							<th width="24%"><?php echo getLang("scientific_paper_total") ?></th> 
							<th width="10%"><?php echo getLang("action") ?></th>
						</tr>
						<tfoot>
							<tr>
								<td colspan="3" align="center"><b>Total</b></td>
								<td align="right"><b><?php echo $total ?> Judul</b></td>
								<td></td>
							</tr>
						</tfoot>
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
        "order": [] ,   
		"pageLength": -1,        
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
        "ajax": {
            "url": 'index.php/karyailmiah/ajax_index',
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

	$("td .viewBuku").on( "click", function() {
		alert("aa");
		
	});
});
</script>					