<?php
/**
 * Template part for rendering event booking status badges.
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$status = isset( $args['status'] ) ? $args['status'] : 'no_booking_required';
$size   = isset( $args['size'] ) ? $args['size'] : 'normal';

$status_config = array(
    'no_booking_required' => array(
        'label' => __( 'No Booking Required', 'weardale-together' ),
        'bg'    => 'rgba(107, 143, 94, 0.1)',
        'color' => 'var(--color-forest)',
        'border' => 'rgba(107, 143, 94, 0.3)',
    ),
    'booking_recommended' => array(
        'label' => __( 'Booking Recommended', 'weardale-together' ),
        'bg'    => 'rgba(107, 143, 94, 0.15)',
        'color' => '#0d9488',
        'border' => 'rgba(13, 148, 136, 0.3)',
    ),
    'booking_required' => array(
        'label' => __( 'Booking Required', 'weardale-together' ),
        'bg'    => 'rgba(37, 99, 235, 0.08)',
        'color' => '#2563eb',
        'border' => 'rgba(37, 99, 235, 0.25)',
    ),
    'sold_out' => array(
        'label' => __( 'Sold Out', 'weardale-together' ),
        'bg'    => 'rgba(234, 88, 12, 0.08)',
        'color' => '#ea580c',
        'border' => 'rgba(234, 88, 12, 0.25)',
    ),
    'cancelled' => array(
        'label' => __( 'Cancelled', 'weardale-together' ),
        'bg'    => 'rgba(220, 38, 38, 0.08)',
        'color' => '#dc2626',
        'border' => 'rgba(220, 38, 38, 0.25)',
    ),
);

$config = isset( $status_config[ $status ] ) ? $status_config[ $status ] : $status_config['no_booking_required'];
$padding = ( 'small' === $size ) ? '0.2rem 0.5rem' : '0.4rem 0.8rem';
$font_size = ( 'small' === $size ) ? '0.75rem' : '0.85rem';
?>

<span class="event-status-badge" style="
    display: inline-flex;
    align-items: center;
    background-color: <?php echo esc_attr( $config['bg'] ); ?>;
    color: <?php echo esc_attr( $config['color'] ); ?>;
    border: 1px solid <?php echo esc_attr( $config['border'] ); ?>;
    padding: <?php echo esc_attr( $padding ); ?>;
    border-radius: 4px;
    font-size: <?php echo esc_attr( $font_size ); ?>;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    line-height: 1;
">
    <?php echo esc_html( $config['label'] ); ?>
</span>
