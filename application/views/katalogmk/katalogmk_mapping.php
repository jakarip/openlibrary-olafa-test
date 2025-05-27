 <div class="row">
	<div class="col-lg-12 portlets">
	  <div class="panel">
		<div class="panel-header bg-red">
		  <h3><i class="fa fa-files-o"></i><strong><?php echo getLang("mapping_catalogue_subject_detail"); ?></strong></h3> 
		</div>
		<div class="panel-content pagination2">  
			<div class="filter-left">  
				
				<form id="form" class="form-horizontal">
				<div class="form-group">
					<label class="col-sm-2 control-label"><?php echo getLang("title") ?></label>
					<div class="col-sm-10">
						<?php echo $katalog->title ?>
					</div> 
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label"><?php echo getLang("choose_curriculum_year") ?></label>
					<div class="col-sm-10">
						<select name="curriculum_year" id="curriculum_year" class="form-control"> 
						<?php
							foreach ($curriculum_year as $row){
								echo '<option value="'.$row->curriculum_code.'">'.$row->curriculum_code.'</option>';
							}
						?>
						</select> 
					</div> 
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label"><?php echo getLang("choose_study_program") ?></label>
					<div class="col-sm-10">
						<select name="study_program" id="study_program" class="form-control">  
						<option value="all"><?php echo getLang("all") ?></option>
						<?php 
							foreach ($study_program as $row){
								echo '<option value="'.$row->C_KODE_PRODI.'">'.$row->NAMA_FAKULTAS.' - '.$row->NAMA_PRODI.'</option>';
							}
						?>
						</select> 
					</div> 
				</div>
				</form>
			</div>
		</div>
	  </div>
	</div>
</div>
 
 <div class="row">
	
	<div class="col-lg-5 portlets">
	  <div class="panel">
		<div class="panel-header bg-red">
			<h3> <strong><?php echo getLang("course_in_catalog"); ?></strong> </h3>
		</div>
		<div class="panel-content pagination2">
		    <div class="filter-left">
				<form id="form_registered_user_list">
					<input type="hidden" name="id" value="<?php echo $katalog->id ?>">  
					<table id="registered_user_list" class="table table-striped table-bordered">
						  <thead>
							<tr>
								<th width="10%"><input type="checkbox" name="all" class="form-control all"></th>
								<th width="20%"><?php echo getLang("code") ?></th>
								<th width="65%"><?php echo getLang("course") ?></th>
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
					<button type="button" class="btn btn-danger" id="left" onclick="left()" title="<?php echo getLang("insert_course") ?>"><i class="fa fa-arrow-left"></i></a></button><br>
					<button type="button" class="btn btn-danger" id="right"  onclick="right()" title="<?php echo getLang("delete_course") ?>"><i class="fa fa-arrow-right"></i></a></button>
				</div>
	</div>
	<div class="col-lg-5 portlets">
	  <div class="panel">
		<div class="panel-header bg-red">
			<h3> <strong><?php echo getLang("course_list"); ?></strong> </h3>
		</div>
		<div class="panel-content pagination2">
		    <div class="filter-left"> 
				<form id="form_all_user_list">
					<input type="hidden" name="id" value="<?php echo $katalog->id ?>"> 
					<table id="all_user_list" class="table table-striped table-bordered">
						  <thead>
							<tr>
								<th width="10%"><input type="checkbox" name="all" class="form-control all"></th>
								<th width="20%"><?php echo getLang("code") ?></th>
								<th width="65%"><?php echo getLang("course") ?></th>
							</tr>
						  </thead>
					</table>
				</form>
		    </div>
		</div>
	  </div>
	</div>
</div>
 

<?php $this->load->view('theme_footer'); ?>

<script type="text/javascript">

var save_method; 
var all_user_list;
var registered_user_list;
var form = $('#modal_form #form');
form.validate();

$(document).ready(function() { 
	
    all_user_list 			= dt_all_user_list($("#curriculum_year").val(),$("#study_program").val());
	registered_user_list 	= dt_registered_user_list($("#curriculum_year").val(),$("#study_program").val()); 
	
   $('.all').click(function(){
      $('tbody input[type="checkbox"]', $(this).parents('table')).prop('checked', this.checked);
   });
   
   $('#curriculum_year').change(function(){ 
		
		all_user_list 			= dt_all_user_list($(this).val(),$("#study_program").val());
		registered_user_list 	= dt_registered_user_list($(this).val(),$("#study_program").val()); 
   });
   
   $('#study_program').change(function(){  
		all_user_list 			= dt_all_user_list($("#curriculum_year").val(),$(this).val());
		registered_user_list 	= dt_registered_user_list($("#curriculum_year").val(),$(this).val()); 
   });
   
}); 


function dt_all_user_list(curriculum_year,study_program) {  
	return $('#all_user_list').DataTable({         
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],  
		"pageLength": 10,
        "processing": true,  
        "serverSide": true,   
		"destroy": true,  
        "order": [],   
        "ajax": {
            "url"	: 'index.php/katalogmk/ajax_not_list',
            "type"	: 'POST',
			"data"	: 	{
							id 				: '<?php echo $katalog->id ?>',
							curriculum_year : curriculum_year,
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

function dt_registered_user_list(curriculum_year,study_program) {    
	return $('#registered_user_list').DataTable({         
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],  
		"pageLength": 10,
        "processing": true,  
        "serverSide": true,  
		"destroy": true,  
        "order": [],   
        "ajax": {
            "url"	: 'index.php/katalogmk/ajax_list',
            "type"	: 'POST',
			"data"	: 	{
							id 				: '<?php echo $katalog->id ?>',
							curriculum_year : curriculum_year,
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
	var total=$('#registered_user_list tbody input[name="inp[id][]"]:checked').length;
	
	if (total==0) info_alert('warning','<?php echo getLang("min_selected_one_course")?>');
	else {
		$.ajax({
			url : 'index.php/katalogmk/delete_course',
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
				reload();
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				info_alert('warning','<?php echo getLang("error_xhr")?>');
			}
		});
	} 
} 

function left() { 
	var total=$('#all_user_list tbody input[name="inp[id][]"]:checked').length;
	if (total==0) info_alert('warning','<?php echo getLang("min_selected_one_course")?>');
	else {
		$.ajax({
			url : 'index.php/katalogmk/insert_course',
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
				reload();
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				info_alert('warning','<?php echo getLang("error_xhr")?>');
			}
		});
	} 
}  

function back() {
   window.location.href='index.php/katalogmk';
}   

function reload() {
   all_user_list.draw();
   registered_user_list.draw();
} 

</script>
</body>
</html>