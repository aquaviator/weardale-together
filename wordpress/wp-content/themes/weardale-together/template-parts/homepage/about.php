<?php
/**
 * Template part for the About Weardale Together / Core Values section.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */
?>

<section class="section-padding about-section">
    <div class="container">
        
        <div class="about-grid">
            
            <!-- Left block: Mission & Introduction -->
            <div>
                <span class="badge badge-creative mb-2"><?php esc_html_e( 'Our Mission', 'weardale-together' ); ?></span>
                <h2 class="font-display about-title"><?php esc_html_e( 'Who We Are', 'weardale-together' ); ?></h2>
                <p class="about-text">
                    <?php esc_html_e( 'Founded in 2021 in Stanhope, Weardale Together began as a small Warm Space to protect residents from isolation and winter cold. Over four years, we have organically blossomed into a multi-strand grassroots Community Interest Company serving the broader North Pennines.', 'weardale-together' ); ?>
                </p>
                <p class="about-text">
                    <?php esc_html_e( 'Our work stretches over 20 miles of stunning but remote valleys, supporting around 8,000 residents poorly served by public transit and public infrastructure. We believe geography should never limit cultural and community access.', 'weardale-together' ); ?>
                </p>
                <a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" class="btn btn-secondary">
                    <?php esc_html_e( 'Read Our Full Story', 'weardale-together' ); ?>
                </a>
            </div>

            <!-- Right block: Values grid -->
            <div class="about-values-box">
                <h3 class="font-display about-values-title"><?php esc_html_e( 'The Values We Stand By', 'weardale-together' ); ?></h3>
                
                <div class="about-values-grid">
                    
                    <!-- Value 1 -->
                    <div class="about-value-item">
                        <span class="about-value-icon">🌱</span>
                        <div>
                            <h4 class="about-value-name"><?php esc_html_e( 'Care & Quality', 'weardale-together' ); ?></h4>
                            <p class="about-value-desc">
                                <?php esc_html_e( 'Doing things thoroughly and thoughtfully, with genuine regard for the dignity and wellbeing of our rural neighbors.', 'weardale-together' ); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Value 2 -->
                    <div class="about-value-item">
                        <span class="about-value-icon">🤝</span>
                        <div>
                            <h4 class="about-value-name"><?php esc_html_e( 'Exchange & Collaboration', 'weardale-together' ); ?></h4>
                            <p class="about-value-desc">
                                <?php esc_html_e( 'Fostering a mutual community space where everyone shares skills, ideas, and stories on equal terms.', 'weardale-together' ); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Value 3 -->
                    <div class="about-value-item">
                        <span class="about-value-icon">🏔️</span>
                        <div>
                            <h4 class="about-value-name"><?php esc_html_e( 'Ambition for our Community', 'weardale-together' ); ?></h4>
                            <p class="about-value-desc">
                                <?php esc_html_e( 'Expecting the highest quality of arts, hospitality, and opportunities for the people of the North Pennines valley.', 'weardale-together' ); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Value 4 -->
                    <div class="about-value-item">
                        <span class="about-value-icon">🔍</span>
                        <div>
                            <h4 class="about-value-name"><?php esc_html_e( 'Honesty & Accountability', 'weardale-together' ); ?></h4>
                            <p class="about-value-desc">
                                <?php esc_html_e( 'Operating as a clear, transparent Community Interest Company where assets are locked for community benefit.', 'weardale-together' ); ?>
                            </p>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
</section>
