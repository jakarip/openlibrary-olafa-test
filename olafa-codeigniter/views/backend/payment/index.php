<div class="panel panel-default flat"> 
	<div class="row" style="margin:20px 15px;">	
		<div class="col-md-12">
			<div class="form-group">
				<select name="periode" id="periode" class="select"> 
						<option value="0">Semua jalur</option>
						<?php foreach ($periode as $key => $row) { ?>
							<option value="<?=$row->periode_id ?>"><?= $row->periode_name.' / '.$row->periode_track_type.' ('.y_convert_date($row->periode_start_date,'d/m/Y').' - '.y_convert_date($row->periode_end_date,'d/m/Y').')'; ?></option>  
						
						<?php } ?>
				</select> 
			</div>
		</div>  
	</div>
    <table class="table table-bordered table-striped table-hover table-xs" id="table">
        <thead>
            <tr>
								<th width="5%" class="nosort center" style="text-align:center">Check All Generate Register Pembayaran <br><input type="checkbox" id="chk-all-location"></th> 
								<th width="5%">Status Generate Register Pembayaran</th> 
                <th width="15%">No. Transaksi</th>
                <th width="25%">Periode</th>
                <th width="20%">Nama</th>
                <th width="15%">Status Pembayaran</th>
                <th width="15%">Tanggal Pembayaran</th>  
                <th width="15%" class="nosort">Riwayat Pembayaran</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div> 

<div class="modal fade" id="frmbox" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger-700" style="background-image:url(assets/images/backgrounds/bg.png)">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            	<h4 class="modal-title"><i class="fa fa-navicon"></i> &nbsp;<?= 'Form '.$title ?></h4>
            </div>
            <form id="frm" class="form-horizontal">
                <div class="modal-body">
                    <div class="box-body" style="padding-bottom:0px">  
											<table class="table table-bordered table-striped table-hover table-xs" id="table">
													<thead>
															<tr>
																	<th width="5%" class="nosort center" style="text-align:center">Tanggal Riwayat</th>  
																	<th width="15%">No. Referensi</th> 
																	<th width="20%">Nama</th> 
																	<th width="20%">Bank</th> 
															</tr>
													</thead>
													<tbody></tbody>
											</table>
                    </div><!-- /.box-body -->
                </div>                
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal --> 


<div class="modal fade" id="frmbox" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger-700" style="background-image:url(assets/images/backgrounds/bg.png)">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            	<h4 class="modal-title"><i class="fa fa-navicon"></i> &nbsp;<?= 'Form '.$title ?></h4>
            </div>
            <form id="frm" class="form-horizontal">
                <div class="modal-body">
                    <div class="box-body" style="padding-bottom:0px"> 
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Summary Hasil</label>
                            <div class="col-sm-9"> 
                                <textarea class="form-control input-sm" name="register" id="register" rows="13" readonly></textarea>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                </div>                
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal --> 

<?php $this->load->view('backend/tpl_footer'); ?>
<!-- Theme JS files -->	
<script type="text/javascript" src="assets/limitless/global/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/core/setting.js"></script> 
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/styling/uniform.min.js"></script>

<script type="text/javascript">
var tb 		= '#table';      
var baseurl = '<?= y_url_admin() ?>/payment';

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
			$(".chk-location").uniform({
				radioClass: 'choice',
				wrapperClass: 'border-primary-600 text-primary-800'
			});
		}
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

	$('.dt-buttons').html('<button class="btn btn-sm btn-primary" onclick="generates()"><i class="icon-files-empty"></i> &nbsp;Generate Register Pembayaran</button>');
});
	
function add()
{
	_reset();
	$('#act-save').show();
	$('#act-update').hide();
	$('#frmbox').modal({keyboard: false, backdrop: 'static'});
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

function generates()
{
	var id = '';
	$('input.chk-location:checkbox:checked').each(function () {
		id += $(this).val()+',';
	});	
	
	if(id == '')
		alert('Pilih Salah Satu No Transaksi');
	else
		generate(id.slice(0,-1));
}

function generate(id)
{		
	$.ajax({
		url:baseurl+'/generate',
		global:false,
		async:true,
		dataType:'json',
		type:'post',
		data: ({ id : id }),
		success: function(e) {
			if(e.status == 'ok;') 
			{ 
				add(); 
				_reload();   
				$('#register').val(e.text);
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