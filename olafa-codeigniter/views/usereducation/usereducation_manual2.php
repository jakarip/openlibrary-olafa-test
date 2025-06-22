<style>
.required {
color : red;
font-weight:bold;
}
</style>

<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong> </h3>
		</div>
		<div class="panel-content pagination2">
			<form id="form" class="form-horizontal"> 
				<div class="form-group">
					<label class="col-sm-3 control-label">Nama Lengkap <span class="required">*</span></label>
					<div class="col-sm-9">
						<input type="text"  class="form-control" name="inp[useredu_name]" id="useredu_name" required placeholder="Nama Lengkap"> 
					</div> 
				</div> 
				<div class="form-group">
					<label class="col-sm-3 control-label">Pilih Prodi <span class="required">*</span></label>
					<div class="col-sm-9">
						<select name="inp[useredu_prodi]" id="useredu_prodi" class="form-control custom-select" required>
							<?php
								echo '<option value="">Pilih Prodi</option>';
								foreach ($prodi as $row){
									
									echo '<option value="'.$row->NAMA_PRODI.'">'.$row->NAMA_PRODI.'</option>';
								}
							?>
						</select>  
					</div> 
				</div> 
				<div class="form-group">
					<label class="col-sm-3 control-label">NIM</label>
					<div class="col-sm-9">
						<input type="text"  class="form-control" name="inp[useredu_nim]" id="useredu_nim" placeholder="NIM"> 
					</div> 
				</div> 
				<div class="form-group">
					<label class="col-sm-3 control-label">No. HP</label>
					<div class="col-sm-9">
						<input type="text"  class="form-control" name="inp[useredu_phone]" id="useredu_phone" placeholder="No. HP"> 
					</div> 
				</div> 
				<div class="form-group"> 
					<div class="col-sm-12"> 
							<button value="register" type="button" id="register" name="register" onclick="registrasi()"  class="btn btn-success">Register</button>
					</div> 
				</div> 
			</form> 
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			<div id="dt_table1">
			<table class="dt_table1 table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th width="5%"><?php echo getLang("no") ?></th>
						<th width="30%"><?php echo getLang("name") ?></th>
						<th width="10%"><?php echo getLang("nim") ?></th>
						<th width="45%"><?php echo getLang("study_program") ?></th> 
						<th width="10%"><?php echo getLang("date") ?></th>
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
var table;



var form = $('#form');  
form.validate({       
	ignore: ""
}); 


$(document).ready(function(){  
	dt_table();
	
	$('.dataTables_filter input').unbind().bind('keyup', function(e) {
       if(e.keyCode == 13) {
		$(".dt_table1").dataTable().fnFilter(this.value);
		}
	}); 
	
	 $("#standard").customselect();
	
   // $('#tahun').on('change', function(e) { 
        // dt_table($(this).val());
		// $('.dataTables_filter input').unbind().bind('keyup', function(e) {
		   // if(e.keyCode == 13) {
			// $(".dt_table1").dataTable().fnFilter(this.value);
			// }
		// });
	// });
});

function dt_table() {
	table = $('.dt_table1').DataTable({ 
        "processing": true,  
        "serverSide": true,  
		"destroy": true,
        "order": [],   
		"pageLength": -1,        
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
        "ajax": {
            "url": 'index.php/usereducation/ajax_index',
            "type": "POST" 
        }, 
        "columnDefs": [
			{ 
				"targets": [ -1 ], 
				"orderable": false,  
			},
        ]
    }); 
}



function registrasi(){ 
	if (form.valid()) {
		$.ajax({
			url : 'index.php/usereducation/add',
			type: "POST",
			data: form.serialize(),
			beforeSend : function() {
				showLoading();
			}, 
			success: function(data)
			{ 
					hideLoading();
					info_alert('success','Registrasi Berhasil');				
					dt_table();
					reset()();
			}
		});	   
	}
}	
 

function reload() {
   table.draw();
}


function reset() {
    form.validate().resetForm();   
	$("#form :input").removeData("previousValue");	//remove remote jquery validate previous value  
    form[0].reset();            
	$("#useredu_prodi").select2("val", ""); 
    $("label.error").hide();
    $(".error").removeClass("error");
} 

</script>