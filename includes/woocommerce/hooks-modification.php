<?php

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);

remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

add_action('woocommerce_custom_breadcrumbs', 'woocommerce_breadcrumb', 20);

remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10);
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

//remove_action('woocommerce_cart_collaterals', 'woocommerce_catalog_ordering', 30);


add_action('woocommerce_custom_before_shop', 'woocommerce_output_all_notices', 10);
//add_action('woocommerce_custom_before_shop', 'woocommerce_result_count', 20);
//add_action('woocommerce_custom_before_shop', 'products_per_page_dropdown', 25);
add_action('woocommerce_custom_before_shop', 'woocommerce_catalog_ordering', 30);

add_action('woocommerce_ordering', 'woocommerce_catalog_ordering', 30);
