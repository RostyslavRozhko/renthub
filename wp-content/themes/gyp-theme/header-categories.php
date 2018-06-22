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
                                'taxonomy' => 'cate',
                                'orderby' => 'ID',
                                'hide_empty' => false,
                                'parent'=>0,
                            ));
                            foreach ( $categories as $category ) {
                                $termId = $category->term_id;
                                $taxonomyName = 'cate';
                                // <img class="close-categories" src="'.get_stylesheet_directory_uri().'/img/cross-out.svg" >
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
                                        <div class="header__category__right hide">
                                            <div class="header__category__topbar">
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
                            <?php } ?>                            
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