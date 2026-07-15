<?php
/**
 * Template part for rendering structured event metadata details (sidebar/widget format).
 *
 * @package WordPress
 * @subpackage Weardale_Together
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$post_id = isset( $args['post_id'] ) ? $args['post_id'] : get_the_ID();
$meta = function_exists( 'weardale_platform_get_event_meta' ) 
    ? weardale_platform_get_event_meta( $post_id ) 
    : array();

if ( empty( $meta ) ) {
    return;
}

// Format start date and end date
$formatted_date = ! empty( $meta['start_date'] ) 
    ? date( 'l, F j, Y', strtotime( $meta['start_date'] ) ) 
    : __( 'Date to be announced', 'weardale-together' );

if ( ! empty( $meta['end_date'] ) && $meta['end_date'] !== $meta['start_date'] ) {
    $formatted_date .= ' - ' . date( 'l, F j, Y', strtotime( $meta['end_date'] ) );
}
?>

<div class="event-meta-card" style="
    background-color: var(--color-cream);
    border: 1px solid var(--color-tan);
    border-radius: var(--border-radius-md);
    padding: 2rem;
    box-shadow: var(--shadow-sm);
">
    <h3 style="
        font-family: var(--font-headings);
        font-size: 1.4rem;
        color: var(--color-forest);
        margin: 0 0 1.5rem 0;
        border-bottom: 2px solid var(--color-tan);
        padding-bottom: 0.75rem;
    ">
        <?php esc_html_e( 'Event Information', 'weardale-together' ); ?>
    </h3>

    <!-- Meta Details Grid -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        <!-- Date Parameter -->
        <div style="display: flex; gap: 0.85rem; align-items: flex-start;">
            <div style="font-size: 1.5rem; line-height: 1; opacity: 0.85; flex-shrink: 0; width: 24px; text-align: center;">📅</div>
            <div>
                <h4 style="margin: 0 0 0.25rem 0; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); font-weight: 700;">
                    <?php esc_html_e( 'Date', 'weardale-together' ); ?>
                </h4>
                <p style="margin: 0; font-size: 1rem; color: var(--color-black); font-weight: 600; line-height: 1.4;">
                    <?php echo esc_html( $formatted_date ); ?>
                    <?php if ( $meta['all_day'] ) : ?>
                        <span style="display: block; font-size: 0.85rem; color: var(--color-forest); font-style: italic; font-weight: normal; margin-top: 0.15rem;">
                            (<?php esc_html_e( 'All-Day or Multi-Day Event', 'weardale-together' ); ?>)
                        </span>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <!-- Time Parameter -->
        <?php if ( ! empty( $meta['time_text'] ) && ! $meta['all_day'] ) : ?>
            <div style="display: flex; gap: 0.85rem; align-items: flex-start;">
                <div style="font-size: 1.5rem; line-height: 1; opacity: 0.85; flex-shrink: 0; width: 24px; text-align: center;">🕒</div>
                <div>
                    <h4 style="margin: 0 0 0.25rem 0; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); font-weight: 700;">
                        <?php esc_html_e( 'Time', 'weardale-together' ); ?>
                    </h4>
                    <p style="margin: 0; font-size: 1rem; color: var(--color-black); line-height: 1.4;">
                        <?php echo esc_html( $meta['time_text'] ); ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Location Parameter -->
        <?php if ( ! empty( $meta['location_addr'] ) ) : ?>
            <div style="display: flex; gap: 0.85rem; align-items: flex-start;">
                <div style="font-size: 1.5rem; line-height: 1; opacity: 0.85; flex-shrink: 0; width: 24px; text-align: center;">📍</div>
                <div>
                    <h4 style="margin: 0 0 0.25rem 0; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); font-weight: 700;">
                        <?php esc_html_e( 'Venue & Address', 'weardale-together' ); ?>
                    </h4>
                    <p style="margin: 0; font-size: 1rem; color: var(--color-black); line-height: 1.4; font-weight: 500;">
                        <?php if ( ! empty( $meta['venue_name'] ) && $meta['venue_name'] !== $meta['location_addr'] ) : ?>
                            <strong><?php echo esc_html( $meta['venue_name'] ); ?></strong><br>
                        <?php endif; ?>
                        <?php echo esc_html( $meta['location_addr'] ); ?>
                    </p>
                    <?php if ( ! empty( $meta['map_url'] ) ) : ?>
                        <a href="<?php echo esc_url( $meta['map_url'] ); ?>" target="_blank" rel="noopener noreferrer" style="
                            display: inline-flex;
                            align-items: center;
                            gap: 0.35rem;
                            font-size: 0.875rem;
                            color: var(--color-forest);
                            font-weight: 700;
                            margin-top: 0.5rem;
                            text-decoration: underline;
                        " onmouseover="this.style.color='var(--color-sage)';" onmouseout="this.style.color='var(--color-forest)';">
                            <span>🗺️</span> <?php esc_html_e( 'Get Directions (Google Maps)', 'weardale-together' ); ?> &rarr;
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Cost Parameter -->
        <?php if ( ! empty( $meta['cost_text'] ) ) : ?>
            <div style="display: flex; gap: 0.85rem; align-items: flex-start;">
                <div style="font-size: 1.5rem; line-height: 1; opacity: 0.85; flex-shrink: 0; width: 24px; text-align: center;">🪙</div>
                <div>
                    <h4 style="margin: 0 0 0.25rem 0; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); font-weight: 700;">
                        <?php esc_html_e( 'Cost / Entry Fee', 'weardale-together' ); ?>
                    </h4>
                    <p style="margin: 0; font-size: 1rem; color: var(--color-black); font-weight: 600; line-height: 1.4;">
                        <?php echo esc_html( $meta['cost_text'] ); ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Audience Guidance Parameter -->
        <?php if ( ! empty( $meta['audience'] ) || ! empty( $meta['age_guidance'] ) ) : ?>
            <div style="display: flex; gap: 0.85rem; align-items: flex-start;">
                <div style="font-size: 1.5rem; line-height: 1; opacity: 0.85; flex-shrink: 0; width: 24px; text-align: center;">👥</div>
                <div>
                    <h4 style="margin: 0 0 0.25rem 0; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); font-weight: 700;">
                        <?php esc_html_e( 'Audience', 'weardale-together' ); ?>
                    </h4>
                    <p style="margin: 0; font-size: 0.95rem; color: var(--color-black); line-height: 1.4;">
                        <?php if ( ! empty( $meta['audience'] ) ) : ?>
                            <strong><?php esc_html_e( 'Who:', 'weardale-together' ); ?></strong> <?php echo esc_html( $meta['audience'] ); ?><br>
                        <?php endif; ?>
                        <?php if ( ! empty( $meta['age_guidance'] ) ) : ?>
                            <strong><?php esc_html_e( 'Age Group:', 'weardale-together' ); ?></strong> <?php echo esc_html( $meta['age_guidance'] ); ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Accessibility Parameter -->
        <?php if ( ! empty( $meta['accessibility'] ) ) : ?>
            <div style="display: flex; gap: 0.85rem; align-items: flex-start;">
                <div style="font-size: 1.5rem; line-height: 1; opacity: 0.85; flex-shrink: 0; width: 24px; text-align: center;">♿</div>
                <div>
                    <h4 style="margin: 0 0 0.25rem 0; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); font-weight: 700;">
                        <?php esc_html_e( 'Accessibility', 'weardale-together' ); ?>
                    </h4>
                    <p style="margin: 0; font-size: 0.925rem; color: var(--color-black); line-height: 1.5; font-style: italic; white-space: pre-line;">
                        <?php echo esc_html( $meta['accessibility'] ); ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Booking Status and Reservation Action -->
        <div style="
            border-top: 1px solid var(--color-tan);
            padding-top: 1.5rem;
            margin-top: 0.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        ">
            <div>
                <h4 style="margin: 0 0 0.5rem 0; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); font-weight: 700;">
                    <?php esc_html_e( 'Booking Status', 'weardale-together' ); ?>
                </h4>
                <?php get_template_part( 'template-parts/events/status', null, array( 'status' => $meta['booking_status'], 'size' => 'normal' ) ); ?>
            </div>

            <?php if ( ! empty( $meta['booking_instructions'] ) ) : ?>
                <div style="background-color: rgba(107, 143, 94, 0.06); border-left: 3px solid var(--color-sage); padding: 0.75rem 1rem; border-radius: 0 4px 4px 0;">
                    <h5 style="margin: 0 0 0.25rem 0; font-size: 0.8rem; font-weight: 700; color: var(--color-forest); text-transform: uppercase; letter-spacing: 0.02em;">
                        <?php esc_html_e( 'Booking Instructions', 'weardale-together' ); ?>
                    </h5>
                    <p style="margin: 0; font-size: 0.875rem; color: var(--color-black); line-height: 1.4;">
                        <?php echo esc_html( $meta['booking_instructions'] ); ?>
                    </p>
                </div>
            <?php endif; ?>

            <!-- Action Button -->
            <?php if ( in_array( $meta['booking_status'], array( 'booking_required', 'booking_recommended' ) ) && ! empty( $meta['booking_url'] ) ) : ?>
                <a href="<?php echo esc_url( $meta['booking_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="
                    text-align: center;
                    justify-content: center;
                    width: 100%;
                    padding: 0.75rem 1rem;
                    font-size: 1rem;
                ">
                    <?php esc_html_e( 'Book Tickets / Places', 'weardale-together' ); ?> &rarr;
                </a>
            <?php endif; ?>
        </div>

        <!-- Organiser Information -->
        <?php if ( ! empty( $meta['organiser_name'] ) || ! empty( $meta['organiser_contact'] ) ) : ?>
            <div style="
                border-top: 1px dashed var(--color-tan);
                padding-top: 1.25rem;
                margin-top: 0.25rem;
            ">
                <h4 style="margin: 0 0 0.5rem 0; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); font-weight: 700;">
                    <?php esc_html_e( 'Organised By', 'weardale-together' ); ?>
                </h4>
                <p style="margin: 0; font-size: 0.9rem; color: var(--color-black); line-height: 1.4;">
                    <?php if ( ! empty( $meta['organiser_name'] ) ) : ?>
                        <strong><?php echo esc_html( $meta['organiser_name'] ); ?></strong><br>
                    <?php endif; ?>
                    <?php if ( ! empty( $meta['organiser_contact'] ) ) : ?>
                        <span style="color: var(--text-secondary); font-size: 0.85rem;"><?php echo esc_html( $meta['organiser_contact'] ); ?></span>
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>

    </div>
</div>
