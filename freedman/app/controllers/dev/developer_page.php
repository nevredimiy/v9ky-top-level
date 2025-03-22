<?php

// Увімкнення відображення помилок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Встановлення рівня звітності помилок
error_reporting(E_ALL);

include_once CONTROLLERS . "/head.php"; 
include_once CONTROLLERS . "/leagues.php";
include_once CONTROLLERS . "/rating_players.php";


if(!isset($turnir) && !isset($tournament)) {
  $turnir = getTurnir();
}

if(!isset($turnir)) {
  $turnir = getTurnir($tournament);
}

// Проверяем, задана ли переменная $turnir
if (!isset($turnir)) {
  die('Ошибка: переменная $turnir не задана.');
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



/// Выводим турнирную таблицу
foreach ($groupedTeams as $group => $teams) {
  echo "<h2>Группа $group</h2>";
  echo "<table border='1'><tr><th>#</th><th>Команда</th><th>И</th><th>В</th><th>Н</th><th>П</th><th>Очки</th><th>Мячи</th>";
  foreach ($teams as $t) echo "<th>{$teamIndex[$t['id']]}</th>";
  echo "</tr>";
  
  foreach ($teams as $i => $team) {
      $id = $team['id'];
      echo "<tr><td>" . ($i + 1) . "</td><td>{$stats[$id]['name']}</td><td>{$stats[$id]['games']}</td><td>{$stats[$id]['wins']}</td><td>{$stats[$id]['draws']}</td><td>{$stats[$id]['losses']}</td><td>{$stats[$id]['points']}</td><td>{$stats[$id]['goals_for']}:{$stats[$id]['goals_against']}</td>";
      foreach ($teams as $t) echo "<td>{$matchResults[$id][$teamIndex[$t['id']]]}</td>";
      echo "</tr>";
  }
  echo "</table>";
}



// Данные кубка
$cupData = getCupData($turnir);

// require_once VIEWS . '/table.tpl.php';
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
                                <th><span class="cell cell--total">О</span></th>
                            </tr>

                            <?php  foreach ($teams as $i => $team) : ?>
                                <?php $id = $team['id']; ?>
                                <?php $team_id = $team['id']; ?>
                                <tr>
                                    <td><span class="cell"><?= $i + 1 ?></span></td>
                                    <td><img width="18" height="18" class="cell--team-logo" src="<?= $team_logo_path ?>/<?= $team['logo'] ?>"></td>
                                    <td><a href="<?= $site_url . '/' . $tournament .'/team_page/id/' . $team_id ?>"><span class="cell--team"><?= $stats[$id]['name']?></span></a></td>

                                    <?php foreach ($teams as $t): ?>   
                                        <td>
                                            <?php if($t['id'] == $team_id): ?>                                                         
                                                <span class="cell--score cell--own"></span>
                                            <?php else :?>
                                                <span class="cell--score<?= $t['id'] == $team_id ? ' cell--own' : '' ?>">
                                                    <?= empty( $matchResults[$id][$teamIndex[$t['id']]] ) ? '-' : $matchResults[$id][$teamIndex[$t['id']]] ?>
                                                </span>
                                            <?php endif?>
                                      </td>                                        
                                    <?php endforeach ?>

                                    <td><span class="cell cell--games"><?= $stats[$id]['games']?></span></td>
                                    <td><span class="cell cell--win"><?= $stats[$id]['wins']?></span></td>
                                    <td><span class="cell cell--draw"><?= $stats[$id]['draws']?></span></td>
                                    <td><span class="cell cell--defeat"><?= $stats[$id]['losses']?></span></td>
                                    <td class="td-scored"><span class="cell cell--scored"><?= $stats[$id]['goals_for']?> - <?= $stats[$id]['goals_against'] ?> </span></td>
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

include_once CONTROLLERS . "/calendar_of_matches.php";
include_once CONTROLLERS . "/controls.php";
include_once CONTROLLERS . "/disqualification.php";

require_once CONTROLLERS . '/footer.php';



