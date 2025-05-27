<div class="row">
    <div class="col-lg-12">
        <div class="panel">
            <div class="panel-header bg-red">
			<h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong> </h3>
            </div>
            <div class="panel-content pagination2"> 
				<div class="row content_button"> 
					<!-- <div class="col-lg-3" > 
						<button type="button" class="btn btn-success" onclick="export_excel()"><i class="fa fa-file"></i>&nbsp;&nbsp;<?php echo getLang("export excel") ?>
					</div>  -->
					<div class="col-lg-1" >Jenis Katalog</div>
					<div class="col-lg-2" > 
					 		<select class="form-control" name="type" id="type">
								<option val="">Semua</option>
							 <?php 
							 	foreach($type as $row){
									echo '<option value="'.$row->id.'">'.$row->name.'</option>';
								}
							 ?>
						</select>
					</div> 
					<div class="col-lg-1" >User</div>
					<div class="col-lg-2" > 
					 		<select class="form-control" name="member" id="member">
								<option val="">Semua</option>
							 <?php 
							 	foreach($admin as $row){
									echo '<option value="'.$row->id.'">'.ucwords(strtolower($row->master_data_user." - ".$row->master_data_fullname)).'</option>';
								}
							 ?>
						</select>
					</div>  
					<div class="col-lg-1" >Lokasi Openlib</div>
					<div class="col-lg-2" > 
					 		<select class="form-control" name="location_openlibrary[]" id="location_openlibrary" multiple="multiple"> 
							 <?php 
							 	foreach($location as $row){
									echo '<option value="'.$row->id.'">'.$row->name.'</option>';
								}
							 ?>
						</select>
					</div> 
					<div class="col-lg-1" >Lokasi SO</div>
					<div class="col-lg-2" > 
					 		<select class="form-control" name="location[]" id="location" multiple="multiple"> 
							 <?php 
							 	foreach($location as $row){
									echo '<option value="'.$row->id.'">'.$row->name.'</option>';
								}
							 ?>
						</select>
					</div> 
				</div>
				<div class="row content_button">  
					
					<div class="col-lg-1" >Status Openlib</div>
					<div class="col-lg-2" > 
					 		<select class="form-control" name="status_openlibrary[]" id="status_openlibrary" multiple="multiple"> 
								<option value="1">Tersedia</option>
								<option value="2">Dipinjam</option>
								<option value="3">Rusak</option>
								<option value="4">Hilang</option>
								<option value="5">Expired</option>
								<option value="6">Hilang Diganti</option>
								<option value="7">Sedang Diproses</option>
								<option value="8">Cadangan</option>
								<option value="9">Weeding</option>
							</select> 
						</select>
					</div> 
					
					<div class="col-lg-1" >Status SO </div>
					<div class="col-lg-2" > 
					 		<select class="form-control" name="status[]" id="status" multiple="multiple"> 
								<option value="1">Tersedia</option>
								<option value="2">Dipinjam</option>
								<option value="3">Rusak</option>
								<option value="4">Hilang</option>
								<option value="5">Expired</option>
								<option value="6">Hilang Diganti</option>
								<option value="7">Sedang Diproses</option>
								<option value="8">Cadangan</option>
								<option value="9">Weeding</option>
							</select> 
						</select> 
					</div>  
					
					<div class="col-lg-1" >Kondisi </div>
					<div class="col-lg-2" > 
					 		<select class="form-control" name="kondisi" id="kondisi">
								<option val="">Pilih Kondisi</option>
								<option value="status">Beda Status Openlib & SO</option>
								<option value="lokasi">Beda Lokasi Openlib & SO</option>
							</select> 
						</select>
					</div>  
				</div>
				
					
				<div class="row content_button">
						<div class="col-lg-2" >
						<a href="javascript:;" onclick="duplicate()" class="btn btn-primary">
								<i class="fa fa-file-o"></i>Barcode Duplicate
							</a>
						</div>
						<div class="col-lg-2" >
						<a href="javascript:;" onclick="not_so()" class="btn btn-primary">
								<i class="fa fa-file-o"></i>Barcode Belum Ada di SO
							</a>
						</div>
						<div class="col-lg-2" >
						<a href="javascript:;" onclick="statistik_not_so()" class="btn btn-info">
								<i class="fa fa-file-o"></i>Statistik Barcode Belum SO
							</a>
						</div>
						<div class="col-lg-2" >
						<a href="javascript:;" onclick="statistik_so()" class="btn btn-info">
								<i class="fa fa-file-o"></i>Statistik Barcode Sudah SO
							</a>
						</div>
				</div> 
				<div class="row content_button">

						<?php $session = $this->session->all_userdata(); 
						if($dt->so_status==1){ 
					 	?>
						<div class="col-lg-2" >
							<a href="javascript:;" onclick="del_all()" class="btn btn-danger">
								<i class="fa fa-trash-o"></i>Hapus Semua Data : <b><?php $session = $this->session->all_userdata();  echo $session['username']; ?></b>
							</a>
						</div> 
					<div class="col-lg-2" >
							<a href="javascript:;" onclick="add()" class="btn btn-success">
								<i class="fa fa-plus-square"></i>Impor Data SO
							</a>
						</div> 
						<div class="col-lg-2" >
							<a href="javascript:;" onclick="add_manual()" class="btn btn-success">
								<i class="fa fa-plus-square"></i>Insert Manual Data SO
							</a>
						</div>  
						<?php  
							}
							 
						?>
				</div> 
                <table id="table-member" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%"><?php echo getLang('no'); ?></th>
                            <th width="9%"><?php echo getLang('date'); ?></th>
                            <th width="9%"><?php echo getLang('member'); ?></th>
                            <th width="9%"><?php echo getLang('jenis_katalog'); ?></th>
                            <th width="20%"><?php echo getLang('judul'); ?></th>
                            <th width="9%"><?php echo getLang('no klasifikasi'); ?></th>
                            <th width="9%"><?php echo getLang('no katalog'); ?></th> 
                            <th width="9%"><?php echo getLang('barcode'); ?></th> 
                            <th width="9%"><?php echo getLang('lokasi openlibrary'); ?></th>
                            <th width="9%"><?php echo getLang('lokasi SO'); ?></th> 
                            <th width="9%"><?php echo getLang('status openlibrary'); ?></th>
                            <th width="9%"><?php echo getLang('status SO'); ?></th> 
                            <th width="9%"><?php echo getLang('label'); ?></th> 
                            <th class="text-center" width="10%"><?php echo getLang('action'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div> 
        </div>
    </div>
</div> 


<div class="modal fade" id="modal_duplicate" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" style="width:80%">
		<div class="modal-content ">
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"><i class="fa fa-trash"></i>&nbsp;&nbsp;List Barcode Duplicate</h4></strong>
			</div>
			<form id="form" class="form-horizontal">
			<input type="hidden" name="id" id="id">
			<div class="modal-body">
				<div class="row content_button"> 
					<div class="col-lg-1" >Jenis Katalog</div>
					<div class="col-lg-2" > 
							<select class="form-control" name="type_duplicate" id="type_duplicate">
								<option val="">Semua</option>
								<?php 
								foreach($type as $row){
									echo '<option value="'.$row->id.'">'.$row->name.'</option>';
								}
								?>
						</select>
					</div>  
					<div class="col-lg-1" >Status Openlib</div>
					<div class="col-lg-2" > 
					 		<select class="form-control" name="status_openlibrary_duplicate[]" id="status_openlibrary_duplicate" multiple="multiple">
								<option val="">Semua</option>
								<option value="1">Tersedia</option>
								<option value="2">Dipinjam</option>
								<option value="3">Rusak</option>
								<option value="4">Hilang</option>
								<option value="5">Expired</option>
								<option value="6">Hilang Diganti</option>
								<option value="7">Sedang Diproses</option>
								<option value="8">Cadangan</option>
								<option value="9">Weeding</option>
							</select> 
						</select>
					</div>  
				</div>  
				<table id="table-duplicate" class="table table-bordered table-hover">
						<thead>
							<tr>
								<th class="text-center" width="5%"><?php echo getLang('no'); ?></th> 
								<th width="9%"><?php echo getLang('total member'); ?></th>
								<th width="9%"><?php echo getLang('member'); ?></th>
								<th width="9%"><?php echo getLang('jenis_katalog'); ?></th>
								<th width="20%"><?php echo getLang('judul'); ?></th>
                            	<th width="9%"><?php echo getLang('no klasifikasi'); ?></th>
								<th width="9%"><?php echo getLang('no katalog'); ?></th> 
								<th width="9%"><?php echo getLang('barcode'); ?></th> 
								<th width="9%"><?php echo getLang('nama file'); ?></th>  
							</tr>
						</thead>
						<tbody>
						</tbody>
                </table>
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-success" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("close") ?></button> 
			</div>
			</form>
		</div>
	</div>
</div>




<div class="modal fade" id="modal_not_so" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" style="width:80%">
		<div class="modal-content ">
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"><i class="fa fa-file-o"></i>&nbsp;&nbsp;List Barcode Belum Ada di Stock Opname</h4></strong>
			</div>
			<form id="form" class="form-horizontal">
			<input type="hidden" name="id" id="id">
			<div class="modal-body">
				<div class="row content_button"> 
					<!-- <div class="col-lg-3" > 
						<button type="button" class="btn btn-success" onclick="export_excel()"><i class="fa fa-file"></i>&nbsp;&nbsp;<?php echo getLang("export excel") ?>
					</div>  -->
					<div class="col-lg-1" >Jenis Katalog</div>
					<div class="col-lg-2" > 
					 		<select class="form-control" name="type_not_so" id="type_not_so">
								<option val="">Semua</option>
							 <?php 
							 	foreach($type as $row){
									echo '<option value="'.$row->id.'">'.$row->name.'</option>';
								}
							 ?>
						</select>
					</div>     
					<div class="col-lg-1" >Status Openlib </div>
					<div class="col-lg-2" > 
					 		<select class="form-control" name="status_openlibrary2[]" id="status_openlibrary2" multiple="multiple"> 
								<option value="1">Tersedia</option>
								<option value="2">Dipinjam</option>
								<option value="3">Rusak</option>
								<option value="4">Hilang</option>
								<option value="5">Expired</option>
								<option value="6">Hilang Diganti</option>
								<option value="7">Sedang Diproses</option>
								<option value="8">Cadangan</option>
								<option value="9">Weeding</option>
							</select> 
						</select>
					</div> 
					
					<div class="col-lg-1" >Lokasi Openlib</div>
					<div class="col-lg-2" > 
					 		<select class="form-control" name="location2[]" id="location2" multiple="multiple"> 
							 <?php 
							 	foreach($location as $row){
									echo '<option value="'.$row->id.'">'.$row->name.'</option>';
								}
							 ?>
						</select>
					</div> 
				</div>
				<div class="row content_button">
					<div class="col-lg-1" >No. Klasifikasi</div>
					<div class="col-lg-1" > 
						<input type="text" name="classification_start" class="form-control" id="classification_start" placeholder="Awal">
					</div> 
					<div class="col-lg-1" >  
						<input type="text" name="classification_end" class="form-control" id="classification_end" placeholder="Akhir">
					</div> 
					<div class="col-lg-2" >
						<a href="javascript:;" onclick="filter3()" class="btn btn-primary">
							<i class="fa fa-file-o"></i>  Filter
						</a>
					</div>
				</div>
				<table id="table-not_so" class="table table-bordered table-hover">
						<thead>
							<tr>
								<th class="text-center" width="5%"><?php echo getLang('no'); ?></th> 
								<th width="9%"><?php echo getLang('lokasi'); ?></th>
								<th width="9%"><?php echo getLang('jenis_katalog'); ?></th>
								<th width="20%"><?php echo getLang('judul'); ?></th>
								<th width="20%"><?php echo getLang('pengarang'); ?></th>
								<th width="9%"><?php echo getLang('no klasifikasi'); ?></th>
								<th width="9%"><?php echo getLang('no katalog'); ?></th> 
								<th width="9%"><?php echo getLang('barcode'); ?></th>
								<th width="9%"><?php echo getLang('Status Openlib'); ?></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
                </table>
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-success" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("close") ?></button> 
			</div>
			</form>
		</div>
	</div>
</div>



<div class="modal fade" id="modal_statistik_not_so" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" style="width:80%">
		<div class="modal-content ">
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"><i class="fa fa-file-o"></i>&nbsp;&nbsp;List Barcode Belum Ada di Stock Opname</h4></strong>
			</div>
			<form id="form" class="form-horizontal">
			<input type="hidden" name="id" id="id">
			<div class="modal-body">
			<div class="row content_button"> 
					<!-- <div class="col-lg-3" > 
						<button type="button" class="btn btn-success" onclick="export_excel()"><i class="fa fa-file"></i>&nbsp;&nbsp;<?php echo getLang("export excel") ?>
					</div>  --> 
					<div class="col-lg-1" >Lokasi Openlib</div>
					<div class="col-lg-2" > 
					 		<select class="form-control" name="location3[]" id="location3" multiple="multiple"> 
							 <?php 
							 	foreach($location as $row){
									echo '<option value="'.$row->id.'">'.$row->name.'</option>';
								}
							 ?>
						</select>
					</div> 
					<div class="col-lg-1" >Tanggal Data</div>
					<div class="col-lg-2" > 
					 		<input class="form-control" name="range_date3" id="range_date3"> 
					</div> 
					<div class="col-lg-2" > 
							<button type="button" class="btn btn-success" name="filter3" id="filter3" ><?php echo getLang("filter") ?></button>  
					</div> 
				</div>
				<table id="table-statistik_not_so" class="table table-bordered table-hover">
						<thead>
							<tr>
								<th rowspan="2" class="text-center" width="5%"><?php echo getLang('no'); ?></th>  
								<th rowspan="2" width="20%"><?php echo getLang('Jenis Katalog'); ?></th>
								<th rowspan="2" width="20%"><?php echo getLang('Total judul'); ?></th>
								<th width="9%" colspan='2'><?php echo getLang('Tersedia'); ?></th>
								<th width="9%" colspan='2'><?php echo getLang('Dipinjam'); ?></th>
								<th width="9%" colspan='2'><?php echo getLang('Rusak'); ?></th>  
								<th width="9%" colspan='2'><?php echo getLang('Hilang'); ?></th> 
								<th width="9%" colspan='2'><?php echo getLang('Expired'); ?></th> 
								<th width="9%" colspan='2'><?php echo getLang('Hilang Diganti'); ?></th>
								<th width="9%" colspan='2'><?php echo getLang('Sedang Diproses'); ?></th>
								<th width="9%" colspan='2'><?php echo getLang('Cadangan'); ?></th>
								<th width="9%" colspan='2'><?php echo getLang('Weeding'); ?></th> 
							</tr>
							<tr>
								<th><?php echo getLang('Judul'); ?></th>
								<th><?php echo getLang('Eksemplar'); ?></th>
								<th><?php echo getLang('Judul'); ?></th>
								<th><?php echo getLang('Eksemplar'); ?></th>
								<th><?php echo getLang('Judul'); ?></th>
								<th><?php echo getLang('Eksemplar'); ?></th>
								<th><?php echo getLang('Judul'); ?></th>
								<th><?php echo getLang('Eksemplar'); ?></th>
								<th><?php echo getLang('Judul'); ?></th>
								<th><?php echo getLang('Eksemplar'); ?></th>
								<th><?php echo getLang('Judul'); ?></th>
								<th><?php echo getLang('Eksemplar'); ?></th>
								<th><?php echo getLang('Judul'); ?></th>
								<th><?php echo getLang('Eksemplar'); ?></th>
								<th><?php echo getLang('Judul'); ?></th>
								<th><?php echo getLang('Eksemplar'); ?></th>
								<th><?php echo getLang('Judul'); ?></th>
								<th><?php echo getLang('Eksemplar'); ?></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
                </table>
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-success" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("close") ?></button> 
			</div>
			</form>
		</div>
	</div>
</div>


<div class="modal fade" id="modal_statistik_so" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" style="width:80%">
		<div class="modal-content ">
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"><i class="fa fa-file-o"></i>&nbsp;&nbsp;List Barcode Belum Ada di Stock Opname</h4></strong>
			</div>
			<form id="form" class="form-horizontal">
			<input type="hidden" name="id" id="id">
			<div class="modal-body">
			<div class="row content_button"> 
					<!-- <div class="col-lg-3" > 
						<button type="button" class="btn btn-success" onclick="export_excel()"><i class="fa fa-file"></i>&nbsp;&nbsp;<?php echo getLang("export excel") ?>
					</div>  --> 
					<div class="col-lg-1" >Lokasi</div>
					<div class="col-lg-2" > 
					 		<select class="form-control" name="location4[]" id="location4" multiple="multiple">
								<option val="">Semua</option>
							 <?php 
							 	foreach($location as $row){
									echo '<option value="'.$row->id.'">'.$row->name.'</option>';
								}
							 ?>
						</select>
					</div> 
					<div class="col-lg-1" >Tanggal Data</div>
					<div class="col-lg-2" > 
					 		<input class="form-control" name="range_date4" id="range_date4"> 
					</div> 
					<div class="col-lg-1" >Tampilkan Jumlah Berdasarkan </div>
					<div class="col-lg-2" > 
					 		<select class="form-control" name="show_status" id="show_status"> 
								<option value="so">Status SO</option>
								<option value="openlib">Status Openlib</option>
							</select> 
						</select>
					</div> 
					<div class="col-lg-2" > 
							<button type="button" class="btn btn-success" name="filter4" id="filter4" ><?php echo getLang("filter") ?></button>  
					</div> 
				</div>
				<table id="table-statistik_so" class="table table-bordered table-hover">
						<thead>
							
						<tr>
								<th rowspan="2" class="text-center" width="5%"><?php echo getLang('no'); ?></th>  
								<th rowspan="2" width="20%"><?php echo getLang('Jenis Katalog'); ?></th>
								<th rowspan="2" width="20%"><?php echo getLang('Total judul'); ?></th>
								<th width="9%" colspan='2'><?php echo getLang('Tersedia'); ?></th>
								<th width="9%" colspan='2'><?php echo getLang('Dipinjam'); ?></th>
								<th width="9%" colspan='2'><?php echo getLang('Rusak'); ?></th>  
								<th width="9%" colspan='2'><?php echo getLang('Hilang'); ?></th> 
								<th width="9%" colspan='2'><?php echo getLang('Expired'); ?></th> 
								<th width="9%" colspan='2'><?php echo getLang('Hilang Diganti'); ?></th>
								<th width="9%" colspan='2'><?php echo getLang('Sedang Diproses'); ?></th>
								<th width="9%" colspan='2'><?php echo getLang('Cadangan'); ?></th>
								<th width="9%" colspan='2'><?php echo getLang('Weeding'); ?></th> 
							</tr>
							<tr>
								<th><?php echo getLang('Judul'); ?></th>
								<th><?php echo getLang('Eksemplar'); ?></th>
								<th><?php echo getLang('Judul'); ?></th>
								<th><?php echo getLang('Eksemplar'); ?></th>
								<th><?php echo getLang('Judul'); ?></th>
								<th><?php echo getLang('Eksemplar'); ?></th>
								<th><?php echo getLang('Judul'); ?></th>
								<th><?php echo getLang('Eksemplar'); ?></th>
								<th><?php echo getLang('Judul'); ?></th>
								<th><?php echo getLang('Eksemplar'); ?></th>
								<th><?php echo getLang('Judul'); ?></th>
								<th><?php echo getLang('Eksemplar'); ?></th>
								<th><?php echo getLang('Judul'); ?></th>
								<th><?php echo getLang('Eksemplar'); ?></th>
								<th><?php echo getLang('Judul'); ?></th>
								<th><?php echo getLang('Eksemplar'); ?></th>
								<th><?php echo getLang('Judul'); ?></th>
								<th><?php echo getLang('Eksemplar'); ?></th>
							</tr>
						</thead>
						<tbody>
						</tbody>
                </table>
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-success" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("close") ?></button> 
			</div>
			</form>
		</div>
	</div>
</div>


<div class="modal fade" id="modal_delete" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"><i class="fa fa-trash"></i>&nbsp;&nbsp;<?php echo getLang("delete").' '. getCurrentMenuName() ?></h4></strong>
			</div>
			<form id="form" class="form-horizontal">
			<input type="hidden" name="id" id="id">
			<div class="modal-body">
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-success" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
			    <button type="button" class="btn btn-danger" onclick="deletes()"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;<?php echo getLang("ok") ?></button>
			</div>
			</form>
		</div>
	</div>
</div>


<div class="modal fade" id="modal_delete_all" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"><i class="fa fa-trash"></i>&nbsp;&nbsp;<?php echo getLang("delete").' '. getCurrentMenuName() ?></h4></strong>
			</div>
			<form id="form" class="form-horizontal">
			<input type="hidden" name="id" id="id">
			<div class="modal-body">
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-success" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
			    <button type="button" class="btn btn-danger" onclick="delete_all()"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;<?php echo getLang("ok") ?></button>
			</div>
			</form>
		</div>
	</div>
</div> 


<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-hidden="true" >
	<div class="modal-dialog modal-lg">
		<div class="modal-content" >
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"></h4></strong>
			</div>
			<form id="form" class="form-horizontal" enctype="multipart/form-data">
			<input type="hidden" name="id" id="id" value="<?=$id?>"> 
			<div class="modal-body"> 
				<div class="form-group">
                    <label class="col-sm-3 control-label">Status SO <span class="required-class">*) </span></label>
                     <div class="col-sm-9 prepend-icon"> 
					 		<select class="form-control" name="inp[sos_status]" id="sos_status" required>
								<option val="">Pilih Status SO</option>
								<option value="1">Tersedia</option>
								<option value="2">Dipinjam</option>
								<option value="3">Rusak</option>
								<option value="4">Hilang</option>
								<option value="5">Expired</option>
								<option value="6">Hilang Diganti</option>
								<option value="7">Sedang Diproses</option>
								<option value="8">Cadangan</option>
								<option value="9">Weeding</option>
							</select> 
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-sm-3 control-label">Lokasi SO<span class="required-class">*) </span></label>
                     <div class="col-sm-9 prepend-icon"> 
					 		<select class="form-control" name="inp[sos_id_location]" id="sos_id_location" required>
								<option val="">Pilih Lokasi SO</option>
							 <?php 
							 	foreach($location as $row){
									echo '<option value="'.$row->id.'">'.$row->name.'</option>';
								}
							 ?>
						</select>
                    </div>
                </div>
				<div class="form-group">
					<label class="col-sm-3 control-label image_required">File .txt  <span class="required-class">*) </span></label>
					<div class="col-sm-9"> 
						<input type="file" name="file" id="file" class="form-control" placeholder="File .txt" required>
					</div> 
				</div> 		 
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
			    <button type="button" class="btn btn-success" onclick="save_image()"><i class="fa fa-save"></i>&nbsp;&nbsp;<?php echo getLang("save") ?></button>
			</div>
			<div class="modal-body">  
				<div class="form-group">
					<label class="col-sm-3 control-label image_required">Result </span></label>
					<div class="col-sm-9 result2"> 
						 
					</div> 
				</div> 		 
			</div>
			</form>
		</div>
	</div>
