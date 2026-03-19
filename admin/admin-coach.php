<?php
// Template Name: Admin Coach
acf_form_head(); // Must be first
get_header();
?>

<div class="dasboard-wrapper-page">
    <div class="container">
<div class = "row">
    <!-- SIDEBAR START HERE -->
    <div class = "col-xl-3 col-lg-3 col-md-3 col-sm-12">
        <?php include get_template_directory() . '/dashboard/dashboard-sidebar.php'; ?>    
    </div>
    <!-- CONTTENT START HERE -->
    <div class = "col-xl-9 col-lg-9 col-md-9 col-sm-12">

    <?php echo 'coach';?>
    </div>
</div>

    </div>
</div>

<?php get_footer(); ?>