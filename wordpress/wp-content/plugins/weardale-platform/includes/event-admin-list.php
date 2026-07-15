<?php
/**
 * Event Admin List Table Customization
 *
 * Implements custom list columns, scannable filter dropdowns, and Duplicate Event actions.
 *
 * @package Weardale_Platform
 * @since 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Configure columns for Weardale Events CPT Admin table
 */
function weardale_platform_set_enhanced_event_columns( $columns ) {
    $new_columns = array(
        'cb'                 => $columns['cb'],
        'title'              => __( 'Event', 'weardale-platform' ),
        'next_occurrence'    => __( 'Next Occurrence', 'weardale-platform' ),
        'recurrence_summary' => __( 'Schedule Pattern', 'weardale-platform' ),
        'event_venue'        => __( 'Venue / Location', 'weardale-platform' ),
        'taxonomy-strand'    => __( 'Strand', 'weardale-platform' ),
        'booking_status'     => __( 'Booking Status', 'weardale-platform' ),
        'event_state'        => __( 'Event State', 'weardale-platform' ),
        'date'               => __( 'Date Published', 'weardale-platform' ),
    );
    return $new_columns;
}
add_filter( 'manage_weardale_event_posts_columns', 'weardale_platform_set_enhanced_event_columns', 15 );

/**
 * Display values inside the enhanced columns
 */
function weardale_platform_display_enhanced_event_columns( $column, $post_id ) {
    global $wpdb;
    
    switch ( $column ) {
        case 'next_occurrence':
            // Query the occurrences table for the closest future/upcoming occurrence
            $now_utc = current_time( 'mysql', 1 );
            $table_occ = $wpdb->prefix . 'weardale_event_occurrences';
            
            $next_occ_query = $wpdb->prepare(
                "SELECT occurrence_start, occurrence_status FROM $table_occ 
                 WHERE event_id = %d AND occurrence_start >= %s 
                 ORDER BY occurrence_start ASC LIMIT 1",
                $post_id,
                $now_utc
            );
            $occ = $wpdb->get_row( $next_occ_query, ARRAY_A );
            
            if ( $occ ) {
                $local_dt = wp_date( 'l, j F Y @ H:i', strtotime( $occ['occurrence_start'] ) );
                echo '<strong>' . esc_html( $local_dt ) . '</strong>';
                if ( $occ['occurrence_status'] === 'cancelled' ) {
                    echo ' <span style="color:#dc2626; font-size:0.7rem; font-weight:bold;">(CANCELLED)</span>';
                }
            } else {
                // Check if there's a past one
                $past_occ_query = $wpdb->prepare(
                    "SELECT occurrence_start FROM $table_occ 
                     WHERE event_id = %d 
                     ORDER BY occurrence_start DESC LIMIT 1",
                    $post_id
                );
                $past_occ = $wpdb->get_var( $past_occ_query );
                if ( $past_occ ) {
                    echo '<span style="color:#64748b;">' . esc_html( wp_date( 'j M Y', strtotime( $past_occ ) ) ) . ' (' . __( 'Past', 'weardale-platform' ) . ')</span>';
                } else {
                    // Fallback to legacy date
                    $legacy_date = get_post_meta( $post_id, '_event_date', true );
                    echo ! empty( $legacy_date ) ? esc_html( date( 'j F Y', strtotime( $legacy_date ) ) ) : '<em>-</em>';
                }
            }
            break;

        case 'recurrence_summary':
            $summary = get_post_meta( $post_id, '_event_recurrence_summary', true );
            echo ! empty( $summary ) ? esc_html( $summary ) : '<em>' . __( 'One-off activity', 'weardale-platform' ) . '</em>';
            break;

        case 'event_venue':
            $venue = get_post_meta( $post_id, '_event_venue_name', true );
            if ( empty( $venue ) ) {
                $venue = get_post_meta( $post_id, '_event_location', true );
            }
            echo ! empty( $venue ) ? esc_html( $venue ) : '<em>-</em>';
            break;

        case 'booking_status':
            $status = get_post_meta( $post_id, '_event_booking_status', true );
            $labels = array(
                'no_booking_required' => array( 'label' => __( 'No Booking Required', 'weardale-platform' ), 'color' => '#475569' ),
                'booking_recommended' => array( 'label' => __( 'Recommended', 'weardale-platform' ), 'color' => '#0d9488' ),
                'booking_required'    => array( 'label' => __( 'Required', 'weardale-platform' ), 'color' => '#2563eb' ),
                'sold_out'            => array( 'label' => __( 'SOLD OUT', 'weardale-platform' ), 'color' => '#ea580c' ),
                'cancelled'           => array( 'label' => __( 'CANCELLED', 'weardale-platform' ), 'color' => '#dc2626' ),
            );

            if ( isset( $labels[ $status ] ) ) {
                $cfg = $labels[ $status ];
                echo '<span class="badge" style="background-color: ' . esc_attr( $cfg['color'] ) . '; color: #fff; padding: 0.25rem 0.5rem; border-radius: 4px; font-weight: bold; font-size: 0.725rem; text-transform: uppercase; display: inline-block;">' . esc_html( $cfg['label'] ) . '</span>';
            } else {
                echo '<em>-</em>';
            }
            break;

        case 'event_state':
            $now_utc = current_time( 'mysql', 1 );
            $table_occ = $wpdb->prefix . 'weardale_event_occurrences';
            
            // Check if there are any upcoming occurrences left in the series
            $has_upcoming = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_occ 
                 WHERE event_id = %d AND occurrence_start >= %s AND occurrence_status != 'cancelled'",
                $post_id,
                $now_utc
            ) );
            
            if ( $has_upcoming > 0 ) {
                echo '<span style="color:#16a34a; font-weight:600;">🟢 ' . esc_html__( 'Upcoming', 'weardale-platform' ) . '</span>';
            } else {
                echo '<span style="color:#94a3b8;">⚪ ' . esc_html__( 'Past / Finished', 'weardale-platform' ) . '</span>';
            }
            break;
    }
}
add_action( 'manage_weardale_event_posts_custom_column', 'weardale_platform_display_enhanced_event_columns', 15, 2 );

