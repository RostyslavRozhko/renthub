<?php

  global $wpdb, $cc_leads_tbl_name, $current_user;
  $update_msg = '';

  if (isset($_REQUEST['action']) && $_REQUEST['action'] == "delete" && isset($_REQUEST['pid'])) {
    $id = $_REQUEST['pid'];
    delete_images( get_post_meta( $id, 'img1', true ));
    delete_images( get_post_meta( $id, 'img2', true ));
    delete_images( get_post_meta( $id, 'img3', true ));
    wp_delete_post($id);
	
    $update_msg = __('An ad has been deleted', 'prokkat');
  }

  if (isset($_REQUEST['action']) && $_REQUEST['action'] == "draft" && isset($_REQUEST['pid'])) {   
    $id = $_REQUEST['pid'];    
    wp_update_post(array('ID' => $id, 'post_status' => 'draft'));
    $update_msg = __('Your ad was added to archive', 'prokkat');
  }

  if (isset($_REQUEST['action']) && $_REQUEST['action'] == "duplicate" && isset($_REQUEST['pid'])) {   
    $post_id = $_REQUEST['pid'];    
    $post = get_post( $post_id );
    $new_post_author = $current_user->ID;
    $new_post_name = $post->post_title . " " . __('copy', 'prokkat');

    $cc_my_post = array(
      'post_title' => $new_post_name,
      'post_status' => 'draft',
      'post_type' => POST_TYPE,
      'post_author'    => $new_post_author,
			'post_content'   => $post->post_content,
    );
    $new_post_id = wp_insert_post($cc_my_post);

    $taxonomies = get_object_taxonomies($post->post_type);
		foreach ($taxonomies as $taxonomy) {
			$post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
			wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
    }
    
    $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
		if (count($post_meta_infos)!=0) {
			$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
			foreach ($post_meta_infos as $meta_info) {
				$meta_key = $meta_info->meta_key;
				if( $meta_key == '_wp_old_slug' ) continue;
				$meta_value = addslashes($meta_info->meta_value);
				$sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
			}
			$sql_query.= implode(" UNION ALL ", $sql_query_sel);
			$wpdb->query($sql_query);
		}

    $update_msg = __('Your ad was duplicated', 'prokkat');    
  }
    
	$featured_home = isset( $_POST['feature_h'] ) ? 'on' : '';
	$featured_cate = isset( $_POST['feature_c'] ) ? 'on' : '';

?>
        <?php if( $update_msg ) echo notification_markup( $update_msg ); ?>
	
        <div class="profile profile-main">
            <div class="profile__add text-center">
                <a href="<?php echo site_url('new/'); ?>" class="login__item login__item__yellow">
                      <i class="fa fa-plus" aria-hidden="true"></i>
                        <?php echo __('Add ads', 'prokkat'); ?>
                </a>
            </div>
       <?php
             $user_id = get_current_user_id();
             $limit = get_option('posts_per_page');
             $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
             $post_type = POST_TYPE;
             query_posts(array(
                "post_type" => $post_type,
                "posts_per_page" => $limit,
                "paged" => $paged,
                "author" => $user_id,
                'post_status' => 'publish, pending',
              ));

             if (have_posts()) : ?>
              <div class="list__item__header">
                <div class="list__item__date-header list__item__header-text"><?php echo __('Date', 'prokkat'); ?></div>
                <div class="list__item__img-header list__item__header-text"><?php echo __('Image', 'prokkat'); ?></div>
                <div class="list__item__price-header list__item__header-text"><?php echo __('Price', 'prokkat'); ?></div>
              </div>
                <?php while (have_posts()): the_post();
                ?>
                  <?php $date = get_the_date(); ?>
                  <div class="list__item">
                    <div class="list__item__date"><?php echo the_time(); ?></br><?php echo $date; ?></div>
                    <div class="list__item__content">
                      <img src="<?php echo ad_thumbnail_url(); ?>" class="list__item__image" />
                      <div class="list__item__title-container">
                        <a href="<?php echo get_permalink(); ?>" class="list__item__title"><?php echo pll_title() ?></a>
                        <div class="list__itme__status">
                          <span class="list__item__status-title"><?php echo __('Status', 'prokkat'); ?>:</span>
                          <span class="list__item__status-value"><?php
                                if ($post->post_status == 'publish')
                                  echo __('Approved', 'prokkat'); 
                                elseif ($post->post_status == 'pending')
                                  echo __('Pending for Approval', 'prokkat'); 
                          ?>
                        </div>
                      </div>
                    </div>
                    <div class="list__item__price"><?php echo price_output(); ?></div>
                    <div class="list__item__menu">
                    <div class="dropopen">
                      <a href="#0" class="dropopen_link list__item__3dot-container" id="dashDrop">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/menu-2.svg" class="list__item__3dot" />
                      </a>
                        <ul class="dashdrop mydropmenu">
                          <li class="dropdown__item">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/magnifying-glass.svg" class="dropdown-icon" />
                            <a href="<?php echo get_permalink(); ?>"><?php echo __('Review', 'prokkat'); ?></a>
                          </li>
                          <li class="dropdown__item">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/edit.svg" class="dropdown-icon" />
                            <a href="<?php echo add_query_arg( array( 'action'=>'edit', 'pid'=>$post->ID ), site_url( 'dashboard/' )); ?>"><?php echo __('Edit', 'prokkat'); ?></a>
                          </li>
                          <li class="dropdown__item dashdrop__exit">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/power-button.svg" class="dropdown-icon" />
                            <a href="<?php echo add_query_arg( array( 'action'=>'draft', 'pid'=>$post->ID ), site_url( 'dashboard/' )); ?>"><?php echo __('Draft', 'prokkat'); ?></a>
                          </li>
                          <li class="dropdown__item">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/copy.svg" class="dropdown-icon" />
                            <a href="<?php echo add_query_arg( array( 'action'=>'duplicate', 'pid'=>$post->ID ), site_url( 'dashboard/' )); ?>"><?php echo __('Duplicate', 'prokkat'); ?></a>
                          </li>
                          <li class="dropdown__item dropdown__item__red">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/cancel-2.svg" class="dropdown-icon" />
                            <a class="delete-ad fancybox" data-link = "<?php echo add_query_arg( array( 'action'=>'delete', 'pid'=>$post->ID ), site_url( 'dashboard/' )); ?>" href="#modalDel"><?php echo __('Delete', 'prokkat'); ?></a>
                          </li>
                        </ul>
                    </div>
                    </div>
                  </div>
              <?php endwhile; ?>

              <?php 
                the_posts_pagination( array(
                  'mid_size'  => 2,
                  'prev_text' => __( 'Back', 'prokkat' ),
                  'next_text' => __( 'Onward', 'prokkat' ),
                ) ); 
              ?>
			  
          <?php else: ?>
        
		      <div class="mess-info mess-info_center">
            <?php echo __('You currently have no ads', 'prokkat'); ?>
          </div>

          <?php endif; ?>

          <?php wp_reset_query(); ?>
		 
        </div>
        
		<div class="modal modal_del hide" id="modalDel">
          <div class="modal__title modal__title_del"><?php echo __('Do you really want to delete your ad?', 'prokkat'); ?></div>
            <div class="modal__body modal__body_del">
              <a href="#0" class="btn btn_flat btn_del"><?php echo __('Delete', 'prokkat'); ?></a>
              <a href="#0" class="btn btn_flat btn_nodel"><?php echo __('Later', 'prokkat'); ?></a>
           </div>
        </div>
