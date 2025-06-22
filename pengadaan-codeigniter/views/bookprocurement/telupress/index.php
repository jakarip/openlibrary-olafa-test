<style>


.label-550080 {
    background-color: #550080;
    color: #FFF;
    border-left-width: 2px;
    padding: 5px 10px;
}
.label-550080[href]:hover,
.label-550080[href]:focus {
  background-color: #550080;
}
  
.label-ffa18e {
    background-color: #ffa18e;
    color: #333333;
    border-left-width: 2px;
    padding: 5px 10px;
}
.label-ffa18e[href]:hover,
.label-ffa18e[href]:focus {
  background-color: #ffa18e;
}
  


.label-ff0090 {
    background-color: #ff0090;
    color: #333333;
    border-left-width: 2px;
    padding: 5px 10px;
}
.label-ff0090[href]:hover,
.label-ff0090[href]:focus {
  background-color: #ff0090; 
}


</style>
<div class="panel panel-default flat">
	<div class="row" style="margin:20px 15px;">	
		<div class="col-md-4"> 
			<div class="form-group"> 
				<?= form_dropdown('prodi', $prodi, '', 'class="form-control select2" id="prodi" required="required"') ?>
			</div>
		</div> 
		<div class="col-md-4"> 
			<div class="form-group"> 
				<?= form_dropdown('status', $status, '', 'class="form-control select2" id="status" required="required"') ?>
			</div>
		</div> 
		<div class="col-sm-1"> 
				<button type="button" class="btn btn-primary btn-labeled btn-xs" id="filter" >
					<b><i class="icon-search4"></i></b> Filter
				</button>
		</div>
	</div>  
	  
    <table class="table table-bordered table-striped table-hover table-xs" id="table">
        <thead>
            <tr>   
                <th class="nosort" rowspan="2">Action</th> 
                <th rowspan="2">Status</th>
                <th rowspan="2">Nama User</th>
                <th rowspan="2">Fakultas</th>
                <th rowspan="2">Prodi</th> 
				<th rowspan="2">Judul Buku</th> 
                <th colspan="2">Pengajuan Naskah (Kelengkapan Administratif)<br><?=$step[1] ?> Hari Kerja</th> 
                <th colspan="2">Review Naskah<br><?=$step[2] ?> Hari Kerja</th> 
                <th colspan="2">Editing & Proofread<br><?=$step[3] ?> Hari Kerja</th> 
                <th colspan="2">Layout<br><?=$step[4] ?> Hari Kerja</th> 
                <th colspan="2">ISBN<br><?=$step[5] ?> Hari Kerja</th> 
                <th colspan="2">Cetak<br><?=$step[6] ?> Hari Kerja</th> 
                <th rowspan="2">Keterangan</th> 
                <th rowspan="2">Penerimaan Naskah</th> 
				<th rowspan="2">Total Biaya Produksi</th>
				<th rowspan="2">Jumlah Hari Kerja Penerimaan Naskah - Cetak</th>
            </tr>
            <tr>
				<th>Target</th>
				<th>Realisasi</th>
				<th>Target</th>
				<th>Realisasi</th>
				<th>Target</th>
				<th>Realisasi</th>
				<th>Target</th>
				<th>Realisasi</th>
				<th>Target</th>
				<th>Realisasi</th>
				<th>Target</th>
				<th>Realisasi</th>
			</tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- <form id="frm" method="post" action="<?= y_url_admin() ?>/login/login_as" target="_blank">
<input type="hidden" id="frm-id" name="id">
<input type="hidden" id="frm-pass" name="pass">
</form> -->





