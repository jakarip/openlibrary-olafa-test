<?php
                    
                    $iuser = $this->session->userdata('user');
?>

<form name="frm" id="frm" method="post" enctype="multipart/form-data" action=""> 
<div class="panel panel-default"> 
    <div class="panel-body">    
        <div class="card">
            <div class="table-responsive">
                <table class="table table-togglable table-hover"> 
                    <tbody>
                        <tr>
                            <td width="20%" class="">Username</td>
                            <td width="2%">:</td>
                            <td><?=$dt->master_data_user ?></td>  
                        </tr> 
                        <tr>
                            <td>Nama / Name</td>
                            <td>:</td>
                            <td><?=$dt->master_data_fullname ?></td>  
                        </tr> 
                        <tr>
                            <td>No. Anggota / Member Number</td>
                            <td>:</td>
                            <td><?=$dt->master_data_number ?></td>  
                        </tr> 
                        <tr>
                            <td>Email</td>
                            <td>:</td>
                            <td><?=$dt->master_data_email ?></td>  
                        </tr> 

                        <tr>
                            <td>HP / Mobile Phone</td>
                            <td>:</td>
                            <td><?=$dt->master_data_mobile_phone ?></td>  
                        </tr> 

                        <tr>
                            <td>QR Code</td>
                            <td>:</td>
                            <td><?=$qrcode ?></td>  
                        </tr> 
           
                    </tbody>
                </table>
            </div> 
        </div> 
    </div>
</div>
 
</form>

<?php $this->load->view('frontend/tpl_footer'); ?>
<script type="text/javascript" src="assets/limitless/global/js/plugins/pickers/datepicker.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/styling/uniform.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>

<script type="text/javascript">

var baseurl = 'pendaftaran';

$(document).ready(function(){
    $(".file-styled").uniform();

    $('#par_birthdate').datepicker({
        format: "dd-mm-yyyy", 
        // startDate: new Date('01-01-1980'),
    }); 
    $('.select').select2();

    // Simple select without search
    $('.select-simple').select2({
        minimumResultsForSearch: Infinity
    });

    $('#par_id_kec').select2({
        ajax: {
            url:baseurl+"/getkec",
            dataType: 'json',
            type: 'POST',
            data: function (params) {
                return {
                    searchTerm: params.term
                };
            },
            processResults: function (data) {
                return { results: data };
            }
        },
        minimumInputLength: 3
    });

    var substringMatcher = function() {
        return function findMatches(q, cb) {
            var matches = [];
            var strs = getcity_birthplace(q);

            $.each(strs, function(i, str) {
                matches.push({ value: str });
            });

            console.log(matches);
            cb(matches);
        };
    };

    $('#par_birthplace').typeahead(
        {
            hint: true,
            highlight: true,
            minLength: 3
        },
        {
            name: 'states',
            displayKey: 'value',
            source: substringMatcher()
        }
    );

    $('#school_city').select2({
        ajax: {
            url:baseurl+"/getcity",
            dataType: 'json',
            type: 'POST',
            data: function (params) {
                return {
                    searchTerm: params.term
                };
            },
            processResults: function (data) {
                return { results: data };
            }
        },
        minimumInputLength: 0
    });

    $("#par_id_kec").change(function() {
        $.ajax({
            url : baseurl+'/getkodepos',
            type: "POST",
            data: {
                'id' : $(this).val()
            },
            dataType: "JSON",
            beforeSend : function() {
                $('#loading-img').show();
            },
            complete : function() {
                $('#loading-img').hide();
            },
            success: function(dt)
            {
                $("#par_postcode").html('<option value=""></option>');
                $.each(dt,function(index, value){
                    $("#par_postcode").append('<option value="'+value.pos_code+'">'+value.pos_code+' - '+value.pos_kel+'</option>');
                });
            },
            error: function (jqXHR, textStatus, errorThrown)
            {

            }
        });
    });

    sch();

    // $.validator.addMethod("minDate", function(value, element) {
    //     var curDate = new Date('01-01-1980');
    //     var inputDate = new Date(value);
    //     if (inputDate >= curDate)
    //         return true;
    //     return false;
    // }, "Min date : 01-01-1980");   // error message 

    $.validator.addClassRules({
        photos:{
            extension: "jpg|jpeg|png",
            maxfilesize: {
                "unit": "MB",
                "size": 1
            },
            required: function() {
                if($('#photos_hidden').val() == '')
                    return true;
                else
                    return false;
            }
        }, 
        // borndate :{
        //     minDate: true
        // }
    }); 
});

