<?php

add_filter('the_content_more_link', 'polskipodarek_read_more_link');
function polskipodarek_read_more_link()
{
    if ( ! is_admin()) {
        return ' <a href="' . esc_url(get_permalink()) . '" class="more-link">' . sprintf(__('...%s', 'polskipodarek'), '<span class="screen-reader-text">  ' . esc_html(get_the_title()) . '</span>') . '</a>';
    }
}