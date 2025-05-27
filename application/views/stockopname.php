<div class="row">
    <div class="col-lg-12">
        <div class="panel">
            <div class="panel-header bg-red">
			<h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong> </h3>
            </div>
            <div class="panel-content pagination2"> 
				<div class="row content_button">  
					<div class="col-lg-6" >
						<!-- <a href="javascript:;" onclick="add()" class="btn btn-danger">
								<i class="fa fa-plus-square"></i><?php echo getLang("add").' '. getCurrentMenuName() ?>
						</a> -->
					</div>
					<div class="text-right col-lg-6">
							Baris dengan tanda warna <strong style="color:#fd9494">Merah</strong> menandakan data di nonaktifkan. Klik tombol warna <strong>Abu</strong> untuk mengaktifkan kembali.
					</div>  
				</div>
				<a href="<?=base_url().'index.php/katalog'?>" target="_blank" class="btn btn-primary">
					<i class="fa fa-file-o"></i>Referensi koleksi untuk weeding
				</a>
                <table id="table-member" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%"><?php echo getLang('no'); ?></th>
                            <th width="9%"><?php echo getLang('date'); ?></th>
                            <th width="9%"><?php echo getLang('name'); ?></th> 
                            <th width="9%"><?php echo getLang('status'); ?></th> 
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

 
<div class="modal fade" id="modal_deactivate" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"><i class="fa fa-unlink"></i>&nbsp;&nbsp;<?php echo getLang("deactivate")?> <?php echo getCurrentMenuName() ?></h4></strong>
			</div>
			<form id="form" class="form-horizontal">
			<input type="hidden" name="id" id="id">
			<input type="hidden" name="inp[so_status]" id="so_status">
			<div class="modal-body">
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-success" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
			    <button type="button" class="btn btn-danger" onclick="deletes(0)"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;<?php echo getLang("ok") ?></button>
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
			<input type="hidden" name="inp[so_status]" id="so_status">
			<div class="modal-body">
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
			    <button type="button" class="btn btn-success" onclick="deletes(1)"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;<?php echo getLang("ok") ?></button>
			</div>
			</form>
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
                    <label class="col-sm-3 control-label"><?php echo getLang('Nama Stock Opname'); ?> <span class="required-class">*) </span></label>
                    <div class="col-sm-9 prepend-icon">
                        <input class="form-control" type="text" name="inp[so_name]" id="so_name" placeholder="<?php echo getLang('Nama Stock Opname'); ?>" required>
                        <i class="fa fa-file-o"></i>
                    </div>
                </div>   
				<div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo getLang('date'); ?> <span class="required-class">*) </span></label>
                     <div class="col-sm-3 prepend-icon">
                        <input class="form-control" type="text" name="inp[so_date]" id="so_date" readonly='true'   placeholder="<?php echo getLang('tanggal'); ?>" required>
                        <i class="fa fa-calendar"></i>
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
 

 
<?php $this->load->view('theme_footer'); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
 

<script type="text/javascript"> 
var save_method; 
var table;
var form 				= $('#modal_form #form');
var form_not_approved 	= $('#modal_not_approved #form');
var form_image 	= $('#modal_form_image #form'); 
var form_processed 	= $('#modal_form_processed #form');  

$(document).ready(function(){

	$('#so_date').datepicker({ 
		// minDate:new Date(),
		dateFormat : 'dd-mm-yy'
	});

	table = $('#table-member').DataTable({     
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],  
		"pageLength": 25,
		"processing": true,  
		"serverSide": true,  
		"destroy": true,  
		"order": [],  
		"ajax": {
			"url": "<?php echo site_url('index.php/stockopname/ajax_data')?>",
			"type": "POST",
			"data": function(d){
				d.status = $('#status').val(); 
			}
		}, 
		"columnDefs": [
			{ 
				"targets": [ 0,-1 ], 
				"orderable": false,  
			},
		], 
		"fnRowCallback": function( nRow, aaData, iDisplayIndex ) { 
			if ( aaData[3] == "Non Aktif" )
				{ 
					$(nRow).addClass('highlight_row_datatables');
					// $('td:eq(1)', nRow).addClass( 'testrow'); 
				}
			}
	});
	
	$('.dataTables_filter input').unbind().bind('keyup', function(e) {
	   if(e.keyCode == 13) {
		$("#table-member").dataTable().fnFilter(this.value);
		}
	}); 
	
	$( "#status" ).on( "change", function() {
		reload();
	} ); 
});  

