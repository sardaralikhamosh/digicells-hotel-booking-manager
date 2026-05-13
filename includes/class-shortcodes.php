<?php
class DGHBM_Shortcodes {
    
    public static function init() {
        add_shortcode('dghbm_hotel_listing', array(__CLASS__, 'hotel_listing'));
        add_shortcode('dghbm_guest_house_listing', array(__CLASS__, 'guest_house_listing'));
        add_shortcode('dghbm_property_listing', array(__CLASS__, 'property_listing'));
        add_shortcode('dghbm_search_form', array(__CLASS__, 'search_form'));
    }
    
    public static function hotel_listing($atts) {
        $atts = shortcode_atts(array('per_page' => 12, 'view' => 'grid'), $atts);
        ob_start();
        include DGHBM_PLUGIN_DIR . 'templates/archive-hotel.php';
        return ob_get_clean();
    }
    
    // Similar for guest houses and properties
    public static function search_form() {
        ob_start();
        ?>
        <div class="dghbm-search-form">
            <input type="text" id="dghbm_search_location" placeholder="Enter city or area">
            <select id="dghbm_search_type">
                <option value="">All Types</option>
                <option value="hotel">Hotel</option>
                <option value="guest_house">Guest House</option>
                <option value="property">Private Property</option>
            </select>
            <button id="dghbm_search_btn">Search</button>
        </div>
        <div id="dghbm_search_results" class="dghbm-cards-grid"></div>
        <?php
        return ob_get_clean();
    }
}