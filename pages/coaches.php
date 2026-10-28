<?php
// Template Name: Coaches

get_header();
?>

<section class="mp-coaches-hero">
    <div class="mp-coaches-container">
        <span class="mp-coaches-eyebrow">MatchPoint coaching</span>
        <h1><?php echo esc_html(get_the_title() ?: 'Our Coaches'); ?></h1>
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <div class="mp-coaches-intro"><?php the_content(); ?></div>
        <?php endwhile; endif; ?>
    </div>
</section>

<main class="mp-coaches-directory">
    <div class="mp-coaches-container">
        <header class="mp-coaches-section-heading">
            <div>
                <span class="mp-coaches-section-eyebrow">Meet the team</span>
                <h2>Find a coach for your game</h2>
                <p>Compare coaching styles and rates, then open a profile to see availability.</p>
            </div>
            <span class="mp-coaches-count-label"><i class="fa-solid fa-user-group" aria-hidden="true"></i> Coach profiles</span>
        </header>

        <div class="mp-coach-directory-grid">
            <?php
            $args = array(
                'post_type'      => 'coach',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
            );

            include get_template_directory() . '/loop/coach-loop.php';
            ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
