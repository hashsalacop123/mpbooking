<?php


// ==================
/**
 * Redirect wp-login.php to custom /login/ page
 * (safe: does NOT break logout or admin)
 */
/**
 * Redirect wp-login.php to custom login page
 * but allow logout + password reset actions
 */
function hash_redirect_wp_login_safe() {

    // Only target wp-login.php
    if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') === false) {
        return;
    }

    // Get current action
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    // ✅ Allow these actions to pass through
    $allowed_actions = [
        'logout',
        'lostpassword',
        'retrievepassword',
        'resetpass',
        'rp'
    ];

    if (in_array($action, $allowed_actions)) {
        return;
    }

    // Optional: allow admin access
    if (is_admin()) {
        return;
    }

    // Build redirect URL
    $redirect = home_url('/login/');

    // Preserve redirect_to
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

/**
 * Customize WP Login / Lost Password Page Design
 */
function hash_customize_login_page() {
    ?>
    <style type="text/css">
        body.login {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            justify-content: center;
            box-sizing: border-box;
            padding: 28px 16px;
            background: linear-gradient(180deg, #fff 0%, #f3f8f6 100%);
            color: #273d3d;
            font-family: Poppins, Arial, sans-serif;
        }
        body.login *,
        body.login *::before,
        body.login *::after { box-sizing: border-box; }
        body.login #login {
            position: relative;
            z-index: 1;
            width: min(100%, 420px);
            margin: 0 auto;
            padding: 0;
        }
        body.login h1 a {
            width: 190px !important;
            height: 68px !important;
            margin: 0 auto 22px;
            background-image: url('<?php echo esc_url(get_stylesheet_directory_uri() . '/img/match-point-logo-transparent.png'); ?>') !important;
            background-size: contain !important;
            background-repeat: no-repeat !important;
            background-position: center !important;
        }
        body.login #login form {
            margin-top: 0;
            padding: 30px;
            border: 1px solid #e8eeeb;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 12px 36px rgba(35, 65, 58, .08);
        }
        body.login #login form label {
            color: #435655;
            font-size: 13px;
            font-weight: 600;
        }
        body.login #login form .input,
        body.login #login form input[type="text"],
        body.login #login form input[type="password"] {
            width: 100%;
            min-height: 46px;
            padding: 9px 12px;
            border: 1px solid #dce7e3;
            border-radius: 8px;
            background: #fbfdfc;
            color: #273d3d;
            box-shadow: none;
        }
        body.login #login form .input:focus {
            border-color: #49a296;
            box-shadow: 0 0 0 3px rgba(49, 145, 132, .13);
        }
        body.login #login form input[type="checkbox"] { accent-color: #168b7e; }
        body.login #login .wp-core-ui .button-primary {
            width: 100%;
            min-height: 46px;
            margin-top: 8px;
            padding: 8px 18px;
            border: 1px solid #168b7e;
            border-radius: 8px;
            background: #168b7e;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            text-shadow: none;
            box-shadow: none;
        }
        body.login #login .wp-core-ui .button-primary:hover,
        body.login #login .wp-core-ui .button-primary:focus { border-color: #116d64; background: #116d64; }
        body.login #login a { color: #168b7e; text-decoration: none; }
        body.login #login a:hover { color: #116d64; text-decoration: underline; }
        body.login #nav,
        body.login #backtoblog { padding: 0; text-align: center; }
        body.login #nav { margin: 18px 0 0; }
        body.login #backtoblog { margin: 10px 0 0; }
        body.login .message,
        body.login .notice,
        body.login .success { border-left: 4px solid #168b7e; background: #eaf6f2; color: #36534e; box-shadow: none; }
        body.login p.description,
        body.login .description.indicator-hint { color: #657572; }
        @media (max-width: 480px) {
            body.login #login { width: 100%; }
            body.login #login form { padding: 24px 20px; }
        }
    </style>
    <?php
}
add_action('login_enqueue_scripts', 'hash_customize_login_page');

add_filter('login_headerurl', function () {
    return home_url('/');
});
add_filter('login_headertext', function () {
    return get_bloginfo('name');
});

/**
 * Hide admin bar on frontend for non-admin users
 */
add_filter('show_admin_bar', function ($show) {
    if (!current_user_can('administrator')) {
        return false;
    }
    return $show;
});

/**
 * Restrict wp-admin access to admins only
 */
add_action('admin_init', function () {

    // Allow AJAX (important for frontend features like forms)
    if (defined('DOING_AJAX') && DOING_AJAX) {
        return;
    }

    // If not admin, block access
    if (!current_user_can('administrator')) {
        wp_redirect(home_url());
        exit;
    }
});
?>
