<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
			
			<div class="row">
				<div class="col-lg-2" > 
					<h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong> </h3>
				</div>
				<div class="text-right col-lg-1" style="margin-top:1px;padding-top:7px;">
					<?php echo getLang('Pilih Jenis Anggota') ?> </div>
				<div class="text-right col-lg-2" style="margin-top:1px;">
					<select name="type" id="type" class="form-control">
							<option value="alls">Semua</option>
							<option value="civitas">Civitas</option>
							<option value="umum">Umum</option>
						</select>  
				</div>
				<div class="text-right col-lg-1" style="margin-top:1px;padding-top:7px;">
					<?php echo getLang('Pilih Fakultas') ?> </div>
				<div class="text-right col-lg-2" style="margin-top:1px;">
					<select name="faculty" id="faculty" class="form-control">
							<option value="alls">Semua</option>
							<?php
								foreach ($faculty as $row){
									echo '<option value="'.$row->C_KODE_FAKULTAS.'">'.$row->NAMA_FAKULTAS.'</option>';
								}
							?>
						</select>  
				</div>
				<div class="text-right col-lg-1" style="margin-top:1px;padding-top:7px;">
					<?php echo getLang('Pilih Tahun Masuk') ?> </div>
				<div class="text-right col-lg-3" style="margin-top:1px;">
					<select name="grow_year" id="grow_year" class="form-control">
							<option value="alls">Semua</option>
							<?php
								$last = date('Y')-7;
								$now = date('Y');
								for($i=$last;$i<=$now;$i++){
									echo '<option value="'.$i.'" >'.$i.'</option>';
								}
							?>
						</select>  
				</div>
			</div>
			<div class="row">
				<div class="col-lg-2" > 
					
				</div>
				<div class="text-right col-lg-2" style="margin-top:1px;padding-top:7px;">
					<?php echo getLang('Login/Akses TelU Openlib Mobile Apps') ?> </div>
				<div class="text-right col-lg-1" style="margin-top:1px;">
					<select name="access" id="access" class="form-control"> 
								<option value="alls">Semua</option>
								<option value="yes">Sudah</option>
								<option value="no">Belum</option>
						</select>  
				</div>
			</div>
		</div>
		<div class="panel-content pagination2"> 
				<!--<div class="form-group">
					<label class="col-sm-12 control-label"><button class="btn btn-sm btn-success btn-embossed" onclick="excel_header()" title="<?php echo getLang('download') ?>"><i class="fa fa-cloud-download"></i>&nbsp; &nbsp;<?php echo getLang('download') ?></button></label>
					 
				</div>-->
			<div id="dt_table1">
			<table class="dt_table1 table table-striped table-bordered table-hover">
				<thead>
					<tr>
						<th width="4%"><?php echo getLang("no") ?></th>
						<th width="15%"><?php echo getLang("faculty") ?></th>
						<th width="15%"><?php echo getLang("study_program") ?></th>
						<th width="15%"><?php echo getLang("ID") ?></th>
						<th width="15%"><?php echo getLang("Nama") ?></th>
						<th width="15%"><?php echo getLang("Jenis Anggota") ?></th>
						<th width="15%"><?php echo getLang("Institusi") ?></th>
						<th width="15%"><?php echo getLang("Phone") ?></th>
						<th width="15%"><?php echo getLang("Email") ?></th>
						<th width="15%"><?php echo getLang("Tanggal Masuk") ?></th>
						<th width="15%"><?php echo getLang("Login/Akses TelU Openlib Mobile Apps") ?></th>
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
$(document).ready(function(){  
	dt_table($("#grow_year").val(),$('#type').val(),$("#faculty").val(),$("#access").val());
	
	$('.dataTables_filter input').unbind().bind('keyup', function(e) {
       if(e.keyCode == 13) {
		$(".dt_table1").dataTable().fnFilter(this.value);
		}
	}); 
	
  $('#type').on('change', function(e) { 
        dt_table($("#grow_year").val(),$(this).val(),$("#faculty").val(),$("#access").val());
		$('.dataTables_filter input').unbind().bind('keyup', function(e) {
		   if(e.keyCode == 13) {
			$(".dt_table1").dataTable().fnFilter(this.value);
			}
		});
	});

  $('#grow_year').on('change', function(e) { 
        dt_table($(this).val(),$("#type").val(),$("#faculty").val(),$("#access").val());
		$('.dataTables_filter input').unbind().bind('keyup', function(e) {
		   if(e.keyCode == 13) {
			$(".dt_table1").dataTable().fnFilter(this.value);
			}
		});
	});

	$('#faculty').on('change', function(e) { 
		dt_table($("#grow_year").val(),$("#type").val(),$(this).val(),$("#access").val());
		$('.dataTables_filter input').unbind().bind('keyup', function(e) {
			if(e.keyCode == 13) {
			$(".dt_table1").dataTable().fnFilter(this.value);
			}
		});
	});

	$('#access').on('change', function(e) { 
		dt_table($("#grow_year").val(),$("#type").val(),$("#faculty").val(),$(this).val());
		$('.dataTables_filter input').unbind().bind('keyup', function(e) {
			if(e.keyCode == 13) {
			$(".dt_table1").dataTable().fnFilter(this.value);
			}
		});
	});
});

