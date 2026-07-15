<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
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
                    <h1 class="entry-title font-display" style="font-size: 3rem; color: var(--color-forest); margin-bottom: 1rem;">
                        <?php the_title(); ?>
                    </h1>
                    <div style="width: 80px; height: 3px; background-color: var(--color-tan); margin: 0 auto;"></div>
                </header>

                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="entry-thumbnail" style="margin-bottom: 3rem; max-width: 900px; margin-left: auto; margin-right: auto; border-radius: var(--border-radius-md); overflow: hidden; border: 1px solid var(--color-tan);">
                        <?php the_post_thumbnail( 'large', array( 'style' => 'width:100%; height:auto; display:block;' ) ); ?>
                    </div>
                <?php endif; ?>

                <div class="entry-content container-narrow" style="background-color: var(--color-white); padding: 3rem; border: 1px solid var(--color-tan); border-radius: var(--border-radius-md); box-shadow: 0 4px 12px rgba(196,184,154,0.1);">
                    <?php
                    the_content();

                    wp_link_pages( array(
                        'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'weardale-together' ),
                        'after'  => '</div>',
                    ) );
                    ?>
                </div>

            </article>
            <?php
        endwhile;
        ?>

    </div>
</main>

<?php
get_footer();
