<?php


add_filter('show_admin_bar', '__return_false');

add_action('wp_footer', 'polskipodarek_footer');
function polskipodarek_footer()
{
    ?>
    <script>
        jQuery(document).ready(function ($) {
            var deviceAgent = navigator.userAgent.toLowerCase();
            if (deviceAgent.match(/(iphone|ipod|ipad)/)) {
                $("html").addClass("ios");
            }
            if (navigator.userAgent.search("MSIE") >= 0) {
                $("html").addClass("ie");
            } else if (navigator.userAgent.search("Chrome") >= 0) {
                $("html").addClass("chrome");
            } else if (navigator.userAgent.search("Firefox") >= 0) {
                $("html").addClass("firefox");
            } else if (navigator.userAgent.search("Safari") >= 0 && navigator.userAgent.search("Chrome") < 0) {
                $("html").addClass("safari");
            } else if (navigator.userAgent.search("Opera") >= 0) {
                $("html").addClass("opera");
            }
        });
    </script>
    <?php
}

add_filter('document_title_separator', 'polskipodarek_document_title_separator');
function polskipodarek_document_title_separator($sep)
{
    $sep = '|';

    return $sep;
}

add_filter('the_title', 'polskipodarek_title');
function polskipodarek_title($title)
{
    if ($title == '') {
        return '...';
    } else {
        return $title;
    }
}

add_filter('nav_menu_link_attributes', 'polskipodarek_schema_url', 10);
function polskipodarek_schema_url($atts)
{
    $atts['itemprop'] = 'url';

    return $atts;
}

add_action('widgets_init', 'polskipodarek_widgets_init');

