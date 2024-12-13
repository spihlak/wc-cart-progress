function updateProgress(newSubtotal) {
    if (typeof newSubtotal === 'number') {
        cartSubtotal = newSubtotal;
    }

    $items.removeClass('visible active done');
    $doneMarkerWrapper.removeClass('visible');
    $itemsWrapper.removeClass('completed');

    if (!steps || steps.length === 0) {
        $progressBarFill.css('width', '0%');
        $contentText.text('No rewards available.');
        return;
    }

    let currentStepIndex = -1;
    const lastStepIndex = steps.length - 1;

    // Determine the current step index
    steps.forEach((step, index) => {
        if (cartSubtotal >= step.threshold) {
            currentStepIndex = index;
        }
    });

    const activeStepIndex = Math.min(currentStepIndex + 1, lastStepIndex);

    // Calculate progress dynamically
    let progress = 0;
    if (currentStepIndex === -1) {
        // Below the first step
        progress = (cartSubtotal / steps[0].threshold) * 50;
    } else if (currentStepIndex === lastStepIndex) {
        // At or beyond the last step
        const lastThreshold = steps[lastStepIndex].threshold;
        progress = (cartSubtotal / lastThreshold) * 100; // Full progress to 100%
    } else {
        // Between steps
        const currentThreshold = steps[currentStepIndex].threshold;
        const nextThreshold = steps[activeStepIndex].threshold;
        const range = nextThreshold - currentThreshold;
        const progressInRange = cartSubtotal - currentThreshold;
        const baseProgress = 50 * currentStepIndex;

        // Limit progress bar to 50% for intermediate steps
        progress = baseProgress + (progressInRange / range) * 50;
    }

    // Update item states and apply progress
    steps.forEach((step, index) => {
        const $item = $items.eq(index);

        // Add the visible class to all relevant steps
        if (index <= activeStepIndex) {
            $item.addClass('visible');
        }

        // Add classes based on progress
        if (index <= currentStepIndex) {
            $item.addClass('done');
        } else if (index === activeStepIndex) {
            $item.addClass('active');
        }
    });

    // Set progress bar width
    $progressBarFill.css('width', `${Math.min(progress, 100)}%`);

    // Update content text and state
    if (currentStepIndex === lastStepIndex) {
        $contentText.text("You've earned all rewards!");
        $itemsWrapper.addClass('completed');
        $doneMarkerWrapper.addClass('visible');
    } else {
        const nextStep = steps[activeStepIndex];
        const remaining = nextStep.threshold - cartSubtotal;
        $contentText.text(`Add €${remaining.toFixed(2)} more to get ${nextStep.label}`);
    }
}
