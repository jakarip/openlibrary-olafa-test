<style> 
	.sc_time {width:150px !important;}
	.tl {width:25px !important;}
	.ui-datepicker{z-index: 10000 !important}; 
	#modal_gallery.modal-backdrop.fade.in {
		height:100% !important;
	}
	
	

</style> 

<div class="row">
    <div class="col-lg-12">
        <div class="panel">
            <div class="panel-header bg-red">
			<h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong> </h3>
            </div>
            <div class="panel-content pagination2"> 
				<div class="row content_button">
					<div class="col-lg-6" >
						<a href="javascript:;" onclick="add()" class="btn btn-danger">
							 <i class="fa fa-plus-square"></i><?php echo getCurrentMenuName() ?>
						</a>
					</div> 	
					<form id="search" name="search" method="post" action="index.php/booking/bo">
					<div class="col-lg-2" style="text-align:right;"><label><?php echo getLang('choose_date'); ?></label></div>
					<div class="col-lg-2" >
							<input class="form-control" type="text" name="date_choose" id="date_choose" value="<?php echo $date_choose ?>" placeholder="<?php echo getLang('date'); ?>" required>
					</div> 
   
					<div class="col-lg-2" >		
							<button type="submit" class="btn btn-primary"><i class="fa fa-search"></i>&nbsp;<?php echo getLang("search") ?></button>
					</div> 
					</form>
				</div> 
				<div id="schedule"></div>
				<br>
				<b>Ketentuan Peminjaman Ruangan : </b>
				<table class="table">
				<tr><td>1. </td><td>Anggota hanya dapat merequest peminjaman maksimal sebanyak 2x hingga admin melalukan approval/reject</td></tr>
				
				<tr><td>2. </td><td>Ketika admin sudah memberikan approval, anggota dapat datang ke bagian sirkulasi untuk mengambil kunci pada jam yang telah dipilih</td></tr>
				
				<tr><td>3. </td><td>Anggota diwajibkan memberikan jaminan kartu identitas baik ktm/karpeg/ktp untuk ditukarkan dengan kunci</td></tr>
				<tr><td>4. </td><td>Jika admin sudah memberikan approval tetapi anggota tidak datang pada jam yang dipesan sebanyak 2x, maka akan dilakukan <b>Blacklist</b> pada bulan itu</td></tr>
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
                    <label class="col-sm-3 control-label"><?php echo getLang('room_name'); ?> <span class="required-class">*) </span></label>
                    <div class="col-sm-9">
						<select class="form-control" name="inp[bk_room_id]" id="bk_room_id" required>
							<option value=""><?php echo getLang('please_choose_room') ?></option>
							<?php
								foreach ($room as $row){
									echo '<option value="'.$row->room_id.'">'.$row->room_name.' ('.$row->room_capacity.' '.getLang('people').')</option>';
								}
							?>
						</select>
                    </div>
                </div>    
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo getLang('mobile_phone'); ?></label>
					<div class="col-sm-9"><?php echo $this->session->userdata('phone') ?> </div>
                    <!-- <div class="col-sm-9 prepend-icon">
                        <input class="form-control" type="text" name="inp[bk_mobile_phone]"id="bk_mobile_phone" placeholder="<?php echo getLang('mobile_phone'); ?>" required>
                        <i class="fa fa-file"></i>
                    </div>-->
                </div> 
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo getLang('date'); ?> <span class="required-class">*) </span></label>
                     <div class="col-sm-3 prepend-icon">
                        <input class="form-control" type="text" name="date" id="date" readonly='true' value="<?php echo (strpos($holiday,date('Y-m-d'))?'':date('d-m-Y')) ?>" placeholder="<?php echo getLang('date'); ?>" required>
                        <i class="fa fa-calendar"></i>
                    </div>
                </div> 
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo getLang('start_hour'); ?> <span class="required-class">*) </span></label>
                     <div class="col-sm-2">
                        <input class="form-control time" type="text" name="starthour" id="starthour" placeholder="<?php echo getLang('start_hour'); ?>" required>
                    </div>
                </div> 

                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo getLang('duration'); ?> <span class="required-class">*) </span></label>
                    <div class="col-sm-9">
						<select class="form-control" name="duration" id="duration" required>
							<option value=""><?php echo getLang('please_choose_duration').' ('.getLang('choose_start_hour_first').')' ?></option> 
						</select>
                    </div>
                </div>  
				<!--
                <div class="form-group" id="timeOnlyExample">
                    <label class="col-sm-3 control-label"><?php echo getLang('hour'); ?> <span class="required-class">*) </span></label>
					<div class="col-sm-2"> 
						<input class="form-control time start col-sm-4" type="text" name="starthour" id="starthour" placeholder="<?php echo getLang('start_hour'); ?>" required> 
					</div>
					 <label class="col-sm-1 control-label"><?php echo getLang('to'); ?></label>
					 <div class="col-sm-2">  
						<input class="form-control time end col-sm-4" type="text" name="endhour" id="endhour" placeholder="<?php echo getLang('end_hour'); ?>" required>
					</div>
                </div> 
				-->
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo getLang('purpose'); ?> <span class="required-class">*) </span></label>
                    <div class="col-sm-9">
                        <textarea class="form-control" name="inp[bk_purpose]" id="bk_purpose" required></textarea>
                    </div>
                </div> 
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo getLang('member_name'); ?> <span class="required-class">*) </span></label>
					 <div class="col-sm-9">
						<input class="form-control member" type="text" name="member" id="member" placeholder="<?php echo getLang('member_name'); ?>" required>
					</div>
				</div>
                
            </div>
            <div class="modal-footer"> 
			    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
			    <button type="button" class="btn btn-danger" onclick="save()"><i class="fa fa-save"></i>&nbsp;&nbsp;<?php echo getLang("save") ?></button>
            </div>
            </form>
        </div>
    </div>
