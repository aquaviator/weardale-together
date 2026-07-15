<?php
/**
 * The template for displaying Weardale Events Archive ("What's On").
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */

get_header();

// Determine if filtering by a strand taxonomy term
$current_strand = get_query_var( 'strand' );
$strand_term = null;
if ( ! empty( $current_strand ) ) {
    $strand_term = get_term_by( 'slug', $current_strand, 'strand' );
}

// Prepare query args
$upcoming_args = array( 'scope' => 'upcoming', 'posts_per_page' => -1 );
$past_args     = array( 'scope' => 'past', 'posts_per_page' => 12 );

if ( $strand_term ) {
    $upcoming_args['strand'] = $strand_term->slug;
    $past_args['strand']     = $strand_term->slug;
}

// Fetch events using our platform's reusable queries
$upcoming_query = function_exists( 'weardale_platform_get_events' ) 
    ? weardale_platform_get_events( $upcoming_args ) 
    : new WP_Query( array( 'post_type' => 'weardale_event', 'posts_per_page' => -1 ) );

$past_query = function_exists( 'weardale_platform_get_events' ) 
    ? weardale_platform_get_events( $past_args ) 
    : null;

$all_strands = get_terms( array( 'taxonomy' => 'strand', 'hide_empty' => false ) );
?>

