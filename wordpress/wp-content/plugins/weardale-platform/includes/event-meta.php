<?php
/**
 * Event Metadata Management & Editor Interface
 *
 * Handles the rich meta box editor sections, saving, and pre-publishing validation.
 *
 * @package Weardale_Platform
 * @since 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Hook into WordPress meta boxes
 */
function weardale_platform_add_event_meta_boxes() {
    add_meta_box(
        'weardale_event_details_v2',
        __( 'Weardale Event Schedule & Details', 'weardale-platform' ),
        'weardale_platform_render_event_meta_box_v2',
        'weardale_event',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'weardale_platform_add_event_meta_boxes' );

/**
 * Render the rich custom schedule and details editor
 */
function weardale_platform_render_event_meta_box_v2( $post ) {
    wp_nonce_field( 'weardale_event_meta_nonce_action', 'weardale_event_meta_nonce_field' );

    // Retrieve existing saved metadata
    $meta = weardale_platform_get_event_meta_full( $post->ID );
    $timezone = wp_timezone();
    $timezone_name = $timezone->getName();
    ?>
    <style>
        .wt-meta-wrapper {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            background: #fcfcfc;
            border-radius: 4px;
        }
        .wt-tabs-header {
            display: flex;
            border-bottom: 2px solid #e2e8f0;
            background: #f1f5f9;
            margin: -12px -12px 1.5rem -12px;
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
        }
        .wt-tab-btn {
            padding: 1rem 1.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            color: #475569;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .wt-tab-btn:hover {
            color: #1e293b;
            background: rgba(0,0,0,0.02);
        }
        .wt-tab-btn.active {
            color: #3b5c3a;
            border-bottom-color: #3b5c3a;
            background: #fff;
        }
        .wt-tab-content {
            display: none;
            padding: 0.5rem 0;
        }
        .wt-tab-content.active {
            display: block;
        }
        .wt-form-grid {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 1.5rem;
            align-items: baseline;
            margin-bottom: 1.25rem;
        }
        .wt-form-grid label {
            font-weight: 600;
            color: #1e293b;
        }
        .wt-form-grid-full {
            grid-column: 1 / -1;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 1.25rem;
            margin-top: 0.5rem;
        }
        .wt-help-desc {
            font-size: 0.85rem;
            color: #64748b;
            margin: 4px 0 0 0;
            line-height: 1.4;
        }
        .wt-checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500 !important;
            cursor: pointer;
        }
        .wt-timezone-badge {
            display: inline-block;
            background: #e2e8f0;
            color: #334155;
            padding: 0.2rem 0.5rem;
            font-size: 0.8rem;
            font-weight: bold;
            border-radius: 4px;
            font-family: monospace;
        }
    </style>

    <div class="wt-meta-wrapper">
        <!-- Navigation Tabs -->
        <div class="wt-tabs-header">
            <button type="button" class="wt-tab-btn active" data-tab="tab-when">📅 <?php esc_html_e( 'When', 'weardale-platform' ); ?></button>
            <button type="button" class="wt-tab-btn" data-tab="tab-where">📍 <?php esc_html_e( 'Where', 'weardale-platform' ); ?></button>
            <button type="button" class="wt-tab-btn" data-tab="tab-who">👥 <?php esc_html_e( 'Who is it for?', 'weardale-platform' ); ?></button>
            <button type="button" class="wt-tab-btn" data-tab="tab-booking">🪙 <?php esc_html_e( 'Booking & Costs', 'weardale-platform' ); ?></button>
        </div>

        <!-- TAB 1: WHEN -->
        <div id="tab-when" class="wt-tab-content active">
            
            <div class="wt-form-grid">
                <label><?php esc_html_e( 'Timezone display', 'weardale-platform' ); ?></label>
                <div>
                    <span class="wt-timezone-badge"><?php echo esc_html( $timezone_name ); ?></span>
                    <p class="wt-help-desc"><?php esc_html_e( 'System-wide timezone configured in WordPress Settings.', 'weardale-platform' ); ?></p>
                </div>
            </div>

            <div class="wt-form-grid">
                <label for="event_date"><?php esc_html_e( 'Start Date *', 'weardale-platform' ); ?></label>
                <div>
                    <input type="date" id="event_date" name="event_date" value="<?php echo esc_attr( $meta['start_date'] ); ?>" class="regular-text" style="width: 200px;" required>
                    <p class="wt-help-desc"><?php esc_html_e( 'Date of the first occurrence.', 'weardale-platform' ); ?></p>
                </div>
            </div>

            <div class="wt-form-grid">
                <label for="event_all_day"><?php esc_html_e( 'All-Day Event', 'weardale-platform' ); ?></label>
                <div>
                    <label class="wt-checkbox-label">
                        <input type="checkbox" id="event_all_day" name="event_all_day" value="1" <?php checked( $meta['all_day'], '1' ); ?>>
                        <?php esc_html_e( 'This activity runs all day or is multi-day without precise operating hours.', 'weardale-platform' ); ?>
                    </label>
                </div>
            </div>

            <div class="wt-form-grid wt-time-fields" style="<?php echo $meta['all_day'] ? 'display:none;' : ''; ?>">
                <label for="event_start_time"><?php esc_html_e( 'Start & End Time', 'weardale-platform' ); ?></label>
                <div style="display: flex; gap: 0.75rem; align-items: center;">
                    <input type="time" id="event_start_time" name="event_start_time" value="<?php echo esc_attr( $meta['start_time'] ); ?>" style="width: 140px;">
                    <span><?php esc_html_e( 'to', 'weardale-platform' ); ?></span>
                    <input type="time" id="event_end_time" name="event_end_time" value="<?php echo esc_attr( $meta['end_time'] ); ?>" style="width: 140px;">
                </div>
            </div>

            <div class="wt-form-grid">
                <label for="event_end_date"><?php esc_html_e( 'End Date', 'weardale-platform' ); ?></label>
                <div>
                    <input type="date" id="event_end_date" name="event_end_date" value="<?php echo esc_attr( $meta['end_date'] ); ?>" class="regular-text" style="width: 200px;">
                    <p class="wt-help-desc"><?php esc_html_e( 'Defaults to Start Date. For multi-day single-occurrence range (e.g. weekend camp), set this to the final day.', 'weardale-platform' ); ?></p>
                </div>
            </div>

            <div class="wt-form-grid">
                <label for="event_is_recurring"><?php esc_html_e( 'Recurring Event', 'weardale-platform' ); ?></label>
                <div>
                    <label class="wt-checkbox-label">
                        <input type="checkbox" id="event_is_recurring" name="event_is_recurring" value="1" <?php checked( $meta['is_recurring'], '1' ); ?>>
                        <strong><?php esc_html_e( 'This is a repeating event', 'weardale-platform' ); ?></strong>
                    </label>
                </div>
            </div>

            <!-- Recurring subpanel -->
            <div class="wt-form-grid-full wt-recurring-panel" style="<?php echo $meta['is_recurring'] ? '' : 'display:none;'; ?>">
                <h4 style="margin-top: 0; color: #3b5c3a; border-bottom: 1px solid #cbd5e1; padding-bottom: 0.5rem; margin-bottom: 1rem;">🔁 <?php esc_html_e( 'Recurrence Pattern Rules', 'weardale-platform' ); ?></h4>
                
                <div class="wt-form-grid">
                    <label for="event_recurrence_mode"><?php esc_html_e( 'Repeat Frequency', 'weardale-platform' ); ?></label>
                    <select id="event_recurrence_mode" name="event_recurrence_mode">
                        <option value="daily" <?php selected( $meta['recurrence_mode'], 'daily' ); ?>><?php esc_html_e( 'Daily', 'weardale-platform' ); ?></option>
                        <option value="weekly" <?php selected( $meta['recurrence_mode'], 'weekly' ); ?>><?php esc_html_e( 'Weekly', 'weardale-platform' ); ?></option>
                        <option value="monthly" <?php selected( $meta['recurrence_mode'], 'monthly' ); ?>><?php esc_html_e( 'Monthly', 'weardale-platform' ); ?></option>
                    </select>
                </div>

                <div class="wt-form-grid">
                    <label for="event_recurrence_interval"><?php esc_html_e( 'Repeat Interval', 'weardale-platform' ); ?></label>
                    <div>
                        <span><?php esc_html_e( 'Every', 'weardale-platform' ); ?></span>
                        <input type="number" id="event_recurrence_interval" name="event_recurrence_interval" value="<?php echo esc_attr( $meta['recurrence_interval'] ); ?>" min="1" max="100" style="width: 60px; text-align: center;">
                        <span id="wt-interval-unit">days/weeks/months</span>
                    </div>
                </div>

                <!-- Weekly Specific Days -->
                <div class="wt-form-grid wt-weekly-days" style="<?php echo $meta['recurrence_mode'] === 'weekly' ? '' : 'display:none;'; ?>">
                    <label><?php esc_html_e( 'On Weekdays', 'weardale-platform' ); ?></label>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <?php
                        $days = array(
                            'monday'    => __( 'Mon', 'weardale-platform' ),
                            'tuesday'   => __( 'Tue', 'weardale-platform' ),
                            'wednesday' => __( 'Wed', 'weardale-platform' ),
                            'thursday'  => __( 'Thu', 'weardale-platform' ),
                            'friday'    => __( 'Fri', 'weardale-platform' ),
                            'saturday'  => __( 'Sat', 'weardale-platform' ),
                            'sunday'    => __( 'Sun', 'weardale-platform' ),
                        );
                        $saved_days = is_array($meta['recurrence_weekdays']) ? $meta['recurrence_weekdays'] : array();
                        foreach ( $days as $day_slug => $day_label ) :
                            $is_checked = in_array( $day_slug, $saved_days );
                        ?>
                            <label class="wt-checkbox-label" style="font-weight: normal !important;">
                                <input type="checkbox" name="event_recurrence_weekdays[]" value="<?php echo esc_attr( $day_slug ); ?>" <?php checked( $is_checked ); ?>>
                                <?php echo esc_html( $day_label ); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Monthly Specific Option -->
                <div class="wt-form-grid wt-monthly-option" style="<?php echo $meta['recurrence_mode'] === 'monthly' ? '' : 'display:none;'; ?>">
                    <label for="event_recurrence_monthly_type"><?php esc_html_e( 'Monthly Pattern', 'weardale-platform' ); ?></label>
                    <select id="event_recurrence_monthly_type" name="event_recurrence_monthly_type">
                        <option value="day_of_month" <?php selected( $meta['recurrence_monthly_type'], 'day_of_month' ); ?>><?php esc_html_e( 'Same day of the month (e.g. the 14th)', 'weardale-platform' ); ?></option>
                        <option value="relative_weekday" <?php selected( $meta['recurrence_monthly_type'], 'relative_weekday' ); ?>><?php esc_html_e( 'Relative weekday (e.g. Second Tuesday)', 'weardale-platform' ); ?></option>
                    </select>
                </div>

                <!-- End Condition -->
                <div class="wt-form-grid" style="border-top: 1px dashed #e2e8f0; padding-top: 1rem; margin-top: 1rem;">
                    <label><?php esc_html_e( 'Recurrence End *', 'weardale-platform' ); ?></label>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <label class="wt-checkbox-label" style="font-weight: normal !important;">
                            <input type="radio" name="event_recurrence_end_type" value="date" <?php checked( $meta['recurrence_end_type'], 'date' ); ?>>
                            <?php esc_html_e( 'End on date:', 'weardale-platform' ); ?>
                            <input type="date" id="event_recurrence_end_date" name="event_recurrence_end_date" value="<?php echo esc_attr( $meta['recurrence_end_date'] ); ?>" style="width: 170px; margin-left: 0.5rem;">
                        </label>
                        
                        <label class="wt-checkbox-label" style="font-weight: normal !important;">
                            <input type="radio" name="event_recurrence_end_type" value="count" <?php checked( $meta['recurrence_end_type'], 'count' ); ?>>
                            <?php esc_html_e( 'End after', 'weardale-platform' ); ?>
                            <input type="number" id="event_recurrence_end_count" name="event_recurrence_end_count" value="<?php echo esc_attr( $meta['recurrence_end_count'] ); ?>" min="1" max="250" style="width: 70px; text-align: center; margin: 0 0.25rem;">
                            <?php esc_html_e( 'occurrences (Max 250)', 'weardale-platform' ); ?>
                        </label>
                    </div>
                </div>

                <!-- Summary output -->
                <?php if ( ! empty( $meta['recurrence_summary'] ) ) : ?>
                    <div style="background: rgba(107, 143, 94, 0.08); border-left: 4px solid #3b5c3a; padding: 0.75rem 1rem; border-radius: 4px; margin-top: 1rem;">
                        <strong><?php esc_html_e( 'Summary Statement:', 'weardale-platform' ); ?></strong>
                        <p style="margin: 4px 0 0 0; font-size: 0.95rem; font-style: italic; color: #1e293b;"><?php echo esc_html( $meta['recurrence_summary'] ); ?></p>
                    </div>
                <?php endif; ?>

            </div>

        </div>

        <!-- TAB 2: WHERE -->
        <div id="tab-where" class="wt-tab-content">
            
            <div class="wt-form-grid">
                <label for="event_is_online"><?php esc_html_e( 'Online Event', 'weardale-platform' ); ?></label>
                <div>
                    <label class="wt-checkbox-label">
                        <input type="checkbox" id="event_is_online" name="event_is_online" value="1" <?php checked( $meta['is_online'], '1' ); ?>>
                        <?php esc_html_e( 'This event takes place online (e.g. Zoom, YouTube)', 'weardale-platform' ); ?>
                    </label>
                </div>
            </div>

            <!-- Physical Location Fields -->
            <div class="wt-physical-location" style="<?php echo $meta['is_online'] ? 'display:none;' : ''; ?>">
                <div class="wt-form-grid">
                    <label for="event_venue_name"><?php esc_html_e( 'Venue Name', 'weardale-platform' ); ?></label>
                    <div>
                        <input type="text" id="event_venue_name" name="event_venue_name" value="<?php echo esc_attr( $meta['venue_name'] ); ?>" placeholder="e.g. Stanhope Hub Community Garden" class="regular-text">
                        <p class="wt-help-desc"><?php esc_html_e( 'The display name of the venue.', 'weardale-platform' ); ?></p>
                    </div>
                </div>

                <div class="wt-form-grid">
                    <label for="event_location"><?php esc_html_e( 'Address / Location *', 'weardale-platform' ); ?></label>
                    <div>
                        <input type="text" id="event_location" name="event_location" value="<?php echo esc_attr( $meta['location_addr'] ); ?>" placeholder="e.g. 12 Front Street, Stanhope, DL13 2TY" class="regular-text">
                        <p class="wt-help-desc"><?php esc_html_e( 'Full physical address details for participants.', 'weardale-platform' ); ?></p>
                    </div>
                </div>

                <div class="wt-form-grid">
                    <label for="event_map_url"><?php esc_html_e( 'Google Maps Link', 'weardale-platform' ); ?></label>
                    <div>
                        <input type="url" id="event_map_url" name="event_map_url" value="<?php echo esc_url( $meta['map_url'] ); ?>" placeholder="e.g. https://maps.google.com/..." class="regular-text">
                        <p class="wt-help-desc"><?php esc_html_e( 'Paste a Google Maps share URL to automatically display a "Get Directions" button!', 'weardale-platform' ); ?></p>
                    </div>
                </div>
            </div>

            <!-- Virtual Joining Fields -->
            <div class="wt-online-location" style="<?php echo $meta['is_online'] ? '' : 'display:none;'; ?>">
                <div class="wt-form-grid">
                    <label for="event_online_url"><?php esc_html_e( 'Online Joining URL', 'weardale-platform' ); ?></label>
                    <div>
                        <input type="url" id="event_online_url" name="event_online_url" value="<?php echo esc_url( $meta['online_url'] ); ?>" placeholder="e.g. https://zoom.us/j/..." class="regular-text">
                        <p class="wt-help-desc"><?php esc_html_e( 'The private joining link (only visible if booking required/recommended or publicly disclosed).', 'weardale-platform' ); ?></p>
                    </div>
                </div>
            </div>

            <div class="wt-form-grid">
                <label for="event_location_notes"><?php esc_html_e( 'Location Notes', 'weardale-platform' ); ?></label>
                <div>
                    <textarea id="event_location_notes" name="event_location_notes" rows="2" class="large-text" placeholder="e.g. Meet behind the greenhouse. Follow the timber signposts."><?php echo esc_textarea( $meta['location_notes'] ); ?></textarea>
                    <p class="wt-help-desc"><?php esc_html_e( 'Helper tips, landmarks, parking options, or instructions to find the exact room.', 'weardale-platform' ); ?></p>
                </div>
            </div>

        </div>

        <!-- TAB 3: WHO IS IT FOR -->
        <div id="tab-who" class="wt-tab-content">
            
            <div class="wt-form-grid">
                <label for="event_audience"><?php esc_html_e( 'Target Audience', 'weardale-platform' ); ?></label>
                <div>
                    <input type="text" id="event_audience" name="event_audience" value="<?php echo esc_attr( $meta['audience'] ); ?>" placeholder="e.g. Isolated seniors, families with toddlers, everyone welcome" class="regular-text">
                    <p class="wt-help-desc"><?php esc_html_e( 'Who is this session primarily organized for?', 'weardale-platform' ); ?></p>
                </div>
            </div>

            <div class="wt-form-grid">
                <label for="event_age_guidance"><?php esc_html_e( 'Age Guidance', 'weardale-platform' ); ?></label>
                <div>
                    <input type="text" id="event_age_guidance" name="event_age_guidance" value="<?php echo esc_attr( $meta['age_guidance'] ); ?>" placeholder="e.g. Accompanied under-12s, Over 65s" class="regular-text">
                    <p class="wt-help-desc"><?php esc_html_e( 'Any specific age range suggestions or safeguarding policies.', 'weardale-platform' ); ?></p>
                </div>
            </div>

            <div class="wt-form-grid">
                <label for="event_capacity"><?php esc_html_e( 'Max Capacity', 'weardale-platform' ); ?></label>
                <div>
                    <input type="number" id="event_capacity" name="event_capacity" value="<?php echo esc_attr( $meta['capacity'] ); ?>" min="1" max="10000" style="width: 100px;">
                    <p class="wt-help-desc"><?php esc_html_e( 'Optional. Limit the number of available slots.', 'weardale-platform' ); ?></p>
                </div>
            </div>

            <div class="wt-form-grid">
                <label for="event_is_family_friendly"><?php esc_html_e( 'Family Friendly', 'weardale-platform' ); ?></label>
                <div>
                    <label class="wt-checkbox-label">
                        <input type="checkbox" id="event_is_family_friendly" name="event_is_family_friendly" value="1" <?php checked( $meta['is_family_friendly'], '1' ); ?>>
                        <?php esc_html_e( 'This is an inclusive, child-safe, and family-friendly environment.', 'weardale-platform' ); ?>
                    </label>
                </div>
            </div>

            <div class="wt-form-grid">
                <label for="event_accessibility"><?php esc_html_e( 'Accessibility Specs', 'weardale-platform' ); ?></label>
                <div>
                    <textarea id="event_accessibility" name="event_accessibility" rows="3" class="large-text" placeholder="e.g. Wheelchair ramp available. Quiet zone active. Accessible toilets. Hearing induction loops."><?php echo esc_textarea( $meta['accessibility'] ); ?></textarea>
                    <p class="wt-help-desc"><?php esc_html_e( 'Detailed physical or sensory accommodations so all residents feel secure and welcomed.', 'weardale-platform' ); ?></p>
                </div>
            </div>

        </div>

        <!-- TAB 4: BOOKING -->
        <div id="tab-booking" class="wt-tab-content">
            
            <div class="wt-form-grid">
                <label for="event_booking_status"><?php esc_html_e( 'Booking Status *', 'weardale-platform' ); ?></label>
                <div>
                    <select id="event_booking_status" name="event_booking_status" style="width: 250px;">
                        <option value="no_booking_required" <?php selected( $meta['booking_status'], 'no_booking_required' ); ?>><?php esc_html_e( 'No Booking Required (Just turn up!)', 'weardale-platform' ); ?></option>
                        <option value="booking_recommended" <?php selected( $meta['booking_status'], 'booking_recommended' ); ?>><?php esc_html_e( 'Booking Recommended', 'weardale-platform' ); ?></option>
                        <option value="booking_required" <?php selected( $meta['booking_status'], 'booking_required' ); ?>><?php esc_html_e( 'Booking Required', 'weardale-platform' ); ?></option>
                        <option value="sold_out" <?php selected( $meta['booking_status'], 'sold_out' ); ?>><?php esc_html_e( 'Sold Out', 'weardale-platform' ); ?></option>
                        <option value="cancelled" <?php selected( $meta['booking_status'], 'cancelled' ); ?>><?php esc_html_e( 'Cancelled', 'weardale-platform' ); ?></option>
                    </select>
                </div>
            </div>

            <div class="wt-form-grid">
                <label for="event_cost"><?php esc_html_e( 'Cost / Price *', 'weardale-platform' ); ?></label>
                <div>
                    <input type="text" id="event_cost" name="event_cost" value="<?php echo esc_attr( $meta['cost_text'] ); ?>" placeholder="e.g. Free (Donations Welcome) or £3 material fee" class="regular-text" required>
                    <p class="wt-help-desc"><?php esc_html_e( 'Price details or voluntary donation guidelines.', 'weardale-platform' ); ?></p>
                </div>
            </div>

            <div class="wt-form-grid">
                <label for="event_booking_url"><?php esc_html_e( 'Ticket Booking Link', 'weardale-platform' ); ?></label>
                <div>
                    <input type="url" id="event_booking_url" name="event_booking_url" value="<?php echo esc_url( $meta['booking_url'] ); ?>" placeholder="e.g. https://eventbrite.co.uk/..." class="regular-text">
                    <p class="wt-help-desc"><?php esc_html_e( 'Ticketing link (e.g. Eventbrite or custom booking provider).', 'weardale-platform' ); ?></p>
                </div>
            </div>

            <div class="wt-form-grid">
                <label for="event_booking_instructions"><?php esc_html_e( 'Booking Instructions', 'weardale-platform' ); ?></label>
                <div>
                    <textarea id="event_booking_instructions" name="event_booking_instructions" rows="2" class="large-text" placeholder="e.g. Please book online in advance or register with Cheryl at the office desk."><?php echo esc_textarea( $meta['booking_instructions'] ); ?></textarea>
                    <p class="wt-help-desc"><?php esc_html_e( 'Add quick steps explaining how people can reserve slots or get in touch.', 'weardale-platform' ); ?></p>
                </div>
            </div>

            <!-- Organiser Contacts -->
            <div class="wt-form-grid-full" style="background: #fafaf9; border-top: 1px solid #e7e5e4;">
                <h4 style="margin-top: 0; color: #78350f;"><?php esc_html_e( 'Representative Contacts (Internal/Public)', 'weardale-platform' ); ?></h4>
                
                <div class="wt-form-grid">
                    <label for="event_organiser_name"><?php esc_html_e( 'Contact Name', 'weardale-platform' ); ?></label>
                    <input type="text" id="event_organiser_name" name="event_organiser_name" value="<?php echo esc_attr( $meta['organiser_name'] ); ?>" placeholder="e.g. Cheryl Thompson" class="regular-text">
                </div>

                <div class="wt-form-grid">
                    <label for="event_organiser_email"><?php esc_html_e( 'Contact Email', 'weardale-platform' ); ?></label>
                    <input type="email" id="event_organiser_email" name="event_organiser_email" value="<?php echo esc_attr( $meta['organiser_email'] ); ?>" placeholder="e.g. cheryl@weardaletogether.co.uk" class="regular-text">
                </div>

                <div class="wt-form-grid">
                    <label for="event_organiser_phone"><?php esc_html_e( 'Contact Telephone', 'weardale-platform' ); ?></label>
                    <input type="tel" id="event_organiser_phone" name="event_organiser_phone" value="<?php echo esc_attr( $meta['organiser_phone'] ); ?>" placeholder="e.g. 01388 528222" class="regular-text">
                </div>
            </div>

        </div>

    </div>

    <!-- Tab, Toggles and Units Javascript -->
    <script>
        (function() {
            // 1. Tab Switching logic
            const tabs = document.querySelectorAll('.wt-tab-btn');
            const contents = document.querySelectorAll('.wt-tab-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = this.getAttribute('data-tab');

                    tabs.forEach(t => t.classList.remove('active'));
                    contents.forEach(c => c.classList.remove('active'));

                    this.classList.add('active');
                    const targetContent = document.getElementById(target);
                    if (targetContent) {
                        targetContent.classList.add('active');
                    }
                });
            });

            // 2. All-Day toggle logic
            const allDayCheckbox = document.getElementById('event_all_day');
            const timeFields = document.querySelector('.wt-time-fields');
            if (allDayCheckbox && timeFields) {
                allDayCheckbox.addEventListener('change', function() {
                    timeFields.style.display = this.checked ? 'none' : 'flex';
                });
            }

            // 3. Online toggle logic
            const onlineCheckbox = document.getElementById('event_is_online');
            const physicalLocation = document.querySelector('.wt-physical-location');
            const onlineLocation = document.querySelector('.wt-online-location');
            if (onlineCheckbox) {
                onlineCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        if (physicalLocation) physicalLocation.style.display = 'none';
                        if (onlineLocation) onlineLocation.style.display = 'block';
                    } else {
                        if (physicalLocation) physicalLocation.style.display = 'block';
                        if (onlineLocation) onlineLocation.style.display = 'none';
                    }
                });
            }

            // 4. Recurring panel toggle logic
            const recurringCheckbox = document.getElementById('event_is_recurring');
            const recurringPanel = document.querySelector('.wt-recurring-panel');
            if (recurringCheckbox && recurringPanel) {
                recurringCheckbox.addEventListener('change', function() {
                    recurringPanel.style.display = this.checked ? 'block' : 'none';
                });
            }

            // 5. Recurrence mode units logic
            const modeSelect = document.getElementById('event_recurrence_mode');
            const unitLabel = document.getElementById('wt-interval-unit');
            const weeklyDays = document.querySelector('.wt-weekly-days');
            const monthlyOption = document.querySelector('.wt-monthly-option');

            function updateRecurrenceUnits() {
                if (!modeSelect || !unitLabel) return;
                const mode = modeSelect.value;
                if (mode === 'daily') {
                    unitLabel.textContent = 'days';
                    if (weeklyDays) weeklyDays.style.display = 'none';
                    if (monthlyOption) monthlyOption.style.display = 'none';
                } else if (mode === 'weekly') {
                    unitLabel.textContent = 'weeks';
                    if (weeklyDays) weeklyDays.style.display = 'grid';
                    if (monthlyOption) monthlyOption.style.display = 'none';
                } else if (mode === 'monthly') {
                    unitLabel.textContent = 'months';
                    if (weeklyDays) weeklyDays.style.display = 'none';
                    if (monthlyOption) monthlyOption.style.display = 'grid';
                }
            }

            if (modeSelect) {
                modeSelect.addEventListener('change', updateRecurrenceUnits);
                updateRecurrenceUnits();
            }
        })();
    </script>
    <?php
}

