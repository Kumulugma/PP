<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

?>

<form name="checkout" method="post" class="row mt-5 checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Checkout', 'woocommerce' ); ?>">

  <?php if ( $checkout->get_checkout_fields() ) : ?>

  <div class="row">
      <div class="col-12 col-md-7 checkout-left">

          <div class="row">
              <div class="col-12">
                  <h5>Podaj swoje dane</h5>

                  <?php if ( is_user_logged_in() ) { } else { ?>
                      <p>Jeśli masz już konto <a href="/moje-konto">zaloguj się</a>.</p>
                  <?php } ?>

                  <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
                  
                  <div id="customer_details">
                      <?php do_action( 'woocommerce_checkout_billing' ); ?>
                      <?php do_action( 'woocommerce_checkout_shipping' ); ?>
                  </div>

                  <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

                  <div class="note-required">
                    <p>
                        <abbr class="required" title="wymagane">*</abbr>
                        Pola obowiązkowe
                    </p>
                  </div>
              </div>
          </div>
      </div>

      <div class="col-12 col-md-5">
          <h5>Podsumowanie zamówienia</h5>

          <?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
          <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
          <div id="order_review" class="woocommerce-checkout-review-order">
              <?php do_action( 'woocommerce_checkout_order_review' ); ?>
          </div>
          <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
      </div>
  </div>

  <?php endif; ?>

</form><!-- row -->

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>