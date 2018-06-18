<?php

  global $wpdb, $expiry_tbl_name, $current_user;
  $update_msg = '';

  if (isset($_REQUEST['action']) && $_REQUEST['action'] == "expire" && isset($_REQUEST['pid'])) {
	$id = $_REQUEST['pid'];
	delete_images( get_post_meta( $id, 'img1', true ));
	delete_images( get_post_meta( $id, 'img2', true ));
	delete_images( get_post_meta( $id, 'img3', true ));
	wp_delete_post($id);
	
    $update_msg = _e('An ad has been deleted', 'prokkat');
  }

  if( isset($_REQUEST['pid']) && $_REQUEST['pid'] && $_REQUEST['action'] == "renew" ) {
      $renew_response = custom_renew_listing($_REQUEST['pid']);
      if($renew_response == true){
        $update_msg = __('Your ad was renewed', 'prokkat');
      }
  }
?>

    <div class="add__title"><?php _e('Your Expired Ads', 'prokkat'); ?></div>
    
    <?php if($update_msg){ ?>
    <div class="notification mess-info mess-info_center1111111111111111111111111111">
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

        <div class="profile profile-main">
          <?php
            $post_type = POST_TYPE;
            $query = $wpdb->query("SELECT " . $wpdb->posts . ".*  FROM " . $wpdb->posts . " WHERE " . $wpdb->posts . ".post_author = $current_user->ID AND " . $wpdb->posts . ".post_type = '$post_type' AND (" . $wpdb->posts . ".post_status = 'draft') ORDER BY  " . $wpdb->posts . ".`ID` DESC"); // Get total of Num rows from the database query
            if (isset($_GET['pn'])) { // Get pn from URL vars if it is present
                $pn = preg_replace('#[^0-9]#i', '', $_GET['pn']); // filter everything but numbers for security(new)
            } else { // If the pn URL variable is not present force it to be value of page number 1
                $pn = 1;
            }

            $itemsPerPage = 20;

            $lastPage = ceil($query / $itemsPerPage);

            if ($pn < 1) { // If it is less than 1
                $pn = 1; // force if to be 1
            } else if ($pn > $lastPage) { // if it is greater than $lastpage
                $pn = $lastPage; // force it to be $lastpage's value
            }

            $centerPages = "";
            $sub1 = $pn - 1;
            $sub2 = $pn - 2;
            $add1 = $pn + 1;
            $add2 = $pn + 2;
            if ($pn == 1) {
                $centerPages .= '<li><a class="current" href="">'.$pn.'</a></li>';
                $centerPages .= '<li><a href="' . site_url(CC_DASHBOARD . "/?action=expire&pn=$add1") . '">' . $add1 . '</a></li>';
            } else if ($pn == $lastPage) {
                $centerPages .= '<li><a href="' . site_url(CC_DASHBOARD . "/?action=expire&pn=$sub1") . '">' . $sub1 . '</a></li>';
                $centerPages .= '<li><a class="current" href="">'.$pn.'</a></li>';
            } else if ($pn > 2 && $pn < ($lastPage - 1)) {
                $centerPages .= '<li><a href="' . site_url(CC_DASHBOARD . "/?action=expire&pn=$sub2") . '">' . $sub2 . '</a></li>';
                $centerPages .= '<li><a href="' . site_url(CC_DASHBOARD . "/?action=expire&pn=$sub1") . '">' . $sub1 . '</a></li>';
                $centerPages .= '<li><a class="current" href="">'.$pn.'</a></li>';
                $centerPages .= '<li><a href="' . site_url(CC_DASHBOARD . "/?action=expire&pn=$add2") . '">' . $add1 . '</a></li>';
                $centerPages .= '<li><a href="' . site_url(CC_DASHBOARD . "/?action=expire&pn=$add2") . '">' . $add2 . '</a></li>';
            } else if ($pn > 1 && $pn < $lastPage) {
                $centerPages .= '<li><a href="' . site_url(CC_DASHBOARD . "/?action=expire&pn=$sub1") . '">' . $sub1 . '</a></li>';
                $centerPages .= '<li><a class="current" href="">'.$pn.'</a></li>';
                $centerPages .= '<li><a href="' . site_url(CC_DASHBOARD . "/?action=expire&pn=$add1") . '">' . $add1 . '</a></li>';
            }

            $limit = 'LIMIT ' . ($pn - 1) * $itemsPerPage . ',' . $itemsPerPage;

            $paginationDisplay = "<ul class='paginate'>"; // Initialize the pagination output variable
            if ($lastPage != "1") {
                //$paginationDisplay .= 'Page <strong>' . $pn . '</strong> of ' . $lastPage . '&nbsp;  &nbsp;  &nbsp; ';

                if ($pn != 1) {
                    $previous = $pn - 1;
                    $paginationDisplay .= '<li><a href="' . site_url(CC_DASHBOARD . "/?action=expire&pn=$previous") . '"> Back</a></li>';
                }
                $paginationDisplay .= $centerPages;
                if ($pn != $lastPage) {
                    $nextPage = $pn + 1;
                    $paginationDisplay .= '<li><a href="' . site_url(CC_DASHBOARD . "/?action=expire&pn=$nextPage") . '"> Next</a></li>';
                }
            }
            $paginationDisplay .= '</ul>';
			
            $expires = $wpdb->get_results("SELECT " . $wpdb->posts . ".*  FROM " . $wpdb->posts . " WHERE " . $wpdb->posts . ".post_author = $current_user->ID AND " . $wpdb->posts . ".post_type = '$post_type' AND (" . $wpdb->posts . ".post_status = 'draft') ORDER BY  " . $wpdb->posts . ".`ID` DESC $limit");
            if ($expires): ?>
                <div class="list__item__header">
                    <div class="list__item__date-header list__item__header-text"><?php echo __('Date', 'prokkat'); ?></div>
                    <div class="list__item__img-header list__item__header-text"><?php echo __('Image', 'prokkat'); ?></div>
                    <div class="list__item__price-header list__item__header-text"><?php echo __('Price', 'prokkat'); ?></div>
                </div>
               <?php foreach ($expires as $expire):
                $categories = get_the_term_list($expire->ID, CUSTOM_CAT_TYPE, '', ', ', '');
              ?>

                <div class="list__item">
                    <div class="list__item__date"><?php echo the_time(); ?></br><?php echo get_the_date(); ?></div>
                    <div class="list__item__content">
                      <img src="<?php echo ad_thumbnail_url($expire->ID); ?>" class="list__item__image" />
                      <div class="list__item__title-container">
                        <a href="<?php echo get_permalink($expire->ID); ?>" class="list__item__title"><?php echo pll_title($expire->ID ); ?></a>
                        <div class="list__itme__status">
                          <span class="list__item__status-title"><?php echo __('Status', 'prokkat'); ?>:</span>
                          <span class="list__item__status-value"><?php echo __('Draft', 'prokkat'); ?></span>
                        </div>
                      </div>
                    </div>
                    <div class="list__item__price"><?php echo price_output($expire->ID ); ?></div>
                    <div class="list__item__menu">
                    <div class="dropopen">
                      <a href="#0" class="login__item dropopen_link list__item__3dot-container" id="dashDrop">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/menu-2.svg" class="list__item__3dot" />
                      </a>
                        <ul class="dashdrop mydropmenu">
                          <li class="dropdown__item">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/magnifying-glass.svg" class="dropdown-icon" />
                            <a href="<?php echo get_permalink($expire->ID); ?>"><?php echo __('Review', 'prokkat'); ?></a>
                          </li>
                          <li class="dropdown__item">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/edit.svg" class="dropdown-icon" />
                            <a href="<?php echo add_query_arg( array( 'action'=>'edit', 'pid'=>$expire->ID ), site_url( 'dashboard/' )); ?>"><?php echo __('Edit', 'prokkat'); ?></a>
                          </li>
                          <li class="dropdown__item dashdrop__exit">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/power-button.svg" class="dropdown-icon" />
                              <a href="<?php echo add_query_arg( array( 'action'=>'renew', 'pid'=>$expire->ID ), site_url( 'dashboard/' )); ?>"><?php echo __('Renew', 'prokkat'); ?></a>
                          </li>
                          <li class="dropdown__item dropdown__item__red">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/cancel-2.svg" class="dropdown-icon" />
                            <a  class="delete-ad fancybox" data-link = "<?php echo add_query_arg( array( 'action'=>'delete', 'pid'=>$expire->ID ), site_url( 'dashboard/' )); ?>" href="#modalDel"><?php echo __('Delete', 'prokkat'); ?></a>
                          </li>
                        </ul>
                    </div>
                    </div>
                  </div>
              <?php endforeach; ?>
			  
              <?php else: ?>
			  
                <div class="mess-info mess-info_center">
                    <?php _e('Archive is empty', 'prokkat'); ?>
                </div>
				
              <?php endif; ?>
        </div>
		
		<div class="modal modal_del hide" id="modalDel">
          <div class="modal__title modal__title_del"><?php echo __('Do you really want to delete your ad?', 'prokkat'); ?></div>
            <div class="modal__body modal__body_del">
              <a href="#0" class="btn btn_flat btn_del"><?php echo __('Delete', 'prokkat'); ?></a>
              <a href="#0" class="btn btn_flat btn_nodel"><?php echo __('Later', 'prokkat'); ?></a>
           </div>
        </div>
