<?php

  function ajax_auth_init()
  {
    wp_register_script('ajax-auth-script', get_stylesheet_directory_uri() . '/js/ajax-auth-script.js', array('jquery'), '1.1', true ); 
    wp_enqueue_script('ajax-auth-script');
	
	wp_localize_script( 'ajax-auth-script', 'ajax_auth_object', array( 
        'ajaxurl' => admin_url( 'admin-ajax.php?lang='.pll_current_language() ),
        'redirecturl_login' => home_url('dashboard/'),
        'redirecturl_reg' => home_url('register/'),
        'loadingmessage' => __('Sending user info, please wait...', 'prokkat'),
        'equalTo' => __('Please enter the same value again.', 'prokkat')
	));
	
    // Enable the user with no privileges to run ajax_login() in AJAX
    add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );
	// Enable the user with no privileges to run ajax_register() in AJAX
	add_action( 'wp_ajax_nopriv_ajaxregister', 'ajax_register' );
	// Enable the user with no privileges to run ajax_forgotPassword() in AJAX
    add_action( 'wp_ajax_nopriv_ajaxforgotpassword', 'ajax_forgotPassword' );
  }

// Execute the action only if the user isn't logged in
if (!is_user_logged_in()) {
    add_action('init', 'ajax_auth_init');
    add_action('pll_language_defined', 'ajax_auth_init');
}
  
function ajax_login(){

    // First check the nonce, if it fails the function will break
    check_ajax_referer( 'ajax-login-nonce', 'security' );

    // Nonce is checked, get the POST data and sign user on
  	// Call auth_user_login
	auth_user_login($_POST['email'], $_POST['password'], 'Login'); 
	
    die();
}

function ajax_register(){

    // First check the nonce, if it fails the function will break
    check_ajax_referer( 'ajax-register-nonce', 'security' );
		
    // Nonce is checked, get the POST data and sign user on
    $info = array();
  	$info['user_nicename'] = $info['user_login'] = sanitize_user( $_POST['username'] );
	$info['display_name'] = $info['nickname'] = $info['first_name'] = sanitize_user( $_POST['nick'] );
    $info['user_pass'] = sanitize_text_field($_POST['password']);
	$info['user_email'] = sanitize_email( $_POST['email']);

	$city_id = sanitize_text_field($_POST['cityId']);
	$city_name = sanitize_text_field($_POST['cityName']);
	
	// Register the user
    $user_register = wp_insert_user( $info );
 	if ( is_wp_error($user_register) ){
		$error  = $user_register->get_error_codes()	;
		
		if(in_array('empty_user_login', $error))
			echo json_encode(array('loggedin'=>false, 'message'=>__($user_register->get_error_message('empty_user_login'), 'prokkat')));
		elseif(in_array('existing_user_login',$error))
			echo json_encode(array('loggedin'=>false, 'message'=>__('This username is already registered.', 'prokkat')));
		elseif(in_array('existing_user_email',$error))
        echo json_encode(array('loggedin'=>false, 'message'=>__('This email address is already registered.', 'prokkat')));
    } else {
	  $user_data = wp_update_user(array('ID' => $user_register, 'role' => 'contributor'));
	  update_user_meta($user_register, 'city_id', $city_id);
	  update_user_meta($user_register, 'city_name', $city_name);

      if( !is_wp_error( $user_data ) ) {
	    custom_new_user_notification( $user_register );
	    echo json_encode(array('loggedin'=>true, 'message'=>__('Registration successful, redirecting...', 'prokkat')));
	    die();
	  }
    }

    die();
}

function auth_user_login($user_login, $password, $login)
{
	$user_id = '';
	if( $user_id = email_exists( $user_login )) {
	  $activated = get_user_meta( $user_id, 'user_activated' , true );
      if( !$activated ) {
		echo json_encode( array('loggedin'=>false, 'message'=>__('Your account is not activated.', 'prokkat')) );
		die();
	  }
	}
    $info = array();
    $info['user_login'] = $user_login;
    $info['user_password'] = $password;
    $info['remember'] = true;
	
	$user_signon = wp_signon( $info, false );
    if ( is_wp_error($user_signon) ){
	  echo json_encode(array('loggedin'=>false, 'message'=>__('Wrong email or password.', 'prokkat')));
    } else {
	  wp_set_current_user($user_signon->ID); 
      echo json_encode(array('loggedin'=>true, 'message'=>__($login.' successful, redirecting...', 'prokkat')));
    }
	die();
}

function ajax_forgotPassword(){
	 
	// First check the nonce, if it fails the function will break
    check_ajax_referer( 'ajax-forgot-nonce', 'security' );
	
	global $wpdb;
	
	$account = $_POST['user_login'];
	
	if( empty( $account ) ) {
		$error = 'Enter e-mail address.';
	} else {
		if(is_email( $account )) {
			if( !email_exists($account) )
				$error = 'There is no user registered with that email address.';
		}
		else
			$error = 'Invalid e-mail address.';
	}
	
	if(empty ($error)) {
		// lets generate our new password
		//$random_password = wp_generate_password( 12, false );
		$random_password = wp_generate_password();

			
		// Get user data by field and data, fields are id, slug, email and login
		$user = get_user_by( 'email', $account );
			
		$update_user = wp_update_user( array ( 'ID' => $user->ID, 'user_pass' => $random_password ) );
			
		// if  update user return true then lets send user an email containing the new password
		if( $update_user ) {
			
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			$admin_email = get_option('admin_email');
			$message = __( 'Your new password is: ', 'prokkat' ).$random_password;
			$headers = array(
	          'Content-Type: text/html; charset=UTF-8',
	          'From: ' . $blogname . ' <' . $admin_email . '>' . "\r\n",
	        );
				
			$mail = wp_mail( $user->user_email, __( 'Your new password', 'prokkat' ), $message, $headers );
			if( $mail )
				$success = __('Check your email address for you new password.', 'prokkat');
			else
				$error = __('System is unable to send you mail containg your new password.', 'prokkat');
		} else {
			$error = __('Oops! Something went wrong while updaing your account.', 'prokkat');
		}
	}
	
	if( ! empty( $error ) )
		echo json_encode(array('loggedin'=>false, 'message'=>__($error,'prokkat')));
			
	if( ! empty( $success ) )
		echo json_encode(array('loggedin'=>false, 'message'=>__($success,'prokkat')));
				
	die();
}