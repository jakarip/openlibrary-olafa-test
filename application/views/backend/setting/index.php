<style>
.input-table {
	padding:3px;
	width:100%;
}

.error{
	color :#f44336;
}
</style>

<div class="panel panel-default flat">
    <div class="panel-heading">
        <h6 class="panel-title">1. Setting Aplikasi<a class="heading-elements-toggle"><i class="icon-more"></i></a></h6>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
                <li><a data-action="reload"></a></li>
                <li><a data-action="close"></a></li>
            </ul>
        </div>
    </div>
    <div class="panel-body">
    	<form class="form-horizontal" id="frm-setting-1">
		 
			<div class="form-group form-group-sm">
				<label class="control-label col-md-3">Title Website</label>
				<div class="col-md-9">
					<input type="text" class="form-control input-sm" name="option[website_name]" id="website_name" value="<?= $setting['website_name'] ?>" required>
				</div>
			</div>  
			<div class="form-group form-group-sm">
				<label class="control-label col-md-3">Tahun Ajar</label>
				<div class="col-md-9">
					<input type="text" class="form-control input-sm test-input" name="option[website_ta]" id="website_ta" value="<?= $setting['website_ta'] ?>" required>
				</div>
			</div>  
			<div class="form-group form-group-sm">
				<label class="control-label col-md-3">Format Awal No Transaksi</label>
				<div class="col-md-9">
					<input type="number" class="form-control input-sm test-input" name="option[format_code]" minlength="2" maxlength="2" id="format_code" value="<?= $setting['format_code'] ?>" required>
				</div>
			</div>  
			<div class="form-group form-group-sm">
				<label class="control-label col-md-3">Format Awal No Transaksi</label>
				<div class="col-md-9">
					<input type="number" class="form-control input-sm test-input" name="option[format_code]" minlength="2" maxlength="2" id="format_code" value="<?= $setting['format_code'] ?>" required>
				</div>
			</div>  
			<div class="form-group form-group-sm">
				<label class="control-label col-md-3">No Surat Kelulusan	</label>
				<div class="col-md-9">
					<input type="text" readonly class="form-control input-sm test-input"  id="letter_number" value="<?= $setting['letter_number'] ?>" required>
				</div>
			</div>  
		<fieldset class="content-group">
			<legend class="text-bold">Institusi</legend>
			<div class="form-group form-group-sm">
				<label class="control-label col-md-3">Nama Institusi</label>
				<div class="col-md-9">
					<input type="text" class="form-control input-sm" name="option[institution]" id="institution" value="<?= $setting['institution'] ?>" required>
				</div>
			</div>
			<div class="form-group form-group-sm">
				<label class="control-label col-md-3">Alamat Institusi</label>
				<div class="col-md-9">
					 <!-- <textarea class="form-control input-sm" name="option[institution_address]" id="ckeditorAddress" spellcheck="false"><?= $setting['institution_address'] ?></textarea>-->
					 <input type="text" class="form-control input-sm" name="option[template_address]" id="template_address" value="<?= $setting['template_address'] ?>" required>
				</div>
			</div>  
			</legend>
		</fieldset>
		<fieldset class="content-group">
			<legend class="text-bold">Email Notifikasi</legend>
			<div class="form-group form-group-sm">
				<label class="control-label col-md-3">Email</label>
				<div class="col-md-9">
					<input type="email" class="form-control input-sm" name="option[email_address]" id="email_address" value="<?= $setting['email_address'] ?>" required>
				</div>
			</div>
			<div class="form-group form-group-sm">
				<label class="control-label col-md-3">Username</label>
				<div class="col-md-9">
					<input type="text" class="form-control input-sm" name="option[email_username]" id="email_username" value="<?= $setting['email_username'] ?>" required>
				</div>
			</div>
			<div class="form-group form-group-sm">
				<label class="control-label col-md-3">Password</label>
				<div class="col-md-9">
					<input type="password" class="form-control input-sm" name="option[email_password]" id="email_password" value="<?= $setting['email_password'] ?>" required>
				</div>
			</div>
			<div class="form-group form-group-sm">
				<label class="control-label col-md-3">Port</label>
				<div class="col-md-9">
					<input type="text" class="form-control input-sm" name="option[email_port]" id="email_port" value="<?= $setting['email_port'] ?>" required>
				</div>
			</div>
			<div class="form-group form-group-sm">
				<label class="control-label col-md-3">URL SMTP</label>
				<div class="col-md-9">
					<input type="text" class="form-control input-sm" name="option[email_host]" id="email_host" value="<?= $setting['email_host'] ?>" required>
				</div>
			</div>
			</legend>
		</fieldset>
		
		<fieldset class="content-group">
			<legend class="text-bold">Upload</legend>
			<div class="form-group form-group-sm">
				<label class="control-label col-md-3">Tata Cara Pembayaran</label>
				<div class="col-md-9">
					<input type="file" class="file-styled file_uploads" name="file"><span class="filename " style="user-select: none;"></span>
					<span class="help-block">File : <strong  id="file"><?= $setting['file_payment_method'] ?></strong><br><br>Format yang diterima : <strong>docx,pdf</strong> <br> Maksimal size : <strong>2 MB</strong></span>
				</div>
			</div>
			<div class="form-group form-group-sm">
				<label class="control-label col-md-3">Logo</label>
				<div class="col-md-9">
					<input type="file" class="file-styled photos" name="logo"><span class="filename" style="user-select: none;"></span> 
					<span class="help-block">File : <strong  id="logo"><?= $setting['image_logo'] ?></strong><br><br>Format yang diterima : <strong>jpg, jpeg, png</strong> <br> Maksimal size : <strong>2 MB</strong></span>
				</div>
			</div>
			<div class="form-group form-group-sm">
				<label class="control-label col-md-3">Background Image</label>
				<div class="col-md-9">
					<input type="file" class="file-styled photos" name="background" placeholder="aaa"><span class="filename" style="user-select: none;"></span>
					 <span class="help-block">File : <strong  id="background"><?= $setting['image_background'] ?></strong><br><br>Format yang diterima : <strong>jpg, jpeg, png</strong> <br> Maksimal size : <strong>2 MB</strong></span>
				</div>
			</div> 
			
			</legend>
		</fieldset>
        <div>
        	<button type="button" class="btn btn-sm btn-primary" onClick="save_setting('frm-setting-1')"><i class="icon-floppy-disk position-left"></i> Simpan</button>
        </div>
        </form>
    </div>
