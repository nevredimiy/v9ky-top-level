<?php

// Проверяем, задана ли переменная $turnir
if (!isset($turnir)) {
    die('Ошибка: переменная $turnir не задана.');
}


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
    t.ru AS turnir_name
FROM 
    v9ky_match m
JOIN 
    v9ky_team t1 ON m.team1 = t1.id
JOIN 
    v9ky_team t2 ON m.team2 = t2.id
LEFT JOIN 
    v9ky_turnir t ON t.id = m.turnir
WHERE 
    m.turnir = :turnir AND canseled = 1
ORDER BY 
    t1.grupa ASC, t1.name ASC, t2.name ASC";

$result = $dbF->query($sql, [":turnir" => $turnir])->findAll();

if (!$result) {
    echo "SQL Query Error: " . $dbF->error;
    die('Ошибка выполнения запроса.');
}

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
    $goals1 = (int)$row['team1_goals'];
    $goals2 = (int)$row['team2_goals'];

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

    // Увеличиваем счётчик игр
    $games_count[$team1]++;
    $games_count[$team2]++;

    // Определяем результат матча
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

    // Записываем забитые и пропущенные мячи
    $goals_scored[$team1] += $goals1;
    $goals_conceded[$team1] += $goals2;

    $goals_scored[$team2] += $goals2;
    $goals_conceded[$team2] += $goals1;


     // Заполняем данные о матчах
     $matches[$row['team1_id']][$row['team2_id']] = "{$row['team1_goals']}:{$row['team2_goals']}";
     $matches[$row['team2_id']][$row['team1_id']] = "{$row['team2_goals']}:{$row['team1_goals']}";
     $matchesHome[$row['team1_id']][$row['team2_id']] = "{$row['team1_goals']}:{$row['team2_goals']}";
     $matchesGuest[$row['team2_id']][$row['team1_id']] = "{$row['team2_goals']}:{$row['team1_goals']}";
 
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
        'matches_guest' => $matchesGuest[$team_id]
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


require_once VIEWS . '/table.tpl.php';