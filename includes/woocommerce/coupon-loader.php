<?php
/**
 * Loader dla plików CSS i JS kuponów rabatowych
 * Polski Podarek 2025
 * 
 * Ten plik ładuje style i skrypty tylko tam gdzie są potrzebne
 * ORAZ zawiera wszystkie funkcje obsługi kuponów
 */

// Zabezpieczenie przed bezpośrednim dostępem
if (!defined('ABSPATH')) {
    exit;
}

/**
 * DODAJ BRAKUJĄCĄ FUNKCJĘ WooCommerce
 */
if (!function_exists('wc_get_cart_remove_coupon_url')) {
    function wc_get_cart_remove_coupon_url($coupon_code) {
        $cart_url = wc_get_cart_url();
        return wp_nonce_url(
            add_query_arg('remove_coupon', $coupon_code, $cart_url),
            'woocommerce-cart'
        );
    }
}

/**
 * Klasa do zarządzania ładowaniem plików kuponów
 */
class PolskiPodarekCouponLoader {
    
    /**
     * Inicjalizacja
     */
    public static function init() {
        // Ładowanie plików CSS/JS
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_coupon_assets'));
        add_action('wp_footer', array(__CLASS__, 'add_coupon_ajax_data'));
        add_filter('script_loader_tag', array(__CLASS__, 'add_script_attributes'), 10, 3);
        
        // Inicjalizacja funkcji kuponów
        self::init_coupon_functions();
    }
    
    /**
     * Inicjalizacja wszystkich funkcji kuponów
     */
    public static function init_coupon_functions() {
        remove_action('init', 'WC_Form_Handler::remove_coupon_action', 10);
        
        // Wyłącz JavaScript kuponów - używaj natywnych metod
        add_action('wp_enqueue_scripts', array(__CLASS__, 'disable_coupon_javascript'), 20);
        
        // Dodaj natywne style
        add_action('wp_head', array(__CLASS__, 'native_coupon_styles'));
        
        // Upewnij się że kupony działają
        add_action('init', array(__CLASS__, 'ensure_native_coupons'));
        
        // Lepsze komunikaty
        add_filter('woocommerce_coupon_message', array(__CLASS__, 'better_coupon_messages'), 10, 3);
        add_filter('woocommerce_coupon_error', array(__CLASS__, 'better_coupon_errors'), 10, 3);
        
        // Wyświetlanie aktywnych kuponów
        add_action('woocommerce_cart_contents', array(__CLASS__, 'show_coupons_in_cart'));
        add_action('woocommerce_checkout_before_order_review_heading', array(__CLASS__, 'show_coupons_in_checkout'));
        
        // Mini cart kupony
        add_action('woocommerce_widget_shopping_cart_before_buttons', array(__CLASS__, 'mini_cart_coupons'));
        
        // Debug i admin
        add_action('wp_footer', array(__CLASS__, 'native_coupon_debug'));
        add_action('admin_bar_menu', array(__CLASS__, 'add_coupon_debug_link'));
        
        // Shortcodes
        add_shortcode('dostepne_kupony', array(__CLASS__, 'available_coupons_shortcode'));
        add_shortcode('aktywne_kupony', array(__CLASS__, 'active_coupons_shortcode'));
        
        // Auto-zastosowanie kuponu z URL
        add_action('init', array(__CLASS__, 'auto_apply_coupon_from_url'));
    }
    
    /**
     * NAPRAWIONA funkcja - generuje URL do usuwania kuponu
     */
    private static function get_remove_coupon_url($coupon_code) {
        $cart_url = wc_get_cart_url();
        return wp_nonce_url(
            add_query_arg('remove_coupon', $coupon_code, $cart_url),
            'woocommerce-cart'
        );
    }
    
