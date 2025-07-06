<?php

/**
 * Polski Podarek - Migracja pola ACF custom_product_title na metabox szablonu
 * Przepina pole z ACF na natywny metabox z zachowaniem danych
 * 
 * @package PolskiPodarek
 * @version 1.0.0
 */

// Zabezpieczenie przed bezpośrednim dostępem
if (!defined('ABSPATH')) {
    exit;
}

class PolskiPodarekCustomProductTitle {
    
    /**
     * Inicjalizacja
     */
    public static function init() {
        
        // Sprawdź czy wtyczka Indemi Core nie jest aktywna
        if (function_exists('indemi_core_init')) {
            return;
        }
        
        // Dodaj metabox do produktów
        add_action('add_meta_boxes', array(__CLASS__, 'add_custom_title_metabox'));
        
        // Zapisz dane metabox
        add_action('save_post', array(__CLASS__, 'save_custom_title_metabox'));
        
        // Dodaj kolumnę na liście produktów
        add_filter('manage_product_posts_columns', array(__CLASS__, 'add_custom_title_column'), 22);
        add_action('manage_product_posts_custom_column', array(__CLASS__, 'populate_custom_title_column'), 10, 2);
        
        // Quick Edit
        add_action('quick_edit_custom_box', array(__CLASS__, 'add_quick_edit_custom_title'), 10, 2);
        add_action('save_post', array(__CLASS__, 'save_quick_edit_custom_title'));
        add_action('admin_footer', array(__CLASS__, 'add_quick_edit_javascript'));
        
        // Dodaj narzędzie migracji w panelu admina
        add_action('init', array(__CLASS__, 'register_migration_page'), 25);
        
        // Obsługa AJAX migracji
        add_action('wp_ajax_migrate_acf_custom_titles', array(__CLASS__, 'ajax_migrate_acf_titles'));
        
        // Dodaj style CSS
        add_action('admin_head', array(__CLASS__, 'add_admin_styles'));
    }
    
    /**
     * Dodaje metabox do edycji produktu
     */
    public static function add_custom_title_metabox() {
        
        add_meta_box(
            'polskipodarek_custom_product_title',
            __('Niestandardowy tytuł produktu', 'polskipodarek'),
            array(__CLASS__, 'custom_title_metabox_content'),
            'product',
            'side',
            'high'
        );
    }
    
