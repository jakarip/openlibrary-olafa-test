<style>
.status-text {
	font-weight:bold
}
.datatable-header {
	padding:20px;
}
</style>
<div class="panel panel-default flat">
	<div class="panel-heading steps-basic wizard clearfix">
    	<div class="steps clearfix">
        	<ul role="tablist">
            	<li role="tab" class="first done" aria-disabled="false" aria-selected="true">
                	<a href="javascript:;" aria-controls="steps-uid-0-p-0"><span class="number">1</span> Download File Simulasi</a>
                </li>
                <li role="tab" class="current" aria-disabled="false">
                	<a href="javascript:;" aria-controls="steps-uid-0-p-1"><span class="number">2</span> Upload File Hasil Simulasi</a>
                </li>
                <li role="tab" class="disabled" aria-disabled="true">
                	<a href="javascript:;" aria-controls="steps-uid-0-p-2"><span class="number">3</span> Simpan Hasil Simulasi</a>
                </li>
    		</ul>
     	</div>
    </div>
    <div class="panel-body">
    	<form id="frm" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-10 col-sm-6">
                    <div class="form-group">
                        <input type="file" class="file-styled" name="xls_file" id="xls_file">
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="form-group">
                        <button class="btn btn-danger btn-sm" type="button" onClick="upload_excel()"><i class="icon-upload position-left"></i> Upload File</button>
                    </div>                
                </div>
            </div>
        </form>
        <br>
        <p align="justify">Jika <strong>Preview</strong> hasil upload sudah sesuai dengan file yang anda upload untuk menyimpan tekan tombol <strong>Simpan dan Lanjutkan</strong> pada bawah tabel. Jika terdapat kesalahan atau kurang sesuai, anda dapat mengulangi proses upload file.</p>
    </div>
	<form id="frm2" method="post" action="<?= y_url_admin() ?>/simulation_utbk/step3" onSubmit="return confirm('Apakah anda yakin untuk melanjutkan proses ini. Silahkan cek kembali hasil upload data. Proses tidak dapat di ulang.')">
    <table class="table table-bordered table-striped table-hover table-xs" id="table">
        <thead>
            <tr>
                <th class="nosort center" width="5%">ID</th>
                <th width="10%">Status</th>
                <th width="25%">Nama</th> 
                <th class="nosort center" width="10%">Lulus</th>
                <th class="nosort" width="14%">Lulus Prodi</th> 
                <th class="nosort center" width="8%">Rata-Rata Nilai</th>
                <th class="nosort center" width="8%">Beasiswa</th>
            </tr>
        </thead>
        <tbody>
        	
        </tbody>
    </table>
    <div class="panel-footer"> 
    	<div class="text-center">
    		<input type="hidden" name="identifier" value="1">
            <button class="btn btn-danger btn-xs heading-btn" type="button" onClick="cancel()"><i class="icon-cancel-circle2 position-left"></i> Cancel</button>
            <button class="btn btn-primary btn-xs heading-btn" type="submit">Lanjutkan<i class="icon-arrow-right13 position-right"></i></button>
    	</div>
    </div>
	</form>
</div>

<?php $this->load->view('backend/tpl_footer'); ?> 


<script type="text/javascript" src="assets/limitless/global/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/styling/uniform.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/core/setting.js"></script> 

<script type="text/javascript">
var tb 		= '#table';      
var baseurl = '<?= y_url_admin() ?>/simulation_utbk';

