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
 * Check if a URL destination exists in the current site database.
 * 
 * @param string $url The destination URL to check.
 * @return bool True if destination exists/is valid, false otherwise.
 */
function weardale_platform_destination_exists( $url ) {
    // 1. Home page always exists
    if ( $url === home_url( '/' ) || $url === home_url() ) {
        return true;
    }

    // 2. Archive pages for registered CPTs are always considered to exist
    $directory_archive = get_post_type_archive_link( 'weardale_directory' );
    $event_archive = get_post_type_archive_link( 'weardale_event' );

    if ( $directory_archive && $url === $directory_archive ) {
        return true;
    }
    if ( $event_archive && $url === $event_archive ) {
        return true;
    }

    // 3. Check for specific page slugs
    $home_url = home_url();
    if ( strpos( $url, $home_url ) !== 0 ) {
        return false;
    }

    $path = str_replace( $home_url, '', $url );
    $slug = trim( $path, '/' );

    if ( empty( $slug ) ) {
        return true;
    }

    // Resolve hierarchical pages or standard slugs
    $page = get_page_by_path( $slug );
    if ( $page && $page->post_status === 'publish' ) {
        return true;
    }

    return false;
}

/**
 * Idempotent Site Navigation Setup Routine
 *
 * Creates Primary, Footer, and Legal menus if unassigned or missing,
 * and populates them with Approved Information Architecture links if they exist.
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
            // Only populate if the menu has no items to prevent duplicate addition
            if ( empty( $menu_items ) ) {
                $candidates = array(
                    array( 'title' => 'Home', 'url' => home_url( '/' ) ),
                    array( 'title' => 'Root & Branch Café', 'url' => home_url( '/cafe/' ) ),
                    array( 'title' => 'Young People', 'url' => home_url( '/young-people/' ) ),
                    array( 'title' => 'Creative Arts', 'url' => home_url( '/creative-arts/' ) ),
                    array( 'title' => 'Roots & Shoots', 'url' => home_url( '/roots-shoots/' ) ),
                    array( 'title' => 'What’s On', 'url' => get_post_type_archive_link( 'weardale_event' ) ?: home_url( '/whats-on/' ) ),
                    array( 'title' => 'Directory', 'url' => get_post_type_archive_link( 'weardale_directory' ) ?: home_url( '/directory/' ) ),
                    array( 'title' => 'About WT', 'url' => home_url( '/about/' ) ),
                );

                $created = 0;
                $skipped = 0;
                $position = 1;

                foreach ( $candidates as $item ) {
                    if ( weardale_platform_destination_exists( $item['url'] ) ) {
                        wp_update_nav_menu_item( $primary_menu_id, 0, array(
                            'menu-item-title'    => $item['title'],
                            'menu-item-url'      => $item['url'],
                            'menu-item-status'   => 'publish',
                            'menu-item-type'     => 'custom',
                            'menu-item-position' => $position++,
                        ) );
                        $created++;
                    } else {
                        $skipped++;
                    }
                }
                $results['primary'] = array(
                    'status' => 'created',
                    'message' => sprintf( __( 'Primary Navigation Menu created: %d items added, %d missing destinations skipped.', 'weardale-platform' ), $created, $skipped )
                );
            } else {
                $results['primary'] = array(
                    'status' => 'assigned',
                    'message' => __( 'Primary Navigation Menu already exists with items. Existing menu items preserved and assigned to location.', 'weardale-platform' )
                );
            }

            // Assign to location
            $locations['primary-menu'] = $primary_menu_id;
        }
    } else {
        $results['primary'] = array(
            'status' => 'preserved',
            'message' => __( 'Existing configuration preserved. Primary Navigation Menu is already assigned and manually configured items remain untouched.', 'weardale-platform' )
        );
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
                $candidates = array(
                    array( 'title' => 'News & Blog', 'url' => home_url( '/news-blog/' ) ),
                    array( 'title' => 'Volunteer With Us', 'url' => home_url( '/volunteer/' ) ),
                    array( 'title' => 'Newsletter Sign-up', 'url' => home_url( '/newsletter/' ) ),
                    array( 'title' => 'Get In Touch', 'url' => home_url( '/contact-us/' ) ),
                );

                $created = 0;
                $skipped = 0;
                $position = 1;

                foreach ( $candidates as $item ) {
                    if ( weardale_platform_destination_exists( $item['url'] ) ) {
                        wp_update_nav_menu_item( $footer_menu_id, 0, array(
                            'menu-item-title'    => $item['title'],
                            'menu-item-url'      => $item['url'],
                            'menu-item-status'   => 'publish',
                            'menu-item-type'     => 'custom',
                            'menu-item-position' => $position++,
                        ) );
                        $created++;
                    } else {
                        $skipped++;
                    }
                }
                $results['footer'] = array(
                    'status' => 'created',
                    'message' => sprintf( __( 'Footer Navigation Menu created: %d items added, %d missing destinations skipped.', 'weardale-platform' ), $created, $skipped )
                );
            } else {
                $results['footer'] = array(
                    'status' => 'assigned',
                    'message' => __( 'Footer Navigation Menu already exists with items. Existing menu items preserved and assigned to location.', 'weardale-platform' )
                );
            }

            $locations['footer-menu'] = $footer_menu_id;
        }
    } else {
        $results['footer'] = array(
            'status' => 'preserved',
            'message' => __( 'Existing configuration preserved. Footer Navigation Menu is already assigned and manually configured items remain untouched.', 'weardale-platform' )
        );
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
                $candidates = array(
                    array( 'title' => 'Privacy Notice', 'url' => home_url( '/privacy-notice/' ) ),
                );

                $created = 0;
                $skipped = 0;
                $position = 1;

                foreach ( $candidates as $item ) {
                    if ( weardale_platform_destination_exists( $item['url'] ) ) {
                        wp_update_nav_menu_item( $legal_menu_id, 0, array(
                            'menu-item-title'    => $item['title'],
                            'menu-item-url'      => $item['url'],
                            'menu-item-status'   => 'publish',
                            'menu-item-type'     => 'custom',
                            'menu-item-position' => $position++,
                        ) );
                        $created++;
                    } else {
                        $skipped++;
                    }
                }
                $results['legal'] = array(
                    'status' => 'created',
                    'message' => sprintf( __( 'Legal Navigation Menu created: %d items added, %d missing destinations skipped.', 'weardale-platform' ), $created, $skipped )
                );
            } else {
                $results['legal'] = array(
                    'status' => 'assigned',
                    'message' => __( 'Legal Navigation Menu already exists with items. Existing menu items preserved and assigned to location.', 'weardale-platform' )
                );
            }

            $locations['legal-menu'] = $legal_menu_id;
        }
    } else {
        $results['legal'] = array(
            'status' => 'preserved',
            'message' => __( 'Existing configuration preserved. Legal Navigation Menu is already assigned and manually configured items remain untouched.', 'weardale-platform' )
        );
    }

    // Update locations theme mod
    set_theme_mod( 'nav_menu_locations', $locations );

    return $results;
}

/**
 * Display restrained administrator notice if Primary Navigation location is unassigned.
 */
