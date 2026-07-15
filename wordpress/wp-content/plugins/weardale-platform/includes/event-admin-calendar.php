<?php
/**
 * Event Admin Calendar View
 *
 * Implements the administrative calendar subpage, quick creation form, and exception management.
 *
 * @package Weardale_Platform
 * @since 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the Calendar Submenu under WT Events CPT menu
 */
function weardale_platform_add_calendar_submenu() {
    add_submenu_page(
        'edit.php?post_type=weardale_event',
        __( 'Event Calendar', 'weardale-platform' ),
        __( 'Calendar', 'weardale-platform' ),
        'edit_posts',
        'weardale-event-calendar',
        'weardale_platform_render_admin_calendar_page'
    );
}
add_action( 'admin_menu', 'weardale_platform_add_calendar_submenu' );

/**
 * Render the Admin Calendar Dashboard
 */
function weardale_platform_render_admin_calendar_page() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'weardale-platform' ) );
    }

    // 1. Process administrative quick actions (Exceptions & Quick Create)
    weardale_platform_process_calendar_actions();

    // 2. Calculate current month/year view bounds
    $current_month_input = isset( $_GET['view_month'] ) ? sanitize_text_field( $_GET['view_month'] ) : '';
    if ( preg_match( '/^\d{4}-\d{2}$/', $current_month_input ) ) {
        list( $year, $month ) = explode( '-', $current_month_input );
        $year = intval( $year );
        $month = intval( $month );
    } else {
        $year = intval( current_time( 'Y' ) );
        $month = intval( current_time( 'n' ) );
    }

    // Calculate dates for previous, next, and today controls
    $prev_year = ( $month === 1 ) ? ( $year - 1 ) : $year;
    $prev_month = ( $month === 1 ) ? 12 : ( $month - 1 );
    $prev_month_str = sprintf( '%04d-%02d', $prev_year, $prev_month );

    $next_year = ( $month === 12 ) ? ( $year + 1 ) : $year;
    $next_month = ( $month === 12 ) ? 1 : ( $month + 1 );
    $next_month_str = sprintf( '%04d-%02d', $next_year, $next_month );

    $today_str = current_time( 'Y-m' );
    $current_month_display = date( 'F Y', mktime( 0, 0, 0, $month, 1, $year ) );

    // Fetch occurrences inside this month's grid bounds
    $start_of_month = sprintf( '%04d-%02d-01 00:00:00', $year, $month );
    $days_in_month = cal_days_in_month( CAL_GREGORIAN, $month, $year );
    $end_of_month = sprintf( '%04d-%02d-%02d 23:59:59', $year, $month, $days_in_month );

    // Query database directly
    global $wpdb;
    $table_occ = $wpdb->prefix . 'weardale_event_occurrences';
    $table_posts = $wpdb->prefix . 'posts';
    
    $query = $wpdb->prepare(
        "SELECT o.*, p.post_title, p.post_status 
         FROM $table_occ o
         INNER JOIN $table_posts p ON o.event_id = p.ID
         WHERE p.post_status IN ('publish', 'draft') 
         AND o.occurrence_start >= %s 
         AND o.occurrence_start <= %s
         ORDER BY o.occurrence_start ASC",
        $start_of_month,
        $end_of_month
    );
    $occurrences_raw = $wpdb->get_results( $query, ARRAY_A );

    // Group occurrences by day
    $occurrences_by_day = array();
    for ( $d = 1; $d <= $days_in_month; $d++ ) {
        $occurrences_by_day[ $d ] = array();
    }
    foreach ( $occurrences_raw as $occ ) {
        $occ_day = intval( date( 'j', strtotime( $occ['occurrence_start'] ) ) );
        $occurrences_by_day[ $occ_day ][] = $occ;
    }

    // Determine grid start day (e.g. 0 for Sunday, 1 for Monday...)
    $first_day_timestamp = mktime( 0, 0, 0, $month, 1, $year );
    $first_day_of_week = intval( date( 'w', $first_day_timestamp ) ); // 0 = Sunday, 1 = Monday
    // Shift so Monday is index 0:
    $start_offset = ( $first_day_of_week === 0 ) ? 6 : ( $first_day_of_week - 1 );
    ?>
    <style>
        .wt-cal-wrap {
            margin: 20px 20px 0 0;
            max-width: 1200px;
        }
        .wt-cal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 1rem 1.5rem;
            border: 1px solid #ccd0d4;
            border-bottom: none;
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
        }
        .wt-cal-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }
        .wt-cal-nav {
            display: flex;
            gap: 0.5rem;
        }
        .wt-grid-header {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            background: #3b5c3a;
            color: #fff;
            text-align: center;
            font-weight: 600;
            border: 1px solid #3b5c3a;
        }
        .wt-grid-header-day {
            padding: 0.75rem 0.5rem;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
        }
        .wt-grid-body {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            grid-auto-rows: minmax(110px, auto);
            background: #ccd0d4;
            border: 1px solid #ccd0d4;
            border-top: none;
            gap: 1px;
        }
        .wt-grid-cell {
            background: #fff;
            padding: 0.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            transition: background 0.1s ease;
        }
        .wt-grid-cell.inactive {
            background: #f0f0f1;
            color: #8c8f94;
        }
        .wt-grid-cell-num {
            font-weight: 700;
            font-size: 0.95rem;
            color: #334155;
            margin-bottom: 2px;
        }
        .wt-grid-cell-today {
            background: rgba(107, 143, 94, 0.08) !important;
            border: 2px solid #3b5c3a !important;
            margin: -1px;
        }
        .wt-cal-occ {
            padding: 0.25rem 0.4rem;
            border-radius: 3px;
            font-size: 0.75rem;
            line-height: 1.2;
            font-weight: 500;
            text-decoration: none;
            display: block;
            border-left: 3px solid #64748b;
            background: #f1f5f9;
            color: #1e293b;
            margin-bottom: 2px;
            transition: transform 0.1s ease;
        }
        .wt-cal-occ:hover {
            transform: translateX(2px);
            background: #e2e8f0;
        }
        .wt-cal-occ.strand-cafe {
            border-left-color: #0d9488;
            background: #f0fdfa;
        }
        .wt-cal-occ.strand-youth {
            border-left-color: #2563eb;
            background: #eff6ff;
        }
        .wt-cal-occ.strand-creative {
            border-left-color: #d97706;
            background: #fffbeb;
        }
        .wt-cal-occ.strand-shoots {
            border-left-color: #16a34a;
            background: #f0fdf4;
        }
        .wt-cal-occ.cancelled {
            text-decoration: line-through;
            border-left-color: #dc2626 !important;
            background: #fef2f2 !important;
            opacity: 0.65;
        }
        .wt-cal-occ.draft {
            border-left-style: dashed;
            background: #fafaf9;
            color: #57534e;
        }
        .wt-cal-occ-time {
            font-weight: 700;
            font-size: 0.7rem;
            display: block;
            color: #475569;
        }
        .wt-cal-occ-title {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }
        .wt-admin-card-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        .wt-admin-card {
            background: #fff;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            padding: 1.25rem;
        }
        .wt-admin-card h3 {
            margin-top: 0;
            font-size: 1.1rem;
            color: #1e293b;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        /* Accessible Lists Styles */
        .wt-accessibility-list {
            margin-top: 10px;
            background: #fff;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            padding: 1.25rem;
        }
        .wt-occ-action-btn {
            font-size: 0.7rem;
            padding: 0.15rem 0.35rem;
            border-radius: 3px;
            border: 1px solid #ccd0d4;
            background: #fff;
            color: #475569;
            text-decoration: none;
            cursor: pointer;
            margin-left: 0.25rem;
            display: inline-block;
        }
        .wt-occ-action-btn:hover {
            border-color: #94a3b8;
            color: #1e293b;
        }
        .wt-occ-action-btn.cancel {
            color: #dc2626;
            border-color: #fca5a5;
        }
        .wt-occ-action-btn.cancel:hover {
            background: #fef2f2;
        }
        .wt-occ-action-btn.restore {
            color: #16a34a;
            border-color: #86efac;
        }
        .wt-occ-action-btn.restore:hover {
            background: #f0fdf4;
        }
    </style>

    <div class="wrap wt-cal-wrap">
        <h1 class="wp-heading-inline">🗓️ <?php esc_html_e( 'Weardale Event Calendar Dashboard', 'weardale-platform' ); ?></h1>
        <hr class="wp-header-end">

        <!-- Display Session Admin Notices -->
        <?php settings_errors( 'weardale_calendar_notices' ); ?>

        <!-- Month Navigation header bar -->
        <div class="wt-cal-header">
            <h2 class="wt-cal-title"><?php echo esc_html( $current_month_display ); ?></h2>
            <div class="wt-cal-nav">
                <a href="<?php echo admin_url('edit.php?post_type=weardale_event&page=weardale-event-calendar&view_month=' . $prev_month_str); ?>" class="button" aria-label="Previous Month"><?php esc_html_e( '« Previous', 'weardale-platform' ); ?></a>
                <a href="<?php echo admin_url('edit.php?post_type=weardale_event&page=weardale-event-calendar&view_month=' . $today_str); ?>" class="button" aria-label="Current Month"><?php esc_html_e( 'Today', 'weardale-platform' ); ?></a>
                <a href="<?php echo admin_url('edit.php?post_type=weardale_event&page=weardale-event-calendar&view_month=' . $next_month_str); ?>" class="button" aria-label="Next Month"><?php esc_html_e( 'Next »', 'weardale-platform' ); ?></a>
            </div>
        </div>

        <!-- 7-Column Grid Headers -->
        <div class="wt-grid-header" role="row">
            <div class="wt-grid-header-day" role="columnheader"><?php esc_html_e( 'Mon', 'weardale-platform' ); ?></div>
            <div class="wt-grid-header-day" role="columnheader"><?php esc_html_e( 'Tue', 'weardale-platform' ); ?></div>
            <div class="wt-grid-header-day" role="columnheader"><?php esc_html_e( 'Wed', 'weardale-platform' ); ?></div>
            <div class="wt-grid-header-day" role="columnheader"><?php esc_html_e( 'Thu', 'weardale-platform' ); ?></div>
            <div class="wt-grid-header-day" role="columnheader"><?php esc_html_e( 'Fri', 'weardale-platform' ); ?></div>
            <div class="wt-grid-header-day" role="columnheader"><?php esc_html_e( 'Sat', 'weardale-platform' ); ?></div>
            <div class="wt-grid-header-day" role="columnheader"><?php esc_html_e( 'Sun', 'weardale-platform' ); ?></div>
        </div>

        <!-- Grid Body -->
        <div class="wt-grid-body">
            <?php
            // 1. Output leading empty/inactive days
            for ( $i = 0; $i < $start_offset; $i++ ) {
                echo '<div class="wt-grid-cell inactive"></div>';
            }

            // 2. Output days of the month
            $today_day = intval( current_time( 'j' ) );
            $today_month = intval( current_time( 'n' ) );
            $today_year = intval( current_time( 'Y' ) );

            for ( $day = 1; $day <= $days_in_month; $day++ ) {
                $is_today = ( $day === $today_day && $month === $today_month && $year === $today_year );
                $today_class = $is_today ? 'wt-grid-cell-today' : '';
                
                echo '<div class="wt-grid-cell ' . esc_attr( $today_class ) . '">';
                echo '<div class="wt-grid-cell-num">' . esc_html( $day ) . '</div>';
                
                // Print occurrences for this day
                if ( isset( $occurrences_by_day[ $day ] ) && ! empty( $occurrences_by_day[ $day ] ) ) {
                    foreach ( $occurrences_by_day[ $day ] as $occ ) {
                        $time_str = date( 'H:i', strtotime( $occ['occurrence_start'] ) );
                        $status_class = ( $occ['occurrence_status'] === 'cancelled' ) ? 'cancelled' : '';
                        $draft_class = ( $occ['post_status'] === 'draft' ) ? 'draft' : '';
                        
                        // Determine strand class
                        $strand_class = '';
                        $terms = wp_get_post_terms( $occ['event_id'], 'strand' );
                        if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
                            $strand_class = 'strand-' . esc_attr( $terms[0]->slug );
                        }
                        
                        $edit_url = get_edit_post_link( $occ['event_id'] );
                        
                        echo '<div class="wt-cal-occ ' . esc_attr( $strand_class . ' ' . $status_class . ' ' . $draft_class ) . '" title="' . esc_attr( $occ['post_title'] ) . '">';
                        echo '<span class="wt-cal-occ-time">' . esc_html( $time_str ) . '</span>';
                        echo '<a href="' . esc_url( $edit_url ) . '" class="wt-cal-occ-title" style="color: inherit; text-decoration: none;">' . esc_html( $occ['post_title'] ) . '</a>';
                        echo '</div>';
                    }
                }
                
                echo '</div>';
            }

            // 3. Output trailing inactive days to fill 7x6 standard grid (42 cells total)
            $total_cells = $start_offset + $days_in_month;
            $remaining_cells = ( $total_cells % 7 === 0 ) ? 0 : ( 7 - ( $total_cells % 7 ) );
            for ( $i = 0; $i < $remaining_cells; $i++ ) {
                echo '<div class="wt-grid-cell inactive"></div>';
            }
            ?>
        </div>

        <div class="wt-admin-card-row">
            
            <!-- Accessible Agenda & Exceptions Management Panel -->
            <div class="wt-admin-card">
                <h3>🗓️ <?php esc_html_e( 'Accessible Agenda & Exceptions Panel', 'weardale-platform' ); ?></h3>
                <p class="description"><?php esc_html_e( 'Manage specific dates inside this month independently. Click Cancel or Reschedule to create specific series exceptions without breaking the parent schedule.', 'weardale-platform' ); ?></p>
                
                <table class="wp-list-table widefat fixed striped" style="margin-top: 1rem;">
                    <thead>
                        <tr>
                            <th style="width: 20%;"><?php esc_html_e( 'Date & Time', 'weardale-platform' ); ?></th>
                            <th style="width: 45%;"><?php esc_html_e( 'Event Title', 'weardale-platform' ); ?></th>
                            <th style="width: 15%;"><?php esc_html_e( 'Status', 'weardale-platform' ); ?></th>
                            <th style="width: 20%;"><?php esc_html_e( 'Administrative Action', 'weardale-platform' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $has_agenda = false;
                        foreach ( $occurrences_by_day as $day_num => $day_occs ) {
                            if ( empty( $day_occs ) ) {
                                continue;
                            }
                            $has_agenda = true;
                            foreach ( $day_occs as $occ ) {
                                $formatted_time = date( 'M d (D), H:i', strtotime( $occ['occurrence_start'] ) );
                                $edit_link = get_edit_post_link( $occ['event_id'] );
                                ?>
                                <tr>
                                    <td><strong><?php echo esc_html( $formatted_time ); ?></strong></td>
                                    <td>
                                        <a href="<?php echo esc_url( $edit_link ); ?>" style="font-weight: 600; text-decoration: none;"><?php echo esc_html( $occ['post_title'] ); ?></a>
                                        <?php if ( $occ['post_status'] === 'draft' ) : ?>
                                            <span style="font-size: 0.75rem; color:#854d0e; background:#fef9c3; padding: 0.1rem 0.3rem; border-radius:3px; margin-left: 5px;"><?php esc_html_e( 'Draft', 'weardale-platform' ); ?></span>
                                        <?php endif; ?>
                                        <?php if ( intval($occ['is_exception']) === 1 ) : ?>
                                            <span style="font-size: 0.75rem; color:#1e40af; background:#dbeafe; padding: 0.1rem 0.3rem; border-radius:3px; margin-left: 5px;"><?php esc_html_e( 'Exception', 'weardale-platform' ); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ( $occ['occurrence_status'] === 'cancelled' ) : ?>
                                            <span style="color:#b91c1c; font-weight:bold; font-size:0.8rem;">❌ <?php esc_html_e( 'CANCELLED', 'weardale-platform' ); ?></span>
                                        <?php elseif ( $occ['occurrence_status'] === 'rescheduled' ) : ?>
                                            <span style="color:#1d4ed8; font-weight:bold; font-size:0.8rem;">🔁 <?php esc_html_e( 'RESCHEDULED', 'weardale-platform' ); ?></span>
                                        <?php else : ?>
                                            <span style="color:#047857; font-weight:bold; font-size:0.8rem;">✅ <?php esc_html_e( 'Scheduled', 'weardale-platform' ); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ( $occ['occurrence_status'] === 'cancelled' ) : ?>
                                            <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=wt_occ_restore&occ_id=' . $occ['occurrence_id'] . '&redirect_month=' . sprintf('%04d-%02d', $year, $month) ), 'wt_occ_action_nonce' ) ); ?>" class="wt-occ-action-btn restore"><?php esc_html_e( 'Restore', 'weardale-platform' ); ?></a>
                                        <?php else : ?>
                                            <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=wt_occ_cancel&occ_id=' . $occ['occurrence_id'] . '&redirect_month=' . sprintf('%04d-%02d', $year, $month) ), 'wt_occ_action_nonce' ) ); ?>" class="wt-occ-action-btn cancel" onclick="return confirm('<?php echo esc_js( __( 'Are you sure you want to cancel this single session occurrence?', 'weardale-platform' ) ); ?>');"><?php esc_html_e( 'Cancel Slot', 'weardale-platform' ); ?></a>
                                            
                                            <!-- Reschedule trigger -->
                                            <button type="button" class="wt-occ-action-btn" onclick="weardale_prompt_reschedule('<?php echo esc_js($occ['occurrence_id']); ?>', '<?php echo esc_js(date('Y-m-d', strtotime($occ['occurrence_start']))); ?>', '<?php echo esc_js(date('H:i', strtotime($occ['occurrence_start']))); ?>')"><?php esc_html_e( 'Reschedule', 'weardale-platform' ); ?></button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        if ( ! $has_agenda ) :
                        ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 2rem; color: #64748b; font-style: italic;">
                                    <?php esc_html_e( 'No active community events scheduled for this month.', 'weardale-platform' ); ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Simplified Quick Create Panel -->
            <div class="wt-admin-card" style="align-self: start;">
                <h3>⚡ <?php esc_html_e( 'Quick-Create Event (Draft)', 'weardale-platform' ); ?></h3>
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                    <input type="hidden" name="action" value="wt_quick_create_event">
                    <?php wp_nonce_field( 'wt_quick_create_nonce', 'wt_quick_create_nonce_field' ); ?>
                    <input type="hidden" name="redirect_month" value="<?php echo sprintf('%04d-%02d', $year, $month); ?>">

                    <div style="margin-bottom: 0.75rem;">
                        <label for="qc_title" style="display:block; font-weight:600; margin-bottom: 4px;"><?php esc_html_e( 'Event Title *', 'weardale-platform' ); ?></label>
                        <input type="text" id="qc_title" name="qc_title" class="widefat" required placeholder="e.g. Clay Crafting Drop-in">
                    </div>

                    <div style="margin-bottom: 0.75rem;">
                        <label for="qc_date" style="display:block; font-weight:600; margin-bottom: 4px;"><?php esc_html_e( 'Event Date *', 'weardale-platform' ); ?></label>
                        <input type="date" id="qc_date" name="qc_date" class="widefat" required value="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div style="display:flex; gap: 10px; margin-bottom: 0.75rem;">
                        <div style="flex: 1;">
                            <label for="qc_start" style="display:block; font-weight:600; margin-bottom: 4px;"><?php esc_html_e( 'Start Time', 'weardale-platform' ); ?></label>
                            <input type="time" id="qc_start" name="qc_start" class="widefat" value="10:00">
                        </div>
                        <div style="flex: 1;">
                            <label for="qc_end" style="display:block; font-weight:600; margin-bottom: 4px;"><?php esc_html_e( 'End Time', 'weardale-platform' ); ?></label>
                            <input type="time" id="qc_end" name="qc_end" class="widefat" value="12:00">
                        </div>
                    </div>

                    <div style="margin-bottom: 1.25rem;">
                        <label for="qc_strand" style="display:block; font-weight:600; margin-bottom: 4px;"><?php esc_html_e( 'Strand Taxonomy *', 'weardale-platform' ); ?></label>
                        <select id="qc_strand" name="qc_strand" class="widefat" required>
                            <option value=""><?php esc_html_e( '-- Choose a Strand --', 'weardale-platform' ); ?></option>
                            <?php
                            $strands = get_terms( array( 'taxonomy' => 'strand', 'hide_empty' => false ) );
                            foreach ( $strands as $str ) {
                                echo '<option value="' . esc_attr( $str->slug ) . '">' . esc_html( $str->name ) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <button type="submit" class="button button-primary widefat" style="height: 38px; line-height: 36px; font-weight: 600; font-size: 0.95rem; background: #3b5c3a; border-color: #3b5c3a; text-shadow: none; box-shadow: none;">
                        ➕ <?php esc_html_e( 'Add Quick Event Draft', 'weardale-platform' ); ?>
                    </button>
                </form>
            </div>

        </div>

    </div>

    <!-- Hidden Form for processing Rescheduling prompt -->
    <form id="wt_reschedule_form" method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="display:none;">
        <input type="hidden" name="action" value="wt_occ_reschedule">
        <?php wp_nonce_field( 'wt_occ_action_nonce', 'wt_occ_nonce_field' ); ?>
        <input type="hidden" name="occ_id" id="rs_occ_id">
        <input type="hidden" name="new_date" id="rs_new_date">
        <input type="hidden" name="new_start" id="rs_new_start">
        <input type="hidden" name="redirect_month" value="<?php echo sprintf('%04d-%02d', $year, $month); ?>">
    </form>

    <script>
        function weardale_prompt_reschedule(occId, currentDate, currentTime) {
            const newDate = prompt("<?php echo esc_js( __( 'Enter the new date (YYYY-MM-DD):', 'weardale-platform' ) ); ?>", currentDate);
            if (!newDate) return;
            const newTime = prompt("<?php echo esc_js( __( 'Enter the new start time (HH:MM):', 'weardale-platform' ) ); ?>", currentTime);
            if (!newTime) return;

            document.getElementById('rs_occ_id').value = occId;
            document.getElementById('rs_new_date').value = newDate;
            document.getElementById('rs_new_start').value = newTime;
            document.getElementById('wt_reschedule_form').submit();
        }
    </script>
    <?php
}

/**
 * Handle administrative POST requests securely (Nonces and edit capabilities checked)
 */
function weardale_platform_process_calendar_actions() {
    // Actions are processed by POST/GET handlers defined below. We keep this function as a safety hook.
}

/**
 * Hook for cancelling a single occurrence exception
 */
function weardale_platform_admin_cancel_occ_handler() {
    if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'wt_occ_action_nonce' ) ) {
        wp_die( esc_html__( 'Security check failed.', 'weardale-platform' ) );
    }
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( esc_html__( 'You do not have permission to edit events.', 'weardale-platform' ) );
    }

    $occ_id = isset( $_GET['occ_id'] ) ? intval( $_GET['occ_id'] ) : 0;
    if ( $occ_id ) {
        weardale_platform_cancel_occurrence( $occ_id );
        add_settings_error( 'weardale_calendar_notices', 'occ_cancelled', __( 'Single session occurrence cancelled successfully.', 'weardale-platform' ), 'success' );
    }

    $redirect = admin_url( 'edit.php?post_type=weardale_event&page=weardale-event-calendar' );
    if ( ! empty( $_GET['redirect_month'] ) ) {
        $redirect = add_query_arg( 'view_month', sanitize_text_field( $_GET['redirect_month'] ), $redirect );
    }
    
    set_transient( 'settings_errors', get_settings_errors( 'weardale_calendar_notices' ), 30 );
    wp_redirect( $redirect );
    exit;
}
add_action( 'admin_post_wt_occ_cancel', 'weardale_platform_admin_cancel_occ_handler' );

