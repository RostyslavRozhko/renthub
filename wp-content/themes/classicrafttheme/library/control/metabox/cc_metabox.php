<?php
function cc_custom_meta_box() {
    global $key;
    if (function_exists('add_meta_box')) {
        add_meta_box('custom-type-meta-boxes', AD_DETAILS, 'cc_custom_metabox', POST_TYPE, 'normal', 'high');        
    }
}

function cc_custom_metabox() {
    global $post, $cc_custom_meta;
    ?>
    <div id="panel-wrap">
        <style>
            .address-add__icon {
                display: none;
            }
            .formated_address {
                display: inline-block;
                font-size: 14px;
            }

            .checkbox-container {
                margin: 3px 0;
            }

            .checkbox-container div {
                display: inline-block;
            }

            .input-wrp {
                margin-top: 10px;
            }
        </style>
        <script type="text/javascript">
                                                                                        
            // -- NO CONFLICT MODE --
            var $s = jQuery.noConflict();
            $s(function(){
                //AJAX Upload
                jQuery('.image_upload_button').each(function(){
                                                                                    			
                    var clickedObject = jQuery(this);
                    var clickedID = jQuery(this).attr('id');	
                    new AjaxUpload(clickedID, {
                        action: '<?php echo admin_url("admin-ajax.php"); ?>',
                        name: clickedID, // File upload name
                        data: { // Additional data to send
                            action: 'of_ajax_post_action',
                            type: 'upload',
                            data: clickedID },
                        autoSubmit: true, // Submit file after selection
                        responseType: false,
                        onChange: function(file, extension){},
                        onSubmit: function(file, extension){
                            clickedObject.text('Uploading'); // change button text, when user selects file	
                            this.disable(); // If you want to allow uploading only 1 file at time, you can disable upload button
                            interval = window.setInterval(function(){
                                var text = clickedObject.text();
                                if (text.length < 13){	clickedObject.text(text + '.'); }
                                else { clickedObject.text('Uploading'); } 
                            }, 200);
                        },
                        onComplete: function(file, response) {
                                                                                    				   
                            window.clearInterval(interval);
                            clickedObject.text('Upload Image');	
                            this.enable(); // enable upload button
                                                                                    					
                            // If there was an error
                            if(response.search('Upload Error') > -1){
                                var buildReturn = '<span class="upload-error">' + response + '</span>';
                                jQuery(".upload-error").remove();
                                clickedObject.parent().after(buildReturn);
                                                                                    					
                            }
                            else{
                                var buildReturn = '<img class="hide meta-image" id="image_'+clickedID+'" src="'+response+'" alt="" />';
                                jQuery(".upload-error").remove();
                                jQuery("#image_" + clickedID).remove();	
                                clickedObject.parent().after(buildReturn);
                                jQuery('img#image_'+clickedID).fadeIn();
                                clickedObject.next('span').fadeIn();
                                clickedObject.parent().prev('input').val(response);
                            }
                        }
                    });
                                                                                    			
                });
                //AJAX Remove (clear option value)
                jQuery('.image_reset_button').click(function(){
                                                                                			
                    var clickedObject = jQuery(this);
                    var clickedID = jQuery(this).attr('id');
                    var theID = jQuery(this).attr('title');	
                                                                                	
                    var ajax_url = '<?php echo admin_url("admin-ajax.php"); ?>';
                                                                                				
                    var data = {
                        action: 'of_ajax_post_action',
                        type: 'image_reset',
                        data: theID
                    };
                                                                                					
                    jQuery.post(ajax_url, data, function(response) {
                        var image_to_remove = jQuery('#image_' + theID);
                        var button_to_hide = jQuery('#reset_' + theID);
                        image_to_remove.fadeOut(500,function(){ jQuery(this).remove(); });
                        button_to_hide.fadeOut();
                        clickedObject.parent().prev('input').val('');
                    });
                                                                                					
                    return false; 
                                                                                					
                }); 
                                                                            		
                jQuery("#maincat").on("change", function(e) {
                    category_ID = jQuery(this).val();
                    var cat_ul = jQuery("#maincat-li").parent();
                    jQuery('#add_filters').hide()

                    jQuery.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                        data: {
                            'action': 'getSubCategories',
                            'catID': category_ID,
                        },
                        success: function (data) {
                            const select = jQuery(data)
                            const subcat = jQuery("#subcat")
                            subcat.prop("disabled", false);
                            subcat.html(select[0].innerHTML);
                            subcat.change(function() {
                                values = jQuery('#ad-categories select').map(function () {
                                if( this.value > 0 ) return this.value;
                                }).get();
                                jQuery("#chosenCategory").val(values)
                            });
                        }
                    });
                    e.preventDefault();
                });

                    jQuery("#subcat").on("change", function(e) {
                        const filters_container = jQuery('#add_filters')
                        filters_container.hide()
                        category_ID = jQuery(this).val()

                    jQuery.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                    data: {
                        'action': 'getCatFilters',
                        'catID': category_ID,
                    },
                    success: function (data) {
                        filters_container.html(data)
                        filters_container.show()
                    },
                    });
                    e.preventDefault();
                });

                jQuery("#ad-categories select").change(function() {
                    jQuery("#chosenCategory").val(jQuery("#maincat").val());
                });

                jQuery("#subcat").change(function() {
                    values = jQuery('#ad-categories select').map(function () {
                                if( this.value > 0 ) return this.value;
                                }).get();
                    jQuery("#chosenCategory").val(values)
                })

                    function updateCats() {
                        const values = {}
                        jQuery('.filters').find('input[type="checkbox"]').map(function() {
                            if(jQuery(this).prop('checked')) {
                                const key = values[jQuery(this).data('filter')]
                                const value = this.value
                                if(key) {
                                    key.push(value)
                                } else {
                                    values[jQuery(this).data('filter')] = [value]
                                }
                            }
                        })
                        jQuery('.filter_value_field').val('')
                        Object.keys(values).map(key => {
                            jQuery(`.${key}`).val(values[key].join())
                        })
                    }

                    jQuery('.filters').find('input[type="checkbox"]').click(function() {
                        updateCats()
                    });

                    (function initCheckbox() {
                        const filtersVals = jQuery('#filtersVals').val()
                        if(filtersVals) {
                            const vals = filtersVals.split(',')
                            jQuery('.filters').find('input').map(function() {
                                if(vals.indexOf(this.value) > -1){
                                    jQuery(this).prop('checked', true)
                                }
                            })
                        }
                    })()
	                                                      	
                                                                            	
            });
        </script>
        <div class="form-wrap">
        <?php $pid = $post->ID; ?>
            <div class="form_row">    
                <label for="cc_title_rus" style="display:inline-block;"><?php _e('Ad Title (russian)', 'prokkat'); ?></label>
                <input type="text" name="cc_title_rus" placeholder="<?php _e('Please enter title', 'prokkat') ?>" value="<?php echo get_post_meta($pid, 'cc_title_rus', true); ?>" />
            </div>

            <div class="form_row" id="ad-categories">
                <div class="col6 nopaddingl">
                    <div class="req form__title"><?php _e('Category', 'prokkat'); ?></div>                                        
                    <?php
                        $cat_objs = $cats = array();
                        $cat_objs = wp_get_post_terms($pid, 'cate');
                        foreach( $cat_objs as $cat_obj ){
                            $cats[] = $cat_obj->term_id;
                        }
                    
                        $terms = get_terms(array( 'taxonomy' => 'cate' ));
                        foreach( $terms as $term ) {
                            if( $term->parent ) $child_ids[] = $term->term_id;
                            else $ids[] = $term->term_id;
                        }
                        
                        $id = array_shift( array_intersect( $ids, $cats ));
                        $child_id = array_shift( array_intersect( $child_ids, $cats ));
                    ?>
                    <div>
                      <?php my_dropdown_categories( 'maincat', __('Category', 'prokkat'), 0, $id  ); ?>
                    </div>
                </div>
                <div class="col6 nopaddingr">
                    <div class=" form__title"><?php _e('Subcategory', 'prokkat'); ?></div>
                    <div class="input-wrp input-wrp_block" id="subcat-li">
                        <?php  if( $id != 98 ) my_dropdown_categories( 'subcat', __('Subcategory', 'prokkat'), $id, $child_id  ); ?>
                    </div>
                    <input id="chosenCategory" name="cc_category" type="hidden" value="<?php echo implode(",",$cats); ?>"/>
                </div>
                <div id="add_filters">
                    <?php echo my_filters_edit_list($child_id, $pid); ?>
                </div>
            </div>

            <div class="form_row">
                <label for="cc_state" style="display:inline-block;"><?php _e('State', 'prokkat'); ?></label>
                <?php $checked = get_post_meta($pid, 'cc_state', true); ?>
                <select name="cc_state">
                    <option value="5" <?php if($checked == 5) echo 'selected'; ?>>5</option>
                    <option value="4" <?php if($checked == 4) echo 'selected'; ?>>4</option>
                    <option value="3" <?php if($checked == 3) echo 'selected'; ?>>3</option>
                    <option value="2" <?php if($checked == 2) echo 'selected'; ?>>2</option>
                    <option value="1" <?php if($checked == 1) echo 'selected'; ?>>1</option>
                </select>
            </div>

            <div class="form_row">    
                <label for="cc_price" style="display:inline-block;"><?php _e('Rent price for 1 to 4 days, UAH', 'prokkat') ?></label>
                <input type="text" name="cc_price" placeholder="<?php _e('Please enter price', 'prokkat') ?>" value="<?php echo get_post_meta($pid, 'cc_price', true); ?>" />
            </div>
            <div class="form_row">    
                <label for="cc_price_week" style="display:inline-block;"><?php _e('Rent price for 5 to 7 days, UAH', 'prokkat'); ?></label>
                <input type="text" name="cc_price_week" placeholder="<?php _e('Please enter price', 'prokkat') ?>" value="<?php echo get_post_meta($pid, 'cc_price_week', true); ?>" />
            </div>    
            <div class="form_row">    
                <label for="cc_price_more" style="display:inline-block;"><?php _e('Rent price for 10+ days, UAH', 'prokkat'); ?></label>
                <input type="text" name="cc_price_more" placeholder="<?php _e('Please enter price', 'prokkat') ?>" value="<?php echo get_post_meta($pid, 'cc_price_more', true); ?>" />
            </div>
            <div class="form_row">    
                <label for="cc_price_deposit" style="display:inline-block;"><?php _e('Deposit size, UAH', 'prokkat'); ?></label>
                <input type="text" name="cc_price_deposit" placeholder="<?php _e('Please enter price', 'prokkat') ?>" value="<?php echo get_post_meta($pid, 'cc_price_deposit', true); ?>" />
            </div>
            
		  <div class="form_row">
            <div class="label">
			  <label><?php echo __( 'Photos', 'cc' ); ?></label>
			</div>
            <div class="add">
			
			  <?php $id1 = "img1"; $id2 = "img2"; $id3 = "img3"; ?>
			  <div class="add__upload_sm ">
			    <div class="add__upload_sh">
                  <div class="upload_img-wrp">
                    <?php my_photo_markup( $id1, get_post_meta( $post->ID, $id1, true ) ); ?>
                    <div class="plupload-thumbs hide" id="<?php echo $id1; ?>plupload-thumbs">
                    </div>
                  </div>
                </div>

                <div class="add__upload_sh">
                  <div class="upload_img-wrp">
                    <?php my_photo_markup( $id2, get_post_meta( $post->ID, $id2, true ) ); ?>
                    <div class="plupload-thumbs hide" id="<?php echo $id2; ?>plupload-thumbs">
                    </div>
                  </div>
                </div>
			
			    <div class="add__upload_sh">
                  <div class="upload_img-wrp">
                    <?php my_photo_markup( $id3, get_post_meta( $post->ID, $id3, true ) ); ?>
                    <div class="plupload-thumbs hide" id="<?php echo $id3; ?>plupload-thumbs">
                    </div>
                  </div>
                </div>
			  </div>

			</div>
          </div>
          <div class="form_row">
            <div class="label">
			  <label for="cc_address"><?php echo __( 'Location', 'cc' ); ?></label>
			</div>
            <div id='address_list'></div>       
            <div class="row">
				<input type="hidden" name="cc_address_list" id="cc_address_list" value='<?php echo get_post_meta($pid, 'cc_address_list', true); ?>' />
                <input type="hidden" name="cc_city_id" id="cc_city_id" value='<?php echo get_post_meta($pid, 'cc_city_id', true); ?>'/>
                <input type="hidden" name="cc_locations" id="cc_locations" value='<?php echo get_post_meta($pid, 'cc_locations', true); ?>'/>
                <div class="address-input__container">
                    <input type="text" id="cc_address" class="input_add noEnterSubmit" value='' placeholder="<?php _e('Address', 'prokkat'); ?>" />
                    <input type="button" id="add_address" value="<?php _e('Add', 'prokkat'); ?>" />
                </div>
			    <div id="map_canvas" style="height:350px; margin-top: 10px; position:relative;"  class="form_row clearfix"></div>
			    <?php edit_map(); ?>
            </div>
          </div>
        </div>
    </div>	
    <?php
}

