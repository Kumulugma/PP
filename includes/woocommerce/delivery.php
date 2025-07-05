<?php

// Add new tab
add_filter('woocommerce_product_tabs', 'woo_new_product_tab');

function woo_new_product_tab($tabs) {

    $tabs['new_tab'] = array(
        'title' => __('Dostawa', 'woocommerce'),
        'priority' => 10,
        'callback' => 'woo_new_product_tab_content'
    );

    return $tabs;
}

// The new tab content
function woo_new_product_tab_content() {

    global $post;

    // Pobieranie aktualnych wartości z tabeli opcji
    $text = "<p>" . get_option('pre_delivery_text') . "</p>";

    $text .= "<p><b>" . __('Podstawowy koszt dostawy (kurier): ', 'polski-podarek') . "</b> " . get_option('flat_rate_shipping_cost') . "zł</p>";
    $text .= "<p><b>" . __('Podstawowy koszt dostawy (paczkomat): ', 'polski-podarek') . "</b> " . get_option('flat_rate_shipping_cost_locker') . "zł</p>";

    $shipping_classes = wc_get_shipping_classes(); // Get Shipping Classes
    $rate_shipping_classes = get_option('flat_rate_shipping_classes');
    $text .= '<table>';
    $text .= '<tbody>';
    $text .= '<tr>';
    $text .= '<th scope="col"><b>Rodzaj produktów</b></th>';
    $text .= '<th scope="col">Limit w jednej paczce</th>';
    $text .= '</tr>';
    foreach ($shipping_classes as $shipping_class) {
        $text .= '<tr>';
        $text .= '<th scope="row"><i>' . $shipping_class->name . '</i></th>';
        $text .= '<td>';
        $text .= ($rate_shipping_classes[$shipping_class->term_id] != "") ? $rate_shipping_classes[$shipping_class->term_id] : 1;
        $text .= ' szt.</td>';
        $text .= '</tr>';
    }
    $text .= '<tr>';
    $text .= '<th scope="col"><i>' . __('Próg mieszanej dostawy: ', 'polski-podarek') . '</i></th>';
    $text .= '<th scope="col">' . get_option('mixed_shipping_rate') . ' szt.</th>';
    $text .= '</tr>';
    $text .= '</tbody>';
    $text .= '</table>';

    $text .= "<br><p>" . get_option('post_delivery_text') . "</p>";

    $terms = get_the_terms(get_the_ID(), 'product_shipping_class');

    $text .= "<p><b>" . __('Ten produkt należy do grupy: ');
    if ($terms && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            $text .= $term->name . " ";
        }
    }
    $text .= "</b></p>";

    echo $text;
}