/**
 * Hook for restoring a single occurrence exception
 */
function weardale_platform_admin_restore_occ_handler() {
    if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'wt_occ_action_nonce' ) ) {
        wp_die( esc_html__( 'Security check failed.', 'weardale-platform' ) );
    }
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( esc_html__( 'You do not have permission to edit events.', 'weardale-platform' ) );
    }

    $occ_id = isset( $_GET['occ_id'] ) ? intval( $_GET['occ_id'] ) : 0;
    if ( $occ_id ) {
        weardale_platform_restore_occurrence( $occ_id );
        add_settings_error( 'weardale_calendar_notices', 'occ_restored', __( 'Single session occurrence restored successfully.', 'weardale-platform' ), 'success' );
    }

    $redirect = admin_url( 'edit.php?post_type=weardale_event&page=weardale-event-calendar' );
    if ( ! empty( $_GET['redirect_month'] ) ) {
        $redirect = add_query_arg( 'view_month', sanitize_text_field( $_GET['redirect_month'] ), $redirect );
    }

    set_transient( 'settings_errors', get_settings_errors( 'weardale_calendar_notices' ), 30 );
    wp_redirect( $redirect );
    exit;
}
add_action( 'admin_post_wt_occ_restore', 'weardale_platform_admin_restore_occ_handler' );

