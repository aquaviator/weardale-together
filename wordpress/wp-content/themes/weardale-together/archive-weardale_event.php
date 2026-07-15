<?php
/**
 * The template for displaying Weardale Events Archive ("What's On").
 *
 * Implements a unified List / Month-Calendar frontend switcher with strand badges
 * and keyboard-accessible popup tooltips.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.1.0
 */

get_header();

// 1. Setup view toggle (List vs Calendar)
$current_view = isset( $_GET['view'] ) ? sanitize_text_field( $_GET['view'] ) : 'list';
if ( ! in_array( $current_view, array( 'list', 'calendar' ), true ) ) {
    $current_view = 'list';
}

// 2. Determine strand taxonomy term
$current_strand = get_query_var( 'strand' );
$strand_term = null;
if ( ! empty( $current_strand ) ) {
    $strand_term = get_term_by( 'slug', $current_strand, 'strand' );
}

// 3. Month bounds calculation for Calendar View
$month_input = isset( $_GET['month'] ) ? sanitize_text_field( $_GET['month'] ) : '';
if ( preg_match( '/^\d{4}-\d{2}$/', $month_input ) ) {
    list( $year, $month ) = explode( '-', $month_input );
    $year = intval( $year );
    $month = intval( $month );
} else {
    $year = intval( current_time( 'Y' ) );
    $month = intval( current_time( 'n' ) );
}

$prev_year = ( $month === 1 ) ? ( $year - 1 ) : $year;
$prev_month = ( $month === 1 ) ? 12 : ( $month - 1 );
$prev_month_str = sprintf( '%04d-%02d', $prev_year, $prev_month );

$next_year = ( $month === 12 ) ? ( $year + 1 ) : $year;
$next_month = ( $month === 12 ) ? 1 : ( $month + 1 );
$next_month_str = sprintf( '%04d-%02d', $next_year, $next_month );

$today_str = current_time( 'Y-m' );
$current_month_display = date( 'F Y', mktime( 0, 0, 0, $month, 1, $year ) );

// Fetch all strands for taxonomy selector
$all_strands = get_terms( array( 'taxonomy' => 'strand', 'hide_empty' => false ) );
?>

