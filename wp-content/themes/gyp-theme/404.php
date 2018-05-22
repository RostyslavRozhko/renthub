<?php get_header(); ?>

<div class="container">
  <div class="not-found-flex">
    <img class="not-found__icon" src="<?php echo get_stylesheet_directory_uri(); ?>/img/tractor.svg" />
    <div class="not-found__text-top"><?php _e('Nothing found...', 'prokkat') ?></div>
    <div class="not-found__text-bot"><?php _e('Sorry, at RentHUB there is no ads for your request!', 'prokkat') ?></div>
    <a href="<?php echo site_url(); ?>" class="not-found__link"><?php _e('Go to main page', 'prokkat') ?></a>
  </div>
</div>

<?php get_footer(); ?>