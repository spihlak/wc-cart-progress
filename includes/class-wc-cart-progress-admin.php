<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WC_Cart_Progress_Admin {

    public function __construct() {
        add_action('admin_footer', array($this, 'enqueue_admin_scripts'));
    }

    public function enqueue_admin_scripts() {
        ?>
        <script>
            jQuery(document).ready(function($) {
                $('#add-step').click(function () {
                    var stepCount = $('#steps-wrapper .step-row').length;
                    var newStep = `
                        <div class="step-row">
                            <label>Step ${stepCount + 1}</label>
                            <input type="number" name="wc_cart_progress_settings[steps][${stepCount}][threshold]" step="0.01" min="0" /> €
                            <input type="text" name="wc_cart_progress_settings[steps][${stepCount}][label]" placeholder="Step Label" />
                            <button type="button" class="remove-step">Remove</button>
                        </div>
                    `;
                    $('#steps-wrapper').append(newStep);
                });

                $(document).on('click', '.remove-step', function () {
                    $(this).closest('.step-row').remove();
                });
            });
        </script>
        <?php
    }
}

?>