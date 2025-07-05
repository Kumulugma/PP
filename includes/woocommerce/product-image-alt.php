<?php

function custom_woocommerce_template_loop_product_thumbnail_alt($html, $post_id) {
    // Pobierz nazwę produktu
    $product_title = get_the_title($post_id->id);

    // Zmień atrybut "alt" na nazwę produktu
    $html = str_replace('alt=""', 'alt="'.$product_title.'"', $html);

    return $html;
}
add_filter('woocommerce_product_get_image', 'custom_woocommerce_template_loop_product_thumbnail_alt', 10, 2);