<?php 
//Template Name: Gallery

get_header(); ?>
<div class = "gallery-main-wrapper">
    <div class="banner-pages" style="background-image:url('<?php echo esc_url( get_template_directory_uri() . '/img/contact-us-2.jpg' ); ?>'); background-position: center top;">
        <div class = "container">
            <div class = "row">
                <div class = "col-lg-6 col-md-6 col-sm-12">
                    <h1>Gallery</h1>
                    <p>Moments from our courts, coaches and community</p>
                </div>
            </div>
        </div>
    </div>
    <?php if (have_rows('gallery_tabs')) : ?>

<div class="container my-5">
    
    <!-- Nav tabs -->
    <ul class="nav nav-tabs justify-content-end" id="galleryTabs" role="tablist">

        <!-- All Tab -->
        <li class="nav-item">
            <a class="nav-link active" id="tab-all" data-toggle="tab" href="#all" role="tab">
                All
            </a>
        </li>

        <?php 
        $tab_index = 0;
        while (have_rows('gallery_tabs')) : the_row(); 
            $gallery_name = get_sub_field('gallery_name');
            $tab_id = 'tab-' . sanitize_title($gallery_name);
        ?>
            <li class="nav-item">
                <a 
                    class="nav-link" 
                    id="<?php echo esc_attr($tab_id); ?>-tab"
                    data-toggle="tab" 
                    href="#<?php echo esc_attr($tab_id); ?>" 
                    role="tab"
                >
                    <?php echo esc_html($gallery_name); ?>
                </a>
            </li>
        <?php 
            $tab_index++;
        endwhile; 
        ?>
    </ul>

    <!-- Tab content -->
    <div class="tab-content pt-4">

        <!-- ALL TAB CONTENT -->
        <div class="tab-pane fade show active" id="all" role="tabpanel">
            <div class="row">
                <?php 
                while (have_rows('gallery_tabs')) : the_row();
                    if (have_rows('image')) :
                        while (have_rows('image')) : the_row();
                            $img = get_sub_field('image');
                            $caption = get_sub_field('image_caption');
                            if ($img) :
                ?>
                    <div class="col-md-4 mb-4">
                        <figure class="mb-0">
                            <div class="gallery-img-wrapper">
                                <a href="<?php echo esc_url( $img['url'] ); ?>" data-fancybox="coach-gallery"
                                        data-caption="<?php echo esc_attr( $img['caption'] );?>"><img 
                                    src="<?php echo esc_url($img['sizes']['medium_large']); ?>" 
                                    alt="<?php echo esc_attr($img['alt']); ?>"
                                ></a>
                            </div>
                            <?php if ($caption) : ?>
                                <figcaption class="mt-2 text-center small">
                                    <?php echo esc_html($caption); ?>
                                </figcaption>
                            <?php endif; ?>
                        </figure>
                    </div>
                <?php 
                            endif;
                        endwhile;
                    endif;
                endwhile; 
                ?>
            </div>
        </div>

        <?php 
        // Reset rows for second loop
        reset_rows();

        while (have_rows('gallery_tabs')) : the_row(); 
            $gallery_name = get_sub_field('gallery_name');
            $tab_id = 'tab-' . sanitize_title($gallery_name);
        ?>
            <div class="tab-pane fade" id="<?php echo esc_attr($tab_id); ?>" role="tabpanel">
                <div class="row">
                    <?php if (have_rows('image')) : ?>
                        <?php while (have_rows('image')) : the_row(); 
                            $img = get_sub_field('image');
                            $caption = get_sub_field('image_caption');
                            if ($img) :
                        ?>
                            <div class="col-md-4 mb-4">
                                <figure class="mb-0">
                                    <div class="gallery-img-wrapper">
                                        <img 
                                            src="<?php echo esc_url($img['sizes']['medium_large']); ?>" 
                                            alt="<?php echo esc_attr($img['alt']); ?>"
                                        >
                                    </div>
                                    <?php if ($caption) : ?>
                                        <figcaption class="mt-2 text-center small">
                                            <?php echo esc_html($caption); ?>
                                        </figcaption>
                                    <?php endif; ?>
                                </figure>
                            </div>
                        <?php 
                            endif;
                        endwhile; 
                        ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>

    </div>

</div>

<?php endif; ?>

</div>




<?php get_footer(); ?>