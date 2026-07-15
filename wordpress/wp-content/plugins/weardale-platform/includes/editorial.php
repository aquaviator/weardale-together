<?php
/**
 * Custom Editorial Experience Enhancements
 *
 * Registers the 'weardale_event' Custom Post Type, 'strand' custom taxonomy,
 * event meta boxes for editorial management, and customized table listings.
 *
 * @package Weardale_Platform
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. Register Custom Post Type: Weardale Event (weardale_event)
 */
function weardale_platform_register_event_cpt() {
    $labels = array(
        'name'               => _x( 'Events', 'post type general name', 'weardale-platform' ),
        'singular_name'      => _x( 'Event', 'post type singular name', 'weardale-platform' ),
        'menu_name'          => _x( 'WT Events', 'admin menu', 'weardale-platform' ),
        'name_admin_bar'     => _x( 'Event', 'add new on admin bar', 'weardale-platform' ),
        'add_new'            => _x( 'Add New Event', 'event', 'weardale-platform' ),
        'add_new_item'       => __( 'Add New Weardale Event', 'weardale-platform' ),
        'new_item'           => __( 'New Event', 'weardale-platform' ),
        'edit_item'          => __( 'Edit Event', 'weardale-platform' ),
        'view_item'          => __( 'View Event', 'weardale-platform' ),
        'all_items'          => __( 'All Events', 'weardale-platform' ),
        'search_items'       => __( 'Search Events', 'weardale-platform' ),
        'parent_item_colon'  => __( 'Parent Events:', 'weardale-platform' ),
        'not_found'          => __( 'No events found.', 'weardale-platform' ),
        'not_found_in_trash' => __( 'No events found in Trash.', 'weardale-platform' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'whats-on' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-calendar-alt',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'show_in_rest'       => true, // Enable Block Editor (Gutenberg)
    );

    register_post_type( 'weardale_event', $args );
}
add_action( 'init', 'weardale_platform_register_event_cpt' );

/**
 * 2. Register Custom Taxonomy: Strand (strand)
 * Groups pages, posts, or events into Root & Branch, Creative Arts, etc.
 */
function weardale_platform_register_strand_taxonomy() {
    $labels = array(
        'name'              => _x( 'Strands', 'taxonomy general name', 'weardale-platform' ),
        'singular_name'     => _x( 'Strand', 'taxonomy singular name', 'weardale-platform' ),
        'search_items'      => __( 'Search Strands', 'weardale-platform' ),
        'all_items'         => __( 'All Strands', 'weardale-platform' ),
        'parent_item'       => __( 'Parent Strand', 'weardale-platform' ),
        'parent_item_colon' => __( 'Parent Strand:', 'weardale-platform' ),
        'edit_item'         => __( 'Edit Strand', 'weardale-platform' ),
        'update_item'       => __( 'Update Strand', 'weardale-platform' ),
        'add_new_item'      => __( 'Add New Strand', 'weardale-platform' ),
        'new_item_name'     => __( 'New Strand Name', 'weardale-platform' ),
        'menu_name'         => __( 'Strands', 'weardale-platform' ),
    );

    $args = array(
        'hierarchical'      => true, // Behaves like standard categories
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'strand' ),
        'show_in_rest'      => true, // Gutenberg integration
    );

    // Apply taxonomy to standard posts, pages, and our custom events CPT
    register_taxonomy( 'strand', array( 'post', 'page', 'weardale_event' ), $args );
}
add_action( 'init', 'weardale_platform_register_strand_taxonomy' );

/**
 * 3. Add Custom Meta Boxes for Weardale Events CPT
 */
