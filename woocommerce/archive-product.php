


<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */
defined('ABSPATH') || exit;

get_header('shop');
?>


<?php
/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action('woocommerce_before_main_content');
?>

<?php
/**
 * Hook: woocommerce_archive_description.
 *
 * @hooked woocommerce_taxonomy_archive_description - 10
 * @hooked woocommerce_product_archive_description - 10
 */
//do_action( 'woocommerce_archive_description' );
?>

<div class="category_header">

    <?php
    $termcat = get_queried_object();

    $term_desc = get_field('short_cat_desc', $termcat);
    $term_h1 = get_field('kategoriaprod_h1', $termcat);

    $termscustom = get_field('cat_links', $termcat);
    ?>

    <?php do_action('woocommerce_custom_breadcrumbs'); ?>

    <?php if (is_shop()) { ?>
        <div class="ch_text">
            <h1 class="h3">Sklep</h1>
        </div>
    <?php } else { ?>
        <div class="ch_text">
            <h1 class="h3">
                <?php
                global $wp_query;
                $additional_title = "";
                if ($wp_query->max_num_pages > 1 && get_query_var('paged') >= 2) {
                    $paged = get_query_var('paged') ? get_query_var('paged') : 1;
                    $additional_title = ' - strona ' . esc_html($paged);
                }
                ?>
                <?php $current_cat = get_queried_object(); ?>
                <?php //echo $current_cat->name; ?>
                <?= ($term_h1 != "") ? $term_h1 : $current_cat->name; ?>
                <?= $additional_title ?>
            </h1>
            <?php if (get_query_var('paged') == 0) { ?>
            <p class="h6"><?php echo $term_desc; ?></p>
            <?php } ?>
        </div>
        <?php
        $thumbnail_id = get_term_meta($current_cat->term_id, 'thumbnail_id', true);
        $image_url = wp_get_attachment_url($thumbnail_id);
        ?>
        <div class="ch_img">
            <?php if (!empty($image_url)) : ?>
                <img src="<?= $image_url ?>" alt="..." class="img-fluid archive-category-image" fetchpriority="high">
            <?php else : ?>
                <img src="<?php bloginfo('template_url'); ?>/images/produkt.png" alt="..." fetchpriority="high" class="img-fluid archive-category-image">
            <?php endif ?>
        </div>
    <?php } ?>









</div>

<?php
if (woocommerce_product_loop()) {

    /**
     * Hook: woocommerce_before_shop_loop.
     *
     * @hooked woocommerce_output_all_notices - 10
     * @hooked woocommerce_result_count - 20
     * @hooked woocommerce_catalog_ordering - 30
     */
    do_action('woocommerce_before_shop_loop');

    woocommerce_product_loop_start();

    if (wc_get_loop_prop('total')) {
        while (have_posts()) {
            the_post();

            /**
             * Hook: woocommerce_shop_loop.
             */
            do_action('woocommerce_shop_loop');

            wc_get_template_part('content', 'product');
        }
    }

    woocommerce_product_loop_end();

    /**
     * Hook: woocommerce_after_shop_loop.
     *
     * @hooked woocommerce_pagination - 10
     */
    do_action('woocommerce_after_shop_loop');
} else {
    /**
     * Hook: woocommerce_no_products_found.
     *
     * @hooked wc_no_products_found - 10
     */
    do_action('woocommerce_no_products_found');
}
?>



<?php
/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action('woocommerce_after_main_content');

/**
 * Hook: woocommerce_sidebar.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
?>
<?php if (!is_paged() || get_query_var('paged') == 1) { ?>
    <div class="section" id="category_more_info">
        <div class="container">
            <div class="row">
                <div class="col-md-12 cmi_left">

                </div>

                <div class="col-md-12 cmi_right">

                    <?php
                    the_archive_description('<div class="taxonomy-description">', '</div>');
                    ?>

                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php
$homepage_id = get_option('page_on_front');
?>
<div id="nasze-atuty">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <span class="ornament"><img src="<?php bloginfo('template_url', $homepage_id); ?>/images/svg/ozdobnik.svg" alt="..." class="img-fluid"></span>
                <h2 class="h3 section_title"><?= get_field('atuty_category_title', $homepage_id) ?></h2>
                <p><?= get_field('atuty_opis_kategorie', $homepage_id) ?></p>
            </div>

            <?php if (have_rows('atuty', $homepage_id)): ?>
                <?php while (have_rows('atuty', $homepage_id)): the_row(); ?>

                    <div class="col-12 col-lg-4 na_item">
                        <img src="<?php the_sub_field('ikona') ?>" alt="<?php the_sub_field('naglowek') ?>">
                        <div class="na_txt">
                            <a href="<?php the_sub_field('link') ?>">
                                <span class="h5"><?php the_sub_field('naglowek') ?></span>
                            </a>
                            <p><?php the_sub_field('opis') ?></p>
                        </div>
                    </div>

                <?php endwhile; ?>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php get_template_part('template-parts/questions'); ?>

<?php if ($termscustom) : ?>
    <div class="section" id="more_links">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <p class="h5">Zobacz więcej:</p>
                    <ul class="list-unstyled">
                        <?php foreach ($termscustom as $termcustom): ?>
                            <?php get_term_by('id', $termcustom, 'product_cat') ?>
                            <?php $term_link = get_term_link($termcustom); ?>
                            <?php $term_name = get_term($termcustom)->name; ?>
                            <li>
                                <a href="<?php echo $term_link ?>" title="<?php echo $term_name; ?>">
                                    <?php echo $term_name; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php get_template_part('template-parts/newsletter'); ?>

<?php
get_footer('shop');
