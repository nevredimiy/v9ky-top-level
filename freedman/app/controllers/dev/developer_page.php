<?

echo "DEBUG: " . __FILE__ . " loaded<br>";
dump('123');
dump('123');
dump('123');
dump('123');
dump('123');
dump('123');
dump('123');
dump('123');
dump('123');
dump('123');
dump('123');
dump('123');

// Увімкнення відображення помилок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Встановлення рівня звітності помилок
error_reporting(E_ALL);


include_once CONTROLLERS . "/head.php";


include_once CONTROLLERS . "/leagues.php";
include_once CONTROLLERS . "/rating_players.php";

// Проверяем, задана ли переменная $turnir
if (!isset($turnir)) {
    die('Ошибка: переменная $turnir не задана.');
}

if(!isset($turnir) && !isset($tournament)) {
    $turnir = getTurnir();
}

if(!isset($turnir)) {
    $turnir = getTurnir($tournament);
}


// 1. Получаем команды и определяем группы
$sql = "SELECT 
        tm.`id`, 
        tm.`name`, 
        tm.`grupa`,
        tm.pict AS logo,
        t.ru AS turnir_name,
        t.cup,
        t.color_place
      FROM `v9ky_team` tm
      LEFT JOIN 
        v9ky_turnir t ON t.id = tm.turnir
      WHERE tm.`turnir` = :turnirId 
      ORDER BY tm.`grupa`, tm.`name`";
$teams = $dbF->query($sql,['turnirId' => $turnir])->findAll();

// Определяем количество кругов
$cupMode = isset($teams[0]['cup']) ? (int)$teams[0]['cup'] : 2; // По умолчанию два круга

$groupedTeams = [];
$teamIndex = [];
$index = 1;
foreach ($teams as $team) {
    $group = $team['grupa'] ? $team['grupa'] : ''; // Если группа пустая
    $groupedTeams[$group][] = $team;
    $teamIndex[$team['id']] = $index++;
    $teamColorPlace = $team['color_place'] ? explode("-", $team['color_place']) : '';
}

$colorStyles = [
    'silver' => 'background: linear-gradient(180deg, #B9B9B9 0%, #E2E2E2 100%)',
    'gold' => 'background: #FDBE11',
    'bronze' => 'background: #CD7F32', 
    'red' => 'background: #FF3B3B', 
    'empty' => 'background: transporant', 
];

// 2. Получаем результаты матчей
$sql = "SELECT team1, team2, gols1, gols2 FROM v9ky_match 
        WHERE turnir = :turnirId AND canseled = 1 AND cup_stage = 0";
$matches = $dbF->query($sql, ['turnirId' => $turnir])->findAll();


// получаем катрочки 
$sql = "SELECT team, COUNT(*) AS red_cards FROM v9ky_red WHERE matc IN (SELECT id FROM `v9ky_match` WHERE turnir = :turnirId) GROUP BY team";
$redCards = $dbF->query($sql, ['turnirId' => $turnir])->findAll();
$sql = "SELECT team, COUNT(*) AS yellow_cards FROM v9ky_yellow WHERE matc IN (SELECT id FROM `v9ky_match` WHERE turnir = :turnirId) GROUP BY team";
$yellowCards = $dbF->query($sql, ['turnirId' => $turnir])->findAll();

foreach($redCards as $red){
    $redCards[$red['team']] = $red['red_cards'];
}

foreach($yellowCards as $yellow){
    $yellowCards[$yellow['team']] = $yellow['yellow_cards'];
}

// 3. Формируем таблицу результатов
$stats = [];
$matchResults = [];
foreach ($teams as $team) {
    $stats[$team['id']] = [
        'name' => $team['name'], 'games' => 0, 'wins' => 0, 'draws' => 0, 'losses' => 0, 'points' => 0, 'goals_for' => 0, 'goals_against' => 0,
        'red_cards' => isset($redCards[$team['id']]) ? $redCards[$team['id']] : 0, 'yellow_cards' => isset($yellowCards[$team['id']]) ? $yellowCards[$team['id']] : 0
    ];
    $matchResults[$team['id']] = array_fill(1, count($teams), '');
}

