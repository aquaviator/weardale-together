<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */

// Retrieve dynamic organization options
$org_name_override = get_option( 'weardale_organisation_name' );
$org_display_name  = ! empty( $org_name_override ) ? $org_name_override : get_bloginfo( 'name' );
$org_company       = get_option( 'weardale_organisation_company_number' );
$org_charity       = get_option( 'weardale_organisation_charity_number' );

// Retrieve dynamic contact details
$contact_address   = get_option( 'weardale_contact_address' );
$contact_email     = get_option( 'weardale_contact_email' );
$contact_phone     = get_option( 'weardale_contact_phone' );

// Fallbacks if not set
if ( empty( $contact_address ) ) {
    $contact_address = 'Stanhope Hub, Front Street, Stanhope, DL13 2YR';
}
if ( empty( $contact_email ) ) {
    $contact_email = 'enquiries@weardaletogether.co.uk';
}
if ( empty( $contact_phone ) ) {
    $contact_phone = '01388 526200';
}

// Retrieve social media links
$social_fb = get_option( 'weardale_contact_social_facebook' );
$social_ig = get_option( 'weardale_contact_social_instagram' );

// Resolve dynamic legal privacy policy page
$privacy_url = function_exists( 'weardale_platform_get_legal_page_url' )
    ? weardale_platform_get_legal_page_url( 'weardale_legal_privacy_page' )
    : home_url( '/privacy-notice/' );
?>

