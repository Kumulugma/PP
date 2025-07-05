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







remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );

function remove_product_upsells() {
    if ( is_singular( 'product' ) ) {
        remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
    }
}


// includes/media/
require_once 'includes/media/thumbnails.php';
require_once 'includes/media/remove-unused-thumbs.php';

// includes/admin/
require_once 'includes/admin/category-options.php';

// includes/woocommerce/
require_once 'includes/woocommerce/remove-download.php';
require_once 'includes/woocommerce/out-of-stock-to-end.php';
require_once 'includes/woocommerce/delivery.php';
require_once 'includes/woocommerce/delivery-text-config.php';
require_once 'includes/woocommerce/customisation-labels.php';
require_once 'includes/woocommerce/hide-cod-inpost.php';
require_once 'includes/woocommerce/shortcode-product-filter.php';
require_once 'includes/woocommerce/product-image-alt.php';
require_once 'includes/woocommerce/hooks-modification.php';
require_once 'includes/woocommerce/cart-fragments.php';
require_once 'includes/woocommerce/notices-shortcode.php';
require_once 'includes/woocommerce/checkout-invoice-fields.php';
require_once 'includes/woocommerce/product-queries.php';

// includes/content/
require_once 'includes/content/breadcrumbs-home.php';
require_once 'includes/content/breadcrumbs.php';
require_once 'includes/content/title-h2-h3.php';
require_once 'includes/content/more-link.php';
require_once 'includes/content/excerpt-link.php';

// includes/optimization/
require_once 'includes/optimization/disable-emojis.php';
require_once 'includes/optimization/image-attributes.php';
require_once 'includes/optimization/pagination-links.php';

// includes/seo/
require_once 'includes/seo/htaccess-redirects.php';
require_once 'includes/seo/feed-robots.php';

// includes/theme/
require_once 'includes/theme/theme-support.php';
require_once 'includes/theme/register-nav.php';
require_once 'includes/theme/scripts.php';
require_once 'includes/theme/navigation-arrows.php';

// includes/integrations/
require_once 'includes/integrations/acf-helper.php';