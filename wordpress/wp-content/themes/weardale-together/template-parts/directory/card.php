<?php
/**
 * Template part for rendering a responsive, highly accessible directory listing card.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$post_id = get_the_ID();

// Safe retrieval of metadata via plugin helper
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
$badge_bg = '#f1f5f9';
$badge_color = '#475569';

switch ( $type_slug ) {
    case 'business':
    case 'food-drink':
        $accent_color = '#b45309'; // Terracotta amber
        $badge_bg = '#fef3c7';
        $badge_color = '#92400e';
        break;
    case 'community-facility':
    case 'transport':
        $accent_color = '#1e3a8a'; // Dark blue
        $badge_bg = '#dbeafe';
        $badge_color = '#1e40af';
        break;
    case 'support-service':
    case 'health-wellbeing':
        $accent_color = '#047857'; // Forest emerald
        $badge_bg = '#d1fae5';
        $badge_color = '#065f46';
        break;
    case 'volunteer-opportunity':
        $accent_color = '#6b21a8'; // Purple
        $badge_bg = '#f3e8ff';
        $badge_color = '#6b21a8';
        break;
    case 'arts-culture':
        $accent_color = '#be185d'; // Deep pink
        $badge_bg = '#fce7f3';
        $badge_color = '#9d174d';
        break;
}
?>

<article id="directory-card-<?php echo esc_attr( $post_id ); ?>" class="wt-directory-card" style="
    background: var(--color-white);
    border: 2px solid var(--color-tan);
    border-radius: var(--border-radius-md);
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    position: relative;
" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 15px -3px rgba(0, 0, 0, 0.08)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.05)';" >

    <div>
        <!-- Top row: Type badge & Verified Status -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
            <?php if ( ! empty( $type_name ) ) : ?>
                <span class="badge" style="
                    background-color: <?php echo esc_attr( $badge_bg ); ?>;
                    color: <?php echo esc_attr( $badge_color ); ?>;
                    font-size: 0.75rem;
                    font-weight: 700;
                    text-transform: uppercase;
                    padding: 0.35rem 0.75rem;
                    border-radius: 9999px;
                    border: 1px solid opacity 0.1;
                ">
                    <?php echo esc_html( $type_name ); ?>
                </span>
            <?php endif; ?>

            <?php if ( ! empty( $meta['verified'] ) ) : ?>
                <span class="verified-badge" style="
                    background-color: #d1fae5;
                    color: #065f46;
                    font-size: 0.75rem;
                    font-weight: 700;
                    padding: 0.35rem 0.75rem;
                    border-radius: 9999px;
                    display: inline-flex;
                    align-items: center;
                    gap: 0.25rem;
                    border: 1px solid #10b981;
                " title="<?php esc_attr_e( 'Verified listing', 'weardale-together' ); ?>">
                    ✅ <?php esc_html_e( 'Verified', 'weardale-together' ); ?>
                </span>
            <?php endif; ?>
        </div>

        <!-- Thumbnail / Image -->
        <?php if ( has_post_thumbnail() ) : ?>
            <div class="card-thumbnail" style="margin-bottom: 1rem; overflow: hidden; border-radius: var(--border-radius-sm); height: 180px;">
                <?php the_post_thumbnail( 'medium_large', array( 'style' => 'width: 100%; height: 100%; object-fit: cover;', 'referrerpolicy' => 'no-referrer' ) ); ?>
            </div>
        <?php endif; ?>

        <!-- Name (Title) -->
        <h3 class="font-display" style="font-size: 1.5rem; margin: 0 0 0.5rem 0; color: var(--color-forest); font-weight: normal; line-height: 1.2;">
            <a href="<?php the_permalink(); ?>" style="color: inherit; text-decoration: none;" onfocus="this.style.outline='2px solid var(--color-forest)'">
                <?php the_title(); ?>
            </a>
        </h3>

        <!-- Village & Service Area -->
        <div style="font-size: 0.85rem; font-family: var(--font-mono); color: var(--text-secondary); margin-bottom: 1rem; display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;">
            <?php if ( ! empty( $village_name ) ) : ?>
                <span>📍 <?php echo esc_html( $village_name ); ?></span>
            <?php endif; ?>
            <?php if ( ! empty( $village_name ) && ! empty( $area_name ) ) : ?>
                <span>•</span>
            <?php endif; ?>
            <?php if ( ! empty( $area_name ) ) : ?>
                <span>🌍 <?php echo esc_html( $area_name ); ?></span>
            <?php endif; ?>
        </div>

        <!-- Description / Summary -->
        <p style="font-size: 0.95rem; line-height: 1.5; color: var(--color-black); margin-bottom: 1.25rem;">
            <?php echo esc_html( get_the_excerpt() ); ?>
        </p>

        <!-- Dynamic Fields Summary -->
        <div class="card-details-fields" style="font-size: 0.875rem; border-top: 1px solid var(--color-tan); padding-top: 1rem; margin-bottom: 1.5rem;">
            <?php if ( ! empty( $meta['opening_hours'] ) ) : ?>
                <div style="margin-bottom: 0.5rem; display: flex; gap: 0.5rem;">
                    <span>🕒</span>
                    <span style="color: var(--color-black);"><strong><?php esc_html_e( 'Hours:', 'weardale-together' ); ?></strong> <?php echo esc_html( wp_trim_words( $meta['opening_hours'], 8 ) ); ?></span>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $meta['accessibility'] ) ) : ?>
                <div style="margin-bottom: 0.5rem; display: flex; gap: 0.5rem;">
                    <span>♿</span>
                    <span style="color: var(--color-black);"><strong><?php esc_html_e( 'Access:', 'weardale-together' ); ?></strong> <?php echo esc_html( wp_trim_words( $meta['accessibility'], 8 ) ); ?></span>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $meta['pricing'] ) ) : ?>
                <div style="margin-bottom: 0.5rem; display: flex; gap: 0.5rem;">
                    <span>🪙</span>
                    <span style="color: var(--color-black);"><strong><?php esc_html_e( 'Pricing:', 'weardale-together' ); ?></strong> <?php echo esc_html( $meta['pricing'] ); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Contact & Action buttons -->
    <div style="display: flex; gap: 0.5rem; margin-top: auto; border-top: 1px solid var(--color-tan); padding-top: 1rem;">
        <a href="<?php the_permalink(); ?>" class="btn btn-primary" style="flex: 1; text-align: center; font-size: 0.875rem; text-decoration: none; padding: 0.5rem;" onfocus="this.style.outline='2px solid var(--color-forest)'">
            <?php esc_html_e( 'View Details', 'weardale-together' ); ?>
        </a>
        
        <?php if ( ! empty( $meta['phone'] ) ) : ?>
            <a href="tel:<?php echo esc_attr( $meta['phone'] ); ?>" class="btn btn-secondary" style="padding: 0.5rem 0.75rem; text-decoration: none;" title="<?php esc_attr_e( 'Call', 'weardale-together' ); ?>" aria-label="<?php esc_attr_e( 'Call', 'weardale-together' ); ?>">
                📞
            </a>
        <?php endif; ?>

        <?php if ( ! empty( $meta['website'] ) ) : ?>
            <a href="<?php echo esc_url( $meta['website'] ); ?>" target="_blank" rel="noopener" class="btn btn-secondary" style="padding: 0.5rem 0.75rem; text-decoration: none;" title="<?php esc_attr_e( 'Visit Website', 'weardale-together' ); ?>" aria-label="<?php esc_attr_e( 'Visit Website', 'weardale-together' ); ?>">
                🌐
            </a>
        <?php endif; ?>
    </div>
</article>
