<?php
class DGHBM_Installer {
    
    public static function activate() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Bookings table (unified for all types)
        $table_name = $wpdb->prefix . 'dghbm_bookings';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            booking_type varchar(50) NOT NULL,
            post_id mediumint(9) NOT NULL,
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
            status varchar(50) DEFAULT 'pending',
            booking_date datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY booking_type (booking_type),
            KEY post_id (post_id),
            KEY status (status)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Set flag to flush rewrite rules
        add_option('dghbm_flush_rewrite_rules', true);
        
        // Create necessary directories
        self::create_directories();
    }
    
    private static function create_directories() {
        $upload_dir = wp_upload_dir();
        $plugin_uploads = $upload_dir['basedir'] . '/dghbm-temp';
        if (!file_exists($plugin_uploads)) {
            wp_mkdir_p($plugin_uploads);
        }
    }
}