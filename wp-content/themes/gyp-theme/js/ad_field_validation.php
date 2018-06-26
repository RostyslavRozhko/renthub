<script type="text/javascript">
  jQuery(document).ready(function(){
		
	var adForm = jQuery("#newpost");
	var editAdForm = jQuery("#edit_post_form");

    var cc_title = jQuery("#title");   
    var cc_desc = jQuery("#cc_desc");
    var cc_price = jQuery("#cc_price");
    var cc_price_week = jQuery("#cc_price_week");
    var cc_price_more = jQuery("#cc_price_more");
    var cc_price_deposit = jQuery("#cc_price_deposit");
    var cc_address_list = jQuery("#cc_address_list");
    var cc_address = jQuery("#cc_address");
	const address_list = jQuery('#address_list')

    var cat_selects = jQuery("#ad-categories select");
    var values = [];

    var cats_error = jQuery(".cats_error");
    const desc_error = jQuery('.desc_error')
    var photo_error = jQuery(".photo_error")

    jQuery("#maincat").on("change", function(e) {
        category_ID = jQuery(this).val();
        var cat_ul = jQuery("#maincat-li").parent();
        jQuery('#add_filters').hide()

        // cat_ul.find('#subcat-li').remove();
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

    cat_selects.change(function() {
	  cats_error.text("");
	  jQuery("#chosenCategory").val(jQuery("#maincat").val());
    });

    jQuery("#subcat").change(function() {
        values = jQuery('#ad-categories select').map(function () {
                      if( this.value > 0 ) return this.value;
                    }).get();
        jQuery("#chosenCategory").val(values)
    })
	
	
	    function validate_cat(){
            if( !jQuery("#chosenCategory").val()) {
                cats_error.text("<?php echo __( 'Please select category', 'prokkat' ); ?>");
				cats_error.addClass("error");
                return false;
            }else{
                cats_error.text("");
				cats_error.removeClass("error");
                return true;
            }
        }
   
		
        function validate_title(){
            if(cc_title.val() == ''){
				cc_title.addClass("error");
                cc_title.attr("placeholder", "<?php echo __( 'Please enter title', 'prokkat' ); ?>");
                cc_title.css('border', 'solid 1px red');
                cc_title.css('background-color', '#ffece8');
                return false;
            } else if(cc_title.val().length > 100) {
				cc_title.addClass("error");
                cc_title.attr("placeholder", "Максимум 100 символов");
                cc_title.css('border', 'solid 1px red');
                cc_title.css('background-color', '#ffece8');
            }else{
				cc_title.removeClass("error");
                cc_title.css('border', 'none');
                cc_title.css('background-color', '');
                return true;
            }
        }
        cc_title.blur(validate_title);
        cc_title.keyup(validate_title);

        const prices = [
                cc_price,
                cc_price_deposit
            ]

        prices.map(price => {
            price.blur(validate_price);
            price.keyup(validate_price);
        })

		function validate_price(){
            let result = false
            prices.map(price => {
                if (price.val() == '' && !jQuery("#custom-price").is(":checked")) {
                    price.addClass("error");
                    price.attr("placeholder", "<?php echo __( 'Please enter price', 'prokkat' ); ?>");
                    price.css('border', 'solid 1px red');
                    price.css('background-color', '#ffece8');
                    result = false;
                } else {
                    price.removeClass("error");
                    price.css('border', 'none');
                    price.css('background-color', '');
                    result = true;
                }
            })
            return result
        }
		
		cc_address.change(function() {
			cc_address.removeClass("error");
            cc_address.css('border', 'none');
            cc_address.css('background-color', '');
        });
		function validate_address(){
            if( cc_address_list.val() == '' || cc_address_list.val() == '[]'){
				cc_address.val('');
				cc_address.addClass("error");
                cc_address.css('border', 'solid 1px red');
                cc_address.css('background-color', '#ffece8');
                return false;
            }
			else return true;
        }
		
  function validate_desc() {
    if(cc_desc.val() == "") {
        cc_desc.addClass("error")
        cc_desc.attr("placeholder", "<?php echo __( 'Please enter description', 'prokkat' ); ?>");
        cc_desc.css('border', 'solid 1px red');
        cc_desc.css('background-color', '#ffece8');
        return false;
    } else {
		cc_desc.removeClass("error");
        cc_desc.css('border', 'none');
        cc_desc.css('background-color', '');
        return true;
    }
  }
  cc_desc.blur(validate_desc);
  cc_desc.keyup(validate_desc);
		
		function validate_photo(){
            if( !jQuery("#img1").val() & !jQuery("#img2").val() & !jQuery("#img3").val() ) {
                photo_error.text("<?php echo __( 'Please upload at least 1 photo.', 'prokkat' ); ?>");
				photo_error.addClass("error");
                return false;
            }else{
                photo_error.text("");
				photo_error.removeClass("error");
                return true;
            }
        }


		adForm.submit(function(){
          if(validate_title() & validate_desc() & validate_cat() & validate_price() & validate_address() & validate_photo())
            return true;
          else {
		    jQuery('html, body').animate({
              scrollTop: jQuery('.error').offset().top -70
            }, 'slow');
		    return false;
		  }
        });
		
		editAdForm.submit(function(){
          if(validate_title() & validate_desc() & validate_cat() & validate_price() & validate_address() & validate_photo())
            return true;
          else {
		    jQuery('html, body').animate({
              scrollTop: jQuery('.error').offset().top -70
            }, 'slow');
		    return false;
		  }
        });
    });
</script>