<style>
    /* Public Calendar Styling */
    .wt-pub-cal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--color-cream);
        border: 2px solid var(--color-tan);
        border-radius: var(--border-radius-md) var(--border-radius-md) 0 0;
        padding: 1.25rem 2rem;
        margin-top: 1.5rem;
    }
    .wt-pub-cal-title {
        font-family: var(--font-headings);
        font-size: 2rem;
        color: var(--color-forest);
        margin: 0;
    }
    .wt-pub-cal-grid-header {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        background: var(--color-forest);
        color: var(--color-cream);
        text-align: center;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.05em;
        border: 2px solid var(--color-forest);
        border-top: none;
        border-bottom: none;
    }
    .wt-pub-cal-grid-header-day {
        padding: 0.75rem 0.5rem;
    }
    .wt-pub-cal-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        grid-auto-rows: minmax(130px, auto);
        background: var(--color-tan);
        gap: 2px;
        border: 2px solid var(--color-forest);
        border-radius: 0 0 var(--border-radius-md) var(--border-radius-md);
        overflow: hidden;
    }
    .wt-pub-cal-cell {
        background: var(--color-white);
        padding: 0.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        transition: background var(--transition-smooth);
        min-height: 130px;
    }
    .wt-pub-cal-cell.inactive {
        background: var(--color-cream);
        opacity: 0.6;
    }
    .wt-pub-cal-num {
        font-weight: 700;
        color: var(--color-forest);
        font-size: 1rem;
        margin-bottom: 2px;
    }
    .wt-pub-cal-cell.today {
        background: rgba(107, 143, 94, 0.12);
        box-shadow: inset 0 0 0 2px var(--color-forest);
    }
    
    /* Interactive Occurrence Badge */
    .wt-pub-occ {
        font-size: 0.75rem;
        line-height: 1.25;
        padding: 0.35rem 0.5rem;
        border-radius: 4px;
        font-weight: 600;
        display: block;
        text-decoration: none;
        color: var(--color-black);
        border-left: 4px solid var(--color-forest);
        background: var(--color-cream);
        position: relative;
        transition: all 0.2s ease;
        outline: none;
        cursor: pointer;
    }
    .wt-pub-occ:hover, .wt-pub-occ:focus {
        background: var(--color-white);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
        z-index: 10;
    }
    .wt-pub-occ:focus {
        box-shadow: 0 0 0 3px var(--color-forest);
    }
    
    /* Strand Coloring mapping for occurrences */
    .wt-pub-occ.strand-cafe {
        border-left-color: #d4c5a9;
        background: #faf8f5;
    }
    .wt-pub-occ.strand-young-people {
        border-left-color: #6b8f5e;
        background: #f4f7f3;
    }
    .wt-pub-occ.strand-creative-arts {
        border-left-color: #b45309;
        background: #fdf6f0;
    }
    .wt-pub-occ.strand-roots-shoots {
        border-left-color: #3b5c3a;
        background: #f4f7f3;
    }
    .wt-pub-occ.cancelled {
        text-decoration: line-through;
        opacity: 0.5;
        border-left-color: #dc2626 !important;
        background: #fff5f5 !important;
    }
    
    .wt-pub-occ-time {
        display: block;
        font-weight: 700;
        font-size: 0.65rem;
        color: var(--text-secondary);
        margin-bottom: 1px;
    }
    .wt-pub-occ-title {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Accessible Tooltip Styles */
    .wt-pub-occ .wt-pub-tooltip {
        visibility: hidden;
        width: 250px;
        background-color: #1e293b;
        color: #fff;
        text-align: left;
        border-radius: 6px;
        padding: 0.85rem;
        position: absolute;
        z-index: 999;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        opacity: 0;
        transition: opacity 0.2s, visibility 0.2s;
        font-weight: normal;
        pointer-events: none;
    }
    .wt-pub-occ .wt-pub-tooltip::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #1e293b transparent transparent transparent;
    }
    .wt-pub-occ:hover .wt-pub-tooltip,
    .wt-pub-occ:focus .wt-pub-tooltip,
    .wt-pub-occ:focus-within .wt-pub-tooltip {
        visibility: visible;
        opacity: 1;
        pointer-events: auto;
    }
    .wt-pub-tooltip-title {
        font-weight: bold;
        font-size: 0.85rem;
        margin-bottom: 0.4rem;
        border-bottom: 1px solid rgba(255,255,255,0.2);
        padding-bottom: 0.25rem;
        color: #f1f5f9;
    }
    .wt-pub-tooltip-row {
        font-size: 0.75rem;
        margin-bottom: 0.3rem;
        display: flex;
        gap: 0.4rem;
        color: #cbd5e1;
    }
    .wt-pub-tooltip-row strong {
        color: #fff;
    }

    /* View Switcher Controls style */
    .wt-view-switcher {
        display: inline-flex;
        background: var(--color-cream);
        border: 1px solid var(--color-tan);
        border-radius: 20px;
        padding: 0.25rem;
        gap: 0.25rem;
    }
    .wt-view-switcher-btn {
        padding: 0.4rem 1.25rem;
        border-radius: 18px;
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        color: var(--color-black);
        transition: all 0.2s ease;
    }
    .wt-view-switcher-btn.active {
        background: var(--color-forest);
        color: var(--color-cream);
    }
</style>

