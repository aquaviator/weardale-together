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
    strand: 'creative'
  },
  {
    id: 'p2',
    title: 'Behind the Recipes: Food Ethos of Root & Branch',
    excerpt: 'Our chef, Cheryl, shares the secrets of our seasonal local soup, sourdough breads, and why making food with true care can transform isolated days...',
    content: 'Our chef, Cheryl, shares the secrets of our seasonal local soup, sourdough breads, and why making food with true care can transform isolated days. "We operate with a simple rule: if it isn\'t made with care, it doesn\'t leave our kitchen," Cheryl smiles. Every soup uses ingredients from Weardale growers, and our daily bread is fermented slowly over 24 hours. More than just food, the café serves as a safe space where isolated seniors, families, and hikers sit side-by-side.',
    date: 'June 28, 2026',
    author: 'Cheryl Thompson',
    strand: 'cafe'
  },
  {
    id: 'p3',
    title: 'Forest School Adventures: Little Spouts Explored',
    excerpt: 'Armed with boots and muddy hands, our Youth Programme completed their first outdoor camp in Stanhope woods, building team spirit...',
    content: 'Armed with boots and muddy hands, our Youth Programme completed their first outdoor camp in Stanhope woods, building team spirit and tree shelters. Guided by our outdoor experts, 15 local kids spent the weekend learning safe knife work, campfire cooking, and plant identification. "Living in the Pennines is amazing, but we often miss out on structured group activities. This program gives our children a space to be loud, dirty, and confident," said a local parent.',
    date: 'May 12, 2026',
    author: 'Andy Clarke',
    strand: 'youth'
  }
];

export const wordpressFiles: WPFile[] = [
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
add_action( 'after_setup_theme', 'weardale_together_setup' );

function weardale_together_scripts() {
    wp_enqueue_style( 'weardale-together-style', get_stylesheet_uri(), array(), '1.0.0' );
    wp_enqueue_script( 'weardale-together-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'weardale_together_scripts' );`
  },
  {
    name: 'front-page.php',
    path: 'themes/weardale-together/front-page.php',
    type: 'theme',
    content: `<?php
/**
 * The template for displaying the front page.
 */
get_header();
?>
<main id="primary-content" class="site-main" role="main">
    <?php
    get_template_part( 'template-parts/homepage/hero' );
    get_template_part( 'template-parts/homepage/hub-and-spoke' );
    get_template_part( 'template-parts/homepage/whats-happening' );
    get_template_part( 'template-parts/homepage/programme-areas' );
    get_template_part( 'template-parts/homepage/about' );
    get_template_part( 'template-parts/homepage/news' );
    get_template_part( 'template-parts/homepage/volunteer' );
    get_template_part( 'template-parts/homepage/newsletter' );
    ?>
</main>
<?php
get_footer();`
  },
  {
    name: 'hub-and-spoke.php',
    path: 'themes/weardale-together/template-parts/homepage/hub-and-spoke.php',
    type: 'theme',
    content: `<?php
/**
 * Template part for the priority Hub-and-Spoke interactive diagram.
 */
?>
<section id="hub-and-spoke-section" class="section-padding">
    <div class="container">
        <!-- Circular interactive diagram for desktop, vertical list for mobile -->
        <div class="hub-spoke-desktop-only">
            <!-- Central WT brand with 4 radiating nodes -->
            <svg class="connecting-vectors">...</svg>
            <ul class="nodes">
                <li><a href="<?php echo esc_url(home_url('/cafe/')); ?>">☕ Root & Branch Café</a></li>
                <li><a href="<?php echo esc_url(home_url('/creative-arts/')); ?>">🎨 Creative Arts</a></li>
                <li><a href="<?php echo esc_url(home_url('/young-people/')); ?>">🌲 Young People</a></li>
                <li><a href="<?php echo esc_url(home_url('/roots-shoots/')); ?>">🧸 Roots & Shoots</a></li>
            </ul>
        </div>
    </div>
</section>`
  },
  {
    name: 'whats-happening.php',
    path: 'themes/weardale-together/template-parts/homepage/whats-happening.php',
    type: 'theme',
    content: `<?php
/**
 * Template part for displaying dynamic Weardale Together event listings.
 */
$args = array(
    'post_type'      => 'weardale_event',
    'posts_per_page' => 3,
    'meta_key'       => '_event_date',
    'orderby'        => 'meta_value',
    'order'          => 'ASC',
);
$events_query = new WP_Query( $args );
if ( $events_query->have_posts() ) :
    while ( $events_query->have_posts() ) : $events_query->the_post();
        // Render custom post meta
        $date = get_post_meta( get_the_ID(), '_event_date', true );
        $time = get_post_meta( get_the_ID(), '_event_time', true );
    endwhile;
endif;`
  },
  {
    name: 'weardale-platform.php',
    path: 'plugins/weardale-platform/weardale-platform.php',
    type: 'plugin',
    content: `<?php
/**
 * Plugin Name: Weardale Platform
 * Description: Core platform-specific logic (Custom Post Types & Taxonomies) for Weardale Together.
 * Version: 1.0.0
 * Text Domain: weardale-platform
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'WEARDALE_PLATFORM_DIR', plugin_dir_path( __FILE__ ) );

function weardale_platform_init() {
    $includes_dir = WEARDALE_PLATFORM_DIR . 'includes/';
    if ( file_exists( $includes_dir . 'editorial.php' ) ) {
        include_once $includes_dir . 'editorial.php';
    }
}
add_action( 'plugins_loaded', 'weardale_platform_init' );`
  },
  {
    name: 'editorial.php',
    path: 'plugins/weardale-platform/includes/editorial.php',
    type: 'plugin',
    content: `<?php
/**
 * Registers Weardale Event CPT, Strand Taxonomy, and Meta Fields.
 */
function weardale_platform_register_event_cpt() {
    register_post_type( 'weardale_event', array(
        'labels'      => array( 'name' => 'Events', 'singular_name' => 'Event' ),
        'public'      => true,
        'has_archive' => true,
        'menu_icon'   => 'dashicons-calendar-alt',
        'supports'    => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'show_in_rest'=> true,
    ) );
}
add_action( 'init', 'weardale_platform_register_event_cpt' );

function weardale_platform_register_strand_taxonomy() {
    register_taxonomy( 'strand', array( 'post', 'page', 'weardale_event' ), array(
        'hierarchical'      => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
    ) );
}
add_action( 'init', 'weardale_platform_register_strand_taxonomy' );`
  },
  {
    name: 'seed-db.sql',
    path: 'scripts/seed-db.sql',
    type: 'script',
    content: `-- Insert Core Information Architecture Pages
INSERT INTO \`wp_posts\` (\`post_author\`, \`post_title\`, \`post_name\`, \`post_type\`) VALUES
(1, 'Root & Branch Café', 'cafe', 'page'),
(1, 'Young People', 'young-people', 'page'),
(1, 'Creative Arts', 'creative-arts', 'page'),
(1, 'Roots & Shoots', 'roots-shoots', 'page');

-- Seed Event CPT metadata
INSERT INTO \`wp_postmeta\` (\`post_id\`, \`meta_key\`, \`meta_value\`) VALUES
(100, '_event_date', '2026-08-10'),
(100, '_event_time', '10:00 AM - 1:00 PM'),
(100, '_event_location', 'Stanhope Hub Workshop Rooms');`
  }
];
