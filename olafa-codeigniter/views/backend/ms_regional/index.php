<style>
.nav-tabs-custom {
	border-radius:0px !important;
}
.nav-tabs.nav-tabs-custom > li:first-child > a {
    border-radius: 3px 0 0 0px !important;
}.nav-tabs.nav-tabs-custom > li:last-child > a {
    border-radius: 0px 3px 0 0px !important;
}
</style>
<!--<div class="panel panel-default flat">
    <table class="table table-bordered table-striped table-hover" id="table">
        <thead>
            <tr>
                <th width="25%">Nama Sekolah</th>
                <th width="40%">Alamat Sekolah</th>
                <th width="15%">Nilai Bobot</th>
                <th width="10%">&nbsp;</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>-->

<div class="panel panel-flat">
        <div class="tabbable">
            <ul class="nav nav-tabs nav-tabs-solid nav-tabs-component nav-justified no-margin nav-tabs-custom">
                <li class="active"><a href="#small-tab1" data-toggle="tab">Provinsi</a></li>
                <li><a href="#small-tab2" data-toggle="tab">Kabupaten</a></li>
                <li><a href="#small-tab3" data-toggle="tab">Kecamatan</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="small-tab1">
                    <table class="table table-bordered table-striped table-hover" id="table-prov">
                        <thead>
                            <tr>
                                <th width="25%">Kode Provinsi</th>
                                <th width="60%">Nama Provinsi</th>
                                <th width="15%">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="tab-pane" id="small-tab2">
                    <table class="table table-bordered table-striped table-hover" id="table-kab">
                        <thead>
                            <tr>
                                <th width="25%">Nama Provinsi</th>
                                <th width="25%">Kode Kabupaten</th>
                                <th width="40%">Nama Kabupaten</th>
                                <th width="10%">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="tab-pane" id="small-tab3">
                    <table class="table table-bordered table-striped table-hover" id="table-kec">
                        <thead>
                            <tr>
                                <th width="25%">Nama Provinsi</th>
                                <th width="25%">Nama Kabupaten</th>
                                <th width="15%">Kode Kecamatan</th>
                                <th width="40%">Nama Kecamatan</th>
                                <th width="10%">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
</div>

<div class="modal fade" id="kecfrmbox" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger-700" style="background-image:url(assets/images/backgrounds/bg.png)">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            	<h4 class="modal-title"><i class="fa fa-navicon"></i> &nbsp;<?= 'Form '.$title ?></h4>
            </div>
            <form id="kecfrm" class="form-horizontal">
                <input type="hidden" name="id" id="id">
                <div class="modal-body">
                    <div class="box-body" style="padding-bottom:0px">
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Provinsi</label>
                            <div class="col-sm-9">
                            	<select name="prov" id="kec-prov" class="form-control input-sm" required><option value="">Pilih Provinsi</option></select>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Kabupaten</label>
                            <div class="col-sm-9">
                            	<select name="" id="kec-kab" class="form-control input-sm" required></select>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Kecamatan</label>
                            <div class="col-sm-9">
                            	<input type="text" class="form-control input-sm" name="inp[school_address]" id="school_address" required>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-labeled btn-xs pull-left" data-dismiss="modal">
                	<b><i class="icon-switch"></i></b> Batal
                </button>                
                <button type="button" class="btn btn-success btn-labeled btn-xs" id="kec-act-save" onclick="save('insert')">
                	<b><i class="icon-floppy-disk"></i></b> Simpan
                </button>
        		<button type="button" class="btn btn-success btn-labeled btn-xs" id="kec-act-update" onclick="save('update')">
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
<!--<script type="text/javascript" src="assets/js/core/setting.js"></script>-->

<script type="text/javascript">
var tb 		= '#table';      
var baseurl = '<?= y_url_admin() ?>/ms_regional';
var json;