function cc_save_custommeta_box($post_id) {
    global $post, $meta_boxes, $key;

    $pid = $post_id;
    
    if (!isset($_POST[$key . '_wpnonce']))
        return $post_id;
    if (!current_user_can('edit_post', $post_id))
        return $post_id;

    if( isset($_POST['custom-price']) ) {
        update_post_meta($pid, 'custom-price', $_POST['custom-price']);
        update_post_meta($pid, 'cc_price', '');
        update_post_meta($pid, 'cc_price_week', '');
        update_post_meta($pid, 'cc_price_more', '');
        update_post_meta($pid, 'cc_price_deposit', '');
      }
      else {
        update_post_meta($pid, 'custom-price', '');
        update_post_meta($pid, 'cc_price', $_POST['cc_price']);
        update_post_meta($pid, 'cc_price_week', $_POST['cc_price_week']);
        update_post_meta($pid, 'cc_price_more', $_POST['cc_price_more']);
        update_post_meta($pid, 'cc_price_deposit', $_POST['cc_price_deposit']);
    }

	update_post_meta( $post_id, 'img1', $_POST['img1'] );
    update_post_meta( $post_id, 'img2', $_POST['img2'] );
    update_post_meta( $post_id, 'img3', $_POST['img3'] );

    update_post_meta( $pid, 'cc_address_list', $_POST['cc_address_list'] );
    update_post_meta( $pid, 'cc_city_id', $_POST['cc_city_id'] );
    update_post_meta( $pid, 'cc_locations', $_POST['cc_locations'] );
    update_post_meta( $pid, 'cc_title_rus', $_POST['cc_title_rus'] );

    update_post_meta( $pid, 'cc_state', $_POST['cc_state'] );

    $category = array_map('intval', explode(',', $_POST['cc_category']));
    wp_set_object_terms($pid, $category, $taxonomy = CUSTOM_CAT_TYPE);

    $filters = get_terms( array('taxonomy' => 'tags', 'hide_empty' => false));

    foreach($filters as $filter) {
        $slug = $filter->slug;
        if($_REQUEST[$slug]) {
            update_post_meta($pid, $slug, $_REQUEST[$slug]);
        }
      }

}

add_action('admin_menu', 'cc_custom_meta_box');
add_action('save_post', 'cc_save_custommeta_box');