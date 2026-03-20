 <div class = "availability-of-coach" id = "booknow">
                            <h3>Availability Calendar</h3>
<?php 
$datacoach = get_field('avalability');
$dates = json_decode($datacoach, true);
// remove duplicate ranges (safety guard)

// echo '<pre>';
//  var_dump($datacoach);
// echo '</pre>';
$unique = [];
$seen = [];

foreach ($dates as $event) {

    // create unique key using start and end time
    $key = $event['start'] . '-' . $event['end'];

    // if not seen yet, store it
    if (!isset($seen[$key])) {
        $seen[$key] = true;
        $unique[] = $event;
    }
}

// replace with cleaned array
$dates = $unique;
// Get current time
$now = new DateTime();

$dates = array_filter($dates, function($event) use ($now) {
    $endDate = new DateTime($event['end']);
    return $endDate >= $now;

});

// Reset array keys (important for loops / sliders)
$dates = array_values($dates);

$rate_value = get_field('hourly_rate'); 

// 2. Convert to a clean number (remove commas or currency signs if any)
$clean_rate = filter_var($rate_value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
if(empty($clean_rate)) { $clean_rate = 0; }

if($dates) {
    // Get coach ID from post author
    $coach_id = get_post_field('post_author', get_the_ID());

    // Get bookings for this coach (pending + approved)
    $args = [
        'post_type' => 'booking',
        'posts_per_page' => -1,
        'meta_query' => [
            [
                'key' => 'coach__services',
                'value' => $coach_id,
                'compare' => '='
            ],
            [
                'key' => 'booking_status',
                'value' => ['pending', 'approved'],
                'compare' => 'IN'
            ]
        ]
    ];

    $booking_posts = get_posts($args);

    $blocked_slots = [];

 foreach ($booking_posts as $booking) {
    $date_raw  = get_field('date_booked', $booking->ID);
    $start_raw = get_field('time_start', $booking->ID);
    $end_raw   = get_field('time_end', $booking->ID);
    $status    = get_field('booking_status', $booking->ID);

    if ($date_raw && $start_raw && $end_raw) {

        $date = date('Y-m-d', strtotime($date_raw));

        $start = new DateTime($start_raw);
        $end   = new DateTime($end_raw);

        // Loop through each hour in the range
        $current = clone $start;

       while ($current < $end){
            $slot_time = $current->format('g:00 A');
            $blocked_slots[$date][$slot_time] = $status;
            $current->modify('+1 hour');
        }
    }
}




    usort($dates, function($a, $b) {
        return strtotime($a['start']) - strtotime($b['start']);
    });

    $timezone = new DateTimeZone('Asia/Manila'); 
    echo '<ul id = "booknow-now" class="step-1-wrapper slider-calendar" data-rate="'.$clean_rate.'">';
    
    foreach ($dates as $date) {
        $start = new DateTime($date['start']);
        $start->setTimezone($timezone);
        $end = new DateTime($date['end']);
        $end->setTimezone($timezone);
        echo '<li class="day-parent">'; 
        echo '<div class="day-booked">
                <div class="month">'.$start->format('F') .'</div>
                <strong>' . $start->format('l, m/d/Y') . '</strong>';
        // --- MOVED INPUT HERE ---
        // Placing it inside an existing div prevents it from breaking the UL layout
        $slots = [];
      $temp_curr = clone $start;
        while ($temp_curr < $end) {
            $slots[] = $temp_curr->format('g:00 A');
            $temp_curr->modify('+1 hour');
        }

        // Ensure at least 2 slots (important for 1-hour availability)
        if (count($slots) === 1) {
            $extra = clone $start;
            $extra->modify('+1 hour');
            $slots[] = $extra->format('g:00 A');
        }
        echo '<input type="hidden" class="day-slots" value="'.htmlspecialchars(json_encode($slots)).'">';
        // ------------------------
        echo '</div>'; // close day-booked
        echo '<ul class="step-2-wrapper">';
$last_index = count($slots) - 1;

foreach ($slots as $index => $time_val) {

    // ❌ Skip last slot (end boundary only)
    if ($index === $last_index) {
        continue;
    }

    $slot_date = $start->format('Y-m-d');
    $status = $blocked_slots[$slot_date][$time_val] ?? '';

    $class = 'availability';
    $style = 'cursor:pointer;';
    $label = $time_val;

    if ($status === 'pending') {
        $class .= ' pending';
        $label .= ' (Pending)';
    }

    if ($status === 'approved') {
        $class .= ' booked';
        $label .= ' (Booked)';
    }

    echo '<li class="'.$class.'" 
            data-time="'.$time_val.'" 
            data-date="'.$slot_date.'" 
            style="'.$style.'">' . $label .'</li>';
}

        echo '</ul>';
        echo '</li>';
    }
    echo '</ul>';
}
?>
                       

</div>
<!--  -->
<div id="bookingModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content p-4">
            <div class="modal-header border-0">
                <h3 class="modal-title">Book this slot</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <label>Name:</label>
                <input type="text" id="booking_name" class="form-control mb-3" placeholder="Enter your name" required>
                <input type="hidden" id="selected_date">

                <label>Email:</label>
                <input type="email" id="booking_email" class="form-control mb-3" placeholder="Enter your email" required>

                <div class="row">
                    <div class="col-md-6">
                        <label>Start Time:</label>
                        <select id="booking_start" class="form-control mb-3"></select>
                    </div>
                    <div class="col-md-6">
                        <label>End Time:</label>
                        <select id="booking_end" class="form-control mb-3"></select>
                    </div>
                </div>

                <label>Comment (optional):</label>
                <textarea id="booking_comment" class="form-control mb-3" rows="3"></textarea>
                
                <label>Total Amount:</label>
                <div class="total-amount">
                    <input id="amount" type="text" class="form-control" placeholder="0.00">
                </div>
            </div>

            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" id="confirm_booking_btn" class="btn btn-primary">Pay Now</button>
            </div>
        </div>
    </div>
</div>
<script>
var coachBookingData = {
    coach_id: <?php echo get_post_field('post_author', get_the_ID()); ?>
};
console.log(coachBookingData);
</script>