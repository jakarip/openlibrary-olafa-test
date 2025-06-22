 
<div class="row">
	<div class="col-lg-12 portlets">
	  <div class="panel"> 
		<div class="panel-content">
			<div class="nav-tabs2">
			  <ul class="nav nav-tabs nav-primary">
				<li class="active"><a aria-expanded="true" href="#tab_1" data-toggle="tab" class=""><?php echo getLang('rfid tidak ada di db') ?></a></li>
				<li class=""><a aria-expanded="false" href="#tab_2" data-toggle="tab"><?php echo getLang('rfid yang tidak ada nama anggota di db') ?></a></li>
			  </ul>
			  <div class="tab-content">
				<div class="tab-pane fade active in" id="tab_1">  
					<div class="row"> 
						<div class="panel-content pagination2">
							<div class="row content_button">
								<div class="col-lg-6" >
									<a href="javascript:;" onclick="add()" class="btn btn-danger">
										 <i class="fa fa-plus-square"></i><?php echo getLang("add") ?> <?php echo getLang("rfid") ?>
									</a>
								</div> 
							</div>
							<div class="filter-left" id="rfidreg"> 
								<table id="table-member" class="table table-bordered table-hover">
									<thead>
										<tr>
											<th class="text-center" width="5%"><?php echo getLang('no'); ?></th>
											<th width="20%"><?php echo getLang('username'); ?></th>
											<th width="35%"><?php echo getLang('fullname'); ?></th>
											<th width="30%"><?php echo getLang('rfid'); ?></th>
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
				<div class="tab-pane fade" id="tab_2"> 
					<div class="row"> 
						<div class="panel-content pagination2">
							<div class="row content_button">
								<div class="col-lg-6" > 
									<button type="button" class="btn btn-danger"  onclick="add_image()"><i class="fa fa-plus-square"></i><?php echo getLang("add") ?> <?php echo getLang("anggota yang tidak ada di db") ?></button>
								</div> 
							</div>
							<div class="filter-left" id="gallery"> 
								<table id="table_gallery" class="table table-striped table-bordered">
									  <thead>
										<tr>
											<th width="5%" class="text-center"><?php echo getLang("no") ?></th> 
											<th width="20%"><?php echo getLang("rfid") ?></th>
											<th width="60%"><?php echo getLang("description") ?></th>
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

                <div class="form-group" id="user_username">
                    <label class="col-sm-3 control-label"><?php echo getLang('username'); ?> <span class="required-class">*) </span></label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="inp[username]" id="username" placeholder="<?php echo getLang('username'); ?>" required> 
                    </div>
                </div> 

                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo getLang('rfid'); ?> <span class="required-class">*) </span></label>
                     <div class="col-sm-9 prepend-icon">
                        <input class="form-control" type="text" name="inp[rfid]" id="rfid" placeholder="<?php echo getLang('rfid'); ?>" required>
                        <i class="fa fa-file-o"></i>
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


<div class="modal fade" id="modal_form_image" tabindex="-1" role="dialog" aria-hidden="true" >
	<div class="modal-dialog modal-lg">
		<div class="modal-content" >
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"></h4></strong>
			</div>
			<form id="form" class="form-horizontal" enctype="multipart/form-data">
			<input type="hidden" name="id" id="id"> 
			<div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo getLang('rfid'); ?> <span class="required-class">*) </span></label>
                     <div class="col-sm-9 prepend-icon">
                        <input class="form-control" type="text" name="inp[rfid]" id="rfid" placeholder="<?php echo getLang('rfid'); ?>" required>
                        <i class="fa fa-file-o"></i>
                    </div>
                </div>  
				 <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo getLang('description'); ?> <span class="required-class">*) </span></label>
                    <div class="col-sm-9 prepend-icon">
                        <input class="form-control" type="text" name="inp[description]" id="description" placeholder="<?php echo getLang('description'); ?>" required>
                        <i class="fa fa-file-o"></i>
                    </div>
                </div> 

			</div>
			<div class="modal-footer">
			    <button type="button" onclick="reset()" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
			    <button type="button" class="btn btn-success" onclick="save_image()"><i class="fa fa-save"></i>&nbsp;&nbsp;<?php echo getLang("save") ?></button>
			</div>
			</form>
		</div>
	</div>
