<?php 
    // Когда tournament пустая это ознчает, что в адресной строке нет названия тура. Обычно это надпись после слеша в адресной строке
    // Если переменная tournament пустая, то заполняем ее из последнего сезона первым туром


    // Стартуем сессию если оне ещё не запущена.
    if (session_status() == PHP_SESSION_NONE) { 
        session_start(); 
    }

    // Запоминаем в сессию последний выбранный турнир, что бы после повторного открытия страницы именно этот турнир. 
    // Получаем текущий URL
    $currentUrl = $_SERVER['REQUEST_URI'];

    // Извлекаем слаг из URL
    $url_path = parse_url($currentUrl, PHP_URL_PATH);
    // Разбиваем URL по символу "/"
    $uri_parts = explode('/', trim($url_path, ' /'));
    // Получаем имя турнира
    $slug = array_shift($uri_parts); 

    $_SESSION['last_selected_tournament'] = '';

    // Проверяем, передан ли слаг турнира
    if (!empty($slug)) {
        // Сохраняем в сессию
        $_SESSION['last_selected_tournament'] = $slug;
    } else {
        // Если слаг не передан, загружаем из сессии
        if (isset($_SESSION['last_selected_tournament'])) {            
            $slug = $_SESSION['last_selected_tournament'];
        }
    }
    // Записывавем текущую лигу из сесесии
    $tournament = htmlspecialchars($slug);
    // Если tournament несуществует
    if (!$tournament) {
        // получаем последний сезон
        $queryGetTurnirsOfLastSeason = $dbF->query("SELECT * FROM `v9ky_turnir` WHERE `seasons` = (SELECT id FROM
        `v9ky_seasons` ORDER BY id DESC LIMIT 1) AND `city` = 2 ORDER BY id")->find();
        // Идентификатор турнира. Берем первый турнир из массива всех турниров в сезоне
        $turnir = $queryGetTurnirsOfLastSeason['id'];
        // Название турнира латинице. Берем там же где и turnir
        $tournament = $queryGetTurnirsOfLastSeason['name'];
    } else {
        // Получаем переменную turnir исходя от tournament. Все данные страницы основуються от переменной turnir
        $turnirsOfSeason = $dbF->query("SELECT * FROM `v9ky_turnir` WHERE `name` = :tournament", [":tournament" => $tournament])->find();
        $turnirsOfSeason['id'] ? $turnir = $turnirsOfSeason['id'] : $turnir = 0;
    }
    
   

    // $season_name = $db->Execute("select season from v9ky_turnir where name='".$tournament."'");
    $season_name = $dbF->query("SELECT `season` FROM `v9ky_turnir` WHERE `name` = :tournament", [":tournament" => $tournament])->find();
    // $gorod_en = $db->Execute("select * from v9ky_city where id=(select city from v9ky_turnir where name='".$tournament."')");
    $gorod_en = $dbF->query("SELECT * FROM `v9ky_city` WHERE `id` = (SELECT `city` FROM `v9ky_turnir` WHERE `name` = :tournament)", [":tournament" => $tournament])->find();

require_once VIEWS . "/head.tpl.php";

require_once CONTROLLERS . "/slider_spons.php";
require_once CONTROLLERS . "/menu.php";