<main id="primary-content" class="site-main" role="main">
    
    <!-- Hero Header Banner -->
    <header class="archive-header" style="
        background: linear-gradient(135deg, var(--color-forest) 0%, var(--color-sage) 100%);
        color: var(--color-cream);
        padding: 5rem 0;
        text-align: center;
        border-bottom: 4px solid var(--color-tan);
    ">
        <div class="container">
            <span class="badge" style="
                background-color: var(--color-cream);
                color: var(--color-forest);
                margin-bottom: 1rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            ">
                <?php esc_html_e( 'Weardale Together CIC', 'weardale-together' ); ?>
            </span>
            
            <h1 class="font-display" style="
                font-size: 3.5rem;
                margin: 0 0 1rem 0;
                color: var(--color-cream);
                line-height: 1.1;
                font-weight: normal;
            ">
                <?php 
                if ( $strand_term ) {
                    printf( esc_html__( 'What\'s On: %s', 'weardale-together' ), esc_html( $strand_term->name ) );
                } else {
                    esc_html_e( 'What\'s On in Weardale', 'weardale-together' );
                }
                ?>
            </h1>
            
            <p style="
                font-size: 1.2rem;
                max-width: 650px;
                margin: 0 auto;
                opacity: 0.95;
                line-height: 1.5;
            ">
                <?php 
                if ( $strand_term && ! empty( $strand_term->description ) ) {
                    echo esc_html( $strand_term->description );
                } else {
                    esc_html_e( 'Explore our upcoming grassroots workshops, group activities, children\'s events, and community meals across Weardale.', 'weardale-together' );
                }
                ?>
            </p>
        </div>
    </header>

    <!-- Navigation Strand Filters & List/Calendar Switcher -->
    <nav class="strand-filters-nav" aria-label="<?php esc_attr_e( 'What\'s On Filters', 'weardale-together' ); ?>" style="
        background-color: var(--color-cream);
        border-bottom: 1px solid var(--color-tan);
        padding: 1.25rem 0;
    ">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            
            <!-- Strands Links -->
            <div style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
                <span style="font-size: 0.9rem; font-weight: 700; color: var(--color-black); text-transform: uppercase; letter-spacing: 0.05em; margin-right: 0.5rem;">
                    <?php esc_html_e( 'Strand:', 'weardale-together' ); ?>
                </span>
                
                <a href="<?php echo esc_url( add_query_arg( 'view', $current_view, get_post_type_archive_link( 'weardale_event' ) ) ); ?>" class="btn <?php echo ( ! $strand_term ) ? 'btn-primary' : 'btn-secondary'; ?>" style="padding: 0.4rem 1rem; font-size: 0.875rem; text-decoration: none;">
                    <?php esc_html_e( 'All Strands', 'weardale-together' ); ?>
                </a>

                <?php if ( ! empty( $all_strands ) && ! is_wp_error( $all_strands ) ) : ?>
                    <?php foreach ( $all_strands as $term ) : ?>
                        <?php 
                        $term_link = add_query_arg( 'view', $current_view, get_term_link( $term ) );
                        if ( is_wp_error( $term_link ) ) {
                            continue;
                        }
                        $is_active = ( $strand_term && $strand_term->term_id === $term->term_id );
                        ?>
                        <a href="<?php echo esc_url( $term_link ); ?>" class="btn <?php echo $is_active ? 'btn-primary' : 'btn-secondary'; ?>" style="padding: 0.4rem 1rem; font-size: 0.875rem; text-decoration: none;">
                            <?php echo esc_html( $term->name ); ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- List / Monthly Calendar switcher toggle (Top-Right requirement) -->
            <div class="wt-view-switcher" role="group" aria-label="<?php esc_attr_e( 'View toggle', 'weardale-together' ); ?>">
                <a href="<?php echo esc_url( add_query_arg( 'view', 'list' ) ); ?>" class="wt-view-switcher-btn <?php echo ( 'list' === $current_view ) ? 'active' : ''; ?>">
                    🗒️ <?php esc_html_e( 'List View', 'weardale-together' ); ?>
                </a>
                <a href="<?php echo esc_url( add_query_arg( 'view', 'calendar' ) ); ?>" class="wt-view-switcher-btn <?php echo ( 'calendar' === $current_view ) ? 'active' : ''; ?>">
                    🗓️ <?php esc_html_e( 'Calendar View', 'weardale-together' ); ?>
                </a>
            </div>
        </div>
    </nav>

    <!-- Content Sections -->
    <div class="section-padding" style="background-color: var(--color-white); min-height: 50vh;">
        <div class="container">

            <?php if ( 'calendar' === $current_view ) : ?>
                <!-- ==================== CALENDAR VIEW ==================== -->
                <section id="calendar-view" aria-labelledby="calendar-view-title">
                    <div style="border-bottom: 2px solid var(--color-tan); padding-bottom: 0.75rem; margin-bottom: 2rem;">
                        <h2 id="calendar-view-title" class="font-display" style="font-size: 2.25rem; color: var(--color-forest); margin: 0; font-weight: normal;">
                            🗓️ <?php esc_html_e( 'Monthly Calendar Agenda', 'weardale-together' ); ?>
                        </h2>
                    </div>

                    <!-- Month Nav header -->
                    <div class="wt-pub-cal-header">
                        <h3 class="wt-pub-cal-title"><?php echo esc_html( $current_month_display ); ?></h3>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="<?php echo esc_url( add_query_arg( array( 'view' => 'calendar', 'month' => $prev_month_str ) ) ); ?>" class="btn btn-secondary" style="padding: 0.4rem 1rem; text-decoration: none;" aria-label="Previous Month"><?php esc_html_e( '« Previous', 'weardale-together' ); ?></a>
                            <a href="<?php echo esc_url( add_query_arg( array( 'view' => 'calendar', 'month' => $today_str ) ) ); ?>" class="btn btn-secondary" style="padding: 0.4rem 1rem; text-decoration: none;" aria-label="Current Month"><?php esc_html_e( 'Today', 'weardale-together' ); ?></a>
                            <a href="<?php echo esc_url( add_query_arg( array( 'view' => 'calendar', 'month' => $next_month_str ) ) ); ?>" class="btn btn-secondary" style="padding: 0.4rem 1rem; text-decoration: none;" aria-label="Next Month"><?php esc_html_e( 'Next »', 'weardale-together' ); ?></a>
                        </div>
                    </div>

                    <!-- 7 days header row -->
                    <div class="wt-pub-cal-grid-header">
                        <div class="wt-pub-cal-grid-header-day"><?php esc_html_e( 'Mon', 'weardale-together' ); ?></div>
                        <div class="wt-pub-cal-grid-header-day"><?php esc_html_e( 'Tue', 'weardale-together' ); ?></div>
                        <div class="wt-pub-cal-grid-header-day"><?php esc_html_e( 'Wed', 'weardale-together' ); ?></div>
                        <div class="wt-pub-cal-grid-header-day"><?php esc_html_e( 'Thu', 'weardale-together' ); ?></div>
                        <div class="wt-pub-cal-grid-header-day"><?php esc_html_e( 'Fri', 'weardale-together' ); ?></div>
                        <div class="wt-pub-cal-grid-header-day"><?php esc_html_e( 'Sat', 'weardale-together' ); ?></div>
                        <div class="wt-pub-cal-grid-header-day"><?php esc_html_e( 'Sun', 'weardale-together' ); ?></div>
                    </div>

                    <!-- Monthly Grid body -->
                    <div class="wt-pub-cal-grid">
                        <?php
                        // Fetch active occurrences for this month grid
                        $start_of_month = sprintf( '%04d-%02d-01 00:00:00', $year, $month );
                        $days_in_month  = cal_days_in_month( CAL_GREGORIAN, $month, $year );
                        $end_of_month    = sprintf( '%04d-%02d-%02d 23:59:59', $year, $month, $days_in_month );

                        $query_args = array(
                            'start_date'        => $start_of_month,
                            'end_date'          => $end_of_month,
                            'scope'             => 'all',
                            'include_cancelled' => true,
                        );
                        if ( $strand_term ) {
                            $query_args['strand'] = $strand_term->slug;
                        }

                        $occurrences = function_exists( 'weardale_platform_query_occurrences' ) 
                            ? weardale_platform_query_occurrences( $query_args ) 
                            : array();

                        // Group occurrences by day
                        $by_day = array();
                        for ( $d = 1; $d <= $days_in_month; $d++ ) {
                            $by_day[ $d ] = array();
                        }
                        foreach ( $occurrences as $occ ) {
                            $day_num = intval( date( 'j', strtotime( $occ['occurrence_start'] ) ) );
                            $by_day[ $day_num ][] = $occ;
                        }

                        // Monday offset shift
                        $first_day_ts = mktime( 0, 0, 0, $month, 1, $year );
                        $first_dow    = intval( date( 'w', $first_day_ts ) );
                        $offset       = ( $first_dow === 0 ) ? 6 : ( $first_dow - 1 );

                        // Inactive leading days
                        for ( $i = 0; $i < $offset; $i++ ) {
                            echo '<div class="wt-pub-cal-cell inactive"></div>';
                        }

                        // Days of month
                        $today_d = intval( current_time( 'j' ) );
                        $today_m = intval( current_time( 'n' ) );
                        $today_y = intval( current_time( 'Y' ) );

                        for ( $d = 1; $d <= $days_in_month; $d++ ) {
                            $is_today = ( $d === $today_d && $month === $today_m && $year === $today_y );
                            $today_class = $is_today ? 'today' : '';
                            
                            echo '<div class="wt-pub-cal-cell ' . esc_attr( $today_class ) . '">';
                            echo '<div class="wt-pub-cal-num">' . esc_html( $d ) . '</div>';

                            if ( ! empty( $by_day[ $d ] ) ) {
                                foreach ( $by_day[ $d ] as $occ ) {
                                    $time_str = date( 'H:i', strtotime( $occ['occurrence_start'] ) );
                                    $status_class = ( $occ['occurrence_status'] === 'cancelled' ) ? 'cancelled' : '';
                                    
                                    // Map first strand slug
                                    $strand_slug = ! empty( $occ['strands'] ) ? $occ['strands'][0]['slug'] : '';
                                    $strand_name = ! empty( $occ['strands'] ) ? $occ['strands'][0]['name'] : 'Activity';
                                    
                                    echo '<a href="' . esc_url( $occ['permalink'] ) . '" class="wt-pub-occ strand-' . esc_attr( $strand_slug ) . ' ' . $status_class . '" tabindex="0">';
                                    echo '<span class="wt-pub-occ-time">' . esc_html( $time_str ) . '</span>';
                                    echo '<span class="wt-pub-occ-title">' . esc_html( $occ['post_title'] ) . '</span>';
                                    
                                    // Fully Keyboard Accessible Tooltip Markup
                                    echo '<div class="wt-pub-tooltip" role="tooltip">';
                                    echo '<div class="wt-pub-tooltip-title">' . esc_html( $occ['post_title'] ) . '</div>';
                                    echo '<div class="wt-pub-tooltip-row"><strong>🕒 ' . __( 'Time', 'weardale-together' ) . ':</strong> ' . esc_html( date( 'g:i A', strtotime($occ['occurrence_start']) ) ) . ' - ' . esc_html( date( 'g:i A', strtotime($occ['occurrence_end']) ) ) . '</div>';
                                    if ( ! empty( $occ['meta']['venue_name'] ) ) {
                                        echo '<div class="wt-pub-tooltip-row"><strong>📍 ' . __( 'Venue', 'weardale-together' ) . ':</strong> ' . esc_html( $occ['meta']['venue_name'] ) . '</div>';
                                    }
                                    if ( ! empty( $occ['meta']['cost_text'] ) ) {
                                        echo '<div class="wt-pub-tooltip-row"><strong>🪙 ' . __( 'Cost', 'weardale-together' ) . ':</strong> ' . esc_html( $occ['meta']['cost_text'] ) . '</div>';
                                    }
                                    echo '<div class="wt-pub-tooltip-row"><strong>🎫 ' . __( 'Booking', 'weardale-together' ) . ':</strong> ' . esc_html( ucwords( str_replace( '_', ' ', $occ['meta']['booking_status'] ) ) ) . '</div>';
                                    echo '<div class="wt-pub-tooltip-row"><strong>🏷️ ' . __( 'Strand', 'weardale-together' ) . ':</strong> ' . esc_html( $strand_name ) . '</div>';
                                    echo '</div>'; // close tooltip
                                    
                                    echo '</a>';
                                }
                            }

                            echo '</div>';
                        }

                        // Inactive trailing days to fill grid
                        $total_cells = $offset + $days_in_month;
                        $remaining = ( $total_cells % 7 === 0 ) ? 0 : ( 7 - ( $total_cells % 7 ) );
                        for ( $i = 0; $i < $remaining; $i++ ) {
                            echo '<div class="wt-pub-cal-cell inactive"></div>';
                        }
                        ?>
                    </div>
                </section>

            <?php else : ?>
                <!-- ==================== LIST VIEW (DEFAULT) ==================== -->
                <!-- SECTION 1: UPCOMING EVENTS -->
                <section id="upcoming-events" aria-labelledby="upcoming-events-title" style="margin-bottom: 5rem;">
                    <div style="
                        border-bottom: 2px solid var(--color-tan);
                        padding-bottom: 0.75rem;
                        margin-bottom: 2.5rem;
                        display: flex;
                        align-items: baseline;
                        justify-content: space-between;
                        flex-wrap: wrap;
                        gap: 1rem;
                    ">
                        <h2 id="upcoming-events-title" class="font-display" style="font-size: 2.25rem; color: var(--color-forest); margin: 0; font-weight: normal;">
                            🌿 <?php esc_html_e( 'Upcoming Activities', 'weardale-together' ); ?>
                        </h2>
                        <span style="font-size: 1.05rem; font-family: var(--font-mono); color: var(--text-secondary); font-weight: 500;">
                            <?php 
                            $upcoming_args = array( 'scope' => 'upcoming', 'posts_per_page' => -1 );
                            if ( $strand_term ) {
                                $upcoming_args['strand'] = $strand_term->slug;
                            }
                            $upcoming_query = function_exists( 'weardale_platform_get_events' ) 
                                ? weardale_platform_get_events( $upcoming_args ) 
                                : new WP_Query( array( 'post_type' => 'weardale_event', 'posts_per_page' => -1 ) );

                            $upcoming_count = $upcoming_query->found_posts;
                            printf( _n( '%d upcoming activity found', '%d upcoming activities found', $upcoming_count, 'weardale-together' ), $upcoming_count );
                            ?>
                        </span>
                    </div>

                    <?php if ( $upcoming_query->have_posts() ) : ?>
                        <div class="grid grid-3">
                            <?php 
                            while ( $upcoming_query->have_posts() ) : 
                                $upcoming_query->the_post();
                                get_template_part( 'template-parts/events/card' );
                            endwhile; 
                            wp_reset_postdata();
                            ?>
                        </div>
                    <?php else : ?>
                        <?php 
                        get_template_part( 'template-parts/events/empty-state', null, array(
                            'message' => $strand_term 
                                ? sprintf( __( 'We do not have any upcoming activities scheduled for %s at the moment. Please check back shortly!', 'weardale-together' ), $strand_term->name )
                                : __( 'We do not have any upcoming activities scheduled at the moment. Please check back shortly!', 'weardale-together' )
                        ) ); 
                        ?>
                    <?php endif; ?>
                </section>

                <!-- SECTION 2: PAST EVENTS (HISTORIC ARCHIVE) -->
                <?php 
                $past_args = array( 'scope' => 'past', 'posts_per_page' => 12 );
                if ( $strand_term ) {
                    $past_args['strand'] = $strand_term->slug;
                }
                $past_query = function_exists( 'weardale_platform_get_events' ) 
                    ? weardale_platform_get_events( $past_args ) 
                    : null;

                if ( $past_query && $past_query->have_posts() ) : 
                ?>
                    <section id="past-events" aria-labelledby="past-events-title" style="
                        border-top: 1px solid var(--color-tan);
                        padding-top: 5rem;
                    ">
                        <div style="
                            border-bottom: 2px solid var(--color-tan);
                            padding-bottom: 0.75rem;
                            margin-bottom: 2.5rem;
                            display: flex;
                            align-items: baseline;
                            justify-content: space-between;
                            flex-wrap: wrap;
                            gap: 1rem;
                        ">
                            <h2 id="past-events-title" class="font-display" style="font-size: 2rem; color: var(--text-secondary); margin: 0; font-weight: normal;">
                                📜 <?php esc_html_e( 'Past & Recent Activities', 'weardale-together' ); ?>
                            </h2>
                            <span style="font-size: 0.95rem; font-family: var(--font-body); color: var(--text-secondary);">
                                <?php esc_html_e( 'A record of our community achievements', 'weardale-together' ); ?>
                            </span>
                        </div>

                        <div class="grid grid-3" style="opacity: 0.85;">
                            <?php 
                            while ( $past_query->have_posts() ) : 
                                $past_query->the_post();
                                get_template_part( 'template-parts/events/card' );
                            endwhile; 
                            wp_reset_postdata();
                            ?>
                        </div>
                    </section>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php
get_footer();
