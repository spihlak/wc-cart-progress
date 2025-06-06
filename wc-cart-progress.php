<?php
/*
Plugin Name: Woocommerce Cart Progress Bar
Description: A plugin that adds a cart progress bar to Mini Cart and Cart Page
Version: 1.2
Author: Simplist Digital
Text Domain: wc-cart-progress
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path(__FILE__) . 'includes/class-wc-cart-progress-settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-wc-cart-progress-bar.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-wc-cart-progress-admin.php';

function wc_cart_progress_init() {
    new WC_Cart_Progress_Settings();
    new WC_Cart_Progress_Bar();
    new WC_Cart_Progress_Admin();
}

add_action('plugins_loaded', 'wc_cart_progress_init');

?>