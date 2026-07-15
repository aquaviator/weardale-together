<?php
/**
 * The template for displaying archive pages.
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

        <?php if ( have_posts() ) : ?>
            
            <header class="page-header" style="margin-bottom: 3.5rem; text-align: center;">
                <?php
                the_archive_title( '<h1 class="page-title font-display" style="font-size: 3rem; color: var(--color-forest); margin-bottom: 0.5rem;">', '</h1>' );
                the_archive_description( '<div class="archive-description" style="color: var(--text-secondary); max-width: 600px; margin: 0 auto;">', '</div>' );
                ?>
                <div style="width: 60px; height: 3px; background-color: var(--color-tan); margin: 1.5rem auto 0 auto;"></div>
            </header>

            <div class="grid grid-3">
                <?php
                while ( have_posts() ) :
                    the_post();
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class( 'card' ); ?>>
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="post-thumbnail" style="margin-bottom: 1.5rem; border-radius: var(--border-radius-sm); overflow: hidden;">
                                <?php the_post_thumbnail( 'medium', array( 'style' => 'width:100%; height:auto; display:block;' ) ); ?>
                            </div>
                        <?php endif; ?>

                        <h2 class="card-title" style="font-size: 1.4rem;">
                            <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: inherit;">
                                <?php the_title(); ?>
                            </a>
                        </h2>

                        <div class="card-meta">
                            <?php echo get_the_date(); ?>
                        </div>

                        <div class="card-content" style="margin-bottom: 1.5rem; flex-grow: 1; font-size: 0.95rem;">
                            <?php the_excerpt(); ?>
                        </div>

                        <a href="<?php the_permalink(); ?>" class="btn btn-secondary" style="align-self: flex-start; padding: 0.5rem 1rem; font-size: 0.9rem;">
                            <?php esc_html_e( 'Read More', 'weardale-together' ); ?>
                        </a>
                    </article>
                    <?php
                endwhile;
                ?>
            </div>

            <div class="pagination" style="margin-top: 4rem; display: flex; justify-content: center; gap: 0.5rem;">
                <?php
                echo paginate_links( array(
                    'prev_text' => '&larr; Previous',
                    'next_text' => 'Next &rarr;',
                ) );
                ?>
            </div>

        <?php else : ?>

            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'weardale-together' ); ?></h1>
            </header>
            <p><?php esc_html_e( 'It seems there are no posts matching this archive.', 'weardale-together' ); ?></p>

        <?php endif; ?>

    </div>
</main>

<?php
get_footer();