/**
 * Hook for rescheduling a single occurrence exception
 */
function weardale_platform_admin_reschedule_occ_handler() {
    if ( ! isset( $_POST['wt_occ_nonce_field'] ) || ! wp_verify_nonce( $_POST['wt_occ_nonce_field'], 'wt_occ_action_nonce' ) ) {
        wp_die( esc_html__( 'Security check failed.', 'weardale-platform' ) );
    }
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( esc_html__( 'You do not have permission to edit events.', 'weardale-platform' ) );
    }

    $occ_id   = isset( $_POST['occ_id'] ) ? intval( $_POST['occ_id'] ) : 0;
    $new_date = isset( $_POST['new_date'] ) ? sanitize_text_field( $_POST['new_date'] ) : '';
    $new_time = isset( $_POST['new_start'] ) ? sanitize_text_field( $_POST['new_start'] ) : '';

    if ( $occ_id && ! empty( $new_date ) && ! empty( $new_time ) ) {
        $occ = weardale_platform_get_occurrence( $occ_id );
        if ( $occ ) {
            // Determine duration to calculate end datetime
            $duration = strtotime( $occ['occurrence_end'] ) - strtotime( $occ['occurrence_start'] );
            if ( $duration < 0 ) {
                $duration = 0;
            }
            $start_dt = "{$new_date} {$new_time}:00";
            $end_dt = date( 'Y-m-d H:i:s', strtotime( $start_dt ) + $duration );
            
            weardale_platform_reschedule_occurrence( $occ_id, $start_dt, $end_dt );
            add_settings_error( 'weardale_calendar_notices', 'occ_rescheduled', __( 'Single session occurrence rescheduled successfully.', 'weardale-platform' ), 'success' );
        }
    }

    $redirect = admin_url( 'edit.php?post_type=weardale_event&page=weardale-event-calendar' );
    if ( ! empty( $_POST['redirect_month'] ) ) {
        $redirect = add_query_arg( 'view_month', sanitize_text_field( $_POST['redirect_month'] ), $redirect );
    }

    set_transient( 'settings_errors', get_settings_errors( 'weardale_calendar_notices' ), 30 );
    wp_redirect( $redirect );
    exit;
}
add_action( 'admin_post_wt_occ_reschedule', 'weardale_platform_admin_reschedule_occ_handler' );

