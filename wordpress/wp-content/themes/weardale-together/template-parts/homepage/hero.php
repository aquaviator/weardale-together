<?php
/**
 * Template part for displaying the homepage hero section.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */
?>

<section class="homepage-hero" style="background-color: var(--color-white); border-bottom: 1px solid var(--color-tan); position: relative; overflow: hidden; padding: 5rem 0;">
    <!-- Organic background shape decoration -->
    <div style="position: absolute; width: 600px; height: 600px; background-color: rgba(107, 143, 94, 0.04); border-radius: 50%; top: -200px; right: -100px; pointer-events: none;"></div>
    
    <div class="container" style="position: relative; z-index: 2;">
        <div style="max-width: 800px; margin: 0 auto; text-align: center;">
            <span class="badge" style="background-color: rgba(59, 92, 58, 0.1); color: var(--color-forest); margin-bottom: 1.5rem; font-size: 0.95rem; font-family: var(--font-body); text-transform: none; letter-spacing: normal; padding: 0.5rem 1rem;">
                <?php esc_html_e( 'Community at the heart', 'weardale-together' ); ?>
            </span>
            
            <h1 class="font-display" style="font-size: 3.5rem; line-height: 1.1; color: var(--color-forest); margin-bottom: 1.5rem;">
                <?php esc_html_e( 'Living rurally shouldn\'t mean living without connection.', 'weardale-together' ); ?>
            </h1>
            
            <p style="font-size: 1.25rem; line-height: 1.6; color: var(--text-secondary); margin-bottom: 2.5rem;">
                <?php esc_html_e( 'Weardale Together is a grassroots Community Interest Company serving over 500 individuals annually across Weardale. Through creative projects, a welcoming community café, and family sessions, we gather, collaborate, and thrive.', 'weardale-together' ); ?>
            </p>
            
            <div style="display: flex; gap: 1.25rem; justify-content: center; flex-wrap: wrap;">
                <a href="#hub-and-spoke-section" class="btn btn-primary">
                    <?php esc_html_e( 'Explore Our Strands', 'weardale-together' ); ?>
                </a>
                <a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>" class="btn btn-secondary">
                    <?php esc_html_e( 'Visit Stanhope Hub', 'weardale-together' ); ?>
                </a>
            </div>
        </div>
    </div>
</section>
