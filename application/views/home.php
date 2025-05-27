<div class="col-md-12 portlets">
	<div class="panel">
		<div class="panel-header bg-red">
		  <h3> <strong><?php echo getIconCurrentMenu() ?> <?php echo getCurrentMenuName() ?></strong> </h3>
		</div>
		<div class="panel-content pagination2"  style="min-height:400px;">
			 Selamat Datang di Open Library Telkom University
			 
		</div>
	</div>
</div> 					 
          
<?php $this->load->view('theme_footer'); ?>
<script type="text/javascript">
var table;
$(document).ready(function(){ 
	totalcollection($('#tahun').val());
	dt_table($('#tahun').val());
	
	$('.dataTables_filter input').unbind().bind('keyup', function(e) {
       if(e.keyCode == 13) {
		$(".dt_table1").dataTable().fnFilter(this.value);
		}
	}); 
	
   $('#tahun').on('change', function(e) {
	    totalcollection($(this).val());
        dt_table($(this).val());
		$('.dataTables_filter input').unbind().bind('keyup', function(e) {
		   if(e.keyCode == 13) {
			$(".dt_table1").dataTable().fnFilter(this.value);
			}
		});
	});
});

function dt_table(val) {
	table = $('.dt_table1').DataTable({ 
        "processing": true,  
        "serverSide": true,  
		"destroy": true,
        "order": [],   
		"pageLength": -1,        
		"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
        "ajax": {
            "url": 'index.php/bahanpustaka/ajax_index',
            "type": "POST",
			"data" : {
				year : val
			}
        }, 
        "columnDefs": [
			{ 
				"targets": [ -1 ], 
				"orderable": false,  
			},
        ]
    }); 
}

function excel(id,year) {
	$.ajax({
        url : 'index.php/bahanpustaka/excel',
        type: "POST",
		data: {
				year :year,
				id : id
			},
		beforeSend : function() {
			showLoading();
		},
		complete : function() {
			hideLoading();
		},
        success: function(data)
        {
			document.location.href =(data);
        }
    });
}  

function excel_header() {
	$.ajax({
        url : 'index.php/bahanpustaka/excel_header',
        type: "POST",
		data: {
				year : $("#tahun").val()
			},
		beforeSend : function() {
			showLoading();
		},
		complete : function() {
			hideLoading();
		},
        success: function(data)
        {
			document.location.href =(data);
        }
    });
}  

function totalcollection(year) {
	$.ajax({
        url : 'index.php/bahanpustaka/totalcollection',
        type: "POST",
		data: {
				year :year
			},
        dataType: "JSON",
        success: function(data)
        {
			$('#judul').html('<b>'+data.judul+' <?php echo getLang('title')?></b>');
			$('#eks').html('<b>'+data.eks+' <?php echo getLang('copy')?></b>');
			$('#mk').html('<b>'+data.mk+' <?php echo getLang('subject')?></b>');
			$('#mkadabuku').html('<b>'+data.mkadabuku+' <?php echo getLang('subject')?></b>');

        }
    });
}

function reload() {
   table.draw();
}

</script>