foreach ($matches as $match) {
    if ($match['gols1'] !== null && $match['gols2'] !== null) {
        $stats[$match['team1']]['games']++;
        $stats[$match['team2']]['games']++;
        $stats[$match['team1']]['goals_for'] += $match['gols1'];
        $stats[$match['team1']]['goals_against'] += $match['gols2'];
        $stats[$match['team2']]['goals_for'] += $match['gols2'];
        $stats[$match['team2']]['goals_against'] += $match['gols1'];

        if ($match['gols1'] > $match['gols2']) {
            $stats[$match['team1']]['wins']++;
            $stats[$match['team1']]['points'] += 3;
            $stats[$match['team2']]['losses']++;
        } elseif ($match['gols1'] < $match['gols2']) {
            $stats[$match['team2']]['wins']++;
            $stats[$match['team2']]['points'] += 3;
            $stats[$match['team1']]['losses']++;
        } else {
            $stats[$match['team1']]['draws']++;
            $stats[$match['team2']]['draws']++;
            $stats[$match['team1']]['points'] += 1;
            $stats[$match['team2']]['points'] += 1;
        }
        
        // Заполняем таблицу матчей
        if ($cupMode == 2) {
          $matchResults[$match['team1']][$teamIndex[$match['team2']]] .= "{$match['gols1']}:{$match['gols2']} ";
          // $matchResults[$match['team2']][$teamIndex[$match['team1']]] .= "Г:{$match['gols2']}:{$match['gols1']} ";
        } else {
            $matchResults[$match['team1']][$teamIndex[$match['team2']]] = "{$match['gols1']}:{$match['gols2']}";
            $matchResults[$match['team2']][$teamIndex[$match['team1']]] = "{$match['gols2']}:{$match['gols1']}";
        }
    }
}

// 5. Сортируем команды
foreach ($groupedTeams as $group => &$teams) {
    usort($teams, function ($a, $b) use ($stats, $matches) {
        $aStats = $stats[$a['id']];
        $bStats = $stats[$b['id']];

        // 1. Сортировка по очкам в турнире
        if ($bStats['points'] !== $aStats['points']) {
            return $bStats['points'] - $aStats['points'];
        }

        // 2. Группировка команд с одинаковыми очками
        $samePointsTeams = [];
        foreach ($stats as $teamId => $teamStats) {
            if ($teamStats['points'] === $aStats['points']) {
                $samePointsTeams[$teamId] = [
                    'id' => $teamId,
                    'points' => 0, // Виртуальные очки за очные встречи
                    'goal_difference' => $teamStats['goals_for'] - $teamStats['goals_against'],
                    'goals_for' => $teamStats['goals_for'],
                    'red_cards' => $teamStats['red_cards'],
                    'yellow_cards' => $teamStats['yellow_cards']
                ];
            }
        }

        // 3. Подсчет очков за очные встречи
        foreach ($matches as $match) {
            $t1 = $match['team1'];
            $t2 = $match['team2'];

            if (isset($samePointsTeams[$t1]) && isset($samePointsTeams[$t2])) {
                if ($match['gols1'] > $match['gols2']) {
                    $samePointsTeams[$t1]['points'] += 3;
                } elseif ($match['gols1'] < $match['gols2']) {
                    $samePointsTeams[$t2]['points'] += 3;
                } else {
                    $samePointsTeams[$t1]['points'] += 1;
                    $samePointsTeams[$t2]['points'] += 1;
                }
            }
        }

        // 4. Сортировка по очным встречам
        if ($samePointsTeams[$b['id']]['points'] !== $samePointsTeams[$a['id']]['points']) {
            return $samePointsTeams[$b['id']]['points'] - $samePointsTeams[$a['id']]['points'];
        }

        // 5. Разница забитых и пропущенных мячей в турнире
        if ($samePointsTeams[$b['id']]['goal_difference'] !== $samePointsTeams[$a['id']]['goal_difference']) {
            return $samePointsTeams[$b['id']]['goal_difference'] - $samePointsTeams[$a['id']]['goal_difference'];
        }

        // 6. Количество забитых мячей (чем больше, тем выше)
        if ($samePointsTeams[$b['id']]['goals_for'] !== $samePointsTeams[$a['id']]['goals_for']) {
            return $samePointsTeams[$b['id']]['goals_for'] - $samePointsTeams[$a['id']]['goals_for'];
        }

        // 7. Красные карточки (чем меньше, тем выше)
        if ($samePointsTeams[$a['id']]['red_cards'] !== $samePointsTeams[$b['id']]['red_cards']) {
            return $samePointsTeams[$a['id']]['red_cards'] - $samePointsTeams[$b['id']]['red_cards'];
        }

        // 8. Желтые карточки (чем меньше, тем выше)
        return $samePointsTeams[$a['id']]['yellow_cards'] - $samePointsTeams[$b['id']]['yellow_cards'];
    });
}

dump($groupedTeams);
dump('123');
  
  // Данные кубка
  $cupData = getCupData($turnir);
?>



<section data-cup-mode="<?= $cupMode ?>" class="table-league">

