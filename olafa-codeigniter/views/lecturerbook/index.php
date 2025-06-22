
<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong> | <?php echo getLang('total') ?> <?php echo $total ?></h3>
		</div>
		<div class="panel-content pagination2">
			<div class="row content_button">
				<div class="col-lg-2" >
					<?php if($this->session->userdata("user_id")=='1'){ ?>
					<a href="javascript:;" onclick="add()" class="btn btn-danger">
						 <i class="fa fa-plus-square"></i><?php echo getLang("add") ?> <?php echo getLang("Buku Dosen") ?>
					</a>
					
					<?php } ?>
				</div> 
				<div class="col-lg-2" >
					<a href="javascript:;" onclick="downloads()" class="btn btn-primary">
						 <i class="fa fa-file"></i><?php echo getLang("Download") ?>
					</a>
				</div> 
			</div>
			<div id="dt_table1">
				<table id="table" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th width="5%"><?php echo getLang("no") ?></th>
							<th width="10%"><?php echo getLang("No Katalog") ?> </th>
							<th width="15%"><?php echo getLang("jenis buku") ?> </th>
							<th width="25%"><?php echo getLang("title") ?> </th>
							<th width="25%"><?php echo getLang("author") ?> </th>
							<th width="15%"><?php echo getLang("publisher") ?> </th>
							<th width="15%"><?php echo getLang("published_year") ?> </th>
							<th width="15%"><?php echo getLang("unit") ?> </th> 
							<th width="10%"><?php echo getLang("ISBN") ?></th>  
							<th width="10%"><?php echo getLang("action") ?></th>  
						</tr> 
					</thead>
					<tbody>
							
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div> 	  


<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-red">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
                <strong><h4 class="modal-title"></h4></strong>
            </div>
            <form id="form" class="form-horizontal form-validation">
            <input type="hidden" name="id" id="id">
            <div class="modal-body">
                <div class="form-group"> 
                    <label class="col-sm-3 control-label"><?php echo getLang('No Katalog'); ?></label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="inp[press_barcode]" id="press_barcode" placeholder="<?php echo getLang('No Katalog'); ?>"> 
                    </div>
                </div>   
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo getLang('Jenis Buku'); ?> <span class="required-class">*) </span></label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="inp[press_type]" id="press_type" placeholder="<?php echo getLang('Jenis Buku'); ?>" required> 
                    </div>
                </div>  
				<div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo getLang('Judul'); ?> <span class="required-class">*) </span></label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="inp[press_title]" id="press_title" placeholder="<?php echo getLang('Judul'); ?>" required> 
                    </div>
                </div> 
				<div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo getLang('Pengarang'); ?> <span class="required-class">*) </span></label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="inp[press_author]" id="press_author" placeholder="<?php echo getLang('Pengarang'); ?>" required> 
                    </div>
                </div> 
				<div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo getLang('Penerbit'); ?> <span class="required-class">*) </span></label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="inp[press_publisher]" id="press_publisher" placeholder="<?php echo getLang('Penerbit'); ?>" required> 
                    </div>
                </div> 
				<div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo getLang('Tahun Terbit'); ?> <span class="required-class">*) </span></label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="inp[press_published_year]" id="press_published_year" placeholder="<?php echo getLang('Tahun Terbit'); ?>" required> 
                    </div>
                </div> 
				<div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo getLang('Unit'); ?> <span class="required-class">*) </span></label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="inp[press_faculty_unit]" id="press_faculty_unit" placeholder="<?php echo getLang('Unit'); ?>" required> 
                    </div>
                </div> 
				<div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo getLang('ISSN/ISBN'); ?> <span class="required-class">*) </span></label>
                    <div class="col-sm-9">
                        <input class="form-control" type="text" name="inp[press_isbn]" id="press_isbn" placeholder="<?php echo getLang('ISSN/ISBN'); ?>" required> 
                    </div>
                </div> 
            </div>
            <div class="modal-footer"> 
			    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
			    <button type="button" class="btn btn-success" onclick="save()"><i class="fa fa-save"></i>&nbsp;&nbsp;<?php echo getLang("save") ?></button>
            </div>
            </form>
        </div>
    </div>
</div>


  
<div class="modal fade" id="modal_delete_rfidreg" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"><i class="fa fa-trash"></i>&nbsp;&nbsp;<?php echo getLang("delete")?> <?php echo getLang('rfid') ?></h4></strong>
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
				
<?php $this->load->view('theme_footer'); ?>
<!--<script type="text/javascript" src="//unpkg.com/xlsx/dist/xlsx.full.min.js"></script>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js" integrity="sha512-jDEmOIskGs/j5S3wBWQAL4pOYy3S5a0y3Vav7BgXHnCVcUBXkf1OqzYS6njmDiKyqes22QEX8GSIZZ5pGk+9nA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script language="javascript" type="application/javascript"> 
var save_method; 
var table;
var form = $('#modal_form #form');
form.validate({       
	ignore: "",
	rules: {
		'inp[rfidreg_capacity]' : 'number'        
	}, 
	onkeyup: false
}); 
$(document).ready(function () { 
	
	
	table = $('#table').DataTable({ 
        "processing": true,  
        "serverSide": true,  
		"destroy": true,
        "order": [] ,   
		"pageLength": 25,        
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
        "ajax": {
            "url": 'index.php/lecturerbook/ajax_index',
            "type": "POST"
        }, 
        "columnDefs": [
			{ 
				"targets": [ ], 
				"orderable": false,  
			},
        ]
    }); 
	
	$('#dt_table1 .dataTables_filter input').unbind().bind('keyup', function(e) {
       if(e.keyCode == 13) {
		$("#table").dataTable().fnFilter(this.value);
		}
	
	}); 
});

