<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
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
                <span class="custom-logo-fallback" style="background-color: var(--color-forest); border-radius: 50%; width: 44px; height: 44px; display: inline-flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-family: var(--font-headings); font-size: 1.25rem;">WT</span>
                <p class="site-title">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                        <?php bloginfo( 'name' ); ?>
                    </a>
                </p>
                <?php
            }
            ?>
        </div>

        <!-- Accessible Navigation -->
        <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'weardale-together' ); ?>">
            <button id="menu-toggle" class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                <span class="sr-only" style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); border: 0;">Menu</span>
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
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
