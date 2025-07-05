<?php

add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

function themesharbor_disable_woocommerce_block_styles() {
    wp_dequeue_style( 'wc-blocks-style' );
}
add_action( 'wp_enqueue_scripts', 'themesharbor_disable_woocommerce_block_styles' );


function smartwp_remove_wp_block_library_css(){
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'wc-blocks-style' ); // Remove WooCommerce block CSS
}
add_action( 'wp_enqueue_scripts', 'smartwp_remove_wp_block_library_css', 100 );

add_action('wp_enqueue_scripts', 'polskipodarek_enqueue');

function polskipodarek_enqueue()
{
    wp_enqueue_style('polskipodarek-style', get_stylesheet_uri());
    wp_enqueue_script('jquery');
}

