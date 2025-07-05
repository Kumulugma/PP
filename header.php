<?php //if (!in_array(@$_SERVER['REMOTE_ADDR'], array('46.149.211.64', '46.149.211.77', '46.149.222.5', '89.64.47.200')))
//{
//    header('Location: https://polskipodarek.pl/');
//    exit;
//} ?>
<!---->
<?php
////echo 'User IP Address - '.$_SERVER['REMOTE_ADDR'];
////?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>"/>
    <meta name="viewport" content="width=device-width"/>
<meta name="google-site-verification" content="EOkVZ0avFbUSx8HC9XQ0FpjLBlr7pSEqqeQenWXkX00" />
    <?php wp_head(); ?>
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/bootstrap.css?v=1.1">
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/style.css?v=1.25">
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/override.css?v=1.01">
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/fontawesome/css/all.min.css?v=1.13222">




    <meta name="theme-color" content="#252525">
</head>
<body <?php body_class(); ?>>
<?php //wp_body_open(); ?>

<?php //bloginfo('template_url');
?>


<a href="#" id="scroll_to_top">
    <svg viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M8.99997 15V3.82998L13.88 8.70998C14.27 9.09998 14.91 9.09998 15.3 8.70998C15.69 8.31998 15.69 7.68998 15.3 7.29998L8.70997 0.70998C8.31997 0.31998 7.68997 0.31998 7.29997 0.70998L0.699971 7.28998C0.309971 7.67998 0.309971 8.30998 0.699971 8.69998C1.08997 9.08998 1.71997 9.08998 2.10997 8.69998L6.99997 3.82998V15C6.99997 15.55 7.44997 16 7.99997 16C8.54997 16 8.99997 15.55 8.99997 15Z" fill="#571B33"/>
    </svg>
</a>