</div>

<div class="panel panel-default flat">
    <div class="panel-heading">
        <h6 class="panel-title">2. Setting Mata Pelajaran<a class="heading-elements-toggle"><i class="icon-more"></i></a></h6>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
                <li><a data-action="reload"></a></li>
                <li><a data-action="close"></a></li>
            </ul>
        </div>
    </div>
    <table class="table table-bordered table-striped table-hover" id="table-course">
        <thead>
            <tr>
                <th width="10%">Status</th>
                <th width="40%">Nama Mata Pelajaran</th>
                <th width="10%">Bobot</th>
                <th width="10%">Jenis Mata Pelajaran</th>
                <th width="10%">Jurusan</th>
                <th width="20%">&nbsp;</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

  
<div class="panel panel-default flat">
    <div class="panel-heading">
        <h6 class="panel-title">3. Template Kelulusan<a class="heading-elements-toggle"><i class="icon-more"></i></a></h6>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
                <li><a data-action="reload"></a></li>
                <li><a data-action="close"></a></li>
            </ul>
        </div>
    </div>
    <div class="panel-body">
    	<form id="frm-setting-2" method="post" action="<?= y_url_admin() ?>/setting/save_setting_submit">
        <p>
            Gunakan kode berikut untuk mengganti dengan entitas dalam database:
            <br><strong>{Nama}</strong> : Nama Calon Mahasiswa
            <br><strong>{Sekolah}</strong> : Sekolah Asal Calon Mahasiswa
        </p>
    	<div class="form-group form-group-sm">
            <label>Template Lulus</label>
            <div>
                <textarea class="form-control input-sm" name="option[template_passed]" id="ckeditor" spellcheck="false"><?= $setting['template_passed'] ?></textarea>
            </div>
        </div>
        <div class="form-group form-group-sm">
        	<button type="submit" name="submit" class="btn btn-sm btn-primary"><i class="icon-floppy-disk position-left"></i> Simpan</button>
        </div>
        </form>
    </div>
