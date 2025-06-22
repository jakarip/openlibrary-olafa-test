<div class="row">
    <div class="col-lg-12">
        <div class="panel">
            <div class="panel-header bg-red">
			<h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong> </h3>
            </div>
            <div class="panel-content pagination2">  
                <table id="table-supplier" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%"><?php echo getLang('no'); ?></th>
                            <th width="10%"><?php echo getLang('code'); ?></th>
                            <th width="30%"><?php echo getLang('title'); ?></th>
                            <th width="13%"><?php echo getLang('author'); ?></th> 
                            <th width="13%"><?php echo getLang('editor'); ?></th> 
                            <th width="7%"><?php echo getLang('jurnal status'); ?></th> 
                            <th width="7%"><?php echo getLang('jurnal eproc status'); ?></th> 
                            <th class="text-center" width="15%"><?php echo getLang('action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> 

<?php $this->load->view('theme_footer'); ?>

<script type="text/javascript"> 
var save_method; 
var table;
var form = $('#modal_form #form');


$(document).ready(function(){
	table = $('#table-supplier').DataTable({     
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],  
		"pageLength": 25,
		"processing": true,  
		"serverSide": true,  
		"destroy": true,  
		"order": [],  
		"ajax": {
			"url": "<?php echo site_url('index.php/removejournal/ajax_data')?>",
			"type": "POST"
		}, 
		"columnDefs": [
			{ 
				"targets": [ 0,-2,-3,-1 ], 
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
		url : "<?php echo site_url('index.php/removejournal/edit')?>",
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

function journal(id,status){
	  $.ajax({
		url : "<?php echo site_url('index.php/removejournal/journal')?>",
		type: "POST",
		data : {
			id : id,
			status : status
		},
		dataType: "JSON",
		beforeSend : function() {
			showLoading();
		},
		complete : function() {
			hideLoading();
		},
		success: function(data){
			reload();
		},
		error: function (jqXHR, textStatus, errorThrown){
			alert('Error get data from ajax');
		}
	});
}

function eproc(id,status){
	  $.ajax({
		url : "<?php echo site_url('index.php/removejournal/eproc')?>",
		type: "POST",
		data : {
			id : id,
			status : status
		},
		dataType: "JSON",
		beforeSend : function() {
			showLoading();
		},
		complete : function() {
			hideLoading();
		},
		success: function(data){
			reload();
		},
		error: function (jqXHR, textStatus, errorThrown){
			alert('Error get data from ajax');
		}
	});
}

function save(){
	var url;
	if(save_method == 'add'){
		url = "<?php echo site_url('index.php/removejournal/insert')?>";
	}else{
		url = "<?php echo site_url('index.php/removejournal/update')?>";
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
				if (data.status){
					reload(); 
					$('#modal_form').modal('hide');  
				}
				else info_alert('warning','<?php echo getLang("your_supplier_code_already_taken_or_use")?>');
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