<?php

add_action('admin_menu', function() {
    add_submenu_page(
        'woocommerce',
        'WC Cart Progress',
        'Cart Progress',
        'manage_options',
        'wc-cart-progress',
        'wc_cart_progress_admin_page'
    );
});

function wc_cart_progress_admin_page() {
   if (isset($_POST['wc_cart_progress_steps'])) {
    $steps= json_decode(stripslashes($_POST['wc_cart_progress_steps']), true);
    update_option('wc_cart_progress_steps', json_encode($steps));
    echo '<div class="updated"><p>Settings saved</p></div>';
   }

   $steps = json_decode(get_option('wc_cart_progress_steps', '[]'), true);

   include_once plugin_dir_path(__FILE__) . 'settings-form.php';

}

add_action('admin_enqueue_scripts', function($hook) {
    if ($hook !== 'woocommerce_page_wc-cart-progress') {
        return;
    }
    wp_enqueue_script('admin-wc-cart-progress', plugins_url('assets/js/admin-wc-cart-progress.js', __FILE__), array('jquery'), null, true);
    wp_enqueue_style('admin-wc-cart-progress', plugins_url('assets/css/admin-wc-cart-progress.css', __FILE__));
});
