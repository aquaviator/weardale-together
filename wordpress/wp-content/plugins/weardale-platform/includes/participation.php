<?php
/**
 * Participation & Engagement Module
 *
 * Implements contact forms, volunteer opportunities, newsletter slot,
 * server-side admin-post.php handlers, honeypot validation, and sample data seeding.
 *
 * @package Weardale_Platform
 * @since 1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Initialize sessions for form validation errors and input preservation (No-Op)
 */
function weardale_platform_init_sessions() {
    // Deprecated: Native PHP sessions have been removed to prevent caching, header-sent errors,
    // and session blocking. Short-lived transients with cryptographic UUID tokens are used instead.
}
add_action( 'init', 'weardale_platform_init_sessions' );

/**
 * Validate and resolve enquiry context safely
 *
 * @param string $type Context type (programme, event, directory, story)
 * @param mixed  $id   Context ID or slug
 * @return array|null Validated context information or null if invalid
 */
function weardale_platform_validate_enquiry_context( $type, $id ) {
    if ( empty( $type ) || empty( $id ) ) {
        return null;
    }

    $validated = array(
        'type'  => $type,
        'id'    => $id,
        'title' => '',
        'url'   => '',
    );

    switch ( $type ) {
        case 'programme':
            // Canonical programmes mapping
            $programmes = array(
                'cafe'          => 'Root & Branch Café',
                'youth'         => 'Young People',
                'young-people'  => 'Young People',
                'forest-school' => 'Forest School',
                'creative'      => 'Creative Arts',
                'creative-arts' => 'Creative Arts',
                'shoots'        => 'Roots & Shoots',
                'roots-shoots'  => 'Roots & Shoots',
            );
            if ( isset( $programmes[ $id ] ) ) {
                $validated['title'] = $programmes[ $id ];
                $validated['url']   = home_url( '/' . $id . '/' );
                return $validated;
            }
            break;

        case 'event':
            $post = get_post( $id );
            if ( $post && $post->post_type === 'weardale_event' && $post->post_status === 'publish' ) {
                $validated['title'] = get_the_title( $post );
                $validated['url']   = get_permalink( $post );
                return $validated;
            }
            break;

        case 'directory':
            $post = get_post( $id );
            if ( $post && $post->post_type === 'weardale_directory' && $post->post_status === 'publish' ) {
                $validated['title'] = get_the_title( $post );
                $validated['url']   = get_permalink( $post );
                return $validated;
            }
            break;

        case 'story':
            $post = get_post( $id );
            if ( $post && $post->post_type === 'post' && $post->post_status === 'publish' ) {
                $validated['title'] = get_the_title( $post );
                $validated['url']   = get_permalink( $post );
                return $validated;
            }
            break;

        case 'volunteer':
            $validated['title'] = __( 'Volunteer Registration', 'weardale-platform' );
            $validated['url']   = home_url( '/volunteer-with-us/' );
            return $validated;
    }

    return null;
}

/**
 * Handle form submission from admin-post.php
 */
function weardale_platform_handle_enquiry_submission() {
    // 1. Verify CSRF Nonce (Gracefully redirect back on expired or invalid nonces)
    if ( ! isset( $_POST['weardale_submit_enquiry_nonce'] ) || ! wp_verify_nonce( $_POST['weardale_submit_enquiry_nonce'], 'weardale_submit_enquiry_action' ) ) {
        $feedback_token = wp_generate_uuid4();
        $feedback_data  = array(
            'errors' => array( 'general' => __( 'Security verification expired. Please reload the page and try submitting again.', 'weardale-platform' ) ),
            'input'  => array(),
        );
        set_transient( 'weardale_feedback_' . $feedback_token, $feedback_data, 300 );
        wp_safe_redirect( add_query_arg( 'wt_feedback', $feedback_token, wp_get_referer() ) );
        exit;
    }

    // 2. Anti-spam Honeypot Check (Silently redirect to fool spam bots)
    if ( ! empty( $_POST['wt_honey'] ) ) {
        $success_url = add_query_arg( 'enquiry_success', '1', wp_get_referer() );
        $success_url = remove_query_arg( 'wt_feedback', $success_url );
        wp_safe_redirect( $success_url );
        exit;
    }

    // 3. Extract and sanitize input fields
    $name         = isset( $_POST['wt_name'] ) ? sanitize_text_field( $_POST['wt_name'] ) : '';
    $email        = isset( $_POST['wt_email'] ) ? sanitize_email( $_POST['wt_email'] ) : '';
    $phone        = isset( $_POST['wt_phone'] ) ? sanitize_text_field( $_POST['wt_phone'] ) : '';
    $message      = isset( $_POST['wt_message'] ) ? sanitize_textarea_field( $_POST['wt_message'] ) : '';
    $consent      = isset( $_POST['wt_consent'] ) ? '1' : '0';
    $context_type = isset( $_POST['wt_context_type'] ) ? sanitize_text_field( $_POST['wt_context_type'] ) : '';
    $context_id   = isset( $_POST['wt_context_id'] ) ? sanitize_text_field( $_POST['wt_context_id'] ) : '';

    $interests    = isset( $_POST['wt_interests'] ) ? (array) $_POST['wt_interests'] : array();
    $availability = isset( $_POST['wt_availability'] ) ? (array) $_POST['wt_availability'] : array();

    $interests    = array_map( 'sanitize_text_field', $interests );
    $availability = array_map( 'sanitize_text_field', $availability );

    // 4. Input validation
    $errors = array();
    if ( empty( $name ) ) {
        $errors['wt_name'] = __( 'Please enter your full name.', 'weardale-platform' );
    }
    if ( empty( $email ) || ! is_email( $email ) ) {
        $errors['wt_email'] = __( 'Please enter a valid email address.', 'weardale-platform' );
    }
    if ( empty( trim( $message ) ) ) {
        if ( $context_type === 'volunteer' ) {
            $errors['wt_message'] = __( 'Please tell us a little about yourself or your interests.', 'weardale-platform' );
        } else {
            $errors['wt_message'] = __( 'Please enter your message or enquiry details.', 'weardale-platform' );
        }
    }
    if ( empty( $consent ) ) {
        if ( $context_type === 'volunteer' ) {
            $errors['wt_consent'] = __( 'You must agree that Weardale Together may contact you regarding volunteering opportunities.', 'weardale-platform' );
        } else {
            $errors['wt_consent'] = __( 'You must consent to our privacy terms to submit your message.', 'weardale-platform' );
        }
    }

    // Validate Context if supplied
    if ( ( ! empty( $context_type ) || ! empty( $context_id ) ) && weardale_platform_validate_enquiry_context( $context_type, $context_id ) === null ) {
        $errors['general'] = __( 'The requested enquiry topic is invalid or has expired.', 'weardale-platform' );
    }

    // 5. Check if contact/enquiry system is disabled or unconfigured
    $enquiry_enabled = get_option( 'weardale_enquiry_enabled', 'yes' );
    if ( $context_type === 'volunteer' ) {
        $recipient = get_option( 'weardale_volunteer_email' );
        if ( empty( $recipient ) ) {
            $recipient = get_option( 'weardale_enquiry_recipient' );
        }
    } else {
        $recipient = get_option( 'weardale_enquiry_recipient' );
    }

    if ( $enquiry_enabled !== 'yes' || empty( $recipient ) || ! is_email( $recipient ) ) {
        $errors['general'] = __( 'Enquiries are temporarily disabled or unconfigured on this site. Please try calling or visiting in person.', 'weardale-platform' );
    }

    // Redirect back with validation errors if found (using transients instead of PHP sessions)
    if ( ! empty( $errors ) ) {
        $feedback_token = wp_generate_uuid4();
        $feedback_data  = array(
            'errors' => $errors,
            'input'  => array(
                'name'         => $name,
                'email'        => $email,
                'phone'        => $phone,
                'message'      => $message,
                'context_type' => $context_type,
                'context_id'   => $context_id,
                'interests'    => $interests,
                'availability' => $availability,
            ),
        );
        set_transient( 'weardale_feedback_' . $feedback_token, $feedback_data, 300 ); // 5 minutes expiry

        wp_safe_redirect( add_query_arg( 'wt_feedback', $feedback_token, wp_get_referer() ) );
        exit;
    }

    // 6. Native Rate Limiting (Max 5 submissions per hour per IP)
    $ip            = $_SERVER['REMOTE_ADDR'];
    $transient_key = 'weardale_rate_limit_' . md5( $ip );
    $rate_count    = get_transient( $transient_key );

    if ( $rate_count === false ) {
        set_transient( $transient_key, 1, HOUR_IN_SECONDS );
    } else {
        if ( $rate_count >= 5 ) {
            $feedback_token = wp_generate_uuid4();
            $feedback_data  = array(
                'errors' => array( 'general' => __( 'You have reached the maximum number of enquiries allowed per hour. Please wait a while or contact us directly by phone.', 'weardale-platform' ) ),
                'input'  => array(
                    'name'         => $name,
                    'email'        => $email,
                    'phone'        => $phone,
                    'message'      => $message,
                    'context_type' => $context_type,
                    'context_id'   => $context_id,
                    'interests'    => $interests,
                    'availability' => $availability,
                ),
            );
            set_transient( 'weardale_feedback_' . $feedback_token, $feedback_data, 300 );
            wp_safe_redirect( add_query_arg( 'wt_feedback', $feedback_token, wp_get_referer() ) );
            exit;
        }
        set_transient( $transient_key, $rate_count + 1, HOUR_IN_SECONDS );
    }

    // 7. Compose Email Message
    if ( $context_type === 'volunteer' ) {
        $subject = __( '[Weardale Together] New Volunteer Enquiry', 'weardale-platform' );
        $context_text = __( 'Volunteer Registration Form Submission', 'weardale-platform' );
    } else {
        $subject = sprintf( '[Weardale Together] New %s Enquiry', ucfirst( $context_type ?: 'General' ) );
        $resolved_context = weardale_platform_validate_enquiry_context( $context_type, $context_id );
        $context_text     = 'General Website Enquiry / Contact';
        if ( $resolved_context ) {
            $context_text = sprintf( '%s (Type: %s, ID/Slug: %s)', $resolved_context['title'], ucfirst( $context_type ), $context_id );
        }
    }

    $body  = "Weardale Together - New Website Submission Received\n";
    $body .= "========================================================\n\n";
    $body .= "Enquiry Context: " . esc_html( $context_text ) . "\n";
    $body .= "Name:            " . esc_html( $name ) . "\n";
    $body .= "Email:           " . esc_html( $email ) . "\n";
    if ( ! empty( $phone ) ) {
        $body .= "Telephone:       " . esc_html( $phone ) . "\n";
    }

    if ( $context_type === 'volunteer' ) {
        if ( ! empty( $interests ) ) {
            $body .= "Volunteering Interests:\n - " . implode( "\n - ", $interests ) . "\n\n";
        } else {
            $body .= "Volunteering Interests: None specified\n\n";
        }
        if ( ! empty( $availability ) ) {
            $body .= "Availability:\n - " . implode( "\n - ", $availability ) . "\n\n";
        } else {
            $body .= "Availability: None specified\n\n";
        }
    }

    $body .= "Message Body / About Yourself:\n";
    $body .= "--------------------------------------------------------\n";
    $body .= $message . "\n";
    $body .= "--------------------------------------------------------\n\n";
    $body .= "Submitted from IP: " . esc_html( $ip ) . "\n";
    $body .= "Sent Date/Time:    " . esc_html( current_time( 'mysql' ) ) . "\n";

    $headers   = array();
    $headers[] = 'Content-Type: text/plain; charset=UTF-8';

    // Optional reply-to header
    $reply_to = get_option( 'weardale_enquiry_reply_to', 'yes' );
    if ( $reply_to === 'yes' ) {
        $headers[] = sprintf( 'Reply-To: %s <%s>', $name, $email );
    }

    // 8. Deliver Email via wp_mail()
    $mail_sent = wp_mail( $recipient, $subject, $body, $headers );

    if ( $mail_sent ) {
        // Redirect to success anchor (clearing previous feedback param if present)
        $success_url = add_query_arg( 'enquiry_success', '1', wp_get_referer() );
        $success_url = remove_query_arg( 'wt_feedback', $success_url );
        wp_safe_redirect( $success_url );
        exit;
    } else {
        $feedback_token = wp_generate_uuid4();
        $feedback_data  = array(
            'errors' => array( 'general' => __( 'Our mail server failed to deliver your message. Please try calling or emailing us directly instead.', 'weardale-platform' ) ),
            'input'  => array(
                'name'         => $name,
                'email'        => $email,
                'phone'        => $phone,
                'message'      => $message,
                'context_type' => $context_type,
                'context_id'   => $context_id,
                'interests'    => $interests,
                'availability' => $availability,
            ),
        );
        set_transient( 'weardale_feedback_' . $feedback_token, $feedback_data, 300 );
        wp_safe_redirect( add_query_arg( 'wt_feedback', $feedback_token, wp_get_referer() ) );
        exit;
    }
}
add_action( 'admin_post_nopriv_weardale_submit_enquiry', 'weardale_platform_handle_enquiry_submission' );
add_action( 'admin_post_weardale_submit_enquiry', 'weardale_platform_handle_enquiry_submission' );

