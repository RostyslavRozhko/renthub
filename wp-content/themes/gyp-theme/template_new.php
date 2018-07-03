<?php
/**
 * Template Name: Template New 
 */
nocache_headers();
if (!is_user_logged_in()) {
	wp_redirect(site_url() . '/login') ;
}
function cc_submit_form_process() {
    $posted = array();

    $fields = array(
        'cc_title',
        'cc_description',
        'cc_category',
        'cc_price',
        'cc_price_week',
        'cc_price_more',
        'cc_price_deposit',
        'custom-price',
        'cc_address_list',
        'cc_city_id',
        'cc_locations',
        'cc_state',
        'img1',
        'img2',
        'img3',
        'manufacturer',
        'manufacturer_id'
    );

    $filters = get_terms( array('taxonomy' => 'tags', 'hide_empty' => false));
    foreach($filters as $filter) {
        $fields[] = $filter->slug;
    }

    //Fecth form values
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            $posted[$field] = stripcslashes(trim($_POST[$field]));
        }
    }

    $errors = new WP_Error();

    $submit_form_results = array(
        'errors' => $errors,
        'posted' => $posted
    );
    return $submit_form_results;
}

global $user_ID;
$posted = array();
$errors = new WP_Error();

$value = cc_submit_form_process();

$errors = $value['errors'];
$posted = $value['posted'];

$success = false;

