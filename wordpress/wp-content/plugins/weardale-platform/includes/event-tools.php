<?php
/**
 * Event Data Migration and Diagnostic Tools
 *
 * Implements admin backfill utility and event diagnostics screen.
 *
 * @package Weardale_Platform
 * @since 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the Tools Submenu under WT Events CPT menu
 */
function weardale_platform_add_tools_submenu() {
    add_submenu_page(
        'edit.php?post_type=weardale_event',
        __( 'Event Data & Diagnostics', 'weardale-platform' ),
        __( 'Event Data', 'weardale-platform' ),
        'manage_options', // Require administrator capability
        'weardale-event-tools',
        'weardale_platform_render_event_tools_page'
    );
}
add_action( 'admin_menu', 'weardale_platform_add_tools_submenu' );

/**
 * Parsers for legacy _event_time meta fields
 */
function weardale_platform_parse_legacy_time( $time_str ) {
    $start_time = '10:00:00';
    $end_time   = '12:00:00';
    
    if ( empty( $time_str ) ) {
        return array( $start_time, $end_time );
    }
    
    // Replace non-breaking spaces, check for ranges separated by dashes or "to"
    $time_str = str_replace( array('&nbsp;', '–'), array(' ', '-'), $time_str );
    $parts = explode( '-', $time_str );
    if ( count( $parts ) < 2 ) {
        $parts = explode( ' to ', $time_str );
    }
    
    if ( ! empty( $parts[0] ) ) {
        $st = trim( $parts[0] );
        $st_ts = strtotime( $st );
        if ( $st_ts !== false ) {
            $start_time = date( 'H:i:s', $st_ts );
        }
    }
    
    if ( ! empty( $parts[1] ) ) {
        $et = trim( $parts[1] );
        $et_ts = strtotime( $et );
        if ( $et_ts !== false ) {
            $end_time = date( 'H:i:s', $et_ts );
        } else {
            // Default to 2 hours after start
            $st_ts = strtotime( $start_time );
            if ( $st_ts !== false ) {
                $end_time = date( 'H:i:s', $st_ts + 7200 );
            }
        }
    } else {
        // Default to 2 hours after start
        $st_ts = strtotime( $start_time );
        if ( $st_ts !== false ) {
            $end_time = date( 'H:i:s', $st_ts + 7200 );
        }
    }
    
    return array( $start_time, $end_time );
}

/**
 * Render the Event Data Tools and Diagnostics page
 */
