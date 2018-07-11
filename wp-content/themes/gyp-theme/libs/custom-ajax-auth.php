
<?php
  function ajax_auth_init()
  {
    wp_register_script('ajax-auth-script', get_stylesheet_directory_uri() . '/js/ajax-auth-script.js', array('jquery'), '1.1', true ); 
    wp_enqueue_script('ajax-auth-script');
	
	wp_localize_script( 'ajax-auth-script', 'ajax_auth_object', array( 
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'redirecturl_login' => site_url('dashboard/'),
        'redirecturl_reg' => site_url(),
        'loadingmessage' => __('Sending user info, please wait...', 'prokkat'),
        'equalTo' => __('Please enter the same value again.', 'prokkat')
	));
	
    // Enable the user with no privileges to run ajax_login() in AJAX
    add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );

    add_action( 'wp_ajax_nopriv_ajaxloginphone', 'ajax_login_phone' );
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

function ajax_login_phone() {
        //check_ajax_referer( 'ajax-login-phone-nonce', 'securityphone' );
	$tel_formated = sanitize_text_field($_POST['tel']);
	$tel = preg_replace('/[()+ -]/', '', $tel_formated);

    $old_users_arr = get_users(array('meta_key' => 'phone', 'meta_value' => $tel_formated));
    $user = get_user_by('login', $tel );
    if($old_users_arr) {
        $user = end($old_users_arr);
        auth_login($user->user_login);
    } else if ($user) {
        auth_login($user->user_login);
    }

    echo json_encode(array('loggedin' => false, 'message' => 'Пользователь не найден.'));
    die();
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
  	$info['user_nicename'] = $info['user_login'] = generate_unique_username(sanitize_user( $_POST['username'] ));
	$info['display_name'] = $info['nickname'] = $info['first_name'] = sanitize_user( $_POST['nick'] );
    $info['user_pass'] = sanitize_text_field($_POST['password']);
	$info['user_email'] = sanitize_email( $_POST['email']);
	$city_id = sanitize_text_field($_POST['cityId']);
  $city_name = sanitize_text_field($_POST['cityName']);
  $phone = sanitize_text_field($_POST['phone']);


	$old_users_arr = get_users(array('meta_key' => 'phone', 'meta_value' => $phone));
	$tel = preg_replace('/[()+ -]/', '', $phone);
	$user = get_user_by('login', $tel );

  	if ($old_users_arr || $user) {
		echo json_encode(array('loggedin' => false, 'phone_exist' => true, 'message' => 'Этот номер телефона уже зарегистрирован.'));
		die();
	}

	preg_match_all('/\d+/', $phone, $result_array);
	$phone_operators = ["039", "050", "063", "066", "067", "068", "073" ,"091", "092", "093", "094", "095", "096", "097", "098", "099"];
	if (!array_search($result_array[0][1], $phone_operators)) {
		echo json_encode(array('loggedin' => false, 'phone_error' => true, 'message' => 'Неверный код оператора. Пожалуйста, введите номер телефона.'));
		die();
	}
	
	// Register the user
    $user_register = wp_insert_user( $info );
 	if ( is_wp_error($user_register) ){
		$error  = $user_register->get_error_codes()	;
		
		if(in_array('empty_user_login', $error))
			echo json_encode(array('loggedin'=>false, 'message'=>__($user_register->get_error_message('empty_user_login'), 'prokkat')));
		elseif(in_array('existing_user_login',$error))
			echo json_encode(array('loggedin'=>false, 'login_exist' => true, 'message'=>__('This username is already registered.', 'prokkat')));
		elseif(in_array('existing_user_email',$error))
			echo json_encode(array('loggedin'=>false, 'email_exist' => true, 'message'=>__('This email address is already registered.', 'prokkat')));
    } else {
	  $user_data = wp_update_user(array('ID' => $user_register, 'role' => 'contributor'));
	  update_user_meta($user_register, 'city_id', $city_id);
    update_user_meta($user_register, 'city_name', $city_name);
    update_user_meta($user_register, 'phone', $phone);
      if( !is_wp_error( $user_data ) ) {
	    custom_new_user_notification( $user_register );
	    echo json_encode(array('loggedin'=>true, 'new_user' => true, 'user_id' => $user_register, 'message'=>__('Registration successful, redirecting...', 'prokkat')));
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

function auth_login($username, $register = false) {
	
	if ( is_user_logged_in() ) {
		wp_logout();
	}

	add_filter( 'authenticate', 'allow_programmatic_login', 10, 3 );
	$user = wp_signon( array( 'user_login' => $username ) );
	remove_filter( 'authenticate', 'allow_programmatic_login', 10, 3 );

	if ( is_a( $user, 'WP_User' ) ) {
		wp_set_current_user( $user->ID, $user->user_login );

		if ( is_user_logged_in() ) {
			echo json_encode(array('loggedin'=>true));
			die();
		}
	}

	echo json_encode(array('loggedin'=>false));
	die();
  }

function allow_programmatic_login( $user, $username, $password ) {
    return get_user_by( 'login', $username );
 }

function ajax_forgotPassword(){
	 
	// First check the nonce, if it fails the function will break
    // check_ajax_referer( 'ajax-forgot-nonce', 'securityforgot' );
	
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
				$success = 'Check your email address for you new password.';
			else
				$error = 'System is unable to send you mail containg your new password.';
		} else {
			$error = 'Oops! Something went wrong while updaing your account.';
		}
	}
	
	if( ! empty( $error ) )
		echo json_encode(array('loggedin'=>false, 'message'=>__($error,'prokkat')));
			
	if( ! empty( $success ) )
		echo json_encode(array('loggedin'=>false, 'message'=>__($success,'prokkat')));
				
	die();
}

function generate_unique_username( $username ) {

	$username = sanitize_title( $username );

	static $i;
	if ( null === $i ) {
		$i = 1;
	} else {
		$i ++;
	}
	if ( ! username_exists( $username ) ) {
		return $username;
	}
	$new_username = sprintf( '%s-%s', $username, $i );
	if ( ! username_exists( $new_username ) ) {
		return $new_username;
	} else {
		return call_user_func( __FUNCTION__, $username );
	}
}

?>
