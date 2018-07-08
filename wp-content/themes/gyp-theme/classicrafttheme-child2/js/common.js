$(document).ready(function () {

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


    $("#authorSlick , mobileSlick").slick({
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
        var currentAttrValue = $(this).attr('href');
        $('.modal__body ' + currentAttrValue).fadeIn(500).siblings().hide();
        $(this).parent('li').addClass('active-tab').siblings().removeClass('active-tab');
        e.preventDefault();
    });

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
        title:false
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

    $('.search-list__phone').each((index, elem) => {
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
            link.prop('href', `tel:${phoneNumber}`)
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
        $('.tel a').prop('href', `tel:${telnomer}`)
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


    //$('input:checkbox[name=custom-price]').prop('checked', false).trigger('refresh');




    /*$('input:checkbox[name=custom-price]').change(function () {
        if ($(this).is(':checked')) {
            priceBlock.slideUp();
            $(this).parents('label').addClass('price-choise');
            $('#cc_price').val('0');
        }
        else {
            priceBlock.slideDown();
            $(this).parents('label').removeClass('price-choise');
            $('#cc_price').val('');
        }

    });*/


    /*--------------select--------------*/

    $("#category").change(function () {
        if ($(this).val() == 'cake') {
            $('#categotyin').fadeOut();
            $('#taste').fadeIn();
            $('#events').fadeIn();


        }
        /* if ($(this).val() == 'default'){
         $('#taste').fadeOut();
         $('#categotyin').fadeOut();
         }*/
        else if (($(this).val() != 'cake') && ($(this).val() != 'default')) {
            $('#taste').fadeOut();
            $('#events').fadeOut();
            $('#categotyin').fadeIn();
        }
        else {
            $('#taste').fadeOut();
            $('#events').fadeOut();
            $('#categotyin').fadeOut();
        }
    });

    delMess.click(function(e){
        e.preventDefault();
        $(this).parent().fadeOut();
    })

    function updateCats() {
        const values = $('#search-cats').find('input').map(function() {
            const values = [];
            if($(this).prop('checked'))
                return this.value
            
        }).get()
        
        $('#search-cat').val(values)    
    }

    $('#search-cats').find('input').click(function() {
        updateCats()
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

            if(cats.indexOf(input.val()) > 0 ){
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

});
