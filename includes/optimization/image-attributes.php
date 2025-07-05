<?php

add_filter('wp_get_attachment_image_attributes', function ($attr, $attachment, $size) {
    if (isset($attr['fetchpriority'])) {
        unset($attr['fetchpriority']);
    }
    return $attr;
}, 10, 3);
