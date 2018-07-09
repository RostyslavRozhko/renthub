<?php
/**
 * The template for displaying Author Archive pages.
 *
 *
 */
    //This sets the $curauth variable
    if (isset($_GET['author_name'])) :
        $curauth = get_user_by('login', $author_name);
    else :
        $curauth = get_userdata(intval($author));
    endif;
	
	$skype = get_the_author_meta('skype', $curauth->ID);
	$awards = get_the_author_meta( 'awards', $curauth->ID );
	$ava = get_the_author_meta( 'user_avatar', $curauth->ID );
	if( !$ava ) $ava = get_stylesheet_directory_uri() .'/img/no-avatar.svg';
   
    get_header();
?>
<div class="container">
<section>
    <div class="container">
        <div class="author author_grid">
            <div class="author__title author__title_grid">
                    <div class="author__img">
					   <img alt="" src="<?php echo $ava; ?>" />
                    </div>
                    <div class="author__name text-center">
                        <?php echo $curauth->nickname; ?>
						<span id="city"></span>
			            <input type="hidden" name="cc_city_id" id="cc_city_id" class="input input_add" value="<?php echo get_the_author_meta('city_id', $curauth->ID); ?>" />
                    </div>

            </div>
            <div class="author__contact author__contact_grid">
                    <div class="author__tel">
                        <?php echo get_the_author_meta('phone', $curauth->ID); ?>
                    </div>
                    <ul class="author__web">
                        <li><a href="<?php echo get_the_author_meta('url', $curauth->ID); ?>"><?php echo get_the_author_meta('url', $curauth->ID); ?></a></li>
                        <li><?php echo get_the_author_meta('user_email', $curauth->ID); ?></li>
                    </ul>
            </div>
            <div class="author__about">
                <h3><?php _e('About me', 'prokkat'); ?></h3>
                <p><?php
				  $desc = nl2br(get_the_author_meta('description', $curauth->ID));
				  if( $desc ) echo $desc;
				  else echo __( 'No description available', 'prokkat' );
				?></p>
            </div>
			<?php
              query_posts(array(
                'post_type' => POST_TYPE,
                'posts_per_page' => 4,
                'author' => $curauth->ID
              ));
              if (have_posts()) :
		    ?>
			
            <div class="author__adv">
                <h3><?php _e('Other ads from this manufacturer', 'prokkat'); ?></h3>
				
				<?php while (have_posts()): the_post(); 
                      $post_id = $post->ID;
                      $coordinates = get_post_meta($post->ID , 'cc_locations',true);
                      $coordinates = explode("},{" , $coordinates)[0];
                      $coordinates = preg_replace ("/[^0-9\s\,\.]/","", $coordinates);
                      $get_address = trim(get_address($coordinates));
                ?>
                <div class="cat-item desktop-only">
                    <div class="product-item">
                        <div class="product-item__img">
                            <?php echo ad_thumbnail(); ?>
                        </div>
                        <a href="<?php the_permalink() ?>" class="product-item__container-title">
                        <div class="product-item__title author_ads">
                            <?php echo title_excerpt(); ?>
                        </div>
                        <div class="product-item__price">
                                <?php echo price_output(); ?>
                        </div>
                        </a>
                    </div>
                </div>
				<?php endwhile; ?>

                <div class="wrap-carousel wrap-carousel_other wrap-carousel_advert mobile-only">
                        <div class="carousel" id="authorSlick">
                        <?php
                                    while (have_posts()): the_post();
                                      if( $current_post_id == $post->ID) continue;
                                      $post_id = $post->ID;
                                      $coordinates = get_post_meta($post->ID , 'cc_locations',true);
                                      $coordinates = explode("},{" , $coordinates)[0];
                                      $coordinates = preg_replace ("/[^0-9\s\,\.]/","", $coordinates);
                                      $get_address = trim(get_address($coordinates));
                                ?>
                        <div>
                        <div class="search-list__result">
                            <div class="search-list__img">
                            <a href="<?php the_permalink() ?>">
                                <img src="<?php echo ad_thumbnail_url(); ?>">
                            </a>
                            </div>
                            <div class="search-list__title">
                            <a href="<?php the_permalink() ?>"><?php echo mb_strtoupper(pll_title($post_id)); ?></a>
                            <div class="search-list__desc"><?php echo content_excerpt(); ?></div>
                            <div class="search-list__title-city">
                                <input type="hidden" value='<?php echo get_post_meta($post_id, 'cc_city_id', true) ?>' >
                            </div>
                            </div>
                            <div class="town"><?php echo $get_address;?></div>
                            <div class="search-list__price-container">
                            <div class="search-list__price">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/calendar-black.svg">
                                <?php echo price_output($post_id); ?>
                            </div>
                            <?php if(get_post_meta( $post_id, 'cc_price_deposit', true )) : ?>
                                <div class="search-list__deposit">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/protection-black.svg">
                                <?php echo get_post_meta( $post_id, 'cc_price_deposit', true ); ?> грн
                                </div>
                            <?php endif ?>
                            </div>
                            <a class="search-list__button search-list__button__grey fancybox-send-msg" href="#send-msg">
                            <input type="hidden" id="author_id" value="<?php echo $author_id; ?>">
                            <input type="hidden" id="user_id" value="<?php echo get_current_user_id(); ?>">
                            <input type="hidden" id="user_name" value="<?php the_author_meta('nickname');?>">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/envelope.svg">
                            </a>
                            <div class="search-list__phone-container">
                            <div class="search-list__phone-mobile hide">
                                <?php 
                                $phone = get_the_author_meta('phone', $s_post->post_author);
                                if($phone) {
                                ?>
                                <span class="telnumber hide"><?php echo $phone; ?></span>
                                <a>
                                    <img class="search-list__phone-image" src="<?php echo get_stylesheet_directory_uri(); ?>/img/call-answer-black.svg">
                                    <div id="tel<?php echo $post_id; ?>" class="shownum search-list_phone-number"></div>
                                </a>
                                <a href="#callFeedback"  class="btn btn_view show_phone search-list__call fancybox-feedback">
                                    <?php _e('Show', 'prokkat'); ?>
                                    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>" >
                                    <?php 
                                        $ava = get_the_author_meta( 'user_avatar', $author_id );
                                        if( !$ava ) $ava = get_stylesheet_directory_uri() .'/img/no-avatar.svg';
                                        $city = get_the_author_meta('city_name', $author_id);
                                        $city = explode("," , $city);
                                    ?>
                                    <input type="hidden" name="image" value="<?php echo $ava; ?>">
                                    <input type="hidden" name="city" value="<?php echo $city[0]; ?>">
                                    <input type="hidden" name="author_name" value="<?php echo the_author_meta('nickname');?>" >
                                    <input type="hidden" name="phone" value="<?php echo get_the_author_meta('phone'); ?>">
                                </a>
                                    <?php } else { ?>
                                <div class="search-list_phone-number"><?php _e('No phone number', 'prokkat'); ?></div>
                                <?php } ?>
                            </div>

                            <a href="#callFeedback" class="search-list__call search-list__button search-list__button__yellow fancybox-feedback">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/call-answer-black.svg">
                                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>" >
                                <?php 
                                    $ava = get_the_author_meta( 'user_avatar', $author_id );
                                    if( !$ava ) $ava = get_stylesheet_directory_uri() .'/img/no-avatar.svg';
                                    $city = get_the_author_meta('city_name', $author_id);
                                    $city = explode("," , $city);
                                ?>
                                <input type="hidden" name="image" value="<?php echo $ava; ?>">
                                <input type="hidden" name="city" value="<?php echo $city[0]; ?>">
                                <input type="hidden" name="author_name" value="<?php echo the_author_meta('nickname'); ?>" >
                                <input type="hidden" name="phone" value="<?php echo get_the_author_meta('phone'); ?>">
                            </a>
                            </div>
                        </div>
                        </div>
                    <?php endwhile;?>
                        </div>
                    </div>

            </div>
			
			<?php endif; wp_reset_query(); ?>
        </div>
    </div>
</section>
</div>
<?php 
get_footer(); 
include('phone_popup.php');
?>