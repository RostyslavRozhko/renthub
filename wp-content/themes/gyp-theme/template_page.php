<?php 
/**
 * Template Name: Template Index Page
 */
get_header(); 
$city_id = get_field('city_id') ? get_field('city_id') : 'ChIJBUVa4U7P1EAR_kYBF9IxSXY';
$city_name = get_field('city_name') ? get_field('city_name') : 'Киев';
?>
<?php search_header_main($city_name, $city_id); ?>

<section class="maincats__section">
    <div class="container maincats__container">
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
        <div class="maincat__item">
            <div class="maincat__top-container">
                <a href="'.esc_url( get_category_link( $termId ) ).'"><img src="'. get_wp_term_image($termId) .'"></a>
                <div class="maincat__top-cats">
                    <div class="maincat__text">
                        <a class="maincat__text" href="'.esc_url( get_category_link( $termId ) ).'">'.esc_html($category->name).'</a>
                    </div>
                    <div class="maincat__subs-list">';
                        $term_children = get_term_children( $termId, $taxonomyName );
                        $term_children = array_slice($term_children, 0, 4);
                        foreach ( $term_children as $child ) {
                            $term = get_term_by( 'id', $child, $taxonomyName );
                            echo '
                                    <a href="'.get_term_link( $child, $taxonomyName ).'" class="maincat__subs-text">'.$term->name.'</a>
                                ';
                            }

                            echo '
                    </div>
                </div>
            </div>
        </div>';
        } ?>
    </div>
</section>
<section class="cat-section">
    <div class="container no-border">
        <div class="group-product group-product_top group-product_grid">
            <div class="group-product__title-container">
                <h3 class="group-product__title"><?php echo __('New ads', 'prokkat'); ?> <?php echo ' ' . $city_name; ?></h3>
                <div class="group-product__title-container">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/questions-circular-button.svg" class="group-product__title-img">
                    <a class="group-product__title-link" href="#info-section"><?php _e('What is RentHUB?', 'prokkat'); ?></a>
                </div>
            </div>
            <div class="row">
                <div class="content">
                    <div class="gallery text-center">
					<?php
                        query_posts(array(
                        'post_type' => POST_TYPE,
                        'showposts' => 12,
                        'orderby' => 'date',
                        'meta_query' => array(
                            array(
                                'key' => 'cc_add_type',
                                'value' => 'draft',
                                'compare' => '!='
                            ),
                            array(
                                'key' => 'cc_city_id',
                                'value' => $city_id,
                                'compare' => 'LIKE'
                            )
                        )
                        ));
                    ?>
                    <div class="desktop-only">
                    
                    <?php if (have_posts()) :                   
                            while (have_posts()) : the_post();
				    ?>
                        <div class="gallery-item">
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
                    <?php endwhile; endif; ?>
                    </div>

					<div class="wrap-carousel wrap-carousel_other wrap-carousel_advert mobile-only">
                        <div class="carousel" id="authorSlick">
                        <?php if (have_posts()) :                   
                                while (have_posts()) : the_post();
                                $post_id = $post->ID;
                                $coordinates = get_post_meta($post->ID , 'cc_locations',true);
                                $coordinates = explode("},{" , $coordinates)[0];
                                $coordinates = preg_replace ("/[^0-9\s\,\.]/","", $coordinates);
                                $get_address = trim(get_address($coordinates));
                        ?>
                        <div>
                        <div class="search-list__result">
                            <div class="search-list__img">
                            <a href="<?php the_permalink() ?>">
                                <img src="<?php echo ad_thumbnail_url(); ?>">
                            </a>
                            </div>
                            <div class="search-list__title">
                            <a href="<?php the_permalink() ?>"><?php echo mb_strtoupper(pll_title($post_id)); ?></a>
                            <div class="search-list__desc"><?php echo content_excerpt(); ?></div>
                            <div class="search-list__title-city">
                                <input type="hidden" value='<?php echo get_post_meta($post_id, 'cc_city_id', true) ?>' >
                            </div>
                            </div>
                            <div class="town"><?php echo $get_address;?></div>
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
                                <a>
                                    <img class="search-list__phone-image" src="<?php echo get_stylesheet_directory_uri(); ?>/img/call-answer-black.svg">
                                    <div id="tel<?php echo $post_id; ?>" class="shownum search-list_phone-number"></div>
                                </a>
                                <a href="#callFeedback"  class="btn btn_view show_phone search-list__call fancybox-feedback">
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
                        </div>
                    <?php endwhile; endif; ?>
                        </div>
                    </div>

                    <?php wp_reset_query(); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="latest-container">
    <div class="container">
        <div class="group-product group-product_top group-product_grid">
            <h3 class="group-product__title"><?php echo __('Popular categories', 'prokkat'); ?></h3>
            <?php
                wp_nav_menu( array( 
                    'theme_location' => 'popular-cats',
                    'menu_class' => 'popular-cats__menu',
                    'walker' => new Popular_Walker_Nav_Menu($city_name, $city_id),
                    'container' => false
                ) ); 
            ?>
        </div>
    </div>
