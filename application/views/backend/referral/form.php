<div class="panel panel-default flat">
    <div class="panel-heading">
        <h6 class="panel-title">Data Akun Referral</h6>
        <div class="heading-elements">
            <button type="button" class="btn btn-xs btn-primary" onClick="save('update')"><i class="icon-floppy-disk position-left"></i> Update Data</button>
        </div>
    </div>
    <div class="panel-body">
        <form id="frm" class="form-horizontal">
                <input type="hidden" name="id" id="id" value="<?= $data->ref_id ?>">
            <div class="form-group form-group-sm">
                <label for="pus_name" class="col-sm-3 control-label">Kode Referral (Username)</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control input-sm" name="" id="ref_username" value="<?= $data->ref_username ?>" disabled>
                    <span class="help-block">Kode Referral digunakan untuk mendapatkan member</span>
                </div>
            </div>
            <div class="form-group form-group-sm">
                <label for="pus_name" class="col-sm-3 control-label">Nama Lengkap</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control input-sm" name="inp[ref_fullname]" id="ref_fullname" value="<?= $data->ref_fullname ?>" required>
                </div>
            </div>
            <div class="form-group form-group-sm">
                <label for="pus_name" class="col-sm-3 control-label">Email</label>
                <div class="col-sm-9">
                    <input type="email" class="form-control input-sm" name="inp[ref_email]" id="ref_email" value="<?= $data->ref_email ?>" required>
                </div>
            </div>
            <div class="form-group form-group-sm">
                <label for="pus_name" class="col-sm-3 control-label">No. Telp</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control input-sm" name="inp[ref_phone]" id="ref_phone" value="<?= $data->ref_phone ?>" required>
                </div>
            </div>
            <div class="form-group form-group-sm">
                <label for="pus_name" class="col-sm-3 control-label">Password</label>
                <div class="col-sm-9">
                    <input type="password" class="form-control input-sm pass" name="password" id="password">
                    <span class="help-block" id="help-password">Biarkan kosong jika tidak ingin merubah password</span>
                </div>
            </div>
            <div class="form-group form-group-sm">
                <label for="pus_name" class="col-sm-3 control-label">Ulangi Password</label>
                <div class="col-sm-9">
                    <input type="password" class="form-control input-sm pass" name="repassword" id="repassword">
                </div>
            </div>
        </form>
    </div>
</div>

<?php $this->load->view('backend/tpl_footer'); ?>
<!-- Theme JS files -->	
<script type="text/javascript" src="assets/limitless/global/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/core/setting.js"></script> 

<script type="text/javascript">
var tb 		= '#table';      
var baseurl = '<?= y_url_admin() ?>/referral';

$(document).ready(function() {	
    $(tb).dataTable({
        'ajax': {
            'url':baseurl+'/json'
		},
		'order':[
			[0, 'asc']
		],
		'columnDefs': [ 
			{ 'targets': -1, 'searchable': false, 'orderable': false, 'className': 'center' } 
		]
    });
	
	$('.dataTables_filter input[type=search]').attr('placeholder','Pencarian');

    $('.dataTables_length select').select2({
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });
	
	$('.dt-buttons').html('');
});
	
function add()
{
	_reset();
	$('#act-save').show();
	$('#act-update').hide();
	
	$('#help-password').hide();
	$('.pass').attr('required','required'); 
	
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
			
			$('#help-password').show();
			$('.pass').removeAttr('required');
			
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
	if($('#password').val() != $('#repassword').val())
	{
		alert('Password dan Ulangi Password tidak sama');
		return false;	
	}
	
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
</script>