<?php
/**
 * Template part for the "Programme Areas" Spotlight Cards section.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */
?>

<section class="section-padding" style="background-color: var(--color-cream); border-bottom: 1px solid var(--color-tan);">
    <div class="container">
        
        <header style="text-align: center; margin-bottom: 4rem;">
            <span class="badge badge-cafe" style="margin-bottom: 0.5rem;"><?php esc_html_e( 'Our Strands', 'weardale-together' ); ?></span>
            <h2 class="font-display" style="font-size: 2.5rem; margin-bottom: 1rem;"><?php esc_html_e( 'Step Into Our Different Rooms', 'weardale-together' ); ?></h2>
            <p style="color: var(--text-secondary); max-width: 600px; margin: 0 auto; font-size: 1.1rem;">
                <?php esc_html_e( 'Each programme we offer has its own distinct personality and target audience, yet they are all rooms in the same warm house.', 'weardale-together' ); ?>
            </p>
            <div style="width: 50px; height: 3px; background-color: var(--color-forest); margin: 1.5rem auto 0 auto;"></div>
        </header>

        <div class="grid grid-2">
            
            <!-- Strand 1: Café -->
            <div class="card strand-cafe" style="border-top: 5px solid var(--color-strand-cafe);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                    <span style="font-size: 2.5rem; line-height: 1;">☕</span>
                    <span class="badge badge-cafe"><?php esc_html_e( 'Root & Branch', 'weardale-together' ); ?></span>
                </div>
                <h3 class="font-display" style="font-size: 1.75rem; color: #9E6B3E; margin-bottom: 1rem;"><?php esc_html_e( 'Root & Branch Café', 'weardale-together' ); ?></h3>
                <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 2rem; flex-grow: 1;">
                    <?php esc_html_e( 'Warm, sandy tones and delicious aroma. The café is the physical and emotional heart of Weardale Together. Experience cosy, food-led hospitality and genuine, unhurried community warmth in Stanhope.', 'weardale-together' ); ?>
                </p>
                <a href="<?php echo esc_url( home_url( '/cafe/' ) ); ?>" class="btn btn-primary" style="background-color: var(--color-strand-cafe); border-color: var(--color-strand-cafe); align-self: flex-start; font-size: 0.95rem; padding: 0.6rem 1.25rem;">
                    <?php esc_html_e( 'Visit the Café', 'weardale-together' ); ?>
                </a>
            </div>

            <!-- Strand 2: Creative Roots -->
            <div class="card strand-creative" style="border-top: 5px solid var(--color-strand-creative);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                    <span style="font-size: 2.5rem; line-height: 1;">🎨</span>
                    <span class="badge badge-creative"><?php esc_html_e( 'Creative Roots', 'weardale-together' ); ?></span>
                </div>
                <h3 class="font-display" style="font-size: 1.75rem; color: #BA7D0C; margin-bottom: 1rem;"><?php esc_html_e( 'Creative Arts', 'weardale-together' ); ?></h3>
                <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 2rem; flex-grow: 1;">
                    <?php esc_html_e( 'Botanical illustration styles, ink drawings, and seasonal washes. We deliver a gorgeous, earthy programme of workshops, crafts, and heritage projects for people who "don\'t think of themselves as creative".', 'weardale-together' ); ?>
                </p>
                <a href="<?php echo esc_url( home_url( '/creative-arts/' ) ); ?>" class="btn btn-primary" style="background-color: var(--color-strand-creative); border-color: var(--color-strand-creative); align-self: flex-start; font-size: 0.95rem; padding: 0.6rem 1.25rem;">
                    <?php esc_html_e( 'Explore Workshops', 'weardale-together' ); ?>
                </a>
            </div>

            <!-- Strand 3: Youth Programme -->
            <div class="card strand-youth" style="border-top: 5px solid var(--color-strand-youth);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                    <span style="font-size: 2.5rem; line-height: 1;">🌲</span>
                    <span class="badge badge-youth"><?php esc_html_e( 'Young People', 'weardale-together' ); ?></span>
                </div>
                <h3 class="font-display" style="font-size: 1.75rem; color: var(--color-strand-youth); margin-bottom: 1rem;"><?php esc_html_e( 'Youth & Forest School', 'weardale-together' ); ?></h3>
                <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 2rem; flex-grow: 1;">
                    <?php esc_html_e( 'High contrast, collage aesthetic, and vivid amber washes. A high-energy, youth-led environment where young minds feel fully ownership of their space. Includes Forest School adventures and outdoor discovery.', 'weardale-together' ); ?>
                </p>
                <a href="<?php echo esc_url( home_url( '/young-people/' ) ); ?>" class="btn btn-primary" style="background-color: var(--color-strand-youth); border-color: var(--color-strand-youth); align-self: flex-start; font-size: 0.95rem; padding: 0.6rem 1.25rem;">
                    <?php esc_html_e( 'See Youth Programs', 'weardale-together' ); ?>
                </a>
            </div>

            <!-- Strand 4: Roots & Shoots -->
            <div class="card strand-shoots" style="border-top: 5px solid var(--color-strand-shoots);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                    <span style="font-size: 2.5rem; line-height: 1;">🧸</span>
                    <span class="badge badge-shoots"><?php esc_html_e( 'Roots & Shoots', 'weardale-together' ); ?></span>
                </div>
                <h3 class="font-display" style="font-size: 1.75rem; color: #B2583E; margin-bottom: 1rem;"><?php esc_html_e( 'Roots & Shoots', 'weardale-together' ); ?></h3>
                <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 2rem; flex-grow: 1;">
                    <?php esc_html_e( 'Soft and gentle, terracotta pot illustration style, with baby pinks and sage greens. An unhurried early years family playroom for babies, toddlers, and their carers, providing a slow, nurturing space.', 'weardale-together' ); ?>
                </p>
                <a href="<?php echo esc_url( home_url( '/roots-shoots/' ) ); ?>" class="btn btn-primary" style="background-color: var(--color-strand-shoots); border-color: var(--color-strand-shoots); align-self: flex-start; font-size: 0.95rem; padding: 0.6rem 1.25rem;">
                    <?php esc_html_e( 'View Playrooms', 'weardale-together' ); ?>
                </a>
            </div>

        </div>

    </div>
</section>
