<?php

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
        t.cup
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
    $group = $team['grupa'] ?: ''; // Если группа пустая
    $groupedTeams[$group][] = $team;
    $teamIndex[$team['id']] = $index++;
}

// 2. Получаем результаты матчей
$sql = "SELECT team1, team2, gols1, gols2 FROM v9ky_match 
        WHERE turnir = :turnirId AND canseled = 1 AND cup_stage = 0";
$matches = $dbF->query($sql, ['turnirId' => $turnir])->findAll();


// 3. Формируем таблицу результатов
$stats = [];
$matchResults = [];
foreach ($teams as $team) {
    $stats[$team['id']] = [
        'name' => $team['name'], 'games' => 0, 'wins' => 0, 'draws' => 0, 'losses' => 0, 'points' => 0, 'goals_for' => 0, 'goals_against' => 0
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

// Сортируем команды по очкам, разнице мячей, победам, забитым мячам
foreach ($groupedTeams as $group => &$teams) {
    usort($teams, function ($a, $b) use ($stats) {
        $aStats = $stats[$a['id']];
        $bStats = $stats[$b['id']];

        if ($bStats['points'] !== $aStats['points']) {
            return $bStats['points'] - $aStats['points'];
        }
        
        if ($bStats['games'] !== $aStats['games']) {
            return $bStats['games'] - $aStats['games'];
        }

        if (($bStats['goals_for'] - $bStats['goals_against']) !== ($aStats['goals_for'] - $aStats['goals_against'])) {
            return ($bStats['goals_for'] - $bStats['goals_against']) - ($aStats['goals_for'] - $aStats['goals_against']);
        }
        if ($bStats['wins'] !== $aStats['wins']) {
            return $bStats['wins'] - $aStats['wins'];
        }
        return $bStats['goals_for'] - $aStats['goals_for'];
    });
}

  
  // Данные кубка
  $cupData = getCupData($turnir);
  
  require_once VIEWS . '/table.tpl.php';