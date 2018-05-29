<?php
if (have_posts()) :
    while (have_posts()): the_post();
        global $post;
        ?>
        <!--Start Post-->
        <div class="post blog-post">
            <div class="post__title__container">
                <?php if ( has_post_thumbnail() ) { the_post_thumbnail('medium'); } ?>
                <div class="post__title__text">
                    <div class="post_title">
                        <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a>
                    </div>
                    <div class="post_meta">
                        <span class="author"><a href="#"><?php the_author_posts_link(); ?></a>, </span>
                        <span class="estimate"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) .' '. __( 'ago', 'prokkat' ); ?></span>
                    </div>
                </div>
            </div>
            <div class="post_content">
                <?php the_excerpt(); ?>
            </div>
            <a href="<?php the_permalink() ?>" class="readmore"><?php _e('Read more', 'prokkat'); ?> <i class="fa fa-arrow-right"></i></a>
        </div>
        <!--End Post-->

        <?php
    endwhile;
endif;
?>