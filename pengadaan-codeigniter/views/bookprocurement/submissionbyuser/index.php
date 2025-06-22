<div class="panel panel-default flat">   
	<div class="row" style="margin:20px 15px;">	
		
		<div class="col-md-4"> 
			<div class="form-group"> 
				<?= form_dropdown('status', $status, '', 'class="form-control select2" id="status" required="required"') ?>
			</div>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				<select name="created_date_option" id="created_date_option" class="select"> 
						<option value="all">Semua Tanggal Pembuatan</option>
						<option value="date">Range Tanggal Pembuatan</option>
				</select> 
			</div>
		</div> 
		<div class="col-sm-2" >
			<input type="text" class="form-control input-sm" name="created_date" id="created_date" value="">
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
			<th width="9%">Aksi</th>
			<th width="9%">Status</th>
			<th width="9%">Alasan Ditolak</th> 
			<th width="9%">File Approval Kaprodi</th>
			<th width="9%">File RPS</th>
			<th width="9%">Tanggal</th>
			<th width="9%">Fakultas</th>
			<th width="9%">Prodi</th>
			<th width="9%">NIK</th>
			<th width="9%">Nama</th>
			<th width="9%">Judul</th>
			<th width="9%">Pengarang</th>
			<th width="9%">Penerbit</th>
			<th width="9%">Tahun terbit</th>
			<th width="9%">Matakuliah</th>   
			<th width="9%">Reference</th>   
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
            	<h4 class="modal-title"><i class="fa fa-navicon"></i> &nbsp;<?= 'Form Data Pengajuan Buku' ?></h4>
            </div>
            <form id="frm" class="form-horizontal">
                <input type="hidden" name="id" id="id">
                <input type="hidden" name="sub_id" id="sub_id">
                <div class="modal-body">
                    <div class="box-body" style="padding-bottom:0px">  
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Prodi</label>
                            <div class="col-sm-9">
									<?= form_dropdown('inp[bp_prodi_id]', $prodi_input, '', 'class="form-control select2" id="bp_prodi_id" required="required"') ?>
                            </div>
                        </div>  
						<hr>
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Matakuliah</label>
                            <div class="col-sm-9"> 
                            	<input type="text" class="form-control input-sm" name="inp[bp_matakuliah][0]" id="bp_matakuliah" required>
                            </div>
                        </div>   
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Judul Buku</label>
                            <div class="col-sm-9"> 
                            	<input type="text" class="form-control input-sm" name="inp[bp_title][0]" id="bp_title" required>
                            </div>
                        </div>  
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Pengarang</label>
                            <div class="col-sm-9"> 
                            	<input type="text" class="form-control input-sm" name="inp[bp_author][0]" id="bp_author" required>
                            </div>
                        </div>  
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Penerbit</label>
                            <div class="col-sm-9"> 
                            	<input type="text" class="form-control input-sm" name="inp[bp_publisher][0]" id="bp_publisher" required>
                            </div>
                        </div>  
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tahun Terbit</label>
                            <div class="col-sm-9"> 
                            	<input type="text" class="form-control input-sm" name="inp[bp_publishedyear][0]" id="bp_publishedyear" required>
                            </div>
                        </div>   
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Reference</label>
                            <div class="col-sm-9">
								<div class="col-md p-4">
									<div class="form-check mt-3">
										<input name="inp[bp_reference][0]" class="bp_reference" type="radio" value="Utama" id="bp_reference" required>
										<label class="form-check-label" for="bp_reference">
										Utama
										</label>
									</div>
									<div class="form-check">
									<input name="inp[bp_reference][0]" class="bp_reference" type="radio" value="Pendukung" id="bp_reference" required>
										<label class="form-check-label" for="bp_reference">
										Pendukung
										</label>
									</div>
								</div>
                            </div>
                        </div>  
						<hr>
						<div id="dynamic_field">
							<!-- Dynamic fields will be added here -->
						</div>
						<div class="form-group form-group-sm">
							<button type="button" name="add" id="add" class="btn btn-success">Add More</button>
							<button type="button" name="remove" id="remove" class="btn btn-danger">Remove</button>
						</div> 
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Upload File Appoval Kaprodi</label>
							<div class="col-sm-9"> 
								<input type="file" class="form-control input-sm" name="approval" id="approval" >
							</div>   
                       	</div>
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Upload File RPS</label>
							<div class="col-sm-9"> 
								<input type="file" class="form-control input-sm" name="rps" id="rps" >
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
 

