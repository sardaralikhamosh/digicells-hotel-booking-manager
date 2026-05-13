<?php
class DGHBM_Assets {
    
    public static function init() {
        add_action('wp_enqueue_scripts', array(__CLASS__, 'frontend_assets'));
        add_action('wp_footer', array(__CLASS__, 'booking_modal'));
    }
    
    public static function frontend_assets() {
        wp_enqueue_style('dghbm-frontend', DGHBM_PLUGIN_URL . 'assets/css/frontend.css', array(), DGHBM_VERSION);
        wp_enqueue_script('dghbm-frontend', DGHBM_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), DGHBM_VERSION, true);
        wp_localize_script('dghbm-frontend', 'dghbm_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('dghbm_booking_nonce')
        ));
        wp_enqueue_style('google-fonts-poppins', 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    }
    
    public static function booking_modal() {
        ?>
        <div id="dghbm-booking-modal" class="dghbm-modal" style="display:none;">
            <div class="dghbm-modal-content">
                <div class="dghbm-modal-header">
                    <h2><?php _e('Book Now', 'digicells-hbm'); ?></h2>
                    <span class="dghbm-modal-close">&times;</span>
                </div>
                <div class="dghbm-modal-body">
                    <form id="dghbm-booking-form">
                        <input type="hidden" name="post_id" id="dghbm_post_id">
                        <input type="hidden" name="booking_type" id="dghbm_booking_type">
                        <div class="dghbm-form-row">
                            <div class="dghbm-form-group"><label>Full Name *</label><input type="text" name="name" required></div>
                            <div class="dghbm-form-group"><label>Email *</label><input type="email" name="email" required></div>
                        </div>
                        <div class="dghbm-form-row">
                            <div class="dghbm-form-group"><label>Phone *</label><input type="tel" name="phone" required></div>
                            <div class="dghbm-form-group"><label>Country</label><input type="text" name="country"></div>
                        </div>
                        <div class="dghbm-form-row">
                            <div class="dghbm-form-group"><label>City</label><input type="text" name="city"></div>
                            <div class="dghbm-form-group"><label>Check-in *</label><input type="date" name="checkin" required></div>
                        </div>
                        <div class="dghbm-form-row">
                            <div class="dghbm-form-group"><label>Check-out *</label><input type="date" name="checkout" required></div>
                            <div class="dghbm-form-group"><label>Guests *</label><input type="number" name="guests" min="1" required></div>
                        </div>
                        <div class="dghbm-form-group"><label>Rooms *</label><input type="number" name="rooms" min="1" required></div>
                        <div class="dghbm-form-group"><label>Special Requests</label><textarea name="special_requests"></textarea></div>
                        <button type="submit" class="dghbm-submit-btn"><?php _e('Submit Booking', 'digicells-hbm'); ?></button>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
}