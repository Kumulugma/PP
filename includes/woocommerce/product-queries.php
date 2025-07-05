<?php



add_filter( 'woocommerce_shortcode_products_query', function ( $query_args, $atts ) {
	if ( $atts['class'] == 'outofstock' ) {
		$query_args['meta_query'] = [
			[
				'key'     => '_stock_status',
				'value'   => 'outofstock',
				'compare' => 'NOT LIKE',
			]
		];
	}

	return $query_args;
}, 10, 2 );

add_filter( 'woocommerce_product_query_meta_query', function ( array $meta_query ): array {
	$pt = explode(',', filter_input( INPUT_GET, "filter_pt", FILTER_SANITIZE_STRING ));

	if ( is_shop() || is_product_category() ) {
		if (in_array('sale', $pt)) {
			$meta_query[] = [
				'key'     => '_sale_price',
				'value'   => '',
				'compare' => '!='
			];
		}
	}

	return $meta_query;
} );

add_filter( 'woocommerce_product_query_tax_query', function ( array $meta_query ): array {
	$pt = explode(',', filter_input( INPUT_GET, "filter_pt", FILTER_SANITIZE_STRING ));

	if ( is_shop() || is_product_category() ) {
        if (in_array('pers', $pt)) {
	        $meta_query[] = [
		        'taxonomy' => 'product_cat',
		        'field'    => 'term_id',
		        'terms'    => 315,
		        'operator' => 'IN',
	        ];
        } elseif (in_array('sellout', $pt)) {
	        $meta_query[] = [
		        'taxonomy' => 'product_cat',
		        'field'    => 'term_id',
		        'terms'    => 317,
		        'operator' => 'IN',
	        ];
        }
	}

	return $meta_query;
} );
