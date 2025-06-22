 
<div class="panel panel-default flat">
	<div class="row" style="margin:20px 15px;">	
		<div class="col-md-2"> 
			<div class="form-group"> 
				<?= form_dropdown('type', $jenis_anggota, '', 'class="form-control select2" id="type" required="required"') ?>
			</div>
		</div>
		<div class="col-md-2"> 
			<div class="form-group">
				<select name="subs" id="subs" class="select"> 
						<option value="">Semua Status Berlangganan</option>
						<option value="Y">Sudah Berlangganan</option>
						<option value="N">Belum Berlangganan</option>
				</select> 
			</div>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				<select name="status" id="status" class="select"> 
						<option value="">Semua Status</option>
						<option value="1">Sudah Aktivasi</option>
						<option value="2">Belum Aktivasi</option>
				</select> 
			</div> 
		</div> 
		<div class="col-md-2">
			<div class="form-group">
				<select name="dates_option" id="dates_option" class="select"> 
						<option value="all">Semua Tanggal</option>
						<option value="date">Range Tanggal</option>
				</select> 
			</div>
		</div> 
		<div class="col-sm-3" >
			<input type="text" class="form-control input-sm" name="dates" id="dates" value="">
		</div> 
		<div class="col-sm-1"> 
			<button type="button" class="btn btn-primary btn-labeled btn-xs" id="filter" >
				<b><i class="icon-search4"></i></b> Filter
			</button>
		</div>
	</div> 
    <table class="table table-bordered table-striped table-hover table-xs" id="table">
        <thead>
            <tr>    
                <th class="nosort">Action</th> 
                <th>Status</th>  
                <th>Status Berlangganan</th>
                <th>Ijasah</th> 
                <th>KTP</th> 
                <th>Karpeg/KTM</th>
                <th>Tanggal Daftar</th> 
                <th>Jenis Anggota</th> 
                <th>No. Anggota</th> 
                <th>Nama Lengkap</th> 
                <th>Email</th> 
                <th>No. HP</th> 
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div> 
<!-- <form id="frm" method="post" action="<?= y_url_admin() ?>/login/login_as" target="_blank">
<input type="hidden" id="frm-id" name="id">
<input type="hidden" id="frm-pass" name="pass">
</form> -->



<div class="modal fade" id="frmbox" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger-700" style="background-image:url(assets/images/backgrounds/bg.png)">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            	<h4 class="modal-title"><i class="fa fa-navicon"></i> &nbsp;<?= 'Form Data Reject Member' ?></h4>
            </div>
            <form id="frm" class="form-horizontal">
                <input type="hidden" name="id" id="id">
                <div class="modal-body">
                    <div class="box-body" style="padding-bottom:0px">  
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Username</label>
                            <div class="col-sm-9">
                            	<input type="text" class="form-control input-sm"  id="master_data_user" readonly>
                            </div>
                        </div> 
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Nama</label>
                            <div class="col-sm-9">
                            	<input type="text" class="form-control input-sm"  id="master_data_fullname" readonly>
                            </div>
                        </div> 
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Alasan</label>
                            <div class="col-sm-9">
                            	<textarea class="form-control input-sm " name="alasan" id="alasan" required></textarea>
                            </div>
                        </div> 
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label"></label>
                            <div class="col-sm-9">
                            	<input type="checkbox" name="send" id="send" checked > Kirim Email
                            </div>
                        </div> 
                    </div><!-- /.box-body -->
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-labeled btn-xs pull-left" data-dismiss="modal">
                	<b><i class="icon-switch"></i></b> Batal
                </button>                
                <button type="button" class="btn btn-success btn-labeled btn-xs" id="act-save" onclick="reject()">
                	<b><i class="icon-floppy-disk"></i></b> Simpan
                </button> 
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
var baseurl = 'index.php/<?= y_url_admin() ?>/member';

