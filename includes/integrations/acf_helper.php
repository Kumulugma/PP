<?php

function acf_image_url($image_field_id) {

    if ($image_field_id) {
        $image = wp_get_attachment_image_src($image_field_id, 'full');
        if ($image) {
            $image_url = $image[0];
            return $image_url;
        } else {
            return null;
        }
    }
        return null;
}

function acf_image_alt($image_field_id){
    if ($image_field_id) {
    $image = wp_get_attachment_image_src($image_field_id, 'full');
    $image_alt = get_post_meta($image_field_id, '_wp_attachment_image_alt', true);

    if ($image && $image_alt) {
            return $image_alt;
        } else {
            return null;
        }
    }
        return null;
}