 <div class="row">
	<div class="col-lg-12 portlets">
	  <div class="panel">
		<div class="panel-header bg-primary">
			<h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong> </h3>
		</div>
		<div class="panel-content pagination2">
		    <div class="filter-left">
				<form id="form_table">
					<table id="table" class="table table-striped table-bordered">
						  <thead>
							<tr>
								<th width="5%" class="text-center"><?php echo getLang("no") ?></th>
								<th width="13%"><?php echo getLang("display_order") ?></th>
								<th width="35%"><?php echo getLang("name") ?></th>
								<th width="40%"><?php echo getLang("parent_page") ?></th>
								<th width="7%" class="text-center"><?php echo getLang("action") ?></th>
							</tr>
						  </thead>
						  <tbody>
						  </tbody>
						  <tfoot>
							<tr>
								<td colspan="5"><div class="text-center"><button type="button" class="btn btn-success" onclick="save()"><i class="fa fa-save"></i><?php echo getLang("save") ?></button></div></td>
							</tr>
						  </tfoot>
					</table>
				</form>
		    </div>
		</div>
	  </div>
	</div>
</div>

<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-hidden="true" >
	<div class="modal-dialog modal-lg">
		<div class="modal-content" >
			<div class="modal-header bg-primary">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"></h4></strong>
			</div>
			<form id="form" class="form-horizontal">
			<input type="hidden" name="id" id="id">
			<div class="modal-body">
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo getLang("name") ?></label>
					<div class="col-sm-9 prepend-icon">
						<input type="text" name="inp[menu_name]" id="menu_name" class="form-control" placeholder="<?php echo getLang("name") ?>" minlength="3" required>
						<i class="fa fa-file-o"></i>
					</div> 
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo getLang("name_eng") ?></label>
					<div class="col-sm-9 prepend-icon">
						<input type="text" name="inp[menu_name_eng]" id="menu_name_eng" class="form-control" placeholder="<?php echo getLang("name_eng") ?>" minlength="3" required>
						<i class="fa fa-file-o"></i>
					</div> 
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo getLang("name_ina") ?></label>
					<div class="col-sm-9 prepend-icon">
						<input type="text" name="inp[menu_name_ina]" id="menu_name_ina" class="form-control" placeholder="<?php echo getLang("name_ina") ?>" minlength="3" required>
						<i class="fa fa-file-o"></i>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo getLang("icon") ?></label>
					<div class="col-sm-9 prepend-icon">
						<input type="text" name="inp[menu_icon]" id="menu_icon" class="form-control" placeholder="<?php echo getLang("icon") ?>">
						<i class="fa fa-file-o"></i> 
						<a href="http://fontawesome.io/icons/" target="_blank"><?php echo getLang("get_icon_code_here") ?></a>	 
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo getLang("url") ?></label>
					<div class="col-sm-9 prepend-icon">
						<input type="text" name="inp[menu_url]" id="menu_url" class="form-control" placeholder="<?php echo getLang("url") ?>">
						<i class="fa fa-file-o"></i>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo getLang("display_menudisplayorder") ?></label>
					<div class="col-sm-9">
						<select name="inp[menu_display]" class="form-control"><option value="yes">yes</option><option value="no">no</option></select>
					</div>
				</div>
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
			    <button type="button" class="btn btn-success" onclick="save()"><i class="fa fa-save"></i>&nbsp;&nbsp;<?php echo getLang("save") ?></button>
			</div>
			</form>
		</div>
	</div>
</div>


<div class="modal fade" id="modal_delete" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"><i class="fa fa-trash"></i>&nbsp;&nbsp;<?php echo getLang("delete")?> <?php echo getCurrentMenuName() ?></h4></strong>
			</div>
			<form id="form" class="form-horizontal">
			<input type="hidden" name="id" id="id">
			<div class="modal-body">
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-success" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
			    <button type="button" class="btn btn-danger" onclick="deletes()"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;<?php echo getLang("ok") ?></button>
			</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-primary">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"><i class="fa fa-plus-square"></i>&nbsp;&nbsp;<?php echo getLang("menu_mapping")?></h4></strong>
			</div>
			<form id="form" class="form-horizontal">
			<input type="hidden" name="id" id="id">
			<div class="modal-body">
				<table class="table table-striped table-bordered">
					    <tbody>
							<tr>
								<td width="20%"><?php echo getLang("menu") ?></td>
								<td width="4%">:</td>
								<td width="76%" id="menu"></td>
							</tr>
					    </tbody>
				</table>
				<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="8%"><?php echo getLang("no") ?></th>
								<th width="85%"><?php echo getLang("usergroup") ?></th>
								<th width="7%"><input type="checkbox" name="all" class="all" value=""></th>
							</tr>
						</thead>
					    <tbody>
							
							<?php if ($usergroup){ $i = 0;
								foreach ($usergroup as $row){
									echo '<tr>
										<td>'.$i++.'</td>
										<td>'.$row->ug_name.'</td>
										<td><input type="checkbox" name="inp[um_ug_id][]" id="um_ug_id'.$row->ug_id.'" value="'.$row->ug_id.'"></td>
									</tr>';
								}
							?>
							<?php } ?>
					    </tbody>
				</table>
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
			    <button type="button" class="btn btn-success" onclick="detail_save()"><i class="fa fa-save"></i>&nbsp;&nbsp;<?php echo getLang("save") ?></button>
			</div>
			</form>
		</div>
	</div>
