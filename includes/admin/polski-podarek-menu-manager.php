<?php

/**
 * Polski Podarek - Centralny Menedżer Menu Admina
 * Zarządza wszystkimi pozycjami menu i podmenu w panelu administracyjnym
 * 
 * @package PolskiPodarek
 * @version 1.0.0
 */

// Zabezpieczenie przed bezpośrednim dostępem
if (!defined('ABSPATH')) {
    exit;
}

class PolskiPodarekMenuManager {
    
    // Podstawowe opcje
    const MAIN_PAGE_SLUG = 'polski-podarek';
    const CAPABILITY = 'manage_options';
    
    // Registrowane strony podmenu
    private static $submenus = array();
    
    /**
     * Inicjalizacja menedżera
     */
    public static function init() {
        
        // Sprawdź czy wtyczka Indemi Core nie jest aktywna
        if (function_exists('indemi_core_init')) {
            return;
        }
        
        // Dodaj główne menu
        add_action('admin_menu', array(__CLASS__, 'add_main_menu'));
        
        // Dodaj podmenu (z opóźnieniem, żeby inne klasy mogły się zarejestrować)
        add_action('admin_menu', array(__CLASS__, 'add_submenus'), 15);
        
        // Dodaj style CSS
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_assets'));
    }
    
    /**
     * Dodaje główne menu Polski Podarek
     */
    public static function add_main_menu() {
        
        add_menu_page(
            __('Polski Podarek', 'polskipodarek'),
            __('Polski Podarek', 'polskipodarek'),
            self::CAPABILITY,
            self::MAIN_PAGE_SLUG,
            array(__CLASS__, 'render_main_page'),
            'dashicons-store',
            30
        );
    }
    
    /**
     * Rejestruje nową stronę podmenu
     * 
     * @param string $slug Slug strony
     * @param string $title Tytuł strony
     * @param string $menu_title Tytuł w menu
     * @param callable $callback Funkcja renderująca stronę
     * @param int $position Pozycja w menu (opcjonalne)
     */
    public static function register_submenu($slug, $title, $menu_title, $callback, $position = 10) {
        
        self::$submenus[] = array(
            'slug' => $slug,
            'title' => $title,
            'menu_title' => $menu_title,
            'callback' => $callback,
            'position' => $position
        );
        
        // Posortuj według pozycji
        usort(self::$submenus, function($a, $b) {
            return $a['position'] - $b['position'];
        });
    }
    
    /**
     * Dodaje wszystkie zarejestrowane podmenu
     */
    public static function add_submenus() {
        
        foreach (self::$submenus as $submenu) {
            add_submenu_page(
                self::MAIN_PAGE_SLUG,
                $submenu['title'],
                $submenu['menu_title'],
                self::CAPABILITY,
                $submenu['slug'],
                $submenu['callback']
            );
        }
    }
    
