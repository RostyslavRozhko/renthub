<?php
global $current_user;
$status = false;
$update_msg = $err_msg = $user_data = '';

  if (isset($_POST['edit_profile'])):

    /* Update user password. */
    if (!empty($_POST['npass']) && !empty($_POST['passagain'])) {
        if ($_POST['npass'] == $_POST['passagain']) {
            $user_data = wp_update_user(array('ID' => $current_user->ID, 'user_pass' => esc_attr($_POST['npass'])));
            if ( is_wp_error( $user_data ) ) {
              $err_msg = $user_data->get_error_message();
            }
        } else {
            $err_msg = "Паролі не збігаються";
        }
    }
	
	if( !$err_msg ) $update_msg = "Ваш пароль оновлено";
	
  endif;

  
 /* if( $update_msg ){ echo '<p class="notification">'. $update_msg .'</p>'; }*/
  
?>
  <div class="add__title"><?php _e('Change password', 'prokkat'); ?></div>
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
            <?php if( $err_msg ){ echo '<div class="error">'. $err_msg .'</div>'; }?>
        </div>
         <?php } ?>
    <form name="edit_profile_form" id="edit-profile-form" action="<?php $_SERVER[PHP_SELF]; ?>" method="post" enctype="multipart/form-data">
        <div class="add__step__container last__step__margin">
            <div class="form__title"><?php _e('New Password:', 'prokkat'); ?></div>
            <div class="input-wrp input-wrp_block margin-bot">
                <input type="password" class = "input_add" id="npass" name="npass" value=""/>
            </div>
            <div class="form__title"><?php _e('Please enter password twice.', 'prokkat'); ?></div>
            <div class="input-wrp input-wrp_block margin-bot">
                <input type="password" class = "input_add" id="passagain" name="passagain" value=""/>
            </div>
            <div class="input-wrp_block">
                <input type="submit" class = "save-btn" name="edit_profile" value="<?php _e('Update', 'prokkat'); ?>" />
            </div>
        </div>
    </form>
  </div>