<?php
/**
 * Template part for the Latest News blog query on the homepage.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */

// Query the latest 3 standard posts
$news_args = array(
    'post_type'      => 'post',
    'posts_per_page' => 3,
    'post_status'    => 'publish',
);

$news_query = new WP_Query( $news_args );
?>

<section class="section-padding" style="background-color: var(--color-cream); border-bottom: 1px solid var(--color-tan);">
    <div class="container">
        
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 3.5rem; flex-wrap: wrap; gap: 1.5rem;">
            <div>
                <span class="badge badge-creative" style="margin-bottom: 0.5rem;"><?php esc_html_e( 'Our Journal', 'weardale-together' ); ?></span>
                <h2 class="font-display" style="font-size: 2.5rem; margin-bottom: 0;"><?php esc_html_e( 'Stories From Around the Dale', 'weardale-together' ); ?></h2>
            </div>
            <div>
                <a href="<?php echo esc_url( home_url( '/news-blog/' ) ); ?>" class="btn btn-secondary">
                    <?php esc_html_e( 'Read All Stories', 'weardale-together' ); ?>
                </a>
            </div>
        </div>

        <?php if ( $news_query->have_posts() ) : ?>
            <div class="grid grid-3">
                <?php
                while ( $news_query->have_posts() ) :
                    $news_query->the_post();
                    ?>
                    <article class="card">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="post-thumbnail" style="margin-bottom: 1.5rem; border-radius: var(--border-radius-sm); overflow: hidden; border: 1px solid var(--color-tan);">
                                <?php the_post_thumbnail( 'medium', array( 'style' => 'width:100%; height:auto; display:block;' ) ); ?>
                            </div>
                        <?php endif; ?>

                        <div class="card-meta">
                            <?php echo get_the_date(); ?>
                        </div>

                        <h3 class="card-title" style="font-size: 1.35rem; margin-bottom: 1rem; min-height: 3rem;">
                            <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: inherit;">
                                <?php the_title(); ?>
                            </a>
                        </h3>

                        <div style="font-size: 0.95rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.5rem; flex-grow: 1;">
                            <?php the_excerpt(); ?>
                        </div>

                        <a href="<?php the_permalink(); ?>" class="btn btn-secondary" style="align-self: flex-start; padding: 0.5rem 1rem; font-size: 0.9rem;">
                            <?php esc_html_e( 'Read Story &rarr;', 'weardale-together' ); ?>
                        </a>
                    </article>
                    <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        <?php else : ?>
            
            <!-- Safe, warm placeholder stories when empty -->
            <div class="grid grid-3">
                
                <!-- Card 1 -->
                <article class="card">
                    <div class="card-meta">July 15, 2026</div>
                    <h3 class="card-title" style="font-size: 1.35rem; margin-bottom: 1rem; min-height: 3rem;">
                        <?php esc_html_e( 'Stanhope Hub Blossoms With Summer Creative Program', 'weardale-together' ); ?>
                    </h3>
                    <p style="font-size: 0.95rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.5rem; flex-grow: 1;">
                        <?php esc_html_e( 'Our latest craft workshops have seen over 40 residents gathering to share skills in printmaking, botanical illustration, and woodcarving...', 'weardale-together' ); ?>
                    </p>
                    <span style="font-size: 0.9rem; font-weight: bold; color: var(--color-forest);"><?php esc_html_e( 'Coming soon', 'weardale-together' ); ?></span>
                </article>

                <!-- Card 2 -->
                <article class="card">
                    <div class="card-meta">June 28, 2026</div>
                    <h3 class="card-title" style="font-size: 1.35rem; margin-bottom: 1rem; min-height: 3rem;">
                        <?php esc_html_e( 'Behind the Recipes: Food Ethos of Root & Branch', 'weardale-together' ); ?>
                    </h3>
                    <p style="font-size: 0.95rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.5rem; flex-grow: 1;">
                        <?php esc_html_e( 'Our chef, Cheryl, shares the secrets of our seasonal local soup, sourdough breads, and why making food with true care can transform isolated days...', 'weardale-together' ); ?>
                    </p>
                    <span style="font-size: 0.9rem; font-weight: bold; color: var(--color-forest);"><?php esc_html_e( 'Coming soon', 'weardale-together' ); ?></span>
                </article>

                <!-- Card 3 -->
                <article class="card">
                    <div class="card-meta">May 12, 2026</div>
                    <h3 class="card-title" style="font-size: 1.35rem; margin-bottom: 1rem; min-height: 3rem;">
                        <?php esc_html_e( 'Forest School Adventures: Little Spouts Explored', 'weardale-together' ); ?>
                    </h3>
                    <p style="font-size: 0.95rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.5rem; flex-grow: 1;">
                        <?php esc_html_e( 'Armed with boots and muddy hands, our Youth Programme completed their first outdoor camp in Stanhope woods, building team spirit and tree shelters...', 'weardale-together' ); ?>
                    </p>
                    <span style="font-size: 0.9rem; font-weight: bold; color: var(--color-forest);"><?php esc_html_e( 'Coming soon', 'weardale-together' ); ?></span>
                </article>

            </div>

        <?php endif; ?>

    </div>
</section>