</div>  
g



<div class="modal fade" id="modal_form_manual" tabindex="-1" role="dialog" aria-hidden="true" >
	<div class="modal-dialog modal-lg">
		<div class="modal-content" >
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"></h4></strong>
			</div>
			<form id="form_manual" class="form-horizontal" enctype="multipart/form-data">
			<input type="hidden" name="id" id="id" value="<?=$id?>"> 
			<div class="modal-body"> 
				<div class="form-group">
                    <label class="col-sm-3 control-label">Status SO <span class="required-class">*) </span></label>
                     <div class="col-sm-9 prepend-icon"> 
					 		<select class="form-control" name="inp[sos_status]" id="sos_status" required>
								<option val="">Pilih Status SO</option>
								<option value="1">Tersedia</option>
								<option value="2">Dipinjam</option>
								<option value="3">Rusak</option>
								<option value="4">Hilang</option>
								<option value="5">Expired</option>
								<option value="6">Hilang Diganti</option>
								<option value="7">Sedang Diproses</option> 
								<option value="8">Cadangan</option>
								<option value="9">Weeding</option>
							</select> 
                    </div>
                </div>
				<div class="form-group">
                    <label class="col-sm-3 control-label">Lokasi SO <span class="required-class">*) </span></label>
                     <div class="col-sm-9 prepend-icon"> 
					 		<select class="form-control" name="inp[sos_id_location]" id="sos_id_location" required>
								<option val="">Pilih Lokasi SO</option>
							 <?php 
							 	foreach($location as $row){
									echo '<option value="'.$row->id.'">'.$row->name.'</option>';
								}
							 ?>
						</select> 
                    </div>
                </div>
				<div class="form-group">
					<label class="col-sm-3 control-label image_required">Label / No Rak <span class="required-class">*) </span></label>
					<div class="col-sm-9"> 
						<input type="text" name="inp[sos_filename]" id="sos_filename" class="form-control" placeholder="Label / No Rak" required>
					</div> 
				</div> 	
				<div class="form-group">
					<label class="col-sm-3 control-label image_required">Barcode  <span class="required-class">*) </span></label>
					<div class="col-sm-9"> 
						<input type="text" name="barcode" id="barcode" class="form-control" placeholder="Barcode" required>
					</div> 
				</div> 		   
				<div class="form-group">
					<label class="col-sm-3 control-label image_required">Result </label>
					<div class="col-sm-9 "> 
						<textarea name="result" id="result" class="form-control" cosl="10" rows="10" ></textarea> 
					</div> 
				</div> 	 
				
			</div>
			<div class="modal-footer"> 
			    <button type="button" class="btn btn-success" onclick="save_manual()" ><i class="fa fa-save"></i>&nbsp;&nbsp;<?php echo getLang("Tutup") ?></button>
			</div> 
			</form>
		</div>
	</div>