</div>
 
<div class="modal fade" id="modal_deactivate" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"><i class="fa fa-unlink"></i>&nbsp;&nbsp;<?php echo getLang("deactivate")?> <?php echo getCurrentMenuName() ?></h4></strong>
			</div>
			<form id="form" class="form-horizontal">
			<input type="hidden" name="id" id="id">
			<input type="hidden" name="inp[room_active]" id="room_active">
			<div class="modal-body">
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-success" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
			    <button type="button" class="btn btn-danger" onclick="deletes(1)"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;<?php echo getLang("ok") ?></button>
			</div>
			</form>
		</div>
	</div>
</div> 
 
<div class="modal fade" id="modal_activate" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-primary">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"><i class="fa fa-link"></i>&nbsp;&nbsp;<?php echo getLang("activate")?> <?php echo getCurrentMenuName() ?></h4></strong>
			</div>
			<form id="form" class="form-horizontal">
			<input type="hidden" name="id" id="id">
			<input type="hidden" name="inp[room_active]" id="room_active">
			<div class="modal-body">
			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
			    <button type="button" class="btn btn-success" onclick="deletes(0)"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;<?php echo getLang("ok") ?></button>
			</div>
			</form>
		</div>
	</div>
</div> 

<div class="modal fade" id="modal_calendar" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"></h4></strong>
			</div>
			<form id="form" class="form-horizontal">
			<input type="hidden" name="id" id="id">
			<input type="hidden" name="inp[room_active]" id="room_active">
			<div class="modal-body">
			</div>
			<div class="modal-footer"> 
			    <button type="button" class="btn btn-danger"  data-dismiss="modal"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp;<?php echo getLang("close") ?></button>
			</div>
			</form>
		</div>
	</div>
</div>  

<div id="ajaxFancyBox" style="display: none;"></div>
 
<div class="modal fade" id="modal_gallery" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-red">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icons-office-52"></i></button>
			    <strong><h4 class="modal-title"><i class="fa fa-link"></i>&nbsp;&nbsp;<?php echo getLang("activate")?> <?php echo getCurrentMenuName() ?></h4></strong>
			</div>
			<form id="form" class="form-horizontal">
			<input type="hidden" name="id" id="id">
			<input type="hidden" name="inp[room_active]" id="room_active">
			<div class="modal-body">
				<ul id="slippry-demo">
				   
				</ul>

			</div>
			<div class="modal-footer">
			    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("close") ?></button>
			</div>
			</form>
		</div>
	</div>
</div> 

<?php $this->load->view('theme_footer'); ?>

<script type="text/javascript"> 
var save_method; 
var table;
var form = $('#modal_form #form');
form.validate({       
	ignore: ""
}); 

