<style>

.validation-invalid-label {
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
    display: block;
    color: #ef5350;
    position: relative;
    padding-left: 1.625rem;
}

.validation-valid-label {
    color: #25b372;
}

.validation-invalid-label:before, .validation-valid-label:before {
    font-family: icomoon;
    font-size: 1rem;
    position: absolute;
    top: 0.1875rem;
    left: 0;
    display: inline-block;
    line-height: 1;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
</style>
<div style="flex-direction: row!important;display: flex!important;justify-content: space-between;align-items: center">
    <h4>Edit Document</h4>
    <a href="https://openlibrary.telkomuniversity.ac.id/open/index.php/document" class="text-primary">Edit Document</a>
</div>
<?php
$session = $this->session->userdata('user_doc'); 
?>
<form name="frm" class="form-horizontal" id="frm" method="post" enctype="multipart/form-data" action="index.php/document/lists/update">
<div class="panel panel-default flat">
    <div class="panel-heading">
        <h6 class="panel-title">Workflow</h6>
    </div>
	<input type="hidden" name="wd_id" id="wd_id" class="form-control " value="<?=$wd->id ?>" >
	<input type="hidden" name="latest_state_id_old" id="latest_state_id_old" class="form-control " value="<?=$wd->latest_state_id ?>" >
	<input type="hidden" name="workflow_id" id="workflow_id" class="form-control " value="<?=$wd->w_id ?>" >
    <div class="panel-body">
        <div class="row">   
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-2 control-label">Pembuat</label>
				<div class="col-sm-10">
                    <?= $wd->master_data_user.' - '.$wd->master_data_fullname ?>
				</div>
			</div> 
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-2 control-label">Workflow</label>
				<div class="col-sm-10">
                    <?= $wd->w_name ?>
				</div>
			</div> 
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-2 control-label">Jenis Pustaka</label>
				<div class="col-sm-10">
                    <?= $wd->jenis_katalog ?>
				</div>
			</div> 
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-2 control-label">Current State</label>
				<div class="col-sm-10">
                    <?= $wd->state_name ?>
				</div>
			</div> 
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-2 control-label">Next State</label>
				<div class="col-sm-10">
					<?php if($wd->can_edit_state=='1'){ ?>
						<?= form_dropdown('latest_state_id', $next,'', 'class="form-control select2" id="latest_state_id"') ?>
					<?php } else { ?> -
					<?php } ?>
				</div>
			</div>  
        </div>   
         
    </div> 
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h6 class="panel-title">Document</h6>
    </div>
    <div class="panel-body">
        <div class="row">   
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-2 control-label">Title <span class="text-danger">*</span></label>
				<div class="col-sm-10">
					<?php if($wd->can_edit_attribute=='1'){ ?>
						<input type="text" name="inp[title]" id="title" class="form-control required" value="<?=$wd->title?>" >
					<?php } else { ?> <?=$wd->title?>
					<?php } ?>
				</div>
			</div> 
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-2 control-label">Subject <span class="text-danger">*</span></label>
				<div class="col-sm-10">
					<?php if($wd->can_edit_attribute=='1'){ ?>
						
						<select name="inp[knowledge_subject_id]" id="knowledge_subject_id" class="form-control select2 required"><option selected value="<?=$wd->ks_id?>"><?=$wd->ks_name ?></option></select>
					<?php } else { ?> <?=$wd->ks_name?>
					<?php } ?>
				</div>
			</div> 
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-2 control-label">Abstrak <span class="text-danger">*</span></label>
				<div class="col-sm-10">
					<?php if($wd->can_edit_attribute=='1'){ ?>
						<textarea name="inp[abstract_content]" id="abstract_content" width="100%" class="form-control"><?=$wd->abstract_content ?></textarea>
					<?php } else { ?> <?=$wd->abstract_content ?>
					<?php } ?>
				</div>
			</div>
		<?php  
		if($wd->w_id=='1') { ?>
            <div id="lecturer">
				<?php
					if($session['membertype']=='1' and $wd->latest_state_id=='1'){ ?>
					<div class="form-group form-group-sm">
						<label for="pus_name" class="col-sm-2 control-label">Approve By <br>(Digunakan jika dosen pembimbing 1 berhalangan)</label>
						<div class="col-sm-10">
							<?php if($wd->can_edit_attribute=='1'){ ?>
								<select name="inp[approved_id]" id="approved_id" class="form-control select2"><option selected value="<?=$wd->approved_id?>"><?='('.$wd->approved_number.') - '.$wd->approved_name ?></option></select> 
							<?php } else { ?> <?='('.$wd->approved_number.') - '.$wd->approved_name?>
							<?php } ?> 
						</div>
					</div> 

				<?php
					}
				?>
                <div class="form-group form-group-sm">
                    <label for="pus_name" class="col-sm-2 control-label">Dosen Pembimbing 1 <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
						<?php if($wd->can_edit_attribute=='1'){ ?>
							<select name="inp[lecturer_id]" id="lecturer_id" class="form-control select2 required"><option selected value="<?=$wd->lecturer_id?>"><?='('.$wd->lecturer_number.') - '.$wd->lecturer_name ?></option></select> 
						<?php } else { ?> <?='('.$wd->lecturer_number.') - '.$wd->lecturer_name?>
						<?php } ?> 
                    </div>
                </div> 
                <div class="form-group form-group-sm">
                    <label for="pus_name" class="col-sm-2 control-label">Dosen Pembimbing 2 <span class="text-danger"></span></label>
                    <div class="col-sm-10">
						
						<?php if($wd->can_edit_attribute=='1'){ ?>
							<select name="inp[lecturer2_id]" id="lecturer2_id" class="form-control select2"><option selected value="<?=$wd->lecturer2_id?>"><?='('.$wd->lecturer2_number.') - '.$wd->lecturer2_name ?></option></select> 
						<?php } else { ?> <?=($wd->lecturer2_name!='' ? '('.$wd->lecturer2_number.') - '.$wd->lecturer2_name : '-') ?>
						<?php } ?> 
                    </div>
                </div> 
            </div>
	<?php	} ?>
        </div>  
          
    </div>
</div>

<?php if($wd->w_id=='1'){ ?> 
	<div class="panel panel-default">
		<div class="panel-heading">
			<h6 class="panel-title">SDGs Point</h6>
		</div>
		<div class="panel-body">
			<div class="row">   
				<div class="form-group form-group-sm">
					<label for="pus_name" class="col-sm-2 control-label">SDGs <span class="text-danger">*</span></label>
					<div class="col-sm-10">
						<?php 	if(($wd->can_edit_attribute=='1' or $wd->can_edit_state=='1') and $wd->latest_state_id=='1'){ ?>
									<?php foreach($sdgs as $key => $row){ ?>
											<input type="checkbox" name="sdgs[<?=$key?>]" id="sdgs<?=$key?>" class="sdgs" <?=(in_array($key,$sdgs_existing)?'checked':'')?>>&nbsp;&nbsp;<?=$row ?><br>
										<?php } 
								} else { ?> <?=$sdgs_view ?>
						<?php } ?>
					</div>
				</div>  
			</div>  
			 
		</div>
	</div>
<?php } ?> 
 
<div class="panel panel-default">
    <div class="panel-heading">
        <h6 class="panel-title">Unit</h6>
    </div>
    <div class="panel-body">
        <div class="row">   
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-2 control-label">Unit <span class="text-danger">*</span></label>
				<div class="col-sm-10">
					<?php if($wd->can_edit_attribute=='1'){ ?>
						<?= form_dropdown('inp[course_code]', $unit,$wd->course_code, 'class="form-control select2" id="unit"') ?>
					<?php } else { ?> <?=$wd->NAMA_PRODI ?>
					<?php } ?>
				</div>
			</div> 
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-2 control-label">Kompetensi</label>
				<div class="col-sm-10">
					<?php if($wd->can_edit_attribute=='1'){ ?>
						<select name="master_subject[]" id="master_subject" multiple="multiple"  data-fouc><?=$master_subject ?></select>
					<?php } else { ?> <?=$master_subject_view ?>
					<?php } ?>
				</div>
			</div>  
        </div>  
         
    </div>
</div>
 
<div class="panel panel-default">
    <div class="panel-heading">
        <h6 class="panel-title">Files</h6>
    </div>
    <div class="panel-body">
        <div class="row">   
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-2 control-label">Existing File</label>
				<div class="col-sm-10"> 
					<strong>file yang telah di upload untuk dokumen ini</strong> 
					<br>
					
					<?php
					if($existing_file){
						foreach($existing_file as $row){ 
						
							if($wd->can_download=='1'){  
								echo '<a style="font-color:#2A82A6" href="/document/'.$row->document_id.'/download.html?fid='.$row->id.'">'.$row->name.' ('.$row->location.')</a><br>';
							} else {  
								echo $row->name.' ('.$row->location.')<br>';
							}  
						}
					}
					?>

				</div> 
			</div>  
        </div>  
        <div class="row">   
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-2 control-label">Upload File</label>
				<div class="col-sm-10" id="file_list"> 
                    <strong>jenis keanggotaan anda tidak diperbolehkan melakukan upload file terhadap dokumen ini </strong>
				</div> 
			</div>  
        </div>  
         
    </div>
</div>
 
<!--<div class="panel panel-default">
    <div class="panel-heading">
        <h6 class="panel-title">Comment</h6>
    </div>
    <div class="panel-body">
        <div class="row">   
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-2 control-label">Created</label>
				<div class="col-sm-10"> <?= $wd->created_at.' by '.$wd->created_by ?>
				</div>
			</div>  
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-2 control-label">Updated</label>
				<div class="col-sm-10"> <?= $wd->updated_at.' by '.$wd->updated_by ?>
				</div>
			</div>  
        </div>  
         
    </div>
</div> -->



 

<div class="panel panel-default">
    <div class="panel-heading">
        <h6 class="panel-title">Comments</h6>
    </div>
    <div class="panel-body">   
		<div class="row">   
			<div class="form-group form-group-sm"> 
				<div class="col-sm-12" id="append_comment">   
					<?php
						if($comment){
							foreach($comment as $key => $row){ 
					?>
							<div  id="append_comment<?=$row['id'] ?>">
								<div class="row">   
									<div class="form-group form-group-sm"> 
										<label for="pus_name" class="col-sm-0 control-label"></label>
										<div class="col-sm-12"> 
											<label for="pus_name" class="control-label"><b><i class="icon-user"></i> <?=$row['user']." - ".$row['name']?></b></label><div class="delete_comment" data-id="<?=$row['id'] ?>" style="text-align: right;float:right;cursor:pointer"><i class="icon-bin"></i></div><br>
											<div style="padding: 5px 10px;background-color: #fafafa;border: 1px solid #dedede;border-radius: 3px;">
												<?=$row['comment']?> 
												<br>
												<div style="font-size: 8pt;text-align: right;">
													<?=$row['created_at'] ?>
												</div>
											</div> 
											<div class="row">   
												<div class="form-group form-group-sm">
													<div class="col-sm-12">
														&nbsp;
													</div>
												</div>  
											</div>   
											<div id="append_reply<?=$row['id'] ?>">	
												<?php if($row['reply']){ ?>   
												<?php	foreach($row['reply'] as $key2 => $row2){ 
												?>
														
														<div id="append_reply2<?=$row2['id'] ?>">	
															<div class="row">   
																<div class="form-group form-group-sm"> 
																	<label for="pus_name" class="col-sm-1 control-label"></label>
																	<div class="col-sm-11">
																		<label for="pus_name" class="control-label"><b><i class="icon-bubbles"></i> <?=$row2['user']." - ".$row2['name']?></b></label><div class="delete_reply" data-id="<?=$row2['id'] ?>" style="text-align: right;float:right;cursor:pointer"><i class="icon-bin"></i></div><br>
																		<div style="padding: 5px 10px;background-color: #fafafa;border: 1px solid #dedede;border-radius: 3px;">
																			<?=$row2['comment']?>
																			<br> 
																			<div style="font-size: 8pt;text-align: right;">
																				<?=$row2['created_at'] ?>
																			</div>
																		</div>
																		
																	</div>
																</div>  
															</div> 
															<div class="row">   
																<div class="form-group form-group-sm">
																	<div class="col-sm-12">
																		&nbsp;
																	</div>
																</div>  
															</div>  
														</div>
												<?php
												

														}
													} 
												?>
											</div>	
											<div class="row">    
												<div class="form-group form-group-sm" style="display:none" id="show_reply<?=$row['id'] ?>">
													<label for="pus_name" class="col-sm-1 control-label"></label>
													<div class="col-sm-11">
														<textarea type="text" name="reply<?=$row['id'] ?>" id="reply<?=$row['id'] ?>" class="form-control"></textarea>
													<br> 
													</div>
												</div>   
											</div>    
											<?php if($wd->can_comment=='1'){ ?>
												<div class="row">   
													<label for="pus_name" class="col-sm-1 control-label"></label>
													<div class="form-group form-group-sm">
														<div class="col-sm-11">
															<div data-id="<?=$row['id'] ?>" class="btn btn-primary btn-labeled add_reply">
																<b><i class="icon-comments"></i></b>Add Reply
															</div>    
														</div>
													</div>  
												</div>  
											<?php } ?>
										</div>
									</div>  
								</div> 
								<div class="row">   
									<div class="form-group form-group-sm">
										<div class="col-sm-12">
											&nbsp;
										</div>
									</div>  
								</div> 
							</div>
					<?php
					

							}
						}
					?>
				</div>
			</div>  
		</div> 
		<div class="row">   
			<div class="form-group form-group-sm" style="display:none" id="show_comment">
				<label for="pus_name" class="col-sm-0 control-label"></label>
				<div class="col-sm-12">
					<textarea type="text" name="comment" id="comment" class="form-control"></textarea>
				</div>
			</div>  
		</div> 
		<?php if($wd->can_comment=='1'){ ?>
			<div id="add_comment" class="btn btn-primary btn-labeled">
				<b><i class="icon-comments"></i></b>Add Comment
			</div>    
		<?php } ?>
    </div>
</div> 


<div class="panel panel-default">
	<div class="panel-heading">
        <h6 class="panel-title">State History</h6>
    </div>
    <div class="panel-body">
        <div class="row">   
			<table class="workflow_tasks" width="100%" style="margin-bottom: 1.5em;">
				<tbody>
					<tr style="border: none;">
						<th style="border-top: none;">State</th>
						<th style="border-top: none; min-width: 100px;">Anggota</th>
						<th style="border-top: none;">Fullname</th>
						<th style="border-top: none; min-width: 100px;">Jenis</th>
						<th style="border-top: none; min-width: 100px;">Date</th>
					</tr>
					
						 <?php
						 foreach($document_state as $row){
							 echo 
							'<tr style="border: none;"><th style="border-top: none;">'.($row->close_date!=""?'<s>'.$row->state_name.'</s>':$row->state_name).'</th>
							<th style="border-top: none; min-width: 100px;">'.$row->master_data_user.'</th>
							<th style="border-top: none;">'.$row->master_data_fullname.'</th>
							<th style="border-top: none; min-width: 100px;">'.$row->name.'</th>
							<th style="border-top: none; min-width: 100px;">'.($row->open_date!=''?'<i class="icon-pencil"></i> '.$row->open_date : '').'<br>'.($row->close_date!=''?'<i class="icon-checkmark-circle"></i> '.$row->close_date : '').'</th></tr>';
						 }
						 ?>
					
				</tbody>
			</table>
        </div>  
         
    </div>
</div> 
 
<div class="panel panel-default">
    <div class="panel-heading">
        <h6 class="panel-title">Timestampable</h6>
    </div>
    <div class="panel-body">
        <div class="row">   
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-2 control-label">Created</label>
				<div class="col-sm-10"> <?= $wd->created_at.' by '.$wd->created_by ?>
				</div>
			</div>  
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-2 control-label">Updated</label>
				<div class="col-sm-10"> <?= $wd->updated_at.' by '.$wd->updated_by ?>
				</div>
			</div>  
        </div>  
         
    </div>
</div> 

<div class="panel panel-default panel-body text-center">  
		<a href="index.php/document/lists" class="btn btn-danger btn-labeled">
			<b><i class="icon-chevron-left position-left"></i></b>Kembali
		</a> 
	<?php if($wd->can_edit_attribute=='1' or $wd->can_edit_state=='1'){ ?>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<button type="button" class="btn btn-primary btn-labeled" onclick="save()">
				<b><i class="icon-floppy-disk position-left"></i></b>Simpan Perubahan
			</button> 
	<?php } ?>
</div>
</form>

<?php $this->load->view('frontend/tpl_footer'); ?>
<script type="text/javascript" src="assets/limitless/global/js/plugins/pickers/datepicker.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/styling/uniform.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/editors/ckeditor/ckeditor.js"></script>

<script type="text/javascript">

var baseurl = 'index.php/document/lists';

$(document).ready(function(){
	<?php if($wd->wsp_id=="" and $wd->member_id!=$session['id']){ ?>
		alert("Jenis anggota Anda tidak diperbolehkan mengakses halaman ini");
		window.location.href=baseurl;
	<?php } ?>
    $(".file-styled").uniform();


	$('#frm').validate({
		ignore: 'input[type=hidden], .select2-search__field', // ignore hidden fields
		errorClass: 'validation-invalid-label',
		successClass: 'validation-valid-label',
		validClass: 'validation-valid-label',
		highlight: function(element, errorClass) {
			$(element).removeClass(errorClass);
		},
		unhighlight: function(element, errorClass) {
			$(element).removeClass(errorClass);
		},
		success: function(label) {
			label.addClass('validation-valid-label').text('Success.'); // remove to hide Success message
		},

		// Different components require proper error label placement
		errorPlacement: function(error, element) {

			// Unstyled controls
			if (element.parents().hasClass('form-check')) {
				error.appendTo( element.closest('.form-check').parent() );
			}

			// Input with icons and Select2
			else if (element.parents().hasClass('form-group-feedback') || element.hasClass('select2-hidden-accessible')) {
				error.appendTo( element.parent() );
			}

			// Input group and custom controls
			else if (element.parent().is('.custom-file, .custom-control') || element.parents().hasClass('input-group')) {
				error.appendTo( element.parent().parent() );
			}

			// Other elements
			else {
				error.insertAfter(element);
			}
		},
		rules: {
			password: {
				minlength: 5
			},
			repeat_password: {
				equalTo: '#password'
			},
			email: {
				email: true
			},
			repeat_email: {
				equalTo: '#email'
			},
			minimum_characters: {
				minlength: 10
			},
			maximum_characters: {
				maxlength: 10
			},
			minimum_number: {
				min: 10
			},
			maximum_number: {
				max: 10
			},
			number_range: {
				range: [10, 20]
			},
			url: {
				url: true
			},
			date: {
				date: true
			},
			date_iso: {
				dateISO: true
			},
			numbers: {
				number: true
			},
			digits: {
				digits: true
			},
			creditcard: {
				creditcard: true
			},
			basic_checkbox: {
				minlength: 2
			},
			styled_checkbox: {
				minlength: 2
			},
			switch_group: {
				minlength: 2
			}
		},
		messages: {
			custom: {
				required: 'This is a custom error message'
			},
			basic_checkbox: {
				minlength: 'Please select at least {0} checkboxes'
			},
			styled_checkbox: {
				minlength: 'Please select at least {0} checkboxes'
			},
			switch_group: {
				minlength: 'Please select at least {0} switches'
			},
			agree: 'Please accept our policy'
		}
	});

    $('#par_birthdate').datepicker({
        format: "dd-mm-yyyy", 
        // startDate: new Date('01-01-1980'),
    }); 
    $('.select').select2();


	$('#lecturer_id').select2({
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

    $('#lecturer2_id').select2({
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


	$('#approved_id').select2({
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

    // Simple select without search
    $('#latest_state_id,#unit,#master_subject,#sdgs').select2({
    });
	<?php if($wd->can_edit_attribute=='1' && $wd->can_upload=='1'){ ?>
		$.ajax({
			url : baseurl+'/getfile',
			type: "POST",
			data: {
				'id' : <?=$wd->w_id ?>
			},
			dataType: "JSON",
			beforeSend : function() {
				$('#loading-img').show();
			},
			complete : function() {
				$('#loading-img').hide();
			},
			success: function(dt)
			{
				$("#file_list").html('<strong>pilih file yang sesuai dengan masing-masing jenis file upload yang disediakan sesuai dengan kebutuhan.<br>file baru akan menggantikan file lama yang sejenis secara otomatis<strong><br><br>'); 
				$.each(dt,function(index, value){
					$("#file_list").append(value.title+' ('+value.name+'.'+value.extension+')<br><input type="file" name="upload_type['+value.id+']" id="upload_type_'+value.id+'" class="upload_type"><br>');
				});  
			},
			error: function (jqXHR, textStatus, errorThrown)
			{

			}
		});
	<?php } ?>

    $('#knowledge_subject_id').select2({
        ajax: {
            url:baseurl+"/getsubjectid",
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
	<?php if($wd->can_edit_attribute=='1'){ ?>
		CKEDITOR.replace('abstract_content', {
				height: 300
			});

	<?php } ?>

    var substringMatcher = function() {
        return function findMatches(q, cb) {
            var matches = [];
            var strs = getcity_birthplace(q);

            $.each(strs, function(i, str) {
                matches.push({ value: str });
            });

            console.log(matches);
            cb(matches);
        };
    };

    $('#par_birthplace').typeahead(
        {
            hint: true,
            highlight: true,
            minLength: 3
        },
        {
            name: 'states',
            displayKey: 'value',
            source: substringMatcher()
        }
    );

    $('#school_city').select2({
        ajax: {
            url:baseurl+"/getcity",
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
        minimumInputLength: 0
    });

    $("#workflow").change(function() {
        $.ajax({
            url : baseurl+'/getknowledgetype',
            type: "POST",
            data: {
                'id' : $(this).val()
            },
            dataType: "JSON",
            beforeSend : function() {
                $('#loading-img').show();
            },
            complete : function() {
                $('#loading-img').hide();
            },
            success: function(dt)
            {
                $("#knowledge_type_id").html('<option value="">Pilih Jenis Pustaka</option>');
                $.each(dt,function(index, value){
                    $("#knowledge_type_id").append('<option value="'+value.id+'">'+value.name+'</option>');
                });
				 $('#knowledge_type_id').select2();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {

            }
        });
		
		
        $.ajax({
            url : baseurl+'/getfile',
            type: "POST",
            data: {
                'id' : $(this).val()
            },
            dataType: "JSON",
            beforeSend : function() {
                $('#loading-img').show();
            },
            complete : function() {
                $('#loading-img').hide();
            },
            success: function(dt)
            {
                $("#file_list").html('<strong>pilih file yang sesuai dengan masing-masing jenis file upload yang disediakan sesuai dengan kebutuhan.<br>file baru akan menggantikan file lama yang sejenis secara otomatis<strong><br><br>'); 
                $.each(dt,function(index, value){
                    $("#file_list").append(value.title+' ('+value.name+'.'+value.extension+')<br><input type="file" name="upload_type['+value.id+']" id="upload_type_'+value.id+'" class="upload_type"><br>');
                });  
            },
            error: function (jqXHR, textStatus, errorThrown)
            {

            }
        });
    }); 

    $("#unit").change(function() {
        $.ajax({
            url : baseurl+'/getmastersubject',
            type: "POST",
            data: {
                'id' : $(this).val()
            },
            dataType: "JSON",
            beforeSend : function() {
                $('#loading-img').show();
            },
            complete : function() {
                $('#loading-img').hide();
            },
            success: function(dt)
            {
                $("#master_subject").html('');
                $.each(dt,function(index, value){
                    $("#master_subject").append('<option value="'+value.id+'">'+value.code+' - '+value.name+'</option>');
                }); 
				
				$('#master_subject').select2({ 
					tokenSeparators: [',']
				});
            },
            error: function (jqXHR, textStatus, errorThrown)
            {

            }
        }); 
    }); 

	$("#add_comment").on('click',function() { 
		if($('#show_comment').css('display') == 'none')
		{
			$('#show_comment').show();
		}
		else {
			if($('#comment').val().trim()!=""){
				$.ajax({ 
					url : baseurl+'/add_comment',
					type: "POST",
					data: {
						'wd_id' : '<?=$wd->id ?>', 
						'latest_state_id_old' : '<?=$wd->latest_state_id ?>', 
						'comment' : $("#comment").val()
					},
					dataType: "JSON",
					beforeSend : function() {
						$('#loading-img').show();
					},
					complete : function() {
						$('#loading-img').hide();
					},
					success: function(dt)
					{ 
						$('#append_comment').append(dt.message);
						$('#show_comment').hide();
						$('#comment').val("");  
					},
					error: function (jqXHR, textStatus, errorThrown)
					{

					}
				}); 
			}
			else $('#show_comment').hide();
		
		} 
	});   

	$("#append_comment").on('click','.delete_comment',function() {    
		var id = $(this).data('id');

		if (confirm('Apakah Anda akan menghapus comment / reply ?')) { 
			$.ajax({ 
				url : baseurl+'/delete_comment_reply',
				type: "POST",
				data: { 
					'id' : id
				},
				dataType: "JSON",
				beforeSend : function() {
					$('#loading-img').show();
				},
				complete : function() {
					$('#loading-img').hide();
				},
				success: function(dt)
				{  
					$('#append_comment'+id).hide();  
				},
				error: function (jqXHR, textStatus, errorThrown)
				{

				}
			});  
		}
	}); 

	$("#append_comment").on('click','.delete_reply',function() {   
		var id = $(this).data('id');

		if (confirm('Apakah Anda akan menghapus comment / reply ?')) { 
			$.ajax({ 
				url : baseurl+'/delete_comment_reply',
				type: "POST",
				data: { 
					'id' : id
				},
				dataType: "JSON",
				beforeSend : function() {
					$('#loading-img').show();
				},
				complete : function() {
					$('#loading-img').hide();
				},
				success: function(dt)
				{  
					$('#append_reply2'+id).hide(); 
				},
				error: function (jqXHR, textStatus, errorThrown)
				{

				}
			});  
		} 
	});

	$("#append_comment").on('click','.add_reply',function() {  
		var id = $(this).data('id');

		if($('#show_reply'+id).css('display') == 'none')
		{
			$('#show_reply'+id).show();
		}
		else {
			if($('#reply'+id).val().trim()!=""){
				$.ajax({ 
					url : baseurl+'/add_reply',
					type: "POST",
					data: {
						'wd_id' : '<?=$wd->id ?>', 
						'latest_state_id_old' : '<?=$wd->latest_state_id ?>', 
						'comment' : $("#reply"+id).val(), 
						'parent_id' : id
					},
					dataType: "JSON",
					beforeSend : function() {
						$('#loading-img').show();
					},
					complete : function() {
						$('#loading-img').hide();
					},
					success: function(dt)
					{ 
						$('#append_reply'+id).append(dt.message);
						$('#show_reply'+id).hide();
						$('#reply'+id).val("");  
						
					},
					error: function (jqXHR, textStatus, errorThrown)
					{

					}
				}); 
			}
			else 
			$('#show_reply'+id).hide(); 
		
		}
	});
    // $.validator.addMethod("minDate", function(value, element) {
    //     var curDate = new Date('01-01-1980');
    //     var inputDate = new Date(value);
    //     if (inputDate >= curDate)
    //         return true;
    //     return false;
    // }, "Min date : 01-01-1980");   // error message 

    // $.validator.addClassRules({
        // photos:{
            // extension: "jpg|jpeg|png",
            // maxfilesize: {
                // "unit": "MB",
                // "size": 1
            // },
            // required: function() {
                // if($('#photos_hidden').val() == '')
                    // return true;
                // else
                    // return false;
            // }
        // }, 
        // // borndate :{
        // //     minDate: true
        // // }
    // }); 
});

function show_adv_search()
{
    if($('#adv-search-block').is(':visible'))
        $('#adv-search').html('Cari Selengkapnya');
    else
        $('#adv-search').html('Tutup');


    $('#adv-search-block').toggle();
} 

function save()
{
    if($("#frm").valid())
    {
		textbox_data = "test";
		<?php if($wd->can_edit_attribute=='1'){ ?>
			textbox_data = CKEDITOR.instances.abstract_content.getData();
		<?php } ?>
		if (textbox_data==='')
		{
			alert('Abstrak belum diinputkan');
		}
		else {
			<?php if($wd->latest_state_id=='1'){ ?>
			if($('.sdgs:checked').length==0 && $('#latest_state_id').val()!=2 && $('#latest_state_id').val()!=""){ 
				alert('Silahkan checklist point SDGs');
			}
			else $( "#frm" ).submit();
			
			<?php }else { ?>
			$( "#frm" ).submit();
			<?php } ?>
			// var formData = new FormData($('#frm')[0]);
			// $.ajax({
				// url:baseurl+'/biodata_save',
				// global:false,
				// async:true,
				// type:'post',
				// data: formData,
				// contentType: false,//untuk upload image
				// processData: false,//untuk upload image
				// dataType:'json',
				// success : function(e) {
					// if(e.status == 'success')
					// {
						// alert('Data telah berhasil diperbaharui'); 
						// window.location.href='dashboard';
					// }
					// else alert(e.error);
				// },
				// error : function() {
					// alert('<?= $this->config->item('alert_error') ?>');
				// },
				// beforeSend : function() {
					// $('#loading-img').show();
				// },
				// complete : function() {
					// $('#loading-img').hide();
				// }
			// });
		}
    } else {
        // $('html, body').animate({
            // scrollTop: ($('.validation-error-label').offset().top - 300)
        // }, 2000);
    }
}

function getcity_birthplace(q)
{
    var r = [];

    $.ajax({
        url:baseurl+'/getcity_birthplace',
        global:false,
        async:false,
        dataType:'json',
        type:'post',
        data: ({ q : q }),
        success: function(e) {
            r = e;
        },
        error : function() {
            alert('<?= $this->config->item('alert_error') ?>');
        }
    });

    return r;
}
</script>
