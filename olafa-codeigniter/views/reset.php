 <?php
	$time 	= strtotime(date('Y-m-d'));
	$final 	= date("d-m-Y", strtotime("+1 month", $time));
	 
 ?>
<div class="row">
	<div class="col-lg-12 portlets">
	  <div class="panel"> 
		<div class="panel-content"> 
			<div class="panel-header bg-red">
			<h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong> </h3>
            </div>
			<div class="panel-content pagination2"> 
				<div class="filter-left" id="room"> 
					<form id="form" class="form-horizontal form-validation">
						<input type="hidden" name="id" id="id">
						<div class="modal-body">
							<div class="form-group">
								<label class="col-sm-3 control-label"><?php echo getLang('member_name'); ?> <span class="required-class">*) </span></label>
								 <div class="col-sm-9">
									<input class="form-control member" type="text" name="member" id="member" placeholder="<?php echo getLang('member_name'); ?>" required>
								</div>
							</div>
						</div>
						<div class="modal-footer"> 
							<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
							<button type="button" class="btn btn-success" onclick="del()"><i class="fa fa-save"></i>&nbsp;&nbsp;<?php echo getLang("save") ?></button>
						</div>
					</form>
				</div>
			</div> 
		</div>
	  </div>
	</div>
</div>    
 
<div class="modal fade" id="modal_delete" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"><i class="fa fa-trash"></i>&nbsp;&nbsp;<?php echo getLang("delete").' '. getCurrentMenuName() ?></h4></strong>
			</div>  
			<div class="modal-body">
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-success" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
			    <button type="button" class="btn btn-danger" onclick="deletes()"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;<?php echo getLang("ok") ?></button>
			</div> 
		</div>
	</div>
</div>

<?php $this->load->view('theme_footer'); ?>

<script type="text/javascript"> 
var save_method; 
var table;
var form = $('#form');
form.validate({       
	ignore: "", 
	onkeyup: false
});  

$(document).ready(function(){
	
	$('.nailthumb-container').nailthumb({width:100,height:100});
	// untuk fix-in bug coloumn width di tabs
	$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
         .columns.adjust();
	}); 
	table = $('#table-member').DataTable({     
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],  
		"pageLength": 25,
		"processing": true,  
		"serverSide": true,  
		"destroy": true,  
		"order": [],  
		"ajax": {
			"url": "<?php echo site_url('index.php/blacklist/ajax_data')?>",
			"type": "POST"
		}, 
		"columnDefs": [
			{ 
				"targets": [ 0,-1 ], 
				"orderable": false,  
			},
		]
	});
	
	$('#room .dataTables_filter input').unbind().bind('keyup', function(e) {
	   if(e.keyCode == 13) {
		$("#table-member").dataTable().fnFilter(this.value);
		}
	});
	
	$('.datepicker').datepicker({
		dateFormat: 'dd-mm-yy'
	});
	
	$('#member').tokenInput("index.php/booking/member", {
		minChars: 3,
		//tokenLimit: 1,
		preventDuplicates: true,
		onDelete: function (item) { 	
		},
		onAdd: function (item) {
			
		}, theme: "facebook"
	}); 
});    
 

function del() { 
	if (form.valid()) {
		$('#modal_delete').modal({keyboard: false, backdrop: 'static'}); 
		$('#modal_delete .modal-body').html('<?php echo getLang("are_you_sure_want_to_submit_data")?> ?');
	}		
}  

function deletes() { 
	$.ajax({
		url : 'index.php/reset/update',
		type: "POST",
		data: form.serialize(),
		dataType: "JSON",
		beforeSend : function(){ 
			showLoading();
		},
		complete : function(){
			hideLoading();
		}, 
		success: function(data)
		{
			$('#modal_delete').modal('hide'); 
			reset();
			info_alert('success','<?php echo getLang("success")?>');
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			info_alert('warning','<?php echo getLang("error_xhr")?>');
		}
	});
}  

function reset() {
   $('.token-input-dropdown-facebook').remove();    
	$('.token-input-list-facebook').remove();
	form[0].reset();
	
	
	$('#member').tokenInput("index.php/booking/member", {
		minChars: 3,
		//tokenLimit: 1,
		preventDuplicates: true,
		onDelete: function (item) { 	
		},
		onAdd: function (item) {
			
		}, theme: "facebook"
	}); 
}
 

</script>