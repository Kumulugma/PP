<?php


add_action('after_setup_theme', 'polskipodarek_setup');
function polskipodarek_setup()
{
    load_theme_textdomain('polskipodarek', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', array('search-form'));
    add_theme_support('woocommerce');
    global $content_width;
    if ( ! isset($content_width)) {
        $content_width = 1920;
    }
    register_nav_menus(array('main-menu' => esc_html__('Main Menu', 'polskipodarek')));
}
