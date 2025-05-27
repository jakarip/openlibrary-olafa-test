<div class="panel panel-default">
    <div class="panel-heading">
        <h6 class="panel-title">Berlangganan</h6>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <ul><li> Layanan ini adalah layanan khusus yang diperuntukan bagi anggota yang ingin berlangganan, dengan memilih/klik Berlangganan Anda dapat memanfaatkan koleksi dan layanan lainnya yang tersedia di Open Library baik secara onsite maupun online seperti <a href="cdn/document_delivery_service.pdf" target="_blank"><i>Document Delivery Service</i></a> dan <a href="cdn/similarity_check_service.pdf" target="_blank"><i>Check Similarity Service</i></a></li> 
                <li> Melakukan pembayaran biaya keanggotaan ke Rekening Impress Fund Open Library sebagai
berikut:<br>
- Nomor rekening : 131.00.142161.56 (Bank Mandiri)<br>
- Atas nama : Siti Mintarsih Oktrianti<br> 
                    </li>
                    <li>Apabila mengalami kesulitan dapat menghubungi Layanan Ask Librarian kami di +62812-80000-110</li>
                </ul>  
            </div>
            <div class="col-md-6 text-right">
                <?php if($subs){ ?>
                <button class="btn btn-dark btn-labeled" style="margin-top: 10px">
                    <b><i class="icon-cart-remove "></i></b> Berlangganan
                </button>
                <?php }else { ?>
                <button class="btn btn-success btn-labeled" onclick="add()" style="margin-top: 10px">
                    <b><i class="icon-cart-remove"></i></b> Berlangganan
                </button>
                <?php } ?>
            </div>
        </div>

    </div>
    <table class="table table-bordered table-striped table-hover" id="table">
        <thead>
            <tr>
                <th width="15%">No. Transaksi</th>
                <th width="30%">Pilihan Paket</th> 
                <th width="15%">Tanggal Pembayaran</th>
                <th width="15%">Nominal Pembayaran</th>
                <th width="15%">Tanggal Berlangganan </th> 
                <th width="20%">Status</th>
                <th width="15%">Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<div class="modal fade" id="frmboxvalidasi" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger-700" style="background-image:url(assets/images/backgrounds/bg.png)">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-navicon"></i> &nbsp;Form Validasi Pembayaran Nomor Transaksi</h4>
            </div>
            <form id="frm2" class="form-horizontal" enctype="multipart/form-data">
                <input type="hidden" name="sreg" id="sreg" value="">
                <div class="modal-body">
                    <div class="box-body" style="padding-bottom:0px">
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Tipe Nomor Transaksi</label>
                            <div class="col-sm-9" id="price-container">
                                <input type="text" class="form-control" name="pin_type" id="pin_type" readonly>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Jalur Seleksi</label>
                            <div class="col-sm-9">
                                <?= form_dropdown('track', $track, '', 'class="form-control input-sm" id="track2" disabled') ?>
                            </div>
                        </div>
                        <div class="form-group form-group-sm" id="nip-container2" style="display: none">
                            <label for="pus_name" class="col-sm-3 control-label">NIK Telkom</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control ref" name="nip" id="nip" readonly>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Nomor Transaksi</label>
                            <div class="col-sm-9" id="price-container">
                                <input type="text" class="form-control ref" name="pin" id="pin" readonly>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Transfer Dari Bank</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control ref" name="inp[sreg_pay_bank]" required>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Jenis Transfer</label> 
                            <div class="col-sm-9">
                                <?= form_dropdown("inp[sreg_pay_norek]", $transfer, '', 'class="form-control input-sm" id="sreg_pay_norek"') ?>
                            </div> 
                        </div>
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Transfer Atas Nama</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control ref" name="inp[sreg_pay_name]" required>
                            </div>
                        </div>
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Upload File</label>
                            <div class="col-sm-9">
                                <input type="file" class="form-control ref" name="photos" required>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-labeled btn-xs pull-left" data-dismiss="modal">
                        <b><i class="icon-switch"></i></b> Batal
                    </button>
                    <button type="button" class="btn btn-success btn-labeled btn-xs" id="act-save" onclick="save_validate()">
                        <b><i class="icon-floppy-disk"></i></b> Simpan
                    </button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="frmbox" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger-700" style="background-image:url(assets/images/backgrounds/bg.png)">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-navicon"></i> &nbsp;Form Berlangganan</h4>
            </div>
            <form id="frm3" class="form-horizontal">
                <input type="hidden" name="id" value="<?= $this->session->userdata('member_id') ?>"> 
                <div class="modal-body">
                    <div class="box-body" style="padding-bottom:0px">
                        <div class="form-group form-group-sm">
                            <label for="pus_name" class="col-sm-3 control-label">Durasi Berlangganan</label>
                            <div class="col-sm-9">
                                <?= form_dropdown('subscribes', $member, '', 'class="form-control input-sm" id="subscribes" required="required"') ?>
                            </div>
                        </div> 
                    </div><!-- /.box-body -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-labeled btn-xs pull-left" data-dismiss="modal">
                        <b><i class="icon-switch"></i></b> Batal
                    </button>
                    <button type="button" class="btn btn-success btn-labeled btn-xs" id="act-save" onclick="save()">
                        <b><i class="icon-floppy-disk"></i></b> Simpan
                    </button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<?php $this->load->view('frontend/tpl_footer'); ?>