</div> 
 
<?php $this->load->view('theme_footer'); ?>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
 

<script type="text/javascript"> 
var save_method; 
var table;
var table2;
var table3;
var table4;
var table5;
var form 				= $('#modal_form #form'); 

$(document).ready(function(){
	table = $('#table-member').DataTable({     
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],  
		"pageLength": 25,
		"processing": true,  
		"serverSide": true,  
		"destroy": true,  
		"order": [],  
		"ajax": {
			"url": "<?php echo site_url('index.php/stockopname_detail/ajax_data')?>",
			"type": "POST",
			"data": function(d){
				d.status = $('#status').val(); 
				d.member = $('#member').val(); 
				d.type = $('#type').val();  
				d.status_openlibrary = $('#status_openlibrary').val();  
				d.location_openlibrary = $('#location_openlibrary').val();  
				d.location = $('#location').val();  
				d.id = '<?php echo $id ?>'; 
				d.kondisi = $('#kondisi').val(); 
			}
		}, 
		"columnDefs": [
			{ 
				"targets": [ 0,-1 ], 
				"orderable": false,  
			},
		], 
        initComplete: function() {
            $('#table-member .dataTables_filter input').unbind();
            $('#table-member .dataTables_filter input').bind('keyup', function(e){
                // console.log("aaa");
                var code = e.keyCode || e.which;
                if (code == 13) { 
					$("#table-member").dataTable().fnFilter(this.value);
                }
            });
        },
	});

	// $('#location_openlibrary,#location').select2({
	// 	maximumSelectionLength: 11
	// });
	 
	
	$( "#status,#member,#type,#status_openlibrary,#location,#location_openlibrary,#kondisi" ).on( "change", function() {
		reload();
	} ); 

	 
	// $( "#location3" ).on( "change", function() {
	// 	reload4();
	// } ); 
	
	// $( "#location4" ).on( "change", function() {
	// 	reload5();
	// } ); 

	$( "#type_duplicate,#status_openlibrary_duplicate" ).on( "change", function() {
		reload2();
	} ); 
	
	$( "#filter3" ).on( "click", function() {
		reload4();
	} ); 
	
	$( "#filter4" ).on( "click", function() {
		reload5();
	} ); 
	

	$('#range_date4,#range_date3').daterangepicker({ 
		locale: {
		format: 'DD-MM-YYYY'
		}
	});

	
	table2 = $('#table-duplicate').DataTable({     
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],  
		"pageLength": 25,
		"processing": true,  
		"serverSide": true,  
		"destroy": true,  
		"order": [],  
		"ajax": {
			"url": "<?php echo site_url('index.php/stockopname_detail/ajax_duplicate')?>",
			"type": "POST",
			"data": function(d){ 
				d.id = '<?php echo $id ?>'; 
				d.type_duplicate = $('#type_duplicate').val();  
				d.status_openlibrary_duplicate = $('#status_openlibrary_duplicate').val();  
			}
		}, 
		"columnDefs": [
			{ 
				"targets": [ 0,-1 ], 
				"orderable": false,  
			},
		],
        initComplete: function() {
            $('#table-duplicate .dataTables_filter input').unbind();
            $('#table-duplicate .dataTables_filter input').bind('keyup', function(e){
                // console.log("aaa");
                var code = e.keyCode || e.which;
                if (code == 13) { 
					$("#table-duplicate").dataTable().fnFilter(this.value);
                }
            });
        },
	});

	
	table3 = $('#table-not_so').DataTable({     
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],  
		"pageLength": 25,
		"processing": true,  
		"serverSide": true,  
		"destroy": true,  
		"order": [],  
		"ajax": {
			"url": "<?php echo site_url('index.php/stockopname_detail/ajax_not_so')?>",
			"type": "POST",
			"data": function(d){ 
				d.id = '<?php echo $id ?>';  
				d.type_not_so = $('#type_not_so').val();  
				d.status_openlibrary2 = $('#status_openlibrary2').val();  
				d.location = $('#location2').val();     
				d.classification_start = $('#classification_start').val();     
				d.classification_end = $('#classification_end').val();   
			}
		}, 
		"columnDefs": [
			{ 
				"targets": [ 0,-1 ], 
				"orderable": false,  
			},
		],
		initComplete: function() {
			$('#table-not_so .dataTables_filter input').unbind();
			$('#table-not_so .dataTables_filter input').bind('keyup', function(e){
				// console.log("aaa");
				var code = e.keyCode || e.which;
				if (code == 13) { 
					$("#table-not_so").dataTable().fnFilter(this.value);
				}
			});
		},
	});  
	
	table4 = $('#table-statistik_not_so').DataTable({     
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],  
		"pageLength": 100,
		"processing": true,  
		"serverSide": true,  
		"destroy": true,  
		"order": [],  
		"ajax": {
			"url": "<?php echo site_url('index.php/stockopname_detail/ajax_statistik')?>",
			"type": "POST",
			"data": function(d){ 
				d.statistik = 'not_so';  
				d.id = '<?php echo $id ?>';   
				d.location = $('#location3').val();  
				d.rangedate = $('#range_date3').val();  
			}
		}, 
		"columnDefs": [
			{ 
				"targets": [ 0,-1 ], 
				"orderable": false,  
			},
		],
		initComplete: function() {
			$('#table-not_so .dataTables_filter input').unbind();
			$('#table-not_so .dataTables_filter input').bind('keyup', function(e){
				// console.log("aaa");
				var code = e.keyCode || e.which;
				if (code == 13) { 
					$("#table-not_so").dataTable().fnFilter(this.value);
				}
			});
		},
	});
	 
	table5 = $('#table-statistik_so').DataTable({     
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],  
		"pageLength": 100,
		"processing": true,  
		"serverSide": true,  
		"destroy": true,  
		"order": [],  
		"ajax": {
			"url": "<?php echo site_url('index.php/stockopname_detail/ajax_statistik')?>",
			"type": "POST",
			"data": function(d){ 
				d.statistik = 'so';  
				d.id = '<?php echo $id ?>';   
				d.location = $('#location4').val();  
				d.rangedate = $('#range_date4').val();  
				d.show_status = $('#show_status').val();
			}
		}, 
		"columnDefs": [
			{ 
				"targets": [ 0,-1 ], 
				"orderable": false,  
			},
		],
		initComplete: function() {
			$('#table-not_so .dataTables_filter input').unbind();
			$('#table-not_so .dataTables_filter input').bind('keyup', function(e){
				// console.log("aaa");
				var code = e.keyCode || e.which;
				if (code == 13) { 
					$("#table-not_so").dataTable().fnFilter(this.value);
				}
			});
		},
	});

	

	$('#barcode').keypress(function (e) {
		var key = e.which;
		if(key == 13)  // the enter key code
		{ 
			if($("#form_manual #sos_status").val()!="Pilih Status SO" && $("#form_manual #sos_id_location").val()!="Pilih Lokasi SO" && $("#form_manual #sos_filename").val()!=""){
				$.ajax({
					url : 'index.php/stockopname_detail/save_manual',
					type: "POST",
					data: $("#modal_form_manual #form_manual").serialize(),
					dataType: "HTML",
					beforeSend : function(){ 
						showLoading();
					},
					complete : function(){
						hideLoading();
					}, 
					success: function(data)
					{
						$('#barcode').val("");  
						$('#modal_form_manual #form_manual #result').append(data);  
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						info_alert('warning','<?php echo getLang("error_xhr")?>');
					}
				});
			}
			else alert("Silahkan Pilih Status SO, Lokasi SO, Label / No Rak NoTerlebih dahulu");
		}
	});   
	 
});  
 

 
function filter3() {
	reload3();
}  

