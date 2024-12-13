function updateProgress(newSubtotal) {
    if (typeof newSubtotal === 'number') {
        cartSubtotal = newSubtotal;
    }
    
    const $items = $container.find('.wc-cart-progress-item');
    $items.removeClass('visible active done');
    $doneMarker.removeClass('visible');
    $itemsWrapper.removeClass('completed');

    if (!steps || steps.length === 0) {
        $progressBar.css('width', '0%');
        $contentText.text('No rewards available.');
        return;
    }

    let currentStepIndex = -1;
    const lastStepIndex = steps.length - 1;

    steps.forEach((step, index) => {
        if (cartSubtotal >= step.threshold) {
            currentStepIndex = index;
        }
    });

    const activeStepIndex = Math.min(currentStepIndex + 1, lastStepIndex);
    const nextStepIndex = Math.min(activeStepIndex + 1, lastStepIndex);

    steps.forEach((step, index) => {
        const $item = $items.eq(index);
        if (index <= currentStepIndex || index === activeStepIndex || index === nextStepIndex) {
            $item.addClass('visible');
        }
        if (index <= currentStepIndex) {
            $item.addClass('done');
        } else if (index === activeStepIndex) {
            $item.addClass('active');
        }
    });

    let progress = 0;
    if (currentStepIndex === lastStepIndex) {
        progress = 100;
        $contentText.text("You've earned all rewards!");
        $itemsWrapper.addClass('completed');
        $doneMarker.addClass('visible');
    } else {
        const nextStep = steps[activeStepIndex];
        const remaining = nextStep.threshold - cartSubtotal;

        if (currentStepIndex === -1) {
            progress = (cartSubtotal / nextStep.threshold) * 50;
        } else {
            const currentThreshold = steps[currentStepIndex].threshold;
            const range = nextStep.threshold - currentThreshold;
            progress = 50 * currentStepIndex + ((cartSubtotal - currentThreshold) / range) * 50;
        }

        $contentText.text(`Add €${remaining.toFixed(2)} more to get ${nextStep.label}`);
    }

    $progressBar.css('width', Math.min(progress, 100) + '%');
}
