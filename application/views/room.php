 
<div class="row">
	<div class="col-lg-12 portlets">
	  <div class="panel"> 
		<div class="panel-content">
			<div class="nav-tabs2">
			  <ul class="nav nav-tabs nav-primary">
				<li class="active"><a aria-expanded="true" href="#tab_1" data-toggle="tab" class=""><i class="fa fa-building-o"></i> <?php echo getLang('room') ?></a></li>
				<li class=""><a aria-expanded="false" href="#tab_2" data-toggle="tab"><i class="fa fa-image"></i> <?php echo getLang('gallery') ?></a></li>
			  </ul>
			  <div class="tab-content">
				<div class="tab-pane fade active in" id="tab_1">  
					<div class="row"> 
						<div class="panel-content pagination2">
							<div class="row content_button">
								<div class="col-lg-6" >
									<a href="javascript:;" onclick="add()" class="btn btn-danger">
										 <i class="fa fa-plus-square"></i><?php echo getLang("add").' '. getCurrentMenuName() ?>
									</a>
								</div>
								<div class="text-right col-lg-6">
									 Baris dengan tanda warna <strong style="color:#fd9494">Merah</strong> menandakan data di nonaktifkan. Klik tombol warna <strong>Abu</strong> untuk mengaktifkan kembali.
								</div>
							</div>
							<div class="filter-left" id="room"> 
								<table id="table-member" class="table table-bordered table-hover">
									<thead>
										<tr>
											<th class="text-center" width="5%"><?php echo getLang('no'); ?></th>
											<th width="20%"><?php echo getLang('room_name'); ?></th>
											<th width="10%"><?php echo getLang('min_capacity'); ?></th>
											<th width="10%"><?php echo getLang('max_capacity'); ?></th>
											<th width="45%"><?php echo getLang('description'); ?></th>
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
									<button type="button" class="btn btn-danger"  onclick="add_image()"><i class="fa fa-plus-square"></i><?php echo getLang("add") ?> <?php echo getLang("gallery") ?></button>
								</div> 
							</div>
							<div class="filter-left" id="gallery"> 
								<table id="table_gallery" class="table table-striped table-bordered">
									  <thead>
										<tr>
											<th width="5%" class="text-center"><?php echo getLang("no") ?></th> 
											<th width="20%"><?php echo getLang("room_name") ?></th>
											<th width="60%"><?php echo getLang("gallery") ?></th>
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

                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo getLang('room_name'); ?> <span class="required-class">*) </span></label>
                    <div class="col-sm-9 prepend-icon">
                        <input class="form-control" type="text" name="inp[room_name]" id="room_name" placeholder="<?php echo getLang('room_name'); ?>" required>
                        <i class="fa fa-file-o"></i>
                    </div>
                </div>
				
				

                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo getLang('min_capacity'); ?> <span class="required-class">*) </span></label>
                     <div class="col-sm-9 prepend-icon">
                        <input class="form-control" type="text" name="inp[room_min]" id="room_min" placeholder="<?php echo getLang('min_capacity'); ?>" required>
                        <i class="fa fa-file-o"></i>
                    </div>
                </div>
				

                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo getLang('max_capacity'); ?> <span class="required-class">*) </span></label>
                     <div class="col-sm-9 prepend-icon">
                        <input class="form-control" type="text" name="inp[room_max]" id="room_max" placeholder="<?php echo getLang('max_capacity'); ?>" required>
                        <i class="fa fa-file-o"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo getLang('description'); ?> <span class="required-class">*) </span></label>
                    <div class="col-sm-9">
                        <textarea class="form-control" name="inp[room_description]" id="room_description" required></textarea>
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
			<input type="hidden" name="existing_image" id="existing_image">
			<div class="modal-body">
			
				<div class="form-group">
					<label class="col-sm-2 control-label"></label>
					<div class="col-sm-10 existing_image">
					</div> 
				</div> 	
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo getLang('room_name'); ?> <span class="required-class">*) </span></label>
                    <div class="col-sm-10">
						<select class="form-control" name="inp[rg_room_id]" id="rg_room_id" required>
							<option value=""><?php echo getLang('please_choose_room') ?></option>
							<?php
								foreach ($room as $row){
									echo '<option value="'.$row->room_id.'">'.$row->room_name.' ('.$row->room_capacity.' '.getLang('people').')</option>';
								}  
							?>
						</select>
                    </div>
                </div> 
				<div class="form-group">
					<label class="col-sm-2 control-label image_required"></label>
					<div class="col-sm-10"> 
						<input type="file" name="image" id="image_input" class="form-control" placeholder="<?php echo getLang("gallery") ?>" required>
					</div> 
				</div> 		 
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
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
			    <strong><h4 class="modal-title"><i class="fa fa-trash"></i>&nbsp;&nbsp;<?php echo getLang("delete")?> <?php echo getLang('product_image') ?></h4></strong>
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