$(document).ready(function(){ 
	  
	$('#room').select2().on("change", function(e) { 
		if (e.val=="") $('#calendar').fullCalendar('destroy');
		else calendar(e.val);
    });  
	<?php if(!strpos($holiday,date('Y-m-d'))){ ?>
		var d = new Date(); 
		var n = d.getDay();
		if( n == 6 ){
			weekend();
		}
		else {
			// weekday();
			weekday_ramadhan();
		} 
	
	<?php } ?>
	var disablethese = [<?php echo $holiday ?>];
	 
	$('#date').datepicker({ 
		minDate:new Date(),
		dateFormat : 'dd-mm-yy',
		onSelect: function (date) {
			parts = date.split('-'),
			year = parseInt(parts[2], 10),
			month = parseInt(parts[1], 10) - 1, // NB: month is zero-based!
			day = parseInt(parts[0], 10),
			date = new Date(year, month, day);
			$('#starthour').timepicker('remove');
			$('#starthour').val('');
			$("#modal_form #duration").select2("destroy");  
			$("#modal_form #duration").select2(); 		
			$("#modal_form #duration").html('<option value=""><?php echo getLang('please_choose_duration').' ('.getLang('choose_start_hour_first').')' ?></option>');
			$("#modal_form #duration").select2("val",""); 		
			if( date.getDay() == 6 ){
				weekend();
			}
			else {
				// weekday();
				weekday_ramadhan();
			}   
		},
		beforeShowDay: function(date) {
			var string = jQuery.datepicker.formatDate('yy-mm-dd', date);
			return [ disablethese.indexOf(string) == -1 ]
		}
	});  
	
	$('#date_choose').datepicker({ 
		dateFormat : 'dd-mm-yy', 
	});  
	
    $('.time').each(function() {
        $(this).rules('add', {
            time: true,
            messages: {
                time: "Please enter a valid time"
            }
        });
    });	  
	
	
	$("#schedule").timeSchedule({
		startTime: "08:00", // schedule start time(HH:ii)
        endTime: "21:00",   // schedule end time(HH:ii)
        widthTime:60 * 10,  // cell timestamp example 10 minutes
        timeLineY:70,       // height(px)
        verticalScrollbar:20,   // scrollbar (px)
        dataWidth:150,		// data width
        timeLineBorder:1,   // border(top and bottom)
        debug:"#debug",     // debug string output elements
		rows : {<?php echo $schedule ?>},
		// change: function(node,data){
			// console.log(data.data.id);
		// },
		init_data: function(node,data){
		},
		click: function(node,data){ 
			$.ajax({
				url : 'index.php/booking/detail',
				type: "POST",
				data: {
					id : data.data.id
				},
				dataType: "JSON",
				beforeSend : function() {
					showLoading();
				},
				complete : function() {
					hideLoading();
				},
				success: function(data){
					$('#modal_calendar .modal-title').html('<i class="fa fa-calendar"></i>&nbsp;&nbsp;'+data.header);
					$('#modal_calendar .modal-body').html(data.desc);
					$('#modal_calendar').modal({keyboard: false, backdrop: 'static'});
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
					//info_alert('warning','<?php echo getLang("system error")?>');
				}
			});
		},
		// append: function(node,data){
		// },
		// time_click: function(time,data){
			// console.log(data.data.id);
		// },
	});
	
	$('.nailthumb-container').nailthumb({width:100,height:100});
	
	
});	  



