<?php
/**
 * Plugin Name: Digicells Hotel Booking Manager
 * Version: 1.4.0
 * Author: Sardar Ali Khamosh (digicells)
 * Text Domain: dghb
 */

if (!defined('ABSPATH')) exit;

define('DGHB_VERSION', '1.4.0');
define('DGHB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DGHB_PLUGIN_URL', plugin_dir_url(__FILE__));

// Activation: create tables
register_activation_hook(__FILE__, 'dghb_activate');
function dghb_activate() {
    global $wpdb;
    $charset = $wpdb->get_charset_collate();
    $bookings_table = $wpdb->prefix . 'dghb_bookings';
    $reviews_table = $wpdb->prefix . 'dghb_reviews';
    $sql = "CREATE TABLE IF NOT EXISTS $bookings_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        hotel_id mediumint(9) NOT NULL,
        customer_name varchar(255) NOT NULL,
        customer_email varchar(255) NOT NULL,
        customer_phone varchar(50) NOT NULL,
        customer_country varchar(100),
        customer_city varchar(100),
        checkin_date date NOT NULL,
        checkout_date date NOT NULL,
        number_of_guests int NOT NULL,
        number_of_rooms int DEFAULT 1,
        special_requests text,
        total_price decimal(10,2),
        status varchar(50) DEFAULT 'pending',
        booking_date datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset;
    CREATE TABLE IF NOT EXISTS $reviews_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        hotel_id mediumint(9) NOT NULL,
        reviewer_name varchar(255) NOT NULL,
        reviewer_email varchar(255),
        rating int NOT NULL,
        review_text text,
        status varchar(20) DEFAULT 'approved',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    flush_rewrite_rules();
    add_option('dghb_enable_comments', 'yes');
}

register_deactivation_hook(__FILE__, 'dghb_deactivate');
function dghb_deactivate() { flush_rewrite_rules(); }

// Register Post Type
add_action('init', 'dghb_register_post_type');
function dghb_register_post_type() {
    register_post_type('dghb_hotel', [
        'labels' => [
            'name'               => __('Hotels', 'dghb'),
            'singular_name'      => __('Hotel', 'dghb'),
            'add_new'            => __('Add New Hotel', 'dghb'),
            'add_new_item'       => __('Add New Hotel', 'dghb'),
            'edit_item'          => __('Edit Hotel', 'dghb'),
            'new_item'           => __('New Hotel', 'dghb'),
            'view_item'          => __('View Hotel', 'dghb'),
            'search_items'       => __('Search Hotels', 'dghb'),
            'not_found'          => __('No hotels found', 'dghb'),
        ],
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'hotel'],
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
        'menu_icon' => 'dashicons-building',
        'show_in_rest' => true,
    ]);
    register_taxonomy('dghb_hotel_location', 'dghb_hotel', [
        'labels' => ['name' => __('Hotel Locations', 'dghb')],
        'hierarchical' => true,
        'show_admin_column' => true,
        'rewrite' => true,
    ]);
    register_taxonomy('dghb_hotel_category', 'dghb_hotel', [
        'labels' => ['name' => __('Hotel Categories', 'dghb')],
        'hierarchical' => true,
        'show_admin_column' => true,
        'rewrite' => true,
    ]);
    register_taxonomy('dghb_hotel_amenity', 'dghb_hotel', [
        'labels' => ['name' => __('Hotel Amenities', 'dghb')],
        'hierarchical' => false,
        'show_admin_column' => true,
    ]);
}

// Meta Boxes (including comment control)
add_action('add_meta_boxes', 'dghb_add_meta_boxes');
function dghb_add_meta_boxes() {
    add_meta_box('dghb_hotel_details', __('Hotel Details', 'dghb'), 'dghb_render_meta_box', 'dghb_hotel', 'normal', 'high');
    add_meta_box('dghb_hotel_gallery', __('Hotel Gallery', 'dghb'), 'dghb_render_gallery_meta', 'dghb_hotel', 'normal', 'high');
    add_meta_box('dghb_comment_control', __('Comment Settings', 'dghb'), 'dghb_render_comment_meta', 'dghb_hotel', 'side', 'low');
}