/**
 * Enhanced Meta box Retrieval Helper returning all detailed fields
 */
function weardale_platform_get_event_meta_full( $post_id ) {
    $meta = array();
    
    // Core timings
    $meta['start_date']                 = get_post_meta( $post_id, '_event_date', true );
    $meta['end_date']                   = get_post_meta( $post_id, '_event_end_date', true );
    $meta['start_time']                 = get_post_meta( $post_id, '_event_start_time', true );
    $meta['end_time']                   = get_post_meta( $post_id, '_event_end_time', true );
    $meta['all_day']                    = get_post_meta( $post_id, '_event_all_day', true ) === '1';
    
    // Recurrence
    $meta['is_recurring']               = get_post_meta( $post_id, '_event_is_recurring', true ) === '1';
    $meta['recurrence_mode']            = get_post_meta( $post_id, '_event_recurrence_mode', true );
    $meta['recurrence_interval']        = get_post_meta( $post_id, '_event_recurrence_interval', true );
    $meta['recurrence_weekdays']        = get_post_meta( $post_id, '_event_recurrence_weekdays', true );
    $meta['recurrence_monthly_type']    = get_post_meta( $post_id, '_event_recurrence_monthly_type', true );
    $meta['recurrence_end_type']        = get_post_meta( $post_id, '_event_recurrence_end_type', true );
    $meta['recurrence_end_date']        = get_post_meta( $post_id, '_event_recurrence_end_date', true );
    $meta['recurrence_end_count']       = get_post_meta( $post_id, '_event_recurrence_end_count', true );
    $meta['recurrence_summary']         = get_post_meta( $post_id, '_event_recurrence_summary', true );

    // Location
    $meta['is_online']                  = get_post_meta( $post_id, '_event_is_online', true ) === '1';
    $meta['online_url']                 = get_post_meta( $post_id, '_event_online_url', true );
    $meta['venue_name']                 = get_post_meta( $post_id, '_event_venue_name', true );
    $meta['location_addr']              = get_post_meta( $post_id, '_event_location', true );
    $meta['map_url']                    = get_post_meta( $post_id, '_event_map_url', true );
    $meta['location_notes']             = get_post_meta( $post_id, '_event_location_notes', true );

    // Audience
    $meta['audience']                   = get_post_meta( $post_id, '_event_audience', true );
    $meta['age_guidance']               = get_post_meta( $post_id, '_event_age_guidance', true );
    $meta['capacity']                   = get_post_meta( $post_id, '_event_capacity', true );
    $meta['is_family_friendly']         = get_post_meta( $post_id, '_event_is_family_friendly', true ) === '1';
    $meta['accessibility']              = get_post_meta( $post_id, '_event_accessibility', true );

    // Booking & Contacts
    $meta['booking_status']             = get_post_meta( $post_id, '_event_booking_status', true );
    $meta['cost_text']                  = get_post_meta( $post_id, '_event_cost', true );
    $meta['booking_url']                = get_post_meta( $post_id, '_event_booking_url', true );
    $meta['booking_instructions']       = get_post_meta( $post_id, '_event_booking_instructions', true );
    $meta['organiser_name']             = get_post_meta( $post_id, '_event_organiser_name', true );
    $meta['organiser_email']            = get_post_meta( $post_id, '_event_organiser_email', true );
    $meta['organiser_phone']            = get_post_meta( $post_id, '_event_organiser_phone', true );

    // Backwards/legacy fallback compatibility fields
    if ( empty( $meta['start_time'] ) ) {
        $meta['start_time'] = '10:00';
    }
    if ( empty( $meta['end_time'] ) ) {
        $meta['end_time'] = '12:00';
    }
    if ( empty( $meta['recurrence_mode'] ) ) {
        $meta['recurrence_mode'] = 'weekly';
    }
    if ( empty( $meta['recurrence_interval'] ) ) {
        $meta['recurrence_interval'] = '1';
    }
    if ( empty( $meta['recurrence_monthly_type'] ) ) {
        $meta['recurrence_monthly_type'] = 'day_of_month';
    }
    if ( empty( $meta['recurrence_end_type'] ) ) {
        $meta['recurrence_end_type'] = 'count';
    }
    if ( empty( $meta['recurrence_end_count'] ) ) {
        $meta['recurrence_end_count'] = '10';
    }
    if ( empty( $meta['booking_status'] ) ) {
        $meta['booking_status'] = ! empty( $meta['booking_url'] ) ? 'booking_required' : 'no_booking_required';
    }

    // Combine legacy contacts if single field is empty
    $meta['organiser_contact'] = get_post_meta( $post_id, '_event_organiser_contact', true );
    if ( empty( $meta['organiser_email'] ) && empty( $meta['organiser_phone'] ) && ! empty( $meta['organiser_contact'] ) ) {
        if ( strpos( $meta['organiser_contact'], '@' ) !== false ) {
            $meta['organiser_email'] = $meta['organiser_contact'];
        } else {
            $meta['organiser_phone'] = $meta['organiser_contact'];
        }
    }

    return $meta;
}

