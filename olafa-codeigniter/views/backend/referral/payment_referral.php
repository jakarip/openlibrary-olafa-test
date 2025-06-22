<div class="panel panel-default flat">
    <table class="table table-bordered table-striped table-hover" id="table">
        <thead>
            <tr>
                <th width="25%">Nama Lengkap</th>
                <th width="15%">Username</th>
                <th width="15%">Email</th>
                <th width="15%">No. Telp</th>
                <th width="15%">Jumlah Pendaftar</th>
                <th width="15%">Total Komisi</th>
                <th width="15%">Action</th>
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
            'url':baseurl+'/json_payment_referral'
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
	 
</script>