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
$datacoach = get_field('avalability');
$dates = json_decode($datacoach, true);

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
| REMOVE PAST
|--------------------------------------------------------------------------
*/
$now = new DateTime();

$dates = array_filter($dates, function($event) use ($now) {
    $endDate = new DateTime($event['end']);
    return $endDate >= $now;
});

$dates = array_values($dates);

/*
|--------------------------------------------------------------------------
| RATE
|--------------------------------------------------------------------------
*/
$rate_value = get_field('hourly_rate');
$clean_rate = filter_var($rate_value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
if(empty($clean_rate)) { $clean_rate = 0; }

if ($dates):

    $coach_id = get_post_field('post_author', get_the_ID());

    /*
    |--------------------------------------------------------------------------
    | FETCH BOOKINGS (SIMPLE)
    |--------------------------------------------------------------------------
    */
    $booking_posts = get_posts([
        'post_type' => 'booking',
        'posts_per_page' => -1,
    ]);

    /*
    |--------------------------------------------------------------------------
    | BUILD BLOCKED SLOTS
    |--------------------------------------------------------------------------
    */
    $blocked_slots = [];

    foreach ($booking_posts as $booking) {

        // ✅ FIX: use raw meta (same as old behavior)
            $coach_field = get_field('coach__services', $booking->ID);

            $coach_saved_id = 0;

            if (is_array($coach_field) && isset($coach_field['ID'])) {
                $coach_saved_id = $coach_field['ID'];
            } elseif (is_object($coach_field) && isset($coach_field->ID)) {
                $coach_saved_id = $coach_field->ID;
            } else {
                $coach_saved_id = intval($coach_field);
            }
            if (intval($coach_saved_id) !== intval($coach_id)) {      
                      continue;
        }

        $date_raw  = get_field('date_booked', $booking->ID);
        $start_raw = get_field('time_start', $booking->ID);
        $end_raw   = get_field('time_end', $booking->ID);
        $status    = get_field('booking_status', $booking->ID);

        if (!$date_raw || !$start_raw || !$end_raw) continue;

$utc = new DateTimeZone('UTC');
$manila = new DateTimeZone('Asia/Manila');

$date_obj = new DateTime($date_raw, $utc);
$date_obj->setTimezone($manila);

$date = $date_obj->format('Y-m-d');
 $start = new DateTime($start_raw);
$end   = new DateTime($end_raw);

// 🔥 FIX: ensure full last hour is included
// $end->modify('+1 minute');

       $current = clone $start;
//        error_log('START: ' . $start->format('H:i'));
// error_log('END: ' . $end->format('H:i'));

        $current = clone $start;

        while ($current < $end) {

            $slotKey = $current->format('g:00 A');

            if (!isset($blocked_slots[$date][$slotKey])) {
                $blocked_slots[$date][$slotKey] = $status;
            } else {

                $existing = $blocked_slots[$date][$slotKey];

                if ($status === 'approved') {
                    $blocked_slots[$date][$slotKey] = 'approved';
                }
                elseif ($status === 'pending' && $existing === 'expired') {
                    $blocked_slots[$date][$slotKey] = 'pending';
                }
            }

            $current->modify('+1 hour');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SORT DATES
    |--------------------------------------------------------------------------
    */
    usort($dates, function($a, $b) {
        return strtotime($a['start']) - strtotime($b['start']);
    });

    $timezone = new DateTimeZone('Asia/Manila');

    echo '<ul id="booknow-now" class="step-1-wrapper slider-calendar" data-rate="'.$clean_rate.'">';

    foreach ($dates as $date):

    $utc = new DateTimeZone('UTC');
    $manila = new DateTimeZone('Asia/Manila');

    $start = new DateTime($date['start'], $utc);
    $start->setTimezone($manila);

    $end = new DateTime($date['end'], $utc);
    $end->setTimezone($manila);

        echo '<li class="day-parent">';

        echo '<div class="day-booked">
                <div class="month">'.$start->format('F').'</div>
                <strong>'.$start->format('l, m/d/Y').'</strong>';

   
       /*
|--------------------------------------------------------------------------
| GENERATE SLOTS (FIXED)
|--------------------------------------------------------------------------
*/
        $slots = [];
        $temp_curr = clone $start;

        // include full range
       while ($temp_curr < $end) {
            $slots[] = $temp_curr->format('g:00 A');
            $temp_curr->modify('+1 hour');
        }

        // remove last slot for UI (so it can't be selected as start)
        $last_index = count($slots) - 1;
                echo '<input type="hidden" class="day-slots" value="'.htmlspecialchars(json_encode($slots)).'">';
                echo '</div>';

        echo '<ul class="step-2-wrapper">';

        $last_index = count($slots) - 1;

        foreach ($slots as $index => $time_val):


            $slot_date = $start->format('Y-m-d');
  $status = '';

if (isset($blocked_slots[$slot_date])) {

    $normalized_slot = date('H:i', strtotime($time_val));

    foreach ($blocked_slots[$slot_date] as $key => $val) {

        $normalized_key = date('H:i', strtotime($key));


        if ($normalized_key === $normalized_slot) {
            $status = $val;
            break;
        }
    }
}
            $class = 'availability-coach';
            $style = 'cursor:pointer;';
            $label = date('h:i A', strtotime($time_val));
            if ($status === 'pending') {
                $class .= ' pending';
                // $label .= ' (Pending)';
                $style = 'cursor:not-allowed;';
            }

            if ($status === 'approved') {
                $class .= ' booked';
                $label .= '';
                $style = 'cursor:not-allowed;';
            }

            echo '<li class="'.$class.'" 
                    data-time="'.$time_val.'" 
                    data-date="'.$slot_date.'" 
                    data-status="'.$status.'" 
                    style="'.$style.'">'.$label.'</li>';

        endforeach;

        echo '</ul>';
        echo '</li>';

    endforeach;

    echo '</ul>';

endif;
?>

</div>

<!-- ========================= MODAL ========================= -->

<div id="bookingModal" class="modal fade" tabindex="-1">
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
                  <?php 
                        $paymongo_fee = get_field('percentage_paymongo','option');
                        $percentage_web_fee = get_field('percentage_web_fee','option');

                    ?>
                    <input id = "paymongo_fee" type = "hidden" value = "<?php echo $paymongo_fee; ?>">
                    <input id = "web_admin_fee" type = "hidden" value = "<?php echo $percentage_web_fee; ?>">

                <input id="amount" type="text" class="form-control" placeholder="0.00">

            </div>
              <div id="booking_summary" class="p-3 mt-3" style="background:#f5f5f5; border-radius:8px; display:none;">
                    <strong>Booking Summary</strong>
                    <div class="summary-content mt-2"></div>
                </div>

            <div class="modal-footer border-0">
                <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button id="confirm_booking_btn_coach" class="btn btn-primary">Pay Now</button>
            </div>

        </div>
    </div>
</div>

<script>
var bookingData = {
    ajaxurl: "<?php echo admin_url('admin-ajax.php'); ?>",
    nonce: "<?php echo wp_create_nonce('booking_nonce'); ?>",
    coach_id: <?php echo get_the_ID(); ?>
};
</script>