<?php

// TODO: freedman  Получаем данные для карточек игроков - "Место в лиге" и топ-таблиц
        

//Получаем всю статистику игроков
$allStaticPlayers = getAllStaticPlayers($turnir);

// Получаем игрока матча
$queryBestPlayerOfMatch = $dbF->query("SELECT `best_player`, id, tur FROM `v9ky_match` WHERE `turnir`= :turnir and `best_player` > 0 ORDER by tur", [":turnir" => $turnir])->findAll();

// Последний тур в турнире (в лиге).
$lastTur = getLastTur($turnir);

// Массив для лучшего игрока матча (иконка ведочка)
$nominationPlayerOfMatch = [];

// Заполняем массив лучшего игрока матча
foreach($queryBestPlayerOfMatch as $value){

  $player = $value['best_player'];
  $match = $value['id'];    
    
  // Заполняем массив. Проверки не нужно. Так как в одном матче лучший игрок может быть только один.
  $nominationPlayerOfMatch[$player][$match]['count_best_player_of_match'] = 1;
    
}


// Получаем общий массив. Добавляем в основной массив статистику лучший игрок матча.
$allStaticPlayers = megreTwoMainArrays($allStaticPlayers, $nominationPlayerOfMatch, 'count_best_player_of_match');



// Получаем данные всех игроков - ФИО, фото и т.д.
if(!isset($dataAllPlayers)) {  
  $dataAllPlayers = getDataPlayers($allStaticPlayers); 
}

if(!empty($dataAllPlayers)){
  // Отсортированный массив по рубрике Топ-Гравець
  $topGravetc = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'topgravetc', $lastTur);

  // Отсортированный массив по рубрике Топ-Голкипер
  $topGolkiper = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'golkiper', $lastTur);

  // Отсортированный массив по рубрике Топ-Бомбардир
  $topBombardi = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'count_goals', $lastTur);

  // Отсортированный массив по рубрике Топ-Асистент
  $topAsists = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'count_asists', $lastTur);

  // Отсортированный массив по рубрике Топ-Захистник
  $topZhusnuk = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'zahusnuk', $lastTur);

  // Отсортированный массив по рубрике Топ-Дриблинг
  $topDribling = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'dribling', $lastTur);

  // Отсортированный массив по рубрике Топ-Удар
  $topUdar = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'udar', $lastTur);

  // Отсортированный массив по рубрике Топ-Пас
  $topPas = getTopPlayers($allStaticPlayers, $dataAllPlayers, 'pas', $lastTur);

  // Берем первый элемент массива - топ-игрок
  $topTopGravetc = reset($topGravetc);

  // Берем первый элемент массива - топ-игрок
  $topTopGolkiper = reset($topGolkiper);

  // Берем первый элемент массива - топ-игрок
  $topTopBombardi = reset($topBombardi);

  // Берем первый элемент массива - топ-игрок
  $topTopAsists = reset($topAsists);

  // Берем первый элемент массива - топ-игрок
  $topTopZhusnuk = reset($topZhusnuk);

  // Берем первый элемент массива - топ-игрок
  $topTopDribling = reset($topDribling);

  // Берем первый элемент массива - топ-игрок
  $topTopUdar = reset($topUdar);

  // Берем первый элемент массива - топ-игрок
  $topTopPas = reset($topPas);

  $topPlayers = [
    'top_gravetc' => $topTopGravetc,
    'top_golkiper' => $topTopGolkiper,
    'top_bombardir' => $topTopBombardi,
    'top_asist' => $topTopAsists,
    'top_zahusnuk' => $topTopZhusnuk,
    'top_dribling' => $topTopDribling,
    'top_udar' => $topTopUdar,
    'top_pas' => $topTopPas
  ];

  $topPlayersData = [
    'top_gravetc' => ['icon' => 'star-icon.png','label' => 'Топ-Гравець' ],
    'top_golkiper' => ['icon' => 'gloves-icon.png', 'label' => 'Топ-Голкіпер' ],
    'top_bombardir' => ['icon' => 'football-icon.png', 'label' => 'Топ-Бомбардир' ],
    'top_asist' => ['icon' => 'boots-icon.svg', 'label' => 'Топ-Асистент' ],
    'top_zahusnuk' => ['icon' => 'pitt-icon.svg', 'label' => 'Топ-Захисник' ],
    'top_dribling' => ['icon' => 'player-icon.svg', 'label' => 'Топ-Дриблінг' ],
    'top_udar' => ['icon' => 'rocket-ball-icon.png', 'label' => 'Топ-Удар' ],
    'top_pas' => ['icon' => 'ball-icon.png', 'label' => 'Топ-Пас' ]
  ];

  $isEmtyTopPlayer = true;

  require_once VIEWS . '/rating_players.tpl.php';
}



