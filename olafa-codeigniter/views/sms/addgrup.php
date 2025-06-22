

					<div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="x_panel"> 
                                <div class="x_content">
                                    <br />
                                    <form id="demo-form2" data-parsley-validate method="post" action="index.php/sms/addGrupDb" class="form-horizontal form-label-left">

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
											<div class="col-md-10 col-sm-10 col-xs-12" > 
												
													<textarea id="textareas" name="member" rows="1"></textarea>			
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
		
		$('#textareas').textext({
			plugins : 'autocomplete tags ajax',  
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
				url:'index.php/sms/addmember',
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