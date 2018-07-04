<?php

  $pid = $_REQUEST['pid'];
  global $wpdb, $user_ID, $post;
  $update_msg = '';

  $post_obj = get_post( $pid );

  if (isset($_POST['update'])) {
    $fields = array(
	  'cc_title',
	  'cc_description',
      'cc_category',
	  'cc_price',
      'cc_price_week',
      'cc_price_more',
      'cc_price_deposit',
      'cc_state',
	  'custom-price',
      'cc_address_list',
      'cc_city_id',
      'cc_locations',
      'img1',
      'img2',
      'img3'
    );

    $filters = get_terms( array('taxonomy' => 'tags', 'hide_empty' => false));

    foreach($filters as $filter) {
        $fields[] = $filter->slug;
    }

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $posted[$field] = stripcslashes(trim($_POST[$field]));
        }
    }
    
	$category = array_map('intval', explode(',', $posted['cc_category']));

    //$post_status = "pending";
    $post_status = "publish";
    $cc_my_post['ID'] = $pid;
    $cc_my_post['post_title'] = esc_attr($posted['cc_title']);
    $cc_my_post['post_content'] = $posted['cc_description'];
    $cc_my_post['post_status'] = $post_status;
    $cc_my_post['post_author'] = $user_ID;
    $cc_my_post['post_type'] = POST_TYPE;
    $cc_my_post['post_category'] = $category;

    wp_update_post($cc_my_post);
    //set the categories and tags
    wp_set_object_terms($pid, $category, $taxonomy = CUSTOM_CAT_TYPE);

    update_post_meta( $pid, 'cc_address_list', $posted['cc_address_list'] );
      update_post_meta( $pid, 'cc_city_id', $posted['cc_city_id'] );
      update_post_meta( $pid, 'cc_locations', $posted['cc_locations'] );

    update_post_meta($pid, 'cc_category', $posted['cc_category']);

    update_post_meta( $pid, 'cc_state', $posted['cc_state'] );

    foreach($filters as $filter) {
        $slug = $filter->slug;
        $term_id = $filter->term_id;
        $value = $posted[$slug];
        if($value) {
            update_post_meta($pid, $slug, $posted[$slug]);
            if(isset($_POST['filter_'.$term_id])) {
                update_filter_values($term_id, $value);
            }
        }
      }

      $additional_fields = get_additional_post_fields();

      foreach($additional_fields as $field) {
        $slug = $field['slug'];
        $value = $_POST[$slug];
        if(isset($value)) {
            $cate_id = $_POST['manufacturer_id'];
            update_post_meta($pid, $slug, $value);
            update_man_list($cate_id, $value, $slug);
          }
      }
	
	if( !$posted['img1'] ) {
	  $img_url = get_post_meta( $pid, 'img1', true);
	  if( $img_url ) delete_images( $img_url );
	}
	if( !$posted['img2'] ) {
	  $img_url = get_post_meta( $pid, 'img2', true);
	  if( $img_url ) delete_images( $img_url );
	}
	if( !$posted['img3'] ) {
	  $img_url = get_post_meta( $pid, 'img3', true);
	  if( $img_url ) delete_images( $img_url );
	}
	
	update_post_meta( $pid, 'img1', $posted['img1'] );
    update_post_meta( $pid, 'img2', $posted['img2'] );
	update_post_meta( $pid, 'img3', $posted['img3'] );
	
	if( !$posted['img1'] && $posted['img2'] ) {
      update_post_meta($pid, 'img1', $posted['img2']);
	  update_post_meta($pid, 'img2', '');
    }
			  
	if( !$posted['img1'] && !$posted['img2'] && $posted['img3'] ) {
	  update_post_meta($pid, 'img1', $posted['img3']);
	  update_post_meta($pid, 'img3', '');
	}
			  
    if( isset($posted['custom-price']) ) {
      update_post_meta($pid, 'custom-price', $posted['custom-price']);
      update_post_meta($pid, 'cc_price', '');
      update_post_meta($pid, 'cc_price_week', '');
      update_post_meta($pid, 'cc_price_more', '');
      update_post_meta($pid, 'cc_price_deposit', '');
	}
    else {
	  update_post_meta($pid, 'custom-price', '');
      update_post_meta($pid, 'cc_price', $posted['cc_price']);
      update_post_meta($pid, 'cc_price_week', $posted['cc_price_week']);
      update_post_meta($pid, 'cc_price_more', $posted['cc_price_more']);
      update_post_meta($pid, 'cc_price_deposit', $posted['cc_price_deposit']);
  }
	
    $update_msg = __('Your ad has been updated.', 'prokkat');
}

  if (isset($_POST['update']) && isset($_REQUEST['pay_method']) && $_REQUEST['pay_method'] != '' && $_POST['total_price'] > 0)
  {
    $post_id = $pid;
	$post_title = $cc_my_post['post_title'];
	
    //$post_status = 'pending';
    $post_status = 'publish';

    //Updating expiry table
    if ($_POST['package_validity'] != '' && $_POST['package_validity_per'] != '') {
        global $wpdb, $expiry_tbl_name;
        $validity = $_POST['package_validity'];
        $validity_per = $_POST['package_validity_per'];
        $pkg_type = $_POST['package_type'];
        if ($pkg_type == '') {
            $pkg_type = 'pkg_free';
        }
    }
    
	$featured_home = isset( $_POST['feature_h'] ) ? 'on' : '';
	$featured_cate = isset( $_POST['feature_c'] ) ? 'on' : '';

      if( file_exists(get_stylesheet_directory().'/gateways/liqpay/liqpay.php' ))
        include_once( get_stylesheet_directory().'/gateways/liqpay/liqpay.php' );
  }

  $post_type = POST_TYPE;
  $sql = "SELECT * FROM $wpdb->posts WHERE post_type = '$post_type' AND ID = {$pid}";
  $ad = $wpdb->get_row($sql);

  require_once( get_stylesheet_directory().'/js/ad_field_validation.php' );
