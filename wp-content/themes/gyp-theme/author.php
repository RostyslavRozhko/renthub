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
	if( !$ava ) $ava = get_stylesheet_directory_uri() .'/img/no-avatar.png';
   
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
                'posts_per_page' => -1,
                'author' => $curauth->ID
              ));
              if (have_posts()) :
		    ?>
			
            <div class="author__adv">
                <h3><?php _e('Other ads from this manufacturer', 'prokkat'); ?></h3>
				
				<?php while (have_posts()): the_post(); ?>
				
                <div class="cat-item">
                    <div class="product-item">
                        <div class="product-item__img">
                            <?php echo ad_thumbnail(); ?>
                        </div>
                        <a href="<?php the_permalink() ?>" class="product-item__container-title">
                        <div class="product-item__title">
                            <?php echo title_excerpt(); ?>
                        </div>
                        <div class="product-item__price">
                                <?php echo price_output(); ?>
                        </div>
                        </a>
                    </div>
                </div>
				<?php endwhile; ?>
            </div>
			
			<?php endif; wp_reset_query(); ?>
        </div>
    </div>
</section>
</div>
<?php get_footer(); ?>