</div>


  
<div class="panel panel-default flat">
    <div class="panel-heading">
        <h6 class="panel-title">4. Landing Page<a class="heading-elements-toggle"><i class="icon-more"></i></a></h6>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
                <li><a data-action="reload"></a></li>
                <li><a data-action="close"></a></li>
            </ul>
        </div>
    </div>
		<form id="frm-setting-2" method="post" action="<?= y_url_admin() ?>/setting/save_setting_submit"> 
			<div class="panel-body">
				<div class="form-group form-group-sm">
					<label>Alur Pendaftaran</label>
					<div>
							<textarea class="form-control input-sm" name="option[landing_page_register_flow]" id="ckeditor1" spellcheck="false"><?= $setting['landing_page_register_flow'] ?></textarea>
					</div>
				</div>
			</div>
			
			<div class="panel-body">
				<div class="form-group form-group-sm">
					<label>Jalur Seleksi</label>
					<div>
							<textarea class="form-control input-sm" name="option[landing_page_info_selection_path]" id="ckeditor2" spellcheck="false"><?= $setting['landing_page_info_selection_path'] ?></textarea>
					</div>
				</div>
			</div>
			
			<div class="panel-body">
				<div class="form-group form-group-sm">
					<label>Biaya Pendidikan</label>
					<div>
							<textarea class="form-control input-sm" name="option[landing_page_info_education_cost]" id="ckeditor3" spellcheck="false"><?= $setting['landing_page_info_education_cost'] ?></textarea>
					</div>
				</div>
			</div>
			
			<div class="panel-body">
				<div class="form-group form-group-sm">
					<label>Beasiswa</label>
					<div>
							<textarea class="form-control input-sm" name="option[landing_page_info_scholarship]" id="ckeditor4" spellcheck="false"><?= $setting['landing_page_info_scholarship'] ?></textarea>
					</div>
				</div>
			</div>
			
			<div class="panel-body">
				<div class="form-group form-group-sm">
					<label>Registrasi Online</label>
					<div>
							<textarea class="form-control input-sm" name="option[landing_page_info_register_online]" id="ckeditor5" spellcheck="false"><?= $setting['landing_page_info_register_online'] ?></textarea>
					</div>
				</div>
			</div>
			
			<div class="panel-body">
				<div class="form-group form-group-sm">
					<label>Registrasi Onsite</label>
					<div>
							<textarea class="form-control input-sm" name="option[landing_page_info_register_onsite]" id="ckeditor6" spellcheck="false"><?= $setting['landing_page_info_register_onsite'] ?></textarea>
					</div>
				</div>
			</div>
			
			<div class="panel-body">
				<div class="form-group form-group-sm">
					<label>F.A.Q</label>
					<div>
							<textarea class="form-control input-sm" name="option[landing_page_info_faq]" id="ckeditor7" spellcheck="false"><?= $setting['landing_page_info_faq'] ?></textarea>
					</div>
				</div>
			</div>
			
			<div class="panel-body">
				<div class="form-group form-group-sm">
					<label>Download</label>
					<div>
							<textarea class="form-control input-sm" name="option[landing_page_info_download]" id="ckeditor8" spellcheck="false"><?= $setting['landing_page_info_download'] ?></textarea>
					</div>
				</div>
			</div>
			<div class="form-group form-group-sm">
				<button type="submit" name="submit" class="btn btn-sm btn-primary"><i class="icon-floppy-disk position-left"></i> Simpan</button>
			</div>
		</form>
</div> 

