<?php
// TEMPLATE NAME: Statistics

acf_form_head(); // Must be first
get_header();
?>


<div class="dasboard-wrapper-page">
    <div class="container">
        <div class="row">
        <?php hash_show_pending_registration_notice(); ?>
        <!-- SIDEBAR -->
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                    <?php include get_template_directory() . '/dashboard/dashboard-sidebar.php'; ?>
            </div>

            <!-- CONTENT -->
            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
                            <?php include get_template_directory() . '/inc/booking-sales.php'; ?>


            
                         <canvas id="myChart"></canvas>
<?php 
$chart_data = get_booking_chart_data();


// echo '<pre>';
//     var_dump($chart_data);
// echo '</pre>';
?>

            <script>
jQuery(document).ready(function ($) {
const chartData = <?php echo wp_json_encode($chart_data); ?>;

    if (typeof Chart === 'undefined') return;

    const ctx = document.getElementById('myChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: 'Approved',
                    data: chartData.data.approved,
                    borderColor: '#198754',
                    fill: false
                },
                {
                    label: 'Pending',
                    data: chartData.data.pending,
                    borderColor: '#ffc107',
                    fill: false
                },
                {
                    label: 'Refunded',
                    data: chartData.data.refunded,
                    borderColor: '#17A2C6!',
                    fill: false
                },
                {
                    label: 'Rejected',
                    data: chartData.data.rejected,
                    borderColor: '#dc3545',
                    fill: false
                },
                 {
                    label: 'Expired',
                    data: chartData.data.expired,
                    borderColor: '#dc3545',
                    fill: false
                }
            ]
        }
    });

});
</script>
            


            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>