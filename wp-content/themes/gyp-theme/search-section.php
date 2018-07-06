<?php
  function search_header_cate($parent, $selected_city_name = '', $selected_city_id = '') { ?>
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
                                <div><?php echo get_term($parent)->name; ?></div>
                            </div>
                            <img class="header-category-img" src="<?php echo get_stylesheet_directory_uri(); ?>/img/arrow-down-sign-to-navigate.svg">
                        </div>  
                        <div class="header__category__window header__category__window-small hide">
                                  <ul class="menu-cat">
                                    <?php
                                      $cat_args = array(
                                      'parent' => $parent,
                                      'orderby' => 'name',
                                      'hierarchical' => 1,
                                      'taxonomy' => CUSTOM_CAT_TYPE,
                                      'hide_empty' => 0,
                                      'style' => 'cat_list',
                                      'title_li' => '',
                                      'walker' => new Custom_Walker_Category()
                                      );
                                      
                                      wp_list_categories($cat_args);
                                    ?>
                                  </ul>
                            <div class="return-to-main-cat">
                                <a href="<?php echo get_site_url(); ?>" ><i class="fa fa-arrow-left"></i> <?php _e('Back to main', 'prokkat') ?></a>
                            </div>
                        </div>
                      </div>
                      <div class="input-wrp input-wrp_grid">
                          <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/flag.svg" class="input-wrp__ico search-icon-input">
                          <input value="<?php echo $selected_city_name; ?>" type="text" id="s_addresss" class="input input_srch-header input_srch-header-btn" placeholder="<?php echo __('All Ukraine', 'prokkat'); ?>" name="address" value="" readonly />
                          <input value="<?php echo $selected_city_id; ?>" type="hidden" id="s_city_id" class="input input_add" name="search_loc" value="" />
                          <img class="header-category-img input-arrow-down" src="<?php echo get_stylesheet_directory_uri(); ?>/img/arrow-down-sign-to-navigate.svg">
                          <div class="header-category-cities hide">
                            <div class="header-category-city" data-id="ChIJBUVa4U7P1EAR_kYBF9IxSXY">Киев</div>
                            <div class="header-category-city" data-id="ChIJiw-rY5-gJ0ERCr6kGmgYTC0">Харьков</div>
                            <div class="header-category-city" data-id="ChIJQ0yGC4oxxkARbBfyjOKPnxI">Одесса</div>
                            <div class="header-category-city" data-id="ChIJLZqRAJWQ4EARhG-Fxf1eMzY">Донецк</div>
                            <div class="header-category-city" data-id="ChIJA7uF-j1n3EARSj9NB9lcZ34">Запорожье</div>
                            <div class="header-category-city" data-id="ChIJV5oQCXzdOkcR4ngjARfFI0I">Львов</div>
                            <div class="header-category-city" data-id="ChIJe6tUMeDf2kARbhhrfRc6-rA">Кривой рог</div>
                            <div class="header-category-city" data-id="ChIJ1RNy-4nLxUARgFbgufo5Dpc">Николаев</div>
                            <div class="header-category-city" data-id="ChIJK1jnvqfm5kARzrV1CjAY0aU">Мариуполь</div>
                          </div>
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
<?php } ?>

