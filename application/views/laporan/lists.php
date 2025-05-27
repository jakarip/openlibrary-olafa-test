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
					<label class="col-sm-2 control-label"><?php echo getLang("choose_type") ?></label>
					<div class="col-sm-5"> 
						<select name="type" id="type" class="form-control select2"  multiple="multiple"> 
							<?php foreach($type as $row){ ?>
								<option value="<?=$row->id ?>" ><?=$row->name ?></option>
							<?php
								} 
							?>
						</select> 
					</div> 
				</div> 		
				<div class="form-group">
					<label class="col-sm-2 control-label"><?php echo getLang('choose_date')?></label>
					<div class="col-sm-5 prepend-icon"> 
						 <input type="text" name="reservation" class="form-control" id="reservation" placeholder="<?php echo getLang('choose_date')?>" aria-describedby="inputSuccess2Status"><i class="fa fa-calendar"></i>
					</div>  
				</div> 		 
				<div class="form-group">
					<label class="col-sm-2 control-label"><?php echo getLang("choose_classification") ?></label>
					<div class="col-sm-5"> 
						<select name="klasifikasi" id="klasifikasi" class="form-control select2">  
							<option value="">Semua</option>
							<option value="0">000-099</option>
							<option value="1">100-199</option>
							<option value="2">200-299</option>
							<option value="3">300-399</option>
							<option value="4">400-499</option>
							<option value="5">500-599</option>
							<option value="6">600-699</option>
							<option value="7">700-799</option>
							<option value="8">800-899</option>
							<option value="9">900-999</option> 
						</select> 
					</div> 
				</div> 		
				<div class="form-group">
					<label class="col-sm-2 control-label"> </label>
					<label class="col-sm-10 control-label"><button type="button" value="submit" id="submitdate" name="submit" class="btn btn-success">Report</button>&nbsp;<button type="button" name="download"id="download" class="btn btn-primary">Download</button></label> 
				</div> 
				
			</form> 
			<div id="dt_table1">
				<table id="table" class="table table-striped table-bordered table-hover">
					<thead>
						<tr class="headings"> 
							<th class="column-title" width="5%">#</th> 
							<th class="column-title" width="10%"><?php echo getLang("type") ?></th>
							<th class="column-title" width="10%"><?php echo getLang("catalog") ?></th> 
							<th class="column-title" width="10%"><?php echo getLang("classification") ?></th>
							<th class="column-title" width="12%"><?php echo getLang("title") ?></th>
							<th class="column-title" width="12%"><?php echo getLang("author") ?></th> 
							<th class="column-title" width="12%"><?php echo getLang("publisher") ?></th> 
							<th class="column-title" width="10%"><?php echo getLang("year") ?></th> 
							<th class="column-title" width="10%"><?php echo getLang("copy") ?></th>  																						
						</tr>
					</thead> 
				</table>
			</div>
		</div>		 
	</div>
</div> 	


<?php $this->load->view('theme_footer'); ?>	
					 
<script type="text/javascript">
var table 		= '#table';      
$( document ).ready(function() {  
	
	$(table).DataTable({ 
        "processing": true,  
        "serverSide": true,  
		"destroy": true,
        "order": [] ,   
		"pageLength": 25,        
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
        "ajax": {
            "url": 'index.php/katalog/ajax_katalog',
            "type": "POST",  
			"data" : function(data) {
				data.month			= $('#reservation').val();
				data.status			= $('#status').val();
				data.type			= $('#type').val(); 
				data.klasifikasi	= $('#klasifikasi').val();
			}
        }, 
        "columnDefs": [
			{ 
				"targets": [], 
				"orderable": false,  
			},
        ]
    }); 
	
 
	$( "#submitdate" ).click(function( event ) {
		$(table).dataTable().fnDraw();
	}); 
	 
	$('#type').select2({  
		allowClear: true, 
		tags: true,
		tokenSeparators: [',', ' '] 
	});
	 
	$('#kasifikasi').select2();
	
	
	$('#reservation').dateRangePicker(
	{
		showShortcuts: false,
		format: 'DD-MM-YYYY'
	}); 
	
    $("#download").click(function() {
		
		showLoading();
		$.ajax({
			type : "POST",
			url: "index.php/katalog/lists_catalog",
			dataType:'JSON',
			data : {
				date :  $("#reservation").val(),
				status :  $("#status").val(),
				type :  $("#type").val(),
				klasifikasi :  $("#klasifikasi").val()
			},
			success: function(result){  
				hideLoading();
				window.location.href="index.php/katalog/download/"+result;
			}  
		}); 
	}); 
	  
	 
}); 
</script>