function dghb_render_meta_box($post) {
    wp_nonce_field('dghb_save_meta', 'dghb_meta_nonce');
    $fields = [
        'hotel_type' => 'Hotel Type (Luxury, Budget, Business, Resort)',
        'price_per_night' => 'Price per night',
        'discount_price' => 'Discount price',
        'currency' => 'Currency (e.g., PKR, USD)',
        'guest_capacity' => 'Guest capacity',
        'total_rooms' => 'Total rooms',
        'checkin_time' => 'Check-in time',
        'checkout_time' => 'Check-out time',
        'owner_name' => 'Owner name',
        'owner_email' => 'Owner email',
        'owner_phone' => 'Owner phone',
        'country' => 'Country',
        'city' => 'City',
        'address' => 'Full address',
        'map_embed' => 'Google Map embed link (iframe or URL)',
        'status' => 'Availability status',
    ];
    echo '<table class="form-table">';
    foreach ($fields as $key => $label) {
        $val = get_post_meta($post->ID, '_dghb_' . $key, true);
        echo '<tr><th><label>' . esc_html($label) . '</label></th><td>';
        if ($key == 'status') {
            echo '<select name="dghb_status">';
            $opts = ['available' => 'Available', 'fully_booked' => 'Fully Booked', 'temporarily_closed' => 'Temporarily Closed'];
            foreach ($opts as $k => $v) echo '<option value="' . $k . '" ' . selected($val, $k, false) . '>' . $v . '</option>';
            echo '</select>';
        } elseif ($key == 'hotel_type') {
            echo '<select name="dghb_hotel_type">';
            $types = ['Luxury', 'Business', 'Budget', 'Resort', 'Boutique'];
            foreach ($types as $t) echo '<option value="' . $t . '" ' . selected($val, $t, false) . '>' . $t . '</option>';
            echo '</select>';
        } else {
            echo '<input type="text" name="dghb_' . $key . '" value="' . esc_attr($val) . '" class="widefat">';
        }
        echo '</td></tr>';
    }
    $amenities_list = ['Free Wifi', 'Parking', 'Air Conditioning', 'Restaurant', 'Swimming Pool', 'Gym', 'Laundry', 'Breakfast Included', 'Room Service', 'Spa'];
    $saved_amenities = get_post_meta($post->ID, '_dghb_amenities', true);
    if (!is_array($saved_amenities)) $saved_amenities = [];
    echo '<tr><th>Amenities</th><td>';
    foreach ($amenities_list as $a) {
        $checked = in_array($a, $saved_amenities) ? 'checked' : '';
        echo '<label style="display:inline-block; width:150px;"><input type="checkbox" name="dghb_amenities[]" value="' . esc_attr($a) . '" ' . $checked . '> ' . $a . '</label>';
    }
    echo '</td></tr></table>';
}

function dghb_render_gallery_meta($post) {
    wp_nonce_field('dghb_save_gallery', 'dghb_gallery_nonce');
    $gallery = get_post_meta($post->ID, '_dghb_gallery', true);
    $gallery_ids = $gallery ? explode(',', $gallery) : [];
    ?>
    <div class="dghb-gallery-container">
        <button type="button" class="button dghb-upload-gallery">Add Gallery Images</button>
        <div class="dghb-gallery-preview" style="display:flex; flex-wrap:wrap; gap:10px; margin-top:15px;">
            <?php foreach ($gallery_ids as $id) : if ($id) : ?>
                <div class="dghb-gallery-item" data-id="<?php echo $id; ?>">
                    <?php echo wp_get_attachment_image($id, 'thumbnail'); ?>
                    <button type="button" class="button dghb-remove-gallery">Remove</button>
                </div>
            <?php endif; endforeach; ?>
        </div>
        <input type="hidden" name="dghb_gallery" id="dghb_gallery" value="<?php echo esc_attr($gallery); ?>">
    </div>
    <?php
}

