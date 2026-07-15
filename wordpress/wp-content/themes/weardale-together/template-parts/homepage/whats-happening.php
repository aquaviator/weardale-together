<?php
/**
 * Template part for the "What's Happening" Section (Upcoming WT Events).
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */

// Custom WP_Query for WT's own upcoming events
$args = array(
    'post_type'      => 'weardale_event',
    'posts_per_page' => 3,
    'meta_key'       => '_event_date',
    'orderby'        => 'meta_value',
    'order'          => 'ASC',
);

$events_query = new WP_Query( $args );
?>

<section class="section-padding" style="background-color: var(--color-white); border-bottom: 1px solid var(--color-tan);">
    <div class="container">
        
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 3.5rem; flex-wrap: wrap; gap: 1.5rem;">
            <div>
                <span class="badge badge-shoots" style="margin-bottom: 0.5rem;"><?php esc_html_e( 'Our Activities', 'weardale-together' ); ?></span>
                <h2 class="font-display" style="font-size: 2.5rem; margin-bottom: 0;"><?php esc_html_e( 'What\'s Happening This Week', 'weardale-together' ); ?></h2>
            </div>
            <div>
                <a href="<?php echo esc_url( home_url( '/events/' ) ); ?>" class="btn btn-secondary">
                    <?php esc_html_e( 'See All WT Events', 'weardale-together' ); ?>
                </a>
            </div>
        </div>

        <?php if ( $events_query->have_posts() ) : ?>
            <div class="grid grid-3">
                <?php
                while ( $events_query->have_posts() ) :
                    $events_query->the_post();
                    
                    // Retrieve metadata registered by the plugin
                    $event_date    = get_post_meta( get_the_ID(), '_event_date', true );
                    $event_time    = get_post_meta( get_the_ID(), '_event_time', true );
                    $event_location = get_post_meta( get_the_ID(), '_event_location', true );
                    $event_cost     = get_post_meta( get_the_ID(), '_event_cost', true );
                    
                    // Format date nicely
                    $formatted_date = ! empty( $event_date ) ? date( 'l, F j, Y', strtotime( $event_date ) ) : 'Date to be announced';
                    ?>
                    
                    <article class="card" style="border-top: 4px solid var(--color-forest);">
                        <div style="margin-bottom: 1rem;">
                            <span class="badge badge-creative" style="font-size: 0.75rem;"><?php esc_html_e( 'WT Event', 'weardale-together' ); ?></span>
                        </div>

                        <h3 class="card-title" style="font-size: 1.4rem; margin-bottom: 1rem; min-height: 3.5rem;">
                            <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: inherit;">
                                <?php the_title(); ?>
                            </a>
                        </h3>

                        <!-- Event meta attributes -->
                        <div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.95rem; color: var(--text-secondary); margin-bottom: 1.5rem; flex-grow: 1;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span>📅</span>
                                <strong><?php echo esc_html( $formatted_date ); ?></strong>
                            </div>
                            <?php if ( ! empty( $event_time ) ) : ?>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span>🕒</span>
                                    <span><?php echo esc_html( $event_time ); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ( ! empty( $event_location ) ) : ?>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span>📍</span>
                                    <span><?php echo esc_html( $event_location ); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ( ! empty( $event_cost ) ) : ?>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span>🪙</span>
                                    <span><?php echo esc_html( $event_cost ); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <a href="<?php the_permalink(); ?>" class="btn btn-secondary" style="align-self: flex-start; padding: 0.5rem 1rem; font-size: 0.9rem;">
                            <?php esc_html_e( 'View Details', 'weardale-together' ); ?>
                        </a>
                    </article>

                    <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        <?php else : ?>
            
            <!-- Warm placeholder when no events are scheduled -->
            <div style="background-color: var(--color-cream); border: 1px dashed var(--color-tan); padding: 3rem; text-align: center; border-radius: var(--border-radius-md);">
                <p style="font-size: 1.15rem; color: var(--text-secondary); margin-bottom: 1.5rem;">
                    <?php esc_html_e( 'We are currently planning our next block of creative workshops, kids camps, and café sessions. Check back shortly!', 'weardale-together' ); ?>
                </p>
                <a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>" class="btn btn-secondary">
                    <?php esc_html_e( 'Enquire About Upcoming Sessions', 'weardale-together' ); ?>
                </a>
            </div>

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
