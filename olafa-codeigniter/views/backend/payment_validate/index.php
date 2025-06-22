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
	</div> 
    <table class="table table-bordered table-striped table-hover table-xs" id="table">
        <thead>
            <tr> 
                <th width="10%" class="nosort center">Validasi</th> 
                <th width="15%">No. Transaksi</th>
                <th width="15%">Periode</th>
                <th width="15%">Nama</th>
                <th width="20%">Tanggal Pembayaran</th>
                <th width="20%">Bukti Pembayaran</th> 
                <th width="20%">Status</th>
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
var baseurl = '<?= y_url_admin() ?>/payment_validate';

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

	$('.dt-buttons').html('');
});
	 
function validate(pin,sreg_id,status)
{		 
	if(confirm('Apakah anda yakin akan '+status+' pendaftar ini ?'))
	{
		$.ajax({
			url:baseurl+'/validate',
			global:false,
			async:true,
			dataType:'json',
			type:'post',
			data: ({ pin : pin, sreg_id : sreg_id, status : status }),
			success: function(e) {  
				alert('Kirim Email Berhasil');
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

function _reload()
{
	$(tb).dataTable().fnDraw();
}
 
 
</script>