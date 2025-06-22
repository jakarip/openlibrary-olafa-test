<div class="panel panel-default flat">
	<div class="panel-heading">
		 <a type="button" class="btn btn-danger btn-labeled btn-xs" href='<?= y_url_admin() ?>/ms_questionnaire'>
			<b><i class="icon-backward2"></i></b> Kembali</a>
		</a> 
	</div>
    <table class="table table-bordered table-striped table-hover" id="table">
        <thead>
            <tr>
                <th width="10%">Urutan Pertanyaan</th> 
                <th width="45%">Pertanyaan</th> 
                <th width="10%">Jenis</th> 
                <th width="10%">Total Pilihan Jawaban Aktif</th>  
                <th width="10%">Status</th>  
                <th width="15%">&nbsp;</th>
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
                            <label for="pus_name" class="col-sm-3 control-label">Nama Kuesioner</label>
                            <div class="col-sm-9">
                            	<input type="text" class="form-control input-sm" name="inp[q_name]" id="q_name" required>
                            </div>
                        </div> 
                    </div><!-- /.box-body -->
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-labeled btn-xs pull-left" data-dismiss="modal">
                	<b><i class="icon-switch"></i></b> Batal
                </button>                
                <button type="button" class="btn btn-success btn-labeled btn-xs" id="act-save" onclick="save('insert_detail')">
                	<b><i class="icon-floppy-disk"></i></b> Simpan
                </button>
        		<button type="button" class="btn btn-success btn-labeled btn-xs" id="act-update" onclick="save('update_detail')">
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
var baseurl = '<?= y_url_admin() ?>/ms_questionnaire';

$(document).ready(function() {	
    $(tb).dataTable({
        'ajax': {
            'url':baseurl+'/json_detail/<?= $id ?>'
		}, 
		'columnDefs': [ 
			{ 'targets': [0,3], 'searchable': false, 'orderable': false },
			{ 'targets': [1,2,4,5], 'orderable': false } 
		]
    });
	
	$('.dataTables_filter input[type=search]').attr('placeholder','Pencarian');

    $('.dataTables_length select').select2({
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });
	
	$('.dt-buttons').html('<button class="btn btn-sm btn-primary" onclick="add(\'<?= $id ?>\')"><i class="icon-add"></i> &nbsp;Tambah Data</button>'); 
});
	
function add(q_id)
{ 
	window.location.href='<?= y_url_admin() ?>/ms_questionnaire/option/'+q_id;
}	

function edit(q_id,qq_id)
{ 
	window.location.href='<?= y_url_admin() ?>/ms_questionnaire/option/'+q_id+'/'+qq_id;
} 

function active(sts, id, txt)
{
	if(confirm('Data: '+txt+'\nApakah anda yakin akan '+(sts == 1 ? 'Aktifkan' : 'Non Aktifkan')+' data tersebut ?')) {
		$.ajax({
			url:baseurl+'/active_detail',
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