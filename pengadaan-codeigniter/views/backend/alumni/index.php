<div class="panel panel-default flat">
	<div class="row" style="margin:20px 15px;">	
		<div class="col-md-6"> 
			<div class="form-group">
				<select name="subs" id="subs" class="select"> 
						<option value="">Semua Status Berlangganan</option>
						<option value="Y">Sudah Berlangganan</option>
						<option value="N">Belum Berlangganan</option>
				</select> 
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<select name="status" id="status" class="select"> 
						<option value="">Semua Status</option>
						<option value="1">Sudah Aktivasi</option>
						<option value="2">Belum Aktivasi</option>
				</select> 
			</div>
		</div> 
	</div> 
    <table class="table table-bordered table-striped table-hover table-xs" id="table">
        <thead>
            <tr>  
                <th>No. Anggota</th> 
                <th>Nama Lengkap</th> 
                <th>Email</th> 
                <th>No. HP</th> 
                <th>Ijasah</th> 
                <th>KTP</th> 
                <th>Status</th>  
                <th>Status Berlangganan</th>  
                <th>Action</th> 
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<form id="frm" method="post" action="<?= y_url_admin() ?>/login/login_as" target="_blank">
<input type="hidden" id="frm-id" name="id">
<input type="hidden" id="frm-pass" name="pass">
</form>



<div class="modal fade" id="frmbox" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger-700" style="background-image:url(assets/images/backgrounds/bg.png)">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            	<h4 class="modal-title"><i class="fa fa-navicon"></i> &nbsp;<?= 'Form '.$title ?></h4>
            </div>
            <form id="frmsave" class="form-horizontal">
                <input type="hidden" name="id" id="id">
                <div class="modal-body">
                    <div class="box-body" style="padding-bottom:0px"> 
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Email</label>
                            <div class="col-sm-9">
                            	<input type="text" class="form-control input-sm" name="inp[par_email]" id="par_email" required>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">No. Telp</label>
                            <div class="col-sm-9">
                            	<input type="text" class="form-control input-sm" name="inp[par_phone]" id="par_phone" required>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">No. Hp</label>
                            <div class="col-sm-9">
                            	<input type="text" class="form-control input-sm" name="inp[par_mobile]" id="par_mobile" required>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-labeled btn-xs pull-left" data-dismiss="modal">
                	<b><i class="icon-switch"></i></b> Batal
                </button>                
                <button type="button" class="btn btn-success btn-labeled btn-xs" id="act-save" onclick="save_form('insert')">
                	<b><i class="icon-floppy-disk"></i></b> Simpan
                </button>
        		<button type="button" class="btn btn-success btn-labeled btn-xs" id="act-update" onclick="save_form('update')">
                	<b><i class="icon-floppy-disk"></i></b> Simpan Perubahan
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
var baseurl = 'index.php/<?= y_url_admin() ?>/alumni';

$(document).ready(function() {	

	
	$("#subs").select2({
		minimumResultsForSearch: Infinity
	}); 

	$("#status").select2({
		minimumResultsForSearch: Infinity
	}); 

	
	$("#subs").change(function(){ 
		_reload();
	});
	$("#status").change(function(){ 
		_reload();
	});  

    $(tb).dataTable({
        'ajax': {
						'url':baseurl+'/json', 
				'data' : function(data) {
					data.status	= $('#status').val();
					data.subs		= $('#subs').val(); 
				}
		},
		'order':[
			[0, 'desc'],[1, 'asc']
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