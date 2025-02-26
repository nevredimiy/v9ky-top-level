<?php

$turnir = 532;
// Увімкнення відображення помилок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Встановлення рівня звітності помилок
error_reporting(E_ALL);


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
$matchesHome = [];
$matchesGuest = [];
$teams = [];



$games_count = [];
$wins_count = [];
$draws_count = [];
$losses_count = [];
$goals_scored = [];  // Забитые мячи
$goals_conceded = []; // Пропущенные мячи

// Обрабатываем данные из таблицы
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



// Сортировка по очкам
uasort($stats, function ($a, $b) {
    return $b['points'] - $a['points'];
});




echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr>";
echo "<th>Команда</th>";

foreach ($stats as $team_id => $team_data) {
    echo "<th>{$team_data['name']}</th>";
}

echo "</tr>";

// Выводим данные по каждой команде
foreach ($stats as $team_id => $team_data) {
    echo "<tr>";
    echo "<td><strong>{$team_data['name']}</strong></td>"; // Название команды

    foreach ($stats as $opponent_id => $opponent_data) {
        if ($team_id === $opponent_id) {
            echo "<td style='background:#ddd'>X</td>"; // Игры с самим собой не отображаем
        } elseif (isset($team_data['matches_home'][$opponent_id])) {
            echo "<td>{$team_data['matches_home'][$opponent_id]}</td>"; // Отображаем результат
        } else {
            echo "<td>-</td>"; // Если нет игры, выводим "-"
        }
    }

    echo "</tr>";
}

echo "</table>";






include_once CONTROLLERS . "/head.php";
 
include_once CONTROLLERS . "/leagues.php";
include_once CONTROLLERS . "/rating_players.php";
?>


<section class="table-league">
    <?php foreach ($groups ?: [''] as $group): ?>
        <?php 
        // Фильтруем команды, оставляя только те, что принадлежат текущей группе
        $group_teams = array_filter($stats, function($team) use ($group) {
            return $team['group'] == $group;
        });

        // Считаем количество команд в группе
        $team_count = count($group_teams);
        ?>
        
        <h2 class="table-league__title title title--inverse">
            <span>Турнірна таблиця</span>
            <span><?= $result[0]['turnir_name'] ?></span>
            <?= $group ? "<span>Группа $group</span>" : '' ?>
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
                                <?php for ($i = 1; $i <= $team_count; $i++): ?>
                                    <th><span class="cell--score"><?= $i ?></span></th>
                                <?php endfor; ?>
                                <th><span class="cell cell--games">І</span></th>
                                <th><span class="cell cell--win">В</span></th>
                                <th><span class="cell cell--draw">Н</span></th>
                                <th><span class="cell cell--defeat">П</span></th>
                                <th><span class="cell cell--total">О</span></th>
                            </tr>

                            <?php $position = 1; ?>
                            <?php foreach ($group_teams as $team_id => $stat): ?>
                                <tr>
                                    <td><span class="cell"><?= $position ?></span></td>
                                    <td><img width="18" height="18" class="cell--team-logo" src="<?= $team_logo_path ?>/<?= $stat['logo'] ?>"></td>
                                    <td><a href="<?= $site_url . '/' . $tournament .'/team_page/id/' . $team_id ?>"><span class="cell--team"><?= $stat['name']?></span></a></td>

                                    <?php foreach ($group_teams as $key => $value): ?>
                                        <?php if ($key === $team_id): ?>  
                                            <td><span class="cell--score cell--own"></span></td> 
                                        <?php else: ?>                                    
                                            <td><span class="cell--score"><?= isset($stat['matches_home'][$key]) ? $stat['matches_home'][$key] : '-' ?></span></td>
                                        <?php endif ?>
                                    <?php endforeach ?>

                                    <td><span class="cell cell--games"><?= $stat['games']?></span></td>
                                    <td><span class="cell cell--win"><?= $stat['wins']?></span></td>
                                    <td><span class="cell cell--draw"><?= $stat['draws']?></span></td>
                                    <td><span class="cell cell--defeat"><?= $stat['losses']?></span></td>
                                    <td><span class="cell cell--total"><?= $stat['points']?></span></td>
                                </tr>
                                <?php $position++; ?>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="swiper-scrollbar-table"></div>
        </div>
    <?php endforeach; ?>
</section>

<?php include_once CONTROLLERS . "/footer.php"; ?>