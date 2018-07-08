<?php
  get_header();
  nocache_headers();

    if (isset($_GET['author_name'])) :
            $curauth = get_user_by('login', $author_name);
    else :
        $curauth = get_userdata(intval($author));
    endif;

  if (have_posts()) :
    while (have_posts()): the_post();
	  $current_post_id = $post->ID;
	  $author_id = $post->post_author;
	  $ad_type = get_post_meta($post->ID, 'cc_add_type', true);
	  $phone = get_the_author_meta('phone');
      $main_img = get_post_meta( $post->ID, 'img1', true );
      $state = get_post_meta($post->ID, 'cc_state', true);
      $terms = wp_get_post_terms($post->ID, CUSTOM_CAT_TYPE);

      usort($terms, function($a, $b)
      {
          return strcmp($a->term_id, $b->term_id);
      });

      $parent = array_slice($terms, 0, 1)[0];
      $subcat = array_slice($terms, 1, 1)[0];


      $ava = get_the_author_meta('user_avatar', $author_id);
      if( !$ava ) $ava = get_stylesheet_directory_uri() .'/img/no-avatar.svg';

      $user = get_userdata($author_id);
      $user_reg = $user->get('user_registered');
      $datetime = new DateTime($user_reg);
      $date_registered = $datetime->format('d.m.Y');
  
?>
 <section class="header-section">
    <div class="container">
        <?php 
            $lang = ( pll_current_language() != 'uk' ) ? '-'.pll_current_language() : ''; ?>
        
        <div class="search-grp search-grp_header">
                <div class="header__search-show-btn hide">
                  <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/magnifying-glass-2.svg">
                  <?php _e('Search', 'prokkat'); ?>
                </div>
                <form action="<?php echo site_url('search'.$lang.'/'); ?>" method="get" class="form_srch-header">
                    <div class="header__popup__container">
                      <div class="input-wrp input-wrp_grid header__category__container" style="padding: 0 15px">
                          <div class="header__category__text-container">
                              <img class="header__category__text-container-img" src="<?php echo get_stylesheet_directory_uri(); ?>/img/bookmark.svg">
                              <div><?php echo $parent->name; ?></div>
                          </div>
                          <img class="header-category-img" src="<?php echo get_stylesheet_directory_uri(); ?>/img/arrow-down-sign-to-navigate.svg">
                      </div>  
                      <div class="header__category__window header__category__window-small header__category__paddings hide">
                                <ul class="menu-cat">
                                  <?php
                                    $cat_args = array(
                                    'parent' => $parent->term_id,
                                    'orderby' => 'name',
                                    'hierarchical' => 1,
                                    'taxonomy' => CUSTOM_CAT_TYPE,
                                    'hide_empty' => 0,
                                    'style' => 'cat_list',
                                    'title_li' => '',
                                    'walker' => new Custom_Walker_Category(),
                                    );
                                    
                                    wp_list_categories($cat_args);
                                  ?>
                                </ul>
                          <div class="return-to-main-cat">
                              <a href="<?php echo get_site_url(); ?>" ><i class="fa fa-arrow-left"></i><?php _e('Return to the main categories', 'prokkat'); ?></a>
                          </div>
                      </div>  
                    </div>
                    <div class="input-wrp input-wrp_grid">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/flag.svg" class="input-wrp__ico search-icon-input">
                        <input type="text" id="s_address" class="input input_srch-header" placeholder="<?php echo __('All Ukraine', 'prokkat'); ?>" name="address" value="" />
                        <input type="hidden" id="s_city_id" class="input input_add" name="search_loc" value="" />
                    </div>
                    <div class="input-wrp input-wrp_grid header__search-input">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/magnifying-glass-2.svg" class="input-wrp__ico search-icon-input">
                        <input type="text" class="input input_srch-header" placeholder="<?php _e('Search', 'prokkat'); ?>" name="search_for" value="" id="autocomplete-field" />
                    </div>
                    <div class="input-wrp input-wrp_btn header__search-btn_container">
                        <input type="submit" class="header__search-btn" value="<?php _e('Find', 'prokkat') ?>"/>
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/magnifying-glass-2.svg">
                    </div>
                </form>
        </div>
    </div>