    /**
     * Ładuje pliki CSS i JS tylko na odpowiednich stronach
     */
    public static function enqueue_coupon_assets() {
        
        // Sprawdź czy jesteśmy na stronie gdzie potrzebne są kupony
        if (!self::should_load_coupon_assets()) {
            return;
        }
        
        $theme_uri = get_template_directory_uri();
        $version = self::get_assets_version();
        
        // Ładuj CSS dla kuponów
        wp_enqueue_style(
            'pp-coupon-styles',
            $theme_uri . '/css/coupon-styles.css',
            array(), 
            $version,
            'all'
        );
        
        // OPCJONALNIE ładuj JavaScript (domyślnie wyłączony dla natywnego trybu)
        if (get_option('pp_coupon_enable_js', false)) {
            wp_enqueue_script(
                'pp-coupon-scripts',
                $theme_uri . '/js/coupon-scripts.js',
                array('jquery'),
                $version,
                true
            );
            
            // Dodaj dane dla AJAX
            wp_localize_script('pp-coupon-scripts', 'pp_coupon_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('validate_coupon_nonce'),
                'messages' => array(
                    'checking' => __('Sprawdzam kupon...', 'polskipodarek'),
                    'valid' => __('Kupon jest prawidłowy', 'polskipodarek'),
                    'invalid' => __('Kupon jest nieprawidłowy', 'polskipodarek'),
                    'applied' => __('Kupon został zastosowany!', 'polskipodarek'),
                    'removed' => __('Kupon został usunięty', 'polskipodarek'),
                    'error' => __('Błąd podczas sprawdzania kuponu', 'polskipodarek'),
                    'confirm_remove' => __('Czy na pewno chcesz usunąć ten kupon?', 'polskipodarek')
                ),
                'settings' => array(
                    'validation_delay' => 500,
                    'notice_auto_hide' => 5000,
                    'min_coupon_length' => 3
                ),
                'debug' => defined('WP_DEBUG') && WP_DEBUG
            ));
        }
        
