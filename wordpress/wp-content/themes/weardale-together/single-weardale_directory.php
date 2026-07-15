<?php
/**
 * The template for displaying a single custom Weardale Directory entry.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.2.0
 */

get_header();

$post_id = get_the_ID();

// Retrieve metadata safely via plugin helper
$meta = function_exists( 'weardale_platform_get_directory_meta' ) 
    ? weardale_platform_get_directory_meta( $post_id ) 
    : array();

// Taxonomies
$types = get_the_terms( $post_id, 'directory_type' );
$villages = get_the_terms( $post_id, 'village' );
$areas = get_the_terms( $post_id, 'service_area' );

$type_name = ! empty( $types ) && ! is_wp_error( $types ) ? reset( $types )->name : '';
$type_slug = ! empty( $types ) && ! is_wp_error( $types ) ? reset( $types )->slug : '';
$village_name = ! empty( $villages ) && ! is_wp_error( $villages ) ? reset( $villages )->name : '';
$area_name = ! empty( $areas ) && ! is_wp_error( $areas ) ? reset( $areas )->name : '';

// Map Type to visual style accents
$accent_color = 'var(--color-forest)';
switch ( $type_slug ) {
    case 'business':
    case 'food-drink':
        $accent_color = '#b45309'; // Terracotta amber
        break;
    case 'community-facility':
    case 'transport':
        $accent_color = '#1e3a8a'; // Dark blue
        break;
    case 'support-service':
    case 'health-wellbeing':
        $accent_color = '#047857'; // Forest emerald
        break;
    case 'volunteer-opportunity':
        $accent_color = '#6b21a8'; // Purple
        break;
    case 'arts-culture':
        $accent_color = '#be185d'; // Deep pink
        break;
}
?>

