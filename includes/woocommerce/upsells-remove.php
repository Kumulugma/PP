<?php

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );

function remove_product_upsells() {
    if ( is_singular( 'product' ) ) {
        remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
    }
}
