<?php
/**
 * Template Name: Template Dashboard 
 */
auth_redirect_home();
get_header();
$id = 'ava';
$svalue = get_user_meta($current_user->ID, 'user_avatar', true);
?>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/libs/jquery-editable-select.js"></script>
<link href="<?php echo get_stylesheet_directory_uri(); ?>/css/jquery-editable-select.css" rel="stylesheet">

<section class="profile_color">
    <div class="container">
        <div class="profile_col4 nopaddingl">
            <div class="profile profile_side">
                <div class="profile__logo">
				
                    <div class="author__img">
					  <input type="hidden" name="<?php echo $id; ?>" id="<?php echo $id; ?>" value="<?php echo $svalue; ?>" />
                      <div class="plupload-upload-uic hide-if-no-js" id="<?php echo $id; ?>plupload-upload-ui">
					    <a id="<?php echo $id; ?>plupload-browse-button" href="#0" class="btn_add-img ">
                        <div class="plupload-thumbs" id="<?php echo $id; ?>plupload-thumbs">
						</div>
			            </a>
						<span class="ajaxnonceplu" id="ajaxnonceplu<?php echo wp_create_nonce($id . 'pluploadan'); ?>"></span>
                        <div class="filelist"></div>
                      </div>
                    </div>
					
                    <div class="profile__title"><?php echo $current_user->nickname; ?></div>
                    <div class="profile__date"><?php echo __('Member since:', 'prokkat'); ?> <?php
                      $registered = ($current_user->user_registered . "\n");
                      echo date("d.m.y", strtotime($registered)); ?>
					  
                    </div>
                    <a href="<?php echo get_author_posts_url( $current_user->ID ); ?>" class="profile__show"><?php echo __('View profile', 'prokkat'); ?></a>                    
                    <a href="<?php echo add_query_arg( array( 'action'=>'profile' ), site_url( 'dashboard/' )); ?>" class="profile__edit">
                      <img src ="<?php echo get_stylesheet_directory_uri(); ?>/img/edit.svg" class="profile__edit-icon" />
                    </a>
                </div>
                <ul class="profile__menu">
                    <li class="profile__menu-item"><a href="<?php echo site_url('new/'); ?>"><?php echo __('Post an Ad', 'prokkat'); ?></a></li>
                    <li class="profile__menu-item <?php if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'view') { echo 'profile__menu-selected';} ?>"><a href="<?php echo my_action_url('view'); ?>"><?php echo __('My Ads', 'prokkat'); ?></a></li>
                    <li class="profile__menu-item <?php if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'expire') { echo 'profile__menu-selected';} ?>"><a href="<?php echo my_action_url('expire'); ?>"><?php echo __('View Expired Ads', 'prokkat'); ?></a></li>
                    <li class="profile__menu-item <?php if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'messagebox') { echo 'profile__menu-selected';} ?>"><a href="<?php echo my_action_url('messagebox'); ?>">
					  <?php
					    echo __( 'My messages', 'prokkat' );
						$style = 'display:inline-block';
                        $new_messages_num = fep_get_new_message_number();
					    if( !$new_messages_num ) $style = 'display:none;'; ?>
						
					  <span class="badge badge_menu" style="<?php echo $style; ?>"><?php echo $new_messages_num; ?></span>
					</a></li>
                    <li class="profile__menu-item <?php if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'profile') { echo 'profile__menu-selected';} ?>"><a href="<?php echo my_action_url('profile'); ?>"><?php echo __('Edit Profile', 'prokkat'); ?></a></li>
                    <li class="profile__menu-item <?php if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'changepass') { echo 'profile__menu-selected';} ?>"><a href="<?php echo my_action_url('changepass'); ?>"><?php echo __('Change password', 'prokkat'); ?></a></li>
                    <li class="profile__menu-item"><a href="<?php echo wp_logout_url(site_url()); ?>"><?php echo __('Log out', 'prokkat'); ?></a></li>
                    
                </ul>
            </div>
        </div>
		
		<div class="profile_col8 nopaddingr">
		<?php

          //Loading author's listing in dashboard
          if (!isset($_REQUEST['action']) || $_REQUEST['action'] == 'view' || $_REQUEST['action'] == 'draft' || $_REQUEST['action'] == 'publish' || $_REQUEST['action'] == 'delete' || $_REQUEST['action'] == 'duplicate') {
            if (file_exists(get_stylesheet_directory() . '/dashboard.php')) {
              include_once(get_stylesheet_directory() . '/dashboard.php');
            }
          }
          //Loading author's listing for edit
          if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit') {
            if (file_exists(get_stylesheet_directory() . '/edit_ad.php')) {
              include_once(get_stylesheet_directory() . '/edit_ad.php');
            }
          }
          //Loading cmments file
          if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'comment') {
            if (file_exists(get_stylesheet_directory() . '/view_comments.php')) {
              include_once(get_stylesheet_directory() . '/view_comments.php');
            }
          }
          //Showing user profile
          if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'profile') {
            if (file_exists(get_stylesheet_directory() . '/edit_profile.php')) {
              include_once(get_stylesheet_directory() . '/edit_profile.php');
            }
          }
          //Showing leads
          if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'lead') {
            if (file_exists(get_stylesheet_directory() . '/cc_show_leads.php')) {
              include_once(get_stylesheet_directory() . '/cc_show_leads.php');
            }
          }
          //Showing expired ads
          if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'expire' || $_REQUEST['action'] == 'renew') {
            if (file_exists(get_stylesheet_directory() . '/cc_show_expire_ads.php')) {
              include_once(get_stylesheet_directory() . '/cc_show_expire_ads.php');
            }
          }
		  //Showing user messages
          if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'messagebox') {
            echo my_message_box();
          }
		  //Showing message
          if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'viewmessage') {
            echo my_view_message();
          }
		  //Delete message
          if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'deletemessage') {
            echo my_delete_corr() . my_message_box();
          }
		  //Change password
          if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'changepass') {
            if (file_exists(get_stylesheet_directory() . '/changepass.php')) {
              include_once(get_stylesheet_directory() . '/changepass.php');
            }
          }
        ?>
		</div>
    </div>
</section>

<div class="modal modal_del hide" id="modalDel">
  <div class="modal__title modal__title_del"><?php echo __( 'Do you really want to delete this dialogue?', 'cc' ); ?></div>
  <div class="modal__body modal__body_del">
    <a href="#0" class="btn btn_flat btn_del"><?php echo __( 'Delete', 'cc' ); ?></a>
    <a href="#0" class="btn btn_flat btn_nodel"><?php echo __( 'Later', 'cc' ); ?></a>
  </div>
</div>
<?php get_footer(); ?>