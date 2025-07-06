<?php

/**
 * Alcohol Age Verification
 * Funkcjonalność weryfikacji wieku dla produktów alkoholowych
 * Używana tylko gdy wtyczka indemi-premium-product-majority nie jest aktywna
 */

// Sprawdź czy wtyczka nie jest aktywna
if (!function_exists('indemi_premium_product_majority_init')) {
    
    /**
     * Inicjalizacja funkcjonalności weryfikacji wieku
     */
    function polskipodarek_alcohol_age_verification_init() {
        
        // Tylko dla frontendu
        if (!is_admin()) {
            polskipodarek_alcohol_frontend_init();
        } else {
            polskipodarek_alcohol_admin_init();
        }
    }
    add_action('init', 'polskipodarek_alcohol_age_verification_init');

    /**
     * Frontend functionality
     */
    function polskipodarek_alcohol_frontend_init() {
        
        // Dodaj checkbox do rejestracji
        add_action('woocommerce_register_form', 'polskipodarek_add_terms_to_registration', 20);
        
        // Walidacja rejestracji
        add_action('woocommerce_register_post', 'polskipodarek_terms_validation', 20, 3);
        
        // Walidacja checkout dla produktów alkoholowych
        add_action('woocommerce_checkout_process', 'polskipodarek_alcohol_checkout_validation');
        
        // Zapisz dane o alkoholu w zamówieniu
        add_action('woocommerce_checkout_update_order_meta', 'polskipodarek_alcohol_save_order_meta');
    }

    /**
     * Admin functionality
     */
    function polskipodarek_alcohol_admin_init() {
        
        // Dodaj meta box do produktów
        add_action('add_meta_boxes', 'polskipodarek_add_alcohol_meta_box');
        
        // Zapisz dane meta box
        add_action('save_post', 'polskipodarek_save_alcohol_meta_box', 10, 1);
    }

    /**
     * Dodaje checkbox zgody na politykę prywatności podczas rejestracji
     */
    function polskipodarek_add_terms_to_registration() {
        
        if (wc_get_page_id('terms') > 0 && is_account_page()) {
            ?>
            <p class="form-row terms wc-terms-and-conditions">
                <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                    <input type="checkbox" 
                           class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" 
                           name="terms" 
                           <?php checked(apply_filters('woocommerce_terms_is_checked_default', isset($_POST['terms'])), true); ?> 
                           id="terms" /> 
                    <span>
                        <?php printf(
                            __('Przeczytałem oraz akceptuję postanowienia <a href="%s" target="_blank" class="woocommerce-terms-and-conditions-link"> polityki prywatności</a>', 'polskipodarek'), 
                            esc_url(wc_get_page_permalink('terms'))
                        ); ?>
                    </span> 
                    <span class="required">*</span>
                </label>
                <input type="hidden" name="terms-field" value="1" />
            </p>
            <?php
        }
    }

    /**
     * Walidacja checkbox podczas rejestracji
     */
    function polskipodarek_terms_validation($username, $email, $validation_errors) {
        
        if (!isset($_POST['terms'])) {
            $validation_errors->add('terms_error', __('Musisz potwierdzić zgodę na postanowienia polityki prywatności.', 'polskipodarek'));
        }

        return $validation_errors;
    }

    /**
     * Walidacja wieku podczas checkout dla produktów alkoholowych
     */
    function polskipodarek_alcohol_checkout_validation() {
        
        if (!empty($_POST['alcohol-field']) && empty($_POST['alcohol'])) {
            wc_add_notice("Potwierdź swoją pełnoletność.", 'error');
            return;
        }
    }

    /**
     * Zapisuje informację o potwierdzeniu pełnoletności w meta zamówienia
     */
    function polskipodarek_alcohol_save_order_meta($order_id) {
        
        if (!empty($_POST['alcohol'])) {
            update_post_meta($order_id, 'alcohol', sanitize_text_field($_POST['alcohol']));
        }
    }

    /**
     * Dodaje meta box do edycji produktu w panelu admina
     */
    function polskipodarek_add_alcohol_meta_box() {
        
        add_meta_box(
            'polskipodarek_product_alcohol_fields', 
            __('Produkt alkoholowy', 'polskipodarek'), 
            'polskipodarek_alcohol_meta_box_content', 
            'product', 
            'side', 
            'core'
        );
    }

    /**
     * Zawartość meta box dla oznaczania produktu jako alkoholowy
     */
    function polskipodarek_alcohol_meta_box_content() {
        
        global $post;

        // Nonce dla bezpieczeństwa
        wp_nonce_field('polskipodarek_alcohol_meta_box', 'polskipodarek_alcohol_meta_box_nonce');

        // Pobierz aktualną wartość
        $current_value = get_post_meta($post->ID, 'product_alcohol', true);
        $current_value = $current_value ? $current_value : 'nie';

        // Generuj select field
        woocommerce_wp_select(
            array(
                'id' => 'product_alcohol_select',
                'name' => 'product_alcohol',
                'label' => __('Czy produkt zawiera alkohol?', 'polskipodarek'),
                'value' => $current_value,
                'options' => array(
                    'nie' => __('Nie', 'polskipodarek'),
                    'tak' => __('Tak', 'polskipodarek')
                ),
                'desc_tip' => true,
                'description' => __('Oznacz produkt jako alkoholowy jeśli wymaga weryfikacji wieku.', 'polskipodarek')
            )
        );
    }

    /**
     * Zapisuje dane z meta box
     */
    function polskipodarek_save_alcohol_meta_box($post_id) {
        
        // Sprawdź nonce
        if (!isset($_POST['polskipodarek_alcohol_meta_box_nonce'])) {
            return $post_id;
        }

        $nonce = $_POST['polskipodarek_alcohol_meta_box_nonce'];

        // Weryfikuj nonce
        if (!wp_verify_nonce($nonce, 'polskipodarek_alcohol_meta_box')) {
            return $post_id;
        }

        // Sprawdź autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        // Sprawdź uprawnienia
        if (isset($_POST['post_type']) && 'product' == $_POST['post_type']) {
            if (!current_user_can('edit_product', $post_id)) {
                return $post_id;
            }
        } else {
            if (!current_user_can('edit_post', $post_id)) {
                return $post_id;
            }
        }

        // Zapisz dane
        if (isset($_POST['product_alcohol'])) {
            $alcohol_value = sanitize_text_field($_POST['product_alcohol']);
            update_post_meta($post_id, 'product_alcohol', $alcohol_value);
        }
    }

    /**
     * Helper function - sprawdza czy produkt jest alkoholowy
     */
    function polskipodarek_is_alcohol_product($product_id) {
        
        $is_alcohol = get_post_meta($product_id, 'product_alcohol', true);
        return ($is_alcohol === 'tak');
    }

    /**
     * Helper function - sprawdza czy koszyk zawiera produkty alkoholowe
     */
    function polskipodarek_cart_has_alcohol() {
        
        if (!WC()->cart) {
            return false;
        }

        foreach (WC()->cart->get_cart() as $cart_item) {
            if (polskipodarek_is_alcohol_product($cart_item['product_id'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Dodaje informację o produkcie alkoholowym w panelu admina zamówienia
     */
    function polskipodarek_display_alcohol_admin_order_meta($order) {
        
        $alcohol_confirmed = get_post_meta($order->get_id(), 'alcohol', true);
        
        if ($alcohol_confirmed) {
            echo '<div><strong>' . __('Potwierdzenie pełnoletności:', 'polskipodarek') . '</strong> ';
            echo ($alcohol_confirmed ? __('Potwierdzone', 'polskipodarek') : __('Nie potwierdzone', 'polskipodarek'));
            echo '</div>';
        }
    }
    add_action('woocommerce_admin_order_data_after_billing_address', 'polskipodarek_display_alcohol_admin_order_meta');

} // Koniec sprawdzenia czy wtyczka nie jest aktywna