<style>
.ui-datepicker-calendar {
    display: none;
    }
</style>
<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong></h3>
		</div>
		<div class="panel-content pagination2">
			<form id="form" class="form-horizontal" action="" method="post"> 
				<div class="form-group">
					<label class="col-sm-2 control-label"><?php echo getLang('choose_month_and_year')?></label>
					<div class="col-sm-8 prepend-icon"> 
						 <input type="text" name="month" value="<?php echo (empty($month)?'':$month)?>" class="form-control has-feedback-left" id="single_cal1" placeholder="<?php echo getLang('choose_month_and_year')?>" aria-describedby="inputSuccess2Status"><i class="fa fa-calendar"></i>
					</div> 
					<label class="col-sm-1 control-label"><button type="submit" value="submit" name="submit" id="submit" class="btn btn-success">Report</button></label>
					<label class="col-sm-1 control-label export" style="display:none"><button   type="button" value="Excel" name="pdf" class="btn btn-primary" id="pdf">PDF</button></label>
				</div>  
			</form> 
			 <div class="x_content">
				<?php $this->load->view('katalog/ecatalog_content') ?>
			</div>
		</div>
	</div>
</div> 	
<?php $this->load->view('theme_footer'); ?>					
<script type="text/javascript">
        $(document).ready(function () {
           
			$("#single_cal1").datepicker( { 
				dateFormat: "mm-yy",
				changeMonth: true,
				changeYear: true,
				showButtonPanel: true, 
				onClose: function() {
					var iMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
					var iYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
					$(this).datepicker('setDate', new Date(iYear, iMonth, 1));
				}, 
				beforeShow: function() {
				   if ((selDate = $(this).val()).length > 0) 
				   { 
					  iYear = selDate.substring(selDate.length - 4, selDate.length);
					  iMonth = selDate.substring(0, 2)-1;
					  $(this).datepicker('option', 'defaultDate', new Date(iYear, iMonth, 1));
					   $(this).datepicker('setDate', new Date(iYear, iMonth, 1));
				   }
				}
			});
			
			$("#pdf").click(function() {
				if  ($('#single_cal1').val()!="")
					window.location.href="index.php/katalog/ecatalog_pdf/"+$('#single_cal1').val();
				else alert ("Silahkan Pilih Bulan & Tahun");
			}); 
			
			if($('#single_cal1').val()!=""){
				$(".export").show();
			}
		});
    </script>