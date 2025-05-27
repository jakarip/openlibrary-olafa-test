 
<div class="row">
	<div class="col-lg-12 portlets">
		<div class="row"> 
			<div class="panel-content pagination2"> 
				<form id="form">
					<div class="modal-body">
							<table id="table" class="table table-hover"><tr><th width="85%">Name</th><th width="5%">Extension</th><th width="10%">Aksi</th></tr>
						<?php foreach ($dt as $row) { ?>
							<tr id="<?php echo $row['id']?>"><td><?php echo $row['title']?></td><td><?php echo $row['extension']?></td><td><button type="button" class="btn btn-danger" onclick="deletes('<?php echo $row['id']?>')"><i class="fa fa-trash-o"></i></button></td></tr>
						<?php } ?>
						</table>
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
		preventDuplicates: true,
		onDelete: function (item) { 	
			$("#result").html('');
		},
		hintText:"Search username / nama lengkap mahasiswa",
		onAdd: function (item) {
			
		}
	}); 
});

function searching(){ 
	if (form.valid()) {
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
				var html ='<table id="table" class="table table-hover"><tr><th width="85%">Nama Document</th><th width="5%">Type Document</th><th width="10%">Aksi</th></tr>';
				for(var i = 0; i < data.length; i++) {
					var obj = data[i]; 
					html +='<tr id="'+obj.id+'"><td>'+obj.name+'</td><td>'+obj.extension+'</td><td><button type="button" class="btn btn-danger" onclick="deletes(\''+obj.id+'\')"><i class="fa fa-trash-o"></i>&nbsp;&nbsp;Delete</button></td></tr>';
				}
				html +='</html>';
				$("#result").html(html); 
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				info_alert('warning','<?php echo getLang("error_xhr")?>');
			}
		});
	} 	   
}


function deletes(id){
	if (confirm('Are you sure you want to delete this '+id+' ?')) {
		$.ajax({
			url : "<?php echo site_url('index.php/bebaspustaka/ajax_delete_file')?>",
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
 
</script>