function add() {
	save_method = 'add';
	reset();   
	$("#modal_form .result2").html(""); 
	$('#modal_form').modal({keyboard: false, backdrop: 'static'});
	$('#modal_form .modal-title').html('<i class="fa fa-plus-square"></i>&nbsp;&nbsp;Impor Data SO');  
	  
}   
 

 function add_manual() {
	 save_method = 'add';
	 reset();    
	 $('#result').html('');
	 $('#modal_form_manual').modal({keyboard: false, backdrop: 'static'});
	 $('#modal_form_manual .modal-title').html('<i class="fa fa-plus-square"></i>&nbsp;&nbsp;Insert Manual Data SO');  
	 $('#modal_form_manual').modal({keyboard: false, backdrop: 'static'});
	   
 } 

function save_manual() { 
	reload(); 
	$('#modal_form_manual').modal('hide');
}   



function duplicate() { 
	reset();  
	reload2();
	$('#modal_duplicate').modal({keyboard: false, backdrop: 'static'});
	$('#modal_duplicate .modal-title').html('<i class="fa fa-file-o"></i>&nbsp;&nbsp;Barcode Duplicate');  
	  
}  


function not_so() {  
	reset();  
	reload3();
	$('#modal_not_so').modal({keyboard: false, backdrop: 'static'});
	$('#modal_not_so .modal-title').html('<i class="fa fa-file-o"></i>&nbsp;&nbsp;Barcode Belum Ada di SO');  
	  
}   

