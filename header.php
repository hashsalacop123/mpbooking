<!DOCTYPE HTML>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<header id="header_bg_color" class="<?php echo (is_front_page() || is_page_template('pages/landing-page.php')) ? 'mp-home-header-wrap' : 'mp-inner-header-wrap'; ?>">
<?php if (is_front_page() || is_page_template('pages/landing-page.php')) : ?>
    <div class="mp-home-header">
        <div class="mp-home-header-inner">
            <a class="mp-home-logo" href="<?php echo esc_url(home_url('/')); ?>" aria-label="MatchPoint Booking home">
                <?php
                $home_logo = get_field('logo', 'option');
                $home_logo_url = (is_array($home_logo) && !empty($home_logo['url']))
                    ? $home_logo['url']
                    : get_template_directory_uri() . '/img/match-point-logo-transparent.png';
                ?>
                <img src="<?php echo esc_url($home_logo_url); ?>" alt="MatchPoint Booking">
            </a>
            <div class="mp-home-header-actions">
                <a class="mp-home-book-link" href="<?php echo esc_url(home_url('/court/')); ?>">Book A Court</a>
                <details class="mp-home-menu">
                    <summary aria-label="Open site menu"><span></span><span></span><span></span></summary>
                    <div class="mp-home-dropdown">
                        <?php
                        wp_nav_menu([
                            'theme_location' => 'header_menu',
                            'container'      => 'nav',
                            'container_aria_label' => 'Main navigation',
                            'fallback_cb'    => false,
                        ]);
                        ?>
                        <div class="mp-home-menu-account">
                            <?php if (is_user_logged_in()) : ?>
                                <a href="<?php echo esc_url(home_url('/dashboard/')); ?>">Dashboard</a>
                                <a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>">Log Out</a>
                            <?php else : ?>
                                <a href="<?php echo esc_url(home_url('/registration/')); ?>">Be a Member</a>
                                <a href="<?php echo esc_url(home_url('/login/')); ?>">Log In</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </details>
                <a class="mp-home-account" href="<?php echo esc_url(is_user_logged_in() ? home_url('/dashboard/') : home_url('/login/')); ?>" aria-label="<?php echo is_user_logged_in() ? 'Your dashboard' : 'Log in'; ?>">
                    <i class="fa-regular fa-circle-user" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
<?php else : ?>
    <?php get_template_part('header/header-inner'); ?>
<?php endif; ?>
</header>
