<?php
/**
 * Checkout coupon form - NATYWNY WooCommerce
 * Zastąp zawartość woocommerce/checkout/form-coupon.php
 */

defined('ABSPATH') || exit;

if (!wc_coupons_enabled()) {
    return;
}
?>

<div class="woocommerce-form-coupon-toggle">
    <?php wc_print_notice(apply_filters('woocommerce_checkout_coupon_message', 
        esc_html__('Masz kupon rabatowy?', 'woocommerce') . 
        ' <a href="#" class="showcoupon">' . 
        esc_html__('Kliknij tutaj, aby wprowadzić kod', 'woocommerce') . 
        '</a>'), 'notice'); ?>
</div>

<form class="checkout_coupon woocommerce-form-coupon" method="post" style="display:none">
    <div class="coupon-form-container">
        <div class="coupon-form-content">
            <p class="coupon-info"><?php esc_html_e('Jeśli masz kod kuponu, wprowadź go poniżej.', 'woocommerce'); ?></p>
            
            <div class="coupon-input-group">
                <label for="coupon_code" class="screen-reader-text"><?php esc_html_e('Kod kuponu', 'woocommerce'); ?></label>
                <input type="text" 
                       name="coupon_code" 
                       class="input-text" 
                       placeholder="<?php esc_attr_e('Kod kuponu', 'woocommerce'); ?>" 
                       id="coupon_code" 
                       value="" />
                       
                <button type="submit" 
                        class="button<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>" 
                        name="apply_coupon" 
                        value="<?php esc_attr_e('Zastosuj kupon', 'woocommerce'); ?>">
                    <?php esc_html_e('Zastosuj kupon', 'woocommerce'); ?>
                </button>
            </div>
            
            <?php do_action('woocommerce_cart_coupon'); ?>
        </div>
    </div>
</form>

<script>
// TYLKO podstawowy toggle - bez walidacji
document.addEventListener('DOMContentLoaded', function() {
    const showLink = document.querySelector('.showcoupon');
    const form = document.querySelector('.checkout_coupon');
    
    if (showLink && form) {
        showLink.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
                form.querySelector('input[name="coupon_code"]')?.focus();
            } else {
                form.style.display = 'none';
            }
        });
    }
});
</script>