<div class="modal fade" id="modal_detail_image" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"><i class="fa fa-list"></i>&nbsp;&nbsp;<?php echo getLang("detail")?> <?php echo getLang('gallery') ?></h4></strong>
			</div> 
			<div class="modal-body">
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-success" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("close") ?></button>
			</div> 
		</div>
	</div>
</div>
 
<div class="modal fade" id="modal_deactivate" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"><i class="fa fa-unlink"></i>&nbsp;&nbsp;<?php echo getLang("deactivate")?> <?php echo getCurrentMenuName() ?></h4></strong>
			</div>
			<form id="form" class="form-horizontal">
			<input type="hidden" name="id" id="id">
			<input type="hidden" name="inp[room_active]" id="room_active">
			<div class="modal-body">
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-success" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
			    <button type="button" class="btn btn-danger" onclick="deletes(1)"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;<?php echo getLang("ok") ?></button>
			</div>
			</form>
		</div>
	</div>
</div> 
 
<div class="modal fade" id="modal_activate" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"><i class="fa fa-link"></i>&nbsp;&nbsp;<?php echo getLang("activate")?> <?php echo getCurrentMenuName() ?></h4></strong>
			</div>
			<form id="form" class="form-horizontal">
			<input type="hidden" name="id" id="id">
			<input type="hidden" name="inp[room_active]" id="room_active">
			<div class="modal-body">
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
			    <button type="button" class="btn btn-success" onclick="deletes(0)"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;<?php echo getLang("ok") ?></button>
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
		'inp[room_min]' : 'number',
		'inp[room_max]' : 'number'  		
	}, 
	onkeyup: false
}); 


var form_image 	= $('#modal_form_image #form'); 
var table_gallery;

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
			"url": "<?php echo site_url('index.php/room/ajax_data')?>",
			"type": "POST"
		}, 
		"columnDefs": [
			{ 
				"targets": [ 0,-1 ], 
				"orderable": false,  
			},
		], 
		"fnRowCallback": function( nRow, aaData, iDisplayIndex ) { 
		if ( aaData[5] == "1" )
			{ 
				$(nRow).addClass('highlight_row_datatables');
				// $('td:eq(1)', nRow).addClass( 'testrow'); 
			}
		}
	});
	
	$('#room .dataTables_filter input').unbind().bind('keyup', function(e) {
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
			"url": "<?php echo site_url('index.php/room/ajax_image')?>",
			"type": "POST"
		}, 
		"columnDefs": [
			{ 
				"targets": [ 0,-1 ], 
				"orderable": false,  
			},
		], 
		"fnRowCallback": function( nRow, aaData, iDisplayIndex ) { 
			if ( aaData[5] == "1" )
			{ 
				$(nRow).addClass('highlight_row_datatables');
				// $('td:eq(1)', nRow).addClass( 'testrow'); 
			}
			
			$('.nailthumb-container').nailthumb({width:100,height:100});
		},
		"initComplete": function( settings, json ) {
			
			$('.nailthumb-container').nailthumb({width:100,height:100});
		},
		"fnDrawCallback": function( settings, json ) {
			
			$('.nailthumb-container').nailthumb({width:100,height:100});
		}
	});
	
	$('#gallery .dataTables_filter input').unbind().bind('keyup', function(e) {
	   if(e.keyCode == 13) {
		$("#table-gallery").dataTable().fnFilter(this.value);
		}
	}); 
});  
 

function add() {
	save_method = 'add';
	reset(); 
	$('#room_id').removeAttr('disabled'); 
	$('#modal_form #member_type').select2("enable",true);		
	$('#modal_form').modal({keyboard: false, backdrop: 'static'});
	$('#modal_form .modal-title').html('<i class="fa fa-plus-square"></i>&nbsp;&nbsp;<?php echo getLang("add").' '. getCurrentMenuName() ?>'); 
} 

