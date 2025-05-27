 
					<div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel"> 
                                <div class="x_content">
                                    <br />
                                    <form id="demo-form2" data-parsley-validate method="post" action="index.php/pengadaan/addPengajuanDb" class="form-horizontal form-label-left">

                                        <div class="form-group">
                                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="first-name">Nomor Pengajuan <span class="required">*</span>
                                            </label>
                                            <div class="col-md-10 col-sm-10 col-xs-12">
                                                <input type="text" id="nomor"name="nomor" required="required" class="form-control col-md-7 col-xs-12">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="first-name">Nama Dosen <span class="required">*</span>
                                            </label>
                                            <div class="col-md-10 col-sm-10 col-xs-12">
                                                <input type="text" id="dosen"name="dosen" required="required" class="form-control col-md-7 col-xs-12">
                                            </div>
                                        </div>
										 <div class="form-group">
                                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="first-name">NIK <span class="required">*</span>
                                            </label>
                                            <div class="col-md-10 col-sm-10 col-xs-12">
                                                <input type="text" id="nik"name="nik" required="required" class="form-control col-md-7 col-xs-12">
                                            </div>
                                        </div>
										 <div class="form-group">
                                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="first-name">Jabatan  
                                            </label>
                                            <div class="col-md-10 col-sm-10 col-xs-12">
                                                <input type="text" id="jabatan"name="jabatan"  class="form-control col-md-7 col-xs-12">
                                            </div>
                                        </div>
										 <div class="form-group">
                                            <label class="control-label col-md-2 col-sm-2 col-xs-12" for="first-name">KK / Kaprodi / Dekan <span class="required">*</span>
                                            </label>
                                            <div class="col-md-10 col-sm-10 col-xs-12">
                                                <input type="text" id="kaprodi"name="kaprodi" required="required" class="form-control col-md-7 col-xs-12">
                                            </div>
                                        </div> 
										<div class="ln_solid"></div>
										 <div class="form-group" id="buttonid">
                                            <button type="button" class="btn btn-primary" id="tambah" value="1">Tambah List</button>
                                        </div>
										<div id="add">
											<div class="form-group">
												<label  class=" col-md-2 col-sm-2 col-xs-12" for="first-name">Mata Kuliah<span class="required">*</span>
												</label>
												<label  class=" col-md-1 col-sm-1 col-xs-12" for="first-name">Semester<span class="required">*</span>
												</label>
												<label  class=" col-md-2 col-sm-2 col-xs-12" for="first-name">Judul<span class="required">*</span>
												</label> 
												<label  class=" col-md-2 col-sm-2 col-xs-12" for="first-name">Pengarang
												</label>
												<label  class=" col-md-2 col-sm-2 col-xs-12" for="first-name">Penerbit
												</label>
												<label  class=" col-md-1 col-sm-1 col-xs-12" for="first-name">Tahun
												</label>
												<label  class=" col-md-2 col-sm-2 col-xs-12" for="first-name">Tipe<span class="required">*</span>
												</label>
												
											</div> 
											<div class="form-group">
												<div class="col-md-2 col-sm-2 col-xs-12">
													<input type="text" id="mk"name="mk[0]" required="required" class="form-control col-md-7 col-xs-12" placeholder="Mata Kuliah">
												</div>
												<div class="col-md-1 col-sm-1 col-xs-12">
													<input type="text" id="smt"name="smt[0]" required="required" class="form-control col-md-7 col-xs-12" placeholder="Smt">
												</div>
												<div class="col-md-2 col-sm-2 col-xs-12">
													<input type="text" id="judul"name="judul[0]" required="required" class="form-control col-md-7 col-xs-12" placeholder="Judul">
												</div>
												<div class="col-md-2 col-sm-2 col-xs-12">
													<input type="text" id="pengarang"name="pengarang[0]" class="form-control col-md-7 col-xs-12" placeholder="Pengarang">
												</div>
												<div class="col-md-2 col-sm-2 col-xs-12">
													<input type="text" id="penerbit"name="penerbit[0]" class="form-control col-md-7 col-xs-12" placeholder="Penerbit">
												</div>
												<div class="col-md-1 col-sm-1 col-xs-12">
													<input type="text" id="tahun"name="tahun[0]" class="form-control col-md-7 col-xs-12" placeholder="Tahun">
												</div>
												<div class="col-md-2 col-sm-2 col-xs-12">
													<select required="required" class="form-control col-md-7 col-xs-12" name="tipe[0]" id="tipe">
														<option value="">Tipe</option>
														<option value="Utama">Utama</option>
														<option value="Penunjang">Penunjang</option>
													</select>
												</div> 
											</div> 
											 
										</div>
                                        
                                        <div class="form-group">
											<div class="col-md-12 col-sm-12 col-xs-12 col-md-offset-3">
                                               &nbsp;
                                            </div>
                                            <div class="col-md-4 col-sm-4 col-xs-12">
                                                <button type="submit" class="btn btn-primary hijau simpan">Simpan</button>
                                                <button type="button" class="btn btn-primary merah" onclick="window.location='index.php/pengadaan/pengajuan'">Cancel</button>
                                            </div>
											 
                                        </div>

                                    </form>
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
				url:'index.php/pengadaan/addbook',
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
    });
</script>