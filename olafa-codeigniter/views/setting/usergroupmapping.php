 <div class="row">
	<div class="col-lg-12 portlets">
	  <div class="panel">
		<div class="panel-header bg-primary">
			<h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong> </h3>
		</div>
		<div class="panel-content pagination2"> 
			<div class="row content_button">
				<div class="col-lg-12">
					<button type="button" class="btn btn-primary"  onclick="back()"><i class="fa fa-arrow-left"></i></a><?php echo getLang("back")." ".getLang("to")." ".getLang("usergroup")?></button>
				</div> 
			</div>
			<div class="filter-left"> 
				<table class="table table-striped"> 
					  <tbody>
						<tr>
							<td width="20%"><?php echo getLang("id") ?></th>
							<td width="3%">:</th>
							<td width="77%"><?php echo $usergroup->ug_id ?></th>
						</tr>
						<tr>
							<td><?php echo getLang("usergroup") ?></th>
							<td>:</th>
							<td><?php echo $usergroup->ug_name ?></th>
						</tr>
						<tr>
							<td><?php echo getLang("description") ?></th>
							<td>:</th>
							<td><?php echo $usergroup->ug_desc ?></th>
						</tr>
					  </tbody>
				</table>
			</div>
		</div>
	  </div>
	</div>
</div>
 
 <div class="row">
	<div class="col-lg-5 portlets">
	  <div class="panel">
		<div class="panel-header bg-primary">
			<h3> <strong><?php echo getLang("all_user_list"); ?></strong> </h3>
		</div>
		<div class="panel-content pagination2">
		    <div class="filter-left"> 
				<form id="form_all_user_list">
					<input type="hidden" name="id" value="<?php echo $usergroup->ug_id ?>">
					<table id="all_user_list" class="table table-striped table-bordered">
						  <thead>
							<tr>
								<th width="10%"><input type="checkbox" name="all" class="form-control all"></th>
								<th width="45%"><?php echo getLang("username") ?></th>
								<th width="45%"><?php echo getLang("name") ?></th>
							</tr>
						  </thead>
						  <tbody>
						  </tbody>
					</table>
				</form>
		    </div>
		</div>
	  </div>
	</div>
	<div class="col-lg-2 portlets"> 
				<div class="text-center">
					<button type="button" class="btn btn-primary" id="left" onclick="left()" title="<?php echo getLang("delete_registered_user") ?>"><i class="fa fa-arrow-left"></i></a></button><br>
					<button type="button" class="btn btn-primary" id="right"  onclick="right()" title="<?php echo getLang("register_user") ?>"><i class="fa fa-arrow-right"></i></a></button>
				</div>
	</div>
	<div class="col-lg-5 portlets">
	  <div class="panel">
		<div class="panel-header bg-primary">
			<h3> <strong><?php echo getLang("registered_user_list"); ?></strong> </h3>
		</div>
		<div class="panel-content pagination2">
		    <div class="filter-left">
				<form id="form_registered_user_list">
					<input type="hidden" name="id" value="<?php echo $usergroup->ug_id ?>">
					<table id="registered_user_list" class="table table-striped table-bordered">
						  <thead>
							<tr>
								<th width="10%"><input type="checkbox" name="all" class="form-control all"></th>
								<th width="45%"><?php echo getLang("username") ?></th>
								<th width="45%"><?php echo getLang("name") ?></th>
							</tr>
						  </thead>
						  <tbody>
						  </tbody>
					</table>
				</form>
		    </div>
		</div>
	  </div>
	</div>
</div>

<?php $this->load->view('backend/theme_footer'); ?>

<script type="text/javascript">

var save_method; 
var all_user_list;
var registered_user_list;
var form = $('#modal_form #form');
form.validate();

$(document).ready(function() {
    all_user_list = $('#all_user_list').DataTable({         
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],  
		"pageLength": 10,
        "processing": true,  
        "serverSide": true,  
        "order": [],   
        "ajax": {
            "url"	: 'backend/setting/ajax_ugmapping_all_user_list',
            "type"	: 'POST',
			"data"	: 	{
							id : '<?php echo $usergroup->ug_id ?>'
						}
        }, 
        "columnDefs": [
			{ 
				"targets": [0], 
				"orderable": false,  
			},
        ],
    }); 
	
    registered_user_list = $('#registered_user_list').DataTable({         
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],  
		"pageLength": 10,
        "processing": true,  
        "serverSide": true,  
        "order": [],   
        "ajax": {
            "url"	: 'backend/setting/ajax_ugmapping_registered_user_list',
            "type"	: 'POST',
			"data"	: 	{
							id : '<?php echo $usergroup->ug_id ?>'
						}
        }, 
        "columnDefs": [
			{ 
				"targets": [ 0], 
				"orderable": false,  
			},
        ],
    }); 
	
   $('.all').click(function(){
      $('tbody input[type="checkbox"]', $(this).parents('table')).prop('checked', this.checked);
   });
}); 

function right() {  
	var total=$('#all_user_list tbody input[name="inp[id][]"]:checked').length;
	if (total==0) info_alert('warning','<?php echo getLang("min_selected_one_user")?>');
	else {
		$.ajax({
			url : 'backend/setting/register_user',
			type: "POST",
			data: $("#form_all_user_list").serialize(),
			dataType: "JSON",
			beforeSend : function(){ 
				showLoading();
			},
			complete : function(){
				hideLoading();
			}, 
			success: function(data)
			{ 
				reload();
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				info_alert('warning','<?php echo getLang("error_xhr")?>');
			}
		});
	}
} 

function left() { 
	var total=$('#registered_user_list tbody input[name="inp[id][]"]:checked').length;
	
	if (total==0) info_alert('warning','<?php echo getLang("min_selected_one_user")?>');
	else {
		$.ajax({
			url : 'backend/setting/delete_registered_user',
			type: "POST",
			data: $("#form_registered_user_list").serialize(),
			dataType: "JSON",
			beforeSend : function(){ 
				showLoading();
			},
			complete : function(){
				hideLoading();
			}, 
			success: function(data)
			{ 
				reload();
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				info_alert('warning','<?php echo getLang("error_xhr")?>');
			}
		});
	}
	
}  

function back() {
   window.location.href='backend/setting/usergroup';
}   

function reload() {
   all_user_list.draw();
   registered_user_list.draw();
} 

</script>
</body>
</html>