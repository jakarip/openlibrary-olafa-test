<div class="panel panel-default flat">
    <table class="table table-bordered table-striped table-hover" id="table">
        <thead>
            <tr>
                <th width="5%" class="nosort nosearch center" style="text-align:center">Copy All<br><input type="checkbox" id="chk-all"></th>
                <th width="5%" class="nosort center" style="text-align:center">Reset All<br><input type="checkbox" id="reset-all"></th>
                <th width="12%">Used</th>
                <th width="10%">Jenis PIN</th>
                <th width="15%">Periode</th>
                <th width="10%">Harga</th>
                <th width="5%">Max Prodi</th> 
                <th width="13%">No Transaksi</th>
                <th width="10%" class="nosort center">TOKEN</th>
                <th width="10%" class="nosort">Keterangan</th>
                <th width="15%" class="nosort center">&nbsp;</th>
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
            <form id="frm" class="form-horizontal">
                <div class="modal-body">
                    <div class="box-body" style="padding-bottom:0px">
                    	<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Jenis PIN</label>
                            <div class="col-sm-9">
                            	<?= form_dropdown('type_pin', $jns, '', 'class="form-control input-sm" id="type_pin" required'); ?>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Periode</label>
                            <div class="col-sm-9">
                            	<select name="periode" id="periode" data-placeholder="Periode" class="select required"> 
                                        <option value=""></option> 
                                        <?php foreach ($periode as $row) { ?>
                                        <option value="<?=$row->periode_id ?>" kode="<?=$row->periode_code ?>"><?= $row->periode_name.' / '.$row->periode_track_type.' ('.y_convert_date($row->periode_start_date,'d/m/Y').' - '.y_convert_date($row->periode_end_date,'d/m/Y').')'; ?></option>  
                                        <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Kode Diskon</label>
                            <div class="col-sm-9">
                                <select name="discount_code" id="discount_code" data-placeholder="Kode Diskon" class="form-control input-sm">
                                    <option value="">Kosongkan jika tidak terpakai</option>
                                    <?php foreach ($discount as $row) { ?>
                                        <option value="<?=$row->discount_id ?>"><?= $row->discount_code.' ('.y_convert_date($row->discount_start_date,'d/m/Y').' - '.y_convert_date($row->discount_end_date,'d/m/Y').')'; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    	<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Maksimal Pilih Prodi</label>
                            <div class="col-sm-9">
                            	<?= form_dropdown('pil_prodi', $pil_prodi, '', 'class="form-control input-sm" id="pil_prodi" required'); ?>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Harga</label>
                            <div class="col-sm-9">
                            	<input type="text" onkeyup="FormatCurrency(this)" class="form-control input-sm harga" name="harga" id="harga" maxlength="255" required>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Keterangan</label>
                            <div class="col-sm-9">
                            	<input type="text" class="form-control input-sm" name="desc" id="desc" maxlength="255" required>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Jumlah PIN</label>
                            <div class="col-sm-9">
                            	<div class="input-group">
                                    <span class="input-group-addon"><i class="icon-sort-numeric-asc"></i></span>
                                    <input type="text" class="form-control input-sm" name="count" id="count" maxlength="5" value="1" required>
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-primary btn-sm" onClick="generate_pin()">Generate dan Simpan</button>
                                    </span>
                                </div>                                
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Summary Hasil</label>
                            <div class="col-sm-9">
                            	<input type="hidden" name="pin_json" id="pin_json">
                                <textarea class="form-control input-sm" name="pin" id="pin" rows="13" readonly></textarea>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                </div>                
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="frmboxedit" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger-700" style="background-image:url(assets/images/backgrounds/bg.png)">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            	<h4 class="modal-title"><i class="fa fa-navicon"></i> &nbsp;<?= 'Form '.$title ?></h4>
            </div>
            <form id="frmedit" class="form-horizontal">
                <input type="hidden" name="id" id="id">
                <div class="modal-body">
                    <div class="box-body" style="padding-bottom:0px"> 
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Username</label>
                            <div class="col-sm-9">
                            	<input type="text" class="form-control input-sm" name="inp[pin_transaction_number]" id="pin_transaction_number" readonly>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Password</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="text" class="form-control input-sm" name="inp[pin_token]" id="pin_token" required>
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-primary btn-sm" onClick="generate_pin_edit()">Generate</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-labeled btn-xs pull-left" data-dismiss="modal">
                	<b><i class="icon-switch"></i></b> Batal
                </button>
        		<button type="button" class="btn btn-success btn-labeled btn-xs" id="act-update" onclick="update()">
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
<script type="text/javascript" src="assets/limitless/global/js/core/setting.js"></script> 


<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/styling/uniform.min.js"></script>

<script type="text/javascript">
var str		= ['A','C','D','E','F','G','H','J','K','L','M','N','P','Q','R','T','U','V','W','X','Y','1','2','3','4','5','6','7','8','9'] //B, I, O, Z, S
var year	= '<?= date('y'); ?>';
var tb 		= '#table';      
var baseurl = '<?= y_url_admin() ?>/generate_pin';


$.validator.addClassRules({ 
	harga: { 
		minlength : 1, 
	}, 
});   

$(document).ready(function() {	
	$("#type_pin").select2({
		minimumResultsForSearch: Infinity
	});
	$("#pil_prodi").select2({
		minimumResultsForSearch: Infinity
	});
	$("#periode").select2({
		minimumResultsForSearch: Infinity
	}); 

    $(tb).dataTable({
        'ajax': {
            'url':baseurl+'/json'
		},
		'order':[
			["7", 'asc']
		],
		'columnDefs': [ 
			{ 'targets': 'nosort',  'orderable': false },
			{ 'targets': 'nosearch', 'searchable': false },
			{ 'targets': 'center', 'className': 'center' }  
		],
		"drawCallback": function( settings ) {
			$(".chk").uniform({
				radioClass: 'choice',
				wrapperClass: 'border-primary-600 text-primary-800'
			});

			$(".reset").uniform({
				radioClass: 'choice',
				wrapperClass: 'border-primary-600 text-primary-800'
			});
		}
    });
	
	$('.dataTables_filter input[type=search]').attr('placeholder','Pencarian');

    $('.dataTables_length select').select2({
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });
	
	$('.dt-buttons').html('<button class="btn btn-sm btn-primary" onclick="add()"><i class="icon-add"></i> &nbsp;Tambah Data</button> <button class="btn btn-sm btn-warning" onclick="copies()"><i class="icon-files-empty"></i> &nbsp;Copy</button>  <button class="btn btn-sm btn-success" onclick="resets()"><i class="icon-reset"></i> &nbsp;Reset</button>');
	
	$('#frmbox').on('hidden.bs.modal', function () {
		$('#periode').val("").trigger('change');
		_reload();
	});
	
	$("#chk-all").change(function(){
		$('input.chk:checkbox').not(this).prop('checked', this.checked);
		$.uniform.update();
	});
	
	$("#reset-all").change(function(){
		$('input.reset:checkbox').not(this).prop('checked', this.checked);
		$.uniform.update();
	});
	
	$("#chk-all").uniform({
        radioClass: 'choice'
    });
	
	$("#reset-all").uniform({
        radioClass: 'choice'
    });
});

function generate_pin_edit()
{
	var password = '';
	for(p=1; p<=9; p++) password += str[Math.floor(Math.random()*str.length)];
	
	$('#pin_token').val(password);
}

function generate(n) {
        var add = 1, max = 12 - add;   // 12 is the min safe number Math.random() can generate without it starting to pad the end with zeros.   

        if ( n > max ) {
                return generate(max) + generate(n - max);
        }

        max        = Math.pow(10, n+add);
        var min    = max/10; // Math.pow(10, n) basically
        var number = Math.floor( Math.random() * (max - min + 1) ) + min;

        return ("" + number).substring(add); 
}

function generate_pin()
{
	if($("#frm").valid())
	{
		$('#pin').val('');
		
		// var pil_prodi 	= $('#pil_prodi').val(); 
		// var pcode 		= $('#periode option:selected').attr("kode");
		// var type_pin 	= $('#type_pin').val();
		// if(type_pin=='online') type_pin=1;
		// else  type_pin=2;
		
		var count = parseInt($('#count').val());
		if(count < 1) count = 1;
		
		insert(count);
		
		// var cdata  = parseInt($(tb).DataTable().page.info().recordsTotal) + parseInt(count); //total data existing + total data yang akan ditambah misal data existing(3) + data akan ditambah (100) = 103
		// var miles  = cdata.toString().length;	//total character // misal : 3 dari "103"
		// var param  = ( cdata/Math.pow(10,miles) ).toFixed(2); // (103/1000) lalu dilakukan pembulatan, hasilnya = 0.10
		 
		// console.log(cdata);
		// console.log(miles);
		// console.log(param);
		
		// if(miles <= 4)
		// {
			// if(param <= 0.5)
				// miles += 1;
			// else
				// miles += 2;
		// }
		// else
			// miles = 5;
		// for(var i=1; i<=count; i++)
		// {
			// //username		
			// // var inum = rand(miles);
			// // var itxt = inum.toString();
			// // var username_last = itxt.length >= 3 ? i : new Array(5 - itxt.length + 1).join('0') + itxt;
			
			
			// var delayInMilliseconds = 1500; //1 second 
			// setTimeout(function() {
				// var username = pcode+type_pin+pil_prodi+generate(3); 
				// //password
				// var password = '';
				// for(p=1; p<=9; p++) password += str[Math.floor(Math.random()*str.length)]; 
			   // insert(i, username, password);
			// }, delayInMilliseconds);
		// }
	}
}

function rand(length) 
{
    return Math.floor(Math.pow(10, length-1) + Math.random() * (Math.pow(10, length) - Math.pow(10, length-1) - 1));
} 

function insert(total)
{
	$.ajax({
		url:baseurl+'/insert',
		global:false,
		async:true,
		type:'post',
		dataType:'json',
		data: ({ total:total, type_pin : $('#type_pin').val(), desc : $('#desc').val(), periode : $('#periode').val(), periode_kode : $('#periode option:selected').attr("kode"), pil_prodi : $('#pil_prodi').val(), harga : $('#harga').val(), discount_code : $('#discount_code').val() }),
		success : function(e) {
			if(e.status == 'ok;') 
			{
				// var result = '- '+u+' : '+p;
				// var pin = $.trim($('#pin').val());
				// if(pin == '')
					// $('#pin').val(result);
				// else
					// $('#pin').val( $('#pin').val()+"\n"+result );
				
				
				$('#pin').val(e.text);
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
			$('#frmboxedit').modal({keyboard: false, backdrop: 'static'});
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

function update()
{
	$.ajax({
		url:baseurl+'/update',
		global:false,
		async:true,
		type:'post',
		dataType:'json',
		data: $('#frmedit').serialize(),
		success : function(e) {
			if(e.status == 'ok;') 
			{
				_reload();
				$("#frmboxedit").modal('hide');
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

function copies()
{
	var id = '';
	$('input.chk:checkbox:checked').each(function () {
		id += $(this).val()+',';
	});	
	
	if(id == '')
		alert('Pilih Salah Satu PIN');
	else
		copy(id.slice(0,-1));
}

function copy(id)
{		
	$.ajax({
		url:baseurl+'/copied',
		global:false,
		async:true,
		dataType:'json',
		type:'post',
		data: ({ id : id }),
		success: function(e) {
			if(e.status == 'ok;') 
			{
				$('<div class="modal fade" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-body">'+e.text+'</div></div></div></div>').modal();
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


function resets()
{
	var id = '';
	$('input.reset:checkbox:checked').each(function () {
		id += $(this).val()+',';
	});	
	
	if(id == '')
		alert('Pilih Salah Satu PIN');
	else
		reset(id.slice(0,-1));
}

function reset(id)
{		
	$.ajax({
		url:baseurl+'/reset',
		global:false,
		async:true,
		dataType:'json',
		type:'post',
		data: ({ id : id }),
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