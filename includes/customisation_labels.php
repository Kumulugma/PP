<?php

// Dodanie podstrony do menu WooCommerce
function dodaj_podstrone_personalizacji() {
    add_submenu_page(
        'woocommerce',                            // Rodzic - slug rodzica strony (WooCommerce)
        'Konfiguracja personalizacji produktów',   // Tytuł podstrony
        'Konfiguracja personalizacji produktów',   // Nazwa menu
        'manage_woocommerce',                      // Uprawnienia wymagane do zobaczenia strony (dla WooCommerce)
        'konfiguracja_personalizacji',             // Unikalny identyfikator podstrony
        'renderuj_podstrone_personalizacji'        // Funkcja renderująca zawartość podstrony
    );
}
add_action('admin_menu', 'dodaj_podstrone_personalizacji');

// Funkcja renderująca zawartość podstrony
function renderuj_podstrone_personalizacji() {
    // Zapisywanie ustawień po zatwierdzeniu formularza
    if (isset($_POST['zapisz_ustawienia'])) {
        $custom_wine_label_content = $_POST['custom_wine_label_content'];
        $custom_package_type_content = $_POST['custom_package_type_content'];
        $custom_package_content = $_POST['custom_package_content'];
        $custom_package_wrapper_content = $_POST['custom_package_wrapper_content'];
        $custom_carafe_content = $_POST['custom_carafe_content'];
        $custom_glass_content = $_POST['custom_glass_content'];
        $custom_giftcard_content = $_POST['custom_giftcard_content'];

        // Zapisanie wartości w tabeli opcji
        update_option('custom_wine_label_content', $custom_wine_label_content);
        update_option('custom_package_type_content', $custom_package_type_content);
        update_option('custom_package_content', $custom_package_content);
        update_option('custom_package_wrapper_content', $custom_package_wrapper_content);
        update_option('custom_carafe_content', $custom_carafe_content);
        update_option('custom_glass_content', $custom_glass_content);
        update_option('custom_giftcard_content', $custom_giftcard_content);

        echo '<div class="updated"><p>Ustawienia zostały zapisane.</p></div>';
    }

    // Pobieranie aktualnych wartości z tabeli opcji
    $custom_wine_label_content = get_option('custom_wine_label_content');
    $custom_package_type_content = get_option('custom_package_type_content');
    $custom_package_content = get_option('custom_package_content');
    $custom_package_wrapper_content = get_option('custom_package_wrapper_content');
    $custom_carafe_content = get_option('custom_carafe_content');
    $custom_glass_content = get_option('custom_glass_content');
    $custom_giftcard_content = get_option('custom_giftcard_content');

    // Wyświetlanie formularza
    echo '<div class="wrap">';
    echo '<h1>Konfiguracja personalizacji produktów</h1>';
    echo '<form method="post">';
    echo '<label for="custom_wine_label_content">Zawartość etykiety wina:</label><br>';
    wp_editor($custom_wine_label_content, 'custom_wine_label_content', array('textarea_rows' => 5));
    echo '<br>';
    echo '<label for="custom_package_type_content">Zawartość etykiety typu opakowania:</label><br>';
    wp_editor($custom_package_type_content, 'custom_package_type_content', array('textarea_rows' => 5));
    echo '<br>';
    echo '<label for="custom_package_content">Zawartość etykiety opakowania:</label><br>';
    wp_editor($custom_package_content, 'custom_package_content', array('textarea_rows' => 5));
    echo '<br>';
    echo '<label for="custom_package_wrapper_content">Zawartość etykiety obwoluty:</label><br>';
    wp_editor($custom_package_wrapper_content, 'custom_package_wrapper_content', array('textarea_rows' => 5));
    echo '<br>';
    echo '<label for="custom_carafe_content">Zawartość etykiety karafki:</label><br>';
    wp_editor($custom_carafe_content, 'custom_carafe_content', array('textarea_rows' => 5));
    echo '<br>';
    echo '<label for="custom_glass_content">Zawartość etykiety szkła:</label><br>';
    wp_editor($custom_glass_content, 'custom_glass_content', array('textarea_rows' => 5));
    echo '<br>';
    echo '<label for="custom_giftcard_content">Zawartość etykiety kartki z życzeniami:</label><br>';
    wp_editor($custom_giftcard_content, 'custom_giftcard_content', array('textarea_rows' => 5));
    echo '<br>';
    echo '<input type="submit" name="zapisz_ustawienia" value="Zapisz" class="button-primary">';
    echo '</form>';
    echo '</div>';
}
