<div class="row">
    <div class="col-lg-12">
        <div class="panel">
            <div class="panel-header bg-red">
			<h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong> </h3>
            </div>
            <div class="panel-content pagination2"> 
				<div class="row content_button">  
					<div class="col-lg-1" >No. Klasifikasi</div>
					<div class="col-lg-1" > 
						<input type="text" name="classification_start" class="form-control" id="classification_start" placeholder="Awal">
					</div> 
					<div class="col-lg-1" >  
						<input type="text" name="classification_end" class="form-control" id="classification_end" placeholder="Akhir">
					</div> 
					
					<div class="col-lg-1" >Tahun Terbit</div>
					<div class="col-lg-1" > 
						<input type="text" name="year_start" class="form-control" id="year_start" placeholder="Awal">
					</div> 
					<div class="col-lg-1" > 
						<input type="text" name="year_end" class="form-control" id="year_end" placeholder="Akhir">
					</div> 
					
					<div class="col-lg-2" >Buku tidak dipinjam </div>
					<div class="col-lg-2" > 
					 		<select class="form-control" name="rent" id="rent">
								<option value="1">1 Tahun Terakhir</option>
								<option value="2">2 Tahun Terakhir</option>
								<option value="3">3 Tahun Terakhir</option>
								<option value="4">4 Tahun Terakhir</option>
								<option value="5">5 Tahun Terakhir</option>
								<option value="6">6 Tahun Terakhir</option>
								<option value="7">7 Tahun Terakhir</option>
								<option value="8">8 Tahun Terakhir</option>
								<option value="9">9 Tahun Terakhir</option>
								<option value="10">10 Tahun Terakhir</option>
							</select> 
						</select>
					</div> 
				</div>
				<div class="row content_button">  
					
					<div class="col-lg-1" >Lokasi Openlib</div>
					<div class="col-lg-2" > 
					 		<select class="form-control" name="location_openlibrary[]" id="location_openlibrary" multiple="multiple"> 
							 <?php 
							 	foreach($location as $row){
									echo '<option value="'.$row->id.'">'.$row->name.'</option>';
								}
							 ?>
						</select>
					</div>  
					<div class="col-lg-1" >Status Openlib</div>
					<div class="col-lg-2" > 
					 		<select class="form-control" name="status_openlibrary[]" id="status_openlibrary" multiple="multiple"> 
								<option value="1">Tersedia</option>
								<option value="2">Dipinjam</option>
								<option value="3">Rusak</option>
								<option value="4">Hilang</option>
								<option value="5">Expired</option>
								<option value="6">Hilang Diganti</option>
								<option value="7">Sedang Diproses</option>
								<option value="8">Cadangan</option>
								<option value="9">Weeding</option>
							</select> 
						</select>
					</div> 
					<div class="col-lg-2" >
						<a href="javascript:;" onclick="filter()" class="btn btn-primary">
							<i class="fa fa-file-o"></i>Filter
						</a>
					</div>
				</div>
				  
                <table id="table-member" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%"><?php echo getLang('no'); ?></th>
                            <th width="9%"><?php echo getLang('jenis_katalog'); ?></th>
                            <th width="20%"><?php echo getLang('judul'); ?></th>
                            <th width="9%"><?php echo getLang('no klasifikasi'); ?></th>
                            <th width="9%"><?php echo getLang('pengarang'); ?></th>
                            <th width="9%"><?php echo getLang('tahun terbit'); ?></th> 
                            <th width="9%"><?php echo getLang('no katalog'); ?></th> 
                            <th width="9%"><?php echo getLang('barcode'); ?></th> 
                            <th width="9%"><?php echo getLang('lokasi openlib'); ?></th> 
                            <th width="9%"><?php echo getLang('status'); ?></th> 
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div> 
        </div>
    </div>
</div>  
 
<?php $this->load->view('theme_footer'); ?>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
 

<script type="text/javascript"> 
var save_method; 
var table; 
var form 				= $('#modal_form #form'); 

$(document).ready(function(){
	table = $('#table-member').DataTable({     
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],  
		"pageLength": 25,
		"processing": true,  
		"serverSide": true,  
		"destroy": true,  
		"order": [],  
		"ajax": {
			"url": "<?php echo site_url('index.php/katalog/ajax_data')?>",
			"type": "POST",
			"data": function(d){ 
				d.classification_start = $('#classification_start').val(); 
				d.classification_end = $('#classification_end').val(); 
				d.year_start = $('#year_start').val(); 
				d.year_end = $('#year_end').val(); 
				d.rent = $('#rent').val(); 
				d.location_openlibrary = $('#location_openlibrary').val();  
				d.status_openlibrary = $('#status_openlibrary').val();
			}
		}, 
		"columnDefs": [
			{ 
				"targets": [ 0,-1 ], 
				"orderable": false,  
			},
		], 
        initComplete: function() {
            $('#table-member .dataTables_filter input').unbind();
            $('#table-member .dataTables_filter input').bind('keyup', function(e){
                // console.log("aaa");
                var code = e.keyCode || e.which;
                if (code == 13) { 
					$("#table-member").dataTable().fnFilter(this.value);
                }
            });
        },
	});

	// $('#location_openlibrary,#location').select2({
	// 	maximumSelectionLength: 11
	// });
	 
	 
	 
 
	 
});  
 
  
function filter() {
   table.draw();
   
} 

function reload() {
   table.draw();
   
} 
 
</script>