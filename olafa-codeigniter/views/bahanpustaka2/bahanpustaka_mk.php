
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">  
								<div class="x_title"> 
															 
										<table width="100%" border="0" cellpadding="2" cellspacing="2">  
										<tr><td rowspan="3"  width="80px">
										<form action="index.php/bahanpustaka2" method="post"><input type="hidden" name="tahun" value="<?php echo $tahun ?>"><button class="btn btn-danger" type="submit" name="submit" value="submit">Back</button></td></form></tr>
										<tr>
											<td style="border-left:1px solid;padding-left:5px;" width="145px">Nama Program Studi</td>
											<td width="10px">:</td>
											<td><?php echo $jurusan->nama_prodi ?></td> 
										</tr> 
										<tr> 
											<td style="border-left:1px solid;padding-left:5px;">Kurikulum</td>
											<td>:</td>
											<td><?php echo $tahun ?></td> 
										</tr>
										</table>  
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">

                                    <table class="table table-striped responsive-utilities jambo_table bulk_action">
                                        <thead>
                                            <tr class="headings">   
												<th width="5%"  class="column-title">#</th> 
												<th width="5%"  class="column-title">Semester</th>
												<th width="10%" class="column-title">Kode Matakuliah</th>
												<th width="35%" class="column-title">Matakuliah</th>
												<th width="5%"  class="column-title">SKS</th>
												<th width="14%" class="column-title">Jumlah Judul Referensi</th> 
											</tr>
                            </thead>

                            <tbody>
								
							<?php  
							$style 	= 'even pointer'; 

							for($i=1; $i<$no; $i++) { ?>	
							<tr class="<?php echo $style?> viewBuku" style="cursor:pointer" id="<?php echo $idx[$i]?>">
								<td class=""><?php echo  $i ?></td> 
								<td class=""><?php echo  $semester[$i] ?></td>
								<td class=""><?php echo  $kodemk[$i] ?></td>
								<td class=""><?php echo  ucwords(strtolower($namamk[$i])) ?></td>
								<td class="" align="center"><?php echo  $sks[$i] ?></td>
								<td class=""><?php echo  $jmljudulref[$i] ?> Judul</td> 
							 </tr> 	

							<?php  
								if($style 	= 'even pointer') $style 	= 'odd pointer'; 
								else $style 	= 'even pointer'; 
							} ?>

                               
                                     </tbody>
									 <tfoot>
											<tr>   
												<td class="">&nbsp;</td>
												<td class="" align="center" colspan="4">JUMLAH [ <?php echo  ($i==1 ? 0 : $i-1)?> ]</td>
												<td class=""><?php echo  ($i==1 ? 0 : $totaljmlref[$i-1]) ?> Judul</td> 
											</tr>
									 </tfoot>

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
								
<script language="javascript" type="application/javascript"> 
$(document).ready(function () { 
	$(".viewBuku").on( "click", function() {
		$('.modalViewBuku .modal-content').html("");
		$('.modalViewBuku').modal('show');
		var id = $(this).attr('id'); 
		$.ajax({
			dataType : "html",
			type : "POST",
			async : true,
			data : {
						id:id
					},
			url : "index.php/bahanpustaka2/viewBuku",
			success : function(response){
				$('.modalViewBuku .modal-content').html(response);
				
			},
			error : function(){
				alert('cannot retrieve data from server!!');
			}				
		});	
	});
});

</script>