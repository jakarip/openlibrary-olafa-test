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
							<th width="5%"><?php echo getLang("no") ?></th>
							<th width="20%"><?php echo getLang("faculty") ?></th>
							<th width="41%"><?php echo getLang("study_program") ?></th>
							<th width="24%"><?php echo getLang("journal") ?></th> 
							<th width="10%"><?php echo getLang("action") ?></th>
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
            <div class="modal-body">
				<div class="row">
					
					<div class="col-lg-5 portlets">
					<div class="panel">
						<div class="panel-header bg-red">
							<h3> <strong><?php echo getLang("list jurnal"); ?></strong> </h3>
						</div>
						<div class="panel-content pagination2">
							<div class="filter-left">
								<form id="form_all_user_list">
									<input type="hidden" name="id" class="prodi_code">  
									<table id="all_user_list" class="table table-striped table-bordered">
										<thead>
											<tr>
												<th width="10%"><input type="checkbox" name="all" class="form-control all"></th>
												<th width="20%"><?php echo getLang("journal") ?></th>
												<th width="65%"><?php echo getLang("url") ?></th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</form>
							</div>
						</div>
					</div>
					</div>
					
					<div class="col-lg-2 portlets"> 
								<div class="text-center" style="margin-top:200px;">
									<button type="button" class="btn btn-danger" id="right"  onclick="right()" title="<?php echo getLang("delete_course") ?>"><i class="fa fa-arrow-right"></i></a></button><br>
									<button type="button" class="btn btn-danger" id="left" onclick="left()" title="<?php echo getLang("insert_course") ?>"><i class="fa fa-arrow-left"></i></a></button>
								</div>
					</div>
					<div class="col-lg-5 portlets">
					<div class="panel">
						<div class="panel-header bg-red">
							<h3> <strong><?php echo getLang("list jurnal termapping"); ?></strong> </h3>
						</div>
						<div class="panel-content pagination2">
							<div class="filter-left"> 
								<form id="form_registered_user_list">
									<input type="hidden" name="id"  class="prodi_code"> 
									<table id="registered_user_list" class="table table-striped table-bordered">
										<thead>
											<tr>
												<th width="10%"><input type="checkbox" name="all" class="form-control all"></th>
												<th width="20%"><?php echo getLang("journal") ?></th>
												<th width="65%"><?php echo getLang("url") ?></th>
											</tr>
										</thead>
									</table>
								</form>
							</div>
						</div>
					</div>
					</div>
				</div>
            </div>
            <div class="modal-footer"> 
			    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button> 
            </div>
            </form>
        </div>
    </div>
</div>

<?php $this->load->view('theme_footer'); ?>

<script type="text/javascript"> 
var save_method; 
var table;
var form = $('#modal_form #form'); 

$(document).ready(function(){
   $('.all').click(function(){
      $('tbody input[type="checkbox"]', $(this).parents('table')).prop('checked', this.checked);
   });

	table = $('#table-supplier').DataTable({     
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],  
		"pageLength": 25,
		"processing": true,  
		"serverSide": true,  
		"destroy": true,  
		"order": [],  
		"ajax": {
			"url": "<?php echo site_url('index.php/internationalonlineperprodi/ajax_dt')?>",
			"type": "POST"
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
	
});  
 

function add() {
	save_method = 'add';
	reset(); 
	$('#modal_form').modal({keyboard: false, backdrop: 'static'});
	$('#modal_form .modal-title').html('<i class="fa fa-plus-square"></i>&nbsp;&nbsp;<?php echo getLang("add")?>'); 
} 

function edit(id){ 
  dt_all_user_list(id);
  dt_registered_user_list(id);

  $('.prodi_code').val(id);

  $('#modal_form').modal({keyboard: false, backdrop: 'static'});
  $('#modal_form .modal-title').html('<i class="fa fa-pencil-square-o"></i>&nbsp;&nbsp;<?php echo getLang("edit")?>')
}


function dt_all_user_list(study_program) {  
	return $('#all_user_list').DataTable({         
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],  
		"pageLength": 10,
        "processing": true,  
        "serverSide": true,   
		"destroy": true,  
        "order": [],   
        "ajax": {
            "url"	: 'index.php/internationalonlineperprodi/ajax_not_list',
            "type"	: 'POST',
			"data"	: 	{ 
							study_program 	: study_program
						}
        }, 
        "columnDefs": [
			{ 
				"targets": [0], 
				"orderable": false,  
			},
        ],
    }); 
}

function dt_registered_user_list(study_program) {    
	return $('#registered_user_list').DataTable({         
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],  
		"pageLength": 10,
        "processing": true,  
        "serverSide": true,  
		"destroy": true,  
        "order": [],   
        "ajax": {
            "url"	: 'index.php/internationalonlineperprodi/ajax_list',
            "type"	: 'POST',
			"data"	: 	{ 
							study_program 	: study_program
						}
        }, 
        "columnDefs": [
			{ 
				"targets": [ 0], 
				"orderable": false,  
			},
        ],
    }); 
}

function right() {  
	var total=$('#all_user_list tbody input[name="inp[id][]"]:checked').length;
	if (total==0) info_alert('warning','<?php echo getLang("min_selected_one_course")?>');
	else {
		$.ajax({
			url : 'index.php/internationalonlineperprodi/insert_course',
			type: "POST",
			data: $("#form_all_user_list").serialize(),
			dataType: "JSON",
			beforeSend : function(){ 
				showLoading();
			},
			complete : function(){
				hideLoading();
			}, 
			success: function(data)
			{ 
				hideLoading();
				reload();
				dt_all_user_list($(".prodi_code").val());
				dt_registered_user_list($(".prodi_code").val());
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				info_alert('warning','<?php echo getLang("error_xhr")?>');
			}
		});
	} 
} 

function left() { 
	var total=$('#registered_user_list tbody input[name="inp[id][]"]:checked').length;
	
	if (total==0) info_alert('warning','<?php echo getLang("min_selected_one_course")?>');
	else {
		$.ajax({
			url : 'index.php/internationalonlineperprodi/delete_course',
			type: "POST",
			data: $("#form_registered_user_list").serialize(),
			dataType: "JSON",
			beforeSend : function(){ 
				showLoading();
			},
			complete : function(){
				hideLoading();
			}, 
			success: function(data)
			{ 
				hideLoading();
				reload();
				dt_all_user_list($(".prodi_code").val());
				dt_registered_user_list($(".prodi_code").val());
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				info_alert('warning','<?php echo getLang("error_xhr")?>');
			}
		});
	} 
}  

function save(){
	var url;
	if(save_method == 'add'){
		url = "<?php echo site_url('index.php/internationalonlineperprodi/insert')?>";
	}else{
		url = "<?php echo site_url('index.php/internationalonlineperprodi/update')?>";
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