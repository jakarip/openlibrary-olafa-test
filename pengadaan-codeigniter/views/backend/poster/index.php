<div class="panel panel-default flat">
    <table class="table table-bordered table-striped table-hover" id="table">
        <thead>
            <tr>
                <th width="10%">Tgl Input</th>
                <th width="50%">Judul</th> 
                <th width="25%">Poster</th> 
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
            <form id="frm-poster" class="form-horizontal" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id">
                <input type="hidden" name="kab_ori" id="kab_ori">
                <div class="modal-body">
                    <div class="box-body" style="padding-bottom:0px"> 
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Judul</label>
                            <div class="col-sm-9"> 
                            	<input type="text" class="form-control input-sm" name="inp[poster_title]" id="poster_title" required>
                            </div>
                        </div> 
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Poster</label>
                            <div class="col-sm-9"> 
															<input type="file" class="form-control input-sm file-styled photos" name="photos" id="photos" required>
															<span class="help-block">Format yang diterima : <strong>jpg, jpeg, png</strong>; &nbsp;Maksimal size : <strong>2 MB</strong>;</span> 
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

<?php $this->load->view('backend/tpl_footer'); ?>
<!-- Theme JS files -->	
<script type="text/javascript" src="assets/limitless/global/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/validation/additional-methods.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/styling/uniform.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/core/setting.js"></script> 

<script type="text/javascript">
var tb 		= '#table';      
var validator; 
var baseurl = '<?= y_url_admin() ?>/poster';

$(document).ready(function() {	
    $(tb).dataTable({
        'ajax': {
            'url':baseurl+'/json'
		},
		'order':[
			[0, 'desc']
		],
		'columnDefs': [ 
			{ 'targets': -1, 'searchable': false, 'orderable': false, 'className': 'center' } 
		]
    });
	
	$('.select2').select2();
	
	$('.dataTables_filter input[type=search]').attr('placeholder','Pencarian');

    $('.dataTables_length select').select2({
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });
		$('.dt-buttons').html('');
	<?php if($this->session->userdata('usergroup')=='admin'){ ?>
			$('.dt-buttons').html('<button class="btn btn-sm btn-primary" onclick="add()"><i class="icon-add"></i> &nbsp;Tambah Data</button>');  
	<?php } ?>
	 
	$(".file-styled").uniform();


	
	$.validator.addClassRules({ 
		photos:{
			extension: "jpg|jpeg|png",
			maxFileSize: {
				"unit": "MB",
				"size": 2
			} 
		}
	});  

	validator = $("#frm-poster").validate({
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
	if($("#frm-poster").valid())
	{
		var formData = new FormData($('#frm-poster')[0]);
		$.ajax({
			url:baseurl+'/'+url,
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
	$('.filename').html('');
	$("label.error").hide();
 	$(".error").removeClass("error");
	$('#frm-poster')[0].reset();
}

function _reload()
{
	$(tb).dataTable().fnDraw();
} 
</script>