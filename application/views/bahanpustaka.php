<div class="row">
    <div class="col-lg-12">
        <div class="panel">
            <div class="panel-header bg-red">
			<h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong> </h3>
            </div>
            <div class="panel-content pagination2"> 
				<div class="row content_button">
					<div class="col-lg-6" > 
						<button type="button" class="btn btn-success" onclick="export_excel()"><i class="fa fa-file"></i>&nbsp;&nbsp;<?php echo getLang("export excel") ?>
					</div> 
					<div class="col-lg-1" >Status</div>
					<div class="col-lg-5" > 
					 		<select name="status" id="status">
							<option value="">Semua</option>
							<option value="Request">Request</option>
							<option value="Approved">Approved</option>
							<option value="Not Approved">Not Approved</option>
							<option value="Process">Process</option>
							<option value="Completed">Completed</option>
						</select>
					</div> 
				</div>
                <table id="table-member" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%"><?php echo getLang('no'); ?></th>
                            <th width="9%"><?php echo getLang('tanggal'); ?></th>
                            <th width="9%"><?php echo getLang('fakultas'); ?></th>
                            <th width="9%"><?php echo getLang('prodi'); ?></th>
                            <th width="9%"><?php echo getLang('nik/nim'); ?></th>
                            <th width="9%"><?php echo getLang('nama'); ?></th>
                            <th width="9%"><?php echo getLang('judul'); ?></th>
                            <th width="9%"><?php echo getLang('pengarang'); ?></th>
                            <th width="9%"><?php echo getLang('penerbit'); ?></th>
                            <th width="9%"><?php echo getLang('tahun terbit'); ?></th>
                            <th width="9%"><?php echo getLang('matakuliah'); ?></th>
                            <th width="9%"><?php echo getLang('semester'); ?></th>
                            <th width="9%"><?php echo getLang('referensi'); ?></th>   
                            <th width="9%"><?php echo getLang('No. Katalog'); ?></th>   
                            <th width="9%"><?php echo getLang('history status'); ?></th>   
                            <th width="9%"><?php echo getLang('status'); ?></th>
                            <th width="9%"><?php echo getLang('Alasan Ditolak'); ?></th>
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
                    <label class="col-sm-3 control-label"><?php echo getLang('capacity'); ?> <span class="required-class">*) </span></label>
                     <div class="col-sm-9 prepend-icon">
                        <input class="form-control" type="text" name="inp[room_capacity]" id="room_capacity" placeholder="<?php echo getLang('capacity'); ?>" required>
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




<div class="modal fade" id="modal_delete" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"><i class="fa fa-pencil"></i>&nbsp;&nbsp;<?php echo getLang("edit")?> <?php echo "Status"; ?></h4></strong>
			</div>
			<form id="form" class="form-horizontal">
			<input type="hidden" name="id" id="id"> 
			<input type="hidden" name="inp[bp_status]" id="bp_status">
			<div class="modal-body">
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
			    <button type="button" class="btn btn-danger" onclick="deletes()"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;<?php echo getLang("ok") ?></button>
			</div>
			</form> 
		</div>
	</div>
</div>

<div class="modal fade" id="modal_not_approved" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"><i class="fa fa-pencil"></i>&nbsp;&nbsp;<?php echo getLang("edit")?> <?php echo "Status"; ?></h4></strong>
			</div>
			<form id="form" class="form-horizontal"> 
			<input type="hidden" name="id" id="id"> 
			<input type="hidden" name="inp[bp_status]" id="bp_status">
			<div class="modal-body">
				<div class="form-group">
					<label class="col-sm-3 control-label"></label>
                    <span class="col-sm-9 control-label"><?php echo getLang("Apakah anda yakin akan mengubah status menjadi ")?> <strong>Not Approved</strong> ?</span>
                </div>
				<div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo getLang('reason'); ?> <span class="required-class">*) </span></label>
                    <div >
						<select name="option_reason" id="option_reason" class="form-control" required>
							<option value="">Pilih Alasan</option>
							<option value="Judul Buku sudah ada di Open Library">Judul Buku sudah ada di Open Library</option>
							<option value="Judul buku tidak ditemukan di penerbit manapun">Judul buku tidak ditemukan di penerbit manapun</option>
							<option value="lainnya">Lainnya</option>
						</select> 
						<div id="option_lain" style="display:none">
                       	 	<textarea class="form-control" name="bp_reason" id="bp_reason"></textarea>
						</div>
                    </div>
                </div>
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
			    <button type="button" class="btn btn-danger" onclick="save_not_approved()"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;<?php echo getLang("ok") ?></button>
			</div>
			</form> 
		</div>
	</div>
</div>

<div class="modal fade" id="modal_completed" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"><i class="fa fa-pencil"></i>&nbsp;&nbsp;<?php echo getLang("edit")?> <?php echo "Status"; ?></h4></strong>
			</div>
			<form id="form" class="form-horizontal">
			<input type="hidden" name="id" id="id"> 
			<input type="hidden" name="inp[bp_status]" id="bp_status">
			<div class="modal-body">
				<div class="form-group">
					<!-- <label class="col-sm-3 control-label"></label> -->
                    <span class="col-sm-12 control-label"><?php echo getLang("Apakah anda yakin akan mengubah status menjadi ")?> <strong>Completed</strong> dan melakukan transfer ke katalog ?</span>
                </div>
				<!-- <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo getLang('No. Katalog'); ?> <span class="required-class">*) </span></label>
                    <div class="col-sm-9 prepend-icon">
                        <input type="text" class="form-control" name="inp[bp_item_code]" id="bp_item_code" required>
                    </div>
                </div> -->
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
			    <button type="button" class="btn btn-danger" onclick="save_completed()"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;<?php echo getLang("ok") ?></button>
			</div>
			</form> 
		</div>
	</div>
