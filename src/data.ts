import { WTEvent, WTPost, WPFile } from './types';

export const starterEvents: WTEvent[] = [
  {
    id: 'e1',
    title: 'Summer Clay Sculpting',
    content: 'Join us for a relaxing morning of clay sculpting and tea at the Stanhope Hub. No previous skill required. We will guide you through shaping rustic bowls, pinch pots, and small plant vessels using natural clay forms.',
    date: '2026-08-10',
    time: '10:00 AM - 1:00 PM',
    location: 'Stanhope Hub Workshop Rooms',
    cost: 'Free (£2 material donation welcome)',
    strand: 'creative'
  },
  {
    id: 'e2',
    title: 'Forest School: Tree Discovery',
    content: 'Bring your little ones for tree planting, mud kitchen fun, and woodland shelter building in the local woods. Outdoor clothing and stout boots are highly recommended!',
    date: '2026-08-14',
    time: '1:30 PM - 4:00 PM',
    location: 'Stanhope Community Woodlands',
    cost: 'Free (Booking Required)',
    strand: 'youth'
  },
  {
    id: 'e3',
    title: 'Senior Soup & Story Exchange',
    content: 'A seasonal gathering for local seniors. Come share hot home-cooked soup, fresh bread, tea, and warm conversations with our chef Cheryl in a safe, unhurried kitchen environment.',
    date: '2026-08-18',
    time: '12:00 PM - 2:30 PM',
    location: 'Root & Branch Café Kitchen',
    cost: 'Free (Lunch provided with care)',
    strand: 'cafe'
  }
];

export const starterPosts: WTPost[] = [
  {
    id: 'p1',
    title: 'Stanhope Hub Blossoms With Summer Creative Program',
    excerpt: 'Our latest craft workshops have seen over 40 residents gathering to share skills in printmaking, botanical illustration, and woodcarving...',
    content: 'Our latest craft workshops have seen over 40 residents gathering to share skills in printmaking, botanical illustration, and woodcarving. Designed especially for "people who do not think of themselves as creative," the summer program has generated dozens of handmade prints and wooden spoons. "It is not about perfect lines; it is about gathering, talking, and realizing you can make things with your hands," says Hollie, our Creative Director.',
    date: 'July 15, 2026',
    author: 'Hollie Clark',
    strand: 'creative',
    featured: false
  },
  {
    id: 'p2',
    title: 'Frosterley Sourdough: Slicing into Community Baking',
    excerpt: 'A look behind the flour at our weekly baking circles, where kneaded loaves build strong connections...',
    content: 'There is a special quiet that falls over the Frosterley Village Hall kitchen on Wednesday evenings. Beneath the warmth of the lights, a dozen local residents stand side-by-side, their hands coated in stoneground flour, working together to shape traditional sourdough loaves. All loaves baked during the circle are either taken home by participants or donated directly to the Root & Branch Community Cafe for their Thursday pay-what-you-can lunch menu.',
    date: 'June 28, 2026',
    author: 'Cheryl Thompson',
    strand: 'cafe',
    featured: true,
    relatedDirectory: 'The Stanhope Artisan Bakery'
  },
  {
    id: 'p3',
    title: 'Forest School Adventures: Little Spouts Explored',
    excerpt: 'Armed with boots and muddy hands, our Youth Programme completed their first outdoor camp in Stanhope woods, building team spirit...',
    content: 'Armed with boots and muddy hands, our Youth Programme completed their first outdoor camp in Stanhope woods, building team spirit and tree shelters. Guided by our outdoor experts, 15 local kids spent the weekend learning safe knife work, campfire cooking, and plant identification. "Living in the Pennines is amazing, but we often miss out on structured group activities. This program gives our children a space to be loud, dirty, and confident," said a local parent.',
    date: 'May 12, 2026',
    author: 'Andy Clarke',
    strand: 'youth',
    featured: false,
    relatedEvent: "Young Rangers Forest Survival Camp"
  },
  {
    id: 'p4',
    title: 'A SONG for Weardale',
    excerpt: 'A beautiful musical celebration echoing across our valleys, bringing local choir voices together to preserve Pennine song heritage.',
    content: 'The old stone arches of the Stanhope Church resonated with choral harmony as residents from across Upper Weardale gathered for the first rehearsal of "A SONG for Weardale." This community-composed musical suite draws inspiration from 19th-century miners\' journals, regional hymns, and the seasonal sounds of wind over the heather. Organized by the Weardale Creative Makers Guild as part of our Creative Arts strand, the project aims to connect generations through shared song.',
    date: 'August 1, 2026',
    author: 'Andy Clarke',
    strand: 'creative',
    featured: false,
    relatedEvent: "Andy's Event",
    relatedDirectory: "Weardale Creative Makers Guild"
  }
];

