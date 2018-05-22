<?php
  
function check_liqpay_response() {
  
  if( !isset( $_REQUEST['feature_h'] ) && !isset( $_REQUEST['feature_c'] ) && !isset( $_REQUEST['signature'] ) && !isset( $_REQUEST['data'] ))
	  return;
	  
  $secret_key = 'B1MkOAkTmZL9JnfNA2kGsUChc4bn2RRwjXystdq9';
  //$post_id = $_REQUEST['post_id'];
  $featured_home = $_REQUEST['feature_h'];
  $featured_cate = $_REQUEST['feature_c'];

  $signature = $_POST['signature'];
  $data = $_POST['data'];

  $MySignature   = base64_encode( sha1($secret_key.$data.$secret_key, 1 ));

  if ( $signature == $MySignature ) {

	$data = json_decode( base64_decode($data) );
	$post_id = explode( '_', $data->order_id );
	$post_id = $post_id[0];
	
	if( $data->status == 'sandbox' ) {
	    update_option('cc_payment_status', 'yes');
	  //Update listing
      cc_set_listing($post_id);
	
	  //Renewing listing
      gc_renew_listing($post_id);
	
	  //Upgrading listing
      custom_upgrade_listing( $post_id, $featured_home, $featured_cate );
	
	  //Set transactions fields
      //cc_set_transaction($_REQUEST);

	  //Mail details to admin email
      $mailto = get_option('admin_email');
      //$mailto = 'anastasiya.tulparova@gmail.com';
      $subject = __('LiqPay Test - payment received', THEME_SLUG);
      $headers = 'From: ' . __('Sweetico', THEME_SLUG) . ' <' . get_option('admin_email') . '>' . "\r\n";
      $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

      $message = __('Dear Admin,', THEME_SLUG) . "\r\n\r\n";
      $message .= sprintf(__('The following payment is receive on your %s website.', THEME_SLUG), $blogname) . "\r\n\r\n";
      $message .= __('Payment Details', THEME_SLUG) . "\r\n";
      $message .= __('-----------------') . "\r\n";
      $message .= __('Post ID: ', THEME_SLUG) . $post_id . "\r\n";
      $message .= __('Transaction ID: ', THEME_SLUG) . $data->transaction_id . "\r\n";
      $message .= __('Payment type: ', THEME_SLUG) . $data->paytype . "\r\n";
      $message .= __('Amount: ', THEME_SLUG) . $data->amount  . " (" . $data->currency  . ")\r\n\r\n";
      $message .= __('Full Details', THEME_SLUG) . "\r\n";
      $message .= __('-----------------') . "\r\n";
      $message .= print_r($data, true) . "\r\n";
    //admin email
      wp_mail($mailto, $subject, $message, $headers);
/*
      $blogtime = current_time('mysql');
      $transaction_details .= "--------------------------------------------------------------------------------\r";
      $transaction_details .= "Payment Details for Listing ID #{$post_id }\r";
      $transaction_details .= "--------------------------------------------------------------------------------\r";
      $transaction_details .= "--------------------------------------------------------------------------------\r";
      $transaction_details .= "Trans ID: {$sTransactionId}\r";
      $transaction_details .= "Status: {$sStatus}\r";
      $transaction_details .= "Date: $blogtime\r";
      $transaction_details .= "--------------------------------------------------------------------------------\r";
      $transaction_details = __($transaction_details, THEME_SLUG);
      $subject = __("Listing Submitted and Payment Success Confirmation Email", THEME_SLUG);
      $site_name = get_option('blogname');
      $fromEmail = 'Admin';
      $filecontent = $transaction_details;
      global $wpdb;
      $placeinfosql = "SELECT ID, post_title, guid, post_author from $wpdb->posts where ID ={$post_id}";
      $placeinfo = $wpdb->get_results($placeinfosql);
      foreach ($placeinfo as $placeinfoObj) {
        $post_link = $placeinfoObj->guid;
        $post_title = '<a href="' . $post_link . '">' . $placeinfoObj->post_title . '</a>';
        $authorinfo = $placeinfoObj->post_author;
        $userInfo = get_author_info($authorinfo);
        $to_name = $userInfo->user_nicename;
        $to_email = $userInfo->user_email;
        $user_email = $userInfo->user_email;
      }
      $headers = 'From: Sweetico <' . $mailto . '>' . "\r\n" . 'Reply-To: ' . $mailto;
      wp_mail($user_email, $subject, $filecontent, $headers); //email to client
*/	}
	  
	  
  /*
  if ( $sStatus == 'success' && $signature == $MySignature ) {
	  
    update_option('cc_payment_status', 'yes');
	//Update listing
    cc_set_listing($post_id);
	
	//Renewing listing
    //gc_renew_listing($request['post_id'], $request['pkg_type']);
	
	//Upgrading listing
    $upgrade_meta_values = get_post_meta($post_id, 'cc_add_type', true);
    if ($upgrade_meta_values == "free") {
      cc_upgrade_listing($post_id, $sAmount);
    }
	
	//Set transactions fields
    cc_set_transaction($_REQUEST);
	//admin email confirmation
    //TODO - move into wordpress options panel and allow customization
	wp_mail(get_option('admin_email'), 'Payment Receive', "A membership payment has been completed. Check to make sure this is a valid order by comparing this messages Paypal Transaction ID to the respective ID in the Paypal payment receipt email.");

    //Mail details to admin email
    $mailto = get_option('admin_email');
    $subject = __('PayPal IPN - payment receiver', THEME_SLUG);
    $headers = 'From: ' . __('Classicraft Admin', THEME_SLUG) . ' <' . get_option('admin_email') . '>' . "\r\n";
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    $message = __('Dear Admin,', THEME_SLUG) . "\r\n\r\n";
    $message .= sprintf(__('The following payment is receive on your %s website.', THEME_SLUG), $blogname) . "\r\n\r\n";
    $message .= __('Payment Details', THEME_SLUG) . "\r\n";
    $message .= __('-----------------') . "\r\n";
    //$message .= __('Payer PayPal address: ', THEME_SLUG) . $request['payer_email'] . "\r\n";
    $message .= __('Payer phone: ', THEME_SLUG) . $sSenderPhone . "\r\n";
    $message .= __('Transaction ID: ', THEME_SLUG) . $sTransactionId . "\r\n";
    //$message .= __('Payer first name: ', THEME_SLUG) . $request['first_name'] . "\r\n";
    //$message .= __('Payer last name: ', THEME_SLUG) . $request['last_name'] . "\r\n";
    $message .= __('Payment type: ', THEME_SLUG) . $sType . "\r\n";
    $message .= __('Amount: ', THEME_SLUG) . $sAmount  . " (" . $sCurrency  . ")\r\n\r\n";
    $message .= __('Full Details', THEME_SLUG) . "\r\n";
    $message .= __('-----------------') . "\r\n";
    //$message .= print_r($request, true) . "\r\n";
    //admin email
    wp_mail($mailto, $subject, $message, $headers);

    $blogtime = current_time('mysql');
    $transaction_details .= "--------------------------------------------------------------------------------\r";
    $transaction_details .= "Payment Details for Listing ID #{$post_id }\r";
    $transaction_details .= "--------------------------------------------------------------------------------\r";
    $transaction_details .= "--------------------------------------------------------------------------------\r";
    $transaction_details .= "Trans ID: {$sTransactionId}\r";
    $transaction_details .= "Status: {$sStatus}\r";
    $transaction_details .= "Date: $blogtime\r";
    $transaction_details .= "--------------------------------------------------------------------------------\r";
    $transaction_details = __($transaction_details, THEME_SLUG);
    $subject = __("Listing Submitted and Payment Success Confirmation Email", THEME_SLUG);
    $site_name = get_option('blogname');
    $fromEmail = 'Admin';
    $filecontent = $transaction_details;
    global $wpdb;
    $placeinfosql = "SELECT ID, post_title, guid, post_author from $wpdb->posts where ID ={$post_id}";
    $placeinfo = $wpdb->get_results($placeinfosql);
    foreach ($placeinfo as $placeinfoObj) {
      $post_link = $placeinfoObj->guid;
      $post_title = '<a href="' . $post_link . '">' . $placeinfoObj->post_title . '</a>';
      $authorinfo = $placeinfoObj->post_author;
      $userInfo = get_author_info($authorinfo);
      $to_name = $userInfo->user_nicename;
      $to_email = $userInfo->user_email;
      $user_email = $userInfo->user_email;
    }
    $headers = 'From: ' . $to_admin . ' <' . $user_email . '>' . "\r\n" . 'Reply-To: ' . $to_admin;
    wp_mail($user_email, $subject, $filecontent, $headers); //email to client
				
    } elseif ( $sStatus == 'failure' && $signature == $MySignature ) {

      update_option('cc_payment_status', 'no');
      //Expire listing 
      cc_listing_expire($post_id);
      // send an email if payment didn't work
      $mailto = get_option('admin_email');
      $subject = __('PayPal IPN - payment failed', THEME_SLUG);
      $headers = 'From: ' . __('Classicraft Admin', THEME_SLUG) . ' <' . get_option('admin_email') . '>' . "\r\n";
      $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

      $message = __('Dear Admin,', THEME_SLUG) . "\r\n\r\n";
      $message .= sprintf(__('The following payment has failed on your %s website.', THEME_SLUG), $blogname) . "\r\n\r\n";
      $message .= __('Payment Details', THEME_SLUG) . "\r\n";
      $message .= __('-----------------') . "\r\n";
      //$message .= __('Payer PayPal address: ', THEME_SLUG) . $request['payer_email'] . "\r\n";
      $message .= __('Payer phone: ', THEME_SLUG) . $sSenderPhone . "\r\n";              
	  $message .= __('Transaction ID: ', THEME_SLUG) . $sTransactionId . "\r\n";
      //$message .= __('Payer first name: ', THEME_SLUG) . $request['first_name'] . "\r\n";
      //$message .= __('Payer last name: ', THEME_SLUG) . $request['last_name'] . "\r\n";
      $message .= __('Payment type: ', THEME_SLUG) . $sType . "\r\n";
      $message .= __('Amount: ', THEME_SLUG) . $sAmount . " (" . $sCurrency . ")\r\n\r\n";
      $message .= __('Full Details', THEME_SLUG) . "\r\n";
      $message .= __('-----------------') . "\r\n";
      //$message .= print_r($request, true) . "\r\n";

      wp_mail($mailto, $subject, $message, $headers);

    } elseif ( $sStatus == 'wait_secure' && $signature == $MySignature ) {

      update_option('cc_payment_status', 'no');
      // send an email if payment is pending
      $mailto = get_option('admin_email');
      $subject = __('PayPal IPN - payment pending', THEME_SLUG);
      $headers = 'From: ' . __('Classicraft Admin', THEME_SLUG) . ' <' . get_option('admin_email') . '>' . "\r\n";
      $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

      $message = __('Dear Admin,', THEME_SLUG) . "\r\n\r\n";
      $message .= sprintf(__('The following payment is pending on your %s website.', THEME_SLUG), $blogname) . "\r\n\r\n";
      $message .= __('Payment Details', THEME_SLUG) . "\r\n";
      $message .= __('-----------------') . "\r\n";
    //$message .= __('Payer PayPal address: ', THEME_SLUG) . $request['payer_email'] . "\r\n";
      $message .= __('Payer phone: ', THEME_SLUG) . $sSenderPhone . "\r\n";
      $message .= __('Transaction ID: ', THEME_SLUG) . $sTransactionId . "\r\n";
    //$message .= __('Payer first name: ', THEME_SLUG) . $request['first_name'] . "\r\n";
    //$message .= __('Payer last name: ', THEME_SLUG) . $request['last_name'] . "\r\n";
      $message .= __('Payment type: ', THEME_SLUG) . $sType . "\r\n";
      $message .= __('Amount: ', THEME_SLUG) . $sAmount . " (" . $sCurrency . ")\r\n\r\n";
      $message .= __('Full Details', THEME_SLUG) . "\r\n";
      $message .= __('-----------------') . "\r\n";
    //$message .= print_r($request, true) . "\r\n";

      wp_mail($mailto, $subject, $message, $headers);

    } else*/ if ( $sStatus == 'sandbox' ) {
	 
	
    }
  }
}
add_action('init', 'check_liqpay_response');

?>