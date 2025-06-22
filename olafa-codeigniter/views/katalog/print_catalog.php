<!doctype html>

<html>
<head>
    <base href='<?=base_url()?>'>
    
    <style>
    body {
    font-family: Roboto,Helvetica Neue,Helvetica,Arial,sans-serif;
    font-size:12px;
    }
    .smb {
        margin-left:20px;
        font-weight:bold;
        font-size:14px;
    }
    
    .header{
        background-color:black;
        color:#fff;
        margin: 10px 0px;
        text-align:center;
        font-weight:bold;
        font-size:30px;
        padding: 10px;
    }
    .subheader{  
        text-align:center;
        font-weight:bold;
        font-size:21px; 
    }
    .logo{
      width:250px;
    }
    
    table {
        font-size:12px;
        
    }
    .content {
        border:1px solid #000;
        padding:5px;
        text-transform:uppercase !important; 
    }
    .labels {
        vertical-align:text-top;
    }
    .rows td {
        margin:10px;
    }
    
    table.datadiri 
    {
        border-collapse:separate;
        border-spacing:5px; 
    }
    </style>
</head>
<body> 
    <table style="width:100%"> 
	<?php foreach($book as $key=>$row){	
		$temp  = explode(",",$row->author);
		$temp  = explode(" ",$temp[0]);
		$temp2 = $temp;
		end($temp);
		$keys = key($temp);
		$auth 	= $temp[$keys];  
		
		array_pop($temp2); 
		$sbj = explode(',',$row->alternate_subject); 
		
		if($key%2==0) echo "<tr>"; 
	?>
		<td valign="top" style="border:1px solid #000;height:6.7cm;width:50%">
			<table align="top" style="vertical-align:text-top;margin-bottom:20px;font-size:12px;"  style="width:100%"> 
				<tr>
					<td style="vertical-align:text-top;text-align:center;width:16%">
						<?= $row->klasifikasi ?>
						<br>
						<?= strtoupper(substr($auth,0,3)) ?>
						<br>
						<?= strtolower(substr($row->title, 0, 1)); ?>
					</td>
					<td style="vertical-align:text-top;width:84%" >
						<br>
						<?= strtoupper($auth).", ".ucwords(strtolower(implode(' ',$temp2))) ?>
						<br>
						<?= strip_tags(ucfirst(strtolower($row->title))) ?>/<?= ucwords(strtolower($row->author)) ?>.--
						<?=$row->publisher_city.': '.$row->publisher_name.', '.$row->published_year ?>
						<br>
						<table style="margin-left:13px;">
							<tr>
								<td>
									<?= ($row->collation!=""?'<br>'.$row->collation.'<br>':'') ?> 
									<br>ISBN: <?= $row->isbn ?>
									<?php
										$no = 1;
										if($row->subjek!="") {
											echo '<br>'.$no.". ".strtoupper($row->subjek);
											$no++;
										}
										
										if($row->alternate_subject!=""){
											foreach($sbj as $sb){
												echo '<br>'.$no.". ".strtoupper($sb);
												$no++;
											}
										}
									?> 
								</td>
							</tr>
						</table>
					</td> 
				</tr>  
			</table>
		</td> 
	<?php 
			if($key%2!=0) echo "</tr>"; 
		}
	?>
    </table>
     
</body>
</html>