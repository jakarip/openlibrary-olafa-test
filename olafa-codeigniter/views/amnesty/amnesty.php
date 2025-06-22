 
<div class="row">
	<div class="col-lg-12 portlets">
	  <div class="panel"> 
		<div class="panel-content"> 
			<div class="row"> 
				<div class="panel-content pagination2">
					<div class="row content_button">
						<div class="col-lg-6" >
							<a href="javascript:;" onclick="add()" class="btn btn-danger">
								 <i class="fa fa-plus-square"></i><?php echo getLang("add") ?> <?php echo getLang("amnesty") ?>
							</a>
						</div> 
					</div>
					<div class="filter-left" id="rfidreg"> 
						<table id="table-member" class="table table-bordered table-hover">
							<thead>
								<tr>
									<th class="text-center" width="5%"><?php echo getLang('no'); ?></th>
									<th width="30%"><?php echo getLang('username'); ?></th>
									<th width="55%"><?php echo getLang('fullname'); ?></th> 
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
                        <input class="form-control" type="text" name="inp[username_id]" id="username" placeholder="<?php echo getLang('username'); ?>" required> 
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
	
	 
	table = $('#table-member').DataTable({     
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],  
		"pageLength": 25,
		"processing": true,  
		"serverSide": true,  
		"destroy": true,  
		"order": [],  
		"ajax": {
			"url": "<?php echo site_url('index.php/amnesty/ajax_data')?>",
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
	 
});  
 

function add() {
	reset(); 
	$('#modal_form #user_username').show();			
	$('#modal_form #username').tokenInput("index.php/amnesty/auto_data", {
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
		url : "<?php echo site_url('index.php/amnesty/edit')?>",
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
		url = "<?php echo site_url('index.php/amnesty/insert')?>";
	}else{
		url = "<?php echo site_url('index.php/amnesty/update')?>";
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
		url : 'index.php/amnesty/deletes',
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
 

</script>