function show_adv_search()
{
    if($('#adv-search-block').is(':visible'))
        $('#adv-search').html('Cari Selengkapnya');
    else
        $('#adv-search').html('Tutup');


    $('#adv-search-block').toggle();
}

function sch() {
    $('#table_school').dataTable({
        dom: '<"datatable-scroll"t><"datatable-footer"ip>',
        'ajax': {
            'url'			: baseurl+'/json_school',
            'dataType' 		: 'json',
            'type'			: 'POST',
            'data' : function(data) {
                data.status	= $('#school_status').val();
                data.city 	= $('#school_city').val();
                data.kec 	= $('#school_kec').val();
            }
        },
        "processing": true,
        "serverSide": true,
        /*"destroy": true,*/
        "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
        "pageLength": 20,
        'order':[
            [1, 'asc']
        ],
        'columnDefs': [
            { 'targets': [0], 'searchable': false, 'orderable': false, 'className': 'center' }
        ],
        "fnDrawCallback": function() {
            $("input:radio[name=school_choose]").click(function(){
                $("#sreg_id_school").val($(this).val());
                $("#sreg_id_school_hidden").val($(this).val());
                $("#sreg_id_school").trigger( "click" );
            });
        }
    });

    $('#school_status').change(function() {
        $('#table_school').dataTable().fnDraw();
    });

    $('#school_kec').change(function() {
        $('#table_school').dataTable().fnDraw();
    });

    $('#search').keypress(function(e) {
        if(e.keyCode==13)
            $('#table_school').dataTable().fnFilter( $('input#search').val() );
    });

    $("#school_city").on('change', function() {

        $('#table_school').dataTable().fnDraw();

        $.ajax({
            url : baseurl+'/getkodekec',
            type: "POST",
            data: {
                'id' : $("#school_city").val()
            },
            dataType: "JSON",
            beforeSend : function() {
                $('#loading-img').show();
            },
            complete : function() {
                $('#loading-img').hide();
            },
            success: function(dt)
            {
                $("#school_kec").html('<option value="0">Semua Kabupaten</option>');
                $.each(dt,function(index, value){
                    $("#school_kec").append('<option value="'+value.kec_id+'">'+value.kec_name+'</option>');
                });
            },
            error: function (jqXHR, textStatus, errorThrown)
            {

            }
        });
    });
}

function save()
{
    if($("#frm").valid())
    {
        var formData = new FormData($('#frm')[0]);
        $.ajax({
            url:baseurl+'/biodata_save',
            global:false,
            async:true,
            type:'post',
            data: formData,
            contentType: false,//untuk upload image
            processData: false,//untuk upload image
            dataType:'json',
            success : function(e) {
                if(e.status == 'success')
                {
                    alert('Data telah berhasil diperbaharui');
                    window.location.href='';
                }
                else alert(e.error);
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
    } else {
        $('html, body').animate({
            scrollTop: ($('.validation-error-label').offset().top - 300)
        }, 2000);
    }
}

function getcity_birthplace(q)
{
    var r = [];

    $.ajax({
        url:baseurl+'/getcity_birthplace',
        global:false,
        async:false,
        dataType:'json',
        type:'post',
        data: ({ q : q }),
        success: function(e) {
            r = e;
        },
        error : function() {
            alert('<?= $this->config->item('alert_error') ?>');
        }
    });

    return r;
}
</script>