</div>   

<div class="modal fade" id="modal_delete_image" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"><i class="fa fa-trash"></i>&nbsp;&nbsp;<?php echo getLang("delete")?> <?php echo getLang('rfid') ?></h4></strong>
			</div>
			<form id="form" class="form-horizontal">
			<input type="hidden" name="id" id="id">
			<div class="modal-body">
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-success" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
			    <button type="button" class="btn btn-danger" onclick="deletes_image()"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;<?php echo getLang("ok") ?></button>
			</div>
			</form>
		</div>
	</div>
</div> 
  
<div class="modal fade" id="modal_delete_rfidreg" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"><i class="fa fa-trash"></i>&nbsp;&nbsp;<?php echo getLang("delete")?> <?php echo getLang('rfid') ?></h4></strong>
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
	rules: {
		'inp[rfidreg_capacity]' : 'number'        
	}, 
	onkeyup: false
}); 


var form_image 	= $('#modal_form_image #form'); 
var table_gallery;

$(document).ready(function(){  
	$('#modal_form #rfid').keyup(function() {
        this.value = this.value.toUpperCase();
    });
	
	$('#modal_form_image #rfid').keyup(function() {
        this.value = this.value.toUpperCase();
    });
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
			"url": "<?php echo site_url('index.php/rfidreg/ajax_data')?>",
			"type": "POST"
		}, 
		"columnDefs": [
			{ 
				"targets": [ 0,-1 ], 
				"orderable": false,  
			},
		]
	});
	
	$('#rfidreg .dataTables_filter input').unbind().bind('keyup', function(e) {
	   if(e.keyCode == 13) {
		$("#table-member").dataTable().fnFilter(this.value);
		}
	});
	
	
	table_gallery = $('#table_gallery').DataTable({     
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],  
		"pageLength": 25,
		"processing": true,  
		"serverSide": true,  
		"destroy": true,  
		"order": [],  
		"ajax": {
			"url": "<?php echo site_url('index.php/rfidreg/ajax_image')?>",
			"type": "POST"
		}, 
		"columnDefs": [
			{ 
				"targets": [ 0,-1 ], 
				"orderable": false,  
			},
		]  
	});
	
	$('#gallery .dataTables_filter input').unbind().bind('keyup', function(e) {
	   if(e.keyCode == 13) {
		$("#table_gallery").dataTable().fnFilter(this.value);
		}
	}); 
});  
 

function add() {
	reset(); 
	$('#modal_form #user_username').show();			
	$('#modal_form #username').tokenInput("index.php/rfidreg/auto_data", {
		minChars: 3,
		tokenLimit:1,
		preventDuplicates: true,
		onDelete: function (item) {  
		},
		hintText:"Search username / nama lengkap mahasiswa",
		onAdd: function (item) { 
		}
	}); 
	
	save_method = 'add';
	$('#rfidreg_id').removeAttr('disabled'); 
	$('#modal_form #member_type').select2("enable",true);		
	$('#modal_form').modal({keyboard: false, backdrop: 'static'});
	$('#modal_form .modal-title').html('<i class="fa fa-plus-square"></i>&nbsp;&nbsp;<?php echo getLang("add").' '. getCurrentMenuName() ?>'); 
} 

function edit(id){
  save_method = 'update';
  reset();

	  $.ajax({
		url : "<?php echo site_url('index.php/rfidreg/edit')?>",
		type: "POST",
		data : {
			id : id
		},
		dataType: "JSON",
		success: function(data){
			$('#modal_form #id').val(data.rfidreg_id);
			$('#modal_form #user_username').hide();			
			 $.each(data, function(key, value) {
				$('#modal_form #'+key).val(value);
			}); 
			
			$('#modal_form #rfidreg_description').val(data.rfidreg_description);  
			
			$('#rfidreg_id').attr('disabled', 'disabled'); 	
			$('#modal_form').modal({keyboard: false, backdrop: 'static'});
			$('#modal_form .modal-title').html('<i class="fa fa-pencil-square-o"></i>&nbsp;&nbsp;<?php echo getLang("edit").' '. getCurrentMenuName() ?>');
		},
		error: function (jqXHR, textStatus, errorThrown){
			alert('Error get data from ajax');
		}
	});
}