$(document).ready(function() {	
    //load_datatable();
	
	
	$('#table-prov').dataTable({
        'ajax': {
            'url':baseurl+'/prov_json'
		},
		'order':[
			[0, 'asc']
		],
		'columnDefs': [ 
			{ 'targets': -1, 'searchable': false, 'orderable': false, 'className': 'center' } 
		],
		pageLength: -1,
		'drawCallback': function( settings ) {
			var api = this.api();
	 		var row = api.rows( {page:'current'} ).data();
			
			$.each(row, function(index, value) {
				$('#kec-prov').append('<option value="'+value[3]+'">'+value[1]+'</option>');
			});
		}
    });
	
	$('#table-kab').dataTable({
        'ajax': {
            'url':baseurl+'/kab_json'
		},
		'order':[
			[0, 'asc'],[2, 'asc']
		],
		'columnDefs': [ 
			{ 'targets': -1, 'searchable': false, 'orderable': false, 'className': 'center' } 
		]
    });
	
	$('#table-kec').dataTable({
        'ajax': {
            'url':baseurl+'/kec_json'
		},
		'order':[
			[0, 'asc'],[1, 'asc'],[3, 'asc']
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
	
	$('#table-prov_wrapper .dt-buttons').html('<button class="btn btn-sm btn-primary" onclick="add()"><i class="icon-add"></i> &nbsp;Tambah Data</button>');
	$('#table-kab_wrapper .dt-buttons').html('<button class="btn btn-sm btn-primary" onclick="add()"><i class="icon-add"></i> &nbsp;Tambah Data</button>');
	$('#table-kec_wrapper .dt-buttons').html('<button class="btn btn-sm btn-primary" onclick="kec_add()"><i class="icon-add"></i> &nbsp;Tambah Data</button>');
	
	/*  $('#table-kec').dataTable({
        'ajax': {
            'url':baseurl+'/json_kec'
		},
		'order':[
			[0, 'asc']
		],
		'columnDefs': [ 
			{ 'targets': -1, 'searchable': false, 'orderable': false, 'className': 'center' } 
		]
    });*/
	
	
});

function load_datatable()
{
	$.ajax({
		url:baseurl+'/json',
		global:false,
		async:true,
		dataType: 'json',
		success: function (obj) {
			$('#table-prov').dataTable({
				autoWidth: false,
        		dom: '<"datatable-header"f<"dt-buttons">><"datatable-scroll"t><"datatable-footer"ip>',
				language: {
					search: '<span>Filter:</span> _INPUT_',
					lengthMenu: '<span>Show:</span> _MENU_',
					paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }
				},
				data : obj,
				columns : [
					{data: 'reg_prov_code'},
					{data: 'reg_prov'},
					{data: 'reg_id'}
				],
				bStateSave: true,
				pageLength: -1,
				'order':[
					[0, 'asc']
				],
				'columnDefs': [ 
					{ 'targets': -1, 'searchable': false, 'orderable': false, 'className': 'center' } 
				],
				'rowCallback': function( row, data, index ) {
					$('td:eq(2)', row).html('<a href="javascript:edit()" title="Edit Data" class="btn btn-xs btn-icon btn-primary"><i class="icon-database-edit2"></i></a> <a href="javascript:del()" title="Delete Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-trash">');
				}
			});
			
			$('#table-kab').dataTable({
				autoWidth: false,
        		dom: '<"datatable-header"f<"dt-buttons">><"datatable-scroll"t><"datatable-footer"ip>',
				language: {
					search: '<span>Filter:</span> _INPUT_',
					lengthMenu: '<span>Show:</span> _MENU_',
					paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }
				},
				data : obj,
				columns : [
					{data: 'reg_prov'},
					{data: 'reg_kab_code'},
					{data: 'reg_kab'},
					{data: 'reg_id'}
				],
				bStateSave: true,
				pageLength: -1,
				'order':[
					[0, 'asc']
				],
				'columnDefs': [ 
					{ 'targets': -1, 'searchable': false, 'orderable': false, 'className': 'center' } 
				],
				'rowCallback': function( row, data, index ) {
					$('td:eq(3)', row).html('<a href="javascript:edit()" title="Edit Data" class="btn btn-xs btn-icon btn-primary"><i class="icon-database-edit2"></i></a> <a href="javascript:del()" title="Delete Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-trash">');
				}
			});
			
			$('#table-kec').dataTable({
				autoWidth: false,
        		dom: '<"datatable-header"f<"dt-buttons">><"datatable-scroll"t><"datatable-footer"ip>',
				language: {
					search: '<span>Filter:</span> _INPUT_',
					lengthMenu: '<span>Show:</span> _MENU_',
					paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }
				},
				data : obj,
				columns : [
					{data: 'reg_prov'},
					{data: 'reg_kab'},
					{data: 'reg_kec_code'},
					{data: 'reg_kec'},
					{data: 'reg_id'}
				],
				bStateSave: true,
				pageLength: -1,
				'order':[
					[0, 'asc']
				],
				'columnDefs': [ 
					{ 'targets': -1, 'searchable': false, 'orderable': false, 'className': 'center' } 
				],
				'rowCallback': function( row, data, index ) {
					$('td:eq(4)', row).html('<a href="javascript:edit()" title="Edit Data" class="btn btn-xs btn-icon btn-primary"><i class="icon-database-edit2"></i></a> <a href="javascript:del()" title="Delete Data" class="btn btn-xs btn-icon btn-danger"><i class="icon-trash">');
				}
			});
			
			$('.dataTables_filter input[type=search]').attr('placeholder','Pencarian');

			$('.dataTables_length select').select2({
				minimumResultsForSearch: Infinity,
				width: 'auto'
			});
			
			$('#table-prov_wrapper .dt-buttons').html('<button class="btn btn-sm btn-primary" onclick="add()"><i class="icon-add"></i> &nbsp;Tambah Data</button>');
			$('#table-kab_wrapper .dt-buttons').html('<button class="btn btn-sm btn-primary" onclick="add()"><i class="icon-add"></i> &nbsp;Tambah Data</button>');
			$('#table-kec_wrapper .dt-buttons').html('<button class="btn btn-sm btn-primary" onclick="add()"><i class="icon-add"></i> &nbsp;Tambah Data</button>');
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
	
function kec_add()
{
	_reset();
	$('#kec-act-save').show();
	$('#kec-act-update').hide();
	$('#kecfrmbox').modal({keyboard: false, backdrop: 'static'});
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