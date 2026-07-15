<?php
/**
 * Plugin Name: Weardale Platform
 * Plugin URI: https://weardaletogether.org.uk/
 * Description: Platform-specific core logic for Weardale Together. Separates system architecture from display themes.
 * Version: 1.0.0
 * Author: Weardale Together Delivery Team
 * License: GPLv2 or later
 * Text Domain: weardale-platform
 * Domain Path: /languages
 *
 * All code namespace-prefixed with weardale_platform_ to prevent conflicts.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Prevent direct access
}

// 1. Version and path constants
define( 'WEARDALE_PLATFORM_VERSION', '1.0.0' );
define( 'WEARDALE_PLATFORM_DIR', plugin_dir_path( __FILE__ ) );
define( 'WEARDALE_PLATFORM_URL', plugin_dir_url( __FILE__ ) );

// 2. Activation safety checks
function weardale_platform_activate() {
    // Check if PHP version is sufficient
    if ( version_compare( PHP_VERSION, '7.4.0', '<' ) ) {
        wp_die( esc_html__( 'Weardale Platform requires PHP version 7.4.0 or higher.', 'weardale-platform' ) );
    }
    
    // Load editorial.php if available, register the CPT/taxonomies, and flush rewrite rules
    $editorial_file = WEARDALE_PLATFORM_DIR . 'includes/editorial.php';
    if ( file_exists( $editorial_file ) ) {
        include_once $editorial_file;
        if ( function_exists( 'weardale_platform_register_event_cpt' ) ) {
            weardale_platform_register_event_cpt();
        }
        if ( function_exists( 'weardale_platform_register_strand_taxonomy' ) ) {
            weardale_platform_register_strand_taxonomy();
        }
    }
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'weardale_platform_activate' );

// 3. Deactivation hook
function weardale_platform_deactivate() {
    // Flush rewrite rules on deactivation to keep permalinks clean
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'weardale_platform_deactivate' );

// 4. Platform includes folder bootstrapper
function weardale_platform_init() {
    // Path to includes directory
    $includes_dir = WEARDALE_PLATFORM_DIR . 'includes/';
    
    // Load the custom editorial experience enhancements (CPTs, Taxonomies, custom fields)
    if ( file_exists( $includes_dir . 'editorial.php' ) ) {
        include_once $includes_dir . 'editorial.php';
    }
}
add_action( 'plugins_loaded', 'weardale_platform_init' );
