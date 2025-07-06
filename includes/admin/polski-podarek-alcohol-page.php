<?php

/**
 * Polski Podarek - Strona zarządzania weryfikacją wieku
 * 
 * @package PolskiPodarek
 * @version 1.0.0
 */

// Zabezpieczenie przed bezpośrednim dostępem
if (!defined('ABSPATH')) {
    exit;
}

class PolskiPodarekAlcoholPage {
    
    /**
     * Inicjalizacja
     */
    public static function init() {
        
        // Sprawdź czy wtyczka Indemi Core nie jest aktywna
        if (function_exists('indemi_core_init')) {
            return;
        }
        
        // Zarejestruj stronę
        add_action('init', array(__CLASS__, 'register_page'), 20);
        
        // Obsługa akcji
        add_action('admin_init', array(__CLASS__, 'handle_actions'));
    }
    
    /**
     * Rejestruje stronę w menu
     */
    public static function register_page() {
        
        pp_register_admin_submenu(
            'polski-podarek-alcohol',
            __('Weryfikacja wieku', 'polskipodarek'),
            __('Weryfikacja wieku', 'polskipodarek'),
            array(__CLASS__, 'render_page'),
            10
        );
    }
    
    /**
     * Renderuje stronę weryfikacji wieku
     */
    public static function render_page() {
        
        // Pobierz statystyki
        $alcohol_products = self::get_alcohol_products();
        
        ?>
        <div class="wrap">
            <h1><?php _e('Weryfikacja wieku - Produkty alkoholowe', 'polskipodarek'); ?></h1>
            
            <!-- Statystyki -->
            <div class="pp-admin-section">
                <h2><?php _e('Statystyki', 'polskipodarek'); ?></h2>
                
                <div class="pp-stats-grid">
                    <div class="pp-stat-box">
                        <span class="pp-stat-number"><?php echo count($alcohol_products); ?></span>
                        <span class="pp-stat-label"><?php _e('Produkty alkoholowe', 'polskipodarek'); ?></span>
                    </div>
                    
                    <div class="pp-stat-box">
                        <?php
                        $all_products = wp_count_posts('product');
                        $percentage = $all_products->publish > 0 ? round((count($alcohol_products) / $all_products->publish) * 100, 1) : 0;
                        ?>
                        <span class="pp-stat-number"><?php echo $percentage; ?>%</span>
                        <span class="pp-stat-label"><?php _e('Procent produktów alkoholowych', 'polskipodarek'); ?></span>
                    </div>
                    
                    <div class="pp-stat-box">
                        <?php
                        $categories_with_alcohol = array();
                        foreach ($alcohol_products as $product) {
                            $terms = get_the_terms($product->ID, 'product_cat');
                            if ($terms && !is_wp_error($terms)) {
                                foreach ($terms as $term) {
                                    $categories_with_alcohol[$term->term_id] = $term->name;
                                }
                            }
                        }
                        ?>
                        <span class="pp-stat-number"><?php echo count($categories_with_alcohol); ?></span>
                        <span class="pp-stat-label"><?php _e('Kategorie z alkoholem', 'polskipodarek'); ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Ostatnie produkty alkoholowe -->
            <div class="pp-admin-section">
                <h2><?php _e('Produkty alkoholowe', 'polskipodarek'); ?></h2>
                
                <div class="pp-section-actions">
                    <a href="<?php echo admin_url('admin.php?page=polski-podarek-alcohol&action=mark_all_alcohol'); ?>" 
                       class="button" 
                       onclick="return confirm('<?php _e('Czy na pewno chcesz oznaczyć wszystkie produkty jako alkoholowe?', 'polskipodarek'); ?>')">
                        <?php _e('Oznacz wszystkie jako alkoholowe', 'polskipodarek'); ?>
                    </a>
                    
                    <a href="<?php echo admin_url('admin.php?page=polski-podarek-alcohol&action=unmark_all_alcohol'); ?>" 
                       class="button" 
                       onclick="return confirm('<?php _e('Czy na pewno chcesz usunąć oznaczenie alkoholowe ze wszystkich produktów?', 'polskipodarek'); ?>')">
                        <?php _e('Usuń oznaczenie ze wszystkich', 'polskipodarek'); ?>
                    </a>
                </div>
                
                <?php if (!empty($alcohol_products)) : ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th style="width: 50%;"><?php _e('Produkt', 'polskipodarek'); ?></th>
                                <th style="width: 20%;"><?php _e('Kategoria', 'polskipodarek'); ?></th>
                                <th style="width: 15%;"><?php _e('Status', 'polskipodarek'); ?></th>
                                <th style="width: 15%;"><?php _e('Akcje', 'polskipodarek'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($alcohol_products, 0, 20) as $product) : ?>
                                <tr>
                                    <td>
                                        <strong>
                                            <a href="<?php echo get_edit_post_link($product->ID); ?>" target="_blank">
                                                <?php echo get_the_title($product->ID); ?>
                                            </a>
                                        </strong>
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
                                        <span class="pp-status-alcohol">
                                            <?php _e('Alkoholowy', 'polskipodarek'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo get_edit_post_link($product->ID); ?>" 
                                           class="button button-small" target="_blank">
                                            <?php _e('Edytuj', 'polskipodarek'); ?>
                                        </a>
                                        
                                        <a href="?page=polski-podarek-alcohol&action=unmark_product&product_id=<?php echo $product->ID; ?>&_wpnonce=<?php echo wp_create_nonce('unmark_product_' . $product->ID); ?>" 
                                           class="button button-small" 
                                           onclick="return confirm('<?php _e('Usuń oznaczenie alkoholowe?', 'polskipodarek'); ?>')">
                                            <?php _e('Usuń oznaczenie', 'polskipodarek'); ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <?php if (count($alcohol_products) > 20) : ?>
                        <p><em><?php printf(__('Pokazano 20 z %d produktów alkoholowych.', 'polskipodarek'), count($alcohol_products)); ?></em></p>
                    <?php endif; ?>
                <?php else : ?>
                    <p><?php _e('Brak produktów alkoholowych.', 'polskipodarek'); ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Narzędzia -->
            <div class="pp-admin-section">
                <h2><?php _e('Narzędzia', 'polskipodarek'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Masowe oznaczanie', 'polskipodarek'); ?></th>
                        <td>
                            <p><?php _e('Oznacz produkty jako alkoholowe na podstawie kategorii:', 'polskipodarek'); ?></p>
                            
                            <form method="post" action="" style="display: inline-block; margin-right: 10px;">
                                <?php wp_nonce_field('pp_mark_by_category', 'pp_mark_category_nonce'); ?>
                                <input type="text" name="category_name" placeholder="<?php _e('Nazwa kategorii', 'polskipodarek'); ?>" style="width: 200px;">
                                <input type="submit" name="mark_by_category" class="button" value="<?php _e('Oznacz kategorię', 'polskipodarek'); ?>">
                            </form>
                            
                            <p class="description">
                                <?php _e('Wprowadź dokładną nazwę kategorii, aby oznaczyć wszystkie jej produkty jako alkoholowe.', 'polskipodarek'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Import/Export', 'polskipodarek'); ?></th>
                        <td>
                            <a href="?page=polski-podarek-alcohol&action=export_alcohol_products" class="button">
                                <?php _e('Eksportuj listę produktów alkoholowych', 'polskipodarek'); ?>
                            </a>
                            <p class="description">
                                <?php _e('Pobierz plik CSV z listą wszystkich produktów alkoholowych.', 'polskipodarek'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
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
        
        .pp-section-actions {
            margin: 15px 0;
        }
        
        .pp-section-actions .button {
            margin-right: 10px;
        }
        
        .pp-status-verified {
            background: #00a32a;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .pp-status-not-verified {
            background: #d63638;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        </style>
        <?php
    }
    
    /**
     * Obsługuje akcje na stronie
     */
    public static function handle_actions() {
        
        if (!isset($_GET['page']) || $_GET['page'] !== 'polski-podarek-alcohol') {
            return;
        }
        
        // Oznacz wszystkie jako alkoholowe
        if (isset($_GET['action']) && $_GET['action'] === 'mark_all_alcohol') {
            self::mark_all_products_as_alcohol();
        }
        
        // Usuń oznaczenie ze wszystkich
        if (isset($_GET['action']) && $_GET['action'] === 'unmark_all_alcohol') {
            self::unmark_all_products_alcohol();
        }
        
        // Usuń oznaczenie z pojedynczego produktu
        if (isset($_GET['action']) && $_GET['action'] === 'unmark_product' && isset($_GET['product_id'])) {
            $product_id = intval($_GET['product_id']);
            if (wp_verify_nonce($_GET['_wpnonce'], 'unmark_product_' . $product_id)) {
                self::unmark_product_alcohol($product_id);
            }
        }
        
        // Eksport produktów alkoholowych
        if (isset($_GET['action']) && $_GET['action'] === 'export_alcohol_products') {
            self::export_alcohol_products();
        }
        
        // Oznacz produkty według kategorii
        if (isset($_POST['mark_by_category']) && wp_verify_nonce($_POST['pp_mark_category_nonce'], 'pp_mark_by_category')) {
            $category_name = sanitize_text_field($_POST['category_name']);
            if (!empty($category_name)) {
                self::mark_products_by_category($category_name);
            }
        }
    }
    
    /**
     * Pobiera produkty alkoholowe
     */
    private static function get_alcohol_products() {
        
        return get_posts(array(
            'post_type' => 'product',
            'meta_key' => 'product_alcohol',
            'meta_value' => 'tak',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
    }
    
    /**
     * Oznacza wszystkie produkty jako alkoholowe
     */
    private static function mark_all_products_as_alcohol() {
        
        $products = get_posts(array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));
        
        $count = 0;
        foreach ($products as $product) {
            update_post_meta($product->ID, 'product_alcohol', 'tak');
            $count++;
        }
        
        add_action('admin_notices', function() use ($count) {
            echo '<div class="notice notice-success is-dismissible"><p>' . 
                 sprintf(__('Oznaczono %d produktów jako alkoholowe.', 'polskipodarek'), $count) . 
                 '</p></div>';
        });
    }
    
    /**
     * Usuwa oznaczenie alkoholowe ze wszystkich produktów
     */
    private static function unmark_all_products_alcohol() {
        
        $products = get_posts(array(
            'post_type' => 'product',
            'meta_key' => 'product_alcohol',
            'meta_value' => 'tak',
            'posts_per_page' => -1
        ));
        
        $count = 0;
        foreach ($products as $product) {
            update_post_meta($product->ID, 'product_alcohol', 'nie');
            $count++;
        }
        
        add_action('admin_notices', function() use ($count) {
            echo '<div class="notice notice-success is-dismissible"><p>' . 
                 sprintf(__('Usunięto oznaczenie alkoholowe z %d produktów.', 'polskipodarek'), $count) . 
                 '</p></div>';
        });
    }
     
    /**
     * Usuwa oznaczenie alkoholowe z pojedynczego produktu
     */
    private static function unmark_product_alcohol($product_id) {
        
        if (get_post_type($product_id) === 'product') {
            update_post_meta($product_id, 'product_alcohol', 'nie');
            
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible"><p>' . 
                     __('Usunięto oznaczenie alkoholowe z produktu.', 'polskipodarek') . 
                     '</p></div>';
            });
        }
    }
    
    /**
     * Oznacza produkty według kategorii
     */
    private static function mark_products_by_category($category_name) {
        
        $category = get_term_by('name', $category_name, 'product_cat');
        
        if (!$category) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error is-dismissible"><p>' . 
                     __('Nie znaleziono kategorii o podanej nazwie.', 'polskipodarek') . 
                     '</p></div>';
            });
            return;
        }
        
        $products = get_posts(array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $category->term_id
                )
            )
        ));
        
        $count = 0;
        foreach ($products as $product) {
            update_post_meta($product->ID, 'product_alcohol', 'tak');
            $count++;
        }
        
        add_action('admin_notices', function() use ($count, $category_name) {
            echo '<div class="notice notice-success is-dismissible"><p>' . 
                 sprintf(__('Oznaczono %d produktów z kategorii "%s" jako alkoholowe.', 'polskipodarek'), $count, $category_name) . 
                 '</p></div>';
        });
    }
    
    /**
     * Eksportuje listę produktów alkoholowych do CSV
     */
    private static function export_alcohol_products() {
        
        $products = self::get_alcohol_products();
        
        if (empty($products)) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error is-dismissible"><p>' . 
                     __('Brak produktów alkoholowych do eksportu.', 'polskipodarek') . 
                     '</p></div>';
            });
            return;
        }
        
        $filename = 'produkty-alkoholowe-' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen(self::generate_csv_content($products)));
        
        echo self::generate_csv_content($products);
        exit;
    }
    
    /**
     * Generuje zawartość CSV
     */
    private static function generate_csv_content($products) {
        
        $output = fopen('php://temp', 'w');
        
        // Nagłówki
        fputcsv($output, array(
            'ID',
            'Nazwa produktu',
            'Slug',
            'Kategorie',
            'Status',
            'Data utworzenia',
            'Data modyfikacji'
        ));
        
        // Dane produktów
        foreach ($products as $product) {
            $categories = get_the_terms($product->ID, 'product_cat');
            $category_names = array();
            
            if ($categories && !is_wp_error($categories)) {
                $category_names = wp_list_pluck($categories, 'name');
            }
            
            fputcsv($output, array(
                $product->ID,
                get_the_title($product->ID),
                $product->post_name,
                implode(', ', $category_names),
                $product->post_status,
                $product->post_date,
                $product->post_modified
            ));
        }
        
        rewind($output);
        $csv_content = stream_get_contents($output);
        fclose($output);
        
        return $csv_content;
    }
}

// Inicjalizuj stronę weryfikacji wieku
PolskiPodarekAlcoholPage::init();