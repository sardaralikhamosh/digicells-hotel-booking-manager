<?php
/**
 * Plugin Name: Digicells Hotel Booking Management Plugin
 * Plugin URI: https://digicellinternational.github.io/
 * Description: Complete hotel, guest house, and private property booking system.
 * Version: 1.0.0
 * Author: Sardar Ali Khamosh (digicells)
 * Author URI: https://digicellinternational.github.io/
 * Text Domain: digicells-hbm
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('DGHBM_VERSION', '1.0.0');
define('DGHBM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DGHBM_PLUGIN_URL', plugin_dir_url(__FILE__));

// Autoload classes (simple manual loading for clarity)
require_once DGHBM_PLUGIN_DIR . 'includes/class-installer.php';
require_once DGHBM_PLUGIN_DIR . 'includes/class-post-types.php';
require_once DGHBM_PLUGIN_DIR . 'includes/class-meta-boxes.php';
require_once DGHBM_PLUGIN_DIR . 'includes/class-shortcodes.php';
require_once DGHBM_PLUGIN_DIR . 'includes/class-ajax-handler.php';
require_once DGHBM_PLUGIN_DIR . 'includes/class-booking-manager.php';
require_once DGHBM_PLUGIN_DIR . 'includes/class-email-handler.php';
require_once DGHBM_PLUGIN_DIR . 'includes/class-admin-menu.php';
require_once DGHBM_PLUGIN_DIR . 'includes/class-assets.php';

// Initialize plugin
class DigicellsHotelBookingManager {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Hook activation
        register_activation_hook(__FILE__, array($this, 'activate'));
        
        // Initialize components
        add_action('plugins_loaded', array($this, 'init'));
    }
    
    public function activate() {
        DGHBM_Installer::activate();
    }
    
    public function init() {
        // Load text domain
        load_plugin_textdomain('digicells-hbm', false, dirname(plugin_basename(__FILE__)) . '/languages');
        
        // Initialize classes
        DGHBM_PostTypes::init();
        DGHBM_MetaBoxes::init();
        DGHBM_Shortcodes::init();
        DGHBM_AjaxHandler::init();
        DGHBM_BookingManager::init();
        DGHBM_EmailHandler::init();
        DGHBM_AdminMenu::init();
        DGHBM_Assets::init();
        
        // Flush rewrite rules on activation
        if (get_option('dghbm_flush_rewrite_rules')) {
            flush_rewrite_rules();
            delete_option('dghbm_flush_rewrite_rules');
        }
    }
}

DigicellsHotelBookingManager::get_instance();