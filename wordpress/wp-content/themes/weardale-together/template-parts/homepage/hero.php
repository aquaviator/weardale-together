<?php
/**
 * Template part for displaying the homepage hero section.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */
?>

<section class="homepage-hero">
    <!-- Organic background shape decoration -->
    <div class="homepage-hero-bg-shape"></div>
    
    <div class="container homepage-hero-container">
        <div class="homepage-hero-content">
            <span class="homepage-hero-badge">
                <?php esc_html_e( 'Community at the heart', 'weardale-together' ); ?>
            </span>
            
            <h1 class="homepage-hero-title font-display">
                <?php esc_html_e( 'Living rurally shouldn\'t mean living without access', 'weardale-together' ); ?> <br class="hero-br" /><?php esc_html_e( 'to creativity, community, and connection.', 'weardale-together' ); ?>
            </h1>
            
            <p class="homepage-hero-description">
                <?php esc_html_e( 'Weardale Together is a grassroots Community Interest Company serving over 500 individuals annually across Weardale. Through creative projects, a welcoming community café, and family sessions, we gather, collaborate, and thrive.', 'weardale-together' ); ?>
            </p>
            
            <div class="homepage-hero-buttons">
                <a href="#hub-and-spoke-section" class="btn btn-primary homepage-hero-btn-primary">
                    <?php esc_html_e( 'Explore Our Strands', 'weardale-together' ); ?>
                </a>
                <a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>" class="btn btn-secondary homepage-hero-btn-secondary">
                    <?php esc_html_e( 'Visit Stanhope Hub', 'weardale-together' ); ?>
                </a>
            </div>
        </div>
    </div>
</section>
