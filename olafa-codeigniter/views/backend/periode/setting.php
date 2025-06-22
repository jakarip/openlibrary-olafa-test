<style>
.input-table {
	padding:3px;
	width:100%;
}

.error{
color : #f44336;
}
</style> 

<div class="panel panel-default flat">  
    <div class="panel-body"> 
		<form action="#" class="form-horizontal" id="form_update"> 
            <input type="hidden" name="id" id="id" value="<?=$periode->periode_id ?>">
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-3 control-label">Nama</label>
				<div class="col-sm-9">
					<input type="text" class="form-control input-sm" value="<?=$periode->periode_name ?>" name="inp[periode_name]" id="periode_name" required>
				</div>
			</div>
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-3 control-label">Tanggal Awal</label>
				<div class="col-sm-9">
					<input type="text" class="form-control input-sm" value="<?=y_convert_date($periode->periode_start_date,'d-m-Y') ?>" name="inp[periode_start_date]" id="periode_start_date" required>
				</div>
			</div>
			<div class="form-group form-group-sm"> 
				<label for="pus_name" class="col-sm-3 control-label">Tanggal Akhir</label>
				<div class="col-sm-9">
					<input type="text" class="form-control input-sm" value="<?=y_convert_date($periode->periode_end_date,'d-m-Y') ?>" name="inp[periode_end_date]" id="periode_end_date" required>
				</div>
			</div>    
			<div class="text-left"> 
				<button type="button" class="btn btn-sm btn-primary" onclick="update()"><i class="icon-floppy-disk position-left"></i> Simpan</button> 
			</div>  
		</form>
    </div>
</div>

<div class="panel panel-default flat">
	
    <div class="panel-heading">
        <h6 class="panel-title">Setting Periode</h6>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
                <li><a data-action="reload"></a></li>
                <li><a data-action="close"></a></li>
            </ul>
        </div>
    </div> 
	<form action="#" class="form-horizontal" id="form_setting">  
	<input type="hidden" name="id" id="id" value="<?=$periode->periode_id ?>">
	<div class="table-responsive">
		<table class="table border-double">
			<thead>
				<tr>
					<th>#</th>
					<?php if($component){ foreach($component as $row){ echo '<th>'.$row->component_name.'</th>'; } } ?>
				</tr>
			</thead>
			<tbody> 
			<?php 
			
			if($prodi){
					foreach($prodi as $dt){
						echo '<tr>';
						echo '<td>'.$dt->prodi_name.'</td>';
						if($component){ 
							foreach($component as $row){  
								echo '<td><input onkeyup="FormatCurrency(this)" class="form-control required fee" type="text" name="fee['.$dt->prodi_id.']['.$row->component_id.']" value="'.(empty($setting[$dt->prodi_id][$row->component_id])?'':number_format($setting[$dt->prodi_id][$row->component_id],0,'','.')).'"></td>'; 
							} 
						} 
						echo '</tr>';
					}
			}
			?>
		
			</tbody>
		</table>
	</div>   
	<div class="panel-footer">
		<div class="heading-elements"> 
			<span class="heading-text text-semibold"><button type="button" class="btn btn-sm btn-primary" onclick="setting()"><i class="icon-floppy-disk position-left"></i> Simpan</button></span> 
		</div>
	</div>
	</form> 
</div> 

<?php $this->load->view('backend/tpl_footer'); ?>
<!-- Theme JS files -->	  
<script type="text/javascript" src="assets/limitless/global/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/core/setting.js"></script> 

<script type="text/javascript">
var baseurl = '<?= y_url_admin() ?>/periode';
var validator; 

var form_update 	= $('#form_update');
var form_setting  	= $('#form_setting');

$.validator.addClassRules({ 
	fee: { 
		minlength : 1,
	}, 
});   

$(document).ready(function() {	
	
	
			
	$('#periode_start_date').daterangepicker({ 
		singleDatePicker: true,
		locale: {
			format: 'DD-MM-YYYY'
		}
	});
	
	$('#periode_end_date').daterangepicker({ 
		singleDatePicker: true,
		locale: {
			format: 'DD-MM-YYYY'
		}
	});

    $('#table-course').dataTable({
        'ajax': {
            'url':baseurl+'/course_json'
		},
		'order':[
			[0, 'desc'],[1, 'asc']
		],
		'columnDefs': [ 
			{ 'targets': -1, 'searchable': false, 'orderable': false, 'className': 'center' } 
		]
    }); 
});



function update()
{
	if(form_update.valid())
	{
		$.ajax({
			url:baseurl+'/update',
			global:false,
			async:true,
			type:'post',
			dataType:'json',
			data: form_update.serialize(),
			success : function(e) {
				if(e.status == 'ok;') 
				{
					alert('Ubah Data Periode Sukses');
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
} 

function setting()
{
	if(form_setting.valid())
	{
		$.ajax({
			url:baseurl+'/update_setting',
			global:false,
			async:true,
			type:'post',
			dataType:'json',
			data: form_setting.serialize(),
			success : function(e) {
				if(e.status == 'ok;') 
				{
					alert('Update Setting Sukses');
					//window.location.reload();
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
} 
</script>