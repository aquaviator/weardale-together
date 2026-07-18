<?php
/**
 * Template part for the priority Hub-and-Spoke interactive diagram.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */

// Define links for fallback or Customizer overrides
$cafe_url = esc_url( home_url( '/cafe/' ) );
$creative_url = esc_url( home_url( '/creative-arts/' ) );
$youth_url = esc_url( home_url( '/young-people/' ) );
$roots_url = esc_url( home_url( '/roots-shoots/' ) );
$events_url = esc_url( home_url( '/events/' ) );
?>

<section id="hub-and-spoke-section" class="section-padding hub-spoke-section">
    <div class="container">
        
        <header class="hub-spoke-header">
            <h2 class="font-display"><?php esc_html_e( 'How We Connect', 'weardale-together' ); ?></h2>
            <p><?php esc_html_e( 'Weardale Together acts as the central hub supporting a variety of community-rooted activities. Hover or focus on any strand below to step into our different rooms.', 'weardale-together' ); ?></p>
            <div class="hub-spoke-divider"></div>
        </header>

        <!-- Desktop Hub-and-Spoke Layout (Shown on md and up) -->
        <div class="hub-spoke-desktop">
            
            <!-- Connecting lines background (using CSS or absolute vectors) -->
            <svg class="hub-spoke-lines-svg" aria-hidden="true">
                <!-- Diagonal Left-Top Line -->
                <line x1="220" y1="130" x2="400" y2="250" stroke="var(--color-tan)" stroke-width="3" stroke-dasharray="6 4" />
                <!-- Diagonal Right-Top Line -->
                <line x1="580" y1="130" x2="400" y2="250" stroke="var(--color-tan)" stroke-width="3" stroke-dasharray="6 4" />
                <!-- Diagonal Left-Bottom Line -->
                <line x1="220" y1="370" x2="400" y2="250" stroke="var(--color-tan)" stroke-width="3" stroke-dasharray="6 4" />
                <!-- Diagonal Right-Bottom Line -->
                <line x1="580" y1="370" x2="400" y2="250" stroke="var(--color-tan)" stroke-width="3" stroke-dasharray="6 4" />
            </svg>

            <!-- Semantic list for Screen Readers, styled absolutely for desktop -->
            <ul class="hub-spoke-list">
                
                <!-- Central Hub (The Brand Anchor) -->
                <li class="hub-spoke-center-li">
                    <div class="hub-spoke-center-circle">
                        <span>Weardale Together</span>
                        <span>Community CIC</span>
                    </div>
                </li>

                <!-- Spoke 1: Root & Branch Café (Top Left) -->
                <li class="spoke-li-1">
                    <a href="<?php echo $cafe_url; ?>" class="spoke-node strand-cafe">
                        <span>☕</span>
                        <span>Root & Branch Café</span>
                        <span>Cosy & Welcoming</span>
                    </a>
                </li>

                <!-- Spoke 2: Creative Arts (Top Right) -->
                <li class="spoke-li-2">
                    <a href="<?php echo $creative_url; ?>" class="spoke-node strand-creative">
                        <span>🎨</span>
                        <span>Creative Arts</span>
                        <span>Botanical & Seasonal</span>
                    </a>
                </li>

                <!-- Spoke 3: Young People (Bottom Left) -->
                <li class="spoke-li-3">
                    <a href="<?php echo $youth_url; ?>" class="spoke-node strand-youth">
                        <span>🌲</span>
                        <span>Young People</span>
                        <span>Bold & Energetic</span>
                    </a>
                </li>

                <!-- Spoke 4: Roots & Shoots (Bottom Right) -->
                <li class="spoke-li-4">
                    <a href="<?php echo $roots_url; ?>" class="spoke-node strand-shoots">
                        <span>🧸</span>
                        <span>Roots & Shoots</span>
                        <span>Soft & Unhurried</span>
                    </a>
                </li>

            </ul>

        </div>

        <!-- Mobile Alternative List (Shown only on small screens via CSS query fallback) -->
        <div class="hub-spoke-mobile-fallback">
            <div class="hub-spoke-mobile-center">
                <h3>Weardale Together</h3>
                <span>Community CIC</span>
            </div>
            <div class="hub-spoke-mobile-list">
                <a href="<?php echo $cafe_url; ?>" class="card hub-spoke-mobile-card strand-cafe">
                    <span>☕</span>
                    <div>
                        <h4>Root & Branch Café</h4>
                        <p>Warm, cosy community kitchen</p>
                    </div>
                </a>
                <a href="<?php echo $creative_url; ?>" class="card hub-spoke-mobile-card strand-creative">
                    <span>🎨</span>
                    <div>
                        <h4>Creative Arts</h4>
                        <p>Craft, botany & community art</p>
                    </div>
                </a>
                <a href="<?php echo $youth_url; ?>" class="card hub-spoke-mobile-card strand-youth">
                    <span>🌲</span>
                    <div>
                        <h4>Young People</h4>
                        <p>Forest schools & youth program</p>
                    </div>
                </a>
                <a href="<?php echo $roots_url; ?>" class="card hub-spoke-mobile-card strand-shoots">
                    <span>🧸</span>
                    <div>
                        <h4>Roots & Shoots</h4>
                        <p>Early years, play & gentle nurture</p>
                    </div>
                </a>
            </div>
        </div>

    </div>
</section>
