<?php

/**
 * Polski Podarek - Strona głównych ustawień z tabami
 * 
 * @package PolskiPodarek
 * @version 1.0.0
 */

// Zabezpieczenie przed bezpośrednim dostępem
if (!defined('ABSPATH')) {
    exit;
}

class PolskiPodarekSettingsPage {
    
    const OPTION_PREFIX = '_pp_';
    
    // Sekcje/taby konfiguracyjne
    const SECTIONS = array(
        'general' => 'Ogólne',
        'woocommerce' => 'WooCommerce',
        'seo' => 'SEO',
        'media' => 'Media',
        'optimization' => 'Optymalizacja'
    );
    
    /**
     * Inicjalizacja
     */
    public static function init() {
        
        // Sprawdź czy wtyczka Indemi Core nie jest aktywna
        if (function_exists('indemi_core_init')) {
            return;
        }
        
        // Zarejestruj stronę ustawień
        add_action('init', array(__CLASS__, 'register_page'), 20);
        
        // Obsługa zapisywania
        add_action('admin_init', array(__CLASS__, 'handle_settings_save'));
        
        // Inicjalizuj domyślne opcje
        add_action('admin_init', array(__CLASS__, 'init_default_options'));
    }
    
    /**
     * Rejestruje stronę w menu
     */
    public static function register_page() {
        
        pp_register_admin_submenu(
            'polski-podarek-settings',
            __('Ustawienia główne', 'polskipodarek'),
            __('Ustawienia', 'polskipodarek'),
            array(__CLASS__, 'render_page'),
            5 // Wysoka pozycja
        );
    }
    
    /**
     * Renderuje stronę z tabami
     */
    public static function render_page() {
        
        $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
        
        ?>
        <div class="wrap">
            <h1><?php _e('Polski Podarek - Ustawienia', 'polskipodarek'); ?></h1>
            
            <!-- Nawigacja tabów -->
            <nav class="nav-tab-wrapper">
                <?php foreach (self::SECTIONS as $tab_key => $tab_label) : ?>
                    <a href="?page=polski-podarek-settings&tab=<?php echo $tab_key; ?>" 
                       class="nav-tab <?php echo $current_tab == $tab_key ? 'nav-tab-active' : ''; ?>">
                        <?php echo $tab_label; ?>
                    </a>
                <?php endforeach; ?>
            </nav>
            
            <!-- Zawartość tabów -->
            <div class="pp-admin-section">
                <?php
                switch ($current_tab) {
                    case 'general':
                        self::render_general_tab();
                        break;
                    case 'woocommerce':
                        self::render_woocommerce_tab();
                        break;
                    case 'seo':
                        self::render_seo_tab();
                        break;
                    case 'media':
                        self::render_media_tab();
                        break;
                    case 'optimization':
                        self::render_optimization_tab();
                        break;
                    default:
                        self::render_general_tab();
                }
                ?>
            </div>
        </div>
        
        <style>
        .nav-tab-wrapper {
            margin-bottom: 0;
        }
        .nav-tab-active {
            background: #fff;
            border-bottom: 1px solid #fff;
        }
        </style>
        <?php
    }
    
