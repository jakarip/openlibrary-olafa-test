<style>
.input-table {
	padding:3px;
	width:100%;
}

.error{
	color :#f44336;
}
</style>

<div class="panel panel-default flat">
    <div class="panel-heading">
        <h6 class="panel-title">Pertanyaan<a class="heading-elements-toggle"><i class="icon-more"></i></a></h6> 
    </div>
    <div class="panel-body">
    	<form class="form-horizontal" id="frm-setting-1">
		 
		<div class="form-group form-group-sm">
			<label class="control-label col-md-2">Pertanyaan</label>
			<div class="col-md-10">
				<input type="hidden"name="status" value="<?=($detail?'update':'insert') ?>"> 
				<input type="hidden" name="id" value="<?=($detail?$detail->qq_id:'') ?>">   
				<input type="hidden" name="inp[qq_id_questionnaire]" value="<?=$q_id ?>"> 
				
				<input type="text" class="form-control input-sm" name="inp[qq_question]" placeholder='Pertanyaan' id="qq_question" required value="<?=($detail?$detail->qq_question:'') ?>"> 
			</div>
		</div>  
		<div class="form-group form-group-sm">
			<label class="control-label col-md-2">Jenis Pertanyaan</label>
			<div class="col-md-10">
				<select name="inp[qq_type]" id="qq_type" <?=($detail?'disabled':'required') ?>  class="form-control input-sm" >
					<option value="">Pilih Jenis Pertanyaan</option>
					<option value="text" >Text</option>
					<option value="checkbox" >checkbox</option>
					<option value="radio" >radio</option>
					<!--<option value="text" >text</option> -->
				</select>
			</div>
		</div>   
		<div class="form-group form-group-sm" id="option_display" <?=(($detail and $detail->qq_type!='text')?'':' style="display:none"') ?>>
			<label class="control-label col-md-2" style="vertical-align:top">Pilihan Jawaban</label>
			<div class="col-md-10"> 
				<table class="table table-bordered table-striped table-hover" id="table-course">
					<thead>
						<tr>
							<th width="90%"><button type="button" class="btn btn-sm btn-primary" onclick="add()"><i class="icon-add"></i> &nbsp;Tambah Pilihan Jawaban</button></th>
							<th width="5%">Text</th> 
							<th width="5%">Aksi</th> 
							<th width="5%">Status Aktif</th> 
						</tr>
					</thead>
					<tbody id="option">
					<?php
						if($detail and $option){
							$inputan = 1;
							foreach($option as $row){
								echo '<input type="hidden" name="existing['.$inputan.']" value="'.$row->qo_id.'">
								<input type="hidden" name="text['.$inputan.']" value="'.$row->qo_text.'">
								<tr id="tr'.$inputan.'"><td><input type="text" value="'.$row->qo_option.'" name="inputan['.$inputan.']" id="inputan'.$inputan.'" class="form-control input-sm inputan" data-id="'.$inputan.'" required placeholder="Pilihan Jawaban"></td><td><input type="checkbox"  class="text" '.($row->qo_text=='1'?'checked':'').' disabled></td><td>-</td><td id="tdCheck'.$inputan.'" '.($row->qo_active=='1'?'class="btn-success"':'class="btn-danger"').'><input type="checkbox" name="check['.$inputan.']" id="check'.$inputan.'" class="check" '.($row->qo_active=='1'?'checked':'').' value="1" data-id='.$inputan.'></td></tr>';
								$inputan++;
							}
						} 
					?>
					</tbody>
				</table>
			</div>
		</div>   
        <div>
        	<a type="button" class=" btn btn-sm btn-labeled btn-danger" href='<?= y_url_admin() ?>/ms_questionnaire/detail/<?= $q_id ?>'><b><i class="icon-switch"></i></b> Batal</a>
        	<button type="button" class=" btn btn-sm btn-labeled btn-primary" onClick="save_setting('frm-setting-1')"><b><i class="icon-floppy-disk"></i></b> Simpan</button>
        </div>
        </form>
    </div>
