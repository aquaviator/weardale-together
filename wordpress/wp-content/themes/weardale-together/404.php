<?php
/**
 * The template for displaying 404 pages (not found).
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
    <div class="container section-padding" style="text-align: center;">
        
        <div class="container-narrow" style="background-color: var(--color-white); padding: 4rem 3rem; border: 1px solid var(--color-tan); border-radius: var(--border-radius-md); box-shadow: 0 4px 12px rgba(196,184,154,0.1);">
            <span style="font-size: 6rem; line-height: 1; font-family: var(--font-headings); color: var(--color-strand-shoots); display: block; margin-bottom: 1.5rem;">404</span>
            
            <h1 class="font-display" style="font-size: 2.25rem; color: var(--color-forest); margin-bottom: 1rem;">
                <?php esc_html_e( 'Oh dear! Page Not Found', 'weardale-together' ); ?>
            </h1>
            
            <p style="font-size: 1.15rem; color: var(--text-secondary); margin-bottom: 2.5rem; line-height: 1.6;">
                <?php esc_html_e( 'It seems like you\'ve wandered into a quiet corner of the North Pennines. The page you are looking for has either moved or doesn\'t exist.', 'weardale-together' ); ?>
            </p>
            
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary">
                    <?php esc_html_e( 'Back to Home', 'weardale-together' ); ?>
                </a>
                <a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>" class="btn btn-secondary">
                    <?php esc_html_e( 'Get in touch', 'weardale-together' ); ?>
                </a>
            </div>
        </div>

    </div>
</main>

<?php
get_footer();
