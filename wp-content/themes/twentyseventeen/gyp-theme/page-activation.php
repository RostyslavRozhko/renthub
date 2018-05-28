<?php
/**
 * Template Name: Template Activation
 *
 */
  get_header();
  
  require_once( ABSPATH . WPINC . '/class-phpass.php');
  $wp_hasher = new PasswordHash(8, true);
  
  $key = $wpdb->get_row( $wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $_GET['login']) );
  $key = explode( ':', $key->user_activation_key, 2 );
  $key = $key[1];
  
  $check = $wp_hasher->CheckPassword($_GET['key'], $key);
  
?>
    <div class="container">
	  <div class="thanks">
      <?php
	    if( $check ) :
		  $user = get_user_by( 'login', $_GET['login'] );
	      $res = update_user_meta( $user->ID, 'user_activated', 1 );
		    if( $res ) :
			  wp_set_auth_cookie( $user->ID, true ); 
 		      header("Location: ".add_query_arg( array( 'action'=>'profile', 'activated'=>'yes' ), site_url( 'dashboard/' )));
              exit;
			else : ?>
	    <h1><?php echo __( 'Your account is already activated.', 'prokkat' ); ?></h1>
	  <?php endif; else : ?>
        <h1><?php echo __( 'Error', 'prokkat' ); ?></h1>
	  <?php endif; ?>
	  </div>
    </div>

<?php get_footer(); ?>
