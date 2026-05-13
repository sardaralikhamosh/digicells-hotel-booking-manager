jQuery(function($) {
    // Booking modal
    $(document).on('click', '.dghb-book-now', function() {
        $('#dghb_hotel_id').val($(this).data('id'));
        $('#dghb_price_per_night').val($(this).data('price'));
        $('#dghb-price-preview').text($(this).data('currency') + ' ' + $(this).data('price') + ' per night');
        $('#dghb-booking-modal').fadeIn(300);
    });
    $('.dghb-modal-close').click(function() { $('#dghb-booking-modal').fadeOut(300); });
    $('#dghb-booking-form').on('submit', function(e) {
        e.preventDefault();
        var data = $(this).serialize() + '&action=dghb_submit_booking&nonce=' + dghb_ajax.booking_nonce;
        $.post(dghb_ajax.ajax_url, data, function(res) {
            if (res.success) { alert(res.data.message); $('#dghb-booking-modal').fadeOut(300); $('#dghb-booking-form')[0].reset(); }
            else alert(res.data);
        }).fail(function() { alert('Error.'); });
    });
    // Search
    function performSearch(paged) {
        $.post(dghb_ajax.ajax_url, {
            action: 'dghb_search_hotels',
            title: $('#dghb_search_title').val(),
            location: $('#dghb_search_location').val(),
            paged: paged
        }, function(html) { $('#dghb_search_results').html(html); attachPagination(); });
    }
    $('#dghb_search_btn').click(function() { performSearch(1); });
    function attachPagination() {
        $('.dghb-pagination a').click(function(e) {
            e.preventDefault();
            var paged = $(this).text();
            if ($(this).hasClass('prev')) paged = parseInt($('.dghb-pagination .current').text()) - 1;
            if ($(this).hasClass('next')) paged = parseInt($('.dghb-pagination .current').text()) + 1;
            performSearch(paged);
        });
    }
    if ($('#dghb_search_results').length) performSearch(1);
    // Review submission
    $('#dghb-review-form').on('submit', function(e) {
        e.preventDefault();
        var data = $(this).serialize() + '&action=dghb_submit_review&nonce=' + dghb_ajax.review_nonce;
        $.post(dghb_ajax.ajax_url, data, function(res) {
            if (res.success) { $('.dghb-review-message').html('<p style="color:green;">Review submitted! It will appear after approval.</p>'); $('#dghb-review-form')[0].reset(); }
            else $('.dghb-review-message').html('<p style="color:red;">Error submitting review.</p>');
        });
    });
    // Admin booking status update
    $('.dghb-booking-status').on('change', function() {
        var id = $(this).data('id'), status = $(this).val();
        $.post(ajaxurl, { action: 'dghb_update_booking_status', booking_id: id, status: status, nonce: dghb_admin_ajax.nonce }, function(res) { if(res.success) location.reload(); });
    });
    // Admin review approve/delete
    $('.dghb-approve-review').on('click', function() {
        var id = $(this).data('id');
        $.post(ajaxurl, { action: 'dghb_approve_review', review_id: id, nonce: dghb_admin_ajax.nonce }, function(res) { if(res.success) location.reload(); });
    });
    $('.dghb-delete-review').on('click', function() {
        if(confirm('Delete this review?')) {
            var id = $(this).data('id');
            $.post(ajaxurl, { action: 'dghb_delete_review', review_id: id, nonce: dghb_admin_ajax.nonce }, function(res) { if(res.success) location.reload(); });
        }
    });
});