<?php

/**
 * Panel administracyjny Polski Podarek
 * GUI do zarządzania funkcjonalnościami tematu
 */

// Sprawdź czy wtyczka Indemi Core nie jest aktywna
if (!function_exists('indemi_core_init')) {

    class PolskiPodarekAdmin {
        
        // Opcje w bazie danych
        const OPTION_THEME_SUPPORT = '_pp_theme_support';
        const OPTION_THUMBNAILS = '_pp_thumbnails';
        const OPTION_PATCHES = '_pp_patches';
        const OPTION_ADMIN_MENU = '_pp_admin_menu';
        
        // Nazwy formularzy
        const FORM_THEME_SUPPORT = 'pp_theme_support';
        const FORM_THUMBNAILS = 'pp_thumbnails';
        const FORM_PATCHES = 'pp_patches';
        const FORM_ADMIN_MENU = 'pp_admin_menu';

        /**
         * Obsługa masowej aktualizacji SEO
         */
        private static function handle_bulk_seo_update() {
            
            // Sprawdź nonce
            if (!wp_verify_nonce($_POST['pp_bulk_seo_nonce'], 'pp_bulk_seo_update')) {
                add_action('admin_notices', function() {
                    echo '<div class="notice notice-error is-dismissible"><p>Błąd zabezpieczeń. Spróbuj ponownie.</p></div>';
                });
                return;
            }

            $updated_count = 0;
            
            if (isset($_POST['seo_text']) && is_array($_POST['seo_text'])) {
                foreach ($_POST['seo_text'] as $product_id => $seo_text) {
                    $product_id = intval($product_id);
                    $seo_text = sanitize_textarea_field($seo_text);
                    
                    // Sprawdź czy produkt istnieje
                    if (get_post_type($product_id) === 'product') {
                        update_post_meta($product_id, 'seo_text', $seo_text);
                        $updated_count++;
                    }
                }
            }

            add_action('admin_notices', function() use ($updated_count) {
                echo '<div class="notice notice-success is-dismissible"><p>Zaktualizowano opisy SEO dla ' . $updated_count . ' produktów.</p></div>';
            });
        }
            /*
         * Inicjalizacja panelu
         */
        public static function init() {
            add_action('admin_menu', array(__CLASS__, 'add_admin_menu'));
            add_action('admin_init', array(__CLASS__, 'handle_form_submissions'));
            add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_scripts'));
            
            // Inicjalizuj domyślne opcje przy pierwszym uruchomieniu
            self::init_default_options();
        }

        /**
         * Dodaje menu w panelu administracyjnym
         */
        public static function add_admin_menu() {
            // Główne menu
            add_menu_page(
                'Konfiguracja Polski Podarek',     // Page title
                'Polski Podarek',                  // Menu title
                'manage_options',                  // Capability
                'polski-podarek',                  // Menu slug
                array(__CLASS__, 'admin_page'),    // Function
                'dashicons-store',                 // Icon
                30                                 // Position
            );

            // Podmenu - Ustawienia tematu
            add_submenu_page(
                'polski-podarek',
                'Ustawienia tematu',
                'Ustawienia tematu',
                'manage_options',
                'polski-podarek',
                array(__CLASS__, 'admin_page')
            );

            // Podmenu - SEO Produktów
            add_submenu_page(
                'polski-podarek',
                'SEO Produktów',
                'SEO Produktów',
                'manage_options',
                'polski-podarek-seo',
                array(__CLASS__, 'seo_products_page')
            );
        }

        /**
         * Główna strona panelu administracyjnego
         */
        public static function admin_page() {
            
            $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'theme_support';
            
            ?>
            <div class="wrap">
                <h1>Konfiguracja Polski Podarek</h1>
                
                <h2 class="nav-tab-wrapper">
                    <a href="?page=polski-podarek&tab=theme_support" class="nav-tab <?php echo $active_tab == 'theme_support' ? 'nav-tab-active' : ''; ?>">Wsparcie tematu</a>
                    <a href="?page=polski-podarek&tab=thumbnails" class="nav-tab <?php echo $active_tab == 'thumbnails' ? 'nav-tab-active' : ''; ?>">Miniaturki</a>
                    <a href="?page=polski-podarek&tab=patches" class="nav-tab <?php echo $active_tab == 'patches' ? 'nav-tab-active' : ''; ?>">Optymalizacje</a>
                    <a href="?page=polski-podarek&tab=admin_menu" class="nav-tab <?php echo $active_tab == 'admin_menu' ? 'nav-tab-active' : ''; ?>">Menu admina</a>
                </h2>

                <?php
                switch ($active_tab) {
                    case 'thumbnails':
                        self::thumbnails_tab();
                        break;
                    case 'patches':
                        self::patches_tab();
                        break;
                    case 'admin_menu':
                        self::admin_menu_tab();
                        break;
                    default:
                        self::theme_support_tab();
                        break;
                }
                ?>
            </div>
        /**
         * Strona SEO Produktów
         */
        public static function seo_products_page() {
            
            // Obsługa aktualizacji
            if (isset($_POST['action']) && $_POST['action'] === 'bulk_update_seo') {
                self::handle_bulk_seo_update();
            }

            // Pobierz produkty
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => 20,
                'paged' => isset($_GET['paged']) ? intval($_GET['paged']) : 1,
                'orderby' => 'title',
                'order' => 'ASC'
            );

            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $args['s'] = sanitize_text_field($_GET['search']);
            }

            $products_query = new WP_Query($args);
            ?>
            <div class="wrap">
                <h1>SEO Produktów</h1>
                
                <p class="description">
                    Zarządzaj opisami SEO dla produktów WooCommerce. Te opisy mogą być używane w meta tagach, strukturalnych danych lub innych celach SEO.
                </p>

                <!-- Formularz wyszukiwania -->
                <form method="get" action="" style="margin: 20px 0;">
                    <input type="hidden" name="page" value="polski-podarek-seo">
                    <p>
                        <input type="text" 
                               name="search" 
                               value="<?php echo esc_attr(isset($_GET['search']) ? $_GET['search'] : ''); ?>" 
                               placeholder="Szukaj produktów..."
                               style="width: 300px;">
                        <button type="submit" class="button">Szukaj</button>
                        <?php if (isset($_GET['search'])): ?>
                            <a href="?page=polski-podarek-seo" class="button">Wyczyść</a>
                        <?php endif; ?>
                    </p>
                </form>

                <?php if ($products_query->have_posts()): ?>
                    <form method="post" action="">
                        <?php wp_nonce_field('pp_bulk_seo_update', 'pp_bulk_seo_nonce'); ?>
                        <input type="hidden" name="action" value="bulk_update_seo">
                        
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">ID</th>
                                    <th style="width: 30%;">Produkt</th>
                                    <th style="width: 50%;">Opis SEO</th>
                                    <th style="width: 80px;">Znaki</th>
                                    <th style="width: 100px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($products_query->have_posts()): $products_query->the_post(); 
                                    $product_id = get_the_ID();
                                    $seo_text = get_post_meta($product_id, 'seo_text', true);
                                    $char_count = strlen($seo_text);
                                ?>
                                    <tr>
                                        <td><?php echo $product_id; ?></td>
                                        <td>
                                            <strong><?php echo get_the_title(); ?></strong>
                                            <div class="row-actions">
                                                <span><a href="<?php echo get_edit_post_link($product_id); ?>">Edytuj</a></span> |
                                                <span><a href="<?php echo get_permalink($product_id); ?>" target="_blank">Zobacz</a></span>
                                            </div>
                                        </td>
                                        <td>
                                            <textarea name="seo_text[<?php echo $product_id; ?>]" 
                                                      rows="3" 
                                                      style="width: 100%;" 
                                                      placeholder="Wprowadź opis SEO..."
                                                      data-product-id="<?php echo $product_id; ?>"><?php echo esc_textarea($seo_text); ?></textarea>
                                        </td>
                                        <td>
                                            <span class="char-count" data-for="<?php echo $product_id; ?>">
                                                <?php echo $char_count; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($seo_text)): ?>
                                                <span class="dashicons dashicons-yes-alt" style="color: #46b450;" title="Ma opis SEO"></span>
                                            <?php else: ?>
                                                <span class="dashicons dashicons-warning" style="color: #ffb900;" title="Brak opisu SEO"></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>

                        <p class="submit">
                            <input type="submit" name="submit" class="button button-primary" value="Zapisz wszystkie zmiany">
                        </p>
                    </form>

                    <!-- Paginacja -->
                    <?php
                    $big = 999999999;
                    echo paginate_links(array(
                        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                        'format' => '?paged=%#%',
                        'current' => max(1, get_query_var('paged')),
                        'total' => $products_query->max_num_pages,
                        'add_args' => isset($_GET['search']) ? array('search' => $_GET['search']) : array()
                    ));
                    ?>

                <?php else: ?>
                    <div class="notice notice-info">
                        <p>Nie znaleziono produktów.</p>
                    </div>
                <?php endif; ?>

                <!-- Statystyki -->
                <?php
                $total_products = wp_count_posts('product')->publish;
                $products_with_seo = $wpdb->get_var("
                    SELECT COUNT(*) 
                    FROM {$wpdb->postmeta} pm 
                    JOIN {$wpdb->posts} p ON pm.post_id = p.ID 
                    WHERE pm.meta_key = 'seo_text' 
                    AND pm.meta_value != '' 
                    AND p.post_type = 'product' 
                    AND p.post_status = 'publish'
                ");
                $percentage = $total_products > 0 ? round(($products_with_seo / $total_products) * 100, 1) : 0;
                ?>
                
                <div class="postbox" style="margin-top: 20px;">
                    <div class="postbox-header">
                        <h2>Statystyki SEO</h2>
                    </div>
                    <div class="inside">
                        <p><strong>Produkty z opisem SEO:</strong> <?php echo $products_with_seo; ?> z <?php echo $total_products; ?> (<?php echo $percentage; ?>%)</p>
                        <div style="background: #f0f0f1; height: 20px; border-radius: 10px; overflow: hidden;">
                            <div style="background: #46b450; height: 100%; width: <?php echo $percentage; ?>%; transition: width 0.3s;"></div>
                        </div>
                    </div>
                </div>

                <!-- JavaScript dla licznika znaków -->
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const textareas = document.querySelectorAll('textarea[data-product-id]');
                    
                    textareas.forEach(function(textarea) {
                        const productId = textarea.getAttribute('data-product-id');
                        const counter = document.querySelector('.char-count[data-for="' + productId + '"]');
                        
                        if (counter) {
                            function updateCounter() {
                                const length = textarea.value.length;
                                counter.textContent = length;
                                
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
                            
                            updateCounter();
                            textarea.addEventListener('input', updateCounter);
                        }
                    });
                });
                </script>
            </div>
            <?php
            wp_reset_postdata();
        }

        /**
         * Zakładka Theme Support
         */
        public static function theme_support_tab() {
            $options = get_option(self::OPTION_THEME_SUPPORT, array());
            ?>
            <form method="post" action="">
                <?php wp_nonce_field('pp_theme_support_save', 'pp_theme_support_nonce'); ?>
                <input type="hidden" name="action" value="save_theme_support">
                
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><label>Wsparcie funkcji tematu</label></th>
                            <td>
                                <?php
                                $theme_features = array(
                                    'post-thumbnails' => 'Miniaturki postów (Featured Images)',
                                    'title-tag' => 'Zarządzanie tagiem title',
                                    'menus' => 'Wsparcie dla menu',
                                    'html5' => 'Wsparcie HTML5',
                                    'custom-logo' => 'Logo w Customizer',
                                    'automatic-feed-links' => 'Automatyczne linki RSS',
                                    'align-wide' => 'Szerokie bloki Gutenberg',
                                    'responsive-embeds' => 'Responsywne osadzenia',
                                    'editor-styles' => 'Style edytora'
                                );
                                
                                foreach ($theme_features as $feature => $label) {
                                    $checked = isset($options[$feature]) ? $options[$feature] : false;
                                    ?>
                                    <fieldset>
                                        <label for="<?php echo self::FORM_THEME_SUPPORT; ?>[<?php echo $feature; ?>]">
                                            <input name="<?php echo self::FORM_THEME_SUPPORT; ?>[<?php echo $feature; ?>]" 
                                                   type="checkbox" 
                                                   id="<?php echo self::FORM_THEME_SUPPORT; ?>[<?php echo $feature; ?>]" 
                                                   value="1" 
                                                   <?php checked($checked, 1); ?>>
                                            <?php echo $label; ?>
                                        </label>
                                    </fieldset>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <p class="submit">
                    <input type="submit" name="submit" class="button button-primary" value="Zapisz zmiany">
                </p>
            </form>
            <?php
        }

        /**
         * Zakładka Miniaturki
         */
        public static function thumbnails_tab() {
            $options = get_option(self::OPTION_THUMBNAILS, array());
            ?>
            <button type="button" class="button button-secondary" id="add-thumbnail-row">Dodaj rozmiar miniatury</button>
            
            <form method="post" action="">
                <?php wp_nonce_field('pp_thumbnails_save', 'pp_thumbnails_nonce'); ?>
                <input type="hidden" name="action" value="save_thumbnails">
                
                <table class="form-table" id="thumbnails-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><label>Miniaturki</label></th>
                            <td id="thumbnails-content">
                                <?php 
                                $i = 0;
                                foreach ($options as $name => $thumb) { 
                                ?>
                                    <fieldset class="thumbnail-row">
                                        <label>Nazwa</label>
                                        <input name="<?php echo self::FORM_THUMBNAILS; ?>[<?php echo $i; ?>][name]" 
                                               type="text" 
                                               value="<?php echo esc_attr($thumb['name']); ?>" 
                                               placeholder="nazwa_miniatury">
                                        
                                        <label>Szerokość</label>
                                        <input name="<?php echo self::FORM_THUMBNAILS; ?>[<?php echo $i; ?>][width]" 
                                               type="number" 
                                               value="<?php echo esc_attr($thumb['width']); ?>" 
                                               class="small-text" 
                                               min="0" 
                                               step="1">
                                        
                                        <label>Wysokość</label>
                                        <input name="<?php echo self::FORM_THUMBNAILS; ?>[<?php echo $i; ?>][height]" 
                                               type="number" 
                                               value="<?php echo esc_attr($thumb['height']); ?>" 
                                               class="small-text" 
                                               min="0" 
                                               step="1">
                                        
                                        <label>
                                            <input name="<?php echo self::FORM_THUMBNAILS; ?>[<?php echo $i; ?>][crop]" 
                                                   type="checkbox" 
                                                   value="1" 
                                                   <?php checked($thumb['crop'], 1); ?>>
                                            Przycinaj
                                        </label>
                                        
                                        <button type="button" class="button button-secondary remove-thumbnail-row">Usuń</button>
                                    </fieldset>
                                <?php 
                                    $i++;
                                } 
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <p class="submit">
                    <input type="submit" name="submit" class="button button-primary" value="Zapisz zmiany">
                </p>
            </form>
            <?php
        }

        /**
         * Zakładka Optymalizacje
         */
        public static function patches_tab() {
            $options = get_option(self::OPTION_PATCHES, array());
            ?>
            <form method="post" action="">
                <?php wp_nonce_field('pp_patches_save', 'pp_patches_nonce'); ?>
                <input type="hidden" name="action" value="save_patches">
                
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><label>Optymalizacje i łatki</label></th>
                            <td>
                                <?php
                                $patches = array(
                                    'clean_wp_head' => 'Wyczyść wp_head z niepotrzebnych elementów',
                                    'disable_emojis' => 'Wyłącz emoji w edytorze',
                                    'add_img_fluid' => 'Dodaj klasę img-fluid do obrazków',
                                    'remove_p_from_images' => 'Usuń tagi &lt;p&gt; wokół obrazków',
                                    'hide_admin_bar' => 'Ukryj belkę admina na froncie',
                                    'custom_admin_footer' => 'Własny footer w panelu admina',
                                    'auto_update_plugins' => 'Automatyczne aktualizacje wtyczek',
                                    'remove_wp_logo' => 'Usuń logo WP z belki admina'
                                );
                                
                                foreach ($patches as $patch => $label) {
                                    $checked = isset($options[$patch]) ? $options[$patch] : false;
                                    ?>
                                    <fieldset>
                                        <label for="<?php echo self::FORM_PATCHES; ?>[<?php echo $patch; ?>]">
                                            <input name="<?php echo self::FORM_PATCHES; ?>[<?php echo $patch; ?>]" 
                                                   type="checkbox" 
                                                   id="<?php echo self::FORM_PATCHES; ?>[<?php echo $patch; ?>]" 
                                                   value="1" 
                                                   <?php checked($checked, 1); ?>>
                                            <?php echo $label; ?>
                                        </label>
                                    </fieldset>
                                    <?php
                                }
                                ?>
                                
                                <fieldset>
                                    <label for="excerpt_limit">
                                        Limit słów w zajawce:
                                        <input name="<?php echo self::FORM_PATCHES; ?>[excerpt_limit]" 
                                               type="number" 
                                               id="excerpt_limit" 
                                               value="<?php echo esc_attr(isset($options['excerpt_limit']) ? $options['excerpt_limit'] : 25); ?>" 
                                               class="small-text" 
                                               min="1" 
                                               step="1">
                                    </label>
                                </fieldset>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <p class="submit">
                    <input type="submit" name="submit" class="button button-primary" value="Zapisz zmiany">
                </p>
            </form>
            <?php
        }

        /**
         * Zakładka Menu Admina
         */
        public static function admin_menu_tab() {
            $options = get_option(self::OPTION_ADMIN_MENU, array());
            ?>
            <form method="post" action="">
                <?php wp_nonce_field('pp_admin_menu_save', 'pp_admin_menu_nonce'); ?>
                <input type="hidden" name="action" value="save_admin_menu">
                
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><label>Ukryj elementy menu</label></th>
                            <td>
                                <?php
                                $menu_items = array(
                                    'index.php' => 'Dashboard',
                                    'edit.php' => 'Wpisy',
                                    'upload.php' => 'Media',
                                    'edit.php?post_type=page' => 'Strony',
                                    'edit-comments.php' => 'Komentarze',
                                    'themes.php' => 'Wygląd',
                                    'plugins.php' => 'Wtyczki',
                                    'users.php' => 'Użytkownicy',
                                    'tools.php' => 'Narzędzia'
                                );
                                
                                foreach ($menu_items as $slug => $label) {
                                    $checked = isset($options[$slug]) ? $options[$slug] : false;
                                    ?>
                                    <fieldset>
                                        <label for="<?php echo self::FORM_ADMIN_MENU; ?>[<?php echo esc_attr($slug); ?>]">
                                            <input name="<?php echo self::FORM_ADMIN_MENU; ?>[<?php echo esc_attr($slug); ?>]" 
                                                   type="checkbox" 
                                                   id="<?php echo self::FORM_ADMIN_MENU; ?>[<?php echo esc_attr($slug); ?>]" 
                                                   value="1" 
                                                   <?php checked($checked, 1); ?>>
                                            <?php echo $label; ?>
                                        </label>
                                    </fieldset>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <p class="submit">
                    <input type="submit" name="submit" class="button button-primary" value="Zapisz zmiany">
                </p>
            </form>
            <?php
        }

        /**
         * Obsługa zapisywania formularzy
         */
        public static function handle_form_submissions() {
            if (!isset($_POST['action'])) {
                return;
            }

            switch ($_POST['action']) {
                case 'save_theme_support':
                    self::save_theme_support();
                    break;
                case 'save_thumbnails':
                    self::save_thumbnails();
                    break;
                case 'save_patches':
                    self::save_patches();
                    break;
                case 'save_admin_menu':
                    self::save_admin_menu();
                    break;
            }
        }

        /**
         * Zapisuje ustawienia theme support
         */
        private static function save_theme_support() {
            if (!wp_verify_nonce($_POST['pp_theme_support_nonce'], 'pp_theme_support_save')) {
                return;
            }

            $options = isset($_POST[self::FORM_THEME_SUPPORT]) ? $_POST[self::FORM_THEME_SUPPORT] : array();
            update_option(self::OPTION_THEME_SUPPORT, $options);
            
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible"><p>Ustawienia wsparcia tematu zostały zapisane.</p></div>';
            });
        }

        /**
         * Zapisuje ustawienia miniaturek
         */
        private static function save_thumbnails() {
            if (!wp_verify_nonce($_POST['pp_thumbnails_nonce'], 'pp_thumbnails_save')) {
                return;
            }

            $thumbnails = array();
            if (isset($_POST[self::FORM_THUMBNAILS])) {
                foreach ($_POST[self::FORM_THUMBNAILS] as $thumb) {
                    if (!empty($thumb['name'])) {
                        $thumbnails[$thumb['name']] = array(
                            'name' => sanitize_text_field($thumb['name']),
                            'width' => intval($thumb['width']),
                            'height' => intval($thumb['height']),
                            'crop' => isset($thumb['crop']) ? 1 : 0
                        );
                    }
                }
            }
            
            update_option(self::OPTION_THUMBNAILS, $thumbnails);
            
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible"><p>Ustawienia miniaturek zostały zapisane.</p></div>';
            });
        }

        /**
         * Zapisuje ustawienia optymalizacji
         */
        private static function save_patches() {
            if (!wp_verify_nonce($_POST['pp_patches_nonce'], 'pp_patches_save')) {
                return;
            }

            $options = isset($_POST[self::FORM_PATCHES]) ? $_POST[self::FORM_PATCHES] : array();
            update_option(self::OPTION_PATCHES, $options);
            
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible"><p>Ustawienia optymalizacji zostały zapisane.</p></div>';
            });
        }

        /**
         * Zapisuje ustawienia menu admina
         */
        private static function save_admin_menu() {
            if (!wp_verify_nonce($_POST['pp_admin_menu_nonce'], 'pp_admin_menu_save')) {
                return;
            }

            $options = isset($_POST[self::FORM_ADMIN_MENU]) ? $_POST[self::FORM_ADMIN_MENU] : array();
            update_option(self::OPTION_ADMIN_MENU, $options);
            
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible"><p>Ustawienia menu admina zostały zapisane.</p></div>';
            });
        }

        /**
         * Ładuje skrypty admina
         */
        public static function enqueue_admin_scripts($hook) {
            if ($hook !== 'settings_page_polski-podarek') {
                return;
            }
            
            wp_enqueue_script('polski-podarek-admin', get_template_directory_uri() . '/js/admin.js', array('jquery'), '1.0.0', true);
        }

        /**
         * Inicjalizuje domyślne opcje
         */
        private static function init_default_options() {
            
            // Domyślne theme support
            if (!get_option(self::OPTION_THEME_SUPPORT)) {
                $defaults = array(
                    'post-thumbnails' => 1,
                    'title-tag' => 1,
                    'menus' => 1,
                    'html5' => 1,
                    'custom-logo' => 1
                );
                update_option(self::OPTION_THEME_SUPPORT, $defaults);
            }

            // Domyślne miniaturki
            if (!get_option(self::OPTION_THUMBNAILS)) {
                $defaults = array(
                    'miniaturka' => array('name' => 'miniaturka', 'width' => 150, 'height' => 150, 'crop' => 1),
                    'blog' => array('name' => 'blog', 'width' => 450, 'height' => 450, 'crop' => 1),
                    'product_thumb' => array('name' => 'product_thumb', 'width' => 300, 'height' => 300, 'crop' => 1)
                );
                update_option(self::OPTION_THUMBNAILS, $defaults);
            }

            // Domyślne optymalizacje
            if (!get_option(self::OPTION_PATCHES)) {
                $defaults = array(
                    'clean_wp_head' => 1,
                    'disable_emojis' => 1,
                    'add_img_fluid' => 1,
                    'excerpt_limit' => 25
                );
                update_option(self::OPTION_PATCHES, $defaults);
            }

            // Domyślne menu admina
            if (!get_option(self::OPTION_ADMIN_MENU)) {
                update_option(self::OPTION_ADMIN_MENU, array());
            }
        }
    }

    // Inicjalizuj panel tylko w panelu administracyjnym
    if (is_admin()) {
        PolskiPodarekAdmin::init();
    }

} // Koniec sprawdzenia czy wtyczka nie jest aktywna