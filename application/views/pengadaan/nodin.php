
					<div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                <div class="x_title">
									<table width="100%" border="0" cellpadding="2" cellspacing="2">  
										<tr >
											<td width="220px" >Jumlah Nota Dinas</td>
											<td width="10px">:</td>
											<td><?php echo $total ?></td> 
											<td align="right"><a href="index.php/pengadaan/addnodins"><button type="button" class="btn btn-primary hijau">Tambah Nota Dinas</button></a></td>
										</tr> 
									</table>									
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
									
                                    <table id="example" class="table table-striped responsive-utilities jambo_table">
                                        <thead>
                                            <tr class="headings"> 
												<th width="5%"># </th>
                                                <th width="50%">No Nodin</th>
                                                <th width="10%">Tanggal</th> 
                                                <th width="20%">Jumlah Judul</th> 
                                                <th width="15%">action</th> 
                                            </tr>
                                        </thead>

                                        <tbody>
                                             <?php  
												$style 	= 'even pointer'; 

												$no=1; foreach ($pengadaan as $row)  { ?>
												<tr class="<?php echo $style?>">  
													<td class=""><?php echo  $no ?></td>
													<td class=""><?php echo  ucwords(strtolower($row->nd_nomor)) ?></td>
													<td class=""><?php echo  $row->nd_tanggal ?></td>  
													<td class=""><?php echo  $row->total ?></td>
													<td class="">
													<button type="button" onclick="Edit('<?php echo $row->nd_id ?>')" class="btn btn-xs green" title="Edit Data"><i class="fa fa-pencil-square-o" ></i></button>
													<button type="button" onclick="Delete('<?php echo $row->nd_id ?>')" class="btn btn-xs red" title="Delete Data"><i class="fa fa-trash-o"></i> </button>
													<a href="index.php/pengadaan/nodindetail/<?php echo $row->nd_id ?>"><button type="button" class="btn btn-xs blue" title="Detail"><i class="fa fa-file-o"></i></button></a></td>
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

<div class="modal fade editNodin" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title" id="myModalLabel">Edit Nota Dinas</h4>
			</div>
			<div class="modal-body">
				<form id="editform" data-parsley-validate method="post" action="index.php/pengadaan/editNodinDb" class="form-horizontal form-label-left">
				<input type="hidden" id="id" name="id">
					<div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-12" for="first-name">Nomor Nota Dinas <span class="required">*</span>
						</label>
						<div class="col-md-10 col-sm-10 col-xs-12">
							<input type="text" id="nomor"name="nomor" required="required" class="form-control col-md-7 col-xs-12">
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
		validateFront();
	});
	$('#editform .simpan').on('click', function () {
		$('#editform').parsley().validate();
		validateFront();
	});
	var validateFront = function () {
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
			data : {id : $( "#id" ).val()},
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
			url:'index.php/pengadaan/delNodin',
			global:false,
			type:'post', 
			data: ({id : id}),
			async:false,
			success: function() { window.location='index.php/pengadaan/nodin' }
		});
	}
}

function Edit(id)
{ 
	$.ajax({
		url:'index.php/pengadaan/editNodin',
		global:false,
		type:'post',
		data: ({id : id}),
		dataType: "json",
		async:false,
		success: function(result) {   
			$('.editNodin').modal('show');
			$('#editform #id').val(result.nd_id);
			$('#editform #nomor').val(result.nd_nomor);
		}
	});
}

</script>