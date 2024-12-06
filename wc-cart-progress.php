<?php
/*
Plugin Name: WC Cart Progress
Description: A plugin that adds a cart progress bar to Mini Cart and Cart Page
Version: 1.0
Author: Simplist Digital
License: GPL-2.0+
Text Domain: wc-cart-progress
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wc_cart_progress_init() {

    load_plugin_textdomain( 'wc-cart-progress', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

}

add_action( 'plugins_loaded', 'wc_cart_progress_init' );

function wc_cart_progress_bar() {
    load_template( plugin_dir_path( __FILE__ ) . 'templates/wc-cart-progress-bar.php' );

}

function wc_cart_progress_enqueue_scripts() {
    wp_enqueue_style( 'wc-cart-progress', plugins_url( 'assets/css/wc-cart-progress.css', __FILE__ ), array(), '1.0' );
	wp_enqueue_script( 'wc-cart-progress', plugins_url( 'assets/js/wc-cart-progress.js', __FILE__ ), array( 'jquery' ), '1.0', true );
}

add_action( 'wp_enqueue_scripts', 'wc_cart_progress_enqueue_scripts' );

?>