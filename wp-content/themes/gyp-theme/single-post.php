<?php

/**

 * Single post page for displaying detailed about

 * the selected post. 

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
                                'taxonomy' => CUSTOM_CAT_TYPE,
                                'orderby' => 'ID',
                                'hide_empty' => false,
                                'parent'=>0,
                            ));
                            foreach ( $categories as $category ) {
                                $termId = $category->term_id;
                                $taxonomyName = CUSTOM_CAT_TYPE;
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
                                                    <img src="'.get_field('big_banner', CUSTOM_CAT_TYPE . '_' . $termId).'" >
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

<div class="container container-paddings">
    <div class="breadcrumbs breadcrumbs__page">
            <a href="<?php echo site_url(); ?>/blog">Блог</a>
    </div>
    <?php
    if (have_posts()) :
        while (have_posts()): the_post();
    ?>
    
    <h1 class="page_title"><?php the_title(); ?></h1>
    <div class="post_meta">
        <span class="author"><a href="#"><?php the_author_posts_link(); ?></a>, </span>
        <span class="estimate"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) .' '. __( 'ago', 'prokkat' ); ?></span>
    </div>
    <div class="thumbnail">
        <?php if ( has_post_thumbnail() ) { the_post_thumbnail('full'); } ?>
    </div>
    <div class="page-container">
        <div class="content single_blog_content">
            <?php the_content(); ?>
            <div class="soc-wrap">
				<?php echo do_shortcode('[supsystic-social-sharing id="1"]'); ?>
            </div>
            <div class="pagination" style="border-top: 1px solid #ebebeb; padding: 30px">
                <div class="previous_post_link">
		            <?php previous_post_link('%link', '<i class="fa fa-arrow-left"></i>  ' . __('Back', 'prokkat', 0)); ?>
                </div>
                <div class="next_post_link">
		            <?php next_post_link('%link', __('Next', 'prokkat') . '  <i class="fa fa-arrow-right"></i>'); ?>
                </div>
            </div>
        </div>
        <div class="sidebar">
            <ul class="sidebar-list">
                <?php 
                    $args = array(
                        'post_type' => 'post',
                        'orderby'   => 'rand',
                        'posts_per_page' => 12, 
                        );
                    
                    $the_query = new WP_Query( $args );
                    
                    if ( $the_query->have_posts() ) {
                        while ( $the_query->have_posts() ) {
                            $the_query->the_post();
                            echo '<li><a href="'. get_permalink() .'">'. get_the_title() .'</a></li>';
                        }
                        wp_reset_postdata();
                    }
                ?>
            </ul>
        </div>
    </div>

<?php
endwhile;
endif;
?>

</div>

<!--End Cotent Wrapper-->

<?php get_footer(); ?>