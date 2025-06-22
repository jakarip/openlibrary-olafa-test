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
					<button type="button" class="btn btn-success"  onclick="plain_pass()"><i class="fa fa-key"></i><?php echo getLang("view_password")?> <?php echo getCurrentMenuName() ?></button>
				</div> 
			</div> 
		    <div class="filter-left"> 
				<table id="table" class="table table-striped table-bordered">
					  <thead>
						<tr>
							<th width="5%" class="text-center"><?php echo getLang("no") ?></th>
							<th width="14%"><?php echo getLang("name") ?></th>
							<th width="14%"><?php echo getLang("username") ?></th>
							<th width="8%"><?php echo getLang("password") ?></th>
							<th width="14%"><?php echo getLang("email") ?></th>
							<th width="14%"><?php echo getLang("default_user_group") ?></th>
							<th width="14%"><?php echo getLang("user_group_list") ?></th>
							<th width="17%" class="text-center"><?php echo getLang("action") ?></th>
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
			<form id="form" method="post" action="login/loginproccess" class="form-horizontal">
			<input type="hidden" name="id" id="id">
			<div class="modal-body">
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo getLang("name") ?></label>
					<div class="col-sm-9 prepend-icon">
						<input type="text" name="inp[user_name]" id="user_name" class="form-control" placeholder="<?php echo getLang("name") ?>" minlength="3" required>
						<i class="fa fa-file-o"></i>
					</div> 
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo getLang("username") ?></label>
					<div class="col-sm-9 prepend-icon">
						<input type="text" name="inp[user_username]" id="user_username" class="form-control" placeholder="<?php echo getLang("username") ?>" minlength="3" required>
						<i class="fa fa-file-o"></i>
					</div> 
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo getLang("password") ?></label>
					<div class="col-sm-9 prepend-icon">
						<input type="password" name="inp[user_plain_pass]" id="user_plain_pass" class="form-control" placeholder="<?php echo getLang("password") ?>" minlength="3" >
						<i class="fa fa-file-o"></i>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo getLang("email") ?></label>
					<div class="col-sm-9 prepend-icon">
						<input type="email" name="inp[user_email]" id="user_email" class="form-control" placeholder="<?php echo getLang("email") ?>">
						<i class="fa fa-file-o"></i>  
					</div>
				</div>
				<div class="form-group" id="form_user_default_ug">
					<label class="col-sm-3 control-label"><?php echo getLang("default_user_group") ?></label>
					<div class="col-sm-9">
						<select name="inp[user_default_ug]" id="user_default_ug" class="form-control" required>
						</select> 
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

<div class="modal fade" id="modal_plain_pass" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-primary">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"><i class="fa fa-key"></i>&nbsp;&nbsp;<?php echo getLang("password")?> <?php echo getCurrentMenuName() ?></h4></strong>
			</div>
			<form id="form" class="form-horizontal">
			<div class="modal-body">
				<div class="form-group">
					<label class="col-sm-12 control-label"><?php echo getLang("before_view_users_password,please_insert_your_password_first") ?></label>
				</div>
				<div class="form-group">
					<div class="col-sm-12 prepend-icon">
						<input type="password" name="password" id="password" class="form-control" placeholder="<?php echo getLang("password") ?>" required>
						<i class="fa fa-file-o"></i>
					</div> 
				</div>
				<div class="form-group" id="notif_plain_pass">
				</div>
			</div>
			<div class="modal-footer"> 
			    <button type="button" class="btn btn-success" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
			    <button type="button" class="btn btn-danger" onclick="check_plain_pass()"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;<?php echo getLang("ok") ?></button>
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
			    <strong><h4 class="modal-title"><i class="fa fa-plus-square"></i>&nbsp;&nbsp;<?php echo getLang("user_mapping")?></h4></strong>
			</div>
			<form id="form" class="form-horizontal">
			<input type="hidden" name="id" id="id">
			<div class="modal-body">
				<table class="table table-striped table-bordered">
					    <tbody>
							<tr>
								<td width="20%"><?php echo getLang("user") ?></td>
								<td width="4%">:</td>
								<td width="76%" id="user"></td>
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
									$i++;
									echo '<tr>
										<td>'.$i.'</td>
										<td>'.$row->ug_name.'</td>
										<td><input type="checkbox" name="inp[uu_ug_id][]" id="uu_ug_id'.$row->ug_id.'" value="'.$row->ug_id.'"></td>
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
var form_plain_pass = $('#modal_plain_pass #form');

