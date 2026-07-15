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

<section id="hub-and-spoke-section" class="section-padding" style="background-color: var(--color-cream); border-bottom: 1px solid var(--color-tan);">
    <div class="container">
        
        <header style="text-align: center; margin-bottom: 4rem;">
            <h2 class="font-display" style="font-size: 2.5rem; margin-bottom: 1rem;"><?php esc_html_e( 'How We Connect', 'weardale-together' ); ?></h2>
            <p style="color: var(--text-secondary); max-width: 600px; margin: 0 auto; font-size: 1.1rem;">
                <?php esc_html_e( 'Weardale Together acts as the central hub supporting a variety of community-rooted activities. Hover or focus on any strand below to step into our different rooms.', 'weardale-together' ); ?>
            </p>
            <div style="width: 50px; height: 3px; background-color: var(--color-forest); margin: 1.5rem auto 0 auto;"></div>
        </header>

        <!-- Desktop Hub-and-Spoke Layout (Shown on md and up) -->
        <div class="hub-spoke-desktop-only" style="display: block; position: relative; width: 100%; max-width: 800px; height: 500px; margin: 0 auto;">
            
            <!-- Connecting lines background (using CSS or absolute vectors) -->
            <svg style="position: absolute; top:0; left:0; width:100%; height:100%; pointer-events:none; z-index: 1;" aria-hidden="true">
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
            <ul class="hub-spoke-list" style="list-style: none; margin: 0; padding: 0;">
                
                <!-- Central Hub (The Brand Anchor) -->
                <li style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 5;">
                    <div style="background-color: var(--color-forest); color: var(--color-cream); width: 180px; height: 180px; border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; border: 4px solid var(--color-white); box-shadow: 0 10px 25px rgba(59, 92, 58, 0.25); padding: 1.5rem;">
                        <span style="font-family: var(--font-headings); font-size: 1.35rem; line-height: 1.2; display: block;">Weardale Together</span>
                        <span style="font-size: 0.8rem; margin-top: 0.5rem; color: var(--color-tan); text-transform: uppercase; letter-spacing: 0.05em;">Community CIC</span>
                    </div>
                </li>

                <!-- Spoke 1: Root & Branch Café (Top Left) -->
                <li style="position: absolute; top: 50px; left: 50px; z-index: 4;">
                    <a href="<?php echo $cafe_url; ?>" class="spoke-node strand-cafe" style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 180px; height: 180px; border-radius: 50%; background-color: var(--color-white); border: 3px solid var(--color-strand-cafe); text-decoration: none; color: var(--text-primary); transition: var(--transition-bounce); box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: center; padding: 1rem;">
                        <span style="font-size: 2rem; margin-bottom: 0.25rem;">☕</span>
                        <span style="font-family: var(--font-headings); font-size: 1.15rem; color: #9E6B3E; line-height: 1.2;">Root & Branch Café</span>
                        <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0.25rem; color: var(--text-light);">Cosy & Welcoming</span>
                    </a>
                </li>

                <!-- Spoke 2: Creative Arts (Top Right) -->
                <li style="position: absolute; top: 50px; right: 50px; z-index: 4;">
                    <a href="<?php echo $creative_url; ?>" class="spoke-node strand-creative" style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 180px; height: 180px; border-radius: 50%; background-color: var(--color-white); border: 3px solid var(--color-strand-creative); text-decoration: none; color: var(--text-primary); transition: var(--transition-bounce); box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: center; padding: 1rem;">
                        <span style="font-size: 2rem; margin-bottom: 0.25rem;">🎨</span>
                        <span style="font-family: var(--font-headings); font-size: 1.15rem; color: #BA7D0C; line-height: 1.2;">Creative Arts</span>
                        <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0.25rem; color: var(--text-light);">Botanical & Seasonal</span>
                    </a>
                </li>

                <!-- Spoke 3: Young People (Bottom Left) -->
                <li style="position: absolute; bottom: 50px; left: 50px; z-index: 4;">
                    <a href="<?php echo $youth_url; ?>" class="spoke-node strand-youth" style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 180px; height: 180px; border-radius: 50%; background-color: var(--color-white); border: 3px solid var(--color-strand-youth); text-decoration: none; color: var(--text-primary); transition: var(--transition-bounce); box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: center; padding: 1rem;">
                        <span style="font-size: 2rem; margin-bottom: 0.25rem;">🌲</span>
                        <span style="font-family: var(--font-headings); font-size: 1.15rem; color: var(--color-strand-youth); line-height: 1.2;">Young People</span>
                        <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0.25rem; color: var(--text-light);">Bold & Energetic</span>
                    </a>
                </li>

                <!-- Spoke 4: Roots & Shoots (Bottom Right) -->
                <li style="position: absolute; bottom: 50px; right: 50px; z-index: 4;">
                    <a href="<?php echo $roots_url; ?>" class="spoke-node strand-shoots" style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 180px; height: 180px; border-radius: 50%; background-color: var(--color-white); border: 3px solid var(--color-strand-shoots); text-decoration: none; color: var(--text-primary); transition: var(--transition-bounce); box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: center; padding: 1rem;">
                        <span style="font-size: 2rem; margin-bottom: 0.25rem;">🧸</span>
                        <span style="font-family: var(--font-headings); font-size: 1.15rem; color: #B2583E; line-height: 1.2;">Roots & Shoots</span>
                        <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0.25rem; color: var(--text-light);">Soft & Unhurried</span>
                    </a>
                </li>

            </ul>

        </div>

        <!-- Mobile Alternative List (Shown only on small screens via CSS query fallback) -->
        <div class="hub-spoke-mobile-fallback" style="display: none; max-width: 480px; margin: 0 auto;">
            <div style="background-color: var(--color-forest); color: var(--color-cream); text-align: center; padding: 1.5rem; border-radius: var(--border-radius-md); margin-bottom: 1.5rem;">
                <h3 style="color: var(--color-cream); margin-bottom: 0.25rem; font-size: 1.35rem;">Weardale Together</h3>
                <span style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">Community CIC</span>
            </div>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <a href="<?php echo $cafe_url; ?>" class="card" style="border-left: 5px solid var(--color-strand-cafe); padding: 1.25rem; text-decoration: none; color: inherit; display: flex; flex-direction: row; align-items: center; gap: 1rem;">
                    <span style="font-size: 1.5rem;">☕</span>
                    <div>
                        <h4 style="margin: 0; color: #9E6B3E; font-size: 1.15rem;">Root & Branch Café</h4>
                        <p style="margin: 0; font-size: 0.85rem; color: var(--text-secondary);">Warm, cosy community kitchen</p>
                    </div>
                </a>
                <a href="<?php echo $creative_url; ?>" class="card" style="border-left: 5px solid var(--color-strand-creative); padding: 1.25rem; text-decoration: none; color: inherit; display: flex; flex-direction: row; align-items: center; gap: 1rem;">
                    <span style="font-size: 1.5rem;">🎨</span>
                    <div>
                        <h4 style="margin: 0; color: #BA7D0C; font-size: 1.15rem;">Creative Arts</h4>
                        <p style="margin: 0; font-size: 0.85rem; color: var(--text-secondary);">Craft, botany & community art</p>
                    </div>
                </a>
                <a href="<?php echo $youth_url; ?>" class="card" style="border-left: 5px solid var(--color-strand-youth); padding: 1.25rem; text-decoration: none; color: inherit; display: flex; flex-direction: row; align-items: center; gap: 1rem;">
                    <span style="font-size: 1.5rem;">🌲</span>
                    <div>
                        <h4 style="margin: 0; color: var(--color-strand-youth); font-size: 1.15rem;">Young People</h4>
                        <p style="margin: 0; font-size: 0.85rem; color: var(--text-secondary);">Forest schools & youth program</p>
                    </div>
                </a>
                <a href="<?php echo $roots_url; ?>" class="card" style="border-left: 5px solid var(--color-strand-shoots); padding: 1.25rem; text-decoration: none; color: inherit; display: flex; flex-direction: row; align-items: center; gap: 1rem;">
                    <span style="font-size: 1.5rem;">🧸</span>
                    <div>
                        <h4 style="margin: 0; color: #B2583E; font-size: 1.15rem;">Roots & Shoots</h4>
                        <p style="margin: 0; font-size: 0.85rem; color: var(--text-secondary);">Early years, play & gentle nurture</p>
                    </div>
                </a>
            </div>
        </div>

    </div>
</section>

<!-- Simple CSS to toggle layout and handle hover states -->
<style>
.spoke-node:hover,
.spoke-node:focus {
    transform: scale(1.08);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}
.spoke-node.strand-cafe:hover, .spoke-node.strand-cafe:focus { border-color: var(--color-forest); background-color: rgba(196, 149, 106, 0.05); }
.spoke-node.strand-creative:hover, .spoke-node.strand-creative:focus { border-color: var(--color-forest); background-color: rgba(232, 160, 32, 0.05); }
.spoke-node.strand-youth:hover, .spoke-node.strand-youth:focus { border-color: var(--color-forest); background-color: rgba(232, 150, 42, 0.05); }
.spoke-node.strand-shoots:hover, .spoke-node.strand-shoots:focus { border-color: var(--color-forest); background-color: rgba(212, 130, 106, 0.05); }

@media (max-width: 820px) {
    .hub-spoke-desktop-only {
        display: none !important;
    }
    .hub-spoke-mobile-fallback {
        display: block !important;
    }
}
</style>
