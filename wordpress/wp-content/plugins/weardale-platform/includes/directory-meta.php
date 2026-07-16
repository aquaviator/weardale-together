<?php
/**
 * Directory Metadata Management & Editor Interface
 *
 * Handles the rich meta box editor sections and saving for Directory.
 *
 * @package Weardale_Platform
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add meta box to weardale_directory post type
 */
function weardale_platform_add_directory_meta_boxes() {
    add_meta_box(
        'weardale_directory_details',
        __( 'Directory Entry Details', 'weardale-platform' ),
        'weardale_platform_render_directory_meta_box',
        'weardale_directory',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'weardale_platform_add_directory_meta_boxes' );

/**
 * Render the Directory Metadata Editor
 */
function weardale_platform_render_directory_meta_box( $post ) {
    wp_nonce_field( 'weardale_directory_meta_nonce_action', 'weardale_directory_meta_nonce_field' );

    // Retrieve existing metadata
    $address            = get_post_meta( $post->ID, '_directory_address', true );
    $phone              = get_post_meta( $post->ID, '_directory_phone', true );
    $email              = get_post_meta( $post->ID, '_directory_email', true );
    $allow_enquiry      = get_post_meta( $post->ID, '_directory_allow_enquiry', true ) === '1';
    $website            = get_post_meta( $post->ID, '_directory_website', true );
    $opening_hours      = get_post_meta( $post->ID, '_directory_opening_hours', true );
    $accessibility      = get_post_meta( $post->ID, '_directory_accessibility', true );
    $who_it_helps       = get_post_meta( $post->ID, '_directory_who_it_helps', true );
    $pricing            = get_post_meta( $post->ID, '_directory_pricing', true );
    $booking_required   = get_post_meta( $post->ID, '_directory_booking_required', true );
    $latitude           = get_post_meta( $post->ID, '_directory_latitude', true );
    $longitude          = get_post_meta( $post->ID, '_directory_longitude', true );
    $facebook           = get_post_meta( $post->ID, '_directory_facebook', true );
    $instagram          = get_post_meta( $post->ID, '_directory_instagram', true );
    $linkedin           = get_post_meta( $post->ID, '_directory_linkedin', true );
    $verified           = get_post_meta( $post->ID, '_directory_verified', true ) === '1';
    $last_reviewed      = get_post_meta( $post->ID, '_directory_last_reviewed', true );
    $related_programme  = get_post_meta( $post->ID, '_directory_related_programme', true );
    $related_events     = get_post_meta( $post->ID, '_directory_related_events', true );
    ?>
    <style>
        .wd-meta-wrapper {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif;
            background: #fcfcfc;
            border-radius: 4px;
        }
        .wd-tabs-header {
            display: flex;
            border-bottom: 2px solid #e2e8f0;
            background: #f1f5f9;
            margin: -12px -12px 1.5rem -12px;
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
        }
        .wd-tab-btn {
            padding: 1rem 1.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            color: #475569;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .wd-tab-btn:hover {
            color: #1e293b;
            background: rgba(0,0,0,0.02);
        }
        .wd-tab-btn.active {
            color: #2b6cb0;
            border-bottom-color: #2b6cb0;
            background: #fff;
        }
        .wd-tab-content {
            display: none;
            padding: 0.5rem 0;
        }
        .wd-tab-content.active {
            display: block;
        }
        .wd-form-grid {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 1.5rem;
            align-items: baseline;
            margin-bottom: 1.25rem;
        }
        .wd-form-grid label {
            font-weight: 600;
            color: #1e293b;
        }
        .wd-help-desc {
            font-size: 0.85rem;
            color: #64748b;
            margin: 4px 0 0 0;
            line-height: 1.4;
        }
        .wd-checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500 !important;
            cursor: pointer;
        }
    </style>

    <div class="wd-meta-wrapper">
        <!-- Tabs Header -->
        <div class="wd-tabs-header">
            <button type="button" class="wd-tab-btn active" data-tab="tab-contact">📞 <?php esc_html_e( 'Contact Info', 'weardale-platform' ); ?></button>
            <button type="button" class="wd-tab-btn" data-tab="tab-details">ℹ️ <?php esc_html_e( 'Details & Access', 'weardale-platform' ); ?></button>
            <button type="button" class="wd-tab-btn" data-tab="tab-social">🌐 <?php esc_html_e( 'Social & Relations', 'weardale-platform' ); ?></button>
        </div>

        <!-- TAB 1: CONTACT -->
        <div id="tab-contact" class="wd-tab-content active">
            <div class="wd-form-grid">
                <label for="directory_address"><?php esc_html_e( 'Address', 'weardale-platform' ); ?></label>
                <div>
                    <textarea id="directory_address" name="directory_address" class="large-text" rows="3"><?php echo esc_textarea( $address ); ?></textarea>
                    <p class="wd-help-desc"><?php esc_html_e( 'Physical location of this business, facility or community group.', 'weardale-platform' ); ?></p>
                </div>
            </div>

            <div class="wd-form-grid">
                <label for="directory_phone"><?php esc_html_e( 'Phone Number', 'weardale-platform' ); ?></label>
                <div>
                    <input type="text" id="directory_phone" name="directory_phone" class="regular-text" value="<?php echo esc_attr( $phone ); ?>">
                    <p class="wd-help-desc"><?php esc_html_e( 'Primary contact telephone number.', 'weardale-platform' ); ?></p>
                </div>
            </div>

            <div class="wd-form-grid">
                <label for="directory_email"><?php esc_html_e( 'Email Address', 'weardale-platform' ); ?></label>
                <div>
                    <input type="email" id="directory_email" name="directory_email" class="regular-text" value="<?php echo esc_attr( $email ); ?>">
                </div>
            </div>

            <div class="wd-form-grid">
                <label><?php esc_html_e( 'On-Site Enquiries', 'weardale-platform' ); ?></label>
                <div>
                    <label class="wd-checkbox-label">
                        <input type="checkbox" name="directory_allow_enquiry" value="1" <?php checked( $allow_enquiry, true ); ?>>
                        <strong><?php esc_html_e( 'Allow Online Enquiries', 'weardale-platform' ); ?></strong>
                    </label>
                    <p class="wd-help-desc"><?php esc_html_e( 'Enable a prominent on-site "Enquire Online" contact action button for this directory listing.', 'weardale-platform' ); ?></p>
                </div>
            </div>

            <div class="wd-form-grid">
                <label for="directory_website"><?php esc_html_e( 'Website URL', 'weardale-platform' ); ?></label>
                <div>
                    <input type="url" id="directory_website" name="directory_website" class="large-text" value="<?php echo esc_url( $website ); ?>">
                    <p class="wd-help-desc"><?php esc_html_e( 'Full website address (e.g. https://example.com).', 'weardale-platform' ); ?></p>
                </div>
            </div>

            <div class="wd-form-grid">
                <label><?php esc_html_e( 'Coordinates (Geotagging)', 'weardale-platform' ); ?></label>
                <div style="display: flex; gap: 1rem;">
                    <div>
                        <input type="text" id="directory_latitude" name="directory_latitude" placeholder="Latitude" value="<?php echo esc_attr( $latitude ); ?>">
                        <span class="wd-help-desc" style="display:block;"><?php esc_html_e( 'e.g. 54.7432', 'weardale-platform' ); ?></span>
                    </div>
                    <div>
                        <input type="text" id="directory_longitude" name="directory_longitude" placeholder="Longitude" value="<?php echo esc_attr( $longitude ); ?>">
                        <span class="wd-help-desc" style="display:block;"><?php esc_html_e( 'e.g. -2.0125', 'weardale-platform' ); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: DETAILS & ACCESS -->
        <div id="tab-details" class="wd-tab-content">
            <div class="wd-form-grid">
                <label for="directory_opening_hours"><?php esc_html_e( 'Opening Hours', 'weardale-platform' ); ?></label>
                <div>
                    <textarea id="directory_opening_hours" name="directory_opening_hours" class="large-text" rows="3"><?php echo esc_textarea( $opening_hours ); ?></textarea>
                    <p class="wd-help-desc"><?php esc_html_e( 'Describe when this place, service or opportunity is open/available.', 'weardale-platform' ); ?></p>
                </div>
            </div>

            <div class="wd-form-grid">
                <label for="directory_accessibility"><?php esc_html_e( 'Accessibility Features', 'weardale-platform' ); ?></label>
                <div>
                    <input type="text" id="directory_accessibility" name="directory_accessibility" class="large-text" value="<?php echo esc_attr( $accessibility ); ?>">
                    <p class="wd-help-desc"><?php esc_html_e( 'e.g. Wheelchair access, Hearing loops, Quiet sessions.', 'weardale-platform' ); ?></p>
                </div>
            </div>

            <div class="wd-form-grid">
                <label for="directory_who_it_helps"><?php esc_html_e( 'Who It Helps', 'weardale-platform' ); ?></label>
                <div>
                    <input type="text" id="directory_who_it_helps" name="directory_who_it_helps" class="large-text" value="<?php echo esc_attr( $who_it_helps ); ?>">
                    <p class="wd-help-desc"><?php esc_html_e( 'e.g. Older adults, Families, Anyone, Toddlers.', 'weardale-platform' ); ?></p>
                </div>
            </div>

            <div class="wd-form-grid">
                <label for="directory_pricing"><?php esc_html_e( 'Pricing / Fees', 'weardale-platform' ); ?></label>
                <div>
                    <input type="text" id="directory_pricing" name="directory_pricing" class="regular-text" value="<?php echo esc_attr( $pricing ); ?>">
                    <p class="wd-help-desc"><?php esc_html_e( 'e.g. Free, £5 per session, Membership required.', 'weardale-platform' ); ?></p>
                </div>
            </div>

            <div class="wd-form-grid">
                <label><?php esc_html_e( 'Booking Required', 'weardale-platform' ); ?></label>
                <div>
                    <label class="wd-checkbox-label" style="display:inline-flex; margin-right: 1.5rem;">
                        <input type="radio" name="directory_booking_required" value="no" <?php checked( $booking_required, 'no' ); ?> <?php checked( empty( $booking_required ) ); ?>>
                        <?php esc_html_e( 'No booking required', 'weardale-platform' ); ?>
                    </label>
                    <label class="wd-checkbox-label" style="display:inline-flex;">
                        <input type="radio" name="directory_booking_required" value="yes" <?php checked( $booking_required, 'yes' ); ?>>
                        <?php esc_html_e( 'Booking is required', 'weardale-platform' ); ?>
                    </label>
                </div>
            </div>
        </div>

        <!-- TAB 3: SOCIAL, RELATIONS & STATUS -->
        <div id="tab-social" class="wd-tab-content">
            <div class="wd-form-grid">
                <label for="directory_facebook"><?php esc_html_e( 'Facebook Link', 'weardale-platform' ); ?></label>
                <div>
                    <input type="url" id="directory_facebook" name="directory_facebook" class="large-text" value="<?php echo esc_url( $facebook ); ?>">
                </div>
            </div>

            <div class="wd-form-grid">
                <label for="directory_instagram"><?php esc_html_e( 'Instagram Link', 'weardale-platform' ); ?></label>
                <div>
                    <input type="url" id="directory_instagram" name="directory_instagram" class="large-text" value="<?php echo esc_url( $instagram ); ?>">
                </div>
            </div>

            <div class="wd-form-grid">
                <label for="directory_linkedin"><?php esc_html_e( 'LinkedIn Link', 'weardale-platform' ); ?></label>
                <div>
                    <input type="url" id="directory_linkedin" name="directory_linkedin" class="large-text" value="<?php echo esc_url( $linkedin ); ?>">
                </div>
            </div>

            <div class="wd-form-grid">
                <label for="directory_related_programme"><?php esc_html_e( 'Related Programme / Strand', 'weardale-platform' ); ?></label>
                <div>
                    <input type="text" id="directory_related_programme" name="directory_related_programme" class="regular-text" value="<?php echo esc_attr( $related_programme ); ?>">
                    <p class="wd-help-desc"><?php esc_html_e( 'Associated thematic program (e.g. Young People, Creative Arts).', 'weardale-platform' ); ?></p>
                </div>
            </div>

            <div class="wd-form-grid">
                <label for="directory_related_events"><?php esc_html_e( 'Related Events (Desc)', 'weardale-platform' ); ?></label>
                <div>
                    <input type="text" id="directory_related_events" name="directory_related_events" class="large-text" value="<?php echo esc_attr( $related_events ); ?>">
                    <p class="wd-help-desc"><?php esc_html_e( 'List of related events or recurring workshops at this location.', 'weardale-platform' ); ?></p>
                </div>
            </div>

            <div class="wd-form-grid">
                <label><?php esc_html_e( 'Status & Review', 'weardale-platform' ); ?></label>
                <div>
                    <label class="wd-checkbox-label" style="margin-bottom: 0.5rem;">
                        <input type="checkbox" name="directory_verified" value="1" <?php checked( $verified, true ); ?>>
                        <strong><?php esc_html_e( 'Verified Listing', 'weardale-platform' ); ?></strong>
                    </label>
                    <p class="wd-help-desc"><?php esc_html_e( 'Has this listing been reviewed and verified by a moderator?', 'weardale-platform' ); ?></p>
                    
                    <div style="margin-top: 1rem;">
                        <label for="directory_last_reviewed" style="display:block; margin-bottom: 4px; font-weight:600;"><?php esc_html_e( 'Last Reviewed Date', 'weardale-platform' ); ?></label>
                        <input type="date" id="directory_last_reviewed" name="directory_last_reviewed" value="<?php echo esc_attr( $last_reviewed ); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple JavaScript for Tab Switching in Admin -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tabs = document.querySelectorAll('.wd-tab-btn');
            var contents = document.querySelectorAll('.wd-tab-content');

            tabs.forEach(function(tab) {
                tab.addEventListener('click', function() {
                    tabs.forEach(t => t.classList.remove('active'));
                    contents.forEach(c => c.classList.remove('active'));

                    this.classList.add('active');
                    var targetId = this.getAttribute('data-tab');
                    var targetContent = document.getElementById(targetId);
                    if (targetContent) {
                        targetContent.classList.add('active');
                    }
                });
            });
        });
    </script>
    <?php
}