function weekday_ramadhan(){  
  if($('#bk_room_id').val()==10){ 
    $('#starthour').timepicker({ 
      'minTime': '08:00',
      'maxTime': '13:00',
      'timeFormat': 'H:i',
      'disableTextInput': true
    }).change(function(e){  	  
      $("#duration").html('<option value=""><?php echo getLang('please_choose_duration').' ('.getLang('choose_start_hour_first').')' ?></option><option value="180">180 menit (3 Jam)'); 
      $("#modal_form #duration").select2("val","");  
    });	
  }
  else if($('#bk_room_id').val()==11 || $('#bk_room_id').val()==12 || $('#bk_room_id').val()==14 || $('#bk_room_id').val()==15){ 
    $('#starthour').timepicker({
      'minTime': '08:00',
      'maxTime': '14:00',
      'timeFormat': 'H:i',
      'disableTextInput': true
    }).change(function(e){  	  
      $("#duration").html('<option value=""><?php echo getLang('please_choose_duration').' ('.getLang('choose_start_hour_first').')' ?></option><option value="120">120 menit (2 Jam)'); 
      $("#modal_form #duration").select2("val","");  
    });	
  }
  else {
    $('#starthour').timepicker({
      'minTime': '08:00',
      'maxTime': '15:30',
      'timeFormat': 'H:i', 
      'disableTextInput': true
    }).change(function(e){  	 
      if ($(this).val()=='15:30') 
        $("#duration").html('<option value=""><?php echo getLang('please_choose_duration').' ('.getLang('choose_start_hour_first').')' ?></option><option value="30">30 <?php echo getLang('minutes') ?>');
      else if ($(this).val()=='15:00') 
        $("#duration").html('<option value=""><?php echo getLang('please_choose_duration').' ('.getLang('choose_start_hour_first').')' ?></option><option value="30">30 <?php echo getLang('minutes') ?></option><option value="60">60 <?php echo getLang('minutes') ?></option>');
      else if ($(this).val()=='14:30') 
        $("#duration").html('<option value=""><?php echo getLang('please_choose_duration').' ('.getLang('choose_start_hour_first').')' ?></option><option value="30">30 <?php echo getLang('minutes') ?></option><option value="60">60 <?php echo getLang('minutes') ?></option><option value="90">90 <?php echo getLang('minutes') ?></option>');
      else $("#duration").html('<option value=""><?php echo getLang('please_choose_duration').' ('.getLang('choose_start_hour_first').')' ?></option><option value="30">30 <?php echo getLang('minutes') ?></option><option value="60">60 <?php echo getLang('minutes') ?></option><option value="90">90 <?php echo getLang('minutes') ?></option><option value="120">120 <?php echo getLang('minutes') ?></option> ');	 
      $("#modal_form #duration").select2("val","");  
    });	
  }
}

function weekend(){
	$('#starthour').timepicker({
		'minTime': '08:00',
		'maxTime': '12:00',
		'timeFormat': 'H:i',
		'disableTextInput': true
	}).change(function(e){
		if ($(this).val()=='12:00') 
			$("#duration").html('<option value=""><?php echo getLang('please_choose_duration').' ('.getLang('choose_start_hour_first').')' ?></option><option value="30">30 <?php echo getLang('minutes') ?></option> ');
		else if ($(this).val()=='11:30') 
			$("#duration").html('<option value=""><?php echo getLang('please_choose_duration').' ('.getLang('choose_start_hour_first').')' ?></option><option value="30">30 <?php echo getLang('minutes') ?></option><option value="60">60 <?php echo getLang('minutes') ?></option>');
		else if ($(this).val()=='11:00') 
			$("#duration").html('<option value=""><?php echo getLang('please_choose_duration').' ('.getLang('choose_start_hour_first').')' ?></option><option value="30">30 <?php echo getLang('minutes') ?></option><option value="60">60 <?php echo getLang('minutes') ?></option><option value="90">90 <?php echo getLang('minutes') ?></option>');
		else $("#duration").html('<option value=""><?php echo getLang('please_choose_duration').' ('.getLang('choose_start_hour_first').')' ?></option><option value="30">30 <?php echo getLang('minutes') ?></option><option value="60">60 <?php echo getLang('minutes') ?></option><option value="90">90 <?php echo getLang('minutes') ?></option><option value="120">120 <?php echo getLang('minutes') ?></option> ');	 
		$("#modal_form #duration").select2("val",""); 
		 
	});	
} 



