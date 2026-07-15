<?php
/**
 * Event Occurrence Storage Layer
 *
 * Handles creation and CRUD operations for the custom occurrences database table.
 *
 * @package Weardale_Platform
 * @since 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Creates or updates the custom event occurrences table.
 */
function weardale_platform_create_occurrences_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'weardale_event_occurrences';
    $charset_collate = $wpdb->get_charset_collate();

    // Note double space after PRIMARY KEY for dbDelta compliance
    $sql = "CREATE TABLE $table_name (
        occurrence_id bigint(20) NOT NULL AUTO_INCREMENT,
        event_id bigint(20) NOT NULL,
        occurrence_start datetime NOT NULL,
        occurrence_end datetime NOT NULL,
        original_start datetime NOT NULL,
        occurrence_status varchar(50) DEFAULT 'scheduled' NOT NULL,
        is_exception tinyint(1) DEFAULT 0 NOT NULL,
        created_at datetime NOT NULL,
        updated_at datetime NOT NULL,
        PRIMARY KEY  (occurrence_id),
        KEY event_id (event_id),
        KEY occurrence_start (occurrence_start),
        KEY occurrence_status (occurrence_status),
        KEY event_start_status (event_id, occurrence_start, occurrence_status)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
    update_option( 'weardale_platform_db_version', '1.0.0' );
}

/**
 * Ensures table is created if database version option doesn't exist on init.
 */
function weardale_platform_ensure_occurrences_table_exists() {
    if ( get_option( 'weardale_platform_db_version' ) !== '1.0.0' ) {
        weardale_platform_create_occurrences_table();
    }
}
add_action( 'init', 'weardale_platform_ensure_occurrences_table_exists', 5 );

/**
 * Fetches all physical occurrence records for a specific event.
 */
function weardale_platform_get_event_occurrences( $event_id ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'weardale_event_occurrences';
    
    $query = $wpdb->prepare(
        "SELECT * FROM $table_name WHERE event_id = %d ORDER BY occurrence_start ASC",
        $event_id
    );
    
    return $wpdb->get_results( $query, ARRAY_A );
}

/**
 * Retrieves a single occurrence by ID.
 */
function weardale_platform_get_occurrence( $occurrence_id ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'weardale_event_occurrences';
    
    $query = $wpdb->prepare(
        "SELECT * FROM $table_name WHERE occurrence_id = %d",
        $occurrence_id
    );
    
    return $wpdb->get_row( $query, ARRAY_A );
}

/**
 * Deletes occurrences for an event, with option to preserve manual exceptions.
 */
function weardale_platform_delete_occurrences( $event_id, $include_exceptions = false ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'weardale_event_occurrences';
    
    if ( $include_exceptions ) {
        $wpdb->delete( $table_name, array( 'event_id' => $event_id ), array( '%d' ) );
    } else {
        // Only delete standard, non-exception occurrences
        $wpdb->query( $wpdb->prepare(
            "DELETE FROM $table_name WHERE event_id = %d AND is_exception = 0",
            $event_id
        ) );
    }
}

/**
 * Inserts or updates an occurrence record.
 */
function weardale_platform_save_occurrence( $data ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'weardale_event_occurrences';
    $now = current_time( 'mysql', 1 ); // UTC
    
    $defaults = array(
        'occurrence_id'     => 0,
        'event_id'          => 0,
        'occurrence_start'  => '',
        'occurrence_end'    => '',
        'original_start'    => '',
        'occurrence_status' => 'scheduled',
        'is_exception'      => 0,
    );
    
    $args = wp_parse_args( $data, $defaults );
    
    if ( empty( $args['original_start'] ) ) {
        $args['original_start'] = $args['occurrence_start'];
    }
    
    $row_data = array(
        'event_id'          => intval( $args['event_id'] ),
        'occurrence_start'  => sanitize_text_field( $args['occurrence_start'] ),
        'occurrence_end'    => sanitize_text_field( $args['occurrence_end'] ),
        'original_start'    => sanitize_text_field( $args['original_start'] ),
        'occurrence_status' => sanitize_text_field( $args['occurrence_status'] ),
        'is_exception'      => intval( $args['is_exception'] ) ? 1 : 0,
        'updated_at'        => $now,
    );
    
    if ( ! empty( $args['occurrence_id'] ) ) {
        $wpdb->update(
            $table_name,
            $row_data,
            array( 'occurrence_id' => intval( $args['occurrence_id'] ) ),
            array( '%d', '%s', '%s', '%s', '%s', '%d', '%s' ),
            array( '%d' )
        );
        return $args['occurrence_id'];
    } else {
        $row_data['created_at'] = $now;
        $wpdb->insert(
            $table_name,
            $row_data,
            array( '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s' )
        );
        return $wpdb->insert_id;
    }
}

/**
 * Cancels a specific occurrence of an event (marking it as an exception).
 */
