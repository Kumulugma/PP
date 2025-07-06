<?php

/**
 * Polski Podarek - Kolumna "Alkoholowy" na liście produktów WooCommerce
 * Dodaje kolumnę do admin/edit.php?post_type=product
 * 
 * @package PolskiPodarek
 * @version 1.0.0
 */

// Zabezpieczenie przed bezpośrednim dostępem
if (!defined('ABSPATH')) {
    exit;
}

class PolskiPodarekProductAlcoholColumn {
    
    /**
     * Inicjalizacja
     */
    public static function init() {
        
        // Sprawdź czy wtyczka Indemi Core nie jest aktywna i czy WooCommerce istnieje
        if (function_exists('indemi_core_init') || !class_exists('WooCommerce')) {
            return;
        }
        
        // Dodaj kolumnę do listy produktów
        add_filter('manage_product_posts_columns', array(__CLASS__, 'add_alcohol_column'), 20);
        
        // Wypełnij zawartość kolumny
        add_action('manage_product_posts_custom_column', array(__CLASS__, 'populate_alcohol_column'), 10, 2);
        
        // Dodaj style CSS
        add_action('admin_head', array(__CLASS__, 'add_column_styles'));
        
        // Dodaj możliwość sortowania (opcjonalne)
        add_filter('manage_edit-product_sortable_columns', array(__CLASS__, 'make_alcohol_column_sortable'));
        
        // Obsługa sortowania
        add_action('pre_get_posts', array(__CLASS__, 'handle_alcohol_column_sorting'));
        
        // Dodaj szybkie akcje (Quick Edit)
        add_action('quick_edit_custom_box', array(__CLASS__, 'add_quick_edit_alcohol'), 10, 2);
        
        // Zapisz dane z Quick Edit
        add_action('save_post', array(__CLASS__, 'save_quick_edit_alcohol'));
        
        // Dodaj JavaScript dla Quick Edit
        add_action('admin_footer', array(__CLASS__, 'add_quick_edit_javascript'));
    }
    
    /**
     * Dodaje kolumnę "Alkoholowy" do listy produktów
     */
    public static function add_alcohol_column($columns) {
        
        // Dodaj kolumnę przed kolumną "Data"
        $new_columns = array();
        
        foreach ($columns as $key => $title) {
            if ($key === 'date') {
                $new_columns['alcohol_status'] = __('Alkoholowy', 'polskipodarek');
            }
            $new_columns[$key] = $title;
        }
        
        return $new_columns;
    }
    
    /**
     * Wypełnia zawartość kolumny "Alkoholowy"
     */
    public static function populate_alcohol_column($column, $post_id) {
        
        if ($column !== 'alcohol_status') {
            return;
        }
        
        $is_alcohol = get_post_meta($post_id, 'product_alcohol', true);
        
        if ($is_alcohol === 'tak') {
            echo '<span class="pp-alcohol-yes" title="' . __('Produkt alkoholowy', 'polskipodarek') . '">';
            echo '<span class="dashicons dashicons-warning"></span> ';
            echo __('Tak', 'polskipodarek');
            echo '</span>';
            
            // Dodaj hidden data dla Quick Edit
            echo '<div class="hidden pp-alcohol-data" data-alcohol="tak"></div>';
        } else {
            echo '<span class="pp-alcohol-no" title="' . __('Produkt bezalkoholowy', 'polskipodarek') . '">';
            echo '<span class="dashicons dashicons-yes-alt"></span> ';
            echo __('Nie', 'polskipodarek');
            echo '</span>';
            
            // Dodaj hidden data dla Quick Edit
            echo '<div class="hidden pp-alcohol-data" data-alcohol="nie"></div>';
        }
    }
    