function dghb_render_comment_meta($post) {
    wp_nonce_field('dghb_save_comment_meta', 'dghb_comment_nonce');
    $enable_comments = get_post_meta($post->ID, '_dghb_enable_comments', true);
    if ($enable_comments === '') $enable_comments = 'default';
    ?>
    <p>
        <label><input type="radio" name="dghb_enable_comments" value="default" <?php checked($enable_comments, 'default'); ?>> Use global setting</label><br>
        <label><input type="radio" name="dghb_enable_comments" value="yes" <?php checked($enable_comments, 'yes'); ?>> Enable comments for this hotel</label><br>
        <label><input type="radio" name="dghb_enable_comments" value="no" <?php checked($enable_comments, 'no'); ?>> Disable comments for this hotel</label>
    </p>
    <?php
}

add_action('save_post', 'dghb_save_meta');
function dghb_save_meta($post_id) {
    if (isset($_POST['dghb_meta_nonce']) && wp_verify_nonce($_POST['dghb_meta_nonce'], 'dghb_save_meta')) {
        if (!defined('DOING_AUTOSAVE') && current_user_can('edit_post', $post_id)) {
            $keys = ['hotel_type', 'price_per_night', 'discount_price', 'currency', 'guest_capacity', 'total_rooms', 'checkin_time', 'checkout_time', 'owner_name', 'owner_email', 'owner_phone', 'country', 'city', 'address', 'map_embed', 'status'];
            foreach ($keys as $k) {
                if (isset($_POST['dghb_' . $k])) update_post_meta($post_id, '_dghb_' . $k, sanitize_text_field($_POST['dghb_' . $k]));
            }
            $amenities = isset($_POST['dghb_amenities']) ? array_map('sanitize_text_field', $_POST['dghb_amenities']) : [];
            update_post_meta($post_id, '_dghb_amenities', $amenities);
        }
    }
    if (isset($_POST['dghb_gallery_nonce']) && wp_verify_nonce($_POST['dghb_gallery_nonce'], 'dghb_save_gallery')) {
        if (isset($_POST['dghb_gallery'])) update_post_meta($post_id, '_dghb_gallery', sanitize_text_field($_POST['dghb_gallery']));
    }
    if (isset($_POST['dghb_comment_nonce']) && wp_verify_nonce($_POST['dghb_comment_nonce'], 'dghb_save_comment_meta')) {
        if (isset($_POST['dghb_enable_comments'])) update_post_meta($post_id, '_dghb_enable_comments', sanitize_text_field($_POST['dghb_enable_comments']));
    }
}

// Single Hotel Template
add_filter('single_template', 'dghb_single_template');
function dghb_single_template($template) {
    global $post;
    if ($post->post_type == 'dghb_hotel') {
        $custom = DGHB_PLUGIN_DIR . 'single-hotel.php';
        if (file_exists($custom)) return $custom;
    }
    return $template;
}

// Helper functions
function dghb_get_all_cities() {
    global $wpdb;
    return $wpdb->get_col("SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_dghb_city' AND meta_value != ''");
}
function dghb_get_all_hotel_types() {
    global $wpdb;
    $types = $wpdb->get_col("SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_dghb_hotel_type' AND meta_value != ''");
    if (empty($types)) $types = ['Luxury', 'Business', 'Budget', 'Resort', 'Boutique'];
    return $types;
}

