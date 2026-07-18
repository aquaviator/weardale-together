<?php
/**
 * Template part for the Community Directory Promo banner on the homepage.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.2.0
 */

$directory_url = get_post_type_archive_link( 'weardale_directory' ) ?: home_url( '/directory/' );
?>

<section id="homepage-directory-promo" class="section-padding directory-promo-section">
    <div class="container">
        <div class="directory-promo-container">
            
            <!-- Left block: Content & CTA -->
            <div>
                <span class="directory-promo-badge">
                    <?php esc_html_e( 'Local Connections', 'weardale-together' ); ?>
                </span>
                <h2 class="font-display directory-promo-title">
                    <?php esc_html_e( 'Explore the Community Directory', 'weardale-together' ); ?>
                </h2>
                <p class="directory-promo-lead">
                    <?php esc_html_e( 'Find local groups, support services, community transport routes, village halls, and local independent businesses across Weardale and the North Pennines. Connect with volunteers and activities near you.', 'weardale-together' ); ?>
                </p>
                <a href="<?php echo esc_url( $directory_url ); ?>" class="btn btn-primary directory-promo-cta">
                    <span>🔎</span> <?php esc_html_e( 'Search the Directory', 'weardale-together' ); ?>
                </a>
            </div>

            <!-- Right block: Aesthetic illustrative list of categories -->
            <div class="directory-promo-list-box">
                <h3 class="directory-promo-list-title">
                    <?php esc_html_e( 'What you can find:', 'weardale-together' ); ?>
                </h3>
                
                <ul class="directory-promo-list">
                    <li class="directory-promo-item">
                        <span>🏛️</span>
                        <span><strong><?php esc_html_e( 'Community Facilities & Halls', 'weardale-together' ); ?></strong></span>
                    </li>
                    <li class="directory-promo-item">
                        <span>🚌</span>
                        <span><strong><?php esc_html_e( 'Transport & Essential Connections', 'weardale-together' ); ?></strong></span>
                    </li>
                    <li class="directory-promo-item">
                        <span>🎨</span>
                        <span><strong><?php esc_html_e( 'Creative Arts, Food & Social Groups', 'weardale-together' ); ?></strong></span>
                    </li>
                    <li class="directory-promo-item">
                        <span>🤝</span>
                        <span><strong><?php esc_html_e( 'Support Services & Volunteering', 'weardale-together' ); ?></strong></span>
                    </li>
                </ul>

                <div class="directory-promo-footer">
                    <span>✓</span>
                    <span><em><?php esc_html_e( 'All listings are community-reviewed and kept up to date.', 'weardale-together' ); ?></em></span>
                </div>
            </div>

        </div>
    </div>
</section>
