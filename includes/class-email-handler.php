<?php
class DGHBM_EmailHandler {
    
    public static function init() {
        // nothing special
    }
    
    public static function send_booking_notification($booking_id, $data) {
        $admin_email = get_option('admin_email');
        $owner_email = get_post_meta($data['post_id'], '_dghbm_owner_email', true);
        if (empty($owner_email)) $owner_email = $admin_email;
        
        $subject = sprintf(__('New Booking Request #%d', 'digicells-hbm'), $booking_id);
        $message = self::get_email_template($booking_id, $data);
        $headers = array('Content-Type: text/html; charset=UTF-8');
        
        wp_mail($admin_email, $subject, $message, $headers);
        wp_mail($owner_email, $subject, $message, $headers);
    }
    
    private static function get_email_template($booking_id, $data) {
        $post_title = get_the_title($data['post_id']);
        ob_start();
        ?>
        <html>
        <body style="font-family: 'Poppins', sans-serif;">
            <h2>New Booking Request</h2>
            <p><strong>Booking ID:</strong> <?php echo $booking_id; ?></p>
            <h3>Property: <?php echo $post_title; ?></h3>
            <h3>Customer Details</h3>
            <p>Name: <?php echo $data['customer_name']; ?><br>
               Email: <?php echo $data['customer_email']; ?><br>
               Phone: <?php echo $data['customer_phone']; ?><br>
               Country: <?php echo $data['customer_country']; ?><br>
               City: <?php echo $data['customer_city']; ?></p>
            <h3>Booking Information</h3>
            <p>Check-in: <?php echo $data['checkin_date']; ?><br>
               Check-out: <?php echo $data['checkout_date']; ?><br>
               Guests: <?php echo $data['number_of_guests']; ?><br>
               Rooms: <?php echo $data['number_of_rooms']; ?><br>
               Special Requests: <?php echo nl2br($data['special_requests']); ?></p>
            <p>Please log in to admin panel to manage this booking.</p>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}