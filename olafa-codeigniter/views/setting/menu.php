<div class="row">
	<div class="col-lg-12 portlets">
	  <div class="panel">
		<div class="panel-header bg-primary">
			<h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong> </h3>
		</div>
		<div class="panel-content pagination2">
			<div class="row content_button">
				<div class="col-lg-12">
					<button type="button" class="btn btn-primary"  onclick="add()"><i class="fa fa-plus-square"></i><?php echo getLang("add")?> <?php echo getCurrentMenuName() ?></button>
				</div> 
			</div>
		    <div class="filter-left"> 
				<table id="table" class="table table-striped table-bordered">
					  <thead>
						<tr>
							<th width="5%" class="text-center"><?php echo getLang("no") ?></th>
							<th width="13%"><?php echo getLang("name") ?></th>
							<th width="13%"><?php echo getLang("name_eng") ?></th>
							<th width="13%"><?php echo getLang("name_ina") ?></th>
							<th width="16%"><?php echo getLang("icon") ?></th>
							<th width="15%"><?php echo getLang("url") ?></th>
							<th width="10%"><?php echo getLang("display_menu") ?></th>
							<th width="15%" class="text-center"><?php echo getLang("action") ?></th>
						</tr>
					  </thead>
					  <tbody>
					  </tbody>
				</table>
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
					<label class="col-sm-3 control-label"><?php echo getLang("display_menu") ?></label>
					<div class="col-sm-9">
						<select name="inp[menu_display]" class="form-control" id="menu_display"><option value="yes">yes</option><option value="no">no</option></select>
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
            "url": 'backend/setting/ajax_menu',
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
        url : 'backend/setting/edit_menu',
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
			
			$("#modal_form #menu_display").select2("val", data.menu_display);
			
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
    var url;
    if(save_method == 'add') {
        url = 'backend/setting/insert_menu';
    } else {
        url = 'backend/setting/update_menu';
    }

    if (form.valid()) {
		$.ajax({
			url : url,
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
				if(data.status)
				{
					$('#modal_form').modal('hide');
					reload();
					if (save_method=='update') dynamic_menu('<?php echo getCurrentUrl() ?>');
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				info_alert('warning','<?php echo getLang("error_xhr")?>');
			}
		}); 
	}
}

function del(id,data) {
	
	$('#modal_delete').modal({keyboard: false, backdrop: 'static'}); 
    $('#modal_delete .modal-body').html('<?php echo getLang("are_you_sure_want_to_delete_data")?> <strong>'+data+'</strong> ?');
	$('#modal_delete #id').val(id);
} 

function deletes() { 
	$.ajax({
		url : 'backend/setting/delete_menu',
		type: "POST",
		data: $("#modal_delete #form").serialize(),
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
			reload();
			dynamic_menu('<?php echo getCurrentUrl() ?>');
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			info_alert('warning','<?php echo getLang("error_xhr")?>');
		}
	});
} 

function reset() {
    form.validate().resetForm();     
    form[0].reset();  			
	
	$("#modal_form #menu_display").select2("val", "yes");
	$("label.error").hide();
 	$(".error").removeClass("error");
}

function reload() {
   table.draw();
}

</script>
</body>
</html>