<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3><i class="fa fa-book"></i><strong><?php echo getLang("student"); ?> <?php echo $jurusan->useredu_prodi ?> <?php echo $tahun ?></strong></h3> 
		</div>
		<?php if (ISSET($status)) {  
									
			if($status=="success"){ ?> 
		<br>
	   <div class="alert alert-success alert-dismissible fade in" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
			</button> 
			Terimakasih <strong><?php echo  $jurusan->useredu_name ?></strong> telah hadir di User Education
		</div>
		 <?php }else {	?>
			<br>
			<div class="alert alert-danger alert-dismissible fade in" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
				</button>
					Mahasiswa belum ada di database
			</div>
		 <?php }
		} ?>
		<div class="panel-content pagination2">
			<div id="dt_table1">
				<table id="table" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th width="5%"><?php echo getLang("no") ?></th>
							<th width="50%"><?php echo getLang("student") ?></th> 
							<th width="25%"><?php echo getLang("date") ?></th> 
							<th width="10%"><?php echo getLang("status") ?></th>
							<th width="10%"><?php echo getLang("action") ?></th>
						</tr>
						
						
					<tbody>
							<?php $no = 1; 
							foreach ($data as $row){ ?>
							
							<tr>
							<td><?php echo $no++ ?></td>
							<td><?php echo $row->useredu_name ?></td>  
							<td><?php echo $row->useredu_date ?></td>   
							<?php 
							if ($row->useredu_date!=null){
										echo '<td><div><a class="btn btn-sm btn-success btn-embossed" href="javascript:;"  title="'.getLang('present').'"><i class="fa fa-check-circle"></i></a></div></td><td></td>';
									}
									else { 
										echo '<td></td><td><div><a class="btn btn-sm btn-danger btn-embossed" href="index.php/usereducation/students/'.$row->useredu_id.'/'.$tahun.'/'.$row->useredu_id.'" title="'.getLang('present').'"><i class="fa fa-check"></i>&nbsp;&nbsp;'.getLang('present').'</a></div></td>';
									}
							?>  
							
						</tr>
							<?php } ?> 
					
							
					</tbody>
						<tfoot>
							<tr>
								<td align="center" id="total" colspan="5"><b><?php echo $present->total.' '.getLang("from").' '.$total->total.' '.getLang("present") ?></b></td>
							</tr>
						</tfoot>
					</thead>
					
				</table>
			</div>
		</div>
	</div>
</div> 	   
          
<?php $this->load->view('theme_footer'); ?>
								
<script language="javascript" type="application/javascript"> 
$(document).ready(function () { 
	
	
	table = $('#table').DataTable({ 
        "processing": true,   
        "order": [] ,   
		"pageLength": 25,        
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
        "columnDefs": [
			{ 
				"targets": [ -1 ], 
				"orderable": false,  
			},
        ]
    }); 
	
	$('#dt_table1 .dataTables_filter input').unbind().bind('keyup', function(e) {
       if(e.keyCode == 13) {
		$("#table").dataTable().fnFilter(this.value);
		}
	});  
});

function present(id){ 
	$.ajax({
		dataType : "html",
		type : "POST",
		beforeSend : function() {
			showLoading();
		},
		complete : function() {
			hideLoading();
		},
		async : true,
		data : {
					id:id
				},
		url : "index.php/usereducation/present",
		success : function(response){ 
			var a = response.split(',');
			if (a[0]=='success') {
				info_alert('success','<?php echo getLang("success")?>');
				
				$("#total").html(a[1]);
				table.draw();
			}
			else  info_alert('warning','<?php echo getLang("error")?>');
		},
		error : function(){
			alert('cannot retrieve data from server!!');
		}				
	});	
	
	
}

</script>