jQuery(document).ready(function ($) {

	var mydata = {
	  action: 'new_message_number',
	  token: leads_object.nonce
	};
    var my_ajax_call = function(){
	  jQuery.post(
	    leads_object.ajaxurl,
		mydata,
		function(results) {
		  jQuery('.login__item_mess .badge, .profile__menu .badge').text(results);
	      if( results != 0 ) {
			jQuery('.profile__menu .badge').css( "display", "inline-block" );
			jQuery('.login__item_mess').removeClass('hide');
		  }
	      else {
			jQuery('.login__item_mess').addClass('hide');
			jQuery('.profile__menu .badge').css( "display", "none" );
	      }
	    }
	  );
    }
    //setInterval(my_ajax_call,3000);
	my_ajax_call();

	// Perform AJAX send message on form submit
	jQuery('form#send-msg').on('submit', function(e){
		
		var message = jQuery(this).find('p.status');

			if (!$(this).valid()) return false;
		
			message.show().text(leads_object.loadingmessage);
			jQuery.ajax({
				type: 'POST',
				dataType: 'json',
							url: leads_object.ajaxurl,
				data: { 
					'action': 'my_new_message_ajax', 
					'message_to': jQuery('#message_to').val(),
					'message_title': jQuery('#message_title').val(),
					'message_from': jQuery('#message_from').val(),
					'parent_id': jQuery('#parent_id').val(),
					'token': jQuery('#token').val(),
					'message_content': jQuery('#message').val(),
				},
				success: function(data){
					if( data.token ) jQuery('#token').val(data.token);
					if( data.captcha ) jQuery('#show_captcha').text(data.captcha);
					if( data.captcha_sum ) jQuery('#captchacode').val(data.captcha_sum);
					if( data.error ) {				
						message.html(data.error);
					}
					else {				
						//message.html(data.message);
						message.text(leads_object.successmessage);
						message.delay(1000).fadeOut(1000);
						jQuery('#message').val('');
						jQuery('#captcha').val('');
					}
				}
			});
			e.preventDefault();
			return false;
	});
	
	jQuery('form#chatform').on('submit', function(e){
		if (!jQuery(this).valid()) return false;
	});

	// Client side form validation
    if (jQuery("#send-msg").length) {
		jQuery.extend(jQuery.validator.messages, {
          required: validate_object.required,
          equalTo: leads_object.equalTo,
		});
		jQuery("#send-msg").validate({
			rules:{
			  captcha:{ equalTo:'#captchacode' }
		    }
		});
	}
		
	if (jQuery("#chatform").length) {
		jQuery.extend(jQuery.validator.messages, {
          required: validate_object.required,
          equalTo: leads_object.equalTo,
		});
		jQuery("#chatform").validate({
			rules:{
			  captcha:{ equalTo:'#captchacode' }
		    }
		});
	}
	
	
	var startFrom = 10;
	
	jQuery('.btn_moremess').on('click', function()
    {
	  var btnid = jQuery(this).parent().attr("id");
	  var id = btnid.replace("more", "");
	  jQuery('.btn_moremess .fa').addClass('spin');
      jQuery.ajax({
			type: 'POST',
			dataType: 'json',
            url: leads_object.ajaxurl,
			data: { 
				'action': 'load_messages',
				"startFrom" : startFrom,
				"id" : id
			},
			success: function(data){
				jQuery('.btn_moremess .fa').removeClass('spin');
				if (data.html) {
		          jQuery('ul.chat__list').prepend(data.html);
				}
				
                startFrom += 10;
				if( data.num <= startFrom) jQuery('.btn_moremess').hide();
			}
      });
    });
    jQuery('.close-categories').click(function(){
    		jQuery('.header__category__window-small').hide();
    });
});