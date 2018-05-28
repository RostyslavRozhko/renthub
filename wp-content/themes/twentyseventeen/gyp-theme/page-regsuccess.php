<?php
/**
 * Template Name: Template Registration Success
 *
 */
  get_header();
?>
    <div class="container register-success__container">
      <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/mail.svg" class="register-success__icon" />
      <div class="register-success__top-text"><?php _e('Account activation', 'prokkat') ?></div>
      <div class="register-success__bot-text"><?php _e('Check your email to continue activation!', 'prokkat'); ?></div>
      <a href="" class="register-success__button"><?php _e('Resend message', 'prokkat'); ?></a>
    </div>

<?php get_footer(); ?>