function weardale_platform_add_event_meta_boxes() {
    add_meta_box(
        'weardale_event_details',
        __( 'Weardale Event Details & Management', 'weardale-platform' ),
        'weardale_platform_render_event_meta_box',
        'weardale_event',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'weardale_platform_add_event_meta_boxes' );

// Render Meta Box Form fields
function weardale_platform_render_event_meta_box( $post ) {
    wp_nonce_field( 'weardale_event_meta_nonce_action', 'weardale_event_meta_nonce_field' );

    // Retrieve existing saved metadata (with new structured keys and legacy fallback)
    $event_date                 = get_post_meta( $post->ID, '_event_date', true );
    $event_end_date             = get_post_meta( $post->ID, '_event_end_date', true );
    $event_all_day              = get_post_meta( $post->ID, '_event_all_day', true );
    $event_time                 = get_post_meta( $post->ID, '_event_time', true );
    $event_venue_name           = get_post_meta( $post->ID, '_event_venue_name', true );
    $event_location             = get_post_meta( $post->ID, '_event_location', true );
    $event_map_url              = get_post_meta( $post->ID, '_event_map_url', true );
    $event_audience             = get_post_meta( $post->ID, '_event_audience', true );
    $event_age_guidance         = get_post_meta( $post->ID, '_event_age_guidance', true );
    $event_accessibility        = get_post_meta( $post->ID, '_event_accessibility', true );
    $event_booking_status       = get_post_meta( $post->ID, '_event_booking_status', true );
    $event_booking_url          = get_post_meta( $post->ID, '_event_booking_url', true );
    $event_booking_instructions = get_post_meta( $post->ID, '_event_booking_instructions', true );
    $event_cost                 = get_post_meta( $post->ID, '_event_cost', true );
    $event_organiser_name       = get_post_meta( $post->ID, '_event_organiser_name', true );
    $event_organiser_contact    = get_post_meta( $post->ID, '_event_organiser_contact', true );

    // Default status if empty
    if ( empty( $event_booking_status ) ) {
        $event_booking_status = ! empty( $event_booking_url ) ? 'booking_required' : 'no_booking_required';
    }
    ?>
    <style>
        .wt-meta-section {
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .wt-meta-section:last-child {
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 0;
        }
        .wt-meta-section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin-top: 0;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-left: 4px solid #3b5c3a;
            padding-left: 0.5rem;
        }
    </style>

    <div class="wt-meta-container">
        
        <!-- SECTION 1: Date & Time -->
        <div class="wt-meta-section">
            <h3 class="wt-meta-section-title">📅 <?php esc_html_e( 'Date & Time', 'weardale-platform' ); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label for="event_date"><strong><?php esc_html_e( 'Event Start Date *', 'weardale-platform' ); ?></strong></label></th>
                    <td>
                        <input type="date" id="event_date" name="event_date" value="<?php echo esc_attr( $event_date ); ?>" class="regular-text" required>
                        <p class="description"><?php esc_html_e( 'The start date of the activity.', 'weardale-platform' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="event_end_date"><?php esc_html_e( 'Event End Date', 'weardale-platform' ); ?></label></th>
                    <td>
                        <input type="date" id="event_end_date" name="event_end_date" value="<?php echo esc_attr( $event_end_date ); ?>" class="regular-text">
                        <p class="description"><?php esc_html_e( 'Optional. For multi-day programs or events.', 'weardale-platform' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="event_all_day"><?php esc_html_e( 'All-Day Event', 'weardale-platform' ); ?></label></th>
                    <td>
                        <input type="checkbox" id="event_all_day" name="event_all_day" value="1" <?php checked( $event_all_day, '1' ); ?>>
                        <span class="description"><?php esc_html_e( 'Check if this is an all-day or multi-day ongoing activity with no specific hours.', 'weardale-platform' ); ?></span>
                    </td>
                </tr>
                <tr>
                    <th><label for="event_time"><?php esc_html_e( 'Event Timing / Hours', 'weardale-platform' ); ?></label></th>
                    <td>
                        <input type="text" id="event_time" name="event_time" value="<?php echo esc_attr( $event_time ); ?>" placeholder="e.g. 10:00 AM - 1:00 PM" class="regular-text">
                        <p class="description"><?php esc_html_e( 'Specify the operating hours or schedule.', 'weardale-platform' ); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- SECTION 2: Location -->
        <div class="wt-meta-section">
            <h3 class="wt-meta-section-title">📍 <?php esc_html_e( 'Location & Venue', 'weardale-platform' ); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label for="event_venue_name"><?php esc_html_e( 'Venue Name', 'weardale-platform' ); ?></label></th>
                    <td>
                        <input type="text" id="event_venue_name" name="event_venue_name" value="<?php echo esc_attr( $event_venue_name ); ?>" placeholder="e.g. Stanhope Hub Community Garden" class="regular-text">
                        <p class="description"><?php esc_html_e( 'Primary display name of the venue (falls back to address if empty).', 'weardale-platform' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label_for="event_location"><strong><?php esc_html_e( 'Address / Location Details *', 'weardale-platform' ); ?></strong></label_for></th>
                    <td>
                        <input type="text" id="event_location" name="event_location" value="<?php echo esc_attr( $event_location ); ?>" placeholder="e.g. Front Street, Stanhope DL13 2UE" class="regular-text" required>
                        <p class="description"><?php esc_html_e( 'Exact street address or localized directions.', 'weardale-platform' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="event_map_url"><?php esc_html_e( 'External Map Link', 'weardale-platform' ); ?></label></th>
                    <td>
                        <input type="url" id="event_map_url" name="event_map_url" value="<?php echo esc_url( $event_map_url ); ?>" placeholder="e.g. https://maps.google.com/..." class="regular-text">
                        <p class="description"><?php esc_html_e( 'Link to Google Maps or directions for residents.', 'weardale-platform' ); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- SECTION 3: Audience & Accessibility -->
        <div class="wt-meta-section">
            <h3 class="wt-meta-section-title">👥 <?php esc_html_e( 'Audience & Accessibility', 'weardale-platform' ); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label for="event_audience"><?php esc_html_e( 'Who is this for?', 'weardale-platform' ); ?></label></th>
                    <td>
                        <input type="text" id="event_audience" name="event_audience" value="<?php echo esc_attr( $event_audience ); ?>" placeholder="e.g. Elderly residents, isolated adults, families" class="regular-text">
                        <p class="description"><?php esc_html_e( 'Describe who the activities target.', 'weardale-platform' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="event_age_guidance"><?php esc_html_e( 'Age Guidance', 'weardale-platform' ); ?></label></th>
                    <td>
                        <input type="text" id="event_age_guidance" name="event_age_guidance" value="<?php echo esc_attr( $event_age_guidance ); ?>" placeholder="e.g. Over 65s or Under 12s must be accompanied" class="regular-text">
                        <p class="description"><?php esc_html_e( 'Any age limits or supervision recommendations.', 'weardale-platform' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="event_accessibility"><?php esc_html_e( 'Accessibility Information', 'weardale-platform' ); ?></label></th>
                    <td>
                        <textarea id="event_accessibility" name="event_accessibility" rows="3" class="large-text" placeholder="e.g. Wheelchair accessible toilet on-site, step-free access, quiet chill-out space available."><?php echo esc_textarea( $event_accessibility ); ?></textarea>
                        <p class="description"><?php esc_html_e( 'Detail physical or sensory accessibility accommodations to welcome everyone.', 'weardale-platform' ); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- SECTION 4: Booking & Cost -->
        <div class="wt-meta-section">
            <h3 class="wt-meta-section-title">🪙 <?php esc_html_e( 'Booking & Admission Cost', 'weardale-platform' ); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label for="event_booking_status"><strong><?php esc_html_e( 'Booking Status *', 'weardale-platform' ); ?></strong></label></th>
                    <td>
                        <select id="event_booking_status" name="event_booking_status" class="postform">
                            <option value="no_booking_required" <?php selected( $event_booking_status, 'no_booking_required' ); ?>><?php esc_html_e( 'No Booking Required (Just turn up!)', 'weardale-platform' ); ?></option>
                            <option value="booking_recommended" <?php selected( $event_booking_status, 'booking_recommended' ); ?>><?php esc_html_e( 'Booking Recommended', 'weardale-platform' ); ?></option>
                            <option value="booking_required" <?php selected( $event_booking_status, 'booking_required' ); ?>><?php esc_html_e( 'Booking Required', 'weardale-platform' ); ?></option>
                            <option value="sold_out" <?php selected( $event_booking_status, 'sold_out' ); ?>><?php esc_html_e( 'Sold Out', 'weardale-platform' ); ?></option>
                            <option value="cancelled" <?php selected( $event_booking_status, 'cancelled' ); ?>><?php esc_html_e( 'Cancelled', 'weardale-platform' ); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e( 'Displays a clear badge indicating status.', 'weardale-platform' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="event_booking_url"><?php esc_html_e( 'Booking Ticket URL', 'weardale-platform' ); ?></label></th>
                    <td>
                        <input type="url" id="event_booking_url" name="event_booking_url" value="<?php echo esc_url( $event_booking_url ); ?>" placeholder="e.g. https://eventbrite.com/..." class="regular-text">
                        <p class="description"><?php esc_html_e( 'Direct link to reserve spots or tickets (if booking required/recommended).', 'weardale-platform' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="event_booking_instructions"><?php esc_html_e( 'Booking Instructions', 'weardale-platform' ); ?></label></th>
                    <td>
                        <textarea id="event_booking_instructions" name="event_booking_instructions" rows="2" class="large-text" placeholder="e.g. Email leatfield@gmail.com or call the Hub office directly."><?php echo esc_textarea( $event_booking_instructions ); ?></textarea>
                        <p class="description"><?php esc_html_e( 'Short custom text explaining how residents can secure a slot.', 'weardale-platform' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="event_cost"><strong><?php esc_html_e( 'Cost / Price *', 'weardale-platform' ); ?></strong></label></th>
                    <td>
                        <input type="text" id="event_cost" name="event_cost" value="<?php echo esc_attr( $event_cost ); ?>" placeholder="e.g. Free (Donations Welcome) or £3 material cost" class="regular-text" required>
                        <p class="description"><?php esc_html_e( 'Specify price details or voluntary donation recommendations.', 'weardale-platform' ); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- SECTION 5: Organiser -->
        <div class="wt-meta-section">
            <h3 class="wt-meta-section-title">👤 <?php esc_html_e( 'Organising Representative', 'weardale-platform' ); ?></h3>
            <table class="form-table">
                <tr>
                    <th><label for="event_organiser_name"><?php esc_html_e( 'Representative Name', 'weardale-platform' ); ?></label></th>
                    <td>
                        <input type="text" id="event_organiser_name" name="event_organiser_name" value="<?php echo esc_attr( $event_organiser_name ); ?>" placeholder="e.g. Sarah Thompson" class="regular-text">
                        <p class="description"><?php esc_html_e( 'The specific point of contact for enquiries.', 'weardale-platform' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="event_organiser_contact"><?php esc_html_e( 'Contact Information', 'weardale-platform' ); ?></label></th>
                    <td>
                        <input type="text" id="event_organiser_contact" name="event_organiser_contact" value="<?php echo esc_attr( $event_organiser_contact ); ?>" placeholder="e.g. sarah.thompson@weardaletogether.org" class="regular-text">
                        <p class="description"><?php esc_html_e( 'Email address or contact telephone number.', 'weardale-platform' ); ?></p>
                    </td>
                </tr>
            </table>
        </div>

    </div>
    <?php
}

// Save Meta Box Form data with robust security
function weardale_platform_save_event_meta( $post_id ) {
    // 1. Verify nonce exists and is correct
    if ( ! isset( $_POST['weardale_event_meta_nonce_field'] ) || ! wp_verify_nonce( $_POST['weardale_event_meta_nonce_field'], 'weardale_event_meta_nonce_action' ) ) {
        return;
    }

    // 2. Prevent auto-save cycles
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // 3. Confirm user edit permissions
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // 4. Sanitize and store fields
    $fields = array(
        'event_date'                 => '_event_date',
        'event_end_date'             => '_event_end_date',
        'event_time'                 => '_event_time',
        'event_venue_name'           => '_event_venue_name',
        'event_location'             => '_event_location',
        'event_audience'             => '_event_audience',
        'event_age_guidance'         => '_event_age_guidance',
        'event_booking_status'       => '_event_booking_status',
        'event_cost'                 => '_event_cost',
        'event_organiser_name'       => '_event_organiser_name',
        'event_organiser_contact'    => '_event_organiser_contact',
    );

    foreach ( $fields as $post_key => $meta_key ) {
        if ( isset( $_POST[ $post_key ] ) ) {
            update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[ $post_key ] ) );
        }
    }

    // Checkbox field
    $all_day_val = isset( $_POST['event_all_day'] ) ? '1' : '0';
    update_post_meta( $post_id, '_event_all_day', $all_day_val );

    // URL Fields
    if ( isset( $_POST['event_map_url'] ) ) {
        update_post_meta( $post_id, '_event_map_url', esc_url_raw( $_POST['event_map_url'] ) );
    }
    if ( isset( $_POST['event_booking_url'] ) ) {
        update_post_meta( $post_id, '_event_booking_url', esc_url_raw( $_POST['event_booking_url'] ) );
    }

    // Textarea Fields
    if ( isset( $_POST['event_accessibility'] ) ) {
        update_post_meta( $post_id, '_event_accessibility', sanitize_textarea_field( $_POST['event_accessibility'] ) );
    }
    if ( isset( $_POST['event_booking_instructions'] ) ) {
        update_post_meta( $post_id, '_event_booking_instructions', sanitize_textarea_field( $_POST['event_booking_instructions'] ) );
    }
}
add_action( 'save_post', 'weardale_platform_save_event_meta' );

/**
 * 4. Custom Columns for Events Admin Dashboard Table Listing
 * Allows easy, non-technical tracking of schedules directly in the admin lists.
 */
function weardale_platform_set_event_columns( $columns ) {
    $new_columns = array(
        'cb'                   => $columns['cb'],
        'title'                => __( 'Event', 'weardale-platform' ),
        'event_date'           => __( 'Date', 'weardale-platform' ),
        'event_time'           => __( 'Time', 'weardale-platform' ),
        'event_venue'          => __( 'Venue', 'weardale-platform' ),
        'taxonomy-strand'      => __( 'Strand', 'weardale-platform' ),
        'event_booking_status' => __( 'Booking Status', 'weardale-platform' ),
        'date'                 => __( 'Published', 'weardale-platform' ),
    );
    return $new_columns;
}
add_filter( 'manage_weardale_event_posts_columns', 'weardale_platform_set_event_columns' );

function weardale_platform_display_event_columns( $column, $post_id ) {
    switch ( $column ) {
        case 'event_date':
            $date = get_post_meta( $post_id, '_event_date', true );
            echo ! empty( $date ) ? esc_html( date( 'F j, Y', strtotime( $date ) ) ) : '<em>-</em>';
            break;
        case 'event_time':
            $time = get_post_meta( $post_id, '_event_time', true );
            echo ! empty( $time ) ? esc_html( $time ) : '<em>-</em>';
            break;
        case 'event_venue':
            $venue = get_post_meta( $post_id, '_event_venue_name', true );
            if ( empty( $venue ) ) {
                $venue = get_post_meta( $post_id, '_event_location', true );
            }
            echo ! empty( $venue ) ? esc_html( $venue ) : '<em>-</em>';
            break;
        case 'event_booking_status':
            $status = get_post_meta( $post_id, '_event_booking_status', true );
            $booking_url = get_post_meta( $post_id, '_event_booking_url', true );
            if ( empty( $status ) ) {
                $status = ! empty( $booking_url ) ? 'booking_required' : 'no_booking_required';
            }
            
            $labels = array(
                'no_booking_required' => array( 'label' => __( 'No Booking Required', 'weardale-platform' ), 'color' => '#64748b' ),
                'booking_recommended' => array( 'label' => __( 'Recommended', 'weardale-platform' ), 'color' => '#0d9488' ),
                'booking_required'    => array( 'label' => __( 'Required', 'weardale-platform' ), 'color' => '#2563eb' ),
                'sold_out'            => array( 'label' => __( 'SOLD OUT', 'weardale-platform' ), 'color' => '#ea580c' ),
                'cancelled'           => array( 'label' => __( 'CANCELLED', 'weardale-platform' ), 'color' => '#dc2626' ),
            );

            if ( isset( $labels[ $status ] ) ) {
                $cfg = $labels[ $status ];
                echo '<span class="badge" style="background-color: ' . esc_attr( $cfg['color'] ) . '; color: #fff; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: bold; font-size: 0.75rem; text-transform: uppercase;">' . esc_html( $cfg['label'] ) . '</span>';
            } else {
                echo '<em>-</em>';
            }
            break;
    }
}
add_action( 'manage_weardale_event_posts_custom_column', 'weardale_platform_display_event_columns', 10, 2 );

/**
 * 5. Enable Click-to-Sort for Scheduled Date Column
 */
function weardale_platform_sortable_event_columns( $columns ) {
    $columns['event_date'] = 'event_date';
    return $columns;
}
add_filter( 'manage_edit-weardale_event_sortable_columns', 'weardale_platform_sortable_event_columns' );

function weardale_platform_sort_event_columns_query( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() ) {
        return;
    }

    if ( $query->get( 'post_type' ) === 'weardale_event' ) {
        $orderby = $query->get( 'orderby' );

        // Default sort chronologically by Scheduled Date
        if ( empty( $orderby ) ) {
            $query->set( 'meta_key', '_event_date' );
            $query->set( 'orderby', 'meta_value' );
            $query->set( 'order', 'ASC' );
        } elseif ( 'event_date' === $orderby ) {
            $query->set( 'meta_key', '_event_date' );
            $query->set( 'orderby', 'meta_value' );
        }
    }
}
add_action( 'pre_get_posts', 'weardale_platform_sort_event_columns_query' );

/**
 * 6. Admin Dropdown Filters for Upcoming/Past & Booking Status
 */
function weardale_platform_admin_event_filters( $post_type ) {
    if ( 'weardale_event' !== $post_type ) {
        return;
    }

    // Upcoming/Past Filter
    $time_filter = isset( $_GET['event_time_filter'] ) ? sanitize_text_field( $_GET['event_time_filter'] ) : '';
    ?>
    <select name="event_time_filter">
        <option value=""><?php esc_html_e( 'All Dates (Upcoming & Past)', 'weardale-platform' ); ?></option>
        <option value="upcoming" <?php selected( $time_filter, 'upcoming' ); ?>><?php esc_html_e( 'Upcoming Events', 'weardale-platform' ); ?></option>
        <option value="past" <?php selected( $time_filter, 'past' ); ?>><?php esc_html_e( 'Past Events', 'weardale-platform' ); ?></option>
    </select>

    <?php
    // Booking Status Filter
    $booking_filter = isset( $_GET['event_booking_filter'] ) ? sanitize_text_field( $_GET['event_booking_filter'] ) : '';
    $statuses = array(
        'no_booking_required' => __( 'No Booking Required', 'weardale-platform' ),
        'booking_recommended' => __( 'Booking Recommended', 'weardale-platform' ),
        'booking_required'    => __( 'Booking Required', 'weardale-platform' ),
        'sold_out'            => __( 'Sold Out', 'weardale-platform' ),
        'cancelled'           => __( 'Cancelled', 'weardale-platform' ),
    );
    ?>
    <select name="event_booking_filter">
        <option value=""><?php esc_html_e( 'All Booking Statuses', 'weardale-platform' ); ?></option>
        <?php foreach ( $statuses as $key => $label ) : ?>
            <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $booking_filter, $key ); ?>><?php echo esc_html( $label ); ?></option>
        <?php endforeach; ?>
    </select>
    <?php
}
add_action( 'restrict_manage_posts', 'weardale_platform_admin_event_filters' );

function weardale_platform_filter_event_posts( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() ) {
        return;
    }

    if ( $query->get( 'post_type' ) === 'weardale_event' ) {
        $meta_query = (array) $query->get( 'meta_query' );
        $today = date( 'Y-m-d' );

        // 1. Time Filter
        if ( ! empty( $_GET['event_time_filter'] ) ) {
            $time_filter = sanitize_text_field( $_GET['event_time_filter'] );
            if ( 'upcoming' === $time_filter ) {
                $meta_query[] = array(
                    'key'     => '_event_date',
                    'value'   => $today,
                    'compare' => '>=',
                    'type'    => 'DATE',
                );
            } elseif ( 'past' === $time_filter ) {
                $meta_query[] = array(
                    'key'     => '_event_date',
                    'value'   => $today,
                    'compare' => '<',
                    'type'    => 'DATE',
                );
            }
        }

        // 2. Booking Status Filter
        if ( ! empty( $_GET['event_booking_filter'] ) ) {
            $booking_filter = sanitize_text_field( $_GET['event_booking_filter'] );
            $meta_query[] = array(
                'key'     => '_event_booking_status',
                'value'   => $booking_filter,
                'compare' => '=',
            );
        }

        if ( ! empty( $meta_query ) ) {
            $query->set( 'meta_query', $meta_query );
        }
    }
}
add_action( 'pre_get_posts', 'weardale_platform_filter_event_posts', 11 );

/**
 * 7. Reusable Event Query Helper
 * Supports upcoming, past, strand filters, bounding counts, and custom query scopes.
 */
function weardale_platform_get_events( $args = array() ) {
    // Standard WordPress timezone offset to prevent query discrepancies
    $timezone = get_option( 'timezone_string' );
    if ( ! empty( $timezone ) ) {
        date_default_timezone_set( $timezone );
    }
    $today = date( 'Y-m-d' );
    
    $default_args = array(
        'posts_per_page' => -1,
        'scope'          => 'upcoming', // 'upcoming', 'past', or 'all'
        'strand'         => '',         // Strand term slug (string or array)
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
        $query_args['order'] = 'DESC'; // Past events show most recent first
    } else {
        $query_args['orderby'] = 'meta_value';
        $query_args['meta_key'] = '_event_date';
        $query_args['order'] = $parsed_args['order'];
    }
    
    if ( ! empty( $meta_query ) ) {
        $query_args['meta_query'] = $meta_query;
    }
    
    // Taxonomy Filter
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
 * 8. Reusable Meta Retrieval Helper with Backward Compatibility
 */
function weardale_platform_get_event_meta( $post_id ) {
    $meta = array();
    
    // Raw inputs
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
    
    // Legacy mapping support & fallbacks
    if ( empty( $meta['venue_name'] ) ) {
        $meta['venue_name'] = $meta['location_addr'];
    }
    
    if ( empty( $meta['booking_status'] ) ) {
        $meta['booking_status'] = ! empty( $meta['booking_url'] ) ? 'booking_required' : 'no_booking_required';
    }
    
    return $meta;
}
