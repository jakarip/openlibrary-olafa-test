<div class="panel panel-default flat">
	<div class="panel-heading steps-basic wizard clearfix">
    	<div class="steps clearfix">
        	<ul role="tablist">
            	<li role="tab" class="first current" aria-disabled="false" aria-selected="true">
                	<a href="javascript:;" aria-controls="steps-uid-0-p-0"><span class="number">1</span> Download File Simulasi</a>
                </li>
                <li role="tab" class="disabled" aria-disabled="true">
                	<a href="<?= y_url_admin() ?>/simulation/step2" aria-controls="steps-uid-0-p-1"><span class="number">2</span> Upload File Hasil Simulasi</a>
                </li>
                <li role="tab" class="disabled" aria-disabled="true">
                	<a href="javascript:;" aria-controls="steps-uid-0-p-2"><span class="number">3</span> Simpan Hasil Simulasi</a>
                </li>
    		</ul>
     	</div>
    </div>
    <div class="panel-body">
    	<p align="center">Terdapat <strong><?= $count ?> Calon Mahasiswa</strong> yang telah menyelesaikan proses registrasi hingga cetak berita acara.<br>Silahkan download file excel untuk simulasi kelulusan.</p>
        
        <div class="alert alert-warning no-border text-center mt-20 mb-20">
            <?php if($count > 0) { ?>
            <a href="<?= y_url_admin() ?>/simulation/step1_download_excel" style="text-transform:uppercase"><span class="text-semibold">Download File Excel Simulasi Kelulusan</span></a>
            <?php } else { ?>            
            <a href="javascript:;" style="text-transform:uppercase" class="text-grey"><span class="text-semibold">Download File Excel Simulasi Kelulusan</span></a>
            <?php } ?>
        </div>
    </div>
    <div class="panel-footer">
    	<div class="text-center">
    		<button class="btn btn-danger btn-xs heading-btn" type="button"><i class="icon-cancel-circle2 position-left"></i> Cancel</button>
            <a href="<?= y_url_admin() ?>/simulation/step2" class="btn btn-primary btn-xs heading-btn">Lanjutkan<i class="icon-arrow-right13 position-right"></i></a>
    	</div>
    </div>
</div>

<?php $this->load->view('backend/tpl_footer'); ?>