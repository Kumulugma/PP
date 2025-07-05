<?php

add_shortcode('woocommerce_notices', function($attrs) {

    if (function_exists('wc_notice_count') && wc_notice_count() > 0) {
        ?>

        <div class="woocommerce-notices-shortcode woocommerce">
            <?php wc_print_notices(); ?>
        </div>

        <?php
    }

});
