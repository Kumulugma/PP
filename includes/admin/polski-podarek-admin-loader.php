<?php

/**
 * Polski Podarek - Główny plik ładujący komponenty admina
 * Ten plik zastępuje poprzednie require_once dla funkcjonalności administratora
 * 
 * @package PolskiPodarek
 * @version 1.0.0
 */

// Zabezpieczenie przed bezpośrednim dostępem
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Klasa ładująca wszystkie komponenty admina Polski Podarek
 */
class PolskiPodarekAdminLoader {
    
    /**
     * Ścieżka do katalogu admin
     */
    const ADMIN_DIR = 'includes/admin/';
    
    /**
     * Lista plików do załadowania
     */
    private static $admin_files = array(
        // Podstawowe komponenty (zachowane z oryginalnej struktury)
        'admin-bar.php',
        'category-options.php',
        
        // Nowe komponenty systemu menu
        'polski-podarek-menu-manager.php',           // Centralny menedżer menu
        'polski-podarek-settings-page.php',         // Główna strona ustawień z tabami
        'polski-podarek-alcohol-page.php',          // Strona weryfikacji wieku
        'polski-podarek-seo-page.php',              // Strony SEO produktów
        'polski-podarek-product-alcohol-column.php', // Kolumna alkoholowy na liście produktów
        'polski-podarek-product-seo-column.php',    // Kolumna SEO na liście produktów
        'polski-podarek-custom-product-title.php',  // Migracja i obsługa niestandardowych tytułów
        
        // Zachowane legacy (jeśli jeszcze potrzebujesz)
        'polski-podarek-panel.php',                 // Stary panel - można usunąć
        'polski-podarek-functions.php',             // Funkcje pomocnicze - można usunąć
    );
    
    /**
     * Inicjalizacja loadera
     */
    public static function init() {
        
        // Sprawdź czy wtyczka Indemi Core nie jest aktywna
        if (function_exists('indemi_core_init')) {
            return;
        }
        
        // Załaduj wszystkie pliki administratora
        self::load_admin_files();
        
        // Dodaj hook do sprawdzenia czy wszystko się załadowało
        add_action('admin_init', array(__CLASS__, 'verify_loaded_components'), 5);
        
        // Dodaj informacje o załadowanych komponentach do debug
        if (defined('WP_DEBUG') && WP_DEBUG) {
            add_action('admin_notices', array(__CLASS__, 'show_debug_info'));
        }
    }
    