/**
 * Render inner enquiry form with active errors and old inputs
 */
function weardale_platform_render_contact_form( $atts = array() ) {
    // Parse attributes if called as a shortcode or direct helper
    $args = array();
    if ( is_array( $atts ) ) {
        $args = shortcode_atts( array(
            'type' => 'general',
        ), $atts, 'weardale_contact_form' );
    } else {
        $args = array( 'type' => 'general' );
    }

    $form_type = $args['type'];

    $errors = array();
    $input  = array();

    if ( isset( $_GET['wt_feedback'] ) ) {
        $feedback_token = sanitize_key( $_GET['wt_feedback'] );
        $feedback_data  = get_transient( 'weardale_feedback_' . $feedback_token );
        if ( is_array( $feedback_data ) ) {
            $errors = isset( $feedback_data['errors'] ) ? $feedback_data['errors'] : array();
            $input  = isset( $feedback_data['input'] ) ? $feedback_data['input'] : array();
            delete_transient( 'weardale_feedback_' . $feedback_token );
        }
    }

    $is_success = isset( $_GET['enquiry_success'] ) && $_GET['enquiry_success'] === '1';

    // Determine delivery recipient to verify configuration
    if ( $form_type === 'volunteer' ) {
        $recipient = get_option( 'weardale_volunteer_email' );
        if ( empty( $recipient ) ) {
            $recipient = get_option( 'weardale_enquiry_recipient' );
        }
    } else {
        $recipient = get_option( 'weardale_enquiry_recipient' );
    }
    $enquiry_enabled = get_option( 'weardale_enquiry_enabled', 'yes' );

    ob_start();

    // Check if configuration exists
    if ( $enquiry_enabled !== 'yes' || empty( $recipient ) || ! is_email( $recipient ) ) {
        if ( $form_type === 'volunteer' ) {
            $fallback_email = $recipient ?: get_option( 'weardale_contact_email', 'hello@weardaletogether.org.uk' );
            ?>
            <div class="card" style="background-color: var(--color-cream); border: 2px dashed var(--color-tan); padding: 2.5rem; border-radius: var(--border-radius-md); text-align: center; max-width: 600px; margin: 0 auto;">
                <span style="font-size: 2.5rem; display: block; margin-bottom: 1rem;" role="img" aria-label="Maintenance">🕒</span>
                <h3 class="font-display" style="font-size: 1.5rem; color: var(--color-forest); margin: 0 0 0.5rem 0; font-weight: normal;">
                    <?php esc_html_e( 'Online Volunteer Enquiries Offline', 'weardale-platform' ); ?>
                </h3>
                <p style="margin: 0 0 1.5rem 0; line-height: 1.5; color: var(--text-secondary); font-size: 1rem;">
                    <?php esc_html_e( 'Our online volunteer enquiry system is currently unavailable. However, we are always eager to welcome new volunteers into the Weardale Together family!', 'weardale-platform' ); ?>
                </p>
                <p style="margin: 0; line-height: 1.5; color: var(--text-primary); font-size: 1.05rem; font-weight: bold;">
                    <?php printf( esc_html__( 'Please contact us directly by email at: %s', 'weardale-platform' ), '<a href="mailto:' . esc_attr( $fallback_email ) . '" style="color: var(--color-forest); text-decoration: underline;">' . esc_html( $fallback_email ) . '</a>' ); ?>
                </p>
            </div>
            <?php
        } else {
            ?>
            <div class="card" style="background-color: var(--color-cream); border: 2px dashed var(--color-tan); padding: 2.5rem; border-radius: var(--border-radius-md); text-align: center; max-width: 600px; margin: 0 auto;">
                <span style="font-size: 2.5rem; display: block; margin-bottom: 1rem;" role="img" aria-label="Maintenance">🕒</span>
                <h3 class="font-display" style="font-size: 1.5rem; color: var(--color-forest); margin: 0 0 0.5rem 0; font-weight: normal;">
                    <?php esc_html_e( 'Online Enquiries Temporarily Offline', 'weardale-platform' ); ?>
                </h3>
                <p style="margin: 0; line-height: 1.5; color: var(--text-secondary); font-size: 1rem;">
                    <?php esc_html_e( 'Our online enquiry system is currently undergoing standard maintenance. Please reach out to us by phone or visit us in person at the Stanhope Hub. We appreciate your patience!', 'weardale-platform' ); ?>
                </p>
            </div>
            <?php
        }
        return ob_get_clean();
    }

    // Success State
    if ( $is_success ) {
        if ( $form_type === 'volunteer' ) {
            $conf_message = __( 'Thank you for getting in touch. A member of the Weardale Together team will contact you soon to discuss volunteering opportunities.', 'weardale-platform' );
        } else {
            $conf_message = get_option( 'weardale_enquiry_confirmation', __( 'Thank you for contacting Weardale Together. Your message has been received, and our team of volunteers and local staff will read it shortly. As a small, grassroots community organization, we appreciate your patience and will get back to you as soon as possible.', 'weardale-platform' ) );
        }
        ?>
        <div id="enquiry-success-message" role="status" class="card" style="background-color: #f0fdf4; border: 2px solid #16a34a; padding: 2.5rem; border-radius: var(--border-radius-md); text-align: center; max-width: 600px; margin: 0 auto; box-shadow: var(--shadow-sm);" tabindex="-1">
            <span style="font-size: 2.5rem; display: block; margin-bottom: 1rem;" role="img" aria-label="Success">💚</span>
            <h3 class="font-display" style="font-size: 1.75rem; color: #14532d; margin: 0 0 1rem 0; font-weight: normal;">
                <?php esc_html_e( 'Message Sent Successfully', 'weardale-platform' ); ?>
            </h3>
            <p style="margin: 0; line-height: 1.6; color: #166534; font-size: 1.05rem;">
                <?php echo esc_html( $conf_message ); ?>
            </p>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var msg = document.getElementById('enquiry-success-message');
                if (msg) msg.focus();
            });
        </script>
        <?php
        return ob_get_clean();
    }

    // Extract Context
    if ( $form_type === 'volunteer' ) {
        $type = 'volunteer';
        $id   = 'general';
    } else {
        $type = isset( $input['context_type'] ) ? $input['context_type'] : ( isset( $_GET['enquiry_type'] ) ? sanitize_text_field( $_GET['enquiry_type'] ) : '' );
        $id   = isset( $input['context_id'] ) ? $input['context_id'] : ( isset( $_GET['enquiry_id'] ) ? sanitize_text_field( $_GET['enquiry_id'] ) : '' );
    }

    $validated             = weardale_platform_validate_enquiry_context( $type, $id );
    $display_context_title = ( $validated && $form_type !== 'volunteer' ) ? $validated['title'] : '';

    $old_name    = isset( $input['name'] ) ? esc_attr( $input['name'] ) : '';
    $old_email   = isset( $input['email'] ) ? esc_attr( $input['email'] ) : '';
    $old_phone   = isset( $input['phone'] ) ? esc_attr( $input['phone'] ) : '';
    $old_message = isset( $input['message'] ) ? esc_textarea( $input['message'] ) : '';
    ?>
    <div class="weardale-enquiry-form-wrapper" style="width: 100%;">
        
        <!-- Error summary block for accessibility -->
        <?php if ( ! empty( $errors ) ) : ?>
            <div id="enquiry-error-summary" role="alert" class="card" style="background-color: #fef2f2; border: 2px solid #ef4444; padding: 1.5rem; border-radius: var(--border-radius-md); margin-bottom: 2rem; box-shadow: var(--shadow-sm);" tabindex="-1">
                <h3 class="font-display" style="font-size: 1.25rem; color: #991b1b; margin-top: 0; margin-bottom: 0.75rem; font-weight: normal;">
                    ⚠️ <?php esc_html_e( 'Please correct the following errors:', 'weardale-platform' ); ?>
                </h3>
                <ul style="list-style-type: disc; margin-left: 1.5rem; color: #991b1b; font-size: 0.95rem;">
                    <?php foreach ( $errors as $field_id => $err_msg ) : ?>
                        <li>
                            <a href="#<?php echo esc_attr( $field_id ); ?>" style="color: inherit; text-decoration: underline; font-weight: 500;">
                                <?php echo esc_html( $err_msg ); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var summary = document.getElementById('enquiry-error-summary');
                    if (summary) summary.focus();
                });
            </script>
        <?php endif; ?>

        <form id="weardale-enquiry-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" novalidate style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <input type="hidden" name="action" value="weardale_submit_enquiry">
            <?php wp_nonce_field( 'weardale_submit_enquiry_action', 'weardale_submit_enquiry_nonce' ); ?>
            
            <!-- Honeypot -->
            <div style="display: none;" aria-hidden="true">
                <label for="wt_honey"><?php esc_html_e( 'Do not fill this field', 'weardale-platform' ); ?></label>
                <input type="text" name="wt_honey" id="wt_honey" autocomplete="off" tabindex="-1">
            </div>

            <input type="hidden" name="wt_context_type" value="<?php echo esc_attr( $type ); ?>">
            <input type="hidden" name="wt_context_id" value="<?php echo esc_attr( $id ); ?>">

            <!-- Context Banner -->
            <?php if ( ! empty( $display_context_title ) ) : ?>
                <div style="background-color: var(--color-cream); border: 1px solid var(--color-tan); border-radius: var(--border-radius-sm); padding: 1.25rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <div>
                        <span style="font-family: var(--font-mono); font-size: 0.75rem; text-transform: uppercase; color: var(--color-forest); display: block; margin-bottom: 0.25rem; font-weight: bold; letter-spacing: 0.05em;">
                            <?php printf( esc_html__( 'Enquiry Topic (%s)', 'weardale-platform' ), esc_html( ucfirst( $type ) ) ); ?>
                        </span>
                        <strong style="font-family: var(--font-headings); font-size: 1.2rem; color: var(--color-forest); font-weight: normal;">
                            <?php echo esc_html( $display_context_title ); ?>
                        </strong>
                    </div>
                    <a href="<?php echo esc_url( remove_query_arg( array( 'enquiry_type', 'enquiry_id' ) ) ); ?>" class="btn btn-secondary" style="font-size: 0.8rem; padding: 0.3rem 0.75rem; text-decoration: none;">
                        <?php esc_html_e( 'Clear', 'weardale-platform' ); ?>
                    </a>
                </div>
            <?php endif; ?>

            <!-- Name -->
            <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="wt_name" style="font-weight: 600; font-family: var(--font-headings); color: var(--color-forest); font-size: 1.05rem;">
                    <?php esc_html_e( 'Full Name', 'weardale-platform' ); ?> <span style="color: #b91c1c;" aria-hidden="true">*</span>
                </label>
                <input type="text" name="wt_name" id="wt_name" class="input-text" required value="<?php echo $old_name; ?>" 
                       style="padding: 0.75rem 1rem; border: 1px solid var(--color-tan); border-radius: var(--border-radius-sm); font-size: 1rem; width: 100%; background-color: var(--color-white);"
                       <?php echo isset( $errors['wt_name'] ) ? 'aria-invalid="true" aria-describedby="wt_name_error"' : ''; ?>>
                <?php if ( isset( $errors['wt_name'] ) ) : ?>
                    <span id="wt_name_error" style="color: #b91c1c; font-size: 0.875rem; font-weight: 500;">
                        <?php echo esc_html( $errors['wt_name'] ); ?>
                    </span>
                <?php endif; ?>
            </div>

            <!-- Email -->
            <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="wt_email" style="font-weight: 600; font-family: var(--font-headings); color: var(--color-forest); font-size: 1.05rem;">
                    <?php esc_html_e( 'Email Address', 'weardale-platform' ); ?> <span style="color: #b91c1c;" aria-hidden="true">*</span>
                </label>
                <input type="email" name="wt_email" id="wt_email" class="input-text" required value="<?php echo $old_email; ?>"
                       style="padding: 0.75rem 1rem; border: 1px solid var(--color-tan); border-radius: var(--border-radius-sm); font-size: 1rem; width: 100%; background-color: var(--color-white);"
                       <?php echo isset( $errors['wt_email'] ) ? 'aria-invalid="true" aria-describedby="wt_email_error"' : ''; ?>>
                <?php if ( isset( $errors['wt_email'] ) ) : ?>
                    <span id="wt_email_error" style="color: #b91c1c; font-size: 0.875rem; font-weight: 500;">
                        <?php echo esc_html( $errors['wt_email'] ); ?>
                    </span>
                <?php endif; ?>
            </div>

            <!-- Phone -->
            <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="wt_phone" style="font-weight: 600; font-family: var(--font-headings); color: var(--color-forest); font-size: 1.05rem;">
                    <?php esc_html_e( 'Telephone Number', 'weardale-platform' ); ?> <span style="font-weight: normal; color: var(--text-light); font-size: 0.9rem; font-family: var(--font-body);">(<?php esc_html_e( 'Optional', 'weardale-platform' ); ?>)</span>
                </label>
                <input type="tel" name="wt_phone" id="wt_phone" class="input-text" value="<?php echo $old_phone; ?>"
                       style="padding: 0.75rem 1rem; border: 1px solid var(--color-tan); border-radius: var(--border-radius-sm); font-size: 1rem; width: 100%; background-color: var(--color-white);">
            </div>

            <?php if ( $form_type === 'volunteer' ) : ?>
                <!-- Volunteering Interests -->
                <?php
                $default_interests = array(
                    'cafe'         => __( 'Café Support', 'weardale-platform' ),
                    'arts'         => __( 'Creative Arts', 'weardale-platform' ),
                    'youth'        => __( 'Young People', 'weardale-platform' ),
                    'events'       => __( 'Events', 'weardale-platform' ),
                    'gardening'    => __( 'Gardening', 'weardale-platform' ),
                    'admin'        => __( 'Administration', 'weardale-platform' ),
                    'transport'    => __( 'Driving / Transport', 'weardale-platform' ),
                    'community'    => __( 'General Community Support', 'weardale-platform' ),
                    'discuss'      => __( 'Happy to discuss opportunities', 'weardale-platform' ),
                );
                $interests_options = apply_filters( 'weardale_volunteer_interests', $default_interests );
                $old_interests = isset( $input['interests'] ) ? (array) $input['interests'] : array();
                ?>
                <div class="form-group" style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <span id="wt_interests_label" style="font-weight: 600; font-family: var(--font-headings); color: var(--color-forest); font-size: 1.05rem; display: block;">
                        <?php esc_html_e( 'Volunteering Interests', 'weardale-platform' ); ?>
                    </span>
                    <div class="checkbox-grid" style="display: grid; grid-template-columns: 1fr; gap: 0.75rem;" role="group" aria-labelledby="wt_interests_label">
                        <style>
                            @media (min-width: 640px) {
                                .checkbox-grid {
                                    grid-template-columns: 1fr 1fr !important;
                                }
                            }
                        </style>
                        <?php foreach ( $interests_options as $key => $label ) : ?>
                            <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.95rem; cursor: pointer; color: var(--text-primary);">
                                <input type="checkbox" name="wt_interests[]" value="<?php echo esc_attr( $label ); ?>" <?php checked( in_array( $label, $old_interests ) ); ?>>
                                <span><?php echo esc_html( $label ); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Availability -->
                <?php
                $default_availability = array(
                    'weekdays' => __( 'Weekdays', 'weardale-platform' ),
                    'evenings' => __( 'Evenings', 'weardale-platform' ),
                    'weekends' => __( 'Weekends', 'weardale-platform' ),
                    'flexible' => __( 'Flexible', 'weardale-platform' ),
                );
                $availability_options = apply_filters( 'weardale_volunteer_availability', $default_availability );
                $old_availability = isset( $input['availability'] ) ? (array) $input['availability'] : array();
                ?>
                <div class="form-group" style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <span id="wt_availability_label" style="font-weight: 600; font-family: var(--font-headings); color: var(--color-forest); font-size: 1.05rem; display: block;">
                        <?php esc_html_e( 'Availability', 'weardale-platform' ); ?>
                    </span>
                    <div class="availability-grid" style="display: grid; grid-template-columns: 1fr; gap: 0.75rem;" role="group" aria-labelledby="wt_availability_label">
                        <style>
                            @media (min-width: 640px) {
                                .availability-grid {
                                    grid-template-columns: 1fr 1fr 1fr 1fr !important;
                                }
                            }
                        </style>
                        <?php foreach ( $availability_options as $key => $label ) : ?>
                            <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.95rem; cursor: pointer; color: var(--text-primary);">
                                <input type="checkbox" name="wt_availability[]" value="<?php echo esc_attr( $label ); ?>" <?php checked( in_array( $label, $old_availability ) ); ?>>
                                <span><?php echo esc_html( $label ); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Message -->
            <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="wt_message" style="font-weight: 600; font-family: var(--font-headings); color: var(--color-forest); font-size: 1.05rem;">
                    <?php 
                    if ( $form_type === 'volunteer' ) {
                        esc_html_e( 'Tell us about yourself', 'weardale-platform' );
                    } else {
                        esc_html_e( 'Your Message', 'weardale-platform' );
                    }
                    ?> <span style="color: #b91c1c;" aria-hidden="true">*</span>
                </label>
                <?php if ( $form_type === 'volunteer' ) : ?>
                    <p style="margin: 0; font-size: 0.9rem; color: var(--text-secondary); line-height: 1.4;">
                        <?php esc_html_e( "Tell us a little about yourself, your interests, or any experience you'd like to share.", 'weardale-platform' ); ?>
                    </p>
                <?php endif; ?>
                <textarea name="wt_message" id="wt_message" rows="5" class="input-textarea" required
                          style="padding: 0.75rem 1rem; border: 1px solid var(--color-tan); border-radius: var(--border-radius-sm); font-size: 1rem; width: 100%; font-family: inherit; line-height: 1.5; background-color: var(--color-white);"
                          <?php echo isset( $errors['wt_message'] ) ? 'aria-invalid="true" aria-describedby="wt_message_error"' : ''; ?>><?php echo $old_message; ?></textarea>
                <?php if ( isset( $errors['wt_message'] ) ) : ?>
                    <span id="wt_message_error" style="color: #b91c1c; font-size: 0.875rem; font-weight: 500;">
                        <?php echo esc_html( $errors['wt_message'] ); ?>
                    </span>
                <?php endif; ?>
            </div>

            <!-- Consent -->
            <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label class="wd-checkbox-label" style="display: flex; align-items: flex-start; gap: 0.75rem; cursor: pointer; font-size: 0.95rem; line-height: 1.45;">
                    <input type="checkbox" name="wt_consent" id="wt_consent" value="1" required style="margin-top: 0.2rem;"
                           <?php echo isset( $errors['wt_consent'] ) ? 'aria-invalid="true" aria-describedby="wt_consent_error"' : ''; ?>>
                    <div>
                        <strong><?php esc_html_e( 'Consent to Contact', 'weardale-platform' ); ?></strong> <span style="color: #b91c1c;" aria-hidden="true">*</span><br>
                        <span style="color: var(--text-secondary);">
                            <?php 
                            if ( $form_type === 'volunteer' ) {
                                esc_html_e( 'I agree that Weardale Together may contact me regarding volunteering opportunities.', 'weardale-platform' );
                            } else {
                                $privacy_link = weardale_platform_get_legal_page_url( 'weardale_legal_privacy_page' );
                                printf(
                                    wp_kses(
                                        __( 'I agree that Weardale Together may use the details submitted in this form to respond to my enquiry, in accordance with the <a href="%s" target="_blank" style="color: var(--color-forest); text-decoration: underline;">Privacy Notice</a>. (Subject to client/legal approval)', 'weardale-platform' ),
                                        array( 'a' => array( 'href' => array(), 'target' => array(), 'style' => array() ) )
                                    ),
                                    esc_url( $privacy_link )
                                ); 
                            }
                            ?>
                        </span>
                    </div>
                </label>
                <?php if ( isset( $errors['wt_consent'] ) ) : ?>
                    <span id="wt_consent_error" style="color: #b91c1c; font-size: 0.875rem; font-weight: 500; margin-left: 1.75rem;">
                        <?php echo esc_html( $errors['wt_consent'] ); ?>
                    </span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary" style="align-self: flex-start; padding: 0.85rem 2rem; font-size: 1.05rem; font-weight: 700; margin-top: 0.5rem; border-radius: var(--border-radius-sm);">
                <?php esc_html_e( 'Send Enquiry', 'weardale-platform' ); ?>
            </button>

        </form>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'weardale_contact_form', 'weardale_platform_render_contact_form' );

