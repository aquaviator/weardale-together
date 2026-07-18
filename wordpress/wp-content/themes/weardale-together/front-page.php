<?php
/**
 * The template for displaying the front page.
 *
 * This template acts as the central homepage hub for Weardale Together.
 * It is structured into reusable, accessible, and Customizer-configurable
 * sections to represent the Approved Information Architecture.
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

    <?php
    // Section 1 – Hero Section
    get_template_part( 'template-parts/homepage/hero' );

    // Section 2 – Hub-and-Spoke Visual (Priority Feature)
    get_template_part( 'template-parts/homepage/hub-and-spoke' );

    // Section 3 – What's Happening Section (Upcoming WT Events)
    get_template_part( 'template-parts/homepage/whats-happening' );

    // Section 4 – Programme Highlights Section (Strand Spotlight Cards)
    get_template_part( 'template-parts/homepage/programme-areas' );

    // Section 4.5 – Community Directory Entry Point
    get_template_part( 'template-parts/homepage/directory-promo' );

    // Brand ornamental divider utilizing Brand Mark
    ?>
    <div class="brand-ornamental-divider">
        <div class="brand-ornamental-divider-icon"></div>
    </div>
    <?php

    // Section 5 – About Weardale Together Section
    get_template_part( 'template-parts/homepage/about' );

    // Section 6 – Latest News Section (Recent blog posts)
    get_template_part( 'template-parts/homepage/news' );

    // Section 7 – Volunteer Section (CTA)
    get_template_part( 'template-parts/homepage/volunteer' );

    // Section 8 – Newsletter Section (Mailchimp area)
    get_template_part( 'template-parts/homepage/newsletter' );
    ?>

</main><!-- #primary-content .site-main -->

<?php
get_footer();
