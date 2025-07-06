<?php

/**
 * SEO Product Metabox
 * Dodaje metabox "Opis SEO" do produktów WooCommerce
 * Używane tylko gdy wtyczka indemi-premium-product-metabox nie jest aktywna
 */

// Sprawdź czy wtyczka nie jest aktywna
if (!function_exists('indemi_premium_product_metabox_init')) {
    
    /**
     * Inicjalizacja metabox SEO dla produktów
     */
    function polskipodarek_seo_product_metabox_init() {
        
        // Tylko w panelu administracyjnym
        if (is_admin()) {
            polskipodarek_seo_metabox_admin_init();
        }
    }
    add_action('init', 'polskipodarek_seo_product_metabox_init');

    /**
     * Inicjalizacja funkcji administracyjnych
     */
    function polskipodarek_seo_metabox_admin_init() {
        
        // Dodaj meta box do produktów
        add_action('add_meta_boxes', 'polskipodarek_add_seo_metabox');
        
        // Zapisz dane meta box
        add_action('save_post', 'polskipodarek_save_seo_metabox', 10, 1);
    }

    /**
     * Dodaje metabox SEO do edycji produktu
     */
    function polskipodarek_add_seo_metabox() {
        
        add_meta_box(
            'polskipodarek_seo_description_metabox',
            __('Opis SEO', 'polskipodarek'),
            'polskipodarek_seo_metabox_content',
            'product',
            'side',
            'core'
        );
    }

    /**
     * Zawartość metabox SEO
     */
    function polskipodarek_seo_metabox_content() {
        
        global $post;

        // Nonce dla bezpieczeństwa
        wp_nonce_field('polskipodarek_seo_metabox', 'polskipodarek_seo_metabox_nonce');

        // Pobierz aktualną wartość
        $seo_text = get_post_meta($post->ID, 'seo_text', true);

        // Generuj pole textarea używając funkcji WooCommerce
        woocommerce_wp_textarea_input(
            array(
                'id' => 'seo_text_field',
                'name' => 'seo_text',
                'placeholder' => __('Wprowadź opis SEO dla tego produktu...', 'polskipodarek'),
                'label' => __('Tekst SEO:', 'polskipodarek'),
                'wrapper_class' => 'form-field-wide',
                'value' => $seo_text,
                'desc_tip' => true,
                'description' => __('Ten tekst może być używany w opisach SEO, schematach lub innych celach marketingowych.', 'polskipodarek'),
                'rows' => 4
            )
        );

        // Dodaj pomocne informacje
        echo '<div style="margin-top: 10px; padding: 10px; background: #f0f0f1; border-radius: 4px;">';
        echo '<small><strong>' . __('Wskazówki:', 'polskipodarek') . '</strong><br>';
        echo '• ' . __('Optymalny opis SEO: 150-160 znaków', 'polskipodarek') . '<br>';
        echo '• ' . __('Użyj słów kluczowych związanych z produktem', 'polskipodarek') . '<br>';
        echo '• ' . __('Napisz atrakcyjny opis zachęcający do kliknięcia', 'polskipodarek') . '</small>';
        echo '</div>';

        // Licznik znaków
        echo '<div style="margin-top: 5px;">';
        echo '<small id="seo-char-count">Znaki: 0</small>';
        echo '</div>';

        // JavaScript dla licznika znaków
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('seo_text_field');
            const counter = document.getElementById('seo-char-count');
            
            if (textarea && counter) {
                function updateCounter() {
                    const length = textarea.value.length;
                    counter.textContent = 'Znaki: ' + length;
                    
                    // Zmień kolor w zależności od długości
                    if (length > 160) {
                        counter.style.color = '#dc3232'; // Czerwony
                    } else if (length > 150) {
                        counter.style.color = '#ffb900'; // Pomarańczowy
                    } else if (length > 50) {
                        counter.style.color = '#46b450'; // Zielony
                    } else {
                        counter.style.color = '#666'; // Szary
                    }
                }
                
                // Aktualizuj licznik przy ładowaniu i zmianie
                updateCounter();
                textarea.addEventListener('input', updateCounter);
                textarea.addEventListener('keyup', updateCounter);
            }
        });
        </script>
        <?php
    }

    /**
     * Zapisuje dane z metabox SEO
     */
    function polskipodarek_save_seo_metabox($post_id) {
        
        // Sprawdź czy to autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        // Sprawdź nonce
        if (!isset($_POST['polskipodarek_seo_metabox_nonce'])) {
            return $post_id;
        }

        if (!wp_verify_nonce($_POST['polskipodarek_seo_metabox_nonce'], 'polskipodarek_seo_metabox')) {
            return $post_id;
        }

        // Sprawdź typ postu
        if (get_post_type($post_id) !== 'product') {
            return $post_id;
        }

        // Sprawdź uprawnienia
        if (!current_user_can('edit_product', $post_id)) {
            return $post_id;
        }

        // Zapisz dane
        if (isset($_POST['seo_text'])) {
            $seo_text = sanitize_textarea_field($_POST['seo_text']);
            update_post_meta($post_id, 'seo_text', $seo_text);
        }
    }

    /**
     * Helper function - pobiera tekst SEO produktu
     */
    function polskipodarek_get_product_seo_text($product_id) {
        
        return get_post_meta($product_id, 'seo_text', true);
    }

    /**
     * Helper function - sprawdza czy produkt ma tekst SEO
     */
    function polskipodarek_product_has_seo_text($product_id) {
        
        $seo_text = polskipodarek_get_product_seo_text($product_id);
        return !empty($seo_text);
    }

    /**
     * Wyświetla tekst SEO w kolumnie produktów (opcjonalne)
     */
    function polskipodarek_add_seo_column_to_products($columns) {
        
        $columns['seo_text'] = __('SEO Text', 'polskipodarek');
        return $columns;
    }

    /**
     * Zawartość kolumny SEO w liście produktów
     */
    function polskipodarek_display_seo_column_content($column, $post_id) {
        
        if ($column === 'seo_text') {
            $seo_text = polskipodarek_get_product_seo_text($post_id);
            
            if (!empty($seo_text)) {
                $truncated = wp_trim_words($seo_text, 10, '...');
                echo '<span title="' . esc_attr($seo_text) . '">' . esc_html($truncated) . '</span>';
                echo '<br><small style="color: #666;">(' . strlen($seo_text) . ' znaków)</small>';
            } else {
                echo '<span style="color: #999;">—</span>';
            }
        }
    }

    // Opcjonalnie: dodaj kolumnę SEO do listy produktów
    // add_filter('manage_product_posts_columns', 'polskipodarek_add_seo_column_to_products');
    // add_action('manage_product_posts_custom_column', 'polskipodarek_display_seo_column_content', 10, 2);

    /**
     * Dodaje shortcode do wyświetlania tekstu SEO
     */
    function polskipodarek_seo_text_shortcode($atts) {
        
        $atts = shortcode_atts(array(
            'id' => get_the_ID(),
            'fallback' => ''
        ), $atts);

        $seo_text = polskipodarek_get_product_seo_text($atts['id']);
        
        if (!empty($seo_text)) {
            return '<div class="product-seo-text">' . wp_kses_post($seo_text) . '</div>';
        } else if (!empty($atts['fallback'])) {
            return '<div class="product-seo-fallback">' . wp_kses_post($atts['fallback']) . '</div>';
        }
        
        return '';
    }
    add_shortcode('product_seo_text', 'polskipodarek_seo_text_shortcode');

} // Koniec sprawdzenia czy wtyczka nie jest aktywna