// Admin Menu
add_action('admin_menu', 'dghb_admin_menu');
function dghb_admin_menu() {
    add_menu_page('Hotel Booking', 'Hotel Booking', 'manage_options', 'dghb_dashboard', 'dghb_dashboard_page', 'dashicons-building', 25);
    add_submenu_page('dghb_dashboard', 'All Hotels', 'All Hotels', 'manage_options', 'edit.php?post_type=dghb_hotel');
    add_submenu_page('dghb_dashboard', 'Add New', 'Add New', 'manage_options', 'post-new.php?post_type=dghb_hotel');
    add_submenu_page('dghb_dashboard', 'Bookings', 'Bookings', 'manage_options', 'dghb_bookings', 'dghb_bookings_page');
    add_submenu_page('dghb_dashboard', 'Settings', 'Settings', 'manage_options', 'dghb_settings', 'dghb_settings_page');
    add_submenu_page('dghb_dashboard', 'Reviews', 'Reviews', 'manage_options', 'dghb_reviews', 'dghb_reviews_admin_page');
}
function dghb_dashboard_page() { echo '<div class="wrap"><h1>Hotel Booking Dashboard</h1><p>Welcome to Digicells Hotel Booking Manager</p></div>'; }
function dghb_settings_page() {
    if (isset($_POST['dghb_save_settings'])) {
        update_option('dghb_enable_comments', sanitize_text_field($_POST['enable_comments']));
        echo '<div class="notice notice-success"><p>Settings saved.</p></div>';
    }
    $enable_comments = get_option('dghb_enable_comments', 'yes');
    ?>
    <div class="wrap">
        <h1>Hotel Booking Settings</h1>
        <form method="post">
            <table class="form-table">
                <tr><th>Global comment setting</th><td>
                    <label><input type="radio" name="enable_comments" value="yes" <?php checked($enable_comments, 'yes'); ?>> Enable comments by default</label><br>
                    <label><input type="radio" name="enable_comments" value="no" <?php checked($enable_comments, 'no'); ?>> Disable comments by default</label>
                </td></tr>
            </table>
            <input type="submit" name="dghb_save_settings" class="button-primary" value="Save Settings">
        </form>
    </div>
    <?php
}

// Bookings Page
function dghb_bookings_page() {
    global $wpdb;
    $bookings = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}dghb_bookings ORDER BY id DESC");
    ?>
    <div class="wrap"><h1>Hotel Bookings</h1>
        <table class="wp-list-table widefat fixed striped">
            <thead><tr><th>ID</th><th>Hotel</th><th>Customer</th><th>Check-in</th><th>Check-out</th><th>Total</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($bookings as $b): $hotel = get_the_title($b->hotel_id); ?>
                <tr>
                    <td><?php echo $b->id; ?></td>
                    <td><?php echo esc_html($hotel); ?></td>
                    <td><?php echo esc_html($b->customer_name); ?><br><small><?php echo esc_html($b->customer_email); ?></small></td>
                    <td><?php echo $b->checkin_date; ?></td>
                    <td><?php echo $b->checkout_date; ?></td>
                    <td><?php echo esc_html($b->total_price); ?></td>
                    <td><select class="dghb-booking-status" data-id="<?php echo $b->id; ?>">
                        <option value="pending" <?php selected($b->status,'pending'); ?>>Pending</option>
                        <option value="confirmed" <?php selected($b->status,'confirmed'); ?>>Confirmed</option>
                        <option value="rejected" <?php selected($b->status,'rejected'); ?>>Rejected</option>
                        <option value="completed" <?php selected($b->status,'completed'); ?>>Completed</option>
                    </select></td>
                    <td><button class="button" onclick="alert('Special requests: <?php echo esc_js($b->special_requests); ?>')">View</button></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}