<?php function search_header_main($selected_city_name = '', $selected_city_id = '') { ?>
  <section class="header-section">
    <div class="container">
        <div class="search-grp search-grp_header">
                <div class="header__search-show-btn hide">
                  <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/magnifying-glass-2.svg">
                  <?php _e('Search', 'prokkat'); ?>
                </div>
                <form action="<?php echo site_url('search-ru/'); ?>" method="get" class="form_srch-header">
                  <div class="header__popup__container">
                        <div class="input-wrp input-wrp_grid header__category__container" id="category-opener" style="padding: 0 15px">
                            <div class="header__category__text-container">
                                <img class="header__category__text-container-img" src="<?php echo get_stylesheet_directory_uri(); ?>/img/bookmark.svg">
                                <div><?php _e('Categories', 'prokkat') ?></div>
                            </div>

                            <img class="header-category-img" src="<?php echo get_stylesheet_directory_uri(); ?>/img/arrow-down-sign-to-navigate.svg">    
                        </div>

                        <div class="header__category__window header__category__window-small header__category__paddings hide">
                        <?php
                            $categories = get_categories(array(
                                'taxonomy' => CUSTOM_CAT_TYPE,
                                'orderby' => 'ID',
                                'hide_empty' => false,
                                'parent'=>0,
                            ));
                            foreach ( $categories as $category ) {
                                $termId = $category->term_id;
                                $taxonomyName = CUSTOM_CAT_TYPE;
                                echo '
                                    <div class="header_switcher">
                                         <div class="header__category__btn">
                                            <button class="header__category__btn" type="button" onclick="window.location=\''.get_term_link( $termId, $taxonomyName ).' \'">
                                                <div class="header__category__text-container">
                                                    <img style="padding: 0 15px" class="header__category__text-container-img" src="'. get_wp_term_image($termId) .'">
                                                    <b>'.esc_html($category->name).'</b>
                                                </div> 
                                                <i id="category-btn-arrow" class="fa fa-angle-right"></i>
                                            </button>
                                        </div>
                                        <div class="back__white"></div>
                                        <div class="header__category__right hide">
                                            <div class="header__category__shadow">
                                            <div class="header__category__topbar">
						                                <img class="close-categories" src="'.get_stylesheet_directory_uri().'/img/cross-out.svg" >
                                                <a href="'.get_term_link( $termId, $taxonomyName ).'">Все объявления в <b>'.esc_html($category->name).'</b></a>
                                            </div>
                                            <div class="header__subcategory">
                                                <div class="header__category-list">
                                                    <div class="header__list">';
                                                    $term_children = get_term_children( $termId, $taxonomyName );
                                                    foreach ( $term_children as $child ) {
                                                        $term = get_term_by( 'id', $child, $taxonomyName );
                                                        echo '
                                                            <a class="header__category-list__item" href="'.get_term_link( $child, $taxonomyName ).'">'.$term->name.'</a>';
                                                    } ?>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>                                
                            </div>
                            <?php } ?>                            
                        </div>
                    </div>

                    <div class="input-wrp input-wrp_grid">
                      <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/flag.svg" class="input-wrp__ico search-icon-input">
                      <input value="<?php echo $selected_city_name; ?>" type="text" id="s_addresss" class="input input_srch-header input_srch-header-btn" placeholder="<?php echo __('All Ukraine', 'prokkat'); ?>" name="address" value="" readonly />
                      <input value="<?php echo $selected_city_id; ?>" type="hidden" id="s_city_id" class="input input_add" name="search_loc" value="" />
                      <img class="header-category-img input-arrow-down" src="<?php echo get_stylesheet_directory_uri(); ?>/img/arrow-down-sign-to-navigate.svg">
                      <div class="header-category-cities hide">
                        <div class="header-category-city" data-id="ChIJBUVa4U7P1EAR_kYBF9IxSXY">Киев</div>
                        <div class="header-category-city" data-id="ChIJiw-rY5-gJ0ERCr6kGmgYTC0">Харьков</div>
                        <div class="header-category-city" data-id="ChIJQ0yGC4oxxkARbBfyjOKPnxI">Одесса</div>
                        <div class="header-category-city" data-id="ChIJLZqRAJWQ4EARhG-Fxf1eMzY">Донецк</div>
                        <div class="header-category-city" data-id="ChIJA7uF-j1n3EARSj9NB9lcZ34">Запорожье</div>
                        <div class="header-category-city" data-id="ChIJV5oQCXzdOkcR4ngjARfFI0I">Львов</div>
                        <div class="header-category-city" data-id="ChIJe6tUMeDf2kARbhhrfRc6-rA">Кривой рог</div>
                        <div class="header-category-city" data-id="ChIJ1RNy-4nLxUARgFbgufo5Dpc">Николаев</div>
                        <div class="header-category-city" data-id="ChIJK1jnvqfm5kARzrV1CjAY0aU">Мариуполь</div>
                      </div>
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
<?php } ?>
