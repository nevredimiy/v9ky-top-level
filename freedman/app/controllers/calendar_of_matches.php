<?php

if(!isset($turnir)){
    $turnir = getTurnir();
}

// Последний тур в турнире (в лиге).
$lastTur = intval(getLastTur($turnir));

if (!isset($allStaticPlayers)) {
    
    $allStaticPlayers = getAllStaticPlayers($turnir);

}

// Получаем данные всех игроков - ФИО, фото и т.д.
if(!isset($dataAllPlayers)) {  
  $dataAllPlayers = getDataPlayers($allStaticPlayers); 
}
  
// Получаем массив с датами каждого тура
$dateTurs = getDateTurs($turnir);
// Добавляем элемент link в массив
$dateTurs = addLinkItem($dateTurs);

// Выбранный тур
$currentTur = $lastTur != '' ? $lastTur : 1;
if(isset($_GET['tur'])){
    // Берем тур из адресной строки
    $currentTur = $_GET['tur'];
}

if($currentTur <= $lastTur) {
    // Все игроки из выбранного тура
    $bestPlayers = getPlayersOfTur($allStaticPlayers, $currentTur);
    

    // Лучшие игроки - отфильтрованные
    $bestPlayersForTable = mergeStaticAndData($bestPlayers, $dataAllPlayers);


    $labels = [
        'topgravetc' => ['icon' => 'star-icon.png', 'role' => 'Топ-Гравець'], 
        'golkiper' => ['icon' => 'gloves-icon.png', 'role' => 'Топ-Голкіпер'], 
        'bombardir' => ['icon' => 'football-icon.png', 'role' => 'Топ-Бомбардир'], 
        'asistent' => ['icon' => 'boots-icon.svg', 'role' => 'Топ-Асистент'],
        'zahusnuk' => ['icon' => 'pitt-icon.svg', 'role' => 'Топ-Захисник'],
        'dribling' => ['icon' => 'player-icon.svg', 'role' => 'Топ-Дриблінг'],
        'udar' => ['icon' => 'rocket-ball-icon.png', 'role' => 'Топ-Удар'],
        'pas' => ['icon' => 'ball-icon.png', 'role' => 'Топ-Пас'],
    ];
}

$dataCurrentTur = getDataCurrentTur($turnir, $currentTur);

// Добавляем два элемента в массивы - форматированная дата и время матча.
$dataCurrentTurWithDate = getArrayWithFormattedDate($dataCurrentTur);

include_once VIEWS . "/calendar_of_matches.tpl.php";