<div class="modal fade" id="frmbox" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger-700" style="background-image:url(assets/images/backgrounds/bg.png)">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            	<h4 class="modal-title"><i class="fa fa-navicon"></i> &nbsp;<?= 'Form Data Pengajuan Buku' ?></h4>
            </div>
            <form id="frm" class="form-horizontal">
                <input type="hidden" name="id" id="id">
                <input type="hidden" name="sub_id" id="sub_id">
                <div class="modal-body">
                    <div class="box-body" style="padding-bottom:0px">  
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Prodi <span style="color:red">*)</span></label>
                            <div class="col-sm-3">
															<?= form_dropdown('inp[book_id_prodi]', $prodi_input, '', 'class="form-control select2" id="book_id_prodi" required="required"') ?>
                            </div>
							<label for="pus_name" class="col-sm-2 control-label">Pemohon <span style="color:red">*)</span></label>
                            <div class="col-sm-4">   
								<select name="inp[book_id_user]" id="book_id_user" class="form-control select2"><option value="">Pilih User</option></select>
                            </div>
                        </div>  
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Judul Buku <span style="color:red">*)</span></label>
                            <div class="col-sm-9"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_title]" id="book_title" required>
                            </div>
                        </div>  
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Realisasi Pengajuan Naskah</label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_startdate_realization_step_1]" id="book_startdate_realization_step_1" placeholder="Tanggal Awal" required>
                            </div>
                            <label for="pus_name" class="col-sm-1 control-label"></label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_enddate_realization_step_1]" id="book_enddate_realization_step_1" placeholder="Tanggal Akhir">
                            </div>
                        </div>   
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Realisasi  Review Naskah</label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_startdate_realization_step_2]" id="book_startdate_realization_step_2" placeholder="Tanggal Awal">
                            </div>
                            <label for="pus_name" class="col-sm-1 control-label"></label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_enddate_realization_step_2]" id="book_enddate_realization_step_2" placeholder="Tanggal Akhir">
                            </div>
                        </div>   
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Realisasi Editing & Proofread</label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_startdate_realization_step_3]" id="book_startdate_realization_step_3" placeholder="Tanggal Awal">
                            </div>
                            <label for="pus_name" class="col-sm-1 control-label"></label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_enddate_realization_step_3]" id="book_enddate_realization_step_3" placeholder="Tanggal Akhir">
                            </div>
                        </div>     
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Awal Realisasi Layout</label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_startdate_realization_step_4]" id="book_startdate_realization_step_4" placeholder="Tanggal Awal">
                            </div>
                            <label for="pus_name" class="col-sm-1 control-label"></label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_enddate_realization_step_4]" id="book_enddate_realization_step_4" placeholder="Tanggal Akhir">
                            </div>
                        </div>    
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Awal Realisasi ISBN</label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_startdate_realization_step_5]" id="book_startdate_realization_step_5" placeholder="Tanggal Awal">
                            </div>
                            <label for="pus_name" class="col-sm-1 control-label"></label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_enddate_realization_step_5]" id="book_enddate_realization_step_5" placeholder="Tanggal Akhir">
                            </div>
                        </div>    
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Awal Realisasi Cetak</label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_startdate_realization_step_6]" id="book_startdate_realization_step_6" placeholder="Tanggal Awal">
                            </div>
                            <label for="pus_name" class="col-sm-1 control-label"></label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_enddate_realization_step_6]" id="book_enddate_realization_step_6" placeholder="Tanggal Akhir">
                            </div>
                        </div>    
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Diterima</label>
                            <div class="col-sm-3"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_received_date]" id="book_received_date">
                            </div>
                            <label for="pus_name" class="col-sm-3 control-label">Total Biaya Produksi</label>
                            <div class="col-sm-3"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_cost]" id="book_cost" >
                            </div>
                        </div> 
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Keterangan</label>
                            <div class="col-sm-9"> 
                            	<input type="text" class="form-control input-sm" name="inp[	]" id="book_desc" >
                            </div>
                        </div>   
                    </div><!-- /.box-body -->
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-labeled btn-xs pull-left" data-dismiss="modal">
                	<b><i class="icon-switch"></i></b> Batal
                </button>                
                <button type="button" class="btn btn-success btn-labeled btn-xs" id="act-save" onclick="save('insert')">
                	<b><i class="icon-floppy-disk"></i></b> Simpan
                </button> 
                </div>                  
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<div class="modal fade" id="frmbox_edit" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger-700" style="background-image:url(assets/images/backgrounds/bg.png)">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            	<h4 class="modal-title"><i class="fa fa-navicon"></i> &nbsp;<?= 'Form Data Pengajuan Buku' ?></h4>
            </div>
            <form id="frm_edit" class="form-horizontal">
                <input type="hidden" name="id" id="id">
                <input type="hidden" name="sub_id" id="sub_id">
                <div class="modal-body">
                    <div class="box-body" style="padding-bottom:0px">  
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Prodi <span style="color:red">*)</span></label>
                            <div class="col-sm-3">
															<?= form_dropdown('inp[book_id_prodi]', $prodi_input, '', 'class="form-control select2" id="book_id_prodi" required="required"') ?>
                            </div>
							<label for="pus_name" class="col-sm-2 control-label">Pemohon <span style="color:red">*)</span></label>
                            <div class="col-sm-4">   
								<select name="inp[book_id_user]" id="book_id_user" class="form-control select2"><option value="">Pilih User</option></select>
                            </div>
                        </div>  
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Judul Buku <span style="color:red">*)</span></label>
                            <div class="col-sm-9"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_title]" id="book_title" required>
                            </div>
                        </div>  
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Realisasi Pengajuan Naskah</label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_startdate_realization_step_1]" id="book_startdate_realization_step_1" placeholder="Tanggal Awal" required>
                            </div>
                            <label for="pus_name" class="col-sm-1 control-label"></label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_enddate_realization_step_1]" id="book_enddate_realization_step_1" placeholder="Tanggal Akhir">
                            </div>
                        </div>   
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Realisasi  Review Naskah</label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_startdate_realization_step_2]" id="book_startdate_realization_step_2" placeholder="Tanggal Awal">
                            </div>
                            <label for="pus_name" class="col-sm-1 control-label"></label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_enddate_realization_step_2]" id="book_enddate_realization_step_2" placeholder="Tanggal Akhir">
                            </div>
                        </div>  
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Realisasi Editing & Proofread</label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_startdate_realization_step_3]" id="book_startdate_realization_step_3" placeholder="Tanggal Awal">
                            </div>
                            <label for="pus_name" class="col-sm-1 control-label"></label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_enddate_realization_step_3]" id="book_enddate_realization_step_3" placeholder="Tanggal Akhir">
                            </div>
                        </div>    
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Awal Realisasi Layout</label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_startdate_realization_step_4]" id="book_startdate_realization_step_4" placeholder="Tanggal Awal">
                            </div>
                            <label for="pus_name" class="col-sm-1 control-label"></label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_enddate_realization_step_4]" id="book_enddate_realization_step_4" placeholder="Tanggal Akhir">
                            </div>
                        </div>    
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Awal Realisasi ISBN</label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_startdate_realization_step_5]" id="book_startdate_realization_step_5" placeholder="Tanggal Awal">
                            </div>
                            <label for="pus_name" class="col-sm-1 control-label"></label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_enddate_realization_step_5]" id="book_enddate_realization_step_5" placeholder="Tanggal Akhir">
                            </div>
                        </div>    
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Awal Realisasi Cetak</label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_startdate_realization_step_6]" id="book_startdate_realization_step_6" placeholder="Tanggal Awal">
                            </div>
                            <label for="pus_name" class="col-sm-1 control-label"></label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_enddate_realization_step_6]" id="book_enddate_realization_step_6" placeholder="Tanggal Akhir">
                            </div>
                        </div>    
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Target Pengajuan Naskah<br>(<?=$step[1] ?> Hari Kerja)</label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_startdate_target_step_1]" id="book_startdate_target_step_1" placeholder="Tanggal Awal" >
                            </div>
                            <label for="pus_name" class="col-sm-1 control-label"></label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_enddate_target_step_1]" id="book_enddate_target_step_1" placeholder="Tanggal Akhir" >
                            </div>
                        </div>   
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Target  Review Naskah><br>(<?=$step[2] ?> Hari Kerja)</label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_startdate_target_step_2]" id="book_startdate_target_step_2" placeholder="Tanggal Awal" >
                            </div>
                            <label for="pus_name" class="col-sm-1 control-label"></label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_enddate_target_step_2]" id="book_enddate_target_step_2" placeholder="Tanggal Akhir" >
                            </div>
                        </div>  
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Target Editing & Proofread<br>(<?=$step[3] ?> Hari Kerja)</label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_startdate_target_step_3]" id="book_startdate_target_step_3" placeholder="Tanggal Awal" >
                            </div>
                            <label for="pus_name" class="col-sm-1 control-label"></label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_enddate_target_step_3]" id="book_enddate_target_step_3" placeholder="Tanggal Akhir" >
                            </div>
                        </div>    
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Awal Target Layout <br>(<?=$step[4] ?> Hari Kerja)</label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_startdate_target_step_4]" id="book_startdate_target_step_4" placeholder="Tanggal Awal" >
                            </div>
                            <label for="pus_name" class="col-sm-1 control-label"></label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_enddate_target_step_4]" id="book_enddate_target_step_4" placeholder="Tanggal Akhir" >
                            </div>
                        </div>    
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Awal Target ISBN<br>(<?=$step[5] ?> Hari Kerja)</label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_startdate_target_step_5]" id="book_startdate_target_step_5" placeholder="Tanggal Awal" >
                            </div>
                            <label for="pus_name" class="col-sm-1 control-label"></label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_enddate_target_step_5]" id="book_enddate_target_step_5" placeholder="Tanggal Akhir" >
                            </div>
                        </div>    
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Awal Target Cetak<br>(<?=$step[6] ?> Hari Kerja)</label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_startdate_target_step_6]" id="book_startdate_target_step_6" placeholder="Tanggal Awal" >
                            </div>
                            <label for="pus_name" class="col-sm-1 control-label"></label>
                            <div class="col-sm-4"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_enddate_target_step_6]" id="book_enddate_target_step_6" placeholder="Tanggal Akhir" >
                            </div>
                        </div>    
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Diterima</label>
                            <div class="col-sm-3"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_received_date]" id="book_received_date">
                            </div>
                            <label for="pus_name" class="col-sm-3 control-label">Total Biaya Produksi</label>
                            <div class="col-sm-3"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_cost]" id="book_cost" >
                            </div>
                        </div> 
						<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Keterangan</label>
                            <div class="col-sm-9"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_desc]" id="book_desc" >
                            </div>
                        </div>   
                    </div><!-- /.box-body -->
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-labeled btn-xs pull-left" data-dismiss="modal">
                	<b><i class="icon-switch"></i></b> Batal
                </button>                
                <button type="button" class="btn btn-success btn-labeled btn-xs" id="act-save" onclick="save_edit('update')">
                	<b><i class="icon-floppy-disk"></i></b> Simpan
                </button> 
                </div>                  
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal fade" id="frmbox2" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger-700" style="background-image:url(assets/images/backgrounds/bg.png)">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            	<h4 class="modal-title"><i class="fa fa-navicon"></i> &nbsp;<?= 'Form Data Pengajuan ke Logistik' ?></h4>
            </div>
            <form id="frm2" class="form-horizontal">
                <input type="hidden" name="id" id="id"> 
                <input type="hidden" name="type" id="type"> 
                <div class="modal-body">
                    <div class="box-body" style="padding-bottom:0px">  
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">List Buku</label>
                            <div class="col-sm-9"> 
								<textarea id="list" cols='60' rows="20"></textarea>
                            </div>
                        </div>  
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Nomor E-Memo Pengajuan ke Logistik</label>
                            <div class="col-sm-9"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_memo_logistic_number]" id="book_memo_logistic_number" required>
                            </div>
                        </div>  
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Pengajuan ke Logistik</label>
                            <div class="col-sm-9"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_date_logistic_submission]" id="book_date_logistic_submission" required>
                            </div>
                        </div>  
                    </div><!-- /.box-body -->
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-labeled btn-xs pull-left" data-dismiss="modal">
                	<b><i class="icon-switch"></i></b> Batal
                </button>                
                <button type="button" class="btn btn-success btn-labeled btn-xs" id="act-save" onclick="save_logistic()">
                	<b><i class="icon-floppy-disk"></i></b> Simpan
                </button> 
                </div>                  
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<div class="modal fade" id="frmbox3" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger-700" style="background-image:url(assets/images/backgrounds/bg.png)">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            	<h4 class="modal-title"><i class="fa fa-navicon"></i> &nbsp;<?= 'Form Data Penerimaan Buku' ?></h4>
            </div>
            <form id="frm3" class="form-horizontal">
                <input type="hidden" name="id" id="id"> 
                <div class="modal-body">
                    <div class="box-body" style="padding-bottom:0px">  
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Nama Buku</label>
                            <div class="col-sm-9" id="list"> 
															<input type="text" class="form-control input-sm" name="inp[book_title]" id="book_title" readonly>
                            </div>
                        </div>  
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Jenis Buku</label>
                            <div class="col-sm-9"> 
															<?= form_dropdown('inp[book_type]', $book_type, '', 'class="form-control select2" id="book_type" required="required"') ?>
                            </div>
                        </div>  
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tanggal Penerimaan Buku</label>
                            <div class="col-sm-9"> 
                            	<input type="text" class="form-control input-sm" name="inp[book_received_date]" id="book_received_date" required>
                            </div>
                        </div>   
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Harga Pengadaan</label>
                            <div class="col-sm-9"> 
														<input type="text" class="form-control input-sm" name="inp[book_procurement_price]" id="book_procurement_price" required>
                            </div>
                        </div>   
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Jumlah Buku</label>
                            <div class="col-sm-9"> 
														<input type="text" class="form-control input-sm" name="inp[book_copy]" id="book_copy" required>
                            </div>
                        </div>  
                    </div><!-- /.box-body -->
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-labeled btn-xs pull-left" data-dismiss="modal">
                	<b><i class="icon-switch"></i></b> Batal
                </button>                
                <button type="button" class="btn btn-success btn-labeled btn-xs" id="act-save" onclick="save_accept()">
                	<b><i class="icon-floppy-disk"></i></b> Simpan
                </button> 
                </div>                  
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<div class="modal fade" id="frmbox4" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger-700" style="background-image:url(assets/images/backgrounds/bg.png)">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            	<h4 class="modal-title"><i class="fa fa-navicon"></i> &nbsp;<?= 'Form Upload Template Pengajuan' ?></h4>
            </div>
            <form id="frm4" class="form-horizontal">
                <div class="modal-body"> 
										<div>
												<div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">File</label>
                            <div class="col-sm-9"> 
														<input type="file" class="form-control input-sm" name="file" id="file" required>
                            </div>
                        </div>  
                    </div><!-- /.box-body -->
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-labeled btn-xs pull-left" data-dismiss="modal">
                	<b><i class="icon-switch"></i></b> Batal
                </button>                
                <button type="button" class="btn btn-success btn-labeled btn-xs" id="act-save" onclick="save_upload()">
                	<b><i class="icon-floppy-disk"></i></b> Simpan
                </button> 
                </div>                  
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
 

 <div class="modal fade" id="frmbox5" role="dialog" aria-hidden="true">
	 <div class="modal-dialog">
		 <div class="modal-content">
			 <div class="modal-header bg-danger-700" style="background-image:url(assets/images/backgrounds/bg.png)">
				 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				 <h4 class="modal-title"><i class="fa fa-navicon"></i> &nbsp;<?= 'Form Data Penerimaan Buku' ?></h4>
			 </div>
			 <form id="frm5" class="form-horizontal">
				 <input type="hidden" name="id" id="id"> 
				 <div class="modal-body">
					 <div class="box-body" style="padding-bottom:0px">  
						 <div class="form-group form-group-sm">
							 <label for="pus_name" class="col-sm-3 control-label">Nama Buku</label>
							 <div class="col-sm-9" id="list"> 
								 <input type="text" class="form-control input-sm" name="inp[book_title]" id="book_title" readonly>
							 </div>
						 </div>  
						  
						 <div class="form-group form-group-sm">
							 <label for="pus_name" class="col-sm-3 control-label">Tanggal Konfirmasi Email</label>
							 <div class="col-sm-9"> 
								 <input type="text" class="form-control input-sm" name="inp[book_date_email_confirmed]" id="book_date_email_confirmed" required>
							 </div> 
						 </div>   
					 </div><!-- /.box-body -->
				 </div>
				 <div class="modal-footer">
				 <button type="button" class="btn btn-danger btn-labeled btn-xs pull-left" data-dismiss="modal">
					 <b><i class="icon-switch"></i></b> Batal
				 </button>                
				 <button type="button" class="btn btn-success btn-labeled btn-xs" id="act-save" onclick="save_email_confirmed()">
					 <b><i class="icon-floppy-disk"></i></b> Simpan
				 </button> 
				 </div>                  
			 </form>
		 </div><!-- /.modal-content -->
	 </div><!-- /.modal-dialog -->
 </div><!-- /.modal -->
 
 
 <div class="modal fade" id="frmbox6" role="dialog" aria-hidden="true">
	 <div class="modal-dialog">
		 <div class="modal-content">
			 <div class="modal-header bg-danger-700" style="background-image:url(assets/images/backgrounds/bg.png)">
				 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				 <h4 class="modal-title"><i class="fa fa-navicon"></i> &nbsp;<?= 'Form Data Penerimaan Buku' ?></h4>
			 </div>
			 <form id="frm6" class="form-horizontal">
				 <input type="hidden" name="id" id="id"> 
				 <div class="modal-body">
					 <div class="box-body" style="padding-bottom:0px">  
						 <div class="form-group form-group-sm"> 
							 <label for="pus_name" class="col-sm-3 control-label">Nama Buku</label>
							 <div class="col-sm-9" id="list"> 
								 <input type="text" class="form-control input-sm" name="inp[book_title]" id="book_title" readonly>
							 </div>
						 </div>  
						  
						 <div class="form-group form-group-sm">
							 <label for="pus_name" class="col-sm-3 control-label">Tanggal Ketersediaan Buku</label>
							 <div class="col-sm-9"> 
								 <input type="text" class="form-control input-sm" name="inp[book_date_available]" id="book_date_available" required>
							 </div>
						 </div>   
						  
						  <div class="form-group form-group-sm">
							  <label for="pus_name" class="col-sm-3 control-label">No. Katalog</label>
							  <div class="col-sm-9"> 
								  <input type="text" class="form-control input-sm" name="inp[book_catalog_number]" id="book_catalog_number" required>
							  </div>
						  </div> 
					 </div><!-- /.box-body -->
				 </div>
				 <div class="modal-footer">
				 <button type="button" class="btn btn-danger btn-labeled btn-xs pull-left" data-dismiss="modal">
					 <b><i class="icon-switch"></i></b> Batal
				 </button>                
				 <button type="button" class="btn btn-success btn-labeled btn-xs" id="act-save" onclick="save_available()">
					 <b><i class="icon-floppy-disk"></i></b> Simpan
				 </button> 
				 </div>                  
			 </form>
		 </div><!-- /.modal-content -->
	 </div><!-- /.modal-dialog -->
 </div><!-- /.modal -->

