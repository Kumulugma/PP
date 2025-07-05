<?php

add_action('template_redirect', function () {
    if (is_feed()) {
        header('X-Robots-Tag: noindex, nofollow', true);
    }
});
