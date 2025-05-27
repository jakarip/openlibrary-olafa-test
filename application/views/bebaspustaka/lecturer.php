 
<div class="row">
	<div class="col-lg-12 portlets">
		<div class="row"> 
			<div class="panel-content pagination2"> 
				<form id="form">
					<div class="modal-body">
						<div class="form-group">
							<div class="col-sm-12"> 
								<b>Search username / nim / nama lengkap mahasiswa :</b>
							</div>  
							<div class="col-sm-12"> 
								<input class="form-control" type="text" name="search" id="search" placeholder="<?php echo getLang('Search'); ?>" required>
							</div>  
						</div>    
						<div class="form-group">
							<div class="col-sm-12"> 
								<b>Search username / nim / nama lengkap dosen :</b>
							</div>  
							<div class="col-sm-12"> 
								<input class="form-control" type="text" name="search_lecturer" id="search_lecturer" placeholder="<?php echo getLang('Search'); ?>" required>
							</div>  
						</div>    
						<div class="form-group">
							<div class="col-sm-12" id="result">  
							</div>
							
						</div>   
					</div>
				</form>
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
	$('#search').tokenInput("index.php/bebaspustaka/auto_data", {
		minChars: 3,
		tokenLimit: 1, 
		hintText:"Search username / nim / nama lengkap mahasiswa",
		theme: "facebook"
	}); 

	$('#search_lecturer').tokenInput("index.php/bebaspustaka/auto_data_lecturer", {
		minChars: 3,
		tokenLimit: 1, 
		hintText:"Search username / nim / nama lengkap dosen",
		theme: "facebook"
	}); 
});

function searching(){ 
	if (form.valid()) {
	} 	   
}


function deletes(id){
	if (confirm('Are you sure you want to delete this '+id+' ?')) {
		$.ajax({
			url : "<?php echo site_url('index.php/bebaspustaka/ajax_delete_document')?>",
			type: "POST",
			data: {
				id:id
			},
			dataType: "JSON",
			beforeSend : function() {
				showLoading();
			},
			complete : function() {
				hideLoading();
			},
			success: function(data){
				if (data.status=="success") $("#"+id).remove();
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				info_alert('warning','<?php echo getLang("error_xhr")?>');
			}
		}); 	   
	}
}	


function details(id){
	window.open("<?php echo site_url('index.php/bebaspustaka/delete_detail')?>"+"/"+id,"_blank");
}	
 
</script>