/**
 * Shortcode: Contact Page Layout
 */
function weardale_platform_render_contact_page_layout() {
    ob_start();
    
    $address    = get_option( 'weardale_contact_address' );
    $phone      = get_option( 'weardale_contact_phone' );
    $email      = get_option( 'weardale_contact_email' );
    $hours      = get_option( 'weardale_contact_opening_hours' );
    $directions = get_option( 'weardale_contact_directions' );
    $facebook   = get_option( 'weardale_contact_social_facebook' );
    $instagram  = get_option( 'weardale_contact_social_instagram' );
    ?>
    <div class="weardale-contact-grid" style="display: flex; flex-direction: column; gap: 3rem;">
        <style>
            @media (min-width: 768px) {
                .contact-grid-inner {
                    display: grid !important;
                    grid-template-columns: 5fr 7fr !important;
                    gap: 3.5rem !important;
                }
            }
        </style>
        
        <!-- Warm introductory wording -->
        <div style="max-width: 800px; margin-bottom: 1rem;">
            <p style="font-size: 1.15rem; line-height: 1.6; color: var(--text-primary);">
                <?php esc_html_e( 'Whether you want to ask about our community café, enquire about one of our creative workshops, join a forest session, or explore volunteer openings, we would love to hear from you. Please fill out our online form, give us a call, or drop by our cozy hub in Stanhope.', 'weardale-platform' ); ?>
            </p>
        </div>

        <div class="contact-grid-inner" style="display: flex; flex-direction: column; gap: 2.5rem;">
            
            <!-- Details Column -->
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                
                <div class="card" style="background-color: var(--color-cream); border: 1px solid var(--color-tan); padding: 2rem; border-radius: var(--border-radius-md); display: flex; flex-direction: column; gap: 1.5rem;">
                    
                    <h3 class="font-display" style="font-size: 1.5rem; color: var(--color-forest); margin: 0 0 0.5rem 0; font-weight: normal; border-bottom: 1px solid var(--color-tan); padding-bottom: 0.5rem;">
                        <?php esc_html_e( 'Contact Information', 'weardale-platform' ); ?>
                    </h3>

                    <?php if ( ! empty( $address ) ) : ?>
                        <div>
                            <strong style="display:block; font-size:0.8rem; text-transform:uppercase; color:var(--color-forest); margin-bottom:0.25rem;">📍 <?php esc_html_e( 'Address', 'weardale-platform' ); ?></strong>
                            <div style="line-height: 1.4; color: var(--color-black); white-space: pre-line;"><?php echo esc_html( $address ); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if ( ! empty( $phone ) ) : ?>
                        <div>
                            <strong style="display:block; font-size:0.8rem; text-transform:uppercase; color:var(--color-forest); margin-bottom:0.25rem;">📞 <?php esc_html_e( 'Phone', 'weardale-platform' ); ?></strong>
                            <a href="tel:<?php echo esc_attr( $phone ); ?>" style="color: var(--color-black); text-decoration: none; font-weight:600;"><?php echo esc_html( $phone ); ?></a>
                        </div>
                    <?php endif; ?>

                    <?php if ( ! empty( $email ) ) : ?>
                        <div>
                            <strong style="display:block; font-size:0.8rem; text-transform:uppercase; color:var(--color-forest); margin-bottom:0.25rem;">✉️ <?php esc_html_e( 'Email', 'weardale-platform' ); ?></strong>
                            <a href="mailto:<?php echo esc_attr( $email ); ?>" style="color: var(--color-forest); text-decoration: underline; word-break: break-all;"><?php echo esc_html( $email ); ?></a>
                        </div>
                    <?php endif; ?>

                    <?php if ( ! empty( $hours ) ) : ?>
                        <div>
                            <strong style="display:block; font-size:0.8rem; text-transform:uppercase; color:var(--color-forest); margin-bottom:0.25rem;">🕒 <?php esc_html_e( 'Opening Hours', 'weardale-platform' ); ?></strong>
                            <div style="line-height: 1.4; color: var(--color-black); white-space: pre-line;"><?php echo esc_html( $hours ); ?></div>
                        </div>
                    <?php endif; ?>

                </div>

                <?php if ( ! empty( $directions ) ) : ?>
                    <div class="card" style="background-color: var(--color-white); border: 1px solid var(--color-tan); padding: 2rem; border-radius: var(--border-radius-md);">
                        <h3 class="font-display" style="font-size: 1.3rem; color: var(--color-forest); margin-top: 0; margin-bottom: 0.75rem; font-weight: normal;">
                            🗺️ <?php esc_html_e( 'Directions', 'weardale-platform' ); ?>
                        </h3>
                        <p style="margin: 0; font-size: 0.95rem; line-height: 1.5; color: var(--text-secondary);">
                            <?php echo esc_html( $directions ); ?>
                        </p>
                    </div>
                <?php endif; ?>

                <?php if ( ! empty( $facebook ) || ! empty( $instagram ) ) : ?>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <span style="font-weight: 600; font-size: 0.9rem; color: var(--text-light);"><?php esc_html_e( 'Follow Us:', 'weardale-platform' ); ?></span>
                        <?php if ( ! empty( $facebook ) ) : ?>
                            <a href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener" class="btn btn-secondary" style="padding: 0.4rem 1rem; font-size: 0.85rem; text-decoration: none;">👥 Facebook</a>
                        <?php endif; ?>
                        <?php if ( ! empty( $instagram ) ) : ?>
                            <a href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener" class="btn btn-secondary" style="padding: 0.4rem 1rem; font-size: 0.85rem; text-decoration: none;">📸 Instagram</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Form Column -->
            <div>
                <?php echo weardale_platform_render_contact_form(); ?>
            </div>

        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'weardale_contact_page_layout', 'weardale_platform_render_contact_page_layout' );