export const wordpressFiles: WPFile[] = [
  {
    name: 'news-meta.php',
    path: 'plugins/weardale-platform/includes/news-meta.php',
    type: 'plugin',
    content: `<?php
/**
 * Story Editorial Metadata Management & Editor Interface
 * Contains programme relationships, related event links, directory associations, and featured story flags.
 */

// Add Meta Box
add_action( 'add_meta_boxes', function() {
    add_meta_box( 'weardale_news_details', 'Story Editorial Metadata', 'weardale_platform_render_news_box', 'post', 'normal', 'high' );
} );

// Save Metadata securely
add_action( 'save_post', function( $post_id ) {
    if ( ! isset( $_POST['weardale_news_meta_nonce_field'] ) || ! wp_verify_nonce( $_POST['weardale_news_meta_nonce_field'], 'weardale_news_meta_nonce_action' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( isset( $_POST['weardale_post_programme'] ) ) {
        update_post_meta( $post_id, '_weardale_post_programme', sanitize_text_field( $_POST['weardale_post_programme'] ) );
    }
    if ( isset( $_POST['weardale_related_event_id'] ) ) {
        update_post_meta( $post_id, '_weardale_related_event_id', sanitize_text_field( $_POST['weardale_related_event_id'] ) );
    }
    if ( isset( $_POST['weardale_related_directory_id'] ) ) {
        update_post_meta( $post_id, '_weardale_related_directory_id', sanitize_text_field( $_POST['weardale_related_directory_id'] ) );
    }

    // Toggle Featured Status
    $featured = isset( $_POST['weardale_featured_post'] ) ? '1' : '0';
    if ( '1' === $featured ) {
        // Enforce single active featured story by un-featuring all other posts
        global $wpdb;
        $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value = '0' WHERE meta_key = '_weardale_featured_post' AND post_id != %d", $post_id ) );
    }
    update_post_meta( $post_id, '_weardale_featured_post', $featured );
} );`
  },
  {
    name: 'content-strand.php',
    path: 'themes/weardale-together/template-parts/content-strand.php',
    type: 'theme',
    content: `<?php
/**
 * Template part for displaying programme strand pages.
 * Includes related Event Query + News & Stories Query linked via _weardale_post_programme.
 */
$strand = weardale_together_get_current_strand(); // "cafe", "creative", etc.
?>
<section class="strand-events-section">
    <!-- Events query logic -->
</section>

<!-- Latest News & Stories Section -->
<section class="strand-news-section" style="background-color: var(--color-cream); border-top: 1px solid var(--color-tan); padding: 4rem 0;">
    <div class="container">
        <h2 class="font-display" style="font-size: 2.25rem; color: var(--color-forest); margin-bottom: 2rem; text-align: center;">Latest News & Stories</h2>
        <?php
        $story_query = new WP_Query( array(
            'post_type'      => 'post',
            'posts_per_page' => 3,
            'post_status'    => 'publish',
            'meta_query'     => array(
                array( 'key' => '_weardale_post_programme', 'value' => $strand, 'compare' => '=' )
            )
        ) );
        if ( $story_query->have_posts() ) : ?>
            <div class="grid grid-3">
                <?php while ( $story_query->have_posts() ) : $story_query->the_post(); ?>
                    <article class="card">
                        <!-- Post thumbnail & details -->
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <p><?php the_excerpt(); ?></p>
                        <a href="<?php the_permalink(); ?>" class="btn btn-secondary">Read Story &rarr;</a>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        <?php else : ?>
            <p style="text-align: center; color: var(--text-light);">No published stories for this strand yet.</p>
        <?php endif; ?>
    </div>
</section>`
  },
  {
    name: 'homepage-news.php',
    path: 'themes/weardale-together/template-parts/homepage/news.php',
    type: 'theme',
    content: `<?php
/**
 * Queries 1 Featured Spotlight Story + 3 Recent News Stories
 */

// 1. Query Featured Spotlight Story
$featured_query = new WP_Query( array(
    'post_type'      => 'post',
    'posts_per_page' => 1,
    'meta_key'       => '_weardale_featured_post',
    'meta_value'     => '1',
    'post_status'    => 'publish',
) );
$featured_id = 0;
if ( $featured_query->have_posts() ) {
    $featured_query->the_post();
    $featured_id = get_the_ID();
}
wp_reset_postdata();

// 2. Query 3 Latest Stories (excluding featured)
$latest_query = new WP_Query( array(
    'post_type'      => 'post',
    'posts_per_page' => 3,
    'post_status'    => 'publish',
    'post__not_in'   => $featured_id ? array( $featured_id ) : array(),
) );
?>
<section class="homepage-news">
    <?php if ( $featured_id ) : ?>
        <div class="featured-spotlight">
            <!-- Featured Spotlight Banner UI -->
        </div>
    <?php endif; ?>

    <div class="grid grid-3">
        <?php while ( $latest_query->have_posts() ) : $latest_query->the_post(); ?>
            <!-- Recent story cards -->
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
</section>`
  },
  {
    name: 'single.php',
    path: 'themes/weardale-together/single.php',
    type: 'theme',
    content: `<?php
/**
 * Single Post Template: Renders body + Community Connections metadata linkages
 */
get_header();
while ( have_posts() ) : the_post(); ?>
    <article>
        <h1><?php the_title(); ?></h1>
        <div class="entry-content"><?php the_content(); ?></div>

        <?php
        // Fetch linkages
        $programme = get_post_meta( get_the_ID(), '_weardale_post_programme', true );
        $event_id  = get_post_meta( get_the_ID(), '_weardale_related_event_id', true );
        $dir_id    = get_post_meta( get_the_ID(), '_weardale_related_directory_id', true );
        if ( $programme || $event_id || $dir_id ) : ?>
            <div class="story-connections-block">
                <h3>Community Connections</h3>
                <!-- Display linked programme strand, events, directory listings -->
            </div>
        <?php endif; ?>
    </article>
<?php endwhile;
get_footer();`
  },
  {
    name: 'style.css',
    path: 'themes/weardale-together/style.css',
    type: 'theme',
    content: `/*
Theme Name: Weardale Together
Theme URI: https://weardaletogether.org.uk/
Author: Weardale Together Delivery Team
Author URI: https://weardaletogether.org.uk/
Description: A warm, organic, handcrafted, and highly accessible classic PHP theme for Weardale Together CIC. Designed for residents, families, and visitors of the North Pennines.
Version: 1.0.0
Text Domain: weardale-together
*/

@import 'css/variables.css';
@import 'css/typography.css';
@import 'css/layout.css';
@import 'css/components.css';
@import 'css/strands.css';`
  },
  {
    name: 'theme.json',
    path: 'themes/weardale-together/theme.json',
    type: 'theme',
    content: `{
  "$schema": "https://schemas.wp.org/trunk/theme.json",
  "version": 2,
  "settings": {
    "color": {
      "palette": [
        { "name": "Warm Cream", "slug": "cream", "color": "#F5F0E8" },
        { "name": "Deep Forest Green", "slug": "forest", "color": "#3B5C3A" },
        { "name": "Sage Green", "slug": "sage", "color": "#6B8F5E" },
        { "name": "Warm Tan", "slug": "tan", "color": "#C4B89A" },
        { "name": "Near Black", "slug": "black", "color": "#2C2C2A" }
      ],
      "custom": false
    },
    "typography": {
      "fontFamilies": [
        { "fontFamily": "\\"Yeseva One\\", Georgia, serif", "name": "Yeseva One", "slug": "display" },
        { "fontFamily": "\\"Nunito\\", sans-serif", "name": "Nunito", "slug": "body" }
      ]
    }
  }
}`
  },
  {
    name: 'functions.php',
    path: 'themes/weardale-together/functions.php',
    type: 'theme',
    content: `<?php
/**
 * Weardale Together Theme Functions
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function weardale_together_setup() {
    load_theme_textdomain( 'weardale-together', get_template_directory() . '/languages' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    
    register_nav_menus( array(
        'primary-menu' => esc_html__( 'Primary Navigation Menu', 'weardale-together' ),
        'footer-menu'  => esc_html__( 'Footer Navigation Menu', 'weardale-together' ),
    ) );
}
add_action( 'after_setup_theme', 'weardale_together_setup' );`
  },
  {
    name: 'weardale-platform.php',
    path: 'plugins/weardale-platform/weardale-platform.php',
    type: 'plugin',
    content: `<?php
/**
 * Plugin Name: Weardale Platform
 * Description: Core platform-specific logic (Custom Post Types & Taxonomies) for Weardale Together.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'WEARDALE_PLATFORM_DIR', plugin_dir_path( __FILE__ ) );

function weardale_platform_init() {
    $includes_dir = WEARDALE_PLATFORM_DIR . 'includes/';
    if ( file_exists( $includes_dir . 'editorial.php' ) ) {
        include_once $includes_dir . 'editorial.php';
    }
    if ( file_exists( $includes_dir . 'news-meta.php' ) ) {
        include_once $includes_dir . 'news-meta.php';
    }
}
add_action( 'plugins_loaded', 'weardale_platform_init' );`
  }
];
