<html lang="ru">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=0.75">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600&family=Roboto+Condensed:wght@400;700&family=Roboto:wght@400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">

    <title><?=$title?></title>

    <!-- Facebook Pixel Code -->
    <script>
    ! function(f, b, e, v, n, t, s) {
        if (f.fbq) return;
        n = f.fbq = function() {
            n.callMethod ?
                n.callMethod.apply(n, arguments) : n.queue.push(arguments)
        };
        if (!f._fbq) f._fbq = n;
        n.push = n;
        n.loaded = !0;
        n.version = '2.0';
        n.queue = [];
        t = b.createElement(e);
        t.async = !0;
        t.src = v;
        s = b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t, s)
    }(window, document, 'script',
        'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '748205805612998');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=748205805612998&ev=PageView&noscript=1" /></noscript>
    <!-- End Facebook Pixel Code -->

</head>

<body class="body">
    <header class=" header">
        <div class="container">
            <nav class="nav">
                <div class="nav__block">
                    <div class="nav__item nav__title-block">
                        <a href="<?=$site_url?>"><strong>V9KY.INUA</strong></a>
                        &nbsp;|&nbsp;&nbsp;<?=$gorod_en['name_en']?>&nbsp;&nbsp;|&nbsp;&nbsp;<?=$season_name['season']?>
                    </div>

                    <div class="nav__item">
                        <div>
                            <a class="button-nav button--choose-city" href="<?=$url;?>/addmyteam/">
                                <img src="/css/components/header/assets/images/ukraine-logo.svg" alt="ukraine-logo">
                                <span>Обери місто</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="nav__block">
                    <a class="button-nav button-with-arrow button--order-team" href="<?=$url;?>/addmyteam/">
                        <p class="button-with-arrow__title">Заявити команду</p>
                        <p class="button-with-arrow__description">на найближчі турніри</p>
                        <img src="/css/components/header/assets/images/arrow.svg" alt="arrow"
                            class="button-with-arrow__icon">
                    </a>

                    <a class="button-nav button--v9ku-logo" href="<?=$site_url?>">
                        <img src="/css/components/header/assets/images/v9ku-logo.png" alt="v9ku-logo"
                            class="button--img-v9ku-logo">
                    </a>

                    <a class="button-nav button-with-arrow button--find-team" href="<?=$url;?>/addme1/">
                        <p class="button-with-arrow__title">Подати заявку</p>
                        <p class="button-with-arrow__description">на пошук команди</p>
                        <img src="/css/components/header/assets/images/arrow.svg" alt="arrow"
                            class="button-with-arrow__icon">
                    </a>
                </div>

                <div class="nav__block">
                    <div class="nav__item">
                        <div>
                            <a class="button-nav button--choose-city" href="#">
                                <img src="/css/components/header/assets/images/ukraine-logo.svg" alt="ukraine-logo">
                                <span>Обери місто</span>
                            </a>
                        </div>
                    </div>

                    <div class="nav__item">
                        <a class="button-nav button--call-to" href="tel:+380934319492">
                            <img src="/css/components/header/assets/images/phone-icon.svg" alt="phone-icon">
                            <span>(093)431-94-92</span>
                        </a>
                    </div>

                    <div class="nav__item nav__item--social-link">
                        <a class="button-nav button--social-link" href="https://www.facebook.com/v9ky.ukraine/"
                            target="_blank">
                            <img src="/css/components/header/assets/images/facebook-icon.svg" alt="facebook-icon">
                        </a>

                        <a class="button-nav button--social-link"
                            href="https://www.youtube.com/c/V9kyInUa?sub_confirmation=1" target="_blank">
                            <img src="/css/components/header/assets/images/youtube-icon.svg" alt="youtube-icon">
                        </a>

                        <a class="button-nav button--social-link"
                            href="https://www.instagram.com/v9ky.ukraine/?igshid=YmMyMTA2M2Y%3D" target="_blank">
                            <img src="/css/components/header/assets/images/instagram-icon.svg" alt="instagram-icon">
                        </a>
                    </div>
                </div>
            </nav>
        </div>
    </header>
    <main class="main">

    