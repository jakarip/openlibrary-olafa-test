<style>
.ui-datepicker-calendar {
    display: none;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="panel">
            <div class="panel-header bg-red">
			<h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong> </h3>
            </div>
            <div class="panel-content pagination2"> 
				
			<form id="forms" class="form-horizontal" action="" method="post">   
				<div class="form-group">
					<label class="col-sm-2 control-label"><?php echo getLang('choose_date')?></label>
					<div class="col-sm-5 prepend-icon"> 
						 <input type="text" name="dates" class="form-control" id="dates" placeholder="<?php echo getLang('choose_date')?>" aria-describedby="inputSuccess2Status"><i class="fa fa-calendar"></i>
					</div>  
				</div> 		  
				<div class="form-group">
					<label class="col-sm-2 control-label"> </label>
					<label class="col-sm-10 control-label"><button type="button" value="submit" id="submitdate" name="submit" class="btn btn-success">Filter</button>&nbsp;<button type="button" name="download"id="download" class="btn btn-primary">Download</button></label> 
				</div> 
				
			</form> 
				<div class="row content_button">
					<div class="col-lg-6" >
						<a href="javascript:;" onclick="add()" class="btn btn-danger">
							 <i class="fa fa-plus-square"></i><?php echo getLang("add").' '. getCurrentMenuName() ?>
						</a>
					</div>
				</div>
                <table id="table-supplier" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%"><?php echo getLang('no'); ?></th>
                            <th width="25%"><?php echo getLang('No Surat'); ?></th>
                            <th width="20%"><?php echo getLang('Anggota'); ?></th>
                            <th width="20%"><?php echo getLang('Nama'); ?></th> 
                            <th width="20%"><?php echo getLang('Judul'); ?></th>
                            <th width="20%"><?php echo getLang('Author'); ?></th>
                            <th width="20%"><?php echo getLang('Create Date'); ?></th> 
                            <th class="text-center" width="10%"><?php echo getLang('action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
 
<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-red">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
                <strong><h4 class="modal-title"></h4></strong>
            </div>
            <form id="form" class="form-horizontal form-validation">
            <input type="hidden" name="id" id="id">
            <div class="modal-body"> 
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo getLang('name'); ?></label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="inp[member_number]" id="member_number" placeholder="<?php echo getLang('name'); ?>" >
                    
                    </div>
                </div>   
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo getLang('Judul'); ?></label>
                    <div class="col-sm-10 prepend-icon">
                        <input class="form-control" type="text" name="inp[donated_item_title]" id="donated_item_title" placeholder="<?php echo getLang('Judul'); ?>" required>
                        <i class="fa fa-file-o"></i>
                    </div>
                </div>   
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo getLang('Pengarang'); ?></label>
                    <div class="col-sm-10 prepend-icon">
                        <input class="form-control" type="text" name="inp[donated_item_author]" id="donated_item_author" placeholder="<?php echo getLang('Pengarang'); ?>" required>
                        <i class="fa fa-file-o"></i>
                    </div>
                </div>     
            </div>
            <div class="modal-footer"> 
			    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
			    <button type="button" class="btn btn-success" onclick="save()"><i class="fa fa-save"></i>&nbsp;&nbsp;<?php echo getLang("save") ?></button>
            </div>
            </form>
        </div>
    </div>
</div>

<?php $this->load->view('theme_footer'); ?>

<script type="text/javascript"> 
var save_method; 
var table = "#table-supplier";
var form = $('#form');  
form.validate({       
	ignore: ""
}); 
$(document).ready(function(){
	$('#member_number').tokenInput("index.php/sbkpprint2/auto_data", {
		minChars: 5,
		tokenLimit: 1,
		preventDuplicates: true,
		onDelete: function (item) { 	
			$("#result").html('');
		},
		hintText:"Search username / nim / nama lengkap mahasiswa",
		onAdd: function (item) {
			
		}
	}); 

	
	
	
	$(table).DataTable({     
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],  
		"pageLength": 25,
		"processing": true,  
		"serverSide": true,  
		"destroy": true,  
		"order": [],  
		"ajax": {
			"url": "<?php echo site_url('index.php/sbkpprint2/ajax_json')?>",
			"type": "POST",
			"data" : function(data) {
				data.dates			= $('#dates').val();
			}
		}, 
		"columnDefs": [
			{ 
				"targets": [ 0,-1 ], 
				"orderable": false,  
			},
		]
	});
	
	$('.dataTables_filter input').unbind().bind('keyup', function(e) {
	   if(e.keyCode == 13) {
		$("#table-supplier").dataTable().fnFilter(this.value);
		}
	});
	
	
	
    $('.b-datepickers').each(function () {
        $(this).bootstrapDatepicker({
			format: 'dd-mm-yyyy',
            startView: $(this).data('view') ? $(this).data('view') : 0, // 0: month view , 1: year view, 2: multiple year view
            language: $(this).data('lang') ? $(this).data('lang') : "en",
            forceParse: $(this).data('parse') ? $(this).data('parse') : false,
            daysOfWeekDisabled: $(this).data('day-disabled') ? $(this).data('day-disabled') : "", // Disable 1 or various day. For monday and thursday: 1,3
            calendarWeeks: $(this).data('calendar-week') ? $(this).data('calendar-week') : false, // Display week number 
            autoclose: $(this).data('autoclose') ? $(this).data('autoclose') : false,
            todayHighlight: $(this).data('today-highlight') ? $(this).data('today-highlight') : true, // Highlight today date
            toggleActive: $(this).data('toggle-active') ? $(this).data('toggle-active') : true, // Close other when open
            multidate: $(this).data('multidate') ? $(this).data('multidate') : false, // Allow to select various days
            orientation: $(this).data('orientation') ? $(this).data('orientation') : "top", // Allow to select various days,
            rtl: $('html').hasClass('rtl') ? true : false
        });
    });

	$( "#submitdate" ).click(function( event ) {
		$(table).dataTable().fnDraw();
	}); 
	 
	$('#type').select2({  
		allowClear: true, 
		tags: true,
		tokenSeparators: [',', ' '] 
	});
	 
	$('#kasifikasi').select2();
	
	
	$('#dates').dateRangePicker(
	{
		showShortcuts: false,
		format: 'DD-MM-YYYY'
	}); 
	
    $("#download").click(function() {
		if($("#dates").val()!=""){
			showLoading();
			$.ajax({
				type : "POST",
				url: "index.php/sbkpprint2/lists_excel",
				dataType:'JSON',
				data : {
					dates :  $("#dates").val(),
				},
				success: function(result){ 
					
					hideLoading();
					window.location.href="index.php/sbkpprint2/download/"+result;
				}  
			}); 
		}
		else alert("Silahkan pilih tanggal terlebih dahulu");
	});  
	
});  
 

function add() {
	save_method = 'add';
	reset(); 
	$('#modal_form').modal({keyboard: false, backdrop: 'static'});
	$('#modal_form .modal-title').html('<i class="fa fa-plus-square"></i>&nbsp;&nbsp;<?php echo getLang("add")?>'); 
} 

function edit(id){
  save_method = 'update';
  reset();

	  $.ajax({
		url : "<?php echo site_url('index.php/sbkpprint2/edit')?>",
		type: "POST",
		data : {
			id : id
		},
		dataType: "JSON",
		success: function(data){
			$('#modal_form #id').val(data.eproc_edition_id);  
			$('#modal_form #start').val(data.datestart);  
			$('#modal_form #end').val(data.datefinish);  
			 $.each(data, function(key, value) {
				$('#modal_form #'+key).val(value);
			}); 
			 
			$('#modal_form').modal({keyboard: false, backdrop: 'static'});
			$('#modal_form .modal-title').html('<i class="fa fa-pencil-square-o"></i>&nbsp;&nbsp;<?php echo getLang("edit")?>');
		},
		error: function (jqXHR, textStatus, errorThrown){
			alert('Error get data from ajax');
		}
	});
}

function save(){
	var url;
	if(save_method == 'add'){
		url = "<?php echo site_url('index.php/sbkpprint2/insert')?>";
	}else{
		url = "<?php echo site_url('index.php/sbkpprint2/update')?>";
	}

	if (form.valid()) {
		$.ajax({
			url : url,
			type: "POST",
			data: form.serialize(),
			dataType: "JSON",
			beforeSend : function() {
				showLoading(); 
			},
			complete : function() {
				hideLoading();
			},
			success: function(data){
				if (data.status=='true'){
					location.reload();
					$('#modal_form').modal('hide');  
				}
				else if (data.status=='denda'){
					info_alert('warning','Masih ada denda atau buku yang dipinjam');
				}else {
					info_alert('warning','gagal');
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				info_alert('warning','<?php echo getLang("error_xhr")?>');
			}
		});
	}
}   
 

function reset() {
    form.validate().resetForm();   
	$("#modal_form #form :input").removeData("previousValue");	//remove remote jquery validate previous value  
    form[0].reset();            
    $("label.error").hide();
    $(".error").removeClass("error");
} 

function reload() {
   table.draw();
}
</script>