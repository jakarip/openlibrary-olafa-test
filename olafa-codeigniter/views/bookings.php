<style>

	#script-warning {
		display: none;
		background: #eee;
		border-bottom: 1px solid #ddd;
		padding: 0 10px;
		line-height: 40px;
		text-align: center;
		font-weight: bold;
		font-size: 12px;
		color: red;
	}

	#loading {
		display: none;
		position: absolute;
		text-align:center;
		width:100%;
		text-align:center;
		font-weight:bold;
		font-size:14px;
	}
	.fc-event,.fc-list-item{
		cursor: pointer;
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
					<div class="col-lg-2" ><label><?php echo getLang('please_choose_room'); ?></label></div>
					<div class="col-lg-4" >
						<select class="form-control" name="room" id="room">
							<option value=""><?php echo getLang('please_choose_room') ?></option>
							<?php
								foreach ($room as $row){
									echo '<option value="'.$row->room_id.'"  '.($row->room_id==$id?'selected':'').'>'.$row->room_name.' ('.$row->room_capacity.' '.getLang('people').')</option>';
								}
							?>
						</select>
					</div>  
				</div>
				<div id='loading'>loading...</div>
				<br> 
				<div id='calendar'></div>
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
                    <label class="col-sm-3 control-label"><?php echo getLang('mobile_phone'); ?> <span class="required-class">*) </span></label>
                     <div class="col-sm-9 prepend-icon">
                        <input class="form-control" type="text" name="inp[bk_mobile_phone]"id="bk_mobile_phone" placeholder="<?php echo getLang('mobile_phone'); ?>" required>
                        <i class="fa fa-file"></i>
                    </div>
                </div> 
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo getLang('date'); ?> <span class="required-class">*) </span></label>
                     <div class="col-sm-3 prepend-icon">
                        <input class="form-control" type="text" name="date" id="date" readonly='true' value="<?php echo date('d-m-Y') ?>" placeholder="<?php echo getLang('date'); ?>" required>
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
			    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-power-off"></i>&nbsp;&nbsp;<?php echo getLang("cancel") ?></button>
			    <button type="button" class="btn btn-success" onclick="save()"><i class="fa fa-save"></i>&nbsp;&nbsp;<?php echo getLang("save") ?></button>
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
	
	var d = new Date(); 
	var n = d.getDay();
	if( n == 6 ){
		weekend();
	}
	else {
		weekday();
	}
	
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
				weekday();
			}
			
			// $.ajax({
				// url : "<?php echo site_url('index.php/room/booking/checkHour')?>",
				// type: "POST",
				// data : {
					// id : 'aa'
				// },
				// dataType: "JSON",
				// beforeSend : function() {
					// showLoading();
				// },
				// complete : function() {
					// hideLoading();
				// },
				// success: function(data){
				// },
				// error: function (jqXHR, textStatus, errorThrown){
					// alert('Error get data from ajax');
				// }
			// });
			
			
			
			
		},
		beforeShowDay: function(date) {
			var string = jQuery.datepicker.formatDate('yy-mm-dd', date);
			return [ disablethese.indexOf(string) == -1 ]
		}
	});  
	
	
	
	
    $('.time').each(function() {
        $(this).rules('add', {
            time: true,
            messages: {
                time: "Please enter a valid time"
            }
        });
    });	  
});	 	
  
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
	$('#starthour').timepicker({
		'minTime': '08:00',
		'maxTime': '19:00',
		'timeFormat': 'H:i',
		'disableTextInput': true
	}).change(function(e){  	 
		if ($(this).val()=='19:00') 
			$("#duration").html('<option value=""><?php echo getLang('please_choose_duration').' ('.getLang('choose_start_hour_first').')' ?></option><option value="30">30 <?php echo getLang('minutes') ?></option> ');
		else if ($(this).val()=='18:30') 
			$("#duration").html('<option value=""><?php echo getLang('please_choose_duration').' ('.getLang('choose_start_hour_first').')' ?></option><option value="30">30 <?php echo getLang('minutes') ?></option><option value="60">60 <?php echo getLang('minutes') ?></option>');
		else if ($(this).val()=='18:00') 
			$("#duration").html('<option value=""><?php echo getLang('please_choose_duration').' ('.getLang('choose_start_hour_first').')' ?></option><option value="30">30 <?php echo getLang('minutes') ?></option><option value="60">60 <?php echo getLang('minutes') ?></option><option value="90">90 <?php echo getLang('minutes') ?></option>');
		else $("#duration").html('<option value=""><?php echo getLang('please_choose_duration').' ('.getLang('choose_start_hour_first').')' ?></option><option value="30">30 <?php echo getLang('minutes') ?></option><option value="60">60 <?php echo getLang('minutes') ?></option><option value="90">90 <?php echo getLang('minutes') ?></option><option value="120">120 <?php echo getLang('minutes') ?></option> ');	 
		$("#modal_form #duration").select2("val",""); 
		 
	});	
} 
function calendar(id=""){
	$('#calendar').fullCalendar('destroy');
	$('#calendar').fullCalendar({
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay,listWeek'
		},
		defaultDate: '<?php echo date('Y-m-d') ?>',
		editable: true,
		navLinks: true, // can click day/week names to navigate views
		eventLimit: true, // allow "more" link when too many events
		slotLabelFormat:"HH:mm",
		events: {
			url: 'index.php/booking/json_event',
			error: function() {
				$('#script-warning').show();
			},
			data : {
				id : id
			}
		},
		loading: function(bool) {
			$('#loading').toggle(bool);
		},
		timeFormat: 'H:mm',
		height: 450,
		displayEventTime : true,
		displayEventEnd: true,
		eventDurationEditable :false,
		eventStartEditable :false,
		eventClick:  function(event, jsEvent, view) {
			console.log(event); 
            $('#modal_calendar .modal-title').html('<i class="fa fa-calendar"></i>&nbsp;&nbsp;'+event.header);
            $('#modal_calendar .modal-body').html(event.description);
			$('#modal_calendar').modal({keyboard: false, backdrop: 'static'});
        },
		locale : '<?php echo ($this->session->userdata('language')=='ina'?'id':'en'); ?>',
		dayRender: function (date, cell) {

			//untuk mengganti background-color tanggal tertentu
			
			// var date = new Date(date);
			// var day = date.getDate().toString();

			// if (day.length == 1) //FIX DAY FORMAT ID 2 = 02 TO MATCH DB ARRAY
				// day = 0 + day;

			// var year = date.getFullYear(); //GET YEAR

			// var month = (date.getMonth() + 1).toString();
			// if (month.length == 1)
				// month = 0 + month;


			// var dateStr = year + "-" + month + "-" + day;
			
			// var dbDates = [<?php echo $holiday ?>]


			// for (var i = 0; i < dbDates.length; i++) {

				// if (dateStr == dbDates[i].toString()) {
						// cell.css("background-color", "#b80005");
					// } 

			// }

		}
		
	});
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
					
					$('#calendar').fullCalendar('destroy');
					calendar($("#modal_form #bk_room_id").select2("val"));
					$("#room").select2("val",$("#modal_form #bk_room_id").select2("val"));
					
					info_alert('success','Permintaan pemesanan ruangan berhasil. Anda akan dikonfirmasi jika telah diproses.');
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