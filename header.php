<!DOCTYPE HTML>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?php wp_title('|',TRUE,'right'); bloginfo('name'); ?></title>
<!--    <link rel="shortcut icon" href="/wp-content/uploads/2016/08/cropped-favicon-32x32.png" />-->
</head>
    <?php wp_head(); ?>
<body <?php body_class(); ?> >
    <header id = "header_bg_color">
<?php 

            get_template_part( 'header/header-inner' );
           

          ?>
    </header>
    