<?php
/**
 * Template part for the Volunteer callout banner on the homepage.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */
?>

<section class="section-padding volunteer-section">
    <!-- Graphical watercolor circle overlay (represented via CSS) -->
    <div class="volunteer-circle-overlay"></div>
    
    <div class="container volunteer-container">
        <div class="volunteer-box">
            
            <div>
                <span class="badge volunteer-badge">
                    <?php esc_html_e( 'Join the Team', 'weardale-together' ); ?>
                </span>
                
                <h2 class="font-display volunteer-title">
                    <?php esc_html_e( 'Share Your Time & Warmth with Weardale', 'weardale-together' ); ?>
                </h2>
                
                <p class="volunteer-text">
                    <?php esc_html_e( 'Our grassroots organization thrives because of neighbors helping neighbors. Whether assisting Cheryl in the kitchen, teaching outdoor crafts, driving residents, or leading youth games, your skills are welcome here.', 'weardale-together' ); ?>
                </p>
            </div>

            <div class="volunteer-btns">
                <a href="<?php echo esc_url( home_url( '/volunteer/' ) ); ?>" class="btn btn-white">
                    <?php esc_html_e( 'Volunteer With Us', 'weardale-together' ); ?>
                </a>
                <a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>" class="btn btn-outline btn-outline-custom">
                    <?php esc_html_e( 'Enquire Today &rarr;', 'weardale-together' ); ?>
                </a>
            </div>

        </div>
    </div>
</section>