function add() {
	reset();  
	save_method = 'add';
	$('#rfidreg_id').removeAttr('disabled');  	
	$('#modal_form').modal({keyboard: false, backdrop: 'static'});
	$('#modal_form .modal-title').html('<i class="fa fa-plus-square"></i>&nbsp;&nbsp;<?php echo getLang("add").' '. getCurrentMenuName() ?>'); 
} 


function edit(id){
  save_method = 'update';
  reset();

	  $.ajax({
		url : "<?php echo site_url('index.php/lecturerbook/edit')?>",
		type: "POST",
		data : {
			id : id
		},
		dataType: "JSON",
		success: function(data){
			$('#modal_form #id').val(id); 		
			 $.each(data, function(key, value) {
				$('#modal_form #'+key).val(value);
			}); 
			 
			
			$('#rfidreg_id').attr('disabled', 'disabled'); 	
			$('#modal_form').modal({keyboard: false, backdrop: 'static'});
			$('#modal_form .modal-title').html('<i class="fa fa-pencil-square-o"></i>&nbsp;&nbsp;<?php echo getLang("edit").' '. getCurrentMenuName() ?>');
		},
		error: function (jqXHR, textStatus, errorThrown){
			alert('Error get data from ajax');
		}
	});
}


function downloads(){  
	  $.ajax({
		url: 'index.php/lecturerbook/ajax_index',
		type: "POST", 
		data : {
			excel : '1'
		},
		dataType: "JSON",
		success: function(data){ 
			 // const result = JSON.parse(JSON.stringify(data));
				// result.foreach((item) => { 
					// for(var i = 0 ; i<= 7; i++){
						// delete item[i];
					// }
				// });
			//jika ingin rename header
			var Heading = [
			  [ "","No. Katalog", "Jenis Buku","Judul","Pengarang","Penerbit","Tahun Terbit","Unit","ISSN/ISBN"],
			];
			 
			//Had to create a new workbook and then add the header
			const ws = XLSX.utils.book_new();
			
			  
			if(typeof XLSX == 'undefined') XLSX = require('xlsx');
			
			
			
			
			//jika ingin rename header
			XLSX.utils.sheet_add_aoa(ws, Heading); 
			XLSX.utils.sheet_add_json(ws, data, {skipHeader: true, origin: "A2"});
			
			//jika tidak perlu rename header  
			// var ws = XLSX.utils.json_to_sheet(data);

			//membatasi kolom tertentu yang ditampilkan
			var wb = XLSX.utils.book_new();
			var range = XLSX.utils.decode_range(ws['!ref']);
			range.s.c = 1;
			range.e.c = 8;
			var newRange = XLSX.utils.encode_range(range);
			ws['!ref'] = newRange;
			//=============================================
			XLSX.utils.book_append_sheet(wb, ws, "Data");

			 
			XLSX.writeFile(wb, "buku dosen telkom university.xlsx");
			 
		},
		error: function (jqXHR, textStatus, errorThrown){
			alert('Error get data from ajax');
		}
	});
	
	
}

function save(){
	var url;
	if(save_method == 'add'){
		url = "<?php echo site_url('index.php/lecturerbook/insert')?>";
	}else{
		url = "<?php echo site_url('index.php/lecturerbook/update')?>";
	}

	if (form.valid()) {
		$.ajax({
			url : url,
			type: "POST",
			data: form.serialize(),
			dataType: "JSON",
			beforeSend : function() {
				showLoading();
			},
			complete : function() {
				hideLoading();
			},
			success: function(data){ 
				
				if (data.status!='False'){ 
					reload(); 
					$('#modal_form').modal('hide');  
				}
				else info_alert('warning','username atau rfid yang diinputkan sudah ada');
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				info_alert('warning','<?php echo getLang("error_xhr")?>');
			}
		});
	}
}   

function del(id,data) { 
	
	 
	$('#modal_delete_rfidreg').modal({keyboard: false, backdrop: 'static'}); 
	$('#modal_delete_rfidreg #id').val(id); 
	$('#modal_delete_rfidreg .modal-body').html('<?php echo getLang("are_you_sure_want_to_delete_data")?> <strong>'+data+'</strong> ?');  
} 

function deletes(status) { 
	var form = $("#modal_delete_rfidreg #form");

	$.ajax({
		url : 'index.php/lecturerbook/deletes',
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
			$('#modal_delete_rfidreg').modal('hide'); 
			reload(); 
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
			info_alert('warning','<?php echo getLang("error_xhr")?>');
		}
	});
} 

function reload() {
   table.draw();
} 
 
function reset() {
	$('#modal_form .token-input-dropdown').remove();    
	$('#modal_form .token-input-list').remove();
	form.validate().resetForm();   
	$("#modal_form #form :input").removeData("previousValue");	//remove remote jquery validate previous value  
	form[0].reset(); 
	$("label.error").hide();
 	$(".error").removeClass("error");
}
</script>	