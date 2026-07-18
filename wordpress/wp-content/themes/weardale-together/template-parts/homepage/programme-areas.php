<?php
/**
 * Template part for the "Programme Areas" Spotlight Cards section.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */
?>

<section class="section-padding programme-areas-section">
    <div class="container">
        
        <header class="programme-areas-header">
            <span class="badge badge-cafe mb-2"><?php esc_html_e( 'Our Strands', 'weardale-together' ); ?></span>
            <h2 class="font-display"><?php esc_html_e( 'Step Into Our Different Rooms', 'weardale-together' ); ?></h2>
            <p>
                <?php esc_html_e( 'Each programme we offer has its own distinct personality and target audience, yet they are all rooms in the same warm house.', 'weardale-together' ); ?>
            </p>
            <div class="programme-areas-divider"></div>
        </header>

        <div class="grid grid-2">
            
            <!-- Strand 1: Café -->
            <div class="card strand-cafe">
                <div class="programme-card-header">
                    <span class="programme-card-icon">☕</span>
                    <span class="badge badge-cafe"><?php esc_html_e( 'Root & Branch', 'weardale-together' ); ?></span>
                </div>
                <h3 class="font-display programme-card-title strand-cafe"><?php esc_html_e( 'Root & Branch Café', 'weardale-together' ); ?></h3>
                <p class="programme-card-desc">
                    <?php esc_html_e( 'Warm, sandy tones and delicious aroma. The café is the physical and emotional heart of Weardale Together. Experience cosy, food-led hospitality and genuine, unhurried community warmth in Stanhope.', 'weardale-together' ); ?>
                </p>
                <a href="<?php echo esc_url( home_url( '/cafe/' ) ); ?>" class="btn btn-primary programme-card-btn strand-cafe">
                    <?php esc_html_e( 'Visit the Café', 'weardale-together' ); ?>
                </a>
            </div>

            <!-- Strand 2: Creative Roots -->
            <div class="card strand-creative">
                <div class="programme-card-header">
                    <span class="programme-card-icon">🎨</span>
                    <span class="badge badge-creative"><?php esc_html_e( 'Creative Roots', 'weardale-together' ); ?></span>
                </div>
                <h3 class="font-display programme-card-title strand-creative"><?php esc_html_e( 'Creative Arts', 'weardale-together' ); ?></h3>
                <p class="programme-card-desc">
                    <?php esc_html_e( 'Botanical illustration styles, ink drawings, and seasonal washes. We deliver a gorgeous, earthy programme of workshops, crafts, and heritage projects for people who "don\'t think of themselves as creative".', 'weardale-together' ); ?>
                </p>
                <a href="<?php echo esc_url( home_url( '/creative-arts/' ) ); ?>" class="btn btn-primary programme-card-btn strand-creative">
                    <?php esc_html_e( 'Explore Workshops', 'weardale-together' ); ?>
                </a>
            </div>

            <!-- Strand 3: Youth Programme -->
            <div class="card strand-youth">
                <div class="programme-card-header">
                    <span class="programme-card-icon">🌲</span>
                    <span class="badge badge-youth"><?php esc_html_e( 'Young People', 'weardale-together' ); ?></span>
                </div>
                <h3 class="font-display programme-card-title strand-youth"><?php esc_html_e( 'Youth & Forest School', 'weardale-together' ); ?></h3>
                <p class="programme-card-desc">
                    <?php esc_html_e( 'High contrast, collage aesthetic, and vivid amber washes. A high-energy, youth-led environment where young minds feel fully ownership of their space. Includes Forest School adventures and outdoor discovery.', 'weardale-together' ); ?>
                </p>
                <a href="<?php echo esc_url( home_url( '/young-people/' ) ); ?>" class="btn btn-primary programme-card-btn strand-youth">
                    <?php esc_html_e( 'See Youth Programs', 'weardale-together' ); ?>
                </a>
            </div>

            <!-- Strand 4: Roots & Shoots -->
            <div class="card strand-shoots">
                <div class="programme-card-header">
                    <span class="programme-card-icon">🧸</span>
                    <span class="badge badge-shoots"><?php esc_html_e( 'Roots & Shoots', 'weardale-together' ); ?></span>
                </div>
                <h3 class="font-display programme-card-title strand-shoots"><?php esc_html_e( 'Roots & Shoots', 'weardale-together' ); ?></h3>
                <p class="programme-card-desc">
                    <?php esc_html_e( 'Soft and gentle, terracotta pot illustration style, with baby pinks and sage greens. An unhurried early years family playroom for babies, toddlers, and their carers, providing a slow, nurturing space.', 'weardale-together' ); ?>
                </p>
                <a href="<?php echo esc_url( home_url( '/roots-shoots/' ) ); ?>" class="btn btn-primary programme-card-btn strand-shoots">
                    <?php esc_html_e( 'View Playrooms', 'weardale-together' ); ?>
                </a>
            </div>

        </div>

    </div>
</section>
