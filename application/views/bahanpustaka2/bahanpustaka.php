					
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel"> 
								 <div class="x_title">
									<form action="" method="post">
									<div class="form-group">
										<label class="control-label col-md-1 col-sm-1 col-xs-12" style="padding-top:8px;">Pilih Bulan & Tahun</label>
										<div class="col-md-10 col-sm-10 col-xs-12">
											
											<select name="tahun" id="tahun" class="form-control has-feedback-left">
												<?php
													foreach ($curriculum as $row){
														echo '<option value="'.$row->curriculum_code.'" '.($tahun==$row->curriculum_code?"selected":"").'>'.$row->curriculum_code.'</option>';
													}
												?>
											</select>
											<span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
										</div> 
                                           <span class="col-md-1 col-sm-1 col-xs-12">     <button   type="submit" value="submit" name="submit" class="btn btn-success">Report</button></span> 
									</div> 
									</form>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">

                                    <table class="table table-striped responsive-utilities jambo_table bulk_action">
                                        <thead>
                                            <tr class="headings"> 
												<th class="column-title" width="4%">#</th>
                                                <th class="column-title" width="30%">Fakultas</th>
                                                <th class="column-title" width="30%">Program Studi</th>
                                                <th class="column-title" width="18%">Jumlah Judul Buku</th>
                                                <th class="column-title" width="18%">Jumlah Eksemplar</th>  
                                </tr>
                            </thead>

                            <tbody>
								
							<?php 
							$no		= 1; 
							$style 	= 'even pointer'; 
							$jdl = 0;
							$ex  = 0;
							foreach ($jurusan as $row) {  
							?>
							<tr class="<?php echo $style?>" style="cursor:pointer;"  onclick="window.location='<?php echo base_url()?>index.php/bahanpustaka2/mk/<?php echo  strtolower($row->c_kode_prodi) ?>/<?php echo $tahun ?>'">
								<td class=""><?php echo $no; ?></td>
								<td class=""><?php echo  ucwords(strtolower($row->nama_fakultas)) ?></td>
								<td class=""><?php echo  ucwords(strtolower($row->nama_prodi)) ?></td>
								<td class=""><?php echo  $judul[$no] ?> Judul</td>
								<td class=""><?php echo  $eks[$no] ?> Eksemplar</td>
							</tr>

							<?php 
								$jdl	= $jdl+$judul[$no];
								$ex		= $ex+$eks[$no];
								$no++; 
								if($style 	= 'even pointer') $style 	= 'odd pointer'; 
								else $style 	= 'even pointer'; 
							} ?>
							

                               
                                     </tbody>
									 <tfoot>
											<tr class="<?php echo $style?>" >
												<td class="" colspan="3">TOTAL</td>
												<td class=""><?php echo  $jdl ?> Judul</td>
												<td class=""><?php echo  $ex ?> Eksemplar</td>
											</tr>
									 </tfoot>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>