</section>
<!-- Content -->
<section class="advert_section">
    <div class="container">
        <div class="breadcrumbs">
        <a href="<?php echo site_url(); ?>">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/house.svg" >
        </a>
            <a href="<?php echo get_term_link( $parent->term_id ); ?>"><?php echo $parent->name; ?></a>
            <a href="<?php echo get_term_link( $subcat->term_id ); ?>"><?php echo $subcat->name; ?></a>
        </div>
        <div class="advert">
		  <?php if( $post->post_status == "draft" ) : ?>
		  <div class="notification mess-info mess-info_center">
             <?php _e('This ad is no longer actual', 'prokkat'); ?>
          </div>
		  <?php endif; ?>
            <div class="title title_grid">
                <div class="title_advert"><?php echo the_title(); ?></div>
            </div>
	        <?php  if( $main_img && file_url_exists( $main_img )) : ?>		
                <div class="advert__img-wrap">
                  <a href="<?php echo $main_img; ?>" class="fancybox"  title="" rel="gallery1">
				    <img src="<?php echo $main_img; ?>" alt="adv" class="advert__img-big" style="display:block;">
				  </a>
				  <ul class="advert__img-wrap">
				  <?php 
				    $img2 = get_post_meta( $post->ID, 'img2', true );
					$img3 = get_post_meta( $post->ID, 'img3', true );
					if( $img2 && file_url_exists( $img2 )) :
					  $src_info = pathinfo( $img2 );
                      $img2_resized = $src_info['dirname'].'/'.$src_info['filename']."_145X86.".$src_info['extension'];
					  $imgsrc2 = file_url_exists( $img2_resized ) ? $img2_resized : $img2;
				  ?>
                    <li class="advert__img-item">
					  <a href="<?php echo $img2; ?>"  class="advert__img-link fancybox" title="" rel="gallery1">
                        <img src="<?php echo $imgsrc2; ?>" alt="ad-photo">
					  </a>
				    </li>
				  <?php endif;
				    if( $img3 && file_url_exists( $img3 )) :
				      $src_info = pathinfo( $img3 );
                      $img3_resized = $src_info['dirname'].'/'.$src_info['filename']."_145X86.".$src_info['extension'];
					  $imgsrc3 = file_url_exists( $img3_resized ) ? $img3_resized : $img3;
					?>
					<li class="advert__img-item">
					  <a href="<?php echo $img3; ?>"  class="advert__img-link fancybox" title="" rel="gallery1">
                        <img src="<?php echo $imgsrc3; ?>" alt="ad-photo">
					  </a>
				    </li>
					<?php endif; ?>
				  </ul>
                </div>

                <div class="wrap-carousel wrap-carousel_other wrap-carousel_advert mobile-only">
                    <div class="carousel" id="mobileSlick">
                        <?php $arr_img = array($main_img , $img2 , $img3); ?>
                        <?php if( $img2 && file_url_exists($img2) && $img3 && file_url_exists($img3)) : ?>
                            <?php foreach ($arr_img as $img): ?>
                                <div class = "carousel__item">
                                    <div class="gallery-item">
                                        <div class="product-item">
                                            <div class="product-item__img">
                                                <a href="<?php echo $img; ?>" class="fancybox"  title="" rel="gallery1">
                                                    <img src="<?php echo $img; ?>" alt="adv" class="advert__img-big" >
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php else: ?>
                                    <a href="<?php echo $main_img; ?>" class="fancybox"  title="" rel="gallery1">
                                        <img src="<?php echo $main_img; ?>" alt="adv" class="advert__img-big" >
                                    </a>
                            <?php endif; ?>
                    </div>
                </div>
				
				<?php endif; ?>

            <div class="info">
                <div class="contact-info">
			        <?php if( get_post_meta( $post->ID, 'cc_price', true )) { ?>
                        <div class="price-container">
					        <?php
					        $price = get_post_meta( $post->ID, 'cc_price', true );
					        $price_week = get_post_meta( $post->ID, 'cc_price_week', true );
					        $price_more = get_post_meta( $post->ID, 'cc_price_more', true );
					        ?>
                            <div class="price-container__block">
                                <div class="price-container__block-top price-container__block-green">
                                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/calendar.svg">
                                    <div class="price-container__block-text">1 <?php _e('day', 'prokkat'); ?></div>
                                </div>
                                <div class="price-container__block-bot">
                                    <div class="price-container__price"><?php echo $price; ?></div>
                                </div>
                            </div>
                            <div class="price-container__block">
                                <div class="price-container__block-top price-container__block-green">
                                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/calendar.svg">
                                    <div class="price-container__block-text">7 <?php _e('days', 'prokkat'); ?></div>
                                </div>
                                <div class="price-container__block-bot">
                                    <div class="price-container__price"><?php if($price_week) { echo $price_week; } else { echo $price; } ?></div>
                                </div>
                            </div>
                            <div class="price-container__block">
                                <div class="price-container__block-top price-container__block-green">
                                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/calendar.svg">
                                    <div class="price-container__block-text">1 <?php _e('month', 'prokkat'); ?></div>
                                </div>
                                <div class="price-container__block-bot">
                                    <div class="price-container__price"><?php if($price_more) { echo $price_more; } else { echo $price; } ?></div>
                                </div>
                            </div>
                            <div class="price-container__block nomargin">
                                <div class="price-container__block-top price-container__block-red">
                                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/protection.svg">
                                    <div class="price-container__block-text"><?php _e('Deposit', 'prokkat'); ?></div>
                                </div>
                                <div class="price-container__block-bot">
                                    <div class="price-container__price"><?php echo get_post_meta( $post->ID, 'cc_price_deposit', true ); ?></div>
                                </div>
                            </div>
                        </div>
			        <?php } else { ?>
                        <div class="contact-ad__container nomargin"><?php echo price_output(); ?></div>
			        <?php } ?>

                    <div class="contact-ad__container">
                        <div class="author-side__name">
                            <img class="contact-ad__author-photo" src="<?php echo $ava; ?>" />
                            <div class="contact-ad__author-text">
                                <a href="<?php echo get_author_posts_url($author_id);?>"><?php echo the_author_meta('nickname');?></a>
                                <span class="date_registered">Дата регистрации: <?php echo $date_registered;?></span>
                                <span>
                                    <a class="single__state" data-state="<?php echo $state; ?>"></a>
                                    <a style="font-weight: lighter; font-size: 14px; color:#63666c;">(512)</a>
                                </span>
                            </div>
                        </div>
                    </div>

			        <?php if ($author_id != $user_ID) : ?>
                        <a href="#write">
                            <a class="contact__write fancybox-send-msg" href="#send-msg">
                                <input type="hidden" id="author_id" value="<?php echo $author_id; ?>">
                                <input type="hidden" id="user_id" value="<?php echo $user_ID; ?>">
                                <input type="hidden" id="user_name" value="<?php echo $current_user->nickname; ?>">
                                <i class="fas fa-envelope" style="color: white; padding-right: 15px"></i>
                                <span class="contact__text"><?php _e('Ask the manufacturer', 'prokkat'); ?></span>
                            </a>
                        </a>
			        <?php endif ?>

			        <?php if ( $phone ) : ?>
                        <div class="tel">
                            <div class="phone-container">
                                <i class="fas fa-phone" style="padding-right: 15px"></i>
                                <a>
                                    <span id="tel<?php echo $post->ID; ?>" class="nuber-tel nuber-tel_big"></span>
                                </a>
                                <span id="phonenumhid" style="display:none"><?php echo $phone; ?></span>
                            </div>
                            <a href="#callFeedback" class="btn btn_view fancybox-feedback" style="text-decoration: underline;
