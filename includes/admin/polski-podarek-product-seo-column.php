<?php

/**
 * Polski Podarek - Kolumna "SEO" na liście produktów WooCommerce
 * Dodaje kolumnę dla pola meta "seo_text"
 * 
 * @package PolskiPodarek
 * @version 1.0.0
 */

// Zabezpieczenie przed bezpośrednim dostępem
if (!defined('ABSPATH')) {
    exit;
}

class PolskiPodarekProductSeoColumn {
    
    /**
     * Inicjalizacja
     */
    public static function init() {
        
        // Sprawdź czy wtyczka Indemi Core nie jest aktywna i czy WooCommerce istnieje
        if (function_exists('indemi_core_init') || !class_exists('WooCommerce')) {
            return;
        }
        
        // Dodaj kolumnę do listy produktów
        add_filter('manage_product_posts_columns', array(__CLASS__, 'add_seo_column'), 21);
        
        // Wypełnij zawartość kolumny
        add_action('manage_product_posts_custom_column', array(__CLASS__, 'populate_seo_column'), 10, 2);
        
        // Dodaj style CSS
        add_action('admin_head', array(__CLASS__, 'add_column_styles'));
        
        // Dodaj możliwość sortowania
        add_filter('manage_edit-product_sortable_columns', array(__CLASS__, 'make_seo_column_sortable'));
        
        // Obsługa sortowania
        add_action('pre_get_posts', array(__CLASS__, 'handle_seo_column_sorting'));
        
        // Dodaj szybkie akcje (Quick Edit)
        add_action('quick_edit_custom_box', array(__CLASS__, 'add_quick_edit_seo'), 10, 2);
        
        // Zapisz dane z Quick Edit
        add_action('save_post', array(__CLASS__, 'save_quick_edit_seo'));
        
        // Dodaj JavaScript dla Quick Edit
        add_action('admin_footer', array(__CLASS__, 'add_quick_edit_javascript'));
    }
    
    /**
     * Dodaje kolumnę "SEO" do listy produktów
     */
    public static function add_seo_column($columns) {
        
        // Dodaj kolumnę przed kolumną "Data"
        $new_columns = array();
        
        foreach ($columns as $key => $title) {
            if ($key === 'date') {
                $new_columns['seo_status'] = __('SEO', 'polskipodarek');
            }
            $new_columns[$key] = $title;
        }
        
        return $new_columns;
    }
    