function add() {
	save_method = 'add';
	reset(); 
	$('#room_id').removeAttr('disabled'); 
	$('#modal_form #member_type').select2("enable",true);		
	$('#modal_form').modal({keyboard: false, backdrop: 'static'});
	$('#modal_form .modal-title').html('<i class="fa fa-plus-square"></i>&nbsp;&nbsp;<?php echo getLang("add").' '. getCurrentMenuName() ?>'); 
} 
 
function save(){
	var url;
	if(save_method == 'add'){
		url = "<?php echo site_url('index.php/stockopname/insert')?>";
	}else{
		url = "<?php echo site_url('index.php/stockopname/update')?>";
	} 
	if ($('#modal_form #form').valid()) {
		$.ajax({
			url : url,
			type: "POST",
			data: $('#modal_form #form').serialize(),
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
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				info_alert('warning','<?php echo getLang("error_xhr")?>');
			}
		});
	}
}   


function edit(id){
  save_method = 'update';
  reset();

	  $.ajax({
		url : "<?php echo site_url('index.php/stockopname/edit')?>",
		type: "POST",
		data : {
			id : id
		},
		dataType: "JSON",
		success: function(data){
			$('#modal_form #id').val(data.so_id);  
			 $.each(data, function(key, value) {
				$('#modal_form #'+key).val(value);
			}); 
			  
			$('#room_id').attr('disabled', 'disabled'); 	
			$('#modal_form').modal({keyboard: false, backdrop: 'static'});
			$('#modal_form .modal-title').html('<i class="fa fa-pencil-square-o"></i>&nbsp;&nbsp;<?php echo getLang("edit").' '. getCurrentMenuName() ?>');
		},
		error: function (jqXHR, textStatus, errorThrown){
			alert('Error get data from ajax');
		}
	});
} 

function del(status,id,data) {  
	if (status=='0')  { 
		$('#modal_deactivate').modal({keyboard: false, backdrop: 'static'}); 
		$('#modal_deactivate #so_status').val(status); 
		$('#modal_deactivate #id').val(id); 
		$('#modal_deactivate .modal-body').html('<?php echo getLang("are_you_sure_want_to_deactivate_data")?> <strong>'+data+'</strong> ?'); 
	}
	else  {  
		$('#modal_activate').modal({keyboard: false, backdrop: 'static'}); 
		$('#modal_activate #so_status').val(status); 
		$('#modal_activate #id').val(id); 
		$('#modal_activate .modal-body').html('<?php echo getLang("are_you_sure_want_to_activate_data")?> <strong>'+data+'</strong> ?'); 
	} 
} 

function deletes(status) { 
	if(status==0) var form = $("#modal_deactivate #form");
	else var form = $("#modal_activate #form");

	$.ajax({
		url : 'index.php/stockopname/deletes',
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
	$("#modal_not_approved #form :input").removeData("previousValue");	//remove remote jquery validate previous value  
    form[0].reset();  	
	$("#modal_form #member_type").select2("val", "");
	$("#modal_form #member_bank_id").select2("val", "");
    $("label.error").hide();
    $(".error").removeClass("error");
}   
  function pic(img) { 
	  $('#modal_pic').modal({keyboard: false, backdrop: 'static'});  	
	  $('#modal_pic .modal-body').html('<img src="'+img+'" width="100%";>'); 
  }   

function reload() {
   table.draw();
}

  
function edit_image(id,data) { 
	$('#modal_form_image').modal({keyboard: false, backdrop: 'static'}); 
	$('#modal_form_image #id').val(id); 
	$('#modal_form_image #bds_status').val(data); 	
	// $('#modal_form_image .modal-body').html('<?php echo getLang("Apakah anda yakin akan mengubah status menjadi ")?> <strong>'+data+'</strong> ?'); 
}   


function save_image() { 
    var url;
    url = 'index.php/stockopname/received';

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
					reload(); 
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

function save_processed() { 
	var form = $("#modal_form_processed #form"); 
	if (form.valid()) {
		$.ajax({
			url : 'index.php/stockopname/processed',
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
				if(data.status!=false){
					$('#modal_form_processed').modal('hide');
					reload(); 
				}
				else alert(data.message);
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				info_alert('warning','<?php echo getLang("error_xhr")?>');
			}
		});
	}
}
</script>