</div>
   
         

<?php $this->load->view('backend/theme_footer'); ?>

<script type="text/javascript">

var save_method; 
var table;
var form = $('#modal_form #form');
form.validate();

$(document).ready(function() {
    table = $('#table').DataTable({        
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],  
		"pageLength": 25,
        "processing": true,  
        "serverSide": true,  
		"destroy": true,  
        "order": [],   
        "ajax": {
            "url": 'backend/setting/ajax_menudisplayorder',
            "type": "POST"
        }, 
        "columnDefs": [
			{ 
				"targets": [ 0,-1 ], 
				"orderable": false,  
			},
        ],
    }); 
	
   $('.dataTables_filter input').unbind().bind('keyup', function(e) {
       if(e.keyCode == 13) {
		$("#table").dataTable().fnFilter(this.value);
		}
	}); 
	
   $('#modal_detail .all').click(function(){
      $('tbody input[type="checkbox"]', $(this).parents('table')).prop('checked', this.checked);
   });
});

function add() {
    save_method = 'add';
    reset(); 
    $('#modal_form').modal({keyboard: false, backdrop: 'static'});
    $('#modal_form .modal-title').html('<i class="fa fa-plus-square"></i>&nbsp;&nbsp;<?php echo getLang("add")?> <?php echo getCurrentMenuName() ?>');
}

function edit(id) {
    save_method = 'update';
    reset();
    $.ajax({
        url : 'backend/setting/edit_menudisplayorder',
        type: "POST",
		data: {
				id :id
			},
        dataType: "JSON",
		beforeSend : function(){ 
			showLoading();
		},
		complete : function(){
			hideLoading();
		}, 
        success: function(data)
        {
            $.each(data, function(key, value) {
				$('#modal_form #'+key).val(value);
			});
			$('#modal_form #id').val(id);
            $('#modal_form').modal({keyboard: false, backdrop: 'static'}); 
            $('.modal-title').html('<i class="fa fa-edit"></i>&nbsp;&nbsp;<?php echo getLang("edit")?> <?php echo getCurrentMenuName() ?>');

        },
		error: function (jqXHR, textStatus, errorThrown)
		{
			info_alert('warning','<?php echo getLang("error_xhr")?>');
		}
    });
}

function save() { 
	$.ajax({
		url : 'backend/setting/update_menudisplayorder',
		type: "POST",
		data: $("#form_table").serialize(),
		dataType: "JSON",
		beforeSend : function(){ 
			showLoading();
		},
		complete : function(){
			hideLoading();
		}, 
		success: function(data)
		{
			if(data.status)
			{
				reload();
				dynamic_menu('<?php echo getCurrentUrl() ?>');
			}
			else {
				info_alert('warning','<?php echo getLang("parent_menu_that_want_to_be_sub_menu,_still_has_sub_menu")?>');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			info_alert('warning','<?php echo getLang("error_xhr")?>');
		}
	}); 
} 

function detail(id) {
	reset(); 
	$('#modal_detail').modal({keyboard: false, backdrop: 'static'}); 
	$('#modal_detail #id').val(id);
	$.ajax({
		url : 'backend/setting/get_usergroup_menu',
		type: "POST",
		data: {
			id :id
		},
		dataType: "JSON",
		beforeSend : function(){ 
			showLoading();
		},
		complete : function(){
			hideLoading();
		}, 
		success: function(data)
		{
			$("#modal_detail #menu").html(data[0].menu_name_ina);
			$.each(data[1], function(key, value) {
				$.each(data[1][key], function(keys, values) {
					if (keys=='um_ug_id') $('#modal_detail #um_ug_id'+values).prop('checked', "true");
				});
			});
            
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			info_alert('warning','<?php echo getLang("error_xhr")?>');
		}
	});
} 

function detail_save() { 
	var total=$('#modal_detail input[name="inp[um_ug_id][]"]:checked').length;
	if (total==0) info_alert('warning','<?php echo getLang("min_selected_one_user_group")?>');
	else {
		$.ajax({
			url : 'backend/setting/set_usergroup_menu',
			type: "POST",
			data: $("#modal_detail #form").serialize(),
			dataType: "JSON",
			beforeSend : function(){ 
				showLoading();
			},
			complete : function(){
				hideLoading();
			}, 
			success: function(data)
			{
				if(data.status)
				{
					reload();
					$('#modal_detail').modal("hide");
					info_alert('success','<?php echo getLang("success")?>'); 
					dynamic_menu('<?php echo getCurrentUrl() ?>');
				}
				
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				info_alert('warning','<?php echo getLang("error_xhr")?>');
			}
		});
	}
} 

function reset() {
	$('#modal_detail input[type="checkbox"]').prop('checked', false);
    form.validate().resetForm();     
    form[0].reset();  			
	$("label.error").hide();
 	$(".error").removeClass("error");
}

function reload() {
   table.draw();
}

</script>
</body>
</html>