function polskipodarek_widgets_init()
{
    register_sidebar(array(
        'name'          => esc_html__('Sidebar Widget Area', 'polskipodarek'),
        'id'            => 'primary-widget-area',
        'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
        'after_widget'  => '</li>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}

add_action('wp_head', 'polskipodarek_pingback_header');
function polskipodarek_pingback_header()
{
    if (is_singular() && pings_open()) {
        printf('<link rel="pingback" href="%s" />' . "\n", esc_url(get_bloginfo('pingback_url')));
    }
}

add_action('comment_form_before', 'polskipodarek_enqueue_comment_reply_script');
function polskipodarek_enqueue_comment_reply_script()
{
    if (get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}

function polskipodarek_custom_pings($comment)
{
    ?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>"><?php echo esc_url(comment_author_link()); ?></li>
    <?php
}

add_filter('get_comments_number', 'polskipodarek_comment_count', 0);
function polskipodarek_comment_count($count)
{
    if ( ! is_admin()) {
        global $id;
        $get_comments     = get_comments('status=approve&post_id=' . $id);
        $comments_by_type = separate_comments($get_comments);

        return count($comments_by_type['comment']);
    } else {
        return $count;
    }
}

add_action( 'woocommerce_after_add_to_cart_quantity', 'mystore_display_quantity_plus' );

function mystore_display_quantity_plus() {
    echo '<button type="button" class="btn btn-secondary quant_btn plus" >+</button>';
}

add_action( 'woocommerce_before_add_to_cart_quantity', 'mystore_display_quantity_minus' );


function mystore_display_quantity_minus() {
    echo '<button type="button" class="btn btn-secondary quant_btn minus" >-</button>';
}


add_action( 'wp_footer', 'mystore_add_cart_quantity_plus_minus' );

function mystore_add_cart_quantity_plus_minus() {
    // Only run this on the single product page
    if ( ! is_product() ) return;
    ?>
    <script type="text/javascript">

        jQuery(document).ready(function($){

            $('form.cart').on( 'click', 'button.plus, button.minus', function() {

// Get current quantity values
                var qty = $( this ).closest( 'form.cart' ).find( '.qty' );
                var val = parseFloat(qty.val());
                var max = parseFloat(qty.attr( 'max' ));
                var min = parseFloat(qty.attr( 'min' ));
                var step = parseFloat(qty.attr( 'step' ));

// Change the value if plus or minus
                if ( $( this ).is( '.plus' ) ) {
                    if ( max && ( max <= val ) ) {
                        qty.val( max );
                    } else {
                        qty.val( val + step );
                    }
                } else {
                    if ( min && ( min >= val ) ) {
                        qty.val( min );
                    } else if ( val > 1 ) {
                        qty.val( val - step );
                    }
                }
            });

        });

    </script>
    <?php
}

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

/**
 * Show cart contents / total Ajax
 */
add_filter( 'woocommerce_add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment' );

function woocommerce_header_add_to_cart_fragment( $fragments ) {
    global $woocommerce;

    ob_start();

    ?>
    <div class="cart-customlocation" title="<?php _e('View your shopping cart', 'woothemes'); ?>"><?php echo sprintf(_n('%d', '%d', $woocommerce->cart->cart_contents_count, 'woothemes'), $woocommerce->cart->cart_contents_count);?></div>
    <?php
    $fragments['div.cart-customlocation'] = ob_get_clean();
    return $fragments;
}


add_shortcode('woocommerce_notices', function($attrs) {

    if (function_exists('wc_notice_count') && wc_notice_count() > 0) {
        ?>

        <div class="woocommerce-notices-shortcode woocommerce">
            <?php wc_print_notices(); ?>
        </div>

        <?php
    }

});


add_filter( 'walker_nav_menu_start_el', 'add_arrow',10,4);
function add_arrow( $output, $item, $depth, $args ){

//Only add class to 'top level' items on the 'primary' menu.
    if($depth === 0 ){
        if (in_array("menu-item-has-children", $item->classes)) {
            $output .='<span class="nav-click"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-down-fill" viewBox="0 0 16 16">
  <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
</svg></span>';
        }
    }
    return $output;
}

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );

function remove_product_upsells() {
    if ( is_singular( 'product' ) ) {
        remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
    }
}

add_filter('woocommerce_checkout_fields', 'addBootstrapToCheckoutFields' );
function addBootstrapToCheckoutFields($fields) {
    foreach ($fields as &$fieldset) {
        foreach ($fieldset as &$field) {
            // if you want to add the form-group class around the label and the input
            $field['class'][] = 'form-group';

            // add form-control to the actual input
            $field['input_class'][] = 'form-control';
        }
    }
    return $fields;
}

add_filter( 'woocommerce_checkout_fields', function ( $fields ) {


    $fields['billing']['billing_nip'] = [
        'label'    => __( 'NIP', 'woocommerce' ),
        'required' => false,
        'label_class' => '',
        'input_class' => array( 'form-control' ),
        'placeholder' => 'NIP',
        'clear'    => true
    ];

    $fields['billing']['billing_company_name'] = [
        'label'    => __( 'Nazwa firmy', 'woocommerce' ),
        'required' => false,
        'label_class' => '',
        'input_class' => array( 'form-control' ),
        'placeholder' => 'Nazwa firmy',
        'clear'    => true
    ];

    $fields['billing']['billing_need_invoice'] = [
        'type'     => 'checkbox',
        'label'    => __( 'Chcę otrzymać fakturę', 'woocommerce' ),
        'required' => false,
        'label_class' => '',
        'clear'    => true
    ];

    return $fields;
} );

add_action( 'woocommerce_order_details_after_customer_details', function ( $order ) {
    $invoice     = get_post_meta( $order->get_id(), '_billing_need_invoice', true );
    $nip         = get_post_meta( $order->get_id(), '_billing_nip', true );
    $companyname = get_post_meta( $order->get_id(), '_billing_company_name', true );

    echo '<div class="woocommerce-customer-details">';
    if ( $invoice ) {
        echo '<div><strong>Chcę otrzymać fakturę:</strong> Tak</div>';
    }

    if ( $nip ) {
        echo '<div><strong>NIP:</strong> ' . esc_html( $nip ) . '</div>';
    }

    if ( $companyname ) {
        echo '<div><strong>Nazwa firmy:</strong> ' . esc_html( $companyname ) . '</div>';
    }
    echo ' </div>';
} );


add_action( 'woocommerce_checkout_update_order_meta', function ( $order_id ) {
    if ( isset( $_POST['billing_need_invoice'] ) ) {
        update_post_meta( $order_id, '_billing_need_invoice', 'Tak' );
    }

    if ( ! empty( $_POST['billing_nip'] ) ) {
        update_post_meta( $order_id, '_billing_nip', sanitize_text_field( $_POST['billing_nip'] ) );
    }

    if ( ! empty( $_POST['billing_company_name'] ) ) {
        update_post_meta( $order_id, '_billing_company_name', sanitize_text_field( $_POST['billing_company_name'] ) );
    }
} );

add_action( 'woocommerce_admin_order_data_after_billing_address', function ( $order ) {
    echo '<div><strong>' . __( 'Chcę otrzymać fakturę' ) . ':</strong> ' .
        ( get_post_meta( $order->get_id(), '_billing_need_invoice', true ) ? 'Tak' : 'Nie' ) . '</div>';

    echo '<div><strong>' . ( 'NIP' ) . ':</strong> '
        . get_post_meta( $order->get_id(), '_billing_nip', true ) . '</div>';

    echo '<div><strong>' . ( 'Nazwa firmy' ) . ':</strong> ' .
        get_post_meta( $order->get_id(), '_billing_company_name', true ) . '</div>';

} );

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



// Rejestracja miniaturek
require_once 'includes/thumbnails.php';
// Category options
require_once 'includes/_category-options.php';
// Usunięcie końcówki pobrane z panelu użytkownika
require_once 'includes/remove_download.php';
// Przesunięcie wyprzedanych na koniec, przy domyślnym filtrze
require_once 'includes/out_of_stock_to_end.php';
// Zmiana domyślnego okruszka Home
require_once 'includes/breadcrumbs_home.php';
// Okruszki
require_once 'includes/breadcrumbs.php';
// Zmian typu nagłówka w nazwach produktów z h2 na h3
require_once 'includes/title_h2_h3.php';
// Filtr na stronie produktów
require_once 'includes/shortcode_product_filter.php';
// Usunięcie emojis
require_once 'includes/disable_emojis.php';
// Wsparcie szablonu
require_once 'includes/theme_support.php';
// Rejestracja dodatkowych menu
require_once 'includes/register_nav.php';
// Zakładka - dostawa
require_once 'includes/delivery.php';
// Link - czytaj więcej
require_once 'includes/more_link.php';
// Linki - więcej (excerpt)
require_once 'includes/excerpt_link.php';
// Usunięcie nieużywanych miniaturek
require_once 'includes/remove_unused_thumbs.php';
// Rejestracja i derejestracja skryptów
require_once 'includes/scripts.php';
// Konfiguracja podstrony dostaw
require_once 'includes/delivery_text_config.php';
// Konfiguracja etykiet personalizacji
require_once 'includes/customisation_labels.php';
// Wrappery do obrazków ACF
require_once 'includes/acf_helper.php';
// Wrappery do obrazków ACF
require_once 'includes/hide_cod_inpost.php';

function custom_woocommerce_template_loop_product_thumbnail_alt($html, $post_id) {
    // Pobierz nazwę produktu
    $product_title = get_the_title($post_id->id);

    // Zmień atrybut "alt" na nazwę produktu
    $html = str_replace('alt=""', 'alt="'.$product_title.'"', $html);

    return $html;
}
add_filter('woocommerce_product_get_image', 'custom_woocommerce_template_loop_product_thumbnail_alt', 10, 2);

function update_redirects_in_htaccess_daily() {
    // Ścieżka do pliku .htaccess
    $htaccess_file = ABSPATH . '.htaccess';

    // Nowe reguły przekierowań
    $redirects = "
# BEGIN Custom Redirects
RewriteEngine On
RewriteRule ^kategoria-produktu/([a-z,A-Z,0-9\-\/]+)$ /$1 [L,R=301]
# END Custom Redirects
";

            // Sprawdź ostatnią aktualizację i treść reguł
    $last_update = get_option('htaccess_redirects_last_update', 0);
    $stored_redirects = get_option('htaccess_redirects_content', '');

    // Aktualizuj, jeśli minęło więcej niż 12 godziny lub treść reguł się zmieniła
    if (time() - $last_update > 43200 || $stored_redirects !== $redirects) {
        if (is_writable($htaccess_file)) {
            // Odczyt istniejącego pliku .htaccess
            $htaccess_content = file_get_contents($htaccess_file);

            // Usuń istniejące reguły w bloku "Custom Redirects"
            $pattern = '/# BEGIN Custom Redirects.*?# END Custom Redirects/s';
            $htaccess_content = preg_replace($pattern, '', $htaccess_content);

            // Dodaj nowe reguły na końcu pliku
            $htaccess_content .= "\n" . $redirects;

            // Zapisz zaktualizowaną treść pliku .htaccess
            file_put_contents($htaccess_file, $htaccess_content);

            // Zapisz czas ostatniej aktualizacji i nowe reguły w bazie danych
            update_option('htaccess_redirects_last_update', time());
            update_option('htaccess_redirects_content', $redirects);
        } else {
            // Loguj błąd, jeśli plik .htaccess nie jest zapisywalny
            error_log('.htaccess is not writable.');
        }
    }
}
add_action('init', 'update_redirects_in_htaccess_daily');

add_action('template_redirect', function () {
    if (is_feed()) {
        header('X-Robots-Tag: noindex, nofollow', true);
    }
});


add_filter('wp_get_attachment_image_attributes', function ($attr, $attachment, $size) {
    if (isset($attr['fetchpriority'])) {
        unset($attr['fetchpriority']);
    }
    return $attr;
}, 10, 3);

add_filter('paginate_links', function($link) {
    $pos = strpos($link, 'page/1/');
    if($pos !== false) {
        $link = substr($link, 0, $pos);
    }
    return $link;
});