form_plain_pass.validate();
form.validate({   
	rules: {
		'inp[user_username]' : { 
            remote: {
                url :  'backend/setting/checkUsername',
                type: "post",
				beforeSend : function() {
					showLoading();
				},
				complete : function() {
					hideLoading();
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					info_alert('warning','<?php echo getLang("error_xhr")?>');
				}
            },
			loginRegex : true
		}           
	},
	messages:{
        'inp[user_username]':{
			remote: jQuery.validator.format("Your username already taken or use")
        }           
    },
	onkeyup: false
});

$(document).ready(function() {
    table = datatables('encrypt');
	
	$('.dataTables_filter input').unbind().bind('keyup', function(e) {
       if(e.keyCode == 13) {
		$("#table").dataTable().fnFilter(this.value);
		}
	});
	
   $('#modal_detail .all').click(function(){
      $('tbody input[type="checkbox"]', $(this).parents('table')).prop('checked', this.checked);
   });
});

function datatables(code) {
	return $('#table').DataTable({         
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],  
		"pageLength": 25,
        "processing": true,  
        "serverSide": true,  
		"destroy": true,  
        "order": [],   
        "ajax": {
            "url": 'backend/setting/ajax_user',
            "type": "POST",
			"data" : {
				code : code
			}
        }, 
        "columnDefs": [
			{ 
				"targets": [ 0,-1 ], 
				"orderable": false,  
			},
        ],
    }); 
}

function add() {
    save_method = 'add';
	$("#user_username").removeAttr("readonly");
	$("#form_user_default_ug").hide();
	$("#user_plain_pass").attr("required","true");
	$("#user_plain_pass").attr("name","inp[user_plain_pass]");
    reset(); 
    $('#modal_form').modal({keyboard: false, backdrop: 'static'});
    $('#modal_form .modal-title').html('<i class="fa fa-plus-square"></i>&nbsp;&nbsp;<?php echo getLang("add")?> <?php echo getCurrentMenuName() ?>');
}

function edit(id) {
	$("#user_username").attr("readonly","true");
	$("#user_plain_pass").removeAttr("required");
	$("#user_plain_pass").attr("name","password");
    save_method = 'update';
    reset();
    $.ajax({
        url : 'backend/setting/edit_user',
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
			if (data[1]!="") {
				$("#form_user_default_ug").show();
				$('#user_default_ug').html(data[1]); 
			}else $("#form_user_default_ug").hide();
			
            $.each(data[0], function(key, value) {
				if (key!='user_plain_pass') $('#modal_form #'+key).val(value);
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
    var url;
    if(save_method == 'add') {
        url = 'backend/setting/insert_user';
    } else {
        url = 'backend/setting/update_user';
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
		url : 'backend/setting/delete_user',
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
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			info_alert('warning','<?php echo getLang("error_xhr")?>');
		}
	});
} 


function plain_pass() {
	reset(); 
	$('#modal_plain_pass').modal({keyboard: false, backdrop: 'static'});  
} 

function check_plain_pass() {
	if (form_plain_pass.valid()) {
		$.ajax({
			url : 'backend/setting/check_plain_pass',
			type: "POST",
			data: $("#modal_plain_pass #form").serialize(),
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
					table = datatables('decrypt');
					$('#modal_plain_pass').modal('hide'); 
				}else {
					$('#modal_plain_pass #notif_plain_pass').html('<label class="col-sm-12 control-label" style="color:red"><?php echo getLang("your_password_is_not_valid") ?></label>');
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				info_alert('warning','<?php echo getLang("error_xhr")?>');
			}
		});
	 }
}

function detail(id) {
	reset(); 
	
	$.ajax({
		url : 'backend/setting/get_usergroup_user',
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
			$('#modal_detail').modal({keyboard: false, backdrop: 'static'});
			$('#modal_detail #id').val(id);
			$("#modal_detail #user").html(data[0].user_name);
			$.each(data[1], function(key, value) {
				$.each(data[1][key], function(keys, values) {
					if (keys=='uu_ug_id') $('#modal_detail #uu_ug_id'+values).prop('checked', "true");
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
	var total=$('#modal_detail input[name="inp[uu_ug_id][]"]:checked').length;
	if (total==0) info_alert('warning','<?php echo getLang("min_selected_one_user_group")?>');
	else {
		$.ajax({
			url : 'backend/setting/set_usergroup_user',
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
    form_plain_pass.validate().resetForm();  
	$("#modal_plain_pass #notif_plain_pass").html("");   
    form_plain_pass[0].reset();  	 
    form.validate().resetForm();     
	$("#modal_form #form :input").removeData("previousValue");	//remove remote jquery validate previous value
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