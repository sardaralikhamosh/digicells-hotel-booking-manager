jQuery(document).ready(function($) {
    $('.book-now-btn').on('click', function() {
        var postId = $(this).data('id');
        var type = $(this).data('type');
        $('#dghbm_post_id').val(postId);
        $('#dghbm_booking_type').val(type);
        $('#dghbm-booking-modal').fadeIn(300);
    });
    $('.dghbm-modal-close').on('click', function() {
        $('#dghbm-booking-modal').fadeOut(300);
    });
    $('#dghbm-booking-form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize() + '&action=dghbm_submit_booking&nonce=' + dghbm_ajax.nonce;
        $.ajax({
            url: dghbm_ajax.ajax_url,
            type: 'POST',
            data: formData,
            beforeSend: function() { $('.dghbm-submit-btn').text('Submitting...').prop('disabled',true); },
            success: function(res) {
                if(res.success) {
                    alert(res.data.message);
                    $('#dghbm-booking-modal').fadeOut(300);
                    $('#dghbm-booking-form')[0].reset();
                } else alert(res.data);
            },
            complete: function() { $('.dghbm-submit-btn').text('Submit Booking').prop('disabled',false); }
        });
    });
});