</div>
 
<?php $this->load->view('backend/tpl_footer'); ?>
<!-- Theme JS files -->	 
<script type="text/javascript" src="assets/limitless/global/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/validation/validate.min.js"></script>
	<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/validation/additional-methods.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/core/setting.js"></script> 


<script src="assets/limitless/global/js/plugins/forms/styling/uniform.min.js"></script> 

<script type="text/javascript">
var baseurl = '<?= y_url_admin() ?>/ms_questionnaire';
var validator;


$.validator.addClassRules({
	file_uploads:{
		extension: "docx|pdf",
		maxFileSize: {
			"unit": "MB",
			"size": 2
		} 
	},
	photos:{
		extension: "jpg|jpeg|png",
		maxFileSize: {
			"unit": "MB",
			"size": 2
		} 
	}
});    

$(document).ready(function() {	
	$(".file-styled").uniform();
	
	$('#qq_type').select2(); 
	$('#qq_type').val('<?=($detail?$detail->qq_type:'') ?>').trigger('change');
	$('#qq_type').change(function(){
		if(this.value=='checkbox' || this.value=='radio'){
			$('#option_display').show();
			var inputan = $(".inputan").length; 
			if(inputan===0){
				inputan++;
				option(inputan); 
			}
		}
		else {
			$('#option_display').hide();
		}
	});  
	checked();  
});

function checked()
{ 
	$('.check').change(function(){
		var id = $(this).data('id'); 
		if ($(this).is(':checked')) {
			$('#tdCheck'+id).removeClass('btn-danger');
			$('#tdCheck'+id).addClass('btn-success');
		}
		else {
			$('#tdCheck'+id).removeClass('btn-success');
			$('#tdCheck'+id).addClass('btn-danger');
		}
	}); 
}

function add()
{
	var inputan = "";
	$('.inputan').each(function(i, obj) {
		inputan = $(obj).data('id'); 
	});
	inputan++;
	option(inputan); 
}

function option(inputan)
{
	$('#option').append('<tr id="tr'+inputan+'"><td><input type="text" value="" name="inputan['+inputan+']" id="inputan'+inputan+'" class="form-control input-sm inputan" data-id="'+inputan+'" required placeholder="Pilihan Jawaban"></td><td><input type="checkbox" class="text" name="text['+inputan+']"></td><td><a href="javascript:remove(\''+inputan+'\')" title="Delete Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-trash"></i></a></td><td class="btn-success" id="tdCheck'+inputan+'"><input type="checkbox" name="check['+inputan+']" id="check'+inputan+'" class="check" checked value="1" data-id='+inputan+'></td></tr>'); 
	checked();
}

function remove(inputan)
{
	var inp = $(".inputan").length; 
	if(inp>=1) $('#tr'+inputan).remove();
	else alert("Minimal harus ada 1 pilihan jawaban");
}




function save_setting(frm)
{
	if($("#"+frm).valid())
	{ 
		if($('input[type="checkbox"]:checked').length>0 || $('#qq_type').val()=='text'){
			var formData = new FormData($("#"+frm)[0]);
			$.ajax({
				url:baseurl+'/insert_detail',
				global:false,
				async:true,
				type:'post',
				dataType:'json',
				data: formData,
				contentType: false,//untuk upload image
				processData: false,//untuk upload image
				success : function(e) {
					if(e.status == 'ok;') 
					{
						$("#logo").html(e.logo);
						$("#background").html(e.background);
						$("#file").html(e.file);
						alert('Update Setting Sukses');
						window.location.href='<?= y_url_admin() ?>/ms_questionnaire/detail/<?= $q_id ?>';
					} 
					else alert(e.text);
				},
				error : function() {
					alert('<?= $this->config->item('alert_error') ?>');	 
				},
				beforeSend : function() {
					$('#loading-img').show();
				},
				complete : function() {
					$('#loading-img').hide();
				}
			});
		}
		else {
			alert("minimal harus ada 1 pilihan jawaban yang aktif");
		}
	}
}  
 
</script>