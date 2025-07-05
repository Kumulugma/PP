<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
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

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>

<?php if (is_product_category() || is_shop()) { ?>

    <?php if ($product->is_in_stock()) { ?>
        <div <?php wc_product_class( 'col-12 col-sm-6 col-lg-4 product_box is-in-stock', $product ); ?>>
            <span class="stock_info instock">Dostępny</span>
    <?php } else { ?>
        <div <?php wc_product_class( 'col-12 col-sm-6 col-lg-4 product_box out-of-stock', $product ); ?>>
            <span class="stock_info outofstock">Niedostępny</span>
    <?php } ?>

<?php } else { ?>

            <?php if ($product->is_in_stock()) { ?>
                <div <?php wc_product_class( 'col-12 col-sm-6 col-lg-3 product_box is-in-stock', $product ); ?>>
                <span class="stock_info instock">Dostępny</span>
            <?php } else { ?>
                <div <?php wc_product_class( 'col-12 col-sm-6 col-lg-3 product_box out-of-stock', $product ); ?>>
                <span class="stock_info outofstock">Niedostępny</span>
            <?php } ?>

<?php } ?>




	<?php
	/**
	 * Hook: woocommerce_before_shop_loop_item.
	 *
	 * @hooked woocommerce_template_loop_product_link_open - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item' ); ?>

	<?php /**
	 * Hook: woocommerce_before_shop_loop_item_title.
	 *
	 * @hooked woocommerce_show_product_loop_sale_flash - 10
	 * @hooked woocommerce_template_loop_product_thumbnail - 10
	 */
	do_action( 'woocommerce_before_shop_loop_item_title' ); ?>

	<?php /**
	 * Hook: woocommerce_shop_loop_item_title.
	 *
	 * @hooked woocommerce_template_loop_product_title - 10
	 */
	do_action( 'woocommerce_shop_loop_item_title' ); ?>

        <small class="d-block mb-1 mt-2" style="padding-bottom: 10px"><?= get_field('seo_text')?></small>

        <?php if ( $price_html = $product->get_price_html() ) : ?>
            <span class="price"><?php echo $price_html; ?></span>
        <?php endif; ?>

    <?php /**
     * Hook: woocommerce_after_shop_loop_item.
     *
     * @hooked woocommerce_template_loop_product_link_close - 5
     * @hooked woocommerce_template_loop_add_to_cart - 10
     */
    do_action( 'woocommerce_after_shop_loop_item' );
    ?>

</div>
