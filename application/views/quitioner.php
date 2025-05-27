 
<div class="row">
	<div class="col-lg-12 portlets">
	  <div class="panel"> 
		<div class="panel-content"> 
			<div class="panel-header bg-red">
			<h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong> </h3>
            </div>
			<div class="panel-content pagination2">
				<div class="row">   
					<form id="form_quit" class="form form-validation">
						<input type="hidden" name="id" id="id">
						<div class="modal-body">
							<?php 
							$cat = "";
							foreach ($quitioner as $key => $row){  
								if($cat!=$row->quitioner_category){ 
							?>
									<div class="col-sm-12">
										<br>
										<br>
										<div class="form-group">
											<button class="col-sm-12 btn btn-primary"><?php echo $row->quitioner_category ?></button>
										</div> 
									</div> 
								<?php
								}
								
								if($key==count($quitioner)-1){ 
								?>
									<div class="col-sm-12">
										<div class="form-group">
											<label class="control-label"><h5><strong><?php echo $row->quitioner_name ?></strong></h5></label>
											<div class="append-icon"> 
												<textarea class="form-control" rows="5" name="quitioner[<?php echo $row->quitioner_id ?>]" id="room_description"></textarea>
											</div>
										</div> 
									</div>
								<?php
								}
								else {
								?> 
									<div class="col-sm-12">
										<div class="form-group">
											<label class="control-label"><h5><strong><?php echo $row->quitioner_name ?>&nbsp;&nbsp;? &nbsp; &nbsp;<span class="required-class">*) </span></strong></h5></label>
											<div class="append-icon"> 
												<div class="radio"><label><input type="radio" class="radio" name="quitioner[<?php echo $row->quitioner_id ?>]" value="Sangat Setuju" required>Sangat Setuju</label></div>
												<div class="radio"><label><input type="radio" class="radio" name="quitioner[<?php echo $row->quitioner_id ?>]" value="Setuju" required>Setuju</label></div>
												<div class="radio"><label><input type="radio" class="radio" name="quitioner[<?php echo $row->quitioner_id ?>]" value="Tidak Setuju" required>Tidak Setuju</label></div>
												<div class="radio"><label><input type="radio" class="radio" name="quitioner[<?php echo $row->quitioner_id ?>]" value="Sangat Tidak Setuju" required>Sangat Tidak Setuju</label></div>
											</div>
										</div> 
									</div>
								<?php
								}
								$cat = $row->quitioner_category;
								?>
								
							<?php 
							} 
							?> 
							<div class="col-sm-12">
							<br>
							<br>
								<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
								<button type="button" class="btn btn-success" onclick="add()"><i class="fa fa-save"></i>&nbsp;&nbsp;<?php echo getLang("save") ?></button>
							</div>
						</div>
					</form>
				</div>
			</div> 
		</div> 
	  </div>
	</div>
</div>    


<div class="modal fade" id="modal_deactivate" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"><i class="fa fa-unlink"></i>&nbsp;&nbsp;<?php echo getLang("save")?> <?php echo getCurrentMenuName() ?></h4></strong>
			</div>
			<form id="form" class="form-horizontal"> 
			<div class="modal-body">
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-success" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
			    <button type="button" class="btn btn-danger" onclick="save()"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;<?php echo getLang("ok") ?></button>
			</div>
			</form>
		</div>
	</div>
</div> 
  
<?php $this->load->view('theme_footer'); ?>

<script type="text/javascript"> 
var save_method; 
var table;
var form = $('#form_quit');
form.validate({
	errorPlacement: function(error, element) 
	{
		error.appendTo( element.parents('.append-icon').prev() );
	 }
	
});  
$(document).ready(function(){
	
	 
});  
 
 

function add() { 
	if (form.valid()) { 
		$('#modal_deactivate').modal({keyboard: false, backdrop: 'static'});  
		$('#modal_deactivate .modal-body').html('<?php echo getLang("are_you_sure_want_to_submit_data")?> ?');
	}
} 

function save() {  
	$.ajax({
		url : 'index.php/questionnaire/insert',
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
			$('#modal_deactivate').modal('hide');
			$('#modal_activate').modal('hide');
			info_alert('success','<?php echo getLang("thank you for filling out the questionnaire")?>');
			window.location.href='index.php/login';
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			info_alert('warning','<?php echo getLang("error_xhr")?>');
		}
	});
}   
</script>