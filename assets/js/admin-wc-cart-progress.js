document.addEventListener('DOMContentLoaded', function () {
    const stepsContainer = document.getElementById('wc-cart-progress-steps');
    const addStepButton = document.getElementById('add-step');
    const stepsInput = document.getElementById('wc_cart_progress_steps_input');

    function updateHiddenInput() {
        const steps = Array.from(stepsContainer.querySelectorAll('.wc-cart-progress-step')).map((row, index) => ({
            threshold: row.querySelector('[name^="steps["][name$="[threshold]"]').value,
            title: row.querySelector('[name^="steps["][name$="[title]"]').value,
            image: row.querySelector('[name^="steps["][name$="[image]"]').value,
        }));
        stepsInput.value = JSON.stringify(steps);
    }

    addStepButton.addEventListener('click', function () {
        const index = stepsContainer.children.length;
        const stepHtml = `
            <tr class="wc-cart-progress-step" data-index="${index}">
                <td>
                    <input type="number" name="steps[${index}][threshold]" required>
                </td>
                <td>
                    <input type="text" name="steps[${index}][title]" required>
                </td>
                <td>
                    <input type="url" name="steps[${index}][image]" required>
                </td>
                <td>
                    <button type="button" class="button remove-step">Remove</button>
                </td>
            </tr>
        `;
        stepsContainer.insertAdjacentHTML('beforeend', stepHtml);
    });

    stepsContainer.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-step')) {
            e.target.closest('.wc-cart-progress-step').remove();
        }
    });

    document.getElementById('wc-cart-progress-form').addEventListener('submit', function (e) {
        updateHiddenInput();
    });
});
