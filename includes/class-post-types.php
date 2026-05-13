<?php
class DGHBM_PostTypes {
    
    public static function init() {
        add_action('init', array(__CLASS__, 'register_post_types'));
        add_action('init', array(__CLASS__, 'register_taxonomies'));
    }
    
    public static function register_post_types() {
        // 1. Hotel
        register_post_type('dghbm_hotel', array(
            'labels' => array(
                'name'               => __('Hotels', 'digicells-hbm'),
                'singular_name'      => __('Hotel', 'digicells-hbm'),
                'menu_name'          => __('Hotel Booking', 'digicells-hbm'),
                'add_new'            => __('Add New Hotel', 'digicells-hbm'),
                'add_new_item'       => __('Add New Hotel', 'digicells-hbm'),
                'edit_item'          => __('Edit Hotel', 'digicells-hbm'),
                'new_item'           => __('New Hotel', 'digicells-hbm'),
                'view_item'          => __('View Hotel', 'digicells-hbm'),
                'search_items'       => __('Search Hotels', 'digicells-hbm'),
                'not_found'          => __('No hotels found', 'digicells-hbm'),
                'not_found_in_trash' => __('No hotels found in trash', 'digicells-hbm'),
            ),
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => array('slug' => 'hotel'),
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => 25,
            'menu_icon'           => 'dashicons-building',
            'supports'            => array('title', 'editor', 'thumbnail', 'excerpt'),
            'show_in_rest'        => true,
        ));
        
        // 2. Guest House
        register_post_type('dghbm_guest_house', array(
            'labels' => array(
                'name'               => __('Guest Houses', 'digicells-hbm'),
                'singular_name'      => __('Guest House', 'digicells-hbm'),
                'menu_name'          => __('Guest Houses', 'digicells-hbm'),
                'add_new'            => __('Add New Guest House', 'digicells-hbm'),
                'add_new_item'       => __('Add New Guest House', 'digicells-hbm'),
            ),
            'public'              => true,
            'has_archive'         => true,
            'rewrite'             => array('slug' => 'guest-house'),
            'menu_icon'           => 'dashicons-home',
            'supports'            => array('title', 'editor', 'thumbnail', 'excerpt'),
            'show_in_rest'        => true,
        ));
        
        // 3. Private Property
        register_post_type('dghbm_property', array(
            'labels' => array(
                'name'               => __('Private Properties', 'digicells-hbm'),
                'singular_name'      => __('Property', 'digicells-hbm'),
                'menu_name'          => __('Private Properties', 'digicells-hbm'),
                'add_new'            => __('Add New Property', 'digicells-hbm'),
            ),
            'public'              => true,
            'has_archive'         => true,
            'rewrite'             => array('slug' => 'property'),
            'menu_icon'           => 'dashicons-building',
            'supports'            => array('title', 'editor', 'thumbnail', 'excerpt'),
            'show_in_rest'        => true,
        ));
    }
    
    public static function register_taxonomies() {
        // For Hotels
        self::register_taxonomy('dghbm_hotel_location', 'dghbm_hotel', 'Hotel Locations');
        self::register_taxonomy('dghbm_hotel_category', 'dghbm_hotel', 'Hotel Categories');
        self::register_taxonomy('dghbm_hotel_amenity', 'dghbm_hotel', 'Hotel Amenities');
        
        // For Guest Houses
        self::register_taxonomy('dghbm_gh_location', 'dghbm_guest_house', 'Guest House Locations');
        self::register_taxonomy('dghbm_gh_category', 'dghbm_guest_house', 'Guest House Categories');
        self::register_taxonomy('dghbm_gh_amenity', 'dghbm_guest_house', 'Guest House Amenities');
        
        // For Private Properties
        self::register_taxonomy('dghbm_prop_location', 'dghbm_property', 'Property Locations');
        self::register_taxonomy('dghbm_prop_category', 'dghbm_property', 'Property Categories');
        self::register_taxonomy('dghbm_prop_amenity', 'dghbm_property', 'Property Amenities');
    }
    
    private static function register_taxonomy($taxonomy, $post_type, $label) {
        register_taxonomy($taxonomy, $post_type, array(
            'labels' => array(
                'name'              => __($label, 'digicells-hbm'),
                'singular_name'     => __($label, 'digicells-hbm'),
                'search_items'      => __('Search', 'digicells-hbm'),
                'all_items'         => __('All', 'digicells-hbm'),
                'parent_item'       => __('Parent', 'digicells-hbm'),
                'parent_item_colon' => __('Parent:', 'digicells-hbm'),
                'edit_item'         => __('Edit', 'digicells-hbm'),
                'update_item'       => __('Update', 'digicells-hbm'),
                'add_new_item'      => __('Add New', 'digicells-hbm'),
                'new_item_name'     => __('New Name', 'digicells-hbm'),
                'menu_name'         => __($label, 'digicells-hbm'),
            ),
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => sanitize_title($label)),
            'show_in_rest'      => true,
        ));
    }
}