<main id="primary-content" class="site-main" role="main">
    
    <!-- Top breadcrumb navigation and back links -->
    <div style="background-color: var(--color-cream); border-bottom: 1px solid var(--color-tan); padding: 1rem 0;">
        <div class="container" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
            <a href="<?php echo esc_url( get_post_type_archive_link( 'weardale_directory' ) ); ?>" class="btn btn-secondary" style="
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.4rem 1rem;
                font-size: 0.9rem;
                text-decoration: none;
                font-weight: 700;
            " aria-label="<?php esc_attr_e( 'Back to Directory listings archive', 'weardale-together' ); ?>">
                &larr; <?php esc_html_e( 'Back to Directory', 'weardale-together' ); ?>
            </a>
            
            <div style="font-size: 0.85rem; color: var(--text-secondary); font-family: var(--font-headings);">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'weardale-together' ); ?></a>
                <span style="margin: 0 0.4rem;">&rarr;</span>
                <a href="<?php echo esc_url( get_post_type_archive_link( 'weardale_directory' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Directory', 'weardale-together' ); ?></a>
                <span style="margin: 0 0.4rem;">&rarr;</span>
                <span style="color: var(--color-black);"><?php the_title(); ?></span>
            </div>
        </div>
    </div>

    <!-- Active post contents loop -->
    <?php 
    while ( have_posts() ) : 
        the_post(); 
    ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="padding: 4rem 0; background-color: var(--color-white);">
            <div class="container">
                
                <div style="display: grid; grid-template-columns: 1fr; @media(min-width: 992px) { grid-template-columns: 2fr 1fr; }; gap: 3rem; align-items: start;">
                    
                    <!-- Media queries helper layout using modern CSS Grid with fallback -->
                    <div style="display: flex; flex-direction: column; gap: 2rem;">
                        
                        <!-- Header Title, Badges, Excerpt -->
                        <div>
                            <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 1rem; flex-wrap: wrap;">
                                <?php if ( ! empty( $type_name ) ) : ?>
                                    <span class="badge" style="background-color: var(--color-forest); color: var(--color-cream); font-weight: 700; font-size: 0.8rem; text-transform: uppercase;">
                                        <?php echo esc_html( $type_name ); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if ( ! empty( $village_name ) ) : ?>
                                    <span class="badge" style="background-color: var(--color-cream); color: var(--color-forest); border: 1px solid var(--color-tan); font-weight: 700; font-size: 0.8rem;">
                                        📍 <?php echo esc_html( $village_name ); ?>
                                    </span>
                                <?php endif; ?>

                                <?php if ( ! empty( $meta['verified'] ) ) : ?>
                                    <span class="badge" style="background-color: #d1fae5; color: #065f46; border: 1px solid #10b981; font-weight: 700; font-size: 0.8rem;">
                                        ✅ <?php esc_html_e( 'Verified Profile', 'weardale-together' ); ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <h1 class="font-display" style="font-size: 3rem; color: var(--color-forest); margin: 0 0 1rem 0; font-weight: normal; line-height: 1.1;">
                                <?php the_title(); ?>
                            </h1>

                            <p style="font-size: 1.25rem; line-height: 1.6; color: var(--color-black); margin: 0; font-style: italic; border-left: 3px solid <?php echo esc_attr( $accent_color ); ?>; padding-left: 1rem;">
                                <?php echo esc_html( get_the_excerpt() ); ?>
                            </p>
                        </div>

                        <!-- Main Featured Image -->
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div style="border-radius: var(--border-radius-md); overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); height: 400px;">
                                <?php the_post_thumbnail( 'large', array( 'style' => 'width: 100%; height: 100%; object-fit: cover;', 'referrerpolicy' => 'no-referrer' ) ); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Rich Text Content (Description) -->
                        <div class="entry-content" style="font-size: 1.1rem; line-height: 1.7; color: var(--color-black);">
                            <h2 class="font-display" style="font-size: 1.75rem; color: var(--color-forest); margin-top: 0; margin-bottom: 1rem; border-bottom: 2px solid var(--color-tan); padding-bottom: 0.5rem; font-weight: normal;">
                                📝 <?php esc_html_e( 'About this Listing', 'weardale-together' ); ?>
                            </h2>
                            <?php the_content(); ?>
                        </div>

                        <!-- Social Media & Share Links -->
                        <?php if ( ! empty( $meta['facebook'] ) || ! empty( $meta['instagram'] ) || ! empty( $meta['linkedin'] ) ) : ?>
                            <div style="background-color: var(--color-cream); border: 1px solid var(--color-tan); border-radius: var(--border-radius-sm); padding: 1.5rem; margin-top: 1rem;">
                                <h3 class="font-display" style="font-size: 1.25rem; color: var(--color-forest); margin: 0 0 1rem 0; font-weight: normal;">
                                    🌐 <?php esc_html_e( 'Connect on Social Media', 'weardale-together' ); ?>
                                </h3>
                                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                                    <?php if ( ! empty( $meta['facebook'] ) ) : ?>
                                        <a href="<?php echo esc_url( $meta['facebook'] ); ?>" target="_blank" rel="noopener" class="btn btn-secondary" style="padding: 0.5rem 1.25rem; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
                                            👥 Facebook
                                        </a>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $meta['instagram'] ) ) : ?>
                                        <a href="<?php echo esc_url( $meta['instagram'] ); ?>" target="_blank" rel="noopener" class="btn btn-secondary" style="padding: 0.5rem 1.25rem; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
                                            📸 Instagram
                                        </a>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $meta['linkedin'] ) ) : ?>
                                        <a href="<?php echo esc_url( $meta['linkedin'] ); ?>" target="_blank" rel="noopener" class="btn btn-secondary" style="padding: 0.5rem 1.25rem; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem;">
                                            💼 LinkedIn
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Last Reviewed Note -->
                        <?php if ( ! empty( $meta['last_reviewed'] ) ) : ?>
                            <div style="font-size: 0.85rem; color: var(--text-secondary); font-family: var(--font-mono); margin-top: 1.5rem;">
                                📅 <?php printf( esc_html__( 'Profile listing verified and last reviewed on %s', 'weardale-together' ), esc_html( wp_date( 'F j, Y', strtotime( $meta['last_reviewed'] ) ) ) ); ?>
                            </div>
                        <?php endif; ?>

                    </div>

                    <!-- Right Column: Contact, Access & Details Card -->
                    <aside class="directory-sidebar" style="
                        background-color: var(--color-cream);
                        border: 2px solid var(--color-tan);
                        border-radius: var(--border-radius-md);
                        padding: 2rem;
                        position: sticky;
                        top: 2rem;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
                        display: flex;
                        flex-direction: column;
                        gap: 1.5rem;
                    }">
                        <h2 class="font-display" style="font-size: 1.75rem; color: var(--color-forest); margin: 0 0 0.5rem 0; font-weight: normal; border-bottom: 2px solid var(--color-tan); padding-bottom: 0.5rem;">
                            📞 <?php esc_html_e( 'Contact & Access', 'weardale-together' ); ?>
                        </h2>

                        <!-- Details Fields -->
                        <div style="display: flex; flex-direction: column; gap: 1.25rem; font-size: 0.95rem;">
                            
                            <!-- Address -->
                            <?php if ( ! empty( $meta['address'] ) ) : ?>
                                <div>
                                    <strong style="display:block; font-size:0.8rem; text-transform:uppercase; color:var(--color-forest); margin-bottom:0.25rem;">📍 <?php esc_html_e( 'Address', 'weardale-together' ); ?></strong>
                                    <div style="line-height: 1.4; color: var(--color-black); white-space: pre-line;"><?php echo esc_html( $meta['address'] ); ?></div>
                                    
                                    <!-- Geotagging link to Google Maps if coords are provided -->
                                    <?php if ( ! empty( $meta['latitude'] ) && ! empty( $meta['longitude'] ) ) : ?>
                                        <a href="https://www.google.com/maps/search/?api=1&query=<?php echo esc_attr( $meta['latitude'] ); ?>,<?php echo esc_attr( $meta['longitude'] ); ?>" target="_blank" rel="noopener" style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.85rem; font-weight: 700; color: var(--color-forest); margin-top: 0.5rem; text-decoration: none;">
                                            🗺️ <?php esc_html_e( 'View map directions', 'weardale-together' ); ?> &rarr;
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Phone -->
                            <?php if ( ! empty( $meta['phone'] ) ) : ?>
                                <div>
                                    <strong style="display:block; font-size:0.8rem; text-transform:uppercase; color:var(--color-forest); margin-bottom:0.25rem;">📞 <?php esc_html_e( 'Phone', 'weardale-together' ); ?></strong>
                                    <a href="tel:<?php echo esc_attr( $meta['phone'] ); ?>" style="color: var(--color-black); text-decoration: none; font-weight:600;"><?php echo esc_html( $meta['phone'] ); ?></a>
                                </div>
                            <?php endif; ?>

                            <!-- Email -->
                            <?php if ( ! empty( $meta['email'] ) ) : ?>
                                <div>
                                    <strong style="display:block; font-size:0.8rem; text-transform:uppercase; color:var(--color-forest); margin-bottom:0.25rem;">✉️ <?php esc_html_e( 'Email', 'weardale-together' ); ?></strong>
                                    <a href="mailto:<?php echo esc_attr( $meta['email'] ); ?>" style="color: var(--color-forest); text-decoration: underline; word-break: break-all;"><?php echo esc_html( $meta['email'] ); ?></a>
                                </div>
                            <?php endif; ?>

                            <!-- Website Button -->
                            <?php if ( ! empty( $meta['website'] ) ) : ?>
                                <div style="margin-top: 0.5rem;">
                                    <a href="<?php echo esc_url( $meta['website'] ); ?>" target="_blank" rel="noopener" class="btn btn-primary" style="display: block; text-align: center; font-weight: 700; text-decoration: none; padding: 0.6rem 1rem;">
                                        🌐 Visit Website &rarr;
                                    </a>
                                </div>
                            <?php endif; ?>

                            <hr style="border: 0; border-top: 1px solid var(--color-tan); margin: 0.5rem 0;">

                            <!-- Opening Hours -->
                            <?php if ( ! empty( $meta['opening_hours'] ) ) : ?>
                                <div>
                                    <strong style="display:block; font-size:0.8rem; text-transform:uppercase; color:var(--color-forest); margin-bottom:0.25rem;">🕒 <?php esc_html_e( 'Opening Hours', 'weardale-together' ); ?></strong>
                                    <div style="line-height: 1.4; color: var(--color-black); white-space: pre-line;"><?php echo esc_html( $meta['opening_hours'] ); ?></div>
                                </div>
                            <?php endif; ?>

                            <!-- Accessibility -->
                            <?php if ( ! empty( $meta['accessibility'] ) ) : ?>
                                <div>
                                    <strong style="display:block; font-size:0.8rem; text-transform:uppercase; color:var(--color-forest); margin-bottom:0.25rem;">♿ <?php esc_html_e( 'Accessibility', 'weardale-together' ); ?></strong>
                                    <div style="line-height: 1.4; color: var(--color-black);"><?php echo esc_html( $meta['accessibility'] ); ?></div>
                                </div>
                            <?php endif; ?>

                            <!-- Who It Helps -->
                            <?php if ( ! empty( $meta['who_it_helps'] ) ) : ?>
                                <div>
                                    <strong style="display:block; font-size:0.8rem; text-transform:uppercase; color:var(--color-forest); margin-bottom:0.25rem;">👥 <?php esc_html_e( 'Who It Helps', 'weardale-together' ); ?></strong>
                                    <div style="line-height: 1.4; color: var(--color-black);"><?php echo esc_html( $meta['who_it_helps'] ); ?></div>
                                </div>
                            <?php endif; ?>

                            <!-- Pricing & Booking -->
                            <?php if ( ! empty( $meta['pricing'] ) || ! empty( $meta['booking_required'] ) ) : ?>
                                <div>
                                    <strong style="display:block; font-size:0.8rem; text-transform:uppercase; color:var(--color-forest); margin-bottom:0.25rem;">🪙 <?php esc_html_e( 'Pricing & Booking', 'weardale-together' ); ?></strong>
                                    <div style="line-height: 1.4; color: var(--color-black);">
                                        <?php if ( ! empty( $meta['pricing'] ) ) : ?>
                                            <div style="margin-bottom:0.25rem;"><strong><?php esc_html_e( 'Cost:', 'weardale-together' ); ?></strong> <?php echo esc_html( $meta['pricing'] ); ?></div>
                                        <?php endif; ?>
                                        <div>
                                            <strong><?php esc_html_e( 'Booking:', 'weardale-together' ); ?></strong> 
                                            <?php echo $meta['booking_required'] ? esc_html__( 'Booking is required', 'weardale-together' ) : esc_html__( 'No booking required', 'weardale-together' ); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Related Programmes -->
                            <?php if ( ! empty( $meta['related_programme'] ) ) : ?>
                                <hr style="border: 0; border-top: 1px solid var(--color-tan); margin: 0.5rem 0;">
                                <div>
                                    <strong style="display:block; font-size:0.8rem; text-transform:uppercase; color:var(--color-forest); margin-bottom:0.25rem;">🤝 <?php esc_html_e( 'Related Programme', 'weardale-together' ); ?></strong>
                                    <div style="line-height: 1.4; color: var(--color-black); font-weight: 600;"><?php echo esc_html( $meta['related_programme'] ); ?></div>
                                </div>
                            <?php endif; ?>

                            <!-- Related Events -->
                            <?php if ( ! empty( $meta['related_events'] ) ) : ?>
                                <div>
                                    <strong style="display:block; font-size:0.8rem; text-transform:uppercase; color:var(--color-forest); margin-bottom:0.25rem;">📅 <?php esc_html_e( 'Workshops & Events', 'weardale-together' ); ?></strong>
                                    <div style="line-height: 1.4; color: var(--color-black);"><?php echo esc_html( $meta['related_events'] ); ?></div>
                                </div>
                            <?php endif; ?>

                        </div>
                    </aside>

                </div>

            </div>
        </article>
    <?php 
    endwhile; 
    ?>

</main>

<!-- Flexible Media Queries styles injected safely for the single page layout -->
<style>
    @media (min-width: 992px) {
        article .container > div {
            grid-template-columns: 2fr 1fr !important;
        }
    }
</style>

<?php
get_footer();
