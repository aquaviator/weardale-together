<?php
/**
 * Event CPT and Taxonomy Registration
 *
 * @package Weardale_Platform
 * @since 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Custom Post Type: Weardale Event (weardale_event)
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
        'rewrite'            => array( 'slug' => 'whats-on', 'with_front' => false ),
        'capability_type'    => 'post',
        'has_archive'        => 'whats-on', // CRITICAL: Setting this to 'whats-on' fixes the 404 on archive list page!
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
 * Register Custom Taxonomy: Strand (strand)
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
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'strand' ),
        'show_in_rest'      => true,
    );

    register_taxonomy( 'strand', array( 'post', 'page', 'weardale_event' ), $args );
}
add_action( 'init', 'weardale_platform_register_strand_taxonomy' );