        // Dodaj inline style dla krytycznych elementów
        $critical_css = self::get_critical_coupon_css();
        if ($critical_css) {
            wp_add_inline_style('pp-coupon-styles', $critical_css);
        }
    }
    
    /**
     * WYŁĄCZ JavaScript kuponów - tryb natywny
     */
    public static function disable_coupon_javascript() {
        if (wp_script_is('pp-coupon-scripts', 'enqueued')) {
            wp_dequeue_script('pp-coupon-scripts');
        }
    }
    
    /**
     * Dodaj natywne style CSS
     */
    public static function native_coupon_styles() {
        if (!is_checkout() && !is_cart()) {
            return;
        }
        ?>
        <style>
        /* NATYWNE STYLE KUPONÓW - bez JavaScript */
        .woocommerce-form-coupon-toggle {
            margin-bottom: 20px;
        }
        
        .woocommerce-form-coupon-toggle .woocommerce-notice {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 6px;
        }
        
        .showcoupon {
            color: #571B33;
            text-decoration: underline;
            font-weight: 600;
            cursor: pointer;
        }
        
        .showcoupon:hover {
            color: #8B2747;
            text-decoration: none;
        }
        
        .checkout_coupon {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .coupon-input-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .coupon-input-group input[type="text"] {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .coupon-input-group input[type="text"]:focus {
            outline: none;
            border-color: #571B33;
            box-shadow: 0 0 0 2px rgba(87, 27, 51, 0.1);
        }
        
        .coupon-input-group .button {
            background: #571B33;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
        }
        
        .coupon-input-group .button:hover {
            background: #8B2747;
        }
        
        .coupon-info {
            margin: 0 0 15px 0;
            color: #666;
            font-size: 14px;
        }
        
        /* Style dla formularza w koszyku */
        .native-coupon-form {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .native-coupon-form .form-control {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .native-coupon-form .btn {
            white-space: nowrap;
            padding: 8px 16px;
        }
        
        /* Zastosowane kupony */
        .applied-coupons-info {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border: 1px solid #28a745;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        
        .applied-coupons-info h4 {
            color: #155724;
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 10px 0;
        }
        
        .applied-coupons-info h4::before {
            content: "🎟️";
            margin-right: 8px;
        }
        
        .coupon-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .coupon-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 6px;
            margin-bottom: 8px;
        }
        
        .coupon-code-display {
            font-family: monospace;
            font-weight: bold;
            color: #155724;
            background: rgba(40, 167, 69, 0.1);
            padding: 4px 8px;
            border-radius: 4px;
        }
        
        .coupon-discount {
            font-weight: 600;
            color: #155724;
            font-size: 14px;
        }
        
        .coupon-remove {
            color: #dc3545;
            text-decoration: none;
            font-size: 12px;
            padding: 2px 6px;
            border-radius: 3px;
        }
        
        .coupon-remove:hover {
            background: #dc3545;
            color: white;
        }
        
        /* Responsywność */
        @media (max-width: 768px) {
            .coupon-input-group,
            .native-coupon-form {
                flex-direction: column;
            }
            
            .coupon-input-group .button,
            .native-coupon-form .btn {
                width: 100%;
                margin-top: 10px;
            }
        }
        
        /* Komunikaty */
        .woocommerce-message {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            color: #0c5460;
            padding: 12px 15px;
            border-radius: 6px;
            margin: 15px 0;
        }
        
        .woocommerce-error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
            padding: 12px 15px;
            border-radius: 6px;
            margin: 15px 0;
        }
        
        /* Style dla zastosowanych kuponów w WooCommerce */
        .cart-discount {
            background: rgba(40, 167, 69, 0.1);
            border-left: 4px solid #28a745;
        }
        
        .cart-discount th,
        .cart-discount td {
            color: #155724;
            font-weight: 600;
        }
        </style>
        <?php
    }
    
    /**
     * Upewnij się że WooCommerce obsługuje kupony natywnie
     */
    public static function ensure_native_coupons() {
        
        // Włącz kupony
        if (!wc_coupons_enabled()) {
            update_option('woocommerce_enable_coupons', 'yes');
        }
        
        // Upewnij się że hooki WooCommerce działają
        add_filter('woocommerce_coupons_enabled', '__return_true');
        
        // Obsługa usuwania kuponów z URL
        add_action('init', array(__CLASS__, 'handle_remove_coupon_url'));
    }
    
    /**
     * Obsługa usuwania kuponów przez URL
     */
    public static function handle_remove_coupon_url() {
        if (isset($_GET['remove_coupon']) && wp_verify_nonce($_GET['_wpnonce'], 'woocommerce-cart')) {
            $coupon_code = sanitize_text_field($_GET['remove_coupon']);
            
            if (WC()->cart && WC()->cart->has_discount($coupon_code)) {
                WC()->cart->remove_coupon($coupon_code);
                wc_add_notice(sprintf(__('Kupon "%s" został usunięty.', 'polskipodarek'), $coupon_code), 'success');
            }
            
            // Przekieruj bez parametrów URL
            wp_redirect(remove_query_arg(array('remove_coupon', '_wpnonce')));
            exit;
        }
    }
    
    /**
     * Lepsze komunikaty dla kuponów
     */
    public static function better_coupon_messages($message, $message_code, $coupon) {
        if (!$coupon) return $message;
        
        $coupon_obj = new WC_Coupon($coupon);
        $discount_info = '';
        
        if ($coupon_obj->get_amount()) {
            if ($coupon_obj->get_discount_type() == 'percent') {
                $discount_info = ' (rabat ' . $coupon_obj->get_amount() . '%)';
            } else {
                $discount_info = ' (rabat ' . wc_price($coupon_obj->get_amount()) . ')';
            }
        }
        
        switch ($message_code) {
            case WC_Coupon::WC_COUPON_SUCCESS:
                return '🎉 Kupon "' . $coupon . '" został zastosowany' . $discount_info . '!';
                
            case WC_Coupon::WC_COUPON_REMOVED:
                return '✅ Kupon "' . $coupon . '" został usunięty.';
        }
        
        return $message;
    }
    
    /**
     * Lepsze komunikaty błędów
     */
    public static function better_coupon_errors($error, $error_code, $coupon) {
        
        switch ($error_code) {
            case WC_Coupon::E_WC_COUPON_NOT_EXIST:
                return '❌ Kupon "' . $coupon->get_code() . '" nie istnieje.';
                
            case WC_Coupon::E_WC_COUPON_EXPIRED:
                return '⏰ Kupon "' . $coupon->get_code() . '" wygasł.';
                
            case WC_Coupon::E_WC_COUPON_MIN_SPEND_LIMIT_NOT_MET:
                return '💰 Minimalna kwota zamówienia: ' . wc_price($coupon->get_minimum_amount());
                
            case WC_Coupon::E_WC_COUPON_USAGE_LIMIT_REACHED:
                return '🚫 Kupon "' . $coupon->get_code() . '" osiągnął limit użycia.';
                
            case WC_Coupon::E_WC_COUPON_ALREADY_APPLIED:
                return '⚠️ Kupon "' . $coupon->get_code() . '" jest już zastosowany.';
        }
        
        return $error;
    }
    
    /**
     * NAPRAWIONA - Wyświetla informacje o zastosowanych kuponach
     */
    public static function display_active_coupons_info() {
        if (!WC()->cart || WC()->cart->is_empty()) {
            return;
        }
        
        $applied_coupons = WC()->cart->get_applied_coupons();
        
        if (empty($applied_coupons)) {
            return;
        }
        
        echo '<div class="applied-coupons-info">';
        echo '<h4>' . __('Aktywne kupony rabatowe:', 'polskipodarek') . '</h4>';
        echo '<ul class="coupon-list">';
        
        foreach ($applied_coupons as $coupon_code) {
            $coupon = new WC_Coupon($coupon_code);
            
            if (!$coupon->get_id()) {
                continue;
            }
            
            $discount_amount = WC()->cart->get_coupon_discount_amount($coupon_code, WC()->cart->display_prices_including_tax());
            $discount_type = $coupon->get_discount_type();
            
            echo '<li class="coupon-item">';
            echo '<div class="coupon-details">';
            echo '<span class="coupon-code-display">' . esc_html($coupon_code) . '</span>';
            
            // Informacja o rabacie
            echo '<div class="coupon-discount">';
            if ($discount_type === 'percent') {
                echo sprintf(__('Rabat: %s%% (-%s)', 'polskipodarek'), 
                    $coupon->get_amount(), 
                    wc_price($discount_amount)
                );
            } elseif ($discount_type === 'fixed_cart') {
                echo sprintf(__('Rabat: -%s', 'polskipodarek'), 
                    wc_price($discount_amount)
                );
            } elseif ($discount_type === 'fixed_product') {
                echo sprintf(__('Rabat na produkty: -%s', 'polskipodarek'), 
                    wc_price($coupon->get_amount())
                );
            } else {
                echo sprintf(__('Rabat: -%s', 'polskipodarek'), 
                    wc_price($discount_amount)
                );
            }
            echo '</div>';
            
            // Opis kuponu jeśli istnieje
            if ($coupon->get_description()) {
                echo '<div class="coupon-description">' . esc_html($coupon->get_description()) . '</div>';
            }
            
            echo '</div>';
            
            // UŻYWAJ GLOBALNEJ FUNKCJI zamiast metody klasy
            $remove_url = wc_get_cart_remove_coupon_url($coupon_code);
            echo '<a href="' . esc_url($remove_url) . '" class="coupon-remove" data-coupon="' . esc_attr($coupon_code) . '" onclick="return confirm(\'Czy na pewno chcesz usunąć kupon ' . esc_js($coupon_code) . '?\');">';
            echo __('Usuń', 'polskipodarek');
            echo '</a>';
            
            echo '</li>';
        }
        
        echo '</ul>';
        echo '</div>';
    }
    
    /**
     * Pokaż kupony w koszyku
     */
    public static function show_coupons_in_cart() {
        if (is_cart()) {
            self::display_active_coupons_info();
        }
    }
    
    /**
     * Pokaż kupony na checkout
     */
    public static function show_coupons_in_checkout() {
        self::display_active_coupons_info();
    }
    
    /**
     * Dodaj informacje o kuponach do mini-cart
     */
    public static function mini_cart_coupons() {
        $applied_coupons = WC()->cart->get_applied_coupons();
        
        if (empty($applied_coupons)) {
            return;
        }
        
        echo '<div class="mini-cart-coupons">';
        echo '<h5>' . __('Aktywne kupony:', 'polskipodarek') . '</h5>';
        
        foreach ($applied_coupons as $coupon_code) {
            $coupon = new WC_Coupon($coupon_code);
            $discount = WC()->cart->get_coupon_discount_amount($coupon_code);
            
            echo '<div class="mini-cart-coupon">';
            echo '<span class="coupon-code">' . esc_html($coupon_code) . '</span>';
            echo '<span class="coupon-amount">-' . wc_price($discount) . '</span>';
            echo '</div>';
        }
        
        echo '</div>';
        
        // Dodaj style inline
        echo '<style>
        .mini-cart-coupons {
            background: #f8f9fa;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        .mini-cart-coupons h5 {
            margin: 0 0 8px 0;
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        .mini-cart-coupon {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 3px;
        }
        .mini-cart-coupon .coupon-code {
            font-family: monospace;
            font-weight: bold;
            color: #571B33;
        }
        .mini-cart-coupon .coupon-amount {
            color: #28a745;
            font-weight: bold;
        }
        </style>';
    }
    
    /**
     * Shortcode dostępnych kuponów
     */
    public static function available_coupons_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 5,
            'show_amount' => 'yes',
            'show_description' => 'yes'
        ), $atts);
        
        $coupons = get_posts(array(
            'post_type' => 'shop_coupon',
            'post_status' => 'publish',
            'numberposts' => $atts['limit'],
            'meta_query' => array(
                array(
                    'key' => 'date_expires',
                    'value' => current_time('timestamp'),
                    'compare' => '>='
                )
            )
        ));
        
        if (empty($coupons)) {
            return '<p>Brak dostępnych kuponów.</p>';
        }
        
        $output = '<div class="available-coupons">';
        $output .= '<h4>Dostępne kupony rabatowe:</h4>';
        $output .= '<div class="coupons-list">';
        
        foreach ($coupons as $coupon_post) {
            $coupon = new WC_Coupon($coupon_post->ID);
            
            $output .= '<div class="coupon-item">';
            $output .= '<div class="coupon-code">' . $coupon->get_code() . '</div>';
            
            if ($atts['show_amount'] === 'yes') {
                if ($coupon->get_discount_type() === 'percent') {
                    $output .= '<div class="coupon-amount">-' . $coupon->get_amount() . '%</div>';
                } else {
                    $output .= '<div class="coupon-amount">-' . wc_price($coupon->get_amount()) . '</div>';
                }
            }
            
            if ($atts['show_description'] === 'yes' && $coupon->get_description()) {
                $output .= '<div class="coupon-description">' . $coupon->get_description() . '</div>';
            }
            
            if ($coupon->get_date_expires()) {
                $output .= '<div class="coupon-expires">Ważny do: ' . $coupon->get_date_expires()->date('d.m.Y') . '</div>';
            }
            
            $output .= '</div>';
        }
        
        $output .= '</div></div>';
        
        return $output;
    }
    
    /**
     * Shortcode aktywnych kuponów
     */
    public static function active_coupons_shortcode($atts) {
        $atts = shortcode_atts(array(
            'show_description' => 'yes',
            'show_remove' => 'yes'
        ), $atts);
        
        ob_start();
        self::display_active_coupons_info();
        return ob_get_clean();
    }
    
    /**
     * Auto-zastosowanie kuponu z URL
     */
    public static function auto_apply_coupon_from_url() {
        if (isset($_GET['coupon']) && !empty($_GET['coupon'])) {
            $coupon_code = sanitize_text_field($_GET['coupon']);
            
            if (WC()->cart && !WC()->cart->has_discount($coupon_code)) {
                WC()->cart->apply_coupon($coupon_code);
                wc_add_notice(sprintf(__('Kupon "%s" został automatycznie zastosowany!', 'polskipodarek'), $coupon_code), 'success');
            }
        }
    }
    
    /**
     * Debug info - tylko dla adminów
     */
    public static function native_coupon_debug() {
        if (!current_user_can('manage_options') || (!is_cart() && !is_checkout())) {
            return;
        }
        
        if (isset($_GET['coupon_debug'])) {
            ?>
            <div style="position: fixed; bottom: 0; right: 0; background: white; padding: 10px; border: 2px solid #571B33; z-index: 9999; font-size: 12px; max-width: 300px;">
                <h4>🎟️ Coupon Debug (Native Mode)</h4>
                <p><strong>Function exists:</strong> <?php echo function_exists('wc_get_cart_remove_coupon_url') ? 'YES' : 'NO'; ?></p>
                <p><strong>Coupons enabled:</strong> <?php echo wc_coupons_enabled() ? 'YES' : 'NO'; ?></p>
                <p><strong>Cart exists:</strong> <?php echo WC()->cart ? 'YES' : 'NO'; ?></p>
                <?php if (WC()->cart): ?>
                    <p><strong>Applied coupons:</strong> <?php 
                        $applied = WC()->cart->get_applied_coupons();
                        echo empty($applied) ? 'NONE' : implode(', ', $applied);
                    ?></p>
                    <p><strong>Cart total:</strong> <?php echo wc_price(WC()->cart->get_total('')); ?></p>
                <?php endif; ?>
                <p><strong>Current page:</strong> <?php echo is_cart() ? 'CART' : (is_checkout() ? 'CHECKOUT' : 'OTHER'); ?></p>
                <p><strong>JavaScript:</strong> DISABLED (Native mode)</p>
                <p><strong>CSS loaded:</strong> <?php echo wp_style_is('pp-coupon-styles', 'enqueued') ? 'YES' : 'NO'; ?></p>
                <button onclick="this.parentNode.style.display='none'" style="float: right; background: #dc3545; color: white; border: none; padding: 2px 8px; border-radius: 3px; cursor: pointer;">×</button>
            </div>
            <?php
        }
    }
    
    /**
     * Dodaj link debug dla adminów
     */
    public static function add_coupon_debug_link($wp_admin_bar) {
        if (!is_checkout() && !is_cart()) {
            return;
        }
        
        $wp_admin_bar->add_node(array(
            'id' => 'coupon-debug',
            'title' => '🎟️ Debug kuponów',
            'href' => add_query_arg('coupon_debug', '1'),
        ));
    }
    
    /**
     * Sprawdza czy należy ładować pliki kuponów
     */
    private static function should_load_coupon_assets() {
        
        // Jeśli kupony są wyłączone globalnie
        if (!wc_coupons_enabled()) {
            return false;
        }
        
        // Strony gdzie kupony są potrzebne
        $coupon_pages = array(
            'is_cart',
            'is_checkout', 
            'is_account_page',
            'is_shop',
            'is_product_category',
            'is_product_tag',
            'is_product'
        );
        
        foreach ($coupon_pages as $page_check) {
            if (function_exists($page_check) && call_user_func($page_check)) {
                return true;
            }
        }
        
        // Sprawdź czy na stronie jest shortcode kuponów
        global $post;
        if (is_object($post) && has_shortcode($post->post_content, 'dostepne_kupony')) {
            return true;
        }
        
        // Sprawdź widget areas
        if (is_active_widget(false, false, 'woocommerce_widget_cart')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Generuje wersję plików na podstawie modyfikacji
     */
    private static function get_assets_version() {
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            return time();
        }
        
        $theme_version = wp_get_theme()->get('Version');
        $css_file = get_template_directory() . '/css/coupon-styles.css';
        $js_file = get_template_directory() . '/js/coupon-scripts.js';
        
        $css_time = file_exists($css_file) ? filemtime($css_file) : 0;
        $js_time = file_exists($js_file) ? filemtime($js_file) : 0;
        
        return $theme_version . '.' . max($css_time, $js_time);
    }
    
    /**
     * Dodaje dane AJAX do footer
     */
    public static function add_coupon_ajax_data() {
        
        if (!self::should_load_coupon_assets()) {
            return;
        }
        
        ?>
        <script type="text/javascript">
        /* Coupon system configuration */
        window.pp_coupon_config = window.pp_coupon_config || {};
        window.pp_coupon_config.cart_url = "<?php echo esc_js(wc_get_cart_url()); ?>";
        window.pp_coupon_config.checkout_url = "<?php echo esc_js(wc_get_checkout_url()); ?>";
        window.pp_coupon_config.currency = "<?php echo esc_js(get_woocommerce_currency_symbol()); ?>";
        window.pp_coupon_config.native_mode = true;
        
        <?php if (WC()->cart && !WC()->cart->is_empty()): ?>
        window.pp_coupon_config.cart_total = <?php echo WC()->cart->get_cart_contents_total(); ?>;
        window.pp_coupon_config.applied_coupons = <?php echo json_encode(WC()->cart->get_applied_coupons()); ?>;
        <?php endif; ?>
        
        <?php if (defined('WP_DEBUG') && WP_DEBUG): ?>
        console.log('🎟️ Coupon config loaded (Native mode):', window.pp_coupon_config);
        <?php endif; ?>
        </script>
        <?php
    }
    
    /**
     * Dodaje atrybuty do skryptów (async, defer)
     */
    public static function add_script_attributes($tag, $handle, $src) {
        
        // Dodaj defer do skryptu kuponów jeśli załadowany
        if ($handle === 'pp-coupon-scripts') {
            $tag = str_replace('<script ', '<script defer ', $tag);
        }
        
        return $tag;
    }
    
    /**
     * Krytyczne style CSS do wstawienia inline
     */
    private static function get_critical_coupon_css() {
        return '
        .woocommerce-form-coupon-toggle .showcoupon { 
            color: #571B33; 
            text-decoration: underline; 
            cursor: pointer; 
        }
        .checkout_coupon { 
            display: none; 
        }
        .coupon-input-group input { 
            border: 1px solid #ddd; 
            padding: 8px 12px; 
            border-radius: 4px; 
        }
        .coupon-input-group .button { 
            background: #571B33; 
            color: white; 
            border: none; 
            padding: 8px 16px; 
            border-radius: 4px; 
            cursor: pointer; 
        }
        ';
    }
    
    /**
     * Sprawdza czy pliki istnieją
     */
    public static function check_assets_exist() {
        
        $css_file = get_template_directory() . '/css/coupon-styles.css';
        $js_file = get_template_directory() . '/js/coupon-scripts.js';
        
        $status = array(
            'css_exists' => file_exists($css_file),
            'js_exists' => file_exists($js_file),
            'css_readable' => is_readable($css_file),
            'js_readable' => is_readable($js_file),
            'css_size' => file_exists($css_file) ? filesize($css_file) : 0,
            'js_size' => file_exists($js_file) ? filesize($js_file) : 0
        );
        
        return $status;
    }
    
    /**
     * Debug info o załadowanych plikach
     */
    public static function debug_loaded_assets() {
        
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }
        
        add_action('wp_footer', function() {
            if (self::should_load_coupon_assets()) {
                echo '<!-- Coupon assets loaded on: ' . get_the_title() . ' (Native mode) -->';
            }
        });
    }
    
    /**
     * AJAX walidacja kuponów (opcjonalna - domyślnie wyłączona)
     */
    public static function ajax_validate_coupon() {
        if (!wp_verify_nonce($_POST['nonce'], 'validate_coupon_nonce')) {
            wp_die('Błąd bezpieczeństwa');
        }
        
        $coupon_code = sanitize_text_field($_POST['coupon_code']);
        $coupon = new WC_Coupon($coupon_code);
        
        $response = array(
            'valid' => false,
            'message' => '',
            'discount_type' => '',
            'amount' => 0
        );
        
        if (!$coupon->get_id()) {
            $response['message'] = 'Kupon nie istnieje';
        } elseif (!$coupon->is_valid()) {
            $response['message'] = 'Kupon jest nieważny';
        } else {
            $response['valid'] = true;
            $response['message'] = 'Kupon jest prawidłowy';
            $response['discount_type'] = $coupon->get_discount_type();
            $response['amount'] = $coupon->get_amount();
            
            if ($coupon->get_discount_type() === 'percent') {
                $response['message'] .= ' - rabat ' . $coupon->get_amount() . '%';
            } else {
                $response['message'] .= ' - rabat ' . wc_price($coupon->get_amount());
            }
        }
        
        wp_send_json($response);
    }
    
    /**
     * Włącz AJAX walidację (opcjonalnie)
     */
    public static function enable_ajax_validation() {
        add_action('wp_ajax_validate_coupon', array(__CLASS__, 'ajax_validate_coupon'));
        add_action('wp_ajax_nopriv_validate_coupon', array(__CLASS__, 'ajax_validate_coupon'));
    }
    
    /**
     * Funkcje pomocnicze administracyjne
     */
    public static function admin_settings() {
        
        // Dodaj sekcję w ustawieniach WooCommerce
        add_filter('woocommerce_get_sections_advanced', function($sections) {
            $sections['pp_coupons'] = __('Polski Podarek - Kupony', 'polskipodarek');
            return $sections;
        });
        
        add_filter('woocommerce_get_settings_advanced', function($settings, $current_section) {
            if ($current_section == 'pp_coupons') {
                $settings = array(
                    array(
                        'name' => __('Ustawienia kuponów Polski Podarek', 'polskipodarek'),
                        'type' => 'title',
                        'desc' => __('Konfiguracja systemu kuponów rabatowych.', 'polskipodarek'),
                        'id' => 'pp_coupon_settings'
                    ),
                    array(
                        'name' => __('Tryb JavaScript', 'polskipodarek'),
                        'desc' => __('Włącz zaawansowane funkcje JavaScript (walidacja na żywo, AJAX)', 'polskipodarek'),
                        'id' => 'pp_coupon_enable_js',
                        'type' => 'checkbox',
                        'default' => 'no'
                    ),
                    array(
                        'name' => __('AJAX walidacja', 'polskipodarek'),
                        'desc' => __('Sprawdzaj kupony przez AJAX przed wysłaniem formularza', 'polskipodarek'),
                        'id' => 'pp_coupon_ajax_validation',
                        'type' => 'checkbox',
                        'default' => 'no'
                    ),
                    array(
                        'name' => __('Auto-zastosowanie z URL', 'polskipodarek'),
                        'desc' => __('Automatycznie zastosuj kupon z parametru ?coupon=KOD', 'polskipodarek'),
                        'id' => 'pp_coupon_auto_apply',
                        'type' => 'checkbox',
                        'default' => 'yes'
                    ),
                    array(
                        'name' => __('Pokaż w mini-cart', 'polskipodarek'),
                        'desc' => __('Wyświetlaj aktywne kupony w mini koszyku', 'polskipodarek'),
                        'id' => 'pp_coupon_show_in_minicart',
                        'type' => 'checkbox',
                        'default' => 'yes'
                    ),
                    array(
                        'type' => 'sectionend',
                        'id' => 'pp_coupon_settings'
                    )
                );
            }
            return $settings;
        }, 10, 2);
    }
    
    /**
     * Funkcja pomocnicza - pobierz status systemu
     */
    public static function get_system_status() {
        return array(
            'native_mode' => true,
            'coupons_enabled' => wc_coupons_enabled(),
            'javascript_enabled' => get_option('pp_coupon_enable_js', false),
            'ajax_validation' => get_option('pp_coupon_ajax_validation', false),
            'assets_exist' => self::check_assets_exist(),
            'applied_coupons' => WC()->cart ? WC()->cart->get_applied_coupons() : array(),
            'function_exists' => function_exists('wc_get_cart_remove_coupon_url'),
            'version' => self::get_assets_version()
        );
    }
}

