<?php
/**
 * Template part for the Newsletter signup section on the homepage.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */
?>

<section class="section-padding newsletter-section">
    <div class="container-narrow newsletter-container">
        
        <span class="newsletter-icon">✉️</span>
        
        <h2 class="font-display newsletter-title">
            <?php esc_html_e( 'Stay Connected in the Hills', 'weardale-together' ); ?>
        </h2>
        
        <p class="newsletter-text">
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
