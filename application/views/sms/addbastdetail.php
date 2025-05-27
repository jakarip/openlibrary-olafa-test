 <input type="hidden" id="temp">
					<div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel"> 
                                <div class="x_content">
                                    <br />
                                    <form id="demo-form2" data-parsley-validate method="post" action="index.php/pengadaan/addBastDetailDb" class="form-horizontal form-label-left">
										<input type="hidden" id="id" name="id" value="<?php echo $pengajuan->bs_id ?>">
                                        <div class="form-group">
                                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="first-name">Nomor Nota Dinas <span class="required">*</span>
                                            </label>
                                            <div class="col-md-10 col-sm-10 col-xs-12">
                                               <input type="text" disabled="disabled" value="<?php echo $pengajuan->bs_nomor ?>" id="nomor" name="nomor" class="form-control col-md-7 col-xs-12">
                                            </div>
                                        </div>
										<div class="ln_solid"></div>
										 <div class="form-group" id="buttonid">
                                            <button type="button" class="btn btn-primary" id="tambah" value="1">Tambah List</button>
                                        </div>
										<div id="add">
											<div class="form-group">
												<label  class=" col-md-10 col-sm-10 col-xs-12" for="first-name">Nomor Pengajuan-Judul<span class="required">*</span>
												</label>
												<label  class=" col-md-2 col-sm-2 col-xs-12" for="first-name">Eksemplar<span class="required">*</span>
												</label>
												
											</div> 
											<div class="form-group">
												<div class="col-md-10 col-sm-10 col-xs-12">
													<input type="text" readonly="readonly" id="judul" name="judul[0]" required="required" class="form-control col-md-7 col-xs-12 judul" placeholder="Nomor Pengajuan-Judul">
													<input type="hidden" id="ids" name="ids[0]">
												</div>
												<div class="col-md-2 col-sm-2 col-xs-12">
													<input type="text" id="eks" name="eks[0]" required="required" class="form-control col-md-7 col-xs-12" placeholder="Eksemplar">
												</div>
											</div> 
											 
										</div>
                                        
                                        <div class="form-group">
											<div class="col-md-12 col-sm-12 col-xs-12 col-md-offset-3">
                                               &nbsp;
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-12">
                                                <button type="submit" class="btn btn-primary hijau simpan">Simpan</button>
                                                <button type="button" class="btn btn-primary merah" onclick="window.location='index.php/pengadaan/bast'">Cancel</button>
                                            </div>
											 
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
					</div>
					

