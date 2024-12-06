<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WC_Cart_Progress_Bar {

    public function __construct() {
        add_action('woocommerce_before_cart_contents', array($this, 'render_cart_progress_bar'));
        add_action('woocommerce_widget_shopping_cart_before_products', array($this, 'render_cart_progress_bar'));
    }

    public function render_cart_progress_bar() {
        echo $this->get_progress_bar_html();
    }

    public function render_mini_cart_progress_bar() {
        echo $this->get_progress_bar_html();
    }

    private function get_progress_bar_html() {
        $options = get_option('wc_cart_progress_settings');
        $steps = isset($options['steps']) ? $options['steps'] : [];
        $cart_subtotal = WC()->cart->get_subtotal();
        $progress = 0;
        $completed_steps = 0;

        foreach ($steps as $index => $step) {
            if ($cart_subtotal >= $step['threshold']) {
                $completed_steps = $index + 1;
                break;
            }
        }

        $progress = ($completed_steps / count($steps)) * 100; 

        ob_start();
        ?>

        <div class="wc-cart-progress-container">
            <div class="wc-cart-progress">

                <div class="wc-cart-progress-items-wrapper">
                    <?php foreach ($steps as $index => $step): ?>
                        <div class="wc-cart-progress-item"></div>
                    <?php endforeach; ?>
                </div>

                <div class="wc-cart-progress-bar-wrapper">
                    <div class="wc-cart-progress-bar-fill-inner">
                        <div class="wc-cart-progress-bar-fill"></div>
                    </div>
                </div>

                <div class="wc-cart-progress-done-marker-wrapper">
                    <div class="wc-cart-progress-done-marker">
                        <i class="fa-solid fa-check"></i>
                    </div>
                </div>

                <div class="wc-cart-progress-content-wrapper">
                    <p class="wc-cart-progress-content-text">
                        <?php echo __('Your shopping cart is empty.', 'wc-cart-progress'); ?>
                    </p>
                </div>

            </div>
        </div>

        <?php
        return ob_get_clean();
    }
}

?>