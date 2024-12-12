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
        <div class="wc-cart-progress-container" data-context="<?php echo esc_attr($context); ?>">
            <div class="wc-cart-progress">
                <div class="wc-cart-progress-items-wrapper">
                    <?php foreach ($steps as $index => $step): ?>
                        <div id="<?php echo esc_attr($unique_id); ?>-item-<?php echo $index; ?>" 
                             class="wc-cart-progress-item">
                            <div class="wc-cart-progress-item-image-wrapper">
                                <img src="<?php echo esc_url($step['image_url']); ?>" 
                                     alt="<?php echo esc_attr($step['label']); ?>" 
                                     title="<?php echo esc_attr($step['label']); ?>" 
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

                <div class="wc-cart-progress-done-marker-wrapper">
                    <div class="wc-cart-progress-done-marker">
                        <i class="fa-solid fa-check"></i>
                    </div>
                </div>

                <div class="wc-cart-progress-content-wrapper">
                    <p class="wc-cart-progress-content-text"></p>
                </div>
            </div>

            <script>
            jQuery(document).ready(function($) {
                var containerId = '<?php echo $unique_id; ?>';
                var $container = $('[data-context="<?php echo $context; ?>"]');
                var steps = <?php echo json_encode($steps); ?>;
                var cartSubtotal = <?php echo $cart_subtotal; ?>;
                var $progressBar = $container.find('.wc-cart-progress-bar-fill');
                var $contentText = $container.find('.wc-cart-progress-content-text');
                var $doneMarker = $container.find('.wc-cart-progress-done-marker-wrapper');
                var $itemsWrapper = $container.find('.wc-cart-progress-items-wrapper');

                function updateProgress() {
                    $container.find('.wc-cart-progress-item').removeClass('visible active done');
                    $doneMarker.removeClass('visible');
                    $itemsWrapper.removeClass('completed');
                    
                    var currentStepIndex = -1;
                    var activeStepIndex = 0;
                    var lastStepIndex = steps.length - 1;
                    
                    // Find current step
                    for (var i = 0; i < steps.length; i++) {
                        if (cartSubtotal >= steps[i].threshold) {
                            currentStepIndex = i;
                        }
                    }
                    
                    activeStepIndex = Math.min(currentStepIndex + 1, lastStepIndex);

                    // Update steps visibility and status
                    steps.forEach(function(step, index) {
                        var $item = $('#' + containerId + '-item-' + index);
                        $item.addClass('visible');

                        if (index <= currentStepIndex) {
                            $item.addClass('done');
                        } else if (index === activeStepIndex) {
                            $item.addClass('active');
                        }
                    });

                    // Calculate progress
                    var progress;
                    if (currentStepIndex === lastStepIndex) {
                        progress = 100;
                        $contentText.text("You've earned all rewards!");
                        $itemsWrapper.addClass('completed');
                        $doneMarker.addClass('visible');
                    } else {
                        var nextStep = steps[activeStepIndex];
                        
                        if (currentStepIndex === -1) {
                            progress = (cartSubtotal / nextStep.threshold) * 50;
                        } else {
                            var currentThreshold = steps[currentStepIndex].threshold;
                            var range = nextStep.threshold - currentThreshold;
                            var progressInRange = cartSubtotal - currentThreshold;
                            var baseProgress = 50 * currentStepIndex;
                            progress = baseProgress + (progressInRange / range) * 50;
                        }

                        var remaining = nextStep.threshold - cartSubtotal;
                        $contentText.text('Add €' + remaining.toFixed(2) + ' more to get ' + nextStep.label);
                    }

                    $progressBar.css('width', Math.min(progress, 100) + '%');
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
        </div>
        <?php
        return ob_get_clean();
    }
}

?>