
 
<?php $this->load->view('frontend/tpl_footer'); ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="assets/real3d/deploy/css/flipbook.style.css"> 
<script type="text/javascript" src="assets/limitless/global/js/plugins/pickers/datepicker.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/styling/uniform.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>

<script type="text/javascript"  src="assets/real3d/deploy/js/flipbook.min.js"></script>
<script type="text/javascript"> 
    $(document).ready(function () {
		
		<?php if($readonly==1){ ?>
        $("#container").flipBook({
            pdfUrl: "/open/index.php/download/<?=($download==1?'flippingbook_url_download':'flippingbook_url') ?>/<?=$var?>",
			skin:"dark",
			btnSize:20,
			backgroundColor:"#666",
			zoomMin:0.9,  
			btnShare : {enabled:false},
			btnDownloadPages : {enabled:false},
			<?php if($download!=1){ ?>
			btnDownloadPdf : {enabled:false }, 
			<?php }
			else {			
			?> 
			btnDownloadPdf : {enabled:true}, 
			<?php } ?>
			btnPrint:{enabled:false}, 
        });
		<?php }else if($readonly==0 and $download==0) { ?>
			alert('jenis keanggotaan anda tidak diperbolehkan men-download dokumen ini');
			window.location.href='/';
		<?php } ?>

    })
</script>
<div class="panel panel-default flat">
    <div id="container"/>
</div>