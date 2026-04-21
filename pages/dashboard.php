<?php
// Template Name: Dashboard
acf_form_head(); // Must be first
get_header();
?>

<div class="dasboard-wrapper-page">
    <div class="container">
        <div class="row">

          <?php hash_show_pending_registration_notice(); ?>

            <!-- SIDEBAR START HERE -->
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                <?php include get_template_directory() . '/dashboard/dashboard-sidebar.php'; ?>
            </div>

            <!-- CONTENT START HERE -->
            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">


                <h4>Bookings</h4>

                <?php
                $current_user_id = get_current_user_id();

                $args = [
                    'post_type'      => 'booking',
                    'posts_per_page' => -1,
                    'meta_query'     => [
                        [
                            'key'     => 'coach__services',
                            'value'   => $current_user_id,
                            'compare' => '='
                        ]
                    ]
                ];

                $bookings = get_posts($args);

                if ($bookings) :
                ?>
                    <table id="bookings" class="display tables-general">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Reference</th>
                                <th>Date</th>
                                <th>Time Start</th>
                                <th>Time End</th>
                                <th>Email</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($bookings as $booking) {

                                $name   = get_field('guest_name', $booking->ID);
                                $reference   = get_field('paymongo_reference', $booking->ID);
                                $email  = get_field('guest_email', $booking->ID);
                                $date   = get_field('date_booked', $booking->ID);
                                $start  = get_field('time_start', $booking->ID);
                                $end    = get_field('time_end', $booking->ID);
                                $amount = get_field('amount', $booking->ID);
                                $status = get_field('booking_status', $booking->ID);

                                $status_color = '';

                                if ($status == 'approved') {
                                    $status_color = 'approved-color';
                                } elseif ($status == 'pending') {
                                    $status_color = 'pending-color';
                                } else {
                                    $status_color = 'reject-color';
                                }
                            ?>
                                <tr>
                                    <td><?php echo esc_html($name); ?></td>
                                     <td><?php echo esc_html($reference); ?></td>
                                    <td class="date-booked"><?php echo esc_html($date); ?></td>
                                    <td><?php echo esc_html($start); ?></td>
                                    <td><?php echo esc_html($end); ?></td>
                                    <td><?php echo esc_html($email); ?></td>
                                    <td>&#x20B1; <?php echo esc_html($amount); ?></td>
                                    <td>
                                        <span class="<?php echo $status_color; ?>">
                                            <?php echo esc_html($status); ?>
                                        </span>
                                    </td>
                                    <td class="button-action">
                                        <button 
                                            class="btn btn-primary open-booking-modal"
                                            data-id="<?php echo $booking->ID; ?>"
                                            data-name="<?php echo esc_attr($name); ?>"
                                            data-email="<?php echo esc_attr($email); ?>"
                                            data-date="<?php echo esc_attr($date); ?>"
                                            data-start="<?php echo esc_attr($start); ?>"
                                            data-end="<?php echo esc_attr($end); ?>"
                                            data-amount="<?php echo esc_attr($amount); ?>"
                                            data-status="<?php echo esc_attr($status); ?>"
                                        >
                                            Action
                                        </button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php
                else :
                    echo '<p>No bookings yet.</p>';
                endif;
                ?>

            </div> <!-- content col -->
        </div>
    </div>
</div>
<!-- Booking Modal -->
<div id="bookingModal" class="booking-modal" style="display:none;">
    <div class="booking-modal-content">
        <h4>Booking Details</h4>

        <input type="hidden" id="modal-booking-id">

        <label>Name</label>
        <input type="text" id="modal-name" disabled>

        <label>Email</label>
        <input type="text" id="modal-email" disabled>

        <label>Date</label>
        <input type="text" id="modal-date" disabled>

        <label>Time Start</label>
        <input type="text" id="modal-start" disabled>

        <label>Time End</label>
        <input type="text" id="modal-end" disabled>

        <label>Amount</label>
        <input type="text" id="modal-amount" disabled>

        <label>Status</label>
        <select id="modal-status">
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="refunded">Refunded</option>
             <option value="expired">Expired</option>

            <option value="refund_pending">Refund pending</option>

        </select>

        <div style="margin-top:15px;">
            <button id="update-booking" class="btn btn-success">Update</button>
            <button id="close-modal" class="btn btn-secondary">Close</button>
        </div>
    </div>
</div>


<?php get_footer(); ?>