/**
 * Register Event Duplication Row Action link
 */
function weardale_platform_add_duplicate_row_action( $actions, $post ) {
    if ( $post->post_type === 'weardale_event' && current_user_can( 'edit_posts' ) ) {
        $duplicate_url = wp_nonce_url(
            admin_url( 'admin-post.php?action=wt_duplicate_event&post_id=' . $post->ID ),
            'wt_duplicate_event_nonce'
        );
        $actions['duplicate_event'] = sprintf(
            '<a href="%s" title="%s" style="color:#3b5c3a; font-weight:600;">%s</a>',
            esc_url( $duplicate_url ),
            esc_attr__( 'Duplicate this event as a draft', 'weardale-platform' ),
            __( 'Duplicate', 'weardale-platform' )
        );
    }
    return $actions;
}
add_filter( 'post_row_actions', 'weardale_platform_add_duplicate_row_action', 10, 2 );

/**
 * Handle Duplication Action POST handler
 */
function weardale_platform_handle_duplicate_event() {
    if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'wt_duplicate_event_nonce' ) ) {
        wp_die( esc_html__( 'Security check failed.', 'weardale-platform' ) );
    }
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( esc_html__( 'You do not have permission to copy events.', 'weardale-platform' ) );
    }

    $source_id = isset( $_GET['post_id'] ) ? intval( $_GET['post_id'] ) : 0;
    if ( ! $source_id ) {
        wp_die( esc_html__( 'Invalid post ID.', 'weardale-platform' ) );
    }

    $source_post = get_post( $source_id );
    if ( ! $source_post ) {
        wp_die( esc_html__( 'Source event not found.', 'weardale-platform' ) );
    }

    // 1. Create duplicate Draft post
    $new_post_args = array(
        'post_title'   => sprintf( __( '%s (Copy)', 'weardale-platform' ), $source_post->post_title ),
        'post_content' => $source_post->post_content,
        'post_excerpt' => $source_post->post_excerpt,
        'post_status'  => 'draft',
        'post_type'    => 'weardale_event',
    );
    $new_post_id = wp_insert_post( $new_post_args );

    if ( is_wp_error( $new_post_id ) ) {
        wp_die( esc_html__( 'Error duplicating event.', 'weardale-platform' ) );
    }

    // 2. Duplicate Strand taxonomies
    $strands = wp_get_post_terms( $source_id, 'strand', array( 'fields' => 'ids' ) );
    if ( ! is_wp_error( $strands ) && ! empty( $strands ) ) {
        wp_set_post_terms( $new_post_id, $strands, 'strand' );
    }

    // 3. Duplicate Featured Image reference
    $thumbnail_id = get_post_thumbnail_id( $source_id );
    if ( $thumbnail_id ) {
        set_post_thumbnail( $new_post_id, $thumbnail_id );
    }

    // 4. Duplicate metadata configuration (Excluding status, historical exceptions, or past schedules)
    $copied_meta_keys = array(
        // TIMING & SCHEDULE meta (Copy them but let editor review and save explicitly)
        '_event_date',
        '_event_end_date',
        '_event_start_time',
        '_event_end_time',
        '_event_all_day',
        '_event_is_recurring',
        '_event_recurrence_mode',
        '_event_recurrence_interval',
        '_event_recurrence_weekdays',
        '_event_recurrence_monthly_type',
        '_event_recurrence_end_type',
        '_event_recurrence_end_date',
        '_event_recurrence_end_count',
        '_event_recurrence_summary',
        
        // LOCATION details
        '_event_is_online',
        '_event_online_url',
        '_event_venue_name',
        '_event_location',
        '_event_map_url',
        '_event_location_notes',
        
        // AUDIENCE details
        '_event_audience',
        '_event_age_guidance',
        '_event_capacity',
        '_event_is_family_friendly',
        '_event_accessibility',
        
        // BOOKING details
        '_event_booking_status',
        '_event_cost',
        '_event_booking_url',
        '_event_booking_instructions',
        '_event_organiser_name',
        '_event_organiser_email',
        '_event_organiser_phone',
        '_event_organiser_contact',
    );

    foreach ( $copied_meta_keys as $key ) {
        $val = get_post_meta( $source_id, $key, true );
        if ( $val !== '' ) {
            update_post_meta( $new_post_id, $key, $val );
        }
    }

    // 5. Generate initial occurrences (No manual cancelled exceptions copied, they are fresh)
    if ( function_exists( 'weardale_platform_regenerate_event_occurrences' ) ) {
        weardale_platform_regenerate_event_occurrences( $new_post_id );
    }

    // Redirect straight to edit page for the newly duplicated draft
    wp_redirect( get_edit_post_link( $new_post_id, 'url' ) );
    exit;
}
add_action( 'admin_post_wt_duplicate_event', 'weardale_platform_handle_duplicate_event' );

