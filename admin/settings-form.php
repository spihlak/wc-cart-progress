<div class="wc-cart-progress-settings">
    <h1>WC Cart Progress Settings</h1>
    <form method="post" id="wc-cart-progress-form">
        <table class="widefat" id="wc-cart-progress-steps-table">
            <thead>
                <tr>
                    <th>Threshold (€)</th>
                    <th>Title</th>
                    <th>Image URL</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="wc-cart-progress-steps">
                <?php if (!empty($steps)) : ?>
                    <?php foreach ($steps as $index => $step) : ?>
                        <tr class="wc-cart-progress-step" data-index="<?php echo $index; ?>">
                            <td>
                                <input type="number" name="steps[<?php echo $index; ?>][threshold]" value="<?php echo esc_attr($step['threshold']); ?>" required>
                            </td>
                            <td>
                                <input type="text" name="steps[<?php echo $index; ?>][title]" value="<?php echo esc_attr($step['title']); ?>" required>
                            </td>
                            <td>
                                <input type="url" name="steps[<?php echo $index; ?>][image]" value="<?php echo esc_url($step['image']); ?>" required>
                            </td>
                            <td>
                                <button type="button" class="button remove-step">Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <p>
            <button type="button" class="button" id="add-step">Add Step</button>
        </p>
        <input type="hidden" name="wc_cart_progress_steps" id="wc_cart_progress_steps_input">
        <p><input type="submit" class="button-primary" value="Save Settings"></p>
    </form>
</div>
