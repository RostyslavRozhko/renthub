<?php
/**
 * Template Name: Template All Ads
 */

 get_header(); ?>

         <div class="container">
             <div class="content">
			     <div class="gallery text-center">
			     <?php
				   $limit = get_option('posts_per_page');
                   $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                   query_posts(array(
                     'post_type' => POST_TYPE,
                     'posts_per_page' => $limit,
                     'paged' => $paged
                   ));
					
				   if (have_posts()) :                   
                     while (have_posts()) : the_post();
				  ?>
				    <div class="gallery-item">
                        <div class="product-item">
						
                            <div class="product-item__img">
							    <div class="group-product_premium__label"><?php echo __( 'Premium', 'cc' ); ?></div>
                                <?php echo ad_thumbnail(); ?>
                            </div>
							
                            <div class="product-item__title">
                                <a href="<?php the_permalink() ?>" class="link product-item__title">
								<?php echo title_excerpt(); ?>
								</a> <?php echo __( 'from', 'cc' ); ?> <?php the_author_posts_link(); ?>
                            </div>
							
                            <?php echo price_output(); ?>
							
                        </div>
                    </div>

                    <?php endwhile; cc_pagination(); endif; wp_reset_query(); ?>
                </div>
            </div>  
        </div>

<?php get_footer(); ?>