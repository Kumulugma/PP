<?php


//WooCommerce Change Title from H2 -> H3

function wps_change_products_title() {
    echo '<h3 class="h6 woocommerce-loop-product__title">'. get_the_title() . '</h3>';
}

remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);

add_action('woocommerce_shop_loop_item_title', 'wps_change_products_title', 10);

add_theme_support( 'editor-color-palette', array(
    array(
        'name'  => __( 'Primary', 'polskipodarek' ),
        'slug'  => 'primary',
        'color'	=> '#E3C47E',
    ),
    array(
        'name'  => __( 'Secondary', 'polskipodarek' ),
        'slug'  => 'secondary',
        'color' => '#571B33',
    ),
    array(
        'name'  => __( 'Secondary - Light', 'polskipodarek' ),
        'slug'  => 'secondar-light',
        'color' => '#BA2762',
    ),
) );