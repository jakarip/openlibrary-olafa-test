<div class="panel panel-default flat">
    <table class="table table-bordered table-striped table-hover" id="table">
        <thead>
            <tr>
                <th width="10%">Kode</th>
                <th width="30%">Nama</th>
                <th width="10%">Jenis Jalur</th>
                <th width="10%">Tanggal</th> 
                <th width="10%">Status</th>
                <th width="10%">Jadwal Tes</th>
                <th width="10%">Setting</th>
                <th width="20%">&nbsp;</th>
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
                <input type="hidden" name="id" id="id">
                <div class="modal-body">
                    <div class="box-body" style="padding-bottom:0px">
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Nama</label>
                            <div class="col-sm-9">
                            	<input type="text" class="form-control input-sm" name="inp[periode_name]" id="periode_name" required>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Jenis Jalur</label>
                            <div class="col-sm-9">
								<?= form_dropdown('inp[periode_track_type]', $jns, '', 'class="form-control input-sm" id="periode_track_type" required'); ?>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Awal</label>
                            <div class="col-sm-9">
                            	<input type="text" class="form-control input-sm" name="inp[periode_start_date]" id="periode_start_date" required>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Akhir</label>
                            <div class="col-sm-9">
                            	<input type="text" class="form-control input-sm" name="inp[periode_end_date]" id="periode_end_date" required>
                            </div>
                        </div> 
                    </div><!-- /.box-body -->
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-labeled btn-xs pull-left" data-dismiss="modal">
                	<b><i class="icon-switch"></i></b> Batal
                </button>                
                <button type="button" class="btn btn-success btn-labeled btn-xs" id="act-save" onclick="save('insert')">
                	<b><i class="icon-floppy-disk"></i></b> Simpan
                </button>
        		<button type="button" class="btn btn-success btn-labeled btn-xs" id="act-update" onclick="save('update')">
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

<script type="text/javascript">
var tb 		= '#table';      
var baseurl = '<?= y_url_admin() ?>/periode';

$(document).ready(function() {	 
	
	$("#periode_track_type").select2({
		minimumResultsForSearch: Infinity
	});

    $(tb).dataTable({
        'ajax': {
            'url':baseurl+'/json'
		},
		'order':[
			[0, 'asc']
		],
		'columnDefs': [ 
			{ 'targets': [-1,-2], 'searchable': false, 'orderable': false, 'className': 'center' } 
		]
    });
	
	$('.dataTables_filter input[type=search]').attr('placeholder','Pencarian');

    $('.dataTables_length select').select2({
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });
	
	$('.dt-buttons').html('<button class="btn btn-sm btn-primary" onclick="add()"><i class="icon-add"></i> &nbsp;Tambah Data</button>');
	 
});
	
function add()
{ 
	$('#frmbox #periode_start_date').daterangepicker({ 
				singleDatePicker: true,
		locale: {
			format: 'DD-MM-YYYY'
		}
	});
	
	$('#frmbox #periode_end_date').daterangepicker({ 
		singleDatePicker: true,
		locale: {
			format: 'DD-MM-YYYY'
		}
	});
	_reset();
	$("#periode_track_type").prop('disabled',false);
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
			
			$('#frmbox #periode_start_date').daterangepicker({ 
				singleDatePicker: true,
				locale: {
					format: 'DD-MM-YYYY'
				}
			});
			
			$('#frmbox #periode_end_date').daterangepicker({ 
				singleDatePicker: true,
				locale: {
					format: 'DD-MM-YYYY'
				}
			});
			
			$("#periode_track_type").prop('disabled',true);
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


function active(sts, id, txt)
{
	if(confirm('Data: '+txt+'\nApakah anda yakin akan '+(sts == 1 ? 'Aktifkan' : 'Non Aktifkan')+' data tersebut ?')) {
		$.ajax({
			url:baseurl+'/active',
			global:false,
			async:true,
			type:'post',
			dataType:'json',
			data: ({id : id, sts : sts}),
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