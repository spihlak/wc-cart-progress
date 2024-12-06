<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WC_Cart_Progress_Settings {

    public function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function register_settings() {
        register_setting('wc_cart_progress_settings_group', 'wc_cart_progress_settings', [$this, 'sanitize_settings']);
        add_settings_section('wc_cart_progress_general_section', '', '', 'wc-cart-progress-settings');
        add_settings_field('cart_steps', __('Cart Progress Steps', 'wc-cart-progress'), [$this, 'render_steps_field'], 'wc-cart-progress-settings', 'wc_cart_progress_general_section');
    }

    public function render_steps_field() {
        $options = get_option('wc_cart_progress_settings');
        $steps = isset($options['steps']) ? $options['steps'] : [];

        ?>
        <div id="steps-wrapper">
            <?php foreach ($steps as $index => $step): ?>
                <div class="step-row">
                    <label><?php echo __('Step ' . ($index + 1), 'wc-cart-progress'); ?></label>
                    <input type="number" name="wc_cart_progress_settings[steps][<?php echo $index; ?>][threshold]" value="<?php echo esc_attr($step['threshold'] ?? ''); ?>" step="0.01" min="0" /> €
                    <input type="text" name="wc_cart_progress_settings[steps][<?php echo $index; ?>][label]" value="<?php echo esc_attr($step['label'] ?? ''); ?>" placeholder="Step Label" />
                    <button type="button" class="remove-step">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="add-step">Add Step</button>
        <?php
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