function weardale_platform_cancel_occurrence( $occurrence_id ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'weardale_event_occurrences';
    $now = current_time( 'mysql', 1 );
    
    $wpdb->update(
        $table_name,
        array(
            'occurrence_status' => 'cancelled',
            'is_exception'      => 1,
            'updated_at'        => $now,
        ),
        array( 'occurrence_id' => intval( $occurrence_id ) ),
        array( '%s', '%d', '%s' ),
        array( '%d' )
    );
}

/**
 * Reschedules a specific occurrence of an event.
 */
function weardale_platform_reschedule_occurrence( $occurrence_id, $new_start, $new_end ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'weardale_event_occurrences';
    $now = current_time( 'mysql', 1 );
    
    $wpdb->update(
        $table_name,
        array(
            'occurrence_start'  => sanitize_text_field( $new_start ),
            'occurrence_end'    => sanitize_text_field( $new_end ),
            'occurrence_status' => 'rescheduled',
            'is_exception'      => 1,
            'updated_at'        => $now,
        ),
        array( 'occurrence_id' => intval( $occurrence_id ) ),
        array( '%s', '%s', '%s', '%d', '%s' ),
        array( '%d' )
    );
}

/**
 * Restores a specific occurrence back to scheduled status.
 */
function weardale_platform_restore_occurrence( $occurrence_id ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'weardale_event_occurrences';
    $now = current_time( 'mysql', 1 );
    
    $occurrence = weardale_platform_get_occurrence( $occurrence_id );
    if ( ! $occurrence ) {
        return;
    }
    
    // If original start equals occurrence start, it's a simple restore.
    // If they differ, it was a rescheduled slot, so we revert to original.
    $wpdb->update(
        $table_name,
        array(
            'occurrence_start'  => $occurrence['original_start'],
            'occurrence_status' => 'scheduled',
            'is_exception'      => 0,
            'updated_at'        => $now,
        ),
        array( 'occurrence_id' => intval( $occurrence_id ) ),
        array( '%s', '%s', '%d', '%s' ),
        array( '%d' )
    );
}

/**
 * Add a custom exception occurrence to a series.
 */
function weardale_platform_add_custom_occurrence( $event_id, $start, $end ) {
    return weardale_platform_save_occurrence( array(
        'event_id'          => $event_id,
        'occurrence_start'  => $start,
        'occurrence_end'    => $end,
        'original_start'    => $start,
        'occurrence_status' => 'scheduled',
        'is_exception'      => 1,
    ) );
}

/**
 * Clears and regenerates all occurrences for an event based on its recurrence rules.
 * This is called on save_post for weardale_event.
 */
function weardale_platform_regenerate_event_occurrences( $event_id ) {
    // 1. Fetch current exceptions for this event (to preserve them)
    $occurrences = weardale_platform_get_event_occurrences( $event_id );
    $exceptions = array();
    
    foreach ( $occurrences as $occ ) {
        if ( intval( $occ['is_exception'] ) === 1 ) {
            // Index exception by original start datetime to find matches
            $exceptions[ $occ['original_start'] ] = $occ;
        }
    }
    
    // 2. Delete all standard occurrences
    weardale_platform_delete_occurrences( $event_id, false );
    
    // 3. Fetch event recurrence configurations
    $is_recurring = get_post_meta( $event_id, '_event_is_recurring', true ) === '1';
    $start_date   = get_post_meta( $event_id, '_event_date', true );
    $end_date     = get_post_meta( $event_id, '_event_end_date', true );
    $start_time   = get_post_meta( $event_id, '_event_start_time', true );
    $end_time     = get_post_meta( $event_id, '_event_end_time', true );
    
    if ( empty( $start_date ) ) {
        return;
    }
    if ( empty( $end_date ) ) {
        $end_date = $start_date;
    }
    if ( empty( $start_time ) ) {
        $start_time = '00:00:00';
    }
    if ( empty( $end_time ) ) {
        $end_time = '23:59:59';
    }
    
    $generated_slots = array();
    
    if ( ! $is_recurring ) {
        // One-off or simple multi-day range
        $generated_slots[] = array(
            'start' => $start_date . ' ' . $start_time,
            'end'   => $end_date . ' ' . $end_time,
        );
    } else {
        // Calculate recurring slots
        if ( function_exists( 'weardale_platform_calculate_recurrences' ) ) {
            $generated_slots = weardale_platform_calculate_recurrences( $event_id );
        }
    }
    
    // 4. Save generated slots while preserving exceptions
    foreach ( $generated_slots as $slot ) {
        $slot_original_start = $slot['start'];
        
        if ( isset( $exceptions[ $slot_original_start ] ) ) {
            // There is already a manual exception row in DB for this slot.
            // Leave it alone (do not insert a standard slot over it).
            continue;
        }
        
        // Otherwise, insert standard scheduled occurrence
        weardale_platform_save_occurrence( array(
            'event_id'          => $event_id,
            'occurrence_start'  => $slot['start'],
            'occurrence_end'    => $slot['end'],
            'original_start'    => $slot_original_start,
            'occurrence_status' => 'scheduled',
            'is_exception'      => 0,
        ) );
    }
}
