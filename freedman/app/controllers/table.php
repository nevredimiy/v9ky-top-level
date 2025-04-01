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

  
  // Данные кубка
  $cupData = getCupData($turnir);
  
  require_once VIEWS . '/table.tpl.php';