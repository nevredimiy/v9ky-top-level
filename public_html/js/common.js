
$('.icon-menu').click(function () {

    $('.mobile-menu').animate({
        left: '0px'
    }, 200);

    $('body').animate({
        left: '285px'
    }, 200);

});

/* Закрытие меню */
$('.icon-close').click(function () {
    $('.mobile-menu').animate({
        left: '-285px'
    }, 200);

    $('body').animate({
        left: '0px'
    }, 200);
});

/* Открытие меню */
var main = function () {



    $('.tabs').mouseover(function () {
        $(this).find('ul').css('display', 'block');
    })

    $('.tabs').mouseout(function () {
        $(this).find('ul').css('display', 'none');
    })

    $('#menu li').mouseover(function () {
        $(this).find('ul').css('display', 'block');
    })

    $('#menu li').mouseout(function () {
        $(this).find('ul').css('display', 'none');
    })

    $("#amazingslider-1").amazingslider({
        sliderid: 1,
        width: 600,
        height: 300,
        skinsfoldername: "",
        loadimageondemand: false,
        donotresize: false,
        enabletouchswipe: true,
        fullscreen: false,
        autoplayvideo: false,
        addmargin: false,
        randomplay: false,
        isresponsive: true,
        pauseonmouseover: false,
        playvideoonclickthumb: true,
        slideinterval: 5000,
        fullwidth: false,
        transitiononfirstslide: false,
        scalemode: "fill",
        loop: 0,
        autoplay: true,
        navplayvideoimage: "play-32-32-0.png",
        navpreviewheight: 60,
        timerheight: 1,
        descriptioncssresponsive: "font-size:12px;",
        shownumbering: false,
        skin: "Light",
        addgooglefonts: true,
        navshowplaypause: true,
        navshowplayvideo: true,
        navshowplaypausestandalonemarginx: 8,
        navshowplaypausestandalonemarginy: 8,
        navbuttonradius: 0,
        navthumbnavigationarrowimageheight: 32,
        navmarginy: 16,
        lightboxshownavigation: false,
        showshadow: false,
        navfeaturedarrowimagewidth: 16,
        navpreviewwidth: 120,
        googlefonts: "Inder",
        navborderhighlightcolor: "",
        bordercolor: "#ffffff",
        lightboxdescriptionbottomcss: "{color:#333; font-size:12px; font-family:Arial,Helvetica,sans-serif; overflow:hidden; text-align:left; margin:4px 0px 0px; padding: 0px;}",
        lightboxthumbwidth: 80,
        navthumbnavigationarrowimagewidth: 32,
        navthumbtitlehovercss: "text-decoration:underline;",
        texteffectresponsivesize: 600,
        navcolor: "#999999",
        arrowwidth: 48,
        texteffecteasing: "easeOutCubic",
        texteffect: "slide",
        lightboxthumbheight: 60,
        navspacing: 4,
        navarrowimage: "navarrows-28-28-0.png",
        ribbonimage: "ribbon_topleft-0.png",
        navwidth: 109,
        navheight: 72,
        arrowimage: "arrows-48-48-4.png",
        timeropacity: 0.6,
        arrowhideonmouseleave: 1000,
        navthumbnavigationarrowimage: "carouselarrows-32-32-3.png",
        navshowplaypausestandalone: false,
        texteffect1: "slide",
        navpreviewbordercolor: "#ffffff",
        customcss: "",
        ribbonposition: "topleft",
        navthumbdescriptioncss: "display:block;position:relative;padding:2px 4px;text-align:left;font:normal 12px Arial,Helvetica,sans-serif;color:#333;",
        lightboxtitlebottomcss: "{color:#333; font-size:14px; font-family:Armata,sans-serif,Arial; overflow:hidden; text-align:left;}",
        arrowstyle: "mouseover",
        navthumbtitleheight: 20,
        textpositionmargintop: 24,
        navswitchonmouseover: false,
        playvideoimage: "playvideo-64-64-0.png",
        arrowtop: 50,
        textstyle: "static",
        playvideoimageheight: 64,
        navfonthighlightcolor: "#666666",
        showbackgroundimage: false,
        navpreviewborder: 4,
        navshowplaypausestandaloneheight: 48,
        shadowcolor: "#aaaaaa",
        navbuttonshowbgimage: true,
        navbuttonbgimage: "navbuttonbgimage-28-28-0.png",
        textbgcss: "display:block; position:absolute; top:0px; left:0px; width:100%; height:100%; background-color:#333333; opacity:0.6; filter:alpha(opacity=60);",
        textpositiondynamic: "bottomleft",
        navpreviewarrowwidth: 16,
        playvideoimagewidth: 64,
        navshowpreviewontouch: false,
        bottomshadowimagewidth: 110,
        showtimer: true,
        navradius: 0,
        navmultirows: false,
        navshowpreview: false,
        navpreviewarrowheight: 8,
        navmarginx: 16,
        navfeaturedarrowimage: "featuredarrow-16-8-0.png",
        showribbon: false,
        navstyle: "thumbnails",
        textpositionmarginleft: 24,
        descriptioncss: "display:block; position:relative; font:12px \"Lucida Sans Unicode\",\"Lucida Grande\",sans-serif,Arial; color:#fff; margin-top:8px;",
        navplaypauseimage: "navplaypause-48-48-0.png",
        backgroundimagetop: -10,
        timercolor: "#ffffff",
        numberingformat: "%NUM/%TOTAL ",
        navdirection: "horizontal",
        navfontsize: 12,
        navhighlightcolor: "#333333",
        texteffectdelay1: 1000,
        navimage: "bullet-24-24-5.png",
        texteffectduration1: 600,
        navshowplaypausestandaloneautohide: true,
        navbuttoncolor: "",
        navshowarrow: false,
        texteffectslidedirection: "left",
        navshowfeaturedarrow: true,
        lightboxbarheight: 64,
        titlecss: "display:block; position:relative; font:bold 14px \"Lucida Sans Unicode\",\"Lucida Grande\",sans-serif,Arial; color:#fff;",
        ribbonimagey: 0,
        ribbonimagex: 0,
        texteffectslidedistance1: 120,
        navrowspacing: 8,
        navshowplaypausestandaloneposition: "bottomright",
        navshowbuttons: false,
        lightboxthumbtopmargin: 12,
        titlecssresponsive: "font-size:12px;",
        navshowplaypausestandalonewidth: 48,
        navfeaturedarrowimageheight: 8,
        navopacity: 0.7,
        textpositionmarginright: 24,
        backgroundimagewidth: 120,
        textautohide: true,
        navthumbtitlewidth: 120,
        navpreviewposition: "top",
        texteffectseparate: false,
        arrowheight: 48,
        arrowmargin: 0,
        texteffectduration: 600,
        bottomshadowimage: "bottomshadow-110-95-4.png",
        border: 4,
        lightboxshowdescription: false,
        timerposition: "bottom",
        navfontcolor: "#333333",
        navthumbnavigationstyle: "arrowinside",
        borderradius: 0,
        navbuttonhighlightcolor: "",
        textpositionstatic: "bottom",
        navthumbstyle: "imageonly",
        texteffecteasing1: "easeOutCubic",
        textcss: "display:block; padding:12px; text-align:left;",
        navbordercolor: "#fff",
        navpreviewarrowimage: "previewarrow-16-8-0.png",
        navthumbtitlecss: "display:block;position:relative;padding:2px 4px;text-align:left;font:bold 14px Arial,Helvetica,sans-serif;color:#333;",
        showbottomshadow: false,
        texteffectslidedistance: 30,
        texteffectdelay: 500,
        textpositionmarginstatic: 0,
        backgroundimage: "",
        navposition: "bottom",
        texteffectslidedirection1: "right",
        navborder: 2,
        textformat: "Bottom bar",
        bottomshadowimagetop: 98,
        texteffectresponsive: true,
        shadowsize: 5,
        lightboxthumbbottommargin: 8,
        textpositionmarginbottom: 24,
        lightboxshowtitle: true,
        slide: {
            duration: 1000,
            easing: "easeOutCubic",
            checked: true
        },
        transition: "slide",
        scalemode: "fill",
        isfullscreen: false,
    });


};

