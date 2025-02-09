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
$dataCurrentTur = getDataCurrentTur($turnir, $currentTur);

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
    0 => IMAGES . "/t-shirt/azure-shirt.png",
    1 => IMAGES . "/t-shirt/yellow-shirt.png",
    2 => IMAGES . "/t-shirt/white-shirt.png",
    3 => IMAGES . "/t-shirt/azure-shirt.png",
    4 => IMAGES . "/t-shirt/blue-shirt.png",
    5 => IMAGES . "/t-shirt/red-shirt.png",
    6 => IMAGES . "/t-shirt/azure-shirt.png",
    7 => IMAGES . "/t-shirt/orange-shirt.png",
    8 => IMAGES . "/t-shirt/rose-shirt.png",
    9 => IMAGES . "/t-shirt/azure-shirt.png",
    10 => IMAGES . "/t-shirt/azure-shirt.png",
    11 => IMAGES . "/t-shirt/azure-shirt.png",
    12 => IMAGES . "/t-shirt/azure-shirt.png",
];



require_once CONTROLLERS . "/head.php";
require_once CONTROLLERS . "/leagues.php";

require_once VIEWS . "/match_calendar.tpl.php";

require_once CONTROLLERS . "/footer.php";