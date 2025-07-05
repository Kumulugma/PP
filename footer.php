<footer>
    <div class="foot_main">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 foot_info">
                    <a href="/">
                        <img src="<?php bloginfo('template_url'); ?>/images/logo_foot.png" class="foot_logo" alt="Polski Podarek">
                    </a>
                    <p class="h5">Polski Podarek Sp. z o.o.</p>
                    <p>Jareniówka 163, 38-200 Jasło<br>
                        NIP: 685 233 98 15<br>
                        Nr konta: 94 1020 2892 0000 5702 0737 2263 </p>

                    <ul class="list-unstyled foot_social">
                        <li>
                            <a href="https://www.facebook.com/polskipodarek/" target="_blank">
                                <img src="<?php bloginfo('template_url'); ?>/images/svg/fb_foot.svg" alt="">
                            </a>
                        </li>
                        <li>
                            <a href="https://www.instagram.com/polskipodarek/" target="_blank">
                                <img src="<?php bloginfo('template_url'); ?>/images/svg/ig_foot.svg" alt="">
                            </a>
                        </li>
                    </ul>

                    <p class="h5">Skontaktuj się z nami</p>
                    <p><a href="mailto:kontakt@polskipodarek.pl">kontakt@polskipodarek.pl</a><br>
                        <a href="tel:+48502695166">+48 502 695 166</a>
                    </p>

                    <a href="mailto:kontakt@polskipodarek.pl" class="btn btn-primary btn-icon btn-full"><span><img src="<?php bloginfo('template_url'); ?>/images/svg/mail_outline_secondary.svg" alt=""></span>Napisz do nas</a>

                </div>

                <div class="col-lg-3 foot_links">
                    <p class="h5">Przydatne linki</p>
                    <?php wp_nav_menu(
                        array(
                            'theme_location' => 'stopka-01',
                            'container'      => false,
                            'items_wrap'     => '<ul id="%1$s" class="list-unstyled" itemscope>%3$s</ul>'
                        )
                    ); ?>
                </div>

                <div class="col-lg-3 foot_links">
                    <p class="h5">Nasza oferta</p>
                    <?php wp_nav_menu(
                        array(
                            'theme_location' => 'stopka-02',
                            'container'      => false,
                            'items_wrap'     => '<ul id="%1$s" class="list-unstyled" itemscope>%3$s</ul>'
                        )
                    ); ?>
                </div>

                <div class="col-lg-3 foot_other">
                    <p class="h5">Akceptujemy płatności</p>
                    <ul class="list-unstyled">
                        <li><img src="<?php bloginfo('template_url'); ?>/images/logo-dostawa-01.png" alt=""></li>
                        <li><img src="<?php bloginfo('template_url'); ?>/images/logo-dostawa-02.png" alt=""></li>
                        <li><img src="<?php bloginfo('template_url'); ?>/images/logo-dostawa-03.png" alt=""></li>
                        <li><img src="<?php bloginfo('template_url'); ?>/images/logo-dostawa-04.png" alt=""></li>
                    </ul>

                    <p class="h5">Nasi partnerzy</p>
                    <ul class="list-unstyled">
                        <li><img src="<?php bloginfo('template_url'); ?>/images/inpost.png" alt=""></li>
                        <li><img src="<?php bloginfo('template_url'); ?>/images/dhl.png" alt=""></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>


    <div class="bottom">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center copyrights">
                    <p><?php echo esc_html(date_i18n(__('Y', 'polskipodarek'))); ?> © <?php echo esc_html(get_bloginfo('name')); ?><span>|</span> Wszelkie prawa zastrzeżone</p>
                </div>
                <div class="col-12 text-center seo_links">
                    <p>Zobacz więcej:
                        <?php if (have_rows('linki_seo' ,50)): ?>
                            <?php while (have_rows('linki_seo', 50)): the_row(); ?>
                                <a href="<?php the_sub_field('link') ?>" title="<?php the_sub_field('opis') ?>"><?php the_sub_field('opis') ?></a>&nbsp;|
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>

<?php
if (is_product()) { ?>

    
    <script src="<?php bloginfo('template_url'); ?>/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/node_modules/owl.carousel/dist/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/node_modules/owl.carousel/dist/assets/owl.theme.default.min.css">
    <script src="<?php bloginfo('template_url'); ?>/node_modules/owl.carousel/dist/owl.carousel.min.js"></script>

    <script src="<?php bloginfo('template_url'); ?>/product.js?v=1.35"></script>

<?php }
if (is_page('najczesciej-zadawane-pytania')) { ?>

    <script src="<?php bloginfo('template_url'); ?>/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php bloginfo('template_url'); ?>/main.js?v=1.36"></script>
    <script src="<?php bloginfo('template_url'); ?>/faq.js?v=1.35"></script>


<?php } else { ?>

    <script src="<?php bloginfo('template_url'); ?>/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/node_modules/owl.carousel/dist/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/node_modules/owl.carousel/dist/assets/owl.theme.default.min.css">
    <script src="<?php bloginfo('template_url'); ?>/node_modules/owl.carousel/dist/owl.carousel.min.js"></script>
    <script src="<?php bloginfo('template_url'); ?>/main.js?v=1.36"></script>

    <script>
        jQuery(document).ready(function () {
            jQuery('.hero_slider').owlCarousel({
                loop: true,
                items: 1,
                autoplay:true,
                autoplayTimeout:3000,
                autoplayHoverPause:true
            });
        });
    </script>

<?php } ?>

<!--<script src="https://cdn.jsdelivr.net/gh/orestbida/cookieconsent@v2.8.0/dist/cookieconsent.js"></script>-->
<!--<script src="--><?php //bloginfo('template_url'); ?><!--/js/cookieconsent-init.js"></script>-->
<!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/orestbida/cookieconsent@v2.8.0/dist/cookieconsent.css">-->

</body>
</html>