function statistik_not_so() { 
	reload4();
	$('#modal_statistik_not_so').modal({keyboard: false, backdrop: 'static'});  
	$('#modal_statistik_not_so .modal-title').html('<i class="fa fa-file-o"></i>&nbsp;&nbsp;Statistik Barcode Belum SO'); 
	$('#modal_statistik_not_so #id').val(id);
}  

function statistik_so() { 
	reload5();
	$('#modal_statistik_so').modal({keyboard: false, backdrop: 'static'});  
	$('#modal_statistik_so .modal-title').html('<i class="fa fa-file-o"></i>&nbsp;&nbsp;Statistik Barcode Sudah SO');  
	$('#modal_statistik_so #id').val(id);
}  

function del(id) { 
	$('#modal_delete').modal({keyboard: false, backdrop: 'static'}); 
	
    $('#modal_delete .modal-body').html('<?php echo getLang("are_you_sure_want_to_delete_data")?> <strong>'+id+'</strong> ?');
	$('#modal_delete #id').val(id);
} 

function deletes() { 
	$.ajax({
		url : 'index.php/stockopname_detail/deletes',
		type: "POST",
		data: $("#modal_delete #form").serialize(),
		dataType: "JSON",
		beforeSend : function(){ 
			showLoading();
		},
		complete : function(){
			hideLoading();
		}, 
		success: function(data)
		{
			$('#modal_delete').modal('hide');
			reload(); 
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			info_alert('warning','<?php echo getLang("error_xhr")?>');
		}
	});
}   


