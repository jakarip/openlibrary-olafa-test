<div class="panel panel-default">
    <div class="panel-heading">
        <h6 class="panel-title">Document</h6>
    </div>
    <div class="panel-body"> 
		<div class="row" style="margin:20px 15px;">	
			<div class="col-md-4"> 
				<div class="form-group"> 
					<?= form_dropdown('workflow', $workflow,'', 'class="form-control select2" id="workflow" required="required"') ?>
				</div>
			</div>  
			<div class="col-md-4">
				<div class="form-group">
					<select name="type" id="type" class="select"> 
							<option value="">Semua Jenis Pustaka</option> 
					</select> 
				</div>
			</div>
			
			<div class="col-md-4"> 
				<div class="form-group"> 
					<?= form_dropdown('status',  $status,'', 'class="form-control select2" id="status" required="required"') ?>
				</div>
			</div>
		</div> 
		<div class="row" style="margin:20px 15px;">	
			<div class="col-md-4"> 
				<div class="form-group"> 
					<input type="checkbox" name="attribute" id="attribute" value="1">&nbsp;&nbsp;<strong>Saya dapat mengubah dokumen</strong>
				</div>
			</div> 
			<div class="col-md-4"> 
				<div class="form-group"> 
					<input type="checkbox" name="onlyforme" id="onlyforme" value="1">&nbsp;&nbsp;<strong>Dokumen ditujukan hanya untuk saya</strong>
				</div>
			</div>  
			<div class="col-md-2">
				<div class="form-group">
					<select name="dates_acceptance_option" id="dates_acceptance_option" class="select"> 
							<option value="all">Semua Tanggal Pembuatan</option>
							<option value="date">Range Tanggal Pembuatan</option>
					</select> 
				</div>
			</div> 
			<div class="col-sm-2" >
				<input type="text" class="form-control input-sm" name="dates_acceptance" id="dates_acceptance" value="">
			</div>
		</div>  
		<div class="row" style="margin:20px 15px;">	 
			<div class="col-md-4"> 
				<div class="form-group"> 
					 <button type="button" class="btn btn-primary btn-labeled btn-xs" id="filter" >
						<b><i class="icon-search4"></i></b> Filter
					</button>
				</div>
			</div> 
		</div> 
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6"> 
            </div>
            <div class="col-md-6 text-right">
                
            </div>
        </div>

    </div> 
    <table class="table table-bordered table-striped table-hover" id="table">
        <thead>
            <tr>
                <th width="10%">Pembuat</th>
                <th width="10%">Workflow</th> 
                <th width="25%">Judul</th>
                <th width="10%">Subject</th>
                <th width="10%">Jenis</th> 
                <th width="10%">Permission</th>
                <th width="15%">State</th>
                <th width="5%">Status</th>
                <th width="5%">Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div> 


<?php $this->load->view('frontend/tpl_footer'); ?> 
<script type="text/javascript" src="assets/limitless/global/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/core/setting.js"></script> 
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/styling/uniform.min.js"></script>

<script type="text/javascript">
var tb 		= '#table';
var baseurl = 'index.php/document/lists2';