<!-- Если кубок, то выводим реультат кубка -->
    <?php if(isset($cupData) && $cupData):?>
        <div class="cup__block">
            <div class="cup__block-wrap">
                <?php foreach($cupData as $key => $cup): ?>
                    <h2><?= $key ?></h2>
                    <?php foreach($cup as $match): ?>
                        <div class="">
                            
                            <div class="">
                                <?php if(isset($match['cup1'])): ?>
                                    <img width="20" height="30" src="<?= IMAGES . '/' . $match['cup1'] ?>" alt="">
                                <?php endif ?>
                                <?= $match['team1_name']?>
                            </div>
                            <div class=""><?= $match['goals1']?>:<?= $match['goals2']?></div>
                            <div class="">
                                <?= $match['team2_name']?>
                                <?php if(isset($match['cup2'])): ?>
                                    <img width="20" height="30" src="<?= IMAGES . '/' . $match['cup2'] ?>" alt="">
                                <?php endif ?>    
                            </div>   
                                                            
                        </div>
                    <?php endforeach ?>
                <?php endforeach ?>
            </div>
        </div>
    <?php endif ?>

    <?php foreach ($groupedTeams as $group => $teams): ?>
        
        <h2 class="table-league__title title title--inverse">
            <span>Турнірна таблиця</span>
            <span><?= $teams[0]['turnir_name'] ?></span>
            <?= $group ? "<span>Група $group</span>" : '' ?>
        </h2>

        <div class="swiper swiper-table">
            <div class="swiper-wrapper">
                <div class="swiper-slide swiper-slide--table swiper-slide-active">
                    <table class="table-league__table">
                        <tbody>
                            <tr>
                                <th><span>М</span></th>
                                <th><span class="cell--team-logo"></span></th>
                                <th><span class="cell--team">Команда</span></th>
                                <?php for ($i = 1; $i <= count($teams); $i++): ?>
                                    <th><span class="cell--score"><?= $i ?></span></th>
                                <?php endfor; ?>
                                <th><span class="cell cell--games">І</span></th>
                                <th><span class="cell cell--win">В</span></th>
                                <th><span class="cell cell--draw">Н</span></th>
                                <th><span class="cell cell--defeat">П</span></th>
                                <th class="td-scored"><span class="cell cell--scored">Г</span></th>
                                <th class="td-card"><span class="cell cell--card">ЧК</span></th>
                                <th class="td-card"><span class="cell cell--card">ЖК</span></th>
                                <th><span class="cell cell--total">О</span></th>
                            </tr>

                            <?php  foreach ($teams as $i => $team) : ?>
                                <?php $id = $team['id']; ?>
                                <?php $team_id = $team['id']; ?>
                                <tr>
                                    <td><span <?= isset($teamColorPlace[$i]) && !empty($teamColorPlace[$i]) ? "style='{$colorStyles[$teamColorPlace[$i]]}'" : '' ?> class="cell" data-color="<?= $teamColorPlace[$i] ?>"><?= $i + 1 ?></span></td>
                                    <td><img width="18" height="18" class="cell--team-logo" src="<?= $team_logo_path ?>/<?= $team['logo'] ?>"></td>
                                    <td><a href="<?= $site_url . '/' . $tournament .'/team_page/id/' . $team_id ?>"><span class="cell--team"><?= $stats[$id]['name']?></span></a></td>

                                    <?php foreach ($teams as $t): ?>                                                            
                                      <td>
                                        <span class="cell--score<?= $t['id'] == $team_id ? ' cell--own' : '' ?>">
                                          <?= empty( $matchResults[$id][$teamIndex[$t['id']]] ) ? '-' : $matchResults[$id][$teamIndex[$t['id']]] ?>
                                        </span>
                                      </td>                                        
                                    <?php endforeach ?>

                                    <td><span class="cell cell--games"><?= $stats[$id]['games']?></span></td>
                                    <td><span class="cell cell--win"><?= $stats[$id]['wins']?></span></td>
                                    <td><span class="cell cell--draw"><?= $stats[$id]['draws']?></span></td>
                                    <td><span class="cell cell--defeat"><?= $stats[$id]['losses']?></span></td>
                                    <td class="td-scored"><span class="cell cell--scored"><?= $stats[$id]['goals_for']?> - <?= $stats[$id]['goals_against'] ?> </span></td>
                                    <td class="td-card"><span class="cell cell--card"><?= $stats[$id]['red_cards']?></span></td>
                                    <td class="td-card"><span class="cell cell--card"><?= $stats[$id]['yellow_cards']?></span></td>
                                    <td><span class="cell cell--total"><?= $stats[$id]['points']?></span></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="swiper-scrollbar-table"></div>
        </div>
    <?php endforeach; ?>
</section>





<?php 


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
dump($playerOfDateTur);

// Лучшие игроки - отфильтрованные
$bestPlayersForTable = mergeStaticAndData($playerOfDateTur, $dataAllPlayers);


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





// include_once CONTROLLERS . "/calendar_of_matches.php";
include_once CONTROLLERS . "/controls.php";
include_once CONTROLLERS . "/disqualification.php";
include_once CONTROLLERS . "/footer.php";


