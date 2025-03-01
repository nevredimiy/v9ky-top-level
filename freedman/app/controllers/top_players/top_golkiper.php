<?php

include_once HOME . "/dates.php";
include_once CONTROLLERS . "/head.php";


// Последний тур в турнире (в лиге).
$lastTur = getLastTur($turnir);

//Получаем всю статистику игроков
$allStaticPlayers = getAllStaticPlayers($turnir);

// Получаем данные всех игроков - ФИО, фото и т.д.
$dataAllPlayers = getDataPlayers($allStaticPlayers); 

function getTopGolkiper($allStaticPlayers, $dataAllPlayers, $keySort, $lastTur = 0)
{  
    // Преобразование массива
    $topPlayers = [];

    foreach ($allStaticPlayers as $playerId => $matches) {
        $matchCount = count($matches); // Кількість матчів
        $totalKeySort = calculateArrayByColumn($keySort, $matches); // Сума всіх значень по ключу $keySort
        $countGoals = array_sum(array_column($matches, 'count_goals'));
        $countAsists = array_sum(array_column($matches, 'count_asists'));
        $countGolevoypas = array_sum(array_column($matches, 'golevoypas'));
        $countYellowCards = array_sum(array_column($matches, 'yellow_cards'));
        $countYellowRedCards = array_sum(array_column($matches, 'yellow_red_cards'));
        $countRed_cards = array_sum(array_column($matches, 'red_cards'));

        $matchIdKeys = array_keys($matches);
        // Підрахунок кількості найкращих гравців матчу
        $countBPM = array_count_values(array_column($matches, 'count_best_player_of_match'));
        $countBestPlayerOfMatch = isset($countBPM[$playerId]) ? $countBPM[$playerId] : 0;

        // Голкіпери
        if ($keySort == "golkiper" && is_array($totalKeySort)) {
            $totalSaves = $totalKeySort['seyv'] + $totalKeySort['seyvmin'];
            $isTop = $totalSaves >= 10; // Позначаємо, чи воротар входить у топ

            $keySortPerMatch = $matchCount > 0 ? $totalKeySort['total_value'] / $matchCount : 0; // Середнє значення по ключу $keySort за матч
            
            $row = [
                'player_id' => $playerId,
                'match_count' => $matchCount,
                'total_key' => $totalKeySort['total_value'],
                'key_per_match' => round($keySortPerMatch, 2), // Округлення до 2 знаків
                'seyv' => $totalKeySort['seyv'],
                'seyvmin' => $totalKeySort['seyvmin'],
                'team_id' => isset($dataAllPlayers[$playerId]['team_id']) ? $dataAllPlayers[$playerId]['team_id'] : '',
                'last_name' => isset($dataAllPlayers[$playerId]['last_name']) ? $dataAllPlayers[$playerId]['last_name'] : '',
                'first_name' => isset($dataAllPlayers[$playerId]['first_name']) ? $dataAllPlayers[$playerId]['first_name'] : '',
                'player_photo' => isset($dataAllPlayers[$playerId]['player_photo']) ? $dataAllPlayers[$playerId]['player_photo'] : '',
                'team_photo' => isset($dataAllPlayers[$playerId]['team_photo']) ? $dataAllPlayers[$playerId]['team_photo'] : '',
                'team_name' => isset($dataAllPlayers[$playerId]['team_name']) ? $dataAllPlayers[$playerId]['team_name'] : '',
                'is_top' => $isTop, // Позначка для візуального відображення
            ];

            // Додаємо значення для кожного матчу
            foreach ($matches as $matchId => $stats) {
                $seyv = isset($stats['seyv']) ? $stats['seyv'] : 0;
                $seyvmin = isset($stats['seyvmin']) ? $stats['seyvmin'] : 0;
                $denominator = $seyv + $seyvmin; // Знаменник
                
                if (isset($stats['tur'])) {
                    $row["match_{$stats['tur']}_key"] = $denominator == 0
                        ? 0 
                        : round((100 / $denominator) * $stats['seyv'], 1);
                }
            }

            $topPlayers[] = $row;
        }
    }

    // Сортуємо воротарів
    usort($topPlayers, function ($a, $b) use ($lastTur) {
        // 1. Спочатку сортуємо за "топовістю" (ті, що мають >10 сейвів, йдуть першими)
        if ($a['is_top'] !== $b['is_top']) {
            return $b['is_top'] - $a['is_top']; // true (1) буде вище за false (0)
        }

        // 2. Сортування за total_key (сумарні очки)
        if ($a['total_key'] != $b['total_key']) {
            return ($b['total_key'] > $a['total_key']) ? 1 : -1;
        }

        // 3. Сортування за кількістю матчів (match_count)
        if ($a['match_count'] != $b['match_count']) {
            return ($b['match_count'] > $a['match_count']) ? 1 : -1;
        }

        // 4. Сортування за останнім туром (якщо є значення)
        if (isset($a["match_{$lastTur}_key"]) && isset($b["match_{$lastTur}_key"])) {
            if ($a["match_{$lastTur}_key"] != $b["match_{$lastTur}_key"]) {
                return ($b["match_{$lastTur}_key"] > $a["match_{$lastTur}_key"]) ? 1 : -1;
            }
        }

        return 0;
    });

    // Присвоюємо позиції
    $rank = 1;
    foreach ($topPlayers as $index => &$player) {
        if ($index > 0 && $topPlayers[$index - 1]['total_key'] === $player['total_key']
            && $topPlayers[$index - 1]['match_count'] === $player['match_count']
        ) {
            $player['rank'] = $topPlayers[$index - 1]['rank'];
        } else {
            $player['rank'] = $rank;
        }
        $rank++;
    }

    return $topPlayers;
}