    /**
     * Dodaje style CSS dla kolumny
     */
    public static function add_column_styles() {
        
        // Tylko na stronie listy produktów
        $screen = get_current_screen();
        if (!$screen || $screen->id !== 'edit-product') {
            return;
        }
        
        ?>
        <style>
        .column-alcohol_status {
            width: 120px;
            text-align: center;
        }
        
        .pp-alcohol-yes {
            color: #d63638;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        
        .pp-alcohol-no {
            color: #00a32a;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        
        .pp-alcohol-yes .dashicons,
        .pp-alcohol-no .dashicons {
            font-size: 16px;
            width: 16px;
            height: 16px;
        }
        
        .pp-alcohol-yes .dashicons {
            color: #d63638;
        }
        
        .pp-alcohol-no .dashicons {
            color: #00a32a;
        }
        
        /* Quick Edit styles */
        .pp-quick-edit-alcohol {
            clear: both;
            padding: 5px 0;
        }
        
        .pp-quick-edit-alcohol label {
            display: inline-block;
            width: 150px;
            font-weight: bold;
        }
        
        .pp-quick-edit-alcohol select {
            width: 200px;
        }
        
        /* Responsive */
        @media screen and (max-width: 782px) {
            .column-alcohol_status {
                display: none;
            }
        }
        </style>
        <?php
    }
    
    /**
     * Dodaje możliwość sortowania kolumny
     */
    public static function make_alcohol_column_sortable($columns) {
        
        $columns['alcohol_status'] = 'alcohol_status';
        return $columns;
    }
    
    /**
     * Obsługuje sortowanie według statusu alkoholowego
     */
    public static function handle_alcohol_column_sorting($query) {
        
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }
        
        $orderby = $query->get('orderby');
        
        if ($orderby === 'alcohol_status') {
            $query->set('meta_key', 'product_alcohol');
            $query->set('orderby', 'meta_value');
        }
    }
    
    /**
     * Dodaje pole "Alkoholowy" do Quick Edit
     */
    public static function add_quick_edit_alcohol($column_name, $post_type) {
        
        if ($column_name !== 'alcohol_status' || $post_type !== 'product') {
            return;
        }
        
        ?>
        <fieldset class="inline-edit-col-right">
            <div class="inline-edit-col">
                <div class="pp-quick-edit-alcohol">
                    <label for="product_alcohol_quick">
                        <?php _e('Alkoholowy:', 'polskipodarek'); ?>
                    </label>
                    <select name="product_alcohol" id="product_alcohol_quick">
                        <option value="nie"><?php _e('Nie', 'polskipodarek'); ?></option>
                        <option value="tak"><?php _e('Tak', 'polskipodarek'); ?></option>
                    </select>
                </div>
            </div>
        </fieldset>
        <?php
    }
    
    /**
     * Zapisuje dane z Quick Edit
     */
    public static function save_quick_edit_alcohol($post_id) {
        
        // Sprawdź czy to Quick Edit
        if (!isset($_POST['_inline_edit']) || !wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce')) {
            return;
        }
        
        // Sprawdź typ postu
        if (get_post_type($post_id) !== 'product') {
            return;
        }
        
        // Sprawdź uprawnienia
        if (!current_user_can('edit_product', $post_id)) {
            return;
        }
        
        // Zapisz status alkoholowy
        if (isset($_POST['product_alcohol'])) {
            $alcohol_value = sanitize_text_field($_POST['product_alcohol']);
            if (in_array($alcohol_value, array('tak', 'nie'))) {
                update_post_meta($post_id, 'product_alcohol', $alcohol_value);
            }
        }
    }
    
    /**
     * Dodaje JavaScript dla Quick Edit
     */
    public static function add_quick_edit_javascript() {
        
        // Tylko na stronie listy produktów
        $screen = get_current_screen();
        if (!$screen || $screen->id !== 'edit-product') {
            return;
        }
        
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            
            // Obsługa Quick Edit
            $('body').on('click', '.editinline', function() {
                
                // Pobierz ID produktu
                var post_id = $(this).closest('tr').attr('id');
                post_id = post_id.replace('post-', '');
                
                // Znajdź dane o alkoholu
                var alcohol_data = $('#post-' + post_id + ' .pp-alcohol-data').data('alcohol');
                
                // Ustaw wartość w select
                if (alcohol_data) {
                    $('#product_alcohol_quick').val(alcohol_data);
                } else {
                    $('#product_alcohol_quick').val('nie');
                }
            });
            
            // Dodaj wskaźnik ładowania podczas zapisywania
            $('body').on('click', '.save', function() {
                var row = $(this).closest('tr');
                var alcohol_select = row.find('#product_alcohol_quick');
                
                if (alcohol_select.length) {
                    alcohol_select.prop('disabled', true);
                    row.find('.pp-quick-edit-alcohol label').append(' <span class="spinner is-active" style="float: none;"></span>');
                }
            });
        });
        </script>
        <?php
    }
    
    /**
     * Dodaje bulk actions dla masowego oznaczania
     */
    public static function add_bulk_actions() {
        
        // Dodaj bulk actions do dropdown
        add_filter('bulk_actions-edit-product', array(__CLASS__, 'register_bulk_actions'));
        
        // Obsługuj bulk actions
        add_filter('handle_bulk_actions-edit-product', array(__CLASS__, 'handle_bulk_actions'), 10, 3);
        
        // Pokaż notice po bulk action
        add_action('admin_notices', array(__CLASS__, 'bulk_action_notices'));
    }
    
    /**
     * Rejestruje bulk actions
     */
    public static function register_bulk_actions($bulk_actions) {
        
        $bulk_actions['mark_as_alcohol'] = __('Oznacz jako alkoholowe', 'polskipodarek');
        $bulk_actions['mark_as_non_alcohol'] = __('Oznacz jako bezalkoholowe', 'polskipodarek');
        
        return $bulk_actions;
    }
    
    /**
     * Obsługuje bulk actions
     */
    public static function handle_bulk_actions($redirect_to, $doaction, $post_ids) {
        
        if ($doaction === 'mark_as_alcohol') {
            foreach ($post_ids as $post_id) {
                update_post_meta($post_id, 'product_alcohol', 'tak');
            }
            $redirect_to = add_query_arg('bulk_alcohol_marked', count($post_ids), $redirect_to);
        }
        
        if ($doaction === 'mark_as_non_alcohol') {
            foreach ($post_ids as $post_id) {
                update_post_meta($post_id, 'product_alcohol', 'nie');
            }
            $redirect_to = add_query_arg('bulk_non_alcohol_marked', count($post_ids), $redirect_to);
        }
        
        return $redirect_to;
    }
    
    /**
     * Pokazuje notices po bulk actions
     */
    public static function bulk_action_notices() {
        
        if (!empty($_REQUEST['bulk_alcohol_marked'])) {
            $count = intval($_REQUEST['bulk_alcohol_marked']);
            printf(
                '<div id="message" class="updated notice is-dismissible"><p>' .
                _n('Oznaczono %d produkt jako alkoholowy.',
                   'Oznaczono %d produktów jako alkoholowe.',
                   $count, 'polskipodarek') .
                '</p></div>',
                $count
            );
        }
        
        if (!empty($_REQUEST['bulk_non_alcohol_marked'])) {
            $count = intval($_REQUEST['bulk_non_alcohol_marked']);
            printf(
                '<div id="message" class="updated notice is-dismissible"><p>' .
                _n('Oznaczono %d produkt jako bezalkoholowy.',
                   'Oznaczono %d produktów jako bezalkoholowe.',
                   $count, 'polskipodarek') .
                '</p></div>',
                $count
            );
        }
    }
}

// Inicjalizuj kolumnę
PolskiPodarekProductAlcoholColumn::init();

// Opcjonalnie: Inicjalizuj bulk actions
// PolskiPodarekProductAlcoholColumn::add_bulk_actions();