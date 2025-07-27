<?php
/**
 * PROSTE rozwiązanie problemu kuponów
 * Polski Podarek 2025
 * 
 * Problem: Po usunięciu kupona nie można dodać kolejnego
 * Rozwiązanie: Resetuj formularz po każdej operacji
 */

// Zabezpieczenie przed bezpośrednim dostępem
if (!defined('ABSPATH')) {
    exit;
}

/**
 * DODAJ BRAKUJĄCĄ FUNKCJĘ - kompatybilność
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
 * Prosta klasa obsługi kuponów
 */
class PolskiPodarekCoupons {
    
    /**
     * Inicjalizacja
     */
    public static function init() {
        
        // Sprawdź czy WooCommerce jest aktywne
        if (!class_exists('WooCommerce')) {
            return;
        }
        
        // Podstawowe funkcje
        add_action('init', array(__CLASS__, 'setup_coupons'), 5);
        
        // KLUCZOWE: Obsługa usuwania kuponów + reset formularza
        add_action('init', array(__CLASS__, 'handle_coupon_actions'), 1);
        
        // Lepsze komunikaty
        add_filter('woocommerce_coupon_message', array(__CLASS__, 'better_messages'), 10, 3);
        add_filter('woocommerce_coupon_error', array(__CLASS__, 'better_errors'), 10, 3);
        
        // Style CSS
        add_action('wp_head', array(__CLASS__, 'coupon_styles'));
        
        // Blok z aktywnymi kuponami
        add_action('woocommerce_before_cart_table', array(__CLASS__, 'show_active_coupons_block'));
        
        // WAŻNE: JavaScript resetujący formularz
        add_action('wp_footer', array(__CLASS__, 'reset_coupon_form_js'));
    }
    
    /**
     * Włącz kupony
     */
    public static function setup_coupons() {
        if (!wc_coupons_enabled()) {
            update_option('woocommerce_enable_coupons', 'yes');
        }
        add_filter('woocommerce_coupons_enabled', '__return_true');
    }
    
    /**
     * GŁÓWNA FUNKCJA: Obsługa akcji kuponów
     */
    public static function handle_coupon_actions() {
        
        // Obsługa usuwania kuponów z linku
        if (isset($_GET['remove_coupon']) && isset($_GET['_wpnonce'])) {
            
            if (!wp_verify_nonce($_GET['_wpnonce'], 'woocommerce-cart')) {
                return;
            }
            
            $coupon_code = sanitize_text_field($_GET['remove_coupon']);
            
            if (WC()->cart && WC()->cart->has_discount($coupon_code)) {
                WC()->cart->remove_coupon($coupon_code);
                WC()->cart->calculate_totals();
                
                wc_add_notice(sprintf('✅ Kupon "%s" został usunięty.', $coupon_code), 'success');
            }
            
            // KLUCZOWE: Przekieruj z parametrem resetującym formularz
            wp_redirect(add_query_arg('coupon_reset', '1', wc_get_cart_url()));
            exit;
        }
        
        // Obsługa dodawania kuponów - resetuj po sukcesie
        if (isset($_POST['apply_coupon']) && isset($_POST['coupon_code'])) {
            $coupon_code = sanitize_text_field($_POST['coupon_code']);
            
            if (!empty($coupon_code)) {
                // WooCommerce automatycznie obsłuży dodawanie
                // Dodajemy tylko flagę że formularz powinien się zresetować
                add_action('woocommerce_applied_coupon', function() {
                    // Ustaw flagę resetowania po sukcesie
                    set_transient('pp_coupon_form_reset_' . session_id(), '1', 30);
                });
            }
        }
    }
    
