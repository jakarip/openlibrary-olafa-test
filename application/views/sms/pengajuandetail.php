
					<div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                <div class="x_title">
									<table width="100%" border="0" cellpadding="2" cellspacing="2">
										<tr><td rowspan="4" width="80px"><button onclick="window.location.href='index.php/pengadaan/pengajuan'" class="btn btn-danger" type="button">Back</button></td></tr>
										<tr >
											<td width="220px" style="border-left:1px solid;padding-left:5px;">Nomor Pengajuan</td>
											<td width="10px">:</td>
											<td><?php echo $pengajuan->pj_nomor ?></td> 
											<td align="right" rowspan="3"><button type="button" class="btn btn-primary hijau" data-toggle="modal" data-target=".addPengajuanDetail">Tambah List</button></td>
										</tr> 
										<tr >
											<td width="220px" style="border-left:1px solid;padding-left:5px;">Nama Dosen</td>
											<td width="10px">:</td>
											<td><?php echo $pengajuan->pj_dosen ?></td> 
										</tr> 
										<tr >
											<td width="220px" style="border-left:1px solid;padding-left:5px;">Jumlah List</td>
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
                                                <th width="15%">Mata Kuliah</th>
                                                <th width="5%">Semester</th>
                                                <th width="15%">Judul</th> 
                                                <th width="10%">Pengarang</th> 
                                                <th width="10%">Penerbit</th> 
                                                <th width="5%">Tahun</th> 
                                                <th width="5%">Tipe</th> 
                                                <th width="15%">Status</th> 
                                                <th width="15%">action</th> 
                                            </tr>
                                        </thead>

                                        <tbody>
                                             <?php  
												$style 	= 'even pointer'; 

												$no=1; foreach ($detail as $row)  { ?>
												<tr class="<?php echo $style?>">  
													<td class=""><?php echo  $no ?></td>
													<td class=""><?php echo  ucwords(strtolower($row->pd_mk)) ?></td> 
													<td class=""><?php echo  ucwords(strtolower($row->pd_semester)) ?></td>
													<td class=""><?php echo  $row->pd_judul ?></td>
													<td class=""><?php echo  $row->pd_pengarang ?></td>  
													<td class=""><?php echo  $row->pd_penerbit ?></td>
													<td class=""><?php echo  $row->pd_tahun ?></td>  
													<td class=""><?php echo  $row->pd_tipe ?></td>
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
													<td class="">
													<?php if ($row->pd_nd_id==0 and $row->pd_status=='Diajukan Dosen'){ ?>
													<button type="button" onclick="Edit('<?php echo $row->pd_id ?>')" class="btn btn-xs green" title="Edit Data"><i class="fa fa-pencil-square-o" ></i></button> 
													<button type="button" onclick="Delete('<?php echo $row->pd_id ?>')" class="btn btn-xs red" title="Delete Data"><i class="fa fa-trash-o"></i> </button></td>
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


<div class="modal fade addPengajuanDetail" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
	
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title" id="myModalLabel">Tambah List</h4>
			</div>
			<div class="modal-body">
				<form id="addform" data-parsley-validate method="post" action="index.php/pengadaan/addPengajuanDetailDb" class="form-horizontal form-label-left">
				<input type="hidden" id="id" name="id" value="<?php echo $pengajuan->pj_id ?>">
					<div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-12" for="first-name">Mata Kuliah <span class="required">*</span>
						</label>
						<div class="col-md-10 col-sm-10 col-xs-12">
							<input type="text" id="mk"name="mk" required="required" class="form-control col-md-7 col-xs-12">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-12" for="first-name">Semester <span class="required">*</span>
						</label>
						<div class="col-md-10 col-sm-10 col-xs-12">
							<input type="text" id="smt"name="smt" required="required" class="form-control col-md-7 col-xs-12">
						</div>
					</div>
					 <div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-12" for="first-name">Judul <span class="required">*</span>
						</label>
						<div class="col-md-10 col-sm-10 col-xs-12">
							<input type="text" id="judul"name="judul" required="required" class="form-control col-md-7 col-xs-12">
						</div>
					</div>
					 <div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-12" for="first-name">Pengarang  
						</label>
						<div class="col-md-10 col-sm-10 col-xs-12">
							<input type="text" id="pengarang"name="pengarang" class="form-control col-md-7 col-xs-12">
						</div>
					</div>
					 <div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-12" for="first-name">Penerbit
						</label>
						<div class="col-md-10 col-sm-10 col-xs-12">
							<input type="text" id="penerbit"name="penerbit"class="form-control col-md-7 col-xs-12">
						</div>
					</div> 
					<div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-12" for="first-name">Tahun
						</label>
						<div class="col-md-10 col-sm-10 col-xs-12">
							<input type="text" id="tahun"name="tahun" class="form-control col-md-7 col-xs-12">
						</div>
					</div> 
					<div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-12" for="first-name">Tipe <span class="required">*</span>
						</label>
						<div class="col-md-10 col-sm-10 col-xs-12">
							<select required="required" class="form-control col-md-7 col-xs-12" name="tipe" id="tipe">
								<option value="">Tipe</option>
								<option value="Utama">Utama</option>
								<option value="Penunjang">Penunjang</option>
							</select>
						</div>
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
					
					
<div class="modal fade editPengajuanDetail" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title" id="myModalLabel">Edit List</h4>
			</div>
			<div class="modal-body">
				<form id="editform" data-parsley-validate method="post" action="index.php/pengadaan/editPengajuanDetailDb" class="form-horizontal form-label-left">
				<input type="hidden" id="id" name="id">
				<input type="hidden" id="idparent" name="idparent" value="<?php echo $pengajuan->pj_id ?>">
					<div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-12" for="first-name">Mata Kuliah <span class="required">*</span>
						</label>
						<div class="col-md-10 col-sm-10 col-xs-12">
							<input type="text" id="mk"name="mk" required="required" class="form-control col-md-7 col-xs-12">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-12" for="first-name">Semester <span class="required">*</span>
						</label>
						<div class="col-md-10 col-sm-10 col-xs-12">
							<input type="text" id="smt"name="smt" required="required" class="form-control col-md-7 col-xs-12">
						</div>
					</div>
					 <div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-12" for="first-name">Judul <span class="required">*</span>
						</label>
						<div class="col-md-10 col-sm-10 col-xs-12">
							<input type="text" id="judul"name="judul" required="required" class="form-control col-md-7 col-xs-12">
						</div>
					</div>
					 <div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-12" for="first-name">Pengarang  
						</label>
						<div class="col-md-10 col-sm-10 col-xs-12">
							<input type="text" id="pengarang"name="pengarang" class="form-control col-md-7 col-xs-12">
						</div>
					</div>
					 <div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-12" for="first-name">Penerbit
						</label>
						<div class="col-md-10 col-sm-10 col-xs-12">
							<input type="text" id="penerbit"name="penerbit"class="form-control col-md-7 col-xs-12">
						</div>
					</div> 
					<div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-12" for="first-name">Tahun
						</label>
						<div class="col-md-10 col-sm-10 col-xs-12">
							<input type="text" id="tahun"name="tahun" class="form-control col-md-7 col-xs-12">
						</div>
					</div> 
					<div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-12" for="first-name">Tipe <span class="required">*</span>
						</label>
						<div class="col-md-10 col-sm-10 col-xs-12">
							<select required="required" class="form-control col-md-7 col-xs-12" name="tipe" id="tipe"> 
								<option value="Utama">Utama</option>
								<option value="Penunjang">Penunjang</option>
							</select>
						</div>
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
		valeditform();
	});
	$('#editform .simpan').on('click', function () {
		$('#editform').parsley().validate();
		valeditform();
	});
	var valeditform = function () {
		if (true === $('#editform').parsley().isValid()) {
			$('.bs-callout-info').removeClass('hidden');
			$('.bs-callout-warning').addClass('hidden');
		} else {
			$('.bs-callout-info').addClass('hidden');
			$('.bs-callout-warning').removeClass('hidden');
		}
	};
	
	
	$.listen('parsley:field:validate', function () {
		valaddform();
	});
	$('#addform .simpan').on('click', function () {
		$('#addform').parsley().validate();
		valaddform();
	});
	var valaddform = function () {
		if (true === $('#addform').parsley().isValid()) {
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

function Delete(id)
{
	if(confirm(' ID = '+id+'\n Apakah anda yakin akan menghapus data tersebut ?')) {
		$.ajax({
			url:'index.php/pengadaan/delPengajuanDetail',
			global:false,
			type:'post', 
			data: ({id : id}),
			async:false,
			success: function() { window.location='index.php/pengadaan/pengajuandetail/<?php echo $pengajuan->pj_id ?>' }
		});
	}
}

function Edit(id)
{ 
	$.ajax({
		url:'index.php/pengadaan/editPengajuanDetail',
		global:false,
		type:'post',
		data: ({id : id}),
		dataType: "json",
		async:false,
		success: function(result) {   
			$('.editPengajuanDetail').modal('show');
			$('#editform #id').val(result.pd_id);
			$('#editform #mk').val(result.pd_mk);
			$('#editform #smt').val(result.pd_semester);
			$('#editform #judul').val(result.pd_judul);
			$('#editform #pengarang').val(result.pd_pengarang);
			$('#editform #penerbit').val(result.pd_penerbit);
			$('#editform #tahun').val(result.pd_tahun);
			$('#editform #tipe').val(result.pd_tipe);
		}
	});
}

</script>