$(document).ready(function() {	

	
	$('#dates').daterangepicker({  
		locale: {
			format: 'DD-MM-YYYY'
		},
		showDropdowns: true,
		opens: 'left',
		applyClass: 'bg-primary-600',
		cancelClass: 'btn-light'
	});

	$("#subs").select2({
		minimumResultsForSearch: Infinity
	}); 
	$("#type").select2({
		minimumResultsForSearch: Infinity
	}); 

	$("#status").select2({
		minimumResultsForSearch: Infinity
	}); 

	$("#dates_option").select2({
		minimumResultsForSearch: Infinity
	});  
	 
	$("#dates").hide(); 

	$("#filter").click(function(){ 
		_reload();
	}); 

	$("#dates_option").change(function(){ 
		if($(this).val()=='all') $("#dates").hide(); 
		else $("#dates").show(); 
	});  

    $(tb).dataTable({
        'ajax': {
						'url':baseurl+'/json', 
				'data' : function(data) {
					data.type	= $('#type').val();
					data.status	= $('#status').val();
					data.subs		= $('#subs').val(); 
					data.dates_option		= $('#dates_option').val(); 
					data.dates		= $('#dates').val(); 
				}
		},
		'order':[ 
			[1, 'desc'],[6, 'asc']
		],
		cache: false,
		'columnDefs': [ 
			{ 'targets': 'nosort', 'searchable': false, 'orderable': false },
			{ 'targets': 'center', 'className': 'center' } 
		], 
		"drawCallback": function( settings ) {
			$(".chk-location").uniform({
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

	
	$("#chk-all-location").change(function(){
		$('input.chk-location:checkbox').not(this).prop('checked', this.checked);
		$.uniform.update();
	});
	
	$("#chk-all-location").uniform({
			radioClass: 'choice'
	}); 
	$('.dt-buttons').html('');
	// $('.dt-buttons').html('<button class="btn btn-sm btn-success" onclick="excel()"><i class="icon-file-excel position-left"></i>Export Excel</button><button class="btn btn-sm btn-primary" onclick="question()"><i class="icon-file-excel position-left"></i>Download Kuesioner</button>');
});
	

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
 
function reject_form(id)
{
	$.ajax({
		url:baseurl+'/reject_form',
		global:false,
		async:true,
		dataType:'json',
		type:'post',
		data: ({ id : id }),
		success: function(e) {
			_reset();
			$('#act-save').show();
			$('#act-update').show();
			$('#id').val(id); 
			$('#master_data_fullname').val(e.master_data_fullname); 
			$('#master_data_user').val(e.master_data_user);  
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

function reject()
{ 
	if($("#frm").valid())
	{
    if(confirm('Nama : '+$('#master_data_fullname').val()+'.\nApakah anda yakin akan melakukan reject ?')) {
			
        $.ajax({
            url:baseurl+'/reject',
            global:false,
            async:true,
            dataType:'json',
            type:'post',
						data: $('#frm').serialize(),
            success: function(e) { 
                _reload();
								$("#frmbox").modal('hide');
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
} 

 
function approval(id,no)
{ 
    if(confirm('Nama : '+no+'.\nApakah anda yakin akan melakukan approval ?')) {
        $.ajax({
            url:baseurl+'/approve',
            global:false,
            async:true,
            dataType:'json',
            type:'post',
						data: ({ id : id }),
            success: function(e) { 
                _reload();
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


function save_form(url)
{
	if($("#frmsave").valid())
	{
		$.ajax({
			url:baseurl+'/'+url,
			global:false,
			async:true,
			type:'post',
			dataType:'json',
			data: $('#frmsave').serialize(),
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

function excel() {
	$.ajax({
		url:baseurl+'/excel2',
        type: "POST", 
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


function question()
{
	var id = '';
	$('input.chk-location:checkbox:checked').each(function () {
		id += $(this).val()+',';
	});	 
	
	if(id == '')
		alert('Pilih Salah Satu No Participant');
	else
	questions(id.slice(0,-1));
}

function questions(id)
{		
	$.ajax({
		url:baseurl+'/questions', 
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

function aktivasi(id,pass) {
	$.ajax({
		url:baseurl+'/aktivasi',
		data : {
			id : id,
			pass : pass
		},
        type: "POST", 
		beforeSend : function() {
			$('#loading-img').show();
		},
		complete : function() {
			$('#loading-img').hide();
		},
        success: function(data)
        {
			_reload();
        }
    });
}   

function login_as(id,pass)
{ 
	$('#frm-id').val(id);
	$('#frm-pass').val(pass)
	$('#frm').trigger('submit');
}  
</script>