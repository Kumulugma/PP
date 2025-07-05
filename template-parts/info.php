<div class="content_wrap" id="info_articles">
    <div class="container">

        <?php
        $homepage_id = get_option('page_on_front');
        while (have_rows('sekcja_tekst-foto', $homepage_id)): the_row();

            if (get_row_index() % 2 == 0) {
                ?>

                <div class="row ha_row">
                    <div class="order-0 col-12 order-md-1 col-md-6">
                        <?php the_sub_field('tresc') ?>
                        <?php if (get_sub_field('etykieta_przycisku')): ?>
                            <a href="<?php the_sub_field('link') ?>" class="btn btn-primary btn-arrow"><?php the_sub_field('etykieta_przycisku') ?></a>
                        <?php endif; ?>
                    </div>
                    <div class="order-1 col-12 order-md-0 col-md-6 ha_img">
                        <?php
                        $image = get_sub_field('obraz');
                        $size = 'full'; // (thumbnail, medium, large, full or custom size)
                        if ($image) {
                            echo wp_get_attachment_image($image, $size, '', ["class" => "img-fluid"]);
                        }
                        ?>
                    </div>
                </div>

            <?php } else { ?>

                <div class="row ha_row">
                    <div class="order-0 col-12 order-md-0 col-md-6">
                        <?php the_sub_field('tresc') ?>
                        <?php if (get_sub_field('etykieta_przycisku')): ?>
                            <a href="<?php the_sub_field('link') ?>" class="btn btn-primary btn-arrow"><?php the_sub_field('etykieta_przycisku') ?></a>
                        <?php endif; ?>
                    </div>
                    <div class="order-1 col-12 order-md-1 col-md-6 ha_img">
                        <?php
                        $image = get_sub_field('obraz');
                        $size = 'full'; // (thumbnail, medium, large, full or custom size)
                        if ($image) {
                            echo wp_get_attachment_image($image, $size, '', ["class" => "img-fluid"]);
                        }
                        ?>
                    </div>
                </div>



            <?php } endwhile; ?>


    </div>
</div>

