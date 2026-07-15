<?php
/**
 * Directory CPT and Taxonomy Registration
 *
 * @package Weardale_Platform
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Custom Post Type: Weardale Directory (weardale_directory)
 */
function weardale_platform_register_directory_cpt() {
    $labels = array(
        'name'               => _x( 'Directory', 'post type general name', 'weardale-platform' ),
        'singular_name'      => _x( 'Directory Entry', 'post type singular name', 'weardale-platform' ),
        'menu_name'          => _x( 'WT Directory', 'admin menu', 'weardale-platform' ),
        'name_admin_bar'     => _x( 'Directory Entry', 'add new on admin bar', 'weardale-platform' ),
        'add_new'            => _x( 'Add New Entry', 'directory', 'weardale-platform' ),
        'add_new_item'       => __( 'Add New Directory Entry', 'weardale-platform' ),
        'new_item'           => __( 'New Entry', 'weardale-platform' ),
        'edit_item'          => __( 'Edit Entry', 'weardale-platform' ),
        'view_item'          => __( 'View Entry', 'weardale-platform' ),
        'all_items'          => __( 'All Directory Entries', 'weardale-platform' ),
        'search_items'       => __( 'Search Directory', 'weardale-platform' ),
        'parent_item_colon'  => __( 'Parent Entries:', 'weardale-platform' ),
        'not_found'          => __( 'No directory entries found.', 'weardale-platform' ),
        'not_found_in_trash' => __( 'No directory entries found in Trash.', 'weardale-platform' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'directory', 'with_front' => false ),
        'capability_type'    => 'post',
        'has_archive'        => 'directory',
        'hierarchical'       => false,
        'menu_position'      => 6,
        'menu_icon'          => 'dashicons-index-card',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
        'show_in_rest'       => true, // Enable Block Editor
    );

    register_post_type( 'weardale_directory', $args );
}
add_action( 'init', 'weardale_platform_register_directory_cpt' );

/**
 * Register Supporting Taxonomies: Directory Type, Village, Service Area
 */
function weardale_platform_register_directory_taxonomies() {
    // 1. Directory Type (directory_type)
    $type_labels = array(
        'name'              => _x( 'Directory Types', 'taxonomy general name', 'weardale-platform' ),
        'singular_name'     => _x( 'Directory Type', 'taxonomy singular name', 'weardale-platform' ),
        'search_items'      => __( 'Search Directory Types', 'weardale-platform' ),
        'all_items'         => __( 'All Directory Types', 'weardale-platform' ),
        'parent_item'       => __( 'Parent Type', 'weardale-platform' ),
        'parent_item_colon' => __( 'Parent Type:', 'weardale-platform' ),
        'edit_item'         => __( 'Edit Directory Type', 'weardale-platform' ),
        'update_item'       => __( 'Update Directory Type', 'weardale-platform' ),
        'add_new_item'      => __( 'Add New Directory Type', 'weardale-platform' ),
        'new_item_name'     => __( 'New Directory Type Name', 'weardale-platform' ),
        'menu_name'         => __( 'Directory Types', 'weardale-platform' ),
    );

    $type_args = array(
        'hierarchical'      => true,
        'labels'            => $type_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'directory-type' ),
        'show_in_rest'      => true,
    );

    register_taxonomy( 'directory_type', array( 'weardale_directory' ), $type_args );

    // 2. Village (village)
    $village_labels = array(
        'name'              => _x( 'Villages', 'taxonomy general name', 'weardale-platform' ),
        'singular_name'     => _x( 'Village', 'taxonomy singular name', 'weardale-platform' ),
        'search_items'      => __( 'Search Villages', 'weardale-platform' ),
        'all_items'         => __( 'All Villages', 'weardale-platform' ),
        'parent_item'       => __( 'Parent Village', 'weardale-platform' ),
        'parent_item_colon' => __( 'Parent Village:', 'weardale-platform' ),
        'edit_item'         => __( 'Edit Village', 'weardale-platform' ),
        'update_item'       => __( 'Update Village', 'weardale-platform' ),
        'add_new_item'      => __( 'Add New Village', 'weardale-platform' ),
        'new_item_name'     => __( 'New Village Name', 'weardale-platform' ),
        'menu_name'         => __( 'Villages', 'weardale-platform' ),
    );

    $village_args = array(
        'hierarchical'      => true,
        'labels'            => $village_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'village' ),
        'show_in_rest'      => true,
    );

    register_taxonomy( 'village', array( 'weardale_directory', 'weardale_event' ), $village_args );

    // 3. Service Area (service_area)
    $area_labels = array(
        'name'              => _x( 'Service Areas', 'taxonomy general name', 'weardale-platform' ),
        'singular_name'     => _x( 'Service Area', 'taxonomy singular name', 'weardale-platform' ),
        'search_items'      => __( 'Search Service Areas', 'weardale-platform' ),
        'all_items'         => __( 'All Service Areas', 'weardale-platform' ),
        'parent_item'       => __( 'Parent Service Area', 'weardale-platform' ),
        'parent_item_colon' => __( 'Parent Service Area:', 'weardale-platform' ),
        'edit_item'         => __( 'Edit Service Area', 'weardale-platform' ),
        'update_item'       => __( 'Update Service Area', 'weardale-platform' ),
        'add_new_item'      => __( 'Add New Service Area', 'weardale-platform' ),
        'new_item_name'     => __( 'New Service Area Name', 'weardale-platform' ),
        'menu_name'         => __( 'Service Areas', 'weardale-platform' ),
    );

    $area_args = array(
        'hierarchical'      => true,
        'labels'            => $area_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'service-area' ),
        'show_in_rest'      => true,
    );

    register_taxonomy( 'service_area', array( 'weardale_directory' ), $area_args );
}
add_action( 'init', 'weardale_platform_register_directory_taxonomies' );
