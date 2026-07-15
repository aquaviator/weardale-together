<?php
/**
 * Event Core Query API Layer
 *
 * Provides optimized database query methods for event occurrences and schedules.
 *
 * @package Weardale_Platform
 * @since 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Perform a master occurrence search in the custom database table
 */
function weardale_platform_query_occurrences( $args = array() ) {
    global $wpdb;
    
    $defaults = array(
        'event_id'          => 0,           // Filter by specific post ID
        'start_date'        => '',          // Y-m-d H:i:s
        'end_date'          => '',          // Y-m-d H:i:s
        'scope'             => 'upcoming',  // 'upcoming', 'past', 'all'
        'strand'            => '',          // String slug or array of slugs
        'booking_status'    => '',          // filter by status
        'include_cancelled' => true,
        'limit'             => -1,
        'offset'            => 0,
        'orderby'           => 'start',     // 'start' or 'end'
        'order'             => 'ASC',
    );
    
    $parsed = wp_parse_args( $args, $defaults );
    
    $table_occ   = $wpdb->prefix . 'weardale_event_occurrences';
    $table_posts = $wpdb->prefix . 'posts';
    
    $select_clause = "SELECT DISTINCT o.*, p.post_title, p.post_content, p.post_excerpt, p.post_author, p.post_status, p.post_name as post_slug ";
    $from_clause   = "FROM $table_occ o INNER JOIN $table_posts p ON o.event_id = p.ID ";
    $where_clauses = array( "p.post_status = 'publish'" );
    $query_params  = array();

    // Specific Event ID filter
    if ( ! empty( $parsed['event_id'] ) ) {
        $where_clauses[] = "o.event_id = %d";
        $query_params[]  = intval( $parsed['event_id'] );
    }
    
    // 1. Time bounds and scopes
    $now_utc = current_time( 'mysql', 1 ); // UTC date/time
    
    if ( ! empty( $parsed['start_date'] ) ) {
        $where_clauses[] = "o.occurrence_start >= %s";
        $query_params[]  = $parsed['start_date'];
    }
    
    if ( ! empty( $parsed['end_date'] ) ) {
        $where_clauses[] = "o.occurrence_start <= %s";
        $query_params[]  = $parsed['end_date'];
    }
    
    // Fallbacks if dates are not explicitly set
    if ( empty( $parsed['start_date'] ) && empty( $parsed['end_date'] ) ) {
        if ( 'upcoming' === $parsed['scope'] ) {
            $where_clauses[] = "o.occurrence_start >= %s";
            $query_params[]  = $now_utc;
        } elseif ( 'past' === $parsed['scope'] ) {
            $where_clauses[] = "o.occurrence_start < %s";
            $query_params[]  = $now_utc;
        }
    }
    
    // 2. Booking Status filter
    if ( ! empty( $parsed['booking_status'] ) ) {
        $table_meta = $wpdb->prefix . 'postmeta';
        $from_clause .= " INNER JOIN $table_meta pm_bs ON o.event_id = pm_bs.post_id AND pm_bs.meta_key = '_event_booking_status' ";
        $where_clauses[] = "pm_bs.meta_value = %s";
        $query_params[]  = $parsed['booking_status'];
    }
    
    // 3. Include/exclude cancelled occurrences
    if ( ! $parsed['include_cancelled'] ) {
        $where_clauses[] = "o.occurrence_status != 'cancelled'";
    }
    
    // 4. Strand Taxonomy filter (uses WP DB relationships)
    if ( ! empty( $parsed['strand'] ) ) {
        $table_rel = $wpdb->prefix . 'term_relationships';
        $table_tax = $wpdb->prefix . 'term_taxonomy';
        $table_terms = $wpdb->prefix . 'terms';
        
        $from_clause .= " INNER JOIN $table_rel tr ON o.event_id = tr.object_id 
                          INNER JOIN $table_tax tt ON tr.term_taxonomy_id = tt.term_taxonomy_id 
                          INNER JOIN $table_terms t ON tt.term_id = t.term_id ";
        
        $where_clauses[] = "tt.taxonomy = 'strand'";
        
        if ( is_array( $parsed['strand'] ) ) {
            $slugs_placeholder = implode( ',', array_fill( 0, count( $parsed['strand'] ), '%s' ) );
            $where_clauses[] = "t.slug IN ($slugs_placeholder)";
            foreach ( $parsed['strand'] as $slug ) {
                $query_params[] = sanitize_title( $slug );
            }
        } else {
            $where_clauses[] = "t.slug = %s";
            $query_params[]  = sanitize_title( $parsed['strand'] );
        }
    }
    
    // Assemble WHERE clause
    $where_sql = '';
    if ( ! empty( $where_clauses ) ) {
        $where_sql = 'WHERE ' . implode( ' AND ', $where_clauses );
    }
    
    // Order criteria
    $order_field = ( 'end' === $parsed['orderby'] ) ? 'o.occurrence_end' : 'o.occurrence_start';
    $order_dir   = strtoupper( $parsed['order'] ) === 'DESC' ? 'DESC' : 'ASC';
    $order_sql   = "ORDER BY $order_field $order_dir";
    
    // Pagination / Limits
    $limit_sql = '';
    $limit  = intval( $parsed['limit'] );
    $offset = intval( $parsed['offset'] );
    if ( $limit > 0 ) {
        if ( $offset > 0 ) {
            $limit_sql = $wpdb->prepare( "LIMIT %d, %d", $offset, $limit );
        } else {
            $limit_sql = $wpdb->prepare( "LIMIT %d", $limit );
        }
    }
    
    $final_sql = "$select_clause $from_clause $where_sql $order_sql $limit_sql";
    
    if ( ! empty( $query_params ) ) {
        $query = $wpdb->prepare( $final_sql, $query_params );
    } else {
        $query = $final_sql;
    }
    
    $results = $wpdb->get_results( $query, ARRAY_A );
    
    // Add processed helper keys (permalinks, image references)
    $processed_occurrences = array();
    foreach ( $results as $row ) {
        $row['permalink']    = get_permalink( $row['event_id'] );
        $row['thumbnail_id'] = get_post_thumbnail_id( $row['event_id'] );
        $row['thumbnail_url']= get_the_post_thumbnail_url( $row['event_id'], 'medium_large' ) ?: '';
        
        // Load detailed parent meta
        $row['meta']         = weardale_platform_get_event_meta_full( $row['event_id'] );
        
        // Load strand taxonomy values
        $row['strands']      = array();
        $terms = wp_get_post_terms( $row['event_id'], 'strand' );
        if ( ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                $row['strands'][] = array(
                    'term_id' => $term->term_id,
                    'name'    => $term->name,
                    'slug'    => $term->slug,
                );
            }
        }
        
        $processed_occurrences[] = $row;
    }
    
    return $processed_occurrences;
}