<header id="main_header">
    <div id="top_bar">
        <div class="container container-wide">
            <div class="row">
                <div class="col-12 col-md-6 top_bar_left">
                    <p class="small">Polski Podarek - Regionalne Zestawy Prezentowe&nbsp</p>
                    <p class="small"> • Masz pytania? <a href="tel:+48502695166" title="Masz pytania? Zadzwoń do nas.">+48 502 695 166</a></p>
                </div>

                <div class="col-12 col-md-6 top_bar_right">
                    <ul class="list-unstyled">
                        <li class="tbr_link login_link"><a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>">Zaloguj się</a></li>
                        <li class="tbr_link register_link"><a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>">Zarejestruj się</a></li>
                        <li class="tbr_link" id="search_desktop">
                            <a href="#">
                                <img src="<?php bloginfo('template_url'); ?>/images/svg/search.svg" alt="Wyszukaj">
                                Wyszukaj w sklepie
                            </a>
                        </li>

                        <?php if(WC()->cart->get_cart_contents_count() > 0) { ?>
                            <li class="tbr_link cart_link full_cart_wrap">
                                <div class="tbr_inner" id="full_cart_btn" title="<?php _e('Zobacz zawartość swojego koszyka','polski-podarek'); ?>">
                                    <img src="<?php bloginfo('template_url'); ?>/images/svg/cart.svg" alt="Koszyk">
                                    <div class="cart-customlocation" title="<?php _e( 'View your shopping cart' ); ?>"><?php echo sprintf ( _n( '%d', '%d', WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ); ?></div>

                                </div>



                                <div class="mini_cart_wrap">
                                    <?php woocommerce_mini_cart(); ?>
                                </div>
                            </li>
                        <?php } else { ?>
                            <li class="tbr_link cart_link empty_cart_wrap">
                                <div class="tbr_inner" id="empty_cart_btn">
                                    <img src="<?php bloginfo('template_url'); ?>/images/svg/cart.svg" alt="">
                                </div>
                                <div class="empty_cart_info">
                                    <h5>Twój koszyk jest pusty</h5>
                                    <p>Gdy dodasz produkty do koszyka, tutaj pojawi się jego zawartość.</p>
                                    <a href="/sklep" class="btn btn-primary btn-full">Odwiedź nasz sklep on-line</a>
                                </div>
                            </li>
                        <?php } ?>
                        <li class="tbr_link"><a href="<?php echo wc_get_cart_url(); ?>">Koszyk</a></li>


                        <li><span class="split_list"></span></li>
                        <li class="tbr_link"><a href="https://www.facebook.com/polskipodarek/" target="_blank"><img src="<?php bloginfo('template_url'); ?>/images/svg/fb.svg" alt=""></a></li>
                        <li class="tbr_link"><a href="https://www.instagram.com/polskipodarek/" target="_blank"><img src="<?php bloginfo('template_url'); ?>/images/svg/ig.svg" alt=""></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!--    <span class="mn_shadow"></span>-->
    <div id="main_nav">
        <div class="container container-wide">
            <div class="row">
                <nav class="col-12 nav_inner">

                    <div class="mobile_nav mobile_left">
                        <ul class="list-unstyled">

                            <li class="" id="close_mm">
                                <img src="<?php bloginfo('template_url'); ?>/images/svg/close.svg" alt="X" title="Zamknij">
                            </li>
                            <li class="mm_item" id="mm_trigger">
                                <img src="<?php bloginfo('template_url'); ?>/images/svg/menu.svg" alt="Menu" title="Menu">
                            </li>
                            <li class="mm_item" id="search_trigger">
                                <img src="<?php bloginfo('template_url'); ?>/images/svg/search.svg" alt="Wyszukaj" title="Wyszukaj">
                            </li>

                        </ul>
                    </div>

                    <?php wp_nav_menu(
                        array(
                            'theme_location' => 'glowne-01',
                            'container' => false,
                            'items_wrap' => '<ul id="%1$s" class="list-unstyled main_nav_left" itemscope>%3$s</ul>'
                        )
                    ); ?>

                    <div class="logo_nav">
                        <a href="/" title="Strona główna">
                            <img src="<?php bloginfo('template_url'); ?>/images/logo.png" alt="Polski Podarek" class="img-fluid">
                        </a>
                    </div>

                    <div class="mobile_nav mobile_right">
                        <ul class="list-unstyled">
                            <li class="mm_item" >
                                <a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>">
                                    <img src="<?php bloginfo('template_url'); ?>/images/svg/user.svg" alt="Konto">
                                </a>
                            </li>

                            <?php if(WC()->cart->get_cart_contents_count() > 0) { ?>
                                <li class="mm_item"  id="cart_trigger">
                                    <img src="<?php bloginfo('template_url'); ?>/images/svg/cart.svg" alt="Koszyk">
                                    <div class="cart-customlocation" title="<?php _e( 'View your shopping cart' ); ?>"><?php echo sprintf ( _n( '%d', '%d', WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ); ?></div>
                                </li>
                            <?php } else { ?>
                                <li class="mm_item" >
                                    <img src="<?php bloginfo('template_url'); ?>/images/svg/cart.svg" alt="Koszyk">
                                </li>
                            <?php } ?>

                        </ul>
                    </div>

                    <?php wp_nav_menu(
                        array(
                            'theme_location' => 'glowne-02',
                            'container' => false,
                            'items_wrap' => '<ul id="%1$s" class="list-unstyled main_nav_right" itemscope>%3$s</ul>'
                        )
                    ); ?>

                </nav>
            </div>
        </div>
    </div>

    <div class="mobile_nav_element" id="mm_cart">
        <div class="mini_cart_wrap">
            <?php woocommerce_mini_cart(); ?>
        </div>
    </div>

    <div class="mobile_nav_element" id="mm_wrap">
        <h4 class="h5">Nasza oferta:</h4>
        <?php wp_nav_menu(
            array(
                'theme_location' => 'glowne-mobile',
                'container' => false,
                'items_wrap' => '<ul id="%1$s" class="list-unstyled main_nav_left" itemscope>%3$s</ul>'
            )
        ); ?>
    </div>

    <div class="mobile_nav_element" id="mm_search">
        <h4 class="h5">Wyszukaj w sklepie</h4>

        <form action="<?php echo home_url('/'); ?>" method="get" class="search_form">
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
    </div>

</header>