    /**
     * Renderuje główną stronę z przekierowaniem do ustawień
     */
    public static function render_main_page() {
        
        ?>
        <div class="wrap">
            <h1><?php _e('Polski Podarek - Panel Główny', 'polskipodarek'); ?></h1>
            
            <div class="pp-dashboard">
                <div class="pp-dashboard-welcome">
                    <h2><?php _e('Witaj w panelu Polski Podarek', 'polskipodarek'); ?></h2>
                    <p><?php _e('Zarządzaj wszystkimi funkcjonalnościami motywu z tego miejsca.', 'polskipodarek'); ?></p>
                </div>
                
                <div class="pp-dashboard-grid">
                    
                    <!-- Szybkie akcje -->
                    <div class="pp-dashboard-card">
                        <h3><?php _e('Szybkie akcje', 'polskipodarek'); ?></h3>
                        <ul class="pp-quick-actions">
                            <li>
                                <a href="<?php echo admin_url('admin.php?page=polski-podarek-settings'); ?>" class="button button-primary">
                                    <?php _e('Ustawienia główne', 'polskipodarek'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo admin_url('admin.php?page=polski-podarek-alcohol'); ?>" class="button">
                                    <?php _e('Weryfikacja wieku', 'polskipodarek'); ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo admin_url('admin.php?page=polski-podarek-seo'); ?>" class="button">
                                    <?php _e('SEO produktów', 'polskipodarek'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Status systemu -->
                    <div class="pp-dashboard-card">
                        <h3><?php _e('Status systemu', 'polskipodarek'); ?></h3>
                        <div class="pp-status-list">
                            <?php
                            $status_items = array(
                                'WooCommerce' => class_exists('WooCommerce'),
                                'Weryfikacja wieku' => function_exists('polskipodarek_alcohol_age_verification_init'),
                                'SEO produktów' => function_exists('polskipodarek_seo_product_metabox_init'),
                                'ACF' => function_exists('get_field')
                            );
                            
                            foreach ($status_items as $name => $status) {
                                $status_class = $status ? 'pp-status-ok' : 'pp-status-error';
                                $status_text = $status ? __('OK', 'polskipodarek') : __('Błąd', 'polskipodarek');
                                echo "<div class='pp-status-item $status_class'>";
                                echo "<span class='pp-status-dot'></span>";
                                echo "<span>$name: $status_text</span>";
                                echo "</div>";
                            }
                            ?>
                        </div>
                    </div>
                    
                    <!-- Statystyki -->
                    <div class="pp-dashboard-card">
                        <h3><?php _e('Statystyki', 'polskipodarek'); ?></h3>
                        <div class="pp-stats">
                            <?php
                            // Produkty alkoholowe
                            $alcohol_products = get_posts(array(
                                'post_type' => 'product',
                                'meta_key' => 'product_alcohol',
                                'meta_value' => 'tak',
                                'posts_per_page' => -1
                            ));
                            
                            // Produkty z SEO
                            $seo_products = get_posts(array(
                                'post_type' => 'product',
                                'meta_key' => 'seo_text',
                                'meta_compare' => '!=',
                                'meta_value' => '',
                                'posts_per_page' => -1
                            ));
                            
                            // Wszystkie produkty
                            $all_products = wp_count_posts('product');
                            ?>
                            
                            <div class="pp-stat-item">
                                <span class="pp-stat-number"><?php echo count($alcohol_products); ?></span>
                                <span class="pp-stat-label"><?php _e('Produkty alkoholowe', 'polskipodarek'); ?></span>
                            </div>
                            
                            <div class="pp-stat-item">
                                <span class="pp-stat-number"><?php echo count($seo_products); ?></span>
                                <span class="pp-stat-label"><?php _e('Produkty z SEO', 'polskipodarek'); ?></span>
                            </div>
                            
                            <div class="pp-stat-item">
                                <span class="pp-stat-number"><?php echo $all_products->publish; ?></span>
                                <span class="pp-stat-label"><?php _e('Wszystkie produkty', 'polskipodarek'); ?></span>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
                <!-- Ostatnie aktywności -->
                <div class="pp-dashboard-card pp-full-width">
                    <h3><?php _e('Zarejestrowane funkcjonalności', 'polskipodarek'); ?></h3>
                    <div class="pp-functionality-list">
                        <?php if (!empty(self::$submenus)) : ?>
                            <ul>
                                <?php foreach (self::$submenus as $submenu) : ?>
                                    <li>
                                        <a href="<?php echo admin_url('admin.php?page=' . $submenu['slug']); ?>">
                                            <?php echo $submenu['menu_title']; ?>
                                        </a>
                                        <span class="pp-submenu-slug">(<?php echo $submenu['slug']; ?>)</span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else : ?>
                            <p><?php _e('Brak zarejestrowanych funkcjonalności.', 'polskipodarek'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Ładuje style CSS dla panelu
     */
    public static function enqueue_admin_assets($hook) {
        
        // Tylko na stronach Polski Podarek
        if (strpos($hook, 'polski-podarek') === false) {
            return;
        }
        
        // Inline CSS dla dashboardu
        $css = '
        .pp-dashboard {
            margin-top: 20px;
        }
        
        .pp-dashboard-welcome {
            background: #fff;
            border: 1px solid #c3c4c7;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .pp-dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .pp-dashboard-card {
            background: #fff;
            border: 1px solid #c3c4c7;
            padding: 20px;
            border-radius: 4px;
        }
        
        .pp-dashboard-card.pp-full-width {
            grid-column: 1 / -1;
        }
        
        .pp-dashboard-card h3 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #1d2327;
        }
        
        .pp-quick-actions {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .pp-quick-actions li {
            margin-bottom: 10px;
        }
        
        .pp-quick-actions .button {
            display: block;
            text-align: center;
        }
        
        .pp-status-list {
            space-y: 8px;
        }
        
        .pp-status-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .pp-status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 8px;
            display: inline-block;
        }
        
        .pp-status-ok .pp-status-dot {
            background-color: #00a32a;
        }
        
        .pp-status-error .pp-status-dot {
            background-color: #d63638;
        }
        
        .pp-stats {
            display: grid;
            gap: 15px;
        }
        
        .pp-stat-item {
            text-align: center;
            padding: 15px;
            background: #f0f0f1;
            border-radius: 4px;
        }
        
        .pp-stat-number {
            display: block;
            font-size: 24px;
            font-weight: bold;
            color: #1d2327;
            margin-bottom: 5px;
        }
        
        .pp-stat-label {
            font-size: 12px;
            color: #646970;
            text-transform: uppercase;
        }
        
        .pp-functionality-list ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 10px;
        }
        
        .pp-functionality-list li {
            padding: 10px;
            background: #f0f0f1;
            border-radius: 4px;
        }
        
        .pp-functionality-list a {
            font-weight: bold;
            text-decoration: none;
        }
        
        .pp-submenu-slug {
            font-size: 11px;
            color: #646970;
            display: block;
            margin-top: 5px;
        }
        
        /* Wspólne style dla wszystkich stron */
        .pp-admin-section {
            background: #fff;
            border: 1px solid #c3c4c7;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        .pp-admin-section h2 {
            margin-top: 0;
        }
        
        .pp-status-alcohol {
            background: #f56e28;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .pp-status-active {
            color: #00a32a;
            font-weight: bold;
        }
        
        .pp-status-inactive {
            color: #d63638;
            font-weight: bold;
        }
        ';
        
        wp_add_inline_style('admin-menu', $css);
    }
    
    /**
     * Pobiera listę zarejestrowanych podmenu
     * 
     * @return array
     */
    public static function get_registered_submenus() {
        return self::$submenus;
    }
    
    /**
     * Sprawdza czy dane podmenu jest zarejestrowane
     * 
     * @param string $slug
     * @return bool
     */
    public static function is_submenu_registered($slug) {
        
        foreach (self::$submenus as $submenu) {
            if ($submenu['slug'] === $slug) {
                return true;
            }
        }
        return false;
    }
}

// Inicjalizuj menedżera menu
add_action('init', array('PolskiPodarekMenuManager', 'init'), 5);

/**
 * Funkcja pomocnicza do rejestrowania nowych podmenu
 * 
 * @param string $slug Slug strony
 * @param string $title Tytuł strony
 * @param string $menu_title Tytuł w menu
 * @param callable $callback Funkcja renderująca
 * @param int $position Pozycja (opcjonalne)
 */
function pp_register_admin_submenu($slug, $title, $menu_title, $callback, $position = 10) {
    PolskiPodarekMenuManager::register_submenu($slug, $title, $menu_title, $callback, $position);
}