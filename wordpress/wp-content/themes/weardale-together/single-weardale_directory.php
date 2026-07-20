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
    <div class="directory-breadcrumb-bar">
        <div class="container directory-breadcrumb-container">
            <a href="<?php echo esc_url( get_post_type_archive_link( 'weardale_directory' ) ); ?>" class="btn btn-secondary directory-back-btn" aria-label="<?php esc_attr_e( 'Back to Directory listings archive', 'weardale-together' ); ?>">
                &larr; <?php esc_html_e( 'Back to Directory', 'weardale-together' ); ?>
            </a>
            
            <div class="directory-breadcrumbs">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'weardale-together' ); ?></a>
                <span>&rarr;</span>
                <a href="<?php echo esc_url( get_post_type_archive_link( 'weardale_directory' ) ); ?>"><?php esc_html_e( 'Directory', 'weardale-together' ); ?></a>
                <span>&rarr;</span>
                <span class="current-item"><?php the_title(); ?></span>
            </div>
        </div>
    </div>

    <!-- Active post contents loop -->
    <?php 
    while ( have_posts() ) : 
        the_post(); 
    ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class( 'directory-article' ); ?>>
            <div class="container">
                
                <div class="directory-layout">
                    
                    <div class="directory-main">
                        
                        <!-- Header Title, Badges, Excerpt -->
                        <div class="directory-header-block">
                            <div class="directory-badges">
                                <?php if ( ! empty( $type_name ) ) : ?>
                                    <span class="directory-badge directory-badge-type">
                                        <?php echo esc_html( $type_name ); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if ( ! empty( $village_name ) ) : ?>
                                    <span class="directory-badge directory-badge-village">
                                        📍 <?php echo esc_html( $village_name ); ?>
                                    </span>
                                <?php endif; ?>

                                <?php if ( ! empty( $meta['verified'] ) ) : ?>
                                    <span class="directory-badge directory-badge-verified">
                                        ✅ <?php esc_html_e( 'Verified Profile', 'weardale-together' ); ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <h1 class="directory-title">
                                <?php the_title(); ?>
                            </h1>

                            <p class="directory-excerpt" style="--directory-accent: <?php echo esc_attr( $accent_color ); ?>;">
                                <?php echo esc_html( get_the_excerpt() ); ?>
                            </p>
                        </div>

                        <!-- Main Featured Image -->
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="directory-image-container">
                                <?php the_post_thumbnail( 'large', array( 'class' => 'directory-featured-img', 'referrerpolicy' => 'no-referrer' ) ); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Rich Text Content (Description) -->
                        <div class="entry-content directory-content directory-content-block">
                            <h2 class="directory-section-title">
                                📝 <?php esc_html_e( 'About this Listing', 'weardale-together' ); ?>
                            </h2>
                            <?php the_content(); ?>
                        </div>

                        <!-- Social Media & Share Links -->
                        <?php if ( ! empty( $meta['facebook'] ) || ! empty( $meta['instagram'] ) || ! empty( $meta['linkedin'] ) ) : ?>
                            <div class="directory-social-container">
                                <h3 class="directory-social-title">
                                    🌐 <?php esc_html_e( 'Connect on Social Media', 'weardale-together' ); ?>
                                </h3>
                                <div class="directory-social-grid">
                                    <?php if ( ! empty( $meta['facebook'] ) ) : ?>
                                        <a href="<?php echo esc_url( $meta['facebook'] ); ?>" target="_blank" rel="noopener" class="btn btn-secondary directory-social-btn">
                                            👥 Facebook
                                        </a>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $meta['instagram'] ) ) : ?>
                                        <a href="<?php echo esc_url( $meta['instagram'] ); ?>" target="_blank" rel="noopener" class="btn btn-secondary directory-social-btn">
                                            📸 Instagram
                                        </a>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $meta['linkedin'] ) ) : ?>
                                        <a href="<?php echo esc_url( $meta['linkedin'] ); ?>" target="_blank" rel="noopener" class="btn btn-secondary directory-social-btn">
                                            💼 LinkedIn
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Last Reviewed Note -->
                        <?php if ( ! empty( $meta['last_reviewed'] ) ) : ?>
                            <div class="directory-meta directory-meta-block">
                                📅 <?php printf( esc_html__( 'Profile listing verified and last reviewed on %s', 'weardale-together' ), esc_html( wp_date( 'F j, Y', strtotime( $meta['last_reviewed'] ) ) ) ); ?>
                            </div>
                        <?php endif; ?>

                    </div>

                    <!-- Right Column: Contact, Access & Details Card -->
                    <aside class="directory-sidebar">
                        <h2 class="directory-sidebar-title">
                            📞 <?php esc_html_e( 'Contact & Access', 'weardale-together' ); ?>
                        </h2>

                        <!-- Details Fields inside Contact Card -->
                        <div class="directory-contact-details">
                            
                            <div class="directory-contact-card">
                                <!-- Address -->
                                <?php if ( ! empty( $meta['address'] ) ) : ?>
                                    <div class="directory-detail-item">
                                        <strong class="directory-detail-label">📍 <?php esc_html_e( 'Address', 'weardale-together' ); ?></strong>
                                        <div class="directory-detail-value"><?php echo esc_html( $meta['address'] ); ?></div>
                                        
                                        <!-- Geotagging link to Google Maps if coords are provided -->
                                        <?php if ( ! empty( $meta['latitude'] ) && ! empty( $meta['longitude'] ) ) : ?>
                                            <a href="https://www.google.com/maps/search/?api=1&query=<?php echo esc_attr( $meta['latitude'] ); ?>,<?php echo esc_attr( $meta['longitude'] ); ?>" target="_blank" rel="noopener" class="directory-map-link">
                                                🗺️ <?php esc_html_e( 'View map directions', 'weardale-together' ); ?> &rarr;
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Phone -->
                                <?php if ( ! empty( $meta['phone'] ) ) : ?>
                                    <div class="directory-detail-item">
                                        <strong class="directory-detail-label">📞 <?php esc_html_e( 'Phone', 'weardale-together' ); ?></strong>
                                        <a href="tel:<?php echo esc_attr( $meta['phone'] ); ?>" class="directory-contact-link"><?php echo esc_html( $meta['phone'] ); ?></a>
                                    </div>
                                <?php endif; ?>

                                <!-- Email -->
                                <?php if ( ! empty( $meta['email'] ) ) : ?>
                                    <div class="directory-detail-item">
                                        <strong class="directory-detail-label">✉️ <?php esc_html_e( 'Email', 'weardale-together' ); ?></strong>
                                        <a href="mailto:<?php echo esc_attr( $meta['email'] ); ?>" class="directory-contact-link directory-contact-link-email"><?php echo esc_html( $meta['email'] ); ?></a>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Website & Enquiry Buttons -->
                            <div class="directory-actions-card">
                                <!-- Website Button -->
                                <?php if ( ! empty( $meta['website'] ) ) : ?>
                                    <div class="directory-action-btn-container">
                                        <a href="<?php echo esc_url( $meta['website'] ); ?>" target="_blank" rel="noopener" class="btn btn-primary directory-action-btn">
                                            🌐 Visit Website &rarr;
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <!-- Online Enquiry Button -->
                                <?php if ( get_post_meta( $post_id, '_directory_allow_enquiry', true ) === '1' ) : ?>
                                    <div class="directory-action-btn-container">
                                        <a href="<?php echo esc_url( add_query_arg( array( 'enquiry_type' => 'directory', 'enquiry_id' => $post_id ), home_url( '/contact-us/' ) ) ); ?>" class="btn btn-primary directory-action-btn" style="background-color: var(--color-forest); color: var(--color-cream); border-color: var(--color-forest);">
                                            ✉️ Enquire Online &rarr;
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <hr class="directory-divider">

                            <div class="directory-sidebar-secondary">
                                <!-- Opening Hours -->
                                <?php if ( ! empty( $meta['opening_hours'] ) ) : ?>
                                    <div class="directory-detail-item">
                                        <strong class="directory-detail-label">🕒 <?php esc_html_e( 'Opening Hours', 'weardale-together' ); ?></strong>
                                        <div class="directory-detail-value"><?php echo esc_html( $meta['opening_hours'] ); ?></div>
                                    </div>
                                <?php endif; ?>

                                <!-- Accessibility -->
                                <?php if ( ! empty( $meta['accessibility'] ) ) : ?>
                                    <div class="directory-detail-item">
                                        <strong class="directory-detail-label">♿ <?php esc_html_e( 'Accessibility', 'weardale-together' ); ?></strong>
                                        <div class="directory-detail-value"><?php echo esc_html( $meta['accessibility'] ); ?></div>
                                    </div>
                                <?php endif; ?>

                                <!-- Who It Helps -->
                                <?php if ( ! empty( $meta['who_it_helps'] ) ) : ?>
                                    <div class="directory-detail-item">
                                        <strong class="directory-detail-label">👥 <?php esc_html_e( 'Who It Helps', 'weardale-together' ); ?></strong>
                                        <div class="directory-detail-value"><?php echo esc_html( $meta['who_it_helps'] ); ?></div>
                                    </div>
                                <?php endif; ?>

                                <!-- Pricing & Booking -->
                                <?php if ( ! empty( $meta['pricing'] ) || ! empty( $meta['booking_required'] ) ) : ?>
                                    <div class="directory-detail-item">
                                        <strong class="directory-detail-label">🪙 <?php esc_html_e( 'Pricing & Booking', 'weardale-together' ); ?></strong>
                                        <div class="directory-detail-value">
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
                                    <hr class="directory-divider">
                                    <div class="directory-detail-item">
                                        <strong class="directory-detail-label">🤝 <?php esc_html_e( 'Related Programme', 'weardale-together' ); ?></strong>
                                        <div class="directory-detail-value" style="font-weight: 600;"><?php echo esc_html( $meta['related_programme'] ); ?></div>
                                    </div>
                                <?php endif; ?>

                                <!-- Related Events -->
                                <?php if ( ! empty( $meta['related_events'] ) ) : ?>
                                    <div class="directory-detail-item">
                                        <strong class="directory-detail-label">📅 <?php esc_html_e( 'Workshops & Events', 'weardale-together' ); ?></strong>
                                        <div class="directory-detail-value"><?php echo esc_html( $meta['related_events'] ); ?></div>
                                    </div>
                                <?php endif; ?>
                            </div>

                        </div>
                    </aside>

                </div>

            </div>
        </article>
    <?php 
    endwhile; 
    ?>

</main>

<?php
get_footer();