/**
 * Save directory metadata when editing a directory post
 */
function weardale_platform_save_directory_metadata( $post_id ) {
    // 1. Verify nonce
    if ( ! isset( $_POST['directory_meta_nonce_field'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['directory_meta_nonce_field'], 'weardale_directory_meta_nonce_action' ) ) {
        return;
    }

    // 2. Prevent autosave updates
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // 3. Authorize the user
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // 4. Save fields
    $fields = array(
        'directory_address'         => '_directory_address',
        'directory_phone'           => '_directory_phone',
        'directory_email'           => '_directory_email',
        'directory_website'         => '_directory_website',
        'directory_opening_hours'   => '_directory_opening_hours',
        'directory_accessibility'   => '_directory_accessibility',
        'directory_who_it_helps'    => '_directory_who_it_helps',
        'directory_pricing'         => '_directory_pricing',
        'directory_booking_required'=> '_directory_booking_required',
        'directory_latitude'        => '_directory_latitude',
        'directory_longitude'       => '_directory_longitude',
        'directory_facebook'        => '_directory_facebook',
        'directory_instagram'       => '_directory_instagram',
        'directory_linkedin'        => '_directory_linkedin',
        'directory_last_reviewed'   => '_directory_last_reviewed',
        'directory_related_programme'=> '_directory_related_programme',
        'directory_related_events'  => '_directory_related_events',
    );

    foreach ( $fields as $post_key => $meta_key ) {
        if ( isset( $_POST[ $post_key ] ) ) {
            $value = sanitize_text_field( $_POST[ $post_key ] );
            if ( $post_key === 'directory_address' || $post_key === 'directory_opening_hours' ) {
                $value = sanitize_textarea_field( $_POST[ $post_key ] );
            } elseif ( $post_key === 'directory_website' || $post_key === 'directory_facebook' || $post_key === 'directory_instagram' || $post_key === 'directory_linkedin' ) {
                $value = esc_url_raw( $_POST[ $post_key ] );
            }
            update_post_meta( $post_id, $meta_key, $value );
        }
    }

    // Handle checkboxes
    $verified = isset( $_POST['directory_verified'] ) ? '1' : '0';
    update_post_meta( $post_id, '_directory_verified', $verified );

    $allow_enquiry = isset( $_POST['directory_allow_enquiry'] ) ? '1' : '0';
    update_post_meta( $post_id, '_directory_allow_enquiry', $allow_enquiry );
}
add_action( 'save_post_weardale_directory', 'weardale_platform_save_directory_metadata' );
