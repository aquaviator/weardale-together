<?php
/**
 * Master Loader and Legacy Compatibility Layer
 *
 * Boots the modular events system and provides backward compatibility APIs.
 *
 * @package Weardale_Platform
 * @since 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. Boot up the modular includes
$includes_dir = plugin_dir_path( __FILE__ );

$modules = array(
    'event-post-type.php',
    'event-occurrences.php',
    'event-recurrence.php',
    'event-meta.php',
    'event-admin-calendar.php',
    'event-admin-list.php',
    'event-queries.php',
    'event-tools.php',
);

foreach ( $modules as $module ) {
    if ( file_exists( $includes_dir . $module ) ) {
        include_once $includes_dir . $module;
    }
}

/**
 * Legacy Compatibility: weardale_platform_get_event_meta()
 * Maps old keys to the new structured metadata.
 */
function weardale_platform_get_event_meta( $post_id ) {
    if ( function_exists( 'weardale_platform_get_event_meta_full' ) ) {
        return weardale_platform_get_event_meta_full( $post_id );
    }
    
    // Fail-safe manual fallback
    $meta = array();
    $meta['start_date']                 = get_post_meta( $post_id, '_event_date', true );
    $meta['end_date']                   = get_post_meta( $post_id, '_event_end_date', true );
    $meta['all_day']                    = get_post_meta( $post_id, '_event_all_day', true ) === '1';
    $meta['time_text']                  = get_post_meta( $post_id, '_event_time', true );
    $meta['location_addr']              = get_post_meta( $post_id, '_event_location', true );
    $meta['venue_name']                 = get_post_meta( $post_id, '_event_venue_name', true );
    $meta['map_url']                    = get_post_meta( $post_id, '_event_map_url', true );
    $meta['audience']                   = get_post_meta( $post_id, '_event_audience', true );
    $meta['age_guidance']               = get_post_meta( $post_id, '_event_age_guidance', true );
    $meta['accessibility']              = get_post_meta( $post_id, '_event_accessibility', true );
    $meta['booking_status']             = get_post_meta( $post_id, '_event_booking_status', true );
    $meta['booking_url']                = get_post_meta( $post_id, '_event_booking_url', true );
    $meta['booking_instructions']       = get_post_meta( $post_id, '_event_booking_instructions', true );
    $meta['cost_text']                  = get_post_meta( $post_id, '_event_cost', true );
    $meta['organiser_name']             = get_post_meta( $post_id, '_event_organiser_name', true );
    $meta['organiser_contact']          = get_post_meta( $post_id, '_event_organiser_contact', true );
    
    if ( empty( $meta['venue_name'] ) ) {
        $meta['venue_name'] = $meta['location_addr'];
    }
    if ( empty( $meta['booking_status'] ) ) {
        $meta['booking_status'] = ! empty( $meta['booking_url'] ) ? 'booking_required' : 'no_booking_required';
    }
    
    return $meta;
}

/**
 * Legacy Compatibility: weardale_platform_get_events()
 * Standard WP_Query lookup for compatibility with original SPRINT 9 list views.
 * Updated to internally utilize weardale_platform_query_occurrences for consistent scheduling.
 */
function weardale_platform_get_events( $args = array() ) {
    $default_args = array(
        'posts_per_page' => -1,
        'scope'          => 'upcoming', // 'upcoming', 'past', or 'all'
        'strand'         => '',
        'order'          => 'ASC',
        'post_status'    => 'publish',
    );
    
    $parsed_args = wp_parse_args( $args, $default_args );
    
    $limit = $parsed_args['posts_per_page'];
    if ( $limit === -1 ) {
        $limit = 100; // Safe upper bound to prevent infinite execution
    }
    
    $query_args = array(
        'scope'             => $parsed_args['scope'],
        'limit'             => $limit,
        'include_cancelled' => false,
    );
    if ( ! empty( $parsed_args['strand'] ) ) {
        $query_args['strand'] = $parsed_args['strand'];
    }
    if ( 'past' === $parsed_args['scope'] ) {
        $query_args['order'] = 'DESC';
    } else {
        $query_args['order'] = $parsed_args['order'];
    }
    
    $occurrences = function_exists( 'weardale_platform_query_occurrences' )
        ? weardale_platform_query_occurrences( $query_args )
        : array();
        
    $post_ids = array();
    foreach ( $occurrences as $occ ) {
        $post_ids[] = intval( $occ['event_id'] );
    }
    
    if ( empty( $post_ids ) ) {
        // Return a WP_Query that returns no posts safely
        return new WP_Query( array( 'post_type' => 'weardale_event', 'post__in' => array( 0 ) ) );
    }
    
    // To maintain exact occurrence scheduling order, orderby post__in is utilized
    return new WP_Query( array(
        'post_type'      => 'weardale_event',
        'post__in'       => $post_ids,
        'orderby'        => 'post__in',
        'posts_per_page' => $limit,
        'post_status'    => $parsed_args['post_status'],
    ) );
}

/**
 * Flush rules dynamically if not flushed.
 * Resolves 404 issue on /whats-on/ immediately.
 */
function weardale_platform_flush_rules_on_init() {
    if ( get_option( 'weardale_platform_rules_flushed_v2' ) !== '1' ) {
        flush_rewrite_rules();
        update_option( 'weardale_platform_rules_flushed_v2', '1' );
    }
}
add_action( 'init', 'weardale_platform_flush_rules_on_init', 99 );
