<?php

// Custom Nav/Menu

function wpb_custom_new_menu() {
    register_nav_menu('stopka-01', __('Przydatne linki'));
    register_nav_menu('stopka-02', __('Nasza oferta'));

    register_nav_menu('glowne-mobile', __('Menu główne - Mobile'));
    register_nav_menu('glowne-01', __('Menu główne - Lewa'));
    register_nav_menu('glowne-02', __('Menu główne - Prawa'));
//    register_nav_menu('stopka-05', __('Stopka - 5'));
}

add_action('init', 'wpb_custom_new_menu');