<div class="modal fade" id="frmbox-course" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger-700" style="background-image:url(assets/images/backgrounds/bg.png)">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            	<h4 class="modal-title"><i class="fa fa-navicon"></i> &nbsp;Form Setting Mata Pelajaran</h4>
            </div>
            <form id="frm-course" class="form-horizontal">
                <input type="hidden" name="id" id="id-course">
                <div class="modal-body">
                    <div class="box-body" style="padding-bottom:0px">
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Nama Mata Pelajaran</label>
                            <div class="col-sm-9">
                            	<input type="text" class="form-control input-sm" name="inp[course_name]" id="course_name" required>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Bobot</label>
                            <div class="col-sm-9">
                            	<?= form_dropdown('inp[course_score]', $bobot, '', 'class="form-control input-sm select2" id="course_score" required="true"') ?>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Jenis Mata Pelajaran</label>
                            <div class="col-sm-9">
                            	<input type="text" class="form-control input-sm" name="inp[course_type]" id="course_type" required>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Jurusan</label>
                            <div class="col-sm-9">
                            	<input type="text" class="form-control input-sm" name="inp[course_tags]" id="course_tags" required>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-labeled btn-xs pull-left" data-dismiss="modal">
                	<b><i class="icon-switch"></i></b> Batal
                </button>                
                <button type="button" class="btn btn-success btn-labeled btn-xs" id="act-save-course" onclick="save('course_insert', 'course')">
                	<b><i class="icon-floppy-disk"></i></b> Simpan
                </button>
        		<button type="button" class="btn btn-success btn-labeled btn-xs" id="act-update-course" onclick="save('course_update', 'course')">
                	<b><i class="icon-floppy-disk"></i></b> Simpan Perubahan
                </button>
                </div>                  
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal --> 

<?php $this->load->view('backend/tpl_footer'); ?>
<!-- Theme JS files -->	
<script type="text/javascript" src="assets/limitless/global/js/plugins/editors/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/validation/validate.min.js"></script>
	<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/validation/additional-methods.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/core/setting.js"></script> 


<script src="assets/limitless/global/js/plugins/forms/styling/uniform.min.js"></script> 

<script type="text/javascript">
var baseurl = '<?= y_url_admin() ?>/setting';
var validator; 
var form_setting  = $('#form_setting');


$.validator.addClassRules({
	file_uploads:{
		extension: "docx|pdf",
		maxFileSize: {
			"unit": "MB",
			"size": 2
		} 
	},
	photos:{
		extension: "jpg|jpeg|png",
		maxFileSize: {
			"unit": "MB",
			"size": 2
		} 
	}
});    

$(document).ready(function() {

	$('.test-input').unbind('keyup change input paste').bind('keyup change input paste',function(e){
    var $this = $(this);
    var val = $this.val();
    var valLength = val.length;
    var maxCount = $this.attr('maxlength');
    if(valLength>maxCount){
        $this.val($this.val().substring(0,maxCount));
    }
}); 

	$(".file-styled").uniform();

    $('#table-course').dataTable({
        'ajax': {
            'url':baseurl+'/course_json'
		},
		'order':[
			[0, 'desc'],[1, 'asc']
		],
		'columnDefs': [ 
			{ 'targets': -1, 'searchable': false, 'orderable': false, 'className': 'center' } 
		]
    });
	
	// $('#table_component').dataTable({
        // 'ajax': {
            // 'url':baseurl+'/component_json'
		// },
		// 'order':[
			// [0, 'desc']
		// ],
		// 'columnDefs': [ 
			// { 'targets': 'nosort', 'searchable': false, 'orderable': false, 'className': 'center' } 
		// ]
    // }); 
	
	$('.dataTables_filter input[type=search]').attr('placeholder','Pencarian');

    $('.dataTables_length select').select2({
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });
	
	$('#table-course_wrapper .dt-buttons').html('<button type="button" class="btn btn-sm btn-primary" onclick="add(\'course\')"><i class="icon-add"></i> &nbsp;Tambah Data</button>');
	$('#table_component_wrapper .dt-buttons').html('<button type="button" class="btn btn-sm btn-success" onclick="save_component()"><i class="icon-floppy-disk"></i> &nbsp;Simpan Perubahan Data</button>');	
	
	$('#table-track_wrapper .dt-buttons').html('<button type="button" class="btn btn-sm btn-primary" onclick="add(\'track\')"><i class="icon-add"></i> &nbsp;Tambah Data</button>');
	
	CKEDITOR.addCss( 'body {font-family: Roboto, Arial, Verdana; font-size: 13px; color:#333}' );
	CKEDITOR.replace( 'ckeditor', {
        height: '500px',
		toolbar : 'MyToolbar',
			extraPlugins: 'youtube'
    });

		
	CKEDITOR.replace( 'ckeditor1', {
        height: '500px',
		toolbar : 'MyToolbar',
			extraPlugins: 'youtube'
    });

		
	CKEDITOR.replace( 'ckeditor2', {
        height: '500px',
		toolbar : 'MyToolbar',
			extraPlugins: 'youtube'
    });

		
	CKEDITOR.replace( 'ckeditor3', {
        height: '500px',
		toolbar : 'MyToolbar',
			extraPlugins: 'youtube'
    });

		
	CKEDITOR.replace( 'ckeditor4', {
        height: '500px',
		toolbar : 'MyToolbar',
			extraPlugins: 'youtube'
    });

		
	CKEDITOR.replace( 'ckeditor5', {
        height: '500px',
		toolbar : 'MyToolbar',
			extraPlugins: 'youtube'
    });

		
	CKEDITOR.replace( 'ckeditor6', {
        height: '500px',
		toolbar : 'MyToolbar',
			extraPlugins: 'youtube'
    });

		
	CKEDITOR.replace( 'ckeditor7', {
        height: '500px',
		toolbar : 'MyToolbar',
			extraPlugins: 'youtube'
    });

		
	CKEDITOR.replace( 'ckeditor8', {
        height: '500px',
			toolbar : 'MyToolbar',
			extraPlugins: 'youtube'
    });
});



