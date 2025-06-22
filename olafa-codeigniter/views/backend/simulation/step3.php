<div class="panel panel-default flat">
	<div class="panel-heading steps-basic wizard clearfix">
    	<div class="steps clearfix">
        	<ul role="tablist">
            	<li role="tab" class="first done" aria-disabled="false" aria-selected="true">
                	<a href="<?= y_url_admin() ?>/simulation" aria-controls="steps-uid-0-p-0"><span class="number">1</span> Download File Simulasi</a>
                </li>
                <li role="tab" class="done" aria-disabled="false">
                	<a href="javascript:;" aria-controls="steps-uid-0-p-1"><span class="number">2</span> Upload File Hasil Simulasi</a>
                </li>
                <li role="tab" class="current" aria-disabled="false">
                	<a href="javascript:;" aria-controls="steps-uid-0-p-2"><span class="number">3</span> Simpan Hasil Simulasi</a>
                </li>
    		</ul>
     	</div>
    </div>
    <div class="panel-body">
    	<p align="center"><strong>Proses Simulasi Kelulusan Telah Berhasil Disimpan.</strong></p>
    </div>
    <div class="panel-footer">
    	<div class="text-center">
            <a href="<?= y_url_admin() ?>/simulation" class="btn btn-primary btn-xs heading-btn"><i class="icon-loop3 position-left"></i>Ulangi Proses</a>
    	</div>
    </div>
</div>

<?php $this->load->view('backend/tpl_footer'); ?>