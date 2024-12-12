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
                        <div id="wc-cart-progress-item-<?php echo $index; ?>" class="wc-cart-progress-item">
                            <div class="wc-cart-progress-item-image-wrapper">
                                <img src="<?php echo $step['image_url']; ?>" alt="<?php echo $step['label']; ?>" width="30" height="30"/>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="wc-cart-progress-bar-wrapper">
                    <div class="wc-cart-progress-bar-fill-inner">
                        <div class="wc-cart-progress-bar-fill"></div>
                    </div>
                </div>

            </div>

            <div class="wc-cart-progress-done-marker-wrapper">
                <div class="wc-cart-progress-done-marker">
                    <i class="fa-solid fa-check"></i>
                </div>
            </div>

            <div class="wc-cart-progress-content-wrapper">
                <p class="wc-cart-progress-content-text">
                </p>
            </div>
        </div>

        <script>
    jQuery(document).ready(function($) {
        var steps = <?php echo json_encode($steps); ?>;
        var cartSubtotal = <?php echo $cart_subtotal; ?>;
        var $progressBar = $('.wc-cart-progress-bar-fill');
        var $contentText = $('.wc-cart-progress-content-text');

        function updateProgress() {
            $('.wc-cart-progress-item').removeClass('visible active done');
            
            var currentStepIndex = -1;
            var activeStepIndex = 0;
            
            // Find current step
            for (var i = 0; i < steps.length; i++) {
                if (cartSubtotal >= steps[i].threshold) {
                    currentStepIndex = i;
                }
            }
            
            activeStepIndex = Math.min(currentStepIndex + 1, steps.length - 1);

            // Update steps visibility and status
            steps.forEach(function(step, index) {
                var $item = $('#wc-cart-progress-item-' + index);
                $item.addClass('visible');

                if (index <= currentStepIndex) {
                    $item.addClass('done');
                } else if (index === activeStepIndex) {
                    $item.addClass('active');
                }
            });

            // Calculate progress
            var progress;
            if (currentStepIndex === steps.length - 1) {
                // All steps completed
                progress = 100;
                $contentText.text("You've earned all rewards!");
            } else {
                var nextStep = steps[activeStepIndex];
                
                if (currentStepIndex === -1) {
                    // First threshold not reached yet
                    progress = (cartSubtotal / nextStep.threshold) * 50; // Max 50% for first threshold
                } else if (activeStepIndex === steps.length - 1) {
                    // Last threshold
                    var currentThreshold = steps[currentStepIndex].threshold;
                    var range = nextStep.threshold - currentThreshold;
                    var progressInRange = cartSubtotal - currentThreshold;
                    var baseProgress = 50 * currentStepIndex;
                    progress = baseProgress + (progressInRange / range) * 100; // Last step goes to 100%
                } else {
                    // Middle thresholds
                    var currentThreshold = steps[currentStepIndex].threshold;
                    var range = nextStep.threshold - currentThreshold;
                    var progressInRange = cartSubtotal - currentThreshold;
                    var baseProgress = 50 * currentStepIndex;
                    progress = baseProgress + (progressInRange / range) * 50; // Middle steps max 50% each
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

        <?php
        return ob_get_clean();
    }
}

?>