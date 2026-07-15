<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <script>document.documentElement.className += ' js';</script>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- WCAG 2.2 Skip Link -->
<a class="skip-link screen-reader-text" href="#primary-content">
    <?php esc_html_e( 'Skip to content', 'weardale-together' ); ?>
</a>

<header id="masthead" class="site-header" role="banner">
    <div class="container header-container">
        
        <!-- Branding area -->
        <div class="site-branding">
            <?php
            if ( has_custom_logo() ) {
                the_custom_logo();
            } else {
                ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" style="text-decoration: none; display: flex; align-items: center; gap: 0.75rem;">
                    <span class="custom-logo-fallback" style="background-color: var(--color-forest); border-radius: 50%; width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; color: var(--color-cream); font-weight: bold; font-family: var(--font-headings); font-size: 1.15rem; transition: var(--transition-smooth); box-shadow: 0 2px 8px rgba(59, 92, 58, 0.15);">WT</span>
                    <p class="site-title" style="margin: 0; color: var(--color-forest); font-family: var(--font-headings); font-size: 1.5rem; line-height: 1; font-weight: normal;">
                        <?php bloginfo( 'name' ); ?>
                    </p>
                </a>
                <?php
            }
            ?>
        </div>

        <!-- Accessible Navigation -->
        <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'weardale-together' ); ?>">
            <button id="menu-toggle" class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="display: inline-block; vertical-align: middle; margin-right: 6px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                <span class="menu-toggle-text" style="font-weight: 700; font-family: var(--font-body); font-size: 1rem; vertical-align: middle;">Menu</span>
            </button>
            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary-menu',
                'menu_id'        => 'primary-menu',
                'container'      => false,
                'fallback_cb'    => 'weardale_together_fallback_menu',
            ) );
            ?>
        </nav>

    </div>
</header>

<?php
// Fallback menu function to ensure links are visible if no menu is configured in WordPress Admin
function weardale_together_fallback_menu() {
    echo '<ul>';
    echo '<li class="current-menu-item"><a href="' . esc_url( home_url( '/' ) ) . '">Home</a></li>';
    echo '<li><a href="' . esc_url( home_url( '/cafe/' ) ) . '">Root & Branch Café</a></li>';
    echo '<li><a href="' . esc_url( home_url( '/young-people/' ) ) . '">Young People</a></li>';
    echo '<li><a href="' . esc_url( home_url( '/creative-arts/' ) ) . '">Creative Arts</a></li>';
    echo '<li><a href="' . esc_url( home_url( '/roots-shoots/' ) ) . '">Roots & Shoots</a></li>';
    echo '<li><a href="' . esc_url( home_url( '/events/' ) ) . '">Events</a></li>';
    echo '<li><a href="' . esc_url( home_url( '/about/' ) ) . '">About WT</a></li>';
    echo '</ul>';
}
?>
