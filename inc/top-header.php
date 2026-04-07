<div class = "coaches-wrapper">
<?php
// get featured image URL
$featured_image = get_the_post_thumbnail_url(get_the_ID(), 'full');

// fallback image
$default_image = get_stylesheet_directory_uri() . '/img/top-heading.jpg';

// use featured if exists, otherwise fallback
$bg_image = $featured_image ? $featured_image : $default_image;
?>

<div class="top-header-page" style="background-image: url('<?php echo esc_url($bg_image); ?>');">