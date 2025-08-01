<?php
/**
 * Checkout Payment Section
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/payment.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_before_payment' );
}
?>
<div id="payment" class="woocommerce-checkout-payment border-top pt-4 mt-1">
    <?php if ( WC()->cart->needs_payment() ) : ?>
      <h5>Wybierz metodę płatności</h5>
      <ul class="wc_payment_methods payment_methods methods border-bottom mb-4">
        <?php
        if ( ! empty( $available_gateways ) ) {
          foreach ( $available_gateways as $gateway ) {
            wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
          }
        } else {
          echo '<div class="alert alert-info">' . apply_filters( 'woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__( 'Sorry, it seems that there are no available payment methods. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) : esc_html__( 'Please fill in your details above to see available payment methods.', 'woocommerce' ) ) . '</div>'; // @codingStandardsIgnoreLine
        }
        ?>
      </ul>
    <?php endif; ?>

    <?php
	$alcohol = 0;
	foreach ( WC()->cart->get_cart() as $item_id => $item ) {
		if ( get_post_meta( $item['product_id'], 'product_alcohol', true ) == "tak" ) {
			$alcohol ++;
		}
	}
	if ( $alcohol > 0 ) : ?>
        <div class="form-row border-bottom mb-4">
            <p>
                W Twoim koszyku znaleźliśmy produkt alkoholowy.
            </p>
            <p class="form-row validate-required">
                <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                    <input type="checkbox"
                           class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox"
                           name="alcohol" id="alcohol" required>
                    <span class="woocommerce-alcohol-checkbox-text">
                        Oświadczam, że jestem osobą pełnoletnią
                        <span class="required">*</span>
                    </span>
                </label>
                <input type="hidden" name="alcohol-field" value="1">
            </p>
        </div>
	<?php endif ?>

    <div class="place-order">
      <noscript>
        <?php
        /* translators: $1 and $2 opening and closing emphasis tags respectively */
        printf( esc_html__( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate Totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce' ), '<em>', '</em>' );
        ?>
        <br/><button type="submit" class="btn btn-secondary button alt<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'Update totals', 'woocommerce' ); ?>"><?php esc_html_e( 'Update totals', 'woocommerce' ); ?></button>
      </noscript>

      <?php wc_get_template( 'checkout/terms.php' ); ?>

      <?php do_action( 'woocommerce_review_order_before_submit' ); ?>
    
      <div class="note-required pb-2">
          <p>
              <abbr class="required" title="wymagane">*</abbr>
              Pola obowiązkowe
          </p>
      </div>

      <div>
        <?php echo apply_filters( 'woocommerce_order_button_html', '<button type="submit" class="btn btn-secondary btn-full button alt' . esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ) . '" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); // @codingStandardsIgnoreLine ?>
      </div>

      <?php do_action( 'woocommerce_review_order_after_submit' ); ?>

      <?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
    </div>

</div>
<?php
if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_after_payment' );
}