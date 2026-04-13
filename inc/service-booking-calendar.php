<div class="availability-of-coach" id="booknow">
<h3>Availability Calendar</h3>
<div class = "availability-indicator">
    <h6>Color indicator to show availability.</h6>
    <ul>
        <li><span>Time</span> Pending</li>
        <li><span>Time</span> Booked</li>
    </ul>
</div>
<?php

$courts = get_field('court_name');

if ($courts):

    foreach ($courts as $court_index => $court):

        $court_label = $court['courth_name_number'];
        $datacoach   = $court['court_calendar'];
        $rate        = $court['rate'];

        $dates = json_decode($datacoach, true);

        if (empty($dates)) continue;

        /*
        |--------------------------------------------------------------------------
        | REMOVE DUPLICATES
        |--------------------------------------------------------------------------
        */
        $unique = [];
        $seen = [];

        foreach ($dates as $event) {
            $key = $event['start'] . '-' . $event['end'];
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $unique[] = $event;
            }
        }

        $dates = $unique;

        /*
        |--------------------------------------------------------------------------
        | REMOVE PAST DATES
        |--------------------------------------------------------------------------
        */
        $now = new DateTime();

        $dates = array_filter($dates, function($event) use ($now) {
            $endDate = new DateTime($event['end']);
            return $endDate >= $now;
        });

        $dates = array_values($dates);

        if (!$dates) continue;

        /*
        |--------------------------------------------------------------------------
        | FETCH BOOKINGS (SINGLE SLOT BLOCKING LIKE COACH)
        |--------------------------------------------------------------------------
        */
        $booking_posts = get_posts([
            'post_type' => 'booking',
            'posts_per_page' => -1,
        ]);

        $blocked_slots = [];

        foreach ($booking_posts as $booking) {

            $saved_service = get_field('service_id', $booking->ID);
            $saved_court   = get_field('court_index', $booking->ID);

            if (intval($saved_service) !== intval(get_the_ID())) continue;
            if (intval($saved_court) !== intval($court_index)) continue;

            $date_raw  = get_field('date_booked', $booking->ID);
            $start_raw = get_field('time_start', $booking->ID);
            $end_raw   = get_field('time_end', $booking->ID);
            $status    = get_field('booking_status', $booking->ID);

            if (!$date_raw || !$start_raw || !$end_raw) continue;

            $date = date('Y-m-d', strtotime($date_raw));

            $start_dt = new DateTime($start_raw);
            $end_dt   = new DateTime($end_raw);

            $current = clone $start_dt;

    while ($current < $end_dt) {
    $slot_time = $current->format('h:00 A');
                    // Initialize if not set
                    if (!isset($blocked_slots[$date][$slot_time])) {
                        $blocked_slots[$date][$slot_time] = $status;
                    } else {

                        // PRIORITY LOGIC
                        $existing = $blocked_slots[$date][$slot_time];

                        // approved always wins
                        if ($status === 'approved') {
                            $blocked_slots[$date][$slot_time] = 'approved';
                        }
                        // pending overrides expired
                        elseif ($status === 'pending' && $existing === 'expired') {
                            $blocked_slots[$date][$slot_time] = 'pending';
                        }
                        // expired should NOT override anything
                    }

                    $current->modify('+1 hour');
                }
        }

        echo '<div class="container-title-and-rate">';
        echo '<h4>'.$court_label.'</h4>';
        echo '<h4>PHP '.$rate.'</h4>';
        echo '</div>';

        echo '<ul class="step-1-wrapper slider-calendar" 
                data-court="'.$court_index.'" 
                data-service="'.get_the_ID().'">';

        foreach ($dates as $date):

            /*
            |--------------------------------------------------------------------------
            | TIMEZONE FIX
            |--------------------------------------------------------------------------
            */
            $timezone = new DateTimeZone('Asia/Manila');

            $start = new DateTime($date['start'], new DateTimeZone('UTC'));
            $start->setTimezone($timezone);

            $end = new DateTime($date['end'], new DateTimeZone('UTC'));
            $end->setTimezone($timezone);

            echo '<li class="day-parent">';

            echo '<div class="day-booked">
                    <strong>'.$start->format('l, m/d/Y').'</strong>';

            /*
            |--------------------------------------------------------------------------
            | GENERATE SLOTS
            |--------------------------------------------------------------------------
            */
            $slots = [];
            $temp = clone $start;

            while ($temp < $end) {
                $slots[] = $temp->format('h:00 A');
                $temp->modify('+1 hour');
            }

            echo '<input type="hidden" class="day-slots" value="'.htmlspecialchars(json_encode($slots)).'">';
            echo '</div>';

            echo '<ul class="step-2-wrapper">';

            foreach ($slots as $time_val):

                $slot_date = $start->format('Y-m-d');
                $status = $blocked_slots[$slot_date][$time_val] ?? '';

                $class = 'availability-service';
                $style = '';
                $label = $time_val;

    

                if ($status === 'pending') {
                    $class .= ' pending';
                    $label .= '';
                    $style = 'pointer-events:none;';
                }

                if ($status === 'approved') {
                    $class .= ' booked';
                    $label .= '';
                    $style = 'pointer-events:none;';
                }

                echo '<li class="'.$class.'"
                        data-time="'.$time_val.'"
                        data-date="'.$slot_date.'"
                        data-court="'.$court_index.'"
                        data-rate="'.$rate.'"
                        style="'.$style.'">'.$label.'</li>';

            endforeach;

            echo '</ul>';
            echo '</li>';

        endforeach;

        echo '</ul>';

    endforeach;

endif;
?>

</div>

<!-- ========================= MODAL ========================= -->

<div id="bookingModalService" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content p-4">

            <div class="modal-header border-0">
                <h3>Book this slot</h3>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">

                <input type="text" id="booking_name" class="form-control mb-2" placeholder="Name">
                <input type="hidden" id="selected_date">

                <input type="email" id="booking_email" class="form-control mb-2" placeholder="Email">
                
                <div class="row">
                    <div class="col-md-6">
                        <select id="booking_start" class="form-control mb-2"></select>
                    </div>
                    <div class="col-md-6">
                        <select id="booking_end" class="form-control mb-2"></select>
                    </div>
                </div>

                <textarea id="booking_comment" class="form-control mb-2" placeholder="Comment"></textarea>

                <input id="amount" type="text" class="form-control" placeholder="0.00">

            </div>
  <div id="booking_summary" class="p-3 mt-3" style="background:#f5f5f5; border-radius:8px; display:none;">
                    <strong>Booking Summary</strong>
                    <div class="summary-content mt-2"></div>
                </div>
            <div class="modal-footer border-0">
                <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button id="confirm_booking_btn_service" class="btn btn-primary">Pay Now</button>
            </div>

        </div>
    </div>
</div>

<script>
var bookingData = {
    ajaxurl: "<?php echo admin_url('admin-ajax.php'); ?>",
    nonce: "<?php echo wp_create_nonce('booking_nonce'); ?>"
};
</script>