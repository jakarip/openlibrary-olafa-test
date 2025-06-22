
					<div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                <div class="x_title">
									<table width="100%" border="0" cellpadding="2" cellspacing="2">  
										<tr >
											<td width="220px" >Jumlah Judul</td>
											<td width="10px">:</td>
											<td><?php echo $total ?></td> 
										</tr> 
									</table>							
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
									
                                    <table id="example" class="table table-striped responsive-utilities jambo_table">
                                        <thead>
                                            <tr class="headings"> 
												<th width="5%"># </th>
                                                <th width="15%">No Pengajuan</th>
                                                <th width="15%">No Nodin</th>
                                                <th width="15%">No BAST</th>
                                                <th width="15%">Nama Dosen</th>
                                                <th width="20%">Judul</th>  
                                                <th width="15%">Eks Pengajuan</th>
                                                <th width="15%">Eks Diterima</th>
                                                <th width="15%">Status</th>
												<?php if($this->session->userdata('login')){		
													echo '<th width="10%">Action</th>';
													}
												?>
                                            </tr>
                                        </thead>

                                        <tbody>
                                             <?php  
												$style 	= 'even pointer'; 

												$no=1; foreach ($detail as $row)  { ?>
												<tr class="<?php echo $style?>">  
													<td class=""><?php echo  $no ?></td>
													<td class=""><?php echo  $row->pj_nomor ?></td>
													<td class=""><?php echo  $row->nd_nomor ?></td>
													<td class=""><?php echo  $row->bs_nomor ?></td>
													<td class=""><?php echo  ucwords(strtolower($row->pj_dosen)) ?></td> 
													<td class=""><?php echo  $row->pd_judul ?></td>
													<td class=""><?php echo  $row->pd_eks_awal ?></td>
													<td class=""><?php echo  $row->pd_eks_akhir ?></td> 
													<td class=""><?php  
														if($row->pd_status=='Diajukan Dosen'){
															echo '<button type="button" class="btn btn-dark">'.$row->pd_status.'</button>';
														}
														else if($row->pd_status=='Diterima'){
															echo '<button type="button" class="btn btn-primary hijau">'.$row->pd_status.'</button>';
														}
														else if($row->pd_status=='Diajukan ke Logistik'){
															echo '<button type="button" class="btn btn-primary ">'.$row->pd_status.'</button>';
														}
														else {
															echo '<button type="button" class="btn btn-danger">'.$row->pd_status.'</button>';
														}
													?></td>
													<?php if($this->session->userdata('login')){ ?>
														<td class="">  
														<?php if ($row->pd_nd_id==0){ ?>
															<button type="button" onclick="Edit('<?php echo $row->pd_id?>')" class="btn btn-xs green" title="Edit Status"><i class="fa fa-pencil-square-o" ></i></button> 
														<?php } ?>
														</td>
													<?php } ?>														
												 </tr> 	

												<?php  
													$no++;
													if($style 	= 'even pointer') $style 	= 'odd pointer'; 
													else $style 	= 'even pointer'; 
												} ?>
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>
					</div> 
 
					
<div class="modal fade editPengajuanDetail" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title" id="myModalLabel">Edit Status</h4>
			</div>
			<div class="modal-body">
				<form id="editform" data-parsley-validate method="post" action="index.php/pengadaan/editListDb" class="form-horizontal form-label-left">
				<input type="hidden" id="id" name="id">
					<div class="form-group">
						<table width="100%" border="0" cellpadding="2" cellspacing="2">  
							<tr>
								<td width="13%" rowspan="3">Ditolak Karena</td>
								<td width="2%"  rowspan="3">:</td>
								<td><input value="Sudah Ada" id="optionsRadios2" checked required="required" name="alasan" type="radio"> Sudah Ada</td> 
							</tr> 
							<tr >
								<td><input value="Anggaran" id="optionsRadios2" required="required" name="alasan" type="radio"> Anggaran
								</td> 
							</tr> 
							<tr >
								<td><input value="Belum Prioritas" id="optionsRadios2" required="required"  name="alasan" type="radio"> Belum Prioritas
								</td> 
							</tr> 
						</table>
									
					</div> 
					<div class="ln_solid"></div>
					<div class="form-group">
						<div class="col-md-12 col-sm-12 col-xs-12 col-md-offset-3">
						   &nbsp;
						</div>
						<div class="col-md-4 col-sm-4 col-xs-12">
							<button type="submit" class="btn btn-primary hijau simpan">Simpan</button>
							<button type="button" class="btn btn-primary merah" data-dismiss="modal">Cancel</button>
						</div>
						 
					</div>
				</form>
			</div> 
		</div>
	</div>
</div>
					
<script language="javascript" type="application/javascript">
  var asInitVals = new Array();
$(document).ready(function () {
	var oTable = $('#example').dataTable({
		"oLanguage": {
			"sSearch": "Search all columns:"
		},
		"aoColumnDefs": [
			{
				'bSortable': false,
				'aTargets': [0]
			} //disables sorting for column one
],
		'iDisplayLength': 25,
		"sPaginationType": "full_numbers",
		"dom": 'T<"clear">lfrtip',
		"tableTools": {
			"sSwfPath": "tools/js/datatables/tools/swf/copy_csv_xls_pdf.swf"
		}
	});
	$("tfoot input").keyup(function () {
		/* Filter on the column based on the index of this element's parent <th> */
		oTable.fnFilter(this.value, $("tfoot th").index($(this).parent()));
	});
	$("tfoot input").each(function (i) {
		asInitVals[i] = this.value;
	});
	$("tfoot input").focus(function () {
		if (this.className == "search_init") {
			this.className = "";
			this.value = "";
		}
	});
	$("tfoot input").blur(function (i) {
		if (this.value == "") {
			this.className = "search_init";
			this.value = asInitVals[$("tfoot input").index(this)];
		}
	});
	
	
	$.listen('parsley:field:validate', function () {
		valaddform();
	});
	$('#editform .simpan').on('click', function () {
		$('#editform').parsley().validate();
		valaddform();
	});
	var valaddform = function () {
		if (true === $('#editform').parsley().isValid()) {
			$('.bs-callout-info').removeClass('hidden');
			$('.bs-callout-warning').addClass('hidden');
		} else {
			$('.bs-callout-info').addClass('hidden');
			$('.bs-callout-warning').removeClass('hidden');
		}
	};  
	
	
	
	$('body').on('click', '#tambah', function(){
		var hit = parseInt($( "#tambah" ).val(), 10) + 1;
		$.ajax({
			url:'index.php/pengadaan/addbook',
			global:false,
			type:'post',
			data : {id : $( "#tambah" ).val()},
			dataType: "html",
			async:false,
			success: function(result) { 
				$( "#buttonid" ).html('<button type="button" class="btn btn-primary" id="tambah" value="1">Tambah Buku</button><button type="button" class="btn btn-primary" id="kurang">Kurang Buku</button>');
				$('#add').append(result);
				$( "#tambah" ).val(hit);
			}
		});
	});
	
	
}); 

function Edit(id)
{   
	$.ajax({
		url:'index.php/pengadaan/editList',
		global:false,
		type:'post',
		data : {id : id},
		dataType: 'json',
		async:false,
		success: function(result) { 
			$('.editPengajuanDetail').modal('show');
			var res = result.pd_status.split(" - "); 
			$("input[name=alasan][value='"+res[1]+"']").prop("checked",true);
			$('#editform #id').val(result.pd_id);
		}
	});
	
}

</script>