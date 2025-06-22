<div class="panel panel-default flat">
    <form id="frm" class="form-horizontal">
		<input type="hidden" name="id" id="id">
		<div class="modal-body">
			<input type="hidden" id="inc" name="inc" value="1"> 
			<?php $iuser = $this->session->userdata('user'); ?>
			<div class="box-body" style="padding-bottom:0px">
				<div class="alert alert-info alert-styled-left alert-dismissible"> 
					<span class="font-weight-semibold"><strong>Hai <?= $iuser['fullname']; ?>,</span> <br>Menu Self Loan ini digunakan untuk melakukan peminjaman buku secara mandiri pada saat Anda sedang berada di Perpustakaan.<br><br>
					Prosedurnya :<br>
					1. Silahkan memilih buku yang ada di rak.<br>
					2. Silahkan inputkan kode barcode yang ada di buku lalu tekan tombol "Proses".<br>
					3. Ketika sudah menginputkan semua buku yang akan dipinjam, Silahkan klik tombol "Simpan". <br>
					4. Silahkan menuju meja sirkulasi untuk melakukan unlock security pada buku yang akan dipinjam agar ALARM tidak berbunyi saat melewati gate keluar.</strong>
				</div>
				<div class="form-group">
					<label class="col-lg-3 control-label">Barcode :</label>
					<div class="col-lg-4">
						<input type="text" class="form-control input-sm" name="barcode" id="barcode" placeholder=' input barcode koleksi yang akan dipinjam'>
					</div>
					<div class="col-lg-1"><button type="button" class="btn btn-primary" onclick="process()"> <i class="icon-arrow-right14 position-left"></i> Proses</button></div>
				</div>
				<div class="form-group"> 
					<div class="col-lg-12"> &nbsp;
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-3 control-label">Peminjaman Baru :</label>
					<div class="col-lg-9"> 
						<table class="table table-bordered table-striped table-hover">
							<thead>
								<tr> 
									<th width="5%">No.</th>
									<th width="20%">Barcode</th>
									<th width="45%">Judul</th>
									<th width="20%">Lama Peminjaman (Hari)</th> 
									<th width="10%"></th> 
								</tr> 
							</thead>
							<tbody id="newrent">
							</tbody>
						</table>
					</div>
				</div>    
				<div class="form-group"> 
					<div class="col-lg-12"> &nbsp;
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-3 control-label">Peminjaman yang belum dikembalikan :</label>
					<div class="col-lg-9">
						<table class="table table-bordered table-striped table-hover" id="table">
							<thead>
								<tr> 
									<th width="5%" rowspan="2">No. </th>
									<th width="20%" rowspan="2">Barcode</th>
									<th width="45%" rowspan="2">Judul</th>
									<th width="30%" colspan="2">Peminjaman</th> 
								</tr>
								<tr>  
									<th width="15%">Tanggal Pinjam</th>
									<th width="15%">Tanggal Harus Kembali</th> 
								</tr>
							</thead>
							<tbody> 
								<?php foreach($book as $key=>$row){ $no = $key + 1; ?>
							
								<tr class="oldrent"> 
									<td><?=$no ?></td>
									<td><?=$row->code ?></td>
									<td><?=$row->title ?></td>
									<td><?=$row->rent_date ?></td>
									<td><?=$row->return_date_expected ?></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>    
				<div class="form-group"> 
					<div class="col-lg-12"> &nbsp;
					</div>
				</div> 
				<div class="form-group"> 
					<div class="col-lg-12"> 
						<button type="button" class="btn btn-success" onclick="save()" ><i class="icon-floppy-disk position-left"></i> Simpan</button>
					</div>
				</div>   
			</div><!-- /.box-body -->
		</div>                
	</form> 
</div> 

<?php $this->load->view('frontend/tpl_footer'); ?>
<!-- Theme JS files -->	
<script type="text/javascript" src="assets/limitless/global/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/core/setting.js"></script> 
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>

<script type="text/javascript">
   
var baseurl = 'selfloan';

$(document).ready(function() {	
    $("#barcode").keypress(function(event) {
		var keycode = event.keyCode || event.which;
		if(keycode == '13') {
			$.ajax({
				url:baseurl+'/check',
				global:false,
				async:true,
				type:'post',
				dataType:'json',
				data: $('#frm').serialize(),
				success : function(e) {
					if(e.status == 'ok;') 
					{
						// _reload();
						// $("#frmbox").modal('hide');
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
	});
});
	
function add()
{
	_reset();
	$('#act-save').show();
	$('#act-update').hide();
	$('#frmbox').modal({keyboard: false, backdrop: 'static'});
}

function process()
{ 
	$.ajax({
		url:baseurl+'/process',
		global:false,
		async:true,
		dataType:'json',
		type:'post',
		data: $('#frm').serialize(),
		success: function(e) {
			$('#loading-img').hide()
			if(e.status == 'ok;') 
			{
				var inc = $("#inc").val(); 
				var status = '';
				$('.code').each(function(i, obj) {
					if(obj.value == e.text.barcode) status= 'duplicate';
				});
				
				var total = $('.code').length + $('.oldrent').length + 1;
				 
				if(total > e.text.quantity) alert('Anda sudah melewati batas jumlah pustaka yang dapat dipinjam. Maksimal pustaka yang dapat dipinjam adalah '+e.text.quantity);
				else if(status=='duplicate') alert('Pustaka sudah ada di daftar peminjaman');
				else {
					$("#newrent").append('<tr id="list'+inc+'"><td>'+inc+'<input class="code" type="hidden" name="code[]" value="'+e.text.barcode+'"><input type="hidden" name="stockid[]" value="'+e.text.id+'"></td><td>'+e.text.barcode+'</td><td>'+e.text.title+'</td><td>'+e.text.duration+'</td><td><a href="javascript:deletes(\''+inc+'\')" title="Edit Data" class="btn btn-xs btn-icon btn-primary"><i class="icon-bin"></i></a></td></tr>'); 
					inc++;
					$("#inc").val(inc);
					$("#barcode").val('');
				}
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


function save()
{
		if($('.code').length!=0){
			$.ajax({
			url:baseurl+'/save',
			global:false,
			async:true,
			dataType:'json',
			type:'post',
			data: $('#frm').serialize(),
			success: function(e) {
				$('#loading-img').hide()
				if(e.status == 'ok;') 
				{
					alert('Peminjaman Anda telah berhasil dilakukan.\nSilahkan menuju meja sirkulasi untuk melakukan unlock security pada buku yang dipinjam');
					 location.reload();
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
	else alert('Anda belum memasukkan list Pustaka yang akan di pinjam');
}



function deletes(id)
{ 
	$("#list"+id).remove();
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
				if(key != 'component_custom')
					$('#'+key).val(value);
			});
			
			if(e.component_custom == 1)
				$('#component_custom').prop('checked', true);
			
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

// function save(url)
// {
	// if($("#frm").valid())
	// {
		// $.ajax({
			// url:baseurl+'/'+url,
			// global:false,
			// async:true,
			// type:'post',
			// dataType:'json',
			// data: ({id : id, mode : mode }),
			// success : function(e) {
				// if(e.status == 'ok;') 
				// {
					// _reload();
					// $("#frmbox").modal('hide');
				// } 
				// else alert(e.text);
			// },
			// error : function() {
				// alert('<?= $this->config->item('alert_error') ?>');	 
			// },
			// beforeSend : function() {
				// $('#loading-img').show();
			// },
			// complete : function() {
				// $('#loading-img').hide();
			// }
		// });
	// }
// }

function active(id, txt, mode)
{
	if(confirm('Data: '+txt+'\nApakah anda yakin akan '+(mode == 1 ? 'Aktifkan' : 'Non Aktifkan')+' data tersebut ?')) {
		$.ajax({
			url:baseurl+'/active',
			global:false,
			async:true,
			type:'post',
			dataType:'json',
			data: ({id : id, mode : mode }),
			success: function(e) { 
				if(e.status == 'ok;') 
				{
					_reload('#table_course');
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