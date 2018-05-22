<?php
global $current_user;
$status = false;
$update_msg = $err_msg = $user_data = '';

  if (isset($_POST['edit_profile'])) {

    $user_meta = $wp_user = array();
	
	if( isset( $_POST['nname'] ) && $_POST['nname'] ) {
	  $user_meta['nickname'] = $wp_user['display_name'] = esc_attr($_POST['nname']);
	}
    $user_meta['city_id'] = isset( $_POST['cc_city_id'] ) ? esc_attr($_POST['cc_city_id']) : '';
    $user_meta['city_name'] = isset( $_POST['cc_address'] ) ? esc_attr($_POST['cc_address']) : '';
    $user_meta['cc_address_list'] = isset( $_POST['cc_address_list'] ) ? esc_attr($_POST['cc_address_list']) : '';
    $user_meta['cc_city_id'] = isset( $_POST['cc_city_id'] ) ? esc_attr($_POST['cc_city_id']) : '';
    $user_meta['cc_locations'] = isset( $_POST['cc_locations'] ) ? esc_attr($_POST['cc_locations']) : '';

    $user_meta['phone'] = isset( $_POST['phone'] ) ? esc_attr($_POST['phone']) : '';
    $user_meta['description'] = isset( $_POST['abtme'] ) ? esc_attr($_POST['abtme']) : '';
    $user_meta['description_ru'] = $user_meta['description'];
	
    //Update user data
    foreach ($user_meta as $meta_key => $meta_value):
        update_user_meta( $current_user->ID, $meta_key, $meta_value );
    endforeach;
	
	$wp_user['user_email'] = esc_attr($_POST['email']);
    $wp_user['user_url'] = esc_attr($_POST['website']);
	
	foreach ($wp_user as $meta_key => $meta_value):
        if ( $meta_key == 'user_email' && $meta_value == '' ) continue;
		$user_data = wp_update_user( array( 'ID' => $current_user->ID, $meta_key => $meta_value ) );
		if ( is_wp_error( $user_data ) ) {
          $err_msg = $user_data->get_error_message();
        }
    endforeach;

	if( !$err_msg ) $update_msg = "Ваш профіль оновлено";

  }

  if( $err_msg ){ echo '<p class="error">'. $err_msg .'</p>'; }
  
  if( isset( $_GET['activated'] ) && $_GET['activated']=='yes' ) : ?>
     <div class="mess-info">
       <div class="upload__control-img close-mess">
         <div class="upload__control-del ">
           <a href="#0">
             <svg fill="#FFFFFF" height="30" viewBox="0 0 24 24" width="30" xmlns="http://www.w3.org/2000/svg">
               <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
               <path d="M0 0h24v24H0z" fill="none"/>
             </svg>
           </a>
         </div>
       </div>
       <div class="mess-info__title">
         <?php _e('Welcome to RentHUB!', 'prokkat'); ?>
       </div>
       <p>
         Ви зареєструвалися, так що ви можете додавати оголошення зі своєю технікою та інструментами. <br /> Наші відвідувачі стануть вашими постійними клієнтами.
       </p>
     </div>
  <?php endif; ?>
	
  <div class="add__title"><?php _e('Edit Profile', 'prokkat'); ?></div>
  <?php if ($update_msg) { ?>
    
     <div class="notification mess-info mess-info_center">
            <div class="upload__control-img close-mess">
                <div class="upload__control-del ">
                    <a href="#0">
                            <svg fill="#FFFFFF" height="30" viewBox="0 0 24 24" width="30" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                <path d="M0 0h24v24H0z" fill="none"/>
                            </svg>
                    </a>
                </div>
            </div>
            <?php echo $update_msg; ?>
        </div> 
    <?php } ?>
  <div>
    <form name="edit_profile_form" id="edit-profile-form" method="post" enctype="multipart/form-data">
    <div class="add__step__container last__step__margin">  
        <div class="input-wrp input-wrp_block add__block">
            <div class="form__title"><?php _e('Your Name', 'prokkat'); ?></div>
            <div class="input-wrp input-wrp_block">
                <input type="text" class = "input_add" id="nname" name="nname" value="<?php echo get_the_author_meta('nickname', $current_user->ID); ?>"/>
            </div>
        </div>

        <div class="input-wrp input-wrp_block add__block">
                                <div class="col6 nopaddingl">
                                    <div class="form__title"><?php _e('City', 'prokkat'); ?></div>
                                    <div class="input-wrp input-wrp_block">
                                        <input type="text" name="cc_address" id="s_address" class="input_add noEnterSubmit" value="" placeholder="<?php _e('Enter your city', 'prokkat') ?>" />
                                        <input type="hidden" name="cc_city_id" id="s_city_id" class="input_add" value="<?php echo get_the_author_meta('city_id', $current_user->ID); ?>" />
                                    </div>
                                </div>
                                <div class="col6 nopaddingr">
                                    <div class="form__title">Email</div>
                                    <div class="input-wrp input-wrp_block">
                                        <input type="text" class = "input_add" id="email" name="email" value="<?php echo get_the_author_meta('user_email', $current_user->ID); ?>"/>
                                    </div>
                                </div>
                            </div>
                        <div class="input-wrp input-wrp_block add__block">
							<div id='address_list'></div>                            
                            <div class="req form__title"><?php _e('Location', 'prokkat'); ?></div>
                            <div class="input-wrp input-wrp_block">
								<span class="max-text"><?php _e('Maximum', 'prokkat'); ?> 4</span>
								<input type="hidden" name="cc_address_list" id="cc_address_list" value='<?php echo get_the_author_meta('cc_address_list', $current_user->ID); ?>'/>
                                <input type="hidden" name="cc_city_id" id="cc_city_id" value='<?php echo get_the_author_meta('cc_city_id', $current_user->ID); ?>'/>
                                <input type="hidden" name="cc_locations" id="cc_locations" value='<?php echo get_the_author_meta('cc_locations', $current_user->ID); ?>'/>
                                <div class="address-input__container">
                                    <input type="text" name="cc_address" id="cc_address" class="input_add noEnterSubmit" value='' placeholder="<?php _e('Address', 'prokkat'); ?>" />
                                    <input type="button" id="add_address" value="<?php _e('Add', 'prokkat'); ?>" />
                                </div>
			                    <div id="map_canvas" style="height:350px; margin-top: 10px; position:relative;"  class="form_row clearfix"></div>
                            </div>
                        </div>
        <div class="input-wrp input-wrp_block add__block">
            <div class="col6 nopaddingl">
                <div class="form__title"><?php _e('Phone', 'prokkat'); ?></div>
                <div class="input-wrp input-wrp_block">
                    <input type="tel" class="input_add" id="phone" name="phone" value="<?php echo get_the_author_meta('phone', $current_user->ID); ?>" />
                </div>
            </div>
        </div>
        <div class="form__title"><?php _e('About me', 'prokkat'); ?></div>
        <div class="input-wrp input-wrp_block">
            <textarea id="abtme" class ="textarea textarea_mess textarea_chat" name="abtme"><?php echo get_the_author_meta('description', $current_user->ID); ?></textarea>
        </div>
        <div class="input-wrp_block text-right">
            <input type="submit" class = "save-btn" name="edit_profile" value="<?php _e('Update', 'prokkat'); ?>" />
        </div>
    <div>
    </form>
</div>
<?php edit_map(); ?>

<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/jquery.maskedinput.min.js"></script>
<script>
jQuery(document).ready(function(){
  $('#phone').mask("+38 (999) 999-99-99");
});
</script>