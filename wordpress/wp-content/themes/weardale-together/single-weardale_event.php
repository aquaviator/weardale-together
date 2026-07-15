<?php
/**
 * The template for displaying a single custom Weardale Event.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */

get_header();

$post_id = get_the_ID();

// Retrieve metadata safely via plugin helper
$meta = function_exists( 'weardale_platform_get_event_meta' ) 
    ? weardale_platform_get_event_meta( $post_id ) 
    : array(
        'start_date' => get_post_meta( $post_id, '_event_date', true ),
        'time_text' => get_post_meta( $post_id, '_event_time', true ),
        'venue_name' => get_post_meta( $post_id, '_event_location', true ),
        'cost_text' => get_post_meta( $post_id, '_event_cost', true ),
        'booking_status' => 'no_booking_required',
    );

// Retrieve strand terms to establish visual consistency
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

// Check if event is past to show a helpful notice
$today = date( 'Y-m-d' );
$is_past = ( ! empty( $meta['start_date'] ) && $meta['start_date'] < $today );
?>

<main id="primary-content" class="site-main" role="main">
    
    <!-- Top breadcrumb navigation and back links -->
    <div style="background-color: var(--color-cream); border-bottom: 1px solid var(--color-tan); padding: 1rem 0;">
        <div class="container" style="display: flex; align-items: center; justify-content: space-between;">
            <a href="<?php echo esc_url( get_post_type_archive_link( 'weardale_event' ) ); ?>" class="btn btn-secondary" style="
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.4rem 1rem;
                font-size: 0.9rem;
                text-decoration: none;
                font-weight: 700;
            " aria-label="<?php esc_attr_e( 'Back to What\'s On listings archive', 'weardale-together' ); ?>">
                &larr; <?php esc_html_e( 'Back to What\'s On', 'weardale-together' ); ?>
            </a>
            
            <div style="font-size: 0.85rem; color: var(--text-secondary); font-family: var(--font-headings);">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'Home', 'weardale-together' ); ?></a>
                <span style="margin: 0 0.4rem;">&rarr;</span>
                <a href="<?php echo esc_url( get_post_type_archive_link( 'weardale_event' ) ); ?>" style="color: inherit; text-decoration: none;"><?php esc_html_e( 'What\'s On', 'weardale-together' ); ?></a>
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
                
                <!-- Past event warning flag -->
                <?php if ( $is_past ) : ?>
                    <div role="note" style="
                        background-color: #fef2f2;
                        border-left: 4px solid #ef4444;
                        padding: 1rem 1.5rem;
                        border-radius: 4px;
                        margin-bottom: 2.5rem;
                        display: flex;
                        align-items: center;
                        gap: 0.75rem;
                        color: #991b1b;
                        font-weight: 500;
                        font-size: 0.95rem;
                    ">
                        <span style="font-size: 1.25rem;">⚠️</span>
                        <div>
                            <strong><?php esc_html_e( 'Past Activity:', 'weardale-together' ); ?></strong>
                            <?php esc_html_e( 'This event took place in the past. Information shown here remains archived for community reference.', 'weardale-together' ); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <header class="entry-header" style="margin-bottom: 2.5rem;">
                    <!-- Strand taxonomy reference -->
                    <div style="margin-bottom: 0.75rem;">
                        <span class="badge <?php echo esc_attr( $badge_class ); ?>" style="font-size: 0.85rem; font-weight: bold; letter-spacing: 0.02em;">
                            <?php echo esc_html( $strand_name ); ?>
                        </span>
                    </div>

                    <h1 class="entry-title font-display" style="
                        font-size: 3rem;
                        color: var(--color-black);
                        margin: 0 0 1rem 0;
                        font-weight: normal;
                        line-height: 1.15;
                    ">
                        <?php the_title(); ?>
                    </h1>
                </header>

                <!-- Two Column Structured Responsive Layout -->
                <div style="
                    display: grid;
                    grid-template-columns: 1fr;
                    gap: 3.5rem;
                " class="md:grid-cols-12 lg:grid-layout">
                    
                    <!-- Style fallback breakpoint overrides for 2 columns using a smart CSS inline pattern -->
                    <style>
                        @media (min-width: 992px) {
                            .event-grid-wrapper {
                                display: grid !important;
                                grid-template-columns: 7fr 4fr !important;
                                gap: 4rem !important;
                            }
                        }
                    </style>

                    <div class="event-grid-wrapper" style="display: flex; flex-direction: column; gap: 2.5rem;">
                        
                        <!-- Left column: Primary narrative and features -->
                        <div class="event-primary-content" style="display: flex; flex-direction: column; gap: 2rem;">
                            
                            <!-- Large Featured Image banner -->
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="entry-thumbnail" style="
                                    border-radius: var(--border-radius-md);
                                    overflow: hidden;
                                    box-shadow: var(--shadow-sm);
                                    border: 1px solid var(--color-tan);
                                ">
                                    <?php the_post_thumbnail( 'large', array( 'style' => 'width: 100%; height: auto; max-height: 480px; object-fit: cover; display: block;' ) ); ?>
                                </div>
                            <?php endif; ?>

                            <!-- Main editor body paragraphs -->
                            <div class="entry-content rich-text" style="
                                font-size: 1.1rem;
                                line-height: 1.65;
                                color: var(--color-black);
                            }">
                                <?php 
                                the_content(); 
                                ?>
                            </div>

                            <!-- Shared activity footer card / Call to Action -->
                            <div style="
                                margin-top: 3rem;
                                background-color: var(--color-cream);
                                border-radius: var(--border-radius-md);
                                border: 1px solid var(--color-tan);
                                padding: 2.5rem;
                            ">
                                <h3 class="font-display" style="font-size: 1.6rem; color: var(--color-forest); margin: 0 0 0.75rem 0; font-weight: normal;">
                                    <?php esc_html_e( 'Want to join us?', 'weardale-together' ); ?>
                                </h3>
                                <p style="margin: 0 0 1.5rem 0; font-size: 1.025rem; color: var(--text-secondary); line-height: 1.5;">
                                    <?php esc_html_e( 'Our events are organized by local volunteers and non-technical staff to build grassroots connections and support resident well-being. All are warmly welcome.', 'weardale-together' ); ?>
                                </p>
                                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                                    <?php if ( ! empty( $meta['booking_url'] ) && in_array( $meta['booking_status'], array( 'booking_required', 'booking_recommended' ) ) ) : ?>
                                        <a href="<?php echo esc_url( $meta['booking_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                                            <?php esc_html_e( 'Book Tickets Now', 'weardale-together' ); ?>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>" class="btn btn-secondary">
                                        <?php esc_html_e( 'Enquire About This Event', 'weardale-together' ); ?>
                                    </a>
                                </div>
                            </div>

                        </div>

                        <!-- Right column: Structural sidebar -->
                        <aside class="event-sidebar" role="complementary" style="height: fit-content; position: sticky; top: 2rem;">
                            <?php 
                            get_template_part( 'template-parts/events/meta', null, array( 'post_id' => $post_id ) ); 
                            ?>
                        </aside>

                    </div>
                </div>

            </div>
        </article>
    <?php 
    endwhile; 
    ?>

</main>

<?php
get_footer();