<?php $this->load->view('frontend/tpl_footer'); ?>
<!-- Theme JS files -->	
<script type="text/javascript" src="assets/limitless/global/js/plugins/pickers/datepicker.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/core/setting.js"></script> 
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/styling/uniform.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>

<script type="text/javascript">
var tb 		= '#table';      
var baseurl = 'index.php/<?= y_url_apps('bookprocurement_url') ?>/telupress';
var date = '<?= date('d F Y').' '.date('H:i:s'); ?>';

$(document).ready(function() {	

	
$('#book_startdate_realization_step_1, #book_enddate_realization_step_1, #book_startdate_realization_step_2, #book_enddate_realization_step_2, #book_startdate_realization_step_3, #book_enddate_realization_step_3, #book_startdate_realization_step_4, #book_enddate_realization_step_4, #book_startdate_realization_step_5, #book_enddate_realization_step_5, #book_startdate_realization_step_6, #book_enddate_realization_step_6,   #book_received_date').datepicker({  
	format: "dd-mm-yyyy"
});

$('#book_id_user').select2({
        ajax: {
            url:baseurl+"/getlecturerid",
            dataType: 'json',
            type: 'POST',
            data: function (params) {
                return {
                    searchTerm: params.term
                };
            },
            processResults: function (data) {
                return { results: data };
            }
        },
        minimumInputLength: 3
    });

$("#status,#prodi,#type,#book_type").select2({
	minimumResultsForSearch: Infinity
});       

$("#dates_submission_option, #dates_logistic_option, #dates_acceptance_option, #dates_email_confirmed_option, #dates_available_option, #book_id_prodi").select2({
	minimumResultsForSearch: Infinity
}); 



$('#book_date_prodi_submission, #book_date_logistic_submission, #book_date_acceptance, #book_date_email_confirmed, #book_date_available').datepicker({
	format: "dd-mm-yyyy"
});
 
$('#dates_submission, #dates_logistic, #dates_acceptance, #dates_email_confirmed, #dates_available').hide(); 

$("#filter").click(function(){ 
	_reload();
}); 
 
$("#dates_submission_option").change(function(){ 
	if($(this).val()=='all') $("#dates_submission").hide(); 
	else $("#dates_submission").show(); 
});  
 
 $("#dates_logistic_option").change(function(){ 
	 if($(this).val()=='all') $("#dates_logistic").hide(); 
	 else $("#dates_logistic").show(); 
 });  
 
 $("#dates_acceptance_option").change(function(){ 
	 if($(this).val()=='all') $("#dates_acceptance").hide(); 
	 else $("#dates_acceptance").show(); 
 });  
 
 $("#dates_email_confirmed_option").change(function(){ 
	 if($(this).val()=='all') $("#dates_email_confirmed").hide(); 
	 else $("#dates_email_confirmed").show(); 
 }); 
 
 $("#dates_available_option").change(function(){ 
	 if($(this).val()=='all') $("#dates_available").hide(); 
	 else $("#dates_available").show(); 
 }); 

$(tb).dataTable({
	'ajax': {
					'url':baseurl+'/json', 
			'data' : function(data) {
				data.prodi		= $('#prodi').val(); 
				data.status		= $('#status').val(); 
				data.dates_submission_option		= $('#dates_submission_option').val(); 
				data.dates_logistic_option			= $('#dates_logistic_option').val(); 
				data.dates_acceptance_option		= $('#dates_acceptance_option').val(); 
				data.dates_email_confirmed_option	= $('#dates_email_confirmed_option').val(); 
				data.dates_available_option			= $('#dates_available_option').val(); 
				data.dates_submission				= $('#dates_submission').val(); 
				data.dates_logistic					= $('#dates_logistic').val(); 
				data.dates_acceptance				= $('#dates_acceptance').val(); 
				data.dates_email_confirmed			= $('#dates_email_confirmed').val(); 
				data.dates_available				= $('#dates_available').val(); 
			}
	}, 
	'order':[ 
		[2, 'desc'],[4, 'asc']
	], 
	'columnDefs': [ 
		{ 'targets': 'nosort', 'searchable': false, 'orderable': false },
		{ 'targets': 'center', 'className': 'center' } 
	],  
	dom: 'Blfrtip',				
	buttons: [{
		text: '<i class="icon-file-excel position-left"></i>Export Excel',
		extend: 'excel',
		className: 'btn btn-sm btn-success',
		title: 'Data Pengajuan TelU Press',
		filename: 'Data Pengajuan TelU Press',
		messageTop: 'Data Pengajuan TelU Press - Per tanggal cetak: '+date
	}],
	"drawCallback": function( settings ) {
		$(".chk-logistic").uniform({
			radioClass: 'choice',
			wrapperClass: 'border-primary-600 text-primary-800'
		});
	},
	'scrollX': true
 });

 

// $('#book_id_member').select2({
// 		ajax: {
// 				url:baseurl+"/getmember",
// 				dataType: 'json',
// 				type: 'POST',
// 				data: function (params) {
// 						return {
// 								searchTerm: params.term
// 						};
// 				},
// 				processResults: function (data) {
// 						return { results: data };
// 				}
// 		},
// 		minimumInputLength: 3
// });

$("#chk-all-logistic").change(function(){
	$('input.chk-logistic:checkbox').not(this).prop('checked', this.checked);
	$.uniform.update();
}); 

$("#chk-all-logistic").uniform({
		radioClass: 'choice'
});   
 
<?php $iuser = $this->session->userdata(); if ($iuser['usergroup']=='superadmin'){ ?>
$('.dt-buttons').append('<button class="btn btn-sm btn-primary" onclick="add()"><i class="icon-add"></i> &nbsp;Tambah Data</button>');
<?php } ?>
$('.dataTables_filter input[type=search]').attr('placeholder','Pencarian');

$('.dataTables_length select').select2({
	minimumResultsForSearch: Infinity,
	width: 'auto'
});

 
// $('.dt-buttons').html('<button class="btn btn-sm btn-success" onclick="excel()"><i class="icon-file-excel position-left"></i>Export Excel</button><button class="btn btn-sm btn-primary" onclick="question()"><i class="icon-file-excel position-left"></i>Download Kuesioner</button>');
});

 
 