" id="viewbtn2" ><?php _e('Show', 'prokkat'); ?></a>
                        </div>
			        <?php endif; ?>

                </div>
                <div class="maps-wrp">
                    <div id='address_list' style="padding: 25px 25px 0 25px"></div>
                    <input id="cc_address_list" type="hidden" value='<?php echo get_post_meta($post->ID, 'cc_address_list', true); ?>' />
                    <div class="maps">
                        <div id="map_canvas" style="height: 250px;"></div>
                    </div>
                </div>
            </div>

            <div class="advert__tags">
                <div class="advert__tags advert__tags_grid">
                    <h3 class="advert__tags-title"><?php _e('State', 'prokkat') ?></h3>
                    <div class="single__state" data-state="<?php echo $state; ?>"></div>
                </div>
                <div class="advert__tags advert__tags_grid">
                    <h3 class="advert__tags-title"><?php _e('Category', 'prokkat'); ?></h3>
			        <?php
			        $term = end($terms);
			        $pll_term_id = pll_get_term($term->term_id);
			        if($pll_term_id == false || $pll_term_id == $term->term_id) {
				        echo '<a href="'.esc_url( get_term_link( $term->term_id )) .'">'.$term->name.'</a>';
			        } else {
				        $term = get_term($pll_term_id, CUSTOM_CAT_TYPE);
				        $name = $term->name;
				        echo '<a href="'.esc_url( get_term_link( $pll_term_id)) .'">'.$name.'</a>';
			        }
			        ?>
                </div>
                <div class="advert__tags advert__tags_grid">
                    <h3 class="advert__tags-title"><?php _e('Added', 'prokkat') ?></h3>
                    <span class="advert_bot-text"><?php the_time(); ?>, </span>
                    <span class="advert_bot-text"><?php the_date(); ?></span>
                </div>
                <div class="advert__tags advert__tags_grid">

			        <?php if( $author_id != $user_ID ) cc_setPostViews(get_the_ID());advert_bot-text ?>

                    <h3 class="advert__tags-title"><?php _e('Views: ', 'prokkat'); ?></h3>
                    <span class="advert_bot-text"><?php echo cc_getPostViews(get_the_ID()); ?></span>
                </div>
            </div>


                <div class="advert__descr">
                    <h3><?php _e('Description', 'prokkat'); ?></h3>
                    <?php the_content(); ?>
                </div>

                <div class="filters-values">
                <?php 

                $additional_fields = get_additional_post_fields();
                foreach($additional_fields as $field) {
                    $man = get_post_meta($post->ID, $field['slug'], true);
                    if($man) : 
                    ?>
                        <div class="filters-values__item">
                            <div class="filters-values__item__name"><?php echo $field['name'] ?></div>
                            <div class="filters-values__item__value"><?php echo $man; ?></div>
                        </div>
                        
                <?php 
                    endif; 
                    }
                ?>
    
                <?php
                $filters = get_field('filters', CUSTOM_CAT_TYPE . '_' . $subcat->term_id);
                if($filters) : ?>
                        <?php 
                            foreach ($filters as $filter) {
                                $filter_name = $filter->name;
                                $filter_value = get_post_meta($post->ID, $filter->slug, true);
                                if($filter_value) :
                            ?>
                                <div class="filters-values__item">
                                    <div class="filters-values__item__name"><?php echo $filter_name; ?></div>
                                    <div class="filters-values__item__value"><?php echo $filter_value; ?></div>
                                </div>
                            <?php 
                            endif;
                            } ?>
                <?php endif; ?>
                </div>
                
                <div class="soc-wrap">
				<?php echo do_shortcode('[supsystic-social-sharing id="1"]'); ?>
                </div>
				
				<?php if( $author_id != $user_ID ) : ?>
                <div class="advert__mess-wrp" id="write">
                    <?php include_once( get_stylesheet_directory() . '/sweet_leads.php'); ?>
                </div>
				<?php endif; ?>

        </div>
    </div>
