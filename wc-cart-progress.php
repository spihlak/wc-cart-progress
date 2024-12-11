<?php

/**
 * Plugin Name: Woocommerce Cart Progress Bar
 * Description: A plugin that adds a cart progress bar to Mini Cart and Cart Page
 * Version: 1.0
 * Author: Simplist Digital
 * Text Domain: wc-cart-progress
 */

 if ( ! defined( 'ABSPATH' ) ) exit;

 add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('wc-cart-progress-styles', plugins_url('assets/css/wc-cart-progress.css', __FILE__));
    wp_enqueue_script('wc-cart-progress-script', plugins_url('assets/js/wc-cart-progress.js', __FILE__), array('jquery'), null, true);
    wp_localize_script('wc-cart-progress-script', 'ajaxurl', admin_url('admin-ajax.php'));
 });

 if (is_admin()) {
    require_once plugin_dir_path(__FILE__) . 'admin/settings.php';
 }
