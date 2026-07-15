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
 */
function weardale_platform_get_events( $args = array() ) {
    $timezone = get_option( 'timezone_string' );
    if ( ! empty( $timezone ) ) {
        date_default_timezone_set( $timezone );
    }
    $today = date( 'Y-m-d' );
    
    $default_args = array(
        'posts_per_page' => -1,
        'scope'          => 'upcoming', // 'upcoming', 'past', or 'all'
        'strand'         => '',
        'order'          => 'ASC',
        'post_status'    => 'publish',
    );
    
    $parsed_args = wp_parse_args( $args, $default_args );
    
    $query_args = array(
        'post_type'      => 'weardale_event',
        'posts_per_page' => $parsed_args['posts_per_page'],
        'post_status'    => $parsed_args['post_status'],
    );
    
    $meta_query = array();
    
    if ( 'upcoming' === $parsed_args['scope'] ) {
        $meta_query[] = array(
            'key'     => '_event_date',
            'value'   => $today,
            'compare' => '>=',
            'type'    => 'DATE',
        );
        $query_args['orderby'] = 'meta_value';
        $query_args['meta_key'] = '_event_date';
        $query_args['order'] = 'ASC';
    } elseif ( 'past' === $parsed_args['scope'] ) {
        $meta_query[] = array(
            'key'     => '_event_date',
            'value'   => $today,
            'compare' => '<',
            'type'    => 'DATE',
        );
        $query_args['orderby'] = 'meta_value';
        $query_args['meta_key'] = '_event_date';
        $query_args['order'] = 'DESC';
    } else {
        $query_args['orderby'] = 'meta_value';
        $query_args['meta_key'] = '_event_date';
        $query_args['order'] = $parsed_args['order'];
    }
    
    if ( ! empty( $meta_query ) ) {
        $query_args['meta_query'] = $meta_query;
    }
    
    if ( ! empty( $parsed_args['strand'] ) ) {
        $query_args['tax_query'] = array(
            array(
                'taxonomy' => 'strand',
                'field'    => 'slug',
                'terms'    => $parsed_args['strand'],
            ),
        );
    }
    
    return new WP_Query( $query_args );
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
