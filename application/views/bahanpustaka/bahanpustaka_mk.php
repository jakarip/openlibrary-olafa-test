<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3><i class="fa fa-book"></i><strong><?php echo getLang("subject_detail"); ?> <?php echo $jurusan->nama_prodi ?> <?php echo $tahun ?></strong></h3> 
		</div>
		<div class="panel-content pagination2">
			<div id="dt_table1">
				<table id="table" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th width="5%"><?php echo getLang("no") ?></th>
							<th width="10%"><?php echo getLang("subject_code") ?></th>
							<th width="8%"><?php echo getLang("semester") ?></th>
							<th width="25%"><?php echo getLang("subject") ?></th>
							<th width="10%"><?php echo getLang("sks") ?></th> 
							<th width="10%"><?php echo getLang("jumlah judul referensi buku tercetak") ?></th>
							<th width="10%"><?php echo getLang("jumlah judul referensi e-book") ?></th> 
							<th width="15%"><?php echo getLang("action") ?></th>
						</tr>
						<tfoot>
							<tr>
								<td colspan="5" align="center"><b><?php echo getLang('subject_that_have_book').' : '.$mk->mk.' '.getLang('of').' '.$mk->totalmk.' '.getLang('subject')  ?></b></td>
								<td align="right"><b><?php echo $mk->judul_fisik ?> Judul</b></td>
								<td align="right"><b><?php echo $mk->judul ?> Judul</b></td>
								<td></td>
							</tr>
						</tfoot>
					</thead>
					<tbody>
							
					</tbody>
					
				</table>
			</div>
		</div>
	</div>
</div> 	
				 

<div class="modal fade modalViewBuku" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" to click out and data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			
		</div>
	</div>
</div>
          
<?php $this->load->view('theme_footer'); ?>
								
<script language="javascript" type="application/javascript"> 
$(document).ready(function () { 
	
	
	table = $('#table').DataTable({ 
        "processing": true,  
        "serverSide": true,  
		"destroy": true,
        "order": [ 2, 'asc' ] ,   
		"pageLength": 25,        
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
        "ajax": {
            "url": 'index.php/bahanpustaka/ajax_mk',
            "type": "POST",
			"data" : {
				jurusan : <?php echo $jurusan->c_kode_prodi ?>,
				tahun : <?php echo $tahun ?>
			}
        }, 
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

	$("td .viewBuku").on( "click", function() {
		alert("aa");
		
	});
});

function viewBuku(id,type){
	$('.modalViewBuku .modal-content').html("");
	$('.modalViewBuku').modal('show');
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
					id:id,
					type:type
				},
		url : "index.php/bahanpustaka/viewBuku",
		success : function(response){
			$('.modalViewBuku .modal-content').html(response);
			
		},
		error : function(){
			alert('cannot retrieve data from server!!');
		}				
	});	
}

</script>