<div class="modal fade" id="modal_approve" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger-700" style="background-image:url(assets/images/backgrounds/bg.png)">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            	<h4 class="modal-title"><i class="fa fa-navicon"></i> &nbsp;<?= 'Approved' ?></h4>
            </div>
            <form id="form" class="form-horizontal">
			<input type="hidden" name="id" id="id"> 
			<input type="hidden" name="inp[bp_status]" id="bp_status">
                <div class="modal-body"> 
							 
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-labeled btn-xs pull-left" data-dismiss="modal">
                	<b><i class="icon-switch"></i></b> Batal
                </button>                
                <button type="button" class="btn btn-success btn-labeled btn-xs" id="act-save" onclick="approval()">
                	<b><i class="icon-floppy-disk"></i></b> OK
                </button> 
                </div>                  
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
 


<div class="modal fade" id="modal_not_approved" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		<div class="modal-header bg-danger-700" style="background-image:url(assets/images/backgrounds/bg.png)">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            	<h4 class="modal-title"><i class="fa fa-navicon"></i> &nbsp;<?= 'Not Approved' ?></h4>
            </div>
			<form id="form" class="form-horizontal"> 
			<input type="hidden" name="id" id="id"> 
			<input type="hidden" name="inp[bp_status]" id="bp_status">
			<div class="modal-body">
				<div class="form-group">
					<label class="col-sm-3 control-label"></label>
                    <span class="col-sm-9 control-label"><?php echo "Apakah anda yakin akan mengubah status menjadi " ?> <strong>Not Approved</strong> ?</span>
                </div>
				<div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo "Alasan"; ?> <span class="required-class">*) </span></label>
                    <div >
						<select name="option_reason" id="option_reason" class="form-control" required>
							<option value="">Pilih Alasan</option>
							<option value="Judul Buku sudah ada di Open Library">Judul Buku sudah ada di Open Library</option>
							<option value="Judul buku tidak ditemukan di penerbit manapun">Judul buku tidak ditemukan di penerbit manapun</option>
							<option value="lainnya">Lainnya</option>
						</select> 
						<div id="option_lain" style="display:none">
                       	 	<textarea class="form-control" name="bp_reason" id="bp_reason"></textarea>
						</div>
                    </div>
                </div>
			</div>
			<div class="modal-footer">
                <button type="button" class="btn btn-danger btn-labeled btn-xs pull-left" data-dismiss="modal">
                	<b><i class="icon-switch"></i></b> Batal
                </button>                
                <button type="button" class="btn btn-success btn-labeled btn-xs" id="act-save" onclick="save_not_approved()">
                	<b><i class="icon-floppy-disk"></i></b> OK
                </button> 
			</div>  
			</form> 
		</div>
	</div>
</div>
 
 

<?php $this->load->view('frontend/tpl_footer'); ?>
<!-- Theme JS files -->	
<script type="text/javascript" src="assets/limitless/global/js/plugins/pickers/datepicker.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/core/setting.js"></script> 
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/styling/uniform.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>

<script type="text/javascript">
var tb 		= '#table';      
var baseurl = 'index.php/<?= y_url_apps('bookprocurement_url') ?>/submissionbyuser';
var date = '<?= date('d F Y').' '.date('H:i:s'); ?>';

