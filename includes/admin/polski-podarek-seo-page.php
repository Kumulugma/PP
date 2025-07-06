<?php

/**
 * Polski Podarek - Strona zarządzania SEO produktów
 * 
 * @package PolskiPodarek
 * @version 1.0.0
 */

// Zabezpieczenie przed bezpośrednim dostępem
if (!defined('ABSPATH')) {
    exit;
}

class PolskiPodarekSeoPage {
    
    /**
     * Inicjalizacja
     */
    public static function init() {
        
        // Sprawdź czy wtyczka Indemi Core nie jest aktywna
        if (function_exists('indemi_core_init')) {
            return;
        }
        
        // Zarejestruj strony
        add_action('init', array(__CLASS__, 'register_pages'), 20);
        
        // Obsługa akcji
        add_action('admin_init', array(__CLASS__, 'handle_actions'));
    }
    
    /**
     * Rejestruje strony w menu
     */
    public static function register_pages() {
        
        // Główna strona SEO
        pp_register_admin_submenu(
            'polski-podarek-seo',
            __('SEO produktów', 'polskipodarek'),
            __('SEO produktów', 'polskipodarek'),
            array(__CLASS__, 'render_seo_page'),
            15
        );
        
        // Masowe edytowanie SEO
        pp_register_admin_submenu(
            'polski-podarek-bulk-seo',
            __('Masowe SEO', 'polskipodarek'),
            __('Masowe SEO', 'polskipodarek'),
            array(__CLASS__, 'render_bulk_seo_page'),
            16
        );
    }
    
