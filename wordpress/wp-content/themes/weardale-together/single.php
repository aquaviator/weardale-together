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

<main id="primary-content" class="site-main" role="main">
    <div class="container section-padding">

        <?php
        while ( have_posts() ) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                
                <header class="entry-header" style="margin-bottom: 3rem; text-align: center;">
                    <div style="margin-bottom: 1rem;">
                        <span class="badge badge-creative"><?php esc_html_e( 'News & Blog', 'weardale-together' ); ?></span>
                    </div>
                    
                    <h1 class="entry-title font-display" style="font-size: 2.75rem; color: var(--color-forest); margin-bottom: 1rem;">
                        <?php the_title(); ?>
                    </h1>
                    
                    <div class="entry-meta" style="font-size: 0.95rem; color: var(--text-light); font-family: var(--font-body);">
                        <span>Published on <?php echo get_the_date(); ?></span>
                        <span style="margin: 0 0.5rem;">&bull;</span>
                        <span>By <?php echo get_the_author(); ?></span>
                    </div>
                </header>

                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="entry-thumbnail" style="margin-bottom: 3rem; max-width: 800px; margin-left: auto; margin-right: auto; border-radius: var(--border-radius-md); overflow: hidden; border: 1px solid var(--color-tan);">
                        <?php the_post_thumbnail( 'large', array( 'style' => 'width:100%; height:auto; display:block;' ) ); ?>
                    </div>
                <?php endif; ?>

                <div class="entry-content container-narrow" style="background-color: var(--color-white); padding: 3rem; border: 1px solid var(--color-tan); border-radius: var(--border-radius-md);">
                    <?php
                    the_content();
                    ?>
                </div>

                <footer class="entry-footer container-narrow" style="margin-top: 3rem; padding-top: 1.5rem; border-top: 1px solid var(--color-tan); display: flex; justify-content: space-between;">
                    <div class="nav-links">
                        <div class="nav-previous" style="margin-bottom: 0.5rem;">
                            <?php previous_post_link( '%link', '&larr; Previous Story: %title' ); ?>
                        </div>
                        <div class="nav-next">
                            <?php next_post_link( '%link', 'Next Story: %title &rarr;' ); ?>
                        </div>
                    </div>
                    <div>
                        <a href="<?php echo esc_url( home_url( '/news-blog/' ) ); ?>" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                            Back to Blog
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
