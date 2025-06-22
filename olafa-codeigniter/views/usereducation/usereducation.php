<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong> </h3>
		</div>
		<div class="panel-content pagination2">
			<form id="form" class="form-horizontal"> 
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo getLang("choose_year") ?></label>
					<div class="col-sm-9">
						<select name="tahun" id="tahun" class="form-control">
							<?php
								foreach ($curriculum as $row){
									echo '<option value="'.$row->useredu_year.'">'.$row->useredu_year.'</option>';
								}
							?>
						</select>  
					</div> 
				</div> 
			</form> 
			<div id="dt_table1">
			<table class="dt_table1 table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th width="5%"><?php echo getLang("no") ?></th>
						<th width="25%"><?php echo getLang("faculty") ?></th>
						<th width="25%"><?php echo getLang("study_program") ?></th> 
						<th width="20%"><?php echo getLang("total") ?></th> 
						<th width="20%"><?php echo getLang("hadir") ?></th> 
						<th width="10%"><?php echo getLang("action") ?></th>
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
<script type="text/javascript">
var table;
$(document).ready(function(){  
	dt_table($('#tahun').val());
	
	$('.dataTables_filter input').unbind().bind('keyup', function(e) {
       if(e.keyCode == 13) {
		$(".dt_table1").dataTable().fnFilter(this.value);
		}
	}); 
	
   $('#tahun').on('change', function(e) { 
        dt_table($(this).val());
		$('.dataTables_filter input').unbind().bind('keyup', function(e) {
		   if(e.keyCode == 13) {
			$(".dt_table1").dataTable().fnFilter(this.value);
			}
		});
	});
});

function dt_table(val) {
	table = $('.dt_table1').DataTable({ 
        "processing": true,  
        "serverSide": true,  
		"destroy": true,
        "order": [],   
		"pageLength": -1,        
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
        "ajax": {
            "url": 'index.php/usereducation/ajax_index',
            "type": "POST",
			"data" : {
				year : val
			}
        }, 
        "columnDefs": [
			{ 
				"targets": [ -1 ], 
				"orderable": false,  
			},
        ]
    }); 
}

function excel(id,year) {
	$.ajax({
        url : 'index.php/bahanpustaka/excel',
        type: "POST",
		data: {
				year :year,
				id : id
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

function excel_header() {
	$.ajax({
        url : 'index.php/bahanpustaka/excel_header',
        type: "POST",
		data: {
				year : $("#tahun").val()
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
 

function reload() {
   table.draw();
}

</script>