function weekday(){  
	if($('#bk_room_id').val()==10){ 
		$('#starthour').timepicker({ 
			'minTime': '08:00',
			'maxTime': '16:00',
			'timeFormat': 'H:i',
			'disableTextInput': true
		}).change(function(e){  	  
			$("#duration").html('<option value=""><?php echo getLang('please_choose_duration').' ('.getLang('choose_start_hour_first').')' ?></option><option value="180">180 menit (3 Jam)'); 
			$("#modal_form #duration").select2("val","");  
		});	
	}
	else if($('#bk_room_id').val()==11 || $('#bk_room_id').val()==12 || $('#bk_room_id').val()==14 || $('#bk_room_id').val()==15){ 
		$('#starthour').timepicker({
			'minTime': '08:00',
			'maxTime': '17:00',
			'timeFormat': 'H:i',
			'disableTextInput': true
		}).change(function(e){  	  
			$("#duration").html('<option value=""><?php echo getLang('please_choose_duration').' ('.getLang('choose_start_hour_first').')' ?></option><option value="120">120 menit (2 Jam)'); 
			$("#modal_form #duration").select2("val","");  
		});	
	}
	else {
		$('#starthour').timepicker({
			'minTime': '08:00',
			'maxTime': '18:30',
			'timeFormat': 'H:i', 
			'disableTextInput': true
		}).change(function(e){  	 
			if ($(this).val()=='18:30') 
				$("#duration").html('<option value=""><?php echo getLang('please_choose_duration').' ('.getLang('choose_start_hour_first').')' ?></option><option value="30">30 <?php echo getLang('minutes') ?>');
			else if ($(this).val()=='18:00') 
				$("#duration").html('<option value=""><?php echo getLang('please_choose_duration').' ('.getLang('choose_start_hour_first').')' ?></option><option value="30">30 <?php echo getLang('minutes') ?></option><option value="60">60 <?php echo getLang('minutes') ?></option>');
			else if ($(this).val()=='17:30') 
				$("#duration").html('<option value=""><?php echo getLang('please_choose_duration').' ('.getLang('choose_start_hour_first').')' ?></option><option value="30">30 <?php echo getLang('minutes') ?></option><option value="60">60 <?php echo getLang('minutes') ?></option><option value="90">90 <?php echo getLang('minutes') ?></option>');
			else $("#duration").html('<option value=""><?php echo getLang('please_choose_duration').' ('.getLang('choose_start_hour_first').')' ?></option><option value="30">30 <?php echo getLang('minutes') ?></option><option value="60">60 <?php echo getLang('minutes') ?></option><option value="90">90 <?php echo getLang('minutes') ?></option><option value="120">120 <?php echo getLang('minutes') ?></option> ');	 
			$("#modal_form #duration").select2("val","");  
		});	
	}
}  

function add() {
	save_method = 'add';
	
	reset();  	
	$('#modal_form').modal({keyboard: false, backdrop: 'static'});
	$('#modal_form .modal-title').html('<i class="fa fa-plus-square"></i>&nbsp;&nbsp;<?php echo getCurrentMenuName() ?>');   

	$('#modal_form #member').tokenInput("index.php/booking/member", {
		minChars: 3,
		//tokenLimit: 1,
		preventDuplicates: true,
		onDelete: function (item) { 	
		},
		onAdd: function (item) {
			
		}, theme: "facebook"
	});
} 


function gallery(id) { 

	$("#ajaxFancyBox").load("index.php/booking/gallery/"+id, function() {                    
		$(".fancybox_gallery").fancybox({
				openEffect  : 'none',
				closeEffect : 'none', 
				prevEffect : 'none',
				nextEffect : 'none', 
				closeBtn  : false, 
				helpers : {
					title : {
						type : 'inside'
					},
					buttons	: {}
				}, 
				afterLoad : function() {
					this.title = 'Image ' + (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title : '');
				}
			});//fanxybox
		$(".fancybox_gallery").first().trigger('click');
	});
	
	// $.ajax({
		// url : 'index.php/booking/gallery',
		// type: "POST",
		// data: {
			// id:id
		// },
		// dataType: "html",
		// beforeSend : function() {
			// showLoading();
		// },
		// complete : function() {
			// hideLoading();
		// },
		// async : false,
		// success: function(data){ 
			// $.fancybox.open({
				// helpers : {
					// title : {
						// type : 'inside'
					// },
					// buttons	: {}
				// },
				// beforeLoad: function () {
					// this.group.push(data); // push 
				// } // beforeLoad
			// });
		// },
		// error: function (jqXHR, textStatus, errorThrown)
		// {
			// info_alert('warning','<?php echo getLang("error_xhr")?>');
		// }
	// }); 
} 

function save(){
	var url;
	if(save_method == 'add'){
		url = "<?php echo site_url('index.php/booking/insert')?>";
	}else{
		url = "<?php echo site_url('index.php/booking/update')?>";
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
				if (data.status){
					$('#modal_form').modal('hide');    
					info_alert('success','Permintaan pemesanan ruangan berhasil. Anda akan dikonfirmasi jika telah diproses.');
					window.location.href='';
				}
				else info_alert('warning',data.error);
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				info_alert('warning','<?php echo getLang("error_xhr")?>');
			}
		});
	}  
}   

function reset() {
	$('#modal_form .token-input-dropdown-facebook').remove();    
	$('#modal_form .token-input-list-facebook').remove();
    form.validate().resetForm();   
	$("#modal_form #form :input").removeData("previousValue");	//remove remote jquery validate previous value  
    form[0].reset();            
	$("#modal_form #bk_room_id").select2("val", "");
    $("label.error").hide();
    $(".error").removeClass("error");
} 

</script>