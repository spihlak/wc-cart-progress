<?php

if ( ! defined( 'ABSPATH' ) ) exit;

wp_enqueue_style('wc-cart-progress-styles', plugins_url('assets/css/wc-cart-progress.css', dirname(__FILE__)));
wp_enqueue_script('wc-cart-progress-script', plugins_url('assets/js/wc-cart-progress.js', dirname(__FILE__)), array('jquery'), null, true);

class WC_Cart_Progress_Bar {

    public function __construct() {
        add_action('woocommerce_before_cart_contents', array($this, 'render_cart_progress_bar'));
        add_action('woocommerce_before_mini_cart', array($this, 'render_mini_cart_progress_bar'));
    }

    public function render_cart_progress_bar() {
        echo $this->get_progress_bar_html('cart');
    }

    public function render_mini_cart_progress_bar() {
        echo $this->get_progress_bar_html('mini-cart');
    }

    private function get_progress_bar_html($context = 'cart') {
        $options = get_option('wc_cart_progress_settings');
        $steps = isset($options['steps']) ? $options['steps'] : [];
        $cart_subtotal = WC()->cart->get_subtotal();
        $unique_id = uniqid($context . '-');

        ob_start();
        ?>
        <div class="wc-cart-progress-container" id="<?php echo esc_attr($unique_id); ?>-container">
            <div class="wc-cart-progress">
                <div class="wc-cart-progress-items-wrapper">
                    <?php foreach ($steps as $index => $step): ?>
                        <div id="<?php echo esc_attr($unique_id); ?>-item-<?php echo $index; ?>" 
                             class="wc-cart-progress-item"
                             data-threshold="<?php echo esc_attr($step['threshold']); ?>">
                            <div class="wc-cart-progress-item-image-wrapper">
                                <img src="<?php echo esc_url($step['image_url']); ?>" 
                                     alt="<?php echo esc_attr($step['label']); ?>" 
                                     width="30" height="30"/>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="wc-cart-progress-bar-wrapper">
                    <div class="wc-cart-progress-bar-fill-inner">
                        <div class="wc-cart-progress-bar-fill"></div>
                    </div>
                </div>

                <div class="wc-cart-progress-content-wrapper">
                    <p class="wc-cart-progress-content-text"></p>
                </div>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            var containerId = '<?php echo $unique_id; ?>';
            var $container = $('#' + containerId + '-container');
            var steps = <?php echo json_encode($steps); ?>;
            var cartSubtotal = <?php echo $cart_subtotal; ?>;
            var $progressBar = $container.find('.wc-cart-progress-bar-fill');
            var $contentText = $container.find('.wc-cart-progress-content-text');

            function updateProgress() {
                // Reset all items in this container only
                $container.find('.wc-cart-progress-item').removeClass('visible active done');
                
                var currentStepIndex = -1;
                var nextThreshold = 0;
                
                steps.forEach(function(step, index) {
                    var $item = $('#' + containerId + '-item-' + index);
                    $item.addClass('visible');

                    if (cartSubtotal >= step.threshold) {
                        $item.addClass('done');
                        currentStepIndex = index;
                    } else if (currentStepIndex === -1) {
                        nextThreshold = step.threshold;
                        $item.addClass('active');
                    }
                });

                // Rest of your update logic...
                // [Previous progress calculation code remains the same]
            }

            // Initial update
            updateProgress();

            // Update on cart changes
            $(document.body).on('updated_cart_totals updated_checkout', function() {
                cartSubtotal = <?php echo $cart_subtotal; ?>;
                updateProgress();
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }
}

?>