function weardale_platform_render_event_tools_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'weardale-platform' ) );
    }

    global $wpdb;
    $table_occ = $wpdb->prefix . 'weardale_event_occurrences';

    // 1. Process Action (Backfill)
    $action_results = null;
    if ( isset( $_POST['wt_backfill_nonce'] ) && wp_verify_nonce( $_POST['wt_backfill_nonce'], 'wt_backfill_action' ) ) {
        if ( isset( $_POST['confirm_backfill'] ) && $_POST['confirm_backfill'] === 'yes' ) {
            // Run backfill
            $migrated = array();
            $skipped = array();
            $errors = array();

            $events = get_posts( array(
                'post_type'      => 'weardale_event',
                'posts_per_page' => -1,
                'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
            ) );

            foreach ( $events as $event ) {
                $event_id = $event->ID;
                $start_date = get_post_meta( $event_id, '_event_date', true );
                
                // Check if they already have occurrences
                $count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_occ WHERE event_id = %d", $event_id ) );
                
                if ( $count > 0 ) {
                    $skipped[] = array(
                        'id'     => $event_id,
                        'title'  => $event->post_title,
                        'reason' => __( 'Already has occurrences.', 'weardale-platform' ),
                    );
                    continue;
                }

                if ( empty( $start_date ) || strtotime( $start_date ) === false ) {
                    $errors[] = array(
                        'id'    => $event_id,
                        'title' => $event->post_title,
                        'error' => __( 'Missing or invalid start date.', 'weardale-platform' ),
                    );
                    continue;
                }

                // If _event_start_time / _event_end_time are empty, let's try to populate them from legacy _event_time
                $start_time = get_post_meta( $event_id, '_event_start_time', true );
                if ( empty( $start_time ) ) {
                    $legacy_time = get_post_meta( $event_id, '_event_time', true );
                    list( $parsed_st, $parsed_et ) = weardale_platform_parse_legacy_time( $legacy_time );
                    update_post_meta( $event_id, '_event_start_time', $parsed_st );
                    update_post_meta( $event_id, '_event_end_time', $parsed_et );
                }

                // Call regenerate
                if ( function_exists( 'weardale_platform_regenerate_event_occurrences' ) ) {
                    weardale_platform_regenerate_event_occurrences( $event_id );
                    // Check if it successfully populated
                    $new_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_occ WHERE event_id = %d", $event_id ) );
                    if ( $new_count > 0 ) {
                        $migrated[] = array(
                            'id'    => $event_id,
                            'title' => $event->post_title,
                        );
                    } else {
                        $errors[] = array(
                            'id'    => $event_id,
                            'title' => $event->post_title,
                            'error' => __( 'Regeneration failed to insert rows.', 'weardale-platform' ),
                        );
                    }
                } else {
                    $errors[] = array(
                        'id'    => $event_id,
                        'title' => $event->post_title,
                        'error' => __( 'Regeneration function not found.', 'weardale-platform' ),
                    );
                }
            }

            $action_results = array(
                'migrated' => $migrated,
                'skipped'  => $skipped,
                'errors'   => $errors,
            );
        }
    }

    // 2. Scan / Diagnostics Information
    $events = get_posts( array(
        'post_type'      => 'weardale_event',
        'posts_per_page' => -1,
        'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
    ) );

    $total_scanned = count( $events );
    $missing_occurrences = 0;
    $eligible_for_migration = 0;
    $invalid_or_missing_dates = 0;

    $diagnostics = array();

    foreach ( $events as $event ) {
        $event_id = $event->ID;
        $start_date = get_post_meta( $event_id, '_event_date', true );
        $start_time = get_post_meta( $event_id, '_event_start_time', true );
        if ( empty( $start_time ) ) {
            $start_time = get_post_meta( $event_id, '_event_time', true );
        }
        
        $count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_occ WHERE event_id = %d", $event_id ) );
        
        // Find next occurrence
        $now_utc = current_time( 'mysql', 1 );
        $next_occ = $wpdb->get_var( $wpdb->prepare(
            "SELECT occurrence_start FROM $table_occ 
             WHERE event_id = %d AND occurrence_start >= %s 
             ORDER BY occurrence_start ASC LIMIT 1",
            $event_id,
            $now_utc
        ) );

        $status = 'Ready';
        $has_valid_date = ( ! empty( $start_date ) && strtotime( $start_date ) !== false );

        if ( ! $has_valid_date ) {
            $status = 'Invalid date';
            $invalid_or_missing_dates++;
        } elseif ( $count === 0 ) {
            $status = 'Missing occurrence';
            $missing_occurrences++;
            $eligible_for_migration++;
        }

        $diagnostics[] = array(
            'id'               => $event_id,
            'title'            => $event->post_title,
            'status'           => $event->post_status,
            'start_date'       => $start_date,
            'start_time'       => $start_time,
            'occ_rows'         => $count,
            'next_occ'         => $next_occ ? date('Y-m-d H:i', strtotime($next_occ)) : 'None',
            'migration_status' => $status,
        );
    }

    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Event Data & Diagnostics', 'weardale-platform' ); ?></h1>
        <p><?php esc_html_e( 'Manage the transition of Weardale Together events from legacy metadata to structured occurrences.', 'weardale-platform' ); ?></p>

        <!-- Migration Report -->
        <?php if ( $action_results ) : ?>
            <div class="notice notice-success is-dismissible" style="padding: 15px; margin-top: 15px; border-left-color: #16a34a;">
                <h3><?php esc_html_e( 'Migration Execution Complete', 'weardale-platform' ); ?></h3>
                
                <p><strong>🎉 <?php printf( esc_html__( 'Migrated: %d events.', 'weardale-platform' ), count( $action_results['migrated'] ) ); ?></strong></p>
                <?php if ( ! empty( $action_results['migrated'] ) ) : ?>
                    <ul style="list-style-type: disc; margin-left: 20px;">
                        <?php foreach ( $action_results['migrated'] as $item ) : ?>
                            <li>
                                <a href="<?php echo esc_url( get_edit_post_link( $item['id'] ) ); ?>" target="_blank">
                                    <strong><?php echo esc_html( $item['title'] ); ?></strong> (ID: <?php echo intval( $item['id'] ); ?>)
                                </a> - <?php esc_html_e( 'Occurrences generated successfully.', 'weardale-platform' ); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <p><strong>ℹ️ <?php printf( esc_html__( 'Skipped: %d events.', 'weardale-platform' ), count( $action_results['skipped'] ) ); ?></strong></p>
                <p><strong>❌ <?php printf( esc_html__( 'Errors: %d events.', 'weardale-platform' ), count( $action_results['errors'] ) ); ?></strong></p>
                <?php if ( ! empty( $action_results['errors'] ) ) : ?>
                    <ul style="list-style-type: disc; margin-left: 20px; color: #b91c1c;">
                        <?php foreach ( $action_results['errors'] as $item ) : ?>
                            <li>
                                <a href="<?php echo esc_url( get_edit_post_link( $item['id'] ) ); ?>" target="_blank" style="color: #b91c1c;">
                                    <strong><?php echo esc_html( $item['title'] ); ?></strong> (ID: <?php echo intval( $item['id'] ); ?>)
                                </a> - <?php echo esc_html( $item['error'] ); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Scan Summary Card -->
        <div class="card" style="max-width: 800px; margin-top: 20px; padding: 20px; background: #fff; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
            <h2>📊 <?php esc_html_e( 'Migration Assessment', 'weardale-platform' ); ?></h2>
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Total Events Scanned', 'weardale-platform' ); ?></th>
                        <td><strong><?php echo intval( $total_scanned ); ?></strong></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Events Missing Occurrences', 'weardale-platform' ); ?></th>
                        <td>
                            <span style="<?php echo $missing_occurrences > 0 ? 'color: #b91c1c; font-weight: bold;' : 'color: green;'; ?>">
                                <?php echo intval( $missing_occurrences ); ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Eligible for Backfill Migration', 'weardale-platform' ); ?></th>
                        <td><strong><?php echo intval( $eligible_for_migration ); ?></strong></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Invalid or Missing Dates', 'weardale-platform' ); ?></th>
                        <td>
                            <span style="<?php echo $invalid_or_missing_dates > 0 ? 'color: #d97706; font-weight: bold;' : 'color: green;'; ?>">
                                <?php echo intval( $invalid_or_missing_dates ); ?>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Backfill Tool Trigger -->
            <?php if ( $eligible_for_migration > 0 ) : ?>
                <form method="post" style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #eee;">
                    <?php wp_nonce_field( 'wt_backfill_action', 'wt_backfill_nonce' ); ?>
                    <p>
                        <label>
                            <input type="checkbox" name="confirm_backfill" value="yes" required>
                            <strong><?php esc_html_e( 'I confirm that I want to automatically backfill occurrences for all eligible events.', 'weardale-platform' ); ?></strong>
                        </label>
                    </p>
                    <p>
                        <input type="submit" class="button button-primary button-large" value="<?php esc_attr_e( 'Generate missing occurrences', 'weardale-platform' ); ?>">
                    </p>
                </form>
            <?php else : ?>
                <div style="margin-top: 20px; padding: 12px; background-color: #f0fdf4; color: #15803d; border-left: 4px solid #16a34a; font-weight: 500;">
                    ✅ <?php esc_html_e( 'All active events have up-to-date occurrence rows. No backfill migration is necessary.', 'weardale-platform' ); ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Diagnostics Registry -->
        <h2 style="margin-top: 40px;"><?php esc_html_e( 'System Registry & Diagnostics', 'weardale-platform' ); ?></h2>
        <table class="wp-list-table widefat fixed striped table-view-list" style="margin-top: 15px;">
            <thead>
                <tr>
                    <th scope="col" style="width: 80px;"><?php esc_html_e( 'ID', 'weardale-platform' ); ?></th>
                    <th scope="col"><?php esc_html_e( 'Event Title', 'weardale-platform' ); ?></th>
                    <th scope="col" style="width: 100px;"><?php esc_html_e( 'Status', 'weardale-platform' ); ?></th>
                    <th scope="col" style="width: 120px;"><?php esc_html_e( 'Start Date', 'weardale-platform' ); ?></th>
                    <th scope="col" style="width: 120px;"><?php esc_html_e( 'Time Config', 'weardale-platform' ); ?></th>
                    <th scope="col" style="width: 100px; text-align: center;"><?php esc_html_e( 'Occ. Rows', 'weardale-platform' ); ?></th>
                    <th scope="col" style="width: 140px;"><?php esc_html_e( 'Next Occurrence', 'weardale-platform' ); ?></th>
                    <th scope="col" style="width: 160px;"><?php esc_html_e( 'Migration Status', 'weardale-platform' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ( ! empty( $diagnostics ) ) : ?>
                    <?php foreach ( $diagnostics as $row ) : ?>
                        <?php 
                        $status_badge_style = 'background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;';
                        if ( $row['migration_status'] === 'Missing occurrence' ) {
                            $status_badge_style = 'background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5;';
                        } elseif ( $row['migration_status'] === 'Invalid date' ) {
                            $status_badge_style = 'background: #fffbeb; color: #92400e; border: 1px solid #fde68a;';
                        }
                        ?>
                        <tr>
                            <td><?php echo intval( $row['id'] ); ?></td>
                            <td>
                                <strong><a href="<?php echo esc_url( get_edit_post_link( $row['id'] ) ); ?>"><?php echo esc_html( $row['title'] ); ?></a></strong>
                            </td>
                            <td><span class="post-state"><?php echo esc_html( ucfirst( $row['status'] ) ); ?></span></td>
                            <td><code><?php echo esc_html( $row['start_date'] ?: 'None' ); ?></code></td>
                            <td><code><?php echo esc_html( $row['start_time'] ?: 'None' ); ?></code></td>
                            <td style="text-align: center; font-weight: bold;"><?php echo intval( $row['occ_rows'] ); ?></td>
                            <td><code><?php echo esc_html( $row['next_occ'] ); ?></code></td>
                            <td>
                                <span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; <?php echo esc_attr( $status_badge_style ); ?>">
                                    <?php echo esc_html( $row['migration_status'] ); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px;"><?php esc_html_e( 'No events found.', 'weardale-platform' ); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
