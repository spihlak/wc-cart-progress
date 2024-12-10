<?php

if ( ! defined( 'ABSPATH' ) ) exit;

wp_enqueue_style('wc-cart-progress-admin-styles', plugins_url('assets/css/wc-cart-progress-admin.css', dirname(__FILE__)));

class WC_Cart_Progress_Admin {

    public function __construct() {
        add_action('admin_footer', array($this, 'enqueue_admin_scripts'));
    }

    public function enqueue_admin_scripts() {
        ?>
        <script>
            jQuery(document).ready(function($) {
                $('#add-step').click(function () {
                    var stepCount = $('#wc-cart-progress-steps tbody tr').length;
                    var newStep = `
                        <tr>
                            <td>
                                <label>${stepCount + 1}</label>
                            </td>
                            <td><input type="number" style="width:100%;" name="wc_cart_progress_settings[steps][${stepCount}][threshold]" step="0.01" min="0" /></td>
                            <td><input type="text" style="width:100%;" name="wc_cart_progress_settings[steps][${stepCount}][label]" placeholder="Step Label" /></td>
                            <td><input type="text" style="width:100%;" name="wc_cart_progress_settings[steps][${stepCount}][image_url]" placeholder="Image URL" /></td>
                            <td align="right"><button type="button" class="remove-step button-delete"><span class="dashicons dashicons-no-alt"></span></button></td>
                        </tr>
                    `;
                    $('#wc-cart-progress-steps tbody').append(newStep);
                });

                $(document).on('click', '.remove-step', function () {
                    $(this).closest('tr').remove();
                });
            });
        </script>
        <?php
    }
}

?>