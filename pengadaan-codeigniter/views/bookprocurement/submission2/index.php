<div class="panel panel-default flat">
	<div class="row" style="margin:20px 15px;">	
		<div class="col-md-4"> 
			<div class="form-group"> 
				<?= form_dropdown('prodi', $prodi, '', 'class="form-control select2" id="prodi" required="required"') ?>
			</div>
		</div> 
		<div class="col-md-4"> 
			<div class="form-group"> 
				<?= form_dropdown('status', $status, '', 'class="form-control select2" id="status" required="required"') ?>
			</div>
		</div>
		<div class="col-md-4"> 
			<div class="form-group"> 
				<?= form_dropdown('type', $type, '', 'class="form-control select2" id="type" required="required"') ?>
			</div>
		</div>
	</div>
	<div class="row" style="margin:20px 15px;">	
		<div class="col-md-2">
			<div class="form-group">
				<select name="dates_submission_option" id="dates_submission_option" class="select"> 
						<option value="all">Semua Tanggal Terima Pengajuan</option>
						<option value="date">Range Tanggal Terima Pengajuan</option>
				</select> 
			</div>
		</div> 
		<div class="col-sm-2" >
			<input type="text" class="form-control input-sm" name="dates_submission" id="dates_submission" value="">
		</div> 
		<div class="col-md-2">
			<div class="form-group">
				<select name="dates_logistic_option" id="dates_logistic_option" class="select"> 
						<option value="all">Semua Tanggal Pengajuan Logistik</option>
						<option value="date">Range Tanggal Pengajuan Logistik</option>
				</select> 
			</div>
		</div> 
		<div class="col-sm-2" >
			<input type="text" class="form-control input-sm" name="dates_logistic" id="dates_logistic" value="">
		</div> 
		<div class="col-md-2">
			<div class="form-group">
				<select name="dates_acceptance_option" id="dates_acceptance_option" class="select"> 
						<option value="all">Semua Tanggal Penerimaan Buku</option>
						<option value="date">Range Tanggal Penerimaan Buku</option>
				</select> 
			</div>
		</div> 
		<div class="col-sm-2" >
			<input type="text" class="form-control input-sm" name="dates_acceptance" id="dates_acceptance" value="">
		</div>  
	</div> 
	
	<div class="row" style="margin:20px 15px;">	
		<div class="col-md-2">
			<div class="form-group">
				<select name="dates_email_confirmed_option" id="dates_email_confirmed_option" class="select"> 
						<option value="all">Semua Tanggal Konfirmasi Email</option>
						<option value="date">Range Tanggal Konfirmasi Email</option>
				</select> 
			</div>
		</div> 
		<div class="col-sm-2" >
			<input type="text" class="form-control input-sm" name="dates_email_confirmed" id="dates_email_confirmed" value="">
		</div> 
		<div class="col-md-2">
			<div class="form-group">
				<select name="dates_available_option" id="dates_available_option" class="select"> 
						<option value="all">Semua Tanggal Ketersediaan Buku</option>
						<option value="date">Range Tanggal Ketersediaan Buku</option>
				</select> 
			</div>
		</div> 
		<div class="col-sm-2" >
			<input type="text" class="form-control input-sm" name="dates_available" id="dates_available" value="">
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
                <th class="nosort">Check All <br>Pengajuan ke Logistik<br><input type="checkbox" id="chk-all-logistic"></th>
                <th class="nosort">Action</th> 
                <th>Status</th>
                <th>Nama Prodi</th>
                <th>Pemohon</th>
                <th>Mata Kuliah</th>
								<th>Jenis Buku</th>
								<th>Judul Buku</th>
                <th>Pengarang</th> 
                <th>Penerbit</th> 
								<th>Tahun Terbit</th> 
								<th>Tanggal Terima Pengajuan dari Prodi</th> 
								<th>Tanggal Pengajuan ke Logistik</th> 
								<th>Nomor E-Memo Pengajuan ke Logistik</th> 
								<th class="nosort">Waktu Proses Pengajuan</th> 
								<th>Tanggal Proses Logistik</th> 
								<th>Tanggal Penerimaan Buku</th> 
								<th class="nosort">Waktu Proses Pengadaan</th> 
								<th>Harga Pengadaan</th>  
								<th>Jumlah Harga</th> 
								<th>Jumlah Buku</th>  
								<th class="nosort">Waktu Proses Konfirmasi Email</th> 
								<th>Tanggal Konfirmasi Email</th> 
								<th class="nosort">Waktu Proses Ketersediaan Buku</th> 
								<th>Tanggal Ketersediaan Buku</th> 
								<th>No. Katalog</th> 
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
															<?= form_dropdown('inp[book_id_prodi]', $prodi_input, '', 'class="form-control select2" id="book_id_prodi" required="required"') ?>
                            </div>
                        </div> 
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Pemohon</label>
                            <div class="col-sm-9">  
                            	<input type="text" class="form-control input-sm" name="inp[book_member]" id="book_member" required>
                            </div>
                        </div> 
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Matakuliah</label>
                            <div class="col-sm-9"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_subject]" id="book_subject" required>
                            </div>
                        </div>   
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Judul Buku</label>
                            <div class="col-sm-9"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_title]" id="book_title" required>
                            </div>
                        </div>  
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Pengarang</label>
                            <div class="col-sm-9"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_author]" id="book_author" required>
                            </div>
                        </div>  
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Penerbit</label>
                            <div class="col-sm-9"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_publisher]" id="book_publisher" required>
                            </div>
                        </div>  
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tahun Terbit</label>
                            <div class="col-sm-9"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_published_year]" id="book_published_year" required>
                            </div>
                        </div>  
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Terima Pengajuan Prodi</label>
                            <div class="col-sm-9"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_date_prodi_submission]" id="book_date_prodi_submission" required>
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