$(document).ready(function() {	  
	
	var i = 0; // Initial field counter

$('#add').click(function(){
	i++; // Increment field counter
	var newFields = `<div id="row${i}" class="dynamic-added">
                        <hr>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-3 control-label">Matakuliah</label>
                            <div class="col-sm-9"> 
                                <input type="text" class="form-control input-sm" name="inp[bp_matakuliah][${i}]" required>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-3 control-label">Judul Buku</label>
                            <div class="col-sm-9"> 
                                <input type="text" class="form-control input-sm" name="inp[bp_title][${i}]" required>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-3 control-label">Pengarang</label>
                            <div class="col-sm-9"> 
                                <input type="text" class="form-control input-sm" name="inp[bp_author][${i}]" required>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-3 control-label">Penerbit</label>
                            <div class="col-sm-9"> 
                                <input type="text" class="form-control input-sm" name="inp[bp_publisher][${i}]" required>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-3 control-label">Tahun Terbit</label>
                            <div class="col-sm-9"> 
                                <input type="text" class="form-control input-sm" name="inp[bp_publishedyear][${i}]" required>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label class="col-sm-3 control-label">Reference</label>
                            <div class="col-sm-9">
                                <div class="form-check">
                                    <input name="inp[bp_reference][${i}]" class="bp_reference" type="radio" value="Utama" id="bp_reference_utama_${i}" required>
                                    <label class="form-check-label" for="bp_reference_utama_${i}">
                                    Utama
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input name="inp[bp_reference][${i}]" class="bp_reference" type="radio" value="Pendukung" id="bp_reference_pendukung_${i}" required>
                                    <label class="form-check-label" for="bp_reference_pendukung_${i}">
                                    Pendukung
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>`;
	$('#dynamic_field').append(newFields);
});

$(document).on('click', '#remove', function(){
	if(i > 1) { // Ensure at least one field remains
		$('#row'+i).remove();
		i--; // Decrement field counter
	}
});


 // Function to check references and update file input requirement
 
 
  
$(tb).dataTable({
	'ajax': {
					'url':baseurl+'/json', 
			'data' : function(data) {
				data.prodi		= $('#prodi').val();
				data.created_date			= $('#created_date').val(); 
				data.created_date_option		= $('#created_date_option').val(); 
				data.status		= $('#status').val();  
			}
	}, 
	'order':[ 
		[2, 'desc'],[4, 'asc']
	],
	'columnDefs': [ 
		{ 'targets': 'nosort', 'searchable': false, 'orderable': false },
		{ 'targets': 'center', 'className': 'center' } 
	],  
	dom: 'Blfrtip',				
	buttons: [{
		text: '<i class="icon-file-excel position-left"></i>Export Excel',
		extend: 'excel',
		className: 'btn btn-sm btn-success',
		title: 'Data Pengajuan Buku',
		filename: 'Data Pengajuan Buku',
		messageTop: 'Data Pengajuan Buku - Per tanggal cetak: '+date
	}],
	"drawCallback": function( settings ) {
		$(".chk-logistic").uniform({
			radioClass: 'choice',
			wrapperClass: 'border-primary-600 text-primary-800'
		});
	},
	'scrollX': true
 });

 
	
	$('.dt-buttons').append('<button class="btn btn-sm btn-primary" onclick="add()"><i class="icon-add"></i> &nbsp;Tambah Data</button>');
	
	$('.dataTables_filter input[type=search]').attr('placeholder','Pencarian');

	$('.dataTables_length select').select2({
		minimumResultsForSearch: Infinity,
		width: 'auto'
	});

	$("#book_id_prodi,#created_date_option").select2({
		minimumResultsForSearch: Infinity
	}); 


	$('#created_date').hide(); 

	$("#filter").click(function(){ 
		_reload();
	}); 
	



	$("#created_date_option").change(function(){ 
		if($(this).val()=='all') $("#created_date").hide(); 
		else $("#created_date").show(); 
	});  


	$('#created_date').daterangepicker({  
		locale: {
			format: 'DD-MM-YYYY'
		},
		showDropdowns: true, 
		opens: 'left',
		applyClass: 'bg-primary-600',
		cancelClass: 'btn-light'
	});

});