// Отсортированный массив по рубрике Топ-Голкипер
$topGolkiper = getTopGolkiper($allStaticPlayers, $dataAllPlayers, 'golkiper', $lastTur);


// dump_arr($topBombardi);


?>

<div class="statistic">
    <div class="container">
    <table id="top-golkiper" class="draggable-container">
      <caption>
        ТОП-Голкіпер
        <a class="statistic__link-to-home" href="<?= $site_url?><?=$get_query_temp?>">
          <img src="/css/components/statistic/assets/images/button-exit.svg" alt="exit">
        </a>
      </caption>
      <thead>
        <tr>
          <th>№</th>
          <th class="th_s" data-label="Ф">ФОТО</th>
          <th class="th_s" data-label="Л">ЛОГО</th>
          <th class="th_s" data-label="Г">ГРАВЕЦЬ</th>
          <th class="th_s" data-label="Т">Тотал</th>
          <th class="th_s" data-label="М">Матчів</th>
          <th class="th_s" data-label="С+">Сейв +</th>
          <th class="th_s" data-label="С-">Сейв -</th>
          <?php for($i = 1; $i <= 10; $i++): ?>
              <th data-label="<?= $i ?>}"><?= $i ?></th>
          <?php endfor ?>
        </tr>
      </thead>
      <tbody>
        <?php $int = 0; ?>
        <?php foreach($topGolkiper as $player): ?>

          
          

            <tr class="<?= $player['is_top'] ? 'top-player' : 'out-of-contest' ?>" data-playerid="<?= $player['player_id'] ?>" data-matchid="<?= $player['match_ids'] ?>" data-serial-number="<?= $int ?>" >
              <td><?= isset($player['rank']) ? $player['rank'] : "?" ?></td>
              <td><img src="<?=$player_face_path?>/<?= $player['player_photo'] ?>" alt="team-logo"></td>
              <td><img src="<?=$team_logo_path?>/<?= $player['team_photo'] ?>" alt="team-logo"></td>
              <td class="name-cell"><?= $player['last_name'] ?> <?= $player['first_name'] ?></td>
              <td><?= $player['total_key'] ?></td>
              <td><?= $player['match_count'] ?></td>
              <td><?= $player['seyv'] ?></td>
              <td><?= $player['seyvmin'] ?></td>
              <?php for ($i = 1; $i <= 10; $i++): ?>
              <?php $stub = $i > $lastTur ? '?' : '-' ?>
              <td class="turs" <?= $i > $lastTur ? 'style="opacity:0.5"' : '' ?> ><?= isset($player["match_{$i}_key"]) ? $player["match_{$i}_key"] . "%"  : $stub  ?></td>
              <?php endfor ?>
          </tr>


        
        <?php $int ++?>
        <?php endforeach ?>
      </tbody>
    </table>
    </div>
</div>
<?php include_once "freedman/footer.php" ?>