/**
 * Inject filters for admin list search filters (Upcoming, Past, Recurring, One-off, Month)
 */
function weardale_platform_restrict_events_list() {
    global $typenow, $wpdb;
    if ( $typenow !== 'weardale_event' ) {
        return;
    }

    // Timeframe Filter (Upcoming vs Past)
    $state_filter = isset( $_GET['wt_state_filter'] ) ? sanitize_text_field( $_GET['wt_state_filter'] ) : '';
    ?>
    <select name="wt_state_filter">
        <option value=""><?php esc_html_e( 'All States', 'weardale-platform' ); ?></option>
        <option value="upcoming" <?php selected( $state_filter, 'upcoming' ); ?>><?php esc_html_e( 'Upcoming Only', 'weardale-platform' ); ?></option>
        <option value="past" <?php selected( $state_filter, 'past' ); ?>><?php esc_html_e( 'Past Only', 'weardale-platform' ); ?></option>
    </select>
    <?php

    // Recurrence Filter (Recurring vs One-off)
    $rec_filter = isset( $_GET['wt_rec_filter'] ) ? sanitize_text_field( $_GET['wt_rec_filter'] ) : '';
    ?>
    <select name="wt_rec_filter">
        <option value=""><?php esc_html_e( 'All Pattern Types', 'weardale-platform' ); ?></option>
        <option value="recurring" <?php selected( $rec_filter, 'recurring' ); ?>><?php esc_html_e( 'Recurring Series', 'weardale-platform' ); ?></option>
        <option value="oneoff" <?php selected( $rec_filter, 'oneoff' ); ?>><?php esc_html_e( 'One-off Events', 'weardale-platform' ); ?></option>
    </select>
    <?php

    // Month Grid Filter (Months derived from database occurrences)
    $table_occ = $wpdb->prefix . 'weardale_event_occurrences';
    $months_raw = $wpdb->get_results(
        "SELECT DISTINCT DATE_FORMAT(occurrence_start, '%Y-%m') as ym_val 
         FROM $table_occ 
         ORDER BY occurrence_start ASC",
        ARRAY_A
    );
    
    $selected_ym = isset( $_GET['wt_ym_filter'] ) ? sanitize_text_field( $_GET['wt_ym_filter'] ) : '';
    ?>
    <select name="wt_ym_filter">
        <option value=""><?php esc_html_e( 'All Months', 'weardale-platform' ); ?></option>
        <?php foreach ( $months_raw as $m ) : 
            if ( empty( $m['ym_val'] ) ) continue;
            $formatted_month = date( 'F Y', strtotime( $m['ym_val'] . '-01' ) );
        ?>
            <option value="<?php echo esc_attr( $m['ym_val'] ); ?>" <?php selected( $selected_ym, $m['ym_val'] ); ?>><?php echo esc_html( $formatted_month ); ?></option>
        <?php endforeach; ?>
    </select>
    <?php
}
add_action( 'restrict_manage_posts', 'weardale_platform_restrict_events_list', 15 );

