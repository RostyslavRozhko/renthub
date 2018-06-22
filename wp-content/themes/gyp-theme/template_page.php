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
            'taxonomy' => 'cate',
            'orderby' => 'ID',
            'hide_empty' => false,
            'parent'=>0,
        ));
        foreach ( $categories as $category ) {
            $termId = $category->term_id;
            $taxonomyName = 'cate';
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
                        $term_children = array_slice($term_children, 0, 5);
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
    <div class="container">
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
                        'showposts' => 10,
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
				    ?>
                                <div class = "carousel__item">
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
                RentHUB - це відкрита платфомна яка спеціалізується на оренді будівельної техніки. Кожна людина може розмісти оголошення та здати в оренду техніку, якою Вона володіє. Кінцевий користувач навпаки знайде те, що йому необіхдно для ремонту чи будівництва.
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
            <div class="info-section__text">
                RentHUB - це відкрита платфомна яка спеціалізується на оренді будівельної техніки. Кожна людина може розмісти оголошення та здати в оренду техніку, якою Вона володіє. Кінцевий користувач навпаки знайде те, що йому необіхдно для ремонту чи будівництва.
            </div>
            <div class="info-section__link-container">
                <a href="<?php echo site_url('new/'); ?>" class="login-top login__item login__item__yellow <?php if (!is_user_logged_in()) echo 'btnModal' ?>">
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
                                'taxonomy' => 'cate',
                                'hide_empty' => false,
                                'parent'=>0,
                            ));
                            foreach ( $categories as $category ) {
                                $termId = $category->term_id;
                                $taxonomyName = 'cate';
                                echo '
                                        <div class="info-section__cat">
                                            <a class="info-section__cat-title" href="'.esc_url( get_category_link( $termId ) ).'">'.esc_html($category->name).'</a>
                                            <div class="info-section__subcat">
                                    ';
                                $term_children = get_term_children( $termId, $taxonomyName );
                                foreach ( $term_children as $child ) {
                                    $term = get_term_by( 'id', $child, $taxonomyName );
                                    echo '
                                            <a class="info-section__subcat-title" href="'.get_term_link( $child, $taxonomyName ).'">'.$term->name.'</a><span>, </span>
                                        ';
                                }
                                echo '</div>
                                    </div>';
                            }
                ?>
            </div>
        </div>
        <div class="container">
        <div class="info-section__title bottom-text"><?php _e('Rent', 'prokkat') ?></div>
            <div class="footer-text">
                <p>
                Рады приветствовать вас на главном портале аренды техники и вещей в Украине. Наш портал предлагает вам размещать объявления о прокате и аренде различной техники в любой точке Украины. ГУП - это аренда всего, от коляски, авто, бура на перфоратор до комбайна.
                </p>
                <p>
                "Главный Украинский Прокат" помогает своим клиентам экономить, поскольку аренда спецтехники в Украине будет всегда оправданной. Содержание собственного инструмента далеко не всем по карману, а необходимость в строительной технике растет постоянно.
Большинство компаний, предлагающих услуги по аренде строительной техники в Украине берут непомерно большую цену, предоставляя при этом не проверенное, старое оборудование, у нас на портале вы сможете самостоятельно выбрать инструмент в качестве и количестве которое вам необходимо.
                </p>
                <p>
                Как мы работаем?
                </p>
                <p>
                Если вы компания которая сдает инструмент в аренду, то вам необходимо зарегистрироваться, при этом указав цену, состояние и местоположение инструмента (спецтехники), который вы сдаете в аренду. После этого ждете звонков или заявок с сайта на прокат строительной техники по территории Украины.
                </p>
                <p>
                Если же вам нужен инструмент на прокат, тогда вам нужно всего лишь указать в поиске либо выбрать в различных категориях нужную спецтехнику и выбрав нужное позвонить владельцу объявления либо заказать аренду инструмента онлайн.
                </p>
                <p>
                Для удобства мы создали для вас множество категорий инструмента и спецтехники, с помощью которых вы сможете выбрать именно тот инструмент, который вам нужен, а именно:
Аренда Виброплиты
Аренда Вибротрамбовки
Аренда дорожного катка
Аренда клинингового оборудования
                </p>
            </div>
        </div>
    </section>

<section class="cities-section">
    <div class="container">
        <div class="cities-section-title"><?php _e('Ukraine cities', 'prokkat'); ?></div>
        <?php
            wp_nav_menu( array( 
                'theme_location' => 'cities_menu',
                'menu_class' => 'cities_menu'
            ) ); 
        ?>
    </div>
</section>

<!--End Cotent Wrapper-->
<?php get_footer(); ?>
