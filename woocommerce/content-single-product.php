<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action('woocommerce_before_single_product');

if (post_password_required()) {
    echo get_the_password_form(); // WPCS: XSS ok.

    return;
}
?>

<?php do_action( 'woocommerce_custom_breadcrumbs' ); ?>

<?php if ( ! empty(get_field('product_alcohol') && get_field('product_alcohol') == 'tak')): ?>
    <?= get_template_part('woocommerce/indemi/alcohol'); ?>
<?php endif; ?>

    <div id="product-<?php the_ID(); ?>" <?php wc_product_class('col-12', $product); ?>>
        <div class="row">

            <div class="col-12 col-lg-6" id="single_product_gallery">
                <?php if ($product->is_on_sale()) : ?>

                    <?php echo apply_filters('woocommerce_sale_flash', '<span class="onsale">' . esc_html__('Sale!', 'woocommerce') . '</span>', $post, $product); ?>

                <?php endif; ?>

                <div class="owl-carousel" id="single_product_carousel">
                    <?php
                    $product_id     = $product->get_id();
                    $product        = new WC_product($product_id);
                    $attachment_ids = $product->get_gallery_image_ids();

                    foreach ($attachment_ids as $attachment_id) { ?>
                        <div class="pc_item_main">
                            <?php echo wp_get_attachment_image($attachment_id, 'single_product_img', false, ['alt' => get_the_title()]); ?>
           
                        </div>
                    <?php }
                    ?>
                </div>

                <div id="product_thumbs">
                    <ul class="dotsCustom dC_thumbs">
                        <?php
                        foreach ($attachment_ids as $attachment_id) { ?>
                            <li class="owl-custom-dot ocd_sale">
                                <?php echo wp_get_attachment_image($attachment_id, 'miniaturka', false, ['alt' => get_the_title()]); ?>
                            </li>
                        <?php }
                        ?>
                    </ul>
                </div>

            </div>

            <div class="col-12 col-lg-6" id="single_product_info">

                <!-- TITLE -->

                <?php
                $custom_title = get_field('custom_product_title');

                if ($custom_title) {
                    echo '<h1 class="product_title entry-title h3">' . esc_html($custom_title) . '</h1>';
                } else {
                    the_title('<h1 class="product_title entry-title h3">', '</h1>');
                }
                ?>

                <div id="product_availability_rating">

                    <!--                    DOSTĘPNOŚĆ-->

                    <?php if ($product->is_in_stock()) { ?>
                        <span class="stock_info instock">Dostępny</span>
                    <?php } else { ?>
                        <span class="stock_info outofstock">Niedostępny</span>
                    <?php } ?>

                    <!-- RATING -->
                    <?php
                    if ( ! wc_review_ratings_enabled()) {
                        return;
                    }
                    $rating_count = $product->get_rating_count();
                    $review_count = $product->get_review_count();
                    $average      = $product->get_average_rating();
                    if ($rating_count > 0) : ?>
                        <div class="woocommerce-product-rating">
                            <?php echo wc_get_rating_html($average, $rating_count); // WPCS: XSS ok. ?>
                            <?php if (comments_open()) : ?>
                                <?php //phpcs:disable ?>
                                <a href="#reviews" class="woocommerce-review-link" rel="nofollow">(<?php printf(_n('%s', '%s', $review_count, 'woocommerce'),
                                        '<span class="count">' . esc_html($review_count) . '</span>'); ?>)</a>
                                <?php // phpcs:enable ?>
                            <?php endif ?>
                        </div>
                    <?php endif; ?>
                </div>


                <!-- EXCERPT -->
                <?php
                $short_description = apply_filters('woocommerce_short_description', $post->post_excerpt);

                if ( ! $short_description) {
                    return;
                }
                ?>
                <div class="woocommerce-product-details__short-description">
                    <?php echo $short_description; // WPCS: XSS ok. ?>
                </div>

                <!-- PRICE -->
                <div class="add_to_cart_wrap">


                    <?php
                    /**
                     * Hook: woocommerce_single_product_summary.
                     *
                     * @hooked woocommerce_template_single_title - 5
                     * @hooked woocommerce_template_single_rating - 10
                     * @hooked woocommerce_template_single_price - 10
                     * @hooked woocommerce_template_single_excerpt - 20
                     * @hooked woocommerce_template_single_add_to_cart - 30
                     * @hooked woocommerce_template_single_meta - 40
                     * @hooked woocommerce_template_single_sharing - 50
                     * @hooked WC_Structured_Data::generate_product_data() - 60
                     */
                    do_action('woocommerce_single_product_summary');
                    ?>
                    <small>Cena:</small>

                    <!-- todo: @arek jezeli zestaw to "Cena zestawu", przycisk add to cart ma wystylowany atrybut :disabled do wykorzystania przy personalizacjach (jak na makiecie) -->

                    <p class="<?php echo esc_attr(apply_filters('woocommerce_product_price_class', 'price')); ?>"
                       data-pp="<?= wc_get_price_to_display($product) ?>">
                        <?php echo $product->get_price_html(); ?>
                    </p>
                </div>


            </div>

        </div>

        <?php
        /**
         * Hook: woocommerce_after_single_product_summary.
         *
         * @hooked woocommerce_output_product_data_tabs - 10
         * @hooked woocommerce_upsell_display - 15
         * @hooked woocommerce_output_related_products - 20
         */
        do_action('woocommerce_after_single_product_summary');
        ?>
    </div>

<?php do_action('woocommerce_after_single_product'); ?>