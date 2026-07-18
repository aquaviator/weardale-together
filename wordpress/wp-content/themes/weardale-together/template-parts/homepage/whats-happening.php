<?php
/**
 * Template part for the "What's Happening" Section (Upcoming WT Events).
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */

// Query the next 3 upcoming occurrences
$upcoming_occurrences = function_exists( 'weardale_platform_query_occurrences' )
    ? weardale_platform_query_occurrences( array( 'limit' => 3, 'scope' => 'upcoming', 'include_cancelled' => false ) )
    : array();
?>

<section class="section-padding whats-happening-section">
    <div class="container">
        
        <div class="whats-happening-header">
            <div>
                <span class="badge badge-shoots mb-2"><?php esc_html_e( 'Our Activities', 'weardale-together' ); ?></span>
                <h2 class="font-display"><?php esc_html_e( 'What\'s Happening This Week', 'weardale-together' ); ?></h2>
            </div>
            <div>
                <a href="<?php echo esc_url( get_post_type_archive_link( 'weardale_event' ) ); ?>" class="btn btn-secondary">
                    <?php esc_html_e( 'See All WT Events', 'weardale-together' ); ?>
                </a>
            </div>
        </div>

        <?php if ( ! empty( $upcoming_occurrences ) ) : ?>
            <div class="grid grid-3">
                <?php
                foreach ( $upcoming_occurrences as $occ ) :
                    get_template_part( 'template-parts/events/card', null, array( 'occurrence' => $occ ) );
                endforeach;
                ?>
            </div>
        <?php else : ?>
            
            <!-- Reusable Warm empty-state when no events are scheduled -->
            <?php get_template_part( 'template-parts/events/empty-state' ); ?>

        <?php endif; ?>

        <!-- Clear signposting to Weardale Places and What's On -->
        <div class="whats-happening-signpost">
            <div class="whats-happening-signpost-text">
                <h4 class="whats-happening-signpost-title">
                    <?php esc_html_e( 'Looking for wider events across Weardale?', 'weardale-together' ); ?>
                </h4>
                <p class="whats-happening-signpost-desc">
                    <?php esc_html_e( 'For a full dale-wide directory including community maps and public calendar submissions, visit Weardale Places & What\'s On.', 'weardale-together' ); ?>
                </p>
            </div>
            <div>
                <a href="https://weardaleplaces.org.uk" target="_blank" class="btn btn-primary whats-happening-signpost-btn">
                    <?php esc_html_e( 'Visit Weardale Places &rarr;', 'weardale-together' ); ?>
                </a>
            </div>
        </div>

    </div>
</section>