    /**
     * Tab: Ogólne
     */
    private static function render_general_tab() {
        
        $options = get_option(self::OPTION_PREFIX . 'general', array());
        
        ?>
        <form method="post" action="">
            <?php wp_nonce_field('pp_save_general', 'pp_general_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Włącz debug', 'polskipodarek'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="pp_general[debug_mode]" value="1" 
                                   <?php checked(isset($options['debug_mode']) ? $options['debug_mode'] : 0, 1); ?>>
                            <?php _e('Włącz tryb debugowania', 'polskipodarek'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Pokazuje dodatkowe informacje diagnostyczne w panelu administracyjnym.', 'polskipodarek'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Wyłącz emoji', 'polskipodarek'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="pp_general[disable_emojis]" value="1"
                                   <?php checked(isset($options['disable_emojis']) ? $options['disable_emojis'] : 1, 1); ?>>
                            <?php _e('Wyłącz emoji w WordPress', 'polskipodarek'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Usuwa skrypty emoji, co może nieznacznie przyspieszyć ładowanie strony.', 'polskipodarek'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Niestandardowe breadcrumbs', 'polskipodarek'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="pp_general[custom_breadcrumbs]" value="1"
                                   <?php checked(isset($options['custom_breadcrumbs']) ? $options['custom_breadcrumbs'] : 1, 1); ?>>
                            <?php _e('Użyj niestandardowych breadcrumbs', 'polskipodarek'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Włącza zoptymalizowane breadcrumbs dostosowane do sklepu.', 'polskipodarek'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Optymalizacja tytułów', 'polskipodarek'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="pp_general[optimize_titles]" value="1"
                                   <?php checked(isset($options['optimize_titles']) ? $options['optimize_titles'] : 1, 1); ?>>
                            <?php _e('Włącz optymalizację tytułów stron', 'polskipodarek'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Automatycznie optymalizuje tytuły dla lepszego SEO.', 'polskipodarek'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
        <?php
    }
    
    /**
     * Tab: WooCommerce
     */
    private static function render_woocommerce_tab() {
        
        $options = get_option(self::OPTION_PREFIX . 'woocommerce', array());
        
        ?>
        <form method="post" action="">
            <?php wp_nonce_field('pp_save_woocommerce', 'pp_woocommerce_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Weryfikacja wieku', 'polskipodarek'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="pp_woocommerce[alcohol_verification]" value="1"
                                   <?php checked(isset($options['alcohol_verification']) ? $options['alcohol_verification'] : 1, 1); ?>>
                            <?php _e('Włącz weryfikację wieku dla produktów alkoholowych', 'polskipodarek'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Dodaje checkbox potwierdzenia pełnoletności podczas zakupu produktów alkoholowych.', 'polskipodarek'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Ukryj pobranie za pobraniem', 'polskipodarek'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="pp_woocommerce[hide_cod_inpost]" value="1"
                                   <?php checked(isset($options['hide_cod_inpost']) ? $options['hide_cod_inpost'] : 1, 1); ?>>
                            <?php _e('Ukryj płatność za pobraniem dla InPost', 'polskipodarek'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Ukrywa opcję płatności za pobraniem gdy wybrana jest dostawa InPost.', 'polskipodarek'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Produkty wyprzedane na końcu', 'polskipodarek'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="pp_woocommerce[out_of_stock_end]" value="1"
                                   <?php checked(isset($options['out_of_stock_end']) ? $options['out_of_stock_end'] : 1, 1); ?>>
                            <?php _e('Przenieś produkty wyprzedane na koniec listy', 'polskipodarek'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Produkty bez stanu magazynowego będą pokazywane na końcu list produktów.', 'polskipodarek'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Przyciski ilości', 'polskipodarek'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="pp_woocommerce[quantity_buttons]" value="1"
                                   <?php checked(isset($options['quantity_buttons']) ? $options['quantity_buttons'] : 1, 1); ?>>
                            <?php _e('Dodaj przyciski +/- do pól ilości', 'polskipodarek'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Ułatwia klientom zmianę ilości produktów w koszyku.', 'polskipodarek'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Usuń upsells', 'polskipodarek'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="pp_woocommerce[remove_upsells]" value="1"
                                   <?php checked(isset($options['remove_upsells']) ? $options['remove_upsells'] : 0, 1); ?>>
                            <?php _e('Usuń sekcję produktów powiązanych', 'polskipodarek'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Ukrywa sekcję "Może Cię zainteresować" na stronach produktów.', 'polskipodarek'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Optymalizacja dostawy', 'polskipodarek'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="pp_woocommerce[delivery_optimization]" value="1"
                                   <?php checked(isset($options['delivery_optimization']) ? $options['delivery_optimization'] : 1, 1); ?>>
                            <?php _e('Włącz optymalizację opcji dostawy', 'polskipodarek'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Automatycznie dostosowuje opcje dostawy w zależności od produktów w koszyku.', 'polskipodarek'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
        <?php
    }
    
    /**
     * Tab: SEO
     */
    private static function render_seo_tab() {
        
        $options = get_option(self::OPTION_PREFIX . 'seo', array());
        
        ?>
        <form method="post" action="">
            <?php wp_nonce_field('pp_save_seo', 'pp_seo_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Metabox SEO produktów', 'polskipodarek'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="pp_seo[product_seo_metabox]" value="1"
                                   <?php checked(isset($options['product_seo_metabox']) ? $options['product_seo_metabox'] : 1, 1); ?>>
                            <?php _e('Dodaj metabox SEO do produktów', 'polskipodarek'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Pozwala dodawać niestandardowe opisy SEO do każdego produktu.', 'polskipodarek'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Schema markup', 'polskipodarek'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="pp_seo[schema_markup]" value="1"
                                   <?php checked(isset($options['schema_markup']) ? $options['schema_markup'] : 1, 1); ?>>
                            <?php _e('Włącz schema markup', 'polskipodarek'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Dodaje strukturalne dane JSON-LD dla lepszego rozpoznawania przez wyszukiwarki.', 'polskipodarek'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Optymalizacja obrazów', 'polskipodarek'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="pp_seo[image_optimization]" value="1"
                                   <?php checked(isset($options['image_optimization']) ? $options['image_optimization'] : 1, 1); ?>>
                            <?php _e('Automatycznie dodaj alt i title do obrazów', 'polskipodarek'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Automatycznie generuje atrybuty alt i title dla obrazów na podstawie nazwy produktu.', 'polskipodarek'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Optymalizacja URL', 'polskipodarek'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="pp_seo[url_optimization]" value="1"
                                   <?php checked(isset($options['url_optimization']) ? $options['url_optimization'] : 1, 1); ?>>
                            <?php _e('Włącz optymalizację struktury URL', 'polskipodarek'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Czyści i optymalizuje strukturę URL dla lepszego SEO.', 'polskipodarek'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Meta opisy', 'polskipodarek'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="pp_seo[auto_meta_descriptions]" value="1"
                                   <?php checked(isset($options['auto_meta_descriptions']) ? $options['auto_meta_descriptions'] : 1, 1); ?>>
                            <?php _e('Automatyczne generowanie meta opisów', 'polskipodarek'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Automatycznie generuje meta opisy dla stron bez własnych opisów.', 'polskipodarek'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
        <?php
    }
    
    /**
     * Tab: Media
     */
    private static function render_media_tab() {
        
        $options = get_option(self::OPTION_PREFIX . 'media', array());
        
        ?>
        <form method="post" action="">
            <?php wp_nonce_field('pp_save_media', 'pp_media_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Niestandardowe rozmiary miniatur', 'polskipodarek'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="pp_media[custom_thumbnails]" value="1"
                                   <?php checked(isset($options['custom_thumbnails']) ? $options['custom_thumbnails'] : 1, 1); ?>>
                            <?php _e('Włącz niestandardowe rozmiary miniatur', 'polskipodarek'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Dodaje zoptymalizowane rozmiary miniatur dla sklepu internetowego.', 'polskipodarek'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Usuwanie nieużywanych miniatur', 'polskipodarek'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="pp_media[remove_unused_thumbs]" value="1"
                                   <?php checked(isset($options['remove_unused_thumbs']) ? $options['remove_unused_thumbs'] : 0, 1); ?>>
                            <?php _e('Automatycznie usuń nieużywane miniatury', 'polskipodarek'); ?>
                        </label>
                        <p class="description">
                            <strong><?php _e('Uwaga:', 'polskipodarek'); ?></strong>
                            <?php _e('Ta opcja może znacznie zwiększyć czas ładowania strony i powinna być używana ostrożnie.', 'polskipodarek'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Kompresja obrazów', 'polskipodarek'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="pp_media[image_compression]" value="1"
                                   <?php checked(isset($options['image_compression']) ? $options['image_compression'] : 0, 1); ?>>
                            <?php _e('Włącz automatyczną kompresję obrazów', 'polskipodarek'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Automatycznie kompresuje przesyłane obrazy w celu zmniejszenia ich rozmiaru.', 'polskipodarek'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Lazy loading', 'polskipodarek'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="pp_media[lazy_loading]" value="1"
                                   <?php checked(isset($options['lazy_loading']) ? $options['lazy_loading'] : 1, 1); ?>>
                            <?php _e('Włącz opóźnione ładowanie obrazów', 'polskipodarek'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Obrazy będą ładowane dopiero gdy staną się widoczne na ekranie.', 'polskipodarek'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
        <?php
    }
    
    /**
     * Tab: Optymalizacja
     */
    private static function render_optimization_tab() {
        
        $options = get_option(self::OPTION_PREFIX . 'optimization', array());
        
        ?>
        <form method="post" action="">
            <?php wp_nonce_field('pp_save_optimization', 'pp_optimization_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Fragmenty koszyka', 'polskipodarek'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="pp_optimization[cart_fragments]" value="1"
                                   <?php checked(isset($options['cart_fragments']) ? $options['cart_fragments'] : 1, 1); ?>>
                            <?php _e('Włącz optymalizację fragmentów koszyka', 'polskipodarek'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Optymalizuje AJAX aktualizacje koszyka dla lepszej wydajności.', 'polskipodarek'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Optymalizacja paginacji', 'polskipodarek'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="pp_optimization[pagination_links]" value="1"
                                   <?php checked(isset($options['pagination_links']) ? $options['pagination_links'] : 1, 1); ?>>
                            <?php _e('Dodaj linki prev/next do paginacji', 'polskipodarek'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Dodaje meta linki prev/next dla lepszej nawigacji i SEO.', 'polskipodarek'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Optymalizacja zapytań', 'polskipodarek'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="pp_optimization[query_optimization]" value="1"
                                   <?php checked(isset($options['query_optimization']) ? $options['query_optimization'] : 1, 1); ?>>
                            <?php _e('Włącz optymalizację zapytań produktów', 'polskipodarek'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Optymalizuje zapytania do bazy danych dla list produktów.', 'polskipodarek'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Minifikacja CSS/JS', 'polskipodarek'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="pp_optimization[minify_assets]" value="1"
                                   <?php checked(isset($options['minify_assets']) ? $options['minify_assets'] : 0, 1); ?>>
                            <?php _e('Włącz minifikację plików CSS i JavaScript', 'polskipodarek'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Automatycznie minifikuje pliki CSS i JS dla szybszego ładowania.', 'polskipodarek'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Cache strony', 'polskipodarek'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="pp_optimization[page_cache]" value="1"
                                   <?php checked(isset($options['page_cache']) ? $options['page_cache'] : 0, 1); ?>>
                            <?php _e('Włącz podstawowe cache strony', 'polskipodarek'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Podstawowe cache dla stron statycznych. Nie używaj jeśli masz wtyczkę cache.', 'polskipodarek'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
        <?php
    }
    
    /**
     * Obsługuje zapisywanie ustawień
     */
    public static function handle_settings_save() {
        
        // Tab: Ogólne
        if (isset($_POST['pp_general_nonce']) && wp_verify_nonce($_POST['pp_general_nonce'], 'pp_save_general')) {
            $options = isset($_POST['pp_general']) ? $_POST['pp_general'] : array();
            update_option(self::OPTION_PREFIX . 'general', $options);
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Ustawienia zostały zapisane.', 'polskipodarek') . '</p></div>';
            });
        }
        
        // Tab: WooCommerce
        if (isset($_POST['pp_woocommerce_nonce']) && wp_verify_nonce($_POST['pp_woocommerce_nonce'], 'pp_save_woocommerce')) {
            $options = isset($_POST['pp_woocommerce']) ? $_POST['pp_woocommerce'] : array();
            update_option(self::OPTION_PREFIX . 'woocommerce', $options);
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Ustawienia WooCommerce zostały zapisane.', 'polskipodarek') . '</p></div>';
            });
        }
        
        // Tab: SEO
        if (isset($_POST['pp_seo_nonce']) && wp_verify_nonce($_POST['pp_seo_nonce'], 'pp_save_seo')) {
            $options = isset($_POST['pp_seo']) ? $_POST['pp_seo'] : array();
            update_option(self::OPTION_PREFIX . 'seo', $options);
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Ustawienia SEO zostały zapisane.', 'polskipodarek') . '</p></div>';
            });
        }
        
        // Tab: Media
        if (isset($_POST['pp_media_nonce']) && wp_verify_nonce($_POST['pp_media_nonce'], 'pp_save_media')) {
            $options = isset($_POST['pp_media']) ? $_POST['pp_media'] : array();
            update_option(self::OPTION_PREFIX . 'media', $options);
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Ustawienia mediów zostały zapisane.', 'polskipodarek') . '</p></div>';
            });
        }
        
        // Tab: Optymalizacja
        if (isset($_POST['pp_optimization_nonce']) && wp_verify_nonce($_POST['pp_optimization_nonce'], 'pp_save_optimization')) {
            $options = isset($_POST['pp_optimization']) ? $_POST['pp_optimization'] : array();
            update_option(self::OPTION_PREFIX . 'optimization', $options);
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Ustawienia optymalizacji zostały zapisane.', 'polskipodarek') . '</p></div>';
            });
        }
    }
    
    /**
     * Inicjalizuje domyślne opcje
     */
    public static function init_default_options() {
        
        // Domyślne opcje ogólne
        if (!get_option(self::OPTION_PREFIX . 'general')) {
            update_option(self::OPTION_PREFIX . 'general', array(
                'debug_mode' => 0,
                'disable_emojis' => 1,
                'custom_breadcrumbs' => 1,
                'optimize_titles' => 1
            ));
        }
        
        // Domyślne opcje WooCommerce
        if (!get_option(self::OPTION_PREFIX . 'woocommerce')) {
            update_option(self::OPTION_PREFIX . 'woocommerce', array(
                'alcohol_verification' => 1,
                'hide_cod_inpost' => 1,
                'out_of_stock_end' => 1,
                'quantity_buttons' => 1,
                'remove_upsells' => 0,
                'delivery_optimization' => 1
            ));
        }
        
        // Domyślne opcje SEO
        if (!get_option(self::OPTION_PREFIX . 'seo')) {
            update_option(self::OPTION_PREFIX . 'seo', array(
                'product_seo_metabox' => 1,
                'schema_markup' => 1,
                'image_optimization' => 1,
                'url_optimization' => 1,
                'auto_meta_descriptions' => 1
            ));
        }
        
        // Domyślne opcje mediów
        if (!get_option(self::OPTION_PREFIX . 'media')) {
            update_option(self::OPTION_PREFIX . 'media', array(
                'custom_thumbnails' => 1,
                'remove_unused_thumbs' => 0,
                'image_compression' => 0,
                'lazy_loading' => 1
            ));
        }
        
        // Domyślne opcje optymalizacji
        if (!get_option(self::OPTION_PREFIX . 'optimization')) {
            update_option(self::OPTION_PREFIX . 'optimization', array(
                'cart_fragments' => 1,
                'pagination_links' => 1,
                'query_optimization' => 1,
                'minify_assets' => 0,
                'page_cache' => 0
            ));
        }
    }
    
    /**
     * Pomocnicze funkcje dostępu do opcji
     */
    
    /**
     * Pobiera opcję z domyślną wartością
     */
    public static function get_option($section, $key, $default = false) {
        
        $options = get_option(self::OPTION_PREFIX . $section, array());
        return isset($options[$key]) ? $options[$key] : $default;
    }
    
    /**
     * Sprawdza czy opcja jest włączona
     */
    public static function is_option_enabled($section, $key) {
        
        return self::get_option($section, $key, false) == 1;
    }
    
    /**
     * Pobiera wszystkie opcje z danej sekcji
     */
    public static function get_section_options($section) {
        
        return get_option(self::OPTION_PREFIX . $section, array());
    }
    
    /**
     * Resetuje sekcję do domyślnych wartości
     */
    public static function reset_section($section) {
        
        delete_option(self::OPTION_PREFIX . $section);
        self::init_default_options();
    }
    
    /**
     * Eksportuje wszystkie ustawienia
     */
    public static function export_all_settings() {
        
        $settings = array();
        foreach (array_keys(self::SECTIONS) as $section) {
            $settings[$section] = get_option(self::OPTION_PREFIX . $section, array());
        }
        
        $settings['export_date'] = current_time('Y-m-d H:i:s');
        $settings['site_url'] = get_site_url();
        
        return $settings;
    }
    
    /**
     * Importuje ustawienia z tablicy
     */
    public static function import_settings($settings) {
        
        if (!is_array($settings)) {
            return false;
        }
        
        $imported_sections = 0;
        foreach (array_keys(self::SECTIONS) as $section) {
            if (isset($settings[$section]) && is_array($settings[$section])) {
                update_option(self::OPTION_PREFIX . $section, $settings[$section]);
                $imported_sections++;
            }
        }
        
        return $imported_sections;
    }
}

// Inicjalizuj stronę ustawień
PolskiPodarekSettingsPage::init();

/**
 * Funkcje pomocnicze dla łatwego dostępu do opcji
 */

if (!function_exists('pp_get_option')) {
    /**
     * Pobiera opcję Polski Podarek
     * 
     * @param string $section Sekcja (general, woocommerce, seo, media, optimization)
     * @param string $key Klucz opcji
     * @param mixed $default Domyślna wartość
     * @return mixed
     */
    function pp_get_option($section, $key, $default = false) {
        return PolskiPodarekSettingsPage::get_option($section, $key, $default);
    }
}

if (!function_exists('pp_is_enabled')) {
    /**
     * Sprawdza czy opcja jest włączona
     * 
     * @param string $section Sekcja
     * @param string $key Klucz opcji
     * @return bool
     */
    function pp_is_enabled($section, $key) {
        return PolskiPodarekSettingsPage::is_option_enabled($section, $key);
    }
}

if (!function_exists('pp_get_section')) {
    /**
     * Pobiera wszystkie opcje z sekcji
     * 
     * @param string $section Nazwa sekcji
     * @return array
     */
    function pp_get_section($section) {
        return PolskiPodarekSettingsPage::get_section_options($section);
    }
}