/**
 * Gets the closest upcoming single occurrence for a given event ID.
 */
function weardale_platform_get_next_occurrence( $event_id ) {
    $results = weardale_platform_query_occurrences( array(
        'event_id'          => $event_id,
        'start_date'        => current_time( 'mysql', 1 ),
        'scope'             => 'upcoming',
        'limit'             => 1,
        'include_cancelled' => true,
    ) );
    
    if ( ! empty( $results ) ) {
        return $results[0];
    }
    
    // If no future occurrences exist, try querying directly by event_id
    global $wpdb;
    $table_occ = $wpdb->prefix . 'weardale_event_occurrences';
    $now_utc = current_time( 'mysql', 1 );
    
    $query = $wpdb->prepare(
        "SELECT * FROM $table_occ 
         WHERE event_id = %d AND occurrence_start >= %s 
         ORDER BY occurrence_start ASC LIMIT 1",
        $event_id,
        $now_utc
    );
    $occ_raw = $wpdb->get_row( $query, ARRAY_A );
    
    if ( $occ_raw ) {
        $occ_raw['permalink']    = get_permalink( $event_id );
        $occ_raw['thumbnail_id'] = get_post_thumbnail_id( $event_id );
        $occ_raw['thumbnail_url']= get_the_post_thumbnail_url( $event_id, 'medium_large' ) ?: '';
        $occ_raw['meta']         = weardale_platform_get_event_meta_full( $event_id );
        $occ_raw['strands']      = array();
        $terms = wp_get_post_terms( $event_id, 'strand' );
        if ( ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                $occ_raw['strands'][] = array(
                    'term_id' => $term->term_id,
                    'name'    => $term->name,
                    'slug'    => $term->slug,
                );
            }
        }
        return $occ_raw;
    }
    
    return false;
}

/**
 * Return all distinct occurrences grouped by calendar day for a specified month
 */
function weardale_platform_get_calendar_month_occurrences( $year_month ) {
    list( $year, $month ) = explode( '-', $year_month );
    $year = intval( $year );
    $month = intval( $month );
    
    $start = sprintf( '%04d-%02d-01 00:00:00', $year, $month );
    $days  = cal_days_in_month( CAL_GREGORIAN, $month, $year );
    $end   = sprintf( '%04d-%02d-%02d 23:59:59', $year, $month, $days );
    
    return weardale_platform_query_occurrences( array(
        'start_date'        => $start,
        'end_date'          => $end,
        'scope'             => 'all',
        'include_cancelled' => true,
    ) );
}
