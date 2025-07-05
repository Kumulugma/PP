<?php
/**
 * Simple product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product->is_purchasable() ) {
	return;
}

echo wc_get_stock_html( $product ); // WPCS: XSS ok.

if ( $product->is_in_stock() ) : ?>

	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>




		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<?php do_action( 'woocommerce_before_add_to_cart_quantity' );

		woocommerce_quantity_input(
			array(
				'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
				'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
				'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
			)
		);

		do_action( 'woocommerce_after_add_to_cart_quantity' ); ?>

		<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="btn btn-secondary btn-addtocart single_add_to_cart_button button alt"><span><svg width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
<g clip-path="url(#clip0_480_4984)">
<path d="M11.7624 10.4082L10.7708 9.4165L7.93744 12.2498L10.7708 15.0832L11.7624 14.0915L10.6291 12.9582H14.3124V11.5415H10.6291L11.7624 10.4082Z" fill="white"/>
<path d="M11.8333 5.16638L9.21251 1.19971C9.00001 0.84554 8.57501 0.774707 8.22085 0.987207C7.86668 1.19971 7.79585 1.62471 8.00835 1.97887L10.1333 5.09554H4.32501L6.45001 1.97887C6.59168 1.69554 6.52085 1.27054 6.16668 1.05804C5.88335 0.84554 5.45835 0.916374 5.24585 1.19971L2.62501 5.16638H0.854177H0.145844V6.58304H0.995844L1.98751 13.6664H7.22918V12.2497H3.26251L2.48334 6.58304H12.0458L11.7625 8.49554L13.1792 8.70804L13.4625 6.58304H14.3125V5.16638H11.8333Z" fill="white"/>
</g>
<defs>
<clipPath id="clip0_480_4984">
<rect width="14.1667" height="14.1667" fill="white" transform="translate(0.145752 0.916504)"/>
</clipPath>
</defs>
</svg></span><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>

	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php endif; ?>