/**
 * Shortcode: Volunteer Page Layout
 */
function weardale_platform_render_volunteer_page() {
    ob_start();
    ?>
    <div class="weardale-volunteer-page">
        
        <div style="margin-bottom: 3.5rem; text-align: center; max-width: 800px; margin-left: auto; margin-right: auto;">
            <h2 class="font-display" style="font-size: 2.25rem; color: var(--color-forest); margin-top: 0; margin-bottom: 1.25rem; font-weight: normal;">
                <?php esc_html_e( 'Why Volunteering Matters', 'weardale-platform' ); ?>
            </h2>
            <p style="font-size: 1.15rem; line-height: 1.65; color: var(--text-primary); margin-bottom: 2rem;">
                <?php esc_html_e( 'Weardale Together is a grassroots organization powered by local residents. Volunteering with us is more than giving your time — it is about making friends, sharing skills, and directly building a stronger, healthier, and more connected valley.', 'weardale-platform' ); ?>
            </p>
        </div>

        <div style="margin-bottom: 4rem;">
            <style>
                .volunteer-info-grid {
                    display: grid;
                    grid-template-columns: 1fr;
                    gap: 2rem;
                }
                @media (min-width: 768px) {
                    .volunteer-info-grid {
                        grid-template-columns: 1fr 1fr;
                    }
                }
            </style>
            <div class="volunteer-info-grid">
                
                <div class="card" style="background-color: var(--color-cream); border: 1px solid var(--color-tan); padding: 2rem; border-radius: var(--border-radius-md);">
                    <h3 class="font-display" style="font-size: 1.4rem; color: var(--color-forest); margin-top: 0; margin-bottom: 1rem; font-weight: normal;">
                        🌸 <?php esc_html_e( 'What You Can Expect', 'weardale-platform' ); ?>
                    </h3>
                    <ul style="list-style-type: none; padding-left: 0; display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.95rem; line-height: 1.45; margin: 0;">
                        <li><strong>🤝 Warm Welcome:</strong> <?php esc_html_e( 'A friendly, supportive team that values your unique contributions.', 'weardale-platform' ); ?></li>
                        <li><strong>🎨 Skill Sharing:</strong> <?php esc_html_e( 'Learn traditional crafts, culinary skills, event support, or digital tools.', 'weardale-platform' ); ?></li>
                        <li><strong>💪 Flexible Commitment:</strong> <?php esc_html_e( 'Whether you can spare two hours a month or a day a week, we have a role for you.', 'weardale-platform' ); ?></li>
                    </ul>
                </div>

                <div class="card" style="background-color: var(--color-cream); border: 1px solid var(--color-tan); padding: 2rem; border-radius: var(--border-radius-md);">
                    <h3 class="font-display" style="font-size: 1.4rem; color: var(--color-forest); margin-top: 0; margin-bottom: 1rem; font-weight: normal;">
                        🕒 <?php esc_html_e( 'Practical Commitment', 'weardale-platform' ); ?>
                    </h3>
                    <p style="font-size: 0.95rem; line-height: 1.5; color: var(--text-primary); margin: 0 0 1rem 0;">
                        <?php esc_html_e( 'We support our volunteers with free travel expenses where appropriate, standard safeguarding and induction training, and plenty of tea, coffee, and home-cooked soup from our café!', 'weardale-platform' ); ?>
                    </p>
                    <p style="font-size: 0.95rem; line-height: 1.5; color: var(--text-primary); margin: 0;">
                        <strong><?php esc_html_e( 'Alternative Route:', 'weardale-platform' ); ?></strong> 
                        <?php esc_html_e( 'Rather talk on the phone? Give us a call at 01388 526200 or visit us in person at the Stanhope Hub during café opening hours.', 'weardale-platform' ); ?>
                    </p>
                </div>

            </div>
        </div>

        <!-- Current opportunities segment -->
        <div style="margin-bottom: 4rem;">
            <h2 class="font-display" style="font-size: 1.85rem; color: var(--color-forest); margin-top: 0; margin-bottom: 1.5rem; text-align: center; font-weight: normal;">
                💼 <?php esc_html_e( 'Current Volunteer Opportunities', 'weardale-platform' ); ?>
            </h2>
            
            <?php
            $opportunities_query = new WP_Query( array(
                'post_type'      => 'weardale_directory',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'directory_type',
                        'field'    => 'slug',
                        'terms'    => 'volunteer-opportunity',
                    ),
                ),
            ) );

            if ( $opportunities_query->have_posts() ) :
                ?>
                <style>
                    .vol-opp-grid {
                        display: grid;
                        grid-template-columns: 1fr;
                        gap: 2rem;
                    }
                    @media (min-width: 768px) {
                        .vol-opp-grid {
                            grid-template-columns: 1fr 1fr;
                        }
                    }
                </style>
                <div class="vol-opp-grid">
                    <?php
                    while ( $opportunities_query->have_posts() ) :
                        $opportunities_query->the_post();
                        $opp_id = get_the_ID();
                        
                        $is_demo      = get_post_meta( $opp_id, '_weardale_demo_content', true ) === '1';
                        $opp_villages = get_the_terms( $opp_id, 'village' );
                        $opp_village  = ! empty( $opp_villages ) && ! is_wp_error( $opp_villages ) ? reset( $opp_villages )->name : '';
                        ?>
                        <article class="card" style="display: flex; flex-direction: column; background-color: var(--color-white); border: 1px solid var(--color-tan); border-radius: var(--border-radius-md); overflow: hidden; box-shadow: 0 4px 12px rgba(59,92,58,0.02); padding: 1.75rem;">
                            
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                                <?php if ( ! empty( $opp_village ) ) : ?>
                                    <span class="badge" style="background-color: var(--color-cream); color: var(--color-forest); border: 1px solid var(--color-tan); font-weight: 700; font-size: 0.8rem; padding: 0.2rem 0.6rem; border-radius: 4px;">
                                        📍 <?php echo esc_html( $opp_village ); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if ( $is_demo ) : ?>
                                    <span class="badge" style="background-color: #fef3c7; color: #92400e; border: 1px solid #f59e0b; font-weight: 700; font-size: 0.8rem; padding: 0.2rem 0.6rem; border-radius: 4px;">
                                        <?php esc_html_e( 'Demo Record', 'weardale-platform' ); ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <h3 class="card-title font-display" style="font-size: 1.35rem; line-height: 1.25; margin-top: 0; margin-bottom: 0.75rem; font-weight: normal; color: var(--color-forest);">
                                <?php the_title(); ?>
                            </h3>

                            <div style="font-size: 0.95rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.25rem;">
                                <?php the_excerpt(); ?>
                            </div>

                            <div style="margin-top: auto; display: flex; gap: 1rem; flex-wrap: wrap;">
                                <a href="<?php echo esc_url( add_query_arg( array( 'enquiry_type' => 'directory', 'enquiry_id' => $opp_id ), home_url( '/contact-us/' ) ) ); ?>" class="btn btn-primary" style="font-size: 0.85rem; padding: 0.5rem 1rem;">
                                    <?php esc_html_e( 'Enquire About Role &rarr;', 'weardale-platform' ); ?>
                                </a>
                                <a href="<?php the_permalink(); ?>" class="btn btn-secondary" style="font-size: 0.85rem; padding: 0.5rem 1rem;">
                                    <?php esc_html_e( 'Read Details', 'weardale-platform' ); ?>
                                </a>
                            </div>

                        </article>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
            <?php else : ?>
                <div style="background-color: var(--color-cream); border: 1px dashed var(--color-tan); padding: 3rem; text-align: center; border-radius: var(--border-radius-md); max-width: 600px; margin: 0 auto;">
                    <p style="font-size: 1.05rem; color: var(--text-light); margin: 0;">
                        <?php esc_html_e( 'We do not have any active volunteer slots posted today, but we are always looking for helpful hands! Please fill out our general contact form below.', 'weardale-platform' ); ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Enquiry Form block -->
        <div id="volunteer-enquiry" style="background-color: var(--color-cream); border: 1px solid var(--color-tan); border-radius: var(--border-radius-md); padding: 3rem 2rem;">
            <h2 class="font-display" style="font-size: 1.85rem; color: var(--color-forest); margin-top: 0; margin-bottom: 0.5rem; text-align: center; font-weight: normal;">
                🙋‍♀️ <?php esc_html_e( 'Volunteer Enquiry Form', 'weardale-platform' ); ?>
            </h2>
            <p style="text-align: center; max-width: 500px; margin: 0 auto 2.5rem; color: var(--text-secondary); line-height: 1.4; font-size: 0.95rem;">
                <?php esc_html_e( 'Interested in helping? Fill out this short, confidential enquiry. We gather minimal data and will never share your details.', 'weardale-platform' ); ?>
            </p>
            <?php
            echo weardale_platform_render_contact_form( array( 'type' => 'volunteer' ) );
            ?>
        </div>

    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'weardale_volunteer_page', 'weardale_platform_render_volunteer_page' );

