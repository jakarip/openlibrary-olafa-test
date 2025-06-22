<?php 
 $date = date("y");
 $awal = $date-5;
 $tahunajar = array();
 for($i=$awal;$i<=($date+1);$i++){ 
	$tahunajar[$i.($i+1)] = $i.'/'.($i+1); 
 }  
?>

<div class="panel panel-default flat">
	
	<div class="row" style="margin:20px 15px;">  
		<div class="col-md-3">
			<div class="form-group">
				<input type="text" id="date_choice" readonly name="date_choice" class="form-control" placeholder="Tanggal Kelulusan">
			</div> 
		</div>  
		<div class="col-md-3">
			<div class="form-group">
				 <input type="text" id="date_sync" readonly name="date_sync" class="form-control" placeholder="Tanggal Sinkronisasi">
			</div> 
		</div>  
		<div class="col-md-4">
			<div class="form-group">
				<select name="track" id="track" class="select"> 
						<option value="0">Semua Jalur</option>
						<?php foreach ($track as $key => $row) { ?>
						<option value="<?=$row->periode_id ?>"><?= $row->periode_name.' / '.$row->periode_track_type.' ('.y_convert_date($row->periode_start_date,'d/m/Y').' - '.y_convert_date($row->periode_end_date,'d/m/Y').')'; ?></option>  
						<?php } ?>
				</select> 
			</div>
		</div> 
		<div class="col-md-2">
			<div class="form-group">
				 <button type="button" class="col-md-5 btn btn-success btn-xs" onclick="clear_btn()" >
					Clear
				</button>
			</div> 
		</div> 
	</div>
	<div class="row" style="margin:20px 15px;">	
		<div class="col-md-3">
			<div class="form-group">
				<select name="tahunajar" id="tahunajar" class="select"> 
						<option value="0">Pilih Tahun Ajar</option>
						<?php foreach ($tahunajar as $key => $row) { ?>
						<option value="<?=$key ?>"><?= $row ?></option>  
						<?php } ?>
				</select> 
			</div>
		</div> 
	</div>
    <table class="table table-bordered table-striped table-hover table-xs" id="table">
        <thead>
            <tr>
                <th width="5%" class="nosort center" style="text-align:center"><input type="checkbox" id="chk-all"></th>
                <th width="10%">Sinkronisasi</th>
                <th width="15%">Nama</th>
                <th width="20%">Asal Sekolah</th>  
                <th width="20%">Program Studi</th>
                <th width="10%">Tanggal Kelulusan</th>
                <th width="10%" >Tanggal Sinkronisasi</th>
                <th width="10%" class="nosort center">&nbsp;</th>
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
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/styling/uniform.min.js"></script>

<script type="text/javascript">
var str		= ['A','C','D','E','F','G','H','J','K','L','M','N','P','Q','R','T','U','V','W','X','Y','1','2','3','4','5','6','7','8','9'] //B, I, O, Z, S
var year	= '<?= date('y'); ?>';
var tb 		= '#table';      
var baseurl = '<?= y_url_admin() ?>/exports';

$(document).ready(function() {	

	 $('.select').select2({
        minimumResultsForSearch: Infinity
    });
	
	$('#date_choice').daterangepicker({ 
				singleDatePicker: true,
		locale: {
			format: 'DD/MM/YYYY'
		}
	}); 
	
	$('#date_sync').daterangepicker({ 
				singleDatePicker: true,
		locale: {
			format: 'DD/MM/YYYY'
		}
	});      
	

	$("#track, #date_choice, #date_sync").change(function(){ 
		_reload();
	});
	
	$('#date_sync').val("");
	$('#date_choice').val("");
	

    $(tb).dataTable({ 
		'ajax': {
			'url'			: baseurl+'/json',
			'dataType' 		: 'json',
			'type'			: 'POST',
			'data' : function(data) {
				data.date_sync	= $('#date_sync').val();
				data.date_choice 	= $('#date_choice').val();
				data.track 	= $('#track').val();
			}
		},
		'order':[
			[1, 'desc']
		],
		'columnDefs': [ 
			{ 'targets': 'nosort', 'searchable': false, 'orderable': false },
			{ 'targets': 'center', 'className': 'center' }  
		],
		"drawCallback": function( settings ) {
			$(".chk").uniform({
				radioClass: 'choice',
				wrapperClass: 'border-primary-600 text-primary-800'
			});
		},
		'scrollX': true
    });
	
	$('.dataTables_filter input[type=search]').attr('placeholder','Pencarian');

    $('.dataTables_length select').select2({
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });
	
	$('.dt-buttons').html('<button class="btn btn-sm btn-warning" onclick="copies()"><i class="icon-database-export"></i> &nbsp;Export</button> <button class="btn btn-sm bg-pink" onclick="sync_master()"><i class="icon-sync"></i> &nbsp;Sync Master Data</button>');
	
	$('#frmbox').on('hidden.bs.modal', function () {
		_reload();
	});
	
	$("#chk-all").change(function(){
		$('input.chk:checkbox').not(this).prop('checked', this.checked);
		$.uniform.update();
	});
	
	$("#chk-all").uniform({
        radioClass: 'choice'
    });
}); 

function clear_btn()
{
	$('#date_sync').val("");
	$('#date_choice').val("");
	$('#track').val("0").trigger("change"); 
}   

function copies()
{
	var id = '';
	$('input.chk:checkbox:checked').each(function () {
		id += $(this).val()+',';
	});	 
	if(id == '')
		alert('Pilih salah satu data');
	else {
		if($("#tahunajar").val()=='')  alert('Pilih Tahun Ajar Terlebih Dahulu');
		else {
			if(confirm('Apakah anda yakin akan mengeksport data ke igracias ?')) {
				$('input.chk:checkbox:checked').each(function () {
					exports($(this).val());
				});	  
			}
		}
	}
}

function copy(id)
{	
	if($("#tahunajar").val()=='0')  alert('Pilih Tahun Ajar Terlebih Dahulu');
	else {
		if(confirm('Data: '+id+'\nApakah anda yakin akan mengeksport data ke igracias ?')) {
			exports(id);
		}
	}
}	

function exports(id)
{	  
	var tahunajar = $("#tahunajar").val();

	if(tahunajar == '')
    {
        alert('Silahkan pilih tahun ajar terlebih dahulu');
        return false;
    }

    $.ajax({
        url:baseurl+'/doexport',
        global:false,
        async:true,
        dataType:'json',
        type:'post',
        data: ({ id : id, ta : tahunajar }),
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

function sync_master()
{
    if(confirm('Apakah anda yakin akan melakukan sync master data ?')) {
        $.ajax({
            url: baseurl + '/doexport_master',
            global: false,
            async: true,
            dataType: 'json',
            type: 'post',
            //data: ({ id : id,ta : $("#tahunajar").val() }),
            success: function (e) {
                if (e.status == 'ok;') {
                    alert("Log:\n"+e.text);
                    //_reload();
                }
                else alert(e.text);
            },
            error: function () {
                alert('<?= $this->config->item('alert_error') ?>');
            },
            beforeSend: function () {
                $('#loading-img').show();
            },
            complete: function () {
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