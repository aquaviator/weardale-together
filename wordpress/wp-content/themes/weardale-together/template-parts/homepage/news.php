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

<section class="section-padding journal-section">
    <div class="container">
        
        <!-- Section Header -->
        <div class="journal-header">
            <div>
                <span class="badge badge-creative mb-2"><?php esc_html_e( 'Our Journal', 'weardale-together' ); ?></span>
                <h2 class="font-display"><?php esc_html_e( 'Stories From Around the Dale', 'weardale-together' ); ?></h2>
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
            <article class="featured-spotlight-card">
                
                <!-- Spotlight Left: Image -->
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="featured-spotlight-image">
                        <?php the_post_thumbnail( 'large' ); ?>
                    </div>
                <?php else : ?>
                    <div class="featured-spotlight-fallback">
                        <span><?php esc_html_e( 'Spotlight Story', 'weardale-together' ); ?></span>
                    </div>
                <?php endif; ?>

                <!-- Spotlight Right: Text -->
                <div class="featured-spotlight-text">
                    <div class="featured-spotlight-badge-area">
                        <span class="badge featured-spotlight-featured-badge">
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
                            echo '<span class="badge badge-creative featured-spotlight-category-badge">' . esc_html( $primary_cat->name ) . '</span>';
                        }
                        ?>
                    </div>

                    <h3 class="font-display featured-spotlight-title">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </h3>

                    <p class="featured-spotlight-excerpt">
                        <?php echo esc_html( get_the_excerpt() ); ?>
                    </p>

                    <div class="featured-spotlight-footer">
                        <a href="<?php the_permalink(); ?>" class="btn btn-primary featured-spotlight-btn">
                            <?php esc_html_e( 'Read Spotlight Story', 'weardale-together' ); ?>
                        </a>
                        <span class="featured-spotlight-date">
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
                    <article class="card journal-card">
                        
                        <!-- Thumbnail -->
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="journal-card-thumbnail">
                                <?php the_post_thumbnail( 'medium_large' ); ?>
                            </div>
                        <?php else : ?>
                            <div class="journal-card-thumbnail-fallback">
                                <span><?php esc_html_e( 'Weardale Together', 'weardale-together' ); ?></span>
                            </div>
                        <?php endif; ?>

                        <!-- Content Body -->
                        <div class="journal-card-content">
                            <div class="journal-card-meta">
                                <span class="journal-card-date">
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
                                    echo '<span class="badge badge-creative journal-card-category-badge">' . esc_html( $primary_cat->name ) . '</span>';
                                }
                                ?>
                            </div>

                            <h3 class="font-display journal-card-title">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h3>

                            <div class="journal-card-excerpt">
                                <?php the_excerpt(); ?>
                            </div>

                            <a href="<?php the_permalink(); ?>" class="btn btn-secondary journal-card-btn">
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
                <article class="card journal-card">
                    <div class="journal-card-content">
                        <div class="journal-card-meta">
                            <span class="journal-card-date">July 15, 2026</span>
                        </div>
                        <h3 class="font-display journal-card-title">
                            <?php esc_html_e( 'Stanhope Hub Blossoms With Summer Creative Program', 'weardale-together' ); ?>
                        </h3>
                        <p class="journal-card-excerpt">
                            <?php esc_html_e( 'Our latest craft workshops have seen over 40 residents gathering to share skills in printmaking, botanical illustration, and woodcarving...', 'weardale-together' ); ?>
                        </p>
                        <span class="journal-card-date"><strong><?php esc_html_e( 'Coming soon', 'weardale-together' ); ?></strong></span>
                    </div>
                </article>

                <!-- Card 2 -->
                <article class="card journal-card">
                    <div class="journal-card-content">
                        <div class="journal-card-meta">
                            <span class="journal-card-date">June 28, 2026</span>
                        </div>
                        <h3 class="font-display journal-card-title">
                            <?php esc_html_e( 'Behind the Recipes: Food Ethos of Root & Branch', 'weardale-together' ); ?>
                        </h3>
                        <p class="journal-card-excerpt">
                            <?php esc_html_e( 'Our chef, Cheryl, shares the secrets of our seasonal local soup, sourdough breads, and why making food with true care can transform isolated days...', 'weardale-together' ); ?>
                        </p>
                        <span class="journal-card-date"><strong><?php esc_html_e( 'Coming soon', 'weardale-together' ); ?></strong></span>
                    </div>
                </article>

                <!-- Card 3 -->
                <article class="card journal-card">
                    <div class="journal-card-content">
                        <div class="journal-card-meta">
                            <span class="journal-card-date">May 12, 2026</span>
                        </div>
                        <h3 class="font-display journal-card-title">
                            <?php esc_html_e( 'Forest School Adventures: Little Spouts Explored', 'weardale-together' ); ?>
                        </h3>
                        <p class="journal-card-excerpt">
                            <?php esc_html_e( 'Armed with boots and muddy hands, our Youth Programme completed their first outdoor camp in Stanhope woods, building team spirit and tree shelters...', 'weardale-together' ); ?>
                        </p>
                        <span class="journal-card-date"><strong><?php esc_html_e( 'Coming soon', 'weardale-together' ); ?></strong></span>
                    </div>
                </article>

            </div>

        <?php endif; ?>

    </div>
</section>
