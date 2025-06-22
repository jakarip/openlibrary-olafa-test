<!-- Blog Posts -->
<div class="blog-single-post">
	 
	<!-- post title -->
	<h2 class="blog-single-title">Register</h2>
	<!-- /post title -->							
	<!-- post content -->
	<form id="register-form" method="post" class="form register-form">
		<div class="form-group" id="status" style="display:none">
				
		</div>
		<div class="form-group">
			<label for="ticket-name" class="col-sm-3 control-label">Name</label>
			<div class="col-sm-9">
				<input class="form-control" id="name" name="name" placeholder="Name" type="text" required>
			</div>
		</div>
		<div class="form-group">
			<label for="ticket-email" class="col-sm-3 control-label">Email</label>
			<div class="col-sm-9">
				<input class="form-control" id="email" name="email" placeholder="Email" type="email" required>
			</div>
		</div>
		<div class="form-group">
			<label for="ticket-email" class="col-sm-3 control-label">Phone</label>
			<div class="col-sm-9">
				<input class="form-control" id="phone" name="phone" placeholder="Phone" type="text" required>
			</div>
		</div>
		<div class="form-group">
			<label for="ticket-priority" class="col-sm-3 control-label">Type User</label>
			<div class="col-sm-9">
				<select id="type" name="type"  class="form-control" required>
					<option value="mahasiswa">Mahasiswa</option>
					<option value="umum">Umum</option>
				</select> 
			</div>
		</div>
		<div class="form-group">
			<label for="ticket-priority" class="col-sm-3 control-label">Event</label>
			<div class="col-sm-9"> 
				<?php
					foreach($event as $ev){
						echo '<input type="checkbox" name="event[]" value="'.$ev->tulw_ev_id.'"> &nbsp;'.$ev->tulw_ev_name.'<br>';
					}
				?>
			</div>
		</div>
		<!--
		<div class="form-group">
			&nbsp;
		</div>
		<div class="form-group">
			<label for="ticket-priority" class="col-sm-3 control-label">Recaptcha</label>
			<div class="col-sm-9"> 
				<div class="g-recaptcha" data-sitekey="6LcnuiITAAAAAO_Y4TTu9nJ32Tw_JkEmaQMQEr2m"></div>
			</div>
		</div>
		-->
		<div class="form-group">
			&nbsp;
		</div>
		<div class="form-group">
			<label for="ticket-priority" class="col-sm-3 control-label"></label>
			<div class="col-sm-4"> 
				<input type="button" id="cancel" class="btn btn-default btn-form" name="cancel" value="Cancel"/>
			</div>
			<div class="col-sm-4"> 
				<input type="submit" class="btn-true btn-default btn-form fancybox-item" id="submit" name="submit" value="SEND"/>
			</div>
		</div> 
	</form>
	<!-- /post content -->
</div>			
<!-- /Blog Posts -->	
 
<script src='https://www.google.com/recaptcha/api.js'></script>
<script type="text/javascript">

$(document).ready(function() {
	$("#cancel").click(function(){
		$(".fancybox-close").trigger('click');
	});
	
	$("#register-form").submit(function(event){
		$.ajax({
			type : "POST",
			url: "<?php echo base_url()?>index.php/tulw/reg_process",
			dataType:'html',
			data : $("#register-form").serialize(),
			success: function(result){ 
				
				var status = result.split(', ');
				 
				if(status[0]=='success') {
					$("#status").show().delay(1000).fadeOut('fast', function() {
						$(".fancybox-close").trigger('click');
					});
					$("#status").html('<label class="col-sm-3 control-label"></label><div class="col-sm-9"><div style="padding:3px;background-color:#2ac500;"><h4 style="color:#fff"><i class="fa fa-check-circle"></i> Success!</h4></div>');
					
				}
				else {
					$("#status").show().delay(2000).fadeOut();
					$("#status").html('<label class="col-sm-3 control-label"></label><div class="col-sm-9"><div style="padding:3px;background-color:#ed1d24;"><h4 style="color:#fff"><i class="fa fa-times-circle"></i> Error! '+ status[1]+'</h4></div>');
				}
			}
		}); 
		event.preventDefault();
	});
});
</script>