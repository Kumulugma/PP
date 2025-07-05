<?php
/* Template name: Strona główna */
get_header();
?>

<div id="hero" class="bg_light_gradient">
    <div class="container">

        <div class="owl-carousel owl-theme hero_slider">
            <?php if (have_rows('slajd')): ?>
                <?php
                while (have_rows('slajd')): the_row();
                    $image = get_sub_field('zdjecie');
                    ?>
                    <div class="row">
                        <div class="col-12 hero_inner">
                            <div class="hero_text">
                                <p class="h3"><?php the_sub_field('naglowek'); ?></p>
                                <p><?php the_sub_field('opis'); ?></p>
                                <a href="<?php the_sub_field('przycisk-link'); ?>" title="Zobacz produkt" class="btn btn-secondary btn-arrow"><?php the_sub_field('przycisk-tresc'); ?></a>
                            </div>

                            <div class="hero_img">
                                <?php echo wp_get_attachment_image($image, 'full', "", ["class" => "img-fluid", "alt" => "Polski Podarek", "loading" => "lazy"]); ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>

        </div>
    </div>

    <span class="ornament"><img src="<?php bloginfo('template_url'); ?>/images/svg/ozdobnik.svg" alt="..." class="img-fluid"></span>
</div>

<div class="container" id="promowane_produkty">
    <div class="row">
        <div class="col-12 text-center">
            <h1 class="h4 color-secondary section_title"><?= get_field('popular_title') ?></h1>
        </div>

        <?php
        $products_data = array();

        if (have_rows('popular_products')):

            while (have_rows('popular_products')) : the_row();
                $products_data[] = (get_sub_field('popular_product')->ID);
            endwhile;

        endif;

        $products_data = implode(',', $products_data);
        ?>
        <?php echo do_shortcode('[products ids="' . $products_data . '" limit="4" columns="4" orderby="rand"]'); ?>
    </div>
</div>

<div class="bg_light_gradient" id="home_zestawy">
    <div class="container">
        <div class="row">
            <div class="col-12" id="hz_wrap">
                <div class="hero_text">

                    <h2 class="h3"><?= get_field('customization_title') ?></h2>
                    <?= get_field('customization_text') ?>
                    <?php
                    $term = get_term_by('id', get_field('customization_1_url'), 'product_cat');
                    if ($term && !is_wp_error($term)) {
                        $permalink = get_term_link($term);
                    } else {
                        $permalink = null;
                    }
                    ?>
                    <?php if ($permalink != null) { ?>
                        <a href="<?= $permalink ?>" title="<?= get_field('customization_1_action') ?>" class="btn btn-arrow btn-secondary"><?= get_field('customization_1_action') ?></a>
                    <?php } ?>

                </div>

                <div class="hero_img">
                    <img src="<?= wp_get_attachment_url(get_field('customization_img')) ?>" alt="<?= get_post_meta(get_field('customization_img'), '_wp_attachment_image_alt', true) ?>" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container" id="home_zestawy_products">
    <?php echo do_shortcode('[products limit="4" columns="4" category="mozliwosc-personalizacji" cat_operator="AND" orderby="popularity" orderby="rand"]') ?>

    <?php
    $term = get_term_by('id', get_field('customization_2_url'), 'product_cat');
    if ($term && !is_wp_error($term)) {
        $permalink = get_term_link($term);
    } else {
        $permalink = null;
    }
    ?>
    <?php if ($permalink != null) { ?>
        <div class="row">
            <div class="col-12 col-sm-8 col-md-5 col-lg-4 col-xl-3 text-center pt-5 mx-auto">
                <a href="/kategoria-produktu/zestawy-prezentowe/" title="<?= get_field('customization_2_action') ?>" class="btn btn-primary btn-arrow btn-full"><?= get_field('customization_2_action') ?></a>
            </div>
        </div>
    <?php } ?>


</div>
<?php /*
<div id="kategorie">
    <div class="section_subbheader">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <span class="ornament"><img src="<?php bloginfo('template_url'); ?>/images/svg/ozdobnik-fff.svg" alt="..." class="img-fluid"></span>
                    <p class="h4 section_title">Kategorie produktów w naszym sklepie</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container" id="kategorie_inner">
        <?php if (have_rows('home_cat_group')): ?>
            <div class="row">
                <?php while (have_rows('home_cat_group')): the_row(); ?>
                    <div class="col-12 col-sm-6 col-md-6 col-lg-3 cat_box">
                        <?php
                        $id = get_sub_field('hc_kategoria');
                        if ($term = get_term_by('id', $id, 'product_cat')) {
                            ?>
                            <a href="/kategoria-produktu/<?php echo $term->slug; ?>" rel="nofollow">
                                <img src="<?php bloginfo('template_url'); ?>/images/cat.jpg" class="img-fluid" alt="<?php echo $term->name; ?>">
                            </a>
                            <p class="h5"><a href="/kategoria-produktu/<?php echo $term->slug; ?>" rel="" title="<?php echo $term->name; ?>"><?php echo $term->name; ?></a></p>
                        <?php } ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12 col-sm-8 col-md-5 col-lg-4 col-xl-3 text-center mx-auto">
                <a href="/kategorie" title="Zobacz wszystkie kategorie" class="btn btn-primary btn-arrow btn-full">Zobacz wszystkie kategorie</a>
            </div>
        </div>
    </div>
</div>
 */ ?>

<?php get_template_part('template-parts/czym-jest-pp'); ?>
<?php get_template_part('template-parts/nasze-atuty'); ?>
<?php get_template_part('template-parts/questions'); ?>
<?php get_template_part('template-parts/info'); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <?php if (has_post_thumbnail()) { ?>
            <?php echo get_the_post_thumbnail_url(); ?>
        <?php } else { ?><?php } ?>
    <?php
    endwhile;
endif;
?>

<?php get_template_part('template-parts/newsletter'); ?>

<?php get_footer(); ?>