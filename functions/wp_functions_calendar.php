<?php 

add_action('wp_ajax_handle_booking', 'handle_booking');
add_action('wp_ajax_nopriv_handle_booking', 'handle_booking');

/** Validate a court booking against the saved court calendar and active bookings. */
function mp_validate_court_booking($service_id, $court_index, $date_raw, $start_raw, $end_raw) {
    $service = get_post($service_id);
    $courts = get_field('court_name', $service_id);
    if (!$service || $service->post_type !== 'service' || $service->post_status !== 'publish') {
        return new WP_Error('invalid_service', 'This court listing is no longer available.');
    }
    if (!is_array($courts) || !array_key_exists($court_index, $courts)) {
        return new WP_Error('invalid_court', 'The selected court could not be found.');
    }

    $timezone = new DateTimeZone('Asia/Manila');
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', (string) $date_raw, $timezone);
    $start_time = DateTimeImmutable::createFromFormat('!g:i A', strtoupper(trim((string) $start_raw)), $timezone);
    $end_time = DateTimeImmutable::createFromFormat('!g:i A', strtoupper(trim((string) $end_raw)), $timezone);
    if (!$date || !$start_time || !$end_time || $start_time->format('i') !== '00' || $end_time->format('i') !== '00' || $start_time->format('H:i') === $end_time->format('H:i')) {
        return new WP_Error('invalid_time', 'Please select a valid court date and time.');
    }

    $start = $date->setTime((int) $start_time->format('H'), 0);
    $end = $date->setTime((int) $end_time->format('H'), 0);
    if ($end < $start) $end = $end->modify('+1 day');
    if ($start <= new DateTimeImmutable('now', $timezone) || $end <= $start) {
        return new WP_Error('past_time', 'Please choose a future court time.');
    }

    $availability = json_decode((string) ($courts[$court_index]['court_calendar'] ?? ''), true);
    $availability_windows = [];
    if (is_array($availability)) {
        foreach ($availability as $event) {
            if (empty($event['start']) || empty($event['end'])) continue;
            try {
                $event_start = (new DateTimeImmutable($event['start'], new DateTimeZone('UTC')))->setTimezone($timezone);
                $event_end = (new DateTimeImmutable($event['end'], new DateTimeZone('UTC')))->setTimezone($timezone);
            } catch (Exception $exception) {
                continue;
            }
            if ($event_end > $event_start) $availability_windows[] = [$event_start, $event_end];
        }
    }
    usort($availability_windows, static function ($a, $b) {
        return $a[0] <=> $b[0];
    });
    $merged_windows = [];
    foreach ($availability_windows as $window) {
        $last_index = count($merged_windows) - 1;
        if ($last_index >= 0 && $window[0] <= $merged_windows[$last_index][1]) {
            if ($window[1] > $merged_windows[$last_index][1]) $merged_windows[$last_index][1] = $window[1];
        } else {
            $merged_windows[] = $window;
        }
    }
    $inside_availability = false;
    foreach ($merged_windows as $window) {
        if ($window[0] <= $start && $window[1] >= $end) {
            $inside_availability = true;
            break;
        }
    }
    if (!$inside_availability) {
        return new WP_Error('outside_availability', 'That time is outside the court’s posted availability. Please choose another slot.');
    }

    $bookings = get_posts([
        'post_type' => 'booking',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'fields' => 'ids',
    ]);
    foreach ($bookings as $booking_id) {
        $saved_service = get_field('service_id', $booking_id);
        if (is_array($saved_service) && isset($saved_service['ID'])) $saved_service = $saved_service['ID'];
        if (is_object($saved_service) && isset($saved_service->ID)) $saved_service = $saved_service->ID;
        if ((int) $saved_service !== (int) $service_id || (int) get_field('court_index', $booking_id) !== (int) $court_index) continue;

        $status = get_field('booking_status', $booking_id);
        if (!in_array($status, ['pending', 'approved'], true)) continue;
        if ($status === 'pending') {
            $hold_expires = get_field('hold_expires', $booking_id);
            if ($hold_expires && strtotime($hold_expires) <= time()) continue;
        }

        $existing_date = get_field('date_booked', $booking_id);
        $existing_start_raw = get_field('time_start', $booking_id);
        $existing_end_raw = get_field('time_end', $booking_id);
        if (!$existing_date || !$existing_start_raw || !$existing_end_raw) continue;
        $existing_date_obj = new DateTimeImmutable((string) $existing_date, $timezone);
        $existing_start_time = DateTimeImmutable::createFromFormat('!g:i A', strtoupper(trim((string) $existing_start_raw)), $timezone);
        $existing_end_time = DateTimeImmutable::createFromFormat('!g:i A', strtoupper(trim((string) $existing_end_raw)), $timezone);
        if (!$existing_start_time || !$existing_end_time) continue;
        $existing_start = $existing_date_obj->setTime((int) $existing_start_time->format('H'), (int) $existing_start_time->format('i'));
        $existing_end = $existing_date_obj->setTime((int) $existing_end_time->format('H'), (int) $existing_end_time->format('i'));
        if ($existing_end <= $existing_start) $existing_end = $existing_end->modify('+1 day');
        if ($start < $existing_end && $end > $existing_start) {
            return new WP_Error('slot_taken', 'That court time has just been booked. Please refresh the calendar and choose another time.');
        }
    }

    $court_rate = (float) ($courts[$court_index]['rate'] ?? 0);
    $hours = (int) (($end->getTimestamp() - $start->getTimestamp()) / HOUR_IN_SECONDS);
    if ($court_rate <= 0 || $hours < 1) {
        return new WP_Error('invalid_rate', 'This court does not have a valid hourly rate.');
    }
    $base = $court_rate * $hours;
    $paymongo_rate = (float) get_field('percentage_paymongo', 'option');
    $booking_rate = (float) get_field('percentage_web_fee', 'option');

    return [
        'date' => $start->format('Y-m-d'),
        'start' => $start->format('g:i A'),
        'end' => $end->format('g:i A'),
        'amount' => round($base + ($base * $paymongo_rate / 100) + ($base * $booking_rate / 100), 2),
    ];
}

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
    $name    = sanitize_text_field($_POST['name'] ?? '');
    $email   = sanitize_email($_POST['email'] ?? '');
    $comment = sanitize_textarea_field($_POST['comment'] ?? '');
    $amount  = floatval($_POST['amount'] ?? 0);

    // Normalize formats (IMPORTANT - must match calendar)
    $start = sanitize_text_field($_POST['start'] ?? '');
    $end   = sanitize_text_field($_POST['end'] ?? '');
    $date  = sanitize_text_field($_POST['date'] ?? '');

    if (!$name || !$email || !$start || !$end || !$date) {
        wp_send_json_error('Missing required fields');
    }

    $service_id = 0;
    $court_index = -1;
    if ($type === 'service') {
        $service_id = absint($_POST['service_id'] ?? 0);
        $court_index = isset($_POST['court_index']) ? (int) $_POST['court_index'] : -1;
        if (!$service_id || $court_index < 0) {
            wp_send_json_error('Please select a court and an available time.');
        }
        $validated_booking = mp_validate_court_booking($service_id, $court_index, $date, $start, $end);
        if (is_wp_error($validated_booking)) {
            wp_send_json_error($validated_booking->get_error_message());
        }
        $date = $validated_booking['date'];
        $start = $validated_booking['start'];
        $end = $validated_booking['end'];
        $amount = $validated_booking['amount'];
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