<footer id="colophon" class="site-footer" role="contentinfo">
    <div class="container">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 3rem;">
            
            <!-- Column 1: Organization Details -->
            <div>
                <div class="footer-logo-container">
                    <?php
                    if ( has_custom_logo() ) {
                        the_custom_logo();
                    } else {
                        ?>
                        <h3 class="font-display" style="font-size: 1.65rem; margin: 0 0 0.5rem 0; color: var(--color-cream);"><?php echo esc_html( $org_display_name ); ?></h3>
                        <?php
                    }
                    ?>
                </div>
                <p style="font-size: 1rem; line-height: 1.5; color: var(--color-cream); opacity: 0.9; margin-top: 0.5rem;">
                    <?php esc_html_e( 'A grassroots Community Interest Company serving rural communities in the North Pennines.', 'weardale-together' ); ?>
                </p>
                <?php if ( ! empty( $org_company ) || ! empty( $org_charity ) ) : ?>
                    <p style="font-size: 0.9rem; color: var(--color-tan); margin-top: 1rem; line-height: 1.4;">
                        <?php
                        if ( ! empty( $org_company ) ) {
                            /* translators: %s: Company registered number */
                            printf( esc_html__( 'CIC Number: %s', 'weardale-together' ), esc_html( $org_company ) );
                        }
                        if ( ! empty( $org_company ) && ! empty( $org_charity ) ) {
                            echo '<br>';
                        }
                        if ( ! empty( $org_charity ) ) {
                            /* translators: %s: Charity registered number */
                            printf( esc_html__( 'Charity Number: %s', 'weardale-together' ), esc_html( $org_charity ) );
                        }
                        ?>
                    </p>
                <?php else : ?>
                    <p style="font-size: 0.9rem; color: var(--color-tan); margin-top: 1rem;">
                        <?php esc_html_e( 'CIC Number: 13483954 (Registered in 2021)', 'weardale-together' ); ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Column 2: Direct Contacts -->
            <div>
                <h4 style="font-family: var(--font-body); font-weight: 700; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.05em; font-size: 0.95rem; color: var(--color-cream);"><?php esc_html_e( 'Contact Us', 'weardale-together' ); ?></h4>
                <p style="font-size: 1rem; margin-bottom: 0.5rem; color: var(--color-cream); line-height: 1.4;">
                    <strong><?php esc_html_e( 'Address:', 'weardale-together' ); ?></strong> <?php echo esc_html( $contact_address ); ?>
                </p>
                <p style="font-size: 1rem; margin-bottom: 0.5rem; color: var(--color-cream);">
                    <strong><?php esc_html_e( 'Email:', 'weardale-together' ); ?></strong> <a href="mailto:<?php echo esc_attr( $contact_email ); ?>" style="text-decoration: underline; color: var(--color-tan);"><?php echo esc_html( $contact_email ); ?></a>
                </p>
                <p style="font-size: 1rem; color: var(--color-cream);">
                    <strong><?php esc_html_e( 'Phone:', 'weardale-together' ); ?></strong> <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $contact_phone ) ); ?>" style="text-decoration: none; color: var(--color-tan);"><?php echo esc_html( $contact_phone ); ?></a>
                </p>
            </div>

            <!-- Column 3: Navigation Menu -->
            <div>
                <h4 style="font-family: var(--font-body); font-weight: 700; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.05em; font-size: 0.95rem; color: var(--color-cream);"><?php esc_html_e( 'Quick Links', 'weardale-together' ); ?></h4>
                <?php
                if ( has_nav_menu( 'footer-menu' ) ) {
                    wp_nav_menu( array(
                        'theme_location' => 'footer-menu',
                        'container'      => false,
                        'fallback_cb'    => false,
                    ) );
                } else {
                    ?>
                    <ul style="list-style: none; padding: 0; margin: 0; line-height: 2;">
                        <li><a href="<?php echo esc_url( home_url( '/news-blog/' ) ); ?>"><?php esc_html_e( 'News & Blog', 'weardale-together' ); ?></a></li>
                        <li><a href="<?php echo esc_url( home_url( '/volunteer/' ) ); ?>"><?php esc_html_e( 'Volunteer With Us', 'weardale-together' ); ?></a></li>
                        <li><a href="<?php echo esc_url( home_url( '/newsletter/' ) ); ?>"><?php esc_html_e( 'Newsletter Sign-up', 'weardale-together' ); ?></a></li>
                        <li><a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>"><?php esc_html_e( 'Get In Touch', 'weardale-together' ); ?></a></li>
                    </ul>
                    <?php
                }
                ?>
            </div>

            <!-- Column 4: Newsletter Signpost -->
            <div>
                <h4 style="font-family: var(--font-body); font-weight: 700; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.05em; font-size: 0.95rem; color: var(--color-cream);"><?php esc_html_e( 'Community Newsletter', 'weardale-together' ); ?></h4>
                <p style="font-size: 0.95rem; line-height: 1.4; color: var(--color-cream); margin-bottom: 1rem;">
                    <?php esc_html_e( 'Stay up to date with events, café menus, and craft workshops across the Pennines.', 'weardale-together' ); ?>
                </p>
                <div class="footer-newsletter-container">
                    <?php
                    if ( function_exists( 'weardale_platform_get_newsletter_form' ) ) {
                        echo weardale_platform_get_newsletter_form( 'footer' );
                    }
                    ?>
                </div>
            </div>

        </div>

        <!-- Copyright and Credits -->
        <div class="footer-bottom">
            <div>
                &copy; <?php echo date( 'Y' ); ?> <?php echo esc_html( $org_display_name ); ?>. <?php esc_html_e( 'All rights reserved.', 'weardale-together' ); ?>
            </div>
            <div style="display: flex; gap: 1.5rem; align-items: center;">
                <a href="<?php echo esc_url( $privacy_url ); ?>" style="text-decoration: none; font-size: 0.85rem;"><?php esc_html_e( 'Privacy Notice', 'weardale-together' ); ?></a>
                
                <!-- Social media icons -->
                <div style="display: flex; gap: 0.75rem;">
                    <?php if ( ! empty( $social_fb ) ) : ?>
                        <a href="<?php echo esc_url( $social_fb ); ?>" target="_blank" aria-label="Facebook (Opens in new tab)" style="text-decoration: none;">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8H7v3h2v9h4v-9h3.6l.4-3H13V6c0-.5.5-1 1-1h3V1H13c-3 0-4 2-4 4v3z"/></svg>
                        </a>
                    <?php else : ?>
                        <a href="https://facebook.com/weardaletogether" target="_blank" aria-label="Facebook (Opens in new tab)" style="text-decoration: none;">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8H7v3h2v9h4v-9h3.6l.4-3H13V6c0-.5.5-1 1-1h3V1H13c-3 0-4 2-4 4v3z"/></svg>
                        </a>
                    <?php endif; ?>

                    <?php if ( ! empty( $social_ig ) ) : ?>
                        <a href="<?php echo esc_url( $social_ig ); ?>" target="_blank" aria-label="Instagram (Opens in new tab)" style="text-decoration: none;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zM17.5 6.5h.01"/></svg>
                        </a>
                    <?php else : ?>
                        <a href="https://instagram.com/weardaletogether" target="_blank" aria-label="Instagram (Opens in new tab)" style="text-decoration: none;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zM17.5 6.5h.01"/></svg>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