/**
 * Hook for handling Quick Create event
 */
function weardale_platform_admin_quick_create_handler() {
    if ( ! isset( $_POST['wt_quick_create_nonce_field'] ) || ! wp_verify_nonce( $_POST['wt_quick_create_nonce_field'], 'wt_quick_create_nonce' ) ) {
        wp_die( esc_html__( 'Security check failed.', 'weardale-platform' ) );
    }
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( esc_html__( 'You do not have permission to create events.', 'weardale-platform' ) );
    }

    $title  = isset( $_POST['qc_title'] ) ? sanitize_text_field( $_POST['qc_title'] ) : '';
    $date   = isset( $_POST['qc_date'] ) ? sanitize_text_field( $_POST['qc_date'] ) : '';
    $start  = isset( $_POST['qc_start'] ) ? sanitize_text_field( $_POST['qc_start'] ) : '10:00';
    $end    = isset( $_POST['qc_end'] ) ? sanitize_text_field( $_POST['qc_end'] ) : '12:00';
    $strand = isset( $_POST['qc_strand'] ) ? sanitize_text_field( $_POST['qc_strand'] ) : '';

    if ( ! empty( $title ) && ! empty( $date ) && ! empty( $strand ) ) {
        // Create draft post
        $post_id = wp_insert_post( array(
            'post_title'  => $title,
            'post_status' => 'draft',
            'post_type'   => 'weardale_event',
        ) );

        if ( ! is_wp_error( $post_id ) ) {
            // Apply strand term
            $term = get_term_by( 'slug', $strand, 'strand' );
            if ( $term ) {
                wp_set_post_terms( $post_id, array( $term->term_id ), 'strand' );
            }

            // Save essential meta
            update_post_meta( $post_id, '_event_date', $date );
            update_post_meta( $post_id, '_event_end_date', $date );
            update_post_meta( $post_id, '_event_start_time', $start );
            update_post_meta( $post_id, '_event_end_time', $end );
            
            $time_text = date( 'g:i A', strtotime( $start ) ) . ' - ' . date( 'g:i A', strtotime( $end ) );
            update_post_meta( $post_id, '_event_time', $time_text );
            update_post_meta( $post_id, '_event_is_recurring', '0' );
            update_post_meta( $post_id, '_event_booking_status', 'no_booking_required' );
            update_post_meta( $post_id, '_event_cost', __( 'Free', 'weardale-platform' ) );

            // Generate initial occurrence
            if ( function_exists( 'weardale_platform_regenerate_event_occurrences' ) ) {
                weardale_platform_regenerate_event_occurrences( $post_id );
            }

            // Redirect user directly to editing this brand new draft!
            wp_redirect( get_edit_post_link( $post_id, 'url' ) );
            exit;
        }
    }

    $redirect = admin_url( 'edit.php?post_type=weardale_event&page=weardale-event-calendar' );
    if ( ! empty( $_POST['redirect_month'] ) ) {
        $redirect = add_query_arg( 'view_month', sanitize_text_field( $_POST['redirect_month'] ), $redirect );
    }
    wp_redirect( $redirect );
    exit;
}
add_action( 'admin_post_wt_quick_create_event', 'weardale_platform_admin_quick_create_handler' );

/**
 * Display stored settings errors on Admin Calendar screen load
 */
function weardale_platform_load_stored_calendar_errors() {
    $errors = get_transient( 'settings_errors' );
    if ( $errors ) {
        delete_transient( 'settings_errors' );
        global $wp_settings_errors;
        $wp_settings_errors = array_merge( (array) $wp_settings_errors, $errors );
    }
}
add_action( 'admin_init', 'weardale_platform_load_stored_calendar_errors' );