/**
 * Retrieve Legal Page URL
 *
 * Resolves selected page ID to its published permalink, with structured fallback support.
 *
 * @param string $page_option Option key containing the page ID.
 * @return string Resolved Page URL.
 */
function weardale_platform_get_legal_page_url( $page_option ) {
    $page_id = get_option( $page_option );
    if ( $page_id ) {
        $url = get_permalink( $page_id );
        if ( $url ) {
            return $url;
        }
    }
    // Fallback defaults if unconfigured
    if ( $page_option === 'weardale_legal_privacy_page' ) {
        return home_url( '/privacy-notice/' );
    } elseif ( $page_option === 'weardale_legal_cookie_page' ) {
        return home_url( '/cookie-policy/' );
    } elseif ( $page_option === 'weardale_legal_terms_page' ) {
        return home_url( '/terms-conditions/' );
    }
    return home_url();
}

/**
 * Reusable Newsletter Signup Component
 *
 * Renders active Mailchimp sign-up form when configured, otherwise displays coming-soon notice.
 * Supports 'page', 'homepage', and 'footer' contexts.
 *
 * @param string $context Context location ('page', 'homepage', 'footer').
 * @return string HTML output.
 */
function weardale_platform_get_newsletter_form( $context = 'page' ) {
    $mailchimp_url = get_option( 'weardale_mailchimp_url' );
    $privacy_link  = weardale_platform_get_legal_page_url( 'weardale_legal_privacy_page' );
    
    ob_start();
    
    if ( ! empty( $mailchimp_url ) ) {
        if ( $context === 'footer' ) {
            ?>
            <form action="<?php echo esc_url( $mailchimp_url ); ?>" method="post" target="_blank" novalidate style="display: flex; flex-direction: column; gap: 0.75rem;">
                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label for="footer_mc_email" class="sr-only" style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); border: 0;">Email Address</label>
                    <input type="email" name="EMAIL" id="footer_mc_email" required placeholder="name@example.com"
                           style="padding: 0.5rem 0.75rem; border: 1px solid rgba(255,255,255,0.2); border-radius: var(--border-radius-sm); font-size: 0.9rem; width: 100%; background-color: rgba(255,255,255,0.1); color: var(--color-cream);">
                </div>
                <button type="submit" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.85rem; font-weight: 700; width: 100%; text-align: center; background-color: var(--color-tan); color: var(--color-forest); border: none; cursor: pointer;">
                    <?php esc_html_e( 'Subscribe', 'weardale-platform' ); ?>
                </button>
                <div style="font-size: 0.75rem; color: var(--color-tan); opacity: 0.8; line-height: 1.3;">
                    <?php printf( __( 'By subscribing, you agree to our <a href="%s" style="color: inherit; text-decoration: underline;">Privacy Notice</a>.', 'weardale-platform' ), esc_url( $privacy_link ) ); ?>
                </div>
            </form>
            <?php
        } elseif ( $context === 'homepage' ) {
            ?>
            <form action="<?php echo esc_url( $mailchimp_url ); ?>" method="post" target="_blank" novalidate style="display: flex; gap: 0.75rem; flex-wrap: wrap; justify-content: center; width: 100%; max-width: 600px; margin: 0 auto;">
                <label for="homepage_mc_email" class="sr-only" style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); border: 0;">Email Address</label>
                <input type="email" name="EMAIL" id="homepage_mc_email" placeholder="Enter your email address..." required 
                       style="max-width: 380px; flex-grow: 1; border: 1px solid var(--color-tan); border-radius: var(--border-radius-pill); padding: 0.75rem 1.25rem; font-size: 1rem; background-color: var(--color-white);">
                <button type="submit" class="btn btn-primary" style="border-radius: var(--border-radius-pill); padding: 0.75rem 1.5rem; cursor: pointer;">
                    <?php esc_html_e( 'Sign Up Now', 'weardale-platform' ); ?>
                </button>
            </form>
            <div style="margin-top: 1.25rem; font-size: 0.85rem; color: var(--text-light); line-height: 1.4;">
                <p style="margin: 0;">
                    <?php 
                    printf(
                        wp_kses(
                            __( 'By joining, you consent to Weardale Together sending newsletter emails in accordance with our <a href="%s" style="color: var(--color-sage); text-decoration: underline;">Privacy Notice</a>.', 'weardale-platform' ),
                            array( 'a' => array( 'href' => array(), 'style' => array() ) )
                        ),
                        esc_url( $privacy_link )
                    ); 
                    ?>
                </p>
            </div>
            <?php
        } else { // 'page'
            ?>
            <div class="card" style="background-color: var(--color-cream); border: 2px solid var(--color-tan); padding: 2.5rem; border-radius: var(--border-radius-md); text-align: left; box-shadow: var(--shadow-sm);">
                <form action="<?php echo esc_url( $mailchimp_url ); ?>" method="post" target="_blank" novalidate style="display: flex; flex-direction: column; gap: 1.25rem;">
                    
                    <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label for="mc_email" style="font-weight: 600; font-family: var(--font-headings); color: var(--color-forest); font-size: 1.05rem;">
                            <?php esc_html_e( 'Email Address', 'weardale-platform' ); ?> <span style="color: #b91c1c;" aria-hidden="true">*</span>
                        </label>
                        <input type="email" name="EMAIL" id="mc_email" class="input-text" required placeholder="name@example.com"
                               style="padding: 0.75rem 1rem; border: 1px solid var(--color-tan); border-radius: var(--border-radius-sm); font-size: 1rem; width: 100%; background-color: var(--color-white);">
                    </div>

                    <div class="form-group" style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label for="mc_fname" style="font-weight: 600; font-family: var(--font-headings); color: var(--color-forest); font-size: 1.05rem;">
                            <?php esc_html_e( 'First Name', 'weardale-platform' ); ?>
                        </label>
                        <input type="text" name="FNAME" id="mc_fname" class="input-text" placeholder="Your first name"
                               style="padding: 0.75rem 1rem; border: 1px solid var(--color-tan); border-radius: var(--border-radius-sm); font-size: 1rem; width: 100%; background-color: var(--color-white);">
                    </div>

                    <div style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.4; margin-top: 0.25rem;">
                        <?php 
                        printf(
                            wp_kses(
                                __( 'By joining, you consent to Weardale Together sending newsletter emails. We use Mailchimp as our platform. You can unsubscribe at any time using the link in our footers. Read our <a href="%s" style="color: var(--color-forest); text-decoration: underline;">Privacy Notice</a>.', 'weardale-platform' ),
                                array( 'a' => array( 'href' => array(), 'style' => array() ) )
                            ),
                            esc_url( $privacy_link )
                        ); 
                        ?>
                    </div>

                    <button type="submit" class="btn btn-primary" style="padding: 0.85rem 2rem; font-size: 1.05rem; font-weight: 700; align-self: flex-start; margin-top: 0.5rem; cursor: pointer;">
                        <?php esc_html_e( 'Subscribe to Newsletter', 'weardale-platform' ); ?>
                    </button>

                </form>
            </div>
            <?php
        }
    } else {
        if ( $context === 'footer' ) {
            ?>
            <div style="background-color: rgba(255, 255, 255, 0.1); padding: 0.75rem; border-radius: var(--border-radius-sm); border: 1px solid rgba(255, 255, 255, 0.2);">
                <p style="font-size: 0.85rem; color: var(--color-tan); margin-bottom: 0; text-align: center; line-height: 1.3;">
                    <em><?php esc_html_e( 'Newsletter signup is temporarily offline.', 'weardale-platform' ); ?></em>
                </p>
                <?php if ( current_user_can( 'manage_options' ) ) : ?>
                    <p style="margin: 0.5rem 0 0 0; font-size: 0.75rem; text-align: center;">
                        <a href="<?php echo esc_url( admin_url( 'tools.php?page=weardale-site-setup' ) ); ?>" style="color: #fff; text-decoration: underline; font-weight: 600;"><?php esc_html_e( 'Configure Now', 'weardale-platform' ); ?></a>
                    </p>
                <?php endif; ?>
            </div>
            <?php
        } elseif ( $context === 'homepage' ) {
            ?>
            <div class="card" style="background-color: var(--color-white); border: 2px dashed var(--color-tan); padding: 2.5rem 2rem; border-radius: var(--border-radius-md); max-width: 600px; margin: 0 auto; box-shadow: 0 4px 15px rgba(196, 184, 154, 0.15);">
                <h3 class="font-display" style="font-size: 1.5rem; color: var(--color-forest); margin-top: 0; margin-bottom: 0.75rem; font-weight: normal;">
                    📬 <?php esc_html_e( 'Newsletter Sign-Up Coming Soon', 'weardale-platform' ); ?>
                </h3>
                <p style="margin: 0; line-height: 1.5; color: var(--text-secondary); font-size: 0.95rem;">
                    <?php esc_html_e( 'We are currently preparing our digital mailing systems. Sign-up forms will be activated as soon as our Mailchimp integration is finalized by our team. Thank you for your interest and support!', 'weardale-platform' ); ?>
                </p>
                <?php if ( current_user_can( 'manage_options' ) ) : ?>
                    <div style="margin-top: 1.5rem; background: #fffbeb; border-left: 4px solid #d97706; padding: 1rem; text-align: left; font-size: 0.85rem; color: #78350f; border-radius: var(--border-radius-sm);">
                        <strong>Administrator Info:</strong> Go to <a href="<?php echo esc_url( admin_url( 'tools.php?page=weardale-site-setup' ) ); ?>" style="text-decoration: underline; color: inherit; font-weight: 600;">Weardale Site Setup</a> and paste your Mailchimp Form Action URL under "Participation Settings" to enable the live sign-up form.
                    </div>
                <?php endif; ?>
            </div>
            <?php
        } else { // 'page'
            ?>
            <div class="card" style="background-color: var(--color-cream); border: 2px dashed var(--color-tan); padding: 3rem; border-radius: var(--border-radius-md); box-shadow: var(--shadow-sm); max-width: 600px; margin: 0 auto;">
                <h3 class="font-display" style="font-size: 1.5rem; color: var(--color-forest); margin-top: 0; margin-bottom: 0.75rem; font-weight: normal;">
                    📬 <?php esc_html_e( 'Newsletter Sign-Up Coming Soon', 'weardale-platform' ); ?>
                </h3>
                <p style="margin: 0; line-height: 1.5; color: var(--text-secondary); font-size: 0.95rem;">
                    <?php esc_html_e( 'We are currently preparing our digital mailing systems. Sign-up forms will be activated as soon as our Mailchimp integration is finalized by our editorial team. Thank you for your interest and support!', 'weardale-platform' ); ?>
                </p>
                <?php if ( current_user_can( 'manage_options' ) ) : ?>
                    <div style="margin-top: 1.5rem; background: #fffbeb; border-left: 4px solid #d97706; padding: 1rem; text-align: left; font-size: 0.85rem; color: #78350f; border-radius: var(--border-radius-sm);">
                        <strong>Administrator Info:</strong> Go to <a href="<?php echo esc_url( admin_url( 'tools.php?page=weardale-site-setup' ) ); ?>" style="text-decoration: underline; color: inherit; font-weight: 600;">Weardale Site Setup</a> and paste your Mailchimp Form Action URL under "Participation Settings" to enable the live sign-up form.
                    </div>
                <?php endif; ?>
            </div>
            <?php
        }
    }
    
    return ob_get_clean();
}

