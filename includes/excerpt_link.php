<?php


add_filter('excerpt_more', 'polskipodarek_excerpt_read_more_link');
function polskipodarek_excerpt_read_more_link($more)
{
    if ( ! is_admin()) {
        global $post;

        return ' <a href="' . esc_url(get_permalink($post->ID)) . '" class="more-link">' . sprintf(__('...%s', 'polskipodarek'), '<span class="screen-reader-text">  ' . esc_html(get_the_title()) . '</span>') . '</a>';
    }
}