<main id="primary-content" class="site-main" role="main">
    
    <!-- Hero Header Banner -->
    <header class="archive-header" style="
        background: linear-gradient(135deg, var(--color-forest) 0%, var(--color-sage) 100%);
        color: var(--color-cream);
        padding: 5rem 0;
        text-align: center;
        border-bottom: 4px solid var(--color-tan);
    ">
        <div class="container">
            <span class="badge" style="
                background-color: var(--color-cream);
                color: var(--color-forest);
                margin-bottom: 1rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            ">
                <?php esc_html_e( 'Weardale Together CIC', 'weardale-together' ); ?>
            </span>
            
            <h1 class="font-display" style="
                font-size: 3.5rem;
                margin: 0 0 1rem 0;
                color: var(--color-cream);
                line-height: 1.1;
                font-weight: normal;
            ">
                <?php 
                if ( $strand_term ) {
                    /* translators: %s: strand taxonomy term name */
                    printf( esc_html__( 'What\'s On: %s', 'weardale-together' ), esc_html( $strand_term->name ) );
                } else {
                    esc_html_e( 'What\'s On in Weardale', 'weardale-together' );
                }
                ?>
            </h1>
            
            <p style="
                font-size: 1.2rem;
                max-width: 650px;
                margin: 0 auto;
                opacity: 0.95;
                line-height: 1.5;
            ">
                <?php 
                if ( $strand_term && ! empty( $strand_term->description ) ) {
                    echo esc_html( $strand_term->description );
                } else {
                    esc_html_e( 'Explore our upcoming grassroots workshops, group activities, children\'s events, and community meals across Weardale.', 'weardale-together' );
                }
                ?>
            </p>
        </div>
    </header>

    <!-- Navigation Strand Filters -->
    <?php if ( ! empty( $all_strands ) && ! is_wp_error( $all_strands ) ) : ?>
        <nav class="strand-filters-nav" aria-label="<?php esc_attr_e( 'Filter by Strand', 'weardale-together' ); ?>" style="
            background-color: var(--color-cream);
            border-bottom: 1px solid var(--color-tan);
            padding: 1.25rem 0;
        ">
            <div class="container" style="display: flex; gap: 0.75rem; align-items: center; justify-content: center; flex-wrap: wrap;">
                <span style="font-size: 0.9rem; font-weight: 700; color: var(--color-black); text-transform: uppercase; letter-spacing: 0.05em; margin-right: 0.5rem;">
                    <?php esc_html_e( 'Filter by Strand:', 'weardale-together' ); ?>
                </span>
                
                <!-- All link -->
                <a href="<?php echo esc_url( get_post_type_archive_link( 'weardale_event' ) ); ?>" class="btn <?php echo ( ! $strand_term ) ? 'btn-primary' : 'btn-secondary'; ?>" style="
                    padding: 0.4rem 1rem;
                    font-size: 0.875rem;
                    text-decoration: none;
                ">
                    <?php esc_html_e( 'All Strands', 'weardale-together' ); ?>
                </a>

                <?php foreach ( $all_strands as $term ) : ?>
                    <?php 
                    $term_link = get_term_link( $term );
                    if ( is_wp_error( $term_link ) ) {
                        continue;
                    }
                    $is_active = ( $strand_term && $strand_term->term_id === $term->term_id );
                    ?>
                    <a href="<?php echo esc_url( $term_link ); ?>" class="btn <?php echo $is_active ? 'btn-primary' : 'btn-secondary'; ?>" style="
                        padding: 0.4rem 1rem;
                        font-size: 0.875rem;
                        text-decoration: none;
                    ">
                        <?php echo esc_html( $term->name ); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </nav>
    <?php endif; ?>

    <!-- Events Content Sections -->
    <div class="section-padding" style="background-color: var(--color-white);">
        <div class="container">

            <!-- SECTION 1: UPCOMING EVENTS -->
            <section id="upcoming-events" aria-labelledby="upcoming-events-title" style="margin-bottom: 5rem;">
                <div style="
                    border-bottom: 2px solid var(--color-tan);
                    padding-bottom: 0.75rem;
                    margin-bottom: 2.5rem;
                    display: flex;
                    align-items: baseline;
                    justify-content: space-between;
                    flex-wrap: wrap;
                    gap: 1rem;
                ">
                    <h2 id="upcoming-events-title" class="font-display" style="font-size: 2.25rem; color: var(--color-forest); margin: 0; font-weight: normal;">
                        🌿 <?php esc_html_e( 'Upcoming Activities', 'weardale-together' ); ?>
                    </h2>
                    <span style="font-size: 1.05rem; font-family: var(--font-mono); color: var(--text-secondary); font-weight: 500;">
                        <?php 
                        $upcoming_count = $upcoming_query->found_posts;
                        /* translators: %d: number of upcoming events */
                        printf( _n( '%d upcoming activity found', '%d upcoming activities found', $upcoming_count, 'weardale-together' ), $upcoming_count );
                        ?>
                    </span>
                </div>

                <?php if ( $upcoming_query->have_posts() ) : ?>
                    <div class="grid grid-3">
                        <?php 
                        while ( $upcoming_query->have_posts() ) : 
                            $upcoming_query->the_post();
                            get_template_part( 'template-parts/events/card' );
                        endwhile; 
                        wp_reset_postdata();
                        ?>
                    </div>
                <?php else : ?>
                    <?php 
                    get_template_part( 'template-parts/events/empty-state', null, array(
                        'message' => $strand_term 
                            /* translators: %s: strand term name */
                            ? sprintf( __( 'We do not have any upcoming activities scheduled for %s at the moment. Please check back shortly!', 'weardale-together' ), $strand_term->name )
                            : __( 'We do not have any upcoming activities scheduled at the moment. Please check back shortly!', 'weardale-together' )
                    ) ); 
                    ?>
                <?php endif; ?>
            </section>

            <!-- SECTION 2: PAST EVENTS (HISTORIC ARCHIVE) -->
            <?php if ( $past_query && $past_query->have_posts() ) : ?>
                <section id="past-events" aria-labelledby="past-events-title" style="
                    border-top: 1px solid var(--color-tan);
                    padding-top: 5rem;
                ">
                    <div style="
                        border-bottom: 2px solid var(--color-tan);
                        padding-bottom: 0.75rem;
                        margin-bottom: 2.5rem;
                        display: flex;
                        align-items: baseline;
                        justify-content: space-between;
                        flex-wrap: wrap;
                        gap: 1rem;
                    ">
                        <h2 id="past-events-title" class="font-display" style="font-size: 2rem; color: var(--text-secondary); margin: 0; font-weight: normal;">
                            📜 <?php esc_html_e( 'Past & Recent Activities', 'weardale-together' ); ?>
                        </h2>
                        <span style="font-size: 0.95rem; font-family: var(--font-body); color: var(--text-secondary);">
                            <?php esc_html_e( 'A record of our community achievements', 'weardale-together' ); ?>
                        </span>
                    </div>

                    <div class="grid grid-3" style="opacity: 0.85;">
                        <?php 
                        while ( $past_query->have_posts() ) : 
                            $past_query->the_post();
                            get_template_part( 'template-parts/events/card' );
                        endwhile; 
                        wp_reset_postdata();
                        ?>
                    </div>
                </section>
            <?php endif; ?>

        </div>
    </div>

</main>

<?php
get_footer();
