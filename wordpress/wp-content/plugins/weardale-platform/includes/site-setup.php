<?php
/**
 * Site Configuration and Navigation Bootstrap Engine
 *
 * Implements idempotent site navigation setup, menu registration,
 * and the "Weardale Site Setup" administrator utility.
 *
 * @package Weardale_Platform
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the Site Setup Submenu under Tools
 */
function weardale_platform_add_site_setup_submenu() {
    add_submenu_page(
        'tools.php',
        __( 'Weardale Site Setup', 'weardale-platform' ),
        __( 'Weardale Site Setup', 'weardale-platform' ),
        'manage_options',
        'weardale-site-setup',
        'weardale_platform_render_site_setup_page'
    );
}
add_action( 'admin_menu', 'weardale_platform_add_site_setup_submenu' );

/**
 * Idempotent Site Navigation Setup Routine
 *
 * Creates Primary, Footer, and Legal menus if unassigned or missing,
 * and populates them with Approved Information Architecture links.
 *
 * @return array Detailed results of the bootstrap execution.
 */
function weardale_platform_bootstrap_navigation() {
    $results = array(
        'primary' => array( 'status' => 'skipped', 'message' => '' ),
        'footer'  => array( 'status' => 'skipped', 'message' => '' ),
        'legal'   => array( 'status' => 'skipped', 'message' => '' ),
    );

    // Get current menu locations
    $locations = get_theme_mod( 'nav_menu_locations' );
    if ( ! is_array( $locations ) ) {
        $locations = array();
    }

    // --- 1. PRIMARY NAVIGATION MENU ---
    $primary_assigned = ! empty( $locations['primary-menu'] ) && is_nav_menu( $locations['primary-menu'] );
    if ( ! $primary_assigned ) {
        $primary_menu_obj = wp_get_nav_menu_object( 'Primary Navigation Menu' );
        if ( ! $primary_menu_obj ) {
            $primary_menu_id = wp_create_nav_menu( 'Primary Navigation Menu' );
        } else {
            $primary_menu_id = $primary_menu_obj->term_id;
        }

        if ( ! is_wp_error( $primary_menu_id ) && $primary_menu_id ) {
            $menu_items = wp_get_nav_menu_items( $primary_menu_id );
            if ( empty( $menu_items ) ) {
                $items_to_add = array(
                    array( 'title' => 'Home', 'url' => home_url( '/' ) ),
                    array( 'title' => 'Root & Branch Café', 'url' => home_url( '/cafe/' ) ),
                    array( 'title' => 'Young People', 'url' => home_url( '/young-people/' ) ),
                    array( 'title' => 'Creative Arts', 'url' => home_url( '/creative-arts/' ) ),
                    array( 'title' => 'Roots & Shoots', 'url' => home_url( '/roots-shoots/' ) ),
                    array( 'title' => 'What’s On', 'url' => home_url( '/whats-on/' ) ),
                    array( 'title' => 'Community Directory', 'url' => get_post_type_archive_link( 'weardale_directory' ) ?: home_url( '/directory/' ) ),
                    array( 'title' => 'About WT', 'url' => home_url( '/about/' ) ),
                );

                $position = 1;
                foreach ( $items_to_add as $item ) {
                    wp_update_nav_menu_item( $primary_menu_id, 0, array(
                        'menu-item-title'    => $item['title'],
                        'menu-item-url'      => $item['url'],
                        'menu-item-status'   => 'publish',
                        'menu-item-type'     => 'custom',
                        'menu-item-position' => $position++,
                    ) );
                }
                $results['primary'] = array( 'status' => 'created', 'message' => __( 'Primary Navigation Menu created and populated with 8 items.', 'weardale-platform' ) );
            } else {
                $results['primary'] = array( 'status' => 'assigned', 'message' => __( 'Primary Navigation Menu already exists with items. Menu assigned to location.', 'weardale-platform' ) );
            }

            // Assign to location
            $locations['primary-menu'] = $primary_menu_id;
        }
    } else {
        $results['primary'] = array( 'status' => 'skipped', 'message' => __( 'Primary Navigation Menu is already assigned and configured.', 'weardale-platform' ) );
    }

    // --- 2. FOOTER NAVIGATION MENU ---
    $footer_assigned = ! empty( $locations['footer-menu'] ) && is_nav_menu( $locations['footer-menu'] );
    if ( ! $footer_assigned ) {
        $footer_menu_obj = wp_get_nav_menu_object( 'Footer Navigation Menu' );
        if ( ! $footer_menu_obj ) {
            $footer_menu_id = wp_create_nav_menu( 'Footer Navigation Menu' );
        } else {
            $footer_menu_id = $footer_menu_obj->term_id;
        }

        if ( ! is_wp_error( $footer_menu_id ) && $footer_menu_id ) {
            $menu_items = wp_get_nav_menu_items( $footer_menu_id );
            if ( empty( $menu_items ) ) {
                $items_to_add = array(
                    array( 'title' => 'News & Blog', 'url' => home_url( '/news-blog/' ) ),
                    array( 'title' => 'Volunteer With Us', 'url' => home_url( '/volunteer/' ) ),
                    array( 'title' => 'Newsletter Sign-up', 'url' => home_url( '/newsletter/' ) ),
                    array( 'title' => 'Get In Touch', 'url' => home_url( '/contact-us/' ) ),
                );

                $position = 1;
                foreach ( $items_to_add as $item ) {
                    wp_update_nav_menu_item( $footer_menu_id, 0, array(
                        'menu-item-title'    => $item['title'],
                        'menu-item-url'      => $item['url'],
                        'menu-item-status'   => 'publish',
                        'menu-item-type'     => 'custom',
                        'menu-item-position' => $position++,
                    ) );
                }
                $results['footer'] = array( 'status' => 'created', 'message' => __( 'Footer Navigation Menu created and populated with 4 items.', 'weardale-platform' ) );
            } else {
                $results['footer'] = array( 'status' => 'assigned', 'message' => __( 'Footer Navigation Menu already exists with items. Menu assigned to location.', 'weardale-platform' ) );
            }

            $locations['footer-menu'] = $footer_menu_id;
        }
    } else {
        $results['footer'] = array( 'status' => 'skipped', 'message' => __( 'Footer Navigation Menu is already assigned and configured.', 'weardale-platform' ) );
    }

    // --- 3. LEGAL NAVIGATION MENU ---
    $legal_assigned = ! empty( $locations['legal-menu'] ) && is_nav_menu( $locations['legal-menu'] );
    if ( ! $legal_assigned ) {
        $legal_menu_obj = wp_get_nav_menu_object( 'Legal Navigation Menu' );
        if ( ! $legal_menu_obj ) {
            $legal_menu_id = wp_create_nav_menu( 'Legal Navigation Menu' );
        } else {
            $legal_menu_id = $legal_menu_obj->term_id;
        }

        if ( ! is_wp_error( $legal_menu_id ) && $legal_menu_id ) {
            $menu_items = wp_get_nav_menu_items( $legal_menu_id );
            if ( empty( $menu_items ) ) {
                $items_to_add = array(
                    array( 'title' => 'Privacy Notice', 'url' => home_url( '/privacy-notice/' ) ),
                );

                $position = 1;
                foreach ( $items_to_add as $item ) {
                    wp_update_nav_menu_item( $legal_menu_id, 0, array(
                        'menu-item-title'    => $item['title'],
                        'menu-item-url'      => $item['url'],
                        'menu-item-status'   => 'publish',
                        'menu-item-type'     => 'custom',
                        'menu-item-position' => $position++,
                    ) );
                }
                $results['legal'] = array( 'status' => 'created', 'message' => __( 'Legal Navigation Menu created and populated with 1 item.', 'weardale-platform' ) );
            } else {
                $results['legal'] = array( 'status' => 'assigned', 'message' => __( 'Legal Navigation Menu already exists with items. Menu assigned to location.', 'weardale-platform' ) );
            }

            $locations['legal-menu'] = $legal_menu_id;
        }
    } else {
        $results['legal'] = array( 'status' => 'skipped', 'message' => __( 'Legal Navigation Menu is already assigned and configured.', 'weardale-platform' ) );
    }

    // Update locations theme mod
    set_theme_mod( 'nav_menu_locations', $locations );

    return $results;
}