/**
 * Validates post scheduling details before saving/publishing.
 */
function weardale_platform_validate_event_details( $post_id, $data ) {
    $errors = array();
    
    $start_date = sanitize_text_field( $data['event_date'] );
    $end_date   = sanitize_text_field( $data['event_end_date'] );
    $all_day    = isset( $data['event_all_day'] ) ? '1' : '0';
    $start_time = sanitize_text_field( $data['event_start_time'] );
    $end_time   = sanitize_text_field( $data['event_end_time'] );
    
    // 1. Start Date validation
    if ( empty( $start_date ) ) {
        $errors[] = __( 'Start Date is required.', 'weardale-platform' );
    } else {
        $start_timestamp = strtotime( $start_date );
        if ( ! $start_timestamp ) {
            $errors[] = __( 'Start Date format is invalid.', 'weardale-platform' );
        }
    }
    
    // 2. End Date and End Time logic validation
    if ( ! empty( $end_date ) ) {
        $end_timestamp = strtotime( $end_date );
        if ( ! $end_timestamp ) {
            $errors[] = __( 'End Date format is invalid.', 'weardale-platform' );
        } elseif ( $end_timestamp < $start_timestamp ) {
            $errors[] = __( 'End Date cannot precede the Start Date.', 'weardale-platform' );
        } elseif ( $end_date === $start_date && '0' === $all_day && ! empty( $start_time ) && ! empty( $end_time ) ) {
            if ( strtotime( $end_time ) < strtotime( $start_time ) ) {
                $errors[] = __( 'End Time cannot precede the Start Time on the same day.', 'weardale-platform' );
            }
        }
    }
    
    // 3. Recurrence checks
    if ( isset( $data['event_is_recurring'] ) ) {
        $mode = sanitize_text_field( $data['event_recurrence_mode'] );
        $end_type = sanitize_text_field( $data['event_recurrence_end_type'] );
        
        if ( 'weekly' === $mode && ( ! isset( $data['event_recurrence_weekdays'] ) || empty( $data['event_recurrence_weekdays'] ) ) ) {
            $errors[] = __( 'Weekly recurring events require at least one weekday to be selected.', 'weardale-platform' );
        }
        
        if ( 'date' === $end_type ) {
            $rec_end_date = sanitize_text_field( $data['event_recurrence_end_date'] );
            if ( empty( $rec_end_date ) ) {
                $errors[] = __( 'A recurrence end date is required for date-bounded repeating schedules.', 'weardale-platform' );
            } elseif ( strtotime( $rec_end_date ) < $start_timestamp ) {
                $errors[] = __( 'Recurrence end date cannot be earlier than the start date.', 'weardale-platform' );
            }
        } elseif ( 'count' === $end_type ) {
            $count = intval( $data['event_recurrence_end_count'] );
            if ( $count <= 0 || $count > 250 ) {
                $errors[] = __( 'Recurrence limit count must be between 1 and 250.', 'weardale-platform' );
            }
        }
    }
    
    // 4. URL Validations
    if ( ! empty( $data['event_booking_url'] ) && filter_var( $data['event_booking_url'], FILTER_VALIDATE_URL ) === false ) {
        $errors[] = __( 'Booking ticket link is not a valid URL.', 'weardale-platform' );
    }
    if ( ! empty( $data['event_map_url'] ) && filter_var( $data['event_map_url'], FILTER_VALIDATE_URL ) === false ) {
        $errors[] = __( 'External Google Maps link is not a valid URL.', 'weardale-platform' );
    }
    if ( isset( $data['event_is_online'] ) && ! empty( $data['event_online_url'] ) && filter_var( $data['event_online_url'], FILTER_VALIDATE_URL ) === false ) {
        $errors[] = __( 'Online Joining link is not a valid URL.', 'weardale-platform' );
    }
    
    return $errors;
}

