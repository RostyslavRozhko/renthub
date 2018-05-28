<?php
/**
 * Template Name: Template Blog
 */
get_header();
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
                                                <a href="'.get_term_link( $termId, $taxonomyName ).'">Все объявления в <b> '.esc_html($category->name).'</b><img src="'. get_stylesheet_directory_uri().'/img/arrow-right.svg"></a>
                                                
                                            </div>
                                            <div class="header__subcategory">
                                                <div class="header__category-list">
                                                    <img src="'.get_field('big_banner', 'cate_' . $termId).'" >
                                                    <div class="header__list">';
                                                    $term_children = get_term_children( $termId, $taxonomyName );
                                                    $term_children = array_slice($term_children, 0, 5);
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
<!--Start Cotent Wrapper-->
<div>
    <div class="container container-paddings">
        <h1 class="page_title page_title_blog"><?php _e('News', 'prokkat'); ?> GYP.COM.UA</h1>
        <div class="page-container blog-page">
                <!--Start Cotent-->
                <div class="content blog-content">
                    <?php
                    $limit = get_option('posts_per_page');
                    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
					query_posts(array(
                     'post_type' => 'post',
                     'posts_per_page' => $limit,
                     'paged' => $paged
                   ));
                    $wp_query->is_archive = true;
                    $wp_query->is_home = false;
                    ?>
                    <?php get_template_part('loop', 'blog'); ?>
                    <div class="pagination">
                        <div class="previous_post_link">
                            <?php next_posts_link(__('Back', 'prokkat'), 0); ?>
                            <!-- <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/arrow-right.svg"> -->
                        </div>
                        <div class="next_post_link">
                            <?php previous_posts_link(__('Next', 'prokkat'), 0); ?>
                            <!-- <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/arrow-right.svg"> -->
                        </div>
                    </div>                    
                <!--End Cotent-->
            </div>
                <div class="sidebar">
                    <ul class="sidebar-list">
                        <?php wp_get_archives( array( 'type' => 'monthly', 'limit' => 12 ) ); ?>
                    </ul>
                </div>
            </div>
        <div class="clear"></div>
    </div>
</div>
<!--End Cotent Wrapper-->
<?php get_footer(); ?>