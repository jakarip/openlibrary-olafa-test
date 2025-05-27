 
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
		preventDuplicates: true,
		onDelete: function (item) { 	
			$.ajax({
				url : "<?php echo site_url('index.php/bebaspustaka/ajax_data')?>",
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
					if (data.status=='empty') $("#result").html(""); 
					else {
						var html ='<table id="table" class="table table-hover"><tr><th width="20%">Nama</th><th width="40%">Judul</th><th width="20%">Status</th><th width="10%">Jumlah File</th><th width="10%">Aksi</th></tr>';
						for(var i = 0; i < data.data.length; i++) {
						var obj = data.data[i]; 
							html +='<tr id="'+obj.id+'"><td>'+obj.master_data_fullname+'</td><td>'+obj.title+'</td><td>'+obj.name+'</td><td>'+obj.jml+'</td>';
						 html +='<td><button type="button" class="btn btn-danger" onclick="deletes(\''+obj.id+'\')"><i class="fa fa-trash-o"></i></button>&nbsp;&nbsp;<button type="button" class="btn btn-success" onclick="details(\''+obj.id+'\')"><i class="fa fa-file-o"></i></button></td>';
							
							html +='</tr>';
						}
						html +='</html>';
						$("#result").html(html); 
					}
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					info_alert('warning','<?php echo getLang("error_xhr")?>');
				}
			}); 
		},
		hintText:"Search username / nim / nama lengkap mahasiswa",
		onAdd: function (item) {
			$.ajax({
				url : "<?php echo site_url('index.php/bebaspustaka/ajax_data')?>",
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
					var html ='<table id="table" class="table table-hover"><tr><th width="20%">Nama</th><th width="40%">Judul</th><th width="20%">Status</th><th width="10%">Jumlah File</th><th width="10%">Aksi</th></tr>';
					for(var i = 0; i < data.data.length; i++) {
						var obj = data.data[i]; 
						html +='<tr id="'+obj.id+'"><td>'+obj.master_data_fullname+'</td><td>'+obj.title+'</td><td>'+obj.name+'</td><td>'+obj.jml+'</td>';
						  html +='<td><button type="button" class="btn btn-danger" onclick="deletes(\''+obj.id+'\')"><i class="fa fa-trash-o"></i></button>&nbsp;&nbsp;<button type="button" class="btn btn-success" onclick="details(\''+obj.id+'\')"><i class="fa fa-file-o"></i></button></td>';
						
						html +='</tr>';
					}
					html +='</html>';
					$("#result").html(html); 
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					info_alert('warning','<?php echo getLang("error_xhr")?>');
				}
			});
		}, theme: "facebook"
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