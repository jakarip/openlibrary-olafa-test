<div class="panel panel-default flat">
    <table class="table table-bordered table-striped table-hover" id="table">
        <thead>
            <tr> 
                <th width="20%">Nama</th>
                <th width="20%">Email</th>
                <th width="20%">No Telp</th>
                <th width="20%">Institusi</th>
                <th width="20%">Tanggal Masuk</th> 
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div> 

<?php $this->load->view('frontend/tpl_footer'); ?>
<!-- Theme JS files -->	
<script type="text/javascript" src="assets/limitless/global/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/core/setting.js"></script> 
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>

<script type="text/javascript">
var tb 		= '#table';      
var baseurl = 'guest';

$(document).ready(function() {	
    $(tb).dataTable({
        'ajax': {
            'url':baseurl+'/json'
		},
		'order':[
			[0, 'desc'],[1, 'asc']
		],
		'columnDefs': [ 
			{ 'targets': [-1,-2], 'searchable': false, 'orderable': false, 'className': 'center' } 
		],
		dom: 'Blfrtip',				
        buttons: [
        {
			text: '<i class="icon-file-excel position-left"></i>Export Excel',
			extend: 'excel',
			className: 'btn btn-sm btn-success',
			filename: 'Data Buku Tamu',
			title: 'Digilib',
			messageTop: 'Data Buku Tamu',
        }
		],	
    });
	
	$('.dataTables_filter input[type=search]').attr('placeholder','Pencarian');

    $('.dataTables_length select').select2({
        minimumResultsForSearch: Infinity,
        width: 'auto'
    }); 
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
				if(key != 'component_custom')
					$('#'+key).val(value);
			});
			
			if(e.component_custom == 1)
				$('#component_custom').prop('checked', true);
			
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

function active(id, txt, mode)
{
	if(confirm('Data: '+txt+'\nApakah anda yakin akan '+(mode == 1 ? 'Aktifkan' : 'Non Aktifkan')+' data tersebut ?')) {
		$.ajax({
			url:baseurl+'/active',
			global:false,
			async:true,
			type:'post',
			dataType:'json',
			data: ({id : id, mode : mode }),
			success: function(e) { 
				if(e.status == 'ok;') 
				{
					_reload('#table_course');
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