    /**
     * Zawartość metabox
     */
    public static function custom_title_metabox_content() {
        
        global $post;
        
        // Nonce dla bezpieczeństwa
        wp_nonce_field('polskipodarek_custom_title_metabox', 'polskipodarek_custom_title_nonce');
        
        // Pobierz aktualną wartość (sprawdź zarówno nowe pole jak i ACF)
        $custom_title = get_post_meta($post->ID, 'custom_product_title', true);
        
        // Jeśli nie ma w nowym polu, sprawdź ACF
        if (empty($custom_title) && function_exists('get_field')) {
            $acf_title = get_field('custom_product_title', $post->ID);
            if (!empty($acf_title)) {
                $custom_title = $acf_title;
                // Informacja o migracji
                echo '<div class="notice notice-info inline" style="margin: 0 0 10px 0; padding: 8px 12px;">';
                echo '<p style="margin: 0;"><strong>' . __('Uwaga:', 'polskipodarek') . '</strong> ';
                echo __('Znaleziono tytuł z ACF. Zapisz produkt, aby zmigrować dane.', 'polskipodarek') . '</p>';
                echo '</div>';
            }
        }
        
        ?>
        <div class="pp-custom-title-field">
            <label for="custom_product_title_field">
                <?php _e('Tytuł niestandardowy:', 'polskipodarek'); ?>
            </label>
            
            <input type="text" 
                   id="custom_product_title_field" 
                   name="custom_product_title" 
                   value="<?php echo esc_attr($custom_title); ?>" 
                   style="width: 100%;" 
                   placeholder="<?php _e('Wprowadź niestandardowy tytuł...', 'polskipodarek'); ?>">
            
            <p class="description">
                <?php _e('Jeśli wypełnisz to pole, będzie używane zamiast standardowego tytułu produktu.', 'polskipodarek'); ?>
            </p>
            
            <?php if (!empty($custom_title)) : ?>
                <div class="pp-title-preview">
                    <strong><?php _e('Podgląd:', 'polskipodarek'); ?></strong>
                    <span class="pp-preview-text"><?php echo esc_html($custom_title); ?></span>
                </div>
            <?php endif; ?>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#custom_product_title_field').on('input', function() {
                var value = $(this).val();
                var preview = $('.pp-title-preview');
                
                if (value.length > 0) {
                    if (preview.length === 0) {
                        $(this).after('<div class="pp-title-preview"><strong><?php _e("Podgląd:", "polskipodarek"); ?></strong> <span class="pp-preview-text"></span></div>');
                        preview = $('.pp-title-preview');
                    }
                    preview.find('.pp-preview-text').text(value);
                    preview.show();
                } else {
                    preview.hide();
                }
            });
        });
        </script>
        <?php
    }
    
    /**
     * Zapisuje dane z metabox
     */
    public static function save_custom_title_metabox($post_id) {
        
        // Sprawdź nonce
        if (!isset($_POST['polskipodarek_custom_title_nonce'])) {
            return $post_id;
        }
        
        if (!wp_verify_nonce($_POST['polskipodarek_custom_title_nonce'], 'polskipodarek_custom_title_metabox')) {
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
        }
        
        // Zapisz dane
        if (isset($_POST['custom_product_title'])) {
            $custom_title = sanitize_text_field($_POST['custom_product_title']);
            update_post_meta($post_id, 'custom_product_title', $custom_title);
            
            // Automatyczna migracja z ACF jeśli istnieje
            if (empty($custom_title) && function_exists('get_field')) {
                $acf_title = get_field('custom_product_title', $post_id);
                if (!empty($acf_title)) {
                    update_post_meta($post_id, 'custom_product_title', sanitize_text_field($acf_title));
                }
            }
        }
    }
    
    /**
     * Dodaje kolumnę do listy produktów
     */
    public static function add_custom_title_column($columns) {
        
        $new_columns = array();
        
        foreach ($columns as $key => $title) {
            if ($key === 'date') {
                $new_columns['custom_title_status'] = __('Tytuł custom', 'polskipodarek');
            }
            $new_columns[$key] = $title;
        }
        
        return $new_columns;
    }
    
    /**
     * Wypełnia kolumnę
     */
    public static function populate_custom_title_column($column, $post_id) {
        
        if ($column !== 'custom_title_status') {
            return;
        }
        
        $custom_title = get_post_meta($post_id, 'custom_product_title', true);
        $acf_title = function_exists('get_field') ? get_field('custom_product_title', $post_id) : '';
        
        if (!empty($custom_title)) {
            echo '<span class="pp-custom-title-yes" title="' . esc_attr($custom_title) . '">';
            echo '<span class="dashicons dashicons-yes-alt"></span> ';
            echo __('Tak', 'polskipodarek');
            echo '</span>';
        } elseif (!empty($acf_title)) {
            echo '<span class="pp-custom-title-acf" title="' . esc_attr($acf_title) . '">';
            echo '<span class="dashicons dashicons-migrate"></span> ';
            echo __('ACF', 'polskipodarek');
            echo '</span>';
        } else {
            echo '<span class="pp-custom-title-no">';
            echo '<span class="dashicons dashicons-minus"></span> ';
            echo __('Nie', 'polskipodarek');
            echo '</span>';
        }
        
        // Hidden data dla Quick Edit
        echo '<div class="hidden pp-custom-title-data" data-title="' . esc_attr($custom_title) . '" data-acf-title="' . esc_attr($acf_title) . '"></div>';
    }
    
    /**
     * Quick Edit
     */
    public static function add_quick_edit_custom_title($column_name, $post_type) {
        
        if ($column_name !== 'custom_title_status' || $post_type !== 'product') {
            return;
        }
        
        ?>
        <fieldset class="inline-edit-col-right">
            <div class="inline-edit-col">
                <div class="pp-quick-edit-custom-title">
                    <label for="custom_product_title_quick">
                        <?php _e('Tytuł niestandardowy:', 'polskipodarek'); ?>
                    </label>
                    <input type="text" name="custom_product_title" id="custom_product_title_quick" 
                           placeholder="<?php _e('Wprowadź niestandardowy tytuł...', 'polskipodarek'); ?>" 
                           style="width: 100%;">
                    <p class="description">
                        <?php _e('Zostaw puste, aby używać standardowego tytułu.', 'polskipodarek'); ?>
                    </p>
                </div>
            </div>
        </fieldset>
        <?php
    }
    
    /**
     * Zapisz Quick Edit
     */
    public static function save_quick_edit_custom_title($post_id) {
        
        if (!isset($_POST['_inline_edit']) || !wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce')) {
            return;
        }
        
        if (get_post_type($post_id) !== 'product') {
            return;
        }
        
        if (!current_user_can('edit_product', $post_id)) {
            return;
        }
        
        if (isset($_POST['custom_product_title'])) {
            $custom_title = sanitize_text_field($_POST['custom_product_title']);
            update_post_meta($post_id, 'custom_product_title', $custom_title);
        }
    }
    
    /**
     * JavaScript dla Quick Edit
     */
    public static function add_quick_edit_javascript() {
        
        $screen = get_current_screen();
        if (!$screen || $screen->id !== 'edit-product') {
            return;
        }
        
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('body').on('click', '.editinline', function() {
                var post_id = $(this).closest('tr').attr('id').replace('post-', '');
                var title_data = $('#post-' + post_id + ' .pp-custom-title-data');
                var current_title = title_data.data('title');
                var acf_title = title_data.data('acf-title');
                
                // Użyj aktualnego tytułu lub ACF jako fallback
                var value = current_title || acf_title || '';
                $('#custom_product_title_quick').val(value);
            });
        });
        </script>
        <?php
    }
    
    /**
     * Rejestruje stronę migracji
     */
    public static function register_migration_page() {
        
        pp_register_admin_submenu(
            'polski-podarek-custom-title-migration',
            __('Migracja tytułów ACF', 'polskipodarek'),
            __('Migracja tytułów', 'polskipodarek'),
            array(__CLASS__, 'render_migration_page'),
            30
        );
    }
    
    /**
     * Renderuje stronę migracji
     */
    public static function render_migration_page() {
        
        // Sprawdź czy ACF istnieje
        if (!function_exists('get_field')) {
            ?>
            <div class="wrap">
                <h1><?php _e('Migracja tytułów niestandardowych', 'polskipodarek'); ?></h1>
                <div class="notice notice-error">
                    <p><?php _e('Wtyczka ACF nie jest aktywna. Migracja nie jest potrzebna.', 'polskipodarek'); ?></p>
                </div>
            </div>
            <?php
            return;
        }
        
        // Sprawdź produkty z ACF
        $acf_products = self::get_products_with_acf_titles();
        $migrated_products = self::get_products_with_migrated_titles();
        $products_to_migrate = array_filter($acf_products, function($product) use ($migrated_products) {
            return !in_array($product->ID, wp_list_pluck($migrated_products, 'ID'));
        });
        
        ?>
        <div class="wrap">
            <h1><?php _e('Migracja tytułów niestandardowych z ACF', 'polskipodarek'); ?></h1>
            
            <div class="pp-admin-section">
                <h2><?php _e('Status migracji', 'polskipodarek'); ?></h2>
                
                <div class="pp-stats-grid">
                    <div class="pp-stat-box">
                        <span class="pp-stat-number"><?php echo count($acf_products); ?></span>
                        <span class="pp-stat-label"><?php _e('Produkty z ACF', 'polskipodarek'); ?></span>
                    </div>
                    
                    <div class="pp-stat-box pp-stat-good">
                        <span class="pp-stat-number"><?php echo count($migrated_products); ?></span>
                        <span class="pp-stat-label"><?php _e('Zmigrowane', 'polskipodarek'); ?></span>
                    </div>
                    
                    <div class="pp-stat-box pp-stat-warning">
                        <span class="pp-stat-number"><?php echo count($products_to_migrate); ?></span>
                        <span class="pp-stat-label"><?php _e('Do migracji', 'polskipodarek'); ?></span>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($products_to_migrate)) : ?>
                <div class="pp-admin-section">
                    <h2><?php _e('Produkty wymagające migracji', 'polskipodarek'); ?></h2>
                    
                    <div class="pp-migration-actions">
                        <button type="button" id="migrate-all-btn" class="button button-primary button-large">
                            <?php _e('Migruj wszystkie produkty', 'polskipodarek'); ?>
                        </button>
                        
                        <button type="button" id="migrate-selected-btn" class="button button-secondary">
                            <?php _e('Migruj zaznaczone', 'polskipodarek'); ?>
                        </button>
                    </div>
                    
                    <div class="pp-migration-progress" style="display: none;">
                        <div class="progress-bar">
                            <div class="progress-fill"></div>
                        </div>
                        <div class="progress-text">0%</div>
                    </div>
                    
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <td class="manage-column column-cb check-column">
                                    <input type="checkbox" id="select-all-products">
                                </td>
                                <th><?php _e('Produkt', 'polskipodarek'); ?></th>
                                <th><?php _e('Tytuł ACF', 'polskipodarek'); ?></th>
                                <th><?php _e('Status', 'polskipodarek'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products_to_migrate as $product) : ?>
                                <?php $acf_title = get_field('custom_product_title', $product->ID); ?>
                                <tr data-product-id="<?php echo $product->ID; ?>">
                                    <th class="check-column">
                                        <input type="checkbox" name="product_ids[]" value="<?php echo $product->ID; ?>">
                                    </th>
                                    <td>
                                        <strong>
                                            <a href="<?php echo get_edit_post_link($product->ID); ?>" target="_blank">
                                                <?php echo get_the_title($product->ID); ?>
                                            </a>
                                        </strong>
                                    </td>
                                    <td>
                                        <code><?php echo esc_html($acf_title); ?></code>
                                    </td>
                                    <td class="migration-status">
                                        <span class="status-pending"><?php _e('Oczekuje', 'polskipodarek'); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <div class="pp-admin-section">
                    <div class="pp-success-message">
                        <p><?php _e('🎉 Wszystkie produkty zostały zmigrowane!', 'polskipodarek'); ?></p>
                        <?php if (!empty($migrated_products)) : ?>
                            <p>
                                <a href="#migrated-products" class="button" id="show-migrated">
                                    <?php _e('Pokaż zmigrowane produkty', 'polskipodarek'); ?>
                                </a>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($migrated_products)) : ?>
                <div class="pp-admin-section" id="migrated-products" style="display: none;">
                    <h2><?php _e('Produkty zmigrowane', 'polskipodarek'); ?></h2>
                    
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php _e('Produkt', 'polskipodarek'); ?></th>
                                <th><?php _e('Tytuł niestandardowy', 'polskipodarek'); ?></th>
                                <th><?php _e('Data migracji', 'polskipodarek'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($migrated_products, 0, 20) as $product) : ?>
                                <?php $custom_title = get_post_meta($product->ID, 'custom_product_title', true); ?>
                                <tr>
                                    <td>
                                        <strong>
                                            <a href="<?php echo get_edit_post_link($product->ID); ?>" target="_blank">
                                                <?php echo get_the_title($product->ID); ?>
                                            </a>
                                        </strong>
                                    </td>
                                    <td>
                                        <code><?php echo esc_html($custom_title); ?></code>
                                    </td>
                                    <td>
                                        <?php echo get_the_modified_date('Y-m-d H:i', $product->ID); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <?php if (count($migrated_products) > 20) : ?>
                        <p><em><?php printf(__('Pokazano 20 z %d zmigrowanych produktów.', 'polskipodarek'), count($migrated_products)); ?></em></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            
            // Select all checkbox
            $('#select-all-products').on('change', function() {
                $('input[name="product_ids[]"]').prop('checked', $(this).prop('checked'));
            });
            
            // Show migrated products
            $('#show-migrated').on('click', function(e) {
                e.preventDefault();
                $('#migrated-products').toggle();
                $(this).text(
                    $('#migrated-products').is(':visible') ? 
                    '<?php _e("Ukryj zmigrowane produkty", "polskipodarek"); ?>' : 
                    '<?php _e("Pokaż zmigrowane produkty", "polskipodarek"); ?>'
                );
            });
            
            // Migration functions
            function updateProgress(current, total) {
                var percent = Math.round((current / total) * 100);
                $('.progress-fill').css('width', percent + '%');
                $('.progress-text').text(percent + '%');
            }
            
            function migrateProducts(productIds) {
                $('.pp-migration-progress').show();
                var total = productIds.length;
                var current = 0;
                
                function migrateNext() {
                    if (current >= total) {
                        $('.pp-migration-progress').hide();
                        location.reload();
                        return;
                    }
                    
                    var productId = productIds[current];
                    var row = $('tr[data-product-id="' + productId + '"]');
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'migrate_acf_custom_titles',
                            product_id: productId,
                            nonce: '<?php echo wp_create_nonce("migrate_acf_titles"); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                row.find('.migration-status').html('<span class="status-success">✓ <?php _e("Zmigrowane", "polskipodarek"); ?></span>');
                            } else {
                                row.find('.migration-status').html('<span class="status-error">✗ <?php _e("Błąd", "polskipodarek"); ?></span>');
                            }
                            
                            current++;
                            updateProgress(current, total);
                            migrateNext();
                        },
                        error: function() {
                            row.find('.migration-status').html('<span class="status-error">✗ <?php _e("Błąd", "polskipodarek"); ?></span>');
                            current++;
                            updateProgress(current, total);
                            migrateNext();
                        }
                    });
                }
                
                migrateNext();
            }
            
            // Migrate all
            $('#migrate-all-btn').on('click', function() {
                if (!confirm('<?php _e("Czy na pewno chcesz zmigrować wszystkie produkty?", "polskipodarek"); ?>')) {
                    return;
                }
                
                var productIds = [];
                $('input[name="product_ids[]"]').each(function() {
                    productIds.push($(this).val());
                });
                
                migrateProducts(productIds);
            });
            
            // Migrate selected
            $('#migrate-selected-btn').on('click', function() {
                var selected = $('input[name="product_ids[]"]:checked');
                
                if (selected.length === 0) {
                    alert('<?php _e("Zaznacz produkty do migracji.", "polskipodarek"); ?>');
                    return;
                }
                
                if (!confirm('<?php _e("Czy na pewno chcesz zmigrować zaznaczone produkty?", "polskipodarek"); ?>')) {
                    return;
                }
                
                var productIds = [];
                selected.each(function() {
                    productIds.push($(this).val());
                });
                
                migrateProducts(productIds);
            });
        });
        </script>
        <?php
    }
    
    /**
     * AJAX handler dla migracji
     */
    public static function ajax_migrate_acf_titles() {
        
        check_ajax_referer('migrate_acf_titles', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $product_id = intval($_POST['product_id']);
        
        if (!$product_id || get_post_type($product_id) !== 'product') {
            wp_send_json_error('Invalid product ID');
        }
        
        // Pobierz tytuł z ACF
        $acf_title = function_exists('get_field') ? get_field('custom_product_title', $product_id) : '';
        
        if (empty($acf_title)) {
            wp_send_json_error('No ACF title found');
        }
        
        // Zapisz do nowego pola
        $result = update_post_meta($product_id, 'custom_product_title', sanitize_text_field($acf_title));
        
        if ($result) {
            wp_send_json_success('Migrated successfully');
        } else {
            wp_send_json_error('Failed to migrate');
        }
    }
    
    /**
     * Pobiera produkty z tytułami ACF
     */
    private static function get_products_with_acf_titles() {
        
        if (!function_exists('get_field')) {
            return array();
        }
        
        global $wpdb;
        
        $query = "
            SELECT DISTINCT p.ID, p.post_title, p.post_modified
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            WHERE p.post_type = 'product'
            AND p.post_status = 'publish'
            AND pm.meta_key = 'custom_product_title'
            AND pm.meta_value != ''
            ORDER BY p.post_modified DESC
        ";
        
        $results = $wpdb->get_results($query);
        
        // Filtruj tylko te które mają rzeczywiście ACF
        return array_filter($results, function($product) {
            $acf_value = get_field('custom_product_title', $product->ID);
            return !empty($acf_value);
        });
    }
    
    /**
     * Pobiera produkty z zmigrowanymi tytułami
     */
    private static function get_products_with_migrated_titles() {
        
        return get_posts(array(
            'post_type' => 'product',
            'meta_key' => 'custom_product_title',
            'meta_compare' => '!=',
            'meta_value' => '',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'modified',
            'order' => 'DESC'
        ));
    }
    
    /**
     * Dodaje style CSS
     */
    public static function add_admin_styles() {
        
        $screen = get_current_screen();
        if (!$screen || !in_array($screen->id, ['edit-product', 'product'])) {
            return;
        }
        
        ?>
        <style>
        .column-custom_title_status {
            width: 120px;
            text-align: center;
        }
        
        .pp-custom-title-yes {
            color: #00a32a;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        
        .pp-custom-title-acf {
            color: #ff922b;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        
        .pp-custom-title-no {
            color: #646970;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        
        .pp-title-preview {
            margin-top: 8px;
            padding: 8px;
            background: #f0f0f1;
            border-radius: 4px;
            font-size: 13px;
        }
        
        .pp-preview-text {
            font-weight: normal;
            color: #1d2327;
        }
        
        .pp-migration-actions {
            margin: 20px 0;
        }
        
        .pp-migration-actions .button {
            margin-right: 10px;
        }
        
        .pp-migration-progress {
            margin: 20px 0;
            max-width: 500px;
        }
        
        .progress-bar {
            width: 100%;
            height: 20px;
            background: #f0f0f1;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #00a32a, #4caf50);
            width: 0%;
            transition: width 0.3s ease;
        }
        
        .progress-text {
            text-align: center;
            margin-top: 8px;
            font-weight: bold;
        }
        
        .status-pending {
            color: #ff922b;
        }
        
        .status-success {
            color: #00a32a;
            font-weight: bold;
        }
        
        .status-error {
            color: #d63638;
            font-weight: bold;
        }
        </style>
        <?php
    }
    
    /**
     * Funkcja pomocnicza - pobiera tytuł (niestandardowy lub standardowy)
     */
    public static function get_product_title($product_id) {
        
        $custom_title = get_post_meta($product_id, 'custom_product_title', true);
        
        if (!empty($custom_title)) {
            return $custom_title;
        }
        
        return get_the_title($product_id);
    }
}

// Inicjalizuj klasę
PolskiPodarekCustomProductTitle::init();

/**
 * Funkcja pomocnicza globalna
 */
if (!function_exists('pp_get_product_title')) {
    /**
     * Pobiera tytuł produktu (niestandardowy lub standardowy)
     * 
     * @param int $product_id ID produktu
     * @return string Tytuł produktu
     */
    function pp_get_product_title($product_id) {
        return PolskiPodarekCustomProductTitle::get_product_title($product_id);
    }
}