$(document).ready(function () {

    var i, j = false;
    var h = $(document).height(),
        subBtn = $('.subBtn'),
        submenu = $('.submenu'),
        menuItem = $('.menu__item'),
        label = $('#label'),
        catBtn = $('#catBtn'),
        catMobile = $('.cat-mobile'),
        link_abs = $('.link_abs'),
        openDash = $('.opendash'),
        chslocation = $('#chslocation'),
        numtel = $('.numtel'),
        numBtn = $('#viewbtn');
    closeMenu = $('.close_menu');
    console.log(h);

    $(':checkbox').each(function() {

    });

    $('.add__check, .radio, .my-select').styler();

    var menubtn = $('#menubtn'),
        menu = $('.menu'),
        moreBtn = $('.btn_more'),
        tab = $('.modal-tab');
    var vWidth = $(window).width();

    if (vWidth < 780) {
        menuItem.children('a').addClass('moblink');
    }

    if (vWidth > 780) {
        menuItem.hover(function () {
            $(this).children('.submenu').stop().slideDown(100);
        },
            function () {
                $(this).children('.submenu').slideUp(100);
            }
        );
    };

    $( window ).scroll(function() {
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
            }
        }
    });


    $('.moblink').click(function (e) {
        e.preventDefault();
        var rot = $(this).children('.fa');
        var clickIndex = $('.moblink').index(this);
        var elem = $('.submenu').eq(clickIndex);
        $('.submenu:visible').not(elem).slideUp(function(){
            $('.fa').removeClass('rotate');
        });
        elem.slideToggle(function () {
            rot.toggleClass('rotate');
        });
    });

    openDash.click(function(e){
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


    $("#authorSlick").slick({
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
            console.log('false');
        }
        else {
            menu.animate({
                height: "toggle",
                left: 0
            }, 400, function () {
                i = false;
            });
            console.log('true');
        }

    });

    catBtn.click(function(e){
        e.preventDefault();

        if (j == false) {
            catMobile.animate({
                left: 0
            }, 200, function () {
                j = true;
            });

        }
        else {

            catMobile.animate({
                left: "-290px"
            }, 200, function () {
                j = false;
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

    console.log(vWidth);


    var modal = $('#myModal');

    var btn = $(".btnModal");

    var span = $(".close_modal");

    btn.click(function () {
        modal.fadeIn();
    });

    span.click(function () {
        modal.fadeOut();
    });


    $(window).click(function (event) {
        if ($(modal).is(event.target)) {
            modal.fadeOut();
            console.log('modal');
        }

        if (!$(chslocation).is(event.target)) {
            label.animate({
                top: '6px',
                opacity: 1
            }, 300, function () {
                chslocation.val('');
            });
        }

    });

    tab.click(function () {
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

    $('.fancybox').fancybox();

function hideNumber(str){
    var newstr = str.replace(/-/g, "" );
    var last = newstr.slice(-4);
    var first =newstr.slice(0, -4);
    var hidefull = first+'-'+'XX'+'-'+'XX';
    $('.nuber-tel').text(hidefull);
}
    var telnomer = numtel.text();
    hideNumber(numtel.text());

    var log = true;

    numBtn.click(function(e){
        e.preventDefault();

        if (log){
            $(this).text('Приховати');
            $('.link_visible').text('Приховати');
            $('.nuber-tel').text(telnomer);
            log = false;
        }
        else {
            $(this).text('Показати');
            $('.link_visible').text('Показати');
            hideNumber(numtel.text());
            log = true;
        }

    });
    $('.link_visible').click(function(e){
        e.preventDefault();
        numBtn.trigger('click');
    })

});