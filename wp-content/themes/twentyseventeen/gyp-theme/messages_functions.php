<?php

  require_once( WP_PLUGIN_DIR  . '/front-end-pm/fep-class.php' );
  $fep = new fep_main_class();

  add_filter( 'fep_formate_date', 'my_formate_date', 5, 2);
  function my_formate_date( $formate, $date ) {
	  $now = current_time('mysql');
	  return human_time_diff(strtotime($date),strtotime($now)).' '.__('ago', 'prokkat');
  }
  

  function new_message_number_ajax() {

	if ( check_ajax_referer( 'fep-message', 'token', false )) {
	  $New_mgs = fep_get_new_message_number();
	  echo $New_mgs;
	}
	wp_die();
	}
	
	add_action('wp_ajax_new_message_number','new_message_number_ajax');
	add_action('wp_ajax_nopriv_new_message_number', 'new_message_number_ajax' );
  
  
  function load_messages() {

    global $fep;
	$start = (int)$_POST['startFrom'];
	$whole = $fep->getWholeThread( $_POST['id'] );
	$wholeThread = my_getWholeThread( $_POST['id'], $start );
	$threadOut = message_markup( $wholeThread );
	echo json_encode( array( 'html'=>$threadOut, 'num'=>count($whole) ));
	exit;
  }
  add_action('wp_ajax_load_messages','load_messages');

  
  function my_new_message_ajax()
  {
	$error_text = $token = $msg = '';
	if( !is_user_logged_in() )
		$error_text = '<div class="error">'. __("Log in to write message.", 'prokkat') .'</div>';
	else {
	  if( isset($_POST['message_to'] )) {
		  $author_id = $_POST['message_to'];
		  $_POST['message_to'] = fep_get_userdata( $author_id, 'user_login', 'id' );
	  }
      global $fep;
	  $errors = $fep->check_message();
	  $errors = $errors->get_error_messages();
	  foreach($errors as $error){
	    $error_text .= '<div class="error">'. __( esc_html( $error ), 'prokkat') .'</div>';
	  }
	  $token = fep_create_nonce( 'new_message' );
	}
    if( $error_text )
	  echo json_encode( array( 'error'=>$error_text, 'token'=>$token ));
	else {
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
		$name = fep_get_userdata($_POST['message_from'], 'display_name', 'id' );
		$email = fep_get_userdata($_POST['message_from'], 'user_email', 'id' );
		$message = $_POST['message_content'];
		$headers = array(
	      'Content-Type: text/html; charset=UTF-8',
	      'From: ' . $blogname . ' <' . $email . '>' . "\r\n",
	    );
		$url = my_action_url('messagebox');
        ob_start();
	    include( get_stylesheet_directory() . '/email/message.php');
	    $message = ob_get_clean();
        $to = get_the_author_meta('user_email', $author_id);
        wp_mail($to, __('New message', 'prokkat'), $message, $headers);
		
		$captcha1 = rand(0, 9);
        $captcha2 = rand(0, 9);
		$captcha_text = $captcha1 . ' + ' . $captcha2;
        $captcha_sum = $captcha1 + $captcha2;
		echo json_encode( array( 'message'=>$msg, 'token'=>$token, 'captcha'=>$captcha_text, 'captcha_sum'=>$captcha_sum  ));
	}
    exit;
  }
  add_action('wp_ajax_my_new_message_ajax', "my_new_message_ajax");
  add_action('wp_ajax_nopriv_my_new_message_ajax', "my_new_message_ajax"); // to show "Log in to write message."
  
  function my_new_message_action( $msg_id )
  {
	$html = $error_text = '';
	if(isset($_POST['new_message'])) {
	  if( isset($_POST['message_to'] )) {
		  $author_id = $_POST['message_to'];
		  $_POST['message_to'] = fep_get_userdata( $author_id, 'user_login', 'id' );
	  }
	  
	  global $fep;
	  $errors = $fep->check_message();
	  $errors = $errors->get_error_messages();
	  if( count( $errors ) > 0 ){
	    foreach($errors as $error){
	      $error_text .= '</p>'.__( esc_html( $error ), 'prokkat' ).'</p>';
	    }
		$html .= notification_markup( $error_text );
	  }
	  else {
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
		$name = fep_get_userdata($_POST['message_from'], 'display_name', 'id' );
		$email = fep_get_userdata($_POST['message_from'], 'user_email', 'id' );
		$message = $_POST['message_content'];
		$headers = array(
	      'Content-Type: text/html; charset=UTF-8',
	      'From: ' . $blogname . ' <' . $email . '>' . "\r\n",
	    );
		$url = my_action_url('viewmessage')."&id=".$msg_id;
        ob_start();
	    include( get_stylesheet_directory() . '/email/message.php');
	    $message = ob_get_clean();
        $to = get_the_author_meta('user_email', $author_id);
        wp_mail($to, __('New message', 'prokkat'), $message, $headers);
		
		$html .= notification_markup(__("Message successfully sent.", 'prokkat'));
	  }
	}
	return $html;
  }
  
  
  function my_message_box($action = '', $title = '', $total_message = false, $messages = false )
  {
	global $user_ID;

	  if ( !$action )
	    $action = ( isset( $_GET['fepaction']) && $_GET['fepaction'] )? $_GET['fepaction']: 'messagebox';
	  
	  if ( !$title )
	    $title = __('My messages', 'prokkat');
	  
	  if( false === $total_message )
	  $total_message = fep_get_user_total_message( $action );
	  
	  if( false === $messages )
	  $messages = fep_get_user_messages( $action );
  
	  $msgsOut = '';
      if ($total_message)
      {
		if( !current_user_can( 'manage_options' )) {
		  global $fep;
		  $msgBoxSize = $fep->getUserNumMsgs();
          $msgBoxTotal = fep_get_option('num_messages');
		  if( $msgBoxSize > $msgBoxTotal )
		    $msgsOut .= notification_markup(__("Your Message Box Is Full! Please delete some messages.", 'prokkat'));
	    }
		
		$msgsOut .= "<div class='add__title text-left'>". $title ."</div> <div class='chat chat_dialog grey-background'><ul class='chat__list'>";
        $numPgs = $total_message / fep_get_option('messages_page',50);
		$page = ( isset ($_GET['feppage']) && $_GET['feppage'] ) ? absint($_GET['feppage']) : 0;
		
        if ($numPgs > 1)
        {
          $msgsOut .= '<ul class="pagination clearfix"><li class="pagination__title"><strong>'. __("Page", 'prokkat') .'</strong></li>';
          for ($i = 0; $i < $numPgs; $i++)
            if ( $page != $i){
			  $msgsOut .= "<li><a href='".esc_url( my_action_url($action) )."&feppage=".$i."' class='pagination__item'>".($i+1)."</a></li>";
            } else {
              $msgsOut .= "<li><span class='pagination__item pagination__item_active'>".($i+1)."</span></li>";
			}
          $msgsOut .= "</ul>";
        }

        foreach ($messages as $msg)
        {
		  $class = "";
          if ($msg->status == 0 && $msg->last_sender != $user_ID)
		    $class = " unread";
         
		  if( $user_ID != $msg->to_user )
		    $cor_with_id = $msg->to_user;
	      else $cor_with_id = $msg->from_user;      // maybe will need: get_author_posts_url( $cor_with_id )
		  $cor_with = fep_get_userdata( $cor_with_id, 'display_name', 'id' );
		  
		  $msgsOut .= "
				<li class='chat__list-item_dialog". $class ."'>
							<img src='".get_stylesheet_directory_uri()."/img/chat.svg' class='chat__icon' />
              <div class='chat__list-body chat__list-body_dialog'>
			    <a href='".my_action_url('viewmessage')."&id=".$msg->id."' class='chat__list-name chat__list-name_dialog'>". __( 'Correspondence with ', 'prokkat' ) . $cor_with ."</a>
		        <div>
                  <p class='chat__list-bottom'>". __( 'Last message from:', 'prokkat' ) ." ".fep_get_userdata( $msg->last_sender, 'display_name', 'id' )." ".fep_format_date($msg->last_date)."</p>
                </div>
			  </div>
              <a href='".my_action_url('viewmessage')."&id=".$msg->id."' class='chat__list__btn'>". __( 'Open', 'prokkat' ) ."</a>
            </li>";
        }
		$msgsOut .= "</ul></div>";
		return $msgsOut;
      }
      else
      {
        return '<div class="title-list">'. __( 'You have no messages.', 'prokkat' ) .'</div>';
      }
	
  }
  
  
  function my_getWholeThread( $id, $start = 0 )
  {
    global $wpdb;
    $end = 10;
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".FEP_MESSAGES_TABLE." WHERE id = %d OR parent_id = %d ORDER BY send_date DESC LIMIT %d, %d", $id, $id, $start, $end));
    return array_reverse($results);
  }

  
  function message_markup( $wholeThread )
  {
    global $wpdb, $user_ID;
	$threadOut = '';
	$num = count($wholeThread);
	$c = 1;
	$parent_status = 0;
	foreach ($wholeThread as $post)
    {
      //Check for privacy errors first
      if ($post->to_user != $user_ID && $post->from_user != $user_ID && !current_user_can( 'manage_options' ))
      {
        return notification_markup( __("You do not have permission to view this message!", 'fep'));
      }

	  $status = $class = $s_class = '';
	  if( $post->from_user == $user_ID ) {
		$class = " chat__list-text_sender";
		$s_class = " chat__list__sender";
	  }
	  else {
        if( $c == $num ) {
		  if( !$parent_status ) $status = "не прочитано";
          else $status = "прочитано";
		}
	  }

      $threadOut .= '
	    <li class="chat__list-item'. $s_class .'">
		  <div class="chat__list-photo">'. get_avatar($post->from_user, 63) .'</div>
          <div class="chat__list-body">
		    <div class="chat__list-name">'. fep_get_userdata( $post->from_user, 'display_name', 'id' ). '<span class="chat__list-status">'. $status .'</span></div>
						<span class="chat__list-text'. $class .'">'. fep_output_filter($post->message_contents) .'</span>
						</br>
	        <div class="chat__list-status chat__list-status_time">'. fep_format_date($post->send_date) .'</div>
		  </div>
		</li>';
      if( $post->parent_id == 0 ) {
		if( $post->status == 1 ) $parent_status = 1;
        elseif ($post->status == 0 && $user_ID != $post->last_sender && ( $user_ID == $post->from_user || $user_ID == $post->to_user )) {//Update only if the reader is not last sender
          $wpdb->update( FEP_MESSAGES_TABLE, array( 'status' => 1 ), array( 'id' => $post->id ), array( '%d' ), array( '%d' ));
		}
	  }
	  $c++;
	}
	return $threadOut;
  }
  
    
  function my_view_message()
  {
    global $wpdb, $user_ID;
    $pID = absint( $_GET['id']);
	
	echo my_new_message_action( $pID );
	
    $order = (isset ( $_GET['order'] ) && strtoupper($_GET['order']) == 'DESC' ) ? 'DESC' : 'ASC';
	if ( 'ASC' == $order ) $anti_order = 'DESC'; else $anti_order = 'ASC';

	if ( !$pID ) return;
	
	global $fep;
	
	$token = fep_create_nonce('delete_message');
	$del_url = my_action_url( "deletemessage" ) . "&id=$pID&token=$token";
	
	
    $wholeThread = my_getWholeThread( $pID );
	$whole = $fep->getWholeThread( $pID );
    
	if( $user_ID != $wholeThread[0]->from_user )
		$cor_with_id = $wholeThread[0]->from_user;
	else $cor_with_id = $wholeThread[0]->to_user;
	$cor_with = fep_get_userdata( $cor_with_id, 'display_name', 'id' );
	$message_title = $wholeThread[0]->message_title;
	if (substr_count($message_title, __("Re:", 'fep')) < 1) //Prevent all the Re:'s from happening
          $re = __("Re:", 'fep');
        else
          $re = "";
	
    $threadOut = '<div class="add__title text-left">'. __('Correspondence with ', 'prokkat') . $cor_with .'<a class="chat__delete" data-link = "'. $del_url .'" href="#modalDel" >'. __('Delete dialogue', 'prokkat') .'</a></div><div class="chat">';
	
	if( count($whole) > count($wholeThread) ) {
	$threadOut .='
	<div id="more'.$pID.'">
      <a href="#0" class="btn btn_flat btn_moremess"><i class="fa fa-refresh" aria-hidden="true"></i>Більше повідомлень</a>
    </div>';
	}
	
	$threadOut .='<ul class="chat__list">';
	  
    $threadOut .= message_markup( $wholeThread );

    $threadOut .= "</ul></div>";

    //SHOW THE REPLY FORM
	if ( fep_is_user_blocked() ){
	  $threadOut .= '<div class="notification mess-info mess-info_center">'. __("You cannot send messages because you are blocked by administrator!", 'prokkat') .'</div>';
	} else {
	  $args = array (
        'message_to' => $cor_with_id,
        'message_top' => fep_get_userdata( $cor_with_id, 'display_name', 'id' ),
        'message_title' => $re.$message_title,
        'message_from' => $user_ID,
        'parent_id' => $pID,
	    'token' => fep_create_nonce('new_message')
      );

      $threadOut .= '
					<div class="chat chat_send">
					<div class="chat__send-container">
						<img src='.get_stylesheet_directory_uri().'/img/chat.svg" class="chat-icon"/>					
						<div class="chat__send-title">'. __('New message', 'prokkat') .'</div>
					</div>
			<form class="chat__send clearfix" id="chatform" action="'.my_action_url('viewmessage').'&id='.$pID.'" method="post">
			  <p class="status input-wrp input-wrp_block hide"></p>
			  <textarea name="message_content" class="textarea textarea_mess textarea_chat required" placeholder="'. __('Message text', 'prokkat') .'" required></textarea>
				<div class="btn-container">
					<input type="submit" name="new_message" class="save-btn" value="'. __('Send', 'prokkat') .'" />				
				</div>
			  <input type="hidden" name="message_to" value="'.$args['message_to'].'" />
			  <input type="hidden" name="message_top" value="'.$args['message_top'].'" />
			  <input type="hidden" name="message_title" value="'.$args['message_title'].'" />
			  <input type="hidden" name="message_from" value="'.$args['message_from'].'" />
			  <input type="hidden" name="parent_id" value="'.$args['parent_id'].'" />
			  <input type="hidden" name="token" value="'.$args['token'].'" /><br/>
			</form>
		  </div>';
		
	}

    return $threadOut;
  }
  
  function my_delete_corr()
  {
      global $wpdb, $user_ID;

      $delID = absint( $_GET['id'] );
	  
	  if (!fep_verify_nonce($_GET['token'], 'delete_message'))
	    return notification_markup(__('Invalid Token!', 'prokkat'));
	  
	  $info = $wpdb->get_row($wpdb->prepare("SELECT from_user, to_user, to_del, from_del FROM ".FEP_MESSAGES_TABLE." WHERE id = %d", $delID));

      if ($info->to_user == $user_ID)
      {
        if ($info->from_del == 0) {
		  $wpdb->update( FEP_MESSAGES_TABLE, array( 'to_del' => 1 ), array( 'id' => $delID ), array( '%d' ), array( '%d' ));
        } else {
		  $ids = $wpdb->get_col($wpdb->prepare("SELECT id FROM ".FEP_MESSAGES_TABLE." WHERE id = %d OR parent_id = %d", $delID, $delID));
	      $id = implode(',',$ids);
	  
          $wpdb->query($wpdb->prepare("DELETE FROM ".FEP_MESSAGES_TABLE." WHERE id = %d OR parent_id = %d", $delID, $delID));
		  $wpdb->query("DELETE FROM ".FEP_META_TABLE." WHERE message_id IN ({$id})");
		}
      }
      elseif ($info->from_user == $user_ID)
      {
        if ($info->to_del == 0){
		  $wpdb->update( FEP_MESSAGES_TABLE, array( 'from_del' => 1 ), array( 'id' => $delID ), array( '%d' ), array( '%d' ));
        } else {
		  $ids = $wpdb->get_col($wpdb->prepare("SELECT id FROM ".FEP_MESSAGES_TABLE." WHERE id = %d OR parent_id = %d", $delID, $delID));
	      $id = implode(',',$ids);
	  
          $wpdb->query($wpdb->prepare("DELETE FROM ".FEP_MESSAGES_TABLE." WHERE id = %d OR parent_id = %d", $delID, $delID));
		  $wpdb->query("DELETE FROM ".FEP_META_TABLE." WHERE message_id IN ({$id})");
		}
      } else {
	    return notification_markup(__("No permission!", 'prokkat'));
	  }
		
		return notification_markup(__("Dialogue was successfully deleted!", 'prokkat'));
    }
  
?>