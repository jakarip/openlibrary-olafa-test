<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
			<div class="row">
				<div class="col-lg-2" > 
					<h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong> </h3>
				</div>
				<div class="text-right col-lg-1" style="margin-top:1px;text-align:right;padding-top:7px;">
					<?php echo getLang('Pilih Fakultas') ?> </div>
				<div class="text-right col-lg-2" style="margin-top:1px;">
					<select name="faculty" id="faculty" class="form-control">
							<?php
								foreach ($faculty as $row){
									echo '<option value="'.$row->C_KODE_FAKULTAS.'">'.$row->NAMA_FAKULTAS.'</option>';
								}
							?>
						</select>  
				</div>
				<div class="text-right col-lg-1" style="margin-top:1px;text-align:right;padding-top:7px;">
					<?php echo getLang('Pilih Tahun') ?> </div>
				<div class="text-right col-lg-2" style="margin-top:1px;">
					<select name="grow_year" id="grow_year" class="form-control">
							<?php
								$last = date('Y')-3;
								$now = date('Y');
								for($i=$last;$i<=$now;$i++){
									echo '<option value="'.$i.'" '.($i==$now?'selected':'').'>'.$i.'</option>';
								}
							?>
						</select>  
				</div>
				<div class="text-right col-lg-1" style="margin-top:1px;text-align:right;padding-top:7px;">
					<?php echo getLang('choose_curriculum_year') ?> 
				</div>
				<div class="text-right col-lg-2" style="margin-top:1px;">
					<select name="tahun" id="tahun" class="form-control">
							<?php
								foreach ($curriculum as $row){
									echo '<option value="'.$row->curriculum_code.'">'.$row->curriculum_code.'</option>';
								}
							?>
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
						<th width="15%"><?php echo getLang("Judul Buku Tercetak") ?></th>
						<th width="14%"><?php echo getLang("Eksemplar Buku Tercetak") ?></th> 
						<th width="15%"><?php echo getLang("Judul E-Book") ?></th>
						<th width="14%"><?php echo getLang("Eksemplar E-book") ?></th>  
						<th width="15%"><?php echo getLang("Semua Judul") ?></th>
						<th width="14%"><?php echo getLang("Semua Eksemplar") ?></th> 
						<th width="14%"><?php echo getLang("subject") ?></th> 
						<th width="14%"><?php echo getLang("subject_that_have_book") ?></th> 
						<th width="14%"><?php echo getLang("Persentase") ?></th> 
						<th width="10%"><?php echo getLang("action") ?></th>
					</tr>
				</thead>
				<tbody>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="3" align="center"><b>TOTAL</b></td>
						<td id="judul_fisik" align="right"></td>
						<td id="eks_fisik" align="right"></td>
						<td id="judul" align="right"></td>
						<td id="eks" align="right"></td>
						<td id="judul_total" align="right"></td>
						<td id="eks_total" align="right"></td>
						<td id="mk" align="right"></td>
						<td id="mkadabuku" align="right"></td>
						<td></td>
						<td></td>
					</tr>
				</tfoot>
			</table>
			</div>
		</div>
	</div>
</div> 					 
          
<?php $this->load->view('theme_footer'); ?>
<script type="text/javascript">
var table;
$(document).ready(function(){ 
	totalcollection($("#grow_year").val(),$('#tahun').val(),$("#faculty").val());
	dt_table($("#grow_year").val(),$('#tahun').val(),$("#faculty").val());
	
	$('.dataTables_filter input').unbind().bind('keyup', function(e) {
       if(e.keyCode == 13) {
		$(".dt_table1").dataTable().fnFilter(this.value);
		}
	}); 
	
  $('#tahun').on('change', function(e) {
	    totalcollection($("#grow_year").val(),$(this).val(),$("#faculty").val());
        dt_table($("#grow_year").val(),$(this).val(),$("#faculty").val());
		$('.dataTables_filter input').unbind().bind('keyup', function(e) {
		   if(e.keyCode == 13) {
			$(".dt_table1").dataTable().fnFilter(this.value);
			}
		});
	});

  $('#grow_year').on('change', function(e) {
	    totalcollection($(this).val(),$("#tahun").val(),$("#faculty").val());
        dt_table($(this).val(),$("#tahun").val(),$("#faculty").val());
		$('.dataTables_filter input').unbind().bind('keyup', function(e) {
		   if(e.keyCode == 13) {
			$(".dt_table1").dataTable().fnFilter(this.value);
			}
		});
	});

	$('#faculty').on('change', function(e) {
		totalcollection($("#grow_year").val(),$("#tahun").val(),$(this).val());
		dt_table($("#grow_year").val(),$("#tahun").val(),$(this).val());
		$('.dataTables_filter input').unbind().bind('keyup', function(e) {
			if(e.keyCode == 13) {
			$(".dt_table1").dataTable().fnFilter(this.value);
			}
		});
	});
});

function dt_table(grow_year,val,faculty) {
	table = $('.dt_table1').DataTable({ 
        "processing": true,  
        "serverSide": true,  
		"destroy": true,
        "order": [],   
		"pageLength": -1,        
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
        "ajax": {
            "url": 'index.php/bahanpustaka/ajax_index',
            "type": "POST",
			"data" : {
				grow_year : grow_year,
				year : val,
				faculty : faculty
			}
        }, 
        "columnDefs": [
			{ 
				"targets": [ -1 ], 
				"orderable": false,  
			},
        ]
    }); 
}

function excel(id,year,grow_year,type) {
	$.ajax({
        url : 'index.php/bahanpustaka/excel',
        type: "POST",
		data: {
				year :year,
				grow_year:grow_year,
				id : id,
				type : type
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
        url : 'index.php/bahanpustaka/excel_header',
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
        url : 'index.php/bahanpustaka/totalcollection',
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