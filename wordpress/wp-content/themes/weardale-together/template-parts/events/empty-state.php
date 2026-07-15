<?php
/**
 * Template part for displaying empty state on events pages.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$message = isset( $args['message'] ) ? $args['message'] : __( 'We are currently planning our next block of creative workshops, kids camps, and café sessions. Check back shortly!', 'weardale-together' );
$button_text = isset( $args['button_text'] ) ? $args['button_text'] : __( 'Enquire About Upcoming Sessions', 'weardale-together' );
?>

<div class="event-empty-state" style="
    background-color: var(--color-cream);
    border: 1px dashed var(--color-tan);
    padding: 4rem 2rem;
    text-align: center;
    border-radius: var(--border-radius-md);
    margin: 2rem 0;
">
    <div style="font-size: 3rem; margin-bottom: 1.5rem; filter: grayscale(0.2);">🌾</div>
    <p style="
        font-size: 1.15rem;
        color: var(--text-secondary);
        margin-bottom: 2rem;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.6;
    ">
        <?php echo esc_html( $message ); ?>
    </p>
    <a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>" class="btn btn-secondary" style="display: inline-block;">
        <?php echo esc_html( $button_text ); ?>
    </a>
</div>
