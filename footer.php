





<footer>
    <div class = "footer-wrapper inner-footer">
        <div class = "container">
                <div class="row">
                    <div class = "col-xl-3 col-lg-3 col-md-3 col-sm-12">
                        <div class = "footer-first-column footer-column">
                            <h5>Quick Links</h5>
                                <ul>
                                    <li><a href = "/about-us/">About Us</a></li>
                                    <li><a href = "/contact-us/">Contact Us</a></li>
                                    <li><a href = "/court/">Court</a></li>
                                    <li><a href = "/coaches/">Coaches</a></li>
                                    <li><a href = "/coaches/">Blogs</a></li>
                                </ul>
 </p>
                        </div>
                    </div>
                    <div class = "col-xl-3 col-lg-3 col-md-3 col-sm-12">
                        <div class = "footer-second-column footer-column">
                            <h5>Contact info</h5>
                                <ul>
                        <li>
                            <i class="fa fa-phone" aria-hidden="true"></i>
                            <a href = "tel:+639215369904">+0921-536-9904</a>
                        </li>
                        <li>
                            <i class="fa fa-envelope" aria-hidden="true"></i>
                            <a href = "mailto:Matchpointtennis@outlook.ph
 ">Matchpointtennis@outlook.ph</a>

                        </li>
                          <li>
                            <i class="fa-solid fa-map-pin"></i>
                            IT Park, Lahug, Cebu City
                        </li>
                        </ul>
                        </div>
                    </div>
                    <div class = "col-xl-3 col-lg-3 col-md-3 col-sm-12">
                        <div class = "footer-third-column footer-column">
                            <h5>Follow Us</h5>
                                <ul class = "social-media">
                                <li><a href = "https://www.facebook.com/MatchpointTennisAcad/" target = "_blank"><i class="fa-brands fa-facebook"></i></a></li>
                                <li><a href = "https://www.youtube.com/@matchpointtenniscebu" target = "_blank"><i class="fa-brands fa-youtube"></i>
                                    </a></li>
                                <li><a href = "https://www.instagram.com/mptenniscebu" target = "_blank"><i class="fa-brands fa-square-instagram"></i>
                                    </a></li>
                                <li><a href = "hhttps://www.tiktok.com/@matchpointtenniscebu" target = "_blank"><i class="fa-brands fa-tiktok"></i>
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                    <div class = "col-xl-3 col-lg-3 col-md-3 col-sm-12">
                        <div class = "footer-third-column footer-column">
                            <h5>Subscribe</h5>
                            <?php 
                             $subscribe = get_field('subsriptions', 'option');

if (!empty($subscribe)) {
    echo do_shortcode($subscribe);
}


                            
                            ?>
                        </div>
                    </div>
                 
                </div>
        </div>
    </div>
    <div class = "bottom-container">
        <div class = "container">
            <div class = "row">
                
                    <div class = "col-sm-12 col-md-12 text-center">
                    <p class = "copy-right-bottom-text">&copy; <?php echo date('Y'); ?>  Match Point. All rights reserved, All Right Reserved | Designed By <a href="https://hashcrafter.com/">hashcrafter</a></p>
                    </div>
            </div>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>

