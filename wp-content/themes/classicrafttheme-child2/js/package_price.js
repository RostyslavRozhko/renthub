jQuery(document).ready(function(){
    var pkg_price = 0.00;
	var categoryh = 0;
    var categoryc = 0;
    var totalCate = 0;
	var totalPrice = 0;
	
	var priceBlock = jQuery('#price-block');
	
    var feature_h = jQuery('#feature_h');
    var feature_c = jQuery('#feature_c');
	
    var spkg_price = jQuery('#pkg_price');
	var feature_price = jQuery('#feature_price');
    var result_price = jQuery('#result_price');
    var totalCost = jQuery('#total_price');

	var btnpay = jQuery('#btnpay');
    var btnsend = jQuery('#btnsend');

	
    jQuery('#cash, #cc_submit').on('click', function (e) {
	   e.preventDefault();
	  jQuery('#newpost').submit();
    });
	
	jQuery('#update, #cash').on('click', function (e) {
	  e.preventDefault();
	  jQuery('#edit_post_form').submit();
    });
	
    jQuery('#featured input:checkbox').change(function(){
	
        jQuery('#pkg_one_time').prop('checked', true).trigger('refresh').trigger('change');
        jQuery('#pkg_free').prop('checked', false).trigger('refresh');

		Calc();
    });

    jQuery('input:radio[name=price_select]').change(function()
	{
		btnsend.addClass('hide');
		btnpay.removeClass('hide');
		
		pkg_price = jQuery(this).val();

        if($(this).val()==0){
          btnsend.removeClass('hide');
          btnpay.addClass('hide');
		  feature_h.prop('checked', false).trigger('refresh');
          feature_c.prop('checked', false).trigger('refresh');
        }
        else {
          btnsend.addClass('hide');
          btnpay.removeClass('hide');
        }
		
        Calc();
		
		//Set value for package type
        var pkg_type = jQuery(this).parent().parent().find('input[name="pkg_type"]').val();
        jQuery('#package_type').val(pkg_type);

     //Set valu for listing active period
/*     var validity = jQuery(this).parent('div').find('input[name="validity"]').val(); 
     var validity_per = jQuery(this).parent('div').find('input[name="validity_per"]').val(); 
     jQuery('#package_validity').val(validity);
     jQuery('#package_validity_per').val(validity_per);
*/
    });
	
	jQuery('input:checkbox[name=price_select]').change(function()
	{
		if( !jQuery(this).is(":checked") ) {
	      btnsend.removeClass('hide');
          btnpay.addClass('hide');
		  pkg_price = 0;
		  feature_h.prop('checked', false).trigger('refresh');
          feature_c.prop('checked', false).trigger('refresh');
		}
		else {
		  btnsend.addClass('hide');
		  btnpay.removeClass('hide');
		  pkg_price = jQuery(this).val();
		}
		
        Calc();
		
		//Set value for package type
        var pkg_type = jQuery(this).parent().parent().find('input[name="pkg_type"]').val();
        jQuery('#package_type').val(pkg_type);
    });
	
	jQuery('#be_paid').change(function()
	{
		if( !jQuery('#be_paid').is(":checked") ) {
		  pkg_price = 0;
		  btnsend.removeClass('hide');
          btnpay.addClass('hide');
		  jQuery('input:checkbox[name=price_select]').prop('checked', false).trigger('refresh');
		  feature_h.prop('checked', false).trigger('refresh');
          feature_c.prop('checked', false).trigger('refresh');
		  Calc();
		}
    });
	
	jQuery('.make_premium').click(function (e){
    console.log('click')
	  var post_id = jQuery(this).attr("id");
      post_id = post_id.replace("makePremium", "");
      jQuery('#post_id').val(post_id);
    });

    function Calc(){
	
	  jQuery('#loading').show();
      categoryh = ( feature_h.is(":checked") & pkg_price ) ? feature_h.val() : 0;
	  categoryc = ( feature_c.is(":checked") & pkg_price ) ? feature_c.val() : 0;
      totalCate = parseFloat(categoryh) + parseFloat(categoryc);
      totalPrice = parseFloat(totalCate) + parseFloat(pkg_price);

	  window.setTimeout(function(){
		spkg_price.text(pkg_price);
		feature_price.text(parseFloat(totalCate));
		result_price.text(parseFloat(totalPrice));
		totalCost.val(totalPrice);
        jQuery('#cash span').text(totalPrice);
	    jQuery('#loading').hide();
	  }, 500);
    }

  
  
  if ($('input:checkbox[name=custom-price]').attr('checked')) {

    $('input:checkbox[name=custom-price]').prop('checked', true).trigger('refresh');
    priceBlock.css("display","none");
    $('input:checkbox[name=custom-price]').parents('label').addClass('price-choise');
    $('#cc_price').val('');
  }
  else {
    priceBlock.css("display","block");
  }

  jQuery('input:checkbox[name=custom-price]').change(function(){
    
	if(jQuery(this).is(':checked')){
      priceBlock.slideUp();
      jQuery(this).parents('label').addClass('price-choise');
      jQuery('#cc_price').val('');
    }
    else {
      priceBlock.slideDown();
      jQuery(this).parents('label').removeClass('price-choise');
    }
  });

});       
