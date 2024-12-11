<?php

$cart_subtotal = WC()->cart->get_cart_subtotal();
$steps = json_decode(get_option('wc_cart_progress_steps', '[]'), true);

?>

<div id="wc-cart-progress">
    <div class="progress-bar-container">
        <div class="progress-bar">
            <?php foreach ($steps as $step): ?>
                <div class="step" style="width: <?php echo 100 / count($steps); ?>%;">
                    <img src="<?php echo esc_url($step['image']); ?>" alt="<?php echo esc_attr($step['title']); ?>" />
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="progress-details">
        <?php foreach ($steps as $step): ?>
            <p>
                Spend <strong>€<?php echo $step['threshold']; ?></strong> more for 
                <strong><?php echo esc_html($step['title']); ?></strong>.
            </p>
        <?php endforeach; ?>
    </div>
</div>