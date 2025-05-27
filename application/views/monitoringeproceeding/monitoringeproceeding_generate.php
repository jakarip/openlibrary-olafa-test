
<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong></h3>
		</div>
		<div class="panel-content pagination2">
			<form id="form" class="form-horizontal" action="" method="post"> 
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo getLang("choose_eproceeding_edition") ?></label>
					<div class="col-sm-9">
						<select class="form-control" name="choose">
							<option value="0"><?php echo getLang("choose_eproceeding_edition") ?></option>
							<?php foreach ($edition as $row){
							$mulai = explode('-',$row->datestart);
							$mulai = $mulai[2].'-'.$mulai[1].'-'.$mulai[0];
							$finish = explode('-',$row->datefinish);
							$finish = $finish[2].'-'.$finish[1].'-'.$finish[0];
							?>
								<option value="<?php echo $row->eproc_edition_id ?>" <?php echo (($row->eproc_edition_id==$choose)?'selected':'')?>><?php echo $row->nama.' ('.$mulai.' s/d '.$finish.')' ?></option> 
							<?php } ?> 
						</select>
					</div> 
				</div> 
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo getLang("choose_eproceeding_list") ?></label>
					<div class="col-sm-9"> 
						<select class="form-control" name="list">
							<option value="0"><?php echo getLang("choose_eproceeding_list") ?></option>
							<option value="engineering" <?php echo (($list=='engineering')?'selected':'')?>>eProceedings of Engineering</option>
							<option value="science" <?php echo (($list=='science')?'selected':'')?>>eProceedings of Applied Science</option>
							<option value="management" <?php echo (($list=='management')?'selected':'')?>>eProceedings of Management</option>
							<option value="design" <?php echo (($list=='design')?'selected':'')?>>e-Proceeding of Art & Design</option>
						</select>
					</div> 
				</div> 
				<div class="form-group">
					<label class="col-sm-3 control-label"></label>
					<div class="col-sm-9"> 
						<button   type="submit" value="submit" name="submit" class="btn btn-success btn-embossed">Generate</button>
					</div> 
				</div> 
			</form> 
			<div class="x_content">
					<?php 
					if (!empty($html)){
						echo "total artikel: $total<br>file jurnal : $jurnal<br>file eproc : $jurnal_eproc<br><br>";
						
						//echo '<button type="button" class="btn btn-primary copyhtml btn-embossed" style="background-color:#1A82C3;color:#fff;" onclick="SelectText(\'selectAll\')">Select All</button>
						//<pre id="selectAll">';
						//echo htmlspecialchars($html);
						//echo '</pre>';
						
						
						echo $html;
					} 
					?>
				</div>
			</div>
					 
		</div>
	</div>
</div> 	  					
					 

<?php $this->load->view('theme_footer'); ?>
					
<script>
function SelectText(element) {
    var doc = document
        , text = doc.getElementById(element)
        , range, selection
    ;    
    if (doc.body.createTextRange) {
        range = document.body.createTextRange();
        range.moveToElementText(text);
        range.select();
    } else if (window.getSelection) {
        selection = window.getSelection();        
        range = document.createRange();
        range.selectNodeContents(text);
        selection.removeAllRanges();
        selection.addRange(range);
    }
} 


$(".selectAllPDF").click(function(){ 
	var id = $(this).val(); 
	if ($(this).is(':checked')) { 
		$(".checkPDF"+id).prop('checked', true);
	}
	else $(".checkPDF"+id).prop('checked', false); 
});

$(".selectAllWord").click(function(){ 
	var id = $(this).val(); 
	if ($(this).is(':checked')) { 
		$(".checkWord"+id).prop('checked', true);
	}
	else $(".checkWord"+id).prop('checked', false); 
});

$(".selectAllPDFno").click(function(){ 
	var id = $(this).val(); 
	if ($(this).is(':checked')) { 
		$(".checkPDFno"+id).prop('checked', true);
	}
	else $(".checkPDFno"+id).prop('checked', false); 
});

$(".selectAllWordno").click(function(){ 
	var id = $(this).val(); 
	if ($(this).is(':checked')) { 
		$(".checkWordno"+id).prop('checked', true);
	}
	else $(".checkWordno"+id).prop('checked', false); 
});

			 
// $("#selectAll").click(function(){
//         $("input[type=checkbox]").prop('checked', $(this).prop('checked')); 
// });

$(".downloadPDF").click(function(){ 
	var id = $(this).data('id');
	$("input:checkbox[name=downloadPDF"+id+"]:checked").each(function() { 
		window.open('https://openlibrary.telkomuniversity.ac.id/open/index.php/download2/flippingbook_url_download_bypass/'+$(this).val());
	});
});

$(".downloadWord").click(function(){ 
	var id = $(this).data('id');
	$("input:checkbox[name=downloadWord"+id+"]:checked").each(function() { 
		window.open('https://openlibrary.telkomuniversity.ac.id/open/index.php/download2/flippingbook_url_download_bypass/'+$(this).val());
	});
});

$(".downloadPDFno").click(function(){ 
	var id = $(this).data('id');
	$("input:checkbox[name=downloadPDFno"+id+"]:checked").each(function() { 
		window.open('https://openlibrary.telkomuniversity.ac.id/open/index.php/download2/flippingbook_url_download_bypass/'+$(this).val());
	});
});

$(".downloadWordno").click(function(){ 
	var id = $(this).data('id');
	$("input:checkbox[name=downloadWordno"+id+"]:checked").each(function() { 
		window.open('https://openlibrary.telkomuniversity.ac.id/open/index.php/download2/flippingbook_url_download_bypass/'+$(this).val());
	});
});



</script>
					