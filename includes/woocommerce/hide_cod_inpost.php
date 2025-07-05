<?php

add_filter('woocommerce_available_payment_gateways', 'disable_cod_for_specific_shipping_method', 10, 1);
function disable_cod_for_specific_shipping_method($available_gateways) {
    // Sprawdzamy, czy wybrana metoda dostawy to "pk_inpost_paczkomat:7"
    if (isset(WC()->session) && isset(WC()->session->chosen_shipping_methods) && in_array('pk_inpost_paczkomat:7', WC()->session->chosen_shipping_methods)) {
        // Tutaj podajemy ID płatności za pobraniem (Cash on Delivery - COD), które chcemy wyłączyć
        $payment_gateway_to_disable = 'cod';

        // Jeśli wyłączamy płatność, to ją usuwamy z dostępnych bramek płatności
        if (isset($available_gateways[$payment_gateway_to_disable])) {
            unset($available_gateways[$payment_gateway_to_disable]);
        }
    }
    return $available_gateways;
}