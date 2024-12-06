<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WC_Cart_Progress_Settings {

    public function __construct() {
        // Register settings and add admin menu
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    // Add settings page to the admin menu
    public function add_settings_page() {
        add_menu_page(
            'WC Cart Progress Settings',
            'WC Cart Progress',
            'manage_options',
            'wc_cart_progress_settings',
            array($this, 'render_settings_page'),
            'dashicons-cart',
            90
        );
    }



    public function register_settings() {
        register_setting('wc_cart_progress_settings_group', 'wc_cart_progress_settings', [$this, 'sanitize_settings']);
        add_settings_section('wc_cart_progress_general_section', '', '', 'wc-cart-progress-settings');
        add_settings_field('cart_steps', __('Cart Progress Steps', 'wc-cart-progress'), [$this, 'render_steps_field'], 'wc-cart-progress-settings', 'wc_cart_progress_general_section');
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('WC Cart Progress Settings', 'wc-cart-progress'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('wc_cart_progress_settings_group');
                do_settings_sections('wc-cart-progress-settings');
                ?>
                <table id="steps-wrapper" cellpadding="10" cellspacing="5" border="0">
                    <thead>
                        <tr>
                            <th align="left"><?php _e('Step', 'wc-cart-progress'); ?></th>
                            <th align="left"><?php _e('Threshold €', 'wc-cart-progress'); ?></th>
                            <th align="left"><?php _e('Label', 'wc-cart-progress'); ?></th>
                            <th align="left"></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $options = get_option('wc_cart_progress_settings');
                    $steps = isset($options['steps']) ? $options['steps'] : [];
                    if (empty($steps)) {
                        // Optionally, display a default step to kick things off
                        $steps[] = ['threshold' => 59, 'label' => 'Free shipping'];
                    }

                    foreach ($steps as $index => $step): ?>
                        <tr>
                            <td>
                                    <label><?php echo __($index + 1); ?></label>                                    
                            </td>
                            <td><input type="number" name="wc_cart_progress_settings[steps][<?php echo $index; ?>][threshold]" value="<?php echo esc_attr($step['threshold']); ?>" step="0.01" min="0" /></td>
                            <td><input type="text" name="wc_cart_progress_settings[steps][<?php echo $index; ?>][label]" value="<?php echo esc_attr($step['label']); ?>" placeholder="Step Label" /></td>
                            <td><button type="button" class="remove-step">Remove</button></td>
                        </tr>
                    <?php endforeach; ?>

                    <tr>
                        <td colspan="4"><button type="button" id="add-step">Add Step</button></td>
                    </tr>
                    </tbody>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function render_steps_field() {
        
    }

    public function sanitize_settings($input) {
        $sanitized_input = [];

        if(isset($input['steps'])) {
            $sanitized_input['steps'] = [];
            foreach ($input['steps'] as $step) {
                $sanitized_input['steps'][] = [
                    'threshold' => floatval($step['threshold']),
                    'label'     => sanitize_text_field($step['label']),
                ];
            }
        }

        return $sanitized_input;
    }
}