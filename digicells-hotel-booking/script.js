// Dynamic review addition
$('#dghb-review-form').on('submit', function(e) {
    e.preventDefault();
    var data = $(this).serialize() + '&action=dghb_submit_review&nonce=' + dghb_ajax.review_nonce;
    $.post(dghb_ajax.ajax_url, data, function(res) {
        if (res.success) {
            $('.dghb-review-message').html('<p style="color:green;">Review submitted! Thank you.</p>');
            // Prepend new review to list
            if ($('#dghb-reviews-list .dghb-review').length === 0) {
                $('#dghb-reviews-list').html(res.data.html);
            } else {
                $('#dghb-reviews-list').prepend(res.data.html);
            }
            // Update review count and average rating (optional)
            var count = parseInt($('.dghb-review-count').text().match(/\d+/)[0]) + 1;
            $('.dghb-review-count').text('(' + count + ')');
            // Simple average update – you can recalc via AJAX, but optional
            $('#dghb-review-form')[0].reset();
            // Reset stars
            $('.dghb-rating-stars span').removeClass('active');
        } else {
            $('.dghb-review-message').html('<p style="color:red;">Error submitting review.</p>');
        }
    });
});