function add()
{
_reset();
$('#act-save').show();
$('#act-update').hide();
$('#frmbox').modal({keyboard: false, backdrop: 'static'});
}

 

function edit(id)
{		
$.ajax({
	url:baseurl+'/edit',
	global:false,
	async:true,
	dataType:'json',
	type:'post',
	data: ({ id : id }),
	success: function(e) {
		_reset();
		$('#act-save').hide();
		$('#act-update').show();
		$.each(e, function(key, value) {
			$('#frmbox_edit #'+key).val(value);
		}); 
		$('#frmbox_edit #id').val(id); 
 
		$('#frmbox_edit #book_id_user').html('<option selected value="'+e.book_id_user+'">('+e.master_data_number+') - '+e.master_data_fullname+'</option>');
		$('#frmbox_edit #book_id_user').val(e.book_id_user).trigger('change');
		$('#frmbox_edit #book_id_prodi').val(e.book_id_prodi).trigger('change');
		$('#frmbox_edit').modal({keyboard: false, backdrop: 'static'});
	},
	error : function() {
		alert('<?= $this->config->item('alert_error') ?>');	 
	},
	beforeSend : function() {
		$('#loading-img').show();
	},
	complete : function() {
		$('#loading-img').hide();
	}
});	
}

function download(){
window.open('<?= base_url() ?>cdn/template_pengajuan_buku.xlsx');
}


