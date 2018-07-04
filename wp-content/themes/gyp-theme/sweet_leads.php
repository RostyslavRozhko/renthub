<?php
  global $current_user;
  $captcha1 = rand(0, 9);
  $captcha2 = rand(0, 9);
  $cc_sbs_captcha = $captcha1 + $captcha2;
  $ava = get_the_author_meta('user_avatar', $author_id);
    if( !$ava ) {
      $ava = get_stylesheet_directory_uri() .'/img/no-avatar.png'; 
    }
  $city = get_the_author_meta('city_name', $author_id);
  $city = explode("," , $city);
?>
    <form id="send-msg" method="post" class="hide">
      <div class="form form_send-msg clearfix">
      <div class="call-feedback__author">
            <img src="<?php echo $ava; ?>" />
            <div class="call-feedback__name">
                <a><?php echo the_author_meta('nickname');?></a>
                <?php if (!empty($city[0])): ?>
                      <span><?php echo $city[0];?></span>
                <?php endif; ?>
            </div>

        </div>
      <!--<h3 class="advert__mess-title"><?php _e('Send message', 'prokkat'); ?></h3>-->
	  
        <div>
        <p class="status input-wrp input-wrp_block hide" style="font-family: 'Fira Sans', sans-serif;"></p>
	      <textarea name="message_content" id="message" class="required textarea textarea_mess" placeholder="<?php echo __( 'Ask the manufacturer', 'prokkat' ); ?>"></textarea>
        </div>

        <div class="input-wrp input-wrp_block">
          <div>
            <div class="input-wrp input-wrp_block">
              <div class="col6 nopaddingl">
                <input class="captcha required input_add" id="captcha" placeholder="<?php echo $captcha1 . ' + ' . $captcha2; ?>=?" type="text" name="captcha" value="" />
              </div>
              <div class="col6 nopaddingl">
              <div class="full nopadding rigth">
                <div class="wrap-btnsend">
                  <input type="hidden" id="captchacode" name="captchacode" value="<?php echo $cc_sbs_captcha; ?>" />
                  <input type="hidden" id="message_to" value="<?php echo $author_id; ?>" />
                        <input type="hidden" id="message_title" value="<?php echo 'Message from '.$current_user->nickname; ?>" />
                        <input type="hidden" id="message_from" value="<?php echo $user_ID; ?>" />
                        <input type="hidden" id="parent_id" value="0" />
                      <input type="hidden" id="token" value="<?php echo fep_create_nonce('new_message'); ?>" />
                        <input type="submit" name="new_message" class="btn btn_send <?php if(!is_user_logged_in()) { echo 'btnModal'; } ?>" id="leads-submit" value="<?php echo __( 'Send', 'prokkat' ); ?>"/>
                      </div>  
                    </div>  
                  </div>
              </div>
            </div>
          </div>
        </div>
		

    </form>