function weardale_platform_site_setup_admin_notice() {
    // Only display to administrators with 'manage_options' capability
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Prevent appearing on the public frontend
    if ( ! is_admin() ) {
        return;
    }

    $locations = get_theme_mod( 'nav_menu_locations' );
    $primary_assigned = ! empty( $locations['primary-menu'] ) && is_nav_menu( $locations['primary-menu'] );

    if ( ! $primary_assigned ) {
        $setup_url = admin_url( 'tools.php?page=weardale-site-setup' );
        ?>
        <div class="notice notice-warning is-dismissible">
            <p>
                <?php
                printf(
                    wp_kses(
                        __( 'Weardale Together site navigation has not been configured. Please visit <a href="%s">Weardale Site Setup</a> to bootstrap standard menus.', 'weardale-platform' ),
                        array( 'a' => array( 'href' => array() ) )
                    ),
                    esc_url( $setup_url )
                );
                ?>
            </p>
        </div>
        <?php
    }
}
add_action( 'admin_notices', 'weardale_platform_site_setup_admin_notice' );

/**
 * Render the Site Setup admin interface
 */
function weardale_platform_render_site_setup_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'weardale-platform' ) );
    }

    $results        = null;
    $settings_saved = false;
    $seed_results   = null;

    // 1. Navigation Bootstrapper
    if ( isset( $_POST['wt_bootstrap_nonce'] ) && wp_verify_nonce( $_POST['wt_bootstrap_nonce'], 'wt_bootstrap_action' ) ) {
        $results = weardale_platform_bootstrap_navigation();
    }

    // 2. Settings Saver
    if ( isset( $_POST['wt_settings_nonce'] ) && wp_verify_nonce( $_POST['wt_settings_nonce'], 'wt_settings_action' ) ) {
        update_option( 'weardale_enquiry_enabled', sanitize_text_field( $_POST['weardale_enquiry_enabled'] ) );
        update_option( 'weardale_enquiry_recipient', sanitize_email( $_POST['weardale_enquiry_recipient'] ) );
        update_option( 'weardale_enquiry_reply_to', sanitize_text_field( $_POST['weardale_enquiry_reply_to'] ) );
        update_option( 'weardale_enquiry_confirmation', sanitize_textarea_field( $_POST['weardale_enquiry_confirmation'] ) );
        update_option( 'weardale_mailchimp_url', esc_url_raw( $_POST['weardale_mailchimp_url'] ) );

        update_option( 'weardale_contact_address', sanitize_textarea_field( $_POST['weardale_contact_address'] ) );
        update_option( 'weardale_contact_phone', sanitize_text_field( $_POST['weardale_contact_phone'] ) );
        update_option( 'weardale_contact_email', sanitize_email( $_POST['weardale_contact_email'] ) );
        update_option( 'weardale_contact_opening_hours', sanitize_textarea_field( $_POST['weardale_contact_opening_hours'] ) );
        update_option( 'weardale_contact_directions', sanitize_textarea_field( $_POST['weardale_contact_directions'] ) );
        update_option( 'weardale_contact_social_facebook', esc_url_raw( $_POST['weardale_contact_social_facebook'] ) );
        update_option( 'weardale_contact_social_instagram', esc_url_raw( $_POST['weardale_contact_social_instagram'] ) );

        // Organisation Details
        update_option( 'weardale_organisation_name', sanitize_text_field( $_POST['weardale_organisation_name'] ) );
        update_option( 'weardale_organisation_short_name', sanitize_text_field( $_POST['weardale_organisation_short_name'] ) );
        update_option( 'weardale_organisation_company_number', sanitize_text_field( $_POST['weardale_organisation_company_number'] ) );
        update_option( 'weardale_organisation_charity_number', sanitize_text_field( $_POST['weardale_organisation_charity_number'] ) );

        // Legal Page Selectors
        update_option( 'weardale_legal_privacy_page', isset( $_POST['weardale_legal_privacy_page'] ) ? intval( $_POST['weardale_legal_privacy_page'] ) : 0 );
        update_option( 'weardale_legal_cookie_page', isset( $_POST['weardale_legal_cookie_page'] ) ? intval( $_POST['weardale_legal_cookie_page'] ) : 0 );
        update_option( 'weardale_legal_terms_page', isset( $_POST['weardale_legal_terms_page'] ) ? intval( $_POST['weardale_legal_terms_page'] ) : 0 );

        $settings_saved = true;
    }

    // 3. Demo Content Seeder
    if ( isset( $_POST['wt_seed_nonce'] ) && wp_verify_nonce( $_POST['wt_seed_nonce'], 'wt_seed_action' ) ) {
        if ( function_exists( 'weardale_platform_seed_participation_data' ) ) {
            $seed_results = weardale_platform_seed_participation_data();
        }
    }

    // Retrieve settings
    $enquiry_enabled      = get_option( 'weardale_enquiry_enabled', 'yes' );
    $enquiry_recipient    = get_option( 'weardale_enquiry_recipient', get_option( 'admin_email' ) );
    $enquiry_reply_to     = get_option( 'weardale_enquiry_reply_to', 'yes' );
    $enquiry_confirmation = get_option( 'weardale_enquiry_confirmation', __( 'Thank you for contacting Weardale Together. Your message has been received, and our team of volunteers and local staff will read it shortly. As a small, grassroots community organization, we appreciate your patience and will get back to you as soon as possible.', 'weardale-platform' ) );
    $mailchimp_url        = get_option( 'weardale_mailchimp_url' );

    $contact_address      = get_option( 'weardale_contact_address' );
    $contact_phone        = get_option( 'weardale_contact_phone' );
    $contact_email        = get_option( 'weardale_contact_email' );
    $contact_opening_hours= get_option( 'weardale_contact_opening_hours' );
    $contact_directions   = get_option( 'weardale_contact_directions' );
    $contact_social_fb    = get_option( 'weardale_contact_social_facebook' );
    $contact_social_ig    = get_option( 'weardale_contact_social_instagram' );

    $org_name             = get_option( 'weardale_organisation_name' );
    $org_short_name       = get_option( 'weardale_organisation_short_name', 'WT' );
    $org_company          = get_option( 'weardale_organisation_company_number' );
    $org_charity          = get_option( 'weardale_organisation_charity_number' );
    $legal_privacy_page   = get_option( 'weardale_legal_privacy_page' );
    $legal_cookie_page    = get_option( 'weardale_legal_cookie_page' );
    $legal_terms_page     = get_option( 'weardale_legal_terms_page' );

    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Weardale Site Setup', 'weardale-platform' ); ?></h1>
        <p><?php esc_html_e( 'Quickly bootstrap core site configuration, menus, layout states, and participation options for Weardale Together.', 'weardale-platform' ); ?></p>

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

        <?php if ( $settings_saved ) : ?>
            <div class="notice notice-success is-dismissible" style="padding: 15px; margin-top: 15px; border-left-color: #16a34a;">
                <p><strong><?php esc_html_e( 'Settings saved successfully.', 'weardale-platform' ); ?></strong></p>
            </div>
        <?php endif; ?>

        <?php if ( $seed_results ) : ?>
            <div class="notice notice-success is-dismissible" style="padding: 15px; margin-top: 15px; border-left-color: #16a34a;">
                <h3><?php esc_html_e( 'Development Demo Seeding Complete', 'weardale-platform' ); ?></h3>
                <p>
                    <?php 
                    printf( 
                        esc_html__( 'Created %d new records, and skipped %d existing records.', 'weardale-platform' ), 
                        intval( $seed_results['created'] ), 
                        intval( $seed_results['skipped'] ) 
                    ); 
                    ?>
                </p>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr; gap: 20px; margin-top: 20px; max-width: 1200px; align-items: start;">
            <?php if ( window_width_min_768() ) : ?>
            <style>
                @media (min-width: 768px) {
                    .wt-setup-grid {
                        display: grid !important;
                        grid-template-columns: 2fr 1fr !important;
                        gap: 30px !important;
                    }
                }
            </style>
            <?php endif; ?>
            <div class="wt-setup-grid" style="display: flex; flex-direction: column; gap: 20px;">
                
                <!-- LEFT COLUMN: Main Config Form -->
                <div>
                    <form method="post" action="">
                        <?php wp_nonce_field( 'wt_settings_action', 'wt_settings_nonce' ); ?>
                        
                        <!-- Box 1: Enquiry settings -->
                        <div class="card" style="margin-bottom: 20px; padding: 20px; background: #fff; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04); max-width: 100%;">
                            <h2>📬 <?php esc_html_e( 'Participation Enquiry Settings', 'weardale-platform' ); ?></h2>
                            <p class="description" style="margin-bottom: 20px;"><?php esc_html_e( 'Configure validation constraints, delivery targets, and confirmation wording for on-site contact/enquiry forms.', 'weardale-platform' ); ?></p>
                            
                            <table class="form-table" role="presentation">
                                <tr>
                                    <th scope="row"><label for="weardale_enquiry_enabled"><?php esc_html_e( 'Enquiry System Status', 'weardale-platform' ); ?></label></th>
                                    <td>
                                        <select id="weardale_enquiry_enabled" name="weardale_enquiry_enabled">
                                            <option value="yes" <?php selected( $enquiry_enabled, 'yes' ); ?>><?php esc_html_e( 'Enabled (Active on-site)', 'weardale-platform' ); ?></option>
                                            <option value="no" <?php selected( $enquiry_enabled, 'no' ); ?>><?php esc_html_e( 'Disabled (Temporarily Offline)', 'weardale-platform' ); ?></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="weardale_enquiry_recipient"><?php esc_html_e( 'Notification Recipient Email', 'weardale-platform' ); ?></label></th>
                                    <td>
                                        <input type="email" id="weardale_enquiry_recipient" name="weardale_enquiry_recipient" class="regular-text" required value="<?php echo esc_attr( $enquiry_recipient ); ?>">
                                        <p class="description"><?php esc_html_e( 'All form submissions from visitors are validated, formatted, and emailed here.', 'weardale-platform' ); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="weardale_enquiry_reply_to"><?php esc_html_e( 'Inject "Reply-To" Header', 'weardale-platform' ); ?></label></th>
                                    <td>
                                        <select id="weardale_enquiry_reply_to" name="weardale_enquiry_reply_to">
                                            <option value="yes" <?php selected( $enquiry_reply_to, 'yes' ); ?>><?php esc_html_e( 'Yes (Allow clicking "Reply" directly to visitors)', 'weardale-platform' ); ?></option>
                                            <option value="no" <?php selected( $enquiry_reply_to, 'no' ); ?>><?php esc_html_e( 'No (Safer, use default mail sender header)', 'weardale-platform' ); ?></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="weardale_enquiry_confirmation"><?php esc_html_e( 'On-Screen Confirmation Wording', 'weardale-platform' ); ?></label></th>
                                    <td>
                                        <textarea id="weardale_enquiry_confirmation" name="weardale_enquiry_confirmation" rows="4" class="large-text"><?php echo esc_textarea( $enquiry_confirmation ); ?></textarea>
                                        <p class="description"><?php esc_html_e( 'Wording shown to the user immediately after a successful submission.', 'weardale-platform' ); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="weardale_mailchimp_url"><?php esc_html_e( 'Mailchimp Form Action URL', 'weardale-platform' ); ?></label></th>
                                    <td>
                                        <input type="url" id="weardale_mailchimp_url" name="weardale_mailchimp_url" class="large-text" placeholder="https://xxxx.usxx.list-manage.com/subscribe/post?u=xxxx&id=xxxx" value="<?php echo esc_url( $mailchimp_url ); ?>">
                                        <p class="description"><?php esc_html_e( 'Paste the naked HTML Form Action URL from your Mailchimp dashboard here. If left empty, a placeholder coming-soon block is displayed to guests.', 'weardale-platform' ); ?></p>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Box 2: Contact Hub details -->
                        <div class="card" style="margin-bottom: 20px; padding: 20px; background: #fff; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04); max-width: 100%;">
                            <h2>🏢 <?php esc_html_e( 'General Contact & Hub Details', 'weardale-platform' ); ?></h2>
                            <p class="description" style="margin-bottom: 20px;"><?php esc_html_e( 'Manage address and physical location parameters shown in sidebar directories and the primary Contact page layout.', 'weardale-platform' ); ?></p>
                            
                            <table class="form-table" role="presentation">
                                <tr>
                                    <th scope="row"><label for="weardale_organisation_name"><?php esc_html_e( 'Organisation Name (Override)', 'weardale-platform' ); ?></label></th>
                                    <td>
                                        <input type="text" id="weardale_organisation_name" name="weardale_organisation_name" class="regular-text" placeholder="<?php echo esc_attr( get_option( 'blogname' ) ); ?>" value="<?php echo esc_attr( $org_name ); ?>">
                                        <p class="description"><?php esc_html_e( 'If left blank, the site title defined in General Settings is used.', 'weardale-platform' ); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="weardale_organisation_short_name"><?php esc_html_e( 'Abbreviation / Short Name', 'weardale-platform' ); ?></label></th>
                                    <td>
                                        <input type="text" id="weardale_organisation_short_name" name="weardale_organisation_short_name" class="regular-text" value="<?php echo esc_attr( $org_short_name ); ?>">
                                        <p class="description"><?php esc_html_e( 'Short name (e.g. WT) used in compact layouts.', 'weardale-platform' ); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="weardale_organisation_company_number"><?php esc_html_e( 'CIC / Company Number', 'weardale-platform' ); ?></label></th>
                                    <td>
                                        <input type="text" id="weardale_organisation_company_number" name="weardale_organisation_company_number" class="regular-text" value="<?php echo esc_attr( $org_company ); ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="weardale_organisation_charity_number"><?php esc_html_e( 'Registered Charity Number', 'weardale-platform' ); ?></label></th>
                                    <td>
                                        <input type="text" id="weardale_organisation_charity_number" name="weardale_organisation_charity_number" class="regular-text" value="<?php echo esc_attr( $org_charity ); ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="weardale_contact_address"><?php esc_html_e( 'Hub Postal Address', 'weardale-platform' ); ?></label></th>
                                    <td>
                                        <textarea id="weardale_contact_address" name="weardale_contact_address" rows="3" class="large-text"><?php echo esc_textarea( $contact_address ); ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="weardale_contact_phone"><?php esc_html_e( 'Hub Telephone Number', 'weardale-platform' ); ?></label></th>
                                    <td>
                                        <input type="text" id="weardale_contact_phone" name="weardale_contact_phone" class="regular-text" value="<?php echo esc_attr( $contact_phone ); ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="weardale_contact_email"><?php esc_html_e( 'Hub Public Email Address', 'weardale-platform' ); ?></label></th>
                                    <td>
                                        <input type="email" id="weardale_contact_email" name="weardale_contact_email" class="regular-text" value="<?php echo esc_attr( $contact_email ); ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="weardale_contact_opening_hours"><?php esc_html_e( 'Public Opening Hours', 'weardale-platform' ); ?></label></th>
                                    <td>
                                        <textarea id="weardale_contact_opening_hours" name="weardale_contact_opening_hours" rows="3" class="large-text"><?php echo esc_textarea( $contact_opening_hours ); ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="weardale_contact_directions"><?php esc_html_e( 'Physical Landmark / Directions', 'weardale-platform' ); ?></label></th>
                                    <td>
                                        <textarea id="weardale_contact_directions" name="weardale_contact_directions" rows="3" class="large-text"><?php echo esc_textarea( $contact_directions ); ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="weardale_contact_social_facebook"><?php esc_html_e( 'Facebook Page Link', 'weardale-platform' ); ?></label></th>
                                    <td>
                                        <input type="url" id="weardale_contact_social_facebook" name="weardale_contact_social_facebook" class="large-text" value="<?php echo esc_url( $contact_social_fb ); ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="weardale_contact_social_instagram"><?php esc_html_e( 'Instagram Link', 'weardale-platform' ); ?></label></th>
                                    <td>
                                        <input type="url" id="weardale_contact_social_instagram" name="weardale_contact_social_instagram" class="large-text" value="<?php echo esc_url( $contact_social_ig ); ?>">
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Box 3: Legal Page Settings -->
                        <div class="card" style="margin-bottom: 20px; padding: 20px; background: #fff; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04); max-width: 100%;">
                            <h2>⚖️ <?php esc_html_e( 'Legal Page Settings', 'weardale-platform' ); ?></h2>
                            <p class="description" style="margin-bottom: 20px;"><?php esc_html_e( 'Select the published pages that correspond to your official privacy policy, cookie rules, and legal terms.', 'weardale-platform' ); ?></p>
                            
                            <table class="form-table" role="presentation">
                                <tr>
                                    <th scope="row"><label for="weardale_legal_privacy_page"><?php esc_html_e( 'Privacy Notice Page', 'weardale-platform' ); ?></label></th>
                                    <td>
                                        <?php
                                        wp_dropdown_pages( array(
                                            'name'             => 'weardale_legal_privacy_page',
                                            'selected'         => get_option( 'weardale_legal_privacy_page' ),
                                            'show_option_none' => __( '-- Select Privacy Page --', 'weardale-platform' ),
                                        ) );
                                        ?>
                                        <p class="description"><?php esc_html_e( 'Links to privacy consent and footer notices will target this page.', 'weardale-platform' ); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="weardale_legal_cookie_page"><?php esc_html_e( 'Cookie Policy Page', 'weardale-platform' ); ?></label></th>
                                    <td>
                                        <?php
                                        wp_dropdown_pages( array(
                                            'name'             => 'weardale_legal_cookie_page',
                                            'selected'         => get_option( 'weardale_legal_cookie_page' ),
                                            'show_option_none' => __( '-- Select Cookie Page --', 'weardale-platform' ),
                                        ) );
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="weardale_legal_terms_page"><?php esc_html_e( 'Terms & Conditions Page', 'weardale-platform' ); ?></label></th>
                                    <td>
                                        <?php
                                        wp_dropdown_pages( array(
                                            'name'             => 'weardale_legal_terms_page',
                                            'selected'         => get_option( 'weardale_legal_terms_page' ),
                                            'show_option_none' => __( '-- Select Terms Page --', 'weardale-platform' ),
                                        ) );
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <input type="submit" class="button button-primary button-large" value="<?php esc_attr_e( 'Save General & Participation Settings', 'weardale-platform' ); ?>">
                    </form>
                </div>

                <!-- RIGHT COLUMN: Tools and seeders -->
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    
                    <!-- Configuration Health Card -->
                    <div class="card" style="padding: 20px; background: #fff; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04); margin: 0;">
                        <h2>📋 <?php esc_html_e( 'Configuration Health', 'weardale-platform' ); ?></h2>
                        <p class="description"><?php esc_html_e( 'Concise diagnostics checking the completeness of your site setup.', 'weardale-platform' ); ?></p>
                        
                        <ul style="list-style-type: none; padding-left: 0; margin-top: 15px; display: flex; flex-direction: column; gap: 10px;">
                            <?php
                            // 1. Mailchimp configured check
                            if ( empty( $mailchimp_url ) ) {
                                echo '<li style="background: #fff8e1; border-left: 4px solid #ffb300; padding: 10px; font-size: 0.9rem; color: #5d4037; border-radius: 4px;">';
                                echo '<strong>⚠️ ' . esc_html__( 'Mailchimp Not Configured', 'weardale-platform' ) . ':</strong> ' . esc_html__( 'Mailchimp Form Action URL is missing. Newsletter forms are currently displaying a coming-soon placeholder block.', 'weardale-platform' );
                                echo '</li>';
                            } else {
                                echo '<li style="background: #e8f5e9; border-left: 4px solid #2e7d32; padding: 10px; font-size: 0.9rem; color: #1b5e20; border-radius: 4px;">';
                                echo '<strong>✅ ' . esc_html__( 'Mailchimp Configured', 'weardale-platform' ) . ':</strong> ' . esc_html__( 'Live Mailchimp sign-up form is active across newsletter components.', 'weardale-platform' );
                                echo '</li>';
                            }

                            // 2. Privacy page configured check
                            $privacy_page_id = get_option( 'weardale_legal_privacy_page' );
                            if ( empty( $privacy_page_id ) || ! get_post( $privacy_page_id ) ) {
                                echo '<li style="background: #fde8e8; border-left: 4px solid #f8b4b4; padding: 10px; font-size: 0.9rem; color: #9b1c1c; border-radius: 4px;">';
                                echo '<strong>⚠️ ' . esc_html__( 'Privacy Page Missing', 'weardale-platform' ) . ':</strong> ' . esc_html__( 'No published privacy policy page has been selected. Links currently fallback to a default /privacy-notice/ slug.', 'weardale-platform' );
                                echo '</li>';
                            } else {
                                echo '<li style="background: #e8f5e9; border-left: 4px solid #2e7d32; padding: 10px; font-size: 0.9rem; color: #1b5e20; border-radius: 4px;">';
                                echo '<strong>✅ ' . esc_html__( 'Privacy Page Configured', 'weardale-platform' ) . ':</strong> ' . esc_html__( 'Privacy Policy links successfully route to selected page.', 'weardale-platform' );
                                echo '</li>';
                            }

                            // 3. Facebook missing check
                            if ( empty( $contact_social_fb ) ) {
                                echo '<li style="background: #fff8e1; border-left: 4px solid #ffb300; padding: 10px; font-size: 0.9rem; color: #5d4037; border-radius: 4px;">';
                                echo '<strong>ℹ️ ' . esc_html__( 'Facebook Link Missing', 'weardale-platform' ) . ':</strong> ' . esc_html__( 'No Facebook page URL has been specified. Footers and contact forms will fallback to standard default placeholder coordinates.', 'weardale-platform' );
                                echo '</li>';
                            }

                            // 4. Logo missing check
                            if ( ! has_custom_logo() ) {
                                echo '<li style="background: #fff8e1; border-left: 4px solid #ffb300; padding: 10px; font-size: 0.9rem; color: #5d4037; border-radius: 4px;">';
                                echo '<strong>ℹ️ ' . esc_html__( 'Logo Missing', 'weardale-platform' ) . ':</strong> ' . esc_html__( 'No custom site logo has been uploaded. Renders are utilizing default visual monogram monograms.', 'weardale-platform' );
                                echo '</li>';
                            } else {
                                echo '<li style="background: #e8f5e9; border-left: 4px solid #2e7d32; padding: 10px; font-size: 0.9rem; color: #1b5e20; border-radius: 4px;">';
                                echo '<strong>✅ ' . esc_html__( 'Logo Loaded', 'weardale-platform' ) . ':</strong> ' . esc_html__( 'Your custom site logo is active.', 'weardale-platform' );
                                echo '</li>';
                            }

                            // 5. Contact email missing check
                            if ( empty( $contact_email ) ) {
                                echo '<li style="background: #fff8e1; border-left: 4px solid #ffb300; padding: 10px; font-size: 0.9rem; color: #5d4037; border-radius: 4px;">';
                                echo '<strong>⚠️ ' . esc_html__( 'Contact Email Missing', 'weardale-platform' ) . ':</strong> ' . esc_html__( 'Public contact email address has not been set. Direct email triggers will fallback to hello@weardaletogether.org.uk.', 'weardale-platform' );
                                echo '</li>';
                            }
                            ?>
                        </ul>
                    </div>

                    <!-- Bootstrapper Box -->
                    <div class="card" style="padding: 20px; background: #fff; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04); margin: 0;">
                        <h2>🗺️ <?php esc_html_e( 'Bootstrap Navigation', 'weardale-platform' ); ?></h2>
                        <p><?php esc_html_e( 'Automatically creates and assigns the Primary, Footer, and Legal menus. It is safe to run repeatedly: it will never overwrite custom menus or items.', 'weardale-platform' ); ?></p>
                        
                        <form method="post" style="margin-top: 15px;">
                            <?php wp_nonce_field( 'wt_bootstrap_action', 'wt_bootstrap_nonce' ); ?>
                            <input type="submit" class="button button-secondary button-large" style="width: 100%; text-align: center;" value="<?php esc_attr_e( 'Bootstrap Menus Now', 'weardale-platform' ); ?>">
                        </form>
                    </div>

                    <!-- Seeder Box -->
                    <div class="card" style="padding: 20px; background: #fff; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04); margin: 0;">
                        <h2>🙋‍♀️ <?php esc_html_e( 'Seed Development Data', 'weardale-platform' ); ?></h2>
                        <p><?php esc_html_e( 'Populates the site database with real-world sample pages, active volunteer opportunities, and test directories to experience active workflows immediately.', 'weardale-platform' ); ?></p>
                        
                        <form method="post" style="margin-top: 15px;">
                            <?php wp_nonce_field( 'wt_seed_action', 'wt_seed_nonce' ); ?>
                            <input type="submit" class="button button-secondary button-large" style="width: 100%; text-align: center;" value="<?php esc_attr_e( 'Seed Sample Data Now', 'weardale-platform' ); ?>">
                        </form>
                    </div>

                </div>

            </div>
        </div>
    </div>
    <?php
}

/**
 * Fallback width check helper
 */
function window_width_min_768() {
    return true;
}
