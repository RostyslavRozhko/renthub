jQuery(document).ready(function ($) {

    const modal = $('.fancybox-error');

    var a = {"Ё":"YO","Й":"I","Ц":"TS","У":"U","К":"K","Е":"E","Н":"N","Г":"G","Ш":"SH","Щ":"SCH","З":"Z","Х":"H","Ъ":"'","ё":"yo","й":"i","ц":"ts","у":"u","к":"k","е":"e","н":"n","г":"g","ш":"sh","щ":"sch","з":"z","х":"h","ъ":"'","Ф":"F","Ы":"I","В":"V","А":"a","П":"P","Р":"R","О":"O","Л":"L","Д":"D","Ж":"ZH","Э":"E","ф":"f","ы":"i","в":"v","а":"a","п":"p","р":"r","о":"o","л":"l","д":"d","ж":"zh","э":"e","Я":"Ya","Ч":"CH","С":"S","М":"M","И":"I","Т":"T","Ь":"'","Б":"B","Ю":"YU","я":"ya","ч":"ch","с":"s","м":"m","и":"i","т":"t","ь":"'","б":"b","ю":"yu"};

    function transliterate(word){
      return word.split('').map(function (char) { 
        return a[char] || char; 
      }).join("");
    }

	jQuery('form#login').on('submit', function (e) {
        jQuery.extend(jQuery.validator.messages, {
            required: validate_object.required,
            email: validate_object.email
          });
        
        if (!jQuery(this).valid()) { 
            return false
        }

		action = 'ajaxlogin';
		username = nick = '';
		email = jQuery('form#login #email').val();
		password = jQuery('form#login #password').val();
		security = jQuery('form#login #security').val();
		ctrl = jQuery(this);
		jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_auth_object.ajaxurl,
            data: {
                'action': action,
                'password': password,
				'email': email,
				'security': security,
            },
            success: function (data) {
                if(data.loggedin){
                    redirecturl = ajax_auth_object.redirecturl_login;
                    document.location.href = redirecturl;
                } else {
                    jQuery('form#login #email').removeClass('valid').addClass('error')
                    jQuery('form#login #password').removeClass('valid').addClass('error')
                    openBox(data.message)
                }
            }
        });
        e.preventDefault();
    });

    jQuery('form#forgot-pass').on('submit', function (e) {
        jQuery.extend(jQuery.validator.messages, {
            required: validate_object.required,
            email: validate_object.email
          });
        
        if (!jQuery(this).valid()) { 
            return false
        }

		action = 'ajaxforgotpassword';
		email = jQuery('form#forgot-pass #forgot-email').val();
		security = jQuery('form#forgot-pass #securityforgot').val();
		ctrl = jQuery(this);
		jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_auth_object.ajaxurl,
            data: {
                'action': action,
				'user_login': email,
				'security': security,
            },
            success: function (data) {
                openBox(data.message)
            }
        });
        e.preventDefault();
    });

    jQuery('form#registration').on('submit', function (e) {
        jQuery.extend(jQuery.validator.messages, {
            required: validate_object.required,
            email: validate_object.email
          });
        
        if (!jQuery(this).valid()) { 
            return false
        }

		action = 'ajaxregister';
		nick = jQuery('#nick').val();
		username = transliterate(nick);
		password = jQuery('#signonpassword').val();
        email = jQuery('form#registration #emailreg').val();
		security = jQuery('#signonsecurity').val();
		cityId = jQuery('#cc_user_city_id').val();
        cityName = jQuery('#cc_user_address').val();
        phone = $('#phone').val()
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
                'cityName': cityName,
                'phone' : phone
            },
            success: function (data) {
				if (data.new_user === true) {
                    sendSMS(data.user_id, phone)
                } else {
                    openBox(data.message)
                    if(data.email_exist) {
                        jQuery('form#registration #emailreg').removeClass('valid')
                        jQuery('form#registration #emailreg').addClass('error')
                    }
                    if(data.login_exist) {
                        jQuery('#nick').removeClass('valid')
                        jQuery('#nick').addClass('error')
                    }
                    if(data.phone_exist) {
                        jQuery('#phone').removeClass('valid')
                        jQuery('#phone').addClass('error')
                    }
                }
            }
        });
        e.preventDefault();
    });

    jQuery('form#phone_login').on('submit', function (e) {
        const returnCode = Math.floor(Math.random() * 999999) + 100000
        const phone_number = $('#phone_number').val()
        const security = jQuery('form#phone_login #securityphone').val();
        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_auth_object.ajaxurl,
            data: {
                'action': 'sendSMS',
                'code': returnCode,
                'number': phone_number,
            },
            success: function (data) {
                showTab('#phone-login-tab', phone_number)
                $('form#phone-login-conf').submit(function(e) {
                    e.preventDefault()
                    const code = $('#phone-code').val()
                    if(returnCode == code) {
                        login_phone(phone_number, security)
                    } else {
                        openBox('Вы ввели неправильный код. Попробуйте еще раз.')
                    }
                })

                $('.resend-sms').click(function(e) {
                    e.preventDefault()
                    resendSMS(phone_number, returnCode)
                })
            }
        })
        e.preventDefault();
    });

    function resendSMS(phone_number, returnCode) {
        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_auth_object.ajaxurl,
            data: {
                'action': 'sendSMS',
                'code': returnCode,
                'number': phone_number,
            },
            success: function (data) {
                openBox('Мы отправили еще одно сообщение с кодом.')
            }
        })
    }

    function login_phone(phone_number, redirect) {
        action = 'ajaxloginphone';
		jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_auth_object.ajaxurl,
            data: {
                'action': action,
                'tel': phone_number
            },
            success: function (data) {
                console.log(data)
                if(data.loggedin && redirect){
                    redirecturl = ajax_auth_object.redirecturl_login;
                    document.location.href = redirecturl;
                } else {
                    openBox(data.message)
                }
            }
        });
    }

    function sendSMS(user_id, phone_number) {
        const returnCode = Math.floor(Math.random() * 999999) + 100000
        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_auth_object.ajaxurl,
            data: {
                'action': 'sendSMS',
                'code': returnCode,
                'number': phone_number,
            },
            success: function (data) {
                showTab('#phone-tab', phone_number)
                $('form#confirmation').submit(function(e) {
                    e.preventDefault()
                    const code = $('#conf_code').val()
                    if(returnCode == code) {
                        activateAccount(user_id, phone_number)
                    } else {
                        openBox('Вы ввели неправильный код. Попробуйте еще раз.')
                    }
                })

                $('.resend-sms').click(function(e) {
                    e.preventDefault()
                    resendSMS(phone_number, returnCode)
                })
            }
        });
    }

    function activateAccount(user_id, phone_number) {
        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_auth_object.ajaxurl,
            data: {
                'action': 'activate_account',
                'user_id': user_id
            },
            success: function (data) {
                if(data.success) {
                    login_phone(phone_number, false)
                    showTab('#thank-tab')
                } else {
                    openBox('Ой, что-то пошло не так.')
                }
            }
        });
    }

    function openBox(content) {
        $.fancybox.open({
            padding: [30, 60, 30, 60],
            width: 250,
            height: 30,
            content: content,
            closeBtn: false
        });
    }

    function showTab(name, phone_number) {
        $('.modal-form').hide()
        $('.modal-form p strong').html(phone_number)
        $(name).show()
    }

    if (jQuery("form#registration").length) 
        jQuery("#registration").validate({ 
            errorPlacement: function(){
                return false;
            }
        });
    else if (jQuery("#login").length) 
        jQuery("#login").validate({
            errorPlacement: function(){
                return false;
            }
        });

        jQuery("#forgot-pass").validate({ 
            errorPlacement: function(){
                return false;
            }
        })

});