// Inicjalizacja systemu kuponów
add_action('init', array('PolskiPodarekCouponLoader', 'init'));

// Włącz ustawienia admin (opcjonalnie)
if (is_admin()) {
    PolskiPodarekCouponLoader::admin_settings();
}

// Debug w trybie WP_DEBUG
if (defined('WP_DEBUG') && WP_DEBUG) {
    PolskiPodarekCouponLoader::debug_loaded_assets();
}

// Włącz AJAX walidację jeśli ustawiona
if (get_option('pp_coupon_ajax_validation', false)) {
    PolskiPodarekCouponLoader::enable_ajax_validation();
}

/**
 * Funkcje pomocnicze globalne
 */

if (!function_exists('pp_coupon_assets_loaded')) {
    /**
     * Sprawdza czy pliki kuponów są załadowane
     */
    function pp_coupon_assets_loaded() {
        return wp_style_is('pp-coupon-styles', 'enqueued');
    }
}

if (!function_exists('pp_force_load_coupon_assets')) {
    /**
     * Wymusza załadowanie plików kuponów
     */
    function pp_force_load_coupon_assets() {
        PolskiPodarekCouponLoader::enqueue_coupon_assets();
    }
}

if (!function_exists('pp_coupon_debug_info')) {
    /**
     * Zwraca informacje debug o kuponach
     */
    function pp_coupon_debug_info() {
        return PolskiPodarekCouponLoader::get_system_status();
    }
}

