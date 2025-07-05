<?php

function move_out_of_stock_products_to_end($clauses, $query) {
    global $wpdb;

    if (is_admin() || !$query->is_main_query()) {
        return $clauses;
    }

    if (is_shop() || is_product_category() || is_product_tag()) {
        $clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} stock_status ON ({$wpdb->posts}.ID = stock_status.post_id AND stock_status.meta_key = '_stock_status')";
        $clauses['orderby'] = "(CASE WHEN stock_status.meta_value = 'outofstock' THEN 1 ELSE 0 END), " . $clauses['orderby'];
    }

    return $clauses;
}
add_filter('posts_clauses', 'move_out_of_stock_products_to_end', 10, 2);