</section>

<?php endwhile; endif; wp_reset_query(); ?>

<?php
  query_posts(array(
    'post_type' => POST_TYPE,
    'posts_per_page' => 4,
    'author' => $author_id
  ));
  $count = 0;
  if (have_posts()) : while (have_posts()) { the_post(); $count++; }
  if ( $count > 1 ) :
?>
<section class="carousel-products padding-top" style="background-color: #f4f6f9;">
    <div class="container">
        <div class="group-product group-product_grid">
            <h3 class="group-product__title"><?php _e('Other ads from this manufacturer', 'prokkat'); ?></h3>
            <div class="row">
                <div class="content">
                    <div class="gallery text-center">
							    <?php
						            while (have_posts()): the_post();
									  if( $current_post_id == $post->ID) continue;
                                      $coordinates = get_post_meta($post->ID , 'cc_locations',true);
                                      $coordinates = explode("},{" , $coordinates)[0];
                                      $coordinates = preg_replace ("/[^0-9\s\,\.]/","", $coordinates);
                                      $get_address = trim(get_address($coordinates));
                                ?>
                                    <!--<div class="gallery-item">
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
                                    </div>-->
                                              <div class="search-list__result">
            <div class="search-list__img">
              <a href="<?php the_permalink() ?>">
                <img src="<?php echo ad_thumbnail_url(); ?>">
              </a>
            </div>
            <div class="search-list__title">
              <a href="<?php the_permalink() ?>"><?php echo mb_strtoupper(pll_title($post->ID)); ?></a>
              <div class="search-list__desc"><?php echo content_excerpt(); ?></div>
              <div class="search-list__title-city">
                <input type="hidden" value='<?php echo get_post_meta($post_id, 'cc_city_id', true) ?>' >
              </div>
            </div>
            <div class="town"><?php echo $get_address;?></div>
            <div class="search-list__price-container">
              <div class="search-list__price">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/calendar-black.svg">
                <?php echo price_output($post->ID); ?>
              </div>
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
                  <a href="#callFeedback"  class="btn btn_view show_phone search-list__call fancybox-feedback">
                    <img class="search-list__phone-image" src="<?php echo get_stylesheet_directory_uri(); ?>/img/call-answer-black.svg">
                    <div id="tel<?php echo $post_id; ?>" class="shownum search-list_phone-number"></div>
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
                  <a href="#callFeedback"  class="btn btn_view show_phone search-list__call fancybox-feedback" id="view_none">
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
								<?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; endif; wp_reset_query(); ?>
        
<?php
include_once('phone_popup.php');
get_footer(); 
?>
