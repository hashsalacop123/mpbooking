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

        /* 🔥 Background */
        body.login {
            background: url('<?php echo get_stylesheet_directory_uri(); ?>/img/banner-home 2.png') no-repeat center center;
            background-size: cover;
        }
           body.login h1 a {
            background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/img/match-point-logo-transparent.png')!important;
            background-size: contain !important;
            background-repeat: no-repeat !important;
            background-position: center !important;

            width: 100% !important;
            height: 80px !important;
        }        body.login::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* adjust opacity */
            z-index: 0;
        }

        /* 🔥 Make sure login box stays above overlay */
        #login {
            position: relative;
            z-index: 1;
        }

        .login form {
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .wp-core-ui .button-primary {
            background: #000;
            border: none;
        }
        .login form label {
             color: #fff;
            }
            .login form {
                overflow: hidden!important;
                background: #1e626674!important;
                padding: 15px!important;
                text-align: left!important;
                border-radius: 15px!important;
                border: 1px solid #1e6366!important;
            }
            #login .wp-core-ui .button-primary {
    
                color: #fff;
                text-decoration: none;
                text-shadow: none;
                background: #1e6366!important;
                border: 1px;
                color: #fff;
                border-radius: 15px;
                padding: 5px 15px !important;
            }
            p#nav a {
                background: #1e6366;
                padding: 6px;
                font-size: 12px;
                color: #fff;
                border-radius: 3px;
                color: #fff!Important;
            }
            p#backtoblog a {
                background: #1e6366;
                padding: 6px;
                font-size: 12px;
                color: #fff!important;
                border-radius: 3px;
            }.wp-core-ui .button-primary {
                background: #1e6366!important;
                border-color: #1e6366!important;

            }
            
            .login .message, .login .notice, .login .success {
                color: #fff;
                border-left: 4px solid #1e6366!important;
                padding: 12px;
                margin-left: 0;
                margin-bottom: 20px;
                background-color: #1e626674 !important;
                box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
                word-wrap: break-word;
            }
            .login form .input, .login form input[type=checkbox], .login input[type=text] {
                background: #1e6366!important;
                border-radius: 30px!important;
                border: 1px solid #0000;
                color: #fff !important;
            }
            p.description.indicator-hint {
                color: #fff;
            }
    </style>
    <?php
}
add_action('login_enqueue_scripts', 'hash_customize_login_page');

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