function del_all() { 
	$('#modal_delete_all .modal-title').html('<i class="fa fa-trash-o"></i>&nbsp;&nbsp;&nbsp;&nbsp;Hapus Semua Data dari user : <b><?php $session = $this->session->all_userdata();  echo $session['username']; ?></b>');  
	$('#modal_delete_all').modal({keyboard: false, backdrop: 'static'}); 
    $('#modal_delete_all .modal-body').html('Apakah Anda yakin akan menghapus semua data dari user : <strong><?=$session['username']?></strong> ?'); 
} 

function delete_all() { 
	$.ajax({
		url : 'index.php/stockopname_detail/delete_all',
		type: "POST",
		data: {
			id : '<?=$id?>'
		},  
		dataType: "JSON",
		beforeSend : function(){ 
			showLoading();
		},
		complete : function(){
			hideLoading();
		}, 
		success: function(data)
		{
			$('#modal_delete_all').modal('hide');
			reload(); 
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			info_alert('warning','<?php echo getLang("error_xhr")?>');
		}
	});
} 

function reset() {
    form.validate().resetForm();   
	$("#modal_form #form :input").removeData("previousValue");	//remove remote jquery validate previous value  
    form[0].reset();    
	$("#modal_not_approved #form :input").removeData("previousValue");	//remove remote jquery validate previous value  
    form[0].reset();  	
	$("#modal_form #sos_status").select2("val", ""); 
	$("#modal_form #sos_id_location").select2("val", ""); 
    $("label.error").hide();
    $(".error").removeClass("error");

	
	$("#modal_form_manual #sos_status").select2("val", ""); 
	$("#modal_form_manual #sos_id_location").select2("val", ""); 
	$("#modal_form_manual #sos_filename").val(""); 
}  
  