add_action('wp_ajax_dghb_update_booking_status', 'dghb_update_status');
function dghb_update_status() {
    check_ajax_referer('dghb_admin_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_die();
    global $wpdb;
    $wpdb->update($wpdb->prefix . 'dghb_bookings', ['status' => sanitize_text_field($_POST['status'])], ['id' => intval($_POST['booking_id'])]);
    wp_send_json_success();
}

// Reviews Admin Page
function dghb_reviews_admin_page() {
    global $wpdb;
    $reviews = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}dghb_reviews ORDER BY id DESC");
    ?>
    <div class="wrap"><h1>Hotel Reviews</h1>
        <table class="wp-list-table widefat fixed striped">
            <thead><tr><th>ID</th><th>Hotel</th><th>Reviewer</th><th>Rating</th><th>Review</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($reviews as $r): $hotel = get_the_title($r->hotel_id); ?>
                <tr>
                    <td><?php echo $r->id; ?></td>
                    <td><?php echo esc_html($hotel); ?></td>
                    <td><?php echo esc_html($r->reviewer_name); ?></td>
                    <td><?php echo str_repeat('★', $r->rating) . str_repeat('☆', 5-$r->rating); ?></td>
                    <td><?php echo esc_html($r->review_text); ?></td>
                    <td><?php echo ucfirst($r->status); ?></td>
                    <td>
                        <button class="button dghb-approve-review" data-id="<?php echo $r->id; ?>">Approve</button>
                        <button class="button dghb-delete-review" data-id="<?php echo $r->id; ?>">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}
