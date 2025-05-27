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
    <h4>New Document</h4>
    <a href="" class="text-primary">New Document</a>
</div>
<?php
$session = $this->session->userdata('user_doc'); 
?>
<form name="frm" class="form-horizontal" id="frm" method="post" enctype="multipart/form-data" action="index.php/document/lists2/save">
<div class="panel panel-default flat">
    <div class="panel-heading">
        <h6 class="panel-title">Workflow</h6>
    </div>
    <div class="panel-body">
        <div class="row">   
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-2 control-label">Pembuat</label>
				<div class="col-sm-10">
                    <?= $session['username'].' - '.$session['fullname'] ?>
				</div>
			</div> 
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-2 control-label">Workflow <span class="text-danger">*</span></label>
				<div class="col-sm-10">
                    <?= form_dropdown('inp[workflow_id]', $workflow,'', 'class="form-control select2 required" id="workflow"') ?>
				</div>
			</div> 
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-2 control-label">Jenis Pustaka <span class="text-danger">*</span></label>
				<div class="col-sm-10">
                    <select name="inp[knowledge_type_id]" id="knowledge_type_id" class="form-control select2 required"><option value="">Pilih Jenis Pustaka</option></select>
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
                    <input type="text" name="inp[title]" id="title" class="form-control required" >
				</div>
			</div> 
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-2 control-label">Subject <span class="text-danger">*</span></label>
				<div class="col-sm-10">
                    <select name="inp[knowledge_subject_id]" id="knowledge_subject_id" class="form-control select2 required"><option value="">Pilih Subject</option></select>
				</div>
			</div> 
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-2 control-label">Abstrak <span class="text-danger">*</span></label>
				<div class="col-sm-10">
                    <textarea name="inp[abstract_content]" id="abstract_content" width="100%" class="form-control"></textarea>
				</div>
			</div> 
            <div id="lecturer">
                <div class="form-group form-group-sm">
                    <label for="pus_name" class="col-sm-2 control-label">Dosen Pembimbing 1 <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select name="inp[lecturer_id]" id="lecturer_id" class="form-control select2 required"><option value="">Pilih Dosen Pembimbing 1</option></select>
                    </div>
                </div> 
                <div class="form-group form-group-sm">
                    <label for="pus_name" class="col-sm-2 control-label">Dosen Pembimbing 2 <span class="text-danger"></span></label>
                    <div class="col-sm-10">
                        <select name="inp[lecturer2_id]" id="lecturer2_id" class="form-control select2 "><option value="">Pilih Dosen Pembimbing 2</option></select>
                    </div>
                </div> 
            </div>
        </div>  
         
    </div>
</div>
 
<div class="panel panel-default">
    <div class="panel-heading">
        <h6 class="panel-title">Unit</h6>
    </div>
    <div class="panel-body">
        <div class="row">   
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-2 control-label">Unit <span class="text-danger">*</span></label>
				<div class="col-sm-10">
                    <?= form_dropdown('inp[course_code]', $unit,'', 'class="form-control select2 required" id="unit"') ?>
				</div>
			</div> 
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-2 control-label">Kompetensi</label>
				<div class="col-sm-10">
                    <select name="master_subject[]" id="master_subject" multiple="multiple"  data-fouc></select>
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
				<label for="pus_name" class="col-sm-2 control-label">Upload File</label>
				<div class="col-sm-10" id="file_list"> 
                    
				</div> 
			</div>  
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
				<div class="col-sm-10"> 
				</div>
			</div>  
			<div class="form-group form-group-sm">
				<label for="pus_name" class="col-sm-2 control-label">Updated</label>
				<div class="col-sm-10"> 
				</div>
			</div>  
        </div>  
         
    </div>
</div> 

<div class="panel panel-default panel-body text-center"> 

	<a href="index.php/document/lists2" class="btn btn-danger btn-labeled">
		<b><i class="icon-chevron-left position-left"></i></b>Kembali
	</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <button type="button" class="btn btn-primary btn-labeled" onclick="save()">
        <b><i class="icon-floppy-disk position-left"></i></b>Simpan Perubahan
    </button> 
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

var baseurl = 'index.php/document/lists2';

$(document).ready(function(){
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

    // Simple select without search
    $('#workflow,#knowledge_subject_id,#knowledge_type_id,#unit,#master_subject').select2({
    });

	$("#lecturer").hide();

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
	
	CKEDITOR.replace('abstract_content', {
            height: 300
        });


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

    

    $("#workflow").change(function() {
        if($(this).val()=='1'){
            $("#lecturer").show();
        }
        else {
            $("#lecturer").hide();
        }
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
		 textbox_data = CKEDITOR.instances.abstract_content.getData();
		if (textbox_data==='')
		{
			alert('Abstrak belum diinputkan');
		}
		else {
			$( "#frm" ).submit();
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
