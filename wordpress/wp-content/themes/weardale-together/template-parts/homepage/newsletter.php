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
        <div class="newsletter-embed-box">
            <?php 
            if ( function_exists( 'weardale_platform_get_newsletter_form' ) ) {
                echo weardale_platform_get_newsletter_form( 'homepage' );
            }
            ?>
        </div>

    </div>
</section>
