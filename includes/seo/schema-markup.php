<?php

add_filter('nav_menu_link_attributes', 'polskipodarek_schema_url', 10);
function polskipodarek_schema_url($atts)
{
    $atts['itemprop'] = 'url';

    return $atts;
}


add_action('wp_head', 'polskipodarek_pingback_header');
function polskipodarek_pingback_header()
{
    if (is_singular() && pings_open()) {
        printf('<link rel="pingback" href="%s" />' . "\n", esc_url(get_bloginfo('pingback_url')));
    }
}
