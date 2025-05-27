<!DOCTYPE html>
<html>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
	<base href="<?php echo base_url() ?>" />
	<meta charset="utf-8">
	<title>OLAFA - Telkom University Open Library</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta content="" name="description" />
	<meta content="themes-lab" name="author" />
	<!--<link rel="shortcut icon" href="tools/assets/global/images/favicon.png">-->
	<link href="tools/assets/global/css/style.css" rel="stylesheet">
	<link href="tools/assets/global/css/ui.css" rel="stylesheet">
	<link href="tools/assets/global/css/custom.css" rel="stylesheet">
	<link href="tools/assets/global/plugins/bootstrap-loading/lada.min.css" rel="stylesheet">
</head>
<body class="account separate-inputs boxed" data-page="login">
	<!-- BEGIN LOGIN BOX -->
	<div class="container" id="login-block">
		<div class="row">
			<div class="col-sm-6 col-md-4 col-md-offset-4">
				<div class="account-wall">
					<i class="user-img icons-faces-users-03"></i>
					<form class="form-signin" action="index.php/login/loginProcess" role="form" method="post">
						<?php 
							if ($this->session->flashdata('error')) { ?>
								<div id="login-error"><?php echo $this->session->flashdata('error') ?> </div>
						<?php } ?>
						<div class="append-icon">
							<input type="text" name="username" id="username" class="form-control username" placeholder="Username" required>
							<i class="icon-user"></i>
						</div>
						<div class="append-icon m-b-20">
							<input type="password" name="password" name="password" class="form-control password" placeholder="Password" required>
							<i class="icon-lock"></i>
						</div>
						<button type="submit" id="submit-form" class="btn btn-lg btn-danger btn-block ladda-button" data-style="expand-left">Sign In</button>
					</form>
				</div>
			</div>
		</div>
		<p class="account-copyright">
			<span>Copyright <span class="copyright">©</span> 2017 </span><span>Telkom University - Open Library</span>. <span>All rights reserved.</span>
		</p>
	</div>
	<script src="tools/assets/global/plugins/jquery/jquery-1.11.1.min.js"></script>
	<script src="tools/assets/global/plugins/jquery/jquery-migrate-1.2.1.min.js"></script>
	<script src="tools/assets/global/plugins/bootstrap/js/bootstrap.min.js"></script>
	<script src="tools/assets/global/plugins/backstretch/backstretch.min.js"></script>
	<script src="tools/assets/global/plugins/bootstrap-loading/lada.min.js"></script>
	<script src="tools/assets/global/plugins/jquery-validation/jquery.validate.min.js"></script>
	<script src="tools/assets/global/plugins/jquery-validation/additional-methods.min.js"></script>
</body>
</html>
<script type="text/javascript">
$(document).ready(function(){ 
	var form = $('.form-signin');
	form.validate();
	
	$("#login-error").show();
	$('#submit-form').click(function(e) {
		e.preventDefault();
		if (form.valid()) {
			$('.form-signin').submit();
			// $(this).addClass('ladda-button');
			// var l = Ladda.create(this);
			// $.ajax({
				// dataType	: "json",
				// url			: 'index.php/login/loginprocess',
				// type		: 'post',
				// data		: form.serialize(),
				// beforeSend	: function(){
					// l.start();
				// },
				// complete	: function(){
					// l.stop();
				// },
				// success		: function(e) { 
					// if(e.status=='success') window.location.href =e.url;
					// else {
						//$("#login-error").show();
						// $("#login-error").html(e.info);
					// }
				// }
			// });
		}
	});
	
	$.backstretch(["tools/assets/global/images/gallery/open_discussion_room.jpg"],
	{
		fade: 600,
		duration: 4000
	});
});
</script>
<script>
  window.chatbaseConfig = {
    chatbotId: "FSDsJNmiyeqaQOwBPXwY5",
  }
</script> 