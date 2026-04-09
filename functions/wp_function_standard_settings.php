<?php

/**
 * Disable Yoast Open Graph ONLY for selected pages
 */
add_filter('wpseo_frontend_presenters', function ($presenters) {

    if (is_front_page() || is_singular('coach') || is_singular('service')) {
        return array_filter($presenters, function ($presenter) {
            return !($presenter instanceof \Yoast\WP\SEO\Presenters\Open_Graph\Title_Presenter
                || $presenter instanceof \Yoast\WP\SEO\Presenters\Open_Graph\Description_Presenter
                || $presenter instanceof \Yoast\WP\SEO\Presenters\Open_Graph\Image_Presenter
                || $presenter instanceof \Yoast\WP\SEO\Presenters\Open_Graph\Url_Presenter);
        });
    }

    return $presenters;
});

/**
 * Custom Open Graph (final working version)
 */
add_action('wp_head', 'my_final_custom_og', 5);


function my_final_custom_og() {

    // Only run for these pages
    if (!(is_front_page() || is_singular('coach') || is_singular('service'))) {
        return;
    }

    $front_id = (int) get_option('page_on_front');

    $title = '';
    $desc  = '';
    $image = '';

    /**
     * HOMEPAGE (most strict check first)
     */
    if ($front_id && is_page($front_id)) {

        $title = get_field('og_title_open_graph', $front_id);
        $desc  = get_field('og_description', $front_id);

        $img = get_field('og_image', $front_id);
        if (is_array($img) && !empty($img['url'])) {
            $image = $img['url'];
        }

    }
    /**
     * COACH
     */
    elseif (is_singular('coach')) {

        $title = get_field('nick_name') ?: get_the_title();
        $desc  = get_field('about_me') ?: wp_trim_words(get_the_content(), 20);

        $img = get_field('featured_image');
        if (is_array($img) && !empty($img['url'])) {
            $image = $img['url'];
        }

    }
    /**
     * SERVICE
     */
    elseif (is_singular('service')) {

        $title = get_field('court_name_gym') ?: get_the_title();
        $desc  = get_field('additional_information') ?: wp_trim_words(get_the_content(), 20);

        $img = get_field('featured_image');
        if (is_array($img) && !empty($img['url'])) {
            $image = $img['url'];
        }
    }

    // Final fallback (avoid empty tags)
    if (empty($title)) $title = get_bloginfo('name');
    if (empty($desc))  $desc  = get_bloginfo('description');

    if (!empty($image)) {
        echo '<meta property="og:image" content="'.esc_url($image).'" />';
        echo '<meta name="twitter:image" content="'.esc_url($image).'" />';
    }

    echo '<meta property="og:title" content="'.esc_attr($title).'" />';
    echo '<meta property="og:description" content="'.esc_attr($desc).'" />';
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