function save_setting(frm)
{
	if($("#"+frm).valid())
	{
		var formData = new FormData($("#"+frm)[0]);
		$.ajax({
			url:baseurl+'/save_setting',
			global:false,
			async:true,
			type:'post',
			dataType:'json',
			data: formData,
			contentType: false,//untuk upload image
			processData: false,//untuk upload image
			success : function(e) {
				if(e.status == 'ok;') 
				{
					$("#logo").html(e.logo);
					$("#background").html(e.background);
					$("#file").html(e.file);
					alert('Update Setting Sukses');
					//window.location.reload();
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

function save_component()
{
	$.ajax({
		url:baseurl+'/save_component',
		global:false,
		async:true,
		type:'post',
		dataType:'json',
		data: $('#frm-component').serialize(),
		success : function(e) {
			if(e.status == 'ok;') 
			{
				alert('Update Setting Sukses');
				$('#table_component').dataTable().fnDraw();
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

/* COMMON */

function add(identifier)
{
	_validator(identifier);
	_reset(identifier);
	
	$('#act-save-'+identifier).show();
	$('#act-update-'+identifier).hide();
	$('#frmbox-'+identifier).modal({keyboard: false, backdrop: 'static'});
}

function edit(id, identifier)
{		
	_validator(identifier)
	
	$.ajax({
		url:baseurl+'/'+identifier+'_edit',
		global:false,
		async:true,
		dataType:'json',
		type:'post',
		data: ({ id : id }),
		success: function(e) {
			_reset(identifier);
			$('#act-save-'+identifier).hide();
			$('#act-update-'+identifier).show();
			$('#id-'+identifier).val(id);
			$.each(e, function(key, value) {
				if(key != 'track_raport')
					$('#'+key).val(value);
			});
			
			if(e.track_raport == 1)
				$('#track_raport').prop('checked', true);
			
			$('#frmbox-'+identifier).modal({keyboard: false, backdrop: 'static'});
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

function save(url, identifier)
{
	if($("#frm-"+identifier).valid())
	{
		$.ajax({
			url:baseurl+'/'+url,
			global:false,
			async:true,
			type:'post',
			dataType:'json',
			data: $('#frm-'+identifier).serialize(),
			success : function(e) {
				if(e.status == 'ok;') 
				{
					_reload('#table-'+identifier);
					$("#frmbox-"+identifier).modal('hide');
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

function active(id, txt, mode, identifier)
{
	if(confirm('Data: '+txt+'\nApakah anda yakin akan '+(mode == 1 ? 'Aktifkan' : 'Non Aktifkan')+' data tersebut ?')) {
		$.ajax({
			url:baseurl+'/'+identifier+'_active',
			global:false,
			async:true,
			type:'post',
			dataType:'json',
			data: ({id : id, mode : mode }),
			success: function(e) { 
				if(e.status == 'ok;') 
				{
					_reload('#table-'+identifier);
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

/* END COMMON */

function format_rupiah(evt)
{   
	var number_string = evt.value.replace(/[^,\d]/g, '').toString(),
	split   = number_string.split(','),
	sisa    = split[0].length % 3,
	rupiah  = split[0].substr(0, sisa),
	ribuan  = split[0].substr(sisa).match(/\d{1,3}/gi);
	
	if (ribuan) {
		separator = sisa ? '.' : '';
		rupiah += separator + ribuan.join('.');
	}
		
	rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
	$(evt).val(rupiah);
}

function _reset(identifier)
{
	validator.resetForm();
	$("label.error").hide();
 	$(".error").removeClass("error");
	$('#frm-'+identifier)[0].reset();
}

function _reload(tb)
{
	$(tb).dataTable().fnDraw();
}

function _validator(identifier)
{
	validator = $("#frm-"+identifier).validate({
		ignore: 'input[type=hidden], .select2-search__field', // ignore hidden fields
		errorClass: 'validation-error-label',
		/*successClass: 'validation-valid-label',*/
		highlight: function(element, errorClass) {
			$(element).removeClass(errorClass);
		},
		unhighlight: function(element, errorClass) {
			$(element).removeClass(errorClass);
		},
	
		// Different components require proper error label placement
		errorPlacement: function(error, element) {
	
			// Styled checkboxes, radios, bootstrap switch
			if (element.parents('div').hasClass("checker") || element.parents('div').hasClass("choice") || element.parent().hasClass('bootstrap-switch-container') ) {
				if(element.parents('label').hasClass('checkbox-inline') || element.parents('label').hasClass('radio-inline')) {
					error.appendTo( element.parent().parent().parent().parent() );
				}
				 else {
					error.appendTo( element.parent().parent().parent().parent().parent() );
				}
			}
	
			// Unstyled checkboxes, radios
			else if (element.parents('div').hasClass('checkbox') || element.parents('div').hasClass('radio')) {
				error.appendTo( element.parent().parent().parent() );
			}
	
			// Input with icons and Select2
			else if (element.parents('div').hasClass('has-feedback') || element.hasClass('select2-hidden-accessible')) {
				error.appendTo( element.parent() );
			}
	
			// Inline checkboxes, radios
			else if (element.parents('label').hasClass('checkbox-inline') || element.parents('label').hasClass('radio-inline')) {
				error.appendTo( element.parent().parent() );
			}
	
			// Input group, styled file input
			else if (element.parent().hasClass('uploader') || element.parents().hasClass('input-group')) {
				error.appendTo( element.parent().parent() );
			}
	
			else {
				error.insertAfter(element);
			}
		},
		/*validClass: "validation-valid-label",
		success: function(label) {
			label.addClass("validation-valid-label").text("Success.")
		},*/
		rules: {
			password: {
				minlength: 5
			},
			repeat_password: {
				equalTo: "#password"
			},
			email: {
				email: true
			},
			repeat_email: {
				equalTo: "#email"
			},
			minimum_characters: {
				minlength: 10
			},
			maximum_characters: {
				maxlength: 10
			},
			minimum_number: {
				min: 10
			},
			maximum_number: {
				max: 10
			},
			number_range: {
				range: [10, 20]
			},
			url: {
				url: true
			},
			date: {
				date: true
			},
			date_iso: {
				dateISO: true
			},
			numbers: {
				number: true
			},
			digits: {
				digits: true
			},
			creditcard: {
				creditcard: true
			},
			basic_checkbox: {
				minlength: 2
			},
			styled_checkbox: {
				minlength: 2
			},
			switchery_group: {
				minlength: 2
			},
			switch_group: {
				minlength: 2
			}
		},
		messages: {
			custom: {
				required: "This is a custom error message",
			},
			agree: "Please accept our policy"
		}
	});	
} 
</script>