    /**
     * Renderuje główną stronę SEO
     */
    public static function render_seo_page() {
        
        // Pobierz statystyki
        $products_with_seo = self::get_products_with_seo();
        $products_without_seo = self::get_products_without_seo();
        $all_products = self::get_all_products_count();
        
        ?>
        <div class="wrap">
            <h1><?php _e('SEO produktów', 'polskipodarek'); ?></h1>
            
            <!-- Statystyki SEO -->
            <div class="pp-admin-section">
                <h2><?php _e('Statystyki SEO', 'polskipodarek'); ?></h2>
                
                <div class="pp-stats-grid">
                    <div class="pp-stat-box pp-stat-good">
                        <span class="pp-stat-number"><?php echo count($products_with_seo); ?></span>
                        <span class="pp-stat-label"><?php _e('Produkty z opisem SEO', 'polskipodarek'); ?></span>
                    </div>
                    
                    <div class="pp-stat-box pp-stat-warning">
                        <span class="pp-stat-number"><?php echo count($products_without_seo); ?></span>
                        <span class="pp-stat-label"><?php _e('Produkty bez opisu SEO', 'polskipodarek'); ?></span>
                    </div>
                    
                    <div class="pp-stat-box">
                        <span class="pp-stat-number"><?php echo $all_products; ?></span>
                        <span class="pp-stat-label"><?php _e('Wszystkie produkty', 'polskipodarek'); ?></span>
                    </div>
                    
                    <div class="pp-stat-box">
                        <?php 
                        $percentage = $all_products > 0 ? round((count($products_with_seo) / $all_products) * 100, 1) : 0;
                        ?>
                        <span class="pp-stat-number"><?php echo $percentage; ?>%</span>
                        <span class="pp-stat-label"><?php _e('Pokrycie SEO', 'polskipodarek'); ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Szybkie akcje -->
            <div class="pp-admin-section">
                <h2><?php _e('Szybkie akcje', 'polskipodarek'); ?></h2>
                
                <div class="pp-quick-actions">
                    <a href="<?php echo admin_url('admin.php?page=polski-podarek-bulk-seo'); ?>" class="button button-primary">
                        <?php _e('Masowe edytowanie SEO', 'polskipodarek'); ?>
                    </a>
                    
                    <a href="?page=polski-podarek-seo&action=generate_seo_descriptions" 
                       class="button" 
                       onclick="return confirm('<?php _e('Czy na pewno chcesz wygenerować opisy SEO dla produktów bez opisów?', 'polskipodarek'); ?>')">
                        <?php _e('Generuj automatyczne opisy SEO', 'polskipodarek'); ?>
                    </a>
                    
                    <a href="?page=polski-podarek-seo&action=export_seo_data" class="button">
                        <?php _e('Eksportuj dane SEO', 'polskipodarek'); ?>
                    </a>
                </div>
            </div>
            
            <!-- Produkty bez opisu SEO -->
            <div class="pp-admin-section">
                <h2><?php _e('Produkty wymagające uzupełnienia SEO', 'polskipodarek'); ?></h2>
                
                <?php if (!empty($products_without_seo)) : ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th style="width: 40%;"><?php _e('Produkt', 'polskipodarek'); ?></th>
                                <th style="width: 20%;"><?php _e('Kategoria', 'polskipodarek'); ?></th>
                                <th style="width: 15%;"><?php _e('Status', 'polskipodarek'); ?></th>
                                <th style="width: 25%;"><?php _e('Akcje', 'polskipodarek'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($products_without_seo, 0, 20) as $product) : ?>
                                <tr>
                                    <td>
                                        <strong>
                                            <a href="<?php echo get_edit_post_link($product->ID); ?>" target="_blank">
                                                <?php echo get_the_title($product->ID); ?>
                                            </a>
                                        </strong>
                                        <div class="pp-product-excerpt">
                                            <?php 
                                            $excerpt = wp_trim_words(get_the_excerpt($product->ID), 15);
                                            echo !empty($excerpt) ? $excerpt : __('Brak opisu produktu', 'polskipodarek');
                                            ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php 
                                        $terms = get_the_terms($product->ID, 'product_cat');
                                        if ($terms && !is_wp_error($terms)) {
                                            echo $terms[0]->name;
                                        } else {
                                            echo __('Brak kategorii', 'polskipodarek');
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <span class="pp-status-missing">
                                            <?php _e('Brak SEO', 'polskipodarek'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo get_edit_post_link($product->ID); ?>" 
                                           class="button button-small button-primary" target="_blank">
                                            <?php _e('Dodaj SEO', 'polskipodarek'); ?>
                                        </a>
                                        
                                        <a href="?page=polski-podarek-seo&action=generate_single_seo&product_id=<?php echo $product->ID; ?>&_wpnonce=<?php echo wp_create_nonce('generate_seo_' . $product->ID); ?>" 
                                           class="button button-small">
                                            <?php _e('Auto-generuj', 'polskipodarek'); ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <?php if (count($products_without_seo) > 20) : ?>
                        <p><em><?php printf(__('Pokazano 20 z %d produktów wymagających uzupełnienia SEO.', 'polskipodarek'), count($products_without_seo)); ?></em></p>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="pp-success-message">
                        <p><?php _e('🎉 Świetnie! Wszystkie produkty mają opisy SEO!', 'polskipodarek'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Ostatnio zaktualizowane produkty SEO -->
            <div class="pp-admin-section">
                <h2><?php _e('Ostatnio zaktualizowane opisy SEO', 'polskipodarek'); ?></h2>
                
                <?php 
                $recent_seo_products = self::get_recently_updated_seo_products();
                ?>
                
                <?php if (!empty($recent_seo_products)) : ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th style="width: 30%;"><?php _e('Produkt', 'polskipodarek'); ?></th>
                                <th style="width: 50%;"><?php _e('Opis SEO', 'polskipodarek'); ?></th>
                                <th style="width: 20%;"><?php _e('Data aktualizacji', 'polskipodarek'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($recent_seo_products, 0, 10) as $product) : ?>
                                <?php $seo_text = get_post_meta($product->ID, 'seo_text', true); ?>
                                <tr>
                                    <td>
                                        <strong>
                                            <a href="<?php echo get_edit_post_link($product->ID); ?>" target="_blank">
                                                <?php echo get_the_title($product->ID); ?>
                                            </a>
                                        </strong>
                                    </td>
                                    <td>
                                        <div class="pp-seo-preview">
                                            <?php echo wp_trim_words($seo_text, 20); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php echo get_the_modified_date('Y-m-d H:i', $product->ID); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p><?php _e('Brak ostatnich aktualizacji opisów SEO.', 'polskipodarek'); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <style>
        .pp-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .pp-stat-box {
            background: #f0f0f1;
            padding: 20px;
            border-radius: 4px;
            text-align: center;
        }
        
        .pp-stat-box.pp-stat-good {
            background: #d4edda;
            border-left: 4px solid #00a32a;
        }
        
        .pp-stat-box.pp-stat-warning {
            background: #fff3cd;
            border-left: 4px solid #ff922b;
        }
        
        .pp-stat-number {
            display: block;
            font-size: 32px;
            font-weight: bold;
            color: #1d2327;
            margin-bottom: 5px;
        }
        
        .pp-stat-label {
            font-size: 12px;
            color: #646970;
            text-transform: uppercase;
        }
        
        .pp-quick-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .pp-product-excerpt {
            font-size: 12px;
            color: #646970;
            margin-top: 5px;
            font-style: italic;
        }
        
        .pp-status-missing {
            background: #d63638;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .pp-seo-preview {
            font-size: 13px;
            line-height: 1.4;
            color: #1d2327;
        }
        
        .pp-success-message {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 20px;
            border-radius: 4px;
            text-align: center;
        }
        
        .pp-success-message p {
            margin: 0;
            font-size: 16px;
        }
        </style>
        <?php
    }
    
    /**
     * Renderuje stronę masowego edytowania SEO
     */
    public static function render_bulk_seo_page() {
        
        // Obsługa zapisywania
        if (isset($_POST['pp_bulk_seo_save'])) {
            self::handle_bulk_seo_save();
        }
        
        // Pobierz produkty do edycji
        $page = isset($_GET['seo_page']) ? max(1, intval($_GET['seo_page'])) : 1;
        $per_page = 15;
        $offset = ($page - 1) * $per_page;
        
        $products = self::get_products_without_seo($per_page, $offset);
        $total_products = count(self::get_products_without_seo());
        $total_pages = ceil($total_products / $per_page);
        
        ?>
        <div class="wrap">
            <h1><?php _e('Masowe edytowanie SEO', 'polskipodarek'); ?></h1>
            
            <div class="pp-admin-section">
                <div class="pp-bulk-seo-header">
                    <h2><?php _e('Produkty do uzupełnienia opisów SEO', 'polskipodarek'); ?></h2>
                    <p><?php printf(__('Strona %d z %d (łącznie: %d produktów)', 'polskipodarek'), $page, $total_pages, $total_products); ?></p>
                </div>
                
                <?php if (!empty($products)) : ?>
                    <form method="post" action="">
                        <?php wp_nonce_field('pp_bulk_seo_save', 'pp_bulk_seo_nonce'); ?>
                        
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th style="width: 30%;"><?php _e('Produkt', 'polskipodarek'); ?></th>
                                    <th style="width: 70%;"><?php _e('Opis SEO', 'polskipodarek'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product) : ?>
                                    <tr>
                                        <td>
                                            <strong>
                                                <a href="<?php echo get_edit_post_link($product->ID); ?>" target="_blank">
                                                    <?php echo get_the_title($product->ID); ?>
                                                </a>
                                            </strong>
                                            
                                            <div class="pp-product-info">
                                                <?php 
                                                $terms = get_the_terms($product->ID, 'product_cat');
                                                if ($terms && !is_wp_error($terms)) {
                                                    echo '<span class="pp-category">' . $terms[0]->name . '</span>';
                                                }
                                                ?>
                                            </div>
                                            
                                            <div class="pp-product-excerpt">
                                                <?php 
                                                $excerpt = wp_trim_words(get_the_excerpt($product->ID), 10);
                                                echo !empty($excerpt) ? $excerpt : __('Brak opisu', 'polskipodarek');
                                                ?>
                                            </div>
                                            
                                            <div class="pp-bulk-actions">
                                                <button type="button" class="button button-small pp-auto-generate" 
                                                        data-product-id="<?php echo $product->ID; ?>">
                                                    <?php _e('Auto-generuj', 'polskipodarek'); ?>
                                                </button>
                                            </div>
                                        </td>
                                        <td>
                                            <textarea name="seo_text[<?php echo $product->ID; ?>]" 
                                                      id="seo_text_<?php echo $product->ID; ?>"
                                                      rows="4" 
                                                      style="width: 100%;"
                                                      placeholder="<?php _e('Wprowadź opis SEO (150-160 znaków)...', 'polskipodarek'); ?>"><?php echo esc_textarea(get_post_meta($product->ID, 'seo_text', true)); ?></textarea>
                                            
                                            <div class="pp-seo-tools">
                                                <span class="pp-char-counter" data-target="seo_text_<?php echo $product->ID; ?>">0 znaków</span>
                                                <button type="button" class="button button-small pp-clear-text" 
                                                        data-target="seo_text_<?php echo $product->ID; ?>">
                                                    <?php _e('Wyczyść', 'polskipodarek'); ?>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <div class="pp-bulk-seo-footer">
                            <p class="submit">
                                <input type="submit" name="pp_bulk_seo_save" class="button button-primary button-large" 
                                       value="<?php _e('Zapisz wszystkie opisy SEO', 'polskipodarek'); ?>">
                                
                                <button type="button" class="button button-secondary pp-generate-all">
                                    <?php _e('Auto-generuj wszystkie', 'polskipodarek'); ?>
                                </button>
                            </p>
                            
                            <!-- Paginacja -->
                            <?php if ($total_pages > 1) : ?>
                                <div class="pp-pagination">
                                    <?php
                                    $base_url = admin_url('admin.php?page=polski-podarek-bulk-seo');
                                    
                                    if ($page > 1) {
                                        echo '<a href="' . $base_url . '&seo_page=' . ($page - 1) . '" class="button">« ' . __('Poprzednia', 'polskipodarek') . '</a> ';
                                    }
                                    
                                    for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++) {
                                        $class = ($i == $page) ? 'button button-primary' : 'button';
                                        echo '<a href="' . $base_url . '&seo_page=' . $i . '" class="' . $class . '">' . $i . '</a> ';
                                    }
                                    
                                    if ($page < $total_pages) {
                                        echo '<a href="' . $base_url . '&seo_page=' . ($page + 1) . '" class="button">' . __('Następna', 'polskipodarek') . ' »</a>';
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>
                <?php else : ?>
                    <div class="pp-success-message">
                        <p><?php _e('🎉 Wszystkie produkty mają opisy SEO!', 'polskipodarek'); ?></p>
                        <p><a href="<?php echo admin_url('admin.php?page=polski-podarek-seo'); ?>" class="button button-primary">
                            <?php _e('Powrót do przeglądu SEO', 'polskipodarek'); ?>
                        </a></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <style>
        .pp-bulk-seo-header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .pp-product-info {
            margin: 8px 0;
        }
        
        .pp-category {
            background: #f0f0f1;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            color: #646970;
        }
        
        .pp-product-excerpt {
            font-size: 12px;
            color: #646970;
            margin: 8px 0;
            line-height: 1.3;
        }
        
        .pp-bulk-actions {
            margin-top: 8px;
        }
        
        .pp-seo-tools {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 5px;
            font-size: 12px;
            color: #646970;
        }
        
        .pp-char-counter {
            font-weight: bold;
        }
        
        .pp-char-counter.pp-optimal {
            color: #00a32a;
        }
        
        .pp-char-counter.pp-warning {
            color: #ff922b;
        }
        
        .pp-char-counter.pp-error {
            color: #d63638;
        }
        
        .pp-bulk-seo-footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        
        .pp-pagination {
            margin-top: 15px;
        }
        
        .pp-pagination .button {
            margin-right: 5px;
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            
            // Licznik znaków
            function updateCharCounter(textarea) {
                var length = textarea.val().length;
                var counter = $('.pp-char-counter[data-target="' + textarea.attr('id') + '"]');
                
                counter.text(length + ' znaków');
                counter.removeClass('pp-optimal pp-warning pp-error');
                
                if (length >= 150 && length <= 160) {
                    counter.addClass('pp-optimal');
                } else if (length > 160) {
                    counter.addClass('pp-error');
                } else if (length > 140) {
                    counter.addClass('pp-warning');
                }
            }
            
            // Aktualizuj liczniki przy wpisywaniu
            $('textarea[name^="seo_text"]').on('input', function() {
                updateCharCounter($(this));
            });
            
            // Inicjalizuj liczniki
            $('textarea[name^="seo_text"]').each(function() {
                updateCharCounter($(this));
            });
            
            // Przycisk wyczyść
            $('.pp-clear-text').on('click', function() {
                var target = $(this).data('target');
                $('#' + target).val('');
                updateCharCounter($('#' + target));
            });
            
            // Auto-generowanie pojedynczego opisu
            $('.pp-auto-generate').on('click', function() {
                var productId = $(this).data('product-id');
                var textarea = $('#seo_text_' + productId);
                var button = $(this);
                
            // Auto-generowanie pojedynczego opisu
            $('.pp-auto-generate').on('click', function() {
                var productId = $(this).data('product-id');
                var textarea = $('#seo_text_' + productId);
                var button = $(this);
                
                button.prop('disabled', true).text('<?php _e('Generuję...', 'polskipodarek'); ?>');
                
                // Podstawowe auto-generowanie na podstawie tytułu i kategorii
                var productTitle = button.closest('tr').find('td:first strong a').text();
                var category = button.closest('tr').find('.pp-category').text();
                
                var generatedText = '';
                if (category) {
                    generatedText = productTitle + ' - ' + category + '. ' + '<?php _e('Sprawdź naszą ofertę i zamów już dziś!', 'polskipodarek'); ?>';
                } else {
                    generatedText = productTitle + '. ' + '<?php _e('Wysokiej jakości produkt w atrakcyjnej cenie. Zamów już dziś!', 'polskipodarek'); ?>';
                }
                
                textarea.val(generatedText);
                updateCharCounter(textarea);
                
                button.prop('disabled', false).text('<?php _e('Auto-generuj', 'polskipodarek'); ?>');
            });
            
            // Auto-generowanie wszystkich opisów
            $('.pp-generate-all').on('click', function() {
                if (!confirm('<?php _e('Czy na pewno chcesz wygenerować opisy SEO dla wszystkich produktów? Istniejące opisy zostaną zastąpione.', 'polskipodarek'); ?>')) {
                    return;
                }
                
                $('.pp-auto-generate').each(function() {
                    $(this).click();
                });
            });
        });
        </script>
        <?php
    }
    
    /**
     * Obsługuje akcje na stronie
     */
    public static function handle_actions() {
        
        if (!isset($_GET['page']) || !in_array($_GET['page'], ['polski-podarek-seo', 'polski-podarek-bulk-seo'])) {
            return;
        }
        
        // Generowanie opisów SEO
        if (isset($_GET['action']) && $_GET['action'] === 'generate_seo_descriptions') {
            self::generate_seo_descriptions();
        }
        
        // Generowanie pojedynczego opisu SEO
        if (isset($_GET['action']) && $_GET['action'] === 'generate_single_seo' && isset($_GET['product_id'])) {
            $product_id = intval($_GET['product_id']);
            if (wp_verify_nonce($_GET['_wpnonce'], 'generate_seo_' . $product_id)) {
                self::generate_single_seo_description($product_id);
            }
        }
        
        // Eksport danych SEO
        if (isset($_GET['action']) && $_GET['action'] === 'export_seo_data') {
            self::export_seo_data();
        }
    }
    
    /**
     * Obsługuje masowe zapisywanie SEO
     */
    private static function handle_bulk_seo_save() {
        
        if (!wp_verify_nonce($_POST['pp_bulk_seo_nonce'], 'pp_bulk_seo_save')) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error is-dismissible"><p>' . __('Błąd zabezpieczeń.', 'polskipodarek') . '</p></div>';
            });
            return;
        }
        
        $updated_count = 0;
        
        if (isset($_POST['seo_text']) && is_array($_POST['seo_text'])) {
            foreach ($_POST['seo_text'] as $product_id => $seo_text) {
                $product_id = intval($product_id);
                $seo_text = sanitize_textarea_field($seo_text);
                
                if (get_post_type($product_id) === 'product' && !empty($seo_text)) {
                    update_post_meta($product_id, 'seo_text', $seo_text);
                    $updated_count++;
                }
            }
        }
        
        add_action('admin_notices', function() use ($updated_count) {
            echo '<div class="notice notice-success is-dismissible"><p>' . 
                 sprintf(__('Zaktualizowano opisy SEO dla %d produktów.', 'polskipodarek'), $updated_count) . 
                 '</p></div>';
        });
    }
    
    /**
     * Pobiera produkty z opisem SEO
     */
    private static function get_products_with_seo() {
        
        return get_posts(array(
            'post_type' => 'product',
            'meta_key' => 'seo_text',
            'meta_compare' => '!=',
            'meta_value' => '',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
    }
    
    /**
     * Pobiera produkty bez opisu SEO
     */
    private static function get_products_without_seo($per_page = -1, $offset = 0) {
        
        $args = array(
            'post_type' => 'product',
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => 'seo_text',
                    'compare' => 'NOT EXISTS'
                ),
                array(
                    'key' => 'seo_text',
                    'value' => '',
                    'compare' => '='
                )
            ),
            'post_status' => 'publish'
        );
        
        if ($per_page !== -1) {
            $args['posts_per_page'] = $per_page;
            $args['offset'] = $offset;
        } else {
            $args['posts_per_page'] = -1;
        }
        
        return get_posts($args);
    }
    
    /**
     * Pobiera liczbę wszystkich produktów
     */
    private static function get_all_products_count() {
        
        $count = wp_count_posts('product');
        return $count->publish;
    }
    
    /**
     * Pobiera ostatnio zaktualizowane produkty SEO
     */
    private static function get_recently_updated_seo_products() {
        
        return get_posts(array(
            'post_type' => 'product',
            'meta_key' => 'seo_text',
            'meta_compare' => '!=',
            'meta_value' => '',
            'posts_per_page' => 10,
            'orderby' => 'modified',
            'order' => 'DESC',
            'post_status' => 'publish'
        ));
    }
    
    /**
     * Generuje opisy SEO dla produktów bez opisów
     */
    private static function generate_seo_descriptions() {
        
        $products = self::get_products_without_seo();
        $generated_count = 0;
        
        foreach ($products as $product) {
            $seo_text = self::generate_auto_seo_text($product);
            if (!empty($seo_text)) {
                update_post_meta($product->ID, 'seo_text', $seo_text);
                $generated_count++;
            }
        }
        
        add_action('admin_notices', function() use ($generated_count) {
            echo '<div class="notice notice-success is-dismissible"><p>' . 
                 sprintf(__('Wygenerowano opisy SEO dla %d produktów.', 'polskipodarek'), $generated_count) . 
                 '</p></div>';
        });
    }
    
    /**
     * Generuje pojedynczy opis SEO
     */
    private static function generate_single_seo_description($product_id) {
        
        $product = get_post($product_id);
        if (!$product || $product->post_type !== 'product') {
            return;
        }
        
        $seo_text = self::generate_auto_seo_text($product);
        if (!empty($seo_text)) {
            update_post_meta($product_id, 'seo_text', $seo_text);
            
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible"><p>' . 
                     __('Wygenerowano opis SEO dla produktu.', 'polskipodarek') . 
                     '</p></div>';
            });
        }
    }
    
    /**
     * Generuje automatyczny tekst SEO na podstawie produktu
     */
    private static function generate_auto_seo_text($product) {
        
        $title = get_the_title($product->ID);
        $excerpt = get_the_excerpt($product->ID);
        $categories = get_the_terms($product->ID, 'product_cat');
        
        // Podstawa opisu
        $seo_text = $title;
        
        // Dodaj kategorię jeśli istnieje
        if ($categories && !is_wp_error($categories)) {
            $category_name = $categories[0]->name;
            $seo_text .= ' - ' . $category_name;
        }
        
        // Dodaj fragment opisu jeśli istnieje i nie jest za długi
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
    
    /**
     * Eksportuje dane SEO do CSV
     */
    private static function export_seo_data() {
        
        $products_with_seo = self::get_products_with_seo();
        $products_without_seo = self::get_products_without_seo();
        
        $all_products = array_merge($products_with_seo, $products_without_seo);
        
        if (empty($all_products)) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error is-dismissible"><p>' . 
                     __('Brak produktów do eksportu.', 'polskipodarek') . 
                     '</p></div>';
            });
            return;
        }
        
        $filename = 'seo-produkty-' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen(self::generate_seo_csv_content($all_products)));
        
        echo self::generate_seo_csv_content($all_products);
        exit;
    }
    
    /**
     * Generuje zawartość CSV dla danych SEO
     */
    private static function generate_seo_csv_content($products) {
        
        $output = fopen('php://temp', 'w');
        
        // Nagłówki
        fputcsv($output, array(
            'ID',
            'Nazwa produktu',
            'Opis SEO',
            'Długość opisu SEO',
            'Status SEO',
            'Kategorie',
            'URL produktu'
        ));
        
        // Dane produktów
        foreach ($products as $product) {
            $seo_text = get_post_meta($product->ID, 'seo_text', true);
            $seo_length = strlen($seo_text);
            
            // Status SEO
            if (empty($seo_text)) {
                $seo_status = 'Brak';
            } elseif ($seo_length >= 150 && $seo_length <= 160) {
                $seo_status = 'Optymalny';
            } elseif ($seo_length > 160) {
                $seo_status = 'Za długi';
            } else {
                $seo_status = 'Za krótki';
            }
            
            $categories = get_the_terms($product->ID, 'product_cat');
            $category_names = array();
            
            if ($categories && !is_wp_error($categories)) {
                $category_names = wp_list_pluck($categories, 'name');
            }
            
            fputcsv($output, array(
                $product->ID,
                get_the_title($product->ID),
                $seo_text,
                $seo_length,
                $seo_status,
                implode(', ', $category_names),
                get_permalink($product->ID)
            ));
        }
        
        rewind($output);
        $csv_content = stream_get_contents($output);
        fclose($output);
        
        return $csv_content;
    }
}

// Inicjalizuj strony SEO
PolskiPodarekSeoPage::init();