<?php

/**
 * Override Yoast Open Graph Title
 */
add_filter('wpseo_opengraph_title', 'my_custom_og_title');
function my_custom_og_title($title) {

    if (is_front_page()) {
        $acf = get_field('og_title_open_graph', get_queried_object_id());
        if ($acf) return $acf;
    }

    if (is_singular('coach')) {
        $acf = get_field('nick_name');
        if ($acf) return $acf;
    }

    if (is_singular('service')) {
        $acf = get_field('court_name_gym');
        if ($acf) return $acf;
    }

    return $title;
}

/**
 * Override Yoast Open Graph Description
 */
add_filter('wpseo_opengraph_desc', 'my_custom_og_desc');
function my_custom_og_desc($desc) {

    if (is_front_page()) {
        $acf = get_field('og_description', get_queried_object_id());
        if ($acf) return $acf;
    }

    if (is_singular('coach')) {
        $acf = get_field('about_me');
        if ($acf) return $acf;
    }

    if (is_singular('service')) {
        $acf = get_field('additional_information');
        if ($acf) return $acf;
    }

    return $desc;
}

/**
 * Manual OG Image (reliable fix for Yoast issue)
 */
add_action('wp_head', 'my_manual_og_image_fallback', 20);
function my_manual_og_image_fallback() {

    if (!(is_front_page() || is_singular('coach') || is_singular('service'))) {
        return;
    }

    $image = '';

    if (is_front_page()) {
        $acf = get_field('og_image', get_queried_object_id());
        if (is_array($acf)) $image = $acf['url'];
    }

    elseif (is_singular('coach') || is_singular('service')) {
        $acf = get_field('featured_image');
        if (is_array($acf)) $image = $acf['url'];
    }

    if (!empty($image)) {
        echo '<meta property="og:image" content="' . esc_url($image) . '" />';
        echo '<meta name="twitter:image" content="' . esc_url($image) . '" />';
    }
}

/**
 * Disable Yoast Open Graph on specific pages
 */
add_filter('wpseo_opengraph', 'disable_yoast_og_for_custom_pages');

function disable_yoast_og_for_custom_pages($enabled) {

    if (is_front_page() || is_singular('coach') || is_singular('service')) {
        return false; // disable Yoast OG
    }

    return $enabled;
}

// ==================
/**
 * Redirect wp-login.php to custom /login/ page
 * (safe: does NOT break logout or admin)
 */
function hash_redirect_wp_login_safe() {

    // Only target wp-login.php
    if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') === false) {
        return;
    }

    // Allow logout to proceed normally
    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
        return;
    }

    // Allow admin login (optional but recommended)
    if (is_admin()) {
        return;
    }

    // Build redirect URL
    $redirect = home_url('/login/');

    // Preserve redirect_to if exists
    if (!empty($_GET['redirect_to'])) {
        $redirect = add_query_arg('redirect_to', $_GET['redirect_to'], $redirect);
    }

    wp_redirect($redirect);
    exit;
}
add_action('init', 'hash_redirect_wp_login_safe');
/**
 * Theme setup features
 */
function theme_setup() {

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable Featured Images
    add_theme_support('post-thumbnails');

    // Enable HTML5 markup
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script'
    ));


    register_nav_menus(array(
        'header_menu'  => 'Header Menu',
        'footer_one'  => 'Footer One',
        'footer_two'  => 'Footer Two'
    ));

   

    // Enable automatic feed links
    add_theme_support('automatic-feed-links');

    // Enable selective refresh for widgets
    add_theme_support('customize-selective-refresh-widgets');

    // Register menus
    register_nav_menus(array(
        'primary' => 'Primary Menu',
        'footer'  => 'Footer Menu'
    ));
}
add_action('after_setup_theme', 'theme_setup');
?>