if ($errors && sizeof($errors) > 0 && $errors->get_error_code()) {


} else {
    $check_submit = get_option('cc_check_submit');
    if( isset($_REQUEST['cc_check_submit']) && $_REQUEST['cc_check_submit'] != $check_submit ) {
	    //isset($_POST['cc_submit']) && $_POST['cc_submit'] ) {

            //Approval needed
            if (isset($_REQUEST['pay_method']) && $_POST['total_price'] > 0) {
                //$post_status = 'pending';
                $post_status = 'publish';
            } elseif (!isset($pay_method) && $_POST['total_price'] == 0) {
                $status = strtolower(cc_get_option('cc_freead'));
                if ($status == 'pending'):
                    $post_status = 'pending';
                endif;
                if ($status == 'publish'):
                    $post_status = 'publish';
                endif;
                if (!$status) {
                    $post_status = 'pending';
                }
            }

			$post_title = $wpdb->escape($posted['cc_title']);

            ## Create Post
            $category = array_map('intval', explode(',', $posted['cc_category']));
            $cc_my_post = array(
                'post_content' => $wpdb->escape($posted['cc_description']),
                'post_title' => $post_title,
                'post_status' => $post_status,
                'post_author' => $user_ID,
                'post_type' => POST_TYPE,
            );
            $post_id = wp_insert_post($cc_my_post);

            // Check either post created or not
            if ( $post_id && !is_wp_error($post_id) )
            {
              $success = true;

              wp_set_object_terms($post_id, $category, $taxonomy = CUSTOM_CAT_TYPE);
              
              $filters = get_terms( array('taxonomy' => 'tags', 'hide_empty' => false));
              foreach($filters as $filter) {
                $slug = $filter->slug;
                $term_id = $filter->term_id;
                $value = $posted[$slug];
                if($value) {
                    add_post_meta($post_id, $slug, $posted[$slug], true);
                    if(isset($_POST['filter_'.$term_id])) {
                        update_filter_values($term_id, $value);
                    }
                }
              }
              
              if($posted['manufacturer']) {
                $cate_id = $posted['manufacturer_id'];
                add_post_meta($post_id, 'manufacturer', $posted['manufacturer'], true);
                update_man_list($cate_id, $posted['manufacturer']);
              }

              add_post_meta($post_id, 'cc_address_list', $posted['cc_address_list'], true);
                add_post_meta($post_id, 'cc_city_id', $posted['cc_city_id'], true);
                add_post_meta($post_id, 'cc_locations', $posted['cc_locations'], true);

                add_post_meta($post_id, 'cc_price', $posted['cc_price'], true);
                add_post_meta($post_id, 'cc_price_deposit', $posted['cc_price_deposit'], true);

              if(!$posted['cc_price_week']) {
                add_post_meta($post_id, 'cc_price_week', intval($posted['cc_price']) * 7, true);
              } else {
                add_post_meta($post_id, 'cc_price_week', $posted['cc_price_week'], true);
              }

              if(!$posted['cc_price_more']) {
                add_post_meta($post_id, 'cc_price_more', intval($posted['cc_price']) * 30, true);
              } else {
                add_post_meta($post_id, 'cc_price_more', $posted['cc_price_more'], true);
                  
              }
              
              add_post_meta($post_id, 'cc_state', $posted['cc_state'], true);

			  add_post_meta($post_id, 'img1', $posted['img1'], true);
			  add_post_meta($post_id, 'img2', $posted['img2'], true);
              add_post_meta($post_id, 'img3', $posted['img3'], true);
              
              add_post_meta($post_id, 'cc_category', $posted['cc_category'], true);

			  if( !$posted['img1'] && $posted['img2'] ) {
				  update_post_meta($post_id, 'img1', $posted['img2']);
			      update_post_meta($post_id, 'img2', '');
			  }

			  if( !$posted['img1'] && !$posted['img2'] && $posted['img3'] ) {
				  update_post_meta($post_id, 'img1', $posted['img3']);
			      update_post_meta($post_id, 'img3', '');
			  }

              //Save add type value
              update_post_meta($post_id, 'cc_add_type', 'free');

              $pkg_type = $_POST['package_type'] ? $_POST['package_type'] : 'pkg_free';

              //set expiry duration
              cc_set_expiry($post_id, $pkg_type);

			  // Email
			  $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			  $admin_email = get_option('admin_email');
	          $headers = array(
	           'Content-Type: text/html; charset=UTF-8',
	           'From: ' . $blogname . ' <' . $admin_email . '>' . "\r\n",
              );
              
			  $permalink = get_permalink( $post_id );
			  $img_url = ad_thumbnail_url ( $post_id );
			  ob_start();
	          include( get_stylesheet_directory() . '/email/complete.php');
	          $message = ob_get_clean();
              wp_mail($current_user->user_email, 'Вітаємо, Ваше оголошення опубліковане', $message, $headers);

			  // email to admin
			  $message = admin_url( 'post.php?post=' . $post_id ) . '&action=edit';
			  wp_mail( $admin_email, __('New Ad', 'cc'), $message );


			  //Validate to prevent duplicate submission
              update_option('cc_check_submit', $_REQUEST['cc_check_submit']);

			  $featured_home = isset( $_POST['feature_h'] ) ? 'on' : '';
              $featured_cate = isset( $_POST['feature_c'] ) ? 'on' : '';

              if ( isset($_REQUEST['pay_method']) && $_REQUEST['pay_method'] != '' && $_POST['total_price'] > 0 ) {

                if( file_exists(get_stylesheet_directory().'/gateways/liqpay/liqpay.php' )) :
                    include_once( get_stylesheet_directory().'/gateways/liqpay/liqpay.php' );
                endif;
              }
			}
        }
}
get_header();
require_once( get_stylesheet_directory().'/js/ad_field_validation.php' );

?>
<section class="grey-background">
<div class="container">
<section class="add-ad">

<?php if( $success ) : ?>
    <div class="fanks">
        <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/success.svg" class="notfound__icon" >
        <div class="fanks__title"><?php _e('Thank you!', 'prokkat'); ?></div>
        <div class="fanks__subtitle"><?php _e('Your ad has been successfully added and sent for check!', 'prokkat'); ?></div>
        <p><?php _e('Once it is activated, we will send you an e-mail. This usually takes no more than 15 minutes.', 'prokkat'); ?></p>
        <div class="text-center">
            <a href="<?php echo site_url('dashboard'); ?>" class="dashboard-btn round-btn"></span><?php _e('My Dashboard', 'prokkat'); ?></a>
            <a href="<?php echo site_url('new/'); ?>" class="login__item login__item__yellow">
                <i class="fa fa-plus" aria-hidden="true"></i>
                <?php echo __('Add Ad', 'prokkat'); ?>
            </a>
        </div>
    </div>

