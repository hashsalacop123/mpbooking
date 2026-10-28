<?php
$service_id = get_the_ID();
$court_rows = get_field('court_name', $service_id);
$timezone = new DateTimeZone('Asia/Manila');
$utc = new DateTimeZone('UTC');
$now = new DateTimeImmutable('now', $timezone);
$calendar = [
    'serviceId' => (int) $service_id,
    'timezone' => 'Asia/Manila',
    'courts' => [],
    'days' => [],
];

if (is_array($court_rows)) {
    foreach ($court_rows as $court_index => $court) {
        if (!is_array($court)) continue;
        $calendar['courts'][$court_index] = [
            'name' => (string) ($court['courth_name_number'] ?? 'Court ' . ($court_index + 1)),
            'rate' => (float) ($court['rate'] ?? 0),
            'courtType' => (string) ($court['court_type'] ?? ''),
            'surface' => (string) ($court['surface_type'] ?? ''),
            'size' => (string) ($court['court_size'] ?? ''),
        ];

        $events = json_decode((string) ($court['court_calendar'] ?? ''), true);
        if (!is_array($events)) continue;

        foreach ($events as $event) {
            if (empty($event['start']) || empty($event['end'])) continue;
            try {
                $event_start = (new DateTimeImmutable($event['start'], $utc))->setTimezone($timezone);
                $event_end = (new DateTimeImmutable($event['end'], $utc))->setTimezone($timezone);
            } catch (Exception $exception) {
                continue;
            }
            if ($event_end <= $event_start) continue;

            // Slots start on the hour and are offered only when the entire hour
            // fits inside the coach's/court's submitted availability window.
            $slot_start = $event_start->setTime((int) $event_start->format('H'), 0, 0);
            if ($slot_start < $event_start) $slot_start = $slot_start->modify('+1 hour');
            while ($slot_start->modify('+1 hour') <= $event_end) {
                $slot_end = $slot_start->modify('+1 hour');
                if ($slot_start > $now) {
                    $date_key = $slot_start->format('Y-m-d');
                    $time_key = $slot_start->format('g:i A');
                    $calendar['days'][$date_key]['slots'][$court_index][$time_key] = [
                        'status' => 'available',
                        'start' => $slot_start->getTimestamp(),
                        'end' => $slot_end->getTimestamp(),
                    ];
                }
                $slot_start = $slot_end;
            }
        }
    }
}

// Apply active holds and paid bookings to matching court and date slots.
$booking_posts = get_posts([
    'post_type' => 'booking',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'fields' => 'ids',
]);

foreach ($booking_posts as $booking_id) {
    $saved_service = get_field('service_id', $booking_id);
    if (is_array($saved_service) && isset($saved_service['ID'])) $saved_service = $saved_service['ID'];
    if (is_object($saved_service) && isset($saved_service->ID)) $saved_service = $saved_service->ID;
    if ((int) $saved_service !== (int) $service_id) continue;

    $status = get_field('booking_status', $booking_id);
    if (!in_array($status, ['pending', 'approved'], true)) continue;
    if ($status === 'pending') {
        $hold_expires = get_field('hold_expires', $booking_id);
        if ($hold_expires && strtotime($hold_expires) <= time()) continue;
    }

    $court_index = (int) get_field('court_index', $booking_id);
    $date_raw = get_field('date_booked', $booking_id);
    $start_raw = get_field('time_start', $booking_id);
    $end_raw = get_field('time_end', $booking_id);
    if (!$date_raw || !$start_raw || !$end_raw) continue;

    try {
        $booking_date = new DateTimeImmutable((string) $date_raw, $timezone);
        $booking_start = DateTimeImmutable::createFromFormat('!g:i A', (string) $start_raw, $timezone);
        $booking_end = DateTimeImmutable::createFromFormat('!g:i A', (string) $end_raw, $timezone);
        if (!$booking_start || !$booking_end) continue;
        $booking_start = $booking_date->setTime((int) $booking_start->format('H'), (int) $booking_start->format('i'));
        $booking_end = $booking_date->setTime((int) $booking_end->format('H'), (int) $booking_end->format('i'));
        if ($booking_end <= $booking_start) $booking_end = $booking_end->modify('+1 day');
    } catch (Exception $exception) {
        continue;
    }

    foreach ($calendar['days'] as $date_key => &$day) {
        foreach (($day['slots'][$court_index] ?? []) as $time_key => &$slot) {
            if ($slot['start'] < $booking_end->getTimestamp() && $slot['end'] > $booking_start->getTimestamp()) {
                $slot['status'] = $status;
            }
        }
        unset($slot);
    }
    unset($day);
}

// Keep fully booked days in the month grid so visitors can distinguish them
// from dates that have no schedule at all.
ksort($calendar['days']);

