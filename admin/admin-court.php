<?php
// Template Name: Admin Court
acf_form_head();
get_header();

$courts = get_posts([
    'post_type'      => 'service',
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
                        <h1>Court directory</h1>
                        <p>Review court listings and open their public pages.</p>
                    </div>
                    <span class="mp-dashboard-heading-chip"><?php echo esc_html(number_format_i18n(count($courts))); ?> courts</span>
                </div>

                <div class="mp-dashboard-card mp-dashboard-table-card">
                    <table class="tables-general display">
                        <thead>
                            <tr><th>Court</th><th>Location</th><th>Rate</th><th>Status</th><th>Listing</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courts as $court) :
                                $author = get_userdata($court->post_author);
                                $address = get_field('address', $court->ID);
                                $rate = get_field('hourly_rate', $court->ID);
                                ?>
                                <tr>
                                    <td><?php echo esc_html(get_field('court_name', $court->ID) ?: get_the_title($court)); ?><small class="mp-dashboard-table-subtext"><?php echo esc_html($author ? $author->user_email : ''); ?></small></td>
                                    <td><?php echo esc_html($address ?: '—'); ?></td>
                                    <td><?php echo $rate !== '' && $rate !== null ? esc_html('₱' . number_format_i18n((float) $rate, 2) . ' / hr') : '—'; ?></td>
                                    <td><span class="mp-dashboard-status mp-dashboard-status--<?php echo esc_attr($court->post_status); ?>"><?php echo esc_html(ucfirst($court->post_status)); ?></span></td>
                                    <td><a class="mp-dashboard-table-link" href="<?php echo esc_url(get_permalink($court->ID)); ?>" target="_blank" rel="noopener">View listing</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php if (!$courts) : ?><div class="mp-dashboard-empty"><h2>No court listings</h2><p>Court profiles will appear here when created.</p></div><?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</div>

<?php get_footer(); ?>
