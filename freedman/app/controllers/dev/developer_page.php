<?php

// Увімкнення відображення помилок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Встановлення рівня звітності помилок
error_reporting(E_ALL);

$turnir = 527;

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
    t1.name ASC, t2.name ASC";

$result = $dbF->query($sql, [":turnir" => $turnir])->findAll();

if (!$result) {
    die('Ошибка выполнения запроса.');
}

// Проверяем есть ли группы в турнире
$groups = []; // массив для название групп (['A', 'B' ...])
if($result[0]['team1_group']) {
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

foreach ($result as $row) {
    // Заполняем данные о матчах
    $matches[$row['team1_id']][$row['team2_id']] = "{$row['team1_goals']}:{$row['team2_goals']}";
    $matches[$row['team2_id']][$row['team1_id']] = "{$row['team2_goals']}:{$row['team1_goals']}";

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
        'games' => 0,
        'wins' => 0,
        'draws' => 0,
        'losses' => 0,
        'points' => 0,
        'group' => $team_data['group']
    ];
}

// Подсчет статистики
foreach ($matches as $team1_id => $opponents) {
    foreach ($opponents as $team2_id => $score) {
       
        // Проверяем, чтобы не было двойного подсчета
        if (!isset($processed_matches["$team1_id-$team2_id"]) && !isset($processed_matches["$team2_id-$team1_id"])) {
            list($goals1, $goals2) = explode(':', $score);

            // Увеличиваем количество игр
            $stats[$team1_id]['games']++;
            $stats[$team2_id]['games']++;

            // Обновляем статистику побед, ничьих, поражений и очков
            if ($goals1 > $goals2) {
                $stats[$team1_id]['wins']++;
                $stats[$team2_id]['losses']++;
                $stats[$team1_id]['points'] += 3;
            } elseif ($goals1 < $goals2) {
                $stats[$team2_id]['wins']++;
                $stats[$team1_id]['losses']++;
                $stats[$team2_id]['points'] += 3;
            } else {
                $stats[$team1_id]['draws']++;
                $stats[$team2_id]['draws']++;
                $stats[$team1_id]['points'] += 1;
                $stats[$team2_id]['points'] += 1;
            }

            // Отмечаем матч как обработанный
            $processed_matches["$team1_id-$team2_id"] = true;
        }
    }
}

// Сортировка по очкам
uasort($stats, function ($a, $b) {
    return $b['points'] - $a['points'];
});

require_once CONTROLLERS . '/head.php';

?>


<section class="table-league">

    <?php if(!empty($groups)): ?>
        <?php foreach( $groups as $group): ?>

            <h2 class="table-league__title title title--inverse ">
                <span>Турнірна таблиця</span>
                <span><?= $rows[0]['turnir_name'] ?></span>
                <span>Группа <?= $group ?></span>
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
                                <?php for( $i = 1; $i <= count($stats); $i++ ): ?>
                                    <th><span class="cell--score"><?= $i ?></span></th>
                                <?php endfor ?>
                                <th><span class="cell cell--games">І</span></th>
                                <th><span class="cell cell--win">В</span></th>
                                <th><span class="cell cell--draw">Н</span></th>
                                <th><span class="cell cell--defeat">П</span></th>
                                <th><span class="cell cell--total">О</span></th>
                            </tr>
                            <?php $position = 1; ?>
                            <?php foreach($stats as $team_id => $stat): ?>
                                <?php if($stat['group'] == $group): ?>
                                    <tr>
                                        <td><span class="cell"><?= $position?></span></td>
                                        <td><img width="18" height="18" class="cell--team-logo" src="<?= $team_logo_path ?>/<?= $stat['logo']?>"></td>
                                        <td><a href="<?= $site_url . '/' . $tournament .'/team_page/id/' . $team_id ?>"><span class="cell--team"><?= $stat['name']?></span></a></td>

                                        <?php foreach ($stats as $key => $value) : ?>
                                            
                                                <?php if($key === $team_id) :?>  
                                                    <td><span class="cell--score cell--own"></span></td> 
                                                <?php else :?>                                    
                                                    <td><span class="cell--score"><?= isset($matches[$team_id][$key]) ? $matches[$team_id][$key] : '-' ?></span></td>
                                                <?php endif ?>
                                            
                                        <?php endforeach ?>
                                        
                                        <td><span class="cell cell--games"><?= $stat['games']?></span></td>
                                        <td><span class="cell cell--win"><?= $stat['wins']?></span></td>
                                        <td><span class="cell cell--draw"><?= $stat['draws']?></span></td>
                                        <td><span class="cell cell--defeat"><?= $stat['losses']?></span></td>
                                        <td><span class="cell cell--total"><?= $stat['points']?></span></td>
                                    </tr>
                                    <?php $position++ ?>
                                <?php endif ?>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                    </div>
                </div>
                <div class="swiper-scrollbar-table"></div>
            </div>

        <?php endforeach ?>
    
    <?php else: ?>
    
    <h2 class="table-league__title title title--inverse ">
    <span>Турнірна таблиця</span>
    <span><?= $rows[0]['turnir_name'] ?></span>
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
                        <?php for( $i = 1; $i <= count($stats); $i++ ): ?>
                            <th><span class="cell--score"><?= $i ?></span></th>
                        <?php endfor ?>
                        <th><span class="cell cell--games">І</span></th>
                        <th><span class="cell cell--win">В</span></th>
                        <th><span class="cell cell--draw">Н</span></th>
                        <th><span class="cell cell--defeat">П</span></th>
                        <th><span class="cell cell--total">О</span></th>
                    </tr>
                    <?php $position = 1; ?>
                    <?php foreach($stats as $team_id => $stat): ?>
                        <tr>
                            <td><span class="cell"><?= $position?></span></td>
                            <td><img width="18" height="18" class="cell--team-logo" src="<?= $team_logo_path ?>/<?= $stat['logo']?>"></td>
                            <td><a href="<?= $site_url . '/' . $tournament .'/team_page/id/' . $team_id ?>"><span class="cell--team"><?= $stat['name']?></span></a></td>

                            <?php foreach ($stats as $key => $value) : ?>
                                
                                    <?php if($key === $team_id) :?>  
                                        <td><span class="cell--score cell--own"></span></td> 
                                    <?php else :?>                                    
                                        <td><span class="cell--score"><?= isset($matches[$team_id][$key]) ? $matches[$team_id][$key] : '-' ?></span></td>
                                    <?php endif ?>
                                
                            <?php endforeach ?>
                            
                            <td><span class="cell cell--games"><?= $stat['games']?></span></td>
                            <td><span class="cell cell--win"><?= $stat['wins']?></span></td>
                            <td><span class="cell cell--draw"><?= $stat['draws']?></span></td>
                            <td><span class="cell cell--defeat"><?= $stat['losses']?></span></td>
                            <td><span class="cell cell--total"><?= $stat['points']?></span></td>
                        </tr>
                    <?php $position++ ?>
                    <?php endforeach ?>
                </tbody>
            </table>
            </div>
        </div>
        <div class="swiper-scrollbar-table"></div>
    </div>
    <?php endif ?>
</section>

<?php require_once CONTROLLERS . '/footer.php'; ?>