/**
 * Shortcode: Newsletter Page Layout
 */
function weardale_platform_render_newsletter_page() {
    ob_start();
    ?>
    <div class="weardale-newsletter-page" style="max-width: 600px; margin: 0 auto; text-align: center; padding: 2rem 0;">
        <span style="font-size: 3rem; display: block; margin-bottom: 1.5rem;" role="img" aria-label="Mailbox">📬</span>
        <h2 class="font-display" style="font-size: 2.25rem; color: var(--color-forest); margin-top: 0; margin-bottom: 1rem; font-weight: normal;">
            <?php esc_html_e( 'Join Weardale Together', 'weardale-platform' ); ?>
        </h2>
        <p style="font-size: 1.1rem; line-height: 1.6; color: var(--text-primary); margin-bottom: 2.5rem;">
            <?php esc_html_e( 'Receive seasonal newsletters, stories from across the valley, updates from our community café, and upcoming event notifications straight to your inbox.', 'weardale-platform' ); ?>
        </p>

        <?php echo weardale_platform_get_newsletter_form( 'page' ); ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'weardale_newsletter_page', 'weardale_platform_render_newsletter_page' );

/**
 * Automatically inject contact and volunteer form shortcodes into pages
 * if the content does not already contain them, to ensure they render on the frontend.
 */