$paymongo_fee = (float) get_field('percentage_paymongo', 'option');
$web_admin_fee = (float) get_field('percentage_web_fee', 'option');
?>
<section class="mp-court-booking" id="booknow" aria-label="Court availability and booking">
    <div class="mp-court-calendar-card">
        <div class="mp-court-calendar-heading">
            <button type="button" class="mp-court-month-nav mp-court-month-prev" aria-label="Previous month">‹</button>
            <h2 class="mp-court-month-label" aria-live="polite"></h2>
            <button type="button" class="mp-court-month-nav mp-court-month-next" aria-label="Next month">›</button>
        </div>
        <div class="mp-court-weekdays" aria-hidden="true">
            <span>Sunday</span><span>Monday</span><span>Tuesday</span><span>Wednesday</span><span>Thursday</span><span>Friday</span><span>Saturday</span>
        </div>
        <div class="mp-court-month-grid" role="grid" aria-label="Available court dates"></div>
    </div>

    <aside class="mp-court-contact-card">
        <div>
            <h2>Hourly Range Rate</h2>
            <?php
            $rates = [];
            foreach ($calendar['courts'] as $court_data) {
                if ($court_data['rate'] > 0) $rates[] = $court_data['rate'];
            }
            if ($rates) : ?>
                <p><?php echo esc_html(number_format_i18n(min($rates))); ?><?php if (min($rates) !== max($rates)) : ?> - <?php echo esc_html(number_format_i18n(max($rates))); ?><?php endif; ?> PHP</p>
            <?php else : ?><p>Contact for rate</p><?php endif; ?>
        </div>
        <div>
            <h2>Contact Us Directly</h2>
            <?php if ($email = get_field('email', $service_id)) : ?><p><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></p><?php endif; ?>
            <?php if ($phone = get_field('phone', $service_id)) : ?><p><a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a></p><?php endif; ?>
            <?php if ($website = get_field('website', $service_id)) : ?><p><a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener noreferrer">Website</a></p><?php endif; ?>
        </div>
        <div>
            <h2>Follow Us</h2>
            <?php
            $social_icons = ['facebook' => 'fa-facebook-f', 'instagram' => 'fa-instagram', 'x' => 'fa-x-twitter', 'linkedin' => 'fa-linkedin-in', 'youtube' => 'fa-youtube'];
            $social_media = get_field('social_media', $service_id);
            ?>
            <div class="mp-court-socials">
                <?php if (is_array($social_media)) : foreach ($social_media as $social) :
                    $platform = strtolower((string) ($social['social_media_account'] ?? ''));
                    $url = $social['url'] ?? '';
                    if (!$url) continue;
                    $icon = $social_icons[$platform] ?? '';
                    if (!$icon) continue;
                    ?>
                    <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr(ucfirst($platform)); ?>"><i class="fa-brands <?php echo esc_attr($icon); ?>" aria-hidden="true"></i></a>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </aside>

    <div class="mp-court-availability-wrap">
        <div class="mp-court-availability-heading">
            <h2>Courts Availability</h2>
            <div class="mp-court-legend" aria-label="Availability legend"><span><i class="available"></i>Available</span><span><i class="pending"></i>Pending</span><span><i class="booked"></i>Booked</span></div>
        </div>
        <div class="mp-court-availability-table-wrap">
            <table class="mp-court-availability-table">
                <thead>
                    <tr class="mp-court-table-date"><th colspan="<?php echo esc_attr(max(2, count($calendar['courts']) + 1)); ?>"><div class="mp-court-table-date-inner"><span class="mp-court-selected-date"></span><button type="button" class="mp-court-refresh">Refresh</button></div></th></tr>
                    <tr class="mp-court-column-head"><th scope="col">Time</th><?php foreach ($calendar['courts'] as $court_data) : ?><th scope="col"><?php echo esc_html($court_data['name']); ?></th><?php endforeach; ?></tr>
                </thead>
                <tbody class="mp-court-table-body"></tbody>
            </table>
        </div>
        <p class="mp-court-empty-availability" hidden>No court times are available for this date. Choose another highlighted date.</p>
    </div>
</section>

<script type="application/json" id="mp-court-calendar-data"><?php echo wp_json_encode($calendar, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>

<div id="bookingModalService" class="modal fade" tabindex="-1" aria-labelledby="bookingModalServiceTitle" aria-hidden="true">
    <div class="modal-dialog"><div class="modal-content p-4">
        <div class="modal-header border-0"><h3 id="bookingModalServiceTitle">Book this court</h3><button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button></div>
        <div class="modal-body">
            <p class="mp-court-modal-summary"></p>
            <label for="booking_name">Name</label><input type="text" id="booking_name" class="form-control mb-2" autocomplete="name">
            <label for="booking_email">Email</label><input type="email" id="booking_email" class="form-control mb-2" autocomplete="email">
            <input type="hidden" id="selected_date"><input type="hidden" id="selected_court">
            <div class="row"><div class="col-md-6"><label for="booking_start">Start time</label><select id="booking_start" class="form-control mb-2"></select></div><div class="col-md-6"><label for="booking_end">End time</label><select id="booking_end" class="form-control mb-2"></select></div></div>
            <label for="booking_comment">Note (optional)</label><textarea id="booking_comment" class="form-control mb-2"></textarea>
            <input id="paymongo_fee" type="hidden" value="<?php echo esc_attr($paymongo_fee); ?>"><input id="web_admin_fee" type="hidden" value="<?php echo esc_attr($web_admin_fee); ?>">
            <input id="amount" type="hidden" value="0">
            <div id="booking_summary" class="mp-court-booking-summary" hidden><strong>Booking Summary</strong><div class="summary-content"></div></div>
        </div>
        <div class="modal-footer border-0"><button class="btn btn-secondary" data-dismiss="modal">Cancel</button><button id="confirm_booking_btn_service" class="btn btn-primary">Continue to payment</button></div>
    </div></div>
</div>
<script>
window.bookingData = window.bookingData || {};
window.bookingData.ajaxurl = <?php echo wp_json_encode(admin_url('admin-ajax.php')); ?>;
window.bookingData.nonce = <?php echo wp_json_encode(wp_create_nonce('booking_nonce')); ?>;
</script>