function dt_table(grow_year,val,faculty,access) {
	table = $('.dt_table1').DataTable({ 
        "processing": true,  
        "serverSide": true,  
		"destroy": true,
        "order": [],   
		"pageLength": 25,        
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
        "ajax": {
            "url": 'index.php/membership/ajax_index',
            "type": "POST",
			"data" : {
				grow_year : grow_year,
				type : val,
				faculty : faculty,
				access : access
			}
        }, 
    }); 
}

function excel(id,year,grow_year) {
	$.ajax({
        url : 'index.php/membership/excel',
        type: "POST",
		data: {
				year :year,
				grow_year:grow_year,
				id : id
			},
		beforeSend : function() {
			showLoading();
		},
		complete : function() {
			hideLoading();
		},
        success: function(data)
        {
			document.location.href =(data);
        }
    });
}  

function excel_header() {
	$.ajax({
        url : 'index.php/membership/excel_header',
        type: "POST",
		data: {
				year : $("#tahun").val(),
				grow_year : $("#grow_year").val(),
				faculty : $("#faculty").val()
			},
		beforeSend : function() {
			showLoading();
		},
		complete : function() {
			hideLoading();
		},
        success: function(data)
        {
			document.location.href =(data);
        }
    });
}  

function totalcollection(grow_year,year,faculty) {
	$.ajax({
        url : 'index.php/membership/totalcollection',
        type: "POST",
		data: {
				year : year,
				grow_year : grow_year,
				faculty : faculty
			},
        dataType: "JSON",
        success: function(data)
        {
			var judul_total = parseInt(data.judul_fisik)+parseInt(data.judul);
			var eks_total = parseInt(data.eks_fisik)+parseInt(data.eks);
			$('#judul_fisik').html('<b>'+data.judul_fisik+' <?php echo getLang('title')?></b>');
			$('#eks_fisik').html('<b>'+data.eks_fisik+' <?php echo getLang('copy')?></b>');
			$('#judul').html('<b>'+data.judul+' <?php echo getLang('title')?></b>');
			$('#eks').html('<b>'+data.eks+' <?php echo getLang('copy')?></b>');
			$('#judul_total').html('<b>'+judul_total+' <?php echo getLang('title')?></b>');
			$('#eks_total').html('<b>'+eks_total+' <?php echo getLang('copy')?></b>');
			$('#mk').html('<b>'+data.mk+' <?php echo getLang('subject')?></b>');
			$('#mkadabuku').html('<b>'+data.mkadabuku+' <?php echo getLang('subject')?></b>');

        }
    });
}

function reload() {
   table.draw();
}

</script>