    /**
     * Wypełnia zawartość kolumny "SEO"
     */
    public static function populate_seo_column($column, $post_id) {
        
        if ($column !== 'seo_status') {
            return;
        }
        
        $seo_text = get_post_meta($post_id, 'seo_text', true);
        $seo_length = strlen($seo_text);
        
        if (!empty($seo_text)) {
            
            // Określ status na podstawie długości
            if ($seo_length >= 150 && $seo_length <= 160) {
                $status_class = 'pp-seo-optimal';
                $status_icon = 'dashicons-yes-alt';
                $status_text = __('Optymalny', 'polskipodarek');
                $status_color = '#00a32a';
            } elseif ($seo_length > 160) {
                $status_class = 'pp-seo-long';
                $status_icon = 'dashicons-warning';
                $status_text = __('Za długi', 'polskipodarek');
                $status_color = '#d63638';
            } elseif ($seo_length > 120) {
                $status_class = 'pp-seo-short';
                $status_icon = 'dashicons-info';
                $status_text = __('Za krótki', 'polskipodarek');
                $status_color = '#ff922b';
            } else {
                $status_class = 'pp-seo-very-short';
                $status_icon = 'dashicons-warning';
                $status_text = __('Bardzo krótki', 'polskipodarek');
                $status_color = '#d63638';
            }
            
            echo '<span class="' . $status_class . '" title="' . sprintf(__('Opis SEO: %d znaków', 'polskipodarek'), $seo_length) . '" style="color: ' . $status_color . ';">';
            echo '<span class="dashicons ' . $status_icon . '"></span> ';
            echo '<span class="pp-seo-length">' . $seo_length . '</span>';
            echo '</span>';
            
            // Podgląd tekstu (tooltip)
            $preview = wp_trim_words($seo_text, 10);
            echo '<div class="pp-seo-preview" title="' . esc_attr($preview) . '"></div>';
            
        } else {
            echo '<span class="pp-seo-missing" title="' . __('Brak opisu SEO', 'polskipodarek') . '">';
            echo '<span class="dashicons dashicons-minus"></span> ';
            echo __('Brak', 'polskipodarek');
            echo '</span>';
        }
        
        // Dodaj hidden data dla Quick Edit
        echo '<div class="hidden pp-seo-data" data-seo="' . esc_attr($seo_text) . '"></div>';
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
        .column-seo_status {
            width: 100px;
            text-align: center;
        }
        
        .pp-seo-optimal,
        .pp-seo-long,
        .pp-seo-short,
        .pp-seo-very-short,
        .pp-seo-missing {
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        
        .pp-seo-optimal {
            color: #00a32a;
        }
        
        .pp-seo-long,
        .pp-seo-very-short {
            color: #d63638;
        }
        
        .pp-seo-short {
            color: #ff922b;
        }
        
        .pp-seo-missing {
            color: #646970;
        }
        
        .pp-seo-optimal .dashicons,
        .pp-seo-long .dashicons,
        .pp-seo-short .dashicons,
        .pp-seo-very-short .dashicons,
        .pp-seo-missing .dashicons {
            font-size: 16px;
            width: 16px;
            height: 16px;
        }
        
        .pp-seo-length {
            font-size: 11px;
            font-weight: normal;
        }
        
        /* Quick Edit styles */
        .pp-quick-edit-seo {
            clear: both;
            padding: 5px 0;
        }
        
        .pp-quick-edit-seo label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .pp-quick-edit-seo textarea {
            width: 100%;
            max-width: 500px;
            height: 60px;
            resize: vertical;
        }
        
        .pp-seo-char-counter {
            font-size: 11px;
            color: #646970;
            margin-top: 3px;
        }
        
        .pp-seo-char-counter.optimal {
            color: #00a32a;
            font-weight: bold;
        }
        
        .pp-seo-char-counter.warning {
            color: #ff922b;
            font-weight: bold;
        }
        
        .pp-seo-char-counter.error {
            color: #d63638;
            font-weight: bold;
        }
        
        /* Responsive */
        @media screen and (max-width: 782px) {
            .column-seo_status {
                display: none;
            }
        }
        </style>
        <?php
    }
    
    /**
     * Dodaje możliwość sortowania kolumny
     */
    public static function make_seo_column_sortable($columns) {
        
        $columns['seo_status'] = 'seo_status';
        return $columns;
    }
    
    /**
     * Obsługuje sortowanie według statusu SEO
     */
    public static function handle_seo_column_sorting($query) {
        
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }
        
        $orderby = $query->get('orderby');
        
        if ($orderby === 'seo_status') {
            $query->set('meta_key', 'seo_text');
            $query->set('orderby', 'meta_value');
            
            // Sortuj tak, aby produkty bez SEO były na końcu
            $query->set('meta_query', array(
                'relation' => 'OR',
                array(
                    'key' => 'seo_text',
                    'compare' => 'EXISTS'
                ),
                array(
                    'key' => 'seo_text',
                    'compare' => 'NOT EXISTS'
                )
            ));
        }
    }
    
    /**
     * Dodaje pole "SEO" do Quick Edit
     */
    public static function add_quick_edit_seo($column_name, $post_type) {
        
        if ($column_name !== 'seo_status' || $post_type !== 'product') {
            return;
        }
        
        ?>
        <fieldset class="inline-edit-col-right">
            <div class="inline-edit-col">
                <div class="pp-quick-edit-seo">
                    <label for="seo_text_quick">
                        <?php _e('Opis SEO:', 'polskipodarek'); ?>
                    </label>
                    <textarea name="seo_text" id="seo_text_quick" placeholder="<?php _e('Wprowadź opis SEO (150-160 znaków)...', 'polskipodarek'); ?>"></textarea>
                    <div class="pp-seo-char-counter">0 <?php _e('znaków', 'polskipodarek'); ?></div>
                </div>
            </div>
        </fieldset>
        <?php
    }
    
