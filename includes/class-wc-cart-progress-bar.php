<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function has_only_virtual_products() {
    $cart = WC()->cart;
    if ($cart->is_empty()) {
        return false;
    }

    foreach ($cart->get_cart() as $cart_item) {
        $product = $cart_item['data'];
        if (!$product->is_virtual()) {
            return false;
        }
    }
    
    return true;
}

wp_enqueue_style('wc-cart-progress-styles', plugins_url('assets/css/wc-cart-progress.css', dirname(__FILE__)));

class WC_Cart_Progress_Bar {

    public function __construct() {
        add_action('woocommerce_before_cart', array($this, 'render_cart_progress_bar'));
        add_action('woocommerce_before_mini_cart', array($this, 'render_mini_cart_progress_bar'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_get_cart_subtotal', array($this, 'get_cart_subtotal'));
        add_action('wp_ajax_nopriv_get_cart_subtotal', array($this, 'get_cart_subtotal'));
    }

    public function enqueue_scripts() {
        wp_enqueue_script('wc-cart-progress-script', 
            plugins_url('assets/js/wc-cart-progress.js', dirname(__FILE__)), 
            array('jquery'), 
            null, 
            true
        );

        wp_localize_script('wc-cart-progress-script', 'wc_cart_progress_params', array(
            'ajax_url' => admin_url('admin-ajax.php')
        ));
    }

    public function render_cart_progress_bar() {
        echo $this->get_progress_bar_html('cart');
    }

    public function render_mini_cart_progress_bar() {
        echo $this->get_progress_bar_html('mini-cart');
    }

    public function get_cart_subtotal() {
        wp_send_json_success(array(
            'subtotal' => WC()->cart->get_cart_contents_total()
        ));
    }

    private function get_progress_bar_html($context = 'cart') {
        // Check if cart is empty and it's mini-cart context
        if ($context === 'mini-cart' && WC()->cart->is_empty()) {
            return ''; // Return empty string to show nothing
        }

        // Check if cart has only virtual products
        if (has_only_virtual_products()) {
            return ''; // Return empty string to show nothing
        }

        $options = get_option('wc_cart_progress_settings');
        $steps = isset($options['steps']) ? $options['steps'] : [];
        $cart_subtotal = WC()->cart->get_cart_contents_total();
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
                            <?php if ($step['product_url']): ?>
                                <a href="<?php echo esc_url($step['product_url']); ?>" target="_blank"> 
                                    <img src="<?php echo esc_url($step['image_url']); ?>" 
                                         alt="<?php echo esc_attr($step['label']); ?>" 
                                         title="<?php echo esc_attr($step['label']); ?>" 
                                         width="30" height="30"/>
                                </a>
                            <?php else: ?>
                                <img src="<?php echo esc_url($step['image_url']); ?>" 
                                         alt="<?php echo esc_attr($step['label']); ?>" 
                                         title="<?php echo esc_attr($step['label']); ?>" 
                                         width="30" height="30"/>
                            <?php endif; ?>

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

            <div class="wc-cart-progress-caret-marker-wrapper">
                <div class="wc-cart-progress-caret-marker">
                </div>
            </div>

            <div class="wc-cart-progress-done-marker-wrapper">
                <div class="wc-cart-progress-done-marker">
                    <i class="fa fa-solid fa-check"></i>
                </div>
            </div>

            <div class="wc-cart-progress-content-wrapper">
                <p class="wc-cart-progress-content-text"></p>
            </div>
                
        </div>

        <script>
        jQuery(document).ready(function($) {
            function initializeCartProgress() {
                var $container = $('[data-context="<?php echo $context; ?>"]');
                var progressBar = initializeProgressBar(
                    $container,
                    '<?php echo $unique_id; ?>',
                    <?php echo json_encode($steps); ?>,
                    <?php echo $cart_subtotal; ?>
                );

                return progressBar;
            }

            // Initial initialization
            var progressBar = initializeCartProgress();
            var $container = $('[data-context="<?php echo $context; ?>"]');

            if ('<?php echo $context; ?>' === 'cart') {
                // Add transition CSS
                $container.css('transition', 'opacity 0.3s ease');
                
                // Hide container before cart update
                $(document.body).on('submit', 'form.woocommerce-cart-form', function() {
                    $container.css('opacity', '0');
                });

                // Listen for cart updates
                $(document.body).on('updated_cart_totals', function() {
                    setTimeout(function() {
                        progressBar = initializeCartProgress();
                        $container.css('opacity', '1');
                    }, 300);
                });
            } else {
                // Mini-cart handlers
                $(document.body).on('added_to_cart removed_from_cart updated_cart_totals', function() {
                    progressBar.fetch();
                });
            }

            // Common handlers
            $(document.body).on('wc_fragments_refreshed wc_fragments_loaded', function() {
                progressBar.fetch();
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }
}

?>
