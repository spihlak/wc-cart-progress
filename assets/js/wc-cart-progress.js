function initializeProgressBar($container, containerId, steps, cartSubtotal) {
    var $progressBar = $container.find('.wc-cart-progress-bar-fill');
    var $contentText = $container.find('.wc-cart-progress-content-text');
    var $doneMarker = $container.find('.wc-cart-progress-done-marker-wrapper');
    var $caretMarker = $container.find('.wc-cart-progress-caret-marker-wrapper');
    var $itemsWrapper = $container.find('.wc-cart-progress-items-wrapper');

    function updateProgress(newSubtotal) {
        if (newSubtotal !== undefined) {
            cartSubtotal = newSubtotal;
        }
        
        $container.find('.wc-cart-progress-item').removeClass('visible active done');
        $doneMarker.removeClass('visible');
        $caretMarker.removeClass('hidden');
        $itemsWrapper.removeClass('completed');
        $itemsWrapper.removeClass('single-step');
        
        var currentStepIndex = -1;
        var activeStepIndex = 0;
        var lastStepIndex = steps.length - 1;
        
        // Find current step
        for (var i = 0; i < steps.length; i++) {
            if (cartSubtotal >= steps[i].threshold) {
                currentStepIndex = i;
            }
        }
        
        activeStepIndex = Math.min(currentStepIndex + 1, lastStepIndex);
        var nextStepIndex = Math.min(activeStepIndex + 1, lastStepIndex);

        // Update steps visibility and status
        steps.forEach(function(step, index) {
            var $item = $container.find('.wc-cart-progress-item').eq(index);
            
            // Only add visible class to:
            // 1. Completed steps (done)
            // 2. Current active step
            // 3. Next step after active
            if (index <= currentStepIndex || // done items
                index === activeStepIndex || // active item
                index === nextStepIndex) {   // next item
                $item.addClass('visible');
            }

            if (index <= currentStepIndex) {
                $item.addClass('done');
            } else if (index === activeStepIndex) {
                $item.addClass('active');
            }
        });

        // Calculate progress
        var progress;
        if (currentStepIndex === lastStepIndex) {
            // Last step is completed
            progress = 100;
            $contentText.text("Palju õnne! Lisasime Sinu ostukorvi kõik kingitused!");
            $itemsWrapper.addClass('completed');
            $doneMarker.addClass('visible');
            $caretMarker.addClass('hidden');
        } else if (steps.length === 1) {
            // Single step case - fill from 0 to 100%
            progress = (cartSubtotal / steps[0].threshold) * 100;
            $itemsWrapper.addClass('single-step');
            
            if (progress >= 100) {
                // Single step is completed
                $contentText.text("Õnnitleme! Lisasime Sinu ostukorvi kõik kingitused!");
                $itemsWrapper.addClass('completed');
                $doneMarker.addClass('visible');
                $caretMarker.addClass('hidden');
            } else {
                // Single step not completed yet
                var remaining = steps[0].threshold - cartSubtotal;
                $contentText.text('Lisa veel ' + remaining.toFixed(2) + ' € eest tooteid ja kingime Sulle ' + steps[0].label);
            }
        } else if (activeStepIndex === lastStepIndex) {
            // Last step is active (multi-step case)
            var currentThreshold = steps[currentStepIndex].threshold;
            var finalThreshold = steps[lastStepIndex].threshold;
            var range = finalThreshold - currentThreshold;
            var progressInRange = cartSubtotal - currentThreshold;
            var baseProgress = 50;
            progress = baseProgress + (progressInRange / range) * 50;
            
            var remaining = steps[activeStepIndex].threshold - cartSubtotal;
            $contentText.text('Lisa veel ' + remaining.toFixed(2) + ' € eest tooteid ja kingime Sulle ' + steps[activeStepIndex].label);
        } else {
            // Any other step (multi-step case)
            if (currentStepIndex === -1) {
                // First step not completed yet
                progress = (cartSubtotal / steps[0].threshold) * 50;
            } else {
                // Between steps
                var currentThreshold = steps[currentStepIndex].threshold;
                var nextThreshold = steps[activeStepIndex].threshold;
                var range = nextThreshold - currentThreshold;
                var progressInRange = cartSubtotal - currentThreshold;
                progress = (progressInRange / range) * 50;
            }
            
            var remaining = steps[activeStepIndex].threshold - cartSubtotal;
            $contentText.text('Lisa veel ' + remaining.toFixed(2) + ' € eest tooteid ja kingime Sulle ' + steps[activeStepIndex].label);
        }
        
        $progressBar.css('width', Math.min(progress, 100) + '%');
    }

    function fetchCartSubtotal() {
        jQuery.ajax({
            url: wc_cart_progress_params.ajax_url,
            type: 'POST',
            data: {
                action: 'get_cart_subtotal'
            },
            success: function(response) {
                if (response.success) {
                    updateProgress(response.data.subtotal);
                }
            }
        });
    }

    // Initial update
    updateProgress();

    return {
        update: updateProgress,
        fetch: fetchCartSubtotal
    };
}