<div class="modal fade editBast" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title" id="myModalLabel">Edit Nota Dinas</h4>
			</div>
			<div class="modal-body">
				<table id="example" class="table table-striped responsive-utilities jambo_table">
					<thead>
						<tr class="headings"> 
							<th width="5%"># </th>
							<th width="5%">ID</th>
							<th width="15%">Nomor Pengajuan</th>
							<th width="15%">Dosen</th>
							<th width="15%">Mata Kuliah</th>
							<th width="5%">Semester</th>
							<th width="25%">Judul</th> 
							<th width="10%">Pengarang</th>  
							<th width="5%">Tipe</th>   
						</tr>
					</thead>

					<tbody id="dtcontent">
						 <?php  
							$style 	= 'even pointer'; 
							
							$no=1; foreach ($detail as $row)  { ?>
							<tr class="<?php echo $style?>" style="cursor:pointer!important;">  
								<td class=""><?php echo  $no ?></td>
								<td class=""><?php echo  ucwords(strtolower($row->pd_id)) ?></td> 
								<td class=""><?php echo  ucwords(strtolower($row->pj_nomor)) ?></td> 
								<td class=""><?php echo  ucwords(strtolower($row->pj_dosen)) ?></td>
								<td class=""><?php echo  ucwords(strtolower($row->pd_mk)) ?></td> 
								<td class=""><?php echo  ucwords(strtolower($row->pd_semester)) ?></td>
								<td class=""><?php echo  $row->pd_judul ?></td>
								<td class=""><?php echo  $row->pd_pengarang ?></td>  
								<td class=""><?php echo  $row->pd_tipe ?></td>
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
					
<script language="javascript" type="text/javascript">
	$(document).ready(function () {
		$.listen('parsley:field:validate', function () {
			validateFront();
		});
		$('#demo-form2 .simpan').on('click', function () {
			$('#demo-form2').parsley().validate();
			validateFront();
		});
		var validateFront = function () {
			if (true === $('#demo-form2').parsley().isValid()) {
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
				url:'index.php/pengadaan/addbastlist',
				global:false,
				type:'post',
				data : {id : $( "#tambah" ).val()},
				dataType: "html",
				async:false,
				success: function(result) { 
					$( "#buttonid" ).html('<button type="button" class="btn btn-primary " id="tambah" value="1">Tambah List</button><button type="button" class="btn btn-primary merah" id="kurang">Hapus List</button>');
					$('#add').append(result);
					$( "#tambah" ).val(hit);
				}
			});
		}); 
		
		$('body').on('click', '#kurang', function(){
			var hit = parseInt($( "#tambah" ).val(), 10) - 1;
			if(hit=="1"){ 
				$( "#buttonid" ).html('<button type="button" class="btn btn-primary " id="tambah" value="1">Tambah List</button>');
				$("#minbook"+hit).remove();  
			}
			else { 
				$("#minbook"+hit).remove();  
				$( "#tambah" ).val(hit);
			}
		}); 
		
		$('body').on('focus', '.judul', function(){
			$('.editBast').modal('show');
			var id = $(this).attr('name');
			str = id.replace(/[^0-9\.]+/g, "");
			$('#temp').val(str);
			$.ajax({
				url:'index.php/pengadaan/bastdtcontent', 
				type:'post',
				data : $('#demo-form2').serialize(),
				dataType: "html",
				async:false,
				success: function(result) { 
					$('.editBast #example').dataTable().fnClearTable();
					$( "#dtcontent" ).html(result);
				}
			});
			
			var oTable = $('.editBast #example').dataTable({
				"oLanguage": {
					"sSearch": "Search all columns:"
				},
				"bDestroy": true,
				"aoColumnDefs": [
					{
						'bSortable': false,
						'aTargets': [0]
					} //disables sorting for column one
				],
				'iDisplayLength': 25,
				"sPaginationType": "full_numbers",
				"dom": 'T<"clear">lfr',
				"tableTools": {
					"sSwfPath": "tools/js/datatables/tools/swf/copy_csv_xls_pdf.swf"
				}
			});
			$(".editBast tfoot input").keyup(function () {
				/* Filter on the column based on the index of this element's parent <th> */
				oTable.fnFilter(this.value, $("tfoot th").index($(this).parent()));
			});
			$(".editBast tfoot input").each(function (i) {
				asInitVals[i] = this.value;
			});
			$(".editBast tfoot input").focus(function () {
				if (this.className == "search_init") {
					this.className = "";
					this.value = "";
				}
			});
			$(".editBast tfoot input").blur(function (i) {
				if (this.value == "") {
					this.className = "search_init";
					this.value = asInitVals[$("tfoot input").index(this)];
				}
			}); 
			
			$('body').on('click', '.editBast tbody tr', function(){
				var aData = oTable.fnGetData(this); // get datarow
				if (null != aData)  // null if we clicked on title row
				{
					//alert('name="judul['+id+']');
					//alert($('#temp').val());
					$('input[name="judul['+$('#temp').val()+']"]').val(aData[2]+' - '+aData[6]);
					//$('.judul').val(aData[2]+' - '+aData[6]);
					$('input[name="ids['+$('#temp').val()+']"]').val(aData[1]);
					$('.editBast').modal('hide');
				}
			});
			
		}); 
		
		
    });
</script>