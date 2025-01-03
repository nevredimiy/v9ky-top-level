<? 
  $season_name = $db->Execute("select season from v9ky_turnir where name='".$tournament."'");
  $gorod_en = $db->Execute("select * from v9ky_city where id=(select city from v9ky_turnir where name='".$tournament."')");
?>
<!--[if lt IE 7]><html lang="ru" class="lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if IE 7]><html lang="ru" class="lt-ie9 lt-ie8"><![endif]-->
<!--[if IE 8]><html lang="ru" class="lt-ie9"><![endif]-->
<!--[if gt IE 8]><!-->
<html lang="ru">
<!--<![endif]-->

<head>
   
    <title><?=$title?></title>

    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=0.75" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600&family=Roboto+Condensed:wght@400;700&family=Roboto:wght@400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css" />

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

    <?php 
      // Когда tournament пустая это ознчает, что в адресной строке нет названия тура. Обычно это надпись после слеша в адресной строке
      // Если переменная tournament пустая, то заполняем ее из последнего сезона первым туром

        // Стартуем сессию если оне ещё не запущена.
        if (session_status() == PHP_SESSION_NONE) { 
            session_start(); 
        }

        // Определяем текущий выбранный пункт меню
        $selectedTournament = isset($_SESSION['tournament']) ? $_SESSION['tournament'] : '';

        // Если был отправлен GET-запрос с выбором меню
        if (isset($_GET['tournament'])) {
            $selectedTournament = $_GET['tournament']; 
            $_SESSION['tournament'] = $selectedTournament; // Сохраняем выбор в сессии
        }

        if(isset($_SESSION['tournament']) &&  $_SESSION['tournament'] != ''){
            $tournament = $_SESSION['tournament'];
        }

        // Если tournament несуществует
        if (!$tournament) {
            // получаем последний сезон
            $queryGetTurnirsOfLastSeason = $db->Execute("SELECT * FROM `v9ky_turnir` WHERE `seasons` = (SELECT id FROM
            `v9ky_seasons` ORDER BY id DESC LIMIT 1)");

            // Идентификатор турнира. Берем первый турнир из массива всех турниров в сезоне
            $turnir = $queryGetTurnirsOfLastSeason->fields['id'];
            // Название турнира латинице. Берем там же где и turnir
            $tournament = $queryGetTurnirsOfLastSeason->fields['name'];
        } else {
            // Получаем переменную turnir исходя от tournament. Все данные страницы основуються от переменной turnir
            $turnirsOfSeason = $db->Execute("select * from v9ky_turnir where name='".$tournament."'");
            $turnirsOfSeason->fields['id'] ? $turnir = $turnirsOfSeason->fields['id'] : $turnir = 0;
        }

    ?>

</head>

<body class="body">

    <header class="header">
        <div class="container">
            <nav class="nav">
                <div class="nav__block">
                    <div class="nav__item nav__title-block">
                        <a href="<?=$site_url?>"><strong>V9KY.INUA</strong></a>
                        &nbsp;|&nbsp;&nbsp;<?=$gorod_en->fields['name_en']?>&nbsp;&nbsp;|&nbsp;&nbsp;<?=$season_name->fields['season']?>
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
                    <a class="button-nav button-with-arrow button--order-team" href="<?=$site_url?>">
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