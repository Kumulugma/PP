<?php

/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.9.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_cart'); ?>

    <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
        <?php do_action('woocommerce_before_cart_table'); ?>

        <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents rounded" cellspacing="0">
            <thead>
            <tr>
                <th class="product-remove"><span class="screen-reader-text"><?php esc_html_e('Remove item', 'woocommerce'); ?></span></th>
                <th class="product-thumbnail"><span class="screen-reader-text"><?php esc_html_e('Thumbnail image', 'woocommerce'); ?></span></th>
                <th class="product-name"><?php esc_html_e('Product', 'woocommerce'); ?></th>
                <th class="product-price"><?php esc_html_e('Price', 'woocommerce'); ?></th>
                <th class="product-quantity"><?php esc_html_e('Quantity', 'woocommerce'); ?></th>
                <th class="product-subtotal"><?php esc_html_e('Subtotal', 'woocommerce'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php do_action('woocommerce_before_cart_contents'); ?>

            <?php
            foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
                /**
                 * Filter the product name.
                 *
                 * @since 2.1.0
                 * @param string $product_name Name of the product in the cart.
                 * @param array $cart_item The product in the cart.
                 * @param string $cart_item_key Key for the product in the cart.
                 */
                $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);

                if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                    $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                    ?>
                    <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">

                        <td class="product-remove">
                            <?php
                            echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                'woocommerce_cart_item_remove_link',
                                sprintf(
                                    '<a href="%s" class="" aria-label="%s" data-product_id="%s" data-product_sku="%s"><i class="fa-regular fa-trash-can"></i></a>',
                                    esc_url(wc_get_cart_remove_url($cart_item_key)),
                                    /* translators: %s is the product name */
                                    esc_attr(sprintf(__('Remove %s from cart', 'woocommerce'), wp_strip_all_tags($product_name))),
                                    esc_attr($product_id),
                                    esc_attr($_product->get_sku())
                                ),
                                $cart_item_key
                            );
                            ?>
                        </td>

                        <td class="product-thumbnail">
                            <?php
                            $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);

                            if ( ! $product_permalink) {
                                echo $thumbnail; // PHPCS: XSS ok.
                            } else {
                                printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail); // PHPCS: XSS ok.
                            }
                            ?>
                        </td>

                        <td class="product-name" data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>">
                            <?php
                            if ( ! $product_permalink) {
                                echo wp_kses_post($product_name . '&nbsp;');
                            } else {
                                /**
                                 * This filter is documented above.
                                 *
                                 * @since 2.1.0
                                 */
                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a><div class="add_info">', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
                            } ?></div>


                            <?php

                            do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);

                            // Meta data.
                            echo wc_get_formatted_cart_item_data($cart_item); // PHPCS: XSS ok.

                            // Backorder notification.
                            if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                                echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__('Available on backorder', 'woocommerce') . '</p>',
                                    $product_id));
                            }
                            ?>
                        </td>

                        <td class="product-price" data-title="<?php esc_attr_e('Price', 'woocommerce'); ?>">
                            <?php
                            echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                            ?>
                        </td>

                        <td class="product-quantity" data-title="<?php esc_attr_e('Quantity', 'woocommerce'); ?>">
                            <?php
                            if ($_product->is_sold_individually()) {
                                $min_quantity = 1;
                                $max_quantity = 1;
                            } else {
                                $min_quantity = 0;
                                $max_quantity = $_product->get_max_purchase_quantity();
                            }

                            if ($_product->is_sold_individually()) {
                                $product_quantity = sprintf('1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key);
                            } else { ?>

                                <div class="quantity_control">
                                    <button id="cart_decrease" class="btn btn-secondary">-</button>
                                    <?php
                                    $product_quantity = woocommerce_quantity_input(
                                        array(
                                            'input_name'   => "cart[{$cart_item_key}][qty]",
                                            'input_value'  => $cart_item['quantity'],
                                            'max_value'    => $max_quantity,
                                            'min_value'    => $min_quantity,
                                            'product_name' => $product_name,
                                        ),
                                        $_product,
                                        false
                                    );

                                    echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item);
                                    ?>
                                    <button id="cart_increase" class="btn btn-secondary">+</button>
                                </div>

                            <?php }

                            // PHPCS: XSS ok.
                            ?>
                        </td>

                        <td class="product-subtotal" data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>">
                            <?php
                            echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // PHPCS: XSS ok.
                            ?>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>

            <?php do_action('woocommerce_cart_contents'); ?>

            </tbody>
        </table>

        <div class="row">
    <div class="col-12 col-md-6">
        <?php if (wc_coupons_enabled()) { ?>
            <h6>Kupon rabatowy:</h6>
            <div class="coupon">
                <div class="native-coupon-form">
                    <label for="coupon_code" class="screen-reader-text"><?php esc_html_e('Kod kuponu', 'woocommerce'); ?></label>
                    <input type="text" 
                           name="coupon_code" 
                           class="form-control" 
                           id="coupon_code" 
                           value="" 
                           placeholder="<?php esc_attr_e('Wprowadź kod kuponu', 'woocommerce'); ?>" />
                    
                    <button type="submit" 
                            class="btn btn-primary" 
                            name="apply_coupon" 
                            value="<?php esc_attr_e('Zastosuj kupon', 'woocommerce'); ?>">
                        <?php esc_html_e('Zastosuj kupon', 'woocommerce'); ?>
                    </button>
                    
                    <?php do_action('woocommerce_cart_coupon'); ?>
                </div>
            </div>
        <?php } ?>

        <button type="submit" 
                class="btn btn-primary" 
                name="update_cart"
                value="<?php esc_attr_e('Aktualizuj koszyk', 'woocommerce'); ?>">
            <?php esc_html_e('Aktualizuj koszyk', 'woocommerce'); ?>
        </button>

        <?php do_action('woocommerce_cart_actions'); ?>
        <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
    </div>

    <div class="col-12 col-md-6" id="cart_subtotal_small">
        <h5><?php esc_html_e('Łączna wartość', 'woocommerce'); ?>: <?php wc_cart_totals_subtotal_html(); ?></h5>
        
        <?php 
        // Pokaż zastosowane kupony
        $applied_coupons = WC()->cart->get_applied_coupons();
        if (!empty($applied_coupons)) {
            echo '<div class="applied-coupons-summary">';
            echo '<h6>Zastosowane kupony:</h6>';
            echo '<ul class="coupon-summary-list">';
            
            foreach ($applied_coupons as $coupon_code) {
                $coupon = new WC_Coupon($coupon_code);
                $discount = WC()->cart->get_coupon_discount_amount($coupon_code);
                
                echo '<li class="coupon-summary-item">';
                echo '<span class="coupon-code">' . esc_html($coupon_code) . '</span>';
                echo '<span class="coupon-discount">-' . wc_price($discount) . '</span>';
                //echo '<a href="' . esc_url(wc_get_cart_remove_coupon_url($coupon_code)) . '" class="remove-coupon">×</a>';
                echo '</li>';
            }
            
            echo '</ul>';
            echo '</div>';
        }
        ?>

        <?php do_action('woocommerce_proceed_to_checkout'); ?>
    </div>
