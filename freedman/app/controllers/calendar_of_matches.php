<?php

// // Увімкнення відображення помилок
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

// // Встановлення рівня звітності помилок
// error_reporting(E_ALL);

if(!isset($turnir)){
    $turnir = getTurnir();
}

// Последний тур в турнире (в лиге).
$lastTur = intval(getLastTur($turnir));


// Получаем все даты 
function getDateMatchesOfLeague($turnir){

    global $dbF;

    $sql = "SELECT `id`,`date`,`field`,`team1`,`team2`,`turnir`,`canseled`,`tur` FROM `v9ky_match` WHERE `turnir`= :turnir ORDER by `date`";

    $fields = $dbF->query($sql, [":turnir" => $turnir])->findAll();

    return($fields);
}

function getLastActiveDate($turnir){
    global $dbF;

    $sql = "SELECT `date` FROM `v9ky_match` WHERE `turnir`= :turnir AND `canseled` = 1 ORDER by `date` DESC LIMIT 1";

    $fields = $dbF->query($sql, [":turnir" => $turnir])->find();

    return($fields['date']);

}

// Данные всех матчей выбранной лиги
$matches = getDateMatchesOfLeague($turnir);

function getUniqueDates($matches) {
    // Список названий месяцев на украинском языке
    $months_ua = [
        "01" => "Січень", "02" => "Лютий", "03" => "Березень", "04" => "Квітень",
        "05" => "Травень", "06" => "Червень", "07" => "Липень", "08" => "Серпень",
        "09" => "Вересень", "10" => "Жовтень", "11" => "Листопад", "12" => "Грудень"
    ];

    $unique_dates = [];

    foreach ($matches as $match) {
        $date = date("Y-m-d", strtotime($match['date'])); // Получаем день и месяц в формате "dd.mm"
        $day = date("d", strtotime($match['date'])); // День
        $month = date("m", strtotime($match['date'])); // Месяц
        $monthNum = date("m", strtotime($match['date'])); // Месяц
        $year = date("Y", strtotime($match['date'])); // год
        $tur = $match['tur'];
        $canseled = $match['canseled'];

        // Проверяем, есть ли уже такая дата в массиве
        if (!isset($unique_dates[$date])) {
            $unique_dates[$date] = [
                "date" => $date,
                "year" => $year,
                "month" => $months_ua[$month], // Название месяца на украинском
                "month_num" => $monthNum, // Название месяца на украинском
                "day" => $day,
                "tur" => $tur,
                "canseled" => $canseled,
            ];
        }

    }

    return array_values($unique_dates); // Возвращаем массив без ключей
}

// данные матчей в выбранный день
$dateMatches = getUniqueDates($matches);



if (!isset($allStaticPlayers)) {    
    $allStaticPlayers = getAllStaticPlayers($turnir);
}

// Получаем данные всех игроков - ФИО, фото и т.д.
if(!isset($dataAllPlayers)) {  
  $dataAllPlayers = getDataPlayers($allStaticPlayers); 
}

// // --------------- под снос -----------------//
// // Получаем массив с датами каждого тура
// $dateTurs = getDateTurs($turnir);


// // Добавляем элемент link в массив
// $dateTurs = addLinkItem($dateTurs);
// // --------------- Конец под снос -----------------//


if(isset($_GET['date'])) {
    $selectedDate = $_GET['date'];
} else {
    $selectedDate = date("Y-m-d", strtotime(getLastActiveDate($turnir)));
}


// Выбранный тур
if(isset($_GET['tur'])){
    // Берем тур из адресной строки
    $currentTur = $_GET['tur'];
} else {
    foreach($dateMatches as $dateMatch){
        if($dateMatch['date'] == $selectedDate){
            $currentTur = $dateMatch['tur'];
        }
    }
}


//-------- Здесь обр вни-------//
// Находим дату выбранного турнира. Это нужно для сравнения с текущей датой, что бы правильно отображать данные.
// $dateLastTurString = '';
// foreach($dateTurs as $dateT){
//     if($dateT['tur'] == $currentTur) {
//         $dateLastTurString = $dateT['min_date'];
//     }
// }

$dateLastTurString = $selectedDate;
//-------- Конец Здесь обр вни-------//

// Преобразуем строку в объект даты
$dateLastTur = new DateTime($dateLastTurString);


// Добавляем 5 дней - это количество дней, когда админы должны внести все данные по последнему туру
$dateLastTur->modify('+5 days');

//-------- Здесь удалить 1 стр-------//
// Текущая дата и время. Эту дату сравниваем с $dateLastTur для правильного отображения данных
$currentDate = new DateTime();
$dateNow = new DateTime();
//-------- Здесь удалить 1 стр-------//

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

//-----------------------------------//


include_once VIEWS . "/calendar_of_matches.tpl.php";