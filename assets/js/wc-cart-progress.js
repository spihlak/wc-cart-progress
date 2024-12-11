jQuery(document).ready(function($) {
    function updateProgressBar() {
        $.post(wcCartProgress.ajax_url, { action: 'wc_cart_progress' }, function(response) {
            if (response.steps && response.cart_total) {

                console.log(response);
            }
        });
    }

    $(document.body).on('updated_cart_totals', updateProgressBar);
});