<script type="text/javascript" src="assets/limitless/global/js/plugins/tables/datatables/datatables.min.js"></script>
<script type="text/javascript" src="assets/limitless/global/js/plugins/forms/styling/uniform.min.js"></script>

<script type="text/javascript">
var tb 		= '#table';
var baseurl = 'index.php/subscribe';

$(document).ready(function(){
    $(tb).dataTable({
        dom: '<"datatable-scroll"t><"datatable-footer"p>',
        'ajax': {
            'url':baseurl+'/json'
        },
        'order':[
            [0, 'desc']
        ],
        'columnDefs': [
            { 'targets': [-1,-2], 'searchable': false, 'orderable': false }
        ]
    });

    $('.dataTables_filter input[type=search]').attr('placeholder','Pencarian');

    $('.dataTables_length select').select2({
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });

    $('#track').change(function() {
        getPin($(this).val());

        var nama = $("#track option:selected" ).text().toLowerCase();
        if(nama.indexOf('telkom') >= 0)
            $('#nip-container').show();
        else
            $('#nip-container').hide();
    });
    $('#track_aff').change(function() {
        getPinAff($(this).val());

        var nama = $("#track_aff option:selected" ).text().toLowerCase();
        if(nama.indexOf('telkom') >= 0)
            $('#nip-container-aff').show();
        else
            $('#nip-container-aff').hide();
    });

    $('#track2').change(function() {
        var nama = $("#track2 option:selected" ).text().toLowerCase();
        if(nama.indexOf('telkom') >= 0)
            $('#nip-container2').show();
        else
            $('#nip-container2').hide();
    }); 
    $('#track_aff2').change(function() {
        var nama = $("#track_aff2 option:selected" ).text().toLowerCase();
        console.log(nama.indexOf('telkom'));
        if(nama.indexOf('telkom') >= 0)
            $('#nip-container-aff2').show();
        else
            $('#nip-container-aff2').hide();
    });

    $('#subscribes').select2();
});


	
function add()
{ 
	$('#act-save').show();
	$('#act-update').hide();
	$('#frmbox').modal({keyboard: false, backdrop: 'static'});
}
 

 
function validate_online(id,no)
{ 
    if(confirm('No. Transaksi : '+no+'.\nApakah anda yakin akan melakukan konfirmasi pembayaran ?')) {
        $.ajax({
            url:baseurl+'/save_validate',
            global:false,
            async:true,
            dataType:'json',
            type:'post',
            data: ({ id : id, no : no }),
            success: function(e) { 
                _reload();
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
} 
 

function save()
{
    
    if(confirm('Durasi Berlangganan : '+$("#subscribes").val()+' Bulan.\nApakah anda yakin akan berlangganan dengan durasi tersebut ?')) {
        if($("#frm3").valid())
        {
            $.ajax({
                url:baseurl+'/save',
                global:false,
                async:true,
                type:'post',
                dataType:'json',
                data: $('#frm3').serialize(),
                success : function(e) {
                    if(e.status == 'ok;')
                    {
                        $("#frmbox").modal('hide');
                        window.location.reload();
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
    }
}

function save_validate()
{
    if($("#frm2").valid())
    {
        var formData = new FormData($('#frm2')[0]);
        $.ajax({
            url:baseurl+'/save_validate',
            global:false,
            async:true,
            type:'post',
            data: formData,
            contentType: false,//untuk upload image
            processData: false,//untuk upload image
            dataType:'json',
            success : function(e) {
                if(e.status == 'ok;')
                {
                    $("#frmboxvalidasi").modal('hide');
                    alert("Terima Kasih telah melakukan validasi pembayaran.\nSilahkan cek email anda untuk mendapatkan token dan melanjutkan registrasi setelah admin kami melakukan verifikasi.");
                    //window.location.href = 'questionnaire';
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
}

function save_validate_affiliate()
{
    if($("#frm3").valid())
    {
        $.ajax({
            url:baseurl+'/save_validate_affiliate',
            global:false,
            async:true,
            type:'post',
            dataType:'json',
            data: $('#frm3').serialize(),
            success : function(e) {
                if(e.status == 'ok;')
                {
                    $("#frmboxaffiliate").modal('hide');
                    window.location.href = 'questionnaire';
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
}

function save_affiliate_online()
{
    if($("#frm4").valid())
    {
        $.ajax({
            url:baseurl+'/save_affiliate_online',
            global:false,
            async:true,
            type:'post',
            dataType:'json',
            data: $('#frm4').serialize(),
            success : function(e) {
                if(e.status == 'ok;')
                {
                    $("#frmbox").modal('hide');
                    window.location.reload();
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
}

function reg(type, pin, step, print)
{
    $.ajax({
        url:'reg',
        global:false,
        async:true,
        dataType:'json',
        type:'post',
        data: ({ pin_type : type, pin : pin, pin_step : step, pin_print : print }),
        success: function(e) {
            if(e.status == 'ok;')
                window.location.href = e.callback;
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

function _reset_frm()
{
    validator.resetForm();

    $("label.error").hide();
    $(".error").removeClass("error");
    $('#frm')[0].reset();
}

function _reload()
{
    $(tb).dataTable().fnDraw();
}

function convertToRupiah(angka)
{
    var rupiah = '';
    var angkarev = angka.toString().split('').reverse().join('');
    for(var i = 0; i < angkarev.length; i++) if(i%3 == 0) rupiah += angkarev.substr(i,3)+'.';
    return 'Rp. '+rupiah.split('',rupiah.length-1).reverse().join('');
}
</script>