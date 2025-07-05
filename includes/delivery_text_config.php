<?php

// Dodanie podstrony do menu WooCommerce
function dodaj_podstrone_administracyjna() {
    add_submenu_page(
        'woocommerce',                     // Rodzic - slug rodzica strony (WooCommerce)
        'Konfiguracja tekstu dostaw',       // Tytuł podstrony
        'Konfiguracja tekstu dostaw',       // Nazwa menu
        'manage_woocommerce',               // Uprawnienia wymagane do zobaczenia strony (dla WooCommerce)
        'konfiguracja_tekstu_dostaw',       // Unikalny identyfikator podstrony
        'renderuj_podstrone_administracyjna' // Funkcja renderująca zawartość podstrony
    );
}
add_action('admin_menu', 'dodaj_podstrone_administracyjna');

// Funkcja renderująca zawartość podstrony
function renderuj_podstrone_administracyjna() {
    // Zapisywanie ustawień po zatwierdzeniu formularza
    if (isset($_POST['zapisz_ustawienia'])) {
        $pre_delivery_text = $_POST['pre_delivery_text'];
        $post_delivery_text = $_POST['post_delivery_text'];

        // Zapisanie wartości w tabeli opcji
        update_option('pre_delivery_text', $pre_delivery_text);
        update_option('post_delivery_text', $post_delivery_text);

        echo '<div class="updated"><p>Ustawienia zostały zapisane.</p></div>';
    }

    // Pobieranie aktualnych wartości z tabeli opcji
    $pre_delivery_text = get_option('pre_delivery_text');
    $post_delivery_text = get_option('post_delivery_text');

    // Wyświetlanie formularza
    echo '<div class="wrap">';
    echo '<h1>Konfiguracja tekstu dostaw</h1>';
    echo '<form method="post">';
    echo '<label for="pre_delivery_text">Tekst przed tabelą dostawy:</label><br>';
    wp_editor($pre_delivery_text, 'pre_delivery_text', array('textarea_rows' => 5));
    echo '<br>';
    echo '<label for="post_delivery_text">Tekst po tabeli dostawy:</label><br>';
    wp_editor($post_delivery_text, 'post_delivery_text', array('textarea_rows' => 5));
    echo '<br>';
    echo '<input type="submit" name="zapisz_ustawienia" value="Zapisz" class="button-primary">';
    echo '</form>';
    echo '</div>';
}