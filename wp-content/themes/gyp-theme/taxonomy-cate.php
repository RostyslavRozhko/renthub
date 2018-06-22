 <?php
  get_header();
  global $wp;
  
  $s_to = isset( $_REQUEST['search_loc'] ) ? $_REQUEST['search_loc'] : '';
  $address = isset( $_REQUEST['address'] ) ? $_REQUEST['address'] : '';
  $order = isset( $_REQUEST['order'] ) ? $_REQUEST['order'] : '';

  $wp_query->query_vars[CUSTOM_CAT_TYPE];
  foreach ($wp_query as $query) {
      if( is_object($query) && isset( $query->term_id )) {
        $current_cat_arr[] = $query->term_id;
        $parent = $query->parent;
    }
  }
  $current_cat_val = array_filter($current_cat_arr);
  $current_id = current($current_cat_val);

  if($parent == 0) {
    $main_cat = true;
    $parent = $current_id;
  }

  $term = get_term($current_id, CUSTOM_CAT_TYPE);
  $slug = $term->slug;
  $term_name = $term->name;

  $filters = get_field('filters', 'cate_' . $current_id);
  if($filters) {
    foreach($filters as $filter) {
      $slug = $filter->slug;
      if (isset($_REQUEST[$slug])){
        $filter_params[$slug] = $_REQUEST[$slug] ;
      }
    }
  }

  $id = pll_get_term($current_id, 'ru');
  if($id)
    $terms[] = $id;        
  $id = pll_get_term($current_id, 'uk');
  if($id)
    $terms[] = $id; 

  $limit = get_option('posts_per_page');
  $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

  $arguments = array(
    'post_type' => POST_TYPE,
    'posts_per_page' => $limit,
    'paged' => $paged,
    'post_status' => 'publish',
    'tax_query' => array(
      array(
          'include_children' => false,
          'taxonomy' => 'cate',
          'field' => 'term_taxonomy_id',
          'terms' => $terms,
        )
      ),
    'meta_query' => array()
  );

  if($s_to) {
    $arguments['meta_query'][] = array(
      'key' => 'cc_city_id',
      'value' => $s_to,
      'compare' => 'LIKE'
    );
  }

  if($to) {
    $arguments['meta_query'][] = array(
      'key' => 'cc_price',
      'value' => $to,
      'compare' => 'LIKE'
    );
  }

  if($order) {
    $arguments['meta_key'] = 'cc_price';
    $arguments['order'] = $order;        
    $arguments['orderby'] = 'meta_value_num';
  }

  if($filters) {
    foreach($filter_params as $filter => $param) {
      if($param){
        $selected_choices[] = $param;
        $fields = explode(',', $param);
        $filters_rel = array();
        $filters_rel[] = array('relation' => 'OR');
        foreach($fields as $field) {
          $filters_rel[] = array(
            'key' => $filter,
            'value' => $field,
            'compare' => 'LIKE'
          );
        }
      }
      $arguments['meta_query'][] = $filters_rel;
    }
  }

  $the_query = new WP_Query( $arguments );
 ?>

 <?php search_header_cate($parent, $address, $s_to); ?>

<div style="position: relative">
<?php
      if ($the_query->have_posts()) :   ?>
          <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDumu-d4N1FXsPcewuVrm4C5y-IZ3eg-5M&libraries=places&language=<?php echo pll_current_language('slug'); ?>"></script>
          <script>
            function initMap() {
              const mapId = document.getElementById('search-map')
              const defaultLocation = {lat: 50.4490244, lng: 30.5201343}

              const bounds = new google.maps.LatLngBounds()
              const geocoder = new google.maps.Geocoder;
              
              const map = new google.maps.Map(mapId, {
                center: defaultLocation,
                zoom: 11
              });

              const data = <?php echo json_encode(getSearchResults($the_query->posts)); ?>;
              let prevWindow;

              data.map(ad => {
                const content = `
                  <div class="infowindow__container">
                    <img src="${ad.img}" class="infowindow__img">
                    <div class="infowindow__text-container">
                      <div class="infowindow__title">${ad.title}</div>
                      <div class="infowindow__price">${ad.price}</div>
                      <a href="${ad.link}" class="infowindow__details"><?php _e('Details', 'prokkat'); ?></div>
                    </div>
                  </div>
                `
                const infowindow = new google.maps.InfoWindow({
                  content: content
                });
                const icon = "<?php echo get_stylesheet_directory_uri(); ?>/img/mark.png"

                if(ad.location) {
                  const locations = JSON.parse(ad.location)
                  length = locations.length

                  locations.map(position => {
                    const marker = new google.maps.Marker({
                      map: map,
                      position: position,
                      icon: icon,
                      title: ad.title
                    })

                    bounds.extend(position)
                    
                    marker.addListener('click', () => {
                      if(prevWindow) prevWindow.close()
                      infowindow.open(map, marker)
                      prevWindow = infowindow
                    })
                  })
                }
                  map.fitBounds(bounds)
                  const listener = google.maps.event.addListener(map, "idle", function() { 
                    if (map.getZoom() > 11) map.setZoom(11); 
                    if (map.getZoom() < 6) map.setZoom(6); 
                    google.maps.event.removeListener(listener); 
                  })
              })

              // $('.search-list__title-city').each(function(index) {
              //   const cities = JSON.parse($(this).find('input').val())
              //   const result = cities.map(id => {
              //     geocoder.geocode({'placeId': id}, function(results, status) {
              //       console.log(results, status)
              //       if (status === google.maps.GeocoderStatus.OK) {
              //         if (results[0]) {
              //           const place = results[0]
              //           console.log(place)
              //         }
              //       }
              //     })
              //   })
              //   // $(this).text(result.join())
              // })
            }

            google.maps.event.addDomListener(window, 'load', initMap);
          </script>
      <?php endif ?>