function save_edit(url)
{
	if($("#frm_edit").valid())
		{
			$.ajax({
				url:baseurl+'/'+url,
				global:false,
				async:true,
				type:'post',
				dataType:'json',
				data: $('#frm_edit').serialize(),
				success : function(e) {
					if(e.status == 'ok;') 
					{
						_reload();
						$("#frmbox_edit").modal('hide');
					} 
					else alert(e.text);
				},
				error : function() {
					alert('<?= $this->config->item('alert_error') ?>');	 
				},
				beforeSend : function() {
					$('#loading-img').show();
				},
				complete : function() {
					$('#loading-img').hide();
				}
			});
		}
}


 
function save(url)
{
if($("#frm").valid())
{
	$.ajax({
		url:baseurl+'/'+url,
		global:false,
		async:true,
		type:'post',
		dataType:'json',
		data: $('#frm').serialize(),
		success : function(e) {
			if(e.status == 'ok;') 
			{
				_reload();
				$("#frmbox").modal('hide');
			} 
			else alert(e.text);
		},
		error : function() {
			alert('<?= $this->config->item('alert_error') ?>');	 
		},
		beforeSend : function() {
			$('#loading-img').show();
		},
		complete : function() {
			$('#loading-img').hide();
		}
	});
}
}

 

function del(id, txt)
{
if(confirm('Data: '+txt+'\nApakah anda yakin akan menghapus data tersebut ?')) {
	$.ajax({
		url:baseurl+'/delete',
		global:false,
		async:true,
		type:'post',
		dataType:'json',
		data: ({id : id }),
		success: function(e) { 
			if(e.status == 'ok;') 
			{
				_reload();
			} 
			else alert(e.text);
		},
		error : function() {
			alert('<?= $this->config->item('alert_error') ?>');	 
		},
		beforeSend : function() {
			$('#loading-img').show();
		},
		complete : function() {
			$('#loading-img').hide();
		}
	});	
}
}

function _reset()
{ 
$('#book_id_prodi').val('').trigger('change');
validator.resetForm();
$("label.error").hide();
 $(".error").removeClass("error");
$('#frm')[0].reset();
}

function _reload()
{
$(tb).dataTable().fnDraw();
} 
</script>