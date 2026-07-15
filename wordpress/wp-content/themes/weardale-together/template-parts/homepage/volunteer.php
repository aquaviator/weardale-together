<?php
/**
 * Template part for the Volunteer callout banner on the homepage.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */
?>

<section class="section-padding" style="background-color: var(--color-white); border-bottom: 1px solid var(--color-tan); position: relative; overflow: hidden;">
    <!-- Graphical watercolor circle overlay (represented via CSS) -->
    <div style="position: absolute; width: 400px; height: 400px; background-color: rgba(196, 149, 106, 0.05); border-radius: 50%; bottom: -100px; left: -100px; pointer-events: none;"></div>
    
    <div class="container" style="position: relative; z-index: 2;">
        <div style="background-color: var(--color-forest); color: var(--color-cream); border-radius: var(--border-radius-md); padding: 4rem 3rem; display: grid; grid-template-columns: 1.5fr 1fr; gap: 3rem; align-items: center; box-shadow: 0 15px 35px rgba(59, 92, 58, 0.15);">
            
            <div>
                <span class="badge" style="background-color: rgba(245, 240, 232, 0.2); color: var(--color-cream); margin-bottom: 1rem; font-size: 0.85rem; font-family: var(--font-body); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                    <?php esc_html_e( 'Join the Team', 'weardale-together' ); ?>
                </span>
                
                <h2 class="font-display" style="font-size: 2.5rem; color: var(--color-cream); margin-bottom: 1rem; line-height: 1.2;">
                    <?php esc_html_e( 'Share Your Time & Warmth with Weardale', 'weardale-together' ); ?>
                </h2>
                
                <p style="font-size: 1.1rem; line-height: 1.5; color: var(--color-cream); opacity: 0.9; margin-bottom: 0;">
                    <?php esc_html_e( 'Our grassroots organization thrives because of neighbors helping neighbors. Whether assisting Cheryl in the kitchen, teaching outdoor crafts, driving residents, or leading youth games, your skills are welcome here.', 'weardale-together' ); ?>
                </p>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1rem; justify-content: center; align-items: flex-start; @media(min-width: 768px){ align-items: flex-end; }">
                <a href="<?php echo esc_url( home_url( '/volunteer/' ) ); ?>" class="btn btn-white" style="width: 100%; text-align: center; max-width: 250px; font-weight: 700; color: var(--color-forest);">
                    <?php esc_html_e( 'Volunteer With Us', 'weardale-together' ); ?>
                </a>
                <a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>" class="btn" style="border-color: rgba(245, 240, 232, 0.3); color: var(--color-cream); width: 100%; text-align: center; max-width: 250px;">
                    <?php esc_html_e( 'Enquire Today &rarr;', 'weardale-together' ); ?>
                </a>
            </div>

        </div>
    </div>
</section>

<style>
@media (max-width: 768px) {
    .section-padding > .container > div {
        grid-template-columns: 1fr !important;
        padding: 3rem 2rem !important;
        gap: 2rem !important;
    }
}
</style>
