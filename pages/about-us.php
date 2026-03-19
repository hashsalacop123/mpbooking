<?php 
//Template Name: About Us

get_header(); ?>
<div class="banner-pages" style="background-image:url('<?php echo esc_url( get_template_directory_uri() . '/img/banner-image.jpg' ); ?>');">
    <div class = "container">
        <div class = "row">
            <div class = "col-lg-6 col-md-6 col-sm-12">
                <h1>About MatchPoint Booking</h1>
                <p>Building skills, confidence, and community through tennis</p>
            </div>
             <div class = "col-lg-6 col-md-6 col-sm-12">
            </div>

        </div>
    </div>

</div>
<section class = "who-we-are-wrapper">
    <div class = "container">
        <div class = "row">
            <div class = "col-lg-6 col-xl-6 col-md-6 col-xs-12">
                <div class = "about-first-col">
                    <img src = "<?php echo get_template_directory_uri().'/img/pass.jpg' ?>" class = "img-fluid">
                </div>
            </div>
            <div class = "col-lg-6 col-xl-6 col-md-6 col-xs-12">
                <div class = "about-second-col">
                    <h2>Who We Are</h2>
                    <p>We are a team of passionate and experienced tennis coaches dedicated to delivering high-quality, inclusive training programs for players of all ages and skill levels. Our mission is to create a supportive and inspiring environment where youth and adults can grow their skills, build confidence, and enjoy the sport for life.</p>

    <p>We believe tennis is more than a game. It teaches discipline, resilience, teamwork, and respect. Through structured coaching and community-focused programs, we help players reach their full potential on and off the court.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class = "our-mission">
    <div class = "container">
        <div class = "row">
            <div class = "col-lg-6 col-xl-6 col-md-6 col-xs-12">
                <div class = "our-mission-first-col">
                    <h2>Our Mission</h2>
                    <ul class = "our-mission-list">
                        <li> <img src = "<?php echo get_template_directory_uri().'/img/tennis-player.png' ?>" class = "img-fluid">

                            <div class = "colab">
                                <h4>Our Mission</h4>
                                <p>To make tennis accessible, enjoyable, and rewarding for everyone through professional coaching and community-focused programs.</p>
                            </div>
                        </li>
                          <li>                                <img src = "<?php echo get_template_directory_uri().'/img/community.png' ?>" class = "img-fluid">

                            <div class = "colab">

                                <h4>Our Vision</h4>
                                <p>To build a strong tennis community that develops confident, skilled, and sports-minded players.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class = "col-lg-6 col-xl-6 col-md-6 col-xs-12">
                <div class = "our-mission-second-col">
                                        <img src = "<?php echo get_template_directory_uri().'/img/coach.jpg' ?>" class = "img-fluid"/    >

                </div>
            </div>
        </div>
    </div>
</section>
<section class = "our-programs">
    <div class = "container">
        <h4>Our Programs</h4>

        <ul class = "programs-ul">
            <li class = "progrmas-li">
                <img src = "<?php echo get_template_directory_uri().'/img/promotion.png' ?>">
                <div class = "programs-list">
                    <h5>Beginner Lessons</h5>
                    <p>Introductory coaching for new players of all ages.</p>
                </div>
            </li>
            <li class = "progrmas-li">
                <img src = "<?php echo get_template_directory_uri().'/img/newbie.png' ?>">
                <div class = "programs-list">
                    <h5>Youth Development</h5>
                    <p>IStructured programs designed for young athletes.</p>
                </div>
            </li>
            <li class = "progrmas-li">
                <img src = "<?php echo get_template_directory_uri().'/img/mentorship.png' ?>">
                <div class = "programs-list">
                    <h5>Private Coaching</h5>
                    <p>One-on-one sessions focused on personal improvement.</p>
                </div>
            </li>
            <li class = "progrmas-li">
                <img src = "<?php echo get_template_directory_uri().'/img/meeting.png' ?>">
                <div class = "programs-list">
                    <h5>Group Clinics</h5>
                    <p>IFun and engaging training in small group settings.</p>
                </div>
            </li>
            <li class = "progrmas-li">
                <img src = "<?php echo get_template_directory_uri().'/img/booking.png' ?>">
                <div class = "programs-list">
                    <h5>Court Booking</h5>
                    <p>Easy and flexible online court reservations.</p>
                </div>
            </li>
            <li class = "progrmas-li">
                <img src = "<?php echo get_template_directory_uri().'/img/diversity.png' ?>">
                <div class = "programs-list">
                    <h5>Community Events</h5>
                    <p>Social matches, tournaments, and local tennis activities.</p>
                </div>
            </li>
        </ul>
    </div>
</section>


<?php get_footer(); ?>