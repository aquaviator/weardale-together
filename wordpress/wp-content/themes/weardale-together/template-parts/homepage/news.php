<?php
/**
 * Template part for the Latest News blog query on the homepage.
 *
 * Updated for Sprint 11: Display 1 Featured Story + 3 Latest Stories in a premium, editorial split layout.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */

// 1. Query Featured Story
$featured_args = array(
    'post_type'      => 'post',
    'posts_per_page' => 1,
    'meta_key'       => '_weardale_featured_post',
    'meta_value'     => '1',
    'post_status'    => 'publish',
);
$featured_query = new WP_Query( $featured_args );
$featured_id = 0;

if ( $featured_query->have_posts() ) {
    $featured_query->the_post();
    $featured_id = get_the_ID();
}
wp_reset_postdata();

// 2. Query 3 Latest Stories (excluding the featured one)
$latest_args = array(
    'post_type'      => 'post',
    'posts_per_page' => 3,
    'post_status'    => 'publish',
);
if ( $featured_id ) {
    $latest_args['post__not_in'] = array( $featured_id );
}
$latest_query = new WP_Query( $latest_args );
?>

<section class="section-padding" style="background-color: var(--color-cream); border-bottom: 1px solid var(--color-tan);">
    <div class="container">
        
        <!-- Section Header -->
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 3.5rem; flex-wrap: wrap; gap: 1.5rem;">
            <div>
                <span class="badge badge-creative" style="margin-bottom: 0.5rem;"><?php esc_html_e( 'Our Journal', 'weardale-together' ); ?></span>
                <h2 class="font-display" style="font-size: 2.5rem; margin-bottom: 0; color: var(--color-forest); font-weight: normal;"><?php esc_html_e( 'Stories From Around the Dale', 'weardale-together' ); ?></h2>
            </div>
            <div>
                <a href="<?php echo esc_url( home_url( '/news-blog/' ) ); ?>" class="btn btn-secondary">
                    <?php esc_html_e( 'Read All Stories', 'weardale-together' ); ?>
                </a>
            </div>
        </div>

        <!-- Part A: Featured Story Spotlight Banner -->
        <?php if ( $featured_id ) : ?>
            <?php
            $post = get_post( $featured_id );
            setup_postdata( $post );
            ?>
            <article class="featured-spotlight-card" style="background-color: var(--color-white); border: 1px solid var(--color-tan); border-radius: var(--border-radius-md); padding: 2.5rem; margin-bottom: 3.5rem; box-shadow: 0 4px 15px rgba(59,92,58,0.03); display: grid; grid-template-columns: 1.1fr 1fr; gap: 3rem; align-items: center;">
                
                <!-- Spotlight Left: Image -->
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="featured-spotlight-image" style="border-radius: var(--border-radius-sm); overflow: hidden; border: 1px solid var(--color-tan); aspect-ratio: 16/10;">
                        <?php the_post_thumbnail( 'large', array( 'style' => 'width:100%; height:100%; object-fit:cover; display:block;' ) ); ?>
                    </div>
                <?php else : ?>
                    <div style="aspect-ratio: 16/10; background-color: var(--color-cream); border: 1px dashed var(--color-tan); border-radius: var(--border-radius-sm); display: flex; align-items: center; justify-content: center; color: var(--text-light);">
                        <span style="font-family: var(--font-headings); font-size: 1.1rem;"><?php esc_html_e( 'Spotlight Story', 'weardale-together' ); ?></span>
                    </div>
                <?php endif; ?>

                <!-- Spotlight Right: Text -->
                <div style="display: flex; flex-direction: column; justify-content: center;">
                    <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.75rem;">
                        <span class="badge" style="background-color: #BA7D0C; color: var(--color-cream); font-size: 0.75rem; padding: 0.25rem 0.6rem; font-weight: bold; border-radius: var(--border-radius-sm); text-transform: uppercase; letter-spacing: 0.05em;">
                            <?php esc_html_e( 'Featured Story', 'weardale-together' ); ?>
                        </span>
                        <?php
                        $fcats = get_the_category();
                        if ( ! empty( $fcats ) ) {
                            $primary_cat = $fcats[0];
                            foreach ( $fcats as $fc ) {
                                if ( $fc->term_id !== 1 ) {
                                    $primary_cat = $fc;
                                    break;
                                }
                            }
                            echo '<span class="badge badge-creative" style="font-size: 0.75rem; background-color: var(--color-cream); color: var(--color-forest); border: 1px solid var(--color-tan); padding: 0.25rem 0.6rem;">' . esc_html( $primary_cat->name ) . '</span>';
                        }
                        ?>
                    </div>

                    <h3 class="font-display" style="font-size: 1.85rem; line-height: 1.2; margin-top: 0; margin-bottom: 1rem; color: var(--color-forest); font-weight: normal;">
                        <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: inherit;">
                            <?php the_title(); ?>
                        </a>
                    </h3>

                    <p style="font-size: 1rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.5rem;">
                        <?php echo esc_html( get_the_excerpt() ); ?>
                    </p>

                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-top: auto;">
                        <a href="<?php the_permalink(); ?>" class="btn btn-primary" style="padding: 0.6rem 1.5rem; font-size: 0.95rem;">
                            <?php esc_html_e( 'Read Spotlight Story', 'weardale-together' ); ?>
                        </a>
                        <span style="font-family: var(--font-mono); font-size: 0.8rem; color: var(--text-light);">
                            <?php echo get_the_date(); ?>
                        </span>
                    </div>
                </div>

            </article>
            <?php wp_reset_postdata(); ?>
        <?php endif; ?>

        <!-- Part B: 3 Latest Stories Grid -->
        <?php if ( $latest_query->have_posts() ) : ?>
            <div class="grid grid-3">
                <?php
                while ( $latest_query->have_posts() ) :
                    $latest_query->the_post();
                    ?>
                    <article class="card" style="display: flex; flex-direction: column; background-color: var(--color-white); border: 1px solid var(--color-tan); border-radius: var(--border-radius-md); overflow: hidden; box-shadow: 0 4px 12px rgba(59,92,58,0.02); transition: transform 0.2s ease, box-shadow 0.2s ease;">
                        
                        <!-- Thumbnail -->
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="post-thumbnail" style="border-bottom: 1px solid var(--color-tan); overflow: hidden; aspect-ratio: 16/10;">
                                <?php the_post_thumbnail( 'medium_large', array( 'style' => 'width:100%; height:100%; object-fit:cover; display:block;' ) ); ?>
                            </div>
                        <?php else : ?>
                            <div style="aspect-ratio: 16/10; background-color: var(--color-cream); border-bottom: 1px solid var(--color-tan); display: flex; align-items: center; justify-content: center; color: var(--text-light);">
                                <span style="font-family: var(--font-mono); font-size: 0.85rem;"><?php esc_html_e( 'Weardale Together', 'weardale-together' ); ?></span>
                            </div>
                        <?php endif; ?>

                        <!-- Content Body -->
                        <div style="padding: 1.75rem; flex-grow: 1; display: flex; flex-direction: column;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                                <span style="font-family: var(--font-mono); font-size: 0.8rem; color: var(--text-light);">
                                    <?php echo get_the_date(); ?>
                                </span>
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

                            <h3 class="card-title font-display" style="font-size: 1.3rem; line-height: 1.25; margin-top: 0; margin-bottom: 1rem; font-weight: normal; min-height: 3.2rem;">
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
                wp_reset_postdata();
                ?>
            </div>
        <?php else : ?>
            
            <!-- Cozy fallback card placeholders when standard loop yields nothing -->
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