</section>
<section>  
    <div class="container info-section__container" id="info-section">
        <div class="col6 nopaddingl">
            <div class="info-section__title"><?php _e('What is RentHUB?', 'prokkat') ?></div>
            <div class="info-section__text">
	            <?php _e('RentHUB is', 'prokkat') ?>
            </div>
            <div class="info-section__stats">
                <div class="info-section__stats-col">
                    <div class="info-section__stats-number">416,000+</div>
                    <div class="info-section__stats-title"><?php _e('visitors everyday', 'prokkat') ?></div>
                </div>
                <div class="info-section__stats-col">
                    <div class="info-section__stats-number">12,000+</div>
                    <div class="info-section__stats-title"><?php _e('real ads', 'prokkat') ?></div>
                </div>
                <div class="info-section__stats-col">
                    <div class="info-section__stats-number">850+</div>
                    <div class="info-section__stats-title"><?php _e('trusted partners', 'prokkat') ?></div>
                </div>
            </div>
        </div>
        <div class="col6 nopaddingr">
            <div class="info-section__title"><?php _e('How it is working?', 'prokkat') ?></div>
            <div class="info-section__text"><?php _e('Every man', 'prokkat') ?></div>
            <div class="info-section__link-container">
                <a href="<?php echo site_url('new/'); ?>" class="login__item login__item__yellow <?php if (!is_user_logged_in()) echo 'btnModal' ?>">
                  <i style="line-height: inherit" class="fa fa-plus" aria-hidden="true"></i>
                  <span><?php echo __('Add Ad', 'prokkat'); ?></span>
                </a>
                <a href="" class="info-section__link"><?php _e('Find out more', 'prokkat'); ?></a>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="info-section__cats">
            <?php
                            $categories = get_categories(array(
                                'taxonomy' => CUSTOM_CAT_TYPE,
                                'hide_empty' => false,
                                'parent'=>0,
                            ));
                            foreach ( $categories as $category ) {
                                $termId = $category->term_id;
                                $taxonomyName = CUSTOM_CAT_TYPE;
                                echo '
                                        <div class="info-section__cat">
                                            <a class="info-section__cat-title" href="'.esc_url( get_category_link( $termId ) ).'">'.esc_html($category->name).'</a>
                                            <div class="info-section__subcat">
                                    ';
                                $term_children = get_term_children( $termId, $taxonomyName );
                                foreach ( $term_children as $child ) {
                                    $term = get_term_by( 'id', $child, $taxonomyName );
                                    echo '
                                            <a class="info-section__subcat-title" href="'.get_term_link( $child, $taxonomyName ).'">'.$term->name.'</a>&nbsp;
                                        ';
                                }
                                echo '</div>
                                    </div>';
                            }
                ?>
            </div>
        </div>
        <div class="container no-border">
        <div class="info-section__title bottom-text"><?php _e('Rent', 'prokkat') ?></div>
            <div class="footer-text">
                <p>
                Рады приветствовать вас на главном портале аренды техники и вещей в Украине. Наш портал предлагает вам размещать объявления о прокате и аренде различной техники в любой точке Украины. ГУП - это аренда всего, от коляски, авто, бура, перфоратора до комбайна.
                </p>
                <p>
                "Главный Украинский Прокат" помогает своим клиентам экономить, поскольку аренда спецтехники в Украине будет всегда оправданной. Содержание собственного инструмента далеко не всем по карману, а необходимость в строительной технике растет постоянно.
Большинство компаний, предлагающих услуги по аренде строительной техники в Украине берут непомерно большую цену, предоставляя при этом не проверенное, старое оборудование, у нас на портале вы сможете самостоятельно выбрать инструмент в качестве и количестве которое вам необходимо.
                </p>
                <p>
                <div class="info-section__title bottom-text"><?php _e('How we are working?', 'prokkat') ?></div>
                </p>
                <p>
                Если вы компания которая сдает инструмент в аренду, то вам необходимо зарегистрироваться, при этом указав цену, состояние и местоположение инструмента (спецтехники), который вы сдаете в аренду. После этого ждете звонков или заявок с сайта на прокат строительной техники по территории Украины.
                </p>
                <p>
                Если же вам нужен инструмент на прокат, тогда вам нужно всего лишь указать в поиске либо выбрать в различных категориях нужную спецтехнику и выбрав нужное позвонить владельцу объявления либо заказать аренду инструмента онлайн.
                </p>
                <p>
                Для удобства мы создали для вас множество категорий инструмента и спецтехники, с помощью которых вы сможете выбрать именно тот инструмент, который вам нужен, а именно:
Аренда виброплиты,
Аренда вибротрамбовки,
Аренда дорожного катка,
Аренда клинингового оборудования.
                </p>
            </div>
        </div>
    </section>

<section class="cities-section">
    <div class="container">
        <div class="cities-section-title"><?php _e('Rent', 'prokkat') ?></div>
        <?php
            wp_nav_menu( array( 
                'theme_location' => 'cities_menu',
                'menu_class' => 'cities_menu'
            ) ); 
        ?>
    </div>
</section>

<!--End Cotent Wrapper-->
<?php include_once('phone_popup.php'); ?>   
<?php get_footer(); ?>