/**
 * Filter the Admin CPT posts query according to user selection
 */
function weardale_platform_filter_admin_posts_query( $query ) {
    global $pagenow, $typenow;
    if ( ! is_admin() || $pagenow !== 'edit.php' || $typenow !== 'weardale_event' || ! $query->is_main_query() ) {
        return;
    }

    $meta_query = (array) $query->get( 'meta_query' );

    // 1. Recurring vs One-off Filter
    if ( ! empty( $_GET['wt_rec_filter'] ) ) {
        $rec = sanitize_text_field( $_GET['wt_rec_filter'] );
        if ( 'recurring' === $rec ) {
            $meta_query[] = array(
                'key'     => '_event_is_recurring',
                'value'   => '1',
                'compare' => '=',
            );
        } else {
            // Include both empty (null/not existing) and explicit '0'
            $meta_query[] = array(
                'relation' => 'OR',
                array(
                    'key'     => '_event_is_recurring',
                    'value'   => '0',
                    'compare' => '=',
                ),
                array(
                    'key'     => '_event_is_recurring',
                    'compare' => 'NOT EXISTS',
                )
            );
        }
    }

    // 2. Upcoming vs Past Filter (Requires subquery check on custom occurrences table!)
    if ( ! empty( $_GET['wt_state_filter'] ) ) {
        global $wpdb;
        $state = sanitize_text_field( $_GET['wt_state_filter'] );
        $table_occ = $wpdb->prefix . 'weardale_event_occurrences';
        $now_utc = current_time( 'mysql', 1 );
        
        if ( 'upcoming' === $state ) {
            // Find post IDs having future occurrences
            $matching_post_ids = $wpdb->get_col( $wpdb->prepare(
                "SELECT DISTINCT event_id FROM $table_occ WHERE occurrence_start >= %s",
                $now_utc
            ) );
            if ( empty( $matching_post_ids ) ) {
                $matching_post_ids = array( 0 ); // trigger empty set
            }
            $query->set( 'post__in', $matching_post_ids );
        } elseif ( 'past' === $state ) {
            // Find post IDs having NO future occurrences
            $matching_post_ids = $wpdb->get_col( $wpdb->prepare(
                "SELECT DISTINCT event_id FROM $table_occ WHERE occurrence_start >= %s",
                $now_utc
            ) );
            
            $all_post_ids = $wpdb->get_col( "SELECT DISTINCT event_id FROM $table_occ" );
            $past_ids = array_diff( $all_post_ids, $matching_post_ids );
            if ( empty( $past_ids ) ) {
                $past_ids = array( 0 );
            }
            $query->set( 'post__in', $past_ids );
        }
    }

    // 3. Year-Month Specific Filter
    if ( ! empty( $_GET['wt_ym_filter'] ) ) {
        global $wpdb;
        $ym = sanitize_text_field( $_GET['wt_ym_filter'] );
        $table_occ = $wpdb->prefix . 'weardale_event_occurrences';
        
        $month_start = "{$ym}-01 00:00:00";
        $month_end   = "{$ym}-31 23:59:59"; // raw bound
        
        $matching_ids = $wpdb->get_col( $wpdb->prepare(
            "SELECT DISTINCT event_id FROM $table_occ 
             WHERE occurrence_start >= %s AND occurrence_start <= %s",
            $month_start,
            $month_end
        ) );
        if ( empty( $matching_ids ) ) {
            $matching_ids = array( 0 );
        }
        $query->set( 'post__in', $matching_ids );
    }

    if ( ! empty( $meta_query ) ) {
        $query->set( 'meta_query', $meta_query );
    }
}
add_action( 'pre_get_posts', 'weardale_platform_filter_admin_posts_query', 15 );