    /**
     * JavaScript resetujący formularz kuponów
     */
    public static function reset_coupon_form_js() {
        if (!is_cart() && !is_checkout()) {
            return;
        }
        
        // Sprawdź czy potrzebny jest reset
        $should_reset = false;
        
        // Reset po usunięciu kupona
        if (isset($_GET['coupon_reset'])) {
            $should_reset = true;
        }
        
        // Reset po dodaniu kupona
        $reset_flag = get_transient('pp_coupon_form_reset_' . session_id());
        if ($reset_flag) {
            $should_reset = true;
            delete_transient('pp_coupon_form_reset_' . session_id());
        }
        
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // RESET FORMULARZA jeśli potrzebny
            <?php if ($should_reset) : ?>
            // Wyczyść pole kuponu
            var couponInput = document.querySelector('input[name="coupon_code"]');
            if (couponInput) {
                couponInput.value = '';
                couponInput.disabled = false;
            }
            
            // Odblokuj przycisk
            var applyButton = document.querySelector('button[name="apply_coupon"]');
            if (applyButton) {
                applyButton.disabled = false;
                applyButton.textContent = applyButton.getAttribute('value') || 'Zastosuj kupon';
            }
            
            // Wyczyść URL z parametru reset
            if (window.history && window.history.replaceState && window.location.search.includes('coupon_reset')) {
                var url = new URL(window.location);
                url.searchParams.delete('coupon_reset');
                window.history.replaceState({}, document.title, url.pathname + url.search);
            }
            
            console.log('Formularz kuponów został zresetowany');
            <?php endif; ?>
            
            // Obsługa formularza kuponów
            var cartForm = document.querySelector('form.woocommerce-cart-form');
            if (cartForm) {
                cartForm.addEventListener('submit', function(e) {
                    var applyButton = e.target.querySelector('button[name="apply_coupon"]');
                    var couponInput = e.target.querySelector('input[name="coupon_code"]');
                    
                    // Sprawdź czy to zastosowanie kuponu
                    if (e.submitter && e.submitter.name === 'apply_coupon') {
                        if (couponInput && couponInput.value.trim() === '') {
                            e.preventDefault();
                            alert('Wprowadź kod kuponu');
                            return false;
                        }
                        
                        // Zmień tekst przycisku
                        if (applyButton) {
                            applyButton.textContent = 'Sprawdzam...';
                            applyButton.disabled = true;
                        }
                        
                        // Timeout na wypadek problemów
                        setTimeout(function() {
                            if (applyButton) {
                                applyButton.disabled = false;
                                applyButton.textContent = applyButton.getAttribute('value') || 'Zastosuj kupon';
                            }
                        }, 5000);
                    }
                });
            }
            
            // Obsługa linków usuwania kuponów
            var removeLinks = document.querySelectorAll('.remove-coupon-btn, .remove-coupon');
            removeLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    var couponCode = this.getAttribute('data-coupon') || 'kupon';
                    if (!confirm('Czy na pewno chcesz usunąć kupon ' + couponCode + '?')) {
                        e.preventDefault();
                        return false;
                    }
                    
                    // Zmień tekst linku
                    this.textContent = 'Usuwam...';
                    this.style.pointerEvents = 'none';
                });
            });
        });
        </script>
        <?php
    }
    
    /**
     * Blok z aktywnymi kuponami
     */
    public static function show_active_coupons_block() {
        
        if (!WC()->cart || WC()->cart->is_empty()) {
            return;
        }
        
        $applied_coupons = WC()->cart->get_applied_coupons();
        
        if (empty($applied_coupons)) {
            return;
        }
        
        ?>
        <div class="active-coupons-block">
            <h3>🎟️ Aktywne kupony rabatowe</h3>
            
            <div class="active-coupons-list">
                <?php foreach ($applied_coupons as $coupon_code) : 
                    $coupon = new WC_Coupon($coupon_code);
                    
                    if (!$coupon->get_id()) {
                        continue;
                    }
                    
                    $discount_amount = WC()->cart->get_coupon_discount_amount($coupon_code);
                    $discount_type = $coupon->get_discount_type();
                ?>
                
                <div class="coupon-item">
                    <div class="coupon-info">
                        <span class="coupon-code"><?php echo esc_html($coupon_code); ?></span>
                        <span class="coupon-discount">
                            <?php if ($discount_type === 'percent') : ?>
                                <?php echo $coupon->get_amount(); ?>% (-<?php echo wc_price($discount_amount); ?>)
                            <?php else : ?>
                                -<?php echo wc_price($discount_amount); ?>
                            <?php endif; ?>
                        </span>
                    </div>
                    
                    <a href="<?php echo esc_url(wc_get_cart_remove_coupon_url($coupon_code)); ?>" 
                       class="remove-coupon-btn"
                       data-coupon="<?php echo esc_attr($coupon_code); ?>">
                        Usuń
                    </a>
                </div>
                
                <?php endforeach; ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Lepsze komunikaty
     */
    public static function better_messages($message, $message_code, $coupon) {
        
        $coupon_code = '';
        if (is_object($coupon) && method_exists($coupon, 'get_code')) {
            $coupon_code = $coupon->get_code();
        } elseif (is_string($coupon)) {
            $coupon_code = $coupon;
        } else {
            return $message;
        }
        
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
     * Lepsze komunikaty błędów
     */
    public static function better_errors($error, $error_code, $coupon) {
        
        $coupon_code = '';
        if (is_object($coupon) && method_exists($coupon, 'get_code')) {
            $coupon_code = $coupon->get_code();
        } elseif (is_string($coupon)) {
            $coupon_code = $coupon;
        } else {
            return $error;
        }
        
        if (empty($coupon_code)) {
            return $error;
        }
        
        switch ($error_code) {
            case WC_Coupon::E_WC_COUPON_NOT_EXIST:
                return '❌ Kupon "' . esc_html($coupon_code) . '" nie istnieje.';
                
            case WC_Coupon::E_WC_COUPON_EXPIRED:
                return '⏰ Kupon "' . esc_html($coupon_code) . '" wygasł.';
                
            case WC_Coupon::E_WC_COUPON_ALREADY_APPLIED:
                return '⚠️ Kupon "' . esc_html($coupon_code) . '" jest już zastosowany.';
                
            case WC_Coupon::E_WC_COUPON_MIN_SPEND_LIMIT_NOT_MET:
                $coupon_obj = new WC_Coupon($coupon_code);
                $min_amount = $coupon_obj->get_minimum_amount();
                return '💰 Minimalna wartość koszyka: ' . wc_price($min_amount) . '.';
                
            case WC_Coupon::E_WC_COUPON_USAGE_LIMIT_REACHED:
                return '🚫 Kupon "' . esc_html($coupon_code) . '" został już wykorzystany.';
        }
        
        return $error;
    }
    
    /**
     * Style CSS
     */
    public static function coupon_styles() {
        
        if (!is_cart() && !is_checkout()) {
            return;
        }
        ?>
        <style>
        /* Komunikaty */
        .woocommerce-message {
            background: #d4edda !important;
            border: 1px solid #c3e6cb !important;
            color: #155724 !important;
            padding: 15px 20px !important;
            border-radius: 8px !important;
            margin: 20px 0 !important;
            font-weight: 500 !important;
        }
        
        .woocommerce-error {
            background: #f8d7da !important;
            border: 1px solid #f5c6cb !important;
            color: #721c24 !important;
            padding: 15px 20px !important;
            border-radius: 8px !important;
            margin: 20px 0 !important;
            font-weight: 500 !important;
        }
        
        /* Blok aktywnych kuponów */
        .active-coupons-block {
            background: #f8f9fa;
            border: 2px solid #28a745;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .active-coupons-block h3 {
            margin: 0 0 15px 0;
            color: #155724;
            font-size: 18px;
        }
        
        .coupon-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 10px;
            border: 1px solid #dee2e6;
        }
        
        .coupon-item:last-child {
            margin-bottom: 0;
        }
        
        .coupon-code {
            font-family: monospace;
            font-weight: bold;
            background: #571B33;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            margin-right: 10px;
        }
        
        .coupon-discount {
            color: #28a745;
            font-weight: bold;
        }
        
        .remove-coupon-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            transition: background 0.3s;
        }
        
        .remove-coupon-btn:hover {
            background: #c82333;
            color: white;
            text-decoration: none;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .coupon-item {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
            
            .active-coupons-block {
                padding: 15px;
            }
        }
        
        /* Ukryj standardowe wiersze kuponów */
        .cart-discount {
            display: none !important;
        }
        </style>
        <?php
    }
}

// Inicjalizacja
add_action('plugins_loaded', array('PolskiPodarekCoupons', 'init'), 15);

/**
 * Funkcja pomocnicza
 */
if (!function_exists('pp_show_active_coupons')) {
    function pp_show_active_coupons() {
        PolskiPodarekCoupons::show_active_coupons_block();
    }
}