<?php else : ?>
    <div class="add">
        <div class="container">
            <div class="add__title text-center">
                <?php echo __('Add ads', 'prokkat'); ?>
            </div>

            <form id="newpost" name="add_post_form" method="post" style="display: block;">
                <ul class="add__wrap">

                    <li class="add__step">

                        <div class="add__form-item clearfix">
                        <div class="step__title"><?php _e('Ad description', 'prokkat') ?><span class="add__step-num">1</span></div>
                            <div class="input-wrp input-wrp_block add__block">    
                                <div class="form__title req"><?php _e('Ad Title', 'prokkat'); ?></div>
                                <div class="input-wrp input-wrp_block">
                                    <span class="max-text"><?php _e('Maximum', 'prokkat') ?> 100 <?php _e('characters', 'prokkat'); ?></span>
                                    <input type="text" id="title" class="input_add" name="cc_title" placeholder="<?php _e('Please enter title', 'prokkat') ?>">
                                </div>
                            </div>

                            <div class="input-wrp input-wrp_block add__block">
                                <div id="ad-categories" >                                                            
                                    <div class="col6 nopaddingl">
                                        <div class="req form__title"><?php _e('Category', 'prokkat'); ?></div>                                        
                                        <span class="cats_error"></span>
                                        <?php my_dropdown_categories( 'maincat', __('Category', 'prokkat')); ?>
                                    </div>
                                    <div class="col6 nopaddingr">
                                        <div class="req form__title"><?php _e('Subcategory', 'prokkat'); ?></div>
                                        <div class="input-wrp input-wrp_block" id="subcat-li">
                                            <select name="cat" id="subcat" class="input_add" disabled>
                                                <option value="-1"><?php echo __('Subcategory', 'prokkat'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>                                                                    
                                <input id="chosenCategory" name="cc_category" type="hidden" value="" />                                
                            </div>
                            <div id="add_filters">

                            </div>
                            <div class="input-wrp input-wrp_block add__block">
                            <div class="req form__title"><?php _e('State', 'prokkat'); ?></div>
                                <div class="input-wrp input-wrp_block ">
                                    <fieldset class="rating">
                                        <input type="radio" id="star5" name="cc_state" value="5" /><label for="star5"></label>
                                        <input type="radio" id="star4" name="cc_state" value="4" /><label for="star4"></label>
                                        <input type="radio" id="star3" name="cc_state" value="3" /><label for="star3"></label>
                                        <input type="radio" id="star2" name="cc_state" value="2" /><label for="star2"></label>
                                        <input type="radio" id="star1" name="cc_state" value="1" /><label for="star1"></label>
                                    </fieldset>
                                </div>
                            </div>
                            <div id="price-block">
                            <div class="input-wrp input-wrp_block add__block">
                                <div class="col6 nopaddingl">
                                    <div class="req form__title"><?php _e('Rent price for 1 to 4 days, UAH', 'prokkat'); ?></div>
                                    <div class="input-wrp input-wrp_block">
                                        <input type="number" type="text" class="input_add" placeholder="<?php _e('Please enter price', 'prokkat'); ?>" min="1" id="cc_price" name="cc_price">
                                    </div>
                                </div>
                                <div class="col6 nopaddingr">
                                    <div class="req form__title"><?php _e('Deposit size, UAH', 'prokkat'); ?></div>
                                    <div class="input-wrp input-wrp_block">
                                        <input type="number" type="text" class="input_add" placeholder="<?php _e('Please enter price', 'prokkat'); ?>" min="1" id="cc_price_deposit" name="cc_price_deposit">  
                                    </div>
                                </div>

                            </div>
                            <div class="input-wrp input-wrp_block add__block">
                                <div class="col6 nopaddingl">
                                    <div class="form__title"><?php _e('Rent price for 10+ days, UAH', 'prokkat'); ?></div>
                                    <div class="input-wrp input-wrp_block">
                                        <input type="number" type="text" class="input_add" placeholder="<?php _e('Please enter price', 'prokkat'); ?>" min="1" id="cc_price_more" name="cc_price_more">
                                    </div>
                                </div>
                                <div class="col6 nopaddingr">
                                    <div class="form__title"><?php _e('Rent price for 5 to 7 days, UAH', 'prokkat'); ?></div>
                                    <div class="input-wrp input-wrp_block">
                                        <input type="number" type="text" class="input_add" placeholder="<?php _e('Please enter price', 'prokkat'); ?>" min="1" id="cc_price_week" name="cc_price_week">  
                                    </div>
                                </div>
                            </div>
                            </div> 
                            <!-- <div class="add__block">
                              <label>
                                  <input type="checkbox" class="add__check" value="1" name="custom-price" id="custom-price">Ціна за домовленістю
                              </label>
                            </div> -->
                            <div class="req form__title"><?php echo __('Description', 'prokkat'); ?></div>
                            <div class="input-wrp input-wrp_block">
                                <span class="max-text"><?php _e('Minimum', 'prokkat') ?> 150 <?php _e('characters', 'prokkat'); ?></span>
                                <span class="desc_error"></span>
                                <textarea name="cc_description" id="cc_desc" class="textarea textarea_mess" placeholder="<?php _e('Please enter description', 'prokkat') ?>"></textarea>
                            </div>
                        </div>
                    </li>

                    <li class="add__step add__step__container">
                        <div class="step__title"><?php echo __('Add photo', 'prokkat'); ?><span class="add__step-num">2</span></div>
                        <span class="photo_error"></span>
						<?php $id1 = "img1"; $id2 = "img2"; $id3 = "img3"; ?>

                        <div class="add__photo-grid-container">
					    <div class="add__upload_big ">
                            <div class="upload_img-wrp">
                                <div class="add__upload">
                                    <div class="add__upload-text">
                                        <div class="div add__photo-title"><?php _e('Upload photos', 'prokkat'); ?></div>
                                        <p><?php _e('Upload photo of your own sweets to gallery. Remember that the better the picture - the more people interested in your services.', 'prokkat'); ?></p>
                                        <?php my_photo_markup( $id1 ); ?>
                                        <div class="photo-error hide"><?php echo __('Minimum', 'prokkat'); ?> 800x800px</div>                                        
                                    </div>
                                </div>
								<div class="plupload-thumbs hide" id="<?php echo $id1; ?>plupload-thumbs">
                                </div>
                            </div>
                        </div>

                            <div class="small-top">
                                <div class="upload_img-wrp">
                                  <?php my_photo_markup( $id2 ); ?>
                                  <div class="plupload-thumbs hide" id="<?php echo $id2; ?>plupload-thumbs">
                                  </div>
                                </div>
                            </div>

                            <div class="small-bot">
                                <div class="upload_img-wrp">
							      <?php my_photo_markup( $id3 ); ?>
                                  <div class="plupload-thumbs hide" id="<?php echo $id3; ?>plupload-thumbs">
                                  </div>
                                </div>
                            </div>
                        </div>

                    </li>

                    <li class="add__step">
                        <div class="add__form-item add__form-item_contact">
                            <div class="step__title"><?php _e('Where is instruments?', 'prokkat'); ?><span class="add__step-num">3</span></div>                            
							<div id='address_list'></div>                            
                            <div class="req form__title"><?php _e('Location', 'prokkat'); ?></div>
                            <div class="input-wrp input-wrp_block">
								<input type="hidden" name="cc_address_list" id="cc_address_list" value='<?php echo get_the_author_meta('cc_address_list', $user_ID); ?>'/>
                                <input type="hidden" name="cc_city_id" id="cc_city_id" value='<?php echo get_the_author_meta('cc_city_id', $user_ID); ?>'/>
                                <input type="hidden" name="cc_locations" id="cc_locations" value='<?php echo get_the_author_meta('cc_locations', $user_ID); ?>'/>
                                <div class="address-input__container">
                                    <input type="text" name="cc_address" id="cc_address" class="input_add noEnterSubmit" value='' placeholder="<?php _e('Address', 'prokkat'); ?>" />
                                    <input type="button" id="add_address" value="<?php _e('Add', 'prokkat'); ?>" />
                                </div>
			                    <div id="map_canvas" style="height:350px; margin-top: 10px; position:relative;"  class="form_row clearfix"></div>
                            </div>
                        </div>
                    </li>

                    <li class="add__step add__step__container hide">

                        <div class="step__title">Варіанти розміщення<span class="add__step-num">4</span></div>
                      <?php

                        global $wpdb, $price_table_name;
						$packages = $wpdb->get_results("SELECT * FROM $price_table_name WHERE status=1");
                        if ($packages):
                          foreach ($packages as $package):
                            $valid_to = $package->validity_per;
                            if ($valid_to == 'D') $valid_to = "днів";
                            if ($valid_to == 'M') $valid_to = __( "Months", 'cc' );
                            if ($valid_to == 'Y') $valid_to = __( "Years", 'cc' );
                      ?>
						<div class="add__seo" >
                          <div class="inline-wrp">
                            <input type="radio" <?php if ($package->package_cost == 0) echo "checked='checked'"; ?> value="<?php echo $package->package_cost; ?>" name="price_select" id="<?php echo $package->package_type; ?>" class="radio">
                            <input type="hidden" class="<?php echo $package->package_type; ?>" name="<?php echo $package->package_type; ?>" value="<?php echo $package->validity; ?>"/>
                            <input type="hidden" class="<?php echo $package->package_type_period; ?>" name="<?php echo $package->package_type_period; ?>" value="<?php echo $package->validity_per; ?>"/>
                            <input type="hidden" class="is_recurring" name="is_recurring" value="<?php echo $package->is_recurring; ?>"/>
                            <input type="hidden" class="validity" name="validity" value="<?php echo $package->validity; ?>"/>
                            <input type="hidden" class="validity_per" name="validity_per" value="<?php echo $package->validity_per; ?>"/>
                            <input type="hidden" class="pkg_type" name="pkg_type" value="<?php echo $package->package_type; ?>"/>
                            <input type="hidden" class="is_featured" name="is_featured" value="<?php echo $package->is_featured; ?>"/>
                            <input type="hidden" id="price_title" name="price_title" value="<?php echo $package->price_title; ?>"/>
							<?php

							  if ( $package->package_cost == 0 ) echo '<img src="'.get_stylesheet_directory_uri().'/img/piggy-bank.svg" class="add__seo__icon" />';
							  else echo '<img src="'.get_stylesheet_directory_uri().'/img/crown.svg" class="add__seo__icon" />';
							?>
                            <label class="big-label" for="<?php echo $package->package_type; ?>">
                                <span class="big-label__title"><?php echo stripslashes(__($package->price_title,'cc')); ?></span>
								<p><?php echo __( 'Термін дії :', 'cc' ) .'&nbsp;'. $package->validity .'&nbsp;'. $valid_to . '<br>' . stripslashes(__($package->price_desc, 'cc')); ?></p>
                            </label>
                            <?php if( $package->package_cost != 0 ) : ?>
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

							<?php endif; ?>

                          </div>
                        </div>

					    <?php  endforeach; endif; ?>
                        <div class="form_row">
                            <input type="hidden" name="total_price" id="total_price" value="0"/>
                            <input type="hidden" name="package_title" id="package_title" value=""/>
                            <input type="hidden" name="package_validity" id="package_validity" value=""/>
                            <input type="hidden" name="package_validity_per" id="package_validity_per" value=""/>
                            <input type="hidden" name="package_type" id="package_type" value=""/>
                        </div>

                    </li>

                    <li class="last__step">

                      <div class="input-wrp input-wrp_block nopadding">
                        <div id="btnsend">
                          <a href="#0" class="add__btn" id="cc_submit"><span class="ico mail-ico"></span><?php _e('Publish ad', 'prokkat'); ?></a>
                        </div>
                        <div id="btnpay" class="hide">
                          <a href="#0" class="btn btn_green" id="cash">LIQPAY: Оплатити (<span></span> грн)</a>
                          <div id="pay-logos" class="add__step text-center">
                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/img/visa.png" alt="visa" class="img-responsive inline">
                          </div>
                        </div>
                      </div>

					  <input type="hidden" name="cc_check_submit" value="<?php echo rand(); ?>"/>

					  <ul id="payments">
                        <li id="liqpay">
                          <label class="r_lbl"><input  type="hidden" value="liqpay" id="liqpay_id" name="pay_method" checked="checked" /></label>
                        </li>
                      </ul>

                    </li>
                </ul>

            </form>
        </div>
    </div>

<?php endif; ?>

</section>
</div>
</section>

<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/js/package_price.js"></script>
<?php edit_map(); ?>
<?php get_footer(); ?>
