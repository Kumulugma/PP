<div id="czym-jest-polski-podarek" style="background-image: url('<?= acf_image_url(get_field('cjpp_background')); ?>')">

    <span class="ornament"><img src="<?php bloginfo('template_url'); ?>/images/svg/ozdobnik.svg" alt="..." class="img-fluid"></span>

    <div class="container">
        <div class="row">
            <div class="col-12 col-md-6 cjpp_text">
                <h2 class="h3 section_title"><?= get_field('cjpp_header')?></h2>
                <h3 class="h5 mb-4"><?= get_field('cjpp_subheader')?></h3>
                <p><?= get_field('cjpp_content')?></p>
            </div>
            <div class="col-12 col-md-6 image-over">
                <img class="img-fluid" src="<?= acf_image_url(get_field('cjpp_image1')); ?>" alt="<?= acf_image_alt(get_field('cjpp_image1')); ?>">
                <img class="img-fluid" src="<?= acf_image_url(get_field('cjpp_image2')); ?>" alt="<?= acf_image_alt(get_field('cjpp_image2')); ?>">
            </div>
        </div>
    </div>
</div>