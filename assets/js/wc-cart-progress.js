function initializeProgressBar($container, containerId, steps, cartSubtotal) {
    var $progressBar = $container.find('.wc-cart-progress-bar-fill');
    var $contentText = $container.find('.wc-cart-progress-content-text');
    var $doneMarker = $container.find('.wc-cart-progress-done-marker-wrapper');
    var $itemsWrapper = $container.find('.wc-cart-progress-items-wrapper');

    function updateProgress(newSubtotal) {
        if (newSubtotal !== undefined) {
            cartSubtotal = newSubtotal;
        }
        
        $container.find('.wc-cart-progress-item').removeClass('visible active done');
        $doneMarker.removeClass('visible');
        $itemsWrapper.removeClass('completed');
        
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

        // Update steps visibility and status using container-scoped selectors
        steps.forEach(function(step, index) {
            var $item = $container.find('.wc-cart-progress-item').eq(index);
            $item.addClass('visible');

            if (index <= currentStepIndex) {
                $item.addClass('done');
            } else if (index === activeStepIndex) {
                $item.addClass('active');
            }
        });

        // Calculate progress
        var progress;
        if (currentStepIndex === lastStepIndex) {
            progress = 100;
            $contentText.text("You've earned all rewards!");
            $itemsWrapper.addClass('completed');
            $doneMarker.addClass('visible');
        } else {
            var nextStep = steps[activeStepIndex];
            
            if (currentStepIndex === -1) {
                progress = (cartSubtotal / nextStep.threshold) * 50;
            } else {
                var currentThreshold = steps[currentStepIndex].threshold;
                var range = nextStep.threshold - currentThreshold;
                var progressInRange = cartSubtotal - currentThreshold;
                var baseProgress = 50 * currentStepIndex;
                progress = baseProgress + (progressInRange / range) * 50;
            }

            var remaining = nextStep.threshold - cartSubtotal;
            $contentText.text('Add €' + remaining.toFixed(2) + ' more to get ' + nextStep.label);
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