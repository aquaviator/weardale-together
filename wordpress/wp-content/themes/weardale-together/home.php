<?php
/**
 * The template for displaying the News & Stories (Our Journal) index.
 *
 * Designed as a rich, warm, editorial community storytelling layout.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

// Fetch the Featured Story (only display on page 1 and when no keyword search is active)
$is_paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
$search_query = get_search_query();

$featured_id = 0;
if ( 1 === $is_paged && empty( $search_query ) ) {
    $featured_query = new WP_Query( array(
        'post_type'      => 'post',
        'posts_per_page' => 1,
        'meta_key'       => '_weardale_featured_post',
        'meta_value'     => '1',
        'post_status'    => 'publish',
    ) );

    if ( $featured_query->have_posts() ) {
        $featured_query->the_post();
        $featured_id = get_the_ID();
    }
    wp_reset_postdata();
}
?>

<main id="primary-content" class="site-main" role="main" style="background-color: var(--bg-primary);">
    
    <!-- Editorial Journal Header -->
    <header class="journal-header" style="background-color: var(--color-cream); border-bottom: 1px solid var(--color-tan); padding: 5rem 0 4rem 0;">
        <div class="container" style="text-align: center;">
            <span class="badge badge-creative" style="margin-bottom: 1rem; padding: 0.4rem 1.25rem; font-size: 0.9rem; font-weight: 700; letter-spacing: 0.05em; border-radius: var(--border-radius-pill);">
                <?php esc_html_e( 'Our Journal', 'weardale-together' ); ?>
            </span>
            <h1 class="font-display" style="font-size: 3.5rem; color: var(--color-forest); margin-top: 0; margin-bottom: 1rem; line-height: 1.1; font-weight: normal;">
                <?php esc_html_e( 'Stories From the Heart of the Dale', 'weardale-together' ); ?>
            </h1>
            <p style="font-family: var(--font-body); font-size: 1.25rem; color: var(--text-light); max-width: 650px; margin: 0 auto;">
                <?php esc_html_e( 'Sharing the slow rhythms, quiet victories, and creative expressions of community life throughout Weardale.', 'weardale-together' ); ?>
            </p>
            <div style="width: 80px; height: 3px; background-color: var(--color-tan); margin: 2rem auto 0 auto;"></div>
        </div>
    </header>

    <!-- Featured Story Spotlight (Only on Page 1) -->
    <?php if ( $featured_id ) : ?>
        <?php
        // Re-query/setup the post details to render properly
        $post = get_post( $featured_id );
        setup_postdata( $post );
        ?>
        <section class="featured-story-section" style="padding: 4rem 0; border-bottom: 1px solid var(--color-tan); background-color: var(--color-white);">
            <div class="container">
                <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 4rem; align-items: center;" class="featured-grid">
                    
                    <!-- Left: Featured Image with Polaroid Feel -->
                    <div class="featured-image-container" style="position: relative;">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div style="border: 1px solid var(--color-tan); padding: 12px; background: var(--color-cream); border-radius: var(--border-radius-md); box-shadow: 0 10px 25px rgba(59,92,58,0.06);">
                                <?php the_post_thumbnail( 'large', array( 'style' => 'width:100%; height:auto; display:block; border-radius: var(--border-radius-sm); border: 1px solid var(--color-tan);' ) ); ?>
                            </div>
                        <?php else : ?>
                            <div style="aspect-ratio: 16/10; background-color: var(--color-cream); border: 2px dashed var(--color-tan); border-radius: var(--border-radius-md); display: flex; align-items: center; justify-content: center; color: var(--text-light);">
                                <span style="font-family: var(--font-headings); font-size: 1.25rem;"><?php esc_html_e( 'Weardale Together Story', 'weardale-together' ); ?></span>
                            </div>
                        <?php endif; ?>
                        <span class="badge" style="position: absolute; top: -10px; left: 30px; background-color: #BA7D0C; color: var(--color-cream); font-size: 0.85rem; padding: 0.4rem 1rem; font-weight: bold; border-radius: var(--border-radius-sm); text-transform: uppercase; letter-spacing: 0.05em; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                            <?php esc_html_e( 'Featured Story', 'weardale-together' ); ?>
                        </span>
                    </div>

                    <!-- Right: Content -->
                    <div class="featured-text">
                        <div style="margin-bottom: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <?php
                            $post_cats = get_the_category();
                            if ( ! empty( $post_cats ) ) {
                                foreach ( $post_cats as $cat ) {
                                    echo '<a href="' . esc_url( get_category_link( $cat->term_id ) ) . '" class="badge badge-creative" style="text-decoration: none; font-size: 0.8rem; background-color: var(--color-cream); color: var(--color-forest); border: 1px solid var(--color-tan);">' . esc_html( $cat->name ) . '</a>';
                                }
                            }
                            ?>
                        </div>

                        <h2 class="font-display" style="font-size: 2.5rem; color: var(--color-forest); line-height: 1.1; margin-top: 0; margin-bottom: 1.5rem; font-weight: normal;">
                            <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: inherit;">
                                <?php the_title(); ?>
                            </a>
                        </h2>

                        <p style="color: var(--text-secondary); font-size: 1.1rem; line-height: 1.6; margin-bottom: 2rem;">
                            <?php echo esc_html( get_the_excerpt() ); ?>
                        </p>

                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                            <a href="<?php the_permalink(); ?>" class="btn btn-primary" style="padding: 0.75rem 2rem;">
                                <?php esc_html_e( 'Read Full Story', 'weardale-together' ); ?>
                            </a>
                            <span style="font-family: var(--font-mono); font-size: 0.85rem; color: var(--text-light);">
                                <?php echo get_the_date(); ?>
                            </span>
                        </div>
                    </div>

                </div>
            </div>
        </section>
        <?php wp_reset_postdata(); ?>
    <?php endif; ?>

    <!-- Search & Filters Bar Section -->
    <section class="journal-filters-section" style="padding: 2.5rem 0; border-bottom: 1px solid var(--color-tan); background-color: var(--bg-primary);">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 2rem; flex-wrap: wrap;" class="filters-row">
                
                <!-- Category Filter Pills -->
                <div class="category-pills-container" style="display: flex; gap: 0.5rem; flex-wrap: wrap; flex-grow: 1;" aria-label="<?php esc_attr_e( 'Filter stories by category', 'weardale-together' ); ?>">
                    <?php
                    $is_main_journal = is_home() && ! is_category();
                    $all_stories_style = $is_main_journal ? 'background-color: var(--color-forest); color: var(--color-cream); border-color: var(--color-forest);' : 'background-color: var(--color-cream); color: var(--text-primary); border-color: var(--color-tan);';
                    ?>
                    <a href="<?php echo esc_url( home_url( '/news-blog/' ) ); ?>" class="badge" style="padding: 0.5rem 1rem; text-decoration: none; font-size: 0.9rem; border-radius: var(--border-radius-pill); font-weight: 500; <?php echo esc_attr( $all_stories_style ); ?> border: 1px solid var(--color-tan);">
                        <?php esc_html_e( 'All Stories', 'weardale-together' ); ?>
                    </a>
                    <?php
                    $cats = get_categories( array(
                        'hide_empty' => true,
                        'exclude'    => array( 1 ), // Exclude Uncategorized
                    ) );
                    if ( ! empty( $cats ) ) {
                        foreach ( $cats as $c ) {
                            $is_active = is_category( $c->term_id );
                            $pill_style = $is_active ? 'background-color: var(--color-forest); color: var(--color-cream); border-color: var(--color-forest);' : 'background-color: var(--color-cream); color: var(--text-primary); border-color: var(--color-tan);';
                            echo '<a href="' . esc_url( get_category_link( $c->term_id ) ) . '" class="badge" style="padding: 0.5rem 1rem; text-decoration: none; font-size: 0.9rem; border-radius: var(--border-radius-pill); font-weight: 500; ' . esc_attr( $pill_style ) . ' border: 1px solid var(--color-tan); transition: all 0.2s ease;">' . esc_html( $c->name ) . '</a>';
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
                        
                        // Skip the featured post in the grid to avoid duplicate listing on page 1
                        if ( get_the_ID() === $featured_id ) {
                            continue;
                        }
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

                                    <!-- Category badge (first non-uncategorized one) -->
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
                <div class="pagination" style="margin-top: 5rem; display: flex; justify-content: center; gap: 0.5rem;" aria-label="<?php esc_attr_e( 'Journal pagination', 'weardale-together' ); ?>">
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
                    <h3 class="font-display" style="font-size: 1.75rem; color: var(--color-forest); margin-top: 0; margin-bottom: 1rem; font-weight: normal;"><?php esc_html_e( 'No Stories Found', 'weardale-together' ); ?></h3>
                    <p style="font-size: 1.1rem; color: var(--text-secondary); line-height: 1.6; margin-bottom: 2rem;">
                        <?php esc_html_e( 'We couldn’t find any stories matching your criteria. Try adjusting your search keywords or browsing different categories.', 'weardale-together' ); ?>
                    </p>
                    <a href="<?php echo esc_url( home_url( '/news-blog/' ) ); ?>" class="btn btn-secondary" style="padding: 0.6rem 1.5rem;">
                        <?php esc_html_e( 'Clear Search & Filters', 'weardale-together' ); ?>
                    </a>
                </div>

            <?php endif; ?>

        </div>
    </section>

</main>

<?php
get_footer();
