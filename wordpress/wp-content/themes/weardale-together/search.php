<?php
/**
 * The template for displaying search results pages.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<main id="primary-content" class="site-main" role="main" style="background-color: var(--bg-primary);">
    
    <!-- Editorial Search Header -->
    <header class="journal-header" style="background-color: var(--color-cream); border-bottom: 1px solid var(--color-tan); padding: 5rem 0 4rem 0;">
        <div class="container" style="text-align: center;">
            <span class="badge badge-creative" style="margin-bottom: 1rem; padding: 0.4rem 1.25rem; font-size: 0.9rem; font-weight: 700; letter-spacing: 0.05em; border-radius: var(--border-radius-pill);">
                <?php esc_html_e( 'Search Results', 'weardale-together' ); ?>
            </span>
            <h1 class="font-display" style="font-size: 3rem; color: var(--color-forest); margin-top: 0; margin-bottom: 1rem; line-height: 1.1; font-weight: normal;">
                <?php
                /* translators: %s: search query. */
                printf( esc_html__( 'Results for: %s', 'weardale-together' ), '<span style="font-style: italic; color: var(--text-primary);">' . esc_html( get_search_query() ) . '</span>' );
                ?>
            </h1>
            <p style="font-family: var(--font-body); font-size: 1.2rem; color: var(--text-light); max-width: 600px; margin: 0 auto;">
                <?php esc_html_e( 'Discover articles, projects, and activities connecting residents throughout the North Pennines.', 'weardale-together' ); ?>
            </p>
            <div style="width: 80px; height: 3px; background-color: var(--color-tan); margin: 2rem auto 0 auto;"></div>
        </div>
    </header>

    <!-- Search & Filters Bar Section -->
    <section class="journal-filters-section" style="padding: 2.5rem 0; border-bottom: 1px solid var(--color-tan); background-color: var(--bg-primary);">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 2rem; flex-wrap: wrap;" class="filters-row">
                
                <!-- Category Filter Pills -->
                <div class="category-pills-container" style="display: flex; gap: 0.5rem; flex-wrap: wrap; flex-grow: 1;" aria-label="<?php esc_attr_e( 'Filter stories by category', 'weardale-together' ); ?>">
                    <a href="<?php echo esc_url( home_url( '/news-blog/' ) ); ?>" class="badge" style="padding: 0.5rem 1rem; text-decoration: none; font-size: 0.9rem; border-radius: var(--border-radius-pill); font-weight: 500; background-color: var(--color-cream); color: var(--text-primary); border: 1px solid var(--color-tan);">
                        <?php esc_html_e( 'All Stories', 'weardale-together' ); ?>
                    </a>
                    <?php
                    $cats = get_categories( array(
                        'hide_empty' => true,
                        'exclude'    => array( 1 ), // Exclude Uncategorized
                    ) );
                    if ( ! empty( $cats ) ) {
                        foreach ( $cats as $c ) {
                            echo '<a href="' . esc_url( get_category_link( $c->term_id ) ) . '" class="badge" style="padding: 0.5rem 1rem; text-decoration: none; font-size: 0.9rem; border-radius: var(--border-radius-pill); font-weight: 500; background-color: var(--color-cream); color: var(--text-primary); border: 1px solid var(--color-tan); transition: all 0.2s ease;">' . esc_html( $c->name ) . '</a>';
                        }
                    }
                    ?>
                </div>

                <!-- Live Keyword Search Form -->
                <div class="journal-search-container" style="min-width: 300px;">
                    <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="display: flex; gap: 0.5rem;">
                        <label style="width: 100%; position: relative;">
                            <span class="screen-reader-text" style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); border: 0;"><?php esc_html_e( 'Search stories:', 'weardale-together' ); ?></span>
                            <input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search stories…', 'weardale-together' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" style="width: 100%; padding: 0.6rem 1rem; border: 1px solid var(--color-tan); border-radius: var(--border-radius-sm); font-size: 0.95rem; background-color: var(--color-white); outline: none;" id="journal-search-input">
                        </label>
                        <input type="submit" class="search-submit btn btn-secondary" value="<?php esc_attr_e( 'Search', 'weardale-together' ); ?>" style="padding: 0.6rem 1.25rem; font-size: 0.95rem; cursor: pointer;">
                    </form>
                </div>

            </div>
        </div>
    </section>

    <!-- Main Grid Content -->
    <section class="journal-grid-section" style="padding: 4rem 0;">
        <div class="container">
            
            <?php if ( have_posts() ) : ?>
                <div class="grid grid-3">
                    <?php
                    while ( have_posts() ) :
                        the_post();
                        ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class( 'card' ); ?> style="display: flex; flex-direction: column; background-color: var(--color-white); border: 1px solid var(--color-tan); border-radius: var(--border-radius-md); overflow: hidden; box-shadow: 0 4px 12px rgba(59,92,58,0.03); transition: transform 0.2s ease, box-shadow 0.2s ease;">
                            
                            <!-- Story Image -->
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="post-thumbnail" style="border-bottom: 1px solid var(--color-tan); overflow: hidden; aspect-ratio: 16/10;">
                                    <?php the_post_thumbnail( 'medium_large', array( 'style' => 'width:100%; height:100%; object-fit:cover; display:block;' ) ); ?>
                                </div>
                            <?php else : ?>
                                <div style="aspect-ratio: 16/10; background-color: var(--color-cream); border-bottom: 1px solid var(--color-tan); display: flex; align-items: center; justify-content: center; color: var(--text-light);">
                                    <span style="font-family: var(--font-mono); font-size: 0.85rem;"><?php esc_html_e( 'Weardale Together', 'weardale-together' ); ?></span>
                                </div>
                            <?php endif; ?>

                            <!-- Body Area -->
                            <div style="padding: 2rem; flex-grow: 1; display: flex; flex-direction: column;">
                                
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                                    <!-- Date -->
                                    <span style="font-family: var(--font-mono); font-size: 0.8rem; color: var(--text-light);">
                                        <?php echo get_the_date(); ?>
                                    </span>

                                    <!-- Category badge -->
                                    <?php
                                    $item_cats = get_the_category();
                                    if ( ! empty( $item_cats ) ) {
                                        $primary_cat = $item_cats[0];
                                        foreach ( $item_cats as $ic ) {
                                            if ( $ic->term_id !== 1 ) {
                                                $primary_cat = $ic;
                                                break;
                                            }
                                        }
                                        echo '<span class="badge badge-creative" style="font-size: 0.75rem; background-color: var(--color-cream); color: var(--color-forest); border: 1px solid var(--color-tan); padding: 0.2rem 0.5rem;">' . esc_html( $primary_cat->name ) . '</span>';
                                    }
                                    ?>
                                </div>

                                <h3 class="card-title font-display" style="font-size: 1.4rem; line-height: 1.25; margin-top: 0; margin-bottom: 1rem; font-weight: normal; min-height: 3.2rem;">
                                    <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: var(--color-forest);">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>

                                <div style="font-size: 0.95rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.5rem; flex-grow: 1;">
                                    <?php the_excerpt(); ?>
                                </div>

                                <a href="<?php the_permalink(); ?>" class="btn btn-secondary" style="align-self: flex-start; padding: 0.5rem 1rem; font-size: 0.9rem;">
                                    <?php esc_html_e( 'Read Story &rarr;', 'weardale-together' ); ?>
                                </a>

                            </div>
                        </article>
                        <?php
                    endwhile;
                    ?>
                </div>

                <!-- Custom Pagination -->
                <div class="pagination" style="margin-top: 5rem; display: flex; justify-content: center; gap: 0.5rem;" aria-label="<?php esc_attr_e( 'Search pagination', 'weardale-together' ); ?>">
                    <?php
                    echo paginate_links( array(
                        'prev_text' => '&larr; Previous',
                        'next_text' => 'Next &rarr;',
                    ) );
                    ?>
                </div>

            <?php else : ?>
                
                <!-- Cozy Empty State Notice -->
                <div class="empty-state-notice" style="background-color: var(--color-white); border: 1px dashed var(--color-tan); padding: 5rem 3rem; text-align: center; border-radius: var(--border-radius-md); max-width: 600px; margin: 0 auto;">
                    <svg width="48" height="48" fill="none" stroke="var(--color-tan)" stroke-width="1.5" viewBox="0 0 24 24" style="margin: 0 auto 1.5rem; display: block;" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <h3 class="font-display" style="font-size: 1.75rem; color: var(--color-forest); margin-top: 0; margin-bottom: 1rem; font-weight: normal;"><?php esc_html_e( 'No Results Found', 'weardale-together' ); ?></h3>
                    <p style="font-size: 1.1rem; color: var(--text-secondary); line-height: 1.6; margin-bottom: 2rem;">
                        <?php esc_html_e( 'We couldn’t find any articles matching your search. Please check your spelling or try standard topics like "cafe", "school", "railway", or "crafts".', 'weardale-together' ); ?>
                    </p>
                    <a href="<?php echo esc_url( home_url( '/news-blog/' ) ); ?>" class="btn btn-secondary" style="padding: 0.6rem 1.5rem;">
                        <?php esc_html_e( 'Return to Journal', 'weardale-together' ); ?>
                    </a>
                </div>

            <?php endif; ?>

        </div>
    </section>

</main>

<?php
get_footer();