if (!function_exists('pp_display_active_coupons')) {
    /**
     * Wyświetla aktywne kupony (użycie w szablonach)
     */
    function pp_display_active_coupons() {
        PolskiPodarekCouponLoader::display_active_coupons_info();
    }
}

if (!function_exists('pp_apply_coupon_from_url')) {
    /**
     * Zastosuj kupon z URL - funkcja pomocnicza
     */
    function pp_apply_coupon_from_url($coupon_code) {
        if (WC()->cart && !WC()->cart->has_discount($coupon_code)) {
            WC()->cart->apply_coupon($coupon_code);
            return true;
        }
        return false;
    }
}



// Tymczasowo wyłącz nasze filtry komunikatów kuponów
remove_filter('woocommerce_coupon_message', array('PolskiPodarekCouponLoader', 'better_coupon_messages'), 10);
remove_filter('woocommerce_coupon_error', array('PolskiPodarekCouponLoader', 'better_coupon_errors'), 10);

// Dodaj prostsze filtry
add_filter('woocommerce_coupon_message', 'pp_simple_coupon_message', 10, 3);
function pp_simple_coupon_message($message, $message_code, $coupon) {
    
    // Pobierz kod kuponu bezpiecznie
    $coupon_code = '';
    if (is_object($coupon) && method_exists($coupon, 'get_code')) {
        $coupon_code = $coupon->get_code();
    } elseif (is_string($coupon)) {
        $coupon_code = $coupon;
    }
    
    // Jeśli nie udało się pobrać kodu, zwróć oryginalny komunikat
    if (empty($coupon_code)) {
        return $message;
    }
    
    switch ($message_code) {
        case WC_Coupon::WC_COUPON_SUCCESS:
            return '🎉 Kupon "' . esc_html($coupon_code) . '" został zastosowany!';
            
        case WC_Coupon::WC_COUPON_REMOVED:
            return '✅ Kupon "' . esc_html($coupon_code) . '" został usunięty.';
    }
    
    return $message;
}
/**
 * Hooki WordPress dla łatwej konfiguracji
 */

// Wyłącz wszystkie style kuponów (jeśli potrzebujesz)
// add_action('wp_enqueue_scripts', function() {
//     wp_dequeue_style('pp-coupon-styles');
// }, 30);

// Włącz tryb JavaScript programowo
// add_action('init', function() {
//     update_option('pp_coupon_enable_js', 'yes');
// });

// Dodaj własne style kuponów
// add_action('wp_head', function() {
//     echo '<style>/* Twoje style */</style>';
// });

/**
 * Przykłady użycia w szablonach:
 * 
 * // Wyświetl aktywne kupony
 * pp_display_active_coupons();
 * 
 * // Sprawdź status systemu
 * $status = pp_coupon_debug_info();
 * var_dump($status);
 * 
 * // Zastosuj kupon programowo
 * pp_apply_coupon_from_url('RABAT20');
 * 
 * // Shortcode w treści
 * [dostepne_kupony limit="3"]
 * [aktywne_kupony]
 */