$(document).ready(function(){
    $(tb).dataTable({ 
        'ajax': {
            'url':baseurl+'/json',
			'data' : function(data) {
				data.workflow		= $('#workflow').val();
				data.type			= $('#type').val(); 
				data.status			= $('#status').val(); 
				data.attribute		= $('#attribute:checked').val(); 
				data.onlyforme		= $('#onlyforme:checked').val();
				data.dates_acceptance_option		= $('#dates_acceptance_option').val();
				data.dates_acceptance = $('#dates_acceptance').val(); 
			}
        },
        'order':[
            [0, 'desc']
        ],
        'columndefs': [
            { 'targets': [-1], 'searchable': false, 'orderable': false }
        ]
    });
	
		
	$('#dates_submission, #dates_logistic, #dates_acceptance').daterangepicker({  
		locale: {
			format: 'DD-MM-YYYY'
		},
		showDropdowns: true,
		opens: 'left',
		applyClass: 'bg-primary-600',
		cancelClass: 'btn-light'
	});
	
	$("#dates_submission_option, #dates_logistic_option, #dates_acceptance_option,#book_id_prodi").select2({
		minimumResultsForSearch: Infinity
	}); 
	
	$('#dates_submission, #dates_logistic, #dates_acceptance').hide(); 
	
	
	$('#book_date_prodi_submission, #book_date_logistic_submission, #book_date_acceptance').datepicker({
        format: "dd-mm-yyyy"
	}); 
	 
	 $("#dates_acceptance_option").change(function(){ 
		 if($(this).val()=='all') $("#dates_acceptance").hide(); 
		 else $("#dates_acceptance").show(); 
	 }); 

    $('.dataTables_filter input[type=search]').attr('placeholder','Pencarian');
	
	$('.dt-buttons').html('<a class="btn btn-sm btn-primary" href="index.php/document/lists2/add"><i class="icon-file-plus"></i> &nbsp;Tambah Data</a>');

    $('.dataTables_length select').select2({
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });
	
	$("#status,#type,#workflow").select2({
		minimumResultsForSearch: Infinity
	});

	$("#workflow").change(function(){ 
		$.ajax({ 
            url : baseurl+'/getknowledgetype',
            type: "POST",
            data: {
                'id' : $(this).val()
            },
            dataType: "JSON",
            beforeSend : function() {
                $('#loading-img').show();
            },
            complete : function() {
                $('#loading-img').hide();
            },
            success: function(dt)
            {
                $("#type").html('<option value="">Semua Jenis Pustaka</option>');
                $.each(dt,function(index, value){
                    $("#type").append('<option value="'+value.id+'">'+value.name+'</option>');
                });
				 $('#type').select2();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {

            }
        }); 
	});	
	
	 
	$("#filter").click(function(){  
		_reload();
	}); 
 
});


	
function add()
{ 
	$('#act-save').show();
	$('#act-update').hide();
	$('#frmbox').modal({keyboard: false, backdrop: 'static'});
}
 

 
function validate_online(id,no)
{ 
    if(confirm('No. Transaksi : '+no+'.\nApakah anda yakin akan melakukan konfirmasi pembayaran ?')) {
        $.ajax({
            url:baseurl+'/save_validate',
            global:false,
            async:true,
            dataType:'json',
            type:'post',
            data: ({ id : id, no : no }),
            success: function(e) { 
                _reload();
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
	if(confirm('ID: '+id+'\nData: '+txt+'\nApakah anda yakin akan menghapus data tersebut ?')) {
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
 

function save()
{
    
    if(confirm('Durasi Berlangganan : '+$("#subscribes").val()+' Bulan.\nApakah anda yakin akan berlangganan dengan durasi tersebut ?')) {
        if($("#frm3").valid())
        {
            $.ajax({
                url:baseurl+'/save',
                global:false,
                async:true,
                type:'post',
                dataType:'json',
                data: $('#frm3').serialize(),
                success : function(e) {
                    if(e.status == 'ok;')
                    {
                        $("#frmbox").modal('hide');
                        window.location.reload();
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
}

function save_validate()
{
    if($("#frm2").valid())
    {
        var formData = new FormData($('#frm2')[0]);
        $.ajax({
            url:baseurl+'/save_validate',
            global:false,
            async:true,
            type:'post',
            data: formData,
            contentType: false,//untuk upload image
            processData: false,//untuk upload image
            dataType:'json',
            success : function(e) {
                if(e.status == 'ok;')
                {
                    $("#frmboxvalidasi").modal('hide');
                    alert("Terima Kasih telah melakukan validasi pembayaran.\nSilahkan cek email anda untuk mendapatkan token dan melanjutkan registrasi setelah admin kami melakukan verifikasi.");
                    //window.location.href = 'questionnaire';
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

function save_validate_affiliate()
{
    if($("#frm3").valid())
    {
        $.ajax({
            url:baseurl+'/save_validate_affiliate',
            global:false,
            async:true,
            type:'post',
            dataType:'json',
            data: $('#frm3').serialize(),
            success : function(e) {
                if(e.status == 'ok;')
                {
                    $("#frmboxaffiliate").modal('hide');
                    window.location.href = 'questionnaire';
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

function save_affiliate_online()
{
    if($("#frm4").valid())
    {
        $.ajax({
            url:baseurl+'/save_affiliate_online',
            global:false,
            async:true,
            type:'post',
            dataType:'json',
            data: $('#frm4').serialize(),
            success : function(e) {
                if(e.status == 'ok;')
                {
                    $("#frmbox").modal('hide');
                    window.location.reload();
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

function reg(type, pin, step, print)
{
    $.ajax({
        url:'reg',
        global:false,
        async:true,
        dataType:'json',
        type:'post',
        data: ({ pin_type : type, pin : pin, pin_step : step, pin_print : print }),
        success: function(e) {
            if(e.status == 'ok;')
                window.location.href = e.callback;
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

function _reset_frm()
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

function convertToRupiah(angka)
{
    var rupiah = '';
    var angkarev = angka.toString().split('').reverse().join('');
    for(var i = 0; i < angkarev.length; i++) if(i%3 == 0) rupiah += angkarev.substr(i,3)+'.';
    return 'Rp. '+rupiah.split('',rupiah.length-1).reverse().join('');
}
</script>