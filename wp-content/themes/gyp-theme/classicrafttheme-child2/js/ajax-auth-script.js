jQuery(document).ready(function ($) {

    var a = {"Ё":"YO","Й":"I","Ц":"TS","У":"U","К":"K","Е":"E","Н":"N","Г":"G","Ш":"SH","Щ":"SCH","З":"Z","Х":"H","Ъ":"'","ё":"yo","й":"i","ц":"ts","у":"u","к":"k","е":"e","н":"n","г":"g","ш":"sh","щ":"sch","з":"z","х":"h","ъ":"'","Ф":"F","Ы":"I","В":"V","А":"a","П":"P","Р":"R","О":"O","Л":"L","Д":"D","Ж":"ZH","Э":"E","ф":"f","ы":"i","в":"v","а":"a","п":"p","р":"r","о":"o","л":"l","д":"d","ж":"zh","э":"e","Я":"Ya","Ч":"CH","С":"S","М":"M","И":"I","Т":"T","Ь":"'","Б":"B","Ю":"YU","я":"ya","ч":"ch","с":"s","м":"m","и":"i","т":"t","ь":"'","б":"b","ю":"yu"};

    function transliterate(word){
      return word.split('').map(function (char) { 
        return a[char] || char; 
      }).join("");
    }

	// Perform AJAX login/register on form submit
	jQuery('form#login, form#registration').on('submit', function (e) {
		console.log(validate_object.required)
		jQuery.extend(jQuery.validator.messages, {
          required: validate_object.required,
          email: validate_object.email,
          equalTo: ajax_auth_object.equalTo,
		});
		
        if (!jQuery(this).valid()) return false;
        jQuery('p.status', this).show().text(ajax_auth_object.loadingmessage);
		action = 'ajaxlogin';
		username = nick = '';
		email = jQuery('form#login #email').val();
		password = jQuery('form#login #password').val();
		security = jQuery('form#login #security').val();
		redirecturl = ajax_auth_object.redirecturl_login;
		cityId = '';
		cityName = ''
		if (jQuery(this).attr('id') == 'registration') {
			action = 'ajaxregister';
			nick = jQuery('#nick').val();
			username = transliterate(nick);
			password = jQuery('#signonpassword').val();
        	email = jQuery('form#registration #emailreg').val();
			security = jQuery('#signonsecurity').val();
			redirecturl = ajax_auth_object.redirecturl_reg;
			cityId = jQuery('#cc_user_city_id').val();
			cityName = jQuery('#cc_user_address').val();
		}  
		ctrl = jQuery(this);
		jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_auth_object.ajaxurl,
            data: {
                'action': action,
                'username': username,
                'nick': nick,
                'password': password,
				'email': email,
				'security': security,
				'cityId': cityId,
				'cityName': cityName
            },
            success: function (data) {
				jQuery('p.status', ctrl).text(data.message);
				if (data.loggedin == true) {
                    document.location.href = redirecturl;
                }
            }
        });
        e.preventDefault();
    });
	
	// Perform AJAX forget password on form submit
	jQuery('form#forgot_password').on('submit', function(e){
		if (!$(this).valid()) return false;
		jQuery('p.status', this).show().text(ajax_auth_object.loadingmessage);
		ctrl = $(this);
		jQuery.ajax({
			type: 'POST',
            dataType: 'json',
            url: ajax_auth_object.ajaxurl,
			data: { 
				'action': 'ajaxforgotpassword', 
				'user_login': jQuery('#user_login').val(), 
				'security': jQuery('#forgotsecurity').val(), 
			},
			success: function(data){					
				jQuery('p.status',ctrl).text(data.message);				
			}
		});
		e.preventDefault();
		return false;
	});

	// Client side form validation
    if (jQuery("#registration").length) 
		jQuery("#registration").validate({ 
			rules:{
			  password2:{ equalTo:'#signonpassword' }	
		    }
		});
    else if (jQuery("#login").length) 
		jQuery("#login").validate();
    if (jQuery('#forgot_password').length)
		jQuery('#forgot_password').validate();
});