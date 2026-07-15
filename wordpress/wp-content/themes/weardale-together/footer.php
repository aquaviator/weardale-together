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
?>

<footer id="colophon" class="site-footer" role="contentinfo">
    <div class="container">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 3rem;">
            
            <!-- Column 1: Organization Details -->
            <div>
                <h3 class="font-display" style="font-size: 1.6rem; margin-bottom: 1rem; color: var(--color-cream);">Weardale Together</h3>
                <p style="font-size: 1rem; line-height: 1.5; color: var(--color-cream); opacity: 0.9;">
                    <?php esc_html_e( 'A grassroots Community Interest Company serving rural communities in the North Pennines.', 'weardale-together' ); ?>
                </p>
                <p style="font-size: 0.9rem; color: var(--color-tan); margin-top: 1rem;">
                    <?php esc_html_e( 'CIC Number: 13483954 (Registered in 2021)', 'weardale-together' ); ?>
                </p>
            </div>

            <!-- Column 2: Direct Contacts -->
            <div>
                <h4 style="font-family: var(--font-body); font-weight: 700; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.05em; font-size: 0.95rem;">Contact Us</h4>
                <p style="font-size: 1rem; margin-bottom: 0.5rem; color: var(--color-cream);">
                    <strong>Address:</strong> Stanhope, County Durham, North Pennines
                </p>
                <p style="font-size: 1rem; margin-bottom: 0.5rem; color: var(--color-cream);">
                    <strong>Email:</strong> <a href="mailto:hello@weardaletogether.org.uk" style="text-decoration: underline; color: var(--color-tan);">hello@weardaletogether.org.uk</a>
                </p>
                <p style="font-size: 1rem; color: var(--color-cream);">
                    <strong>Website:</strong> <a href="https://weardaletogether.org.uk" target="_blank" style="text-decoration: underline; color: var(--color-tan);">weardaletogether.org.uk</a>
                </p>
            </div>

            <!-- Column 3: Navigation Menu -->
            <div>
                <h4 style="font-family: var(--font-body); font-weight: 700; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.05em; font-size: 0.95rem;">Quick Links</h4>
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
                        <li><a href="<?php echo esc_url( home_url( '/news-blog/' ) ); ?>">News & Blog</a></li>
                        <li><a href="<?php echo esc_url( home_url( '/volunteer/' ) ); ?>">Volunteer With Us</a></li>
                        <li><a href="<?php echo esc_url( home_url( '/newsletter/' ) ); ?>">Newsletter Sign-up</a></li>
                        <li><a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Get In Touch</a></li>
                    </ul>
                    <?php
                }
                ?>
            </div>

            <!-- Column 4: Newsletter Signpost -->
            <div>
                <h4 style="font-family: var(--font-body); font-weight: 700; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.05em; font-size: 0.95rem;">Community Newsletter</h4>
                <p style="font-size: 0.95rem; line-height: 1.4; color: var(--color-cream); margin-bottom: 1rem;">
                    <?php esc_html_e( 'Stay up to date with events, café menus, and craft workshops across the Pennines.', 'weardale-together' ); ?>
                </p>
                <div style="background-color: rgba(255, 255, 255, 0.1); padding: 0.5rem; border-radius: var(--border-radius-sm); border: 1px solid rgba(255, 255, 255, 0.2);">
                    <p style="font-size: 0.85rem; color: var(--color-tan); margin-bottom: 0; text-align: center;">
                        <em><?php esc_html_e( 'Newsletter signup form will load here once the Mailchimp embed code has been connected.', 'weardale-together' ); ?></em>
                    </p>
                </div>
            </div>

        </div>

        <!-- Copyright and Credits -->
        <div class="footer-bottom">
            <div>
                &copy; <?php echo date( 'Y' ); ?> Weardale Together CIC. <?php esc_html_e( 'All rights reserved.', 'weardale-together' ); ?>
            </div>
            <div style="display: flex; gap: 1.5rem; align-items: center;">
                <a href="<?php echo esc_url( home_url( '/privacy-notice/' ) ); ?>" style="text-decoration: none; font-size: 0.85rem;"><?php esc_html_e( 'Privacy Notice', 'weardale-together' ); ?></a>
                
                <!-- Social media icons -->
                <div style="display: flex; gap: 0.75rem;">
                    <a href="https://facebook.com/weardaletogether" target="_blank" aria-label="Facebook (Opens in new tab)" style="text-decoration: none;">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8H7v3h2v9h4v-9h3.6l.4-3H13V6c0-.5.5-1 1-1h3V1H13c-3 0-4 2-4 4v3z"/></svg>
                    </a>
                    <a href="https://instagram.com/weardaletogether" target="_blank" aria-label="Instagram (Opens in new tab)" style="text-decoration: none;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zM17.5 6.5h.01"/></svg>
                    </a>
                </div>
            </div>
        </div>

    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
