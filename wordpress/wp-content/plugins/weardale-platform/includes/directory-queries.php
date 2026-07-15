<?php
/**
 * Public Query Layer for Weardale Directory
 *
 * Provides a clean and reusable API for querying directory listings.
 *
 * @package Weardale_Platform
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main Public Query Function for Weardale Directory
 *
 * @param array $args Custom filters and parameters.
 * @return WP_Query
 */
function weardale_platform_query_directory( $args = array() ) {
    $default_args = array(
        'search'         => '',
        'directory_type' => '',
        'village'        => '',
        'service_area'   => '',
        'verified'       => 'all', // 'all', 'yes' (or true), 'no' (or false)
        'accessibility'  => '', // Keyword to search in accessibility field, or 'any'
        'limit'          => 12,
        'paged'          => 1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    );

    $parsed = wp_parse_args( $args, $default_args );

    $query_args = array(
        'post_type'      => 'weardale_directory',
        'posts_per_page' => intval( $parsed['limit'] ),
        'paged'          => intval( $parsed['paged'] ),
        'orderby'        => sanitize_key( $parsed['orderby'] ),
        'order'          => strtoupper( $parsed['order'] ) === 'DESC' ? 'DESC' : 'ASC',
        'post_status'    => 'publish',
    );

    // 1. Keyword search (Name/Description)
    if ( ! empty( $parsed['search'] ) ) {
        $query_args['s'] = sanitize_text_field( $parsed['search'] );
    }

    // 2. Taxonomies Query
    $tax_query = array( 'relation' => 'AND' );

    // Directory Type
    if ( ! empty( $parsed['directory_type'] ) ) {
        $type_terms = is_array( $parsed['directory_type'] ) ? $parsed['directory_type'] : array( $parsed['directory_type'] );
        $tax_query[] = array(
            'taxonomy' => 'directory_type',
            'field'    => 'slug',
            'terms'    => array_map( 'sanitize_title', $type_terms ),
            'operator' => 'IN',
        );
    }

    // Village
    if ( ! empty( $parsed['village'] ) ) {
        $village_terms = is_array( $parsed['village'] ) ? $parsed['village'] : array( $parsed['village'] );
        $tax_query[] = array(
            'taxonomy' => 'village',
            'field'    => 'slug',
            'terms'    => array_map( 'sanitize_title', $village_terms ),
            'operator' => 'IN',
        );
    }

    // Service Area
    if ( ! empty( $parsed['service_area'] ) ) {
        $area_terms = is_array( $parsed['service_area'] ) ? $parsed['service_area'] : array( $parsed['service_area'] );
        $tax_query[] = array(
            'taxonomy' => 'service_area',
            'field'    => 'slug',
            'terms'    => array_map( 'sanitize_title', $area_terms ),
            'operator' => 'IN',
        );
    }

    if ( count( $tax_query ) > 1 ) {
        $query_args['tax_query'] = $tax_query;
    }

    // 3. Metadata Query
    $meta_query = array( 'relation' => 'AND' );

    // Verified
    if ( 'yes' === $parsed['verified'] || true === $parsed['verified'] || '1' === $parsed['verified'] ) {
        $meta_query[] = array(
            'key'     => '_directory_verified',
            'value'   => '1',
            'compare' => '=',
        );
    } elseif ( 'no' === $parsed['verified'] || false === $parsed['verified'] || '0' === $parsed['verified'] ) {
        // May be '0' or not set at all
        $meta_query[] = array(
            'relation' => 'OR',
            array(
                'key'     => '_directory_verified',
                'value'   => '0',
                'compare' => '=',
            ),
            array(
                'key'     => '_directory_verified',
                'compare' => 'NOT EXISTS',
            ),
        );
    }

    // Accessibility filter (e.g. check if not empty, or matches specific keyword if provided)
    if ( ! empty( $parsed['accessibility'] ) ) {
        if ( 'any' === $parsed['accessibility'] ) {
            $meta_query[] = array(
                'key'     => '_directory_accessibility',
                'value'   => '',
                'compare' => '!=',
            );
        } else {
            $meta_query[] = array(
                'key'     => '_directory_accessibility',
                'value'   => sanitize_text_field( $parsed['accessibility'] ),
                'compare' => 'LIKE',
            );
        }
    }

    if ( count( $meta_query ) > 1 ) {
        $query_args['meta_query'] = $meta_query;
    }

    // 4. Run WP_Query
    return new WP_Query( $query_args );
}

/**
 * Get full formatted metadata array for a directory entry
 *
 * @param int $post_id Post ID.
 * @return array
 */
function weardale_platform_get_directory_meta( $post_id ) {
    $meta = array();
    $meta['address']            = get_post_meta( $post_id, '_directory_address', true );
    $meta['phone']              = get_post_meta( $post_id, '_directory_phone', true );
    $meta['email']              = get_post_meta( $post_id, '_directory_email', true );
    $meta['website']            = get_post_meta( $post_id, '_directory_website', true );
    $meta['opening_hours']      = get_post_meta( $post_id, '_directory_opening_hours', true );
    $meta['accessibility']      = get_post_meta( $post_id, '_directory_accessibility', true );
    $meta['who_it_helps']       = get_post_meta( $post_id, '_directory_who_it_helps', true );
    $meta['pricing']            = get_post_meta( $post_id, '_directory_pricing', true );
    $meta['booking_required']   = get_post_meta( $post_id, '_directory_booking_required', true ) === 'yes';
    $meta['latitude']           = get_post_meta( $post_id, '_directory_latitude', true );
    $meta['longitude']          = get_post_meta( $post_id, '_directory_longitude', true );
    $meta['facebook']           = get_post_meta( $post_id, '_directory_facebook', true );
    $meta['instagram']          = get_post_meta( $post_id, '_directory_instagram', true );
    $meta['linkedin']           = get_post_meta( $post_id, '_directory_linkedin', true );
    $meta['verified']           = get_post_meta( $post_id, '_directory_verified', true ) === '1';
    $meta['last_reviewed']      = get_post_meta( $post_id, '_directory_last_reviewed', true );
    $meta['related_programme']  = get_post_meta( $post_id, '_directory_related_programme', true );
    $meta['related_events']     = get_post_meta( $post_id, '_directory_related_events', true );

    return $meta;
}