function weardale_platform_auto_inject_forms( $content ) {
    if ( ! is_page() ) {
        return $content;
    }

    global $post;
    if ( ! $post ) {
        return $content;
    }

    $slug = $post->post_name;

    // Contact Us page: slug might be 'contact-us' or 'contact'
    if ( in_array( $slug, array( 'contact-us', 'contact' ), true ) ) {
        if ( ! has_shortcode( $content, 'weardale_contact_page_layout' ) && ! has_shortcode( $content, 'weardale_contact_form' ) && strpos( $content, 'weardale-contact-grid' ) === false ) {
            $content .= "\n\n" . do_shortcode( '[weardale_contact_page_layout]' );
        }
    }

    // Volunteer page: slug might be 'volunteer-with-us', 'volunteer'
    if ( in_array( $slug, array( 'volunteer-with-us', 'volunteer' ), true ) ) {
        if ( ! has_shortcode( $content, 'weardale_volunteer_page' ) && strpos( $content, 'weardale-volunteer-page' ) === false ) {
            $content .= "\n\n" . do_shortcode( '[weardale_volunteer_page]' );
        }
    }

    // Newsletter page: slug might be 'newsletter', 'newsletter-signup'
    if ( in_array( $slug, array( 'newsletter', 'newsletter-signup' ), true ) ) {
        if ( ! has_shortcode( $content, 'weardale_newsletter_page' ) && strpos( $content, 'weardale-newsletter-page' ) === false ) {
            $content .= "\n\n" . do_shortcode( '[weardale_newsletter_page]' );
        }
    }

    return $content;
}
add_filter( 'the_content', 'weardale_platform_auto_inject_forms', 20 );

/**
 * Repeat-Safe Development Seeder for Participation & Engagement Data
 */