/**
 * Handle save metadata and validation hooks
 */
function weardale_platform_save_event_meta_v2( $post_id ) {
    // 1. Safety checks
    if ( ! isset( $_POST['weardale_event_meta_nonce_field'] ) || ! wp_verify_nonce( $_POST['weardale_event_meta_nonce_field'], 'weardale_event_meta_nonce_action' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    if ( get_post_type( $post_id ) !== 'weardale_event' ) {
        return;
    }

    // 2. Check if this is a publication action and perform validations
    $is_publishing = isset( $_POST['original_post_status'] ) && $_POST['original_post_status'] !== 'publish' && isset( $_POST['post_status'] ) && $_POST['post_status'] === 'publish';
    $errors = weardale_platform_validate_event_details( $post_id, $_POST );
    
    if ( ! empty( $errors ) && ( $is_publishing || ( isset( $_POST['post_status'] ) && $_POST['post_status'] === 'publish' ) ) ) {
        // Validation failed, revert to draft status
        // Unhook save_post to avoid infinite loop
        remove_action( 'save_post', 'weardale_platform_save_event_meta_v2' );
        wp_update_post( array(
            'ID'          => $post_id,
            'post_status' => 'draft',
        ) );
        add_action( 'save_post', 'weardale_platform_save_event_meta_v2' );
        
        set_transient( 'weardale_event_errors_' . $post_id, $errors, 45 );
    }

    // 3. Save values
    $start_date = sanitize_text_field( $_POST['event_date'] );
    update_post_meta( $post_id, '_event_date', $start_date );

    $end_date = sanitize_text_field( $_POST['event_end_date'] );
    if ( empty( $end_date ) ) {
        $end_date = $start_date;
    }
    update_post_meta( $post_id, '_event_end_date', $end_date );

    // All Day checkbox
    $all_day = isset( $_POST['event_all_day'] ) ? '1' : '0';
    update_post_meta( $post_id, '_event_all_day', $all_day );

    // Timing Fields
    $start_time = sanitize_text_field( $_POST['event_start_time'] );
    $end_time   = sanitize_text_field( $_POST['event_end_time'] );
    if ( empty( $start_time ) ) {
        $start_time = '10:00';
    }
    if ( empty( $end_time ) ) {
        $end_time = '12:00';
    }
    update_post_meta( $post_id, '_event_start_time', $start_time );
    update_post_meta( $post_id, '_event_end_time', $end_time );

    // Calculate dynamic time_text legacy compatibility string
    if ( $all_day === '1' ) {
        $time_text = __( 'All Day', 'weardale-platform' );
    } else {
        $time_text = date( 'g:i A', strtotime( $start_time ) ) . ' - ' . date( 'g:i A', strtotime( $end_time ) );
    }
    update_post_meta( $post_id, '_event_time', $time_text );

    // Recurrence Toggle
    $is_recurring = isset( $_POST['event_is_recurring'] ) ? '1' : '0';
    update_post_meta( $post_id, '_event_is_recurring', $is_recurring );

    // Recurrence Fields
    $mode = isset( $_POST['event_recurrence_mode'] ) ? sanitize_text_field( $_POST['event_recurrence_mode'] ) : 'daily';
    update_post_meta( $post_id, '_event_recurrence_mode', $mode );

    $interval = isset( $_POST['event_recurrence_interval'] ) ? intval( $_POST['event_recurrence_interval'] ) : 1;
    update_post_meta( $post_id, '_event_recurrence_interval', $interval );

    // Weekly weekdays array
    $weekdays = isset( $_POST['event_recurrence_weekdays'] ) ? array_map( 'sanitize_text_field', $_POST['event_recurrence_weekdays'] ) : array();
    update_post_meta( $post_id, '_event_recurrence_weekdays', $weekdays );

    $monthly_type = isset( $_POST['event_recurrence_monthly_type'] ) ? sanitize_text_field( $_POST['event_recurrence_monthly_type'] ) : 'day_of_month';
    update_post_meta( $post_id, '_event_recurrence_monthly_type', $monthly_type );

    $end_type = isset( $_POST['event_recurrence_end_type'] ) ? sanitize_text_field( $_POST['event_recurrence_end_type'] ) : 'count';
    update_post_meta( $post_id, '_event_recurrence_end_type', $end_type );

    $rec_end_date = isset( $_POST['event_recurrence_end_date'] ) ? sanitize_text_field( $_POST['event_recurrence_end_date'] ) : '';
    update_post_meta( $post_id, '_event_recurrence_end_date', $rec_end_date );

    $rec_end_count = isset( $_POST['event_recurrence_end_count'] ) ? intval( $_POST['event_recurrence_end_count'] ) : 10;
    update_post_meta( $post_id, '_event_recurrence_end_count', $rec_end_count );

    // Generate dynamic plain description summary on save!
    if ( function_exists( 'weardale_platform_generate_recurrence_summary' ) ) {
        $summary = weardale_platform_generate_recurrence_summary( $post_id );
        update_post_meta( $post_id, '_event_recurrence_summary', $summary );
    }

    // Location Fields
    $is_online = isset( $_POST['event_is_online'] ) ? '1' : '0';
    update_post_meta( $post_id, '_event_is_online', $is_online );
    
    $online_url = isset( $_POST['event_online_url'] ) ? esc_url_raw( $_POST['event_online_url'] ) : '';
    update_post_meta( $post_id, '_event_online_url', $online_url );

    $venue_name = isset( $_POST['event_venue_name'] ) ? sanitize_text_field( $_POST['event_venue_name'] ) : '';
    update_post_meta( $post_id, '_event_venue_name', $venue_name );

    $location_addr = isset( $_POST['event_location'] ) ? sanitize_text_field( $_POST['event_location'] ) : '';
    update_post_meta( $post_id, '_event_location', $location_addr );

    $map_url = isset( $_POST['event_map_url'] ) ? esc_url_raw( $_POST['event_map_url'] ) : '';
    update_post_meta( $post_id, '_event_map_url', $map_url );

    $location_notes = isset( $_POST['event_location_notes'] ) ? sanitize_textarea_field( $_POST['event_location_notes'] ) : '';
    update_post_meta( $post_id, '_event_location_notes', $location_notes );

    // Audience Fields
    update_post_meta( $post_id, '_event_audience', isset( $_POST['event_audience'] ) ? sanitize_text_field( $_POST['event_audience'] ) : '' );
    update_post_meta( $post_id, '_event_age_guidance', isset( $_POST['event_age_guidance'] ) ? sanitize_text_field( $_POST['event_age_guidance'] ) : '' );
    update_post_meta( $post_id, '_event_capacity', isset( $_POST['event_capacity'] ) ? intval( $_POST['event_capacity'] ) : '' );
    update_post_meta( $post_id, '_event_is_family_friendly', isset( $_POST['event_is_family_friendly'] ) ? '1' : '0' );
    update_post_meta( $post_id, '_event_accessibility', isset( $_POST['event_accessibility'] ) ? sanitize_textarea_field( $_POST['event_accessibility'] ) : '' );

    // Booking Fields
    update_post_meta( $post_id, '_event_booking_status', isset( $_POST['event_booking_status'] ) ? sanitize_text_field( $_POST['event_booking_status'] ) : 'no_booking_required' );
    update_post_meta( $post_id, '_event_cost', isset( $_POST['event_cost'] ) ? sanitize_text_field( $_POST['event_cost'] ) : '' );
    update_post_meta( $post_id, '_event_booking_url', isset( $_POST['event_booking_url'] ) ? esc_url_raw( $_POST['event_booking_url'] ) : '' );
    update_post_meta( $post_id, '_event_booking_instructions', isset( $_POST['event_booking_instructions'] ) ? sanitize_textarea_field( $_POST['event_booking_instructions'] ) : '' );
    
    // Organiser Fields
    update_post_meta( $post_id, '_event_organiser_name', isset( $_POST['event_organiser_name'] ) ? sanitize_text_field( $_POST['event_organiser_name'] ) : '' );
    update_post_meta( $post_id, '_event_organiser_email', isset( $_POST['event_organiser_email'] ) ? sanitize_email( $_POST['event_organiser_email'] ) : '' );
    update_post_meta( $post_id, '_event_organiser_phone', isset( $_POST['event_organiser_phone'] ) ? sanitize_text_field( $_POST['event_organiser_phone'] ) : '' );
    
    // Store compound legacy organiser string
    $phone = isset( $_POST['event_organiser_phone'] ) ? sanitize_text_field( $_POST['event_organiser_phone'] ) : '';
    $email = isset( $_POST['event_organiser_email'] ) ? sanitize_email( $_POST['event_organiser_email'] ) : '';
    $organiser_contact = $email ?: $phone;
    update_post_meta( $post_id, '_event_organiser_contact', $organiser_contact );

    // 4. Fire occurrence regeneration based on saved parameters!
    if ( function_exists( 'weardale_platform_regenerate_event_occurrences' ) ) {
        weardale_platform_regenerate_event_occurrences( $post_id );
    }
}
add_action( 'save_post', 'weardale_platform_save_event_meta_v2' );

/**
 * Display publishing validation notices in WP Admin
 */
function weardale_platform_display_validation_notices() {
    global $post;
    if ( ! $post || $post->post_type !== 'weardale_event' ) {
        return;
    }
    
    $errors = get_transient( 'weardale_event_errors_' . $post->ID );
    if ( ! empty( $errors ) ) {
        delete_transient( 'weardale_event_errors_' . $post->ID );
        ?>
        <div class="notice notice-error is-dismissible" style="border-left-color: #dc2626; padding: 12px 16px;">
            <p style="margin-top: 0; font-weight: bold; font-size: 1.1rem; color: #b91c1c;">
                ⚠️ <?php esc_html_e( 'Event Save Blocked / Reverted to Draft', 'weardale-platform' ); ?>
            </p>
            <p style="margin-bottom: 0.5rem; font-size: 0.95rem;">
                <?php esc_html_e( 'The scheduling details did not pass validation checks. Please correct the following errors before publishing:', 'weardale-platform' ); ?>
            </p>
            <ul style="list-style-type: square; padding-left: 20px; font-size: 0.925rem; color: #1f2937;">
                <?php foreach ( $errors as $err ) : ?>
                    <li style="margin-bottom: 4px;"><strong><?php echo esc_html( $err ); ?></strong></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
    }
}
add_action( 'admin_notices', 'weardale_platform_display_validation_notices' );
