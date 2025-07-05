<?php
/**
 * Product Loop Start
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-start.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.3.0
 */

if ( ! defined('ABSPATH')) {
    exit;
}
?>

<div class="clear"></div>

<div class="row">
    <?php if (is_product_category() || is_shop()) { ?>
    <div class="col-sm-4 col-md-3" id="pl_sidebar">

        <?php /*
 <span id="close_filters"><svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.99997 15V3.82998L13.88 8.70998C14.27 9.09998 14.91 9.09998 15.3 8.70998C15.69 8.31998 15.69 7.68998 15.3 7.29998L8.70997 0.70998C8.31997 0.31998 7.68997 0.31998 7.29997 0.70998L0.699971 7.28998C0.309971 7.67998 0.309971 8.30998 0.699971 8.69998C1.08997 9.08998 1.71997 9.08998 2.10997 8.69998L6.99997 3.82998V15C6.99997 15.55 7.44997 16 7.99997 16C8.54997 16 8.99997 15.55 8.99997 15Z" fill="#571B33"/>
    </svg></span>
 */ ?>

        <span id="close_filters">
            <img src="<?php bloginfo('template_url'); ?>/images/svg/close.svg" alt="X" title="Zamknij">
        </span>

        <p class="h5 mb-3">Filtruj produkty</p>

        <form action="<?php echo home_url('/'); ?>" method="get" class="search_form_mobile">
            <input
                    type="text"
                    id="searchInput"
                    name="s"
                    placeholder="<?php _e('Jakiego prezentu szukasz?','polski-podarek'); ?>"
                    value="<?php the_search_query(); ?>"
                    aria-label="<?php _e('Search','polski-podarek'); ?>"
            >
            <button type="submit">
                <img src="<?php bloginfo('template_url'); ?>/images/svg/search.svg" alt="Wyszukaj" title="Wyszukaj">
            </button>
        </form>


        <?php if ( is_active_sidebar( 'primary-widget-area' ) ) : ?>
        <ul class="sidebar_wrap">
            <?php dynamic_sidebar( 'primary-widget-area' ); ?>
        </ul>

        <?php endif; ?>
    </div>
    <div class="col-sm-12 col-md-9">
    <?php } else { ?>
        
        <div class="col-12">
    <?php } ?>
        <div class="row" id="product_list">
            <div class="custom_list_header">
                <div id="mobile_filters_trigger">
                    <svg viewBox="0 0 18 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8 12H10C10.55 12 11 11.55 11 11C11 10.45 10.55 10 10 10H8C7.45 10 7 10.45 7 11C7 11.55 7.45 12 8 12ZM0 1C0 1.55 0.45 2 1 2H17C17.55 2 18 1.55 18 1C18 0.45 17.55 0 17 0H1C0.45 0 0 0.45 0 1ZM4 7H14C14.55 7 15 6.55 15 6C15 5.45 14.55 5 14 5H4C3.45 5 3 5.45 3 6C3 6.55 3.45 7 4 7Z" fill="#BA2762"/>
                    </svg><span>Filtruj produkty</span>
                </div>
                <?php do_action( 'woocommerce_custom_before_shop' ); ?>
            </div>
            <!--<div class="products row columns---><?php //echo esc_attr( wc_get_loop_prop( 'columns' ) ); ?><!--">-->