<?php


add_filter('big_image_size_threshold', '__return_false');
add_filter('intermediate_image_sizes_advanced', 'polskipodarek_image_insert_override');
function polskipodarek_image_insert_override($sizes)
{
    unset($sizes['medium_large']);
    unset($sizes['1536x1536']);
    unset($sizes['2048x2048']);

    return $sizes;
}
