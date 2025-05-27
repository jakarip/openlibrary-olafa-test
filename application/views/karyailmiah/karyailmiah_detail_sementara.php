<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3><i class="fa fa-mortar-board"></i><strong><?php echo getLang("scientific_paper_detail"); ?> <?php echo $jurusan->NAMA_PRODI ?></strong></h3> 
		</div>
		<div class="panel-content pagination2">
			<div id="dt_table1">
				<table id="table" class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th width="5%"><?php echo getLang("no") ?></th>
							<th width="30%"><?php echo getLang("name") ?> </th>
							<th width="55%"><?php echo getLang("title") ?> </th>
							<th width="10%"><?php echo getLang("year") ?></th> 
						</tr> 
					</thead>
					<tbody>
						<tr>
							<td width="5%">1</th>
							<td width="30%">ADY SAMSU BAHTERA</th>
							<td width="55%">SUBSTITUSI TEPUNG BONGGOL PISANG TERHADAP COOKIES TAHUN 2017</th>
							<td width="10%">2017</th> 
						</tr> 
						<tr>
							<td width="5%">2</th>
							<td width="30%">YOGI DENAS PUTRA</th>
							<td width="55%"> TEKNIK PENJUALAN DI RESTORAN SUIS BUTCHER STEAK HOUSEKOTA BANDUNG 2017 (STUDI KASUS DI RESTORAN SUIS BUTCHER CABANG JALAN RIAU BANDUNG)</th>
							<td width="10%">2017</th> 
						</tr> 
						<tr>
							<td width="5%">3</th>
							<td width="30%">PUTRI VENESIA PANDUWINATA</th>
							<td width="55%">SHOPPING EXPERIENCE WISATAWAN NUSANTARA DI DAYA TARIK WISATA BELANJA KOTA BANDUNG</th>
							<td width="10%">2017</th> 
						</tr> 
						<tr>
							<td width="5%">4</th>
							<td width="30%">DINDA NURUL ADISA</th>
							<td width="55%">INOVASI FROZEN CHEESE CAKE BERBASIS TAHU SUTERA SEBAGAI SUBSTITUSI CREAM CHEESE 2017</th>
							<td width="10%">2017</th> 
						</tr> 
						<tr>
							<td width="5%">5</th>
							<td width="30%">RUBY NARISSA</th>
							<td width="55%">Inovasi Pembuatan Cookies Berbasis Tepung Ubi Ungu 2017</th>
							<td width="10%">2017</th> 
						</tr> 
						<tr>
							<td width="5%">6</th>
							<td width="30%">SELLA PERMATASARI</th>
							<td width="55%">INOVASI KOLAK CANDIL BERBASIS KACANG MERAH SEBAGAI PENGGANTI UBI JALAR</th>
							<td width="10%">2017</th> 
						</tr> 
						<tr>
							<td width="5%">7</th>
							<td width="30%">YOGI AZIZ HAKIM</th>
							<td width="55%">EVALUASI LINEN CYCLE DI HOTEL PROMENADE BANDUNG TAHUN 2017</th>
							<td width="10%">2017</th> 
						</tr> 
						<tr>
							<td width="5%">8</th>
							<td width="30%">HANNA FEBRI RAMDHINA</th>
							<td width="55%">INOVASI PENGGUNAAN TEPUNG TULANG IKAN MAS DALAM PEMBUATAN BAKPAO 2017</th>
							<td width="10%">2017</th> 
						</tr> 
						<!--<tr>
							<td width="5%">8</th>
							<td width="30%"></th>
							<td width="55%"></th>
							<td width="10%">2017</th> 
						</tr> -->
					</tbody>
					
				</table>
			</div>
		</div>
	</div>
</div> 	  
					

<?php $this->load->view('theme_footer'); ?>
								
<script language="javascript" type="application/javascript"> 
$(document).ready(function () { 
	
	
	table = $('#table').DataTable({ 
        "processing": true,   
        "order": [ 3, 'desc' ] ,   
		"pageLength": 25,        
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
        
        "columnDefs": [
			{ 
				"targets": [ -1 ], 
				"orderable": false,  
			},
        ]
    }); 
	
	$('#dt_table1 .dataTables_filter input').unbind().bind('keyup', function(e) {
       if(e.keyCode == 13) {
		$("#table").dataTable().fnFilter(this.value);
		}
	}); 

	$("td .viewBuku").on( "click", function() {
		alert("aa");
		
	});
});
</script>	