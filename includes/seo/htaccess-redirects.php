<?php

function update_redirects_in_htaccess_daily() {
    // Ścieżka do pliku .htaccess
    $htaccess_file = ABSPATH . '.htaccess';

    // Nowe reguły przekierowań
    $redirects = "
# BEGIN Custom Redirects
RewriteEngine On
RewriteRule ^kategoria-produktu/([a-z,A-Z,0-9\-\/]+)$ /$1 [L,R=301]
# END Custom Redirects
";

            // Sprawdź ostatnią aktualizację i treść reguł
    $last_update = get_option('htaccess_redirects_last_update', 0);
    $stored_redirects = get_option('htaccess_redirects_content', '');

    // Aktualizuj, jeśli minęło więcej niż 12 godziny lub treść reguł się zmieniła
    if (time() - $last_update > 43200 || $stored_redirects !== $redirects) {
        if (is_writable($htaccess_file)) {
            // Odczyt istniejącego pliku .htaccess
            $htaccess_content = file_get_contents($htaccess_file);

            // Usuń istniejące reguły w bloku "Custom Redirects"
            $pattern = '/# BEGIN Custom Redirects.*?# END Custom Redirects/s';
            $htaccess_content = preg_replace($pattern, '', $htaccess_content);

            // Dodaj nowe reguły na końcu pliku
            $htaccess_content .= "\n" . $redirects;

            // Zapisz zaktualizowaną treść pliku .htaccess
            file_put_contents($htaccess_file, $htaccess_content);

            // Zapisz czas ostatniej aktualizacji i nowe reguły w bazie danych
            update_option('htaccess_redirects_last_update', time());
            update_option('htaccess_redirects_content', $redirects);
        } else {
            // Loguj błąd, jeśli plik .htaccess nie jest zapisywalny
            error_log('.htaccess is not writable.');
        }
    }
}
add_action('init', 'update_redirects_in_htaccess_daily');