</div>

<style>
/* Style dla natywnego formularza w koszyku */
.native-coupon-form {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

.native-coupon-form .form-control {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.native-coupon-form .btn {
    white-space: nowrap;
    padding: 8px 16px;
}

.applied-coupons-summary {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 15px;
    border: 1px solid #dee2e6;
}

.applied-coupons-summary h6 {
    margin: 0 0 10px 0;
    color: #495057;
    font-size: 14px;
}

.coupon-summary-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.coupon-summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 5px 8px;
    background: white;
    border-radius: 4px;
    margin-bottom: 5px;
    font-size: 13px;
}

.coupon-summary-item:last-child {
    margin-bottom: 0;
}

.coupon-code {
    font-family: monospace;
    font-weight: bold;
    color: #571B33;
}

.coupon-discount {
    color: #28a745;
    font-weight: bold;
}

.remove-coupon {
    color: #dc3545;
    text-decoration: none;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 3px;
    transition: background 0.3s;
}

.remove-coupon:hover {
    background: #dc3545;
    color: white;
}

@media (max-width: 768px) {
    .native-coupon-form {
        flex-direction: column;
    }
    
    .native-coupon-form .btn {
        width: 100%;
    }
}
</style>

        <?php do_action('woocommerce_after_cart_contents'); ?>

        <?php do_action('woocommerce_after_cart_table'); ?>
    </form>

<?php do_action('woocommerce_before_cart_collaterals'); ?>

    <div class="cart-collaterals">
        <div class="row">

            <div class="col-md-4 col-lg-6">
            </div>



        </div>
    </div>

<?php do_action('woocommerce_after_cart'); ?>