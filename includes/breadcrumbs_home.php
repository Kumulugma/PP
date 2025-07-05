<?php

add_filter( 'woocommerce_breadcrumb_defaults', 'woo_change_breadcrumb_home_text' );
/**
 * Change the breadcrumb home text from "Home" to "Shop".
 * @param  array $defaults The default array items.
 * @return array           Modified array
 */
function woo_change_breadcrumb_home_text( $defaults ) {
    $defaults['home'] = 'Sklep';

    return $defaults;
}

add_filter( 'woocommerce_breadcrumb_home_url', 'woo_custom_breadrumb_home_url' );
/**
 * Change the breadcrumb home link URL from / to /shop.
 * @return string New URL for Home link item.
 */
function woo_custom_breadrumb_home_url() {
    return '/sklep/';
}