/**
 * Auto-bootstrap core menus on dashboard entry (idempotent, runs once)
 */
function weardale_platform_auto_bootstrap_menus() {
    if ( get_option( 'weardale_menus_bootstrapped_v1' ) !== '1' ) {
        weardale_platform_bootstrap_navigation();
        update_option( 'weardale_menus_bootstrapped_v1', '1' );
    }
}
add_action( 'admin_init', 'weardale_platform_auto_bootstrap_menus' );

/**
 * Render the Site Setup admin interface
 */
function weardale_platform_render_site_setup_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'weardale-platform' ) );
    }

    $results = null;
    if ( isset( $_POST['wt_bootstrap_nonce'] ) && wp_verify_nonce( $_POST['wt_bootstrap_nonce'], 'wt_bootstrap_action' ) ) {
        $results = weardale_platform_bootstrap_navigation();
    }

    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Weardale Site Setup', 'weardale-platform' ); ?></h1>
        <p><?php esc_html_e( 'Quickly bootstrap core site configuration, menus, and layout states for Weardale Together.', 'weardale-platform' ); ?></p>

        <?php if ( $results ) : ?>
            <div class="notice notice-success is-dismissible" style="padding: 15px; margin-top: 15px; border-left-color: #16a34a;">
                <h3><?php esc_html_e( 'Site Navigation Bootstrap Complete', 'weardale-platform' ); ?></h3>
                <ul style="list-style-type: disc; margin-left: 20px;">
                    <?php foreach ( $results as $menu_key => $info ) : ?>
                        <li>
                            <strong><?php echo esc_html( ucfirst( $menu_key ) ); ?>:</strong> 
                            <?php echo esc_html( $info['message'] ); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card" style="max-width: 600px; margin-top: 20px; padding: 20px; background: #fff; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04);">
            <h2>🗺️ <?php esc_html_e( 'Bootstrap Site Navigation', 'weardale-platform' ); ?></h2>
            <p><?php esc_html_e( 'This tool will automatically create and assign the Primary, Footer, and Legal navigation menus. It is safe to run repeatedly: it will never overwrite custom menus or items that you have already configured manually.', 'weardale-platform' ); ?></p>
            
            <form method="post" style="margin-top: 20px;">
                <?php wp_nonce_field( 'wt_bootstrap_action', 'wt_bootstrap_nonce' ); ?>
                <input type="submit" class="button button-primary button-large" value="<?php esc_attr_e( 'Bootstrap Site Navigation', 'weardale-platform' ); ?>">
            </form>
        </div>
    </div>
    <?php
}
