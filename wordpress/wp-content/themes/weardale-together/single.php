<?php
/**
 * The template for displaying all single posts.
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
    <div class="container section-padding">

        <?php
        while ( have_posts() ) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                
                <header class="entry-header" style="margin-bottom: 3rem; text-align: center;">
                    <div style="margin-bottom: 1rem; display: flex; justify-content: center; gap: 0.5rem; flex-wrap: wrap;">
                        <span class="badge badge-creative" style="padding: 0.35rem 1rem; font-size: 0.85rem; font-weight: bold;"><?php esc_html_e( 'Our Journal', 'weardale-together' ); ?></span>
                        <?php
                        $post_cats = get_the_category();
                        if ( ! empty( $post_cats ) ) {
                            foreach ( $post_cats as $cat ) {
                                if ( $cat->term_id !== 1 ) { // Skip Uncategorized
                                    echo '<span class="badge" style="background-color: var(--color-cream); color: var(--color-forest); border: 1px solid var(--color-tan); padding: 0.35rem 1rem; font-size: 0.85rem; font-weight: bold; border-radius: var(--border-radius-pill);">' . esc_html( $cat->name ) . '</span>';
                                }
                            }
                        }
                        ?>
                    </div>
                    
                    <h1 class="entry-title font-display" style="font-size: 3rem; color: var(--color-forest); margin-top: 0; margin-bottom: 1rem; line-height: 1.1; font-weight: normal;">
                        <?php the_title(); ?>
                    </h1>
                    
                    <div class="entry-meta" style="font-size: 0.95rem; color: var(--text-light); font-family: var(--font-body);">
                        <span>Published on <?php echo get_the_date(); ?></span>
                        <span style="margin: 0 0.5rem;">&bull;</span>
                        <span>By <?php echo get_the_author(); ?></span>
                    </div>
                </header>

                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="entry-thumbnail" style="margin-bottom: 3rem; max-width: 850px; margin-left: auto; margin-right: auto; border-radius: var(--border-radius-md); overflow: hidden; border: 1px solid var(--color-tan); padding: 10px; background-color: var(--color-white); box-shadow: 0 4px 15px rgba(59,92,58,0.04);">
                        <?php the_post_thumbnail( 'large', array( 'style' => 'width:100%; height:auto; display:block; border-radius: var(--border-radius-sm); border: 1px solid var(--color-tan);' ) ); ?>
                    </div>
                <?php endif; ?>

                <div class="entry-content container-narrow" style="background-color: var(--color-white); padding: 3.5rem; border: 1px solid var(--color-tan); border-radius: var(--border-radius-md); box-shadow: 0 4px 15px rgba(59,92,58,0.03); font-size: 1.1rem; line-height: 1.7; color: var(--text-primary);">
                    <?php
                    the_content();
                    ?>

                    <?php
                    // Retrieve editorial relationships
                    $post_programme      = get_post_meta( get_the_ID(), '_weardale_post_programme', true );
                    $related_event_id    = get_post_meta( get_the_ID(), '_weardale_related_event_id', true );
                    $related_directory_id = get_post_meta( get_the_ID(), '_weardale_related_directory_id', true );

                    $has_connections = ! empty( $post_programme ) || ! empty( $related_event_id ) || ! empty( $related_directory_id );

                    if ( $has_connections ) :
                    ?>
                        <div class="story-connections-block" style="margin-top: 4rem; padding-top: 3rem; border-top: 1px solid var(--color-tan);">
                            <h3 class="font-display" style="font-size: 1.75rem; color: var(--color-forest); margin-top: 0; margin-bottom: 1.5rem; font-weight: normal;">
                                <?php esc_html_e( 'Community Connections', 'weardale-together' ); ?>
                            </h3>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem;" class="connections-grid">
                                
                                <?php if ( ! empty( $post_programme ) ) : ?>
                                    <?php
                                    $prog_title = '';
                                    $prog_url = '';
                                    switch ( $post_programme ) {
                                        case 'cafe':
                                            $prog_title = 'Root & Branch Café';
                                            $prog_url = home_url( '/cafe/' );
                                            break;
                                        case 'youth':
                                            $prog_title = 'Young People & Forest School';
                                            $prog_url = home_url( '/young-people/' );
                                            break;
                                        case 'creative':
                                            $prog_title = 'Creative Arts';
                                            $prog_url = home_url( '/creative-arts/' );
                                            break;
                                        case 'shoots':
                                            $prog_title = 'Roots & Shoots Early Years';
                                            $prog_url = home_url( '/roots-shoots/' );
                                            break;
                                    }
                                    ?>
                                    <?php if ( $prog_title ) : ?>
                                        <div style="background-color: var(--color-cream); border: 1px solid var(--color-tan); border-radius: var(--border-radius-sm); padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between;">
                                            <div>
                                                <span class="badge" style="background-color: var(--color-forest); color: var(--color-cream); font-size: 0.75rem; margin-bottom: 0.5rem; padding: 0.25rem 0.5rem; border-radius: var(--border-radius-pill); display: inline-block; text-transform: uppercase; font-weight: bold;"><?php esc_html_e( 'Programme Strand', 'weardale-together' ); ?></span>
                                                <h4 style="margin: 0.5rem 0; font-size: 1.15rem; color: var(--color-forest); line-height: 1.25; font-weight: 600;"><?php echo esc_html( $prog_title ); ?></h4>
                                            </div>
                                            <a href="<?php echo esc_url( $prog_url ); ?>" class="btn btn-secondary" style="margin-top: 1rem; align-self: flex-start; padding: 0.4rem 0.8rem; font-size: 0.85rem; width: 100%; text-align: center;">
                                                <?php esc_html_e( 'View Programme &rarr;', 'weardale-together' ); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if ( ! empty( $related_event_id ) && 'publish' === get_post_status( $related_event_id ) ) : ?>
                                    <div style="background-color: var(--color-cream); border: 1px solid var(--color-tan); border-radius: var(--border-radius-sm); padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between;">
                                        <div>
                                            <span class="badge" style="background-color: #BA7D0C; color: var(--color-cream); font-size: 0.75rem; margin-bottom: 0.5rem; padding: 0.25rem 0.5rem; border-radius: var(--border-radius-pill); display: inline-block; text-transform: uppercase; font-weight: bold;"><?php esc_html_e( 'Related Activity', 'weardale-together' ); ?></span>
                                            <h4 style="margin: 0.5rem 0; font-size: 1.15rem; color: var(--color-forest); line-height: 1.25; font-weight: 600;"><?php echo esc_html( get_the_title( $related_event_id ) ); ?></h4>
                                        </div>
                                        <a href="<?php echo esc_url( get_permalink( $related_event_id ) ); ?>" class="btn btn-secondary" style="margin-top: 1rem; align-self: flex-start; padding: 0.4rem 0.8rem; font-size: 0.85rem; width: 100%; text-align: center;">
                                            <?php esc_html_e( 'View Activity Details &rarr;', 'weardale-together' ); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <?php if ( ! empty( $related_directory_id ) && 'publish' === get_post_status( $related_directory_id ) ) : ?>
                                    <div style="background-color: var(--color-cream); border: 1px solid var(--color-tan); border-radius: var(--border-radius-sm); padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between;">
                                        <div>
                                            <span class="badge" style="background-color: #B2583E; color: var(--color-cream); font-size: 0.75rem; margin-bottom: 0.5rem; padding: 0.25rem 0.5rem; border-radius: var(--border-radius-pill); display: inline-block; text-transform: uppercase; font-weight: bold;"><?php esc_html_e( 'Directory Listing', 'weardale-together' ); ?></span>
                                            <h4 style="margin: 0.5rem 0; font-size: 1.15rem; color: var(--color-forest); line-height: 1.25; font-weight: 600;"><?php echo esc_html( get_the_title( $related_directory_id ) ); ?></h4>
                                        </div>
                                        <a href="<?php echo esc_url( get_permalink( $related_directory_id ) ); ?>" class="btn btn-secondary" style="margin-top: 1rem; align-self: flex-start; padding: 0.4rem 0.8rem; font-size: 0.85rem; width: 100%; text-align: center;">
                                            <?php esc_html_e( 'View Listing Details &rarr;', 'weardale-together' ); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <footer class="entry-footer container-narrow" style="margin-top: 3rem; padding-top: 1.5rem; border-top: 1px solid var(--color-tan); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div class="nav-links">
                        <div class="nav-previous" style="margin-bottom: 0.5rem; font-size: 0.9rem;">
                            <?php previous_post_link( '%link', '&larr; Previous Story: %title' ); ?>
                        </div>
                        <div class="nav-next" style="font-size: 0.9rem;">
                            <?php next_post_link( '%link', 'Next Story: %title &rarr;' ); ?>
                        </div>
                    </div>
                    <div>
                        <a href="<?php echo esc_url( home_url( '/news-blog/' ) ); ?>" class="btn btn-secondary" style="padding: 0.5rem 1.25rem; font-size: 0.9rem;">
                            Back to Journal
                        </a>
                    </div>
                </footer>

            </article>
            <?php
        endwhile;
        ?>

    </div>
</main>

<?php
get_footer();