function updateFileInputRequirement() {
	let utamaSelected = false; 
	let approvalUploaded = $('#approval').val() !== ''; // Checks if "Approval Kaprodi" file is selected
    let rpsUploaded = $('#rps').val() !== ''; // Checks if "RPS" file is selected
        
	
	// Iterate over all reference radio buttons to check if any 'Utama' is selected
	$('.bp_reference').each(function() {
		if ($(this).is(':checked') && $(this).val() === 'Utama') {
			utamaSelected = true;
			return false; // Break the loop
		}
	});
	
	// Set the 'required' attribute on the file inputs based on utamaSelected flag
	if (utamaSelected && !approvalUploaded && !rpsUploaded) {
		
		return false;
	}  
	else return true;
} 
 
function add()
{
_reset();
$('#act-save').show();
$('#act-update').hide();
$('#frmbox').modal({keyboard: false, backdrop: 'static'});
}

 
 

function save(url)
{
	if($("#frm").valid())
	{


		if(updateFileInputRequirement()){
			var formData = new FormData($('#frm')[0]);
			$.ajax({
				url:baseurl+'/save_upload',
				global:false,
				async:false,
				type:'post',
				data: formData,
				dataType: 'json',
				contentType: false,
				processData: false, 
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
		else {
			alert('Silahkan Upload File Approval Kaprodi atau Upload File RPS');
		}
	}
}

 


function save_upload()
{
if($("#frm4").valid())
{

	var formData = new FormData($('#frm4')[0]);
	$.ajax({
		url:baseurl+'/save_upload',
		global:false,
		async:false,
		type:'post',
		data: formData,
		dataType: 'json',
		contentType: false,
		processData: false, 
		success : function(e) {
			if(e.status == 'ok;') 
			{
				_reload();
				$("#frmbox4").modal('hide');
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
$('#book_id_prodi').val('').trigger('change');
validator.resetForm();
$("label.error").hide();
 $(".error").removeClass("error");
$('#frm')[0].reset();
}

function _reload()
{
$(tb).dataTable().fnDraw();
} 


  
function edit(id,data) { 
	$('#modal_approve').modal({keyboard: false, backdrop: 'static'}); 
	$('#modal_approve #id').val(id); 
	$('#modal_approve #bp_status').val(data); 	
	$('#modal_approve .modal-body').html('<?php echo "Apakah anda yakin akan mengubah status menjadi " ?> <strong>'+data+'</strong> ?'); 
}  


function approval(status) { 
	var form = $("#modal_approve #form"); 

	$.ajax({
		url:baseurl+'/update', 
		type: "POST",
		data: form.serialize(),
		dataType: "JSON",
		beforeSend : function() {
			$('#loading-img').show();
		},
		complete : function() {
			$('#loading-img').hide();
		},
		success: function(data)
		{
			$('#modal_approve').modal('hide');
			_reload();
		},
		error : function() {
			alert('<?= $this->config->item('alert_error') ?>');	 
		},
	});
}  


function not_approved(id,data) { 
	$('#modal_not_approved').modal({keyboard: false, backdrop: 'static'}); 
	$('#modal_not_approved #id').val(id);  
	$('#modal_not_approved #bp_status').val(data); 
	//$('#modal_not_approved .modal-body').html('<?php echo "Apakah anda yakin akan mengubah status menjadi " ?> <strong>'+data+'</strong> ?'); 

	$("#modal_not_approved #option_reason").change(function(){
		if($(this).val()=='lainnya'){
			$("#modal_not_approved #option_lain").show();
			$("#modal_not_approved #bp_reason").prop('required',true);
		}
		else { 
			$("#modal_not_approved #option_lain").hide();
			$("#modal_not_approved #bp_reason").prop('required',false);
		}
	});
} 


function save_not_approved(status) { 
	var form = $("#modal_not_approved #form");  
	if ($('#modal_not_approved #option_reason').select2().val()!="") {
		$.ajax({
			url:baseurl+'/not_approved',  
			type: "POST",
			data: form.serialize(),
			dataType: "JSON", 
			beforeSend : function() {
				$('#loading-img').show();
			},
			complete : function() {
				$('#loading-img').hide();
			},
			success: function(data)
			{
				$('#modal_not_approved').modal('hide');
				_reload();
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				alert('<?= $this->config->item('alert_error') ?>');	 
			}
		});
	}
	else alert("Silahkan Pilih Alasan");
}

</script>