add_action('wp_ajax_dghb_approve_review', 'dghb_approve_review');
function dghb_approve_review() {
    check_ajax_referer('dghb_admin_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_die();
    global $wpdb;
    $wpdb->update($wpdb->prefix . 'dghb_reviews', ['status' => 'approved'], ['id' => intval($_POST['review_id'])]);
    wp_send_json_success();
}
add_action('wp_ajax_dghb_delete_review', 'dghb_delete_review');
function dghb_delete_review() {
    check_ajax_referer('dghb_admin_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_die();
    global $wpdb;
    $wpdb->delete($wpdb->prefix . 'dghb_reviews', ['id' => intval($_POST['review_id'])]);
    wp_send_json_success();
}

// AJAX Submit Review
add_action('wp_ajax_dghb_submit_review', 'dghb_submit_review');
add_action('wp_ajax_nopriv_dghb_submit_review', 'dghb_submit_review');
function dghb_submit_review() {
    check_ajax_referer('dghb_review_nonce', 'nonce');
    global $wpdb;
    $data = [
        'hotel_id' => intval($_POST['hotel_id']),
        'reviewer_name' => sanitize_text_field($_POST['name']),
        'reviewer_email' => sanitize_email($_POST['email']),
        'rating' => intval($_POST['rating']),
        'review_text' => sanitize_textarea_field($_POST['review_text']),
        'status' => 'approved'
    ];
    $inserted = $wpdb->insert($wpdb->prefix . 'dghb_reviews', $data);
    if ($inserted) wp_send_json_success(['message' => 'Review submitted!']);
    else wp_send_json_error('Error submitting review.');
}

// SHORTCODES
add_shortcode('dghb_hotel_listing', 'dghb_hotel_listing_shortcode');
function dghb_hotel_listing_shortcode($atts) {
    $atts = shortcode_atts(['per_page' => 8], $atts);
    $paged = get_query_var('paged') ? get_query_var('paged') : 1;
    $query = new WP_Query([
        'post_type' => 'dghb_hotel',
        'posts_per_page' => intval($atts['per_page']),
        'paged' => $paged,
        'orderby' => 'title',
        'order' => 'ASC',
        'post_status' => 'publish'
    ]);
    ob_start();
    if ($query->have_posts()) {
        echo '<div class="dghb-cards-grid">';
        while ($query->have_posts()) { $query->the_post(); dghb_render_card(get_post()); }
        echo '</div><div class="dghb-pagination">';
        echo paginate_links(['total' => $query->max_num_pages, 'current' => $paged]);
        echo '</div>';
    } else echo '<p>No hotels found.</p>';
    wp_reset_postdata();
    return ob_get_clean();
}

function dghb_render_card($post) {
    $price = get_post_meta($post->ID, '_dghb_price_per_night', true);
    $currency = get_post_meta($post->ID, '_dghb_currency', true);
    $status = get_post_meta($post->ID, '_dghb_status', true);
    $city = get_post_meta($post->ID, '_dghb_city', true);
    $transmission = get_post_meta($post->ID, '_dghb_transmission', true);
    $capacity = get_post_meta($post->ID, '_dghb_guest_capacity', true);
    $thumbnail = get_the_post_thumbnail($post->ID, 'medium');
    if (!$thumbnail) $thumbnail = '<img src="' . DGHB_PLUGIN_URL . 'assets/placeholder.jpg" alt="Hotel">';
    ?>
    <div class="dghb-card">
        <div class="dghb-card-image"><?php echo $thumbnail; ?></div>
        <div class="dghb-card-content">
            <div class="dghb-status-badge <?php echo esc_attr($status); ?>"><?php echo ucfirst(str_replace('_', ' ', $status)); ?></div>
            <h3><?php echo esc_html($post->post_title); ?></h3>
            <div class="dghb-specs">
                <span><?php echo esc_html($transmission ?: 'Automatic'); ?></span>
                <span>👥 <?php echo esc_html($capacity ?: 4); ?> seats</span>
            </div>
            <div class="dghb-location">📍 <?php echo esc_html($city); ?></div>
            <div class="dghb-price"><?php echo esc_html($currency) . ' ' . number_format($price); ?> <span>/ day</span></div>
            <div class="dghb-buttons">
                <a href="<?php echo get_permalink($post->ID); ?>" class="dghb-btn dghb-btn-outline">Details</a>
                <?php if ($status == 'available') : ?>
                <button class="dghb-btn dghb-book-now" data-id="<?php echo $post->ID; ?>" data-price="<?php echo esc_attr($price); ?>" data-currency="<?php echo esc_attr($currency); ?>">Book Now</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}

// Search Shortcode (with icons)
add_shortcode('dghb_search_form', 'dghb_search_form_shortcode');
function dghb_search_form_shortcode() {
    ob_start(); ?>
    <div class="dghb-advanced-search">
        <div class="dghb-search-field">
            <label><span class="dghb-icon">🏷️</span> Name</label>
            <input type="text" id="dghb_search_title" placeholder="Hotel name...">
        </div>
        <div class="dghb-search-field">
            <label><span class="dghb-icon">📍</span> Location</label>
            <select id="dghb_search_location">
                <option value="">All Locations</option>
                <?php foreach (dghb_get_all_cities() as $city) echo '<option value="' . esc_attr($city) . '">' . esc_html($city) . '</option>'; ?>
            </select>
        </div>
        <div class="dghb-search-field">
            <label><span class="dghb-icon">🏨</span> Type of Property</label>
            <select id="dghb_search_type">
                <option value="">All Types</option>
                <?php foreach (dghb_get_all_hotel_types() as $type) echo '<option value="' . esc_attr($type) . '">' . esc_html($type) . '</option>'; ?>
            </select>
        </div>
        <div class="dghb-search-field">
            <label>&nbsp;</label>
            <button id="dghb_search_btn">🔍 Search</button>
        </div>
    </div>
    <div id="dghb_search_results"></div>
    <?php return ob_get_clean();
}

// AJAX Search Handler
add_action('wp_ajax_dghb_search_hotels', 'dghb_search_hotels');
add_action('wp_ajax_nopriv_dghb_search_hotels', 'dghb_search_hotels');
function dghb_search_hotels() {
    $title = sanitize_text_field($_POST['title']);
    $location = sanitize_text_field($_POST['location']);
    $type = sanitize_text_field($_POST['type']);
    $paged = intval($_POST['paged']) ?: 1;
    $args = [
        'post_type' => 'dghb_hotel',
        'posts_per_page' => 8,
        'paged' => $paged,
        'post_status' => 'publish'
    ];
    if ($title) $args['s'] = $title;
    if ($location) $args['meta_query'][] = ['key' => '_dghb_city', 'value' => $location, 'compare' => 'LIKE'];
    if ($type) $args['meta_query'][] = ['key' => '_dghb_hotel_type', 'value' => $type, 'compare' => '='];
    $query = new WP_Query($args);
    ob_start();
    if ($query->have_posts()) {
        echo '<div class="dghb-cards-grid">';
        while ($query->have_posts()) { $query->the_post(); dghb_render_card(get_post()); }
        echo '</div><div class="dghb-pagination">';
        echo paginate_links(['total' => $query->max_num_pages, 'current' => $paged]);
        echo '</div>';
    } else echo '<p>No hotels found.</p>';
    wp_die(ob_get_clean());
}

// Booking Submission
add_action('wp_ajax_dghb_submit_booking', 'dghb_submit_booking');
add_action('wp_ajax_nopriv_dghb_submit_booking', 'dghb_submit_booking');
function dghb_submit_booking() {
    check_ajax_referer('dghb_booking_nonce', 'nonce');
    global $wpdb;
    $checkin = sanitize_text_field($_POST['checkin']);
    $checkout = sanitize_text_field($_POST['checkout']);
    $days = (strtotime($checkout) - strtotime($checkin)) / (60*60*24);
    if ($days <= 0) wp_send_json_error('Invalid dates');
    $price_per_night = floatval($_POST['price_per_night']);
    $total_price = $days * $price_per_night;
    $data = [
        'hotel_id' => intval($_POST['hotel_id']),
        'customer_name' => sanitize_text_field($_POST['name']),
        'customer_email' => sanitize_email($_POST['email']),
        'customer_phone' => sanitize_text_field($_POST['phone']),
        'customer_country' => sanitize_text_field($_POST['country']),
        'customer_city' => sanitize_text_field($_POST['city']),
        'checkin_date' => $checkin,
        'checkout_date' => $checkout,
        'number_of_guests' => intval($_POST['guests']),
        'number_of_rooms' => intval($_POST['rooms']),
        'special_requests' => sanitize_textarea_field($_POST['special_requests']),
        'total_price' => $total_price,
        'status' => 'pending'
    ];
    $inserted = $wpdb->insert($wpdb->prefix . 'dghb_bookings', $data);
    if ($inserted) {
        dghb_send_booking_email($wpdb->insert_id, $data, $days, $price_per_night);
        wp_send_json_success(['message' => 'Booking request submitted. You will receive a confirmation email soon.']);
    } else wp_send_json_error('Submission failed.');
}
function dghb_send_booking_email($booking_id, $data, $days, $price_per_night) {
    $hotel = get_post($data['hotel_id']);
    $owner_email = get_post_meta($data['hotel_id'], '_dghb_owner_email', true);
    $admin_email = get_option('admin_email');
    $to = array_filter([$admin_email, $owner_email]);
    $subject = sprintf('New Booking Request #%d - %s', $booking_id, $hotel->post_title);
    $currency = get_post_meta($data['hotel_id'], '_dghb_currency', true);
    $message = "<html><body style='font-family:Arial;'><h2>New Booking Request</h2><p><strong>Hotel:</strong> {$hotel->post_title}</p>
        <p><strong>Customer:</strong> {$data['customer_name']}<br><strong>Email:</strong> {$data['customer_email']}<br><strong>Phone:</strong> {$data['customer_phone']}</p>
        <p><strong>Check-in:</strong> {$data['checkin_date']}<br><strong>Check-out:</strong> {$data['checkout_date']}<br><strong>Nights:</strong> {$days}<br><strong>Guests:</strong> {$data['number_of_guests']}<br><strong>Rooms:</strong> {$data['number_of_rooms']}</p>
        <p><strong>Price per night:</strong> {$currency} ".number_format($price_per_night)."<br><strong>Total:</strong> {$currency} ".number_format($data['total_price'])."</p>
        <p><strong>Special Requests:</strong><br>".nl2br($data['special_requests'])."</p><p>Login to admin to manage this booking.</p></body></html>";
    $headers = ['Content-Type: text/html; charset=UTF-8'];
    foreach ($to as $email) wp_mail($email, $subject, $message, $headers);
}

// Enqueue Assets
add_action('wp_enqueue_scripts', 'dghb_frontend_assets');
function dghb_frontend_assets() {
    if (!is_admin()) {
        wp_enqueue_style('dghb-style', DGHB_PLUGIN_URL . 'style.css', [], DGHB_VERSION);
        wp_enqueue_script('dghb-script', DGHB_PLUGIN_URL . 'script.js', ['jquery'], DGHB_VERSION, true);
        wp_localize_script('dghb-script', 'dghb_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'booking_nonce' => wp_create_nonce('dghb_booking_nonce'),
            'review_nonce' => wp_create_nonce('dghb_review_nonce')
        ]);
    }
}
add_action('admin_enqueue_scripts', 'dghb_admin_assets');
function dghb_admin_assets($hook) {
    if ($hook == 'post.php' || $hook == 'post-new.php') {
        wp_enqueue_media();
        wp_enqueue_script('dghb-admin', DGHB_PLUGIN_URL . 'assets/js/admin.js', ['jquery'], DGHB_VERSION, true);
        wp_enqueue_style('dghb-admin-css', DGHB_PLUGIN_URL . 'assets/css/admin.css', [], DGHB_VERSION);
    }
    wp_enqueue_script('dghb-admin-ajax', DGHB_PLUGIN_URL . 'script.js', ['jquery'], DGHB_VERSION, true);
    wp_localize_script('dghb-admin-ajax', 'dghb_admin_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('dghb_admin_nonce')
    ]);
}
add_action('wp_footer', 'dghb_booking_modal');
function dghb_booking_modal() { ?>
    <div id="dghb-booking-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:999999;">
        <div style="max-width:600px; margin:50px auto; background:#fff; border-radius:12px; padding:20px; position:relative;">
            <span style="position:absolute; right:20px; top:10px; cursor:pointer; font-size:28px;" class="dghb-modal-close">&times;</span>
            <h2>Book Hotel</h2>
            <form id="dghb-booking-form">
                <input type="hidden" name="hotel_id" id="dghb_hotel_id">
                <input type="hidden" name="price_per_night" id="dghb_price_per_night">
                <div><input type="text" name="name" placeholder="Full Name *" required style="width:100%; margin-bottom:10px; padding:8px;"></div>
                <div><input type="email" name="email" placeholder="Email *" required style="width:100%; margin-bottom:10px; padding:8px;"></div>
                <div><input type="tel" name="phone" placeholder="Phone *" required style="width:100%; margin-bottom:10px; padding:8px;"></div>
                <div style="display:flex; gap:10px;"><input type="text" name="country" placeholder="Country" style="flex:1; padding:8px;"><input type="text" name="city" placeholder="City" style="flex:1; padding:8px;"></div>
                <div style="display:flex; gap:10px; margin-top:10px;"><input type="date" name="checkin" required style="flex:1; padding:8px;"><input type="date" name="checkout" required style="flex:1; padding:8px;"></div>
                <div style="display:flex; gap:10px; margin-top:10px;"><input type="number" name="guests" placeholder="Guests" required style="flex:1; padding:8px;"><input type="number" name="rooms" placeholder="Rooms" required style="flex:1; padding:8px;"></div>
                <div><textarea name="special_requests" placeholder="Special requests" style="width:100%; margin-top:10px; padding:8px;"></textarea></div>
                <div id="dghb-price-preview" style="background:#f68511; color:#fff; padding:10px; border-radius:8px; margin:10px 0; text-align:center;"></div>
                <button type="submit" style="background:#0048a5; color:#fff; border:none; padding:12px; width:100%; border-radius:30px; cursor:pointer;">Submit Booking</button>
            </form>
        </div>
    </div>
<?php }
?>