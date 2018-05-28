<?php
/**
 * Template Name: Template Lost Password
 *
 */
  get_header();
?>
    <div class="container">
    <div class="full">
       <div class="email-res">
       <h3 class="bold">Для відновлення паролю вкажіть свій емейл</h1>
      <form id="forgot_password" class="ajax-auth" action="forgot_password" method="post">
        <p class="status"></p>
        <?php wp_nonce_field('ajax-forgot-nonce', 'forgotsecurity'); ?>
        <div class="input-wrp">
                  <input id="user_login" type="text" name="user_login" class="input input_add">
        </div>
        <div class="input-wrp input-wrp_block">
                  <input class="btn btn_lblue" type="submit" value="<?php echo __( 'Send', 'cc' ); ?>" >

        </div>  
      </form>
    </div>
    </div>
   
    </div>

<?php get_footer(); ?>
