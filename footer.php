<footer class="mp-home-footer">
    <div class="mp-home-footer-inner">
        <p><?php echo wp_kses_post(get_field('copy_right_bottom_text', 'option') ?: '© ' . gmdate('Y') . ' Match Point. All Rights Reserved.'); ?></p>
        <nav aria-label="Footer navigation">
            <a href="<?php echo esc_url(home_url('/about-us/')); ?>">About Us</a>
            <a href="<?php echo esc_url(home_url('/contact-us/')); ?>">Contact Us</a>
            <?php
            $refund_page = get_page_by_path('refund-policy') ?: get_page_by_path('refund');
            $refund_url = $refund_page ? get_permalink($refund_page) : home_url('/refund/');
            $privacy_url = function_exists('get_privacy_policy_url') ? get_privacy_policy_url() : '';
            if (!$privacy_url) {
                $privacy_page = get_page_by_path('privacy-policy');
                $privacy_url = $privacy_page ? get_permalink($privacy_page) : home_url('/privacy-policy/');
            }
            ?>
            <a href="<?php echo esc_url($refund_url); ?>">Refund Policy</a>
            <a href="<?php echo esc_url($privacy_url); ?>">Privacy Policy</a>
        </nav>
        <div class="mp-home-footer-socials" aria-label="Social media">
            <?php
            $footer_socials = [
                'facebook'  => 'fa-facebook-f',
                'instagram' => 'fa-instagram',
                'youtube'   => 'fa-youtube',
                'tiktok'    => 'fa-tiktok',
            ];
            foreach ($footer_socials as $field => $icon) :
                $social_url = get_field($field, 'option');
                if ($social_url) : ?>
                    <a href="<?php echo esc_url($social_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr(ucfirst($field)); ?>">
                        <i class="fa-brands <?php echo esc_attr($icon); ?>" aria-hidden="true"></i>
                    </a>
                <?php endif;
            endforeach; ?>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