$(document).ready(function () {

    $('.breadcrumb a').click(function () {
        $('.breadcrumb a').removeClass('active-tab-content');
        $(this).addClass('active-tab-content');
    })


    $('#href1').click(function (e) {
        e.preventDefault();
        $('section').removeClass('active-content');
        $('.main-content-page').find('#tab1').toggleClass('active-content');
    })

    $('#href2').click(function (e) {
        e.preventDefault();
        $('section').removeClass('active-content');
        $('.main-content-page').find('#tab2').toggleClass('active-content');
    })

    $('.acc-header').on('click', function () {
        $(this).toggleClass('toggle-color');
        $(this).find('span').toggleClass('toggle-back');
        $(this).parent().find('.acc-content').slideToggle(400);
    })

    $(".tabs").click(function (e) {
        $(".tabs span").removeClass('active-tabs');
        $(this).find("span").toggleClass('active-tabs');
    })

    $(".tabs").click(function (e) {
        $(".tabs").removeClass('active-tab');
        $(this).toggleClass('active-tab');
    })


    $(window).scroll(function () {
        if ($(this).scrollTop() > 0) {
            $('p.scroll-up').fadeIn();
        } else {
            $('p.scroll-up').fadeOut();
        }
    });

    $("p.scroll-up").click(function () {
        $.scrollTo($("header"), 800, {
            offset: -90
        });
    });

    var owl = $(".slider-thumb");

    owl.owlCarousel({
        items: 6,
        nav: false,
        loop: true,
        responsiveClass: true,
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 3
            },
            1000: {
                items: 6
            }
        }
    });



    var owl = $(".slider-thumb-photo");

    owl.owlCarousel({
        items: 5,
        nav: true,
        loop: true,
        responsiveClass: true,
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 3
            },
            1000: {
                items: 5
            }
        }
    });

    $('.slider-spons').slick({
        vertical: true,
        autoplay: true,
        autoplaySpeed: 5000,
        slidesToScroll: -1,
        accessibility: false
    });


    $("#top").click(function () {
        $("body, html").animate({
            scrollTop: 0
        }, 800);
        return false;
    });

    var sliderItemHeight = $('.slide').height(),
        sliderHeight = sliderItemHeight,
        count = $('.slide').length,
        start = 1;



    $('#gallery-1').royalSlider({
        fullscreen: {
            enabled: true,
            nativeFS: true
        },
        controlNavigation: 'thumbnails',
        autoScaleSlider: true,
        autoScaleSliderWidth: 960,
        autoScaleSliderHeight: 850,
        loop: true,
        imageScaleMode: 'fit-if-smaller',
        navigateByClick: true,
        numImagesToPreload: 2,
        arrowsNav: true,
        arrowsNavAutoHide: false,
        arrowsNavHideOnTouch: false,
        keyboardNavEnabled: true,
        fadeinLoadedSlide: true,
        globalCaption: true,
        globalCaptionInside: false,
        thumbs: {
            appendSpan: true,
            firstMargin: true,
            paddingBottom: 4
        }
    });

    
});

