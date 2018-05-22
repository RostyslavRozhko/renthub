<?php
  
  require_once( 'LiqPayClass.php' );
  
  $pay_method = $_REQUEST['pay_method'];
  $page_id = get_option('cc_payment_stat');
  $return_url = site_url("?page_id=$page_id&post_id=$post_id&pay_method=$pay_method");
  $notify_url = site_url("?post_id=$post_id&pay_method=$pay_method&feature_h=$featured_home&feature_c=$featured_cate");
  
  $public_key = 'i99415099765';
  $secret_key = 'B1MkOAkTmZL9JnfNA2kGsUChc4bn2RRwjXystdq9';
  $order_desc = sprintf( 'Платеж за премиум пакет # %s', $post_id );
  $test = '1'; // NO - ''
  
  $amount = $_POST['total_price'];
  $currency = 'UAH';
  $orderID = $post_id.'_'.time();
  
  $liqpay = new LiqPay($public_key, $secret_key);

  $data = array();
  $data['form'] = $liqpay->cnb_form(array(
        'version'       => '3',
		'action'        => 'pay',
        'amount'        => $amount,
        'currency'      => $currency,
        'description'   => $order_desc,
        'order_id'      => $orderID,
        'result_url'	=> $return_url,
        'server_url'	=> $notify_url,
        'sandbox'		=> $test
  ));
  echo $data['form'];
?>