function edit(id){
  save_method = 'update';
  reset();

	  $.ajax({
		url : "<?php echo site_url('index.php/room/edit')?>",
		type: "POST",
		data : {
			id : id
		},
		dataType: "JSON",
		success: function(data){
			$('#modal_form #id').val(data.room_id);  
			 $.each(data, function(key, value) {
				$('#modal_form #'+key).val(value);
			}); 
			
			$('#modal_form #room_description').val(data.room_description);  
			
			$('#room_id').attr('disabled', 'disabled'); 	
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
		url = "<?php echo site_url('index.php/room/insert')?>";
	}else{
		url = "<?php echo site_url('index.php/room/update')?>";
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

function del(status,id,data) { 
	
	if (status=='1')  { 
		$('#modal_deactivate').modal({keyboard: false, backdrop: 'static'}); 
		$('#modal_deactivate #room_active').val(status); 
		$('#modal_deactivate #id').val(id); 
		$('#modal_deactivate .modal-body').html('<?php echo getLang("are_you_sure_want_to_deactivate_data")?> <strong>'+data+'</strong> ?'); 
	}
	else  {  
		$('#modal_activate').modal({keyboard: false, backdrop: 'static'}); 
		$('#modal_activate #room_active').val(status); 
		$('#modal_activate #id').val(id); 
		$('#modal_activate .modal-body').html('<?php echo getLang("are_you_sure_want_to_activate_data")?> <strong>'+data+'</strong> ?'); 
	} 
} 

function deletes(status) { 
	if(status==1) var form = $("#modal_deactivate #form");
	else var form = $("#modal_activate #form");

	$.ajax({
		url : 'index.php/room/deletes',
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
			reload(); 
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			info_alert('warning','<?php echo getLang("error_xhr")?>');
		}
	});
}  

function reset() {
    form.validate().resetForm();   
	$("#modal_form #form :input").removeData("previousValue");	//remove remote jquery validate previous value  
    form[0].reset();            
	$("#modal_form #member_type").select2("val", "");
	$("#modal_form #member_bank_id").select2("val", "");
    $("label.error").hide();
    $(".error").removeClass("error");
} 

function reload() {
   table.draw();
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

function edit_image(id) {
    save_method = 'update';
	
	$('.image_required').html('<?php echo getLang("image") ?>');
	 $('#image_input').removeAttr('required');
    reset_image(); 
    $('.modal-title').html('<i class="fa fa-edit"></i>&nbsp;&nbsp;<?php echo getLang("edit")?> <?php echo getLang('gallery') ?>');
	
    $.ajax({
        url : 'masterdata/product/edit_image',
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
				$('#modal_form_image #'+key).val(value);
			});  
			
			$('#modal_form_image #existing_image').val(data.rg_image);
			$('.existing_image').html('<div class="nailthumb-container"><img src="tools/images/'+data.rg_image+'"  width="150px"> </img></div>');
			$('.nailthumb-container').nailthumb({width:150,height:150});
			$('#modal_form_image').modal({keyboard: false, backdrop: 'static'}); 
			$('#modal_form_image #id').val(id);

        },
		error: function (jqXHR, textStatus, errorThrown)
		{
			info_alert('warning','<?php echo getLang("error_xhr")?>');
		}
    });
} 

function save_image() { 
    var url;
    url = 'index.php/room/insert_image';

    if (form_image.valid()) {
		var formData = new FormData($('#modal_form_image #form')[0]);
		$.ajax({
			url : url,
			type: "POST",
			data: formData,
			contentType: false,//untuk upload image
			processData: false,//untuk upload image
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
					reload_image(); 
					$('#modal_form_image').modal('hide');
				}
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
		url : 'index.php/room/delete_image',
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
    $('#modal_detail_image .modal-body').html('<div class="nailthumb-container"><img src="tools/images/'+image+'"  width="100%"</img></div>');
}

function reset_image() {
    form_image.validate().resetForm();     
    form_image[0].reset();   
	$("#modal_form_image #rg_room_id").select2("val","");
	$("#modal_form_image #form :input").removeData("previousValue");	//remove remote jquery validate previous value
	$("label.error").hide();
 	$(".error").removeClass("error");
}

function reload_image() {
   table_gallery.draw();
}

</script>