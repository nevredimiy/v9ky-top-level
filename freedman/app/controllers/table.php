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



function getDataTable($turnir, $canseled){
    global $dbF;
  
    // SQL-запрос для получения данных
    $sql = "SELECT 
    t1.id AS team1_id,
    t1.name AS team1_name,
    t1.pict AS team1_logo,
    t2.id AS team2_id,
    t2.name AS team2_name,
    t2.pict AS team2_logo,
    m.gols1 AS team1_goals,
    m.gols2 AS team2_goals,
    t1.grupa AS team1_group,
    t2.grupa AS team2_group,
    t.ru AS turnir_name,
    t.cup
  
    FROM 
    v9ky_match m
    JOIN 
    v9ky_team t1 ON m.team1 = t1.id
    JOIN 
    v9ky_team t2 ON m.team2 = t2.id
    LEFT JOIN 
    v9ky_turnir t ON t.id = m.turnir
    WHERE 
    m.turnir = :turnir AND canseled = :canseled AND m.cup_stage = 0 
    ORDER BY 
    t1.grupa ASC, t1.name ASC, t2.name ASC";
  
  
    $result = $dbF->query($sql, [":turnir" => $turnir, ":canseled" => $canseled])->findAll();
    
    return $result;
  
  }
  
  $result = getDataTable($turnir, 1);
  
  if(!$result) {
    $sql = "SELECT 
            tm.id AS team1_id,
            tm.name AS team1_name,
            tm.pict AS team1_logo,
            tm.id AS team2_id,
            tm.name AS team2_name,
            tm.pict AS team2_logo,
            tm.grupa AS team1_group,
            tm.grupa AS team2_group,
            t.ru AS turnir_name,
            t.cup
            FROM `v9ky_team` tm
            LEFT JOIN 
              v9ky_turnir t ON t.id = tm.turnir
            WHERE `turnir` = :turnir";
    $result = $dbF->query($sql, [":turnir" => $turnir])->findAll();
  }
  
  
  // Определяем количество кругов
  $cupMode = isset($result[0]['cup']) ? (int)$result[0]['cup'] : 2; // По умолчанию два круга
  
  // Проверяем есть ли группы в турнире
  $groups = []; // массив для название групп (['A', 'B' ...])
  if (!empty($result) && isset($result[0]['team1_group'])) {
    foreach($result as $row) {
        if(!in_array($row['team1_group'], $groups)) {
            $groups[] = $row['team1_group'];
        }
    }
  }
  
  // Преобразуем результат запроса в массив
  $rows = $result;
  
  $matches = [];
  $teams = [];
  $games_count = [];
  $wins_count = [];
  $draws_count = [];
  $losses_count = [];
  $goals_scored = [];  // Забитые мячи
  $goals_conceded = []; // Пропущенные мячи
  
  foreach ($result as $row) {
    $team1 = $row['team1_id'];
    $team2 = $row['team2_id'];
    $goals1 = isset($row['team1_goals']) ? (int)$row['team1_goals'] : null;
    $goals2 = isset($row['team2_goals']) ? (int)$row['team2_goals'] : null;
    
  
    // Если один круг, проверяем, был ли матч уже учтен
    if ($cupMode === 0 && isset($matches[$team2][$team1])) {
        continue; // Пропускаем повторный матч
    }
  
    // Инициализируем команду в массиве, если её там нет
    foreach ([$team1, $team2] as $team) {
        if (!isset($games_count[$team])) {
            $games_count[$team] = 0;
            $wins_count[$team] = 0;
            $draws_count[$team] = 0;
            $losses_count[$team] = 0;
            $goals_scored[$team] = 0;
            $goals_conceded[$team] = 0;
        }
    }
  
    // Определяем результат матча
    if( !is_null( $goals1 ) || !is_null( $goals2 ) ){
  
        // Увеличиваем счётчик игр
        $games_count[$team1]++;
        $games_count[$team2]++;
        
        if ($goals1 > $goals2) {
        $wins_count[$team1]++;   // Победа team1
        $losses_count[$team2]++; // Поражение team2
        } elseif ($goals2 > $goals1) {
        $wins_count[$team2]++;   // Победа team2
        $losses_count[$team1]++; // Поражение team1
        } else {
        $draws_count[$team1]++;  // Ничья
        $draws_count[$team2]++;
        }
    }
  
    // Записываем забитые и пропущенные мячи
    $goals_scored[$team1] += $goals1;
    $goals_conceded[$team1] += $goals2;
  
    $goals_scored[$team2] += $goals2;
    $goals_conceded[$team2] += $goals1;
  
    // Заполняем данные о матчах для дома и гостя
    if ($cupMode === 0 || $cupMode === 1) {
        // Для одного круга и кубка: дублируем результат
        $matches[$row['team1_id']][$row['team2_id']] = $goals1 || $goals2 ? "{$row['team1_goals']}:{$row['team2_goals']}" : "-";
        $matches[$row['team2_id']][$row['team1_id']] = $goals1 || $goals2 ? "{$row['team2_goals']}:{$row['team1_goals']}" : "-";
        $matchesHome[$row['team1_id']][$row['team2_id']] = $goals1 || $goals2 ? "{$row['team1_goals']}:{$row['team2_goals']}" : "-";
        $matchesHome[$row['team2_id']][$row['team1_id']] = $goals1 || $goals2 ? "{$row['team2_goals']}:{$row['team1_goals']}" : "-";
    } else {
        // Для двух кругов - как обычно
        $matches[$row['team1_id']][$row['team2_id']] = $goals1 || $goals2 ? "{$row['team1_goals']}:{$row['team2_goals']}" : "-";
        $matches[$row['team2_id']][$row['team1_id']] = $goals1 || $goals2 ? "{$row['team2_goals']}:{$row['team1_goals']}" : "-";
        $matchesHome[$row['team1_id']][$row['team2_id']] = $goals1 || $goals2 ? "{$row['team1_goals']}:{$row['team2_goals']}" : "-";
    }
  
     // Заполняем данные о командах
     $teams[$row['team1_id']] = [
         'name' => $row['team1_name'], 
         'logo' => $row['team1_logo'], 
         'group' => $row['team1_group']
     ];
     $teams[$row['team2_id']] = [
         'name' => $row['team2_name'], 
         'logo' => $row['team2_logo'], 
         'group' => $row['team2_group']
     ];
  }
  
  // Инициализация турнирной таблицы
  $stats = [];
  
  foreach ($teams as $team_id => $team_data) {
    $stats[$team_id] = [
        'name' => $team_data['name'],
        'logo' => $team_data['logo'],
        'games' => $games_count[$team_id],
        'wins' => $wins_count[$team_id],
        'draws' => $draws_count[$team_id],
        'losses' => $losses_count[$team_id],
        'goals_scored' => $goals_scored[$team_id],
        'goals_conceded' => $goals_conceded[$team_id],
        'points' => $wins_count[$team_id] * 3 + $draws_count[$team_id],
        'group' => $team_data['group'],
        'matches_home' => $matchesHome[$team_id],
        
    ];
  }
  
  /// Сортировка с учетом всех критериев
  uasort($stats, function ($a, $b) {
    // 1. Сортировка по очкам
    if ($b['points'] !== $a['points']) {
        return $b['points'] - $a['points'];
    }
    
    // 2. Сортировка по разнице мячей (забитые - пропущенные)
    $goal_difference_a = $a['goals_scored'] - $a['goals_conceded'];
    $goal_difference_b = $b['goals_scored'] - $b['goals_conceded'];
    
    if ($goal_difference_b !== $goal_difference_a) {
        return $goal_difference_b - $goal_difference_a;
    }
  
    // 3. Сортировка по количеству забитых мячей
    return $b['goals_scored'] - $a['goals_scored'];
  });
  
  // Данные кубка
  $cupData = getCupData($turnir);
  
  require_once VIEWS . '/table.tpl.php';