?>

<div class="add__title"><?php _e('Edit Ad', 'prokkat'); ?></div>

<div>
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

    <form name="edit_post_form" id="edit_post_form" method="post" enctype="multipart/form-data">
    <div class="add__step add__step__container">  
    <div class="input-wrp input-wrp_block add__block">    
          <div class="form__title req"><?php _e('Ad Title', 'prokkat'); ?></div>
          <div class="input-wrp input-wrp_block">
              <span class="max-text"><?php _e('Maximum', 'prokkat') ?> 100 <?php _e('characters', 'prokkat'); ?></span>
              <input type="text" id="title" class="input_add" name="cc_title" placeholder="<?php _e('Please enter title', 'prokkat') ?>" value="<?php echo $ad->post_title; ?>" />
          </div>
      </div>

      <div class="input-wrp input-wrp_block add__block" id=ad-categories>                            
                                <div class="col6 nopaddingl">
                                    <div class="req form__title"><?php _e('Category', 'prokkat'); ?></div>                                        
                                    <span class="cats_error"></span>
                                    <?php
                                    debug_to_console('dsadsadasd');
                                        $terms = wp_get_post_terms($pid, CUSTOM_CAT_TYPE);

                                        usort($terms, function($a, $b)
                                        {
                                            return strcmp($a->term_id, $b->term_id);
                                        });

                                        $parent = array_slice($terms, 0, 1)[0];
                                        $subcat = array_slice($terms, 1, 1)[0];

                                        $id = $parent->term_id;
                                        $child_id = $subcat->term_id;

                                        ?>
                                    <div class="input-wrp input-wrp_block ">
                                      <?php my_dropdown_categories( 'maincat', __('Category', 'prokkat'), 0, $id  ); ?>
                                    </div>
                                </div>
                                <div class="col6 nopaddingr">
                                <div class="req form__title"><?php _e('Subcategory', 'prokkat'); ?></div>
                                <div class="input-wrp input-wrp_block" id="subcat-li">
                                    <?php  if( $id != 98 ) my_dropdown_categories( 'subcat', __('Subcategory', 'prokkat'), $id, $child_id  ); ?>
                                </div>
                                </div>
                                <input id="chosenCategory" name="cc_category" type="hidden" value="<?php echo get_post_meta($pid, 'cc_category', true) ?>"/>
                            </div>
                            <div id="add_filters">
                                <?php echo my_filters_edit_list($child_id, $pid); ?>
                            </div>
                            <div class="input-wrp input-wrp_block add__block"> 
                                <div class="req form__title"><?php _e('State', 'prokkat'); ?></div>
                                    <div class="input-wrp input-wrp_block ">
                                        <fieldset class="rating">
                                            <?php $checked = get_post_meta($pid, 'cc_state', true); ?>
                                            <input type="radio" id="star5" name="cc_state" value="5" <?php if($checked == 5) echo 'checked'; ?> /><label for="star5"></label>
                                            <input type="radio" id="star4" name="cc_state" value="4" <?php if($checked == 4) echo 'checked'; ?> /><label for="star4"></label>
                                            <input type="radio" id="star3" name="cc_state" value="3" <?php if($checked == 3) echo 'checked'; ?> /><label for="star3"></label>
                                            <input type="radio" id="star2" name="cc_state" value="2" <?php if($checked == 2) echo 'checked'; ?> /><label for="star2"></label>
                                            <input type="radio" id="star1" name="cc_state" value="1" <?php if($checked == 1) echo 'checked'; ?> /><label for="star1"></label>
                                        </fieldset>
                                    </div>
                            </div>
                            <div id="price-block">
                            <div class="input-wrp input-wrp_block add__block">
                                <div class="col6 nopaddingl">
                                    <div class="req form__title"><?php _e('Rent price for 1 to 4 days, UAH', 'prokkat'); ?></div>
                                    <div class="input-wrp input-wrp_block">
                                        <input type="number" type="text" class="input_add" placeholder="<?php _e('Please enter price', 'prokkat'); ?>" min="1" id="cc_price" name="cc_price" value="<?php echo get_post_meta($pid, 'cc_price', true); ?>">
                                    </div>
                                </div>
                                <div class="col6 nopaddingr">
                                    <div class="req form__title"><?php _e('Deposit size, UAH', 'prokkat'); ?></div>
                                    <div class="input-wrp input-wrp_block">
                                        <input type="number" type="text" class="input_add" placeholder="<?php _e('Please enter price', 'prokkat'); ?>" min="1" id="cc_price_deposit" name="cc_price_deposit" value="<?php echo get_post_meta($pid, 'cc_price_deposit', true); ?>">  
                                    </div>
                                </div>

                            </div>
                            <div class="input-wrp input-wrp_block add__block">
                                <div class="col6 nopaddingl">
                                    <div class="req form__title"><?php _e('Rent price for 10+ days, UAH', 'prokkat'); ?></div>
                                    <div class="input-wrp input-wrp_block">
                                        <input type="number" type="text" class="input_add" placeholder="<?php _e('Please enter price', 'prokkat'); ?>" min="1" id="cc_price_more" name="cc_price_more" value="<?php echo get_post_meta($pid, 'cc_price_more', true); ?>">
                                    </div>
                                </div>
                                <div class="col6 nopaddingr">
                                    <div class="req form__title"><?php _e('Rent price for 5 to 7 days, UAH', 'prokkat'); ?></div>
                                    <div class="input-wrp input-wrp_block">
                                        <input type="number" type="text" class="input_add" placeholder="<?php _e('Please enter price', 'prokkat'); ?>" min="1" id="cc_price_week" name="cc_price_week" value="<?php echo get_post_meta($pid, 'cc_price_week', true); ?>">  
                                    </div>
                                </div>
                            </div>
                            </div>
                            <!-- <div class="add__block">
                              <label>
                                  <input type="checkbox" class="add__check" value="1" name="custom-price" id="custom-price" <?php if( get_post_meta($pid, 'custom-price', true) ) echo 'checked="checked"'; ?>>Ціна за домовленістю
                              </label>
                            </div> -->
    
        <div class="req form__title"><?php echo __('Description', 'prokkat'); ?></div>
        <div class="input-wrp input-wrp_block">
        <span class="max-text"><?php _e('Maximum', 'prokkat') ?> 1000 <?php _e('characters', 'prokkat'); ?></span>
          <span class="desc_error"></span>
            <textarea id="cc_desc" class="textarea textarea_mess" name="cc_description"><?php echo $ad->post_content; ?></textarea>
        </div>
      </div>
        <div class="add add__step add__step__container">
                        <div class="step__title"><?php echo __('Add photo', 'prokkat'); ?></div>
                        <span class="photo_error"></span>
						<?php $id1 = "img1"; $id2 = "img2"; $id3 = "img3"; ?>

                        <div class="add__photo-grid-container">
					    <div class="add__upload_big ">
                            <div class="upload_img-wrp">
                                <div class="add__upload">
                                    <div class="add__upload-text">
                                        <div class="div add__photo-title"><?php _e('Upload photos', 'prokkat'); ?></div>
                                        <p><?php _e('Upload photo of your own sweets to gallery. Remember that the better the picture - the more people interested in your services', 'prokkat'); ?></p>
                                        <?php my_photo_markup( $id1, get_post_meta( $pid, $id1, true )); ?>
                                    </div>
                                </div>
								<div class="plupload-thumbs hide" id="<?php echo $id1; ?>plupload-thumbs">
                                </div>
                            </div>
                        </div>

                            <div class="small-top">
                                <div class="upload_img-wrp">
                                <?php my_photo_markup( $id2, get_post_meta( $pid, $id2, true ) ); ?>
                                  <div class="plupload-thumbs hide" id="<?php echo $id2; ?>plupload-thumbs">
                                  </div>
                                </div>
                            </div>

                            <div class="small-bot">
                                <div class="upload_img-wrp">
                                <?php my_photo_markup( $id3, get_post_meta( $pid, $id3, true ) ); ?>
                                  <div class="plupload-thumbs hide" id="<?php echo $id3; ?>plupload-thumbs">
                                  </div>
                                </div>
                            </div>
                        </div>

      </div>
       
        <div class="add__form-item_contact add__step add__step__container">
                            <div class="step__title"><?php _e('Where is instruments?', 'prokkat'); ?></div>                            
							<div id='address_list'></div>                            
                            <div class="req form__title"><?php _e('Location', 'prokkat'); ?></div>
                            <div class="input-wrp input-wrp_block">
								<input type="hidden" name="cc_address_list" id="cc_address_list" value='<?php echo get_post_meta($pid, 'cc_address_list', true); ?>' />
                                <input type="hidden" name="cc_city_id" id="cc_city_id" value='<?php echo get_post_meta($pid, 'cc_city_id', true); ?>'/>
                                <input type="hidden" name="cc_locations" id="cc_locations" value='<?php echo get_post_meta($pid, 'cc_locations', true); ?>'/>
                                <div class="address-input__container">
                                    <input type="text" name="cc_address" id="cc_address" class="input_add noEnterSubmit" value='' placeholder="<?php _e('Address', 'prokkat'); ?>" />
                                    <input type="button" id="add_address" value="<?php _e('Add', 'prokkat'); ?>" />
                                </div>
			                    <div id="map_canvas" style="height:350px; margin-top: 10px; position:relative;"  class="form_row clearfix"></div>
			                    <?php edit_map(); ?>
                            </div>
                        </div>
        
     <div class="add__step__container last__step__margin">
                        <?php 
                            // get_post_meta( $ad->ID, 'cc_add_type', true ) == 'free'
                            if( false ) : ?>
                        <div class="input-wrp input-wrp_block">
                            <div class="input-wrp input-wrp_block">                                            
                                <label><input type="checkbox" class= "add__check" id="be_paid" name="be_paid" />Зробити Преміум</label> <br/>
                            </div>
                        </div>
                    <?php endif; ?>
                  
                    <div style="display:none;" id="upgrade_form">
                  
                    <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/js/package_price.js"></script>
                      <script type="text/javascript">
                        jQuery(document).ready(function(){
                          jQuery('input:checkbox[name=be_paid]').prop('checked', false).trigger('refresh');
                            jQuery('input:checkbox[name=be_paid]').change(function(){  
                
                                if(this.checked){
                                    jQuery("#upgrade_form").slideDown();
                                } 
                                else{
                                    jQuery("#upgrade_form").slideUp();
                                }
                            });         
                                                                          
                        });
                      </script>
                  <?php
                    
                    global $wpdb, $price_table_name;
                    $packages = $wpdb->get_results("SELECT * FROM $price_table_name WHERE status=1");
                    if ($packages):
                      foreach ($packages as $package):
                        $valid_to = $package->validity_per;
                        if ($valid_to == 'D') $valid_to = __( "днів", 'cc' );
                        if ($valid_to == 'M') $valid_to = __( "місяців", 'cc' );
                        if ($valid_to == 'Y') $valid_to = __( "років", 'cc' );
                        
                    if ($package->package_type == 'pkg_free') continue;
                  ?>
                          <div class="add__seo">
                            <div class="inline-wrp">
                              <input type="checkbox" value="<?php echo $package->package_cost; ?>" name="price_select" id="<?php echo $package->package_type; ?>" class="add__check">
                              <input type="hidden" class="<?php echo $package->package_type; ?>" name="<?php echo $package->package_type; ?>" value="<?php echo $package->validity; ?>"/>
                              <input type="hidden" class="<?php echo $package->package_type_period; ?>" name="<?php echo $package->package_type_period; ?>" value="<?php echo $package->validity_per; ?>"/>
                              <input type="hidden" class="is_recurring" name="is_recurring" value="<?php echo $package->is_recurring; ?>"/>
                              <input type="hidden" class="validity" name="validity" value="<?php echo $package->validity; ?>"/>
                              <input type="hidden" class="validity_per" name="validity_per" value="<?php echo $package->validity_per; ?>"/>
                              <input type="hidden" class="pkg_type" name="pkg_type" value="<?php echo $package->package_type; ?>"/>
                              <input type="hidden" class="is_featured" name="is_featured" value="<?php echo $package->is_featured; ?>"/>                            
                              <input type="hidden" id="price_title" name="price_title" value="<?php echo $package->price_title; ?>"/>
                        
                              <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/crown.svg" class="add__seo__icon" />
                        
                              <label class="big-label" for="<?php echo $package->package_type; ?>">
                                <span class="big-label__title"><?php echo stripslashes(__($package->price_title,'cc')); ?></span>
                                <p><?php echo __( 'Термін дії :', 'cc' ) .'&nbsp;'. $package->validity .'&nbsp;'. $valid_to . '<br>' . stripslashes(__($package->price_desc, 'cc')); ?></p>
                              </label>
                              <div class="add__seo__price">
                                  <?php echo $package->package_cost; ?> грн
                                </div>
                                <div class="big-label__option" id="featured">
                                <label>
							        <input type="checkbox" id="feature_h" name="feature_h" class="add__check" value="<?php echo $package->feature_amount; ?>"><?php echo __( 'Відображати як преміум на головній сторінці сайту', 'cc' ).' <b>+<span id="fhome">'.$package->feature_amount.'</span> '.cc_get_option('cc_currency').'</b>'; ?>
								</label>
                                <label>
                                    <input type="checkbox" id="feature_c" name="feature_c" class="add__check" value="<?php echo $package->feature_cat_amount; ?>"><?php echo __( 'Відображати як преміум на сторінці категорії', 'cc' ).' <b>+<span id="fhome">'.$package->feature_cat_amount.'</span> '.cc_get_option('cc_currency').'</b>'; ?>
								</label>
                            </div>
                            </div>
                          </div>
                      
                          <?php endforeach; endif; ?>
                
                            <div class="form_row">
                              <div class="label">
                                <input type="hidden" name="total_price" id="total_price" value="0"/>  
                                <input type="hidden" name="package_title" id="package_title" value=""/>
                                <input type="hidden" name="package_validity" id="package_validity" value=""/>
                                <input type="hidden" name="package_validity_per" id="package_validity_per" value=""/>
                                <input type="hidden" name="package_type" id="package_type" value=""/>
                              </div>
                            </div>
                          </div>
                
                          <div class="input-wrp input-wrp_block">
                        <div id="btnsend" class="btnsend">
                          <a href="#0" class="btn btn_lblue" id="update"><?php _e('Update', 'prokkat'); ?></a>
                        <input type="button" class="btn btn_lblue btn_cancel" onclick="window.location.href='<?php echo CC_DASHBOARD ?>'" value="<?php _e('Cancel', 'prokkat'); ?>" />
                      </div>
                      <div id="btnpay" class="hide">
                              <a href="#0" class="btn btn_green" id="cash">LIQPAY: Оплатити (<span></span> грн)</a>
                        <input type="button" class="btn btn_lblue btn_cancel" onclick="window.location.href='<?php echo CC_DASHBOARD ?>'" value="<?php _e('Cancel', 'prokkat'); ?>" />
                              <div id="pay-logos" class="add__step text-center">
                                <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/visa.png" alt="visa" class="img-responsive inline">
                              </div>
                            </div>
                          </div>
                      
                      <input type="hidden" name="update" value="" />
                      
                      <ul id="payments">
                            <li id="liqpay">
                              <label class="r_lbl"><input  type="hidden" value="liqpay" id="liqpay_id" name="pay_method" checked="checked" /></label>
                            </li>
                          </ul>
                      </div>
	  
    </form>
      </div>