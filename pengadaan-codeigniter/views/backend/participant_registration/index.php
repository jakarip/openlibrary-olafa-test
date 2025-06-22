<div class="panel panel-default flat"> 
	<div class="row" style="margin:20px 15px;">	
		<div class="col-md-6">
			<div class="form-group">
				<select name="periode" id="periode" class="select"> 
						<option value="0">Semua jalur</option>
						<?php foreach ($periode as $key => $row) { ?>
							<option value="<?=$row->periode_id ?>"><?= $row->periode_name.' / '.$row->periode_track_type.' ('.y_convert_date($row->periode_start_date,'d/m/Y').' - '.y_convert_date($row->periode_end_date,'d/m/Y').')'; ?></option>  
						
						<?php } ?>
				</select> 
			</div>
		</div> 
		<div class="col-md-6">
			<div class="form-group">
				<select name="status" id="status" class="select"> 
						<option value="">Semua Status</option> 
						<option value="Y">Diterima</option>
						<option value="N">Belum Diterima</option>
				</select> 
			</div>
		</div> 
	</div> 
    <table class="table table-bordered table-striped table-hover table-xs" id="table">
        <thead>
            <tr>
								<th width="5%" class="nosort center" style="text-align:center"><input type="checkbox" id="chk-all-location"></th>  
								<th width="5%" class="nosort center" style="text-align:center">Email</th> 
                <th width="15%">No. Transaksi</th>
                <th width="25%">Periode</th>
                <th width="20%">Nama</th>
                <th width="15%">Last Update</th>
                <th width="15%">Status</th>
                <th width="15%">No Surat</th>
               <!--<th width="10%" class="nosort center">Login as</th>-->
                <th width="10%" class="nosort center">Reset Status Berita Acara</th> 
                <th width="15%">Status Email</th> 
                <th width="10%" class="nosort center">Ubah Status menjadi Tidak Lulus</th> 
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<form id="frm" method="post" action="<?= y_url_admin() ?>/login/login_as" target="_blank">
<input type="hidden" id="frm-id" name="id">
</form>

<?php $this->load->view('backend/tpl_footer'); ?>
<!-- Theme JS files -->	
<script type="text/javascript" src="assets/limitless/global/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/core/setting.js"></script> 
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/styling/uniform.min.js"></script>

<script type="text/javascript">
var tb 		= '#table';      
var baseurl = '<?= y_url_admin() ?>/participant_registration';

$(document).ready(function() {	

	$("#periode").select2({
		minimumResultsForSearch: Infinity
	}); 

	$("#status").select2({
		minimumResultsForSearch: Infinity
	}); 

	$(tb).dataTable({
		'ajax': {
			'url':baseurl+'/json',
			'data' : function(data) {
				data.periode	= $('#periode').val();
				data.status		= $('#status').val(); 
			}
		}, 
		'order':[
			[4, 'desc']
		],
		'columnDefs': [ 
			{ 'targets': 'nosort', 'searchable': false, 'orderable': false },
			{ 'targets': 'center', 'className': 'center' } 
		],
		"drawCallback": function( settings ) {
			$(".chk-email,.chk-location,.chk-reset").uniform({
				radioClass: 'choice',
				wrapperClass: 'border-primary-600 text-primary-800'
			});
		},
		'scrollX': true
  }); 
	
	$('.dataTables_filter input[type=search]').attr('placeholder','Pencarian');

    $('.dataTables_length select').select2({
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });

	$("#periode").change(function(){ 
		_reload();
	});
	$("#status").change(function(){ 
		_reload();
	});  

	$("#chk-all-location").change(function(){
		$('input.chk-location:checkbox').not(this).prop('checked', this.checked);
		$.uniform.update();
	}); 
	
	$("#chk-all-location").uniform({
			radioClass: 'choice'
	});   

	$('.dt-buttons').html('<button class="btn btn-sm btn-success" onclick="excel()"><i class="icon-file-excel"></i> &nbsp;Download Location</button>');
});
	
