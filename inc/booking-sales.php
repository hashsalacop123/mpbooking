<?php 
//"THIS TEMPLATE IS TO GET THE TOTAL SALES PERMONTH AND ALL AND ALL DATA"
?>
<?php
// Get current user (coach)
$current_user_id = get_current_user_id();

// Base query
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

$query = new WP_Query($args);

// Initialize counters
$total_sales_count   = 0;
$total_sales_amount  = 0;

$month_sales_count   = 0;
$month_sales_amount  = 0;

$refund_count        = 0;
$refund_amount       = 0;

$expired_count       = 0;
$expired_amount      = 0;

// Current month
$current_month = date('m');
$current_year  = date('Y');

if ($query->have_posts()) {
    while ($query->have_posts()) {
        $query->the_post();

        // Get ACF fields
        $amount         = (float) get_field('amount');
        $status         = get_field('booking_status');
        $post_date      = get_the_date('Y-m-d');

        $post_month = date('m', strtotime($post_date));
        $post_year  = date('Y', strtotime($post_date));

        // ✅ SALES (only approved)
        if ($status === 'approved') {
            $total_sales_count++;
            $total_sales_amount += $amount;

            // Monthly sales
            if ($post_month == $current_month && $post_year == $current_year) {
                $month_sales_count++;
                $month_sales_amount += $amount;
            }
        }

        // ✅ REFUNDS (refunded only, ignore refund_pending)
        if ($status === 'refunded') {
            $refund_count++;
            $refund_amount += $amount;
        }

        // ✅ EXPIRED
        if ($status === 'expired') {
            $expired_count++;
            $expired_amount += $amount;
        }
    }
    wp_reset_postdata();
}
?>

<div class="sales-wrapper">
    <ul class="sales-list">
        <li class = "sales-amount-color">
            <img src = "<?php echo get_template_directory_uri().'/img/sales.png' ?>" class = "img-fluid">            Sales Total 
            <div class="numbers-count">(<?php echo $total_sales_count; ?>)</div>
            <div class="count total-ammount">&#8369;<?php echo number_format($total_sales_amount, 2); ?></div>
        </li>

        <li class = "sales-monthly-color">
            <img src = "<?php echo get_template_directory_uri().'/img/revenue.png' ?>" class = "img-fluid">            Month Sales 
            <div class="numbers-count">(<?php echo $month_sales_count; ?>)</div>
            <div class="count total-ammount">&#8369;<?php echo number_format($month_sales_amount, 2); ?></div>
        </li>

        <li class = "sales-refunded-color">
            <img src = "<?php echo get_template_directory_uri().'/img/refund.png' ?>" class = "img-fluid">            Refunded
            <div class="numbers-count">(<?php echo $refund_count; ?>)</div>
            <div class="count total-ammount">&#8369;<?php echo number_format($refund_amount, 2); ?></div>
        </li>

        <li class = "sales-expire-color">
            <img src = "<?php echo get_template_directory_uri().'/img/time.png' ?>" class = "img-fluid">            Exp Booking 
            <div class="numbers-count">(<?php echo $expired_count; ?>)</div>
            <div class="count total-ammount">&#8369;<?php echo number_format($expired_amount, 2); ?></div>
        </li>

    </ul>
</div>