</div>

<div class="container">

<div class="breadcrumbs breadcrumbs-cat">
    <a href="<?php echo site_url(); ?>">
      <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/house.svg" >
    </a>
    <a href="<?php echo esc_url( get_term_link( $parent ))?>"><?php echo get_term($parent)->name ?></a>
    <?php if($parent != $current_id) : ?>
      <a href="<?php echo esc_url( get_term_link( $current_id ))?>"><?php echo get_term($current_id)->name ?></a>
    <?php endif; ?>
  </div>

  <div class="active-title"><span><?php single_cat_title(); ?></span><span class="items-count"> <?php $count = get_cate_count($current_id, $main_cat); if($count) { echo '('.$count.')'; } ?></span></div>
  <div class="container-results">
    <?php if(!$main_cat && $filters) : ?>
      <div class="menu-cat_grid">
            <form method="get" class="filters">
              <div class="filters__title">
                <div class="filters__title__top"><?php _e('Filters', 'prokkat'); ?></div>
              </div>
              <div class="filters__title filters__title-mobile hide">
                <div class="filters__title__top"><?php _e('Filters', 'prokkat'); ?></div>
                <img class="header-category-img hide" src="<?php echo get_stylesheet_directory_uri(); ?>/img/arrow-down-sign-to-navigate.svg">
              </div>
              <?php 
                  $filter_inputs = '';
                  
                  if($filters) {
                    foreach ($filters as $filter) {
                      $filter_id = $filter->term_id;                
                      $tag_id = 'tags_' . $filter_id;
                      $filter_type = get_field('filter_type', $tag_id);
                      
                      if($filter_type != 'input'){
                        echo '<div class="filters__container">';
                        $name = $filter->name;
                        $slug = $filter->slug;
                        $choices = array_filter(explode(';', str_replace(array("\n","\r"), '', get_field('choices', $tag_id))));
                        
                        echo '<div class="filters__show-btn">
                            <div class="filters__title__top">'. $name .'</div>
                            <img src="'. get_stylesheet_directory_uri() .'/img/arrow-down-sign-to-navigate.svg" class="filters__show-btn__arrow">
                          </div>';
                        
                        echo '<div class="filters__vis-container">';
                        foreach ($choices as $value) {
                          $value = trim($value);
                          if($value) {
                            echo '<label class="checkbox-container">';
                            echo '<input type="checkbox" data-filter="'. $slug .'" value="'. $value .'">';
                            echo '<span class="checkmark"></span>';
                            echo '<div>'. $value .'</div>';
                            echo '</label>';     
                          } 
                        }
                        echo '</div>';
                        $filter_inputs .= '<input type="hidden" class="'. $slug .' filter_value_field" name="'. $slug .'" value="">';
                        echo '</div>';
                      }
                    }
                  }
              ?>
              <div id="filter_inputs">
                  <?php echo $filter_inputs; ?>
              </div>
              <input type="hidden" id="filtersVals" value="<?php echo implode(',', $selected_choices); ?>">
              <input class="save-btn full-width" type="submit" value="<?php _e('Filter', 'prokkat'); ?>">
            </form>
      </div>
                <?php endif; ?>

    <div class="cat-main">
      <?php
      if($main_cat) { 
        ?>
          <div class="search-list__container"> 
            <div class="search-list__results">  
              <?php 
                $categories = get_categories(array(
                    'taxonomy' => 'cate',
                    'hide_empty' => false,
                    'parent'=> $parent,
                )); 
                $counter = 0;
                foreach ( $categories as $category ) {
                  $cat_id = $category->term_id;
                  $taxonomyName = 'cate';   
                  $count = get_cate_count($cat_id);
                  if($count < 1)
                    continue;
              ?>
              <div class="search-list__cate-container">
                  <a href="<?php echo get_term_link( $cat_id, 'cate' ); ?>">
                    <img src="<?php echo get_wp_term_image($cat_id); ?>" class="search-list__cate-img">
                  </a>
                  <a href="<?php echo get_term_link( $cat_id, 'cate' ); ?>" class="search-list__cate-title"><?php echo $category->name ?></a>
                  <div class="search-list__cate-price"><?php echo max_price($cat_id); ?> грн/день</div>
                  <div class="search-list__cate-num">Предложений (<?php echo $count; ?>)</div>
                  <a href="<?php echo get_term_link( $cat_id, 'cate' ); ?>" class="search-list__cate-details search-list__cate-details-text">Подробнее</a>
                  <a href="<?php echo get_term_link( $cat_id, 'cate' ); ?>" class="search-list__cate-details search-list__cate-details-arrow"><img src="<?php echo get_stylesheet_directory_uri(); ?>/img/arrow-right.svg"></a>
              </div>
              <?php if($counter == 2) : 
                  include('banner.php');
                ?>
              <?php
                  endif;
                  $counter++;
                }
              if($counter < 2)
                include('banner.php');
              ?>
          </div>
        </div>   
      <?php
      } else {
      if ($the_query->have_posts()) :   ?>
        <div style="position:relative">
          <div class="map" id="search-map"></div>
          <div class="hide-map hide"><?php _e('Hide map', 'prokkat'); ?></div>
          <div class="show-map"><?php _e('Show map', 'prokkat'); ?></div>
        </div>

          <div class="search-list__container">
            <div class="cats__top-panel">
            <div class="cats__sort">
                <div class="cats__sort-title"><?php _e('Sort by', 'prokkat') ?>: </div>
                  <?php preg_match('/\?.*/', $_SERVER['REQUEST_URI'], $match);
                    $url = $match ? $match[0] : ''; 
                  ?>
                  <a href="<?php echo remove_query_arg('order', $url) ? remove_query_arg('order', $url) : '?'; ?>" class="<?php if($order == '') echo 'active'; ?>"><?php _e('Newer', 'prokkat'); ?></a>
                  <a href="<?php echo add_query_arg( array( 'order'=>'ASC' ), $url); ?>" class="<?php if($order == 'ASC') echo 'active'; ?>"><?php _e('Cheaper', 'prokkat'); ?></a>
                  <a href="<?php echo add_query_arg( array( 'order'=>'DESC' ), $url); ?>" class="<?php if($order == 'DESC') echo 'active'; ?>"><?php _e('Expensive', 'prokkat'); ?></a>
                </div>
              <div class="cats__city">
                <div class="cats__city-title"><?php _e('City', 'prokkat'); ?>: </div>
                <a href="" class="cats__city-name"><?php if($address) { echo $address; } else { echo __('All Ukraine', 'prokkat'); } ?></a>
              </div>
            </div>  
            <div class="search-list__results">            
          <?php 
            while ($the_query->have_posts()) : 
              $the_query->the_post();
              $post_id = get_the_ID();
              $post = get_post($post_id);
              $author = get_userdata($post->post_author); 
              $author_id = $author->ID;

          ?>
          <div class="search-list__result">
            <div class="search-list__img">
              <a href="<?php the_permalink() ?>">
                <img src="<?php echo ad_thumbnail_url(); ?>">
              </a>
            </div>
            <div class="search-list__title">
              <a href="<?php the_permalink() ?>"><?php echo pll_title($post_id); ?></a>
              <div class="search-list__desc"><?php echo content_excerpt(); ?></div>
              <div class="search-list__title-city">
                <input type="hidden" value='<?php echo get_post_meta($post_id, 'cc_city_id', true) ?>' >
              </div>
            </div>
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
              <input type="hidden" id="user_name" value="<?php echo $author->display_name; ?>">
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
                        if( !$ava ) $ava = get_stylesheet_directory_uri() .'/img/no-avatar.png'; 
                    ?>
                    <input type="hidden" name="image" value="<?php echo $ava; ?>">
                    <input type="hidden" name="author_name" value="<?php echo $author->display_name; ?>" >
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
                    if( !$ava ) $ava = get_stylesheet_directory_uri() .'/img/no-avatar.png'; 
                ?>
                <input type="hidden" name="image" value="<?php echo $ava; ?>">
                <input type="hidden" name="author_name" value="<?php echo $author->display_name; ?>" >
                <input type="hidden" name="phone" value="<?php echo get_the_author_meta('phone'); ?>">
              </a>
              </div>
          </div>
	    <?php
          endwhile;
          echo '<div class="paginator">';
          echo paginate_links( array(
            'mid_size'  => 2,
            'prev_text' => '<i class="fas fa-angle-left"></i>',
            'next_text' => '<i class="fas fa-angle-right"></i>',
          ) ); 
          echo '</div>';
          wp_reset_postdata();
	    else : ?>
        <div class="fanks__title fanks__title_category"><?php _e('This category does not include ads', 'prokkat'); ?></div> 
        
      
        <?php 
          endif; 
        }
        ?>
        </div>
          </div>
        </div>
	  
	</div>
</div>
<section>
<div class="container">
  <div class="cities-section-title"><?php _e('Rent and rent', 'prokkat'); echo ' ' . $term_name; ?></div>
            <div class="footer-text">
              <p>
                <?php echo get_field('text', 'cate_'.$current_id); ?>
              </p>
            </div>
</section>

<?php 
include_once('phone_popup.php');
include_once('sweet_leads.php');
get_footer(); 
?>