function add()
{
	_reset();
	$('#act-save').show();
	$('#act-update').hide();
	$('#frmbox').modal({keyboard: false, backdrop: 'static'});
}

function edit(id)
{		
	$.ajax({
		url:baseurl+'/edit',
		global:false,
		async:true,
		dataType:'json',
		type:'post',
		data: ({ id : id }),
		success: function(e) {
			_reset();
			$('#act-save').hide();
			$('#act-update').show();
			$('#id').val(id);
			$.each(e, function(key, value) {
				$('#'+key).val(value);
			});
			$('#frmbox').modal({keyboard: false, backdrop: 'static'});
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

function save(url)
{
	if($("#frm").valid())
	{
		$.ajax({
			url:baseurl+'/'+url,
			global:false,
			async:true,
			type:'post',
			dataType:'json',
			data: $('#frm').serialize(),
			success : function(e) {
				if(e.status == 'ok;') 
				{
					_reload();
					$("#frmbox").modal('hide');
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
 


function excel()
{
	var id = '';
	$('input.chk-location:checkbox:checked').each(function () {
		id += $(this).val()+',';
	});	 
	
	if(id == '')
		alert('Pilih Salah Satu No Transaksi');
	else
	excels(id.slice(0,-1));
}

function excels(id)
{		
	$.ajax({
		url:baseurl+'/excels', 
		type:'post',
		data: ({ id : id }),
		beforeSend : function() {
			$('#loading-img').show();
		},
		complete : function() {
			$('#loading-img').hide();
		},
		success: function(data)
		{
				document.location.href =(data);
		}
	});	
} 

function send_email(id)
{
	if(confirm('Apakah anda yakin akan mengirimkan email ke pendaftar ini ?'))
	{
		$.ajax({
			url:baseurl+'/send_email_kelulusan',
			global:false,
			async:true,
			type:'post',
			dataType:'json',
			data: ({ id : id }),
			success : function(e) {
				if(e.status == 'ok;') 
				{
					alert('Kirim Email Berhasil');
					_reload();
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


function reset_status(id)
{
	if(confirm('Apakah anda yakin akan mereset status pendaftar ini ?'))
	{
		$.ajax({
			url:baseurl+'/reset_status',
			global:false,
			async:true,
			type:'post',
			dataType:'json',
			data: ({ id : id }),
			success : function(e) {
				if(e.status == 'ok;') 
				{
					alert('Reset Status Berhasil');
					_reload();
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


function reset_notransaksi(id)
{
	if(confirm('Apakah anda yakin merubah status menjadi tidak lulus ?'))
	{
		$.ajax({
			url:baseurl+'/reset_notransaksi',
			global:false,
			async:true,
			type:'post',
			dataType:'json',
			data: ({ id : id }),
			success : function(e) {
				if(e.status == 'ok;') 
				{
					alert('Ubah Status Berhasil');
					_reload();
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

function del(id, txt)
{
	if(confirm('Data: '+txt+'\nApakah anda yakin akan menghapus data tersebut ?')) {
		$.ajax({
			url:baseurl+'/delete',
			global:false,
			async:true,
			type:'post',
			dataType:'json',
			data: ({id : id }),
			success: function(e) { 
				if(e.status == 'ok;') 
				{
					_reload();
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

function login_as(id)
{
	/*$.ajax({
		url:'<?= y_url_admin() ?>/login/login_as',
		global:false,
		async:true,
		type:'post',
		dataType:'json',
		data: ({ id : id }),
		success : function(e) {
			if(e.status == 'ok;') 
			{
				var win = window.open('<?= base_url() ?>', '_blank')
				win.focus();
				//$('#hidden-url')[0].click();
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
	});*/
	
	$('#frm-id').val(id);
	$('#frm').trigger('submit');
}

function _reset()
{
	validator.resetForm();
	$("label.error").hide();
 	$(".error").removeClass("error");
	$('#frm')[0].reset();
}

function _reload()
{
	$(tb).dataTable().fnDraw();
}
 
</script>