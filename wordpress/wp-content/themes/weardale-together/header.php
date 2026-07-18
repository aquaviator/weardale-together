<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <script>document.documentElement.className += ' js';</script>
    <?php if ( ! ( function_exists( 'has_site_icon' ) && has_site_icon() ) ) : ?>
        <link rel="icon" href="<?php echo esc_url( get_template_directory_uri() . '/assets/branding/wt-monogram.svg' ); ?>" type="image/svg+xml">
        <link rel="apple-touch-icon" href="<?php echo esc_url( get_template_directory_uri() . '/assets/branding/wt-monogram.svg' ); ?>">
    <?php endif; ?>
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
                $logo_landscape = get_template_directory_uri() . '/assets/branding/compact-header-logo.svg';
                $logo_monogram = get_template_directory_uri() . '/assets/branding/wt-monogram.svg';
                ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="site-branding-link">
                    <img src="<?php echo esc_url( $logo_landscape ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="header-logo-landscape">
                    <img src="<?php echo esc_url( $logo_monogram ); ?>" alt="<?php bloginfo( 'name' ); ?> Monogram" class="header-logo-monogram">
                    <div class="site-title-container mobile-only-title">
                        <p class="site-title"><?php bloginfo( 'name' ); ?></p>
                        <p class="site-description"><?php esc_html_e( 'Community Interest Company', 'weardale-together' ); ?></p>
                    </div>
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
    $directory_url = get_post_type_archive_link( 'weardale_directory' ) ?: home_url( '/directory/' );
    $events_url    = get_post_type_archive_link( 'weardale_event' ) ?: home_url( '/whats-on/' );
    echo '<ul>';
    echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">Home</a></li>';
    
    echo '<li class="menu-item-has-children"><a href="#">Our Programmes</a>';
    echo '  <ul class="sub-menu">';
    echo '    <li><a href="' . esc_url( home_url( '/cafe/' ) ) . '">Root & Branch Café</a></li>';
    echo '    <li><a href="' . esc_url( home_url( '/young-people/' ) ) . '">Young People</a></li>';
    echo '    <li><a href="' . esc_url( home_url( '/creative-arts/' ) ) . '">Creative Arts</a></li>';
    echo '    <li><a href="' . esc_url( home_url( '/roots-shoots/' ) ) . '">Roots & Shoots</a></li>';
    echo '  </ul>';
    echo '</li>';
    
    echo '<li class="cta-nav"><a href="' . esc_url( $events_url ) . '">What\'s On</a></li>';
    echo '<li><a href="' . esc_url( $directory_url ) . '">Community Directory</a></li>';
    echo '<li><a href="' . esc_url( home_url( '/about/' ) ) . '">About WT</a></li>';
    
    echo '<li class="menu-item-has-children"><a href="#">Get Involved</a>';
    echo '  <ul class="sub-menu">';
    echo '    <li><a href="' . esc_url( home_url( '/volunteer/' ) ) . '">Volunteer with Us</a></li>';
    echo '    <li><a href="' . esc_url( home_url( '/newsletter/' ) ) . '">Newsletter</a></li>';
    echo '    <li><a href="' . esc_url( home_url( '/contact-us/' ) ) . '">Contact Us</a></li>';
    echo '  </ul>';
    echo '</li>';
    echo '</ul>';
}
?>
