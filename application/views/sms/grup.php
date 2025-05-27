
					<div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                <div class="x_title">
									<table width="100%" border="0" cellpadding="2" cellspacing="2">  
										<tr >
											<td width="220px" >Jumlah Grup</td>
											<td width="10px">:</td>
											<td><?php echo $total ?></td> 
											<td align="right"><a href="index.php/sms/addgrup"><button type="button" class="btn btn-primary hijau">Tambah Grup Sms</button></a></td>
										</tr> 
									</table>									
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
									
                                    <table id="example" class="table table-striped responsive-utilities jambo_table">
                                        <thead>
                                            <tr class="headings"> 
												<th width="5%"># </th>
                                                <th width="70%">Nama Grup</th>
                                                <th width="10%">Jumlah Anggota</th> 
                                                <th width="15%">action</th> 
                                            </tr>
                                        </thead>

                                        <tbody>
                                             <?php  
												$style 	= 'even pointer'; 

												$no=1; foreach ($sms as $row)  { ?>
												<tr class="<?php echo $style?>">  
													<td class=""><?php echo  $no ?></td>
													<td class=""><?php echo  ucwords(strtolower($row->sg_name)) ?></td>  
													<td class=""><?php echo  $row->total ?></td>
													<td class="">
													<button type="button" onclick="Edit('<?php echo $row->sg_id ?>')" class="btn btn-xs green" title="Edit Data"><i class="fa fa-pencil-square-o" ></i></button>
													<button type="button" onclick="Delete('<?php echo $row->sg_id ?>')" class="btn btn-xs red" title="Delete Data"><i class="fa fa-trash-o"></i> </button>
													<a href="index.php/sms/grupdetail/<?php echo $row->sg_id ?>"><button type="button" class="btn btn-xs blue" title="Detail"><i class="fa fa-file-o"></i></button></a></td>
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

<div class="modal fade editGrup" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title" id="myModalLabel">Edit Grup</h4>
			</div>
			<div class="modal-body">
				<form id="editform" data-parsley-validate method="post" action="index.php/sms/editGrupDb" class="form-horizontal form-label-left">
				<input type="hidden" id="id" name="id"> 
					<div class="form-group">
						<label class="control-label col-md-2 col-sm-2 col-xs-12" for="first-name">Nama Grup <span class="required">*</span>
						</label>
						<div class="col-md-10 col-sm-10 col-xs-12">
							<input type="text" id="grup"name="grup" required="required" class="form-control col-md-7 col-xs-12" >
						</div>
					</div>  
					<div class="form-group">
						<label  class="control-label col-md-2 col-sm-2 col-xs-12" for="first-name">Nama Anggota<span class="required">*</span>
						</label> 
						<div class="col-md-10 col-sm-10 col-xs-12" id="textareass" >   
								
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
	
	
});

function Delete(id)
{
	if(confirm(' ID = '+id+'\n Apakah anda yakin akan menghapus data tersebut ?')) {
		$.ajax({
			url:'index.php/sms/delGrup',
			global:false,
			type:'post', 
			data: ({id : id}),
			async:false,
			success: function() { window.location='index.php/sms/grup' }
		});
	}
}

function Edit(id)
{ 
	$.ajax({
		url:'index.php/sms/editGrup',
		global:false,
		type:'post',
		data: ({id : id}),
		dataType: "json",
		async:false,
		success: function(result) {   
			$('.editGrup').modal('show');
			$('#editform #id').val(result.grup.sg_id);
			$('#editform #grup').val(result.grup.sg_name); 
			$('#editform #textareass').html('<textarea id="textareas" name="member" rows="1"></textarea>');
			$('#textareas').textext({
				plugins : 'autocomplete tags ajax', 
				tagsItems : result.member,
				ajax : {
					url : 'index.php/sms/memberjson',
					dataType : 'json'
					//cacheResults : true
				}
			}).bind('isTagAllowed', function(e, data){
			var formData = $(e.target).textext()[0].tags()._formData,
			list = eval(formData);

				// duplicate checking
			if (formData.length && list.indexOf(data.tag) >= 0) { 

					   data.result = false;
			}});   
		}
	});
}

</script>