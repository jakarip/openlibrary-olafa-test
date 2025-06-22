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
				<div class="row content_button">
					<div class="col-lg-6" >
						<a href="javascript:;" onclick="add()" class="btn btn-danger">
							 <i class="fa fa-plus-square"></i><?php echo getLang("add").' '. getCurrentMenuName() ?>
						</a>
					</div> 
				</div>
				<div class="filter-left" id="room"> 
					<table id="table-member" class="table table-bordered table-hover">
						<thead>
							<tr>
								<th class="text-center" width="5%"><?php echo getLang('no'); ?></th>
								<th width="20%"><?php echo getLang('username'); ?></th>
								<th width="25%"><?php echo getLang('name'); ?></th>
								<th width="10%"><?php echo getLang('date'); ?></th>
								<th width="30%"><?php echo getLang('reason'); ?></th>
								<th class="text-center" width="10%"><?php echo getLang('action'); ?></th>
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
</div>    

<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-red">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
                <strong><h4 class="modal-title"></h4></strong>
            </div>
            <form id="form" class="form-horizontal form-validation">
            <input type="hidden" name="id" id="id">
            <div class="modal-body">
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo getLang('member_name'); ?> <span class="required-class">*) </span></label>
					 <div class="col-sm-9">
						<input class="form-control member" type="text" name="member" id="member" placeholder="<?php echo getLang('member_name'); ?>" required>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo getLang('reason'); ?> <span class="required-class">*) </span></label>
					 <div class="col-sm-9">
						<input class="form-control" type="text" name="reason" id="reason" placeholder="<?php echo getLang('reason'); ?>" required>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo getLang('blaclist_date_until'); ?> <span class="required-class">*) </span></label>
					 <div class="col-sm-9">
							<input type="text" class="input-sm form-control datepicker" class="datepicker" data-date-format="dd-mm-yyyy" name="dates" placeholder="<?php echo getLang('date') ?>" value="<?php echo $final ?>" required/>
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
			    <strong><h4 class="modal-title"><i class="fa fa-trash"></i>&nbsp;&nbsp;<?php echo getLang("delete").' '. getCurrentMenuName() ?></h4></strong>
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

<?php $this->load->view('theme_footer'); ?>

<script type="text/javascript"> 
var save_method; 
var table;
var form = $('#modal_form #form');
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
});  
 

function add() {
	save_method = 'add';
	reset();  
	$('#modal_form').modal({keyboard: false, backdrop: 'static'});
	$('#modal_form .modal-title').html('<i class="fa fa-plus-square"></i>&nbsp;&nbsp;<?php echo getLang("add").' '. getCurrentMenuName() ?>'); 
	
	$('#modal_form #member').tokenInput("index.php/booking/member", {
		minChars: 3,
		//tokenLimit: 1,
		preventDuplicates: true,
		onDelete: function (item) { 	
		},
		onAdd: function (item) {
			
		}, theme: "facebook"
	}); 
	  
} 
 

function save(){
	var url;
	if(save_method == 'add'){
		url = "<?php echo site_url('index.php/blacklist/insert')?>";
	}else{
		url = "<?php echo site_url('index.php/blacklist/update')?>";
	}

	if (form.valid()) {
		$.ajax({
			url : url,
			type: "POST",
			data: form.serialize(),
			dataType: "JSON",
			beforeSend : function() {
				showLoading();
			},
			complete : function() {
				hideLoading();
			},
			success: function(data){
				if (data.status){
					reload(); 
					$('#modal_form').modal('hide');  
				}
				else info_alert('warning','<?php echo getLang("your_room_id_already_taken_or_use")?>');
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				info_alert('warning','<?php echo getLang("error_xhr")?>');
			}
		});
	}
}   
 

function del(id) { 
	$('#modal_delete').modal({keyboard: false, backdrop: 'static'}); 
    $('#modal_delete .modal-body').html('<?php echo getLang("are_you_sure_want_to_delete_data")?> <strong>'+id+'</strong> ?');
	$('#modal_delete #id').val(id);
} 

function reload() {
   table.draw();
}

function deletes() { 
	$.ajax({
		url : 'index.php/blacklist/deletes',
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

function reset() {
   $('#modal_form .token-input-dropdown-facebook').remove();    
	$('#modal_form .token-input-list-facebook').remove();
	form[0].reset();
}
 

</script>