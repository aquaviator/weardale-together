<?php
/**
 * Template part for rendering a unified, responsive event card.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$post_id = get_the_ID();

// Retrieve all metadata safely via plugin helper
$meta = function_exists( 'weardale_platform_get_event_meta' ) 
    ? weardale_platform_get_event_meta( $post_id ) 
    : array(
        'start_date' => get_post_meta( $post_id, '_event_date', true ),
        'time_text' => get_post_meta( $post_id, '_event_time', true ),
        'venue_name' => get_post_meta( $post_id, '_event_location', true ),
        'cost_text' => get_post_meta( $post_id, '_event_cost', true ),
        'booking_status' => 'no_booking_required',
    );

// Map the strand taxonomy to visual design accents
$strands = get_the_terms( $post_id, 'strand' );
$accent_color = 'var(--color-forest)';
$badge_class = 'badge-shoots';
$strand_name = 'WT Event';

if ( ! empty( $strands ) && ! is_wp_error( $strands ) ) {
    $strand = reset( $strands );
    $strand_name = $strand->name;
    
    switch ( $strand->slug ) {
        case 'cafe':
        case 'root-branch-cafe':
            $accent_color = '#d4c5a9'; // Tan gold
            $badge_class = 'badge-cafe';
            break;
        case 'young-people':
            $accent_color = '#6b8f5e'; // Sage
            $badge_class = 'badge-young';
            break;
        case 'creative-arts':
            $accent_color = '#b45309'; // Terracotta amber
            $badge_class = 'badge-creative';
            break;
        case 'roots-shoots':
            $accent_color = '#3b5c3a'; // Forest
            $badge_class = 'badge-shoots';
            break;
    }
}

// Date formatting
$formatted_date = ! empty( $meta['start_date'] ) 
    ? date( 'l, F j, Y', strtotime( $meta['start_date'] ) ) 
    : __( 'Date to be announced', 'weardale-together' );

if ( ! empty( $meta['end_date'] ) && $meta['end_date'] !== $meta['start_date'] ) {
    $formatted_date .= ' - ' . date( 'F j, Y', strtotime( $meta['end_date'] ) );
}
?>

<article class="card event-card" style="
    display: flex;
    flex-direction: column;
    height: 100%;
    border-top: 4px solid <?php echo esc_attr( $accent_color ); ?>;
    border-radius: var(--border-radius-md);
    background-color: var(--color-white);
    box-shadow: var(--shadow-sm);
    transition: var(--transition-smooth);
    overflow: hidden;
    position: relative;
" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='var(--shadow-md)';" onmouseout="this.style.transform='none'; this.style.boxShadow='var(--shadow-sm)';">

    <!-- Card Image Header -->
    <div class="event-card-image" style="position: relative; height: 180px; overflow: hidden; background-color: var(--color-cream);">
        <?php if ( has_post_thumbnail() ) : ?>
            <a href="<?php the_permalink(); ?>" style="display: block; width: 100%; height: 100%;">
                <?php the_post_thumbnail( 'medium_large', array( 'style' => 'width: 100%; height: 100%; object-fit: cover; transition: transform var(--transition-smooth);', 'class' => 'card-img' ) ); ?>
            </a>
        <?php else : ?>
            <!-- Warm stylized fallback banner -->
            <a href="<?php the_permalink(); ?>" style="display: flex; width: 100%; height: 100%; align-items: center; justify-content: center; text-decoration: none; background: linear-gradient(135deg, rgba(59, 92, 58, 0.05) 0%, rgba(107, 143, 94, 0.1) 100%);">
                <span style="font-size: 3rem; opacity: 0.25;">🌿</span>
            </a>
        <?php endif; ?>
        
        <!-- Floating Strand Badge -->
        <div style="position: absolute; top: 1rem; left: 1rem; z-index: 5;">
            <span class="badge <?php echo esc_attr( $badge_class ); ?>" style="box-shadow: 0 2px 4px rgba(0,0,0,0.1); font-weight: bold;">
                <?php echo esc_html( $strand_name ); ?>
            </span>
        </div>

        <!-- Floating Booking Status Badge -->
        <div style="position: absolute; bottom: 1rem; right: 1rem; z-index: 5;">
            <?php get_template_part( 'template-parts/events/status', null, array( 'status' => $meta['booking_status'], 'size' => 'small' ) ); ?>
        </div>
    </div>

    <!-- Card Content -->
    <div class="event-card-body" style="padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1;">
        <h3 class="card-title" style="font-size: 1.35rem; font-family: var(--font-headings); font-weight: normal; line-height: 1.3; margin: 0 0 1rem 0; min-height: 3rem;">
            <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: var(--color-black); transition: var(--transition-smooth);" onmouseover="this.style.color='var(--color-forest)';" onmouseout="this.style.color='var(--color-black)';">
                <?php the_title(); ?>
            </a>
        </h3>

        <!-- Event excerpt -->
        <?php if ( has_excerpt() ) : ?>
            <p style="font-size: 0.925rem; line-height: 1.5; color: var(--text-secondary); margin: 0 0 1.25rem 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                <?php echo esc_html( get_the_excerpt() ); ?>
            </p>
        <?php endif; ?>

        <!-- Event Meta Parameters -->
        <div class="event-card-meta-list" style="
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
            font-size: 0.925rem;
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
            margin-top: auto;
            border-top: 1px solid var(--color-cream);
            padding-top: 1rem;
        ">
            <!-- Start Date -->
            <div style="display: flex; align-items: flex-start; gap: 0.6rem;">
                <span aria-hidden="true" style="font-size: 1.1rem; flex-shrink: 0; display: inline-block; width: 1.2rem; text-align: center;">📅</span>
                <div>
                    <strong><?php echo esc_html( $formatted_date ); ?></strong>
                    <?php if ( $meta['all_day'] ) : ?>
                        <span style="font-style: italic; font-size: 0.85rem; color: var(--color-forest); margin-left: 0.25rem; font-weight: 600;">(<?php esc_html_e( 'All Day', 'weardale-together' ); ?>)</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Time Text -->
            <?php if ( ! empty( $meta['time_text'] ) && ! $meta['all_day'] ) : ?>
                <div style="display: flex; align-items: flex-start; gap: 0.6rem;">
                    <span aria-hidden="true" style="font-size: 1.1rem; flex-shrink: 0; display: inline-block; width: 1.2rem; text-align: center;">🕒</span>
                    <span><?php echo esc_html( $meta['time_text'] ); ?></span>
                </div>
            <?php endif; ?>

            <!-- Venue Name / Address -->
            <?php if ( ! empty( $meta['venue_name'] ) ) : ?>
                <div style="display: flex; align-items: flex-start; gap: 0.6rem;">
                    <span aria-hidden="true" style="font-size: 1.1rem; flex-shrink: 0; display: inline-block; width: 1.2rem; text-align: center;">📍</span>
                    <span style="display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;"><?php echo esc_html( $meta['venue_name'] ); ?></span>
                </div>
            <?php endif; ?>

            <!-- Cost Entry Fee -->
            <?php if ( ! empty( $meta['cost_text'] ) ) : ?>
                <div style="display: flex; align-items: flex-start; gap: 0.6rem;">
                    <span aria-hidden="true" style="font-size: 1.1rem; flex-shrink: 0; display: inline-block; width: 1.2rem; text-align: center;">🪙</span>
                    <span><?php echo esc_html( $meta['cost_text'] ); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <a href="<?php the_permalink(); ?>" class="btn btn-secondary" style="width: 100%; text-align: center; justify-content: center; font-size: 0.95rem; padding: 0.6rem 1rem;">
            <?php esc_html_e( 'View Details', 'weardale-together' ); ?>
        </a>
    </div>

</article>
