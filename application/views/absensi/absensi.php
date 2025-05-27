<style>
.table-condensed thead tr:nth-child(2),
.table-condensed tbody {
  display: none
}
</style>					
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel"> 
								 <div class="x_title">
									<form action="" method="post">
									<div class="form-group">
										<label class="control-label col-md-1 col-sm-1 col-xs-12" style="padding-top:8px;">Pilih Bulan & Tahun</label>
										<div class="col-md-10 col-sm-10 col-xs-12">
											 <input type="text" name="month" value="<?php echo (empty($month)?'':$month)?>" class="form-control has-feedback-left" id="single_cal1" placeholder="Pilih Bulan & Tahun" aria-describedby="inputSuccess2Status">
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
                                                <th class="column-title" width="40%">Fakultas</th>
                                                <th class="column-title" width="40%">Program Studi</th>
                                                <th class="column-title" width="16%">Jumlah</th> 												
											</tr>
										</thead>

                            <tbody>
							<?php 
							$no		= 1; 
							$style 	= 'even pointer'; 
							if(ISSET($jurusan)){
								foreach ($jurusan as $row) {   
								?>
								<tr class="<?php echo $style?>"> 
									<td class="" align="center"><?php echo  $no; ?></td>
									<td class=""><?php echo  ucwords(strtolower($row->nama_fakultas)) ?></td>
									<td class=""><?php echo  ucwords(strtolower($row->nama_prodi)) ?></td>
									<td class=""><?php echo  $jumlah[$no] ?></td> 
								</tr>


								<?php 
									$no++; 
									if($style 	= 'even pointer') $style 	= 'odd pointer'; 
									else $style 	= 'even pointer'; 
								}
								?>
								<tr>
									<td colspan="3">TOTAL</td>
									<td><?php echo array_sum($jumlah).' Pengunjung' ?></td>
								</tr>
							<?php } ?>

                               
                                     </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div> 	 
					
					
					 <script type="text/javascript">
        $(document).ready(function () {
            $('#single_cal1').daterangepicker({
                singleDatePicker: true,
                calender_style: "picker_1",
				showDropdowns: true,
				format: 'MM-YYYY'
            }).on('hide.daterangepicker', function (ev, picker) {
				$('.table-condensed tbody tr:nth-child(2) td').click();
				 $('#single_cal1').val(picker.startDate.format('MM-YYYY'));
			});
		});
    </script>