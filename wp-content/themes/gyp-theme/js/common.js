jQuery(document).ready(function () {

    const $ = jQuery

    var i, j = false;
    var h = $(document).height(),
        subBtn = $('.subBtn'),
        submenu = $('.submenu'),
        menuItem = $('.menu__item'),
        submenulink = $('.submenu__link'),
        label = $('#label'),
        catBtn = $('#catBtn'),
        loginItem = $('.login__item '),
        catMobile = $('.cat-mobile'),
        link_abs = $('.link_abs'),
        delMess = $('.close-mess'),
        openDash = $('.opendash'),
        closeModal = $('.upload__control-del'),
        chslocation = $('#chslocation'),
        numtel = $('.numtel'),
        dashDrop = $('#dashDrop'),
        dashmenu = $('.dashdrop'),
        numBtn2 = $('#viewbtn2');
        numBtn = $('#viewbtn')
    var priceBlock = $('#price-block');
    var closeMenu = $('.close_menu');

        //$('#captcha').val('');

    var menubtn = $('#menubtn'),
        menu = $('.menu'),
        moreBtn = $('.btn_more'),
        tab = $('.modal-tab');
    var vWidth = $(window).width();

    if (vWidth < 780) {
        menuItem.children('a').addClass('moblink');
    }

    if (vWidth > 780) {

    };

    menuItem.hover(function () {
          /*  $(this).children('.submenu').slideDown(100, function(){

            });*/
            $(this).children('.moblink').children('.fa').toggleClass('rotate');
        },
        function () {
           /* $(this).children('.submenu').slideUp(100, function(){

            });*/
            $(this).children('.moblink').children('.fa').toggleClass('rotate');

        }
    );


    closeModal.click(function(e){
        e.preventDefault();
        modal.fadeOut();

    });

    $(window).scroll(function () {
        if (vWidth < 780) {

            var bo = $(this).scrollTop();
            var styles = {
                background: "rgba(0, 0, 0, 1) none repeat scroll 0% 0%"
            };
            var nostyles = {
                background: "white"
            };

            if (bo >= 100) {
                $('.login').fadeOut();
            }
            if (bo <= 100) {

            }

            if (bo >= 280) {
                catBtn.css({
                    right: "-89px"
                });
                


            }
            if (bo <= 280) {
                catBtn.css({
                    right: "-54px"
                });
                 catMobile.animate({
                left: "-290px"
            }, 100, function () {
                j = false;
                catMobile.css("overflow-y","inherit");
            });
            }
        }
    });

    $('.moblink').click(function (e) {
        e.preventDefault();
       
    });

    openDash.click(function (e) {
        $('.login').toggle();
    });

    $(".slick").slick({
        dots: false,
        infinite: true,
        speed: 800,
        slidesToShow: 1,
        adaptiveHeight: true,
        arrows: false,
        autoplay: true
    });


    $("#authorSlick , #mobileSlick").slick({
        infinite: false,
        speed: 800,
        slidesToShow: 5,
        slidesToScroll: 1,
        autoplay: true,
        responsive: [
            {
                breakpoint: 1312,
                settings: {
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    infinite: true,
                    dots: true
                }
            },
            {
                breakpoint: 936,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 681,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ]
    });

    menubtn.on('click', function (e) {
        e.preventDefault();

        if (i == false) {
            menu.animate({
                height: "toggle",
                left: '-100%'
            }, 400, function () {
                i = true;

            });
        }
        else {
            menu.animate({
                height: "toggle",
                left: 0
            }, 400, function () {
                i = false;
            });
        }

    });

    catBtn.click(function (e) {
        e.preventDefault();

        if (j == false) {
            catMobile.animate({
                left: 0
            }, 200, function () {
                j = true;
                catMobile.css("overflow-y","auto");
            });

        }
        else {

            catMobile.animate({
                left: "-290px"
            }, 200, function () {
                j = false;
                catMobile.css("overflow-y","inherit");
            });

        }
    });

    closeMenu.click(function (e) {

        menu.animate({
            height: "toggle",
            left: '-100%'
        }, 400, function () {
            i = true;
        });
    });



    var modal = $('#myModal');

    var btn = $(".btnModal");

    var span = $(".close_modal");

    btn.click(function (e) {
        modal.fadeIn();
		e.preventDefault();
    });

    span.click(function () {
        modal.fadeOut();
    });


    $(window).click(function (event) {
        if ($(modal).is(event.target)) {
            modal.fadeOut();

        };

        if (!$(catMobile).is(event.target)&&(!$(catBtn).is(event.target))) {
           catMobile.animate({
                left: "-290px"
            }, 200, function () {
                j = false;
                catMobile.css("overflow-y","inherit");
            });
        };

        if (!$(chslocation).is(event.target)) {
            label.animate({
                top: '6px',
                opacity: 1
            }, 300, function () {
                chslocation.val('');
            });
        }


    });

    $('#restore-back').click(() => {
        $('#restore-back').hide()
        $('#tabs').show()
        tab.removeClass('link_active');

        $('#tab1-link').toggleClass('link_active');
        var currentAttrValue = $('#tab1-link').attr('href');
        $('.modal__body ' + currentAttrValue).fadeIn(500).siblings().hide();
        $('#tab1-link').parent('li').addClass('active-tab').siblings().removeClass('active-tab');
        e.preventDefault();
    })

    tab.click(function (e) {
        if($(this).attr('id') === 'restore-btn') {
            jQuery('#tabs').hide()
            $('#restore-back').show()
        }
        tab.removeClass('link_active');
        $(this).toggleClass('link_active');
        const currentAttrValue = $(this).attr('href');
        $('.modal-form').removeClass('active-tab').hide()
        $(currentAttrValue).addClass('active-tab').show()
        $('.modal__body ' + currentAttrValue).fadeIn(500).siblings().hide();
        $(this).parent('li').addClass('active-tab').siblings().removeClass('active-tab');
        e.preventDefault();
    });

    $('.modal-tab-phone-login').click(function(e) {
        const currentAttrValue = $(this).data('tab');
        $('.modal-form').removeClass('active-tab').hide()
        $(currentAttrValue).addClass('active-tab').show()
    })

    $('.modal__btn__forgot').click(function(e) {
        const currentAttrValue = $(this).data('tab');
        $('.modal-form').hide()
        $(currentAttrValue).show()
    })

    $('.btn_descr').click(function (e) {
        e.preventDefault();
        $('.advert__descr').slideToggle();
    });

    $('.messBtn').click(function () {
        if (vWidth < 650) {
            $('.advert__mess-wrp').slideToggle();
        }
        else {
            e.preventDefault();
        }
    });


    chslocation.click(function (e) {
        e.preventDefault();
        label.animate({
            top: '-100%',
            opacity: 0
        }, 100, function () {

        });
    });


    link_abs.click(function (e) {
        labelAnim();
    });
    label.click(function (e) {
        labelAnim();
        $(chslocation).focus();
    });
    chslocation.keypress(function (e) {
        var key = e.which;
        if (key == 13)  // the enter key code
        {
            labelAnim();
        }
    });


    function labelAnim() {
        var val = chslocation.val();
        label.text(val);
        chslocation.val('');
        label.animate({
            top: '6px',
            opacity: 1
        }, 300, function () {
        });
    }

    /*fancy*/

  
    $('.fancybox, .link_del').fancybox({
        padding: 0,
        titleShow: false
    });

    $('.btn_nodel').click(function(e){
        e.preventDefault();
        $.fancybox.close();
    });

    $('.delete-ad').click(function (e){
     $('.btn_del').attr('href', $(this).attr('data-link'));
       
    });
    $('.delete-mess').click(function (e){
     $('.btn_del').attr('href', $(this).attr('data-link'));
      
    });

    $('.search-list__phone-mobile').each((index, elem) => {
        const phoneNumber = $(elem).find('.telnumber').text()
        const telHidden = $(elem).find('.shownum')
        const showButton = $(elem).find('.show_phone')
        const link = $(elem).find('a')

        const newstr = phoneNumber.replace(/-/g, "");
        const last = newstr.slice(-4);
        const first = newstr.slice(0, -4);
        const hidefull = first + '-' + 'XX' + '-' + 'XX';
        telHidden.text(hidefull)

        showButton.click((e) => {
            e.preventDefault();
            showButton.hide()
            telHidden.text(phoneNumber);
            link.prop('href', `#callFeedback`)
            link.addClass('fancybox-feedback')
            link.addClass('phone-link')
            const post_id = telHidden.attr("id").replace("tel", "");
            jQuery.ajax({
                type: 'POST',
                dataType: 'json',
                url: commonjs_object.ajaxurl,
                data: {
                  'action': 'phone_views',
                  'post_id': post_id,
                },
            });
        })
    })    

    function hideNumber(str) {
        var newstr = str.replace(/-/g, "");
        var last = newstr.slice(-4);
        var first = newstr.slice(0, -4);
        var hidefull = first + '-' + 'XX' + '-' + 'XX';
        $('.nuber-tel').text(hidefull);
    }

    var telnomer = $('#phonenumhid').text();
    hideNumber(telnomer);
	
    numBtn.click(showTel)
    numBtn2.click(showTel)

    function showTel(e) {
        e.preventDefault();
        numBtn2.hide()
        numBtn.hide()
        $('.link_visible').hide();
        $('.nuber-tel').text(telnomer);
        $('.tel a').prop('href', `#callFeedback`)
        $('.tel a').addClass('fancybox-feedback')
		var post_id = $(this).parent().find('span').attr("id");
		post_id = post_id.replace("tel", "");
		jQuery.ajax({
            type: 'POST',
			dataType: 'json',
            url: commonjs_object.ajaxurl,
            data: {
              'action': 'phone_views',
              'post_id': post_id,
            },
        });
    }

    $('.link_visible').click(function (e) {
        e.preventDefault();
        numBtn.trigger('click');
    });

    delMess.click(function(e){
        e.preventDefault();
        $(this).parent().fadeOut();
    })

    function updateCatsSearch() {
        const values = $('#search-cats').find('input:checkbox').map(function() {
            const values = [];
            if($(this).prop('checked'))
                return this.value
            
        }).get()
        
        $('#search-cat').val(values)    
    }

    $('#search-cats').find('input').click(function() {
        updateCatsSearch()
    })

    jQuery('.search-sidebar__maincat').click(function() {
        const value = $(this).find('input').prop('checked')
        $(this).parent().find('input').map(function() {
            $(this).prop('checked', value)
        })
        updateCats()
    })

    function setCats(){
        const cats = $('#search-cat').val()
        $('#search-cats').find('input').map(function() {
            const input = $(this)

            if(cats.indexOf(input.val()) > -1 ){
                input.prop('checked', true)
            }
            
        }).get()
    }

    setCats();

    (function setState() {
        const stateContainer = $('.single__state')
        const state = stateContainer.data('state')
        for(let i = 1; i <= 5; i ++) {
            if (i <= state) {
                stateContainer.append('<span class="state__arrow">★ </span>')
            } else {
                stateContainer.append('<span class="state__arrow state__arrow-grey">★ </span>')
            }

        }
    })();

    (function openCats() {
        const items = $('.maincat__item')
        const item_height = items.height()
        items_uparrow = $('.maincat__item__title .uparrow')
        items_title = $('.maincat__item__title span')
        items.click(function(e){
            const list = $(this).find('.maincat__item-subs')
            const all_lists = $('.maincat__item-subs')
            const item_arrow = $(this).find('.maincat__item__title .arrow')
            const item_uparrow = $(this).find('.maincat__item__title .uparrow')
            const item_title = $(this).find('.maincat__item__title span')
            items_uparrow.hide()
            item_arrow.show()
            items_title.removeClass('selected_cat')

            const elementInRow = Math.round($(this)[0].parentElement.clientWidth / $(this)[0].clientWidth)
            const offsetLeft = $(this)[0].offsetLeft

            if(elementInRow  === 3) {
                if(offsetLeft === 0) list.addClass('arrow15')
                else if(offsetLeft > 300 && offsetLeft < 500) list.addClass('arrow50')
                else list.addClass('arrow85')
            } else if(elementInRow === 2) {
                if(offsetLeft === 0) list.addClass('arrow25')
                else list.addClass('arrow75')
            } else if(elementInRow === 1) {
                list.addClass('arrow50')
            }

            if ($(this).data('opened') === 'true') {
                items.data('opened', 'false')
                all_lists.hide()
                items.height(item_height)
            } else {
                $(this).data('opened', 'true')
                item_uparrow.show()
                item_arrow.hide()
                item_title.addClass('selected_cat')
                all_lists.hide()
                list.show()
                const list_height = list[0].clientHeight
                const height = list_height + item_height + 20
                items.height(item_height)                
                $(this).height(height)
            }
        })
    })();

    const btnSuccess = $('#call-feedback__success')
    const failSuccess = $('#call-feedback__fail')
    const feedbackTabs = $('.call-feedback__tab')

    btnSuccess.click(() => {
        feedbackTabs.hide()
        $('#successTab').show()
    })

    failSuccess.click(() => {
        feedbackTabs.hide()
        $('#failTab').show()
    })

    const feedbackReason = $('.call-feedback__reasons ul li')

    feedbackReason.click(function(e) {
        const text = $(this).text()
        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: commonjs_object.ajaxurl,
            data: {
              'action': 'send_feedback',
              'post_id': $('#post_id').val(),
              'text': text
            },
            success: function(data) {
                feedbackTabs.hide()
                $('.additional-text').show()
                $('#successTabText').show()
            }
        });
    }) 

    $(".fancybox-feedback").fancybox({
        padding: 0,
        afterClose: function() {
            feedbackTabs.hide()
            $('#mainTab').show()
        }
    });

    $('.fancybox-send-msg').fancybox({
        padding: 0,
        beforeLoad: function() {
            $('#message_to').val($('#author_id').val())
            $('#message_from').val($('#user_id').val())
            $('#message_title').val(`Message from ${$('#user_name').val()}`)
        }
    })

    function updateURLParameter(url, param, paramVal){
    var newAdditionalURL = "";
    var tempArray = url.split("?");
    var baseURL = tempArray[0];
    var additionalURL = tempArray[1];
    var temp = "";
    if (additionalURL) {
        tempArray = additionalURL.split("&");
        for (var i=0; i<tempArray.length; i++){
            if(tempArray[i].split('=')[0] != param){
                newAdditionalURL += temp + tempArray[i];
                temp = "&";
            }
        }
    }

    var rows_txt = temp + "" + param + "=" + paramVal;
    return baseURL + "?" + newAdditionalURL + rows_txt;
}

    let opened = false;

    $('.header__category__container').click(function() {
        if($('.header__category__window').css('display') == 'none'){
            $('.header__category__window').show()
            $('.hover_dark').show();
            const firstElement = $('.header_switcher').first()
            showCategoty(firstElement, true)
        } else {
            if(opened) {
                $('.header__category__right, .back__white').hide()
                $('#category-opener').find('.header__category__text-container div').html('Категории')
                $('#category-opener').find('.header-category-img').show()
                $('#category-opener').find('.header__category__text-container-img').show()
                $('#category-opener .header__category__text-container i').remove()
                opened = false
            } else {
                $('.header__category__window').hide()
                $('.header__category__right, .back__white').hide()
            }
        }
    })

    // $('.close-categories').click(function() {
    //     $('.header__category__right').hide()        
    //     $('.header__category__window').hide()
    // })
    jQuery('.close-categories').click(function(){
            jQuery('.header__category__window-small').hide();
    });

    function showCategoty(node, hover) {
        if($(window).width() > 768 && hover) {
            $('.header__category__right, .back__white').hide()
            $(node).find('.header__category__right, .back__white').show()
            const leftWidth = $(node).width();
            const containerWidth = $('.form_srch-header').width()
            const height = $('.header__category__window').height()
    
            $('.header__category__right , .back__white').width(containerWidth-leftWidth)
            $('.header__category__right').css('left', leftWidth)
            $('.header__category__right').height(height + 50)
            $('.back__white').height(height);
        } else if($(window).width() < 768 && !hover){
            opened = true

            const title = $(node).find('b').html()

            $('.header__category__right, .back__white').hide()
            const list = $(node).find('.header__category__right, .back__white')
            list.show()

            const opener = $('#category-opener')
            opener.find('.header-category-img').hide()
            opener.find('.header__category__text-container div').html(title)
            opener.find('.header__category__text-container-img').hide()
            const textContainer = $('#category-opener .header__category__text-container')
            textContainer.html('<i class="fas fa-arrow-left"></i>' + textContainer.html())

            const height = jQuery('.header__category__window-small').height()

            $('.header__list').css('padding', 0)
            $('.header__list').css('overflow', 'scroll')
            $('.header__list').height(height)
            $('.header__category-list__item').addClass('header__category__btn header_category__small-link')
        }
    }

    $('.header_switcher').hover(function() {
        showCategoty(this, true)
    })

    $('.header_switcher').click(function(e) {
        showCategoty(this, false)
    })    

    $('.hide-map').click(function(e) {
        $(this).hide()
        $('.show-map').show()

        $('.map').height(50)
    })

    $('.show-map').click(function(e) {
        $(this).hide()
        $('.hide-map').show()

        $('.map').height(450)
    })

    $('.search-list__call').click(function() {
        const button = $(this)
        $('.call-feedback__author img').attr('src', button.find('input[name="image"]').val())
        $('.call-feedback__name a').text(button.find('input[name="author_name"]').val())
        $('.call-feedback__name span').text(button.find('input[name="city"]').val())
        $('.call-feedback__number a').attr('href', `tel:${button.find('input[name="phone"]').val()}`)
        $('.call-feedback__number a').text(button.find('input[name="phone"]').val())
        $('#post_id').val(button.find('input[name="post_id"]').val())
    })

    $('.filters__title-mobile , .button_filter_circle , .button_filter').click(function() {
        if($('.filters__container').css('display') == 'none'){
            $('.filters__container , .save-btn').show()
        } else {
            $('.filters__container , .save-btn').hide()
        }
    })



    $('.header__search-show-btn').click(function() {
        $(this).hide()
        $('.hover_dark').show();
        $('.form_srch-header').show()
    })

    function updateCats() {
        const values = {}
        $('.filters').find('input[type="checkbox"]').map(function() {
            if($(this).prop('checked')) {
                const key = values[$(this).data('filter')]
                const value = this.value
                if(key) {
                    key.push(value)
                } else {
                    values[$(this).data('filter')] = [value]
                }
            }
        })
        $('.filter_value_field').val('')
        Object.keys(values).map(key => {
            $(`.${key}`).val(values[key].join())
        })
    }

    jQuery('.filters').find('input[type="checkbox"]').click(function() {
        updateCats()
    });

    (function initCheckbox() {
        const filtersVals = $('#filtersVals').val()
        if(filtersVals) {
            const vals = filtersVals.split(',')
            $('.filters').find('input').map(function() {
                if(vals.indexOf(this.value) > -1){
                    $(this).prop('checked', true)
                }
            })
        }
    })()

    $('.filters__show-btn').click(function() {
        const parent = $(this).parent()
        const filters = parent.find('.filters__vis-container')
        if(filters.css('display') == 'none'){
            filters.show()
            $(this).find('.filters__show-btn__arrow').addClass('filters__show-btn__arrow-up')
        } else {
            filters.hide()
            $(this).find('.filters__show-btn__arrow').removeClass('filters__show-btn__arrow-up')
        }
    })

    $('.header__search-btn_container').click(function(e) {
        if(document.location.href.indexOf('search') > -1){
            const url = document.location.href.replace(/page\/\d*\//, '')
            e.preventDefault()
            const params = $('.form_srch-header').serializeArray()
            const href = params.reduce((accum, param) => {
                return updateURLParameter(accum, param.name, param.value)
            }, url)
    
            document.location.href = href
        } else {
            $('.form_srch-header').submit()
        }

        // $('.form_srch-header').submit()

        // if($('#autocomplete-field').val()){
        // } else {

        // }
    })

    $('.form-submit').submit(function(e) {
        e.preventDefault()
        const url = document.location.href.replace(/page\/\d*\//, '')
        const params = $(this).serializeArray()
        const href = params.reduce((accum, param) => {
            return updateURLParameter(accum, param.name, param.value)
        }, url)

        document.location.href = href
    })

    $('.filters').submit(function(e) {
        e.preventDefault()
        const url = document.location.href.replace(/page\/\d*\//, '')
        const params = $(this).serializeArray()
        const href = params.reduce((accum, param) => {
            return updateURLParameter(accum, param.name, param.value)
        }, url)

        document.location.href = href
    })

    $('.input_srch-header-btn').keypress(function(e) {
        return false
    });

    $('.input_srch-header-btn').click(function(e) {
        e.preventDefault()
        const cities = $('.header-category-cities')
        if(cities.css('display') == 'none'){
            cities.show()
        } else {
            cities.hide()
        }
    })

    $('.header-category-city').click(function(e) {
        e.preventDefault()
        console.log('click')
        const city = $(this)
        const name = city.text()
        const id = city.data('id')
        
        $('#s_addresss').val(name)
        $('#s_city_id').val(id)

        $('.header-category-cities').hide()
    })
    /*Hide item filters if count more 5*/
    $.each($('.filters__container'), function () { 
             if($(this).find('.checkbox-container').length > 5){
                $(this).children('.filters__vis-container').hide();
        }
    });
    /*Truncate title text in mobile version*/
    if ($(window).width() <= 480){
        $('.search-list__title').each(function(){
            if($(this).children('a').text().length > 40){
                var text = $(this).children('a').text();
                var shorttext = $.trim(text).substring(0, 30) + '...';
                $(this).children('a').text(shorttext);
            }
        });
    }

});
