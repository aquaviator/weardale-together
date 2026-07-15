<?php
/**
 * Template part for rendering directory empty state.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$message = isset( $args['message'] ) ? $args['message'] : __( 'No directory entries found matching your criteria. Try widening your filters!', 'weardale-together' );
?>

<div class="wt-directory-empty-state" style="
    text-align: center;
    background-color: var(--color-cream);
    border: 2px dashed var(--color-tan);
    border-radius: var(--border-radius-md);
    padding: 4rem 2rem;
    margin: 2rem 0;
">
    <div style="font-size: 3rem; margin-bottom: 1.5rem;">🔍</div>
    <h3 class="font-display" style="font-size: 1.75rem; color: var(--color-forest); margin: 0 0 1rem 0; font-weight: normal;">
        <?php esc_html_e( 'No Entries Found', 'weardale-together' ); ?>
    </h3>
    <p style="font-size: 1.1rem; line-height: 1.5; color: var(--color-black); max-width: 500px; margin: 0 auto 2rem auto;">
        <?php echo esc_html( $message ); ?>
    </p>
    <a href="<?php echo esc_url( get_post_type_archive_link( 'weardale_directory' ) ); ?>" class="btn btn-primary" style="padding: 0.6rem 1.5rem; text-decoration: none; font-weight: 600;">
        <?php esc_html_e( 'Reset All Filters', 'weardale-together' ); ?>
    </a>
</div>
