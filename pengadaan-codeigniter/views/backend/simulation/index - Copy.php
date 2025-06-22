<div class="panel panel-default flat">
	<div class="panel-body">
    	<div class="row">
        	<div class="col-md-3">
            	<div class="form-group">
                    <label class="control-label">Total Calon Mahasiswa Mendaftar</label>
                    <div>
                        <div class="input-group">
                        	<span class="input-group-addon"><i class="icon-user"></i></span>
                            <input type="text" class="form-control input-xs" id="sim-total" readonly>
                            <span class="input-group-addon">Orang</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
            	<div class="form-group">
                    <label class="control-label">Jumlah Calon Mahasiswa Yang Diterima</label>
                    <div class="row">
                    	<div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-user-check"></i></span>
                                <input type="text" class="form-control input-xs" id="sim-count-real" value="0">
                                <span class="input-group-addon">Orang</span>
                            </div>
                    	</div>
                    	<div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="icon-percent"></i></span>
                                <input type="text" class="form-control input-xs" id="sim-count-persen" value="0">
                                <span class="input-group-addon">%</span>
                            </div>
                    	</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
            	<div class="form-group">
                    <label class="control-label">&nbsp;</label>
                    <div>
            			<button class="btn btn-success btn-xs" type="button" onClick="save()"><i class="icon-floppy-disk position-left"></i>Simpan</button>
            		</div>
                </div>
            </div>
        </div>
    </div>
    <form id="frm">
    <table class="table table-bordered table-striped table-hover table-xs" id="table">
        <thead>
            <tr>
                <th rowspan="2" class="nosort center">&nbsp;</th>
                <th rowspan="2" class="nosort">Nama</th>
                <th rowspan="2" class="nosort">Asal Sekolah</th>
                <th rowspan="2" class="nosort center" width="100">Bobot Sekolah</th>
                <th rowspan="2" class="nosort center" width="50">Rata-Rata Nilai</th>
                <th rowspan="2" class="center" width="100">Nilai Akhir</th>
                <?php foreach($course as $c) { ?>
                <th colspan="5" class=" center"><?= $c->course_name ?></th>
                <?php } ?>
            </tr>
            <tr>
            	<?php foreach($course as $c) { ?>
                <th class="nosort center">1</th>
                <th class="nosort center">2</th>
                <th class="nosort center">3</th>
                <th class="nosort center">4</th>
                <th class="nosort center">5</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
        	
        </tbody>
    </table>
    </form>
</div>

<?php $this->load->view('backend/tpl_footer'); ?>
<!-- Theme JS files -->	
<script type="text/javascript" src="assets/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/selects/select2.min.js"></script>
<script type="text/javascript" src="assets/js/plugins/forms/validation/validate.min.js"></script>
<script type="text/javascript" src="assets/js/core/setting.js"></script>

<script type="text/javascript">
var tb 		= '#table';      
var baseurl = '<?= y_url_admin() ?>/simulation';

$(document).ready(function() {	
    $(tb).dataTable({
		'dom': '<"datatable-scroll"t><"datatable-footer"ip>',
        'ajax': {
            'url':baseurl+'/json'
		},
		'order':[
			[5, 'desc']
		],
		'columnDefs': [ 
			{ 'targets': 'nosort', 'searchable': false, 'orderable': false },
			{ 'targets': 'center', 'className': 'center' }  
		],
		"bStateSave": true,
		"pageLength": -1,
		"autoWidth": false,
		
		"searching": false,
		"lengthChange": false,
		"paging": false,
		
		'scrollX': true,
		'drawCallback': function( settings ) {
			var api = this.api();
	 		var row = api.rows( {page:'current'} ).data();
			
			$('#sim-total').val(row.length);
		},
		'rowCallback': function( row, data, index ) {
			var maks = $('#sim-count-real').val();
			if(maks > 0)
			{
				maks = parseInt($('#sim-count-real').val()) - parseInt(1);
				if(index <= maks)
				{
					//console.log(index);
					$('td', row).css('background-color', '#76FF03');
					$('input.chk', row).prop( 'checked', 'checked' );
				}
			}
		}
    });
	
	$('.dataTables_filter input[type=search]').attr('placeholder','Pencarian');

    $('.dataTables_length select').select2({
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });
	
	$('body').on('keypress', '.input-table', function(e) {
		if(e.which == 13) 
		{
			var id = $(this).attr('id').replace('sch-id-', '');
			var score = $(this).val();
			var score_avg = $(this).data('score-avg');
			
			update_school_score(id, score, score_avg);
		}
	});
	
	$('#sim-count-persen').keypress(function(e) {
		if(e.which == 13) 
		{
			simulation('persen', $(this).val());
		}
	});
	
	$('#sim-count-real').keypress(function(e) {
		if(e.which == 13) 
		{
			simulation('real', $(this).val());
		}
	});
});

function simulation(type, count)
{
	var c = parseInt(count);
	if(c <= 0)
		return false;
	
	var maks = $('#sim-total').val();
	if(type == 'persen')
	{
		if(c > 100) 
		{
			c = 100;
			$('#sim-count-persen').val(c);
		}
		
		var real = Math.floor(maks * (c / 100));		
		$('#sim-count-real').val(real);		
		_reload();
	}
	else
	{
		if(c > maks) 
		{
			c = maks;
			$('#sim-count-real').val(c);
		}
		
		var persen = Math.floor((c / maks) * 100);		
		$('#sim-count-persen').val(persen);		
		_reload();
	}	
}

function save()
{
	var atLeastOneIsChecked = $('input[name="inp[]"]:checked').length > 0;
	if(!atLeastOneIsChecked)
	{
		alert('Silahkan pilih mimimal 1 siswa terlebih dahulu.');
		return false;	
	}
	
	$.ajax({
		url:baseurl+'/save',
		global:false,
		async:true,
		type:'post',
		dataType:'json',
		data: $('#frm').serialize(),
		success : function(e) {
			if(e.status == 'ok;') 
			{
				alert('Data berhasil disimpan.');
				_reload();
			} 
			else alert(e.text);
		},
		error : function() {
			alert('<?= $this->config->item('alert_error') ?>');	 
		},
		beforeSend : function() {
			$('#loading-img').show();
		},
		complete : function() {
			$('#loading-img').hide();
		}
	});
}

function update_school_score(id, score, score_avg)
{
	$.ajax({
		url:baseurl+'/update_school_score',
		global:false,
		async:true,
		type:'post',
		dataType:'json',
		data: ({ id : id, score_school : score, score_avg : score_avg }),
		success : function(e) {
			if(e.status == 'ok;') 
			{				
				_reload();
			} 
			else alert(e.text);
		},
		error : function() {
			alert('<?= $this->config->item('alert_error') ?>');	 
		},
		beforeSend : function() {
			$('#loading-img').show();
		},
		complete : function() {
			$('#loading-img').hide();
		}
	});
}

function _reload()
{
	$(tb).dataTable().fnDraw();
}
</script>