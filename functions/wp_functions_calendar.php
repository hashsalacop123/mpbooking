<?php 

add_action('wp_ajax_handle_booking', 'handle_booking');
add_action('wp_ajax_nopriv_handle_booking', 'handle_booking');

/**
 * Unified booking handler (coach + service)
 */

function handle_booking() {

    /*
    |--------------------------------------------------------------------------
    | SECURITY
    |--------------------------------------------------------------------------
    */
    check_ajax_referer('booking_nonce', 'nonce');

    /*
    |--------------------------------------------------------------------------
    | TYPE
    |--------------------------------------------------------------------------
    */
    $type = in_array($_POST['type'] ?? '', ['coach','service']) 
        ? $_POST['type'] 
        : 'coach';

    /*
    |--------------------------------------------------------------------------
    | SANITIZE INPUTS
    |--------------------------------------------------------------------------
    */
    $name    = sanitize_text_field($_POST['name']);
    $email   = sanitize_email($_POST['email']);
    $comment = sanitize_textarea_field($_POST['comment']);
    $amount  = floatval($_POST['amount']);

    // Normalize formats (IMPORTANT - must match calendar)
    $start = date('g:00 A', strtotime($_POST['start']));
    $end   = date('g:00 A', strtotime($_POST['end']));
    $date  = date('Y-m-d', strtotime($_POST['date']));

    if (!$name || !$email || !$start || !$end || !$date) {
        wp_send_json_error('Missing required fields');
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE BOOKING POST
    |--------------------------------------------------------------------------
    */
    $booking_id = wp_insert_post([
        'post_type'   => 'booking',
        'post_status' => 'publish',
        'post_title'  => ucfirst($type) . ' Booking - ' . $name,
    ]);

    if (!$booking_id) {
        wp_send_json_error('Booking failed');
    }

    /*
    |--------------------------------------------------------------------------
    | COMMON FIELDS
    |--------------------------------------------------------------------------
    */
    update_field('guest_name', $name, $booking_id);
    update_field('guest_email', $email, $booking_id);
    update_field('guest_comment', $comment, $booking_id);
    update_field('date_booked', $date, $booking_id);
    update_field('time_start', $start, $booking_id);
    update_field('time_end', $end, $booking_id);
    update_field('amount', $amount, $booking_id);

    /*
    |--------------------------------------------------------------------------
    | STATUS (STATE OF BOOKING)
    |--------------------------------------------------------------------------
    */
    update_field('booking_status', 'pending', $booking_id);

    /*
    |--------------------------------------------------------------------------
    | WHO BOOKED (CUSTOMER)
    |--------------------------------------------------------------------------
    */
    $user_id = get_current_user_id();
    if (!$user_id) {
        $user_id = 0;
    }

    update_field('booked_by_user', $user_id, $booking_id);

    /*
    |--------------------------------------------------------------------------
    | TYPE-SPECIFIC
    |--------------------------------------------------------------------------
    */
    if ($type === 'coach') {

        /*
        |--------------------------------------------------------------------------
        | GET COACH OWNER FROM AJAX (FIXED)
        |--------------------------------------------------------------------------
        */

        // Get coach post ID from frontend
        $coach_post_id = intval($_POST['coach_id'] ?? 0);

        if (!$coach_post_id) {
            wp_send_json_error('Coach post not found');
        }

        // Get author (owner of coach post)
        $coach_owner_id = get_post_field('post_author', $coach_post_id);

        if (!$coach_owner_id) {
            wp_send_json_error('Coach owner not found');
        }

        /*
        |--------------------------------------------------------------------------
        | SAVE COACH OWNER (CONSISTENT FORMAT)
        |--------------------------------------------------------------------------
        */
        update_field('coach__services', $coach_owner_id, $booking_id);

        /*
        |--------------------------------------------------------------------------
        | EMAIL COACH
        |--------------------------------------------------------------------------
        */
        $coach_user  = get_userdata($coach_owner_id);
        $coach_email = $coach_user ? $coach_user->user_email : '';

        if ($coach_email) {
            wp_mail(
                $coach_email,
                'New Booking Request',
                "New coach booking from $name\n$date $start - $end"
            );
        }

    }   else if ($type === 'service') {

    /*
    |--------------------------------------------------------------------------
    | SERVICE BOOKING (COURT)
    |--------------------------------------------------------------------------
    */

    $court_index = intval($_POST['court_index'] ?? -1);
    $service_id  = intval($_POST['service_id'] ?? 0);

    if ($court_index < 0) {
        wp_send_json_error('Missing court index');
    }

    if (!$service_id) {
        wp_send_json_error('Service post not found');
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE SERVICE DATA
    |--------------------------------------------------------------------------
    */
    update_field('court_index', $court_index, $booking_id);
    update_field('service_id', $service_id, $booking_id);

    /*
    |--------------------------------------------------------------------------
    | GET SERVICE OWNER (THIS IS THE FIX)
    |--------------------------------------------------------------------------
    */
    $service_owner_id = get_post_field('post_author', $service_id);

    /*
    |--------------------------------------------------------------------------
    | SAVE OWNER (MATCH COACH STRUCTURE)
    |--------------------------------------------------------------------------
    */
    update_field('coach__services', $service_owner_id, $booking_id);

    /*
    |--------------------------------------------------------------------------
    | EMAIL OWNER
    |--------------------------------------------------------------------------
    */
    $service_user  = get_userdata($service_owner_id);
    $service_email = $service_user ? $service_user->user_email : '';

    if ($service_email) {
        wp_mail(
            $service_email,
            'New Court Booking',
            "New court booking from $name\nCourt: $court_index\n$date $start - $end"
        );
    }
}

    /*
    |--------------------------------------------------------------------------
    | HOLD EXPIRATION
    |--------------------------------------------------------------------------
    */
    $hold_minutes = 15;
    $expires_timestamp = strtotime("+{$hold_minutes} minutes");
    $expires = date('Y-m-d H:i:s', $expires_timestamp);

    update_field('hold_expires', $expires, $booking_id);

    wp_schedule_single_event(
        $expires_timestamp,
        'expire_coach_booking',
        [$booking_id]
    );

    /*
    |--------------------------------------------------------------------------
    | EMAIL GUEST
    |--------------------------------------------------------------------------
    */
    wp_mail(
        $email,
        'Booking Pending',
        'Your booking is pending and will expire if not completed.'
    );

    /*
    |--------------------------------------------------------------------------
    | PAYMENT (PAYMONGO)
    |--------------------------------------------------------------------------
    */
    $checkout_url = create_paymongo_gcash_payment(
        $amount,
        ucfirst($type) . " booking for $name",
        $booking_id
    );

    if (!$checkout_url) {
        wp_send_json_error('Payment link failed');
    }

    /*
    |--------------------------------------------------------------------------
    | SUCCESS RESPONSE
    |--------------------------------------------------------------------------
    */
    wp_send_json_success([
        'checkout_url' => $checkout_url
    ]);
}
/**
 * Expire pending bookings
 */
add_action('expire_coach_booking', 'expire_coach_booking_callback');

function expire_coach_booking_callback($booking_id) {

    $status = get_field('booking_status', $booking_id);

    if ($status !== 'pending') return;

    $guest_email = get_field('guest_email', $booking_id);
    $guest_name  = get_field('guest_name', $booking_id);

    // Coach (if exists)
    $coach_id = get_field('coach__services', $booking_id);
    $coach_email = '';

    if ($coach_id) {
        $coach_user  = get_userdata($coach_id);
        $coach_email = $coach_user ? $coach_user->user_email : '';
    }

    update_field('booking_status', 'expired', $booking_id);

    // Email guest
    if ($guest_email) {
        wp_mail(
            $guest_email,
            'Booking Expired',
            'Your booking hold has expired.'
        );
    }

    // Email coach (if exists)
    if ($coach_email) {
        wp_mail(
            $coach_email,
            'Booking Expired',
            "Booking for $guest_name has expired."
        );
    }
}


/**
 * Auto email on approve/reject
 */
add_action('acf/save_post', function ($post_id) {

    if (get_post_type($post_id) !== 'booking') return;

    $status = get_field('booking_status', $post_id);
    $email  = get_field('guest_email', $post_id);

    if ($status === 'approved') {
        wp_mail($email, 'Booking Approved', 'Your booking was approved.');
    }

    if ($status === 'rejected') {
        wp_mail($email, 'Booking Rejected', 'Your booking was rejected.');
    }

    if ($status === 'refund_pending') {
        wp_mail($email, 'Booking Refund', 'Your Booking has been Process.');
    }

     if ($status === 'refunded') {
        wp_mail($email, 'Booking refunded', 'Your Refund has been successfully refunded.');
    }
});


/**
 * Update booking status (dashboard)
 */
function update_booking_status() {

    check_ajax_referer('booking_nonce', 'nonce');

    $current_user_id = get_current_user_id();

    $booking_id = intval($_POST['booking_id']);
    $status     = sanitize_text_field($_POST['status']);

    if (!$booking_id) {
        wp_send_json_error('Invalid booking.');
    }

    $allowed_statuses = ['pending', 'approved', 'rejected','refunded','refund_pending','expired'];
    if (!in_array($status, $allowed_statuses)) {
        wp_send_json_error('Invalid status.');
    }

    $coach_field = get_field('coach__services', $booking_id);

    $coach_id = 0;

    if (is_array($coach_field) && isset($coach_field['ID'])) {
        $coach_id = $coach_field['ID'];
    } elseif (is_object($coach_field) && isset($coach_field->ID)) {
        $coach_id = $coach_field->ID;
    } else {
        $coach_id = intval($coach_field);
    }

    if ($coach_id !== $current_user_id) {
        wp_send_json_error('Unauthorized.');
    }

    update_field('booking_status', $status, $booking_id);

    $guest_email = get_field('guest_email', $booking_id);
    $guest_name  = get_field('guest_name', $booking_id);
    $date        = get_field('date_booked', $booking_id);
    $start       = get_field('time_start', $booking_id);
    $end         = get_field('time_end', $booking_id);

    if ($guest_email) {

        $subject = 'Your Booking Status Has Been Updated';

        $message = "
Hi {$guest_name},

Your booking status has been updated.

Date: {$date}
Time: {$start} - {$end}
Status: " . ucfirst($status) . "

Thank you.
";

        wp_mail($guest_email, $subject, $message);
    }

    wp_send_json_success('Status updated.');
}
add_action('wp_ajax_update_booking_status', 'update_booking_status');