function save(){
	var url;
	if(save_method == 'add'){
		url = "<?php echo site_url('index.php/rfidreg/insert')?>";
	}else{
		url = "<?php echo site_url('index.php/rfidreg/update')?>";
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
				
				if (data.status!='False'){ 
					reload(); 
					$('#modal_form').modal('hide');  
				}
				else info_alert('warning','username atau rfid yang diinputkan sudah ada');
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				info_alert('warning','<?php echo getLang("error_xhr")?>');
			}
		});
	}
}   

function del(id,data) { 
	
	 
	$('#modal_delete_rfidreg').modal({keyboard: false, backdrop: 'static'}); 
	$('#modal_delete_rfidreg #id').val(id); 
	$('#modal_delete_rfidreg .modal-body').html('<?php echo getLang("are_you_sure_want_to_delete_data")?> <strong>'+data+'</strong> ?');  
} 

function deletes(status) { 
	var form = $("#modal_delete_rfidreg #form");

	$.ajax({
		url : 'index.php/rfidreg/deletes',
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
			$('#modal_delete_rfidreg').modal('hide'); 
			reload(); 
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			info_alert('warning','<?php echo getLang("error_xhr")?>');
		}
	});
}   

function reload() {
   table.draw();
} 
 
function reset() {
	$('#modal_form .token-input-dropdown').remove();    
	$('#modal_form .token-input-list').remove();
	form.validate().resetForm();   
	$("#modal_form #form :input").removeData("previousValue");	//remove remote jquery validate previous value  
	form[0].reset(); 
	$("label.error").hide();
 	$(".error").removeClass("error");
}

function add_image() {  
    save_method = 'add';
    reset_image(); 
	$('.image_required').html('<?php echo getLang("image") ?> <span class="required-class">*) </span>');
	$('#image_input').attr('required','true');
	$('.existing_image').html('');
    $('#modal_form_image').modal({keyboard: false, backdrop: 'static'});
    $('#modal_form_image .modal-title').html('<i class="fa fa-plus-square"></i>&nbsp;&nbsp;<?php echo getLang("add")?> <?php echo getLang('gallery') ?>');
}
 

function save_image() { 
    var url;
    url = 'index.php/rfidreg/insert_image';

    if (form_image.valid()) { 
		$.ajax({
			url : url,
			type: "POST",
			data: form_image.serialize(),
			// contentType: false,//untuk upload image
			// processData: false,//untuk upload image
			dataType: "JSON",
			beforeSend : function(){ 
				showLoading();
			},
			complete : function(){
				hideLoading();
			}, 
			success: function(data)
			{
				
				if (data.status!='False'){ 
					reload_image(); 
					$('#modal_form_image').modal('hide'); 
				}
				else info_alert('warning','rfid yang diinputkan sudah ada');
				
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				info_alert('warning','<?php echo getLang("error_xhr")?>');
			}
		}); 
	}
}

function del_image(id,data) { 
	$('#modal_delete_image').modal({keyboard: false, backdrop: 'static'}); 
    $('#modal_delete_image .modal-body').html('<?php echo getLang("are_you_sure_want_to_delete_data")?> <strong>'+data+'</strong> ?');
	$('#modal_delete_image #id').val(id);
} 

function deletes_image() { 
	$.ajax({
		url : 'index.php/rfidreg/delete_image',
		type: "POST",
		data: $("#modal_delete_image #form").serialize(),
		dataType: "JSON",
		beforeSend : function(){ 
			showLoading();
		},
		complete : function(){
			hideLoading();
		}, 
		success: function(data)
		{
			$('#modal_delete_image').modal('hide');
			reload_image(); 
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			info_alert('warning','<?php echo getLang("error_xhr")?>');
		}
	});
} 


function detail_image(image) {  
    $('#modal_detail_image').modal({keyboard: false, backdrop: 'static'});
}

function reset_image() {
    form_image.validate().resetForm();     
    form_image[0].reset();   
	$("#modal_form_image #rg_rfidreg_id").select2("val","");
	$("#modal_form_image #form :input").removeData("previousValue");	//remove remote jquery validate previous value
	$("label.error").hide();
 	$(".error").removeClass("error");
}



function reload_image() {
   table_gallery.draw();
}

</script>