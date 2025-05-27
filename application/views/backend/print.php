<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
	<base href="<?= base_url() ?>" />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="admin-themes-lab">
    <meta name="author" content="themes-lab">
    <!--<link rel="shortcut icon" href="tools/assets/global/images/favicon.png" type="image/png">-->
    <title>Azzurra</title>
	
<style>
	@page {
		size: auto;
		margin: 0;
	  }
	body {
		font-family : calibri;
		font-size : 12px;
	}
	
	table{
		border-collapse:collapse;
	}
	th {
		font-size : 12px;
		padding: 3px 10px;
	}
	td {
		text-align:center;
		padding: 3px 10px;
		font-size : 12px;
	}
	.logos {
		font-weight:bold;
	}
	
	.left {
		text-align:left;
	}
	
	.right {
		text-align:right;
	}
</style>
</head>

<body>
<button style="cursor:pointer;margin-bottom : 50px;" type="button" class="btn btn-success" onclick="printDiv('printableArea')">Print</button>
<div id="printableArea" width="100%;">
<?php 
echo $table;
?>
</div>
<script>
	function printDiv(divName) {
		 var printContents = document.getElementById(divName).innerHTML;
		 var originalContents = document.body.innerHTML;

		 document.body.innerHTML = printContents; 
		 window.print(); 
		 document.body.innerHTML = originalContents;
	}
</script>

</body>