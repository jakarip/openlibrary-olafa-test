
					<div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                <div class="x_title">
									<table width="100%" border="0" cellpadding="2" cellspacing="2">
										<tr><td rowspan="3" width="80px"><button onclick="window.location.href='index.php/pengadaan/bast'" class="btn btn-danger" type="button">Back</button></td></tr>
										<tr >
											<td width="220px" style="border-left:1px solid;padding-left:5px;">Nomor BAST</td>
											<td width="10px">:</td>
											<td><?php echo $pengajuan->bs_nomor ?></td> 
											<td align="right" rowspan="3"><a href="index.php/pengadaan/addbastdetail/<?php echo $pengajuan->bs_id ?>"><button type="button" class="btn btn-primary hijau">Tambah List</button></td>
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
                                                <th width="15%">Nama Dosen</th>
                                                <th width="15%">Mata Kuliah</th>
                                                <th width="5%">Semester</th>
                                                <th width="20%">Judul</th>  
                                                <th width="15%">Eks Diterima</th>
                                                <th width="15%">Status</th> 
                                                <th width="10%">action</th> 
                                            </tr>
                                        </thead>

                                        <tbody>
                                             <?php  
												$style 	= 'even pointer'; 

												$no=1; foreach ($detail as $row)  { ?>
												<tr class="<?php echo $style?>">  
													<td class=""><?php echo  $no ?></td>
													<td class=""><?php echo  ucwords(strtolower($row->pj_dosen)) ?></td> 
													<td class=""><?php echo  ucwords(strtolower($row->pd_mk)) ?></td> 
													<td class=""><?php echo  ucwords(strtolower($row->pd_semester)) ?></td>
													<td class=""><?php echo  $row->pd_judul ?></td>
													<td class=""><?php echo  $row->pd_eks_akhir ?></td>
													<td class=""><?php echo  $row->pd_status ?></td>
													<td class="">
													<button type="button" onclick="Edit('<?php echo $row->pd_id ?>')" class="btn btn-xs green" title="Edit Data"><i class="fa fa-pencil-square-o" ></i></button>
													<button type="button" onclick="Delete('<?php echo $row->pd_id ?>')" class="btn btn-xs red" title="Delete Data"><i class="fa fa-trash-o"></i> </button></td>
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
				<h4 class="modal-title" id="myModalLabel">Edit List</h4>
			</div>
			<div class="modal-body">
				<form id="editform" data-parsley-validate method="post" action="index.php/pengadaan/editBastDetailDb" class="form-horizontal form-label-left">
				<input type="hidden" id="id" name="id">
				<input type="hidden" id="idparent" name="idparent" value="<?php echo $pengajuan->bs_id ?>">
					<div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-12" for="first-name">Eksemplar<span class="required">*</span>
						</label>
						<div class="col-md-10 col-sm-10 col-xs-12">
							<input type="text" id="eks"name="eks" required="required" class="form-control col-md-7 col-xs-12">
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

function Delete(id)
{
	if(confirm(' ID = '+id+'\n Apakah anda yakin akan menghapus data tersebut ?')) {
		$.ajax({
			url:'index.php/pengadaan/delBastDetail',
			global:false,
			type:'post', 
			data: ({id : id}),
			async:false,
			success: function() { window.location='index.php/pengadaan/bastdetail/<?php echo $pengajuan->bs_id ?>' }
		});
	}
}

function Edit(id)
{ 
	$.ajax({
		url:'index.php/pengadaan/editBastDetail',
		global:false,
		type:'post',
		data: ({id : id}),
		dataType: "json",
		async:false,
		success: function(result) {   
			$('.editPengajuanDetail').modal('show');
			$('#editform #id').val(result.pd_id);
			$('#editform #eks').val(result.pd_eks_akhir);
		}
	});
}

</script>