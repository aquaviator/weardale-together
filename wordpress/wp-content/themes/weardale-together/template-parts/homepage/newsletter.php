<?php
/**
 * Template part for the Newsletter signup section on the homepage.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */
?>

<section class="section-padding" style="background-color: var(--color-cream); border-bottom: 1px solid var(--color-tan);">
    <div class="container-narrow" style="text-align: center;">
        
        <span style="font-size: 2.5rem; line-height: 1; display: block; margin-bottom: 1rem;">✉️</span>
        
        <h2 class="font-display" style="font-size: 2.25rem; color: var(--color-forest); margin-bottom: 1rem;">
            <?php esc_html_e( 'Stay Connected in the Hills', 'weardale-together' ); ?>
        </h2>
        
        <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 2rem;">
            <?php esc_html_e( 'Subscribe to our community newsletter and receive our monthly menus, class announcements, seasonal Wassail dates, and volunteer roundups delivered right to your inbox.', 'weardale-together' ); ?>
        </p>

        <!-- Newsletter Embed Container / Placeholder -->
        <div class="newsletter-embed-box" style="background-color: var(--color-white); border: 1px solid var(--color-tan); border-radius: var(--border-radius-md); padding: 2.5rem 2rem; box-shadow: 0 4px 15px rgba(196, 184, 154, 0.15);">
            
            <!-- Standard human label fallback -->
            <form action="#" method="get" onsubmit="event.preventDefault();" style="display: flex; gap: 0.75rem; flex-wrap: wrap; justify-content: center;">
                <label for="newsletter-email" class="sr-only" style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); border: 0;">Email Address</label>
                <input type="email" id="newsletter-email" class="form-control" placeholder="Enter your email address..." required style="max-width: 380px; flex-grow: 1; border-color: var(--color-tan); border-radius: var(--border-radius-pill); padding-left: 1.25rem;">
                <button type="submit" class="btn btn-primary">
                    <?php esc_html_e( 'Sign Up Now', 'weardale-together' ); ?>
                </button>
            </form>

            <div style="margin-top: 1.25rem; font-size: 0.85rem; color: var(--text-light); line-height: 1.4;">
                <p style="margin: 0;">
                    <em><?php esc_html_e( '*This form will instantly load your Mailchimp account integrations once the embed code is inserted into the widgets panel.', 'weardale-together' ); ?></em>
                </p>
                <p style="margin: 0.25rem 0 0 0;">
                    <?php esc_html_e( 'We respect your privacy. Unsubscribe at any time. Read our ', 'weardale-together' ); ?>
                    <a href="<?php echo esc_url( home_url( '/privacy-notice/' ) ); ?>" style="color: var(--color-sage);"><?php esc_html_e( 'Privacy Notice', 'weardale-together' ); ?></a>.
                </p>
            </div>

        </div>

    </div>
</section>
