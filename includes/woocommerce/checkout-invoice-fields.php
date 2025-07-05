<?php


add_filter('woocommerce_checkout_fields', 'addBootstrapToCheckoutFields' );
function addBootstrapToCheckoutFields($fields) {
    foreach ($fields as &$fieldset) {
        foreach ($fieldset as &$field) {
            // if you want to add the form-group class around the label and the input
            $field['class'][] = 'form-group';

            // add form-control to the actual input
            $field['input_class'][] = 'form-control';
        }
    }
    return $fields;
}

add_filter( 'woocommerce_checkout_fields', function ( $fields ) {


    $fields['billing']['billing_nip'] = [
        'label'    => __( 'NIP', 'woocommerce' ),
        'required' => false,
        'label_class' => '',
        'input_class' => array( 'form-control' ),
        'placeholder' => 'NIP',
        'clear'    => true
    ];

    $fields['billing']['billing_company_name'] = [
        'label'    => __( 'Nazwa firmy', 'woocommerce' ),
        'required' => false,
        'label_class' => '',
        'input_class' => array( 'form-control' ),
        'placeholder' => 'Nazwa firmy',
        'clear'    => true
    ];

    $fields['billing']['billing_need_invoice'] = [
        'type'     => 'checkbox',
        'label'    => __( 'Chcę otrzymać fakturę', 'woocommerce' ),
        'required' => false,
        'label_class' => '',
        'clear'    => true
    ];

    return $fields;
} );

add_action( 'woocommerce_order_details_after_customer_details', function ( $order ) {
    $invoice     = get_post_meta( $order->get_id(), '_billing_need_invoice', true );
    $nip         = get_post_meta( $order->get_id(), '_billing_nip', true );
    $companyname = get_post_meta( $order->get_id(), '_billing_company_name', true );

    echo '<div class="woocommerce-customer-details">';
    if ( $invoice ) {
        echo '<div><strong>Chcę otrzymać fakturę:</strong> Tak</div>';
    }

    if ( $nip ) {
        echo '<div><strong>NIP:</strong> ' . esc_html( $nip ) . '</div>';
    }

    if ( $companyname ) {
        echo '<div><strong>Nazwa firmy:</strong> ' . esc_html( $companyname ) . '</div>';
    }
    echo ' </div>';
} );


add_action( 'woocommerce_checkout_update_order_meta', function ( $order_id ) {
    if ( isset( $_POST['billing_need_invoice'] ) ) {
        update_post_meta( $order_id, '_billing_need_invoice', 'Tak' );
    }

    if ( ! empty( $_POST['billing_nip'] ) ) {
        update_post_meta( $order_id, '_billing_nip', sanitize_text_field( $_POST['billing_nip'] ) );
    }

    if ( ! empty( $_POST['billing_company_name'] ) ) {
        update_post_meta( $order_id, '_billing_company_name', sanitize_text_field( $_POST['billing_company_name'] ) );
    }
} );

add_action( 'woocommerce_admin_order_data_after_billing_address', function ( $order ) {
    echo '<div><strong>' . __( 'Chcę otrzymać fakturę' ) . ':</strong> ' .
        ( get_post_meta( $order->get_id(), '_billing_need_invoice', true ) ? 'Tak' : 'Nie' ) . '</div>';

    echo '<div><strong>' . ( 'NIP' ) . ':</strong> '
        . get_post_meta( $order->get_id(), '_billing_nip', true ) . '</div>';

    echo '<div><strong>' . ( 'Nazwa firmy' ) . ':</strong> ' .
        get_post_meta( $order->get_id(), '_billing_company_name', true ) . '</div>';

} );