    /**
     * Ładuje wszystkie pliki administratora
     */
    private static function load_admin_files() {
        
        foreach (self::$admin_files as $file) {
            $file_path = get_template_directory() . '/' . self::ADMIN_DIR . $file;
            
            if (file_exists($file_path)) {
                require_once $file_path;
                
                // Log załadowania w trybie debug
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log("Polski Podarek Admin: Załadowano {$file}");
                }
            } else {
                // Log błędu jeśli plik nie istnieje
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log("Polski Podarek Admin: BŁĄD - Nie znaleziono pliku {$file_path}");
                }
            }
        }
    }
    
    /**
     * Sprawdza czy wszystkie komponenty się załadowały
     */
    public static function verify_loaded_components() {
        
        $required_classes = array(
            'PolskiPodarekMenuManager',
            'PolskiPodarekSettingsPage',
            'PolskiPodarekAlcoholPage',
            'PolskiPodarekSeoPage'
        );
        
        $missing_classes = array();
        
        foreach ($required_classes as $class) {
            if (!class_exists($class)) {
                $missing_classes[] = $class;
            }
        }
        
        // Jeśli brakuje klas, pokaż błąd
        if (!empty($missing_classes) && current_user_can('manage_options')) {
            add_action('admin_notices', function() use ($missing_classes) {
                echo '<div class="notice notice-error">';
                echo '<p><strong>Polski Podarek:</strong> Nie udało się załadować niektórych komponentów administratora:</p>';
                echo '<ul>';
                foreach ($missing_classes as $class) {
                    echo '<li>' . $class . '</li>';
                }
                echo '</ul>';
                echo '<p>Sprawdź czy wszystkie pliki są na swoim miejscu w katalogu <code>includes/admin/</code>.</p>';
                echo '</div>';
            });
        }
    }
    
    /**
     * Pokazuje informacje debug o załadowanych komponentach
     */
    public static function show_debug_info() {
        
        // Tylko dla administratorów i tylko na stronach Polski Podarek
        if (!current_user_can('manage_options') || !isset($_GET['page']) || strpos($_GET['page'], 'polski-podarek') === false) {
            return;
        }
        
        $loaded_classes = array();
        $required_classes = array(
            'PolskiPodarekMenuManager',
            'PolskiPodarekSettingsPage', 
            'PolskiPodarekAlcoholPage',
            'PolskiPodarekSeoPage'
        );
        
        foreach ($required_classes as $class) {
            $loaded_classes[$class] = class_exists($class);
        }
        
        $registered_submenus = array();
        if (class_exists('PolskiPodarekMenuManager')) {
            $registered_submenus = PolskiPodarekMenuManager::get_registered_submenus();
        }
        
        ?>
        <div class="notice notice-info">
            <p><strong>Polski Podarek Debug Info:</strong></p>
            <ul>
                <?php foreach ($loaded_classes as $class => $loaded) : ?>
                    <li><?php echo $class; ?>: 
                        <span style="color: <?php echo $loaded ? 'green' : 'red'; ?>">
                            <?php echo $loaded ? '✓ Załadowany' : '✗ Brak'; ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
            
            <?php if (!empty($registered_submenus)) : ?>
                <p><strong>Zarejestrowane podmenu:</strong></p>
                <ul>
                    <?php foreach ($registered_submenus as $submenu) : ?>
                        <li><?php echo $submenu['slug']; ?> - <?php echo $submenu['menu_title']; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Pobiera listę wszystkich załadowanych plików
     */
    public static function get_loaded_files() {
        return self::$admin_files;
    }
    
    /**
     * Sprawdza czy dany plik został załadowany
     */
    public static function is_file_loaded($filename) {
        return in_array($filename, self::$admin_files);
    }
    
    /**
     * Dodaje nowy plik do listy ładowanych (dla przyszłych rozszerzeń)
     */
    public static function register_admin_file($filename) {
        
        if (!in_array($filename, self::$admin_files)) {
            self::$admin_files[] = $filename;
            
            // Jeśli plik jest dodawany po inicjalizacji, załaduj go od razu
            $file_path = get_template_directory() . '/' . self::ADMIN_DIR . $filename;
            if (file_exists($file_path)) {
                require_once $file_path;
            }
        }
    }
    
    /**
     * Funkcja pomocnicza do sprawdzenia status systemu administracyjnego
     */
    public static function get_system_status() {
        
        $status = array(
            'loader_active' => true,
            'indemi_core_active' => function_exists('indemi_core_init'),
            'loaded_files' => array(),
            'missing_files' => array(),
            'loaded_classes' => array(),
            'missing_classes' => array(),
            'registered_submenus' => array()
        );
        
        // Sprawdź pliki
        foreach (self::$admin_files as $file) {
            $file_path = get_template_directory() . '/' . self::ADMIN_DIR . $file;
            if (file_exists($file_path)) {
                $status['loaded_files'][] = $file;
            } else {
                $status['missing_files'][] = $file;
            }
        }
        
        // Sprawdź klasy
        $required_classes = array(
            'PolskiPodarekMenuManager',
            'PolskiPodarekSettingsPage',
            'PolskiPodarekAlcoholPage', 
            'PolskiPodarekSeoPage'
        );
        
        foreach ($required_classes as $class) {
            if (class_exists($class)) {
                $status['loaded_classes'][] = $class;
            } else {
                $status['missing_classes'][] = $class;
            }
        }
        
        // Pobierz zarejestrowane submenu
        if (class_exists('PolskiPodarekMenuManager')) {
            $status['registered_submenus'] = PolskiPodarekMenuManager::get_registered_submenus();
        }
        
        return $status;
    }
}

// Automatyczna inicjalizacja loadera
add_action('after_setup_theme', array('PolskiPodarekAdminLoader', 'init'), 5);

/**
 * Funkcje pomocnicze globalne
 */

if (!function_exists('pp_admin_is_loaded')) {
    /**
     * Sprawdza czy system administratora Polski Podarek jest załadowany
     * 
     * @return bool
     */
    function pp_admin_is_loaded() {
        return class_exists('PolskiPodarekAdminLoader') && class_exists('PolskiPodarekMenuManager');
    }
}

if (!function_exists('pp_admin_status')) {
    /**
     * Pobiera status systemu administratora
     * 
     * @return array
     */
    function pp_admin_status() {
        if (class_exists('PolskiPodarekAdminLoader')) {
            return PolskiPodarekAdminLoader::get_system_status();
        }
        return array('loader_active' => false);
    }
}

if (!function_exists('pp_register_admin_file')) {
    /**
     * Rejestruje nowy plik administratora do załadowania
     * 
     * @param string $filename Nazwa pliku
     */
    function pp_register_admin_file($filename) {
        if (class_exists('PolskiPodarekAdminLoader')) {
            PolskiPodarekAdminLoader::register_admin_file($filename);
        }
    }
}