<?php
class DGHBM_AdminMenu {
    
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_menu'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'admin_assets'));
    }
    
    public static function add_menu() {
        add_submenu_page('edit.php?post_type=dghbm_hotel', __('Bookings', 'digicells-hbm'), __('All Bookings', 'digicells-hbm'), 'manage_options', 'dghbm_bookings', array(__CLASS__, 'bookings_page'));
    }
    
    public static function bookings_page() {
        include DGHBM_PLUGIN_DIR . 'views/admin/bookings-page.php';
    }
    
    public static function admin_assets($hook) {
        if ($hook == 'hotel_booking_page_dghbm_bookings') {
            wp_enqueue_style('dghbm-admin', DGHBM_PLUGIN_URL . 'assets/css/admin.css');
            wp_enqueue_script('dghbm-admin', DGHBM_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), DGHBM_VERSION, true);
            wp_localize_script('dghbm-admin', 'dghbm_admin', array('ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('dghbm_admin_nonce')));
        }
    }
}