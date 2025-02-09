<?php

if(!isset($turnir)){
    $turnir = getTurnir();
}

// Последний тур в турнире (в лиге).
$lastTur = intval(getLastTur($turnir));

// Дата последнего тура
$lastTurDate = getLastTurDate($turnir);

$lastTurDateD = new DateTime($lastTurDate);

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

// Находим дату выбранного турнира
$dateLastTurString = '';
foreach($dateTurs as $dateT){
    if($dateT['tur'] == $currentTur) {
        $dateLastTurString = $dateT['min_date'];
    }
}

// Преобразуем строку в объект даты
$dateLastTur = new DateTime($dateLastTurString);

// Добавляем 5 дней - это количество дней, когда админы должны внести все данные по последнему туру
$dateLastTur->modify('+5 days');

// Текущая дата и время
$currentDate = new DateTime();

if($currentTur <= $lastTur && !empty($dataAllPlayers)) {
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



//-----------------------------------//

  // Данные тура
  $dataCurrentTur = getDataCurrentTur( $turnir, $currentTur);
    
  // Добавляем два элемента в массивы - форматированная дата и время матча.
  $dataCurrentTurWithDate = getArrayWithFormattedDate($dataCurrentTur);

  $dataMatch = [];
  
  foreach ($dataCurrentTurWithDate as $match) {
      if($match['id'] == $dataCurrentTurWithDate[0]['id']){
          $dataMatch['team1_name'] = $match['team1_name'];
          $dataMatch['team1_photo'] = $match['team1_photo'];
          $dataMatch['team2_name'] = $match['team2_name'];
          $dataMatch['team2_photo'] = $match['team2_photo'];
          $dataMatch['anons'] = $match['anons'];
          $dataMatch['goals1'] = $match['goals1'];
          $dataMatch['goals2'] = $match['goals2'];
          break;
      }
  }


  $historyMeets = getHistoryMeets($dataMatch['team1_name'], $dataMatch['team2_name']);

  
  // количество побед, ничих, количество голов
  $team1Wins = 0;
  $team2Wins = 0;
  $draws = 0;
  $countGoals1 = 0;
  $countGoals2 = 0;

 
 foreach($historyMeets as $match){
      if($match['goals1'] > $match['goals2']) {
        (strcasecmp(cleanString($dataMatch['team1_name']), cleanString($match['team1_name'])) === 0) ? $team1Wins ++ : $team2Wins ++; 
      } elseif ($match['goals1'] < $match['goals2']) {
        (strcasecmp(cleanString($dataMatch['team1_name']), cleanString($match['team1_name'])) === 0) ? $team2Wins ++ : $team1Wins ++; 
      } else {
          $draws ++;
      }
      (strcasecmp(cleanString($dataMatch['team1_name']), cleanString($match['team1_name'])) === 0) ? $countGoals1 += $match['goals1'] : $countGoals2 += $match['goals1'];
      (strcasecmp(cleanString($dataMatch['team2_name']), cleanString($match['team2_name'])) === 0) ? $countGoals2 += $match['goals2'] : $countGoals1 += $match['goals2'];
  }

  // Находим прогноз на будущие матчи
  // Рассчитываем общее количество матчей
  $totalMatches = count($historyMeets);

  // Вычисляем проценты
  $team1WinPercent = $totalMatches == 0 ? 0 : ($team1Wins / $totalMatches) * 100;
  $drawPercent = $totalMatches == 0 ? 0 : ($draws / $totalMatches) * 100;
  $team2WinPercent = $totalMatches == 0 ? 0 : ($team2Wins / $totalMatches) * 100;

  // Проверяем и корректируем проценты
  $minimumPercent = 10;

  // Список для перераспределения
  $percentages = [
      'team1Win' => $team1WinPercent,
      'draw' => $drawPercent,
      'team2Win' => $team2WinPercent,
  ];

  // Найти, какие значения меньше минимального
  $totalReduction = 0;
  foreach ($percentages as $key => &$percent) {
      if ($percent < $minimumPercent) {
          $totalReduction += $minimumPercent - $percent;
          $percent = $minimumPercent;
      }
  }
  unset($percent);

  // Перераспределить излишек между остальными
  $remainingKeys = array_keys(array_filter(
      $percentages, 
      function ($p) use ($minimumPercent) { 
          
          return $p > $minimumPercent;
      } 
  ));
  if (count($remainingKeys) > 0) {
      foreach ($remainingKeys as $key) {
          $percentages[$key] -= $totalReduction / count($remainingKeys);
      }
  }

//-----------------------------------//


include_once VIEWS . "/calendar_of_matches.tpl.php";