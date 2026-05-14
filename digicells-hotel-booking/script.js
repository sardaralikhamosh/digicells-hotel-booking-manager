jQuery(function($) {
    // Open booking modal
    $(document).on('click', '.dghb-book-now', function() {
        var id = $(this).data('id');
        var price = $(this).data('price');
        var currency = $(this).data('currency');
        $('#dghb_hotel_id').val(id);
        $('#dghb_price_per_night').val(price);
        $('#dghb-price-preview').text(currency + ' ' + price + ' per night');
        $('#dghb-booking-modal').fadeIn(300);
    });
    
    // Close modal
    $('.dghb-modal-close').click(function() {
        $('#dghb-booking-modal, #dghb-booking-details-modal').fadeOut(300);
    });
    
    // Submit booking
    $('#dghb-booking-form').on('submit', function(e) {
        e.preventDefault();
        var data = $(this).serialize() + '&action=dghb_submit_booking&nonce=' + dghb_ajax.booking_nonce;
        $.post(dghb_ajax.ajax_url, data, function(res) {
            if (res.success) {
                alert(res.data.message);
                $('#dghb-booking-modal').fadeOut(300);
                $('#dghb-booking-form')[0].reset();
            } else {
                alert(res.data);
            }
        }).fail(function() { alert('Error submitting booking.'); });
    });
    
    // Search hotels (AJAX) with initial load
    function performSearch(paged) {
        var title = $('#dghb_search_title').val();
        var location = $('#dghb_search_location').val();
        var type = $('#dghb_search_type').val();
        $.post(dghb_ajax.ajax_url, {
            action: 'dghb_search_hotels',
            title: title,
            location: location,
            type: type,
            paged: paged
        }, function(html) {
            $('#dghb_search_results').html(html);
            attachPaginationEvents();
        });
    }
    
    // Search button click
    $('#dghb_search_btn').click(function() {
        performSearch(1);
    });
    
    // Pagination links (delegated)
    function attachPaginationEvents() {
        $('.dghb-pagination a').off('click').on('click', function(e) {
            e.preventDefault();
            var paged = $(this).text();
            if ($(this).hasClass('prev')) {
                paged = parseInt($('.dghb-pagination .current').text()) - 1;
            } else if ($(this).hasClass('next')) {
                paged = parseInt($('.dghb-pagination .current').text()) + 1;
            }
            performSearch(paged);
        });
    }
    
    // Load initial results if search container exists
    if ($('#dghb_search_results').length) {
        performSearch(1);
    }
    
    // Submit review
    $('#dghb-review-form').on('submit', function(e) {
        e.preventDefault();
        var data = $(this).serialize() + '&action=dghb_submit_review&nonce=' + dghb_ajax.review_nonce;
        $.post(dghb_ajax.ajax_url, data, function(res) {
            if (res.success) {
                $('.dghb-review-message').html('<p style="color:green;">' + res.data.message + '</p>');
                $('#dghb-review-form')[0].reset();
                // Reset stars
                $('.dghb-rating-stars span').removeClass('active');
            } else {
                $('.dghb-review-message').html('<p style="color:red;">' + res.data + '</p>');
            }
        });
    });
    
    // Admin: update booking status
    $(document).on('change', '.dghb-booking-status', function() {
        var id = $(this).data('id');
        var status = $(this).val();
        $.post(ajaxurl, {
            action: 'dghb_update_booking_status',
            booking_id: id,
            status: status,
            nonce: dghb_admin_ajax.nonce
        }, function(res) {
            if (res.success) location.reload();
        });
    });
    
    // Admin: view booking details (modal)
    $(document).on('click', '.dghb-view-booking', function() {
        var id = $(this).data('id');
        $.post(ajaxurl, {
            action: 'dghb_get_booking_details',
            booking_id: id,
            nonce: dghb_admin_ajax.nonce
        }, function(res) {
            if (res.success) {
                $('#dghb-booking-details-content').html(res.data.html);
                $('#dghb-booking-details-modal').fadeIn(300);
            } else {
                alert('Error loading details.');
            }
        });
    });
    
    // Admin: approve review
    $(document).on('click', '.dghb-approve-review', function() {
        var id = $(this).data('id');
        $.post(ajaxurl, {
            action: 'dghb_approve_review',
            review_id: id,
            nonce: dghb_admin_ajax.nonce
        }, function(res) {
            if (res.success) location.reload();
        });
    });
    
    // Admin: delete review
    $(document).on('click', '.dghb-delete-review', function() {
        if (confirm('Delete this review?')) {
            var id = $(this).data('id');
            $.post(ajaxurl, {
                action: 'dghb_delete_review',
                review_id: id,
                nonce: dghb_admin_ajax.nonce
            }, function(res) {
                if (res.success) location.reload();
            });
        }
    });
});