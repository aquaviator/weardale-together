<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
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
            
            <header class="page-header" style="margin-bottom: 3rem;">
                <h1 class="page-title"><?php single_post_title(); ?></h1>
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

                        <h2 class="card-title">
                            <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: inherit;">
                                <?php the_title(); ?>
                            </a>
                        </h2>

                        <div class="card-meta">
                            <?php echo get_the_date(); ?>
                        </div>

                        <div class="card-content" style="margin-bottom: 1.5rem; flex-grow: 1;">
                            <?php the_excerpt(); ?>
                        </div>

                        <a href="<?php the_permalink(); ?>" class="btn btn-secondary" style="align-self: flex-start; padding: 0.5rem 1.25rem; font-size: 0.95rem;">
                            <?php esc_html_e( 'Read More', 'weardale-together' ); ?>
                        </a>
                    </article>
                    <?php
                endwhile;
                ?>
            </div>

            <div class="pagination" style="margin-top: 3rem; display: flex; justify-content: center; gap: 0.5rem;">
                <?php
                echo paginate_links( array(
                    'prev_text' => '&laquo; Previous',
                    'next_text' => 'Next &raquo;',
                ) );
                ?>
            </div>

        <?php else : ?>
            
            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'weardale-together' ); ?></h1>
            </header>
            <p><?php esc_html_e( 'It seems we can\'t find what you\'re looking for. Perhaps searching can help.', 'weardale-together' ); ?></p>
            <?php get_search_form(); ?>

        <?php endif; ?>

    </div>
</main>

<?php
get_footer();
