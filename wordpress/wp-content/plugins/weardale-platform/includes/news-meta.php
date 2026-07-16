<?php
/**
 * News Metadata Management & Editor Interface
 *
 * Handles rich meta boxes for standard posts to extend them into a community news & stories system.
 * Contains programme relationships, related event links, directory associations, and featured story flags.
 *
 * @package Weardale_Platform
 * @since 1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add meta box to standard post type
 */
function weardale_platform_add_news_meta_boxes() {
    add_meta_box(
        'weardale_news_details',
        __( 'Story Editorial Metadata', 'weardale-platform' ),
        'weardale_platform_render_news_meta_box',
        'post',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'weardale_platform_add_news_meta_boxes' );

/**
 * Render the News & Stories Metadata Editor
 */
function weardale_platform_render_news_meta_box( $post ) {
    wp_nonce_field( 'weardale_news_meta_nonce_action', 'weardale_news_meta_nonce_field' );

    // Retrieve existing metadata
    $is_featured        = get_post_meta( $post->ID, '_weardale_featured_post', true ) === '1';
    $programme          = get_post_meta( $post->ID, '_weardale_post_programme', true );
    if ( function_exists( 'weardale_platform_normalize_programme_key' ) ) {
        $programme = weardale_platform_normalize_programme_key( $programme );
    }
    $related_event      = get_post_meta( $post->ID, '_weardale_related_event_id', true );
    $related_directory  = get_post_meta( $post->ID, '_weardale_related_directory_id', true );

    // Fetch Events for selection
    $events = get_posts( array(
        'post_type'      => 'weardale_event',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
    ) );

    // Fetch Directory Listings for selection
    $directory_listings = get_posts( array(
        'post_type'      => 'weardale_directory',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
    ) );
    ?>
    <style>
        .wd-news-meta-wrapper {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif;
            padding: 0.5rem 0;
        }
        .wd-news-form-grid {
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 1.5rem;
            align-items: baseline;
            margin-bottom: 1.25rem;
        }
        .wd-news-form-grid label {
            font-weight: 600;
            color: #1e293b;
        }
        .wd-news-help-desc {
            font-size: 0.85rem;
            color: #64748b;
            margin: 4px 0 0 0;
            line-height: 1.4;
        }
        .wd-news-checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500 !important;
            cursor: pointer;
        }
    </style>

    <div class="wd-news-meta-wrapper">
        <!-- 1. Featured Story Toggle -->
        <div class="wd-news-form-grid">
            <label><?php esc_html_e( 'Featured Story', 'weardale-platform' ); ?></label>
            <div>
                <label class="wd-news-checkbox-label">
                    <input type="checkbox" name="weardale_featured_post" value="1" <?php checked( $is_featured, true ); ?>>
                    <strong><?php esc_html_e( 'Promote to Featured Story', 'weardale-platform' ); ?></strong>
                </label>
                <p class="wd-news-help-desc"><?php esc_html_e( 'Featured stories are highlighted prominently at the top of the homepage and journal archive.', 'weardale-platform' ); ?></p>
            </div>
        </div>

        <!-- 2. Related Programme / Strand -->
        <div class="wd-news-form-grid">
            <label for="weardale_post_programme"><?php esc_html_e( 'Associated Programme Strand', 'weardale-platform' ); ?></label>
            <div>
                <select id="weardale_post_programme" name="weardale_post_programme" class="postform">
                    <option value="" <?php selected( $programme, '' ); ?>><?php esc_html_e( '(No associated programme)', 'weardale-platform' ); ?></option>
                    <option value="root-branch-cafe" <?php selected( $programme, 'root-branch-cafe' ); ?>><?php esc_html_e( 'Root & Branch Café', 'weardale-platform' ); ?></option>
                    <option value="young-people" <?php selected( $programme, 'young-people' ); ?>><?php esc_html_e( 'Young People & Forest School', 'weardale-platform' ); ?></option>
                    <option value="creative-arts" <?php selected( $programme, 'creative-arts' ); ?>><?php esc_html_e( 'Creative Arts', 'weardale-platform' ); ?></option>
                    <option value="roots-shoots" <?php selected( $programme, 'roots-shoots' ); ?>><?php esc_html_e( 'Roots & Shoots Early Years', 'weardale-platform' ); ?></option>
                </select>
                <p class="wd-news-help-desc"><?php esc_html_e( 'Connect this article to a main community strand. This will help group content visually.', 'weardale-platform' ); ?></p>
            </div>
        </div>

        <!-- 3. Related Event -->
        <div class="wd-news-form-grid">
            <label for="weardale_related_event_id"><?php esc_html_e( 'Related Community Event', 'weardale-platform' ); ?></label>
            <div>
                <select id="weardale_related_event_id" name="weardale_related_event_id" class="postform">
                    <option value="" <?php selected( $related_event, '' ); ?>><?php esc_html_e( '(None - No related event)', 'weardale-platform' ); ?></option>
                    <?php if ( ! empty( $events ) ) : ?>
                        <?php foreach ( $events as $ev ) : ?>
                            <option value="<?php echo esc_attr( $ev->ID ); ?>" <?php selected( $related_event, $ev->ID ); ?>>
                                <?php echo esc_html( $ev->post_title ); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <p class="wd-news-help-desc"><?php esc_html_e( 'Link this story directly to an upcoming community event to cross-promote activities.', 'weardale-platform' ); ?></p>
            </div>
        </div>

        <!-- 4. Related Directory Listing -->
        <div class="wd-news-form-grid">
            <label for="weardale_related_directory_id"><?php esc_html_e( 'Related Directory Listing', 'weardale-platform' ); ?></label>
            <div>
                <select id="weardale_related_directory_id" name="weardale_related_directory_id" class="postform">
                    <option value="" <?php selected( $related_directory, '' ); ?>><?php esc_html_e( '(None - No related directory listing)', 'weardale-platform' ); ?></option>
                    <?php if ( ! empty( $directory_listings ) ) : ?>
                        <?php foreach ( $directory_listings as $listing ) : ?>
                            <option value="<?php echo esc_attr( $listing->ID ); ?>" <?php selected( $related_directory, $listing->ID ); ?>>
                                <?php echo esc_html( $listing->post_title ); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <p class="wd-news-help-desc"><?php esc_html_e( 'Associate this story with a local facility, community group or business listed in the directory.', 'weardale-platform' ); ?></p>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Save post metadata when editing a post
 */
function weardale_platform_save_news_metadata( $post_id ) {
    // 1. Verify nonce
    if ( ! isset( $_POST['weardale_news_meta_nonce_field'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['weardale_news_meta_nonce_field'], 'weardale_news_meta_nonce_action' ) ) {
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
    if ( isset( $_POST['weardale_post_programme'] ) ) {
        $programme_value = sanitize_text_field( $_POST['weardale_post_programme'] );
        if ( function_exists( 'weardale_platform_normalize_programme_key' ) ) {
            $programme_value = weardale_platform_normalize_programme_key( $programme_value );
        }
        update_post_meta( $post_id, '_weardale_post_programme', $programme_value );
    }

    if ( isset( $_POST['weardale_related_event_id'] ) ) {
        $event_id = sanitize_text_field( $_POST['weardale_related_event_id'] );
        update_post_meta( $post_id, '_weardale_related_event_id', $event_id );
    }

    if ( isset( $_POST['weardale_related_directory_id'] ) ) {
        $dir_id = sanitize_text_field( $_POST['weardale_related_directory_id'] );
        update_post_meta( $post_id, '_weardale_related_directory_id', $dir_id );
    }

    // Handle checkboxes
    $featured = isset( $_POST['weardale_featured_post'] ) ? '1' : '0';
    if ( '1' === $featured ) {
        // Enforce single active featured story by un-featuring all other posts
        global $wpdb;
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$wpdb->postmeta} SET meta_value = '0' WHERE meta_key = '_weardale_featured_post' AND post_id != %d",
                $post_id
            )
        );
    }
    update_post_meta( $post_id, '_weardale_featured_post', $featured );
}
add_action( 'save_post', 'weardale_platform_save_news_metadata' );
