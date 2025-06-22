 
					<div class="row"> 
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel">
                                <div class="x_title"> 
									<table width="100%" border="0" cellpadding="2" cellspacing="2">
									<tr>
										<td width="155px" style="padding-top:5px;">Nama Program Studi</td>
										<td width="10px">:</td>
										<td style="color:#555;"><?php echo $jurusan->nama_prodi ?></td> 
									</tr> 
									<tr> 
										<td style="padding-top:5px;">Jumlah</td>
										<td>:</td>
										<td style="color:#555;"><?php echo $total ?></td> 
									</tr>
									<tr> 
										<td style="padding-top:5px;">Edisi</td>
										<td>:</td>
										<td style="color:#555;"><?php echo $edition->nama ?></td> 
									</tr>
									</table> 									
                                    <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
                                    <table id="example" class="table table-striped responsive-utilities jambo_table">
                                        <thead>
											<?php if ($detail=="ta"){ ?>
											
											<tr class="headings"> 
												<th width="5%"># </th>
                                                <th width="10%">NIM </th>
                                                <th width="20%">Nama </th>
                                                <th width="45%">Judul </th>
												<th width="20%">Status </th> 
                                            </tr>
											
											<?php }else { ?> 
											
                                            <tr class="headings"> 
												<th width="5%"># </th>
                                                <th width="10%">NIM </th>
                                                <th width="20%">Nama </th>
                                                <th width="65%">Judul </th>
												
                                            </tr>
											
												<?php } ?> 
                                        </thead>

                                        <tbody>
                                             <?php  
												$style 	= 'even pointer'; 													
												$no=1; foreach ($data as $row)  { ?>
												<tr class="<?php echo $style?>"> 
													<td class=""><?php echo  $no ?></td>
													<td class=""><?php echo  $row->master_data_user ?></td>
													<td class=""><?php echo  ucwords(strtolower($row->master_data_fullname)) ?></td>
													<td class=""><?php echo  ucwords(strtolower($row->title)) ?></td> 
													<?php if ($detail=="ta") { ?> <td class=""><?php echo  ucwords(strtolower($row->state_name)) ?></td> <?php } ?>
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
            });

</script>