<?php
$dashboard_user = wp_get_current_user();
$dashboard_roles = (array) $dashboard_user->roles;
$dashboard_request_path = wp_parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$dashboard_name = $dashboard_user->first_name ?: $dashboard_user->display_name;
$dashboard_initials = '';
foreach (preg_split('/\s+/', trim($dashboard_name)) as $name_part) {
    if ($name_part !== '') {
        $dashboard_initials .= strtoupper(substr($name_part, 0, 1));
    }
    if (strlen($dashboard_initials) >= 2) break;
}

$dashboard_items = [];
$dashboard_title = 'Member workspace';
$dashboard_caption = 'Your MatchPoint account';

if (in_array('coach', $dashboard_roles, true)) {
    $dashboard_title = 'Coach workspace';
    $dashboard_caption = 'Manage your coaching business';
    $dashboard_items = [
        ['Dashboard', '/dashboard/', 'fa-gauge-high'],
        ['Information', '/my-account/', 'fa-user'],
        ['Your Reviews', '/review/', 'fa-star'],
        ['Statistics', '/statistics/', 'fa-chart-line'],
    ];

    $coach_posts = get_posts([
        'post_type'      => 'coach',
        'author'         => $dashboard_user->ID,
        'posts_per_page' => 1,
        'post_status'    => ['publish', 'draft', 'pending', 'private'],
    ]);
    if ($coach_posts) {
        $dashboard_items[] = ['View Public Profile', get_permalink($coach_posts[0]->ID), 'fa-arrow-up-right-from-square'];
    }
    $dashboard_items[] = ['Coach Profile', '/dashboard/update/', 'fa-id-card'];
} elseif (in_array('court', $dashboard_roles, true)) {
    $dashboard_title = 'Court workspace';
    $dashboard_caption = 'Manage your venue and bookings';
    $dashboard_items = [
        ['Dashboard', '/dashboard/', 'fa-gauge-high'],
        ['Account Information', '/my-account/', 'fa-user'],
        ['Statistics', '/statistics/', 'fa-chart-line'],
        ['Reviews', '/dashboard/review/', 'fa-star'],
        ['Court Profile', '/dashboard/create-update/', 'fa-building'],
        ['Court Schedule', '/dashboard/court-name-schedule/', 'fa-calendar-days'],
    ];
} elseif (in_array('administrator', $dashboard_roles, true)) {
    $dashboard_title = 'Site administration';
    $dashboard_caption = 'Manage MatchPoint members';
    $dashboard_items = [
        ['Dashboard', '/dashboard/admin/', 'fa-gauge-high'],
        ['Account Information', '/dashboard/my-account/', 'fa-user'],
        ['Statistics', '/statistics/', 'fa-chart-line'],
        ['Coaches', '/dashboard/coach/', 'fa-person-running'],
        ['Courts', '/dashboard/court/', 'fa-building'],
    ];
} else {
    $dashboard_items = [
        ['Dashboard', '/dashboard/', 'fa-gauge-high'],
        ['Account Information', '/my-account/', 'fa-user'],
    ];
}
?>

<aside class="sidebar-wrapper mp-dashboard-sidebar">
    <div class="mp-dashboard-sidebar__identity">
        <span class="mp-dashboard-sidebar__avatar" aria-hidden="true"><?php echo esc_html($dashboard_initials ?: 'MP'); ?></span>
        <div>
            <p class="mp-dashboard-sidebar__eyebrow">MatchPoint</p>
            <h2><?php echo esc_html($dashboard_title); ?></h2>
            <p class="mp-dashboard-sidebar__caption"><?php echo esc_html($dashboard_caption); ?></p>
        </div>
    </div>

    <nav class="mp-dashboard-sidebar__nav" aria-label="Dashboard navigation">
        <p class="mp-dashboard-sidebar__label">Workspace</p>
        <ul>
            <?php foreach ($dashboard_items as $item) :
                $item_url = preg_match('#^https?://#i', $item[1]) ? $item[1] : home_url($item[1]);
                $item_path = wp_parse_url($item_url, PHP_URL_PATH) ?: '/';
                $is_current = untrailingslashit($dashboard_request_path) === untrailingslashit($item_path);
                ?>
                <li>
                    <a href="<?php echo esc_url($item_url); ?>" class="<?php echo $is_current ? 'is-current' : ''; ?>" <?php echo $is_current ? 'aria-current="page"' : ''; ?>>
                        <i class="fa-solid <?php echo esc_attr($item[2]); ?>" aria-hidden="true"></i>
                        <span><?php echo esc_html($item[0]); ?></span>
                        <?php if ($is_current) : ?><i class="fa-solid fa-chevron-right mp-dashboard-sidebar__active-mark" aria-hidden="true"></i><?php endif; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div class="mp-dashboard-sidebar__footer">
        <div class="mp-dashboard-sidebar__signed-in">
            <span class="mp-dashboard-sidebar__online" aria-hidden="true"></span>
            <span>Signed in as <strong><?php echo esc_html($dashboard_name); ?></strong></span>
        </div>
        <a class="mp-dashboard-sidebar__logout" href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>">
            <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
            <span>Log out</span>
        </a>
    </div>
</aside>