    /**
     * Zapisuje dane z Quick Edit
     */
    public static function save_quick_edit_seo($post_id) {
        
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
        
        // Zapisz opis SEO
        if (isset($_POST['seo_text'])) {
            $seo_text = sanitize_textarea_field($_POST['seo_text']);
            update_post_meta($post_id, 'seo_text', $seo_text);
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
            
            // Licznik znaków
            function updateSeoCharCounter() {
                var textarea = $('#seo_text_quick');
                var counter = $('.pp-seo-char-counter');
                var length = textarea.val().length;
                
                counter.text(length + ' <?php _e("znaków", "polskipodarek"); ?>');
                counter.removeClass('optimal warning error');
                
                if (length >= 150 && length <= 160) {
                    counter.addClass('optimal');
                } else if (length > 160 || length < 120) {
                    counter.addClass('error');
                } else if (length > 140) {
                    counter.addClass('warning');
                }
            }
            
            // Obsługa Quick Edit
            $('body').on('click', '.editinline', function() {
                
                // Pobierz ID produktu
                var post_id = $(this).closest('tr').attr('id');
                post_id = post_id.replace('post-', '');
                
                // Znajdź dane SEO
                var seo_data = $('#post-' + post_id + ' .pp-seo-data').data('seo');
                
                // Ustaw wartość w textarea
                $('#seo_text_quick').val(seo_data || '');
                
                // Zaktualizuj licznik
                updateSeoCharCounter();
            });
            
            // Aktualizuj licznik podczas pisania
            $('body').on('input', '#seo_text_quick', updateSeoCharCounter);
            
            // Dodaj wskaźnik ładowania podczas zapisywania
            $('body').on('click', '.save', function() {
                var row = $(this).closest('tr');
                var seo_textarea = row.find('#seo_text_quick');
                
                if (seo_textarea.length) {
                    seo_textarea.prop('disabled', true);
                    row.find('.pp-quick-edit-seo label').append(' <span class="spinner is-active" style="float: none;"></span>');
                }
            });
        });
        </script>
        <?php
    }
    
    /**
     * Dodaje bulk actions dla SEO
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
        
        $bulk_actions['generate_seo_descriptions'] = __('Generuj opisy SEO', 'polskipodarek');
        $bulk_actions['clear_seo_descriptions'] = __('Wyczyść opisy SEO', 'polskipodarek');
        
        return $bulk_actions;
    }
    
    /**
     * Obsługuje bulk actions
     */
    public static function handle_bulk_actions($redirect_to, $doaction, $post_ids) {
        
        if ($doaction === 'generate_seo_descriptions') {
            $generated = 0;
            
            foreach ($post_ids as $post_id) {
                $existing_seo = get_post_meta($post_id, 'seo_text', true);
                
                // Generuj tylko jeśli nie ma opisu
                if (empty($existing_seo)) {
                    $seo_text = self::generate_auto_seo_text($post_id);
                    if (!empty($seo_text)) {
                        update_post_meta($post_id, 'seo_text', $seo_text);
                        $generated++;
                    }
                }
            }
            
            $redirect_to = add_query_arg('bulk_seo_generated', $generated, $redirect_to);
        }
        
        if ($doaction === 'clear_seo_descriptions') {
            foreach ($post_ids as $post_id) {
                delete_post_meta($post_id, 'seo_text');
            }
            $redirect_to = add_query_arg('bulk_seo_cleared', count($post_ids), $redirect_to);
        }
        
        return $redirect_to;
    }
    
    /**
     * Pokazuje notices po bulk actions
     */
    public static function bulk_action_notices() {
        
        if (!empty($_REQUEST['bulk_seo_generated'])) {
            $count = intval($_REQUEST['bulk_seo_generated']);
            printf(
                '<div id="message" class="updated notice is-dismissible"><p>' .
                _n('Wygenerowano opis SEO dla %d produktu.',
                   'Wygenerowano opisy SEO dla %d produktów.',
                   $count, 'polskipodarek') .
                '</p></div>',
                $count
            );
        }
        
        if (!empty($_REQUEST['bulk_seo_cleared'])) {
            $count = intval($_REQUEST['bulk_seo_cleared']);
            printf(
                '<div id="message" class="updated notice is-dismissible"><p>' .
                _n('Wyczyszczono opis SEO dla %d produktu.',
                   'Wyczyszczono opisy SEO dla %d produktów.',
                   $count, 'polskipodarek') .
                '</p></div>',
                $count
            );
        }
    }
    
    /**
     * Generuje automatyczny opis SEO
     */
    private static function generate_auto_seo_text($post_id) {
        
        $product = get_post($post_id);
        if (!$product) {
            return '';
        }
        
        $title = get_the_title($post_id);
        $excerpt = get_the_excerpt($post_id);
        $categories = get_the_terms($post_id, 'product_cat');
        
        // Podstawa opisu
        $seo_text = $title;
        
        // Dodaj kategorię jeśli istnieje
        if ($categories && !is_wp_error($categories)) {
            $category_name = $categories[0]->name;
            $seo_text .= ' - ' . $category_name;
        }
        
        // Dodaj fragment opisu jeśli istnieje
        if (!empty($excerpt)) {
            $short_excerpt = wp_trim_words($excerpt, 8);
            if (strlen($seo_text . '. ' . $short_excerpt) <= 140) {
                $seo_text .= '. ' . $short_excerpt;
            }
        }
        
        // Dodaj call-to-action jeśli jest miejsce
        $cta_options = array(
            __('Sprawdź naszą ofertę!', 'polskipodarek'),
            __('Zamów już dziś!', 'polskipodarek'),
            __('Dostępny w naszym sklepie.', 'polskipodarek'),
            __('Wysokiej jakości w atrakcyjnej cenie.', 'polskipodarek')
        );
        
        $cta = $cta_options[array_rand($cta_options)];
        if (strlen($seo_text . ' ' . $cta) <= 160) {
            $seo_text .= ' ' . $cta;
        }
        
        return $seo_text;
    }
}

// Inicjalizuj kolumnę
PolskiPodarekProductSeoColumn::init();

// Opcjonalnie: Inicjalizuj bulk actions
// PolskiPodarekProductSeoColumn::add_bulk_actions();