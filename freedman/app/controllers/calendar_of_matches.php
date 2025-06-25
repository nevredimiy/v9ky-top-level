<?php

// // Увімкнення відображення помилок
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

// // Встановлення рівня звітності помилок
// error_reporting(E_ALL);

if(!isset($tournament) || empty($tournament)){
    $tournament = getTournament();
}

if(!isset($turnir)){
    $turnir = getTurnir($tournament);
}

if(isset($_GET['first_day'])) {
    $selectedDate = $_GET['first_day'];
} else {
    $selectedDate = date("Y-m-d", strtotime(getLastActiveDate($turnir)));
}

$dateNow = new DateTime();
 
// Данные матча выбранного турнира
$sql = "SELECT 
    DATE(`date`) as `match_date`,
    tur,
    turnir,
    canseled
    FROM `v9ky_match` 
    WHERE turnir = :turnir 
    ORDER BY `date` ASC";
$matches = $dbF->query($sql, [":turnir" => $turnir])->findAll();
 
// Извлекаем даты в отдельный массив
$dates = array_column($matches, 'match_date');


if(!empty($dates)){


// Форматируем даты матчей для отображения в шаблоне
$dateMatches = formatMatchDates($dates);

if($selectedDate == '1970-01-01'){
    $selectedDate = $dateMatches[0]['first_day'];
  }

$daysOfTur = getFirstAndLastDays($dateMatches, $selectedDate);

$dataMatchesOfDate = 0;
if($daysOfTur['first_day']){
    $dataMatchesOfDate = getDataMatchesOfDate($turnir, $daysOfTur['first_day'], $daysOfTur['last_day']);
}

// Строка - дата последнего сыгранного матча в турнире (canseled = 1) (формат Y-m-d)
$dateLastTurString = $selectedDate;

// Преобразуем строку в объект даты
$dateLastTur = new DateTime($dateLastTurString);

// Добавляем 5 дней - это количество дней, когда админы должны внести все данные по последнему туру
$dateLastTur->modify('+ 6days');

$playerOfDateTur = getPlayersOfDateTur( $allStaticPlayers, $daysOfTur['first_day'], $daysOfTur['last_day'] );

// Лучшие игроки - отфильтрованные
$bestPlayersForTable = mergeStaticAndData($playerOfDateTur, $dataAllPlayers);
// dump($bestPlayersForTable);

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



$currentTur = getMostFrequentTur($dataMatchesOfDate);




//-----------------------------------//

  // Данные матчей выбранного тура и выбранного дня
$dataCurrentTur = getDataCurrentTur( $turnir, $currentTur);
$dataCurrentDay = getDataMatchesOfDay( $turnir, $selectedDate);

// Объединяет два массива данных матчей
function mergeUniqueById($array1, $array2) {
    $merged = [];

    // Добавляем элементы из первого массива
    foreach ($array1 as $item) {
        $merged[$item['id']] = $item;
    }

    // Добавляем элементы из второго массива (если id нет в массиве)
    foreach ($array2 as $item) {
        if (!isset($merged[$item['id']])) {
            $merged[$item['id']] = $item;
        }
    }

    // Преобразуем обратно в индексированный массив
    $result = array_values($merged);

    // Сортируем по дате (от меньшей к большей)
    usort($result, function ($a, $b) {
        return strtotime($a['date']) - strtotime($b['date']);
    });

    return $result;
}

// Итоговый массив данных матчей, которые выбраны по дате и по туру  
$matchesOfTurAndDate = mergeUniqueById($dataCurrentTur, $dataCurrentDay);
    
  // Добавляем два элемента в массивы - форматированная дата и время матча.
  $dataCurrentTurWithDate = getArrayWithFormattedDate($matchesOfTurAndDate);

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

}

//-----------------------------------//


include_once VIEWS . "/calendar_of_matches.tpl.php";