<div class="modal fade" id="frmbox2" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger-700" style="background-image:url(assets/images/backgrounds/bg.png)">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            	<h4 class="modal-title"><i class="fa fa-navicon"></i> &nbsp;<?= 'Form Data Pengajuan ke Logistik' ?></h4>
            </div>
            <form id="frm2" class="form-horizontal">
                <input type="hidden" name="id" id="id"> 
                <input type="hidden" name="type" id="type"> 
                <div class="modal-body">
                    <div class="box-body" style="padding-bottom:0px">  
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">List Buku</label>
                            <div class="col-sm-9"> 
								<textarea id="list" cols='60' rows="20"></textarea>
                            </div>
                        </div>  
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Nomor E-Memo Pengajuan ke Logistik</label>
                            <div class="col-sm-9"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_memo_logistic_number]" id="book_memo_logistic_number" required>
                            </div>
                        </div>  
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Pengajuan ke Logistik</label>
                            <div class="col-sm-9"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_date_logistic_submission]" id="book_date_logistic_submission" required>
                            </div>
                        </div>  
                    </div><!-- /.box-body -->
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-labeled btn-xs pull-left" data-dismiss="modal">
                	<b><i class="icon-switch"></i></b> Batal
                </button>                
                <button type="button" class="btn btn-success btn-labeled btn-xs" id="act-save" onclick="save_logistic()">
                	<b><i class="icon-floppy-disk"></i></b> Simpan
                </button> 
                </div>                  
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal fade" id="frmbox3" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger-700" style="background-image:url(assets/images/backgrounds/bg.png)">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            	<h4 class="modal-title"><i class="fa fa-navicon"></i> &nbsp;<?= 'Form Data Penerimaan Buku' ?></h4>
            </div>
            <form id="frm3" class="form-horizontal">
                <input type="hidden" name="id" id="id"> 
                <div class="modal-body">
                    <div class="box-body" style="padding-bottom:0px">  
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Nama Buku</label>
                            <div class="col-sm-9" id="list"> 
															<input type="text" class="form-control input-sm" name="inp[book_title]" id="book_title" readonly>
                            </div>
                        </div>  
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Jenis Buku</label>
                            <div class="col-sm-9"> 
															<?= form_dropdown('inp[book_type]', $book_type, '', 'class="form-control select2" id="book_type" required="required"') ?>
                            </div>
                        </div>  
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Penerimaan Buku</label>
                            <div class="col-sm-9"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_date_acceptance]" id="book_date_acceptance" required>
                            </div>
                        </div>  
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Harga Pengadaan</label>
                            <div class="col-sm-9"> 
														<input type="text" class="form-control input-sm" name="inp[book_procurement_price]" id="book_procurement_price" required>
                            </div>
                        </div>   
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Jumlah Buku</label>
                            <div class="col-sm-9"> 
														<input type="text" class="form-control input-sm" name="inp[book_copy]" id="book_copy" required>
                            </div>
                        </div>  
                    </div><!-- /.box-body -->
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-labeled btn-xs pull-left" data-dismiss="modal">
                	<b><i class="icon-switch"></i></b> Batal
                </button>                
                <button type="button" class="btn btn-success btn-labeled btn-xs" id="act-save" onclick="save_accept()">
                	<b><i class="icon-floppy-disk"></i></b> Simpan
                </button> 
                </div>                  
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<div class="modal fade" id="frmbox4" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger-700" style="background-image:url(assets/images/backgrounds/bg.png)">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            	<h4 class="modal-title"><i class="fa fa-navicon"></i> &nbsp;<?= 'Form Upload Template Pengajuan' ?></h4>
            </div>
            <form id="frm4" class="form-horizontal">
                <div class="modal-body"> 
										<div>
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">File</label>
                            <div class="col-sm-9"> 
														<input type="file" class="form-control input-sm" name="file" id="file" required>
                            </div>
                        </div>  
                    </div><!-- /.box-body -->
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-labeled btn-xs pull-left" data-dismiss="modal">
                	<b><i class="icon-switch"></i></b> Batal
                </button>                
                <button type="button" class="btn btn-success btn-labeled btn-xs" id="act-save" onclick="save_upload()">
                	<b><i class="icon-floppy-disk"></i></b> Simpan
                </button> 
                </div>                  
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
 

