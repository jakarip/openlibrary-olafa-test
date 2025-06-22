<div class="row">
    <div class="col-lg-12">
        <div class="panel">
            <div class="panel-header bg-red">
			<h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong> </h3>
            </div>
            <div class="panel-content pagination2"> 
                <table id="table-member" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%"><?php echo getLang('no'); ?></th>
                            <th width="10%"><?php echo getLang('date'); ?></th>
                            <th width="10%"><?php echo getLang('name'); ?></th> 
							<th width="10%"><?php echo $quitioner['22']->quitioner_name ?></th> 
							<?php foreach ($quitioner as $key=> $row){ 
								
								if($key==count($quitioner)-1) break;
								?>
							<th width="10%"><?php echo $row->quitioner_name ?></th>
							<?php	
							}
							?>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> 

<?php $this->load->view('theme_footer'); ?>

<script type="text/javascript"> 
var save_method; 
var table;
var form 				= $('#modal_form #form');
var form_not_approved 	= $('#modal_not_approved #form');
form_not_approved.validate();
form.validate({       
	ignore: "",
	rules: {
		'inp[room_capacity]' : 'number'        
	}, 
	onkeyup: false
}); 

$(document).ready(function(){
	table = $('#table-member').DataTable({     
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],  
		"pageLength": 10,
		"processing": true,  
		"serverSide": true,  
		"destroy": true,  
		"order": [],  
		"ajax": {
			"url": "<?php echo site_url('index.php/respond/ajax_data')?>",
			"type": "POST"
		}, 
		"columnDefs": [
			{ 
				"targets": [ 0,-1 ], 
				"orderable": false,  
			},
		]
	});
	
	$('.dataTables_filter input').unbind().bind('keyup', function(e) {
	   if(e.keyCode == 13) {
		$("#table-member").dataTable().fnFilter(this.value);
		}
	});
});   

function reset() {
    form.validate().resetForm();   
	$("#modal_form #form :input").removeData("previousValue");	//remove remote jquery validate previous value  
    form[0].reset();    
	$("#modal_not_approved #form :input").removeData("previousValue");	//remove remote jquery validate previous value  
    form[0].reset();  	
	$("#modal_form #member_type").select2("val", "");
	$("#modal_form #member_bank_id").select2("val", "");
    $("label.error").hide();
    $(".error").removeClass("error");
} 

function reload() {
   table.draw();
}
</script>