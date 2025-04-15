<?php

// // Увімкнення відображення помилок
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

// // Встановлення рівня звітності помилок
// error_reporting(E_ALL);

// $calendarMatches = getCalendar();


if(!isset($turnir) && !isset($tournament)){
    $turnir = getTurnir();
}
if(!isset($turnir)){
    $turnir = getTurnir($tournament);
}


$dateTurs = getDateTurs($turnir);
$lastTur = getLastTur($turnir);

// Выбранный тур
$currentTur = isset($lastTur) && $lastTur != '' ? $lastTur : 1;
if(isset($_GET['tur'])){
    // Берем тур из адресной строки
    $currentTur = $_GET['tur'];
}

// Добавляем элемент link в массив $dateTurs
$dateTurs = addLinkItem($dateTurs);

// Данные тура
// $dataCurrentTur = getDataCurrentTur($turnir, $currentTur);


function getDataMatchesUnplayed(){
    global $dbF;

    $sql = 
        "SELECT 
        m.id,
        m.anons,
        t.season,
        m.date,
        m.tur, 
        m.team1,
        m.tcolor1 as color_tshirt1,
        t1.id AS team1_id,
        t1.name AS team1_name,
        t1.pict AS team1_photo,
        m.team2,    
        m.tcolor2 as color_tshirt2,
        t2.id AS team2_id,
        t2.name AS team2_name,
        t2.pict AS team2_photo,
        m.field,
        f.name AS field_name,
        m.canseled,
        m.gols1 AS goals1,
        m.gols2 AS goals2,
        t.ru AS turnir_name,
        m.videohiden AS video_hd,
        m.video AS video,
        m.videobest AS videobest,
        m.video_intervu AS video_intervu,
        m.video_intervu2 AS video_intervu2
    FROM 
        v9ky_match m
    LEFT JOIN 
        `v9ky_team` t1 ON t1.id = m.team1
    LEFT JOIN
        `v9ky_team` t2 ON t2.id = m.team2
    LEFT JOIN
        `v9ky_turnir` t ON t.id = m.turnir
    LEFT JOIN
        `v9ky_fields` f ON f.id = m.field
    WHERE m.canseled = 0
    ORDER BY 
        m.date";

    // Делаем запрос в БД на игроков которые "вибули"
    $fields = $dbF->query($sql)->findAll();
    
    return $fields;
}

$dataCurrentTur = getDataMatchesUnplayed();


// Создаем массив для группировки
$groupedData = [];

// Проходим по исходному массиву
foreach ($dataCurrentTur as $item) {
    // Получаем дату без времени
    $dateWithoutTime = (new DateTime($item['date']))->format('Y-m-d');

    // Группируем по дате
    if (!isset($groupedData[$dateWithoutTime])) {
        $groupedData[$dateWithoutTime] = [];
    }

    $groupedData[$dateWithoutTime][] = $item;
}


$tshirtImages = [
    0 => IMAGES . "/t-shirt/gray-manish.png",
    1 => IMAGES . "/t-shirt/yellow-shirt.png",
    2 => IMAGES . "/t-shirt/white-shirt.png",
    3 => IMAGES . "/t-shirt/black-shirt.png",
    4 => IMAGES . "/t-shirt/light-blue-shirt.png",
    5 => IMAGES . "/t-shirt/red-shirt.png",
    6 => IMAGES . "/t-shirt/green-shirt.png",
    7 => IMAGES . "/t-shirt/orange-shirt.png",
    8 => IMAGES . "/t-shirt/purpur-shirt.png",
    9 => IMAGES . "/t-shirt/gray-shirt.png",
    10 => IMAGES . "/t-shirt/blue-shirt.png",
    11 => IMAGES . "/t-shirt/brown-shirt.png",
    12 => IMAGES . "/t-shirt/light-green-shirt.png",
];



require_once CONTROLLERS . "/head.php";
require_once CONTROLLERS . "/leagues.php";

require_once VIEWS . "/match_calendar.tpl.php";

require_once CONTROLLERS . "/footer.php";