<div class="modal fade" id="frmbox5" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger-700" style="background-image:url(assets/images/backgrounds/bg.png)">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            	<h4 class="modal-title"><i class="fa fa-navicon"></i> &nbsp;<?= 'Form Data Penerimaan Buku' ?></h4>
            </div>
            <form id="frm5" class="form-horizontal">
                <input type="hidden" name="id" id="id"> 
                <div class="modal-body">
                    <div class="box-body" style="padding-bottom:0px">  
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Nama Buku</label>
                            <div class="col-sm-9" id="list"> 
								<input type="text" class="form-control input-sm" name="inp[book_title]" id="book_title" readonly>
                            </div>
                        </div>  
						 
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Konfirmasi Email</label>
                            <div class="col-sm-9"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_date_email_confirmed]" id="book_date_email_confirmed" required>
                            </div> 
                        </div>   
                    </div><!-- /.box-body -->
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-labeled btn-xs pull-left" data-dismiss="modal">
                	<b><i class="icon-switch"></i></b> Batal
                </button>                
                <button type="button" class="btn btn-success btn-labeled btn-xs" id="act-save" onclick="save_email_confirmed()">
                	<b><i class="icon-floppy-disk"></i></b> Simpan
                </button> 
                </div>                  
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal fade" id="frmbox6" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger-700" style="background-image:url(assets/images/backgrounds/bg.png)">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            	<h4 class="modal-title"><i class="fa fa-navicon"></i> &nbsp;<?= 'Form Data Penerimaan Buku' ?></h4>
            </div>
            <form id="frm6" class="form-horizontal">
                <input type="hidden" name="id" id="id"> 
                <div class="modal-body">
                    <div class="box-body" style="padding-bottom:0px">  
						<div class="form-group form-group-sm"> 
                            <label for="pus_name" class="col-sm-3 control-label">Nama Buku</label>
                            <div class="col-sm-9" id="list"> 
								<input type="text" class="form-control input-sm" name="inp[book_title]" id="book_title" readonly>
                            </div>
                        </div>  
						 
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Ketersediaan Buku</label>
                            <div class="col-sm-9"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_date_available]" id="book_date_available" required>
                            </div>
                        </div>   
						 
						 <div class="form-group form-group-sm">
							 <label for="pus_name" class="col-sm-3 control-label">No. Katalog</label>
							 <div class="col-sm-9"> 
								 <input type="text" class="form-control input-sm" name="inp[book_catalog_number]" id="book_catalog_number" required>
							 </div>
						 </div> 
                    </div><!-- /.box-body -->
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-labeled btn-xs pull-left" data-dismiss="modal">
                	<b><i class="icon-switch"></i></b> Batal
                </button>                
                <button type="button" class="btn btn-success btn-labeled btn-xs" id="act-save" onclick="save_available()">
                	<b><i class="icon-floppy-disk"></i></b> Simpan
                </button> 
                </div>                  
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

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
var baseurl = 'index.php/<?= y_url_apps('bookprocurement_url') ?>/submission2';
var date = '<?= date('d F Y').' '.date('H:i:s'); ?>';

