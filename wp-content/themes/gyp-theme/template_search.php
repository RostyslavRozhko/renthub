<?php
/**
 * Template Name: Template Search 
 */
 
  get_header();
?>
    <?php
        $s_for = isset( $_REQUEST['search_for'] ) ? $_REQUEST['search_for'] : '';
        $s_to = isset( $_REQUEST['search_loc'] ) ? $_REQUEST['search_loc'] : '';
        $price_from = isset( $_REQUEST['price_from'] ) ? $_REQUEST['price_from'] : '';
        $price_to = isset( $_REQUEST['price_to'] ) ? $_REQUEST['price_to'] : '';
        $categories = isset( $_REQUEST['cats'] ) ? $_REQUEST['cats'] : '';
        $cats_str = $categories;
        $order = isset( $_REQUEST['order'] ) ? $_REQUEST['order'] : '';

        $cat_array = explode(",", $categories);
        foreach($cat_array as $cat) {
          $cats[] = pll_get_term($cat, 'uk');
          $cats[] = pll_get_term($cat, 'ru');      
        }

        $terms = array();
        $children = array();

        $address = isset( $_REQUEST['address'] ) ? stripslashes( $_REQUEST['address'] ) : '';
        $search_text = $s_for ? '"' . $s_for . '" ' : '';
        $search_header =  __('Search result: ', 'prokkat') . $search_text;

        $limit = get_option('posts_per_page');
        $page = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
        if(!$page)
            $page = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1; 
        
        $arguments = array(
          'post_type' => POST_TYPE,
          'posts_per_page' => $limit,
          'paged' => $page,
          'post_status' => 'publish',
          'meta_query' => array(
              array(
                  'key' => 'cc_add_type',
                  'value' => 'draft',
                  'compare' => '!='
              )
          )
        );
	$args = array(
    		'post_type' => POST_TYPE,
    		'posts_per_page' => -1,
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
	$all_query = new WP_Query($args);

        if($s_for) {
          $arguments['title_like'] = $s_for;
        }

        if($price_from) {
          $arguments['meta_query'][] = array(
            'key' => 'cc_price',
            'value' => $price_from,
            'compare' => '>='
          );
        }

        if($price_to) {
          $arguments['meta_query'][] = array(
            'key' => 'cc_price',
            'value' => $price_to,
            'compare' => '<='
          );
        }

        if($s_to) {
          $arguments['meta_query'][] = array(
            'key' => 'cc_city_id',
            'value' => $s_to,
            'compare' => 'LIKE'
          );
        }


        if($categories) {
          $cat_array = explode(",", $categories);
          $cats = array();
          foreach($cat_array as $cat) {
            $id = pll_get_term($cat, 'ru');
            if($id)
              $cats[] = pll_get_term($cat, 'ru');      
              
            $id = pll_get_term($cat, 'uk');
            if($id)
              $cats[] = pll_get_term($cat, 'uk');  
          }

          $arguments['tax_query'] = array(
            array(
                'include_children' => false,
                'taxonomy' => 'cate',
                'field' => 'term_taxonomy_id',
                'terms' => $cats,
              )
            );
        }

        if($order) {
          $arguments['meta_key'] = 'cc_price';
          $arguments['order'] = $order;        
          $arguments['orderby'] = 'meta_value_num';
        }

        $the_query = new WP_Query($arguments);

        $temp_query = $wp_query;
        $wp_query   = NULL;
        $wp_query   = $the_query;

      ?>

<?php search_header_main($address, $s_to); ?>

<div style="position: relative">
<?php
      if ($the_query->have_posts()) :   ?>
	 <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDumu-d4N1FXsPcewuVrm4C5y-IZ3eg-5M&libraries=places&language=ru" type="text/javascript"></script>
  <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
          <script>
              function initMap() {

                    const mapId = document.getElementById('search-map')
                    const defaultLocation = {lat: 50.4490244, lng: 30.5201343}

                    const bounds = new google.maps.LatLngBounds()
                    const geocoder = new google.maps.Geocoder;
                    
                      const map = new google.maps.Map(mapId, {
                              center: defaultLocation,
                              zoom: 4
                      });

                      const data = <?php echo json_encode(getSearchResults($the_query->posts)); ?>;

                      console.log(data);

                      let prevWindow;

                      var markers = [];

                      const icon = "<?php echo get_stylesheet_directory_uri(); ?>/img/mark.png";

                      for (var i = 0; i < data.length; i++) {

                      var position = JSON.parse(data[i].location);

                        const content = `
                          <div class="infowindow__container">
                            <img src="${data[i].img}" class="infowindow__img">
                            <div class="infowindow__text-container">
                              <div class="infowindow__title">${data[i].title}</div>
                              <div class="infowindow__price">${data[i].price}</div>
                              <div class="infowindow__name">${data[i].name}</div>
                              <div class="infowindow__address">${data[i].address}</div>
                              <a href="${data[i].link}" class="infowindow__details"><?php _e('Details', 'prokkat'); ?></a>
                              <a class="search-list__button search-list__button__grey fancybox-send-msg" id="mess_author" href="#send-msg">
                                <input type="hidden" id="author_id" value="<?php echo $author_id; ?>">
                                <input type="hidden" id="user_id" value="<?php echo get_current_user_id(); ?>">
                                <input type="hidden" id="user_name" value="<?php the_author_meta('nickname');?>">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/envelope.svg">
                              </a>
                              <a href="#callFeedback" id="call_author" class="search-list__call search-list__button search-list__button__yellow fancybox-feedback">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/call-answer-black.svg">
                                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>" >
                                <?php 
                                    $ava = get_the_author_meta( 'user_avatar', $author_id );
                                    if( !$ava ) $ava = get_stylesheet_directory_uri() .'/img/no-avatar.png'; 
                                ?>
                                <input type="hidden" name="image" value="<?php echo $ava; ?>">
                                <input type="hidden" name="author_name" value="<?php echo the_author_meta('nickname'); ?>" >
                                <input type="hidden" name="phone" value="<?php echo get_the_author_meta('phone'); ?>">
                              </a>
                            </div>
                          </div>
                        `

                        const infowindow = new google.maps.InfoWindow({
                          content: content
                        });

                        var marker = new google.maps.Marker({
                                      map: map,
                                      icon: icon,
                                      position: position[0],
                                      title: data[i].title
                            });
                        
                            bounds.extend(position[0]);

                             google.maps.event.addListener(marker, 'click', function () {
                                if(prevWindow) 
                                prevWindow.close()
                                infowindow.open(map, this);
                                prevWindow = infowindow
                            });

                            map.fitBounds(bounds)
                            const listener = google.maps.event.addListener(map, "idle", function() { 
                              if (map.getZoom() > 11) map.setZoom(11); 
                              if (map.getZoom() < 4) map.setZoom(4); 
                              google.maps.event.removeListener(listener); 
                            });
                            

                            markers.push(marker);

                        if (position.length > 1) {
                          for ( var j = 0; j < position.length ; j++){

                          var marker = new google.maps.Marker({
                              map: map,
                              icon: icon,
                              position: position[j],
                              title: data[i].title
                          });

                          bounds.extend(position[j]);

                          google.maps.event.addListener(marker, 'click', function () {
                            if(prevWindow) prevWindow.close()
                            infowindow.open(map, this);
                            prevWindow = infowindow
                          });

                          map.fitBounds(bounds)
                          const listener = google.maps.event.addListener(map, "idle", function() { 
                            if (map.getZoom() > 11) map.setZoom(11); 
                            if (map.getZoom() < 4) map.setZoom(4); 
                            google.maps.event.removeListener(listener); 
                          });
                          }

                          markers.push(marker);
                          }
	                      }
				            var markerCluster = new MarkerClusterer(map, markers,{
                      imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m',
                      maxZoom: 10,
                      gridSize: 50
                        }
                    );
               }
               google.maps.event.addDomListener(window, 'load', initMap);
          </script>
      <?php endif ?>
</div>

    <div class="container">
      <div class="active-title"><span><?php echo $search_header; ?></span></div>
    </div>

      <div class="container">
            <div class="search-container">
            <div class="search-results">
            <?php
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
                  <a href="<?php echo remove_query_arg('order', basename($_SERVER['REQUEST_URI'])); ?>" class="<?php if($order == '') echo 'active'; ?>"><?php _e('Newer', 'prokkat'); ?></a>
                  <a href="<?php echo add_query_arg( array( 'order'=>'ASC' ), basename($_SERVER['REQUEST_URI'])); ?>" class="<?php if($order == 'ASC') echo 'active'; ?>"><?php _e('Cheaper', 'prokkat'); ?></a>
                  <a href="<?php echo add_query_arg( array( 'order'=>'DESC' ), basename($_SERVER['REQUEST_URI'])); ?>" class="<?php if($order == 'DESC') echo 'active'; ?>"><?php _e('Expensive', 'prokkat'); ?></a>
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
		          /*get town*/
              $coordinates = get_post_meta($post_id , 'cc_locations',true);
	            $coordinates = explode("},{" , $coordinates)[0];
              $coordinates = preg_replace ("/[^0-9\s\,\.]/","", $coordinates);
              $get_address = trim(get_address($coordinates));

              $curr_terms = wp_get_post_terms($post_id, CUSTOM_CAT_TYPE);

              foreach( $curr_terms as $term ) {
                if($term->parent == 0) {
                  $terms[$term->term_id] = array('term_id' => $term->term_id ,'name' => $term->name, 'children' => array());
                } else {
                  $children[$term->parent][$term->term_id] = array('term_id' => $term->term_id ,'name' => $term->name);
                }
              } 
            
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
		        <div class="town"><?php echo $get_address;?></div>
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
                        $city = get_the_author_meta('city_name', $author_id);
                        $city = explode("," , $city);
                    ?>
                    <input type="hidden" name="city" value="<?php echo $city[0]; ?>">
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
                    $city = get_the_author_meta('city_name', $author_id);
                    $city = explode("," , $city); 
                ?>
                <input type="hidden" name="image" value="<?php echo $ava; ?>">
                <input type="hidden" name="city" value="<?php echo $city[0]; ?>">
                <input type="hidden" name="author_name" value="<?php echo $author->display_name; ?>" >
                <input type="hidden" name="phone" value="<?php echo get_the_author_meta('phone'); ?>">
              </a>
          </div>
          </div>
	    <?php
          endwhile;
	    ?>
                    </div>
		   <?php if(count($arr) > 9 && $all_query->post_count > 9) { ?>
                    <div class="paginator">
                      <?php
                        echo paginate_links( array(
                          'mid_size'  => 2,
                          'prev_text' => '<i class="fas fa-angle-left"></i>',
                          'next_text' => '<i class="fas fa-angle-right"></i>',
                        ) );
                        wp_reset_postdata();
                      ?>
                    </div>
		   <?php } ?>
                  </div>
      <?php else : ?>

         <div class="container notfound__container">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/tractor.svg" class="notfound__icon" />
                <div class="notfound__top-text"><?php _e('Nothing found...', 'prokkat'); ?></div>
                <div class="notfound__bot-text"><?php _e('Sorry, at RentHUB there is no ads for your request!', 'prokkat'); ?></div>
                <a href="<?php echo site_url(); ?>" class="notfound__button"><?php _e('Go to main page', 'prokkat'); ?></a>
            </div>

        <?php 
          endif; 
          $wp_query = NULL;
          $wp_query = $temp_query;
        ?>

        </div>
                <div class="search-sidebar">
              <form method="get" class="form-submit">
              <div class="seach-sidebar__block">
                <div class="seach-sidebar__block-title"><?php _e('Categories', 'prokkat'); ?></div>
                  <div id="search-cats">
                
                <?php
                foreach ($children as $parent => $term) {
                  $terms[$parent]['children'] = $term;
                }
                 
                foreach($terms as $t) {
                  echo '<div class="maincat-container">';
                  $pll_term_id = pll_get_term($t['term_id']);

                  if($pll_term_id == false || $pll_term_id == $t['term_id']) {
                      echo '<label class="search-sidebar__cat search-sidebar__maincat"><input type="checkbox" value="'.$t['term_id'].'">'.$t['name'].'</label>';
                  } else {
                      $term = get_term($pll_term_id, CUSTOM_CAT_TYPE);
                      $name = $term->name;
                      echo '<label class="search-sidebar__cat search-sidebar__maincat"><input type="checkbox" value="'.$pll_term_id.'">'.$name.'</label>';
                  }
                  $children = $t['children'];
                  foreach ($children as $child) {
                    $pll_term_id = pll_get_term($child['term_id']);
                    if($pll_term_id == false || $pll_term_id == $child['term_id']) {
                        echo '<label class="search-sidebar__subcat search-sidebar__cat"><input type="checkbox" value="'.$child['term_id'].'">'.$child['name'].'</label>';
                    } else {
                        $term = get_term($pll_term_id, CUSTOM_CAT_TYPE);
                        $name = $term->name;
                        echo '<label class="search-sidebar__subcat search-sidebar__cat"><input type="checkbox" value="'.$pll_term_id.'">'.$name.'</label>';
                    }
                  }
                  echo '</div>';
                }
          ?>
                <input type="hidden" id="search-cat" name="cats" value="<?php echo $cats_str; ?>" >
                </div>
              </div>

              <div class="seach-sidebar__block">
                <div class="seach-sidebar__block-title"><?php _e('Rent price', 'prokkat'); ?></div>
                <div class="search-sidebar__price-container">
                  <div class="search-sidebar__price">
                    <div class="search-sidebar__price-text"><?php _e('From', 'prokkat'); ?>, грн</div>
                    <input type="text" name="price_from" id="price_from" placeholder="<?php echo get_price_placeholders($the_query->posts)[0]; ?>" value="<?php echo $price_from; ?>">
                  </div>
                  <div class="search-sidebar__price nomargin">
                    <div class="search-sidebar__price-text"><?php _e('To', 'prokkat'); ?>, грн</div>
                    <input type="text" name="price_to" id="price_to" placeholder="<?php echo get_price_placeholders($the_query->posts)[1]; ?>" value="<?php echo $price_to; ?>">
                  </div>
                </div>
              </div>

              <div class="seach-sidebar__block">
                <input type="hidden" name="search_for" value="<?php echo $s_for; ?>" >
                <input type="submit" class="save-btn full-width" value="<?php _e('Filter', 'prokkat'); ?>">
              </div>
              </form>
            </div>
        </div>

        <!-- <div class="paging"><span style="float:right;"><?php echo $paginationDisplay; ?></span></div> -->
</div>
	
<?php 
  include_once('phone_popup.php');
  include_once('sweet_leads.php');
  get_footer(); 
?>
