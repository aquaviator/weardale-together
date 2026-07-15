<?php
/**
 * Template part for the About Weardale Together / Core Values section.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */
?>

<section class="section-padding" style="background-color: var(--color-white); border-bottom: 1px solid var(--color-tan);">
    <div class="container">
        
        <div style="display: grid; grid-template-columns: 1fr 1.25fr; gap: 4rem; align-items: center;">
            
            <!-- Left block: Mission & Introduction -->
            <div>
                <span class="badge badge-creative" style="margin-bottom: 0.5rem;"><?php esc_html_e( 'Our Mission', 'weardale-together' ); ?></span>
                <h2 class="font-display" style="font-size: 2.5rem; margin-bottom: 1.5rem;"><?php esc_html_e( 'Who We Are', 'weardale-together' ); ?></h2>
                <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 1.5rem;">
                    <?php esc_html_e( 'Founded in 2021 in Stanhope, Weardale Together began as a small Warm Space to protect residents from isolation and winter cold. Over four years, we have organically blossomed into a multi-strand grassroots Community Interest Company serving the broader North Pennines.', 'weardale-together' ); ?>
                </p>
                <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 2rem;">
                    <?php esc_html_e( 'Our work stretches over 20 miles of stunning but remote valleys, supporting around 8,000 residents poorly served by public transit and public infrastructure. We believe geography should never limit cultural and community access.', 'weardale-together' ); ?>
                </p>
                <a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" class="btn btn-secondary">
                    <?php esc_html_e( 'Read Our Full Story', 'weardale-together' ); ?>
                </a>
            </div>

            <!-- Right block: Values grid -->
            <div style="background-color: var(--color-cream); padding: 3rem; border-radius: var(--border-radius-md); border: 1px solid var(--color-tan);">
                <h3 class="font-display" style="font-size: 1.75rem; text-align: center; margin-bottom: 2.5rem;"><?php esc_html_e( 'The Values We Stand By', 'weardale-together' ); ?></h3>
                
                <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
                    
                    <!-- Value 1 -->
                    <div style="display: flex; gap: 1rem;">
                        <span style="font-size: 1.5rem; line-height: 1;">🌱</span>
                        <div>
                            <h4 style="font-family: var(--font-body); font-weight: 700; margin-bottom: 0.25rem; font-size: 1.15rem; color: var(--color-forest);"><?php esc_html_e( 'Care & Quality', 'weardale-together' ); ?></h4>
                            <p style="margin: 0; font-size: 0.95rem; color: var(--text-secondary); line-height: 1.4;">
                                <?php esc_html_e( 'Doing things thoroughly and thoughtfully, with genuine regard for the dignity and wellbeing of our rural neighbors.', 'weardale-together' ); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Value 2 -->
                    <div style="display: flex; gap: 1rem;">
                        <span style="font-size: 1.5rem; line-height: 1;">🤝</span>
                        <div>
                            <h4 style="font-family: var(--font-body); font-weight: 700; margin-bottom: 0.25rem; font-size: 1.15rem; color: var(--color-forest);"><?php esc_html_e( 'Exchange & Collaboration', 'weardale-together' ); ?></h4>
                            <p style="margin: 0; font-size: 0.95rem; color: var(--text-secondary); line-height: 1.4;">
                                <?php esc_html_e( 'Fostering a mutual community space where everyone shares skills, ideas, and stories on equal terms.', 'weardale-together' ); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Value 3 -->
                    <div style="display: flex; gap: 1rem;">
                        <span style="font-size: 1.5rem; line-height: 1;">🏔️</span>
                        <div>
                            <h4 style="font-family: var(--font-body); font-weight: 700; margin-bottom: 0.25rem; font-size: 1.15rem; color: var(--color-forest);"><?php esc_html_e( 'Ambition for our Community', 'weardale-together' ); ?></h4>
                            <p style="margin: 0; font-size: 0.95rem; color: var(--text-secondary); line-height: 1.4;">
                                <?php esc_html_e( 'Expecting the highest quality of arts, hospitality, and opportunities for the people of the North Pennines valley.', 'weardale-together' ); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Value 4 -->
                    <div style="display: flex; gap: 1rem;">
                        <span style="font-size: 1.5rem; line-height: 1;">🔍</span>
                        <div>
                            <h4 style="font-family: var(--font-body); font-weight: 700; margin-bottom: 0.25rem; font-size: 1.15rem; color: var(--color-forest);"><?php esc_html_e( 'Honesty & Accountability', 'weardale-together' ); ?></h4>
                            <p style="margin: 0; font-size: 0.95rem; color: var(--text-secondary); line-height: 1.4;">
                                <?php esc_html_e( 'Operating as a clear, transparent Community Interest Company where assets are locked for community benefit.', 'weardale-together' ); ?>
                            </p>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
</section>

<style>
@media (max-width: 992px) {
    .section-padding > .container > div {
        grid-template-columns: 1fr !important;
        gap: 3rem !important;
    }
}
</style>