$(document).ready(function() {	

	
	$('#dates_submission, #dates_logistic, #dates_acceptance, #dates_email_confirmed, #dates_available').daterangepicker({  
		locale: {
			format: 'DD-MM-YYYY'
		},
		showDropdowns: true, 
		opens: 'left',
		applyClass: 'bg-primary-600',
		cancelClass: 'btn-light'
	});
	
	$("#status,#prodi,#type,#book_type").select2({
		minimumResultsForSearch: Infinity
	});       

	$("#dates_submission_option, #dates_logistic_option, #dates_acceptance_option, #dates_email_confirmed_option, #dates_available_option, #book_id_prodi").select2({
		minimumResultsForSearch: Infinity
	}); 

	
    
	$('#book_date_prodi_submission, #book_date_logistic_submission, #book_date_acceptance, #book_date_email_confirmed, #book_date_available').datepicker({
        format: "dd-mm-yyyy"
	});
	 
	$('#dates_submission, #dates_logistic, #dates_acceptance, #dates_email_confirmed, #dates_available').hide(); 

	$("#filter").click(function(){ 
		_reload();
	}); 
	 
	$("#dates_submission_option").change(function(){ 
		if($(this).val()=='all') $("#dates_submission").hide(); 
		else $("#dates_submission").show(); 
	});  
	 
	 $("#dates_logistic_option").change(function(){ 
		 if($(this).val()=='all') $("#dates_logistic").hide(); 
		 else $("#dates_logistic").show(); 
	 });  
	 
	 $("#dates_acceptance_option").change(function(){ 
		 if($(this).val()=='all') $("#dates_acceptance").hide(); 
		 else $("#dates_acceptance").show(); 
	 });  
	 
	 $("#dates_email_confirmed_option").change(function(){ 
		 if($(this).val()=='all') $("#dates_email_confirmed").hide(); 
		 else $("#dates_email_confirmed").show(); 
	 }); 
	 
	 $("#dates_available_option").change(function(){ 
		 if($(this).val()=='all') $("#dates_available").hide(); 
		 else $("#dates_available").show(); 
	 }); 

    $(tb).dataTable({
        'ajax': {
						'url':baseurl+'/json', 
				'data' : function(data) {
					data.prodi		= $('#prodi').val();
					data.type			= $('#type').val(); 
					data.status		= $('#status').val(); 
					data.dates_submission_option		= $('#dates_submission_option').val(); 
					data.dates_logistic_option			= $('#dates_logistic_option').val(); 
					data.dates_acceptance_option		= $('#dates_acceptance_option').val(); 
					data.dates_email_confirmed_option	= $('#dates_email_confirmed_option').val(); 
					data.dates_available_option			= $('#dates_available_option').val(); 
					data.dates_submission				= $('#dates_submission').val(); 
					data.dates_logistic					= $('#dates_logistic').val(); 
					data.dates_acceptance				= $('#dates_acceptance').val(); 
					data.dates_email_confirmed			= $('#dates_email_confirmed').val(); 
					data.dates_available				= $('#dates_available').val(); 
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

	 

	// $('#book_id_member').select2({
	// 		ajax: {
	// 				url:baseurl+"/getmember",
	// 				dataType: 'json',
	// 				type: 'POST',
	// 				data: function (params) {
	// 						return {
	// 								searchTerm: params.term
	// 						};
	// 				},
	// 				processResults: function (data) {
	// 						return { results: data };
	// 				}
	// 		},
	// 		minimumInputLength: 3
	// });

	$("#chk-all-logistic").change(function(){
		$('input.chk-logistic:checkbox').not(this).prop('checked', this.checked);
		$.uniform.update();
	}); 
	
	$("#chk-all-logistic").uniform({
			radioClass: 'choice'
	});   
	 
	<?php $iuser = $this->session->userdata(); if ($iuser['usergroup']=='superadmin'){ ?>
	$('.dt-buttons').append('<button class="btn btn-sm btn-warning" onclick="logistic()"><i class="icon-calendar2"></i> &nbsp;Input Pengajuan Logistik</button> <button class="btn btn-sm btn-primary" onclick="add()"><i class="icon-add"></i> &nbsp;Tambah Data</button><button class="btn btn-sm  bg-purple" onclick="download()"><i class="icon-file-download"></i> &nbsp;Download Template Pengajuan</button><button class="btn btn-sm bg-danger" onclick="upload()"><i class="icon icon-file-upload"></i> &nbsp;Upload Data Pengajuan</button>');
	<?php } ?>
	$('.dataTables_filter input[type=search]').attr('placeholder','Pencarian');

    $('.dataTables_length select').select2({
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });

	 
	// $('.dt-buttons').html('<button class="btn btn-sm btn-success" onclick="excel()"><i class="icon-file-excel position-left"></i>Export Excel</button><button class="btn btn-sm btn-primary" onclick="question()"><i class="icon-file-excel position-left"></i>Download Kuesioner</button>');
});


function logistic()
{
	var id = '';
	$('input.chk-logistic:checkbox:checked').each(function () {
		id += $(this).val()+',';
	});	 
	
	if(id == '')
		alert('Pilih Salah Satu Buku');
	else
	logistics(id.slice(0,-1),'input');
}

function logistics(id,type)
{		
	$.ajax({
		url:baseurl+'/logistics', 
		type:'post',
		data: ({ id : id,type:type }),
		beforeSend : function() {
			$('#loading-img').show();
		},
		complete : function() {
			$('#loading-img').hide();
		},
		success: function(data)
		{
			$('#frmbox2 #id').val(id);
			$('#frmbox2 #type').val(type);
			var obj = JSON.parse(data);  
			$('#frmbox2 #book_date_logistic_submission').val(''); 
			$('#frmbox2 #book_memo_logistic_number').val(''); 
			$('#frmbox2 #list').html(obj.list); 
			console.log(obj.dt);
			$('#frmbox2 #book_date_logistic_submission').val(obj.dt.book_date_logistic_submission);
			$('#frmbox2 #book_memo_logistic_number').val(obj.dt.book_memo_logistic_number); 
			$('#frmbox2').modal({keyboard: false, backdrop: 'static'});
				// document.location.href =(data);
		}
	});	
} 
	
function add()
{
	_reset();
	$('#act-save').show();
	$('#act-update').hide();
	$('#frmbox').modal({keyboard: false, backdrop: 'static'});
}


	
function upload()
{ 
	$('#frmbox4').modal({keyboard: false, backdrop: 'static'});
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
			$.each(e, function(key, value) {
				$('#'+key).val(value);
			}); 
			$('#frmbox #id').val(id); 
			$('#book_id_prodi').val(e.book_id_prodi).trigger('change');
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

function download(){
	window.open('<?= base_url() ?>cdn/template_pengajuan_buku.xlsx');
}



function edit_accept(id)
{		
	$.ajax({
		url:baseurl+'/edit_accept',
		global:false,
		async:true,
		dataType:'json',
		type:'post',
		data: ({ id : id }),
		success: function(e) { 

			$.each(e, function(key, value) {
				$('#frmbox3 #'+key).val(value);
			}); 
			$('#frmbox3 #id').val(id); 
			$('#book_type').val(e.book_type).trigger('change');
			$('#frmbox3').modal({keyboard: false, backdrop: 'static'});
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


function edit_email_confirmed(id)
{		
	$.ajax({
		url:baseurl+'/edit_email_confirmed_and_available',
		global:false,
		async:true,
		dataType:'json',
		type:'post',
		data: ({ id : id }),
		success: function(e) { 

			$.each(e, function(key, value) {
				$('#frmbox5 #'+key).val(value);
			}); 
			$('#frmbox5 #id').val(id);  
			 
			$('#frmbox5').modal({keyboard: false, backdrop: 'static'});
		},
		error : function() {
			alert('<?= $this->config->item('alert_error') ?>');	 
		},
		// beforeSend : function() {
		// 	$('#loading-img').show();
		// },
		// complete : function() {
		// 	$('#loading-img').hide();
		// }
	});	
}


function edit_available(id)
{		
	$.ajax({
		url:baseurl+'/edit_email_confirmed_and_available',
		global:false,
		async:true,
		dataType:'json',
		type:'post',
		data: ({ id : id }),
		success: function(e) { 

			$.each(e, function(key, value) {
				$('#frmbox6 #'+key).val(value);
			}); 
			$('#frmbox6 #id').val(id);   
			$('#frmbox6').modal({keyboard: false, backdrop: 'static'});
		},
		error : function() {
			alert('<?= $this->config->item('alert_error') ?>');	 
		},
		// beforeSend : function() {
		// 	$('#loading-img').show();
		// },
		// complete : function() {
		// 	$('#loading-img').hide();
		// }
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


function save_logistic()
{
	if($("#frm2").valid())
	{
		$.ajax({
			url:baseurl+'/save_logistic',
			global:false,
			async:true,
			type:'post',
			dataType:'json',
			data: $('#frm2').serialize(),
			success : function(e) {
				if(e.status == 'ok;') 
				{
					_reload();
					$("#frmbox2").modal('hide');
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


function save_accept()
{
	if($("#frm3").valid())
	{
		$.ajax({
			url:baseurl+'/save_accept',
			global:false,
			async:true,
			type:'post',
			dataType:'json',
			data: $('#frm3').serialize(),
			success : function(e) {
				if(e.status == 'ok;') 
				{
					_reload();
					$("#frmbox3").modal('hide');
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



function save_email_confirmed()
{
	if($("#frm5").valid())
	{
		$.ajax({
			url:baseurl+'/save_email_confirmed',
			global:false,
			async:true,
			type:'post',
			dataType:'json',
			data: $('#frm5').serialize(),
			success : function(e) {
				if(e.status == 'ok;') 
				{
					_reload();
					$("#frmbox5").modal('hide');
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

function save_available()
{
	if($("#frm6").valid())
	{
		$.ajax({
			url:baseurl+'/save_available',
			global:false,
			async:true,
			type:'post',
			dataType:'json',
			data: $('#frm6').serialize(),
			success : function(e) {
				if(e.status == 'ok;') 
				{
					_reload();
					$("#frmbox6").modal('hide');
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
</script>