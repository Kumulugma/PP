<div id="masz-pytania">
    <div class="container">
        <div class="row">
            <div class="col-12 col-sm-10 col-md-10 col-lg-8 col-xl-6 mx-auto text-center">
                <p class="h3 section_title"><?= get_field('mp_header', 50)?></p>
                <p><?= get_field('mp_content', 50)?></p>

                <div class="row mt-5">
                    <div class="col-12 col-md-6 mx-auto">
                        <a href="mailto:<?= get_field('mp_mail', 50)?>" class="btn btn-secondary btn-icon btn-full"><span><img src="<?php bloginfo('template_url'); ?>/images/svg/mail_outline.svg" alt=""></span><?= get_field('mp_label_mail', 50)?></a>
                    </div>
                    <?php if ( !is_page('najczesciej-zadawane-pytania') ) { ?>
                        <div class="col-12 col-md-6 mx-auto">
                            <a href="<?= get_field('mp_url_faq', 50)?>" class="btn btn-primary btn-icon btn-full"><span><img src="<?php bloginfo('template_url'); ?>/images/svg/question_answer.svg" alt=""></span><?= get_field('mp_label_faq', 50)?></a>
                        </div>
                    <?php } ?>



                </div>

            </div>
        </div>
    </div>
</div>