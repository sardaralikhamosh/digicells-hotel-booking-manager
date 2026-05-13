<?php
class DGHBM_AjaxHandler {
    
    public static function init() {
        add_action('wp_ajax_dghbm_submit_booking', array(__CLASS__, 'submit_booking'));
        add_action('wp_ajax_nopriv_dghbm_submit_booking', array(__CLASS__, 'submit_booking'));
        add_action('wp_ajax_dghbm_search_listings', array(__CLASS__, 'search_listings'));
        add_action('wp_ajax_nopriv_dghbm_search_listings', array(__CLASS__, 'search_listings'));
        add_action('wp_ajax_dghbm_update_booking_status', array(__CLASS__, 'update_booking_status'));
    }
    
    public static function submit_booking() {
        check_ajax_referer('dghbm_booking_nonce', 'nonce');
        
        global $wpdb;
        $table = $wpdb->prefix . 'dghbm_bookings';
        
        $data = array(
            'booking_type'      => sanitize_text_field($_POST['booking_type']),
            'post_id'           => intval($_POST['post_id']),
            'customer_name'     => sanitize_text_field($_POST['name']),
            'customer_email'    => sanitize_email($_POST['email']),
            'customer_phone'    => sanitize_text_field($_POST['phone']),
            'customer_country'  => sanitize_text_field($_POST['country']),
            'customer_city'     => sanitize_text_field($_POST['city']),
            'checkin_date'      => sanitize_text_field($_POST['checkin']),
            'checkout_date'     => sanitize_text_field($_POST['checkout']),
            'number_of_guests'  => intval($_POST['guests']),
            'number_of_rooms'   => intval($_POST['rooms']),
            'special_requests'  => sanitize_textarea_field($_POST['special_requests']),
            'status'            => 'pending'
        );
        
        $inserted = $wpdb->insert($table, $data);
        
        if ($inserted) {
            $booking_id = $wpdb->insert_id;
            
            // Send email to admin and owner
            DGHBM_EmailHandler::send_booking_notification($booking_id, $data);
            
            wp_send_json_success(array('message' => __('Your booking is submitted for confirmation. Once confirmed, you will receive an email soon.', 'digicells-hbm')));
        } else {
            wp_send_json_error(__('Submission failed. Please try again.', 'digicells-hbm'));
        }
    }
    
    public static function search_listings() {
        // Implement search logic (by location and type)
        // Return HTML for results
    }
    
    public static function update_booking_status() {
        check_ajax_referer('dghbm_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_die();
        
        global $wpdb;
        $table = $wpdb->prefix . 'dghbm_bookings';
        $wpdb->update($table, array('status' => sanitize_text_field($_POST['status'])), array('id' => intval($_POST['booking_id'])));
        wp_send_json_success();
    }
}