<?php

/**
 * Funkcjonalności Polski Podarek
 * Implementacja funkcji na podstawie ustawień z panelu administracyjnego
 */

// Sprawdź czy wtyczka Indemi Core nie jest aktywna
if (!function_exists('indemi_core_init')) {

    class PolskiPodarekFunctions {

        /**
         * Inicjalizacja wszystkich funkcji
         */
        public static function init() {
            add_action('after_setup_theme', array(__CLASS__, 'setup_theme_support'));
            add_action('after_setup_theme', array(__CLASS__, 'setup_thumbnails'));
            add_action('init', array(__CLASS__, 'apply_patches'));
            add_action('admin_menu', array(__CLASS__, 'hide_admin_menu_items'));
            
            // Helper functions
            self::register_helper_functions();
        }

        /**
         * Ustawia theme support na podstawie opcji z panelu
         */
        public static function setup_theme_support() {
            $options = get_option('_pp_theme_support', array());

            if (isset($options['post-thumbnails']) && $options['post-thumbnails']) {
                add_theme_support('post-thumbnails');
            }

            if (isset($options['title-tag']) && $options['title-tag']) {
                add_theme_support('title-tag');
            }

            if (isset($options['menus']) && $options['menus']) {
                add_theme_support('menus');
            }

            if (isset($options['html5']) && $options['html5']) {
                add_theme_support('html5', array(
                    'comment-list',
                    'comment-form',
                    'search-form',
                    'gallery',
                    'caption',
                    'script',
                    'style'
                ));
            }

            if (isset($options['custom-logo']) && $options['custom-logo']) {
                add_theme_support('custom-logo', array(
                    'height'      => 100,
                    'width'       => 400,
                    'flex-height' => true,
                    'flex-width'  => true,
                ));
            }

            if (isset($options['automatic-feed-links']) && $options['automatic-feed-links']) {
                add_theme_support('automatic-feed-links');
            }

            if (isset($options['align-wide']) && $options['align-wide']) {
                add_theme_support('align-wide');
            }

            if (isset($options['responsive-embeds']) && $options['responsive-embeds']) {
                add_theme_support('responsive-embeds');
            }

            if (isset($options['editor-styles']) && $options['editor-styles']) {
                add_theme_support('editor-styles');
            }

            // Zawsze dodaj WooCommerce support
            add_theme_support('woocommerce');
        }

        /**
         * Ustawia rozmiary miniaturek na podstawie opcji z panelu
         */
        public static function setup_thumbnails() {
            $thumbnails = get_option('_pp_thumbnails', array());

            foreach ($thumbnails as $thumbnail) {
                if (!empty($thumbnail['name'])) {
                    add_image_size(
                        $thumbnail['name'],
                        $thumbnail['width'],
                        $thumbnail['height'],
                        $thumbnail['crop']
                    );
                }
            }
        }

        /**
         * Stosuje optymalizacje na podstawie opcji z panelu
         */
        public static function apply_patches() {
            $patches = get_option('_pp_patches', array());

            // Wyczyść wp_head
            if (isset($patches['clean_wp_head']) && $patches['clean_wp_head']) {
                self::clean_wp_head();
            }

            // Wyłącz emoji
            if (isset($patches['disable_emojis']) && $patches['disable_emojis']) {
                self::disable_emojis();
            }

            // Dodaj img-fluid
            if (isset($patches['add_img_fluid']) && $patches['add_img_fluid']) {
                self::add_img_fluid();
            }

            // Usuń <p> wokół obrazków
            if (isset($patches['remove_p_from_images']) && $patches['remove_p_from_images']) {
                self::remove_p_from_images();
            }

            // Ukryj admin bar
            if (isset($patches['hide_admin_bar']) && $patches['hide_admin_bar']) {
                add_filter('show_admin_bar', '__return_false');
            }

            // Własny footer admina
            if (isset($patches['custom_admin_footer']) && $patches['custom_admin_footer']) {
                add_filter('admin_footer_text', array(__CLASS__, 'custom_admin_footer'));
            }

            // Automatyczne aktualizacje
            if (isset($patches['auto_update_plugins']) && $patches['auto_update_plugins']) {
                add_filter('auto_update_plugin', '__return_true');
            }

            // Usuń logo WP
            if (isset($patches['remove_wp_logo']) && $patches['remove_wp_logo']) {
                add_action('wp_before_admin_bar_render', array(__CLASS__, 'remove_wp_logo'));
            }

            // Limit zajawki
            if (isset($patches['excerpt_limit']) && $patches['excerpt_limit']) {
                add_filter('excerpt_length', function($length) use ($patches) {
                    return intval($patches['excerpt_limit']);
                }, 999);
            }
        }

        /**
         * Ukrywa elementy menu admina
         */
        public static function hide_admin_menu_items() {
            $hidden_items = get_option('_pp_admin_menu', array());

            foreach ($hidden_items as $slug => $hide) {
                if ($hide) {
                    remove_menu_page($slug);
                }
            }
        }

        /**
         * Czyści wp_head z niepotrzebnych elementów
         */
        private static function clean_wp_head() {
            remove_action('wp_head', 'feed_links_extra', 3);
            remove_action('wp_head', 'feed_links', 2);
            remove_action('wp_head', 'rsd_link');
            remove_action('wp_head', 'wlwmanifest_link');
            remove_action('wp_head', 'wp_generator');
            remove_action('wp_head', 'index_rel_link');
            remove_action('wp_head', 'parent_post_rel_link', 10, 0);
            remove_action('wp_head', 'start_post_rel_link', 10, 0);
            remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
        }

        /**
         * Wyłącza emoji
         */
        private static function disable_emojis() {
            remove_action('wp_head', 'print_emoji_detection_script', 7);
            remove_action('admin_print_scripts', 'print_emoji_detection_script');
            remove_action('wp_print_styles', 'print_emoji_styles');
            remove_action('admin_print_styles', 'print_emoji_styles');
            remove_filter('the_content_feed', 'wp_staticize_emoji');
            remove_filter('comment_text_rss', 'wp_staticize_emoji');
            remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
            
            add_filter('tiny_mce_plugins', function($plugins) {
                if (is_array($plugins)) {
                    return array_diff($plugins, array('wpemoji'));
                }
                return array();
            });
        }

        /**
         * Dodaje klasę img-fluid do obrazków
         */
        private static function add_img_fluid() {
            add_filter('get_image_tag_class', function($class) {
                $class .= ' img-fluid';
                return $class;
            });
        }

        /**
         * Usuwa tagi <p> wokół obrazków
         */
        private static function remove_p_from_images() {
            add_filter('the_content', function($content) {
                return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
            });
        }

        /**
         * Własny footer w panelu admina
         */
        public static function custom_admin_footer() {
            return 'Projekt i realizacja: <a href="https://indemi.pl/" target="_blank">indemi.pl</a>';
        }

        /**
         * Usuwa logo WP z belki admina
         */
        public static function remove_wp_logo() {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu('wp-logo');
        }

        /**
         * Rejestruje funkcje helper
         */
        private static function register_helper_functions() {
            
            // Sprawdza czy jesteśmy na stronie lub podstronie
            if (!function_exists('is_tree')) {
                function is_tree($pid) {
                    global $post;
                    if (is_page() && ($post->post_parent == $pid || is_page($pid))) {
                        return true;
                    }
                    return false;
                }
            }

            // Ogranicza liczbę słów w stringu
            if (!function_exists('string_limit_words')) {
                function string_limit_words($string, $word_limit) {
                    $words = explode(' ', $string, ($word_limit + 1));
                    if (count($words) > $word_limit) {
                        array_pop($words);
                    }
                    return implode(' ', $words);
                }
            }

            // Polskie nazwy miesięcy
            if (!function_exists('polish_months')) {
                function polish_months($month) {
                    $months = array(
                        'Jan' => 'stycznia',
                        'Feb' => 'lutego', 
                        'Mar' => 'marca',
                        'Apr' => 'kwietnia',
                        'May' => 'maja',
                        'Jun' => 'czerwca',
                        'Jul' => 'lipca',
                        'Aug' => 'sierpnia',
                        'Sep' => 'września',
                        'Oct' => 'października',
                        'Nov' => 'listopada',
                        'Dec' => 'grudnia'
                    );
                    
                    return isset($months[$month]) ? $months[$month] : $month;
                }
            }

            // Helper function dla debugowania
            if (!function_exists('pp_pre')) {
                function pp_pre($element) {
                    echo '<pre>';
                    print_r($element);
                    echo '</pre>';
                }
            }

            // Sprawdza czy produkt jest alkoholowy (dla kompatybilności)
            if (!function_exists('is_alcohol_product')) {
                function is_alcohol_product($product_id) {
                    $is_alcohol = get_post_meta($product_id, 'product_alcohol', true);
                    return ($is_alcohol === 'tak');
                }
            }

            // Sprawdza czy koszyk zawiera produkty alkoholowe
            if (!function_exists('cart_has_alcohol')) {
                function cart_has_alcohol() {
                    if (!WC()->cart) {
                        return false;
                    }

                    foreach (WC()->cart->get_cart() as $cart_item) {
                        if (is_alcohol_product($cart_item['product_id'])) {
                            return true;
                        }
                    }

                    return false;
                }
            }
        }

        /**
         * Pobiera wszystkie ustawienia (dla debugowania)
         */
        public static function get_all_settings() {
            return array(
                'theme_support' => get_option('_pp_theme_support', array()),
                'thumbnails' => get_option('_pp_thumbnails', array()),
                'patches' => get_option('_pp_patches', array()),
                'admin_menu' => get_option('_pp_admin_menu', array())
            );
        }

        /**
         * Reset wszystkich ustawień do domyślnych
         */
        public static function reset_to_defaults() {
            delete_option('_pp_theme_support');
            delete_option('_pp_thumbnails');
            delete_option('_pp_patches');
            delete_option('_pp_admin_menu');
            
            // Ponowna inicjalizacja domyślnych opcji
            if (class_exists('PolskiPodarekAdmin')) {
                PolskiPodarekAdmin::init_default_options();
            }
        }
    }

    // Inicjalizuj funkcje
    PolskiPodarekFunctions::init();

} // Koniec sprawdzenia czy wtyczka nie jest aktywna