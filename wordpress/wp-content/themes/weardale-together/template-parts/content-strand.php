<?php
/**
 * Template part for displaying strand program pages.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$strand = weardale_together_get_current_strand();
$title = get_the_title();
global $post;

// Define strand-specific details
$subtitle = '';
$accent_color = '';
$strand_term = '';
switch ( $strand ) {
    case 'cafe':
        $subtitle = esc_html__( 'A warm, cosy space at the heart of our community.', 'weardale-together' );
        $accent_color = 'var(--color-strand-cafe)';
        $strand_term = 'cafe';
        break;
    case 'youth':
        $subtitle = esc_html__( 'Energetic, youth-led, and outdoor-focused activities.', 'weardale-together' );
        $accent_color = 'var(--color-strand-youth)';
        $strand_term = 'youth';
        break;
    case 'creative':
        $subtitle = esc_html__( 'Rooted and seasonal expressive arts in the North Pennines.', 'weardale-together' );
        $accent_color = 'var(--color-strand-creative)';
        $strand_term = 'creative';
        break;
    case 'shoots':
        $subtitle = esc_html__( 'An unhurried playroom for early years toddlers and carers.', 'weardale-together' );
        $accent_color = 'var(--color-strand-shoots)';
        $strand_term = 'roots-shoots';
        break;
    default:
        $subtitle = '';
        $accent_color = 'var(--color-forest)';
        $strand_term = '';
        break;
}
?>

<!-- Strand Hero Section -->
<section class="strand-hero" style="background-color: var(--bg-strand-overlay); border-bottom: 1px solid var(--color-tan); padding: 4rem 0;">
    <div class="container" style="text-align: center;">
        <span class="badge badge-<?php echo esc_attr( $strand ); ?>" style="margin-bottom: 1rem; font-size: 0.9rem; padding: 0.4rem 1rem;">
            <?php 
            if ( $strand === 'cafe' ) echo 'Root & Branch';
            elseif ( $strand === 'youth' ) echo 'Young People';
            elseif ( $strand === 'creative' ) echo 'Creative Arts';
            elseif ( $strand === 'shoots' ) echo 'Roots & Shoots';
            ?>
        </span>
        <h1 class="entry-title font-display" style="font-size: 3.5rem; margin-top: 0; margin-bottom: 0.5rem; line-height: 1.1;">
            <?php echo esc_html( $title ); ?>
        </h1>
        <?php if ( $subtitle ) : ?>
            <p class="strand-subtitle" style="font-size: 1.25rem; font-family: var(--font-body); color: var(--text-light); max-width: 600px; margin: 0 auto 1.5rem;">
                <?php echo esc_html( $subtitle ); ?>
            </p>
        <?php endif; ?>
        <div style="width: 80px; height: 4px; background-color: <?php echo esc_attr( $accent_color ); ?>; margin: 0 auto;"></div>
    </div>
</section>

<div class="container section-padding">
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 3rem; align-items: start;" class="strand-layout-grid">
        
        <!-- Left Column: Main Editor Content -->
        <div class="strand-main-content">
            <?php if ( has_post_thumbnail() ) : ?>
                <div class="entry-thumbnail" style="margin-bottom: 2rem; border-radius: var(--border-radius-md); overflow: hidden; border: 1px solid var(--color-tan);">
                    <?php the_post_thumbnail( 'large', array( 'style' => 'width:100%; height:auto; display:block;' ) ); ?>
                </div>
            <?php endif; ?>

            <div class="entry-content" style="background-color: var(--color-white); padding: 2.5rem; border: 1px solid var(--color-tan); border-radius: var(--border-radius-md); box-shadow: 0 4px 12px rgba(196,184,154,0.08); font-size: 1.1rem; line-height: 1.7; color: var(--text-primary);">
                <?php
                the_content();

                wp_link_pages( array(
                    'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'weardale-together' ),
                    'after'  => '</div>',
                ) );
                ?>
            </div>
        </div>

        <!-- Right Column: Strand-Specific Side Features -->
        <aside class="strand-sidebar">
            <?php if ( $strand === 'cafe' ) : ?>
                
                <!-- Polaroid Frame Visual -->
                <div style="text-align: center; margin-bottom: 2rem;">
                    <div class="polaroid-frame" style="max-width: 280px; width: 100%;">
                        <div style="width:100%; aspect-ratio:4/3; background-color:var(--color-cream); border:1px solid var(--color-tan); display:flex; align-items:center; justify-content:center; color:var(--text-light); font-size:0.9rem;">
                            [ Cozy Café Image ]
                        </div>
                        <div style="font-family: var(--font-headings); font-size: 1.1rem; color: #9E6B3E; margin-top: 1rem; text-align: center;">Our cozy hearth</div>
                    </div>
                </div>

                <!-- Opening Hours Card -->
                <div class="card" style="margin-bottom: 1.5rem; border-color: var(--color-strand-cafe);">
                    <h3 style="color: #9E6B3E; margin-top: 0; margin-bottom: 0.75rem; font-family: var(--font-headings); font-size: 1.3rem;">Opening Hours</h3>
                    <p style="margin: 0.25rem 0; font-size: 0.95rem;"><strong>Tuesday – Saturday:</strong> 10:00 AM – 3:30 PM</p>
                    <p style="margin: 0.25rem 0; font-size: 0.95rem; color: var(--text-light);"><em>Sunday & Monday: Closed</em></p>
                </div>

                <!-- Food Ethos Card -->
                <div class="card" style="margin-bottom: 1.5rem; border-color: var(--color-strand-cafe);">
                    <h3 style="color: #9E6B3E; margin-top: 0; margin-bottom: 0.75rem; font-family: var(--font-headings); font-size: 1.3rem;">Kitchen Ethos</h3>
                    <p style="margin: 0; font-size: 0.95rem; line-height: 1.5;">
                        We serve simple, home-cooked seasonal meals using organic produce grown in our community gardens. Every brew and bite supports local community connectivity.
                    </p>
                </div>

                <!-- Find Us Card -->
                <div class="card" style="border-color: var(--color-strand-cafe);">
                    <h3 style="color: #9E6B3E; margin-top: 0; margin-bottom: 0.75rem; font-family: var(--font-headings); font-size: 1.3rem;">Find Us</h3>
                    <p style="margin: 0; font-size: 0.95rem; line-height: 1.5;">
                        Located inside the Stanhope Hub. Drop in anytime for warm hospitality and a friendly ear.
                    </p>
                </div>

            <?php elseif ( $strand === 'youth' ) : ?>

                <!-- Collage Card Visual -->
                <div class="collage-card" style="margin-bottom: 2rem;">
                    <h3 style="color: var(--color-strand-youth); margin-top: 0; margin-bottom: 0.75rem; font-family: var(--font-headings); font-size: 1.3rem;">Get Involved!</h3>
                    <p style="margin: 0; font-size: 0.95rem; line-height: 1.5; color: var(--color-black);">
                        Whether you are a local youth wanting to join, a parent looking for Forest School schedules, or a volunteer ready to support us, our door is always open!
                    </p>
                </div>

                <!-- Forest School Signposting Card -->
                <div class="collage-card" style="margin-bottom: 2rem;">
                    <h3 style="color: var(--color-strand-youth); margin-top: 0; margin-bottom: 0.75rem; font-family: var(--font-headings); font-size: 1.3rem;">Forest School</h3>
                    <p style="margin: 0 0 1rem 0; font-size: 0.95rem; line-height: 1.5; color: var(--color-black);">
                        Our woodland explorations foster teamwork, confidence, and real woodland skills. Children engage in tree discovery, safe fire craft, and mud kitchen play.
                    </p>
                    <a href="<?php echo esc_url( home_url( '/forest-school/' ) ); ?>" class="btn btn-primary" style="background-color: var(--color-black); color: var(--color-cream); font-size: 0.9rem; padding: 0.5rem 1rem; width: 100%; text-align: center; display: block; border-radius: var(--border-radius-sm);">Forest School Info</a>
                </div>

                <!-- Activities Card -->
                <div class="collage-card">
                    <h3 style="color: var(--color-strand-youth); margin-top: 0; margin-bottom: 0.75rem; font-family: var(--font-headings); font-size: 1.3rem;">Youth Club</h3>
                    <p style="margin: 0; font-size: 0.95rem; line-height: 1.5; color: var(--color-black);">
                        High-energy spaces for teenagers and children to create, connect, and thrive in an inclusive local setting. Follow us to stay in the loop!
                    </p>
                </div>

            <?php elseif ( $strand === 'creative' ) : ?>

                <!-- Sketch Card: Current Projects -->
                <div class="sketch-card" style="margin-bottom: 2rem;">
                    <h3 style="color: #BA7D0C; margin-top: 0; margin-bottom: 0.75rem; font-family: var(--font-headings); font-size: 1.3rem;">Current Projects</h3>
                    <p style="margin: 0; font-size: 0.95rem; line-height: 1.5;">
                        We are currently focusing on traditional botanical ink drawing, wood carving, and block printing using organic local materials. Workshops run on seasonal rotation.
                    </p>
                </div>

                <!-- Sketch Card: Botanical Roots -->
                <div class="sketch-card">
                    <h3 style="color: #BA7D0C; margin-top: 0; margin-bottom: 0.75rem; font-family: var(--font-headings); font-size: 1.3rem;">Our Botanical Root</h3>
                    <p style="margin: 0; font-size: 0.95rem; line-height: 1.5;">
                        Connecting community wellness with regional arts. We gather local materials from our North Pennines valley to craft tactile expressions of our rural home.
                    </p>
                </div>

            <?php elseif ( $strand === 'shoots' ) : ?>

                <!-- Clay Card: Tactile Rounded curves -->
                <div class="clay-card" style="margin-bottom: 2rem;">
                    <h3 style="color: #B2583E; margin-top: 0; margin-bottom: 0.75rem; font-family: var(--font-headings); font-size: 1.3rem;">What to Expect</h3>
                    <p style="margin: 0; font-size: 0.95rem; line-height: 1.5;">
                        An unhurried early years family playroom. Soft textures, sensory elements, terracotta clay, and gentle music designed for toddlers and their caregivers.
                    </p>
                </div>

                <!-- Clay Card: Family Friendly info -->
                <div class="clay-card">
                    <h3 style="color: #B2583E; margin-top: 0; margin-bottom: 0.75rem; font-family: var(--font-headings); font-size: 1.3rem;">Playroom Sessions</h3>
                    <p style="margin: 0; font-size: 0.95rem; line-height: 1.5;">
                        Our early years playroom offers soft, sensory-focused morning sessions. Drop in with your little ones to explore, mold clay, and connect with other local families in a relaxed, warm setting.
                    </p>
                </div>

            <?php endif; ?>
        </aside>

    </div>
</div>

<!-- Related Events Section -->
<section class="strand-events-section" style="background-color: var(--bg-primary); border-top: 1px solid var(--color-tan); padding: 4rem 0;">
    <div class="container">
        <h2 class="font-display" style="font-size: 2.25rem; color: var(--color-forest); margin-top: 0; margin-bottom: 2rem; text-align: center;">
            Upcoming Activities & Events
        </h2>
        
        <?php
        // Query related events tagged to the active strand
        $query_args = array(
            'limit'             => 3,
            'scope'             => 'upcoming',
            'include_cancelled' => false,
        );

        if ( $strand_term ) {
            $query_args['strand'] = $strand_term;
        }

        $upcoming_occurrences = function_exists( 'weardale_platform_query_occurrences' )
            ? weardale_platform_query_occurrences( $query_args )
            : array();

        if ( ! empty( $upcoming_occurrences ) ) :
            ?>
            <div class="grid grid-3">
                <?php
                foreach ( $upcoming_occurrences as $occ ) :
                    get_template_part( 'template-parts/events/card', null, array( 'occurrence' => $occ ) );
                endforeach;
                ?>
            </div>
        <?php else : ?>
            <div class="empty-state-notice" style="background-color: var(--color-white); border: 1px dashed var(--color-tan); padding: 3rem; text-align: center; border-radius: var(--border-radius-md); max-width: 600px; margin: 0 auto;">
                <svg width="40" height="40" fill="none" stroke="var(--color-tan)" stroke-width="1.5" viewBox="0 0 24 24" style="margin: 0 auto 1rem; display: block;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <p style="font-size: 1.05rem; color: var(--text-light); margin: 0;">
                    <?php esc_html_e( 'There are currently no scheduled events for this programme strand. Please check back soon or sign up to our newsletter for the latest schedules.', 'weardale-together' ); ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</section>
