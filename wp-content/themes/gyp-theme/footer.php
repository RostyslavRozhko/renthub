<footer class="footer">
    <section>
        <div class="container">
            <div class="top-nav bot-nav">
                <a class="logo" href="<?php echo site_url(); ?>"><img
                            src="<?php echo get_stylesheet_directory_uri(); ?>/img/logo.png"
                            class="img-responsive fleft"/></a>

                <div class="footer__left">
                <a href="<?php echo site_url('new/'); ?>" class="login-top login__item login__item__yellow <?php if (!is_user_logged_in()) echo 'btnModal' ?>">
                    <i style="line-height: inherit" class="fa fa-plus" aria-hidden="true"></i>
                    <span><?php echo __('Add Ad', 'prokkat'); ?></span>
                </a>
                </div>


            </div>
        </div>
        </div>
        </div>
    </section>
    <section class="footer-cats-section">
        <div class="container footer-cats-container">
            <?php
                $categories = get_categories(array(
                    'taxonomy' => 'cate',
                    'orderby' => 'ID',
                    'hide_empty' => false,
                    'parent'=>0,
                ));
                foreach ( $categories as $category ) { 
                    $termId = $category->term_id;
                    $taxonomyName = 'cate';
                    ?>
                        <div href="<?php echo get_term_link( $termId, $taxonomyName ); ?>" class="footer__cate-container">
                            <a href="<?php echo get_term_link( $termId, $taxonomyName ); ?>">
                                <img src="<?php echo get_wp_term_image($termId); ?>" class="footer__cate-img">
                            </a>
                            <a href="<?php echo get_term_link( $termId, $taxonomyName ); ?>" class="footer__cate-name">
                                <?php echo esc_html($category->name); ?>
                            </a>
                        </div>
                <?php
                }
            ?>
        </div>
    </section>
    <section class="footer__menu">
        <div class="container">
            <div class="footer_padding">
                <div class="footer__column store-btn__container" style="display: none">
                    <div class="store-btn">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/apple.svg" class="store-icon"/>
                        <div>
                            <div class="store-bnt__text__top">Available in</div>
                            <div class="store-bnt__text__bottom">App store</div>
                        </div>
                    </div>
                    <div class="store-btn">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/play-store.svg" class="store-icon"/>
                        <div>
                            <div class="store-bnt__text__top">Available in</div>
                            <div class="store-bnt__text__bottom">Google Play</div>
                        </div>
                    </div>
                </div>
                <div class="footer__column">
                    <div class="footer__column-text bold"><?php _e( 'Buyers', 'prokkat' ); ?></div>
					<?php
					wp_nav_menu( array(
						'theme_location' => 'footer_buyers',
						'menu_class'     => 'footer__menu'
					) );
					?>
                </div>
                <div class="footer__column">
                    <div class="footer__column-text bold column-bigger"><?php _e( 'Sellers', 'prokkat' ); ?></div>
					<?php
					wp_nav_menu( array(
						'theme_location' => 'footer_sellers',
						'menu_class'     => 'footer__menu'
					) );
					?>
                </div>
                <div class="footer__column">
                    <div class="footer__column-text bold"><?php _e( 'About us', 'prokkat' ); ?></div>
					<?php
					wp_nav_menu( array(
						'theme_location' => 'footer_about',
						'menu_class'     => 'footer__menu'
					) );
					?>
                </div>
            </div>
        </div>
    </section>
    <div class="footer__copyright">
        <div class="container">
            <div class="footer__social-icons">
                <img src="<?php echo get_stylesheet_directory_uri() . "/img/twitter.svg"; ?>"/>
                <img src="<?php echo get_stylesheet_directory_uri() . "/img/facebook-2.svg"; ?>"/>
            </div>
            <div class="footer__logo footer__left">RENTHUB.COM.UA</div>
            <div>
            </div>
</footer>
<?php
map_script();
wp_footer();
?>
</body>
</html>
