<?php
// Template Name: Admin Coach
acf_form_head();
get_header();

$coaches = get_posts([
    'post_type'      => 'coach',
    'posts_per_page' => -1,
    'post_status'    => ['publish', 'pending', 'draft'],
    'orderby'        => 'date',
    'order'          => 'DESC',
]);
?>

<div class="dasboard-wrapper-page">
    <div class="container">
        <div class="row">
            <aside class="col-xl-3 col-lg-3 col-md-4 col-sm-12">
                <?php include get_template_directory() . '/dashboard/dashboard-sidebar.php'; ?>
            </aside>
            <section class="col-xl-9 col-lg-9 col-md-8 col-sm-12 mp-dashboard-content">
                <div class="mp-dashboard-page-heading">
                    <div>
                        <p class="mp-dashboard-eyebrow">Administration</p>
                        <h1>Coach directory</h1>
                        <p>Review coach profiles and open their public listings.</p>
                    </div>
                    <span class="mp-dashboard-heading-chip"><?php echo esc_html(number_format_i18n(count($coaches))); ?> coaches</span>
                </div>

                <div class="mp-dashboard-card mp-dashboard-table-card">
                    <table class="tables-general display">
                        <thead>
                            <tr><th>Coach</th><th>Sports</th><th>Rate</th><th>Status</th><th>Profile</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($coaches as $coach) :
                                $author = get_userdata($coach->post_author);
                                $sports = get_the_terms($coach->ID, 'sport');
                                $sport_names = (!is_wp_error($sports) && $sports) ? implode(', ', wp_list_pluck($sports, 'name')) : '—';
                                $rate = get_field('hourly_rate', $coach->ID);
                                ?>
                                <tr>
                                    <td><?php echo esc_html(get_field('nick_name', $coach->ID) ?: get_the_title($coach)); ?><small class="mp-dashboard-table-subtext"><?php echo esc_html($author ? $author->user_email : ''); ?></small></td>
                                    <td><?php echo esc_html($sport_names); ?></td>
                                    <td><?php echo $rate !== '' && $rate !== null ? esc_html('₱' . number_format_i18n((float) $rate, 2) . ' / hr') : '—'; ?></td>
                                    <td><span class="mp-dashboard-status mp-dashboard-status--<?php echo esc_attr($coach->post_status); ?>"><?php echo esc_html(ucfirst($coach->post_status)); ?></span></td>
                                    <td><a class="mp-dashboard-table-link" href="<?php echo esc_url(get_permalink($coach->ID)); ?>" target="_blank" rel="noopener">View profile</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php if (!$coaches) : ?><div class="mp-dashboard-empty"><h2>No coach profiles</h2><p>Coach profiles will appear here when created.</p></div><?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</div>

<?php get_footer(); ?>
