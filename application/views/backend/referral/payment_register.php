<div class="panel panel-default flat">
    <table class="table table-bordered table-striped table-hover" id="table">
        <thead>
            <tr>
                <th width="10%">No. Transaksi</th>
                <th width="10%">No Peserta</th>
                <th width="15%">Nama Lengkap</th>
                <th width="15%">Asal Sekolah</th> 
                <th width="10%">No. Telp</th>
                <th width="15%">Status</th>
                <th width="15%">Komisi</th>
                <th width="10%">Status Komisi</th> 
                <th width="10%">Action</th> 
            </tr>
        </thead>
        <tbody></tbody>
    </table>
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
            'url':baseurl+'/json_payment_register',
            "data" : {
                "id" : "<?=$id?>", 
            }
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


function active(sts, id, txt)
{
	if(confirm('Data: '+txt+'\nApakah anda yakin akan merubah status menjadi '+(sts == 1 ? 'Sudah Ditransfer' : 'Belum Ditransfer')+' data tersebut ?')) {
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



function _reload()
{
	$(tb).dataTable().fnDraw();
}
	 
</script>