function edit(id,data) { 
	$('#modal_delete').modal({keyboard: false, backdrop: 'static'}); 
	$('#modal_delete #id').val(id); 
	$('#modal_delete #bds_status').val(data); 	
	$('#modal_delete .modal-body').html('<?php echo getLang("Apakah anda yakin akan mengubah status menjadi ")?> <strong>'+data+'</strong> ?'); 
}   
  
  function pic(img) { 
	  $('#modal_pic').modal({keyboard: false, backdrop: 'static'});  	
	  $('#modal_pic .modal-body').html('<img src="'+img+'" width="100%";>'); 
  }   

function reload() {
   table.draw();
   
}
function reload2() {
   table2.draw();
}
function reload3() {
	table3.draw();
}
function reload4() {
	table4.draw();
}
function reload5() {
	table5.draw();
}
 

function save_image() { 
    var url;
    url = 'index.php/stockopname_detail/save_image';
 
	if($("#modal_form #form #sos_status").val()!=null && $("#modal_form #form #sos_id_location").val()!=null){
		if (form.valid()) {
			var formData = new FormData($('#modal_form #form')[0]);
			$.ajax({
				url : url,
				type: "POST",
				data: formData,
				contentType: false,//untuk upload image 
				processData: false,//untuk upload image
				dataType: "HTML",
				beforeSend : function(){ 
					showLoading();
				},
				complete : function(){
					hideLoading();
				}, 
				success: function(data)
				{ 
					reload(); 
					$('#modal_form .result2').html(data); 
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					info_alert('warning','<?php echo getLang("error_xhr")?>');
				}
			}); 
		}
	}
	else alert("Silahkan Pilih Status SO dan Lokasi SO");
} 

function save_processed() { 
	var form = $("#modal_form_processed #form"); 
	if (form.valid()) {
		$.ajax({
			url : 'index.php/bds/processed',
			type: "POST",
			data: form.serialize(),
			dataType: "JSON",
			beforeSend : function(){ 
				showLoading();
			},
			complete : function(){
				hideLoading();
			}, 
			success: function(data)
			{
				if(data.status!=false){
					$('#modal_form_processed').modal('hide');
					reload(); 
				}
				else alert(data.message);
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				info_alert('warning','<?php echo getLang("error_xhr")?>');
			}
		});
	}
}
</script>