</div>



<div class="modal fade" id="modal_history" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"><i class="fa fa-pencil"></i>&nbsp;&nbsp;<?php echo getLang("history status")?></h4></strong>
			</div> 
			<div class="modal-body">
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
var form_completed 	= $('#modal_completed #form');
form_not_approved.validate();
form.validate({       
	ignore: "",
	rules: {
		'inp[room_capacity]' : 'number'        
	}, 
	onkeyup: false
}); 

$(document).ready(function(){
	table = $('#table-member').DataTable({     
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],  
		"pageLength": 25,
		"processing": true,  
		"serverSide": true,  
		"destroy": true,  
		"order": [],  
		"ajax": {
			"url": "<?php echo site_url('index.php/bahanpustaka/ajax_data')?>",
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
		]
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


function export_excel() { 

	$.ajax({
		url : 'index.php/bahanpustaka/export_excel',
		type: "POST",
		data: {
            status : $('#status').val() 
        }, 
		beforeSend : function(){ 
			showLoading();
		},
		complete : function(){
			hideLoading();
		}, 
		success: function(data)
		{
			console.log(data);
			// var data2 = [
			// 	["Joa Doe", "joa@doe.com"],
			// 	["Job Doe", "job@doe.com"],
			// 	["Joe Doe", "joe@doe.com"],
			// 	["Jon Doe", "jon@doe.com"],
			// 	["Joy Doe", "joy@doe.com"]
			// ];

			
			var obj = JSON.parse(data); 
 
		 
			var workbook = XLSX.utils.book_new(),
			worksheet = XLSX.utils.aoa_to_sheet(obj);
			workbook.SheetNames.push("First");
			workbook.Sheets["First"] = worksheet;

			XLSX.writeFile(workbook, "export_usulan_bahanpustaka.xlsx");
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			info_alert('warning','<?php echo getLang("error_xhr")?>');
		}
	}); 

	
} 


function not_approved(id,data) { 
	$('#modal_not_approved').modal({keyboard: false, backdrop: 'static'}); 
	$('#modal_not_approved #id').val(id);  
	$('#modal_not_approved #bp_status').val(data); 
	//$('#modal_not_approved .modal-body').html('<?php echo getLang("Apakah anda yakin akan mengubah status menjadi ")?> <strong>'+data+'</strong> ?'); 

	$("#modal_not_approved #option_reason").change(function(){
		if($(this).val()=='lainnya'){
			$("#modal_not_approved #option_lain").show();
			$("#modal_not_approved #bp_reason").prop('required',true);
		}
		else { 
			$("#modal_not_approved #option_lain").hide();
			$("#modal_not_approved #bp_reason").prop('required',false);
		}
	});
} 


function completed(id,data) { 
	$('#modal_completed').modal({keyboard: false, backdrop: 'static'}); 
	$('#modal_completed #id').val(id);  
	$('#modal_completed #bp_status').val(data); 
	//$('#modal_not_approved .modal-body').html('<?php echo getLang("Apakah anda yakin akan mengubah status menjadi ")?> <strong>'+data+'</strong> ?'); 
}  

function save_not_approved(status) { 
	var form = $("#modal_not_approved #form");  
	if ($('#modal_not_approved #option_reason').select2().val()!="") {
		$.ajax({
			url : 'index.php/bahanpustaka/not_approved',
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
				$('#modal_not_approved').modal('hide');
				reload(); 
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				info_alert('warning','<?php echo getLang("error_xhr")?>');
			}
		});
	}
	else alert("Silahkan Pilih Alasan");
}

function save_completed(status) { 
	var form = $("#modal_completed #form"); 
	if (form.valid()) {
		$.ajax({
			url : 'index.php/bahanpustaka/completed',
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
					$('#modal_completed').modal('hide');
					reload(); 
					var url = "https://openlibrary.telkomuniversity.ac.id/knowledgeitem/" + data.ids + "/edit.html";
					window.open(url, '_blank');
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
 
function history(id) { 
	$('#modal_history').modal({keyboard: false, backdrop: 'static'}); 
	$.ajax({
		url : 'index.php/bahanpustaka/history',
		type: "POST",
		data: {
            id : id
        }, 
		beforeSend : function(){ 
			showLoading();
		},
		complete : function(){
			hideLoading();
		}, 
		success: function(data)
		{
			$('#modal_history .modal-body').html(data);  
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			info_alert('warning','<?php echo getLang("error_xhr")?>');
		}
	}); 
}   

function deletes(status) { 
	var form = $("#modal_delete #form"); 

	$.ajax({
		url : 'index.php/bahanpustaka/update',
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
  
function edit(id,data) { 
	$('#modal_delete').modal({keyboard: false, backdrop: 'static'}); 
	$('#modal_delete #id').val(id); 
	$('#modal_delete #bp_status').val(data); 	
	$('#modal_delete .modal-body').html('<?php echo getLang("Apakah anda yakin akan mengubah status menjadi ")?> <strong>'+data+'</strong> ?'); 
}   

function reload() {
   table.draw();
}
</script>