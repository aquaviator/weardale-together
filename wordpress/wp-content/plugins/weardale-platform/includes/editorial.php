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
        'rewrite'            => array( 'slug' => 'events-list' ),
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
        __( 'Weardale Event Details', 'weardale-platform' ),
        'weardale_platform_render_event_meta_box',
        'weardale_event',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'weardale_platform_add_event_meta_boxes' );

// Render Meta Box Form fields
function weardale_platform_render_event_meta_box( $post ) {
    // Add nonce token for security checks
    wp_nonce_field( 'weardale_event_meta_nonce_action', 'weardale_event_meta_nonce_field' );

    // Retrieve existing saved metadata
    $event_date     = get_post_meta( $post->ID, '_event_date', true );
    $event_time     = get_post_meta( $post->ID, '_event_time', true );
    $event_location = get_post_meta( $post->ID, '_event_location', true );
    $event_cost     = get_post_meta( $post->ID, '_event_cost', true );
    ?>
    <table class="form-table">
        <tr>
            <th><label for="event_date"><?php esc_html_e( 'Event Date', 'weardale-platform' ); ?></label></th>
            <td>
                <input type="date" id="event_date" name="event_date" value="<?php echo esc_attr( $event_date ); ?>" class="regular-text">
                <p class="description"><?php esc_html_e( 'Select the date when this activity takes place.', 'weardale-platform' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="event_time"><?php esc_html_e( 'Event Timing', 'weardale-platform' ); ?></label></th>
            <td>
                <input type="text" id="event_time" name="event_time" value="<?php echo esc_attr( $event_time ); ?>" placeholder="e.g. 10:00 AM - 1:00 PM" class="regular-text">
                <p class="description"><?php esc_html_e( 'Specify the operating hours or session schedule.', 'weardale-platform' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="event_location"><?php esc_html_e( 'Location / Venue', 'weardale-platform' ); ?></label></th>
            <td>
                <input type="text" id="event_location" name="event_location" value="<?php echo esc_attr( $event_location ); ?>" placeholder="e.g. Stanhope Hub Community Garden" class="regular-text">
                <p class="description"><?php esc_html_e( 'Specify where residents should arrive.', 'weardale-platform' ); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="event_cost"><?php esc_html_e( 'Cost / Entry Fee', 'weardale-platform' ); ?></label></th>
            <td>
                <input type="text" id="event_cost" name="event_cost" value="<?php echo esc_attr( $event_cost ); ?>" placeholder="e.g. Free (Donations Welcome) or £5 per child" class="regular-text">
                <p class="description"><?php esc_html_e( 'Admission rules or workshop supply fees.', 'weardale-platform' ); ?></p>
            </td>
        </tr>
    </table>
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
    if ( isset( $_POST['event_date'] ) ) {
        update_post_meta( $post_id, '_event_date', sanitize_text_field( $_POST['event_date'] ) );
    }
    if ( isset( $_POST['event_time'] ) ) {
        update_post_meta( $post_id, '_event_time', sanitize_text_field( $_POST['event_time'] ) );
    }
    if ( isset( $_POST['event_location'] ) ) {
        update_post_meta( $post_id, '_event_location', sanitize_text_field( $_POST['event_location'] ) );
    }
    if ( isset( $_POST['event_cost'] ) ) {
        update_post_meta( $post_id, '_event_cost', sanitize_text_field( $_POST['event_cost'] ) );
    }
}
add_action( 'save_post', 'weardale_platform_save_event_meta' );

/**
 * 4. Custom Columns for Events Admin Dashboard Table Listing
 * Allows easy, non-technical tracking of schedules directly in the admin lists.
 */
function weardale_platform_set_event_columns( $columns ) {
    $new_columns = array();
    foreach ( $columns as $key => $title ) {
        if ( $key === 'date' ) {
            // Insert schedules before post publish date
            $new_columns['event_date']     = __( 'Scheduled Date', 'weardale-platform' );
            $new_columns['event_location'] = __( 'Venue', 'weardale-platform' );
        }
        $new_columns[$key] = $title;
    }
    return $new_columns;
}
add_filter( 'manage_weardale_event_posts_columns', 'weardale_platform_set_event_columns' );

function weardale_platform_display_event_columns( $column, $post_id ) {
    switch ( $column ) {
        case 'event_date':
            $date = get_post_meta( $post_id, '_event_date', true );
            echo ! empty( $date ) ? esc_html( date( 'F j, Y', strtotime( $date ) ) ) : '<em>- None -</em>';
            break;
        case 'event_location':
            $location = get_post_meta( $post_id, '_event_location', true );
            echo ! empty( $location ) ? esc_html( $location ) : '<em>- None -</em>';
            break;
    }
}
add_action( 'manage_weardale_event_posts_custom_column', 'weardale_platform_display_event_columns', 10, 2 );
