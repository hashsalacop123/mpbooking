<?php

/**
 * ------------------------------------------------------------
 * Create PayMongo GCash payment link
 * ------------------------------------------------------------
 */
function create_paymongo_gcash_payment($amount, $description, $booking_id) {

    // PayMongo secret key
    $secret_key = PAYMONGO_SECRET_KEY;

    // Payment data
    $data = [
        'data' => [
            'attributes' => [
                'amount' => intval($amount * 100), // convert to cents
                'description' => $description,
                'remarks' => 'Booking ID: ' . $booking_id,
                'payment_method_types' => ['gcash'],
                'redirect' => [
                    'success' => home_url('/payment-success/?booking_id=' . $booking_id),
                    'failed'  => home_url('/payment-failed/')
                ]
            ]
        ]
    ];

    // API request arguments
    $args = [
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode($secret_key . ':'),
            'Content-Type'  => 'application/json'
        ],
        'body' => json_encode($data),
        'method' => 'POST'
    ];

    // Send request to PayMongo
    $response = wp_remote_post(
        'https://api.paymongo.com/v1/links',
        $args
    );

    // Handle API error
    if (is_wp_error($response)) {
        error_log('PayMongo error: ' . $response->get_error_message());
        return false;
    }

    // Get response body
    $body_raw = wp_remote_retrieve_body($response);
    error_log('PayMongo response: ' . $body_raw);

    $body = json_decode($body_raw, true);

    // Return checkout URL
    return $body['data']['attributes']['checkout_url'] ?? false;
}



/**
 * ------------------------------------------------------------
 * Register PayMongo webhook endpoint
 * ------------------------------------------------------------
 */
add_action('rest_api_init', function () {

    register_rest_route('paymongo/v1', '/webhook', [
        'methods'  => 'POST',
        'callback' => 'handle_paymongo_webhook',
        'permission_callback' => '__return_true',
    ]);

});



/**
 * ------------------------------------------------------------
 * Handle PayMongo webhook
 * This updates booking_status when payment succeeds
 * ------------------------------------------------------------
 */
function handle_paymongo_webhook($request) {

    error_log('PAYMONGO WEBHOOK HIT');

    $payload = $request->get_body();

    error_log('PAYMONGO WEBHOOK RECEIVED');
    error_log($payload);

    $data = json_decode($payload, true);

    if (!$data) {
        error_log('Invalid payload');
        return new WP_REST_Response(['error' => 'Invalid payload'], 400);
    }

    $event_type = $data['data']['attributes']['type'] ?? '';

    error_log('Event type: ' . $event_type);


    /* ==========================
       PAYMENT SUCCESS
    ========================== */

    if ($event_type === 'link.payment.paid') {

        $payment_data = $data['data']['attributes']['data']['attributes'];
        error_log(print_r($payment_data, true));

        $remarks = $payment_data['remarks'] ?? '';

        preg_match('/Booking ID:\s*(\d+)/', $remarks, $matches);

        if (!empty($matches[1])) {

            $booking_id = intval($matches[1]);

            error_log('Booking ID: ' . $booking_id);

            if (get_post_type($booking_id) === 'booking') {

                // Prevent duplicate approval
                $current_status = get_field('booking_status', $booking_id);

                if ($current_status !== 'approved') {

                    update_field('booking_status', 'approved', $booking_id);

                    // Safely capture reference number
                    $reference = '';

                    if (!empty($payment_data['reference_number'])) {
                        $reference = $payment_data['reference_number'];
                        update_field('paymongo_reference', $reference, $booking_id);
                        error_log('Saved PayMongo reference: ' . $reference);
                    }

                    // Booking info
                    $name  = get_post_meta($booking_id, 'guest_name', true);
                    $email = get_post_meta($booking_id, 'guest_email', true);
                    $date  = get_post_meta($booking_id, 'date_booked', true);
                    $start = get_post_meta($booking_id, 'time_start', true);
                    $end   = get_post_meta($booking_id, 'time_end', true);

                    if (!empty($email)) {

                        $subject = 'Your Booking is Confirmed';

                        $message = "
Hi {$name},

Your booking has been successfully confirmed.

Booking Details:
Date: {$date}
Time: {$start} - {$end}

Reference Number: {$reference}

Thank you for your payment.

Regards,
MatchPoint
";

                        wp_mail($email, $subject, $message);

                        error_log('Booking approved and email sent!');
                    }

                }

            }
        }
    }


    /* ==========================
       PAYMENT FAILED
    ========================== */

    if ($event_type === 'payment.failed') {

        $payment_data = $data['data']['attributes']['data']['attributes'];
        $remarks = $payment_data['remarks'] ?? '';

        preg_match('/Booking ID:\s*(\d+)/', $remarks, $matches);

        if (!empty($matches[1])) {

            $booking_id = intval($matches[1]);

            if (get_post_type($booking_id) === 'booking') {

                update_field('booking_status', 'rejected', $booking_id);

                $email = get_field('guest_email', $booking_id);
                $name  = get_field('name', $booking_id);

                if (!empty($email)) {

                    $subject = 'Payment Failed';

                    $message = "
Hi {$name},

Unfortunately your payment for the booking did not go through.

Please try again by returning to the booking page.

Regards,
MatchPoint
";

                    wp_mail($email, $subject, $message);

                    error_log('Payment failed email sent for booking: ' . $booking_id);
                }
            }
        }
    }

    return new WP_REST_Response(['received' => true], 200);
}
?>