function weardale_platform_seed_participation_data() {
    $results = array(
        'created' => 0,
        'skipped' => 0,
    );

    // 1. Ensure Directory taxonomy terms exist
    if ( taxonomy_exists( 'directory_type' ) ) {
        if ( ! term_exists( 'volunteer-opportunity', 'directory_type' ) ) {
            wp_insert_term( 'Volunteer Opportunity', 'directory_type', array( 'slug' => 'volunteer-opportunity' ) );
        }
    }

    // 2. Define Volunteer Opportunities and Directory Listings
    $listings = array(
        array(
            'key'     => 'demo-volunteer-cafe-support',
            'title'   => 'Café Support Volunteer',
            'excerpt' => 'Join our Root & Branch Café team in Stanhope. Help serve coffee, bake treats, and offer a friendly ear.',
            'content' => 'The Root & Branch Café is the social heart of Weardale Together. As a Café Support Volunteer, you will work alongside our welcoming kitchen staff to greet guests, take orders, prepare light lunches, bake traditional treats, and maintain a warm, clean environment. No prior professional kitchen experience is required — we provide all training and food hygiene certifications. It is a fantastic opportunity to build confidence, learn cooking crafts, and connect with local residents.',
            'type'    => 'volunteer-opportunity',
            'village' => 'stanhope',
            'area'    => 'all-weardale',
            'meta'    => array(
                '_directory_address'         => 'Stanhope Hub, Front Street, Stanhope, DL13 2YR',
                '_directory_phone'           => '01388 526200',
                '_directory_email'           => 'cafe@weardaletogether.org.uk',
                '_directory_opening_hours'   => 'Shifts available Tue-Sat: 10:00am - 1:00pm or 1:00pm - 4:00pm.',
                '_directory_accessibility'   => 'Wheelchair accessible kitchen and café area.',
                '_directory_who_it_helps'    => 'Volunteers seeking social connection, hospitality skills, and local residents.',
                '_directory_pricing'         => 'Free (volunteering opportunity).',
                '_directory_booking_required'=> 'yes',
                '_directory_allow_enquiry'   => '1',
            ),
        ),
        array(
            'key'     => 'demo-volunteer-event-welcome',
            'title'   => 'Event Welcome Volunteer',
            'excerpt' => 'Help us welcome guests, coordinate registrations, and guide families at our community events.',
            'content' => 'Weardale Together coordinates seasonal community events, creative arts exhibitions, and open-air workshops throughout the year. Event Welcome Volunteers are the friendly faces that greet our visitors, register participants, guide families to activities, and help hand out maps or refreshments. If you are a chatty, organized person who loves community gatherings, this is the perfect role! Hours are flexible and correspond with scheduled weekend or holiday events.',
            'type'    => 'volunteer-opportunity',
            'village' => 'wolsingham',
            'area'    => 'mid-weardale',
            'meta'    => array(
                '_directory_address'         => 'Wolsingham Recreation Ground & Community Rooms, Wolsingham',
                '_directory_phone'           => '01388 526200',
                '_directory_email'           => 'events@weardaletogether.org.uk',
                '_directory_opening_hours'   => 'Saturdays and Sundays during event schedules (typically 2-4 hours).',
                '_directory_accessibility'   => 'Varies by event venue, but primarily accessible rooms and outdoor parks.',
                '_directory_who_it_helps'    => 'Families, children, and visitors to our community workshops.',
                '_directory_pricing'         => 'Free (volunteering).',
                '_directory_booking_required'=> 'yes',
                '_directory_allow_enquiry'   => '1',
            ),
        ),
        array(
            'key'     => 'demo-volunteer-creative-helper',
            'title'   => 'Creative Programme Helper',
            'excerpt' => 'Assist our resident artist Sarah during autumn craft classes and botanical workshops.',
            'content' => 'Our Creative Arts strand connects community wellness with traditional expressive crafts. As a Creative Programme Helper, you will work closely with our lead artists to set up materials (such as raw flora, block prints, or botanical inks), assist participants with crafts, and help tidy the workspace. This is a brilliant opportunity to learn traditional North Pennines art techniques while helping residents feel relaxed and creative.',
            'type'    => 'volunteer-opportunity',
            'village' => 'frosterley',
            'area'    => 'mid-weardale',
            'meta'    => array(
                '_directory_address'         => 'Frosterley Village Hall, Front Street, Frosterley, DL13 2SL',
                '_directory_phone'           => '01388 526200',
                '_directory_email'           => 'creative@weardaletogether.org.uk',
                '_directory_opening_hours'   => 'Thursdays 1:30pm - 4:30pm (seasonal blocks).',
                '_directory_accessibility'   => 'Fully accessible hall with accessible parking.',
                '_directory_who_it_helps'    => 'Adults and seniors seeking creative expression and mental well-being.',
                '_directory_pricing'         => 'Free (volunteering).',
                '_directory_booking_required'=> 'yes',
                '_directory_allow_enquiry'   => '1',
            ),
        ),
        array(
            'key'     => 'demo-directory-enquiry-test',
            'title'   => 'Weardale Creative Makers Guild',
            'excerpt' => 'A local craft cooperative allowing directory enquiries directly on-site.',
            'content' => 'The Weardale Creative Makers Guild brings together local artisans, weavers, and artists. We run creative drop-in sessions and allow online on-site contact to arrange partnerships or custom crafts.',
            'type'    => 'community-group',
            'village' => 'stanhope',
            'area'    => 'all-weardale',
            'meta'    => array(
                '_directory_address'         => 'High Street, Stanhope, DL13 2SL',
                '_directory_phone'           => '01388 526200',
                '_directory_email'           => 'guild@weardaletogether.org.uk',
                '_directory_website'         => 'https://weardale-makers.co.uk',
                '_directory_opening_hours'   => 'Mon-Sat 10:00am - 4:00pm.',
                '_directory_accessibility'   => 'Ground floor accessible workshop.',
                '_directory_who_it_helps'    => 'Local artists and craft makers.',
                '_directory_pricing'         => 'Free entry.',
                '_directory_booking_required'=> 'no',
                '_directory_allow_enquiry'   => '1', // ALLOW ON-SITE ENQUIRY
            ),
        ),
    );

    // Seed Directory Entries
    foreach ( $listings as $list ) {
        $existing = get_posts( array(
            'post_type'   => 'weardale_directory',
            'meta_key'    => '_weardale_demo_key',
            'meta_value'  => $list['key'],
            'post_status' => 'any',
            'numberposts' => 1,
        ) );

        if ( ! empty( $existing ) ) {
            $results['skipped']++;
            continue;
        }

        $post_id = wp_insert_post( array(
            'post_title'   => $list['title'],
            'post_excerpt' => $list['excerpt'],
            'post_content' => $list['content'],
            'post_status'  => 'publish',
            'post_type'    => 'weardale_directory',
        ) );

        if ( $post_id && ! is_wp_error( $post_id ) ) {
            update_post_meta( $post_id, '_weardale_demo_content', '1' );
            update_post_meta( $post_id, '_weardale_demo_key', $list['key'] );

            foreach ( $list['meta'] as $meta_key => $meta_val ) {
                update_post_meta( $post_id, $meta_key, $meta_val );
            }

            $type_term = get_term_by( 'slug', $list['type'], 'directory_type' );
            if ( $type_term ) {
                wp_set_object_terms( $post_id, $type_term->term_id, 'directory_type' );
            }

            $village_term = get_term_by( 'slug', $list['village'], 'village' );
            if ( $village_term ) {
                wp_set_object_terms( $post_id, $village_term->term_id, 'village' );
            }

            $area_term = get_term_by( 'slug', $list['area'], 'service_area' );
            if ( $area_term ) {
                wp_set_object_terms( $post_id, $area_term->term_id, 'service_area' );
            }

            $results['created']++;
        } else {
            $results['skipped']++;
        }
    }

    // 3. Seed an Event Enquiry Route Event
    $event_key = 'demo-event-participation-test';
    $existing_event = get_posts( array(
        'post_type'   => 'weardale_event',
        'meta_key'    => '_weardale_demo_key',
        'meta_value'  => $event_key,
        'post_status' => 'any',
        'numberposts' => 1,
    ) );

    if ( empty( $existing_event ) ) {
        $event_id = wp_insert_post( array(
            'post_title'   => 'Community Heritage Craft Day',
            'post_excerpt' => 'Join us for a hands-on community day of heritage craft wood carving and botanical sketching.',
            'post_content' => 'Spend an unhurried Saturday with our creative volunteers exploring regional wood carving and natural ink drawing. All levels are warmly welcome, and a warm soup lunch is provided.',
            'post_status'  => 'publish',
            'post_type'    => 'weardale_event',
        ) );

        if ( $event_id && ! is_wp_error( $event_id ) ) {
            update_post_meta( $event_id, '_weardale_demo_content', '1' );
            update_post_meta( $event_id, '_weardale_demo_key', $event_key );

            $event_date = date( 'Y-m-d', strtotime( '+14 days' ) );
            update_post_meta( $event_id, '_event_date', $event_date );
            update_post_meta( $event_id, '_event_time', '10:00 AM - 3:00 PM' );
            update_post_meta( $event_id, '_event_location', 'Frosterley Village Hall' );
            update_post_meta( $event_id, '_event_cost', 'Free (Booking recommended)' );
            update_post_meta( $event_id, '_event_booking_status', 'booking_recommended' );
            update_post_meta( $event_id, '_event_booking_url', 'https://tickets.example.com/heritage-day' );

            $results['created']++;
        } else {
            $results['skipped']++;
        }
    } else {
        $results['skipped']++;
    }

    // 4. Seed Pages
    $pages = array(
        'contact-us' => array(
            'title'   => 'Get In Touch',
            'content' => '[weardale_contact_page_layout]',
        ),
        'volunteer' => array(
            'title'   => 'Volunteer With Us',
            'content' => '[weardale_volunteer_page]',
        ),
        'newsletter' => array(
            'title'   => 'Newsletter Sign-up',
            'content' => '[weardale_newsletter_page]',
        ),
    );

    foreach ( $pages as $slug => $page_data ) {
        $existing_page = get_page_by_path( $slug );
        if ( ! $existing_page ) {
            $inserted = wp_insert_post( array(
                'post_title'   => $page_data['title'],
                'post_name'    => $slug,
                'post_content' => $page_data['content'],
                'post_status'  => 'publish',
                'post_type'    => 'page',
            ) );
            if ( $inserted && ! is_wp_error( $inserted ) ) {
                update_post_meta( $inserted, '_weardale_demo_content', '1' );
                update_post_meta( $inserted, '_weardale_demo_key', 'demo-page-' . $slug );
                $results['created']++;
            } else {
                $results['skipped']++;
            }
        } else {
            // Ensure the shortcode exists in the page content
            $current_content = $existing_page->post_content;
            $shortcode = $page_data['content'];
            $clean_shortcode = substr($shortcode, 1, -1); // e.g. "weardale_volunteer_page"
            if ( strpos( $current_content, $clean_shortcode ) === false ) {
                wp_update_post( array(
                    'ID'           => $existing_page->ID,
                    'post_content' => $current_content . "\n\n" . $shortcode,
                ) );
                $results['created']++;
            } else {
                $results['skipped']++;
            }
        }
    }

    // 5. Seed default contact/options settings so things work out of the box in development
    if ( ! get_option( 'weardale_contact_address' ) ) {
        update_option( 'weardale_contact_address', "Stanhope Hub, Front Street, Stanhope, DL13 2YR" );
    }
    if ( ! get_option( 'weardale_contact_phone' ) ) {
        update_option( 'weardale_contact_phone', "01388 526200" );
    }
    if ( ! get_option( 'weardale_contact_email' ) ) {
        update_option( 'weardale_contact_email', "enquiries@weardaletogether.co.uk" );
    }
    if ( ! get_option( 'weardale_contact_opening_hours' ) ) {
        update_option( 'weardale_contact_opening_hours', "Tuesday – Saturday: 10:00 AM – 3:30 PM\nSunday & Monday: Closed" );
    }
    if ( ! get_option( 'weardale_contact_directions' ) ) {
        update_option( 'weardale_contact_directions', "We are situated right on the main Front Street in Stanhope, directly next to the community market place. Free parking is available nearby." );
    }
    if ( ! get_option( 'weardale_enquiry_recipient' ) ) {
        update_option( 'weardale_enquiry_recipient', "enquiries@weardaletogether.co.uk" );
    }
    if ( ! get_option( 'weardale_enquiry_enabled' ) ) {
        update_option( 'weardale_enquiry_enabled', 'yes' );
    }
    if ( ! get_option( 'weardale_enquiry_reply_to' ) ) {
        update_option( 'weardale_enquiry_reply_to', 'yes' );
    }

    return $results;
}
