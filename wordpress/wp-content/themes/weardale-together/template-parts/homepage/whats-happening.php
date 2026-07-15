<?php
/**
 * Template part for the "What's Happening" Section (Upcoming WT Events).
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */

// Custom WP_Query for WT's own upcoming events using platform query helper
$events_query = function_exists( 'weardale_platform_get_events' ) 
    ? weardale_platform_get_events( array( 'posts_per_page' => 3, 'scope' => 'upcoming' ) )
    : new WP_Query( array(
        'post_type'      => 'weardale_event',
        'posts_per_page' => 3,
        'meta_key'       => '_event_date',
        'orderby'        => 'meta_value',
        'order'          => 'ASC',
        'post_status'    => 'publish',
    ) );
?>

<section class="section-padding" style="background-color: var(--color-white); border-bottom: 1px solid var(--color-tan);">
    <div class="container">
        
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 3.5rem; flex-wrap: wrap; gap: 1.5rem;">
            <div>
                <span class="badge badge-shoots" style="margin-bottom: 0.5rem;"><?php esc_html_e( 'Our Activities', 'weardale-together' ); ?></span>
                <h2 class="font-display" style="font-size: 2.5rem; margin-bottom: 0;"><?php esc_html_e( 'What\'s Happening This Week', 'weardale-together' ); ?></h2>
            </div>
            <div>
                <a href="<?php echo esc_url( get_post_type_archive_link( 'weardale_event' ) ); ?>" class="btn btn-secondary">
                    <?php esc_html_e( 'See All WT Events', 'weardale-together' ); ?>
                </a>
            </div>
        </div>

        <?php if ( $events_query->have_posts() ) : ?>
            <div class="grid grid-3">
                <?php
                while ( $events_query->have_posts() ) :
                    $events_query->the_post();
                    get_template_part( 'template-parts/events/card' );
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        <?php else : ?>
            
            <!-- Reusable Warm empty-state when no events are scheduled -->
            <?php get_template_part( 'template-parts/events/empty-state' ); ?>

        <?php endif; ?>

        <!-- Clear signposting to Weardale Places and What's On -->
        <div style="margin-top: 4rem; padding: 2rem; background-color: rgba(107, 143, 94, 0.08); border-radius: var(--border-radius-md); border: 1px solid rgba(107, 143, 94, 0.15); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem;">
            <div style="max-width: 600px;">
                <h4 style="margin: 0 0 0.5rem 0; font-family: var(--font-body); font-weight: 700; color: var(--color-forest); font-size: 1.15rem;">
                    <?php esc_html_e( 'Looking for wider events across Weardale?', 'weardale-together' ); ?>
                </h4>
                <p style="margin: 0; font-size: 0.95rem; color: var(--text-secondary); line-height: 1.4;">
                    <?php esc_html_e( 'For a full dale-wide directory including community maps and public calendar submissions, visit Weardale Places & What\'s On.', 'weardale-together' ); ?>
                </p>
            </div>
            <div>
                <a href="https://weardaleplaces.org.uk" target="_blank" class="btn btn-primary" style="padding: 0.6rem 1.25rem; font-size: 0.95rem;">
                    <?php esc_html_e( 'Visit Weardale Places &rarr;', 'weardale-together' ); ?>
                </a>
            </div>
        </div>

    </div>
</section>
