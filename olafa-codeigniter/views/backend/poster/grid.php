<div class="row">
    <?php foreach($data as $row) { ?>
    <div class="col-lg-3 col-sm-6">
        <div class="thumbnail">
            <div class="thumb">
                <img src="<?= base_url().$row->poster_image ?>" alt="">
                <div class="caption-overflow">
                    <span>
                        <a href="<?= base_url().$row->poster_image ?>" class="btn border-white text-white btn-flat btn-icon btn-rounded"><i class="icon-zoomin3"></i></a>
                        <a href="<?= base_url().$row->poster_image ?>" class="btn border-white text-white btn-flat btn-icon btn-rounded ml-5" target="_blank"><i class="icon-link2"></i></a>
                    </span>
                </div>
            </div>

            <div class="caption">
                <h6 class="text-semibold no-margin-top"><?= $row->poster_title ?></h6>
                <?= y_date_text($row->poster_date) ?>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<?php $this->load->view('backend/tpl_footer'); ?>

<script type="text/javascript">
</script>