$(document).ready(function() {	
    $(tb).dataTable({
		'dom': '<"datatable-header"<"dt-status">><"datatable-scroll"t><"datatable-footer"ip>',
        'ajax': {
            'url':baseurl+'/json'
		},
		'order':[
			[1, 'asc']
		],
		'columnDefs': [ 
			{ 'targets': 'nosort', 'searchable': false, 'orderable': false },
			{ 'targets': 'center', 'className': 'center' }  
		],
		"bStateSave": true,
		"pageLength": -1,
		"autoWidth": false,
		
		"searching": false,
		"lengthChange": false,
		"paging": false,
		
		'scrollX': true,
		"language": {
			"zeroRecords": "Preview Hasil Upload. Upload File Simulasi Kelulusan Terlebih Dahulu"
		},
		'drawCallback': function( settings ) {
			var api = this.api();
	 		var row = api.rows( {page:'current'} ).data();
			
			var success = 0;
			var failed = 0;
			
			$.each(row, function(index, value) {
				var sts = $($.parseHTML(value[1])[0]).val();
				if(sts == 1)
					success += 1;
				else
					failed += 1;
			});
			
			var total = parseInt(success) + parseInt(failed);
			
			$('.dt-status').html('<span id="status-total" class="status-text">'+total+'</span> Total Data; <span id="status-sukses" class="status-text text-success">'+success+'</span> Sukses; <span id="status-error" class="status-text text-danger">'+failed+'</span> Error');
		}
    });
	
	$('.dataTables_filter input[type=search]').attr('placeholder','Pencarian');

    $('.dataTables_length select').select2({
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });
	
	$('body').on('keypress', '.input-table', function(e) {
		if(e.which == 13) 
		{
			var id = $(this).attr('id').replace('sch-id-', '');
			var score = $(this).val();
			var score_avg = $(this).data('score-avg');
			
			update_school_score(id, score, score_avg);
		}
	});
	
	$('#sim-count-persen').keypress(function(e) {
		if(e.which == 13) 
		{
			simulation_utbk('persen', $(this).val());
		}
	});
	
	$('#sim-count-real').keypress(function(e) {
		if(e.which == 13) 
		{
			simulation_utbk('real', $(this).val());
		}
	});
	
	$(".file-styled").uniform({
		fileButtonClass: 'action btn btn-default'
	});
	
	$('#frm2').submit(pre_save);
});

function upload_excel()
{
	if($("#xls_file").val() == '')
	{
		alert('Anda belum memilih File Excel Hasil Simulasi');
		return false;
	}
	
	var formData = new FormData($('#frm')[0]);
	$.ajax({
		url:baseurl+'/step2_upload_excel',
		global:false,
		async:false,
		type:'post',
		data: formData,
		dataType: 'json',
		contentType: false,
		processData: false,
		success : function(e) {
			if(e.status) 
			{
				alert('Upload File Hasil Simulasi Sukses');
				$(tb).dataTable().fnDraw();
				$('#frm')[0].reset();
				$.uniform.update();
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

function pre_save()
{
	var total = $('#status-total').text();
	
	if(total <= 0)
	{
		alert('Upload File Excel Hasil Simulasi Terlebih Dahulu');
		return false;	
	}
	
	var err = $('#status-error').text();
	if(err > 0)
	{
		if(confirm('Terdapat '+err+' Data Error.\nSistem akan mengabaikan data error.\nTekan Yes jika ingin tetap memproses ?')) {
			return true;
		}
		else {
			return false;
		}
	}
	else
	{
		return true;
	}
}

/*function save()
{
	$.ajax({
		url:baseurl+'/save_from_temp',
		global:false,
		async:false,
		type:'post',
		dataType: 'json',
		success : function(e) {
			if(e.status) 
			{
				alert('Simpan Data Sukses');
				window.location.href=baseurl+'/save_from_temp';
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
}*/

function cancel()
{
	var total = $('#status-total').text();
	
	if(total > 0)
	{
		if(confirm('Apakah anda yakin akan membatalkan proses ini ?')) {
		$.ajax({
			url:baseurl+'/step2_cancel',
			global:false,
			async:false,
			type:'post',
			dataType: 'json',
			success : function(e) {
				if(e.status) 
				{
					window.location.href=baseurl;
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
	else
	{
		window.location.href=baseurl;
	}

}
</script>
