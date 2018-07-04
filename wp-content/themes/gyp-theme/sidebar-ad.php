<!--Start Sidebar-->
    <div class="menu-cat_grid">
          <div class="cat-mobile">
          <a href="#0" class="btn btn_mobile btn_mobile-cat" id="catBtn"><?php _e('Categories', 'prokkat'); ?></a>
          <div class="menu-cat__title"><?php _e('City', 'prokkat'); ?></div>
            <div class="location location_cat">
                <div class="input-wrp input-wrp_location">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/flag-black.svg" class="search-icon">
                    <input type="text" id="cat_address" placeholder="<?php _e('All Ukraine', 'prokkat'); ?>" class="input input_location" value="<?php if( isset($_GET['address']) ) echo $_GET['address']; ?>">
					<a id="cat_address_link" href="" class="link link_blue link_abs fright"><?php _e('Change', 'prokkat'); ?></a>
                </div>
            </div>
            <div class="menu-cat__title"><?php _e('Categories', 'prokkat'); ?></div>
		    <ul class="menu-cat">
            <?php
		      $cat_args = array(
			    'orderby' => 'name',
			    'show_count' => 0,
			    'hierarchical' => 1,
			    'taxonomy' => CUSTOM_CAT_TYPE,
			    'hide_empty' => 0,
				'style' => 'list',
			    'title_li' => '',
				'walker' => new Custom_Walker_Category(),
		      );
              wp_list_categories($cat_args);
            ?>
            </ul>
        </div>
    </div>
<!--End Sidebar-->