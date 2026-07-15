<?php
/**
 * Weardale Together Functions and Definitions
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Prevent direct access
}

/**
 * 1. Setup Theme Support Constants and Features
 */
function weardale_together_setup() {
    // Add translation support
    load_theme_textdomain( 'weardale-together', get_template_directory() . '/languages' );

    // Core supports
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script'
    ) );

    // Enable custom logo
    add_theme_support( 'custom-logo', array(
        'height'      => 80,
        'width'       => 240,
        'flex-width'  => true,
        'flex-height' => true,
    ) );

    // Register primary navigation and footer menus
    register_nav_menus( array(
        'primary-menu' => esc_html__( 'Primary Navigation Menu', 'weardale-together' ),
        'footer-menu'  => esc_html__( 'Footer Navigation Menu', 'weardale-together' ),
        'legal-menu'   => esc_html__( 'Legal Navigation Menu', 'weardale-together' ),
    ) );
}
add_action( 'after_setup_theme', 'weardale_together_setup' );

/**
 * 2. Enqueue Theme Stylesheets and JavaScript Scripts
 */
function weardale_together_scripts() {
    // Enqueue Google Fonts & Stylesheet
    wp_enqueue_style( 'weardale-together-style', get_stylesheet_uri(), array(), '1.0.0' );
    
    // Custom mobile menu script
    wp_enqueue_script( 'weardale-together-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'weardale_together_scripts' );

/**
 * 3. Dynamic Body Class Filtering for Strand Branding
 * Determines if a page is tagged to a specific strand taxonomy and injects
 * the CSS helper class (.strand-cafe, .strand-youth, etc.) into the body tag.
 */
function weardale_together_body_classes( $classes ) {
    // Check if we are viewing a single strand page or event
    if ( is_singular() ) {
        global $post;
        
        // 1. Check if the page itself has a strand slug
        $slug = $post->post_name;
        if ( in_array( $slug, array( 'root-branch-cafe', 'cafe' ) ) ) {
            $classes[] = 'strand-cafe';
        } elseif ( in_array( $slug, array( 'young-people', 'youth', 'forest-school' ) ) ) {
            $classes[] = 'strand-youth';
        } elseif ( in_array( $slug, array( 'creative-arts', 'creative-roots' ) ) ) {
            $classes[] = 'strand-creative';
        } elseif ( in_array( $slug, array( 'roots-shoots' ) ) ) {
            $classes[] = 'strand-shoots';
        }
        
        // 2. Check if the content is tagged with our Custom "Strand" taxonomy (if active)
        if ( has_term( 'cafe', 'strand', $post->ID ) ) {
            $classes[] = 'strand-cafe';
        } elseif ( has_term( 'youth', 'strand', $post->ID ) ) {
            $classes[] = 'strand-youth';
        } elseif ( has_term( 'creative', 'strand', $post->ID ) ) {
            $classes[] = 'strand-creative';
        } elseif ( has_term( 'roots-shoots', 'strand', $post->ID ) ) {
            $classes[] = 'strand-shoots';
        }
    }
    
    return $classes;
}
add_filter( 'body_class', 'weardale_together_body_classes' );

/**
 * 4. Helper function to render Customizer controls or fallbacks gracefully
 */
function weardale_together_get_theme_option( $name, $fallback = '' ) {
    $val = get_theme_mod( $name );
    return ! empty( $val ) ? $val : $fallback;
}

/**
 * 5. Get Current Strand Helper
 * Identifies if the current page is associated with one of the four principal
 * community program strands based on its slug or taxonomy term.
 */
function weardale_together_get_current_strand() {
    global $post;
    if ( ! $post ) {
        return false;
    }
    
    $slug = $post->post_name;
    
    // 1. Check page slug direct matches
    if ( in_array( $slug, array( 'root-branch-cafe', 'cafe' ) ) ) {
        return 'cafe';
    } elseif ( in_array( $slug, array( 'young-people', 'youth', 'forest-school' ) ) ) {
        return 'youth';
    } elseif ( in_array( $slug, array( 'creative-arts', 'creative-roots' ) ) ) {
        return 'creative';
    } elseif ( in_array( $slug, array( 'roots-shoots' ) ) ) {
        return 'shoots';
    }
    
    // 2. Check terms of the custom "strand" taxonomy if applied
    if ( has_term( 'cafe', 'strand', $post->ID ) ) {
        return 'cafe';
    } elseif ( has_term( 'youth', 'strand', $post->ID ) ) {
        return 'youth';
    } elseif ( has_term( 'creative', 'strand', $post->ID ) ) {
        return 'creative';
    } elseif ( has_term( 'roots-shoots', 'strand', $post->ID ) ) {
        return 'shoots';
    }
    
    return false;
}

