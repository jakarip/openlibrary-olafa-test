<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
	</button>
	<h4 class="modal-title" id="myModalLabel">Detail Buku</h4>
</div>
<div class="modal-body">
                                              
                                            
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">  
								<div class="x_title">  			 
										<table width="100%" border="0" cellspacing="2" cellpadding="2">  
											<tr>
												<td width="80px">Mata Kuliah</td>
												<td width="10px"> : </td>
												<td><?php echo ucwords(strtolower($mk['namamk']))?></td>
											</tr>   
										</table>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">

                                    <table class="table table-striped responsive-utilities jambo_table bulk_action">
                                        <thead>
                                            <tr class="headings">    
												<th width="3%" class="column-title">#</th>
												<th width="10%" class="column-title">No Induk</th>
												<th width="10%" class="column-title">No Kelas</th>
												<th width="37%" class="column-title">Judul Buku</th>
												<th width="15%" class="column-title">Pengarang</th>
												<th width="12%" class="column-title">Jumlah Total</th> 
												<th width="12%" class="column-title">Jumlah Tersedia</th> 
											</tr>
										</thead>

										<tbody>
											
										<?php  
										$style 	= 'even pointer'; $no=0;  
										foreach ($bukuref as $row) { ?>
											<tr class="<?php echo $style?>">
												<td class=""><?php echo  ++$no ?></td>
												<td class=""><?php echo  $row->kode_buku ?></td>
												<td class=""><?php echo  $row->klasifikasi ?></td>
												<td class=""><?php echo  ucwords(strtolower($row->title)) ?></td>
												<td class=""><?php echo  $row->author ?></td>
												<td class=""><?php echo  $row->eks ?> Eksemplar</td> 
												<td class=""><?php echo  $row->tersedia ?> Eksemplar</td> 
											</tr> 
										<?php  
											if($style 	= 'even pointer') $style 	= 'odd pointer'; 
											else $style 	= 'even pointer'; 
										} ?> 
										</tbody>
									 
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>

 